<?php

namespace Drupal\authzero\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AuthZero Settings Form class.
 */
class AuthZeroSettingsForm extends FormBase {

  /**
   * Instance of Drupal\Core\Routing\RouteProvider.
   *
   * @var \Drupal\Core\Routing\RouteProvider
   */
  protected RouteProvider $routeProvider;

  /**
   * Instance of Drupal\Core\State\StateInterface.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected StateInterface $state;

  /**
   * Instance of Drupal\Core\Messenger\Messenger.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected Messenger $showMessage;

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
    $instance->state = $container->get('state');
    $instance->showMessage = $container->get('messenger');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain'),
      '#description' => $this->t('Domain added in the Application page on auth0 platform.'),
      '#default_value' => $this->state->get('authzero.domain'),
      '#required' => TRUE,
    ];
    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Id'),
      '#description' => $this->t('Client Id associated with the Application.'),
      '#default_value' => $this->state->get('authzero.client_id'),
      '#required' => TRUE,
    ];
    $form['client_secret'] = [
      '#type' => 'password',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('Client Secret associated with the Application.'),
      '#default_value' => $this->state->get('authzero.client_secret'),
      '#required' => TRUE,
    ];
    $form['cookie_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookie Secret'),
      '#description' => $this->t('A long secret value used to encrypt the session cookie.'),
      '#default_value' => $this->state->get('authzero.cookie_secret'),
      '#suffix' => <<<STR
<div class="messages messages--warning">
  <span>You can generate a suitable string by running "openssl rand -hex 32" in your terminal.</span>
</div>
STR,
      '#required' => TRUE,
    ];
    $siteHost = \Drupal::request()->getSchemeAndHttpHost();
    $form['callback_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Callback URI'),
      '#description' => $this->t('The URI to redirect, after successfully authenticated by auth0 platform. This route will actually
        get the user information from auth0 and the authenticate/create the user in the Drupal system.'),
      '#default_value' => $this->state->get('authzero.callback_url') ?? "$siteHost/web/auth0/callback",
      '#disabled' => TRUE,
    ];
    $form['post_login_route'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Post login route name'),
      '#description' => $this->t('Path of the route to redirect after login.'),
      '#default_value' => $this->state->get('authzero.post_login_route') ?? '<front>',
      '#required' => TRUE,
    ];
    $form['post_logout_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Post logout URI (RETURN_TO path)'),
      '#description' => $this->t('Path of the route to redirect after logout from drupal. Make sure this value matches
        the "Allowed logout URL" value in auth0.com application setting.'),
      '#default_value' => $this->state->get('authzero.post_logout_url') ?? $siteHost,
      '#required' => TRUE,
    ];
    $form['override_logout'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Override default logout action ?'),
      '#description' => <<<STR
        Need to logout user from Auth0 platform as well as Drupal.</br>
        Checking this will override the normal url callback when clicked on logout.
        The module also provides an additional route "<strong>authzero.logout(/auth0/logout)</strong>" to handle user logout.'
STR,
      '#suffix' => <<<STR
<div class="messages messages--warning">
  <span>Do not forget to rebuild routes by clearing caches on checking this option.</span>
</div>
STR,
      '#default_value' => $this->state->get('authzero.override_logout') ?? 0,
      '#required' => FALSE,
    ];
    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
      ],
    ];
    return $form;
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
    $postLoginRoute = $this->routeProvider->getRouteByName(
      $form_state->getValue('post_login_route')
    );
    $this->state->setMultiple(
      [
        'authzero.callback_url' => $form_state->getValue('callback_url'),
        'authzero.client_id' => $form_state->getValue('client_id'),
        'authzero.client_secret' => $form_state->getValue('client_secret'),
        'authzero.cookie_secret' => $form_state->getValue('cookie_secret'),
        'authzero.domain' => $form_state->getValue('domain'),
        'authzero.post_login_route' => $form_state->getValue('post_login_route'),
        'authzero.post_login_url' => $postLoginRoute->getPath(),
        'authzero.post_logout_url' => $form_state->getValue('post_logout_url'),
        'authzero.override_logout' => $form_state->getValue('override_logout'),
      ]
    );
    $this->showMessage->addStatus($this->t('Auth0 account settings saved successfully.'));
  }

}
