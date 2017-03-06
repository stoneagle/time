<?php
use yii\helpers\Html;
use app\models\CountRecord;
use app\models\Action;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '任务管理';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlx.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxgrid_dhx_terrace.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxlist_dhx_terrace.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/select2.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxgrid.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxlist.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/select2.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

// 计时器
$this->registerCssFile('@web/css/lib/flipclock.css',['depends'=>['app\assets\AppAsset']]);
$this->registerJsFile('@web/js/lib/flipclock.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

/* $this->registerCssFile('@web/css/time/xxx.css',['depends'=>['app\assets\AppAsset']]); */
//$this->registerJsFile('@web/js/frontend/time/tab_carousel.js',['depends'=>['app\assets\AppAsset']]);

?>
<style >
    .dhx_list {
        padding-left: 0px;
        padding-right: 0px;
    }
    .gridbox_dhx_terrace {
        padding-left: 0px;
        padding-right: 0px;
    }
</style>
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
        <div style="height:600px">
            <div id="exec_list" class="col-md-6"></div>
            <div id="end_list" class="col-md-6"></div>
        </div>
    </div>  
    <div class="col-md-8" style="height:1000px;">

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <?php 
        $i = 0;
        foreach ($task_list as $id => $one) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="collapse_head_<?php echo $id;?>"
        >
            <h4 class="panel-title">
                <a 
                    <?php if ($i == 0) { ?>
                    role="button" 
                    data-toggle="collapse" 
                    data-parent="#accordion" 
                    href="#collapse_<?php echo $id;?>" 
                    aria-expanded="true" 
                    aria-controls="collapse_<?php echo $id;?>"
                    <?php } else { ?> 
                    class="collapsed" 
                    role="button" 
                    data-toggle="collapse" 
                    data-parent="#accordion" 
                    href="#collapse_<?php echo $id;?>" 
                    aria-expanded="false" 
                    aria-controls="collapse_<?php echo $id;?>"
                    <?php } ?>
                >
                    <?php echo $one['text'];?> 
                </a>
            </h4>
        </div>
        <div 
            id="collapse_<?php echo $id;?>" 
            class="panel-collapse collapse <?php if ($i == 0){?> in <?php }?>" 
            role="tabpanel" 
            aria-labelledby="headingOne"
            field_id = <?php echo $one['field_id'];?>
            task_name = "<?php echo $one['task_name'];?>"
        >
            <div id="gridbox_<?php echo $id; ?>"></div>  
        </div>
    </div>
    <?php
            $i++;
        }
    ?>
</div>

    </div>
</div>

<div class="modal fade" id="action_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="action_form" method="post" action="" class="form-horizontal" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="action_save_title"></h4>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label for="text" class="col-sm-3 control-label">所属任务:</label>
                            <div class="col-sm-9">
                                <input type="text" readonly class="form-control" id="action_task_name"></input>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="text" class="col-sm-3 control-label">行为名称:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="action_text" name="text" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="plan_time" class="col-sm-3 control-label">预计时间颗粒(30min/颗):</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="plan_time" min=0 value=0 >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="action_type" class="col-sm-3 control-label">行动类别:</label>
                            <div class="col-sm-9">
                                <select id="action_type" name="type_id" class="form-control" >
                                </select>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="action_id" name="id">
                        <input type="hidden" class="form-control" id="action_task_id" name="task_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary" id="tree_save_submit" >提交</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 左下方执行与完成的list列表
    execList = new dhtmlXList({
        container:"exec_list",
        type:{
            template:"#text# : #plan_time#<br/>#task_id#",
        }
    });
    endList = new dhtmlXList({
        container:"end_list",
        type:{
            template:"#text# : #plan_time#<br/>#task_id#",
        }
    });
    execList.load("/frontend/action-api/list/" + <?php echo Action::LIST_EXEC;?>, "json");
    endList.load("/frontend/action-api/list/" + <?php echo Action::LIST_END;?>, "json");
    var dp = new dataProcessor("/frontend/action-api/"); 
    dp.init(execList);
    dp.init(endList);

    // 计时器
    var clock;
    var count_add_href    = "/frontend/count-record/add";
    var count_update_href = "/frontend/count-record/update";
    var count_one_href    = "/frontend/count-record/one";

    var action_add_href    = "/frontend/action-api/add"
    var action_update_href = "/frontend/action-api/update"
    var action_del_href    = "/frontend/action-api/del"

    var field_dict = <?php echo $field_arr;?>;
    var type_dict = <?php echo $type_arr;?>;
    var task_id_arr = <?php echo $task_id_arr;?>;

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
        // 未来可以增加一种情况，标记unload是刷新还是退出页面
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

    // collapse与grid的初始化
    for (var i = 0 ;i < task_id_arr.length; i++) {
        initGrid(task_id_arr[i]);
    }

    // grid初始化
    function initGrid(task_id)
    {
        var grid_prefix = "gridbox_";
        // 右侧grid
        grid = new dhtmlXGridObject(grid_prefix + task_id);

        grid.setImagePath("/css/lib/imgs/dhxgrid_terrace/");                 
        grid.setHeader("行动名称,计划时间,类别,状态,<div style='width:100%; text-align:left;'><button id = 'grid" + task_id + "_add' class='btn btn-success btn-xs' >新建</button></div>");
        grid.setInitWidths("300,150,150,150,150");            
        grid.setColAlign("left, left, left, left, left"); 
        grid.setColTypes("ro,ro,ed,ed,ed");                  
        grid.setColSorting("str,str,str,str,button");             
        grid.enableAutoHeight(true, 700);
        grid.init();       
        grid.load("/frontend/action-api/data/"+task_id,"json"); 

        gridDataProcessor = new dataProcessor("/frontend/action-api/"); 
        gridDataProcessor.init(grid); 
        gridDataProcessor.setTransactionMode("REST");


        // 右键删除行动
        // 禁止grid右键弹出情况
        $('body').on('contextmenu',grid_prefix + task_id,function(){
            return false;
        });
        $('body').on('contextmenu','#action_save',function(){
            return false;
        });
        $('body').on('contextmenu','.modal-backdrop',function(){
            return false;
        });
        grid.attachEvent("onRightClick", function(id,ind,obj){
            swal({
                title: "是否删除该行动",           // 弹出框的title
                text: "该行动的记录将会消除",             // 弹出框里面的提示文本
                type: "warning",               // 弹出框类型
                showCancelButton: true,        // 是否显示取消按钮
                confirmButtonColor: "#DD6B55", // 确定按钮颜色
                cancelButtonText: "取消",        // 取消按钮文本
                confirmButtonText: "是的，确定！",   // 确定按钮上面的文档
                closeOnConfirm: true,
                showLoaderOnConfirm: true,
            }, function () {
                /* myGrid.deleteRow("row1") */
                /* post_data = { */
                /* }; */
                /* directPost(action_del_href + id, post_data, true, true); */
                /* clock.reset(); */
            });
        });

        // 双击将已初始化的任务，移入执行堆栈
        $("#action_type").select2({
            placeholder: '请选择类别'
        })

        grid.attachEvent("onRowDblClicked", function(id,ind,obj){
            console.log(this.cellById(id, 3));
            // 如果CountRecord没有正在执行的任务，执行该任务

            /* clock.start(); */
            /* post_data = { */
            /*     "task_id" : id, */
            /* }; */
            /* directPost(count_add_href, post_data, true, true); */
            /* grid.setRowColor(id,"red"); */

            // 如果有则移入等待队列
        });

        // 新建按钮操作
        $("#grid"+task_id+"_add").on('click', function(e){
            var collapse_obj = $("#collapse_" + task_id);
            var task_name  = collapse_obj.attr("task_name");
            var field_id   = collapse_obj.attr("field_id");

            $("#action_type").empty();
            $("#action_type").select2(type_dict[field_id]);
            $('#action_save_title').html("新增行动");
            $('#action_task_id').val(task_id)
            $('#action_task_name').val(task_name)
            $('#action_save').modal('show')
        });
        return grid;
    }
    
    // 表单提交操作
    $('#action_form').on('submit', function(e){
        e.preventDefault();
        var post_data = $(this).serializeArray();
        var href = "";
        var new_flag = null;
        var tree_id = $('#action_id').val();
        if (tree_id) {
            new_flag = false;
            href = action_update_href;
        } else {
            new_flag = true;
            href = action_add_href;
        }
        $.ajax({
            url: href,
            data: post_data,
            dataType: 'text',
            type: 'POST',
            success: function(result) {
                var data = eval('(' + result + ')');  
                if (data.action == "error") {
                    swal("操作失败!", data.msg, "error");
                } else {
                    $('#action_save').modal('hide')
                    resetForm("action_form");
                }
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    });


    // 如果加载后，存在执行中或暂停中的行动，提示后重新执行
    var left_action = <?php echo $action_left;?>;
    if (left_action == 0) {
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
                    "id" : action_info.id,
                }
                var init_time = action_info.init_time;
                $.ajax({
                    url: count_one_href,
                    data: post_data,
                    dataType: 'text',
                    async: false,
                    type: 'POST',
                    success: function(result) {
                        var data = eval('(' + result + ')');  
                        if (data.error != 0) {
                            swal("操作失败!", data.message, "error");
                        } else {
                            init_time = data.data.info.init_time;
                        }
                    },
                    error: function(data) {
                        swal("操作失败!", data.message, "error");
                    }
                })
                post_data = {
                    "init_time" : init_time,
                    "status" : <?php echo CountRecord::STATUS_EXEC;?>,
                };
                directPost(count_update_href, post_data, true, true);
                
                // init_time需要动态获取，要不然会因为数据库更新延迟，无法同步
                clock.setTime(parseInt(init_time));
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