<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'LinkoScope',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $isGuest = Yii::$app->user->isGuest;
    $id = Yii::$app->user->id;
    $userId = $isGuest ? '' : Yii::$app->user->getIdentity()->username;
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            [
                'label' => 'Links',
                'url' => ['/link'],
                'visible' => $isGuest || Yii::$app->user->getIdentity()->username != 'admin'
            ],
            ['label' => 'Login', 'url' => ['/site/login'], 'visible' => $isGuest],
            [
                'label' => "Logout ($userId) ($id)",
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post'],
                'visible' => !$isGuest,
            ]
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?php if (Yii::$app->session->hasFlash('error')) : ?>
            <p style="background-color:red;">
                Error: <?= Yii::$app->session->getFlash('error'); ?>
            </p>
        <?php endif ?>
        <?php if (Yii::$app->session->hasFlash('info')) : ?>
            <p style="background-color:green;">
                Info: <?= Yii::$app->session->getFlash('info'); ?>
            </p>
        <?php endif ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
