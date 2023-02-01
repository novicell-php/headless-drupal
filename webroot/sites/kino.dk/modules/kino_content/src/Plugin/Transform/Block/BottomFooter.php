<?php

namespace Drupal\kino_content\Plugin\Transform\Block;

use Drupal\atoms\Transform\AtomTransform;
use Drupal\transform_api\BlockTransformBase;

/**
 * @BlockTransform(
 *  id = "bottom_footer",
 *  title = "Bottom Footer",
 * )
 */
class BottomFooter extends BlockTransformBase {

  public function transform() {
    return [
      'footer-bottom-privacy' => new AtomTransform('footer-bottom-privacy'),
      'footer-bottom-cookie' => new AtomTransform('footer-bottom-cookie'),
      'footer-bottom-consent' => new AtomTransform('footer-bottom-consent'),
      'footer-bottom-sitemap' => new AtomTransform('footer-bottom-sitemap'),
    ];
  }

}
