<?php
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
        'brandLabel' => '进化',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $nav_items = [
        ['label' => '首页', 'url' => ['/frontend/site/index']],
        [
            'label' => '时间', 
            'items' => [
                ['label' => '项目管理', 'url' => ['/frontend/project/index']],
                ['label' => '计划管理', 'url' => ['/frontend/plan/index']],
                ['label' => '任务管理', 'url' => ['/frontend/task/index']],
                ['label' => '时间回顾', 'url' => ['/frontend/scheduler/index']]
            ]
        ],
        [
            'label'        => '目标',
            'items'        => [
                ['label'   => '目标管理', 'url'   => ['/frontend/target/index']],
                ['label'   => '领域管理', 'url'   => ['/frontend/area/index']],
                //['label' => '资产名目管理', 'url' => ['/frontend/assets/index']],
            ]
        ],
        [
            'label'      => '实体',
            'items'      => [
                ['label' => '技能管理', 'url' => ['/frontend/entity-skill/config-index']],
                ['label' => '作品管理', 'url' => ['/frontend/entity-work/index']],
                ['label' => '圈子管理', 'url' => ['/frontend/entity-circle/index']],
                ['label' => '资产管理', 'url' => ['/frontend/entity-asset/index']],
                ['label' => '探索管理', 'url' => ['/frontend/entity-quest/index']],
                ['label' => '生活管理', 'url' => ['/frontend/entity-life/index']],
            ]
        ],
        [
            'label' => "后台管理",
            'items' => [
                ['label' => '国家配置', 'url' => ['/frontend/country/index']],
                ['label' => '日常作息', 'url' => ['/frontend/daily/index']],
            ]
        ]
    ];
    if (Yii::$app->user->isGuest) {
        array_push(
            $nav_items,
            ['label' => '登录', 'url' => ['/user/security/login']],
            ['label' => '注册', 'url' => ['/user/registration/register']]
        );
    } else {
        array_push(
            $nav_items,
            [
                'label' => '退出('.Yii::$app->user->identity->username .')', 
                'url' => ['/user/security/logout'],
                ['class' => 'btn btn-link logout'],
                'linkOptions' => ['data-method' => 'post']
            ]
        );
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $nav_items
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; 火种 <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
