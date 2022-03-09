<?php

declare(strict_types=1);

namespace Circlical\TailwindForms;

use Laminas\Form\ElementInterface;

use function in_array;

class ThemeManager
{
    private static array $supportedElements;

    public static function setSupportedElements(array $elementList): void
    {
        static::$supportedElements = $elementList;
    }

    /**
     * Doing this here for now, I'll change it into a config bundle down the road.
     */
    public static function isSupported(ElementInterface $element): bool
    {
        return in_array($element::class, static::$supportedElements, true);
    }
}
