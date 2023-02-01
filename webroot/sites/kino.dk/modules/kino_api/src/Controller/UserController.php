<?php
namespace Drupal\kino_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends ControllerBase {

  public function register(Request $request) {
    $result = NULL;
    $values = json_decode($request->getContent(), TRUE);

    if (empty($values['name'])) {
      return new JsonResponse([
        'code' => 'register_field_is_required',
        'field' => 'name',
        'error' => $this->t('@name field is required.', ['@name' => $this->t('Username')])
      ], 400);
    }
    if (empty($values['mail'])) {
      return new JsonResponse([
        'code' => 'register_field_is_required',
        'field' => 'mail',
        'error' => $this->t('@name field is required.', ['@name' => $this->t('E-mail')])
      ], 400);
    }

    $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    $uids = $userStorage->getQuery()
      ->condition('name', $values['name'])
      ->execute();
    if (!empty($uids)) {
      return new JsonResponse([
        'code' => 'register_username_exists',
        'error' => $this->t('User name already exists.')
      ], 400);
    }
    $uids = $userStorage->getQuery()
      ->condition('mail', $values['mail'])
      ->execute();
    if (!empty($uids)) {
      return new JsonResponse([
        'code' => 'register_email_exists',
        'error' => $this->t('E-mail already exists.')
      ], 400);
    }

    $account = User::create();

    // This form is used for two cases:
    // - Self-register (route = 'user.register').
    // - Admin-create (route = 'user.admin_create').
    // If the current user has permission to create users then it must be the
    // second case.
    $admin = $account->access('create');

    // Because the user status has security implications, users are blocked by
    // default when created programmatically and need to be actively activated
    // if needed. When administrators create users from the user interface,
    // however, we assume that they should be created as activated by default.
    if ($admin || \Drupal::config('user.settings')->get('register') == UserInterface::REGISTER_VISITORS) {
      $account->activate();
    }

    if (!\Drupal::config('user.settings')->get('verify_mail') || $admin) {
      $pass = $values['pass'];
    }
    else {
      $pass = \Drupal::service('password_generator')->generate();
    }

    $values['pass'] = $pass;
    $values['init'] = $values['mail'];
    if ($admin) {
      $notify = !empty($values['notify']);
    } else {
      $notify = FALSE;
    }

    /** @var EntityFieldManagerInterface $entityFieldManager */
    $entityFieldManager = \Drupal::service('entity_field.manager');
    $base_fields = array_keys($entityFieldManager->getBaseFieldDefinitions('user'));
    foreach ($account->getFields() as $field_name => $field) {
      if (in_array($field_name, ['name', 'pass', 'mail', 'init', 'timezone'])) {
        if (isset($values[$field_name])) {
          $account->set($field_name, $values[$field_name]);
        }
      } elseif (in_array($field_name, $base_fields)) {
        continue;
      } else {
        if (isset($values[$field_name])) {
          $account->set($field_name, $values[$field_name]);
        }
      }
    }
    $account->setChangedTime(\Drupal::service('datetime.time')->getRequestTime());


    // Save has no return value so this cannot be tested.
    // Assume save has gone through correctly.
    $account->save();

    \Drupal::logger('user')->notice('New user: %name %email.', ['%name' => $values['name'], '%email' => '<' . $values['mail'] . '>', 'type' => $account->toLink($this->t('Edit'), 'edit-form')->toString()]);

    // Add plain text password into user account to generate mail tokens.
    $account->password = $pass;

    // New administrative account without notification.
    if ($admin && !$notify) {
      $result = [
        'code' => 'register_admin_created_no_mail',
        'status' => $this->t('Created a new user account for <a href=":url">%name</a>. No email has been sent.', [':url' => $account->toUrl()->toString(), '%name' => $account->getAccountName()])
      ];
    }
    // No email verification required; log in user immediately.
    elseif (!$admin && !\Drupal::config('user.settings')->get('verify_mail') && $account->isActive()) {
      _user_mail_notify('register_no_approval_required', $account);
      user_login_finalize($account);
      $result = [
        'code' => 'register_no_approval_required',
        'status' => $this->t('Registration successful. You are now logged in.')
      ];
    }
    // No administrator approval required.
    elseif ($account->isActive() || $notify) {

      if (!$account->getEmail() && $notify) {
        $result = [
          'code' => 'register_without_email',
          'status' => $this->t('The new user <a href=":url">%name</a> was created without an email address, so no welcome message was sent.', [':url' => $account->toUrl()->toString(), '%name' => $account->getAccountName()])
        ];
      }
      else {
        $op = $notify ? 'register_admin_created' : 'register_no_approval_required';
        if (_user_mail_notify($op, $account)) {
          if ($notify) {
            $result = [
              'code' => 'register_admin_created',
              'status' => $this->t('A welcome message with further instructions has been emailed to the new user <a href=":url">%name</a>.', [':url' => $account->toUrl()->toString(), '%name' => $account->getAccountName()])
            ];
          }
          else {
            $result = [
              'code' => 'register_pending_email_verification',
              'status' => $this->t('A welcome message with further instructions has been sent to your email address.')
            ];
          }
        } else {
          return new JsonResponse([
            'code' => 'register_cant_send_mail',
            'error' => $this->t('Unable to send email. Contact the site administrator if the problem persists.')
          ], 500);
        }
      }
    }
    // Administrator approval required.
    else {
      _user_mail_notify('register_pending_approval', $account);
      $result = [
        'code' => 'register_pending_approval',
        'status' => $this->t('Thank you for applying for an account. Your account is currently pending approval by the site administrator.<br />In the meantime, a welcome message with further instructions has been sent to your email address.')
      ];
    }

    if (empty($result)) {
      $result = [
        'code' => 'register_no_outcome',
        'error' => $this->t('No outcome')
      ];
      return new JsonResponse($result, 500);
    } else {
      return new JsonResponse($result);
    }
  }

}
