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
   * The autologout manager service.
   *
   * @var \Drupal\autologout\AutologoutManagerInterface
   */
  protected $autoLogoutManager;

  /**
   * Constructs the plugin instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AutologoutManagerInterface $autologout) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->autoLogoutManager = $autologout;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self($configuration, $plugin_id, $plugin_definition, $container->get('autologout.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $remainingTime = $this->autoLogoutManager->createTimer();
    $build['content'] = [
      '#remainingTime' => $remainingTime,
      '#theme' => 'timer_autoLogout',
    ];
    return $build;
  }

}
