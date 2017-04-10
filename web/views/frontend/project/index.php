<?php
use yii\helpers\Html;
use app\models\Project;
use app\models\Config;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;

$this->title                   = '项目管理';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlx.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxgantt.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxgantt_meadow.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/gantt/index.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxgantt.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxgantt_marker.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/dhtmlx/locale_cn_gantt.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
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

    // 日期格式
    gantt.config.open_tree_initially = true;
    gantt.config.scale_unit            = "month";
    gantt.config.row_height            = 30;
    gantt.config.date_scale            = "%F, %Y";
    gantt.config.scale_height          = 50;
    gantt.config.subscales             = [
        {unit:"day", step:1, date:"%j, %D" }
    ];

    // section定制
    gantt.config.columns = [
        {name:"text", label:"名称", tree:true, width:'*' },    
        {name:"start_date", label:"开始日期", align: "center" },
        {name:"duration",   label:"持续天数",   align: "center" },
        /* {name:"priority", label:"Priority", width:80, align: "center", */ 
        /*     template: function(item){ */
        /*         if (item.priority == 1) */
        /*             return "High"; */
        /*         if (item.priority == 2) */
        /*             return "Normal"; */
        /*         return "Low"; */
        /*     }} */
        {name:"add",        label:"",           width:44 }
    ];
    var field_opt                           = <?php echo $fieldDict;?>;
    var priority_opt                        = <?php echo $priorityDict;?>;
    var type_dict                           = <?php echo $typeDict;?>;
    var entity_field_dict                   = <?php echo $entityFieldDict;?>;
    gantt.locale.labels.section_description = "名称";
    gantt.locale.labels.section_field_id    = "领域";
    gantt.locale.labels.section_priority_id = "重要性";
    gantt.locale.labels.section_entity_id   = "相关实体";
    gantt.locale.labels.section_plan_time   = "计划时间";
    gantt.locale.labels.section_type_id     = "行为类别";

    var custom_sections                = [
        {name: "description", height: 30, map_to: "text", type: "textarea", focus: true},
        {name: "time", type: "duration", map_to: "auto"}
    ];
    var action_sections                = [
        {name: "description", height: 30, map_to: "text", type: "textarea", focus: true},
        {name: "plan_time", height: 30, map_to: "plan_time", type: "textarea", focus: true},
    ];

    gantt.attachEvent("onBeforeLightbox", function(id){
        gantt.resetLightbox();
        var task = gantt.getTask(id);

        if (task.$level == <?php echo Project::LEVEL_PROJECT;?>) {
            var currentSections = custom_sections.slice();
            if (field_opt) {
                var default_v = field_opt[0]['key'];
            } else {
                var default_v = "";
            }
            var field_section = {name:"field_id", height:30, type:"select", map_to:"field_id", options:field_opt, default_value:default_v};
            currentSections.push(field_section);

            if (priority_opt) {
                var default_v = priority_opt[0]['key'];
            } else {
                var default_v = "";
            }
            var priority_section = {name:"priority_id", height:30, type:"select", map_to:"priority_id", options:priority_opt, default_value:default_v};
            currentSections.push(priority_section);
            gantt.config.lightbox.sections = currentSections;
        } else if (task.$level == <?php echo Project::LEVEL_TASK;?>) {
            var currentSections    = custom_sections.slice();

            var parent_project_obj = gantt.getTask(task.parent);
            if (parent_project_obj.field_id != <?php echo Config::FIELD_GENERAL ?>) {
                var project_field_id   = parent_project_obj.field_id;
                var project_obj_id     = parent_project_obj.obj_id;
                var entity_arr         = entity_field_dict[project_field_id][project_obj_id].slice();
                entity_arr.unshift({"key" : 0, "label" : "无关联"});

                if (entity_arr) {
                    var default_v = entity_arr[0]['key'];
                } else {
                    var default_v = "";
                }
                var entity_section   = {name:"entity_id", height:30, type:"select", map_to:"entity_id", options:entity_arr, default_value:default_v};
                currentSections.push(entity_section);
            }

            gantt.config.lightbox.sections = currentSections;
        } else if (task.$level == <?php echo Project::LEVEL_ACTION;?>) {
            var currentSections = action_sections.slice();
            var task_level_obj = gantt.getTask(task.parent); 
            var project_level_obj = gantt.getTask(task_level_obj.parent); 
            var action_type_arr = type_dict[project_level_obj.field_id];

            if (action_type_arr) {
                var default_v = action_type_arr[0]['key'];
            } else {
                var default_v = "";
            }
            var action_type_section = {name:"type_id", height:30, type:"select", map_to:"type_id", options:action_type_arr, default_value:default_v};
            currentSections.push(action_type_section);
            gantt.config.lightbox.sections = currentSections;
        }
        return true;
    });

    // 自定义按键+待定
    /* gantt.config.buttons_left=["dhx_save_btn","dhx_cancel_btn","unscheduler_button"]; */
    /* gantt.locale.labels["unscheduler_button"] = "待定"; */
    /* gantt.attachEvent("onLightboxButton", function(button_id, node, e){ */
    /*     if(button_id == "unscheduler_button"){ */
    /*         var id = gantt.getState().lightbox; */
    /*         gantt.getTask(id).unscheduled_flag = 1; */
    /*         gantt.getTask(id).unscheduled = true; */

    /*         $(".gantt_duration_dec").attr("disabled","disabled"); */
    /*         $(".gantt_duration_inc").attr("disabled","disabled"); */
    /*         $(".gantt_duration_value").val(0); */
    /*         $(".gantt_duration_value").attr("readonly", "readonly"); */
    /*     } */
    /* }); */
    function isInt(value) {
          return !isNaN(value) && (function(x) { return (x | 0) === x;  })(parseFloat(value))
    }

    gantt.attachEvent("onLightboxSave", function(id, task, is_new){
        if (task.$level == <?php echo Project::LEVEL_ACTION;?>) {
            task.unscheduled = true;
            task.duration = 0;
            if (!isInt(task.plan_time) || task.plan_time <= 0 || task.plan_time > 5) {
                swal("操作失败!", "计划时间必须是数字，且范围在1-5以内", "error");
                return false;
            }
        }
        return true;
    })

    gantt.attachEvent("onAfterTaskUpdate", function(id,item){
        if (item.unscheduled) {
            item.duration = 0;
            // before事件中，item的duration无法修改，估计跟生命周期有关
            // 修改table_row中，duration的持续时间
            gantt.getTaskRowNode(id).childNodes[2].childNodes[0].innerHTML = 0;
        }
        return true;
    });

    // 树状三层结构
    gantt.config.types.project = "<?php echo Project::LEVEL_PROJECT; ?>";
    gantt.config.types.task    = "<?php echo Project::LEVEL_TASK; ?>";
    gantt.config.types.action    = "<?php echo  Project::LEVEL_ACTION; ?>";
    gantt.config.lightbox.project_sections = gantt.config.lightbox.sections;

    function defaultValues(task) {
        var text = "",
                index = gantt.getChildren(task.parent || gantt.config.root_id).length + 1,
                types = gantt.config.types;

        switch (task.type) {
            case types.project:
                text = '项目';
                break;
            case types.task:
                text = "任务";
                break;
            case types.action:
                text = "行动";
                break;
            default:
                text = '';
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
            case <?php echo Project::LEVEL_PROJECT;?>:
                task.type = types.project;
                break;
            case <?php echo Project::LEVEL_TASK;?>:
                task.type = types.task;
                break;
            default:
                task.type = types.action;
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
            case <?php echo Project::LEVEL_PROJECT;?>:
                task.type = types.project;
                break;
            case <?php echo Project::LEVEL_TASK;?>:
                task.type = types.task
                break;
            default:
                task.type = types.action;
                break;
        }

        defaultValues(task);
        return true;
    });

    // 子项目与父项目合并
    /* gantt.templates.rightside_text = function(start, end, task){ */
    /*     if(task.type == gantt.config.types.milestone){ */
    /*         return task.text; */
    /*     } */
    /*     return ""; */
    /* }; */

    /* function createBox(sizes, class_name){ */
    /*     var box = document.createElement('div'); */
    /*     box.style.cssText = [ */
    /*         "height:" + sizes.height + "px", */
    /*         "line-height:" + sizes.height + "px", */
    /*         "width:" + sizes.width + "px", */
    /*         "top:" + sizes.top + 'px', */
    /*         "left:" + sizes.left + "px", */
    /*         "position:absolute" */
    /*     ].join(";"); */
    /*     box.className = class_name; */
    /*     return box; */
    /* } */

    gantt.templates.grid_row_class = gantt.templates.task_class = function(start, end, task){
        // 树状三层结构

        var css = [];
        switch (task.type) {
            case gantt.config.types.project:
                css.push('project-task');
                break;
            case gantt.config.types.task:
                css.push('phase-task');
                break;
            default:
                css.push('regular-task');
                break;
        }

        // 父项目与子项目合并
        /* if(gantt.hasChild(task.id)){ */
        /*     css.push("task-parent"); */
        /* } */
        /* if (!task.$open && gantt.hasChild(task.id)) { */
        /*     css.push("task-collapsed"); */
        /* } */

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

    // 副项目与子项目合并
    /* gantt.addTaskLayer(function show_hidden(task) { */
    /*     if (!task.$open && gantt.hasChild(task.id)) { */
    /*         var sub_height = gantt.config.row_height - 5, */
    /*             el = document.createElement('div'), */
    /*             sizes = gantt.getTaskPosition(task); */

    /*         var sub_tasks = gantt.getChildren(task.id); */

    /*         var child_el; */

    /*         for (var i = 0; i < sub_tasks.length; i++){ */
    /*             var child = gantt.getTask(sub_tasks[i]); */
    /*             var child_sizes = gantt.getTaskPosition(child); */

    /*             child_el = createBox({ */
    /*                 height: sub_height, */
    /*                 top:sizes.top, */
    /*                 left:child_sizes.left, */
    /*                 width: child_sizes.width */
    /*             }, "child_preview gantt_task_line"); */
    /*             child_el.innerHTML =  child.text; */
    /*             el.appendChild(child_el); */
    /*         } */
    /*         return el; */
    /*     } */
    /*     return false; */
    /* }); */

    // 左侧grid拉拽(与order互相冲突)
    gantt.config.order_branch = true;
    gantt.config.xml_date     = "%Y-%m-%d %H:%i:%s";
    gantt.config.show_errors  = true;
    gantt.config.grid_width   = 450;
    // 滑动触碰会出现奇怪的情况
    gantt.config.touch        = "force";

    gantt.attachEvent("onLoadEnd", function(){
        gantt.sort("sort_date", true);
    });

    // 初始化
    gantt.init("gantt_here");
    gantt.load("/frontend/project-api/data");

    var dp = new gantt.dataProcessor("/frontend/project-api");
    dp.init(gantt);
    dp.setTransactionMode("REST");
    dp.attachEvent("onAfterUpdate", function(id, action, tid, response){
        if (action == "error") {
            swal("操作失败!", response.msg, "error");
        }
    })
</script>
