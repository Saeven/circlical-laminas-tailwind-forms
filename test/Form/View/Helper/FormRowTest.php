<?php

/**
 * @see       https://github.com/laminas/laminas-form for the canonical source repository
 * @copyright https://github.com/laminas/laminas-form/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-form/blob/master/LICENSE.md New BSD License
 */

namespace Circlical\TailwindFormsTest\Form\View\Helper;

use Circlical\TailwindForms\Form\Element\Toggle;
use Circlical\TailwindForms\Form\Form;
use Circlical\TailwindForms\Form\View\Helper\FormRow;
use Circlical\TailwindFormsTest\Bootstrap;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\StringLength;
use Laminas\View\Renderer\PhpRenderer;
use PHPUnit\Framework\TestCase;

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
                Form::ADD_CLASSES => 'w-full',
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

    public function testSupportsAlpineModeWithHelpBlocks()
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
                'help-block' => "This is a help block.",
            ],
        ]);

        $markup = $this->helper->render($this->form->get('email'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/alpine_text_row_help_block.txt', $markup);
    }

    public function testRendersSelectFields()
    {
        $this->form->add([
            'name' => 'location',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Location',
                'value_options' => [
                    'US' => 'USA',
                    'CA' => 'Canada',
                    'EU' => 'EU',
                ],
            ],
            'attributes' => [
                'value' => 'CA',
                'id' => 'location',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('location'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/select_row.txt', $markup);
    }

    public function testRendersCheckBoxes()
    {
        $this->form->add([
            'name' => 'comments',
            'type' => Element\Checkbox::class,
            'options' => [
                'label' => "New comments",
                'help-block' => "Get notified when someones posts a comment on a posting.",
            ],
            'attributes' => [
                'id' => 'comments',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('comments'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/checkbox_row_help_block.txt', $markup);
    }

    public function testRendersToggles()
    {
        $this->form->setGenerateAlpineMarkup(true);
        $this->form->add([
            'name' => 'disable_email_notifications',
            'type' => Toggle::class,
            'options' => [
                'label' => "Disable Email Notifications",
                'help-block' => "Prevent the system from sending emails to learners, for example, when new steps are published.",
            ],
            'attributes' => [
                'id' => 'disable_email_notifications',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('disable_email_notifications'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/toggle_row_help_block.txt', $markup);
    }

    public function testRendersTextArea()
    {
        $this->form->setGenerateAlpineMarkup(true);
        $this->form->add([
            'name' => 'comment',
            'type' => Element\Textarea::class,
            'attributes' => [
                'id' => 'comment',
                'rows' => 4,
            ],
            'options' => [
                'label' => "Comment",
                'help-block' => "This is a help block.",
            ],
        ]);

        $markup = $this->helper->render($this->form->get('comment'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/alpine_textarea_row_help_block.txt', $markup);
    }

    public function testRendersRadioButtons()
    {
        $this->form->add([
            'name' => 'sso_type',
            'type' => Element\Radio::class,
            'options' => [
                'label' => 'Type',
                'help-block' => 'What kind of SSO would you like?',
                'value_options' => [
                    'none' => 'Disabled',
                    'other' => 'Foo',
                ],
                Form::OPTION_RADIO_LEGEND => 'Please select an option',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('sso_type'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/radio_row_help_block.txt', $markup);
    }

    public function testRendersRadioButtonsWithCustomLabelClasses()
    {
        $this->form->add([
            'name' => 'sso_type',
            'type' => Element\Radio::class,
            'attributes' => [
                'class' => 'hotsauce', // this overwrites the radio-option class
            ],
            'options' => [
                'label' => 'Type',
                'label_attributes' => [
                    'class' => 'milk',
                ],
                'help-block' => 'What kind of SSO would you like?',
                'value_options' => [
                    'none' => 'Disabled',
                    'other' => 'Foo',
                ],
                Form::ELEMENT_RADIO_OPTION_LABEL_CLASS => 'bbq-chimken',
                Form::ELEMENT_RADIO_GROUP_CLASS => 'default-radio-planet',
                Form::OPTION_RADIO_LEGEND => 'Please select an option',
            ],
        ]);

        $markup = $this->helper->render($this->form->get('sso_type'));
        self::assertStringMatchesFormatFile(__DIR__ . '/_templates/radio_row_help_block_custom_lc.txt', $markup);
    }
}