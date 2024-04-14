<?php

namespace Drupal\Tests\authzero\Unit;

use Drupal\authzero\Service\AuthZeroService;
use Drupal\Core\State\State;
use Drupal\Tests\UnitTestCase;

/**
 * Test class for the AuthZeroService class.
 *
 * @group authzero_unit
 */
class AuthZeroServiceTest extends UnitTestCase {
  /**
   * Instance of \Drupal\Core\State\State.
   *
   * @var authzeroSettings
   */
  private $state;

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
    $this->state = $this->prophesize(State::class);
    $this->state->get('authzero.domain')->willReturn('https://test.auth0.domain');
    $this->state->get('authzero.client_id')->willReturn('TEST_CLIENT_ID');
    $this->state->get('authzero.client_secret')->willReturn('TEST_CLIENT_SECRET');
    $this->state->get('authzero.callback_url')->willReturn('TEST_CALLBACK_URL');
    $this->state->get('authzero.post_login_url')->willReturn('/node');
    $this->state->get('authzero.post_logout_url')->willReturn('/logout');
    $this->state->get('authzero.override_logout')->willReturn(TRUE);
    // Inject the mock of ConfigFactoryInterface.
    $this->authZeroService = new AuthZeroService($this->state->reveal());
  }

  /**
   * Check the function return value getDomain.
   */
  public function testGetDomain() {
    $this->assertEquals('https://test.auth0.domain', $this->authZeroService->getDomain());
  }

  /**
   * Check the function return type getPostLoginRedirectUrl.
   */
  public function testGetPostLoginRedirectUrl() {
    $this->assertEquals('/node', $this->authZeroService->getPostLoginRedirectUrl());
  }

  /**
   * Check the function return type getPostLogoutRedirectUrl.
   */
  public function testGetPostLogoutRedirectUrl() {
    $this->assertEquals('/logout', $this->authZeroService->getPostLogoutRedirectUrl());
  }

  /**
   * Check the function return type negates getPostLoginRedirectUrl.
   */
  public function testNegateGetPostLoginRedirectUrl() {
    $this->assertNotEquals('/login', $this->authZeroService->getPostLoginRedirectUrl());
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

}
