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
    return $this->authzeroSettings->get('authzero.domain');
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
      domain: $this->authzeroSettings->get('authzero.domain'),
      clientId: $this->authzeroSettings->get('authzero.client_id'),
      clientSecret: $this->authzeroSettings->get('authzero.client_secret'),
      redirectUri: $this->authzeroSettings->get('authzero.callback_url'),
      cookieSecret: $this->authzeroSettings->get('authzero.cookie_secret')
    );
    return new Auth0($configuration);
  }

  /**
   * Return the user details, after successfully validated by auth0 portal.
   */
  public function getLoggedInUserDetails(): array|null {
    $auth0 = $this->getInstance();
    $auth0->exchange();
    return $auth0->getUser();
  }

  /**
   * Return the route to redirect to after login.
   *
   * @return string
   *   Redirect to the configured url after logging in.
   */
  public function getPostLoginRedirectUrl(): string {
    return $this->authzeroSettings->get('authzero.post_login_url');
  }

  /**
   * Return the url to redirect to after logout.
   *
   * @return string
   *   Redirect to the configured url after lgging out.
   */
  public function getPostLogoutRedirectUrl(): string {
    return $this->authzeroSettings->get('authzero.post_logout_url');
  }

  /**
   * Returns the auth0 app login url, to redirect the user's to authenticate.
   *
   * @param string|null $error
   *   The error code to be sent to auth0 login page.
   *
   * @return string
   *   The auth0 portal login url.
   */
  public function initiateLogin(string|null $error = NULL): string | NULL {
    $auth0 = $this->getInstance();
    $auth0->clear();
    $additionalParams = $this->getExtraParams($error);
    return $auth0->login(NULL, $additionalParams);
  }

  /**
   * Returns the logout url, to redirect the user's to end their session.
   *
   * @param string|null $error
   *   The error code to be sent to auth0 login page.
   *
   * @return string
   *   The auth0 portal login url.
   */
  public function initiateLogout(string|null $error = NULL): string {
    $auth0 = $this->getInstance();
    $additionalParams = $this->getExtraParams($error);
    $returnToUrl = $this->getPostLogoutRedirectUrl();
    return $auth0->logout($returnToUrl, $additionalParams);
  }

  /**
   * Determine if the Route needs to be overridden.
   *
   * @return bool
   *   Return true
   */
  public function overrideLogout() : bool {
    return (bool) $this->authzeroSettings->get('authzero.override_logout');
  }

}
