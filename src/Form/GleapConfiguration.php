<?php

namespace Drupal\gleap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;

/**
 * Class of GleapConfiguration.
 */
class GleapConfiguration extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gleap_configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'gleap.gleap_configuration',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Default settings.
    $config = $this->config('gleap.gleap_configuration');

    // Gleap API key.
    $form['gleap_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Gleap API key'),
      '#default_value' => $config->get('gleap_api_key'),
      '#description' => $this->t('Give your gleap API key.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('gleap.gleap_configuration');
    $config->set('gleap_api_key', $form_state->getValue('gleap_api_key'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

}
