<?php

namespace Circlical\TailwindFormsTest\View\Helper;

use Circlical\TailwindForms\Form\Element\Toggle;
use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\View\Helper\AlpineFormBindings;
use Circlical\TailwindFormsTest\Bootstrap;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Form\Element;
use PHPUnit\Framework\TestCase;

class AlpineFormBindingsTest extends TestCase
{
    private ?Form $form;
    private ?AlpineFormBindings $helper;

    protected function setUp(): void
    {
        $serviceManager = Bootstrap::getServiceManager();
        $oViewHelperPluginManager = $serviceManager->get('ViewHelperManager');
        $oRenderer = new PhpRenderer();
        $this->helper = $oViewHelperPluginManager
            ->get('alpineBindings')
            ->setView($oRenderer->setHelperPluginManager($oViewHelperPluginManager));

        $formElementManager = $serviceManager->get('FormElementManager');
        $this->form = $formElementManager->get(Form::class, ['theme' => 'default']);
        $inputFilter = new InputFilter();
        $this->form->setInputFilter($inputFilter);
    }

    public function testRendersBindings()
    {
        $element = new Element\Text('email');
        $element->setAttributes([
            'type' => 'text',
            'id' => 'email',
            'placeholder' => 'you@example.com',
        ]);
        $element->setLabel('Email');
        $this->form->add($element);

        $markup = $this->helper->__invoke($this->form);

        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/alpine_form_binding.txt', $markup);
    }

    public function testRendersBindingsWithData()
    {
        $element = new Element\Email('email');
        $element->setAttributes([
            'type' => 'text',
            'id' => 'email',
            'placeholder' => 'you@example.com',
        ]);
        $element->setLabel('Email');
        $this->form->add($element);
        $this->form->setData(['email' => 'invalid']);
        $this->form->isValid();

        $markup = $this->helper->__invoke($this->form);

        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/alpine_form_binding_with_data.txt', $markup);
    }

    public function testRendersBindingsButNoButtons()
    {
        $this->form->add([
            'name' => 'email',
            'type' => Element\Email::class,
            'attributes' => [
                'type' => 'text',
                'id' => 'email',
                'placeholder' => 'you@example.com',
                'value' => 'test@example.com',
            ],
        ]);

        $this->form->add([
            'name' => 'id',
            'type' => Element\Hidden::class,
            'attributes' => [
                'value' => 12,
            ],
        ]);

        $this->form->add([
            'name' => 'cancel',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Cancel',
            ],
        ]);

        $this->form->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'options' => [
                'value' => 'submit',
            ],
        ]);

        $this->form->add([
            'name' => 'intvalue',
            'type' => Toggle::class,
            'options' => [
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
            'attributes' => [
                'value' => '1',
            ],
        ]);

        $inputFilter = $this->form->getInputFilter();
        $inputFilter->add([
            'name' => 'intvalue',
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->form->get('id')->getValue();
        $markup = $this->helper->__invoke($this->form);
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/alpine_form_binding_without_buttons.txt', $markup);
    }
}

