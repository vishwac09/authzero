<?php

namespace Drupal\authzero\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * AuthZero Settings Form class.
 */
class AuthZeroSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'authzero.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'authzero_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('authzero.settings');
    $form['domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain'),
      '#description' => $this->t('Domain added in the Application page on auth0 platform.'),
      '#default_value' => $config->get('domain'),
      '#required' => TRUE
    ];
    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Id'),
      '#description' => $this->t('Client Id associated with the Application.'),
      '#default_value' => $config->get('client_id'),
      '#required' => TRUE
    ];
    $form['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('Client Secret associated with the Application.'),
      '#default_value' => $config->get('client_secret'),
      '#required' => TRUE
    ];
    $form['redirect_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect URI'),
      '#description' => $this->t('The URI to redirect, after successfully authenticated by auth0 platform.'),
      '#default_value' => $config->get('redirect_uri') ?? \Drupal::request()->getSchemeAndHttpHost() . '/auth0/callback',
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('authzero.settings')
      ->set('domain', $form_state->getValue('domain'))
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('client_secret', $form_state->getValue('client_secret'))
      ->set('callback_url', $form_state->getValue('redirect_uri'))
      ->save();
  }
}
