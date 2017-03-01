<?php
use yii\helpers\Html;
use app\assets\AppAsset;

$this->title                   = '时间管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);

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
                                <label for="recipient-name" class="control-label">过程:</label>
                                <input type="text" class="form-control" id="tree_text" name="text" >
                            </div>
                            <div class="form-group">
                                <label for="message-text" class="control-label">预计时间颗粒(30min/颗):</label>
                                <input type="number" class="form-control" name="plan_num" min=0 value=0 >
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
    var tree = new dhtmlXTreeObject("treebox", "100%", "100%", 0);
    tree.setImagesPath("/css/lib/dhxtree_skyblue/");
    tree.enableDragAndDrop(true);

    // 设置双击父节点添加，双击子节点修改
    tree.attachEvent("onDblClick", function(id){
        var level = tree.getLevel(id);
        switch (level) {
            case 2 : 
                $('#tree_save_title').html("过程新增");
                $('#tree_task_id').val(id);
                $('#tree_save').modal('show')
                break;
            case 3 : 
                $('#tree_save_title').html("过程修改");
                $('#tree_id').val(id);
                $('#tree_save').modal('show')
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
            var raw_id = tree_id.split("_")[0];
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
                    var obj_id       = data.data['id'];
                    var obj_text     = $('#tree_text').val();
                    if (new_flag) {
                        var fid          = $('#tree_task_id').val();
                        tree.insertNewItem(fid, obj_id, obj_text, 0, 0, 0, 0, 'SELECT');
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
            case 2 :
                var text_info = "是否完成该任务";
                var hint = "已完成的任务将不再出现";
                var href = "/frontend/gantt/finish?id=" + id;
                var post_data = {};
                checkPost(text_info, hint, href, post_data);
                break;
            case 3 : 
                var text_info = "是否真的要删除该过程";
                var hint = "已经开始执行的不允许删除";
                var href = "/frontend/process/del?id=" + id;
                var post_data = {};
                checkPost(text_info, hint, href, post_data);
                break;
            default :
                break;
        }
        return false;
    });

    tree.setXMLAutoLoading("/frontend/process/data");
    tree.setDataMode("json");
    tree.load("/frontend/gantt-api/task-tree", 'json');

    // 实现已完成的过程标记横线
    tree.attachEvent("onOpenEnd", function(id, state){
        var level = tree.getLevel(id);
        if (level == 2) {
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
                                tree.setItemStyle(origin_id, "color:red;text-decoration:line-through;");
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
    var task_opt = <?php echo $taskDict; ?>;
    scheduler.locale.labels.section_task = "所属任务";
    scheduler.config.lightbox.sections = [  
        {name:"description", height:200, map_to:"text", type:"textarea" , focus:true},
        {name:"task", height:21, map_to:"task", type:"select", options:task_opt},
        {name:"time", height:72, type:"calendar_time", map_to:"auto"}    
    ];

    // 悬浮高亮
    scheduler.attachEvent("onTemplatesReady", function() {
        var highlight_step = 60; // we are going to highlight 30 minutes timespan

        var highlight_html = "";
        var hours = scheduler.config.last_hour - scheduler.config.first_hour; // e.g. 24-8=16
        var times = hours*60/highlight_step; // number of highlighted section we should add
        var height = scheduler.config.hour_size_px*(highlight_step/60);
        for (var i=0; i<times; i++) {
            highlight_html += "<div class='highlighted_timespan' style='height: "+height+"px;'></div>"
        }
        scheduler.addMarkedTimespan({
            days: "fullweek",
            zones: "fullday",
            html: highlight_html
        });
    });

    // 拖拽
    scheduler.attachEvent("onExternalDragIn", function(id, source, event){
        scheduler.getEvent(id).task_id = tree._dragged[0].id;
        return true;
    });

    // 复制，剪切，粘贴
    var modified_event_id = null;
    scheduler.templates.event_class = function(start, end, event) {
        if (event.id == modified_event_id)
            return "copied_event";
        return ""; // default
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
        return task_opt[ev.task_id]['label'];
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
</script>
