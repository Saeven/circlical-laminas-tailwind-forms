<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Circlical\TailwindFormsTest\Form\View\Helper;

use Circlical\TailwindForms\Form\ChildForm;
use Circlical\TailwindForms\Form\Form;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilter;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Circlical\TailwindFormsTest\Bootstrap;

class FormRowTest extends TestCase
{
    public $helper;

    private $form;

    protected function setUp(): void
    {
        $serviceManager = Bootstrap::getServiceManager();
        $oViewHelperPluginManager = $serviceManager->get('ViewHelperManager');
        $oRenderer = new PhpRenderer();
        $this->helper = $oViewHelperPluginManager
            ->get('formRow')
            ->setView($oRenderer->setHelperPluginManager($oViewHelperPluginManager));

        $formElementManager = $serviceManager->get('FormElementManager');
        $this->form = $formElementManager->get(ChildForm::class, ['theme' => 'default']);
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

    public function getMultiElements()
    {
        return [
//            ['radio', 'input', 'type="radio"'],
//            ['multi_checkbox', 'input', 'type="checkbox"'],
//            ['select', 'option', '<select'],
        ];
    }

    /**
     * @dataProvider getMultiElements
     * @group        multi
     */
//    public function testRendersMultiElementsAsExpected($type, $inputType, $additionalMarkup)
//    {
//        if ($type === 'radio') {
//            $element = new Element\Radio('foo');
//            $this->assertEquals('radio', $element->getAttribute('type'));
//        } elseif ($type === 'multi_checkbox') {
//            $element = new Element\MultiCheckbox('foo');
//            $this->assertEquals('multi_checkbox', $element->getAttribute('type'));
//        } elseif ($type === 'select') {
//            $element = new Element\Select('foo');
//            $this->assertEquals('select', $element->getAttribute('type'));
//        } else {
//            $element = new Element('foo');
//        }
//        $element->setAttribute('type', $type);
//        $element->setValueOptions([
//            'value1' => 'option',
//            'value2' => 'label',
//            'value3' => 'last',
//        ]);
//        $element->setAttribute('value', 'value2');
//        $markup = $this->helper->render($element);
//
//        $this->assertEquals(3, substr_count($markup, '<' . $inputType), $markup);
//        $this->assertStringContainsString($additionalMarkup, $markup);
//        if ($type == 'select') {
//            $this->assertMatchesRegularExpression('#value="value2"[^>]*?(selected="selected")#', $markup);
//        }
//    }
}