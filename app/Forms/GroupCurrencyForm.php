<?php

namespace App\Forms;

use Config;
use Kris\LaravelFormBuilder\Form;

class GroupCurrencyForm extends Form
{
    public function buildForm()
    {
        $this
        // Don't worry about this-- It's just here to help with adding
        // this form to the larger "Create Submission" form!
        ->add('start', 'hidden', [
            'value' => ''
        ]);

        foreach(Config::get('lorekeeper.group_currency_form') as $key=>$field) {
            $this->add($key, $field['type'], [
                'label' => $field['label'],
                'choices' => isset($field['choices']) ? $field['choices'] : null,
                'choice_options' => ['wrapper' => ['class' => 'choice-wrapper']],
                'label_attr' => ['class' => 'label-class'],
                'expanded' => true,
                'multiple' => isset($field['multiple']) ? $field['multiple'] : null,
                'value' => isset($field['value']) ? $field['value'] : null,
                'checked' => false,
                'rules' => isset($field['rules']) ? $field['rules'] : null,
            ]);
        }
    }
}
