<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Factory;

use Circlical\TailwindForms\Factory\Form\ThemedFormElementManagerFactory;
use Circlical\TailwindForms\Form\ThemedFormElementManager;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Psr\Container\ContainerInterface;

class ThemedFormDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, ?array $options = null)
    {
        $factory = new ThemedFormElementManagerFactory();
        $themedFormManager = $factory($container, ThemedFormElementManager::class, $options);
        $container->setFactory(ThemedFormElementManager::class, $factory);
        $container->setFactory('FormElementManager', $factory);

        return $themedFormManager;
    }
}
