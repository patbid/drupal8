<?php

namespace Drupal\reusable_forms\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Defines the BasicForm class.
 */
class EmailForm extends ReusableFormBase {

    /**
     * {@inheritdoc}.
     */
    public function getFormId() {
        return 'email_form';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['email'] = array(
            '#type' => 'email',
            '#title' => $this->t('Your email'),
            '#description' => t('Enter email to subscribe'),
            '#size' => '50',
            '#maxlength' => '60',
            '#attributes' => array('placeholder' => 'Enter email to subscribe'),
        );

        $form['actions']['submit']['#value'] = "subscribe";

        $form = parent::buildForm($form, $form_state);

        return $form;
    }


    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $email = $form_state->getValue('email');
        if (empty($email)) {
            $form_state->setErrorByName('email', t('Email needed'));
        }
        $db = \Drupal::database();

        $res = $db->select('emails','s')
                ->fields('s', array('id'))
                ->condition('s.email', $email)
                ->range(0, 1)
                ->execute()
                ->fetchField();
        if (!empty($res)) {
            $form_state->setErrorByName('email', t('You are already subscribed'));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $mail = $form_state->getValue('email');
        $db = \Drupal::database();

        $res = $db->insert('emails')->fields(
                    array(
                      'email' => $mail
                    )
                )->execute();

    }
}
