<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;

use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function sprintf;

class FormButton extends \Laminas\Form\View\Helper\FormButton
{
    /**
     * @inheritDoc
     */
    public function openTag($attributesOrElement = null): string
    {
        if (null === $attributesOrElement) {
            return '<button>';
        }

        if (is_array($attributesOrElement)) {
            $attributes = $this->createAttributesString($attributesOrElement);

            return sprintf('<button %s>', $attributes);
        }

        if (!$attributesOrElement instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Laminas\Form\ElementInterface instance; received "%s"',
                __METHOD__,
                is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement)
            ));
        }

        $element = $attributesOrElement;
        $name = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getType($element);

        // buttons shouldn't carry empty values

        return sprintf(
            '<button %s>',
            $this->createAttributesString($attributes)
        );
    }
}
