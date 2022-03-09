<?php

declare(strict_types=1);

namespace Circlical\TailwindForms;

use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Mvc\MvcEvent;

class Module implements BootstrapListenerInterface, ConfigProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e)
    {
        if (!$e instanceof MvcEvent) {
            return;
        }
        $config = $e->getApplication()->getServiceManager()->get('config');
        ThemeManager::setSupportedElements($config['circlical']['tailwindcss']['supported_form_elements']);
    }
}
