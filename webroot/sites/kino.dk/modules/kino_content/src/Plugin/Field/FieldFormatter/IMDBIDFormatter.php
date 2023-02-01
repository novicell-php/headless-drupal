<?php

namespace Drupal\kino_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'imdb_id' formatter.
 *
 * @FieldFormatter(
 *   id = "imdb_id",
 *   label = @Translation("IMDB ID"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class IMDBIDFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    /** @var \Drupal\link\LinkItemInterface $item */
    foreach ($items as $delta => $item) {
      $imdbId = static::getIMDBID($item->getValue());
      $element[$delta] = [
        '#plain_text' => $imdbId,
      ];
    }
    return $element;
  }

  public static function getIMDBID(array $value) {
    $imdbId = NULL;
    if (!empty($value) && !empty($value['uri'])) {
      $path = trim(parse_url($value['uri'], PHP_URL_PATH));
      $paths = explode('/', substr($path, 1, -1));
      $imdbId = $paths[array_key_last($paths)];
    }
    return $imdbId;
  }

}
