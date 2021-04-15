<?php

namespace Circlical\TailwindForms\Form;

use Laminas\Form\ElementInterface;
use Traversable;

class Form extends \Laminas\Form\Form
{
    public const ELEMENT_ERROR_CLASS = 'elementErrorClass';
    public const ELEMENT_LABEL_CLASS = 'elementLabelClass';
    public const ELEMENT_CLASS = 'elementClass';

    public function add($elementOrFieldset, array $flags = [])
    {
        if (is_array($elementOrFieldset)
            || ($elementOrFieldset instanceof Traversable && !$elementOrFieldset instanceof ElementInterface)
        ) {
            $elementOrFieldset = $this->getFormFactory()->create($elementOrFieldset);
        }

        parent::add($elementOrFieldset, $flags);
        $labelClass = $this->getOption(self::ELEMENT_LABEL_CLASS);
        if ($labelClass) {
            $elementOrFieldset->setLabelAttributes([
                'class' => $labelClass,
            ]);
        }

        $elementClass = $this->getOption(self::ELEMENT_CLASS);
        if ($elementClass) {
            $elementOrFieldset->setAttribute(
                'class', $elementClass
            );
        }

        $elementErrorClass = $this->getOption(self::ELEMENT_ERROR_CLASS);
        if ($elementErrorClass) {
            $elementOrFieldset->setOption(
                self::ELEMENT_ERROR_CLASS, $elementErrorClass
            );
        }

        return $this;
    }
}
