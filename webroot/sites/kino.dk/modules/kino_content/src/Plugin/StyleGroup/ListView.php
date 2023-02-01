<?php

namespace Drupal\kino_content\Plugin\StyleGroup;

use Drupal\image_style_generator\StyleGroupBase;

/**
 * @StyleGroup(
 *  id = "list_view",
 *  title = "List View",
 *  view_modes = {
 *  }
 * )
 */
class ListView extends StyleGroupBase {

  public function getCrop($breakpoint, $width, $view_mode = NULL): array {
    return [
      'width' => 365,
      'height' => 200
    ];
  }

}
