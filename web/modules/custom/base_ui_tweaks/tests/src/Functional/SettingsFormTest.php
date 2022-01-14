<?php

namespace Drupal\Tests\base_ui_tweaks\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test description.
 *
 * @group base_ui_tweaks
 */
class SettingsFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['user', 'base_ui_tweaks'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
  }

  /**
   * Tests that the settings page is acessible by a privileged user.
   */
  public function testSettingsPage() {
    $account = $this->drupalCreateUser(['administer account settings']);
    $this->drupalLogin($account);

    $this->drupalGet('admin/config/user-interface/ui-tweaks/settings');
    $this->assertSession()
      ->statusCodeEquals(200);

    // Test the default configuration.
    $default_config = $this->config('base_ui_tweaks.settings')->get('wrap');
    $this->assertTrue($default_config);

    // Test the form element is checked by default.
    $this->assertSession()
      ->checkboxChecked('wrap');

    // Test form submission.
    $this->submitForm(['wrap' => FALSE], 'Save configuration');
    $this->assertSession()
      ->pageTextContains('The configuration options have been saved.');

    // Test the new value is there.
    $this->drupalGet('admin/config/user-interface/ui-tweaks/settings');
    $this->assertSession()
      ->statusCodeEquals(200);
    $this->assertSession()
      ->checkboxNotChecked('wrap');

    // Test the configuration has been updated.
    $new_config = $this->config('base_ui_tweaks.settings')->get('wrap');
    $this->assertNotEquals($default_config, $new_config);
  }

  /**
   * Tests that the settings page is NOT acessible by a non-privileged user.
   */
  public function testSettingsPageWithoutPermission() {
    $account = $this->drupalCreateUser(['access content']);
    $this->drupalLogin($account);

    $this->drupalGet('admin/config/user-interface/ui-tweaks/settings');
    $this->assertSession()->statusCodeEquals(403);
  }

}
