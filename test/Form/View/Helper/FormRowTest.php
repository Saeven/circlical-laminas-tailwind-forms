<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Circlical\TailwindFormsTest\Form\View\Helper;

use Circlical\TailwindForms\Form\Form;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilter;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Circlical\TailwindFormsTest\Bootstrap;

class FormRowTest extends TestCase
{
    public $helper;

    protected function setUp(): void
    {
        $oViewHelperPluginManager = Bootstrap::getServiceManager()->get('ViewHelperManager');
        $oRenderer = new PhpRenderer();
        $this->helper = $oViewHelperPluginManager
            ->get('formRow')
            ->setView($oRenderer->setHelperPluginManager($oViewHelperPluginManager));
    }

    public function testRendersTextFieldWithLabelInRow()
    {
        $form = new Form();
        $form->setOptions([
            Form::ELEMENT_LABEL_CLASS => 'block text-sm font-medium text-gray-700',
            Form::ELEMENT_CLASS => 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md',
        ]);

        $element = new Element\Text('email');
        $element->setAttributes([
            'type' => 'text',
            'id' => 'email',
            'placeholder' => 'you@example.com',
        ]);
        $element->setLabel('Email');

        $form->add($element);
        $markup = $this->helper->render($element);

        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/text_row_label.txt', $markup);
    }

    public function testRendersTextFieldInRow()
    {
        $form = new Form();
        $form->setOptions([
            Form::ELEMENT_LABEL_CLASS => 'block text-sm font-medium text-gray-700',
            Form::ELEMENT_CLASS => 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md',
        ]);
        $form->add([
            'name' => 'email',
            'type' => Element\Text::class,
            'attributes' => [
                'id' => 'email',
            ],
        ]);

        $markup = $this->helper->render($form->get('email'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/text_row.txt', $markup);
    }

    public function testRendersErrorMessages()
    {
        $form = new Form();
        $form->setOptions([
            Form::ELEMENT_LABEL_CLASS => 'block text-sm font-medium text-gray-700',
            Form::ELEMENT_CLASS => 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md',
            Form::ELEMENT_ERROR_CLASS => 'mt-2 text-sm text-red-600',
        ]);
        $form->add([
            'name' => 'email',
            'type' => Element\Email::class,
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => 'Email',
            ],
        ]);
        $form->setData(['email' => 'helloworld']);
        $form->isValid();

        $markup = $this->helper->render($form->get('email'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/text_row_label_error.txt', $markup);
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