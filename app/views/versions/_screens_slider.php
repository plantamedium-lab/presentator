<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Project;
use common\models\Screen;
use common\components\helpers\CFileHelper;

/**
 * $model                \common\models\Version
 * $activeScreenId       integer|null
 * $unreadCommentTargets array
 */

// set default values
$unreadCommentTargets = isset($unreadCommentTargets) ? $unreadCommentTargets : [];
$activeScreenId       = isset($activeScreenId) ? $activeScreenId : null;

if ($model->project->type == Project::TYPE_TABLET) {
    $type = 'tablet';
} elseif ($model->project->type == Project::TYPE_MOBILE) {
    $type = 'mobile';
} else {
    $type = 'desktop';
}

$generalSlideStyles = [];
if ($model->project->subtype && !empty(Project::SUBTYPES[$model->project->subtype])) {
    $generalSlideStyles['width']  = Project::SUBTYPES[$model->project->subtype][0] . 'px';
    $generalSlideStyles['height'] = Project::SUBTYPES[$model->project->subtype][1] . 'px';
}

$isGuest = Yii::$app->user->isGuest;
?>

<div id="version_slider_<?= $model->id ?>"
    class="version-slider <?= $type ?>"
    data-version-id="<?= $model->id ?>"
>
    <div class="version-slider-panel control-panel">
        <nav class="panel-menu">
            <div class="ctrl-wrapper ctrl-left">
                <ul>
                    <li id="slider_prev_handle" class="ctrl-item slider-nav-handle slider-prev"><i class="ion ion-android-arrow-back"></i></li>
                     
                </ul>
            </div>
            <div class="ctrl-wrapper ctrl-center">
                <ul>
                    <li id="panel_hotspots_handle" class="ctrl-item hotspots-handle active" data-cursor-tooltip="<?= Yii::t('app', 'Hotspots mode') ?>" data-cursor-tooltip-class="hotspots-mode-tooltip"><i class="ion ion-ios-crop"></i></li>
                    <li id="panel_comments_handle" class="ctrl-item comments-handle" data-cursor-tooltip="<?= Yii::t('app', 'Comments mode') ?>" data-cursor-tooltip-class="comments-mode-tooltip">
                        <i class="ion ion-ios-chatboxes-outline"></i>
                        <span class="bubble comments-counter">0</span>
                    </li>
                    <li id="panel_settings_handle" class="ctrl-item settings-handle" data-cursor-tooltip="<?= Yii::t('app', 'Screen settings') ?>"><i class="ion ion-ios-gear-outline"></i></li>
                <ul>
            </div>

            <div class="ctrl-wrapper ctrl-right">
                <ul>
                    <li id="slider_next_handle" class="ctrl-item slider-nav-handle slider-next"><i class="ion ion-android-arrow-forward"></i></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="version-slider-content">
        <div class="close-handle-wrapper">
            <span class="close-handle close-screen-edit"><i class="ion ion-ios-close-empty"></i></span>
        </div>
        <div class="slider-items">
            <?php foreach ($model->screens as $i => $screen): ?>
                <?php
                    if ($activeScreenId === null && $i === 0) {
                        $isActive = true;
                    } else {
                        $isActive = $activeScreenId !== null && $activeScreenId == $screen->id;
                    }

                    // alignment
                    if ($screen->alignment == Screen::ALIGNMENT_LEFT) {
                        $align = 'left';
                    } elseif ($screen->alignment == Screen::ALIGNMENT_RIGHT) {
                        $align = 'right';
                    } else {
                        $align = 'center';
                    }

                    $background = ($screen->background ? $screen->background : '#eff2f8');

                    // image dimensions
                    $width  = 0;
                    $height = 0;
                    if (file_exists(CFileHelper::getPathFromUrl($screen->imageUrl))) {
                        list($width, $height) = getimagesize(CFileHelper::getPathFromUrl($screen->imageUrl));
                    }

                    // scaling
                    $scaleFactor = $model->project->getScaleFactor($width);

                    // hotspots
                    $hotspots = $screen->hotspots ? json_decode($screen->hotspots, true) : [];
                ?>
                <div class="slider-item screen <?= $isActive ? 'active' : ''?>"
                    data-scale-factor="<?= $scaleFactor ?>"
                    data-screen-id="<?= $screen->id ?>"
                    data-alignment="<?= $align ?>"
                    data-title="<?= Html::encode($screen->title) ?>"
                    style="<?= Html::cssStyleFromArray(array_merge($generalSlideStyles, ['background' => $background])) ?>"
                >
                    <figure class="img-wrapper hotspot-layer-wrapper">
                        <img class="img lazy-load hotspot-layer"
                            alt="<?= Html::encode($screen->title) ?>"
                            width="<?= $width / $scaleFactor ?>px"
                            height="<?= $height / $scaleFactor ?>px"
                            data-src="<?= $screen->imageUrl ?>"
                            data-priority="<?= $isActive ? 'high' : 'medium' ?>"
                        >

                        <!-- Hotspots -->
                        <div id="hotspots_wrapper">
                            <?php foreach ($hotspots as $id => $spot): ?>
                                <div id="<?= Html::encode($id) ?>"
                                    class="hotspot"
                                    data-context-menu="#hotspot_context_menu"
                                    data-link="<?= Html::encode(ArrayHelper::getValue($spot, 'link', '')); ?>"
                                    style="width: <?= (float) (ArrayHelper::getValue($spot, 'width', 0) / $scaleFactor); ?>px; height: <?= (float) (ArrayHelper::getValue($spot, 'height', 0) / $scaleFactor); ?>px; top: <?= (float) (ArrayHelper::getValue($spot, 'top', 0) / $scaleFactor); ?>px; left: <?= (float) (ArrayHelper::getValue($spot, 'left', 0) / $scaleFactor); ?>px"
                                >
                                    <span class="remove-handle context-menu-ignore"><i class="ion ion-trash-a"></i></span>
                                    <span class="resize-handle context-menu-ignore"></span>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <!-- Comment targets -->
                        <div id="comment_targets_list" class="comment-targets-list">
                            <?php foreach ($screen->screenComments as $comment): ?>
                                <?php if (!$comment->replyTo): // we make use of the already eager loaded screenComments relation ?>
                                    <div class="comment-target <?= (in_array($comment->id, $unreadCommentTargets)) ? 'unread' : '' ?>"
                                        data-comment-id="<?= $comment->id ?>"
                                        style="left: <?= (float) ($comment->posX / $scaleFactor) ?>px; top: <?= (float) ($comment->posY / $scaleFactor) ?>px;"
                                    ></div>
                                <?php endif ?>
                            <?php endforeach ?>
                        </div>
                    </figure>
                </div>
            <?php endforeach ?>
        </div>

        <?= $this->render('_hotspots_popover', ['screens' => $model->screens]); ?>

        <?= $this->render('_comments_popover'); ?>

        <div id="hotspots_bulk_panel" class="fixed-panel hotspots-bulk-panel" style="display: none;">
            <span class="close hotspots-bulk-reset"><i class="ion ion-close"></i></span>

            <div class="table-wrapper">
                <div class="table-cell min-width">
                    <button id="hotspots_bulk_screens_select" type="button" class="btn btn-sm btn-primary btn-ghost hotspots-bulk-screens-btn">
                        <?= Yii::t('app', 'Duplicate on screen') ?>
                        <i class="ion ion-android-arrow-dropdown m-l-5"></i>

                        <div id="hotspots_bulk_screens_popover" class="popover hotspots-bulk-screens-popover bottom-left">
                            <div class="popover-thumbs-wrapper">
                                <?php foreach ($model->screens as $screen): ?>
                                    <div class="box popover-thumb" data-screen-id="<?= $screen->id ?>" data-cursor-tooltip="<?= Html::encode($screen->title) ?>">
                                        <div class="content">
                                            <figure class="featured">
                                                <img class="img lazy-load"
                                                    alt="<?= Html::encode($screen->title) ?>"
                                                    data-src="<?= $screen->getThumbUrl('small') ?>"
                                                    data-priority="low"
                                                >
                                            </figure>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </button>
                </div>
                <div class="table-cell min-width p-l-15 p-r-15"><?= Yii::t('app', 'or') ?></div>
                <div class="table-cell min-width">
                    <a href="#" id="hotspots_bulk_delete" class="danger-link"><?= Yii::t('app', 'Delete selected') ?></a>
                </div>
                <div class="table-cell text-right">
                    <a href="#" class="hotspots-bulk-reset"><?= Yii::t('app', 'Reset selection') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
