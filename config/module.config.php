<?php

namespace Circlical\TailwindForms;

use Circlical\TailwindForms\Factory\ThemedFormDelegatorFactory;
use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\Form\View\Helper\FormButton;
use Circlical\TailwindForms\Form\View\Helper\FormElement;
use Circlical\TailwindForms\Form\View\Helper\FormElementErrors;
use Circlical\TailwindForms\Form\View\Helper\FormInput;
use Circlical\TailwindForms\Form\View\Helper\FormRow;
use Circlical\TailwindForms\Form\View\Helper\FormText;
use Circlical\TailwindForms\View\Helper\AlpineFormBindings;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'view_helpers' => [
        'aliases' => [
            'alpineBindings' => AlpineFormBindings::class,
            'formbutton' => FormButton::class,
            'form_button' => FormButton::class,
            'formButton' => FormButton::class,
            'FormButton' => FormButton::class,
            'form_element' => FormElement::class,
            'formelement' => FormElement::class,
            'formElement' => FormElement::class,
            'FormElement' => FormElement::class,
            'form_element_errors' => FormElementErrors::class,
            'formelementerrors' => FormElementErrors::class,
            'formElementErrors' => FormElementErrors::class,
            'FormElementErrors' => FormElementErrors::class,
            'forminput' => FormInput::class,
            'form_input' => FormInput::class,
            'formInput' => FormInput::class,
            'FormInput' => FormInput::class,
            'formrow' => FormRow::class,
            'form_row' => FormRow::class,
            'formRow' => FormRow::class,
            'FormRow' => FormRow::class,
            'formtext' => FormText::class,
            'form_text' => FormText::class,
            'formText' => FormText::class,
            'FormText' => FormText::class,
        ],
        'factories' => [
            FormButton::class => InvokableFactory::class,
            FormElementErrors::class => InvokableFactory::class,
            FormText::class => InvokableFactory::class,
            FormElement::class => InvokableFactory::class,
            FormRow::class => InvokableFactory::class,
            FormInput::class => InvokableFactory::class,
            AlpineFormBindings::class => InvokableFactory::class,
        ],
    ],

    'service_manager' => [
        'delegators' => [
            'FormElementManager' => [
                ThemedFormDelegatorFactory::class,
            ],
        ],
    ],

    'circlical' => [
        'tailwindcss' => [
            'supported_form_elements' => [
                Email::class,
                Button::class,
                Password::class,
                Submit::class,
                Text::class,
            ],

            'form_themes' => [
                'default' => [
                    Form::ELEMENT_CLASS => 'default-form-element',
                    Form::ELEMENT_ERROR_CLASS => 'default-form-error',
                    Form::ELEMENT_LABEL_CLASS => 'default-form-label',
                    Form::BUTTON_THEMES => [
                        'primary' => 'default-form-button-primary',
                        'default' => 'default-form-button',
                    ],
                ],
            ],
        ],
    ],
];