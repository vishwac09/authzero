<?php

namespace Drupal\authzero\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AuthZero Settings Form class.
 */
class AuthZeroSettingsForm extends ConfigFormBase {

  /**
   * Instance of the Drupal\Core\Routing\RouteProvider.
   *
   * @var \Drupal\Core\Routing\RouteProvider
   */
  protected $routeProvider;

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
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->routeProvider = $container->get('router.route_provider');
    return $instance;
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
      '#required' => TRUE,
    ];
    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Id'),
      '#description' => $this->t('Client Id associated with the Application.'),
      '#default_value' => $config->get('client_id'),
      '#required' => TRUE,
    ];
    $form['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('Client Secret associated with the Application.'),
      '#default_value' => $config->get('client_secret'),
      '#required' => TRUE,
    ];
    $form['redirect_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect URI'),
      '#description' => $this->t('The URI to redirect, after successfully authenticated by auth0 platform.'),
      '#default_value' => $config->get('redirect_uri') ?? \Drupal::request()->getSchemeAndHttpHost() . '/auth0/callback',
      '#required' => TRUE,
    ];
    $form['post_login_route'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Route name'),
      '#description' => $this->t('Name of the route to redirect after login.'),
      '#default_value' => $config->get('post_login_route') ?? '<front>',
      '#required' => TRUE,
    ];
    $form['override_logout'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Override default logout action?'),
      '#description' => <<<STR
        Need to logout user from Auth0 platform as well as Drupal.</br>
        Checking this will override the normal url callback when clicked on logout.
        The module also provides an additional route "<strong>authzero.logout(/auth0/logout)</strong>" to handle user logout.'
STR,
      '#suffix' => <<<STR
<div class="messages messages--warning">
  <strong>Do not forget to rebuild routes by clearing caches on checking this option.</strong>
</div>
STR,
      '#default_value' => $config->get('override_logout') ?? 0,
      '#required' => FALSE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $postLoginRoute = $form_state->getValue('post_login_route');
    try {
      $this->routeProvider->getRouteByName($postLoginRoute);
    }
    catch (\Exception $e) {
      $form_state->setErrorByName('post_login_route', $e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $postLoginRoute = $this->routeProvider->getRouteByName(
      $form_state->getValue('post_login_route')
    );
    $this->config('authzero.settings')
      ->set('domain', $form_state->getValue('domain'))
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('client_secret', $form_state->getValue('client_secret'))
      ->set('callback_url', $form_state->getValue('redirect_uri'))
      ->set('post_login_route', $form_state->getValue('post_login_route'))
      ->set('post_login_url', $postLoginRoute->getPath())
      ->set('override_logout', $form_state->getValue('override_logout'))
      ->save();
  }

}
