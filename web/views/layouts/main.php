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
                ['label' => '项目管理', 'url' => ['/frontend/project/index']],
                ['label' => '计划管理', 'url' => ['/frontend/plan/index']],
                ['label' => '任务管理', 'url' => ['/frontend/task/index']],
                ['label' => '时间回顾', 'url' => ['/frontend/scheduler/index']]
            ]
        ],
        [
            'label' => '知识', 
            'items' => [
                ['label' => '领域管理', 'url' => ['/frontend/knowledge-area/index']],
                ['label' => '训练管理', 'url' => ['/frontend/knowledge-links/index']],
            ]
        ],
        [
            'label' => '资产', 
            'items' => [
                ['label' => '资产管理', 'url' => ['/frontend/assets/index']],
            ]
        ],
        [
            'label' => '挑战', 
            'items' => [
                ['label' => '挑战管理', 'url' => ['/frontend/chanllege-links/index']],
            ]
        ],
        [
            'label' => '艺术', 
            'items' => [
                ['label' => '作品管理', 'url' => ['/frontend/art-work/index']],
                ['label' => '艺术管理', 'url' => ['/frontend/art-links/index']],
            ]
        ],
        [
            'label' => '组织', 
            'items' => [
                ['label' => '组织管理', 'url' => ['/frontend/organization-links/index']],
            ]
        ],
        [
            'label' => "后台管理",
            'items' => [
                ['label' => '配置管理', 'url' => ['/frontend/config/index']],
                ['label' => '技能配置', 'url' => ['/frontend/knowledge-skill/config-index']],
                ['label' => '天赋配置', 'url' => ['/frontend/talent/index']],
                ['label' => '资产配置', 'url' => ['/frontend/assets-entity/index']],
                ['label' => '挑战配置', 'url' => ['/frontend/chanllege-entity/index']],
                ['label' => '艺术配置', 'url' => ['/frontend/art-entity/index']],
                ['label' => '组织配置', 'url' => ['/frontend/organization-entity/index']],
                ['label' => '国家配置', 'url' => ['/frontend/country/index']],
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
