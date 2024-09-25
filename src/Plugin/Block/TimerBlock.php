<?php

declare(strict_types=1);

namespace Drupal\timer_auto_logout\Plugin\Block;

use Drupal\autologout\AutologoutManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
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
//    $paddingTime = $this->autoLogoutManager->
    //TODO: get padding time from autologout manager
    $build['content'] = [
      '#remainingTime' => $remainingTime,
      '#prefix_text' => $this->configuration['prefix_text'],
      '#suffix_text' => $this->configuration['suffix_text'],
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
