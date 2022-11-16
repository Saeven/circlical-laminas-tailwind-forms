<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form\View\Helper;

use InvalidArgumentException;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormRadio as LaminasFormRadio;

use function array_key_exists;
use function array_merge;
use function implode;
use function in_array;
use function is_scalar;
use function sprintf;

class FormRadio extends LaminasFormRadio
{
    /**
     * @inheritDoc
     */
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof MultiCheckboxElement) {
            throw new InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\MultiCheckbox',
                __METHOD__
            ));
        }

        $name = static::getName($element);

        $options = $element->getValueOptions();

        $attributes = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();
        $selectedOptions = (array) $element->getValue();

        $rendered = $this->renderOptions($element, $options, $selectedOptions, $attributes);

        // Render hidden element
        if ($element->useHiddenElement()) {
            $rendered = $this->renderHiddenElement($element) . $rendered;
        }

        return $rendered;
    }

    /**
     * @inheritDoc
     */
    protected function renderOptions(
        MultiCheckboxElement $element,
        array $options,
        array $selectedOptions,
        array $attributes
    ): string {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $labelClose = $labelHelper->closeTag();
        $globalLabelAttributes = [];
        $closingBracket = $this->getInlineClosingBracket();

        if ($element instanceof LabelAwareInterface) {
            $globalLabelAttributes = $element->getLabelAttributes();
        }

        if (empty($globalLabelAttributes)) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = [];
        $count = 0;
        $elementOptions = $element->getOptions();
        $optionLabelClass = $elementOptions['option_label_attributes']['class'] ?? 'label-class-not-set';

        foreach ($options as $key => $optionSpec) {
            $count++;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value = '';
            $label = '';
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
            $selected = isset($inputAttributes['selected'])
                && $inputAttributes['type'] !== 'radio'
                && $inputAttributes['selected'];
            $disabled = isset($inputAttributes['disabled']) && $inputAttributes['disabled'];

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key,
                ];
            }

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }
            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }
            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }
            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }
            if (isset($optionSpec['label_attributes'])) {
                $labelAttributes = isset($labelAttributes)
                    ? array_merge($labelAttributes, $optionSpec['label_attributes'])
                    : $optionSpec['label_attributes'];
            }
            if (isset($optionSpec['attributes'])) {
                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }

            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }

            $inputAttributes['value'] = $value;
            $inputAttributes['checked'] = $selected;
            $inputAttributes['disabled'] = $disabled;
            $inputAttributes['id'] = $element->getName() . '-opt-' . $value;
            $labelAttributes['for'] = $inputAttributes['id'];
            $labelAttributes['class'] = $optionLabelClass;

            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            $label = $labelHelper->openTag($labelAttributes) . $label . $labelClose;
            $markup = sprintf("            <div class=\"flex items-center\">\n                %s\n                %s\n            </div>\n", $input, $label);
            $combinedMarkup[] = $markup;
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }
}
