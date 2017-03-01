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
            'label' => '时间计划', 
            'items' => [
                ['label' => '计划管理', 'url' => ['/frontend/gantt/index']],
                ['label' => '时间管理', 'url' => ['/frontend/scheduler/index']]
            ]
        ],
        ['label' => '知识网络', 'url' => ['/site/contact']],
        [
            'label' => "后台管理",
            'items' => [
                ['label' => '配置管理', 'url' => ['/frontend/config/index']],
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
