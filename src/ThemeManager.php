<?php

namespace Circlical\TailwindForms;

use Laminas\Form\ElementInterface;

class ThemeManager
{
    private static array $supportedElements;

    public static function setSupportedElements(array $elementList): void
    {
        static::$supportedElements = $elementList;
    }

    public static function isSupported(ElementInterface $element): bool
    {
        return in_array(get_class($element), static::$supportedElements, true);
    }
}