<?php

namespace Drupal\authzero\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handler for Auth0 login/logout callbacks.
 */
class AuthZeroController extends ControllerBase {

  /**
   * Instance of Drupal\Core\Session\AccountProxy.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Instance of Drupal\auth0_drupal\Service\AuthZeroService.
   *
   * @var \Drupal\authzero\Service\AuthZeroService
   */
  protected $authZeroService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->currentUser = $container->get('current_user');
    $instance->authZeroService = $container->get('authzero.authzero_service');
    return $instance;
  }

  /**
   * Handles redirecting to auth0 login page.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @throws \Auth0\SDK\Exception\CoreException
   *   Any misconfiguration will throw the Auth0 Exception.
   */
  public function login(Request $request) {
    if ($this->currentUser->isAnonymous()) {
      $query = $request->query->get('error');
      $errorCode = $query ?? '';
      $auth0 = $this->authZeroService->getInstance();
      $auth0->login(NULL, NULL, $this->authZeroService->getExtraParams($errorCode));
    }
    else {
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectLink());
    }
  }

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
  public function auth0Callback(Request $request): RedirectResponse {
    if ($this->currentUser->isAnonymous()) {
      $errorCode = $request->query->get('error') ?? 'unauthorized';
      try {
        $auth0 = $this->authZeroService->getInstance();
        $user = $auth0->getUser();
        if (isset($user['email'])) {
          /** @var \Drupal\user\UserInterface $omhUser */
          $user = user_load_by_mail($user['email']);
          if (isset($user)) {
            user_login_finalize($user);
            return new RedirectResponse($this->authZeroService->getPostLoginRedirectLink());
          } else {
            return $this->logoutUser('access denied');
          }
        }
      } catch (\Exception $e) {
        return $this->logoutUser('unauthorized');
      }
    } else {
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectLink());
    }
  }

  /**
   * Handles user logout, from OMH as well as Auth0.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Logout user from drupal and redirect to auth0 logout.
   */
  public function logout(): RedirectResponse {
    if (!empty($this->currentUser->getEmail())) {
      user_logout();
      return $this->logoutUser();
    }
    else {
      return new RedirectResponse('/');
    }
  }

  /**
   * Force user to logout.
   *
   * @return Symfony\Component\HttpFoundation\TrustedRedirectResponse
   *   Redirect to Auth0 logout link.
   */
  public function logoutUser($error = NULL): TrustedRedirectResponse {
    return new TrustedRedirectResponse($this->authZeroService->getLogoutLink($error));
  }

}
