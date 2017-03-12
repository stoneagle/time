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
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxgrid_dhx_terrace.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_minical.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxgrid.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxcommon.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/locale_cn_scheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/echarts.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>
<style >
    .gridbox_task {
        padding-left: 0px;
        padding-right: 0px;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="gridbox_task col-md-4 panel panel-default" style="height:1024px;" >
        <div id="time_cookie" style="width:auto;height:400px;"></div>
        <div id="time_sum" style="width:auto;height:200px;"></div>
        <div id="gridbox_task"></div>  
    </div>
    <div id="plan_scheduler" class="dhx_cal_container col-md-8 panel panel-default" style=' height:1024px; '>
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
    // 时间饼图
    var chart = echarts.init(document.getElementById('time_cookie'));
    chart.title = '时间规划表';
    chart.setOption({
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            x: 'left',
            data:[]
        },
        series: [
            {
                name:'目标任务',
                type:'pie',
                selectedMode: 'single',
                radius: ['30%', '50%'],

                label: {
                    normal: {
                        position: 'inner'
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[]
            },
            {
                name:'预估时间',
                type:'pie',
                radius: ['55%', '70%'],
                data:[]
            }
        ]
    });

    function setChartData(year, week) {
        $.get('/frontend/plan-api/chart/' + year + '/' + week).done(function (data) {
            chart.setOption({
                legend: {
                    data: data.field_time_x
                },
                series: [
                    {
                        // 根据名字对应到相应的系列
                        name: '预估时间',
                        data: data.week_time
                    },
                    {
                        // 根据名字对应到相应的系列
                        name: '目标任务',
                        data: data.field_time
                    },
                ]
            });
            sum_chart.setOption({
                series: [
                    {
                        // 根据名字对应到相应的系列
                        name: '计划分配',
                        data: [ data.week_time_sum ],
                    },
                    {
                        // 根据名字对应到相应的系列
                        name: '任务预估',
                        data: [ data.field_time_sum ],
                    },
                ]
            });
        });
    }

    // 总和柱状图
    var sum_chart = echarts.init(document.getElementById('time_sum'));
    sum_chart.title = "时间总和";
    sum_chart.setOption({
        title: {
            text: '时间总和',
            subtext: ''
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: ['计划分配', '任务预估']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            boundaryGap: [0, 0.01]
        },
        yAxis: {
            type: 'category',
            data: ['时间']
        },
        series: [
            {
                name: '计划分配',
                type: 'bar',
                data: []
            },
            {
                name: '任务预估',
                type: 'bar',
                data: []
            }
        ]
    });

    setChartData(0,0);

    // 时间表 
    var plan_scheduler   = init_plan_scheduler();
    plan_scheduler.attachEvent("onBeforeViewChange", function(old_mode,old_date,mode,date){
        week = scheduler.date.getISOWeek(date);
        var string = JSON.stringify(date); 
        var year_arr = string.replace(/\"/g, "").split('-');
        grid.clearAll();
        grid.load("/frontend/task-api/plan/" + year_arr[0] + "/" + week,"json"); 
        scheduler_date = year_arr[0] + "-" + week;
        setChartData(year_arr[0],week);
        return true;
    });

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

        scheduler.attachEvent("onEventAdded", function(id,ev){
            var scheduler_arr = scheduler_date.split("-");
            setChartData(scheduler_date[0], scheduler_date[1]);
            return true;
        });

        scheduler.attachEvent("onEventChanged", function(id,ev){
            var scheduler_arr = scheduler_date.split("-");
            setChartData(scheduler_date[0], scheduler_date[1]);
            return true;
        });

        scheduler.attachEvent("onEventDeleted", function(id){
            var scheduler_arr = scheduler_date.split("-");
            setChartData(scheduler_date[0], scheduler_date[1]);
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

    // 左侧grid复选框栏
    var init_date = "<?php echo $init_date;?>";
    var scheduler_date = "<?php echo $init_date;?>";

    grid = new dhtmlXGridObject("gridbox_task");
    grid.setImagePath("/css/lib/imgs/dhxgrid_terrace/");
    grid.setHeader("复选框,周,任务名称,预估时间,完成情况");
    grid.setInitWidthsP("12,13,40,15,19");            
    grid.setColAlign("left, left, left, left, left"); 
    grid.setColTypes("ch,ro,ro,ro,ro");                  
    grid.enableAutoHeight(true);
    grid.setColumnIds("select, week, text, plan_time, finish");

    grid.init();       
    grid.load("/frontend/task-api/plan/0/0","json"); 

    var check_index = 0;
    grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
        // 非本周的不允许进行操作，不论是过去还是未来都不行
        if ((stage == 0) && (cInd == check_index)) {
            if (init_date != scheduler_date) {
                return false;
            }
            //var state = this.cellById(rId,cInd).getValue();
        }
        return true;
    });

    grid.attachEvent("onCheck", function(rId,cInd,state){
        // todo 延时问题，图表更新不同步
        var scheduler_arr = scheduler_date.split("-");
        setChartData(scheduler_date[0], scheduler_date[1]);
        return true;
    });

    gridDataProcessor = new dataProcessor("/frontend/plan-api/plan/");
    gridDataProcessor.init(grid); 
    gridDataProcessor.setTransactionMode("REST");

    gridDataProcessor.attachEvent("onAfterUpdate", function(id, action, tid, response){
        if (action == "error") {
            swal("操作失败!", response.msg, "error");
        }
    })
</script>
