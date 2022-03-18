<?php

namespace Drupal\authzero;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides an Interface to handle user authentication via auth0 IDP.
 */
interface AuthZeroInterface {

  /**
   * Handles redirecting to auth0 login page.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @throws \Auth0\SDK\Exception\CoreException
   *   Any misconfiguration will throw the Auth0 Exception.
   */
  public function login(Request $request);

  /**
   * Call back function, invoked when user is authenticated by Auth0.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return Symfony\Component\HttpFoundation\RedirectResponse
   *   The location to redirect after login.
   *
   * @throws \Auth0\SDK\Exception\ApiException
   *   Any misconfiguration will throw the Auth0 Exception.
   * @throws \Auth0\SDK\Exception\CoreException
   *   Any misconfiguration will throw the Auth0 Exception.
   */
  public function auth0Callback(Request $request): RedirectResponse;

  /**
   * Handles user logout, from Drupal as well as Auth0.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Logout user from drupal and redirect to auth0 logout.
   */
  public function logout(): RedirectResponse;

  /**
   * Force user to logout.
   *
   * @return Symfony\Component\HttpFoundation\TrustedRedirectResponse
   *   Redirect to Auth0 logout link.
   */
  public function logoutUser($error = NULL): TrustedRedirectResponse;

}
