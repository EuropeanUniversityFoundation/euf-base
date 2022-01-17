<?php

namespace Drupal\Tests\base_ui_tweaks\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Url;

/**
 * Test description.
 *
 * @group base_ui_tweaks
 */
class UserFormTest extends BrowserTestBase {

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
   * Tests the user form is altered by default with the module enabled.
   */
  public function testUserFormAlter() {
    $account = $this->drupalCreateUser(['access content']);
    $this->drupalLogin($account);

    $args = ['user' => $account->id()];
    $this->drupalGet(Url::fromRoute('entity.user.edit_form', $args));
    $this->assertSession()
      ->statusCodeEquals(200);
    $this->assertSession()
      ->elementExists('xpath', "//details[@id = 'edit-account']");
  }

  /**
   * Tests the user form is not altered when the config is changed.
   */
  public function testUserFormRevertAlter() {
    $config = $this->config('base_ui_tweaks.settings');
    $config->set('wrap', FALSE);
    $config->save();

    $account = $this->drupalCreateUser(['access content']);
    $this->drupalLogin($account);

    $args = ['user' => $account->id()];
    $this->drupalGet(Url::fromRoute('entity.user.edit_form', $args));
    $this->assertSession()
      ->statusCodeEquals(200);
    $this->assertSession()
      ->elementNotExists('xpath', "//details[@id = 'edit-account']");
  }

  /**
   * Tests the user register form is altered by default with the module enabled.
   */
  public function testUserRegisterFormAlter() {
    $account = $this->drupalCreateUser(['administer users']);
    $this->drupalLogin($account);

    $element = "//details[@id = 'edit-account']";
    $this->drupalGet('admin/people/create');
    $this->assertSession()
      ->statusCodeEquals(200);
    $this->assertSession()
      ->elementExists('xpath', $element);
    $this->assertSession()
      ->elementAttributeExists('xpath', $element, 'open');
    $this->assertSession()
      ->elementAttributeContains('xpath', $element, 'required', 'required');
  }

  /**
   * Tests the user register form is not altered when the config is changed.
   */
  public function testUserRegisterFormRevertAlter() {
    $config = $this->config('base_ui_tweaks.settings');
    $config->set('wrap', FALSE);
    $config->save();

    $account = $this->drupalCreateUser(['administer users']);
    $this->drupalLogin($account);

    $this->drupalGet('admin/people/create');
    $this->assertSession()
      ->statusCodeEquals(200);
    $this->assertSession()
      ->elementNotExists('xpath', "//details[@id = 'edit-account']");
  }

}
