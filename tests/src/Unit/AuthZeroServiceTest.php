<?php

namespace Drupal\Tests\authzero\Unit;

use Drupal\authzero\Service\AuthZeroService;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Test class for the AuthZeroService class.
 *
 * @group authzero_unit
 */
class AuthZeroServiceTest extends UnitTestCase {

  /**
   * Instance of ConfigFactoryInterface.
   *
   * @var configFactory
   */
  private $configFactory;

  /**
   * Instance of ImmutableConfig.
   *
   * @var config
   */
  private $config;

  /**
   * Instance of AuthZeroService.
   *
   * @var authZeroService
   */
  private $authZeroService;

  /**
   * Do the initial setup.
   */
  public function setup(): void {
    parent::setUp();
    $this->config = $this->prophesize(ImmutableConfig::class);
    $this->config->get('domain')->willReturn('https://test.auth0.domain');
    $this->config->get('client_id')->willReturn('TEST_CLIENT_ID');
    $this->config->get('client_secret')->willReturn('TEST_CLIENT_SECRET');
    $this->config->get('callback_url')->willReturn('TEST_CALLBACK_URL');
    $this->config->get('post_login_url')->willReturn('/node');
    $this->config->get('override_logout')->willReturn(TRUE);
    // Initialize the ConfigFactoryInterface mock.
    $this->configFactory = $this->prophesize(ConfigFactoryInterface::class);
    // Will return instance of Immutable config.
    $this->configFactory->get('authzero.settings')->willReturn($this->config->reveal());
    // Inject the mock of ConfigFactoryInterface.
    $this->authZeroService = new AuthZeroService($this->configFactory->reveal());

    $container = new ContainerBuilder();
    \Drupal::setContainer($container);
    $request = $this->prophesize(Request::class);
    $request->getSchemeAndHttpHost()->willReturn('https://drupal.site');

    $requestStack = $this->prophesize(RequestStack::class);
    $requestStack->getCurrentRequest()->willReturn($request->reveal());
    $container->set('request_stack', $requestStack->reveal());
  }

  /**
   * Check the function return type getPostLoginRedirectLink.
   */
  public function testGetPostLoginRedirectLink() {
    $this->assertEquals('/node', $this->authZeroService->getPostLoginRedirectLink());
  }

  /**
   * Check the function return type negates getPostLoginRedirectLink.
   */
  public function testNegateGetPostLoginRedirectLink() {
    $this->assertNotEquals('/login', $this->authZeroService->getPostLoginRedirectLink());
  }

  /**
   * Check the function return type overrideLogout.
   */
  public function testOverrideLogout() {
    $this->assertTrue($this->authZeroService->overrideLogout());
  }

  /**
   * Must return keyed array with 'error' as property and null value.
   */
  public function testGetExtraParams() {
    $this->assertEquals(['error' => NULL],
            $this->authZeroService->getExtraParams());
    $this->assertEquals(['error' => 'unauthenticated'],
            $this->authZeroService->getExtraParams('unauthenticated'));
    $this->assertNotEquals(['error' => ''],
            $this->authZeroService->getExtraParams('unauthenticated'));
  }

  /**
   * Check the function return type overrideLogout.
   */
  public function testGetLogoutLink() {
    $config = $this->config->reveal();
    $link = 'https://%s/v2/logout?client_id=%s&federated=true&returnTo=%s?error_description=%s';
    $fullLink = sprintf(
      $link,
      $config->get('domain'),
      $config->get('client_id'),
      'https://drupal.site/auth0/login',
      ''
    );
    $this->assertEquals($fullLink, $this->authZeroService->getLogoutLink());
  }

}
