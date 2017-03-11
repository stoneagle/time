<?php
use yii\helpers\Html;
use app\models\Process;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '时间管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxscheduler.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/scheduler/index.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_outerdrag.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_quick_info.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_minical.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_key_nav.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_recurring.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_limit.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxscheduler_editors.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxcommon.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/locale_cn_scheduler.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="col-md-2 panel panel-default" id="treebox" style="height:1024px;" ></div>
    <div id="review_scheduler" class="dhx_cal_container col-md-10 panel panel-default" style=' height:1024px; '>
        <div class="dhx_cal_navline">
            <div class="dhx_cal_prev_button">&nbsp;</div>
            <div class="dhx_cal_next_button">&nbsp;</div>
            <div class="dhx_cal_today_button"></div>
            <div class="dhx_cal_date"></div>
            <div class="dhx_minical_icon" id="review_dhx_minical_icon" onclick="show_minical()">&nbsp;</div>
            <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
            <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
            <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
        </div>
        <div class="dhx_cal_header"></div>
        <div class="dhx_cal_data"></div>       
    </div>
</div>

<script type="text/javascript">
    // 时间表 
    var review_scheduler = init_review_scheduler();
    function init_review_scheduler() 
    {
        scheduler.config.first_hour = 3;
        scheduler.config.time_step = 15;
        scheduler.config.touch = "force";
        scheduler.config.multi_day = true;
        scheduler.config.xml_date="%Y-%m-%d %H:%i:%s";
        
        var init_sections = [  
            {name:"description", height:200, map_to:"text", type:"textarea" , focus:true},
            {name:"time", height:72, type:"calendar_time", map_to:"auto"},
        ];

        /* // 额外选项 */
        scheduler.config.lightbox.sections         = init_sections;


        /* // 自定义event内容 */
        scheduler.attachEvent("onTemplatesReady", function(){
            scheduler.templates.event_text=function(start,end,event){
                return "<b>" + event.text+ "</b>";
            }
        }); 

        // 初始化
        scheduler.init('review_scheduler', new Date(),"week");
        scheduler.load("/frontend/action-api/scheduler","json");

        var dp = new dataProcessor("/frontend/action-api/");
        dp.init(scheduler);
        dp.setTransactionMode("REST");

        // 左上角日历
        function show_minical(){
        if (scheduler.isCalendarVisible())
            scheduler.destroyCalendar();
        else
            scheduler.renderCalendar({
                position:"review_dhx_minical_icon",
                date:scheduler._date,
                navigation:true,
                handler:function(date,calendar){
                    scheduler.setCurrentView(date);
                    scheduler.destroyCalendar()
                }
            });
        }


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

        /* // 复制，剪切，粘贴 */
        /* var modified_event_id = null; */
        /* scheduler.templates.event_class = function(start, end, event) { */
        /*     // my_event是自定义event的class */
        /*     if (event.id == modified_event_id) */
        /*         return "copied_event my_event"; */
        /*     return "my_event"; */ 
        /* }; */

        /* scheduler.attachEvent("onEventCopied", function(ev) { */
        /*     dhtmlx.message("You've copied event: <br/><b>"+ev.text+"</b>"); */
        /*     modified_event_id = ev.id; */
        /*     scheduler.updateEvent(ev.id); */
        /* }); */
        /* scheduler.attachEvent("onEventCut", function(ev) { */
        /*     dhtmlx.message("You've cut event: <br/><b>"+ev.text+"</b>"); */
        /*     modified_event_id = ev.id; */
        /*     scheduler.updateEvent(ev.id); */
        /* }); */

        /* scheduler.attachEvent("onEventPasted", function(isCopy, modified_ev, original_ev) { */
        /*     modified_event_id = null; */
        /*     scheduler.updateEvent(modified_ev.id); */

        /*     var evs = scheduler.getEvents(modified_ev.start_date, modified_ev.end_date); */
        /*     if (evs.length > 1) { */
        /*         dhtmlx.modalbox({ */
        /*             text: "There is another event at this time! What do you want to do?", */
        /*             width: "500px", */
        /*             position: "middle", */
        /*             buttons:["Revert changes", "Edit event", "Save changes"], */
        /*             callback: function(index) { */
        /*                 switch(+index) { */
        /*                     case 0: */
        /*                         if (isCopy) { */
        /*                             // copy operation, need to delete new event */
        /*                             scheduler.deleteEvent(modified_ev.id); */
        /*                         } else { */
        /*                             // cut operation, need to restore dates */
        /*                             modified_ev.start_date = original_ev.start_date; */
        /*                             modified_ev.end_date = original_ev.end_date; */
        /*                             scheduler.setCurrentView(); */
        /*                         } */
        /*                         break; */
        /*                     case 1: */
        /*                         scheduler.showLightbox(modified_ev.id); */
        /*                         break; */
        /*                     case 2: */
        /*                         return; */
        /*                 } */
        /*             } */
        /*         }); */
        /*     } */
        /* }); */

        // 快捷信息标题显示
        /* scheduler.templates.quick_info_title = function(start, end, ev){ */ 
        /*     return ev.process_name; */
        /* }; */

        // 自定义event盒子
        /* scheduler.renderEvent = function(container, ev, width, height, header_content, body_content) { */
        /*     var container_width = container.style.width; // e.g. "105px" */

        /*     // move section */
        /*     var html = "<div class='dhx_event_move my_event_move' style='width: " + container_width + "'></div>"; */
        /*     // container for event contents */
        /*     html+= "<div class='my_event_body'>"; */
        /*         html += "<span class='event_date'>"; */
        /*         // two options here: show only start date for short events or start+end for long */
        /*         if ((ev.end_date - ev.start_date) / 60000 > 40) { // if event is longer than 40 minutes */
        /*             html += scheduler.templates.event_header(ev.start_date, ev.end_date, ev); */
        /*             html += "</span><br/>"; */
        /*         } else { */
        /*             html += scheduler.templates.event_date(ev.start_date) + "</span>"; */
        /*         } */
        /*         // displaying event text */
        /*     html += "</div>"; */

        /*     // resize section */
        /*     html += "<div class='dhx_event_resize my_event_resize' style='width: " + container_width + "'></div>"; */

        /*     container.innerHTML = html; */
        /*     return true; // required, true - we've created custom form; false - display default one instead */
        /* }; */
        return scheduler;
    }
</script>
