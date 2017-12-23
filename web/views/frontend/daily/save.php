<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
AppAsset::register($this);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxscheduler.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/scheduler/index.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxgrid_dhx_terrace.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxcommon.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/locale_cn_scheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>

<div class="col-md-4 col-md-offset-1">
    <?php
    $this->params['breadcrumbs'][] = ['label' => '日常作息', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
    if ($model->isNewRecord) {
        $href = "create";
        $this->title = '创建作息表';
    } else {
        $href = "update?id=".$id;
        $this->params['breadcrumbs'][] = ['label' => $model->name];
        $this->title = '更新作息表';
    }

    $form = ActiveForm::begin([
        'id' => 'form',
        'options' => ['class' => 'daily'],
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
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end() ?>
</div>
<div class="col-md-4 col-md-offset-1">
    <div id="daily_scheduler" class="dhx_cal_container panel panel-default" style=' height:1024px; '>
        <div class="dhx_cal_navline">
            <div class="dhx_cal_date"></div>
        </div>
        <div class="dhx_cal_header"></div>
        <div class="dhx_cal_data"></div>       
    </div>
</div>

<script type="text/javascript">
    // 时间表 
    var daily_id = "<?php echo $id;?>";
    var daily_scheduler   = init_daily_scheduler(daily_id);

    function init_daily_scheduler(daily_id)
    {
        scheduler.config.first_hour = 3;
        scheduler.config.time_step = 15;
        scheduler.config.details_on_create = true;
        scheduler.config.xml_date="%Y-%m-%d %H:%i:%s";
        scheduler.init('daily_scheduler', new Date(), "day");
        console.log(daily_id);
        scheduler.load("/frontend/daily-scheduler-api/data?daily_id=" + daily_id, "json");

        var dp = new dataProcessor("/frontend/daily-scheduler-api/");
        dp.init(scheduler);
        dp.setTransactionMode("REST");

        scheduler.attachEvent("onBeforeLightbox", function (id){
            var event = scheduler.getEvent(id);
            event.text = "";
            return true;
        });

        scheduler.attachEvent("onLightbox", function (id){    
            scheduler.save_lightbox();
        });
        return scheduler
    }
</script>
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
                        window.location.href = "/frontend/daily/index";
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
