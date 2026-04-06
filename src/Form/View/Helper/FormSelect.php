<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\ElementInterface;

use function array_key_exists;
use function is_string;
use function method_exists;
use function sprintf;

class FormSelect extends \Laminas\Form\View\Helper\FormSelect
{
    private const SELECT_MODEL_TYPE_ARRAY = 'array';
    private const SELECT_MODEL_TYPE_MAP = 'map';

    public function render(ElementInterface $element): string
    {
        if (!$element instanceof SelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\Select',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $options = $element->getValueOptions();

        if (($emptyOption = $element->getEmptyOption()) !== null) {
            $options = ['' => $emptyOption] + $options;
        }

        $attributes = $element->getAttributes();
        $value = $this->validateMultiValue($element->getValue(), $attributes);

        $attributes['name'] = $name;
        if (array_key_exists('multiple', $attributes) && $attributes['multiple']) {
            $attributes['name'] .= '[]';
        }
        $this->validTagAttributes = $this->validSelectAttributes;

        $rendered = $this->renderSelect($element, $attributes, $options, $value);

        // Render hidden element
        $useHiddenElement = method_exists($element, 'useHiddenElement')
            && method_exists($element, 'getUnselectedValue')
            && $element->useHiddenElement();

        if (!$useHiddenElement) {
            return $rendered;
        }

        return $this->renderHiddenElement($element) . $rendered;
    }

    private function getEmptyLabel(SelectElement $element): string
    {
        $emptyOption = $element->getEmptyOption();
        if ($emptyOption && is_string($emptyOption)) {
            return sprintf('<option x-text="%s"></option>', $emptyOption);
        }

        return '';
    }

    private function renderSelect(SelectElement $element, array $attributes, array $options, $value): string
    {
        $selectModel = $element->getOption(Form::OPTION_ELEMENT_X_SELECT_MODEL_NAME);
        if (!$selectModel) {
            return sprintf(
                "<select %s>
%s
</select>",
                $this->createAttributesString($attributes),
                $this->renderOptions($options, $value)
            );
        }

        return sprintf(
            "<select %s>%s
%s
</select>",
            $this->createAttributesString($attributes),
            $this->getEmptyLabel($element),
            $this->renderSelectTemplate($element, $selectModel)
        );
    }

    private function renderSelectTemplate(SelectElement $element, string $selectModel): string
    {
        $selectType = $element->getOption(Form::OPTION_ELEMENT_X_SELECT_MODEL_TYPE) ?? self::SELECT_MODEL_TYPE_MAP;
        if ($selectType === self::SELECT_MODEL_TYPE_ARRAY) {
            return $this->renderArrayTemplate($element, $selectModel);
        }

        return $this->renderMapTemplate($element, $selectModel);
    }

    private function renderArrayTemplate(SelectElement $element, string $selectModel): string
    {
        $valueField = $element->getOption(Form::OPTION_ELEMENT_X_SELECT_VALUE_FIELD) ?? 'id';
        $labelField = $element->getOption(Form::OPTION_ELEMENT_X_SELECT_LABEL_FIELD) ?? 'title';
        $selectedValue = $element->getOption(Form::OPTION_ELEMENT_X_MODEL_NAME);

        return sprintf(
            '<template x-for="item in %s">'
            . '<option x-text="item.%s" :value="item.%s" :selected="item.%s == %s"></option>'
            . '</template>',
            $selectModel,
            $labelField,
            $valueField,
            $valueField,
            $selectedValue
        );
    }

    private function renderMapTemplate(SelectElement $element, string $selectModel): string
    {
        return sprintf(
            '<template x-for="(value, item) in %s">'
            . '<option x-text="value" :value="item" :selected="item == %s"></option>'
            . '</template>',
            $selectModel,
            $element->getOption(Form::OPTION_ELEMENT_X_MODEL_NAME)
        );
    }
}
