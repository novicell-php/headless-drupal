<?php

namespace Drupal\kino_content\Plugin\StyleGroup;

use Drupal\image_style_generator\Annotation\StyleGroup;
use Drupal\image_style_generator\StyleGroupBase;

/**
 * @StyleGroup(
 *  id = "image_16_9",
 *  title = "Image 16 by 9",
 *  view_modes = {
 *    "full_width",
 *    "width_100",
 *    "width_66",
 *    "width_50",
 *    "width_33"
 *  }
 * )
 */
class Image16by9 extends StyleGroupBase {

  public function getCrop($breakpoint, $width, $view_mode = NULL): array {
    return [
      'width' => $width,
      'height' => $width / 16 * 9
    ];
  }

}
