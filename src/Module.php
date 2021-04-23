<?php

namespace Circlical\TailwindForms;

use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\Mvc\MvcEvent;

class Module implements BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e)
    {
        if( !$e instanceof MvcEvent){
            return;
        }
        $config = $e->getApplication()->getServiceManager()->get('config');
        ThemeManager::setSupportedElements($config['circlical']['tailwindcss']['supported_form_elements']);
    }
}
