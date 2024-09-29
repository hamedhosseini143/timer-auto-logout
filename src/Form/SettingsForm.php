<?php

declare(strict_types=1);

namespace Drupal\timer_auto_logout\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * @return string
   */
  public function getFormId(): string {
    return 'timer_auto_logout_settings';
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('timer_auto_logout.settings');
    $form['prefix_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prefix text'),
      '#default_value' => $this->t($config->get('prefix_text')),
    ];
    $form['suffix_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Suffix text'),
      '#default_value' => $this->t($config->get('suffix_text')),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return void
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('timer_auto_logout.settings');
    $config->set('prefix_text', $form_state->getValue('prefix_text'))
      ->set('suffix_text', $form_state->getValue('suffix_text'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * @return string[]
   */
  protected function getEditableConfigNames(): array {
    return ['timer_auto_logout.settings'];
  }

}
