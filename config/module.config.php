<?php

namespace Circlical\TailwindForms;

use Circlical\TailwindForms\Factory\FormDelegatorFactory;
use Circlical\TailwindForms\Factory\ThemedFormDelegatorFactory;
use Circlical\TailwindForms\Factory\ThemedFormElementManagerFactory;
use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\Form\View\Helper\FormElement;
use Circlical\TailwindForms\Form\View\Helper\FormElementErrors;
use Circlical\TailwindForms\Form\View\Helper\FormInput;
use Circlical\TailwindForms\Form\View\Helper\FormRow;
use Circlical\TailwindForms\Form\View\Helper\FormText;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'view_helpers' => [
        'aliases' => [
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
            FormElementErrors::class => InvokableFactory::class,
            FormText::class => InvokableFactory::class,
            FormElement::class => InvokableFactory::class,
            FormRow::class => InvokableFactory::class,
            FormInput::class => InvokableFactory::class,
        ],
    ],

    'form_elements' => [
        'delegators' => [
            Form::class => [
                FormDelegatorFactory::class,
            ],
        ],
    ],

    'service_manager' => [

    ],

    'circlical' => [
        'tailwindcss' => [
            'form_themes' => [
                'default' => [
                    Form::ELEMENT_CLASS => 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md',
                    Form::ELEMENT_ERROR_CLASS => 'mt-2 text-sm text-red-600',
                    Form::ELEMENT_LABEL_CLASS => 'block text-sm font-medium text-gray-700',
                ],
            ],
        ],
    ],
];