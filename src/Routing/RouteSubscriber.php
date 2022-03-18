<?php

namespace Drupal\authzero\Routing;

use Drupal\authzero\Service\AuthZeroService;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Instance of Drupal\auth0_drupal\Service\AuthZeroService.
   *
   * @var \Drupal\authzero\Service\AuthZeroService
   */
  protected $authZeroService;

  /**
   * Defining constructor for Auth0.
   *
   * @param \Drupal\authzero\Service\AuthZeroService $authZeroService
   *   The AuthZero Service object.
   */
  public function __construct(AuthZeroService $authZeroService) {
    $this->authZeroService = $authZeroService;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    if ($route = $collection->get('user.logout') && $this->authZeroService->overrideLogout()) {
      $route->setDefaults(
        [
          '_controller' => '\Drupal\authzero\Controller\AuthZeroController::logout',
        ]
      );
      $route->setOptions(
        [
          'no_cache' => 'TRUE',
        ]
      );
    }
  }

}
