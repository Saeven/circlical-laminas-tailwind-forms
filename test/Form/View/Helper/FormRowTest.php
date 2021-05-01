<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Circlical\TailwindFormsTest\Form\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\Form\View\Helper\FormRow;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\StringLength;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Circlical\TailwindFormsTest\Bootstrap;

class FormRowTest extends TestCase
{
    public ?FormRow $helper;

    private ?Form $form;

    protected function setUp(): void
    {
        $serviceManager = Bootstrap::getServiceManager();
        $oViewHelperPluginManager = $serviceManager->get('ViewHelperManager');
        $oRenderer = new PhpRenderer();
        $this->helper = $oViewHelperPluginManager
            ->get('formRow')
            ->setView($oRenderer->setHelperPluginManager($oViewHelperPluginManager));

        $formElementManager = $serviceManager->get('FormElementManager');
        $this->form = $formElementManager->get(Form::class, ['theme' => 'default']);
        $inputFilter = new InputFilter();
        $this->form->setInputFilter($inputFilter);
    }

    public function testRendersTextFieldWithLabelInRow()
    {
        $element = new Element\Text('email');
        $element->setAttributes([
            'type' => 'text',
            'id' => 'email',
            'placeholder' => 'you@example.com',
        ]);
        $element->setLabel('Email');
        $this->form->add($element);

        $markup = $this->helper->render($element);

        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/text_row_label.txt', $markup);
    }

    public function testRendersTextFieldInRow()
    {
        $this->form->add([
            'name' => 'email',
            'type' => Element\Text::class,
            'attributes' => [
                'id' => 'email',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('email'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/text_row.txt', $markup);
    }

    public function testRendersErrorMessages()
    {
        $this->form->add([
            'name' => 'email',
            'type' => Element\Email::class,
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => 'Email',
            ],
        ]);
        $this->form->getInputFilter()->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 255,
                    ],
                ],
            ],
        ]);
        $this->form->setData(['email' => 'helloworld']);
        $this->form->isValid();

        $markup = $this->helper->render($this->form->get('email'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/text_row_label_error.txt', $markup);
    }

    public function testRendersButtons()
    {
        $this->form->add([
            'name' => 'foo',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Submit',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('foo'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/button_row.txt', $markup);
    }

    public function testRendersThemedButtons()
    {
        $this->form->add([
            'name' => 'foo',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Submit',
                Form::BUTTON_TYPE => 'primary',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('foo'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/button_primary_row.txt', $markup);
    }

    public function testRendersThemedButtonsWithAddedClasses()
    {
        $this->form->add([
            'name' => 'foo',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Submit',
                Form::BUTTON_TYPE => 'primary',
                FORM::ADD_CLASSES => 'w-full',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('foo'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/button_primary_row_full.txt', $markup);
    }

    public function testIgnoresHiddenFields()
    {
        $this->form->add([
            'name' => 'foo',
            'type' => Element\Hidden::class,
            'attributes' => [
                'value' => "bar",
            ],
        ]);

        $markup = $this->helper->render($this->form->get('foo'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/hidden_row.txt', $markup);
    }

    public function testSupportsAlpineMode()
    {
        $this->form->setGenerateAlpineMarkup(true);
        $this->form->add([
            'name' => 'email',
            'type' => Element\Text::class,
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => "Email",
            ],
        ]);

        $markup = $this->helper->render($this->form->get('email'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/alpine_text_row.txt', $markup);
    }
}