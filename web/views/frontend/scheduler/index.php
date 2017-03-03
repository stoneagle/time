<?php
use yii\helpers\Html;
use app\models\Process;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '时间管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$process_dict_index = ArrayHelper::index(json_decode($processDict, true), 'key');
$process_dict_index = json_encode($process_dict_index);

$this->registerCssFile('@web/css/lib/dhtmlxscheduler.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlxtree_dhx_skyblue.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler_outerdrag.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler_quick_info.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler_minical.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler_key_nav.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler_recurring.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler_limit.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxscheduler_editors.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

$this->registerJsFile('@web/js/lib/dhtmlxtree.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxtree_json.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxcommon.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/locale_cn_scheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>
<style >
    .highlighted_timespan {
        transition: background-color 400ms;
        -moz-transition: background-color 400ms;
        -webkit-transition: background-color 400ms;
        -o-transition: background-color 400ms;
        opacity:0.4;
        filter:alpha(opacity=40);
    }
    .highlighted_timespan:hover {
        background-color: #90ee90;
    }
    .container-fluid #scheduler_here {
        border: 1px solid #cecece;
    }
    .container-fluid #treebox {
        /* height: 700px; */
        /* width: 100%; */
        border: 1px solid #cecece;
    }
    #scheduler_here {
        border-radius: 4px;
    }
    #treebox {
        border-radius: 4px;
    }

    /* background color for whole container and it's border*/
    .my_event {
        background-color: #add8e6;
        border: 1px solid #778899;
        overflow: hidden;
    }
    /* disabling default color for select menu */
    .dhx_cal_select_menu.my_event div {
        border: 0;
        background-color: transparent;
        color: black;
    }
    /* styles for event content */
    .dhx_cal_event.my_event .my_event_body {

        padding-top: 3px;
        padding-left: 5px;
    }
    /* event's date information */
    .my_event .event_date {
        font-weight: bold;
        padding-right: 5px;
    }
    /* event's resizing section */
    .my_event_resize {
        height: 3px;
        position: absolute;
        bottom: -1px;
    }
    /* event's move section */
    .my_event_move {
        position: absolute;
        top: 0;
        height: 10px;
        cursor: pointer;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="col-md-2 panel" id="treebox" style="height:1000px;" ></div>
    <div class="modal fade" id="tree_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="tree_form" method="post" action="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="tree_save_title"></h4>
                    </div>
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="text" class="control-label">行为名称:</label>
                                <input type="text" class="form-control" id="tree_text" name="text" >
                            </div>
                            <div class="form-group">
                                <label for="plan_num" class="control-label">预计时间颗粒(30min/颗):</label>
                                <input type="number" class="form-control" name="plan_num" min=0 value=0 >
                            </div>
                            <div class="form-group">
                                <label for="action_id" class="control-label">行动类别:</label>
                                <?php
                                echo Select2::widget([
                                    'id'             => 'tree_action_id',
                                    'name'           => 'action_id',
                                    'data'           => $actionDict,
                                    'size'           => Select2::SMALL,
                                    'options'        => ['placeholder' => '请选择类别'],
                                    'pluginOptions'  => [
                                        'allowClear' => true
                                    ],
                                ]);
                                ?>
                            </div>
                            <input type="hidden" class="form-control" id="tree_task_id" name="task_id">
                            <input type="hidden" class="form-control" id="tree_id" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="submit" class="btn btn-primary" id="tree_save_submit" >提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="scheduler_here" class="dhx_cal_container col-md-10 panel " style=' height:1000px; '>
        <div class="dhx_cal_navline">
            <div class="dhx_cal_prev_button">&nbsp;</div>
            <div class="dhx_cal_next_button">&nbsp;</div>
            <div class="dhx_cal_today_button"></div>
            <div class="dhx_cal_date"></div>
            <div class="dhx_minical_icon" id="dhx_minical_icon" onclick="show_minical()">&nbsp;</div>
            <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
            <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
            <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
        </div>
        <div class="dhx_cal_header"></div>
        <div class="dhx_cal_data"></div>       
    </div>
</div>

<script type="text/javascript">
    // 树状图
    var finish_color       = 'red';
    var task_node_level    = 2;
    var process_node_level = 3;
    var process_name       = "行动";

    var process_opt = <?php echo $processDict; ?>;
    var process_map = <?php echo $process_dict_index; ?>;

    var init_sections = [  
        {name:"description", height:200, map_to:"text", type:"textarea" , focus:true},
        {name:"process_id", height:21, map_to:"process_id", type:"select", options:process_opt},
        {name:"time", height:72, type:"calendar_time", map_to:"auto"},
        {name:"finish", map_to:"finish", type:"checkbox", checked_value: "<?php echo Process::FINISH_TRUE; ?>", unchecked_value: "no", height:40}
    ];

    var tree = new dhtmlXTreeObject("treebox", "100%", "100%", 0);
    tree.setImagesPath("/css/lib/dhxtree_skyblue/");
    tree.enableDragAndDrop(true);

    // 设置双击父节点添加，双击子节点修改
    tree.attachEvent("onDblClick", function(id){
        var level = tree.getLevel(id);
        switch (level) {
            case task_node_level : 
                $('#tree_save_title').html(process_name + "新增");
                $('#tree_task_id').val(id);
                $('#tree_save').modal('show')
                break;
            case process_node_level : 
                var color = tree.getItemColor(id).acolor;
                if (color != finish_color) {
                    $('#tree_save_title').html(process_name + "修改");
                    $('#tree_id').val(id);
                    $('#tree_save').modal('show')
                }
                break;
            default :
                break;
        }
        return false;
    });

    // 浮层表单提交
    $('#tree_form').on('submit', function(e){
        e.preventDefault();
        var post_data = $(this).serializeArray();
        var href = "";
        var new_flag = null;
        var tree_id = $('#tree_id').val();
        if (tree_id) {
            new_flag = false;
            href = "/frontend/process/update";
        } else {
            // tree的id后面加了时间戳
            // todo,用其它字段记录新建的数值，要不然版本更新后，会出现错误
            // var raw_id = tree_id.split("_")[0];
            new_flag = true;
            href = "/frontend/process/add";
        }
        $.ajax({
            url: href,
            data: post_data,
            dataType: 'text',
            type: 'POST',
            success: function(result) {
                var data = eval('(' + result + ')');  
                if (data.error != 0) {
                    swal("操作失败!", data.message, "error");
                } else {
                    $('#tree_save').modal('hide')
                    var obj_id          = data.data['id'];
                    var obj_text        = $('#tree_text').val();
                    var obj_action_type = $('#tree_action_id').find("option:selected").text();
                    obj_text = "[" + obj_action_type + "]" + obj_text;
                    if (new_flag) {
                        var fid          = $('#tree_task_id').val();
                        tree.insertNewItem(fid, obj_id, obj_text, 0, 0, 0, 0, 'SELECT');
                        tree.refreshItem(fid);
                        // 无法更新scheduler选项
                        var new_process_opt = data.data['process_dict'];
                        process_map = data.data['process_map'];
                        scheduler.resetLightbox();
                        scheduler.config.lightbox.sections = [  
                            {name:"description", height:200, map_to:"text", type:"textarea" , focus:true},
                            {name:"process_id", height:21, map_to:"process_id", type:"select", options:new_process_opt},
                            {name:"time", height:72, type:"calendar_time", map_to:"auto"},
                            {name:"finish", map_to:"finish", type:"checkbox", checked_value: "<?php echo Process::FINISH_TRUE; ?>", unchecked_value: "no", height:40}
                        ];
                    } else {
                        tree.setItemText(tree_id, obj_text);
                    }
                    resetForm("tree_form");
                }
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    });

    // 禁止树状图下，右键弹出情况
    $('body').on('contextmenu','#treebox',function(){
        return false;
    });
    $('body').on('contextmenu','.sweet-alert',function(){
        return false;
    });
    $('body').on('contextmenu','.sweet-overlay',function(){
        return false;
    });

    // 设置右键单击子节点删除(包括鼠标右键和弹出框与层)
    tree.attachEvent("onRightClick", function(id, ev){
        var level = tree.getLevel(id);
        switch (level) {
            case task_node_level :
                var text_info = "是否完成该任务";
                var hint = "已完成的任务将不再出现";
                var href = "/frontend/gantt/finish?id=" + id;
                var post_data = {};
                checkPost(text_info, hint, href, post_data);
                break;
            case process_node_level : 
                var color = tree.getItemColor(id).acolor;
                if (color != finish_color) {
                    var text_info = "是否真的要删除该" + process_name;
                    var hint = "已经开始执行的不允许删除";
                    var href = "/frontend/process/del?id=" + id;
                    var post_data = {};
                    checkPost(text_info, hint, href, post_data);
                }
                break;
            default :
                break;
        }
        return false;
    });

    tree.attachEvent("onBeforeDrag", function(id){
        var level = tree.getLevel(id);
        if (level != process_node_level) {
            return false;
        }
        var color = tree.getItemColor(id).acolor;
        if (color === finish_color) {
            swal("操作失败!", "该" + process_name + "已结束", "error");
            return false;
        }
        return true;
    });

    tree.setXMLAutoLoading("/frontend/process/data");
    tree.setDataMode("json");
    tree.load("/frontend/gantt-api/task-tree", 'json');

    // 实现已完成的行为标记横线
    tree.attachEvent("onOpenEnd", function(id, state){
        var level = tree.getLevel(id);
        if (level == task_node_level) {
            var sub_list = tree.getAllSubItems(id);
            var sub_arr = sub_list.split(',');
            var ids_obj = new Object();
            var ids_list = new Array();
            for (var i = 0 ;i < sub_arr.length; i++)
            {
                var raw_id = sub_arr[i].split('_');
                ids_obj[raw_id[0]] = sub_arr[i];
                ids_list.push(raw_id[0]);
            }
            if (ids_list.length > 0) {
                var post_data = {
                    'ids' : ids_list
                };
                var href = "/frontend/process/finish-check";
                $.post(
                    href, 
                    post_data,
                    function(data){
                        if (data.error === 0) {
                            var finish_list = data.data['list'];
                            for (var i = 0 ; i < finish_list.length; i++) {
                                var origin_id = ids_obj[finish_list[i]];
                                tree.setItemStyle(origin_id, "text-decoration:line-through;");
                                tree.setItemColor(origin_id, finish_color);
                            }
                        } else {
                            swal("操作失败!", data.message, "error");
                        }
                    }, 
                "json");
            }
        }
        return true;
    });

    // 时间表
    scheduler.config.first_hour = 3;
    scheduler.config.time_step = 15;
    scheduler.config.touch = "force";
    scheduler.config.multi_day = true;
    scheduler.config.xml_date="%Y-%m-%d %H:%i:%s";

    // 额外选项
    scheduler.locale.labels.section_process_id = "所属" + process_name;
    scheduler.locale.labels.section_finish     = process_name + "是否完成";
    scheduler.config.lightbox.sections         = init_sections;

    // 保存前校验
    scheduler.attachEvent("onEventSave", function(id, ev, is_new){
        if (!ev.process_id) {
            swal("操作失败!", "请选择所属" + process_name, "error");
            return false;
        }
        ev.process_name = process_map[ev.process_id]['label'];
        return true;
    });

    // 保存后树状结构修改
    scheduler.attachEvent("onEventAdded", function(id,ev){
        if (ev.finish == "<?php echo Process::FINISH_TRUE;?>") {
            tree.setItemColor(ev.process_id, finish_color);
            tree.setItemStyle(ev.process_id, "text-decoration:line-through;");
            swal("提示!", ev.process_name + "已完成", "success");
        } else {
            tree.setItemStyle(ev.process_id, "text-decoration:none;color:rgb(0,0,0);");
            //swal("提示!", ev.process_name + "未完成", "error");
        }
    });
    scheduler.attachEvent("onEventChanged", function(id,ev){
        if (ev.finish == "<?php echo Process::FINISH_TRUE;?>") {
            tree.setItemColor(ev.process_id, finish_color);
            tree.setItemStyle(ev.process_id, "text-decoration:line-through;");
            swal("提示!", ev.process_name + "已完成", "success");
        } else {
            tree.setItemStyle(ev.process_id, "text-decoration:none;color:rgb(0,0,0);");
            //swal("提示!", ev.process_name + "未完成", "error");
        }
    });

    // 悬浮高亮
    /* scheduler.attachEvent("onTemplatesReady", function() { */
    /*     var highlight_step = 60; // we are going to highlight 30 minutes timespan */

    /*     var highlight_html = ""; */
    /*     var hours = scheduler.config.last_hour - scheduler.config.first_hour; // e.g. 24-8=16 */
    /*     var times = hours*60/highlight_step; // number of highlighted section we should add */
    /*     var height = scheduler.config.hour_size_px*(highlight_step/60); */
    /*     for (var i=0; i<times; i++) { */
    /*         highlight_html += "<div class='highlighted_timespan' style='height: "+height+"px;'></div>" */
    /*     } */
    /*     scheduler.addMarkedTimespan({ */
    /*         days: "fullweek", */
    /*         zones: "fullday", */
    /*         html: highlight_html */
    /*     }); */
    /* }); */

    // 自定义event内容
    scheduler.attachEvent("onTemplatesReady", function(){
        scheduler.templates.event_text=function(start,end,event){
            return "<b>" + event.info+ "</b>";
        }
    }); 

    // 拖拽
    scheduler.attachEvent("onExternalDragIn", function(id, source, event){
        var tree_id = tree._dragged[0].id;
        var tree_arr = tree_id.split('_');
        scheduler.getEvent(id).process_id = tree_arr[0];
        return true;
    });

    // 复制，剪切，粘贴
    var modified_event_id = null;
    scheduler.templates.event_class = function(start, end, event) {
        // my_event是自定义event的class
        if (event.id == modified_event_id)
            return "copied_event my_event";
        return "my_event"; 
    };

    scheduler.attachEvent("onEventCopied", function(ev) {
        dhtmlx.message("You've copied event: <br/><b>"+ev.text+"</b>");
        modified_event_id = ev.id;
        scheduler.updateEvent(ev.id);
    });
    scheduler.attachEvent("onEventCut", function(ev) {
        dhtmlx.message("You've cut event: <br/><b>"+ev.text+"</b>");
        modified_event_id = ev.id;
        scheduler.updateEvent(ev.id);
    });

    scheduler.attachEvent("onEventPasted", function(isCopy, modified_ev, original_ev) {
        modified_event_id = null;
        scheduler.updateEvent(modified_ev.id);

        var evs = scheduler.getEvents(modified_ev.start_date, modified_ev.end_date);
        if (evs.length > 1) {
            dhtmlx.modalbox({
                text: "There is another event at this time! What do you want to do?",
                width: "500px",
                position: "middle",
                buttons:["Revert changes", "Edit event", "Save changes"],
                callback: function(index) {
                    switch(+index) {
                        case 0:
                            if (isCopy) {
                                // copy operation, need to delete new event
                                scheduler.deleteEvent(modified_ev.id);
                            } else {
                                // cut operation, need to restore dates
                                modified_ev.start_date = original_ev.start_date;
                                modified_ev.end_date = original_ev.end_date;
                                scheduler.setCurrentView();
                            }
                            break;
                        case 1:
                            scheduler.showLightbox(modified_ev.id);
                            break;
                        case 2:
                            return;
                    }
                }
            });
        }
    });

    // 快捷信息标题显示
    scheduler.templates.quick_info_title = function(start, end, ev){ 
        return ev.process_name;
    };

    // 初始化
    scheduler.init('scheduler_here', new Date(),"week");
    scheduler.load("/frontend/scheduler-api/data");

    var dp = new dataProcessor("/frontend/scheduler-api/");
    dp.init(scheduler);
    dp.setTransactionMode("REST");

    // 左上角日历
    function show_minical(){
    if (scheduler.isCalendarVisible())
        scheduler.destroyCalendar();
    else
        scheduler.renderCalendar({
            position:"dhx_minical_icon",
            date:scheduler._date,
            navigation:true,
            handler:function(date,calendar){
                scheduler.setCurrentView(date);
                scheduler.destroyCalendar()
            }
        });
    }

    // 自定义event盒子
    scheduler.renderEvent = function(container, ev, width, height, header_content, body_content) {
        var container_width = container.style.width; // e.g. "105px"

        // move section
        var html = "<div class='dhx_event_move my_event_move' style='width: " + container_width + "'></div>";

        // container for event contents
        html+= "<div class='my_event_body'>";
            html += "<span class='event_date'>";
            // two options here: show only start date for short events or start+end for long
            if ((ev.end_date - ev.start_date) / 60000 > 40) { // if event is longer than 40 minutes
                html += scheduler.templates.event_header(ev.start_date, ev.end_date, ev);
                html += "</span><br/>";
            } else {
                html += scheduler.templates.event_date(ev.start_date) + "</span>";
            }
            // displaying event text
            html += "<span>" + scheduler.templates.event_text(ev.start_date, ev.end_date, ev) + "</span>";
        html += "</div>";

        // resize section
        html += "<div class='dhx_event_resize my_event_resize' style='width: " + container_width + "'></div>";

        container.innerHTML = html;
        return true; // required, true - we've created custom form; false - display default one instead
    };
</script>
