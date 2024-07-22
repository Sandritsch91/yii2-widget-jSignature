<?php

namespace sandritsch91\yii2\jSignature;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class JSignature extends InputWidget
{
    /**
     * The format of the signature passed to the server.
     *
     * The possible values are:
     * - 'default' - The signature is passed as base64: data:image/png;base64,... (not recommended, VERY big strings)
     * - 'native' - The signature is passed as a json of x and y coordinates
     * - 'base30' - The signature is passed as base30: image/jSignature;base30,... (use this if small size is important)
     * - 'svg' - The signature is passed as an svg
     * - 'svgbase64' - The signature is passed as an svg, base64 encoded
     * - 'image' - The signature is passed as an image: image/png;base64,... (alias of default)
     *
     * Defaults to 'svgbase64'.
     *
     * For more information see the jSignature plugin documentation
     *
     * @var string
     */
    public string $format = 'svgbase64';

    /**
     * The class used to generate the form field.
     * @var string|Html
     */
    public string|Html $htmlClass = 'yii\helpers\Html';

    /**
     * @var array the event handlers for the underlying JS plugin.
     */
    public array $clientEvents = [];

    /**
     * Html attributes for the wrapper div.
     * @var array
     */
    public array $wrapperOptions = [];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->hasModel()) {
            $id = $this->options['id'] ?? $this->htmlClass::getInputId($this->model, $this->attribute);
            $name = $this->htmlClass::getInputName($this->model, $this->attribute);
            $formatName = $this->htmlClass::getInputName($this->model, $this->attribute . '_format');
        } else {
            $id = $this->options['id'] ?? $this->getId();
            $name = $this->name;
            $formatName = $name . '_format';
        }

        $this->options['id'] = $id;
        $this->wrapperOptions = ArrayHelper::merge($this->wrapperOptions, [
            'class' => 'jSignature-wrapper'
        ]);
        $this->wrapperOptions['id'] = $this->wrapperOptions['id'] ?? $id . '-wrapper';

        // handle value
        $value = $this->hasModel() ? $this->model->{$this->attribute} : $this->value;
        if ($value !== '' && $this->format !== 'native' && $this->format !== 'base30') {
            $value = '';
            \Yii::error('The signature value is not empty, but the format is not native or base30. The value will be ignored.');
        }
        $this->value = is_array($value) ? Json::encode($value) : $value;

        // render html
        $html = $this->htmlClass::tag('div', '', $this->wrapperOptions) .
            $this->htmlClass::hiddenInput($name, '', $this->options) .
            $this->htmlClass::hiddenInput($formatName, $this->format);

        $this->registerClientScript();
        return $html;
    }

    /**
     * Registers the needed JavaScript.
     * @return void
     */
    protected function registerClientScript(): void
    {
        $wrapperId = $this->wrapperOptions['id'];
        $id = $this->options['id'];

        // register plugin source scripts
        $view = $this->getView();
        JSignatureAsset::register($view);

        // init js plugin
        $var = Inflector::variablize($wrapperId);
        $js = <<<JS
window.$var = jQuery("#$wrapperId");
window.$var.jSignature();
var {$var}_value = '$this->value';
if ({$var}_value !== '') {
    var {$var}_format = '';
    switch ('$this->format') {
        case 'native':
            window.$var.jSignature('setData', JSON.parse({$var}_value), '$this->format');
            break;
        case 'base30':
            window.$var.jSignature('setData', 'data:image/jsignature;base30,' + {$var}_value);
            break;
       default:
           throw 'format not supported: $this->format'
    }
}
JS;
        $this->view->registerJs($js);

        // pass signature to hidden input
        $js = <<<JS
jQuery("#$wrapperId").on('change', function () {
    var data = window.$var.jSignature('getData', '$this->format');
    switch ('$this->format') {
        case 'default':
        case 'image':
            break;
        case 'native':
            data = JSON.stringify(data);
            break;
        case 'base30':
        case 'svg':
        case 'svgbase64':
            data = data[1];
            break;
        default:
            throw 'Unknown format: $this->format';
    }
    jQuery('#$id').val(data);
});
JS;
        $this->view->registerJs($js);

        // custom client events
        foreach ($this->clientEvents as $event => $handler) {
            $js = <<<JS
jQuery("#$wrapperId").on('$event', $handler);
JS;
            $this->view->registerJs($js);
        }
    }
}
