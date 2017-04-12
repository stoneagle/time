<div class="col-md-6 col-md-offset-3">
    <?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use kartik\select2\Select2;

    $this->params['breadcrumbs'][] = ['label' => '技能配置', 'url' => ['config-index']];
    $this->params['breadcrumbs'][] = $this->title;
    if ($model->isNewRecord) {
        $href = "config-create";
        $this->title = '创建技能';
    } else {
        $href = "config-update?id=".$id;
        $this->params['breadcrumbs'][] = ['label' => $model->name];
        $this->title = '更新技能';
    }

    $form = ActiveForm::begin([
        'id' => 'form',
        'options' => ['class' => 'project'],
        'enableAjaxValidation' => true,
        'validationUrl' => 'valid',
    ])
    ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if(!$model->isNewRecord) { 
        echo $form->field($model, 'id')->textInput()->hiddenInput()->label(false);
    } 
    ?>
    <?= $form->field($model, 'name')->textInput(['placeholder' => '不能为空'])?>
    <?= $form->field($model, 'max_points')->textInput()->hint("最大不能超过5")?>
    <?= $form->field($model, 'description')->textarea(['row' => 4])?>
    <?= $form->field($model, 'img_url')->textInput(['placeholder' => '图片地址'])?>
    <?= $form->field($model, 'rank_desc')->textarea(['row' => 4])?>
    <?php 
        echo $form->field($model, 'type_id')->widget(
            Select2::className(), [
            'data' => $typeArr,
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->hint('请选择类别'); 
    ?>
    <?php 
        echo $form->field($model, 'area_ids')->widget(
            Select2::className(), [
            'data' => $areaDict,
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => true
            ],
        ])->hint('请选择所属领域，可多选'); 
    ?>
    <?php 
        echo $form->field($model, 'depend_ids')->widget(
            Select2::className(), [
            'data' => $dependSkills,
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => true
            ],
        ])->hint('请选择前置技能，可多选'); 
    ?>
    <?php 
        echo $form->field($model, 'talent_ids')->widget(
            Select2::className(), [
            'data' => $talents,
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => true
            ],
        ])->hint('请选择天赋，可多选'); 
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
                        window.location.href = "/frontend/knowledge-skill/config-index";
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
