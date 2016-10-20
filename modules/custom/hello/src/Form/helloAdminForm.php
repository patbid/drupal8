<?php

/**
 *
 */

namespace Drupal\hello\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class helloAdminForm extends ConfigFormBase{

    public function getFormID() {
        return 'hello_admin_form';
    }

    protected function getEditableConfigNames()
    {
        // TODO: Implement getEditableConfigNames() method.
        return ['hello.config'];
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $var= \Drupal::state()->get('mytimet');
        if ($var){
            $form['result'] = array(
                '#type' => 'markup',
                '#title' => 'Last saved time',
            );
            $form['result'] = array(
                '#type'     => 'item',
                '#markup'   => "Last saved time : <strong>".date("d-m-Y H:i", $var)."</strong>",
            );

        }
         $form['main_color'] = array(
            '#type' => 'select',
            '#title' => 'Choose background color',
            '#default_value' => $this->config('hello.config')->get('color'),
            '#options' => array('green' => t('Vert'), 'orange' => t('Orange'), 'blue' => t('Bleu')),
        );

        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface$form_state){
        $color = $form_state->getValue('main_color');
        $this->config('hello.config')->set('color', $color)->save();
        \Drupal::state()->set('mytimet', REQUEST_TIME);
        parent::submitForm($form, $form_state);
    }
}

