<?php

namespace Circlical\TailwindForms\Form;

use Circlical\TailwindForms\ThemeManager;
use Laminas\Form\Element\Button;
use Laminas\Form\ElementInterface;
use Traversable;

class Form extends \Laminas\Form\Form
{
    public const ELEMENT_ERROR_CLASS = 'elementErrorClass';
    public const ELEMENT_LABEL_CLASS = 'elementLabelClass';
    public const ELEMENT_HELP_BLOCK_CLASS = 'elementHelpBlockClass';
    public const ELEMENT_CLASS = 'elementClass';
    public const BUTTON_THEMES = 'buttonThemes';
    public const BUTTON_TYPE = 'buttonType';
    public const BUTTON_THEME_DEFAULT = 'default';
    public const ADD_CLASSES = 'addClasses';
    public const OPTION_ADD_ALPINEJS_MARKUP = 'option_alpine_markup';
    public const OPTION_BIND_ERROR_CLASS = 'option_alpine_bind_errors';
    public const OPTION_HELP_BLOCK = 'help-block';

    private ?array $tailwindThemeData;

    private bool $generateAlpineMarkup = false;

    public function setThemeConfiguration(array $tailwindThemeData): void
    {
        $this->tailwindThemeData = $tailwindThemeData;
    }

    public function setGenerateAlpineMarkup(bool $generateAlpineMarkup): void
    {
        $this->generateAlpineMarkup = $generateAlpineMarkup;
    }

    /**
     * We propagate theme information to the elements as they are added to the form.  We're limited to doing this
     * since there is no reference from the element back to the parent.
     */
    public function add($elementOrFieldset, array $flags = [])
    {
        if (is_array($elementOrFieldset) || ($elementOrFieldset instanceof Traversable && !$elementOrFieldset instanceof ElementInterface)) {
            $elementOrFieldset = $this->getFormFactory()->create($elementOrFieldset);
        }

        parent::add($elementOrFieldset, $flags);

        if (!ThemeManager::isSupported($elementOrFieldset)) {
            return $this;
        }

        $elementOrFieldset
            ->setLabelAttributes([
                'class' => $this->tailwindThemeData[self::ELEMENT_LABEL_CLASS] ?? '',
            ])
            ->setOption(self::ELEMENT_ERROR_CLASS, $this->tailwindThemeData[self::ELEMENT_ERROR_CLASS] ?? '')
            ->setOption(self::ELEMENT_HELP_BLOCK_CLASS, $this->tailwindThemeData[self::ELEMENT_HELP_BLOCK_CLASS] ?? '');


        //
        // 1. Are we in "Alpine" mode?
        //
        $elementOrFieldset->setOption(self::OPTION_ADD_ALPINEJS_MARKUP, $this->generateAlpineMarkup);
        if ($this->generateAlpineMarkup) {
            if (!$elementOrFieldset instanceof Button) {
                $elementOrFieldset->setAttribute('x-model', sprintf("data['%s']", $elementOrFieldset->getName()));

                // if there is no class binding, and auto-error binding has not been disabled, enable it
                if (!$elementOrFieldset->getAttribute('x-bind:class') && $elementOrFieldset->getOption(self::OPTION_BIND_ERROR_CLASS) !== false) {
                    $elementOrFieldset->setAttribute('x-bind:class', sprintf("{'error': errors['%s'].length > 0}", $elementOrFieldset->getName()));
                }
            }
        }


        //
        // 2. Assert the class by theme, setting it outright, or appending
        //
        if (!$elementOrFieldset->getAttribute('class')) {
            $class = $this->tailwindThemeData[self::ELEMENT_CLASS] ?? '';
            if ($elementOrFieldset instanceof Button) {
                $theme = $elementOrFieldset->getOption(self::BUTTON_TYPE);
                $class = $this->tailwindThemeData[self::BUTTON_THEMES][!empty($this->tailwindThemeData[self::BUTTON_THEMES][$theme]) ? $theme : self::BUTTON_THEME_DEFAULT];
            }

            if ($addedClasses = $elementOrFieldset->getOption(self::ADD_CLASSES)) {
                $class .= ' ' . $addedClasses;
            }

            $elementOrFieldset->setAttribute('class', $class);
        }

        return $this;
    }
}
