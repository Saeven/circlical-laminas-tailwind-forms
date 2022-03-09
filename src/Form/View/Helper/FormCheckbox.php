<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form\View\Helper;

use DomainException;
use InvalidArgumentException;
use Laminas\Form\Element\Checkbox as CheckboxElement;
use Laminas\Form\ElementInterface;

use function sprintf;

class FormCheckbox extends \Laminas\Form\View\Helper\FormCheckbox
{
    public function render(ElementInterface $element): string
    {
        if (!$element instanceof CheckboxElement) {
            throw new InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\Checkbox',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();
        $attributes['value'] = $element->getCheckedValue();
        $closingBracket = $this->getInlineClosingBracket();

        if ($element->isChecked()) {
            $attributes['checked'] = 'checked';
        }

        return sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );
    }
}
