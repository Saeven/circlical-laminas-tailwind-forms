<?php

namespace Circlical\TailwindForms\Factory\Form\View\Helper;

use Circlical\TailwindForms\Form\View\Helper\FormRow;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FormRowFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        return new FormRow($config['circlical']['tailwindcss']['supported_form_elements'] ?? []);
    }
}

