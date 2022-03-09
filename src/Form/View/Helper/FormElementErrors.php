<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Traversable;

use function array_map;
use function array_walk_recursive;
use function gettype;
use function implode;
use function is_array;
use function is_object;
use function iterator_to_array;
use function sprintf;

class FormElementErrors extends \Laminas\Form\View\Helper\FormElementErrors
{
    protected static string $errorTemplate = "<div %s>%s\n    </div>";

    /**
     * @inheritDoc
     */
    public function render(ElementInterface $element, array $attributes = []): string
    {
        $messages = $element->getMessages();
        if ($messages instanceof Traversable) {
            $messages = iterator_to_array($messages, true);
        } elseif (!is_array($messages)) {
            throw new DomainException(sprintf(
                '%s expects that $element->getMessages() will return an array or Traversable; received "%s"',
                __METHOD__,
                is_object($messages) ? $messages::class : gettype($messages)
            ));
        }

        if (!$messages) {
            return '';
        }

        $messages = $this->flattenMessages($messages);
        $errorBlockId = $element->getAttribute('id') . '-error';
        $element->setAttributes([
            'aria-invalid' => 'true',
            'aria-describedby' => $errorBlockId,
        ]);

        $errorClass = $element->getOption(Form::ELEMENT_ERROR_CLASS) ?? '';

        return sprintf(
            static::$errorTemplate,
            $this->createAttributesString([
                'id' => $errorBlockId,
            ]),
            implode('', array_map(static function (string $message) use ($errorClass) {
                return sprintf("\n        <p class=\"%s\">%s</p>", $errorClass, $message);
            }, $messages))
        );
    }

    private function flattenMessages(array $messages): array
    {
        return $this->translateErrorMessages && $this->getTranslator()
            ? $this->flattenMessagesWithTranslator($messages)
            : $this->flattenMessagesWithoutTranslator($messages);
    }

    private function flattenMessagesWithoutTranslator(array $messages): array
    {
        $messagesToPrint = [];
        array_walk_recursive($messages, static function ($item) use (&$messagesToPrint) {
            $messagesToPrint[] = $item;
        });

        return $messagesToPrint;
    }

    private function flattenMessagesWithTranslator(array $messages): array
    {
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();
        $messagesToPrint = [];
        $messageCallback = static function ($item) use (&$messagesToPrint, $translator, $textDomain) {
            $messagesToPrint[] = $translator->translate($item, $textDomain);
        };
        array_walk_recursive($messages, $messageCallback);

        return $messagesToPrint;
    }
}
