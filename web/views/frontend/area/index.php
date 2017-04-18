<?php
use yii\helpers\Html;
use app\models\BusinessAssets;
use app\models\Project;
use app\models\Area;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '知识管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/css/lib/select2.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/jstree/jstree.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/select2.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/jstree.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/d3.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
?>
<style type="text/css">
    .area-tree {
        padding-left:0px;
        padding-right:0px;
    }

    // 右侧圈圈图
    .node {
        cursor: pointer;
    }

    .node:hover {
        stroke: #000;
        stroke-width: 1.5px;
    }

    .node--leaf {
        fill: white;
    }

    .label {
        font: 11px "Helvetica Neue", Helvetica, Arial, sans-serif;
        text-anchor: middle;
        text-shadow: 0 1px 0 #fff, 1px 0 0 #fff, -1px 0 0 #fff, 0 -1px 0 #fff;
    }

    .label,
    .node--root,
    .node--leaf {
        pointer-events: all;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="col-md-4 panel panel-default area-tree" style="height:1024px;" >
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a id="nav_<?php echo Area::$field_en_arr[Area::FIELD_KNOWLEDGE];?>" href="#<?php echo Area::$field_en_arr[Area::FIELD_KNOWLEDGE];?>" aria-controls="<?php echo Area::$field_en_arr[Area::FIELD_KNOWLEDGE];?>" role="tab" data-toggle="tab"><?php echo Area::$field_arr[Area::FIELD_KNOWLEDGE];?></a></li> 
            <li role="presentation" ><a id="nav_<?php echo Area::$field_en_arr[Area::FIELD_WEALTH];?>" href="#<?php echo Area::$field_en_arr[Area::FIELD_WEALTH];?>" aria-controls="<?php echo Area::$field_en_arr[Area::FIELD_WEALTH];?>" role="tab" data-toggle="tab"><?php echo Area::$field_arr[Area::FIELD_WEALTH];?></a></li>
            <li role="presentation" ><a id="nav_<?php echo Area::$field_en_arr[Area::FIELD_CULTURE]; ?>" href="#<?php echo Area::$field_en_arr[Area::FIELD_CULTURE];?>" aria-controls="<?php echo Area::$field_en_arr[Area::FIELD_CULTURE];?>" role="tab" data-toggle="tab"><?php echo Area::$field_arr[Area::FIELD_CULTURE];?></a></li>
            <li role="presentation" ><a id="nav_<?php echo Area::$field_en_arr[Area::FIELD_SOCIAL];?>" href="#<?php echo Area::$field_en_arr[Area::FIELD_SOCIAL];?>" aria-controls="<?php echo Area::$field_en_arr[Area::FIELD_SOCIAL];?>" role="tab" data-toggle="tab"><?php echo Area::$field_arr[Area::FIELD_SOCIAL];?></a></li>
            <li role="presentation" ><a id="nav_<?php echo Area::$field_en_arr[Area::FIELD_CHANLLEGE];?>" href="#<?php echo Area::$field_en_arr[Area::FIELD_CHANLLEGE];?>" aria-controls="<?php echo Area::$field_en_arr[Area::FIELD_CHANLLEGE];?>" role="tab" data-toggle="tab"><?php echo Area::$field_arr[Area::FIELD_CHANLLEGE];?></a></li>
            <div style="float:right;margin-top:5px;margin-right:10px" >
                <button type="button" class="btn btn-success btn-sm" id="new_root_area_btn" >新增根领域</button>
            </div>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" field_id="<?php echo Area::FIELD_KNOWLEDGE;?>" class="tab-pane active" id="<?php echo Area::$field_en_arr[Area::FIELD_KNOWLEDGE];?>">
                <div id="jstree_<?php echo Area::$field_en_arr[Area::FIELD_KNOWLEDGE];?>"></div>
            </div>
            <div role="tabpanel" field_id="<?php echo Area::FIELD_WEALTH;?>" class="tab-pane" id="<?php echo Area::$field_en_arr[Area::FIELD_WEALTH];?>">
                <div id="jstree_<?php echo Area::$field_en_arr[Area::FIELD_WEALTH];?>"></div>
            </div>
            <div role="tabpanel" field_id="<?php echo Area::FIELD_CULTURE;?>" class="tab-pane" id="<?php echo Area::$field_en_arr[Area::FIELD_CULTURE];?>">
                <div id="jstree_<?php echo Area::$field_en_arr[Area::FIELD_CULTURE];?>"></div>
            </div>
            <div role="tabpanel" field_id="<?php echo Area::FIELD_SOCIAL;?>" class="tab-pane" id="<?php echo Area::$field_en_arr[Area::FIELD_SOCIAL];?>">
                <div id="jstree_<?php echo Area::$field_en_arr[Area::FIELD_SOCIAL];?>"></div>
            </div>
            <div role="tabpanel" field_id="<?php echo Area::FIELD_CHANLLEGE;?>" class="tab-pane" id="<?php echo Area::$field_en_arr[Area::FIELD_CHANLLEGE];?>">
                <div id="jstree_<?php echo Area::$field_en_arr[Area::FIELD_CHANLLEGE];?>"></div>
            </div>
        </div>
    </div>
    <div id="area_bubble" class="col-md-8 panel panel-default" style=' height:1024px; '>
        <svg width="960" height="960"></svg>
    </div>
</div>

<div class="modal fade" id="root_add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="root_form" method="post" action="" class="form-horizontal" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">新增根领域</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="root_name" class="col-sm-3 control-label">领域名称:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="root_name" name="name"></input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="root_field" class="col-sm-3 control-label">所属范畴:</label>
                        <div class="col-sm-9">
                            <select id="root_field" name="field_id" class="form-control" >
                            </select>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="root_parent" name="parent" value=0 >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary" id="tree_save_submit" >提交</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    // jstree
    $("#new_root_area_btn").on("click", function(e){
        $("#root_add").modal("show");
    });
    var field_list         = <?php echo $field_list;?>;
    var field_dict_select2 = <?php echo $field_dict_select2;?>;
    var field_en_dict      = <?php echo $field_en_dict;?>;

    var jstree_prefix = "jstree_"
    var nav_prefix = "nav_"

    var href_area_add    = "/frontend/area/add-area";
    var href_area_update = "/frontend/area/update-area";
    var href_area_del    = "/frontend/area/del-area";

    $("#root_field").select2(field_dict_select2);

    $('#root_form').on('submit', function(e){
        e.preventDefault();
        var post_data = $(this).serializeArray();
        var href = href_area_add;
        directPost(href, post_data, false);
    });

    var i = 0;
    for (var k in field_list) {
        var area_fix = field_en_dict[k];
        area_tree_id = "#" + jstree_prefix + area_fix;
        // 初始化工程内容
        if (i == 0) {
            init_jstree(area_tree_id, k);
            init_circle(k);
        }
        
        // 点击触发 
        $("#" + nav_prefix + area_fix).on("click", { tree_id:area_tree_id,k:k } ,function(e){
            init_jstree(e.data.tree_id, e.data.k);
            init_circle(e.data.k);
        });

        i++;
    } 

    // jstree初始化
    function init_jstree(tree_id, area_key)
    {
        $(tree_id).jstree({
            "core" : {
                "animation" : 0,
                "check_callback" : true,
                "themes" : { "stripes" : true },
                'data' : {
                    'url' : function (node) {
                    return node.id === '#' ?
                        '/frontend/area/'+ area_key+'/area-tree-root' : '/frontend/area/'+ area_key+'/area-tree-children';
                    },
                    'data' : function (node) {
                        return { 'id' : node.id };
                    }
                }
            },
            "types" : {
                "#" : {
                    "max_children" : 1,
                    "max_depth" : 4,
                    "valid_children" : ["root"]
                },
                "root" : {
                    "icon" : "/static/3.3.3/assets/images/tree_icon.png",
                    "valid_children" : ["default"]
                },
                "default" : {
                    "valid_children" : ["default","file"]
                },
                "file" : {
                    "icon" : "glyphicon glyphicon-file",
                    "valid_children" : []
                }
            },
            /* "contextmenu" : { */
            /*     "items": { */
            /*         "create": { */
            /*             "label": "Create", */
            /*             "action": function (obj) { */
            /*                 console.log(obj); */
            /*             }, "_disabled": false, */
            /*             "_class": "add", */
            /*             "separator_before": false, */
            /*             "separator_after": false, */
            /*             "icon": false */
            /*         } */
            /*     } */
            /* }, */
            "plugins" : [
                "contextmenu", 
                "dnd", 
                //"search",
                "state", 
                "types", 
                "wholerow"
            ]
        });
        $(tree_id).on("delete_node.jstree", function (event, node, parent) {
            var id = node.node.id;
            var href = href_area_del + "?id=" + id;
            var post_data = {};
            directPost(href, post_data, false, true);
        });
        $(tree_id).on("create_node.jstree", function (event, node, parent, position) {
            var field_id = $(".tab-pane.active").attr("field_id");
            var href = href_area_add;
            var tree_obj = $(this).jstree(true);
            var post_data = {
                'parent' : node.parent,
                'name' : node.node.text,
                'field_id' : field_id,
            };
            var set_id_func = function (data) {
                tree_obj.set_id(node.node, data.data.id);
            }  
            directPost(href, post_data, true, true, set_id_func);
        });
        $(tree_id).on("rename_node.jstree", function (event, node, text, old) {
            var id = node.node.id;
            var href = href_area_update + "?id=" + id;
            var post_data = {
                "name" : node.node.text,
                'parent' : node.node.parent,
            };
            directPost(href, post_data, true, true);
        });
    }

    // 初始化网络图
    function init_circle(field_id) 
    {
        var svg = d3.select("svg"),
            margin = 20,
            diameter = +svg.attr("width"),
            g = svg.append("g").attr("transform", "translate(" + diameter / 2 + "," + diameter / 2 + ")");

        var color = d3.scaleLinear()
            .domain([-1, 5])
            .range(["hsl(152,80%,80%)", "hsl(228,30%,40%)"])
            .interpolate(d3.interpolateHcl);

        var pack = d3.pack()
            .size([diameter - margin, diameter - margin])
            .padding(2);

        d3.json("/frontend/area/area-circle/"+field_id, function(error, root) {
            if (error) throw error;

            root = d3.hierarchy(root)
                .sum(function(d) { return d.size; })
                .sort(function(a, b) { return b.value - a.value; });

            var focus = root,
                nodes = pack(root).descendants(),
                view;

            var circle = g.selectAll("circle")
                .data(nodes)
                .enter().append("circle")
                .attr("class", function(d) { 
                    return d.parent ? d.children ? "node" : "node node--leaf" : "node node--root"; })
                .style("fill", function(d) { return d.children ? color(d.depth) : null; })
                .on("click", function(d) { 
                    if (d3.select(this).classed("node--leaf")) {
                        var href = "/frontend/entity-skill/index/" + d.data.id;
                        window.location = href;
                    } else {
                        if (focus !== d) 
                        {
                            zoom(d); 
                            d3.event.stopPropagation();
                        }
                    }
                });

            var text = g.selectAll("text")
                .data(nodes)
                .enter().append("text")
                .attr("class", "label")
                .style("fill-opacity", function(d) { return d.parent === root ? 1 : 0; })
                .style("display", function(d) { return d.parent === root ? "inline" : "none"; })
                .text(function(d) { 
                    return d.data.name; 
                });
                    ;

            var node = g.selectAll("circle,text");

            svg
                .style("background", color(-1))
                .on("click", function() { zoom(root); });

            zoomTo([root.x, root.y, root.r * 2 + margin]);

            function zoom(d) {
                var focus0 = focus; focus = d;

                var transition = d3.transition()
                    .duration(d3.event.altKey ? 7500 : 750)
                    .tween("zoom", function(d) {
                var i = d3.interpolateZoom(view, [focus.x, focus.y, focus.r * 2 + margin]);
                return function(t) { zoomTo(i(t)); };
            });

            transition.selectAll("text")
                .filter(function(d) { return d.parent === focus || this.style.display === "inline"; })
                .style("fill-opacity", function(d) { return d.parent === focus ? 1 : 0; })
                .on("start", function(d) { if (d.parent === focus) this.style.display = "inline"; })
                .on("end", function(d) { if (d.parent !== focus) this.style.display = "none"; });
            }

            function zoomTo(v) {
                var k = diameter / v[2]; view = v;
                node.attr("transform", function(d) { return "translate(" + (d.x - v[0]) * k + "," + (d.y - v[1]) * k + ")"; });
                circle.attr("r", function(d) { return d.r * k; });
            }
        }); 
    }
</script>
