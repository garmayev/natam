<?php

use backend\assets\AppAsset;
use dmstr\web\AdminLteAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $content string
 */

if (Yii::$app->user->isGuest) {
    /**
     * Do not use this code in your template. Remove it.
     * Instead, use the code  $this->layout = '//main-login'; in your controller.
     */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    AppAsset::register($this);
    AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>
    <script type="text/javascript">(function (c, s, t, r, e, a, m) {
            c[e] = c[e] || function () {
                (c[e].q = c[e].q || []).push(arguments)
            }, c[e].p = r, a = s.createElement(t), m = s.getElementsByTagName(t)[0], a.async = 1, a.src = r, m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://c.sberlead.ru/clickstream.bundle.js', 'csa');
        csa('init', {analyticsId: 'ddd29733-cb08-472a-9035-f89a32cee563'}, true, true);</script>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
