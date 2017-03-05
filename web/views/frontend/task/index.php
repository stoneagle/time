<?php
use yii\helpers\Html;
use app\models\CountRecord;
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
              <button id="clock_pause" type="button" class="btn btn-primary btn-sm">暂停</button>
              <button id="clock_cancel" type="button" class="btn btn-danger btn-sm">撤除</button>
            </p>
        </div>
    </div>  
    <div id="gridbox" class="col-md-8" style="height:1000px;"></div>  
</div>

<script>
    // 计时器
    var clock;
    var count_add_href    = "/frontend/count-record/add";
    var count_update_href = "/frontend/count-record/update";

    clock = $('#clockbox').FlipClock({
        clockFace: 'HourlyCounter',
        autoStart: false
    });

    $("#clock_pause").on('click', function (e) {
        var time = clock.getTime().time;
        if (time != 0) {
            var text = $("#clock_pause").html();
            if (text == "暂停") {
                post_data = {
                    "init_time" : time,
                    "status" : <?php echo CountRecord::STATUS_PAUSE;?>,
                };
                directPost(count_update_href, post_data, true, true);
                clock.stop();
                $("#clock_pause").html("继续");
            } else {
                post_data = {
                    "init_time" : time,
                    "status" : <?php echo CountRecord::STATUS_EXEC;?>,
                };
                directPost(count_update_href, post_data, true, true);
                clock.start();
                $("#clock_pause").html("暂停");
            }
        }
        return true;
    });

    $("#clock_finish").on('click', function (e) {
        var time = clock.getTime().time;
        if (time != 0) {
            swal({
                title: "是否完成该行动",           // 弹出框的title
                text: "将会记录该次行动内容",             // 弹出框里面的提示文本
                type: "warning",               // 弹出框类型
                showCancelButton: true,        // 是否显示取消按钮
                confirmButtonColor: "#DD6B55", // 确定按钮颜色
                cancelButtonText: "取消",        // 取消按钮文本
                confirmButtonText: "是的，确定！",   // 确定按钮上面的文档
                closeOnConfirm: true,
                showLoaderOnConfirm: true,
            }, function () {
                post_data = {
                    "init_time" : time,
                    "status" : <?php echo CountRecord::STATUS_FINISH;?>,
                };
                directPost(count_update_href, post_data, true, true);
                clock.reset();
            });
        }
        return true;
    });

    $("#clock_cancel").on('click', function (e) {
        var time = clock.getTime().time;
        if (time != 0) {
            swal({
                title: "是否取消该行动",           // 弹出框的title
                text: "将不会记录该次行动内容",             // 弹出框里面的提示文本
                type: "warning",               // 弹出框类型
                showCancelButton: true,        // 是否显示取消按钮
                confirmButtonColor: "#DD6B55", // 确定按钮颜色
                cancelButtonText: "取消",        // 取消按钮文本
                confirmButtonText: "是的，确定！",   // 确定按钮上面的文档
                closeOnConfirm: true,
                showLoaderOnConfirm: true,
            }, function () {
                post_data = {
                    "init_time" : time,
                    "status" : <?php echo CountRecord::STATUS_CANCEL;?>,
                };
                directPost(count_update_href, post_data, true, true);
                clock.reset();
            });
        }
        return true;
    });

    $(window).unload(function(){
        var time = clock.getTime().time;
        if (time != 0) {
            post_data = {
                "init_time" : time,
                "status" : <?php echo CountRecord::STATUS_PAUSE;?>,
            };
            $.ajax({
                url: count_update_href,
                data: post_data,
                dataType: 'text',
                type: 'POST',
                async: false,
                success: function(result) {
                    var data = eval('(' + result + ')');  
                    if (data.error != 0) {
                        swal("操作失败!", data.message, "error");
                    }
                },
                error: function(data) {
                    swal("操作失败!", data.message, "error");
                }
            });
        }
    });

    // 左下方list

    // 右侧grid
    grid = new dhtmlXGridObject('gridbox');

    grid.setImagePath("/css/lib/imgs/dhxgrid_terrace/");                 
    grid.setHeader("领域,项目名称,任务名称,重要性,时间颗粒,已行动次数,完成情况,<div style='width:100%; text-align:left;'><button id = 'grid_task_add' class='btn btn-success btn-xs' >新建</button></div>");

    grid.attachEvent("onRightClick", function(id,ind,obj){
    });

    grid.attachEvent("onRowDblClicked", function(id,ind,obj){
        cell = grid.cellById(id, 2);
        var task_name = cell.cell.innerHTML;

        var count_flag = clock.getTime().time;
        if (count_flag == 0) {
            swal({
                title: "是否执行["+ task_name +"]的行动",           // 弹出框的title
                text: "启动后将会开始计时",             // 弹出框里面的提示文本
                type: "warning",               // 弹出框类型
                showCancelButton: true,        // 是否显示取消按钮
                confirmButtonColor: "#DD6B55", // 确定按钮颜色
                cancelButtonText: "取消",        // 取消按钮文本
                confirmButtonText: "是的，确定！",   // 确定按钮上面的文档
                closeOnConfirm: true,
                showLoaderOnConfirm: true,
            }, function () {
                clock.start();
                post_data = {
                    "task_id" : id,
                };
                directPost(count_add_href, post_data, true, true);
                grid.setRowColor(id,"red");
            });
        } else {
            swal("有行动正在执行!", "请完成或撤除该行动，再开启新的行动", "error");
        }
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

    // 如果加载后，存在执行中或暂停中的行动，提示后重新执行
    var left_action = <?php echo $action_left;?>;
    if (left_action) {
        var action_info = <?php echo $action_info;?>;
        swal({
            title: "遗留行动["+ action_info.text +"]，是否继续",           // 弹出框的title
            text: "取消的话，该行动会撤除，将会丢失已消耗的时间",             // 弹出框里面的提示文本
            type: "warning",               // 弹出框类型
            showCancelButton: true,        // 是否显示取消按钮
            confirmButtonColor: "#DD6B55", // 确定按钮颜色
            cancelButtonText: "取消",        // 取消按钮文本
            confirmButtonText: "是的，确定！",   // 确定按钮上面的文档
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
        }, function (flag) {
            if (flag) {
                post_data = {
                    "init_time" : action_info.init_time,
                    "status" : <?php echo CountRecord::STATUS_EXEC;?>,
                };
                directPost(count_update_href, post_data, true, true);
                grid.setRowColor(action_info.task_id,"red");
                // init_time需要动态获取，要不然会因为数据库更新延迟，无法同步
                clock.setTime(parseInt(action_info.init_time));
                clock.start();
            } else {
                if (!check_flag) {
                    post_data = {
                        "init_time" : action_info.init_time,
                        "status" : <?php echo CountRecord::STATUS_CANCEL;?>,
                    };
                    directPost(count_update_href, post_data, true, true);
                }
            }
        });
    }
</script>
