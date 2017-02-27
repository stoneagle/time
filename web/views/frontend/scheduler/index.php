<?php
use yii\helpers\Html;
use app\assets\AppAsset;

$this->title                   = '时间管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);

$this->registerCssFile('@web/css/lib/dhtmlxscheduler.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlxtree_dhx_skyblue.css',['depends'=>['app\assets\AppAsset']]);

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
    <div class="col-md-2 panel" id="treebox" style="height:1000px;">
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
    tree.load("/frontend/gantt-api/task-tree",'json');

    // 时间表
    scheduler.config.first_hour = 3;
    scheduler.config.time_step = 15;
    //scheduler.config.touch = "force";
    scheduler.config.multi_day = true;
    scheduler.config.xml_date="%Y-%m-%d %H:%i:%s";

    // 额外选项
    var task_opt = <?php echo $taskDict; ?>;
    scheduler.locale.labels.section_task = "所属任务";
    scheduler.config.lightbox.sections = [  
        {name:"description", height:200, map_to:"text", type:"textarea" , focus:true},
        {name:"task", height:21, map_to:"task", type:"select", options:task_opt},
        {name:"time", height:72, type:"time", map_to:"auto"}    
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
        scheduler.getEvent(id).task = tree._dragged[0].id;
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
