<?php

namespace Drupal\kino_content\Plugin\StyleViewMode;

use Drupal\image_style_generator\Annotation\StyleViewMode;
use Drupal\image_style_generator\StyleViewModeBase;

/**
 * @StyleViewMode(
 *  id = "width_50",
 *  title = "Width 50%"
 * )
 */
class Width50 extends ContentRegionBase {

  public function calcWidth($breakpoint, $width): int {
    if ($this->isMobile($breakpoint)) {
      return $this->widths[$breakpoint] = parent::calcWidth($breakpoint, $width);
    } else {
      return $this->widths[$breakpoint] = (parent::calcWidth($breakpoint, $width) / 2) - $this->getGutterWidth($breakpoint);
    }
  }

}
