<?php

namespace Circlical\TailwindForms\Factory;

use Circlical\TailwindForms\Factory\Form\ThemedFormElementManagerFactory;
use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\Form\ThemedFormElementManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;

class ThemedFormDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $factory = new ThemedFormElementManagerFactory();
        $themedFormManager = $factory($container, ThemedFormElementManager::class, $options);
        $container->setFactory(ThemedFormElementManager::class, $factory);
        $container->setFactory('FormElementManager', $factory);

        return $themedFormManager;
    }
}

