<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception\DomainException;
use Traversable;

class FormElementErrors extends \Laminas\Form\View\Helper\FormElementErrors
{
    protected static string $ERROR_TEMPLATE = '<p %s>%s</p>';

    public function render(ElementInterface $element, array $attributes = [])
    {
        $messages = $element->getMessages();
        if ($messages instanceof Traversable) {
            $messages = iterator_to_array($messages, true);
        } elseif (!is_array($messages)) {
            throw new DomainException(sprintf(
                '%s expects that $element->getMessages() will return an array or Traversable; received "%s"',
                __METHOD__,
                is_object($messages) ? get_class($messages) : gettype($messages)
            ));
        }

        if (!$messages) {
            return '';
        }


        $messages = $this->flattenMessages($messages);
        if (!$messages) {
            return '';
        }

        $errorBlockId = $element->getAttribute('id') . '-error';
        $element->setAttributes([
            'aria-invalid' => 'true',
            'aria-describedby' => $errorBlockId,
        ]);

        return sprintf(
            static::$ERROR_TEMPLATE,
            $this->createAttributesString([
                'class' => $element->getOption(Form::ELEMENT_ERROR_CLASS),
                'id' => $errorBlockId,
            ]),
            implode("\n", $messages)
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