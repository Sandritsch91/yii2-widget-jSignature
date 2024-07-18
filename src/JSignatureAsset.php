<?php

namespace sandritsch91\yii2\jSignature;

use yii\web\AssetBundle;

class JSignatureAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower-asset/jsignature/libs';

    public $js = [
        'jSignature.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
}
