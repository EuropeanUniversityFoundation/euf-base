<?php

namespace Drupal\base_ui_tweaks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure base_ui_tweaks settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'base_ui_tweaks_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['base_ui_tweaks.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $config = $this->config('base_ui_tweaks.settings');

    $form['wrap'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Wrap user account form item in a details element.'),
      '#default_value' => $config->get('base_ui_tweaks.wrap'),
      '#return_value' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('base_ui_tweaks.settings');
    $wrap = $form_state->getValue('wrap');
    $config->set('base_ui_tweaks.wrap', $wrap);
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
