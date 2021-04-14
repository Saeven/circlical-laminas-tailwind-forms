<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormElement extends \Laminas\Form\View\Helper\FormElement
{
    protected function renderHelper($name, ElementInterface $element)
    {
        return parent::renderHelper($name, $element);
    }

}