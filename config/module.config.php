<?php

namespace Circlical\TailwindForms;

use Circlical\TailwindForms\Form\View\Helper\FormElement;
use Circlical\TailwindForms\Form\View\Helper\FormRow;
use Circlical\TailwindForms\Form\View\Helper\FormText;

return [
    'view_helpers' => [
        'invokables' => [
            'formtext' => FormText::class,
            'formElement' => FormElement::class,
            'formRow' => FormRow::class,
        ],
        'aliases' => [
        ],
    ],
];