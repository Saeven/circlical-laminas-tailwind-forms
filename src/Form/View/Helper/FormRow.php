<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\ThemeManager;
use Laminas\Form\Element\Button;
use Laminas\Form\ElementInterface;

class FormRow extends \Laminas\Form\View\Helper\FormRow
{
    protected static string $ALPINE_ERROR_TEMPLATE = <<< ALPINE_ERROR_TEMPLATE
<div id="{{NAME}}-errors">
    <template x-for="(error) in errors.{{NAME}}">
        <p class="{{ERROR-CLASS}}" x-text="error"></p>
    </template>
    </div>
ALPINE_ERROR_TEMPLATE;


    public function render(ElementInterface $element, $labelPosition = null)
    {
        if (!ThemeManager::isSupported($element)) {
            return parent::render($element, $labelPosition);
        }

        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();
        $label = '';
        $type = $element->getAttribute('type');

        if (!$element instanceof Button) {
            $label = $element->getLabel();
            if (isset($label) && '' !== $label && $type !== 'hidden') {
                if (!$element->getLabelOption('disable_html_escape')) {
                    $label = $escapeHtmlHelper($label);
                }
                $label = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
            }
        }

        // Could move this into a custom helper to 'conform', but there's no need I don't think,
        // since element errors are always bound to structure under Alpine
        if ($element->getOption(Form::OPTION_ADD_ALPINEJS_MARKUP)) {
            $elementErrors = strtr(static::$ALPINE_ERROR_TEMPLATE, [
                '{{NAME}}' => $element->getName(),
                '{{ERROR-CLASS}}' => $element->getOption(Form::ELEMENT_ERROR_CLASS) ?? '',
            ]);
        } elseif ($this->renderErrors) {
            $elementErrors = $elementErrorsHelper->render($element);
        }

        return sprintf(
            <<< ENDTEMPLATE
<div>%s
    <div class="mt-1">
        %s
    </div>%s
</div>
ENDTEMPLATE,
            $label ? "\n    $label" : '',
            $elementHelper->render($element),
            $elementErrors ? "\n    $elementErrors" : ''
        );
    }
}
