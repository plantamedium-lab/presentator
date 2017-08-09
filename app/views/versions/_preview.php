<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Project;
use common\models\Screen;
use common\components\helpers\CFileHelper;

/**
 * $project        \common\models\Project
 * $activeVersion  \common\models\Version
 * $activeScreenId integer|null
 * $allowComment   boolean
 */

$activeScreenId = isset($activeScreenId) ? $activeScreenId : null;
$allowComment   = isset($allowComment) ? $allowComment : false;

if ($project->type == Project::TYPE_TABLET) {
    $projectTypeClass = 'tablet';
} elseif ($project->type == Project::TYPE_MOBILE) {
    $projectTypeClass = 'mobile';
} else {
    $projectTypeClass = 'desktop';
}

$generalSlideStyles = [];
if ($project->subtype && !empty(Project::SUBTYPES[$project->subtype])) {
    $generalSlideStyles['width']  = Project::SUBTYPES[$project->subtype][0] . 'px';
    $generalSlideStyles['height'] = Project::SUBTYPES[$project->subtype][1] . 'px';
}

$hasScreens = !empty($activeVersion->screens);
?>

<div id="version_slider_<?= $activeVersion->id ?>"
    data-version-id="<?= $activeVersion->id ?>"
    class="version-slider <?= $projectTypeClass ?>"
>
    <div class="version-slider-panel control-panel">
        <div class="panel-content">
            <div id="preview_thumbs_container" class="preview-thumbs" style="display: none;">
                <?php foreach ($activeVersion->screens as $screen): ?>
                    <div class="box preview-thumb" data-screen-id="<?= $screen->id ?>" data-cursor-tooltip="<?= Html::encode($screen->title) ?>">
                        <div class="content">
                            <figure class="featured">
                                <img class="img lazy-load"
                                    alt="<?= Html::encode($screen->title) ?>"
                                    data-src="<?= $screen->getThumbUrl('small') ?>"
                                    data-nocache="1"
                                    data-priority="low"
                                >
                            </figure>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <nav class="panel-menu">
                   <div class="ctrl-wrapper ctrl-left">
                <ul>
                    <!--
                    <li id="slider_prev_handle" class="ctrl-item slider-nav-handle slider-prev"><i class="ion ion-android-arrow-back"></i></li>
                    <li class="ctrl-item info-handle">
                        <i class="ion ion-ios-information-outline"></i>
                        <div class="dropdown-menu info-dropdown">
                            <h6 class="title m-b-10" title="<?= Html::encode($project->title) ?>"><?= Html::encode($project->title) ?></h6>
                            <span class="hint"><?= Yii::t('app', 'Project admins') ?></span>
                            <?php foreach ($project->users as $user): ?>
                                <div class="table-wrapper project-user">
                                    <div class="table-cell min-width">
                                        <figure class="avatar small">
                                            <img data-src="<?= $user->getAvatarUrl(true) ?>" alt="Avatar" class="lazy-load" data-priority="low">
                                        </figure>
                                    </div>
                                    <div class="table-cell p-l-10 max-width name">
                                        <?= Html::encode($user->getIdentificator()) ?>
                                    </div>
                                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->email == $user->email): ?>
                                        <div class="table-cell p-l-10 min-width">
                                            <a href="<?= Url::to(['site/index']) ?>" data-cursor-tooltip="<?= Yii::t('app', 'Dashboard') ?>">
                                                <i class="ion ion-android-home"></i>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <div class="table-cell p-l-10 min-width">
                                        <a href="mailto: <?= Html::encode($user->email) ?>" data-cursor-tooltip="<?= Yii::t('app', 'Send an email') ?>">
                                            <i class="ion ion-ios-email"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </li>
                    -->
                </ul>
            </div>

            <?php if ($hasScreens): ?>
                <div class="ctrl-wrapper ctrl-center">
                    <ul>
                    <li id="slider_prev_handle" class="ctrl-item slider-nav-handle slider-prev"><i class="ion ion-android-arrow-back"></i></li>
                    <li class="ctrl-item info-handle">
                        <i class="ion ion-ios-information-outline"></i>
                        <div class="dropdown-menu info-dropdown">
                            <h6 class="title m-b-10" title="<?= Html::encode($project->title) ?>"><?= Html::encode($project->title) ?></h6>
                            <span class="hint"><?= Yii::t('app', 'Project admins') ?></span>
                            <?php foreach ($project->users as $user): ?>
                                <div class="table-wrapper project-user">
                                    <div class="table-cell min-width">
                                        <figure class="avatar small">
                                            <img data-src="<?= $user->getAvatarUrl(true) ?>" alt="Avatar" class="lazy-load" data-priority="low">
                                        </figure>
                                    </div>
                                    <div class="table-cell p-l-10 max-width name">
                                        <?= Html::encode($user->getIdentificator()) ?>
                                    </div>
                                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->email == $user->email): ?>
                                        <div class="table-cell p-l-10 min-width">
                                            <a href="<?= Url::to(['site/index']) ?>" data-cursor-tooltip="<?= Yii::t('app', 'Dashboard') ?>">
                                                <i class="ion ion-android-home"></i>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <div class="table-cell p-l-10 min-width">
                                        <a href="mailto: <?= Html::encode($user->email) ?>" data-cursor-tooltip="<?= Yii::t('app', 'Send an email') ?>">
                                            <i class="ion ion-ios-email"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </li>



                        <?php if (count($project->versions) > 1): ?>
                            <li id="panel_versions_handle" class="ctrl-item versions-handle" data-cursor-tooltip="<?= Yii::t('app', 'Change versions') ?>">
                                <select class="versions-select selectify-select">
                                    <?php foreach ($project->versions as $i => $version): ?>
                                        <option value="<?= $version->id ?>" <?= $version->id === $activeVersion->id ? 'selected' : '' ?>>
                                            <?php if ($version->title): ?>
                                                <?= Html::encode($version->title) ?>
                                            <?php else: ?>
                                                v.<?= $i + 1 ?>
                                            <?php endif ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </li>
                        <?php endif ?>

                        <?php if ($allowComment): ?>
                            <li id="panel_preview_handle" class="ctrl-item preview-handle active" data-cursor-tooltip="<?= Yii::t('app', 'Preview mode') ?>" data-cursor-tooltip-class="hotspots-mode-tooltip">
                                <i class="ion ion-ios-eye-outline"></i>
                            </li>
                            <li id="panel_comments_handle" class="ctrl-item comments-handle" data-cursor-tooltip="<?= Yii::t('app', 'Comments mode') ?>" data-cursor-tooltip-class="comments-mode-tooltip">
                                <i class="ion ion-ios-chatboxes-outline"></i>
                                <span class="bubble comments-counter">0</span>
                            </li>
                        <?php endif ?>

                        <li id="panel_screens_handle" class="ctrl-item screens-handle" data-cursor-tooltip="<?= Yii::t('app', 'All screens') ?>">
                            <i class="ion ion-ios-photos active-icon"></i>
                            <i class="ion ion-ios-photos-outline inactive-icon"></i>
                        </li>
                        <li id="slider_next_handle" class="ctrl-item slider-nav-handle slider-next"><i class="ion ion-android-arrow-forward"></i></li>

                    </ul>
                </div>
            <?php endif; ?>

            <div class="ctrl-wrapper ctrl-right">
                <ul>
                    <!--
                     <li id="slider_next_handle" class="ctrl-item slider-nav-handle slider-next"><i class="ion ion-android-arrow-forward"></i></li>
                     -->

                    <li class="ctrl-item ">
                         <a href="http://www.plantamedium.de"><img class="logo-pm logo-large" src="/images/logo-plantamedium-large.png" alt="Logo Plantamedium"></a>
                         <a href="http://www.plantamedium.de"><img class="logo-pm logo-medium" src="/images/logo-plantamedium-medium.png" alt="Logo Plantamedium"></a>
                         <a href="http://www.plantamedium.de"><img class="logo-pm logo-small" src="/images/logo-plantamedium-small.png" alt="Logo Plantamedium"></a>
                            
                    </li>
                    
                </ul>
            </div>
        </nav>
    </div>

    <div class="version-slider-content">
        <span id="panel_toggle_handle" class="version-slider-panel-toggle" data-collapsed-text="<?= Yii::t('app', 'Menu') ?>" data-expanded-text="<?= Yii::t('app', 'Hide') ?>"></span>

        <?php if (!$hasScreens): ?>
            <div class="block text-center m-t-30 m-b-30 padded panel panel-sm">
                <h5><?= Yii::t('app', 'Oops, the selected version does not have any screens.') ?></h5>

                <?php if (count($project->versions) > 1): ?>
                    <p><?= Yii::t('app', 'Choose another version') ?></p>
                    <select class="versions-select selectify-select">
                        <?php foreach ($project->versions as $i => $version): ?>
                            <option value="<?= $version->id ?>" <?= $version->id === $activeVersion->id ? 'selected' : '' ?>>
                                <?php if ($version->title): ?>
                                    <?= Html::encode($version->title) ?>
                                <?php else: ?>
                                    v.<?= $i + 1 ?>
                                <?php endif ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                <?php endif ?>
            </div>
        <?php else: ?>
            <div class="slider-caption-wrapper">
                <div class="slider-caption">
                    <h3 class="title active-slide-title"></h3>
                    <span class="slide-counter"><?= Yii::t('app', 'Screen')?>&nbsp;<span class="active-slide-order"></span>&nbsp;<?= Yii::t('app', 'of') ?>&nbsp;<?= count($activeVersion->screens) ?></span>
                </div>
            </div>

            <div class="slider-items">
                <?php foreach ($activeVersion->screens as $i => $screen): ?>
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
                        $scaleFactor = $project->getScaleFactor($width);

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
                                        data-link="<?= Html::encode(ArrayHelper::getValue($spot, 'link', '')); ?>"
                                        style="width: <?= (float) (ArrayHelper::getValue($spot, 'width', 0) / $scaleFactor); ?>px; height: <?= (float) (ArrayHelper::getValue($spot, 'height', 0) / $scaleFactor); ?>px; top: <?= (float) (ArrayHelper::getValue($spot, 'top', 0) / $scaleFactor); ?>px; left: <?= (float) (ArrayHelper::getValue($spot, 'left', 0) / $scaleFactor); ?>px"
                                    >
                                    </div>
                                <?php endforeach ?>
                            </div>

                            <!-- Comment targets -->
                            <div id="comment_targets_list" class="comment-targets-list">
                                <?php foreach ($screen->primaryScreenComments as $comment): ?>
                                    <div class="comment-target"
                                        data-comment-id="<?= $comment->id ?>"
                                        style="left: <?= (float) ($comment->posX / $scaleFactor) ?>px; top: <?= (float) ($comment->posY / $scaleFactor) ?>px;"
                                    ></div>
                                <?php endforeach ?>
                            </div>
                        </figure>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif; ?>

        <?php if ($allowComment): ?>
            <?= $this->render('_comments_popover') ?>
        <?php endif ?>
    </div>
</div>
