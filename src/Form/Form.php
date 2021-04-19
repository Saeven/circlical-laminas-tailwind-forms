<?php

namespace Circlical\TailwindForms\Form;

use Laminas\Form\Element\Button;
use Laminas\Form\ElementInterface;
use Traversable;

class Form extends \Laminas\Form\Form
{
    public const ELEMENT_ERROR_CLASS = 'elementErrorClass';
    public const ELEMENT_LABEL_CLASS = 'elementLabelClass';
    public const ELEMENT_CLASS = 'elementClass';
    public const BUTTON_THEMES = 'buttonThemes';
    public const BUTTON_TYPE = 'buttonType';
    public const BUTTON_THEME_DEFAULT = 'default';

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
            ->setOption(self::ELEMENT_ERROR_CLASS, $this->tailwindThemeData[self::ELEMENT_ERROR_CLASS] ?? '');

        if (!$elementOrFieldset->getAttribute('class')) {
            $class = $this->tailwindThemeData[self::ELEMENT_CLASS] ?? '';
            if ($elementOrFieldset instanceof Button) {
                $theme = $theme = $elementOrFieldset->getOption(self::BUTTON_TYPE);
                $class = $this->tailwindThemeData[self::BUTTON_THEMES][!empty($this->tailwindThemeData[self::BUTTON_THEMES][$theme]) ? $theme : self::BUTTON_THEME_DEFAULT];
            }
            $elementOrFieldset->setAttribute('class', $class);
        }

        return $this;
    }
}
