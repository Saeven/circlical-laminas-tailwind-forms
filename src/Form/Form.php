<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form;

use Circlical\TailwindForms\Form\Element\Toggle;
use Circlical\TailwindForms\ThemeManager;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Textarea;
use Laminas\Form\ElementInterface;
use Traversable;

use function is_array;
use function is_string;
use function sprintf;

class Form extends \Laminas\Form\Form
{
    public const ELEMENT_ERROR_CLASS = 'elementErrorClass';
    public const ELEMENT_LABEL_CLASS = 'elementLabelClass';
    public const ELEMENT_HELP_BLOCK_CLASS = 'elementHelpBlockClass';
    public const ELEMENT_CHECKBOX_CLASS = 'elementCheckboxClass';
    public const ELEMENT_TOGGLE_CLASS = 'elementToggleClass';
    public const ELEMENT_TEXTAREA_CLASS = 'elementTextAreaClass';
    public const ELEMENT_RADIO_OPTION_CLASS = 'elementRadioOption';
    public const ELEMENT_RADIO_OPTION_LABEL_CLASS = 'elementRadioOptionLabelClass';
    public const ELEMENT_RADIO_GROUP_CLASS = 'elementRadioGroupClass';
    public const ELEMENT_CLASS = 'elementClass';
    public const BUTTON_THEMES = 'buttonThemes';
    public const BUTTON_TYPE = 'buttonType';
    public const BUTTON_THEME_DEFAULT = 'default';
    public const ADD_CLASSES = 'addClasses';
    public const OPTION_ADD_ALPINEJS_MARKUP = 'option_alpine_markup';
    public const OPTION_BIND_ERROR_CLASS = 'option_alpine_bind_errors';
    public const OPTION_HELP_BLOCK = 'help-block';
    public const OPTION_RADIO_LEGEND = 'radio-legend';

    public const OPTION_DATA_MODEL_NAME = 'data-model-name';

    public const OPTION_ERROR_MODEL_NAME = 'error-model-name';

    private ?array $tailwindThemeData;

    private bool $generateAlpineMarkup = false;

    private string $dataModelName = 'data';

    private string $errorModelName = 'errors';

    public function setThemeConfiguration(array $tailwindThemeData): void
    {
        $this->tailwindThemeData = $tailwindThemeData;
    }

    public function setGenerateAlpineMarkup(bool $generateAlpineMarkup): void
    {
        $this->generateAlpineMarkup = $generateAlpineMarkup;
    }

    public function getDataModelName(): string
    {
        return $this->dataModelName;
    }

    public function getErrorModelName(): string
    {
        return $this->errorModelName;
    }

    public function setDataModelName(string $dataModelName): void
    {
        $this->dataModelName = $dataModelName;
    }

    public function setErrorModelName(string $errorModelName): void
    {
        $this->errorModelName = $errorModelName;
    }

    /**
     * We propagate theme information to the elements as they are added to the form.  We're limited to doing this
     * since there is no reference from the element back to the parent.
     *
     * @inheritDoc
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

        //
        // Check to see if a custom label attribute was created
        //
        $labelClass = $this->tailwindThemeData[self::ELEMENT_LABEL_CLASS];
        $elementOptions = $elementOrFieldset->getOptions();
        $labelAttributes = $elementOptions['label_attributes'] ?? null;
        if ($labelAttributes !== null && !empty($labelAttributes['class']) && is_string($labelAttributes['class'])) {
            $labelClass = $labelAttributes['class'];
        }

        $elementOrFieldset
            ->setLabelAttributes([
                'class' => $labelClass,
            ])
            ->setOption(self::ELEMENT_ERROR_CLASS, $this->tailwindThemeData[self::ELEMENT_ERROR_CLASS] ?? '')
            ->setOption(self::ELEMENT_HELP_BLOCK_CLASS, $this->tailwindThemeData[self::ELEMENT_HELP_BLOCK_CLASS] ?? '')
            ->setOption(self::OPTION_DATA_MODEL_NAME, $this->dataModelName)
            ->setOption(self::OPTION_ERROR_MODEL_NAME, $this->errorModelName);

        //
        // 1. Are we in "Alpine" mode? Toggle requires Alpine.
        //
        $elementRequiresAlpine = $elementOrFieldset instanceof Toggle;
        $elementOrFieldset->setOption(self::OPTION_ADD_ALPINEJS_MARKUP, $this->generateAlpineMarkup || $elementRequiresAlpine);
        if ($this->generateAlpineMarkup || $elementOrFieldset instanceof Toggle) {
            if (!($elementOrFieldset instanceof Button || $elementOrFieldset instanceof Submit)) {
                $modelValue = sprintf("%s['%s']", $this->dataModelName, $elementOrFieldset->getName());
                $elementOrFieldset->setAttribute('x-model', $modelValue);

                // if there is no class binding, and auto-error binding has not been disabled, enable it
                if (!$elementOrFieldset->getAttribute('x-bind:class') && $elementOrFieldset->getOption(self::OPTION_BIND_ERROR_CLASS) !== false) {
                    if ($elementOrFieldset instanceof Toggle) {
                        $elementOrFieldset->setAttribute(
                            'x-bind:class',
                            sprintf("{'error': %s['%s'].length > 0, 'active': %s}", $this->errorModelName, $elementOrFieldset->getName(), $modelValue)
                        );
                    } else {
                        $elementOrFieldset->setAttribute('x-bind:class', sprintf("{'error': %s['%s'].length > 0}", $this->errorModelName, $elementOrFieldset->getName()));
                    }
                }
            }
        }

        //
        // 2. Assert the class by theme, setting it outright, or appending
        //
        if (!$elementOrFieldset->getAttribute('class')) {
            $class = $this->tailwindThemeData[self::ELEMENT_CLASS] ?? '';
            if ($elementOrFieldset instanceof Button || $elementOrFieldset instanceof Submit) {
                $theme = $elementOrFieldset->getOption(self::BUTTON_TYPE);
                $class = $this->tailwindThemeData[self::BUTTON_THEMES][!empty($this->tailwindThemeData[self::BUTTON_THEMES][$theme]) ? $theme : self::BUTTON_THEME_DEFAULT];
            } elseif ($elementOrFieldset instanceof Toggle) {
                $class = $this->tailwindThemeData[self::ELEMENT_TOGGLE_CLASS];
            } elseif ($elementOrFieldset instanceof Radio) {
                $class = $this->tailwindThemeData[self::ELEMENT_RADIO_OPTION_CLASS];
                $options = $elementOrFieldset->getOptions();

                if (empty($options[self::ELEMENT_RADIO_OPTION_LABEL_CLASS])) {
                    $options[self::ELEMENT_RADIO_OPTION_LABEL_CLASS] = $this->tailwindThemeData[self::ELEMENT_RADIO_OPTION_LABEL_CLASS];
                    $elementOrFieldset->setOptions($options);
                }
            } elseif ($elementOrFieldset instanceof Checkbox) {
                $class = $this->tailwindThemeData[self::ELEMENT_CHECKBOX_CLASS];
            } elseif ($elementOrFieldset instanceof Textarea) {
                $class = $this->tailwindThemeData[self::ELEMENT_TEXTAREA_CLASS];
            }

            if ($addedClasses = $elementOrFieldset->getOption(self::ADD_CLASSES)) {
                $class .= ' ' . $addedClasses;
            }

            $elementOrFieldset->setAttribute('class', $class);
        }

        return $this;
    }
}
