<div class="col-md-6 col-md-offset-3">
    <?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use kartik\select2\Select2;
    use kartik\depdrop\DepDrop;
    use app\models\Target;

    $this->params['breadcrumbs'][] = ['label' => '领域项目管理', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
    if ($model->isNewRecord) {
        $href = "create";
        $this->title = '创建领域项目';
    } else {
        $href = "update?id=".$id;
        $this->params['breadcrumbs'][] = ['label' => $model->name];
        $this->title = '更新领域项目';
    }

    $form = ActiveForm::begin([
        'id'                   => 'form',
        'options'              => ['class' => 'target'],
        'enableAjaxValidation' => true,
        'validationUrl'        => 'valid',
    ])
    ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if(!$model->isNewRecord) { 
        echo $form->field($model, 'id')->textInput()->hiddenInput()->label(false);
    } 
    ?>
    <?= $form->field($model, 'name')->textInput(['placeholder' => '不能为空'])?>
    <?= $form->field($model, 'field_id')->dropDownList($fieldArr, 
        [
            'id'     => 'field_id',
            'prompt' => '请选择领域'
        ]
    )?>
    <?php 
        echo $form->field($model, 'entity_ids')->widget(DepDrop::classname(), [
            'type' => DepDrop::TYPE_SELECT2,
            'options' => ['multiple' => true],
            'data' => $initEntityArr,
            'pluginOptions' => [
                'depends' => ['field_id'],
                'placeholder' => '请选择相关实体',
                'url' => 'get-field-entity',
                'multiple' => true, 
            ]
        ]);
    ?>
    <?php 
        echo $form->field($model, 'priority_id')->widget(
            Select2::className(), [
            'data' => $priorityArr,
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->hint('请选择优先级'); 
    ?>
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end() ?>
</div>

<?php $this->beginBlock('js') ?>  
$(function(){
    // 提交按钮
    $('#form').on('beforeSubmit',function(e){
        var post_data = $(this).serializeArray();
        var href = "<?php echo $href;?>";
        $.ajax({
            url: href,
            data: post_data,
            dataType: 'text',
            type: 'POST',
            success: function(result) {
                var data = eval('(' + result + ')');  
                if (data.error === 0) {
                    swal({
                        title: "操作成功!",   
                        text: data.message,  
                        type: "success",    
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定",
                    },function(){
                        id = data.data['id'];
                        window.location.href = "index";
                    });
                } else {
                    swal("操作失败!", data.message, "error");
                }
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    }).on('submit', function(e){
        e.preventDefault();
    });
});
<?php $this->endBlock() ?>  

<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>  
