<?php

declare(strict_types=1);

namespace Circlical\TailwindForms\Form;

use Laminas\Form\FormElementManager;
use Psr\Container\ContainerInterface;

use function is_string;

class ThemedFormElementManager extends FormElementManager
{
    private array $formThemes;

    /**
     * @inheritDoc
     */
    public function __construct($configInstanceOrParentLocator = null, array $config = [])
    {
        parent::__construct($configInstanceOrParentLocator, $config);
        $config = $configInstanceOrParentLocator->get('config');
        $this->formThemes = $config['circlical']['tailwindcss']['form_themes'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function callElementInit(ContainerInterface $container, $instance): void
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
