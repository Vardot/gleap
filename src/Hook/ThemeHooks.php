<?php

namespace Drupal\gleap\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Provides theme hooks for the Gleap module.
 */
final class ThemeHooks {

  /**
   * Implements hook_theme().
   *
   * Defines the theme template for the Gleap block.
   *
   * @param array $existing
   *   An array of existing implementations.
   * @param string $type
   *   The type of the theme engine being used.
   * @param string $theme
   *   The name of the theme.
   * @param string $path
   *   The path to the theme.
   *
   * @return array
   *   An associative array of theme implementations.
   */
  #[Hook('theme')]
  public function theme(array $existing, string $type, string $theme, string $path): array {
    return [
      'gleap_block_template' => [
        'variables' => [
          'back_link' => NULL,
          'show_message' => FALSE,
          'message' => NULL,
        ],
        'template' => 'gleap-block-template',
      ],
    ];
  }

}
