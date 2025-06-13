# yii2-widget-jsignature

A [jSignature](https://github.com/brinley/jSignature) widget for [yii2](https://www.yiiframework.com/)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist sandritsch91/yii2-widget-jsignature
```

or add

```json
"sandritsch91/yii2-widget-jsignature": "*"
```

to the require section of your composer.json

## Good to know

Loading a signature is only supported for the following formats:

- native
- base30

If you need to load a signature again, use one of these formats.

## Usage

### Widget

with a model:

```php
use sandritsch91\yii2\jSignature\JSignature;

echo JSignature::widget([
    'model' => $model,                          // The model to be used in the form
    'attribute' => 'signature',                 // The attribute to be used in the form
    'format' => 'svgbase64',                    // The format of the signature. Defaults to svgbase64
    'htmlClass' => yii\helpers\Html::class,     // Optional. The class used to generate the form field
    'clientOptions' => [],                      // Optional. The options for the jSignature plugin
    'clientEvents' => [                         // Optional. Pass the client events to be attached to the textarea
        'change' => 'function() { console.log("changed"); }'
    ],
    'wrapperOptions' => [...]                   // Optional. The options for the wrapper div
]);
```

with an ActiveForm:

```php
use sandritsch91\yii2\jSignature\JSignature;

echo $form->field($model, 'content')->widget(JSignature::class, [
    'format' => 'svgbase64',                    // The format of the signature. Defaults to svgbase64
    'htmlClass' => yii\helpers\Html::class,     // Optional. The class used to generate the form field
    'clientEvents' => [                         // Optional. Pass the client events to be attached to the textarea
        'change' => 'function() { console.log("changed"); }'
    ],
    'wrapperOptions' => [...]                   // Optional. The options for the wrapper div
]);
```

without a model:

```php
use sandritsch91\yii2\jSignature\JSignature;

echo JSignature::widget([
    'name' => 'myText',                         // The name of the input
    'value' => ...,                             // The value of the input, depends on the format
    'format' => 'svgbase64',                    // The format of the signature. Defaults to svgbase64
    'htmlClass' => yii\helpers\Html::class,     // Optional. The class used to generate the form field
    'clientEvents' => [                         // Optional. Pass the client events to be attached to the textarea
        'change' => 'function() { console.log("changed"); }'
    ],
    'wrapperOptions' => [...]                   // Optional. The options for the wrapper div
]);
```

### Widget options

- format: The format of the signature. Defaults to svgbase64
    - default
    - native
    - base30
    - svg
    - svgbase64
    - image
- htmlClass: The class used to generate the form field. Defaults to yii\helpers\Html::class
- clientEvents: The client events to be attached to the textarea. Defaults to []
    - change: Triggered after each stroke
- wrapperOptions: The options for the wrapper div. Defaults to []

### Helper

User the ```sandritsch91\yii2\jSignature\JSignatureHelper``` to convert the signature to a different format.

Possible conversions are:

- base30 to native
- native to svg
- base30 to svg
- native to image
- base30 to image
