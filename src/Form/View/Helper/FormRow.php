<?php

namespace Circlical\TailwindForms\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\LabelAwareInterface;

class FormRow extends \Laminas\Form\View\Helper\FormRow
{
    public function render(ElementInterface $element, $labelPosition = null)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $label = $element->getLabel();

        $type = $element->getAttribute('type');
        $id = $element->getAttribute('id') ?: ('autoform-id-' . $element->getName());

        if (isset($label) && '' !== $label && $type !== 'hidden') {
            if (!$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }
            $label = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
        }

        return sprintf(
            <<< ENDTEMPLATE
<div>
    %s
    <div class="mt-1">
        %s
    </div>
</div>
ENDTEMPLATE,
            $label,
            $elementHelper->render($element)
        );
    }

}
