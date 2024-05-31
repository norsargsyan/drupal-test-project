<?php

namespace Drupal\sl_main\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for showing Hello world.
 */
class HelloWorldController extends ControllerBase {

  /**
   * Return render array.
   *
   * @return array
   */
  public function content(): array {
    return [
      '#markup' => $this->t("Hello world content"),
    ];
  }

}
