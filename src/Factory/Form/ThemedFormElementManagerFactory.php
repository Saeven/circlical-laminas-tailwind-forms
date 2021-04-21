<?php

namespace Circlical\TailwindForms\Factory\Form;


use Circlical\TailwindForms\Form\ThemedFormElementManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManagerFactory;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ThemedFormElementManagerFactory extends FormElementManagerFactory
{
    /**
     * {@inheritDoc}
     *
     * @return AbstractPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $pluginManager = new ThemedFormElementManager($container, $options ?: []);

        // If this is in a laminas-mvc application, the ServiceListener will inject
        // merged configuration during bootstrap.
        if ($container->has('ServiceListener')) {
            return $pluginManager;
        }

        // If we do not have a config service, nothing more to do
        if (!$container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config');

        // If we do not have form_elements configuration, nothing more to do
        if (!isset($config['form_elements']) || !is_array($config['form_elements'])) {
            return $pluginManager;
        }

        // Wire service configuration for forms and elements
        (new Config($config['form_elements']))->configureServiceManager($pluginManager);

        return $pluginManager;
    }
}

