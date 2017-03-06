<div class="col-md-6 col-md-offset-3">
    <?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use kartik\select2\Select2;

    $this->params['breadcrumbs'][] = ['label' => '配置管理', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
    if ($model->isNewRecord) {
        $href = "create";
        $this->title = '创建配置';
    } else {
        $href = "update?id=".$id;
        $this->params['breadcrumbs'][] = ['label' => $model->name];
        $this->title = '更新配置';
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
    <?php 
        if($model->isNewRecord) { 
            echo $form->field($model, 'type')->widget(
                Select2::className(), [
                'data' => $typeArr,
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])->hint('请选择配置类别'); 
        } else {
            echo $form->field($model, 'type')->dropdownList($typeArr, ['disabled' => true]); 
        }
    ?>
    <?php 
        if($model->isNewRecord) { 
            echo $form->field($model, 'parent')->widget(
                Select2::className(), [
                'data' => $parentArr,
                'options' => ['placeholder' => '所属类别默认为空'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])->hint('请选择配置类别'); 
        } else {
            echo $form->field($model, 'parent')->dropdownList($parentArr, ['disabled' => true]); 
        }
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
                        window.location.href = "index?Config[id]=" + id;
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
