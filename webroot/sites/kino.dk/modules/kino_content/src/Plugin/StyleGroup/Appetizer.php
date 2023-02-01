<?php

namespace Drupal\kino_content\Plugin\StyleGroup;

use Drupal\image_style_generator\StyleGroupBase;

/**
 * @StyleGroup(
 *  id = "appetizer",
 *  title = "Appetizer",
 *  view_modes = {
 *    "width_100"
 *  }
 * )
 */
class Appetizer extends StyleGroupBase {

  public function getCrop($breakpoint, $width, $view_mode = NULL): array {
    return [
      'width' => $width,
      'height' => $width / 4 * 3
    ];
  }

}
