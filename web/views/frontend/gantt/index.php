<?php
use yii\helpers\Html;
use app\models\GanttTasks;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;

$this->title                   = '计划管理';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/lib/dhtmlx.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlxgantt.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlxgantt_meadow.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/gantt/index.css',['depends'=>['app\assets\AppAsset']]);
$this->registerJsFile('@web/js/lib/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxgantt.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlxgantt_marker.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/locale_cn_gantt.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>
<div>
    <h1><?= Html::encode($this->title); ?></h1>
    <div id="gantt_here" style='width:100%; height:640px'></div>
</div>

<script type="text/javascript">
    // 今日线
    var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
    var today = new Date();
    gantt.addMarker({
        start_date: today,
        css: "today",
        text: "Today",
        title:"Today: "+ date_to_str(today)
    });

    // 时间范围选择
    /* var duration = function (a, b, c) { */
    /*     var res = gantt.calculateDuration(a.getDate(false), b.getDate(false)); */
    /*     c.innerHTML = res + ' days'; */
    /* }; */

    /* var calendar_init = function (id, data, date) { */
    /*     var obj = new dhtmlXCalendarObject(id); */
    /*     obj.setDateFormat(data.date_format ? data.date_format : ''); */
    /*     obj.setDate(date ? date : (new Date())); */
    /*     obj.hideTime(); */
    /*     if (data.skin) */
    /*         obj.setSkin(data.skin); */
    /*     return obj; */
    /* }; */

    /* gantt.form_blocks["dhx_calendar"] = { */
    /*     render: function (sns) { */
    /*         return "<div class='dhx_calendar_cont'><input type='text' readonly='true' id='calendar1'/> &#8211; " */
    /*                 + "<input type='text' readonly='true' id='calendar2'/><label id='duration'></label></div>"; */
    /*     }, */
    /*     set_value: function (node, value, task, data) { */
    /*         var a = node._cal_start = calendar_init('calendar1', data, task.start_date); */
    /*         var b = node._cal_end = calendar_init('calendar2', data, task.end_date); */
    /*         var c = node.lastChild; */

    /*         b.setInsensitiveRange(null, new Date(a.getDate(false) - 86400000)); */

    /*         var a_click = a.attachEvent("onClick", function (date) { */
    /*             b.setInsensitiveRange(null, new Date(date.getTime() - 86400000)); */
    /*             duration(a, b, c); */
    /*         }); */

    /*         var b_click = b.attachEvent("onClick", function (date) { */
    /*             duration(a, b, c); */
    /*         }); */

    /*         var a_time_click = a.attachEvent("onChange", function (d) { */
    /*             b.setInsensitiveRange(null, new Date(d.getTime() - 86400000)); */
    /*             duration(a, b, c); */
    /*         }); */

    /*         var b_time_click = b.attachEvent("onChange", function (d) { */
    /*             duration(a, b, c); */
    /*         }); */


    /*         var id = gantt.attachEvent("onAfterLightbox", function () { */
    /*             a.detachEvent(a_click); */
    /*             a.detachEvent(a_time_click); */
    /*             a.unload(); */
    /*             b.detachEvent(b_click); */
    /*             b.detachEvent(b_time_click); */
    /*             b.unload(); */
    /*             a = b = null; */
    /*             this.detachEvent(id); */
    /*         }); */

    /*         document.getElementById('calendar1').value = a.getDate(true); */
    /*         document.getElementById('calendar2').value = b.getDate(true); */
    /*         duration(a, b, c); */
    /*     }, */
    /*     get_value: function (node, task) { */
    /*         task.start_date = node._cal_start.getDate(false); */
    /*         task.end_date = node._cal_end.getDate(false); */
    /*         return task; */
    /*     }, */
    /*     focus: function (node) { */
    /*     } */
    /* }; */

    // 日期格式
    gantt.config.open_tree_initially = true;
    gantt.config.scale_unit = "month";
    gantt.config.row_height = 30;
    gantt.config.date_scale = "%F, %Y";
    gantt.config.scale_height = 50;
    gantt.config.subscales = [
        {unit:"day", step:1, date:"%j, %D" }
    ];

    // section定制
    /* gantt.config.columns = [ */
    /*     {name:"text", label:"Task name", tree:true, width:'*' }, */    
    /*     {name:"start_date", label:"Start time", align: "center" }, */
    /*     {name:"duration",   label:"Duration",   align: "center" }, */
    /*     {name:"priority", label:"Priority", width:80, align: "center", */ 
    /* template: function(item){ */
    /*     if (item.priority == 1) */
    /*         return "High"; */
    /*     if (item.priority == 2) */
    /*         return "Normal"; */
    /*     return "Low"; */
    /* }} */
    /* ]; */
    var field_opt                      = <?php echo $fieldDict;?>;
    var action_opt                     = <?php echo $actionDict;?>;
    gantt.locale.labels.section_field  = "领域";
    gantt.locale.labels.section_action = "行为";
    gantt.locale.labels.section_level  = "层级";

    var custom_sections                = [
        {name: "description", height: 70, map_to: "text", type: "textarea", focus: true},
        {name: "time", type: "duration", map_to: "auto"}
    ];

    gantt.attachEvent("onBeforeLightbox", function(id){
        gantt.resetLightbox();
        var currentSections = custom_sections.slice();
        var task = gantt.getTask(id);
        if (task.$level == 1) {
            if (field_opt) {
                var default_v = field_opt[0]['key'];
            } else {
                var default_v = "";
            }
            var field_section = {name:"field", height:30, type:"select", map_to:"field", options:field_opt, default_value:default_v};
            currentSections.push(field_section);
        } else if (task.$level == 2) {
            if (action_opt) {
                var default_v = action_opt[0]['key'];
            } else {
                var default_v = "";
            }
            var action_section = {name:"action", height:30, type:"select", map_to:"action", options:action_opt, default_value:default_v};
            currentSections.push(action_section);
        }
        gantt.config.lightbox.sections = currentSections;
        return true;
    });

    gantt.attachEvent("onLightbox", function(id) {
    });

    gantt.attachEvent("onBeforeTaskAdd", function(id,item){
        //console.log(item);
        return true;
    });

    // 树状三层结构
    //gantt.config.types.root  = "plan-task";
    gantt.config.types.plan    = "<?php echo GanttTasks::LEVEL_PLAN; ?>";
    gantt.config.types.project = "<?php echo GanttTasks::LEVEL_PROJECT; ?>";
    gantt.config.types.task    = "<?php echo  GanttTasks::LEVEL_TASK; ?>";

    gantt.config.lightbox.project_sections = gantt.config.lightbox.sections;
    gantt.config.lightbox.plan_sections    = [
        {name: "description", height: 70, map_to: "text", type: "textarea", focus: true},
        {name: "time", type: "duration", map_to: "auto", readonly: true}
    ];

    function defaultValues(task) {
        var text = "",
                index = gantt.getChildren(task.parent || gantt.config.root_id).length + 1,
                types = gantt.config.types;

        switch (task.type) {
            case types.plan:
                text = "计划";
                break;
            case types.project:
                text = '项目';
                break;
            default:
                text = '任务';
                break;
        }
        task.text = text + " #" + index;
        return;
    }

    gantt.attachEvent("onTaskLoading", function (task) {
        var parent = task.parent,
                types = gantt.config.types,
                level = 0;

        if (parent == gantt.config.root_id || !parent) {
            level = 0;
        } else {
            level = gantt.getTask(task.parent).$level + 1;
        }
        switch (level) {
            case 0:
                task.type = types.plan;
                break;
            case 1:
                task.type = types.project;
                break;
            default:
                task.type = types.task;
                break;
        }

        return true;
    })

    gantt.attachEvent("onTaskCreated", function (task) {
        var parent = task.parent,
                types = gantt.config.types,
                level = 0;

        if (parent == gantt.config.root_id || !parent) {
            level = 0;
        } else {
            level = gantt.getTask(task.parent).$level + 1;
        }
        switch (level) {
            case 0:
                task.type = types.plan;
                break;
            case 1:
                task.type = types.project;
                break;
            default:
                task.type = types.task;
                break;
        }

        defaultValues(task);
        return true;
    });

    

    // 进度百分比
    /* gantt.templates.progress_text = function(start, end, task){ */
    /*     return "<span style='text-align:left;'>" + Math.round(task.progress*100)+ "% </span>"; */
    /* }; */

    // 子项目与父项目合并
    gantt.templates.rightside_text = function(start, end, task){
        if(task.type == gantt.config.types.milestone){
            return task.text;
        }
        return "";
    };

    function createBox(sizes, class_name){
        var box = document.createElement('div');
        box.style.cssText = [
            "height:" + sizes.height + "px",
            "line-height:" + sizes.height + "px",
            "width:" + sizes.width + "px",
            "top:" + sizes.top + 'px',
            "left:" + sizes.left + "px",
            "position:absolute"
        ].join(";");
        box.className = class_name;
        return box;
    }

    gantt.templates.grid_row_class = gantt.templates.task_class = function(start, end, task){
        // 树状三层结构

        var css = [];
        switch (task.type) {
            case gantt.config.types.plan:
                css.push('project-task');
                break;
            case gantt.config.types.project:
                css.push('phase-task');
                break;
            default:
                css.push('regular-task');
                break;
        }

        // 父项目与子项目合并
        if(gantt.hasChild(task.id)){
            css.push("task-parent");
        }
        if (!task.$open && gantt.hasChild(task.id)) {
            css.push("task-collapsed");
        }

        // meadow皮肤

        if (task.$level == 0) {
            css.push("gantt_project");
        }

        return css.join(" ");
    };

    // 皮肤
    gantt.templates.task_row_class = function(start, end, item){
        return item.$level==0?"gantt_project":""
    }

    gantt.addTaskLayer(function show_hidden(task) {
        if (!task.$open && gantt.hasChild(task.id)) {
            var sub_height = gantt.config.row_height - 5,
                el = document.createElement('div'),
                sizes = gantt.getTaskPosition(task);

            var sub_tasks = gantt.getChildren(task.id);

            var child_el;

            for (var i = 0; i < sub_tasks.length; i++){
                var child = gantt.getTask(sub_tasks[i]);
                var child_sizes = gantt.getTaskPosition(child);

                child_el = createBox({
                    height: sub_height,
                    top:sizes.top,
                    left:child_sizes.left,
                    width: child_sizes.width
                }, "child_preview gantt_task_line");
                child_el.innerHTML =  child.text;
                el.appendChild(child_el);
            }
            return el;
        }
        return false;
    });

    // 初始化
    gantt.config.xml_date = "%Y-%m-%d %H:%i:%s";
    gantt.config.show_errors = true;
    gantt.init("gantt_here");
    gantt.load("/frontend/gantt-api/data");

    var dp = new gantt.dataProcessor("/frontend/gantt-api");
    dp.init(gantt);
    dp.setTransactionMode("REST");

</script>
