<?php

namespace tigrov\arrayField;

use yii\bootstrap\ActiveField;
use yii\bootstrap\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

class ArrayField extends ActiveField
{
    public $fieldWrapperOptions = ['class' => 'array-field-wrapper'];

    public $options = ['class' => 'form-group array-field-group'];

    public $showNewFields = 1;

    /** @var string javascript function with arguments ($wrapper, id, name, index). It will be called after new field added. */
    public $jsInitFunction;

    public function init()
    {
        parent::init();

        ArrayFieldAsset::register(\Yii::$app->getView());
    }

    /**
     * @return array
     */
    public function getAttributeValue()
    {
        $attribute = Html::getAttributeName($this->attribute);
        $model = $this->model;
        $value = $model->$attribute;

        return is_array($value) ? array_values($value) : [];
    }

    public function renderFields($fields)
    {
        $options = $this->fieldWrapperOptions;

        $this->parts['{input}'] = '';
        for ($i = 0, $l = count($fields) - 1; $i <= $l; ++$i) {
            $options['data']['index'] = $i;
            if ($i == $l) {
                $options['data']['id'] = $this->getInputId();
                $options['data']['name'] = Html::getInputName($this->model, $this->attribute);
                if ($this->jsInitFunction) {
                    $options['data']['init'] = $this->jsInitFunction;
                }
                $button = Html::button('+', ['class' => 'btn btn-success array-field-add']);
            } else {
                $button = Html::button('&ndash;', ['class' => 'btn btn-danger array-field-remove']);
            }

            $content = $fields[$i] . ' ' . $button;
            $this->parts['{input}'] .= Html::tag('div', $content, $options);
        }
    }

    public function prepareOptions($options, $merge = true)
    {
        if ($merge) {
            $options = array_merge($this->inputOptions, $options);
        }

        if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_INPUT) {
            $this->addErrorClassIfNeeded($options);
        }

        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);

        return $options;
    }

    public function getCount($values)
    {
        return count($values) + $this->showNewFields ?: 1;
    }

    /**
     * @inheritDoc
     */
    public function input($type, $options = [])
    {
        $options = $this->prepareOptions($options);

        $fields = [];
        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activeInput($type, $this->model, $attribute, $options);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function textInput($options = [])
    {
        $options = $this->prepareOptions($options);

        $fields = [];
        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activeTextInput($this->model, $attribute, $options);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hiddenInput($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);

        $fields = [];
        foreach ($this->getAttributeValue() as $i => $value) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activeHiddenInput($this->model, $attribute, $options);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function passwordInput($options = [])
    {
        $options = $this->prepareOptions($options);

        $fields = [];
        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activePasswordInput($this->model, $attribute, $options);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fileInput($options = [])
    {
        // https://github.com/yiisoft/yii2/pull/795
        if ($this->inputOptions !== ['class' => 'form-control']) {
            $options = array_merge($this->inputOptions, $options);
        }
        // https://github.com/yiisoft/yii2/issues/8779
        if (!isset($this->form->options['enctype'])) {
            $this->form->options['enctype'] = 'multipart/form-data';
        }

        $options = $this->prepareOptions($options, false);

        $fields = [];
        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activeFileInput($this->model, $attribute, $options);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function textarea($options = [])
    {
        $options = $this->prepareOptions($options);

        $fields = [];
        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activeTextarea($this->model, $attribute, $options);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dropDownList($items, $options = [])
    {
        $options = $this->prepareOptions($options);

        $fields = [];
        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activeDropDownList($this->model, $attribute, $items, $options);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function widget($class, $config = [])
    {
        /* @var $class \yii\base\Widget */
        $config['model'] = $this->model;
        $config['view'] = $this->form->getView();
        if (is_subclass_of($class, 'yii\widgets\InputWidget')) {
            foreach ($this->inputOptions as $key => $value) {
                if (!isset($config['options'][$key])) {
                    $config['options'][$key] = $value;
                }
            }
            $config['field'] = $this;
            if (!isset($config['options'])) {
                $config['options'] = [];
            }

            $config['options'] = $this->prepareOptions($config['options'], false);
        }

        $fields = [];
        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $config['attribute'] = $this->attribute . '[' . $i . ']';
            $fields[] = $class::widget($config);
        }

        $this->renderFields($fields);

        return $this;
    }

    /**
     * Returns the JS options for the field.
     * @return array the JS options.
     */
    protected function getClientOptions()
    {
        $attribute = Html::getAttributeName($this->attribute);
        if (!in_array($attribute, $this->model->activeAttributes(), true)) {
            return [];
        }

        $clientValidation = $this->isClientValidationEnabled();
        $ajaxValidation = $this->isAjaxValidationEnabled();

        if ($clientValidation) {
            $validators = [];
            foreach ($this->model->getActiveValidators($attribute) as $validator) {
                /* @var $validator \yii\validators\Validator */
                $js = $validator->clientValidateAttribute($this->model, $attribute, $this->form->getView());
                if ($validator->enableClientValidation && $js != '') {
                    if ($validator->whenClient !== null) {
                        $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
                    }
                    $validators[] = $js;
                }
            }
        }

        if (!$ajaxValidation && (!$clientValidation || empty($validators))) {
            return [];
        }

        $options = [];

        if (isset($this->selectors['error'])) {
            $options['error'] = $this->selectors['error'];
        } elseif (isset($this->errorOptions['class'])) {
            $options['error'] = '.' . implode('.', preg_split('/\s+/', $this->errorOptions['class'], -1, PREG_SPLIT_NO_EMPTY));
        } else {
            $options['error'] = isset($this->errorOptions['tag']) ? $this->errorOptions['tag'] : 'span';
        }

        $options['encodeError'] = !isset($this->errorOptions['encode']) || $this->errorOptions['encode'];
        if ($ajaxValidation) {
            $options['enableAjaxValidation'] = true;
        }
        foreach (['validateOnChange', 'validateOnBlur', 'validateOnType', 'validationDelay'] as $name) {
            $options[$name] = $this->$name === null ? $this->form->$name : $this->$name;
        }

        if (!empty($validators)) {
            $options['validate'] = new JsExpression('function (attribute, value, messages, deferred, $form) {' . implode('', $validators) . '}');
        }

        if ($this->addAriaAttributes === false) {
            $options['updateAriaInvalid'] = false;
        }

        $values = $this->getAttributeValue();
        for ($i = 0, $l = $this->getCount($values); $i < $l; ++$i) {
            $attribute = $this->attribute . '[' . $i . ']';
            $options = $this->getAttributeClientOptions($options, $attribute);
        }

        // only get the options that are different from the default ones (set in yii.activeForm.js)
        return array_diff_assoc($options, [
            'validateOnChange' => true,
            'validateOnBlur' => true,
            'validateOnType' => false,
            'validationDelay' => 500,
            'encodeError' => true,
            'error' => '.help-block',
            'updateAriaInvalid' => true,
        ]);
    }

    protected function getAttributeClientOptions($options, $attribute) {
        $inputID = $this->getInputId();
        $options['id'] = Html::getInputId($this->model, $attribute);
        $options['name'] = $attribute;

        $options['container'] = isset($this->selectors['container']) ? $this->selectors['container'] : ".field-$inputID";
        $options['input'] = isset($this->selectors['input']) ? $this->selectors['input'] : "#$inputID";

        return $options;
    }

    public function render($content = null)
    {
        if ($content === null) {
            if ($this->inputTemplate) {
                if (!isset($this->parts['{input}'])) {
                    $this->textInput();
                }
                $this->parts['{input}'] = strtr($this->inputTemplate, ['{input}' => $this->parts['{input}']]);
                $this->inputTemplate = null;
            }
        }
        return parent::render($content);
    }

    /**
     * @inheritDoc
     */
    public function staticControl($options = [])
    {
        $this->adjustLabelFor($options);

        $fields = [];
        foreach ($this->getAttributeValue() as $i => $value) {
            $attribute = $this->attribute . '[' . $i . ']';
            $fields[] = Html::activeStaticControl($this->model, $attribute, $options);
        }

        $this->renderFields($fields);

        return $this;
    }
}