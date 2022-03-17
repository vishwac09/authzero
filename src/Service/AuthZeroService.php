<?php

namespace Drupal\authzero\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

use Auth0\SDK\Auth0;

/**
 * Set of utility functions.
 */
class AuthZeroService {

  /**
   * The authZero Settings.
   *
   * @var array
   */
  protected $auth0;

  /**
   * Defining constructor for Auth0.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory object.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->auth0 = $config_factory->get('authzero.settings');
  }

  /**
   * Returns the Auth0 instance.
   *
   * @throws \Auth0\SDK\Exception\CoreException
   *   The Auth0 exception.
   */
  public function getInstance(): Auth0 {
    return new Auth0([
      'domain' => $this->auth0->get('domain'),
      'client_id' => $this->auth0->get('client_id'),
      'client_secret' => $this->auth0->get('client_secret'),
      'redirect_uri' => $this->auth0->get('callback_url'),
      'scope' => 'openid profile email',
      'protocol' => 'oauth2',
    ]);
  }

  /**
   * List of params that needs to be sent to the auth0 universal login page.
   *
   * @param string|null $error
   *   The error message code.
   *
   * @return string[]
   *   Params to be sent.
   */
  public function getExtraParams(string $error = NULL): array {
    return [
      'error' => $error,
    ];
  }

  /**
   * User logout Link, for auth0.
   *
   * @param string|null $error
   *   The error messages.
   *
   * @return string
   *   The logout link.
   */
  public function getLogoutLink(string $error = NULL): string {
    return sprintf(
      'https://%s/v2/logout?client_id=%s&federated=true&returnTo=%s?error_description=%s',
      $this->auth0->get('domain'),
      $this->auth0->get('client_id'),
      \Drupal::request()->getSchemeAndHttpHost() . '/auth0/login',
      $error
    );
  }

  /**
   * Return the route to redirect to after login.
   *
   * @return string
   *   Redirect to the configured url after logging in.
   */
  public function getPostLoginRedirectLink(): string {
    return $this->auth0->get('post_login_url');
  }

  /**
   * Determine if the Route needs to be overridden.
   *
   * @return bool
   *   Return true
   */
  public function overrideLogout() : bool {
    return (bool) $this->auth0->get('post_login_url');
  }

}
