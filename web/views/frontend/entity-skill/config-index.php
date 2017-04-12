<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use \kartik\date\DatePicker;

$this->title = '技能配置';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/frontend/knowledge-skill/config-index.js',['depends'=>['app\assets\AppAsset']]);
?>

<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div data-pjax-timeout="1000" data-pjax-push-state="" data-pjax-container="" id="w0">
        <?php
         $gridColumns = [
                "box" => [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'box',
                    'contentOptions' => [
                        'class' => 'data-id'
                    ],
                ],
                "id",
                "name",
                [
                    "attribute" => "type_id",
                    'contentOptions' => ['width' => '10%'],
                    'filter' => Html::activeDropDownList($searchModel, 'type_id', $typeArr, ['class' => 'form-control']),
                    'value' => function ($model) use($typeArr) {
                        return ArrayHelper::getValue($typeArr, $model->type_id);
                    },
                ],
                [
                    'attribute' => 'ctime',
                    'contentOptions' => ['width' => '10%'],
                    'filter'    => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'ctime',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'autoclose'=> true,
                            'format' => 'yyyy-M-dd'
                        ],
                    ]),
                    'value' => function ($model) {
                        return $model->ctime;
                    },
                ],
                [
                    'attribute' => 'utime',
                    'contentOptions' => ['width' => '10%'],
                    'filter'    => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'utime',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'autoclose'=> true,
                            'format' => 'yyyy-M-dd'
                        ],
                    ]),
                    'value' => function ($model) {
                        return $model->utime;
                    },
                ],
                "button" => [
                    'header' => '操作',
                    'contentOptions' => ['style' => 'white-space: normal;', 'width' => '12%'],
                    'class' => 'yii\grid\ActionColumn',
                    'template' => "{edit} {delete}",
                    'buttons' => [
                        'edit' => function ($url, $model) {
                            return Html::a( 
                                "修改",
                                "config-update?id=".$model->id,
                                [
                                    'data-pjax' => '0',
                                    'class'     => 'label label-primary handle',
                                ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return Html::a( 
                                "删除",
                                "config-delete",
                                [
                                    'data-pjax' => '0',
                                    'name'      => "delete_one",
                                    'model_id'  => $model->id,
                                    'class'     => 'label label-primary handle',
                                ]
                            );
                        }
                    ]
                ],
            ];

        ?>
        <div class="grid-view" id="w1">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i></h3>',
                ],
                'toolbar' => [
                    //$fullExportMenu,
                    Html::a('新建技能',['config-create?'.$_SERVER['QUERY_STRING']],['data-pjax'      => 0, 'class' => 'btn btn-success',]),
                ],
                'options' => ['class' => 'grid-view','style'=>'overflow:auto', 'id' => 'grid'],
                'columns' => $gridColumns
                ])
            ?>
        </div>
    </div>
</div>
