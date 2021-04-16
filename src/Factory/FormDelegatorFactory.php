<?php

namespace Circlical\TailwindForms\Factory;

use Circlical\TailwindForms\Form\Form;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;

class FormDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var Form $tailwindForm */
        $tailwindForm = $callback();

        $theme = 'default';

        $config = $container->get('config');
        if (is_string($options['theme'] ?? null) && !empty($config['circlical']['tailwindcss']['form_themes'][$options['theme']])) {
            $theme = $options['theme'];
        }
        $tailwindForm->setThemeConfiguration($config['circlical']['tailwindcss']['form_themes'][$theme]);

        return $tailwindForm;
    }
}

