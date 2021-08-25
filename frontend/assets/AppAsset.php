<?php

namespace frontend\assets;

use Yii;
use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/normalize.css',
	    'https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap',
	    'css/slick.css',
	    'css/style.css',
	    ['https://fonts.googleapis.com', 'rel' => 'preconnected'],
	    ['https://fonts.gstatic.com', 'rel' => 'preconnected', 'crossorigin' => true],
    ];
    public $js = [
    	'js/slick.min.js',
	    'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];

    public function init()
    {
	    parent::init();
	    Yii::$app->assetManager->bundles[BootstrapAsset::class] = [
	    	'css' => [],
		    'js' => [],
	    ];
    }
}
