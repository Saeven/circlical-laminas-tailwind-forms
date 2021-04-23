<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Circlical\TailwindForms\ThemeManager;
use Laminas\Form\Element\Button;
use Laminas\Form\ElementInterface;

class FormRow extends \Laminas\Form\View\Helper\FormRow
{
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

        if ($this->renderErrors) {
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
