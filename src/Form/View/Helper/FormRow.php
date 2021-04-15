<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Laminas\Form\ElementInterface;

class FormRow extends \Laminas\Form\View\Helper\FormRow
{
    public function render(ElementInterface $element, $labelPosition = null)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();
        $label = $element->getLabel();

        $type = $element->getAttribute('type');

        if (isset($label) && '' !== $label && $type !== 'hidden') {
            if (!$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }
            $label = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
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
