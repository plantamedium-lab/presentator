<?php
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $name    string
 * @var $message string
 * @var $exception Exception
 */

$this->title = $name;
?>

<div class="table-wrapper full-vh-height">
    <div class="table-cell text-center">
        <div class="site-error panel panel-md padded">
            <a href="<?= Url::home() ?>" class="logo">
                <img src="/images/logo_large.png" alt="Plantamedium logo">
            </a>
            <div class="clearfix m-b-15"></div>

            <article class="content">
                <h1><?= Html::encode($this->title) ?></h1>

                <div class="alert alert-danger">
                    <?= nl2br(Html::encode($message)) ?>
                </div>
                <p>
                    <?= Yii::t('app', 'The above error occurred while the Web server was processing your request.') ?>
                </p>
                <p>
                    <?= Yii::t('app', 'Please contact us if you think this is a server error. Thank you.') ?>
                </p>
            </article>
            <div class="clearfix m-b-30"></div>

            <a href="<?= Url::home() ?>" class="btn btn-primary">
                <i class="ion ion-android-arrow-back m-r-5"></i>
                <span class="txt"><?= Yii::t('app', 'Back to homepage') ?></span>
            </a>
        </div>
    </div>
</div>

