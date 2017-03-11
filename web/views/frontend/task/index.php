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
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxgrid_drag.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
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
    .collapse.in {
          height: auto !important;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div id="actionbox" class="col-md-4 panel panel-default">
        <div id="clockbox">
        </div>
        <div class="col-md-6 col-md-offset-4"  >
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
    <div class="col-md-8 panel panel-default" style="height:1000px;padding-right:0;padding-left:0">
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
    // 通用变量
    var clock_exec_aid = 0;
    var clock_exec_tid = 0;

    var action_add_href    = "/frontend/action-api/add";
    var action_update_href = "/frontend/action-api/update";
    var action_del_href    = "/frontend/action-api/del";

    var field_dict      = <?php echo $field_arr;?>;
    var type_dict       = <?php echo $type_arr;?>;
    var type_raw        = <?php echo $type_raw;?>;
    var task_id_arr     = <?php echo $task_id_arr;?>;
    var status_arr      = <?php echo $status_arr;?>;

    var grid_prefix = "gridbox_";
    var task_id = 0;
    var grid_hash = {};

    var text_index      = 1;
    var plan_time_index = 2;
    var type_index      = 3;
    var status_index    = 4;
    var check_index     = 6;

    // 左下方执行与完成的list列表
    execList = new dhtmlXList({
        container:"exec_list",
        type:{
            template:"所属项目:#task_name#<br/>行动名称:(#text#)<br/>计划时间:#plan_time#",
            height:75,
        },
        drag:true
    });
    endList = new dhtmlXList({
        container:"end_list",
        type:{
            template:"所属项目:#task_name#<br/>行动名称:(#text#)<br/>计划时间:#plan_time#",
        }
    });
    var exec_load_path = "/frontend/action-api/list/" + <?php echo Action::LIST_EXEC;?>;
    var end_load_path = "/frontend/action-api/list/" + <?php echo Action::LIST_END;?>;
    execList.load(exec_load_path, "json");
    endList.load(end_load_path, "json");
    
    /* var dp = new dataProcessor("/frontend/action-api/"); */ 
    /* dp.init(execList); */
    /* dp.init(endList); */
    /* dp.attachEvent("onAfterUpdate", function(id, action, tid, response){ */
    /*     if (action == "error") { */
    /*         swal("操作失败!", response.msg, "error"); */
    /*     } */
    /* }) */

    // execlist双击启动行动
    execList.attachEvent("onItemDblClick", function (id, ev, html){
        var obj = this.get(id);
        var grid_obj = grid_hash[obj.task_id];
        var check_status = grid_obj.cellById(obj.id,status_index).getValue();
        if (check_status != <?php echo Action::STATUS_WAIT;?>) {
            return false;
        } else {
            swal({
                title: "是否启动行动[" + obj.text + "]",           // 弹出框的title
                text: "该行动将会开始计时",             // 弹出框里面的提示文本
                type: "warning",               // 弹出框类型
                showCancelButton: true,        // 是否显示取消按钮
                confirmButtonColor: "#DD6B55", // 确定按钮颜色
                cancelButtonText: "取消",        // 取消按钮文本
                confirmButtonText: "是的，确定！",   // 确定按钮上面的文档
                closeOnConfirm: true,
                showLoaderOnConfirm: true,
            }, function () {
                // todo 一致性问题的解决
                post_data = {
                    "action_id" : obj.id,
                };
                directPost(count_add_href, post_data, true, true);
                post_data = {
                    "status" : <?php echo Action::STATUS_EXEC;?>,
                };
                $.ajax({
                    url: action_update_href + "/" + obj.id, 
                    data: post_data,
                    dataType: 'text',
                    type: 'POST',
                    success: function(result) {
                        var data = eval('(' + result + ')');  
                        if (data.action == "error") {
                            swal("操作失败!", data.message, "error");
                        } else {
                            swal("操作成功!", data.message, "success");
                            var status_exec = <?php echo Action::STATUS_EXEC;?>;
                            grid_obj.cellById(obj.id,status_index).setValue(status_exec);
                            grid_obj.cellById(obj.id,status_index).cell.innerHTML = status_arr[status_exec];
                            // todo, reset后开始clock时，会提示时间已归0，不允许再次start
                            clock.start();
                            clock_exec_aid = obj.id; 
                            clock_exec_tid = obj.task_id; 
                        }
                    },
                    error: function(data) {
                        swal("操作失败!", data.message, "error");
                    }
                })
                return true;
            });
        }
    });

    execList.attachEvent("onBeforeDrag", function (context, ev){
        var obj = this.get(context.start);
        if (obj.status != <?php echo Action::STATUS_WAIT;?>) {
            return false;
        } else {
            context.html = "<div style='background-color:white; padding:10px'>"+ obj.task_name +"</div>";
            return true;
        }
    });

    // execlist拖动删除行动
    execList.attachEvent("onDragOut", function (context, ev){
        var obj = this.get(context.start);
        swal({
            title: "是否取消行动[" + obj.text + "]",           // 弹出框的title
            text: "该行动将会从列表中去除",             // 弹出框里面的提示文本
            type: "warning",               // 弹出框类型
            showCancelButton: true,        // 是否显示取消按钮
            confirmButtonColor: "#DD6B55", // 确定按钮颜色
            cancelButtonText: "取消",        // 取消按钮文本
            confirmButtonText: "是的，确定！",   // 确定按钮上面的文档
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
        }, function () {
            post_data = {
                "status" : <?php echo Action::STATUS_INIT;?>,
            };
            $.ajax({
                url: action_update_href + "/" + obj.id, 
                data: post_data,
                dataType: 'text',
                type: 'POST',
                success: function(result) {
                    var data = eval('(' + result + ')');  
                    if (data.action == "error") {
                        swal("操作失败!", data.message, "error");
                    } else {
                        swal("操作成功!", data.message, "success");
                        execList.remove(obj.id);
                        // 修改grid选框状态
                        var grid_obj = grid_hash[obj.task_id];
                        grid_obj.cellById(obj.id, check_index).setValue(0);
                        var status_init = <?php echo Action::STATUS_INIT;?>;
                        grid_obj.cellById(obj.id,status_index).setValue(status_init);
                        grid_obj.cellById(obj.id,status_index).cell.innerHTML = status_arr[status_init];
                    }
                },
                error: function(data) {
                    swal("操作失败!", data.message, "error");
                }
            })
        });
        return true;
    });

    // 计时器
    var clock;
    var count_add_href    = "/frontend/count-record/add";
    var count_update_href = "/frontend/count-record/update";
    var count_one_href    = "/frontend/count-record/one";

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
                var status_end = <?php echo Action::STATUS_END;?>;
                var grid_obj = grid_hash[clock_exec_tid];
                grid_obj.cellById(clock_exec_aid,status_index).setValue(status_end);
                grid_obj.cellById(clock_exec_aid,status_index).cell.innerHTML = status_arr[status_end];
                var exec_obj = execList.get(clock_exec_aid);
                execList.remove(clock_exec_aid);
                endList.add(
                    {
                        id:clock_exec_aid,
                        text:exec_obj['text'],
                        task_name:exec_obj['task_name'],
                        type_id:exec_obj['type_id'],
                        task_id:exec_obj['task_id'],
                        plan_time:exec_obj[ 'plan_time' ],
                    }
                );
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
                var status_wait = <?php echo Action::STATUS_WAIT;?>;
                var grid_obj = grid_hash[clock_exec_tid];
                grid_obj.cellById(clock_exec_aid,status_index).setValue(status_wait);
                grid_obj.cellById(clock_exec_aid,status_index).cell.innerHTML = status_arr[status_wait];
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

    // 选择框
    // collapse与grid的初始化
    for (var i = 0 ;i < task_id_arr.length; i++) {
        task_id = task_id_arr[i];
        // 右侧grid
        grid = new dhtmlXGridObject(grid_prefix + task_id);
        grid.setImagePath("/css/lib/imgs/dhxgrid_terrace/");                 
        grid.setHeader("ID,行动名称,计划时间,类别,状态,描述,<div style='width:100%; text-align:left;'><button id = 'grid" + task_id + "_add' class='btn btn-success btn-xs' >新建</button></div>");
        // grid.enableAutoWidth(true,600,100);
        grid.setInitWidthsP("10,25,10,10,10,25,9");            
        grid.setColAlign("left, left, left, left, left, left"); 
        grid.setColTypes("ro,ed,ed,co,coro,ed,ch");                  
        grid.setColSorting("str,str,int,str,str,txt");             
        grid.enableAutoHeight(true);
        /* grid.enableDragAndDrop(true); */
        /* grid.rowToDragElement=function(id){ */
        /*     var text = this.cellById(this._dragged[i].idd,1).getValue(); */
        /*     return text; */
        /* } */

        // 双击修改，其中field需要针对性的修改combo
        var collapse_obj = $("#collapse_" + task_id);
        var field_id   = collapse_obj.attr("field_id");
        var type_combo = grid.getCombo(type_index);
        for (key in type_raw[field_id]) {
            type_combo.put(key, type_raw[field_id][key]);
        }
        var status_combo = grid.getCombo(status_index);
        for (key in status_arr) {
            status_combo.put(key, status_arr[key]);
        }

        grid.setColumnIds("id,text,plan_time,type_id,status,desc,check");
        grid.init();       
        grid.load("/frontend/action-api/data/" + task_id,"json"); 

        gridDataProcessor = new dataProcessor("/frontend/action-api/");
        gridDataProcessor.init(grid); 
        gridDataProcessor.setTransactionMode("REST");
        gridDataProcessor.attachEvent("onAfterUpdate", function(id, action, tid, response){
            if (action == "error") {
                swal("操作失败!", response.msg, "error");
            }
        })


        // 禁止grid右键弹出情况
        $('body').on('contextmenu', grid_prefix + task_id ,function(){
            return false;
        });
        // 右键删除行动
        grid.attachEvent("onRightClick", function(id,ind,obj){
            var grid_obj = this;
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
                grid_obj.deleteRow(id);
            });
        });

        $("#action_type").select2({
            placeholder: '请选择类别'
        })

        // checkbox的勾选，对已初始化的任务，进行移入移出堆栈操作
        // onCheck时机在更新后出发，checkbox状态会修改
        // 修改成onEditCell
        grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            if ((stage == 0) && (cInd == check_index)) {
                var status_id = this.cellById(rId,status_index).getValue();
                var state = this.cellById(rId,cInd).getValue();
                
                if (state == 0) {
                    // 全部移入等待队列
                    if (status_id != <?php echo Action::STATUS_INIT;?>) {
                        swal("修改失败!", "状态异常", "error");
                        return false;
                    } else {
                        var status_wait = <?php echo Action::STATUS_WAIT;?>;
                        this.cellById(rId,status_index).setValue(status_wait);
                        this.cellById(rId,status_index).cell.innerHTML = status_arr[status_wait];
                    }
                } else {
                    if (status_id == <?php echo Action::STATUS_INIT;?>) {
                        swal("修改失败!", "状态异常", "error");
                        return false;
                    } else if (status_id == <?php echo Action::STATUS_WAIT;?>){
                        // todo 需要校验下当前行动是否存在已执行过的时间
                        var status_init = "<?php echo Action::STATUS_INIT;?>";
                        this.cellById(rId,status_index).setValue(status_init);
                        this.cellById(rId,status_index).cell.innerHTML = status_arr[status_init];
                    } else {
                        return false;
                    }
                }
            }
            return true;
            //this.setRowColor(rId,"red");
        });

        grid.attachEvent("onCheck", function(rId,cInd,state){
            var collapse_obj = $("#collapse_" + task_id);
            var task_name  = collapse_obj.attr("task_name");
            if (state) {
                execList.add(
                    {
                        id:rId,
                        text:this.cellById(rId,text_index).getValue(),
                        task_name:task_name,
                        type_id:this.cellById(rId,type_index).getValue(),
                        task_id:task_id,
                        plan_time:this.cellById(rId,plan_time_index).getValue(),
                    }
                );
            } else {
                execList.remove(rId.toString());
            }
        });


        // 新建按钮操作
        var add_button_name = "#grid" + task_id + "_add";
        $(add_button_name).on('click', '', {tid:task_id}, function(e){
            var task_id = e.data.tid;
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
        grid_hash[task_id] = grid;
    }

    /* $('body').on('contextmenu','#action_save',function(){ */
    /*     return false; */
    /* }); */
    /* $('body').on('contextmenu','.modal-backdrop',function(){ */
    /*     return false; */
    /* }); */
    $('body').on('contextmenu','.sweet-alert',function(){
        return false;
    });
    $('body').on('contextmenu','.sweet-overlay',function(){
        return false;
    });
    
    // 表单提交操作
    $('#action_form').on('submit', function(e){
        e.preventDefault();
        var post_data = $(this).serializeArray();
        var href = action_add_href;
        var task_id = $("#action_task_id").val();
        var grid_obj = grid_hash[task_id];

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
                grid_obj.clearAndLoad("/frontend/action-api/data/" + task_id,"json");
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    });

    // 如果加载后，存在执行中或暂停中的行动，提示后重新执行
    var left_action = <?php echo $action_left;?>;
    if (left_action == 1) {
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
                clock_exec_aid = action_info.action_id; 
                clock_exec_tid = action_info.task_id; 
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
                post_data = {
                    "init_time" : action_info.init_time,
                    "status" : <?php echo CountRecord::STATUS_CANCEL;?>,
                };
                directPost(count_update_href, post_data, true, true);
            }
        });
    }
</script>
