<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use app\assets\AppAsset;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use \kartik\date\DatePicker;

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

$this->params['breadcrumbs'][] = ['label' => '计划管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$href = "update?id=".$id;
$this->params['breadcrumbs'][] = ['label' => $model->from_date ."-". $model->to_date];
$this->title = '查看计划';
?>
<style >
    .gridbox_task {
        padding-left: 0px;
        padding-right: 0px;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div id="plan_scheduler" class="dhx_cal_container col-md-3 panel panel-default" style=' height:1024px; '>
        <div class="dhx_cal_navline">
            <div class="dhx_cal_date"></div>
        </div>
        <div class="dhx_cal_header"></div>
        <div class="dhx_cal_data"></div>       
    </div>
    <div class="gridbox_task col-md-9 panel panel-default" style="height:1024px;" >
        <?php 
            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'from_date',
                'attribute2' => 'to_date',
                'options' => ['placeholder' => '开始日期'],
                'options2' => ['placeholder' => '结束日期'],
                'type' => DatePicker::TYPE_RANGE,
                'form' => $form,
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ]);
        ?>
        <div id="time_sum" style="width:auto;height:560px;"></div>
        <div id="gridbox_task"></div>  
    </div>
</div>

<script type="text/javascript">
    var default_daily_id = "<?php echo $defaultDailyKey;?>";
    var plan_id = "<?php echo $id;?>";

    function setChart(plan_id, daily_id) {
        var from_date = $('#plan-from_date').val();
        var to_date = $('#plan-to_date').val();
        if (daily_id && from_date && to_date) {
            $.get('/frontend/daily-scheduler-api/data?daily_id=' + daily_id).done(function (data) {
                result = data.data
                day_hour = 0;
                for (x in result) {
                    time_start = new Date(result[x]["start_date"]);
                    time_end = new Date(result[x]["end_date"]);
                    time_diff_hours = (time_end - time_start) / 1000 / (60 * 60);
                    day_hour += time_diff_hours;
                }
                from_date_obj = new Date(from_date);
                to_date_obj = new Date(to_date);
                diff_date_days = (to_date_obj - from_date_obj) / 1000 / (60 * 60 * 24) + 1
                daily_hours_sum = day_hour * diff_date_days;
                $.get('/frontend/plan/project-check?plan_id=' + plan_id).done(function (data) {
                    result = data.rows;
                    plan_hours_sum = 0;
                    exec_hours_sum = 0;
                    charts_data = new Array();
                    project_map = {};
                    for (x in result) {
                        project_title = result[x]["data"][0];
                        plan_hours = result[x]["data"][1];
                        exec_hours = result[x]["data"][2];
                        project_id = result[x]["data"][4];
                        plan_hours_sum += parseInt(exec_hours);
                        exec_hours_sum += parseInt(exec_hours);
                        charts_data.push([project_title, plan_hours, exec_hours, 0]);
                        project_map[project_title] = project_id;
                    }
                    charts_data.push(["总和", plan_hours_sum, exec_hours_sum, daily_hours_sum]);
                    function sortNumber(a,b){return a[1]-b[1]}
                    charts_data = charts_data.sort(sortNumber)
                    type_list = new Array();
                    plan_data = new Array();
                    exec_data = new Array();
                    daily_data = new Array();
                    for (x in charts_data) {
                        type_list.push(charts_data[x][0]);
                        plan_data.push(charts_data[x][1]);
                        exec_data.push(charts_data[x][2]);
                        daily_data.push(charts_data[x][3]);
                    }
                    sum_chart = echarts.init(document.getElementById('time_sum'));
                    sum_chart.on('click', function (params) {
                        // 控制台打印数据的名称
                        project_id = project_map[params.name]
                        if (project_id) {
                            // TODO 更改成modal展示
                            url = "/frontend/action/index?project_id=" + project_id + "&Action%5Bstart_date%5D=" + from_date + "&Action%5Bend_date%5D=" + to_date;
                            window.open(url, '_blank');
                        }
                    });
                    option = {
                        title: {
                            text: '时间统计',
                            subtext: '作息结果'
                        },
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'shadow'
                            }
                        },
                        legend: {
                            data: ['作息', '预估', '实际']
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
                            data: type_list 
                        },
                        series: [
                            {
                                name: '作息',
                                type: 'bar',
                                data: daily_data 
                            },
                            {
                                name: '预估',
                                type: 'bar',
                                data: plan_data 
                            },
                            {
                                name: '实际',
                                type: 'bar',
                                data: exec_data 
                            }
                        ]
                    };
                    sum_chart.setOption(option);
                });
            });
        }
    }

    // 总和柱状图
    setChart(plan_id, default_daily_id);

    // 时间表 
    var plan_scheduler = init_plan_scheduler(default_daily_id);

    function init_plan_scheduler(daily_id)
    {
        scheduler.config.first_hour = 3;
        scheduler.config.time_step = 15;
        scheduler.config.details_on_create = true;
        scheduler.config.xml_date="%Y-%m-%d %H:%i:%s";
        scheduler.init('plan_scheduler', new Date(),"day");
        scheduler.clearAll();
        scheduler.load("/frontend/daily-scheduler-api/data?daily_id=" + daily_id, "json");
        return scheduler
    }

    // 右侧grid复选框栏
    grid = new dhtmlXGridObject("gridbox_task");
    grid.setImagePath("/css/lib/imgs/dhxgrid_terrace/");
    grid.setHeader("任务名称,预估时间,实际花费,状态");
    grid.setInitWidthsP("50,20,20,9");
    grid.setColAlign("left, left, left, left"); 
    grid.setColTypes("ro,ro,ro,ro"); 
    grid.enableAutoHeight(true, 600);
    grid.setColumnIds("text, hours, exec, status");

    grid.init();       
    grid.load("/frontend/plan/project-check?plan_id=" + plan_id, "json"); 
</script>

<?php 
Modal::begin([
    'id' => 'common-modal',
    'header' => '<h4 class="modal-title"></h4>',
    'footer' =>  '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
Modal::end(); 
?>
