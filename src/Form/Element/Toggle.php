<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form\Element;

use Laminas\Form\Element\Checkbox;

class Toggle extends Checkbox
{
    public function getValue()
    {
        return (int) parent::getValue(); // TODO: Change the autogenerated stub
    }

}
