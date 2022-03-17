<?php

namespace Drupal\authzero\Controller;

use Drupal\authzero\AuthZeroInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handler for Auth0 login/logout callbacks.
 */
class AuthZeroController extends ControllerBase implements AuthZeroInterface {

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
   * {@inheritDoc}
   */
  public function login(Request $request) {
    // Check if the current logged-in user is not anonymous.
    if ($this->currentUser->isAnonymous()) {
      // Check the error query param is set, if yes send it to
      // auth0 universal login page in URL.
      $query = $request->query->get('error');
      $errorCode = $query ?? '';
      // Get the instance of Auth0.
      $auth0 = $this->authZeroService->getInstance();
      $auth0->login(NULL, NULL, $this->authZeroService->getExtraParams($errorCode));
    }
    else {
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectLink());
    }
  }

  /**
   * {@inheritDoc}
   */
  public function auth0Callback(Request $request): RedirectResponse {
    if ($this->currentUser->isAnonymous()) {
      $errorCode = $request->query->get('error') ?? 'unauthorized';
      try {
        $auth0 = $this->authZeroService->getInstance();
        $user = $auth0->getUser();
        if (isset($user['email'])) {
          /** @var \Drupal\user\UserInterface $user */
          $user = user_load_by_mail($user['email']);
          if (!empty($user)) {
            user_login_finalize($user);
            \Drupal::messenger()->addStatus('Successfully logged in ' . $user->getEmail());
            return new RedirectResponse($this->authZeroService->getPostLoginRedirectLink());
          } else {
            return $this->logoutUser('access_denied');
          }
        }
      } catch (\Exception $e) {
        return $this->logoutUser($errorCode);
      }
    } else {
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectLink());
    }
  }

  /**
   * {@inheritDoc}
   */
  public function logout(): RedirectResponse {
    if (!empty($this->currentUser->getEmail())) {
      user_logout();
      return $this->logoutUser();
    }
    else {
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectLink());
    }
  }

  /**
   * {@inheritDoc}
   */
  public function logoutUser($error = NULL): TrustedRedirectResponse {
    return new TrustedRedirectResponse($this->authZeroService->getLogoutLink($error));
  }

}
