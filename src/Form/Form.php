<?php

namespace Circlical\TailwindForms\Form;

class Form extends \Laminas\Form\Form
{
    public const ELEMENT_LABEL_CLASS = 'elementLabelClass';

    public function add($elementOrFieldset, array $flags = [])
    {
        parent::add($elementOrFieldset, $flags);
        $class = $this->getOption(self::ELEMENT_LABEL_CLASS);
        if ($class) {
            $elementOrFieldset->setLabelAttributes([
                'class' => $class
            ]);
        }

        return $this;
    }
}
