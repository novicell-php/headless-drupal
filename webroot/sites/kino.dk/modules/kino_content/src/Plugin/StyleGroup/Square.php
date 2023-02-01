<?php

namespace Drupal\kino_content\Plugin\StyleGroup;

use Drupal\image_style_generator\Annotation\StyleGroup;
use Drupal\image_style_generator\StyleGroupBase;

/**
 * @StyleGroup(
 *  id = "square",
 *  title = "Square",
 *  view_modes = {
 *    "width_50",
 *    "width_33"
 *  }
 * )
 */
class Square extends StyleGroupBase {

  public function getCrop($breakpoint, $width, $view_mode = NULL): array {
    return [
      'width' => $width,
      'height' => $width
    ];
  }

}
