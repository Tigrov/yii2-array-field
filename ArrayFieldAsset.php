<?php
namespace tigrov\arrayField;

class ArrayFieldAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
    public $css = [
        'array-field.css',
    ];
    public $js = [
        'array-field.js',
    ];
}