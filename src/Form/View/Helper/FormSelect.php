<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Laminas\Form\Element\Select as SelectElement;
use Laminas\Form\ElementInterface;

class FormSelect extends \Laminas\Form\View\Helper\FormSelect
{
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

        $rendered = sprintf(
            "<select %s>\n%s\n</select>",
            $this->createAttributesString($attributes),
            $this->renderOptions($options, $value)
        );

        // Render hidden element
        $useHiddenElement = method_exists($element, 'useHiddenElement')
            && method_exists($element, 'getUnselectedValue')
            && $element->useHiddenElement();

        if ($useHiddenElement) {
            $rendered = $this->renderHiddenElement($element) . $rendered;
        }

        return $rendered;
    }

}

