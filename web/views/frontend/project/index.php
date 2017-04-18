<?php
use yii\helpers\Html;
use app\models\Project;
use app\models\Area;
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
    <h1>
        <?= Html::encode($this->title); ?>
    </h1>
    <p>
        <?= Html::a('展示已完成项目', ['/###'], ['class' => 'btn btn-success', 'id' => "toggle_finish"]) ?>
    </p>
    <div id="gantt_here" style='width:100%; height:640px'></div>
</div>

<script type="text/javascript">
    function init_gantt(data_load_href) {
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

        var target_dict        = <?php echo $targetDict; ?>;
        var target_dict_raw    = <?php echo $targetDictRaw; ?>;
        var action_status_dict = <?php echo $actionStatusDict ;?>;
        var target_entity_raw  = <?php echo $targetEntityMap; ?>;

        // section定制
        gantt.config.columns = [
            {name:"text", label:"名称", tree:true, width:250 },    
            {name:"relate_info", label:"隶属", width:150, align: "left", 
                template: function(item){
                    if (item.type == <?php echo Project::LEVEL_PROJECT;?>) {

                        return target_dict_raw[item.target_id];
                    } else if (item.type == <?php echo Project::LEVEL_TASK;?>) {
                        var parent_obj = gantt.getTask(item.parent);
                        if (typeof(target_entity_raw[parent_obj.target_id]) == "undefined" || typeof(target_entity_raw[parent_obj.target_id][item.entity_id]) == "undefined") {
                            return "无关联";
                        } else {
                            return "&nbsp&nbsp" + target_entity_raw[parent_obj.target_id][item.entity_id];
                        }
                    } else if (item.type == <?php echo Project::LEVEL_ACTION;?>) {
                        return "&nbsp&nbsp&nbsp&nbsp" + action_status_dict[item.status];
                    }
                }
            },
            {name:"start_date", label:"开始日期", align: "center" },
            {name:"duration",   label:"持续天数", align: "center" },
            {name:"add",        label:"",         width:44 }
        ];
        gantt.locale.labels.section_description = "名称";
        gantt.locale.labels.section_target_id   = "所属目标";
        gantt.locale.labels.section_entity_id   = "相关实体";
        gantt.locale.labels.section_plan_time   = "计划时间";

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

                if (target_dict) {
                    var default_v = target_dict[0]['key'];
                } else {
                    var default_v = "";
                }
                var target_section = {name:"target_id", height:30, type:"select", map_to:"target_id", options:target_dict, default_value:default_v};
                currentSections.unshift(target_section);
                gantt.config.lightbox.sections = currentSections;
                gantt.config.buttons_left=["dhx_save_btn","dhx_cancel_btn","complete_btn"];
            } else if (task.$level == <?php echo Project::LEVEL_TASK;?>) {
                var currentSections    = custom_sections.slice();
                var parent_project_obj = gantt.getTask(task.parent);
                //if (parent_project_obj.field_id != <?php echo Area::FIELD_GENERAL ?>) {
                    var target_id   = parent_project_obj.target_id;
                    var href = "/frontend/target/get-entity-dict/" + target_id;
                    var post_data = {}
                    $.ajax({
                        url: href,
                        data: post_data,
                        dataType: 'text',
                        async: false,
                        type: 'POST',
                        success: function(result) {
                            var data = eval('(' + result + ')');  
                            if (data.error != 0) {
                                swal("实体获取失败!", data.message, "error");
                            } else {
                                var entity_dict = data.data.dict;
                                entity_dict.unshift({"key" : "0", "label" : "无关联"});
                                var entity_section = {name:"entity_id", height:30, type:"select", map_to:"entity_id", options:entity_dict, default_value:entity_dict[0]['key']};
                                currentSections.unshift(entity_section);
                            }
                        },
                        error: function(data) {
                            swal("操作失败!", data.message, "error");
                        }
                    })
                //}
                gantt.config.lightbox.sections = currentSections;
                gantt.config.buttons_left=["dhx_save_btn","dhx_cancel_btn","complete_btn"];
            } else if (task.$level == <?php echo Project::LEVEL_ACTION;?>) {
                var currentSections   = action_sections.slice();
                gantt.config.lightbox.sections = currentSections;
                gantt.config.buttons_left=["dhx_save_btn","dhx_cancel_btn"];
            }
            return true;
        });

        // 自定义按键+已完成
        gantt.locale.labels["complete_btn"] = "完成";
        gantt.attachEvent("onLightboxButton", function(button_id, node, e){
            if(button_id == "complete_btn"){
                var id = gantt.getState().lightbox;
                gantt.getTask(id).progress = 1;
                gantt.updateTask(id)
                gantt.hideLightbox();
            }
        });
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
        // gantt.templates.rightside_text = function(start, end, task){ 
        //     if(task.type == gantt.config.types.milestone){ 
        //         return task.text; 
        //     } 
        //     return ""; 
        // }; 

        // function createBox(sizes, class_name){ 
        //     var box = document.createElement('div'); 
        //     box.style.cssText = [ 
        //         "height:" + sizes.height + "px", 
        //         "line-height:" + sizes.height + "px", 
        //         "width:" + sizes.width + "px", 
        //         "top:" + sizes.top + 'px', 
        //         "left:" + sizes.left + "px", 
        //         "position:absolute" 
        //     ].join(";"); 
        //     box.className = class_name; 
        //     return box; 
        // } 

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
            // if(gantt.hasChild(task.id)){ 
            //     css.push("task-parent"); 
            // } 
            // if (!task.$open && gantt.hasChild(task.id)) { 
            //     css.push("task-collapsed"); 
            // } 

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
        // gantt.addTaskLayer(function show_hidden(task) { 
        //     if (!task.$open && gantt.hasChild(task.id)) { 
        //         var sub_height = gantt.config.row_height - 5, 
        //             el = document.createElement('div'), 
        //             sizes = gantt.getTaskPosition(task); 
        //         var sub_tasks = gantt.getChildren(task.id); 
        //         var child_el; 
        //         for (var i = 0; i < sub_tasks.length; i++){ 
        //             var child = gantt.getTask(sub_tasks[i]); 
        //             var child_sizes = gantt.getTaskPosition(child); 
        //             child_el = createBox({ 
        //                 height: sub_height, 
        //                 top:sizes.top, 
        //                 left:child_sizes.left, 
        //                 width: child_sizes.width 
        //             }, "child_preview gantt_task_line"); 
        //             child_el.innerHTML =  child.text; 
        //             el.appendChild(child_el); 
        //         } 
        //         return el; 
        //     } 
        //     return false; 
        // }); 

        // 左侧grid拉拽(与order互相冲突)
        gantt.config.order_branch = true;
        gantt.config.xml_date     = "%Y-%m-%d %H:%i:%s";
        gantt.config.show_errors  = true;
        gantt.config.grid_width   = 600;
        // 滑动触碰会出现奇怪的情况
        // gantt.config.touch        = "force";

        gantt.attachEvent("onLoadEnd", function(){
            gantt.sort("start_date", false);
        });

        // 初始化
        gantt.init("gantt_here");
        gantt.load(data_load_href);

        var dp = new gantt.dataProcessor("/frontend/project-api");
        dp.init(gantt);
        dp.setTransactionMode("REST");
        dp.attachEvent("onAfterUpdate", function(id, action, tid, response){
            if (action == "error") {
                swal("操作失败!", response.msg, "error");
            }
        })

        var progress_finish_flag = false;
        $("#toggle_finish").on('click', function(e) {
            e.preventDefault();
            progress_finish_flag = !progress_finish_flag;
            gantt.refreshData();
        });

        gantt.attachEvent("onBeforeTaskDisplay", function(id, task){
            var ret = true;
            if (task.type == <?php echo Project::LEVEL_PROJECT;?>) {
                if (!progress_finish_flag && task.progress == 1) {
                    ret = false;
                }
            }
            return ret;
        });
    }
    var load_no_progress_href  = "/frontend/project-api/data";
    init_gantt(load_no_progress_href);
</script>
