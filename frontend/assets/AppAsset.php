<?php

namespace frontend\assets;

use Yii;
use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/normalize.css',
	    '//fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap',
	    'css/slick.css',
	    'css/style_new.css',
	    'css/programmer.css',
	    '//unpkg.com/aos@2.3.1/dist/aos.css',
	    ['https://fonts.googleapis.com', 'rel' => 'preconnected'],
	    ['https://fonts.gstatic.com', 'rel' => 'preconnected', 'crossorigin' => true],
	    '//cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css',
	    '//cdn.jsdelivr.net/npm/suggestions-jquery@21.6.0/dist/css/suggestions.min.css',
	    '//unpkg.com/swiper@8/swiper-bundle.min.css',
    ];
    public $js = [
    	    'js/slick.min.js',
	    'js/jquery.maskedinput.min.js',
	    'js/programmer_new.js',
	    'js/main.js',
	    '//unpkg.com/aos@2.3.1/dist/aos.js',
	    '//cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js',
	    '//cdn.jsdelivr.net/npm/suggestions-jquery@21.8.0/dist/js/jquery.suggestions.min.js',
	    '//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU',
	    ['https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js', 'depends' => JqueryAsset::class],
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
	    JqueryAsset::class,
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
