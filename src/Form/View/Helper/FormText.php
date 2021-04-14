<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormText extends \Laminas\Form\View\Helper\FormText
{
    public function render(ElementInterface $element)
    {
        return parent::render($element);
    }
}

