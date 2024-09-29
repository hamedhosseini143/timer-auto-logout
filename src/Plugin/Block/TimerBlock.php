<?php

declare(strict_types=1);

namespace Drupal\timer_auto_logout\Plugin\Block;

use Drupal\autologout\AutologoutManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a timer auto logout block.
 *
 * @Block(
 *   id = "timer_auto_logout",
 *   admin_label = @Translation("timer auto logout"),
 *   category = @Translation("Custom"),
 * )
 */
final class TimerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The auto log-out manager service.
   *
   * @var \Drupal\autologout\AutologoutManagerInterface
   */
  protected AutologoutManagerInterface $autoLogoutManager;

  /**
   * The Config service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected ConfigFactory $config;

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\autologout\AutologoutManagerInterface $autoLogout
   * @param \Drupal\Core\Config\ConfigFactory $config
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AutologoutManagerInterface $autoLogout, ConfigFactory $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->autoLogoutManager = $autoLogout;
    $this->config = $config;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   *
   * @return self
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ): self {
    return new self(
      $configuration
      , $plugin_id,
      $plugin_definition,
      $container->get('autologout.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * @return array
   */
  public function build(): array {
    $remainingTime = $this->autoLogoutManager->createTimer();
    $TimeoutPadding = $this->config->get('autologout.settings')->get('padding');
    $prefixText = $this->config->get('timer_auto_logout.settings')->get('prefix_text');
    $suffixText = $this->config->get('timer_auto_logout.settings')->get('suffix_text');
    $build['content'] = [
      '#remainingTime' => $remainingTime,
      '#prefix_text' => $this->t($prefixText),
      '#suffix_text' => $this->t($suffixText),
      '#timeoutPadding' => $TimeoutPadding,
      '#theme' => 'timer_auto_logout',
      '#attached' => [
        'library' => [
          'timer_auto_logout/timer_auto_logout',
        ],
      ],
    ];

    $build['reset_timer'] = [
      '#type' => 'button',
      '#value' => $this->t('Reset Timer'),
      '#attributes' => ['id' => 'timer_auto_logout_reset-timer'],
    ];

    return $build;
  }

  /**
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    // Only authenticated users can see this block.
    return AccessResult::allowedIf($account->isAuthenticated());
  }

}
