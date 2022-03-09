<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\ThemeManager;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\ElementInterface;

use function preg_replace;
use function sprintf;
use function strtr;

class FormRow extends \Laminas\Form\View\Helper\FormRow
{
    protected static string $alpineErrorTemplate = <<<ALPINE_ERROR_TEMPLATE
<div id="{{NAME}}-errors">
    <template x-for="(error) in errors['{{NAME}}']">
        <p class="{{ERROR-CLASS}}" x-text="error"></p>
    </template>
</div>
ALPINE_ERROR_TEMPLATE;

    protected static string $standardElementTemplate = <<<STANDARD_ELEMENT_TEMPLATE
<div>
    {{LABEL}}
    <div class="mt-1">
        {{ELEMENT}}
    </div>
    {{HELP-BLOCK}}
    {{ERRORS}}
</div>
STANDARD_ELEMENT_TEMPLATE;

    protected static string $checkboxElementTemplate = <<<CHECKBOX_ELEMENT_TEMPLATE
<div>
    <div class="mt-1">
        <div class="relative flex items-start">
            <div class="flex items-center h-5">
                {{ELEMENT}}
            </div>
            <div class="ml-3 text-sm">
                {{LABEL}}
                {{HELP-BLOCK}}
            </div>
        </div>
    </div>
    {{ERRORS}}
</div>
CHECKBOX_ELEMENT_TEMPLATE;

    /**
     * @inheritDoc
     */
    public function render(ElementInterface $element, $labelPosition = null): string
    {
        if (!ThemeManager::isSupported($element)) {
            return parent::render($element, $labelPosition);
        }

        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();
        $elementErrors = null;
        $label = '';
        $helpBlock = '';

        // Could move this into a custom helper to 'conform', but there's no need I don't think,
        // since element errors are always bound to structure under Alpine
        if ($element->getOption(Form::OPTION_ADD_ALPINEJS_MARKUP)) {
            if (!$element instanceof Button) {
                $elementErrors = strtr(static::$alpineErrorTemplate, [
                    '{{NAME}}' => $element->getName(),
                    '{{ERROR-CLASS}}' => $element->getOption(Form::ELEMENT_ERROR_CLASS) ?? '',
                ]);
            }
        } elseif ($this->renderErrors) {
            $elementErrors = $elementErrorsHelper->render($element);
        }

        $selectedTemplate = static::$standardElementTemplate;
        if ($element instanceof Button) {
            // these aren't the droids you are looking for
        } elseif ($element instanceof Checkbox) {
            // checkbox gets handled a bit differently, because the element helpers don't
            // get any notion of label helpers which carry into translation and so forth.
            // It's an expensive chain to recreate, so we reassemble the object herein instead.
            $selectedTemplate = static::$checkboxElementTemplate;
            $label = $this->renderLabel($element);
            $element->setAttribute('aria-describedby', $element->getName() . '-description');

            if ($helpBlockText = $element->getOption(Form::OPTION_HELP_BLOCK)) {
                $helpBlock = sprintf(
                    '<p id="%s-description" class="%s">%s</p>',
                    $element->getName(),
                    $element->getOption(Form::ELEMENT_HELP_BLOCK_CLASS),
                    $helpBlockText
                );
            }
        } else {
            $label = $this->renderLabel($element);

            if ($helpBlockText = $element->getOption(Form::OPTION_HELP_BLOCK)) {
                $helpBlock = sprintf(
                    '<p class="%s">%s</p>',
                    $element->getOption(Form::ELEMENT_HELP_BLOCK_CLASS),
                    $helpBlockText
                );
            }
        }

        $rendered = strtr($selectedTemplate, [
            '{{LABEL}}' => $label,
            '{{ELEMENT}}' => $elementHelper->render($element),
            '{{HELP-BLOCK}}' => $helpBlock,
            '{{ERRORS}}' => $elementErrors,
        ]);

        return preg_replace('/^\h*\v+/m', '', $rendered);
    }

    protected function renderLabel(ElementInterface $element): ?string
    {
        $type = $element->getAttribute('type');
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();

        $label = $element->getLabel();
        if (isset($label) && '' !== $label && $type !== 'hidden') {
            if (!$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }
            $label = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
        }

        return $label;
    }
}
