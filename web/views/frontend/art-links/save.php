<div class="col-md-6 col-md-offset-3">
    <?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use kartik\select2\Select2;

    $this->params['breadcrumbs'][] = ['label' => '艺术管理', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
    if ($model->isNewRecord) {
        $href = "create";
        $this->title = '创建艺术';
    } else {
        $href = "update?id=".$id;
        $this->params['breadcrumbs'][] = ['label' => $model->project_name];
        $this->title = '更新艺术';
    }

    $form = ActiveForm::begin([
        'id' => 'form',
        'options' => ['class' => 'chanllege-entity'],
        'enableAjaxValidation' => true,
        'validationUrl' => 'valid',
    ])
    ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if(!$model->isNewRecord) { 
        echo $form->field($model, 'id')->textInput()->hiddenInput()->label(false);
    } 
    ?>
    <?= $form->field($model, 'project_name')->textInput(['placeholder' => '不能为空'])?>
    <?php 
        echo $form->field($model, 'work_ids')->widget(
            Select2::className(), [
            'data' => $workArr,
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => true,
            ],
        ])->hint('请选择相关作品'); 
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
                        window.location.href = "/frontend/art-links/index";
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
