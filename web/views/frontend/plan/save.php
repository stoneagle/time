<?php
use yii\helpers\Html;
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
if ($model->isNewRecord) {
    $href = "create";
    $this->title = '创建计划';
} else {
    $href = "update?id=".$id;
    $this->params['breadcrumbs'][] = ['label' => $model->from_date ."-". $model->to_date];
    $this->title = '更新计划';
}
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
        $form = ActiveForm::begin([
            'id' => 'form',
            'options' => ['class' => 'plan'],
            'enableAjaxValidation' => true,
            'validationUrl' => 'valid',
        ])
        ?>
        <?php if(!$model->isNewRecord) { 
            echo $form->field($model, 'id')->textInput()->hiddenInput()->label(false);
        } 
        ?>
        <?php 
          echo $form->field($model, 'daily_id')->widget(
          Select2::className(), 
            [
              'data' => $dailyArr,
              'pluginOptions' => [
                  'allowClear' => true,
              ],
          ]); 
        ?>
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
        <div id="time_sum" style="width:auto;height:200px;"></div>
        <div id="gridbox_task"></div>  
        <div class="form-group">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

<script type="text/javascript">
    function setChartPlanData() {
        var daily_id = $('#plan-daily_id').val()
        var from_date = $('#plan-from_date').val()
        var to_date = $('#plan-to_date').val()
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
                sum_hours = day_hour * diff_date_days;
                sum_chart.setOption({
                    series: [
                        {
                            // 根据名字对应到相应的系列
                            name: '计划分配',
                            data: [ sum_hours ],
                        },
                        /* { */
                        /*     // 根据名字对应到相应的系列 */
                        /*     name: '任务预估', */
                        /*     data: [ 0 ], */
                        /* }, */
                    ]
                });
            });
        }
    }

    function setChartSumData(plan_id) {
        $.get('/frontend/plan/project-list?plan_id=' + plan_id).done(function (data) {
            result = data.rows;
            hours_sum = 0;
            for (x in result) {
                hours = result[x]["data"][3];
                if (result[x]["data"][0]) {
                    hours_sum += parseInt(hours);
                }
            }
            sum_chart.setOption({
                series: [
                    {
                        // 根据名字对应到相应的系列
                        name: '任务预估',
                        data: [ hours_sum ],
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

    // TODO, 更新模式下提交的优化，避免修改列表后未提交的删除或更新

    var plan_id = "<?php echo $id;?>";
    setChartPlanData();
    setChartSumData(plan_id);

    // 时间表 
    var default_daily_id = "<?php echo $defaultDailyKey;?>";
    var plan_scheduler = init_plan_scheduler(default_daily_id);

    // 更新图表计划bar
    $('#plan-daily_id').on('select2:select', function (e) {
        var data = e.params.data;
        plan_scheduler = init_plan_scheduler(data.id);
        setChartPlanData();
    });
    $('#plan-from_date').on('changeDate', function (e) {
        setChartPlanData();
    });
    $('#plan-to_date').on('changeDate', function (e) {
        setChartPlanData();
    });

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
    grid.setHeader("复选框,所属计划,任务名称,预估时间");
    grid.setInitWidthsP("12,12,58,30");
    grid.setColAlign("left, left, left, left"); 
    grid.setColTypes("ch,ro,ro,ed"); 
    grid.enableAutoHeight(true);
    grid.setColumnIds("select, plan_id, text, hours");
    grid.setColumnHidden(1,true);  

    grid.init();       
    grid.load("/frontend/plan/project-list?plan_id=" + plan_id, "json"); 

    grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
        // 需要注意触发时机
        if ((stage == 2) || (stage == 1)) {
            ids = this.getAllRowIds();
            ids_arr = ids.split(",");
            sum_hours = 0; 
            for (x in ids_arr) {
                select = this.cellById(ids_arr[x], 0).getValue();
                if (select == 1) {
                    hours = this.cellById(ids_arr[x], 3).getValue();
                    sum_hours += parseInt(hours);
                }
            }
            sum_chart.setOption({
                series: [
                    {
                        // 根据名字对应到相应的系列
                        name: '任务预估',
                        data: [ sum_hours ],
                    },
                ]
            });
        }
        return true;
    });

    gridDataProcessor = new dataProcessor("/frontend/plan-project/");
    gridDataProcessor.init(grid); 
    gridDataProcessor.setTransactionMode("REST");

    gridDataProcessor.attachEvent("onAfterUpdate", function(id, action, tid, response){
        if (action == "error") {
            swal("操作失败!", response.msg, "error");
        }
    })
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
                        window.location.href = "/frontend/plan/index";
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
