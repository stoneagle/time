<?php
use yii\helpers\Html;
use app\models\Process;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '任务管理';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlx.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxgrid_dhx_terrace.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxgrid.js',['depends'=>['app\assets\AppAsset']]);

// 计时器
$this->registerCssFile('@web/css/lib/flipclock.css',['depends'=>['app\assets\AppAsset']]);
$this->registerJsFile('@web/js/lib/flipclock.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

/* $this->registerCssFile('@web/css/time/xxx.css',['depends'=>['app\assets\AppAsset']]); */
//$this->registerJsFile('@web/js/frontend/time/tab_carousel.js',['depends'=>['app\assets\AppAsset']]);

AppAsset::register($this);
?>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div id="actionbox" class="col-md-4 panel">
        <div id="clockbox"></div>
        <div class="col-md-4 col-md-offset-3"  >
            <p>
              <button id="clock_finish" type="button" class="btn btn-success btn-sm">完成</button>
              <button id="clock_stop" type="button" class="btn btn-primary btn-sm">中止</button>
              <button id="clock_finish" type="button" class="btn btn-danger btn-sm">撤除</button>
            </p>
        </div>
    </div>  
    <div id="gridbox" class="col-md-8" style="height:1000px;"></div>  
</div>

<script>
    // 计时器
    var clock;
    clock = $('#clockbox').FlipClock({
        clockFace: 'HourlyCounter',
        autoStart: false
    });

    $("#clock_stop").on('click', function (e) {
        clock.stop(function() {
            var text = $("#clock_stop").html();
            if (text == "中止") {
                $("#clock_stop").html("继续");
            } else {
                $("#clock_stop").html("中止");
            }
            console.log("stop");
            return false;
        });
    });

    $("#clock_finish").on('click', function (e) {
        clock.reset(function() {
            console.log("stop");
            return false;
        });
    });

    // 左下方list

    // 右侧grid
    grid = new dhtmlXGridObject('gridbox');

    grid.setImagePath("/css/lib/imgs/dhxgrid_terrace/");                 
    grid.setHeader("领域,项目名称,任务名称,重要性,时间颗粒,已行动次数,完成情况,<div style='width:100%; text-align:left;'><button id = 'grid_task_add' class='btn btn-success btn-xs' >新建</button></div>");

    grid.attachEvent("onRightClick", function(id,ind,obj){
    });

    grid.attachEvent("onRowDblClicked", function(id,ind,obj){
        swal("行动开始!", "", "info");
        clock.start();
    });

    grid.setInitWidths("120,200,200,120,120,120,120");            
    grid.setColAlign("left, left, left, left, left, left, left"); 
    grid.setColTypes("ro,ro,ed,ed,ed,ed,ch");                  
    grid.setColSorting("str,str,str,str,str,int,str,button");             
    grid.init();       
    grid.load("/frontend/task-api/data","json"); 

    gridDataProcessor = new dataProcessor("/frontend/task-api/"); 
    gridDataProcessor.init(grid); 
    gridDataProcessor.setTransactionMode("REST");
</script>
