<?php

namespace Circlical\TailwindForms\Form;

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

    public function get($name, $options = [])
    {
        return $this->injectTheme(parent::get($name, $options));
    }

    private function injectTheme($object)
    {
        if ($object instanceof Form) {
            $theme = 'default';
            if (($configuredTheme = $object->getOption('theme')) && is_string($configuredTheme) && !empty($this->formThemes[$configuredTheme])) {
                $theme = $configuredTheme;
            }
            $object->setThemeConfiguration($this->formThemes[$theme]);
        }

        return $object;
    }
}

