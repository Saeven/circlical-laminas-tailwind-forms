<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Laminas\Form\Element;
use Laminas\Form\FieldsetInterface;
use Laminas\View\Helper\AbstractHelper;

use function array_values;
use function json_encode;
use function rtrim;
use function substr;

use const JSON_THROW_ON_ERROR;

class AlpineFormBindings extends AbstractHelper
{
    /**
     * @inheritDoc
     */
    public function __invoke(Form $form, bool $printDataModel = true, bool $printErrorModel = true)
    {
        $errors = [];
        $data = [];
        foreach ($form->getIterator() as $name => $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                continue;
            }

            if ($elementOrFieldset instanceof Element\Collection) {
                continue;
            }

            if ($elementOrFieldset instanceof Element\Button || $elementOrFieldset instanceof Element\Submit) {
                continue;
            }

            $data[$name] = $elementOrFieldset->getValue() ?? '';
            $errors[$name] = [];
            if ($form->hasValidated()) {
                $errors[$name] = array_values($form->getMessages($name));
            }
        }

        $model = [];

        if ($printDataModel) {
            $model[$form->getDataModelName()] = $data;
        }

        if ($printErrorModel) {
            $model[$form->getErrorModelName()] = $errors;
        }

        return rtrim(substr(json_encode($model, JSON_THROW_ON_ERROR), 1, -1)) . ',';
    }
}
