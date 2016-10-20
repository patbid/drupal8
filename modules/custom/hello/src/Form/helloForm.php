<?php

/**
 *
 */

namespace Drupal\hello\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class helloForm extends FormBase{

    public function getFormID() {
        return 'hello_form';
    }

    public function buildform(array $form, FormStateInterface $form_state){
        if(!empty($form_state->getTemporary())) {
            $form['result'] = array(
                '#type' => 'markup',
                '#title' => 'Résultat',
                '#size' => '120',
                '#maxlength' => '120',
            );
            $form['result'] = array(
                '#type'     => 'item',
                '#markup'   => $form_state->getTemporary()['result'],
            );
        }
        $form['first_value'] = array(
            '#type' => 'textfield',
            '#title' => 'First Value',
            '#description' => t('Enter first value'),
            '#size' => '60',
            '#maxlength' => '120',
            '#attributes' => array('placeholder' => 'Your first value'),
            '#ajax' => array(
                'callback' => array($this, 'validateFirstAjax'),
                'event' => 'change',
            ),
            '#suffix' => '<span class="text-message"></span>',
        );
        $form['operation'] = array(
            '#type' => 'radios',
            '#title' => t('Operation'),
            '#default_value' => 0,
            '#options' => array(0 => t('Ajouter'), 1 => t('Soustract'), 2 => t('Multiply'), 3 => t('Divide')),
            '#description' => t('Choose operation for processing'),
        );
        $form['second_value'] = array(
            '#type' => 'textfield',
            '#title' => 'Second Value',
            '#description' => t('Enter second value'),
            '#size' => '60',
            '#maxlength' => '120',
            '#attributes' => array('placeholder' => 'Your second value'),
            '#ajax' => array(
                'callback' => array($this, 'validateSecondAjax'),
                'event' => 'change',
            ),
            '#suffix' => '<span class="text2-message"></span>',
        );
        $form['check_third'] = array(
            '#type' => 'checkbox',
            '#title' => 'display third value',
        );
        $form['third_value'] = array(
            '#type' => 'textfield',
            '#title' => 'Third Value',
            '#description' => t('Enter third value'),
            '#size' => '60',
            '#maxlength' => '120',
            '#attributes' => array('placeholder' => 'Your third value'),
            '#states' => array(
                'visible' => array(
                    '#edit-check-third' => array('checked' => TRUE)
                ),
            )
        );
        $form['outprint'] = array(
            '#type' => 'select',
            '#title' => 'Choisissez votre affichage du résultat',
            '#default_value' => 0,
            '#options' => array(0 => t('Formulaire'), 1 => t('message'), 2 => t('Page')),
        );
        $form['bouton_submit'] = array(
            '#type' => 'submit',
            '#value' => t('Calculate'),
        );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $value1 = $form_state->getValue('first_value');
        $value2 = $form_state->getValue('second_value');
        $op = $form_state->getValue('operation');
        if (!is_numeric($value1)) {
            $form_state->setErrorByName('first_value', t('1ère valeur non numérique'));
        }
        if (!is_numeric($value2)) {
            $form_state->setErrorByName('second_value', t('2è valeur non numérique'));
        }
        if ($op == 3 and $value2 == 0) {
            $form_state->setErrorByName('operation', t('division par 0 impossible'));
        }
    }

    public function validateFirstAjax(array &$form, FormStateInterface $form_state)
    {
        $value1 = $form_state->getValue('first_value');
        if (!is_numeric($value1)) {
            $css = ['border' => '2px solid red'];
            $message = 'Cette valeur doit être numérique';
        }
        else {
            $css = ['border' => '2px solid green'];
        }
        $response = new AjaxResponse();
        $response->addCommand(new CssCommand('#edit-first-value',$css));
        $response->addCommand(new HtmlCommand('.text-message', $message));
        return $response;
    }

    public function validateSecondAjax(array &$form, FormStateInterface $form_state)
    {
        $value2 = $form_state->getValue('second_value');
        if (!is_numeric($value2)) {
            $css = ['border' => '2px solid red'];
            $message = 'Cette valeur doit être numérique';
        }
        else {
            $css = ['border' => '2px solid green'];
        }
        $response = new AjaxResponse();
        $response->addCommand(new CssCommand('#edit-second-value',$css));
        $response->addCommand(new HtmlCommand('.text2-message', $message));
        return $response;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $value1 = $form_state->getValue('first_value');
        $value2 = $form_state->getValue('second_value');
        $op = $form_state->getValue('operation');
        $page = $form_state->getValue('outprint');
        switch($op) {
            case 0:
                $result = $value1 + $value2;
                break;
            case 1:
                $result = $value1 - $value2;
                break;
            case 2:
                $result = $value1 * $value2;
                break;
            case 3:
                $result = $value1 / $value2;
                break;
        }
        switch($page) {
            case 0:
                $form_state->setTemporary(array('result' => '<span style="color: darkred;">Le résultat est <strong>'.$result.'</strong></span>'));
                $form_state->setRebuild();
                break;
            case 1:
                drupal_set_message('résultat = '.$result);
                break;
            case 2:
                $tabop = array('+', '-', '*','/');
                $form_state->setRedirect('hello.resultat',array ('value1' => $value1, 'value2' => $value2, 'op' => $tabop[$op], 'result' => $result));
                break;
        }

    }
}

