<?php
use yii\helpers\Html;
use app\models\Process;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '计划管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxscheduler.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/scheduler/index.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_minical.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxcommon.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/locale_cn_scheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="col-md-2 panel panel-default" style="height:1024px;" >
    </div>
    <div id="plan_scheduler" class="dhx_cal_container col-md-10 panel panel-default" style=' height:1024px; '>
        <div class="dhx_cal_navline">
            <div class="dhx_cal_prev_button">&nbsp;</div>
            <div class="dhx_cal_next_button">&nbsp;</div>
            <div class="dhx_cal_today_button"></div>
            <div class="dhx_cal_date"></div>
            <div class="dhx_minical_icon" id="plan_dhx_minical_icon" onclick="show_minical()">&nbsp;</div>
            <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
        </div>
        <div class="dhx_cal_header"></div>
        <div class="dhx_cal_data"></div>       
    </div>
</div>

<script type="text/javascript">
    // 时间表 
    var plan_scheduler   = init_plan_scheduler();

    function init_plan_scheduler()
    {
        scheduler.config.first_hour = 3;
        scheduler.config.time_step = 15;
        scheduler.config.details_on_create = true;
        scheduler.config.xml_date="%Y-%m-%d %H:%i:%s";
        scheduler.init('plan_scheduler', new Date(),"week");
        scheduler.load("/frontend/plan-api/data","json");

        var dp = new dataProcessor("/frontend/plan-api/");
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

        function show_minical(){
        if (scheduler.isCalendarVisible())
            scheduler.destroyCalendar();
        else
            scheduler.renderCalendar({
                position:"plan_dhx_minical_icon",
                date:scheduler._date,
                navigation:true,
                handler:function(date,calendar){
                    scheduler.setCurrentView(date);
                    scheduler.destroyCalendar()
                }
            });
        }
        return scheduler
    }
</script>
