<?php

namespace Circlical\TailwindForms\Form;

use Laminas\Form\ElementInterface;
use Traversable;

class Form extends \Laminas\Form\Form
{
    public const ELEMENT_ERROR_CLASS = 'elementErrorClass';
    public const ELEMENT_LABEL_CLASS = 'elementLabelClass';
    public const ELEMENT_CLASS = 'elementClass';

    private ?array $tailwindThemeData;

    public function setThemeConfiguration(array $tailwindThemeData): void
    {
        $this->tailwindThemeData = $tailwindThemeData;
    }

    public function add($elementOrFieldset, array $flags = [])
    {
        if (is_array($elementOrFieldset)
            || ($elementOrFieldset instanceof Traversable && !$elementOrFieldset instanceof ElementInterface)
        ) {
            $elementOrFieldset = $this->getFormFactory()->create($elementOrFieldset);
        }

        parent::add($elementOrFieldset, $flags);
        $elementOrFieldset
            ->setLabelAttributes([
                'class' => $this->tailwindThemeData[self::ELEMENT_LABEL_CLASS] ?? '',
            ])
            ->setAttribute('class', $this->tailwindThemeData[self::ELEMENT_CLASS] ?? '')
            ->setOption(self::ELEMENT_ERROR_CLASS, $this->tailwindThemeData[self::ELEMENT_ERROR_CLASS] ?? '');

        return $this;
    }
}
