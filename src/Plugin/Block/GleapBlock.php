<?php

namespace Drupal\gleap\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a Gleap block.
 *
 * @Block( id = "gleap_block",
 * admin_label = @Translation("Gleap Block"),
 * category = @Translation("Gleap")
 * )
 */
class GleapBlock extends BlockBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The admin Gleap configuration.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Gleap Block.
   *
   * @param array $configuration
   *   The Gleap block configuration.
   * @param mixed $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin path destination.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   The current user.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, AccountInterface $currentUser, MessengerInterface $messenger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory->get('gleap.gleap_configuration');
    $this->currentUser = $currentUser;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('current_user'),
      $container->get('messenger'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    if (!$this->configFactory->get('gleap_enable')) {
      return [];
    }

    if (empty($this->configFactory->get('gleap_api_key')) && ($this->currentUser->hasPermission('administer gleap'))) {
      $url = Url::fromUri('route:gleap_configuration');
      $link = new Link($this->t('here'), $url);
      $link = $link->toString()->getGeneratedLink();
      $message = Markup::create($this->t('Please fill out the Gleap API key from %link', ['%link' => $link]));

      return [
        '#theme' => 'gleap_block_template',
        '#show_message' => TRUE,
        '#message' => $message,
        '#cache' => [
          'tags' => ['config:gleap.gleap_configuration'],
        ],
      ];
    }
    else {
      return [
        '#theme' => 'gleap_block_template',
        '#show_message' => FALSE,
        '#attached' => [
          'library' => [
            'gleap/gleap',
          ],
          'drupalSettings' => [
            'gleap' => [
              'apiKey' => $this->configFactory->get('gleap_api_key'),
            ],
          ],
        ],
        '#cache' => [
          'tags' => ['config:gleap.gleap_configuration'],
        ],
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $current_roles = $this->currentUser->getRoles();
    if (!empty(array_intersect($current_roles, $this->configFactory->get('gleap_roles') ?? []))) {
      return AccessResult::allowed();
    } else {
      return AccessResult::forbidden();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['config:gleap.gleap_configuration']);
  }

}
