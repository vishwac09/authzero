<?php

namespace Drupal\authzero\Controller;

use Drupal\authzero\AuthZeroInterface;
use Drupal\authzero\Service\AuthZeroService;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handler for Auth0 login/logout callbacks.
 */
class AuthZeroController extends ControllerBase implements AuthZeroInterface {

  /**
   * Instance of Drupal\auth0_drupal\Service\AuthZeroService.
   *
   * @var \Drupal\authzero\Service\AuthZeroService
   */
  protected AuthZeroService $authZeroService;

  /**
   * Instance of Drupal\Core\Session\AccountProxy.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Instance of \Drupal\Core\Logger\LoggerChannelFactory.
   *
   * @var Drupal\Core\Logger\LoggerChannelFactory
   */
  protected LoggerChannelFactory $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->currentUser = $container->get('current_user');
    $instance->authZeroService = $container->get('authzero.authzero_service');
    $instance->logger = $container->get('logger.factory');
    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  public function auth0Callback(Request $request): RedirectResponse {
    // When the user serssion has not been started in drupal.
    if ($this->currentUser->isAnonymous()) {
      $errorCode = $request->query->get('error') ?? 'unauthorized';
      $code = $request->query->get('code') ?? NULL;
      $state = $request->query->get('state') ?? NULL;
      try {
        $user = $state && $code ? $this->authZeroService->getLoggedInUserDetails() : NULL;
        if ($user && isset($user['email'])) {
          \Drupal::moduleHandler()->invokeAll('authzero_pre_validate_user', [$user]);
          /** @var \Drupal\user\UserInterface | false $user */
          $user = user_load_by_mail($user['email']);
          if (!empty($user)) {
            user_login_finalize($user);
            $this->logger->get('authzero')->info("User {$user->getInitialEmail()} successfully logged in.");
            return new RedirectResponse($this->authZeroService->getPostLoginRedirectUrl());
          }
          else {
            return $this->logoutUser('access_denied');
          }
        }
      }
      catch (\Exception $e) {
        return $this->logoutUser($errorCode);
      }
    }
    else {
      // If the user visits the callback url when there is an active session
      // normally redirect the user to the postLoginREdirectLink.
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectUrl());
    }
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
      // Error handler to hadle unseen cases.
      try {
        // Get the Auth0 login url and redirect the user to Authenticate.
        $loginURL = $this->authZeroService->initiateLogin($errorCode);
        return new TrustedRedirectResponse($loginURL);
      }
      catch (\Exception $e) {
        // Return the user to homepage in case of error.
        return new RedirectResponse('/');
      }
    }
    else {
      // Do not allow logged in user to navigate to the Auth0 login page.
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectUrl());
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
      return new RedirectResponse($this->authZeroService->getPostLoginRedirectUrl());
    }
  }

  /**
   * {@inheritDoc}
   */
  public function logoutUser($error = NULL): TrustedRedirectResponse {
    $logoutUrl = $this->authZeroService->initiateLogout();
    return new TrustedRedirectResponse(
      $logoutUrl
    );
  }

}
