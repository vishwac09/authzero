<?php

namespace Drupal\authzero\Service;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Drupal\Core\State\StateInterface;

/**
 * Set of utility functions.
 */
class AuthZeroService {

  /**
   * The state interface where authzero settings are stored.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected StateInterface $authzeroSettings;

  /**
   * Defining constructor for Auth0.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The config factory object.
   */
  public function __construct(StateInterface $state) {
    $this->authzeroSettings = $state;
  }

  /**
   * Return the domain of the application setup in authzero setting form.
   *
   * @return string|null
   *   The domain of the application.
   */
  public function getDomain(): string {
    return $this->authzeroSettings->get('domain');
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
   * Returns the Auth0 instance.
   *
   * @throws \Auth0\SDK\Exception\CoreException
   *   The Auth0 exception.
   */
  public function getInstance(): Auth0 {
    $configuration = new SdkConfiguration(
      domain: $this->authzeroSettings->get('domain'),
      clientId: $this->authzeroSettings->get('client_id'),
      clientSecret: $this->authzeroSettings->get('client_secret'),
      redirectUri: $this->authzeroSettings->get('callback_url'),
      cookieSecret: $this->authzeroSettings->get('cookie_secret')
    );
    return new Auth0($configuration);
  }

  /**
   * Return the route to redirect to after login.
   *
   * @return string
   *   Redirect to the configured url after logging in.
   */
  public function getPostLoginRedirectLink(): string {
    return $this->authzeroSettings->get('post_login_url');
  }

  /**
   * Return the url to redirect to after logout.
   *
   * @return string
   *   Redirect to the configured url after lgging out.
   */
  public function getPostLogoutRedirectLink(): string {
    return $this->authzeroSettings->get('post_logout_url');
  }

  /**
   * Determine if the Route needs to be overridden.
   *
   * @return bool
   *   Return true
   */
  public function overrideLogout() : bool {
    return (bool) $this->authzeroSettings->get('override_logout');
  }

}
