<?php
use yii\helpers\Html;
use app\models\Process;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '资产管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlx.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxlist_dhx_terrace.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/select2.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/gridstack.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxlist.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/select2.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/echarts.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/lodash.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/jquery-ui.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/gridstack.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/gridstack.jQueryUI.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>
<style type="text/css">
    #grid1 {
        background: lightgoldenrodyellow;
    }

    #grid2 {
        background: lightcyan;
    }

    .grid-stack-item-content {
        color: #2c3e50;
        text-align: center;
        background-color: #18bc9c;
    }

    #grid2 .grid-stack-item-content {
        background-color: #9caabc;
    }

    .grid-stack-item-removing {
        opacity: 0.5;
    }

    .trash {
        height: 150px;
        margin-bottom: 20px;
        background: rgba(255, 0, 0, 0.1) center center url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjY0cHgiIGhlaWdodD0iNjRweCIgdmlld0JveD0iMCAwIDQzOC41MjkgNDM4LjUyOSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDM4LjUyOSA0MzguNTI5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQxNy42ODksNzUuNjU0Yy0xLjcxMS0xLjcwOS0zLjkwMS0yLjU2OC02LjU2My0yLjU2OGgtODguMjI0TDMwMi45MTcsMjUuNDFjLTIuODU0LTcuMDQ0LTcuOTk0LTEzLjA0LTE1LjQxMy0xNy45ODkgICAgQzI4MC4wNzgsMi40NzMsMjcyLjU1NiwwLDI2NC45NDUsMGgtOTEuMzYzYy03LjYxMSwwLTE1LjEzMSwyLjQ3My0yMi41NTQsNy40MjFjLTcuNDI0LDQuOTQ5LTEyLjU2MywxMC45NDQtMTUuNDE5LDE3Ljk4OSAgICBsLTE5Ljk4NSw0Ny42NzZoLTg4LjIyYy0yLjY2NywwLTQuODUzLDAuODU5LTYuNTY3LDIuNTY4Yy0xLjcwOSwxLjcxMy0yLjU2OCwzLjkwMy0yLjU2OCw2LjU2N3YxOC4yNzQgICAgYzAsMi42NjQsMC44NTUsNC44NTQsMi41NjgsNi41NjRjMS43MTQsMS43MTIsMy45MDQsMi41NjgsNi41NjcsMi41NjhoMjcuNDA2djI3MS44YzAsMTUuODAzLDQuNDczLDI5LjI2NiwxMy40MTgsNDAuMzk4ICAgIGM4Ljk0NywxMS4xMzksMTkuNzAxLDE2LjcwMywzMi4yNjQsMTYuNzAzaDIzNy41NDJjMTIuNTY2LDAsMjMuMzE5LTUuNzU2LDMyLjI2NS0xNy4yNjhjOC45NDUtMTEuNTIsMTMuNDE1LTI1LjE3NCwxMy40MTUtNDAuOTcxICAgIFYxMDkuNjI3aDI3LjQxMWMyLjY2MiwwLDQuODUzLTAuODU2LDYuNTYzLTIuNTY4YzEuNzA4LTEuNzA5LDIuNTctMy45LDIuNTctNi41NjRWODIuMjIxICAgIEM0MjAuMjYsNzkuNTU3LDQxOS4zOTcsNzcuMzY3LDQxNy42ODksNzUuNjU0eiBNMTY5LjMwMSwzOS42NzhjMS4zMzEtMS43MTIsMi45NS0yLjc2Miw0Ljg1My0zLjE0aDkwLjUwNCAgICBjMS45MDMsMC4zODEsMy41MjUsMS40Myw0Ljg1NCwzLjE0bDEzLjcwOSwzMy40MDRIMTU1LjMxMUwxNjkuMzAxLDM5LjY3OHogTTM0Ny4xNzMsMzgwLjI5MWMwLDQuMTg2LTAuNjY0LDguMDQyLTEuOTk5LDExLjU2MSAgICBjLTEuMzM0LDMuNTE4LTIuNzE3LDYuMDg4LTQuMTQxLDcuNzA2Yy0xLjQzMSwxLjYyMi0yLjQyMywyLjQyNy0yLjk5OCwyLjQyN0gxMDAuNDkzYy0wLjU3MSwwLTEuNTY1LTAuODA1LTIuOTk2LTIuNDI3ICAgIGMtMS40MjktMS42MTgtMi44MS00LjE4OC00LjE0My03LjcwNmMtMS4zMzEtMy41MTktMS45OTctNy4zNzktMS45OTctMTEuNTYxVjEwOS42MjdoMjU1LjgxNVYzODAuMjkxeiIgZmlsbD0iI2ZmOWNhZSIvPgoJCTxwYXRoIGQ9Ik0xMzcuMDQsMzQ3LjE3MmgxOC4yNzFjMi42NjcsMCw0Ljg1OC0wLjg1NSw2LjU2Ny0yLjU2N2MxLjcwOS0xLjcxOCwyLjU2OC0zLjkwMSwyLjU2OC02LjU3VjE3My41ODEgICAgYzAtMi42NjMtMC44NTktNC44NTMtMi41NjgtNi41NjdjLTEuNzE0LTEuNzA5LTMuODk5LTIuNTY1LTYuNTY3LTIuNTY1SDEzNy4wNGMtMi42NjcsMC00Ljg1NCwwLjg1NS02LjU2NywyLjU2NSAgICBjLTEuNzExLDEuNzE0LTIuNTY4LDMuOTA0LTIuNTY4LDYuNTY3djE2NC40NTRjMCwyLjY2OSwwLjg1NCw0Ljg1MywyLjU2OCw2LjU3QzEzMi4xODYsMzQ2LjMxNiwxMzQuMzczLDM0Ny4xNzIsMTM3LjA0LDM0Ny4xNzJ6IiBmaWxsPSIjZmY5Y2FlIi8+CgkJPHBhdGggZD0iTTIxMC4xMjksMzQ3LjE3MmgxOC4yNzFjMi42NjYsMCw0Ljg1Ni0wLjg1NSw2LjU2NC0yLjU2N2MxLjcxOC0xLjcxOCwyLjU2OS0zLjkwMSwyLjU2OS02LjU3VjE3My41ODEgICAgYzAtMi42NjMtMC44NTItNC44NTMtMi41NjktNi41NjdjLTEuNzA4LTEuNzA5LTMuODk4LTIuNTY1LTYuNTY0LTIuNTY1aC0xOC4yNzFjLTIuNjY0LDAtNC44NTQsMC44NTUtNi41NjcsMi41NjUgICAgYy0xLjcxNCwxLjcxNC0yLjU2OCwzLjkwNC0yLjU2OCw2LjU2N3YxNjQuNDU0YzAsMi42NjksMC44NTQsNC44NTMsMi41NjgsNi41N0MyMDUuMjc0LDM0Ni4zMTYsMjA3LjQ2NSwzNDcuMTcyLDIxMC4xMjksMzQ3LjE3MnogICAgIiBmaWxsPSIjZmY5Y2FlIi8+CgkJPHBhdGggZD0iTTI4My4yMiwzNDcuMTcyaDE4LjI2OGMyLjY2OSwwLDQuODU5LTAuODU1LDYuNTctMi41NjdjMS43MTEtMS43MTgsMi41NjItMy45MDEsMi41NjItNi41N1YxNzMuNTgxICAgIGMwLTIuNjYzLTAuODUyLTQuODUzLTIuNTYyLTYuNTY3Yy0xLjcxMS0xLjcwOS0zLjkwMS0yLjU2NS02LjU3LTIuNTY1SDI4My4yMmMtMi42NywwLTQuODUzLDAuODU1LTYuNTcxLDIuNTY1ICAgIGMtMS43MTEsMS43MTQtMi41NjYsMy45MDQtMi41NjYsNi41Njd2MTY0LjQ1NGMwLDIuNjY5LDAuODU1LDQuODUzLDIuNTY2LDYuNTdDMjc4LjM2NywzNDYuMzE2LDI4MC41NSwzNDcuMTcyLDI4My4yMiwzNDcuMTcyeiIgZmlsbD0iI2ZmOWNhZSIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=) no-repeat;
    }

    .sidebar {
        background: rgba(0, 255, 0, 0.1);
        height: 150px;
        padding: 25px 0;
        text-align: center;
    }

    .sidebar .grid-stack-item {
        width: 200px;
        height: 100px;
        border: 2px dashed green;
        text-align: center;
        line-height: 100px;
        z-index: 10;
        background: rgba(0, 255, 0, 0.1);
        cursor: default;
        display: inline-block;
    }

    .sidebar .grid-stack-item .grid-stack-item-content {
        background: none;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="gridbox_task col-md-4 panel panel-default" style="height:1024px;" >
        <div id="time_cookie" style="width:auto;height:400px;"></div>
        <div id="time_sum" style="width:auto;height:200px;"></div>
        <div id="assets_project_list" style="height:400px"></div>  
    </div>
    <div id="assets_dashboard" class="dhx_cal_container col-md-8 panel panel-default" style=' height:1024px; '>
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <div class="grid-stack-item" id="grid_add_area" ><div class="grid-stack-item-content">Drag me</div></div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="trash">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="grid-stack grid-stack-6" id="grid1">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // 资产块图
    var options = {
        width: 6,
        float: false,
        removable: '.trash',
        removeTimeout: 100,
        acceptWidgets: '.grid-stack-item'
    };

    $('#grid1').on('added', function(event, items) {
        // 设置class的颜色
        // 设置items的信息
        for (var i = 0; i < items.length; i++) {
            console.log(items[i]);
        }
    });
    $('#grid1').on('change', function(event, items) {
        //根据items的数目，判断新增与删除
        /* console.log(event); */
        /* console.log(items); */
        /* console.log('item change'); */
    });
    /* $('#grid1').on('dragstop', function(event, items) { */
    /*     console.log('item drag stop'); */
    /* }); */
    /* $('#grid1').on('resizestart', function(event, items) { */
    /*     console.log('item resizestart'); */
    /* }); */
    /* $('#grid1').on('resizestop', function(event, items) { */
    /*     console.log('item resizestop'); */
    /* }); */

    $('#grid1').gridstack(options);

    var items = [
        {id:"x", x: 0, y: 0, width: 2, height: 2},
        {id:"y", x: 3, y: 1, width: 1, height: 2},
    ];

    $('.grid-stack').each(function () {
        var grid = $(this).data('gridstack');
        _.each(items, function (node) {
            grid.addWidget($('<div><div class="grid-stack-item-content" /><div/>'),
                node.x, node.y, node.width, node.height, true, 1, 5, 1, 5, node.id);
        }, this);
    });

    $('.sidebar .grid-stack-item').draggable({
        revert: 'invalid',
        handle: '.grid-stack-item-content',
        scroll: false,
        appendTo: 'body'
    });

    // 时间饼图
    /* var chart = echarts.init(document.getElementById('time_cookie')); */
    /* chart.title = '时间规划表'; */
    /* chart.setOption({ */
    /*     tooltip: { */
    /*         trigger: 'item', */
    /*         formatter: "{a} <br/>{b}: {c} ({d}%)" */
    /*     }, */
    /*     legend: { */
    /*         orient: 'vertical', */
    /*         x: 'left', */
    /*         data:[] */
    /*     }, */
    /*     series: [ */
    /*         { */
    /*             name:'目标任务', */
    /*             type:'pie', */
    /*             selectedMode: 'single', */
    /*             radius: ['30%', '50%'], */

    /*             label: { */
    /*                 normal: { */
    /*                     position: 'inner' */
    /*                 } */
    /*             }, */
    /*             labelLine: { */
    /*                 normal: { */
    /*                     show: false */
    /*                 } */
    /*             }, */
    /*             data:[] */
    /*         }, */
    /*         { */
    /*             name:'预估时间', */
    /*             type:'pie', */
    /*             radius: ['55%', '70%'], */
    /*             data:[] */
    /*         } */
    /*     ] */
    /* }); */

    /* function setChartData(year, week) { */
    /*     $.get('/frontend/plan-api/chart/' + year + '/' + week).done(function (data) { */
    /*         chart.setOption({ */
    /*             legend: { */
    /*                 data: data.field_time_x */
    /*             }, */
    /*             series: [ */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '预估时间', */
    /*                     data: data.week_time */
    /*                 }, */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '目标任务', */
    /*                     data: data.field_time */
    /*                 }, */
    /*             ] */
    /*         }); */
    /*         sum_chart.setOption({ */
    /*             series: [ */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '计划分配', */
    /*                     data: [ data.week_time_sum ], */
    /*                 }, */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '任务预估', */
    /*                     data: [ data.field_time_sum ], */
    /*                 }, */
    /*             ] */
    /*         }); */
    /*     }); */
    /* } */

    /* // 总和柱状图 */
    /* var sum_chart = echarts.init(document.getElementById('time_sum')); */
    /* sum_chart.title = "时间总和"; */
    /* sum_chart.setOption({ */
    /*     title: { */
    /*         text: '时间总和', */
    /*         subtext: '' */
    /*     }, */
    /*     tooltip: { */
    /*         trigger: 'axis', */
    /*         axisPointer: { */
    /*             type: 'shadow' */
    /*         } */
    /*     }, */
    /*     legend: { */
    /*         data: ['计划分配', '任务预估'] */
    /*     }, */
    /*     grid: { */
    /*         left: '3%', */
    /*         right: '4%', */
    /*         bottom: '3%', */
    /*         containLabel: true */
    /*     }, */
    /*     xAxis: { */
    /*         type: 'value', */
    /*         boundaryGap: [0, 0.01] */
    /*     }, */
    /*     yAxis: { */
    /*         type: 'category', */
    /*         data: ['时间'] */
    /*     }, */
    /*     series: [ */
    /*         { */
    /*             name: '计划分配', */
    /*             type: 'bar', */
    /*             data: [] */
    /*         }, */
    /*         { */
    /*             name: '任务预估', */
    /*             type: 'bar', */
    /*             data: [] */
    /*         } */
    /*     ] */
    /* }); */

    // 左下方执行与完成的list列表
    /* execList = new dhtmlXList({ */
    /*     container:"exec_list", */
    /*     type:{ */
    /*         template:"所属项目:#task_name#<br/>行动名称:(#text#)<br/>计划时间:#plan_time#", */
    /*         height:75, */
    /*     }, */
    /*     drag:true */
    /* }); */
    /* endList = new dhtmlXList({ */
    /*     container:"end_list", */
    /*     type:{ */
    /*         template:"所属项目:#task_name#<br/>行动名称:(#text#)<br/>计划时间:#plan_time#", */
    /*     } */
    /* }); */
    /* var exec_load_path = "/frontend/action-api/list/"; */
    /* execList.load(exec_load_path, "json"); */
</script>
