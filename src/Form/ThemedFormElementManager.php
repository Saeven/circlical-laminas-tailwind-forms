<?php

namespace Circlical\TailwindForms\Form;

use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;

class ThemedFormElementManager extends FormElementManager
{
    private array $formThemes;

    public function __construct($configInstanceOrParentLocator = null, array $config = [])
    {
        parent::__construct($configInstanceOrParentLocator, $config);
        $config = $configInstanceOrParentLocator->get('config');
        $this->formThemes = $config['circlical']['tailwindcss']['form_themes'] ?? [];
    }

    public function callElementInit(ContainerInterface $container, $instance)
    {
        if ($instance instanceof Form) {
            $this->injectTheme($instance);
        }

        parent::callElementInit($container, $instance);
    }

    protected function injectTheme(Form $object): void
    {
        $theme = 'default';
        if (($configuredTheme = $object->getOption('theme')) && is_string($configuredTheme) && !empty($this->formThemes[$configuredTheme])) {
            $theme = $configuredTheme;
        }
        $object->setThemeConfiguration($this->formThemes[$theme]);
    }
}

