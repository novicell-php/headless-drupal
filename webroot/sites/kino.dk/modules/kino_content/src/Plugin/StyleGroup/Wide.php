<?php

namespace Drupal\kino_content\Plugin\StyleGroup;

use Drupal\image_style_generator\StyleGroupBase;

/**
 * @StyleGroup(
 *  id = "wide",
 *  title = "Wide",
 *  view_modes = {
 *    "full_width"
 *  }
 * )
 */
class Wide extends StyleGroupBase {

  public function getCrop($breakpoint, $width, $view_mode = NULL): array {
    return [
      'width' => $width,
      'height' => $width / 4
    ];
  }

}
