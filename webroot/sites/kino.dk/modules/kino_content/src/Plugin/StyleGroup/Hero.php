<?php

namespace Drupal\kino_content\Plugin\StyleGroup;

/**
 * @StyleGroup(
 *  id = "hero",
 *  title = "Hero",
 *  view_modes = {
 *    "full_width",
 *    "width_100",
 *    "width_66",
 *    "width_50",
 *    "width_33"
 *  }
 * )
 */
class Hero extends \Drupal\image_style_generator\StyleGroupBase {

  public function getCrop($breakpoint, $width, $view_mode = NULL): array {
    return [
      'width' => $width,
      'height' => $width / 16 * 9
    ];
  }

}
