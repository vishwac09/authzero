<?php

namespace Drupal\Tests\authzero\Unit;

use Drupal\authzero\Form\AuthZeroSettingsForm;
use Drupal\Tests\UnitTestCase;

/**
 * Test class for the AuthZeroSettingsForm.
 *
 * @group authzero_unit
 */
class AuthZeroSettingsFormTest extends UnitTestCase {

  /**
   * Reference to the ConfigForm Instance.
   *
   * @var configForm
   */
  private $configForm;

  /**
   * Do the initial setup.
   */
  public function setup(): void {
    parent::setUp();
    $this->configForm = $this->prophesize(AuthZeroSettingsForm::class);
    $this->configForm->getFormId()->willReturn('authzero_settings_form');
  }

  /**
   * Check the id of the config form.
   */
  public function testFormId() {
    $form = $this->configForm->reveal();
    $this->assertEquals('authzero_settings_form', $form->getFormId());
  }

}
