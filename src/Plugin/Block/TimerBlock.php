<?php

declare(strict_types=1);

namespace Drupal\timer_autologout\Plugin\Block;

use Drupal\autologout\AutologoutManagerInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a timer auto logout block.
 *
 * @Block(
 *   id = "timer_autologout_timer",
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
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\autologout\AutologoutManagerInterface $autoLogout
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AutologoutManagerInterface $autoLogout) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->autoLogoutManager = $autoLogout;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   *
   * @return self
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self($configuration, $plugin_id, $plugin_definition, $container->get('autologout.manager'));
  }

  /**
   * @return array
   */
  public function build(): array {
    $remainingTime = $this->autoLogoutManager->createTimer();
    $build['content'] = [
      '#remainingTime' => $remainingTime,
      '#theme' => 'timer_autoLogout',
      '#attached' => [
        'library' => [
          'timer_autologout/timer_auto_logout',
        ],
      ],
    ];
    return $build;
  }

}
