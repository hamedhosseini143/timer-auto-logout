<?php

declare(strict_types=1);

namespace Drupal\timer_autologout\Plugin\Block;

use Drupal\autologout\AutologoutManagerInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

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
  public function defaultConfiguration(): array {
    return [
      'prefix_text' => $this->t('Hello world!'),
      'suffix_text' => $this->t('Goodbye world!'),
    ];
  }

  /**
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['prefix_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('prefix text'),
      '#default_value' => $this->configuration['prefix_text'],
    ];
    $form['suffix_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('suffix text'),
      '#default_value' => $this->configuration['suffix_text'],
    ];
    return $form;
  }

  /**
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return void
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['prefix_text'] = $form_state->getValue('prefix_text');
    $this->configuration['suffix_text'] = $form_state->getValue('suffix_text');
  }

  /**
   * @return array
   */
  public function build(): array {
    $remainingTime = $this->autoLogoutManager->createTimer();
    $build['content'] = [
      '#remainingTime' => $remainingTime,
      '#prefix_text' => $this->configuration['prefix_text'],
      '#suffix_text' => $this->configuration['suffix_text'],
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
