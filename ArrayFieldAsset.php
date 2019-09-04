<?php
/**
 * @link https://github.com/Tigrov/yii2-array-field
 * @author Sergei Tigrov <rrr-r@ya.ru>
 */
namespace tigrov\arrayField;

/**
 * This asset bundle provides the necessary asset files for ArrayField.
 */
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