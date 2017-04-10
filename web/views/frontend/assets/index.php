<?php
use yii\helpers\Html;
use app\models\BusinessAssets;
use app\models\Project;
use app\models\Config;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use \kartik\date\DatePicker;
use kartik\grid\GridView;

$this->title                   = '资产管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
/* $this->registerCssFile('@web/css/lib/dhtmlx/dhtmlx.css',['depends'=>['app\assets\AppAsset']]); */
/* $this->registerCssFile('@web/css/lib/dhtmlx/dhtmlxlist_dhx_terrace.css',['depends'=>['app\assets\AppAsset']]); */
$this->registerCssFile('@web/css/lib/select2.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/gridstack.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/jquery-ui.css',['depends'=>['app\assets\AppAsset']]);

/* $this->registerJsFile('@web/js/lib/dhtmlx/dhtmlx.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]); */
/* $this->registerJsFile('@web/js/lib/dhtmlx/dhtmlxlist.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]); */
$this->registerJsFile('@web/js/lib/select2.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/echarts.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/lodash.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/jquery-ui.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/gridstack.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/gridstack.jQueryUI.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/frontend/assets/index.js',['depends'=>['app\assets\AppAsset']]);
?>
<style type="text/css">
    .assets-horizontal dt {
        float: left;
        width: 80px;
        overflow: hidden;
        clear: left;
        text-align: right;
        text-overflow: ellipsis;
        white-space: nowrap;
        //margin-left: 80px;
    }
    .assets-horizontal dd {
        margin-left: 100px;
    }
    .assets-panel {
        margin-bottom:0px;
    }
    .assets-container {
        padding-left:0px;
        padding-right:0px;
    }

    #grid1 {
        background: lightgoldenrodyellow;
    }

    #grid2 {
        background: lightcyan;
    }

    .grid-stack-item-content {
        color: #2c3e50;
        text-align: center;
        background-color: #18bc9c;
    }

    #grid2 .grid-stack-item-content {
        background-color: #9caabc;
    }

    .grid-stack-item-removing {
        opacity: 0.5;
    }

    .trash {
        height: 150px;
        margin-bottom: 20px;
        background: rgba(255, 0, 0, 0.1) center center url(data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjY0cHgiIGhlaWdodD0iNjRweCIgdmlld0JveD0iMCAwIDQzOC41MjkgNDM4LjUyOSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDM4LjUyOSA0MzguNTI5OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQxNy42ODksNzUuNjU0Yy0xLjcxMS0xLjcwOS0zLjkwMS0yLjU2OC02LjU2My0yLjU2OGgtODguMjI0TDMwMi45MTcsMjUuNDFjLTIuODU0LTcuMDQ0LTcuOTk0LTEzLjA0LTE1LjQxMy0xNy45ODkgICAgQzI4MC4wNzgsMi40NzMsMjcyLjU1NiwwLDI2NC45NDUsMGgtOTEuMzYzYy03LjYxMSwwLTE1LjEzMSwyLjQ3My0yMi41NTQsNy40MjFjLTcuNDI0LDQuOTQ5LTEyLjU2MywxMC45NDQtMTUuNDE5LDE3Ljk4OSAgICBsLTE5Ljk4NSw0Ny42NzZoLTg4LjIyYy0yLjY2NywwLTQuODUzLDAuODU5LTYuNTY3LDIuNTY4Yy0xLjcwOSwxLjcxMy0yLjU2OCwzLjkwMy0yLjU2OCw2LjU2N3YxOC4yNzQgICAgYzAsMi42NjQsMC44NTUsNC44NTQsMi41NjgsNi41NjRjMS43MTQsMS43MTIsMy45MDQsMi41NjgsNi41NjcsMi41NjhoMjcuNDA2djI3MS44YzAsMTUuODAzLDQuNDczLDI5LjI2NiwxMy40MTgsNDAuMzk4ICAgIGM4Ljk0NywxMS4xMzksMTkuNzAxLDE2LjcwMywzMi4yNjQsMTYuNzAzaDIzNy41NDJjMTIuNTY2LDAsMjMuMzE5LTUuNzU2LDMyLjI2NS0xNy4yNjhjOC45NDUtMTEuNTIsMTMuNDE1LTI1LjE3NCwxMy40MTUtNDAuOTcxICAgIFYxMDkuNjI3aDI3LjQxMWMyLjY2MiwwLDQuODUzLTAuODU2LDYuNTYzLTIuNTY4YzEuNzA4LTEuNzA5LDIuNTctMy45LDIuNTctNi41NjRWODIuMjIxICAgIEM0MjAuMjYsNzkuNTU3LDQxOS4zOTcsNzcuMzY3LDQxNy42ODksNzUuNjU0eiBNMTY5LjMwMSwzOS42NzhjMS4zMzEtMS43MTIsMi45NS0yLjc2Miw0Ljg1My0zLjE0aDkwLjUwNCAgICBjMS45MDMsMC4zODEsMy41MjUsMS40Myw0Ljg1NCwzLjE0bDEzLjcwOSwzMy40MDRIMTU1LjMxMUwxNjkuMzAxLDM5LjY3OHogTTM0Ny4xNzMsMzgwLjI5MWMwLDQuMTg2LTAuNjY0LDguMDQyLTEuOTk5LDExLjU2MSAgICBjLTEuMzM0LDMuNTE4LTIuNzE3LDYuMDg4LTQuMTQxLDcuNzA2Yy0xLjQzMSwxLjYyMi0yLjQyMywyLjQyNy0yLjk5OCwyLjQyN0gxMDAuNDkzYy0wLjU3MSwwLTEuNTY1LTAuODA1LTIuOTk2LTIuNDI3ICAgIGMtMS40MjktMS42MTgtMi44MS00LjE4OC00LjE0My03LjcwNmMtMS4zMzEtMy41MTktMS45OTctNy4zNzktMS45OTctMTEuNTYxVjEwOS42MjdoMjU1LjgxNVYzODAuMjkxeiIgZmlsbD0iI2ZmOWNhZSIvPgoJCTxwYXRoIGQ9Ik0xMzcuMDQsMzQ3LjE3MmgxOC4yNzFjMi42NjcsMCw0Ljg1OC0wLjg1NSw2LjU2Ny0yLjU2N2MxLjcwOS0xLjcxOCwyLjU2OC0zLjkwMSwyLjU2OC02LjU3VjE3My41ODEgICAgYzAtMi42NjMtMC44NTktNC44NTMtMi41NjgtNi41NjdjLTEuNzE0LTEuNzA5LTMuODk5LTIuNTY1LTYuNTY3LTIuNTY1SDEzNy4wNGMtMi42NjcsMC00Ljg1NCwwLjg1NS02LjU2NywyLjU2NSAgICBjLTEuNzExLDEuNzE0LTIuNTY4LDMuOTA0LTIuNTY4LDYuNTY3djE2NC40NTRjMCwyLjY2OSwwLjg1NCw0Ljg1MywyLjU2OCw2LjU3QzEzMi4xODYsMzQ2LjMxNiwxMzQuMzczLDM0Ny4xNzIsMTM3LjA0LDM0Ny4xNzJ6IiBmaWxsPSIjZmY5Y2FlIi8+CgkJPHBhdGggZD0iTTIxMC4xMjksMzQ3LjE3MmgxOC4yNzFjMi42NjYsMCw0Ljg1Ni0wLjg1NSw2LjU2NC0yLjU2N2MxLjcxOC0xLjcxOCwyLjU2OS0zLjkwMSwyLjU2OS02LjU3VjE3My41ODEgICAgYzAtMi42NjMtMC44NTItNC44NTMtMi41NjktNi41NjdjLTEuNzA4LTEuNzA5LTMuODk4LTIuNTY1LTYuNTY0LTIuNTY1aC0xOC4yNzFjLTIuNjY0LDAtNC44NTQsMC44NTUtNi41NjcsMi41NjUgICAgYy0xLjcxNCwxLjcxNC0yLjU2OCwzLjkwNC0yLjU2OCw2LjU2N3YxNjQuNDU0YzAsMi42NjksMC44NTQsNC44NTMsMi41NjgsNi41N0MyMDUuMjc0LDM0Ni4zMTYsMjA3LjQ2NSwzNDcuMTcyLDIxMC4xMjksMzQ3LjE3MnogICAgIiBmaWxsPSIjZmY5Y2FlIi8+CgkJPHBhdGggZD0iTTI4My4yMiwzNDcuMTcyaDE4LjI2OGMyLjY2OSwwLDQuODU5LTAuODU1LDYuNTctMi41NjdjMS43MTEtMS43MTgsMi41NjItMy45MDEsMi41NjItNi41N1YxNzMuNTgxICAgIGMwLTIuNjYzLTAuODUyLTQuODUzLTIuNTYyLTYuNTY3Yy0xLjcxMS0xLjcwOS0zLjkwMS0yLjU2NS02LjU3LTIuNTY1SDI4My4yMmMtMi42NywwLTQuODUzLDAuODU1LTYuNTcxLDIuNTY1ICAgIGMtMS43MTEsMS43MTQtMi41NjYsMy45MDQtMi41NjYsNi41Njd2MTY0LjQ1NGMwLDIuNjY5LDAuODU1LDQuODUzLDIuNTY2LDYuNTdDMjc4LjM2NywzNDYuMzE2LDI4MC41NSwzNDcuMTcyLDI4My4yMiwzNDcuMTcyeiIgZmlsbD0iI2ZmOWNhZSIvPgoJPC9nPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=) no-repeat;
    }

    .sidebar {
        background: rgba(0, 255, 0, 0.1);
        height: 150px;
        padding: 25px 0;
        text-align: center;
    }

    .sidebar .grid-stack-item {
        width: 200px;
        height: 100px;
        border: 2px dashed green;
        text-align: center;
        line-height: 100px;
        z-index: 10;
        background: rgba(0, 255, 0, 0.1);
        cursor: default;
        display: inline-block;
    }

    .sidebar .grid-stack-item .grid-stack-item-content {
        background: none;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="gridbox_task col-md-4 panel panel-default" style="height:1024px;" >
        <div id="time_cookie" style="width:auto;height:400px;"></div>
        <div id="time_sum" style="width:auto;height:200px;"></div>
    </div>
    <div id="assets_dashboard" class="dhx_cal_container col-md-8 panel panel-default" style=' height:1024px; '>
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                        <input  id="grid_add_area" type="button" class="grid-stack-item" value="新增"></input>
                </div>
            </div>
            <div class="col-md-9">
                <div class="trash">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="grid-stack grid-stack-6" id="grid1" style="width:auto;" >
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="assets_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="assets_form" method="post" action="" class="form-horizontal" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="assets_save_title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assets_name" class="col-sm-3 control-label">资产项目名称:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="assets_name" name="name"></input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="assets_priority" class="col-sm-3 control-label">优先级:</label>
                        <div class="col-sm-9">
                            <?php
                                echo Select2::widget([
                                    'name' => 'priority_id',
                                    'data' => $priority_dict,
                                    'options' => [
                                        'placeholder' => '请选择优先级',
                                        'multiple' =>  false
                                    ],
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="assets_entity" class="col-sm-3 control-label">资产相关实体:</label>
                        <div class="col-sm-9">
                            <?php
                                echo Select2::widget([
                                    'name' => 'entity_ids',
                                    'data' => $entity_dict,
                                    'options' => [
                                        'placeholder' => '请选择对应实体',
                                        'multiple' => true 
                                    ],
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="head_count" class="col-sm-3 control-label">初始人力资源(人数):</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="head_count" min=0 value=0 >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="value" class="col-sm-3 control-label">初始价值(人民币/元):</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="value" min=0 value=0 >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="income_flow" class="col-sm-3 control-label">初始现金流(人民币/k):</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="income_flow" min=0 value=0 >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="time_span" class="col-sm-3 control-label">结算间隔:</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="time_span" min=0 value=0 >
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="assets_position" name="position">
                    <input type="hidden" class="form-control" id="assets_id" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary" id="tree_save_submit" >提交</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="assets_info" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            子资产列表 
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <table id="assets_sub_table" class="table table-bordered">
                            </table>
                            <div class="modal-footer">
                                <button id="add_assets_sub" type="button" class="btn btn-success" >新增子资产</button>
                                <button id="rm_field_obj" type="button" class="btn btn-danger" >删除项目</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // 资产块图
    var priority_dict    = <?php echo $priority_dict;?>;

    var general_width = 3;
    var general_height = 3;

    var chunk_num      = <?php echo $init_chunk_num;?>;
    var last_one_id    = <?php echo $last_one_id;?>;
    var init_flag      = false;
    var options        = {
        width: 12,
        float: false,
        removable: '.trash',
        removeTimeout: 100,
        acceptWidgets: '.grid-stack-item'
    };
    // 方块列表
    var items = <?php echo $chunk_list;?>;
    var items_dict = <?php echo $chunk_dict;?>;
    $('#grid1').gridstack(options);

    $('.grid-stack').each(function () {
        var grid = $(this).data('gridstack');
        _.each(items, function (node) {
            var assets_info = items_dict[node.id];
            var assets_detail = 
                '<dl class="assets-horizontal">' + 
                '<dt>资产名称</dt><dd>' + assets_info.project_name + '</dd>' + 
                '<dt>价值</dt><dd>' + assets_info.value + '</dd>' + 
                '<dt>人力资源</dt><dd>' + assets_info.head_count + '</dd>' + 
                '<dt>现金流</dt><dd>' + assets_info.income_flow + '/' + assets_info.time_span + '天</dd>' + 
                '</dl>'
            var assets_one = '<div><div class="grid-stack-item-content" ><div class="container-fluid assets-container" style="height:100%" ><div class="panel panel-default assets-panel" style="height:100%;text-align:left;" id="assets-panel-'+node.id+'" assets_id="'+ node.id +'" >' + assets_detail + '</div></div></div><div/>';
            grid.addWidget($(assets_one),
                node.x, node.y, node.width, node.height, false, 1, 5, 1, 5, node.id);
        }, this);
        init_flag = true;
    });

    /* $('.sidebar .grid-stack-item').draggable({ */
    /*     revert: 'invalid', */
    /*     handle: '.grid-stack-item-content', */
    /*     scroll: false, */
    /*     appendTo: 'body' */
    /* }); */

    $("#grid_add_area").dblclick(function(){
        if (chunk_num == 0) {
            $('#assets_position').val("0,0,3,3")
        } else {
            var grid = $("#grid1").data('gridstack');
            var x_pos = 0;
            var y_pos = 0;
            for (var y = 0; y <= 12; y++) {
                for (var x = 0; x<= 12; x++) {
                    var result = grid.isAreaEmpty(x, y, general_width, general_height)
                    if (result) {
                        x_pos = x;
                        y_pos = y;
                        break;
                    }
                }
                if ( x_pos != 0 || y_pos != 0 ) {
                    break;
                }
            }
            var position = x_pos + "," + y_pos + "," + general_width + ","+ general_height;
            $('#assets_position').val(position)
        }
        $('#assets_save_title').html("新增资产");
        $('#assets_save').modal('show')
    });

    $('#grid1').on('added', function(event, items) {
        // 设置class的颜色
        // 设置items的信息
        for (var i = 0; i < items.length; i++) {
        }
    });

    $('#grid1').on('change', function(event, items) {
        //根据items的数目，判断新增与删除
        if (init_flag == true) {
            if (items) {
                var item_length = items.length
            } else {
                var item_length = 0;
            }
            if (item_length < chunk_num) {
                console.log(items);
                // 对比找出被删除的那一项
                if (last_one_id != 0) {
                    var href = "/frontend/assets/del/" + last_one_id;
                    var post_data = {};
                    directPost(href, post_data, false, true)
                }
                // 删除区域方块
                chunk_num = item_length;
                if (item_length == 1) {
                    last_one_id == items[0].id;
                }
            }
        }
    });

    // 表单提交操作
    $('#assets_form').on('submit', function(e){
        e.preventDefault();
        var post_data = $(this).serializeArray();
        var assets_id = $("#assets_id").val();
        var href = "";
        if (assets_id == 0) {
            href = "/frontend/assets/add";
        } else {
            href = "/frontend/assets/update/" + assets_id;
        }

        $.ajax({
            url: href,
            data: post_data,
            dataType: 'text',
            type: 'POST',
            success: function(result) {
                var data = eval('(' + result + ')');  
                if (data.error === 0) {
                    $('#assets_save').modal('hide')
                    if (assets_id == 0) {
                        var grid = $("#grid1").data('gridstack');
                        grid.addWidget($('<div><div class="grid-stack-item-content" /><div/>'),
                            data.data.x, data.data.y, data.data.width, data.data.height, true, general_width, 5, general_height, 5, data.data.id);
                        chunk_num += 1;
                    }
                    resetForm("assets_form");
                } else {
                    swal("操作失败!", data.message, "error");
                }
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    });

    // assets-panel管理
    $(".assets-panel").dblclick(function(){
        var id = $(this).attr("assets_id");
        var href = "/frontend/assets-sub/one-assets-sub?id=" + id;
        $("#add_assets_sub").attr("assets_id", id);
        $("#rm_field_obj").attr("assets_id", id);
        $.ajax({
            url: href,
            data: {},
            dataType: 'text',
            type: 'POST',
            success: function(result) {
                var data = eval('(' + result + ')');  
                if (data.error === 0) {
                    table_html = "";
                    var table_html = "<tr><th>子资产名称</th><th>描述</th><th>实体名称</th><th>创建时间</th><th>操作</th></tr>"; 
                    for (var arr_index in data.data) {
                        var one = data.data[arr_index];
                        table_html += "<tr><td>" 
                            + one.name + "</td><td>" 
                            + one.desc + "</td><td>" 
                            + one.entity_name + "</td><td>" 
                            + one.ctime + '</td>'
                            + '<td>'
                            + '<button type="button" class="btn btn-success btn-sm assets_sub_edit" assets_sub_id=' +one.id+ '>修改</button>'
                            + '<button type="button" class="btn btn-danger btn-sm assets_sub_del" assets_sub_id=' +one.id+ '>删除</button>'
                            + '</td></tr>'
                    }
                    $('#assets_sub_table').html(table_html);   
                    $('#assets_info').modal('show')
                    init_assets_sub_btn();
                } else {
                    swal("操作失败!", data.message, "error");
                }
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    });

    $("#add_assets_sub").on("click", function(e) {
        e.preventDefault();
        var assets_id = $(this).attr("assets_id");
        jump_href = "/frontend/assets-sub/create?obj_id=" + assets_id;
        window.location.href = jump_href;
    });

    $("#rm_field_obj").on("click", function(e) {
        e.preventDefault();
        var field_obj_id = $(this).attr("assets_id");
        var href = "/frontend/assets/del/" + field_obj_id;
        var post_data = {};
        directPost(href, post_data, false, true)
    });

    function init_assets_sub_btn() {
        $('.assets_sub_del').on('click', function(e){
            e.preventDefault();
            var id = $(this).attr("assets_sub_id");
            var href      = "/frontend/assets-sub/delete";
            var text_info = "是否删除该子资产";
            var hint      = "已执行的子资产不会被彻底删除";
            var post_data = {
                "ids" : id
            };
            checkPost(text_info, hint, href, post_data);
        }); 
        $('.assets_sub_edit').on('click', function(e){
            e.preventDefault();
            var id = $(this).attr("assets_sub_id");
            jump_href = "/frontend/assets-sub/update?id=" + id;
            window.location.href = jump_href;
        }); 
    }

    // 时间饼图
    /* var chart = echarts.init(document.getElementById('time_cookie')); */
    /* chart.title = '时间规划表'; */
    /* chart.setOption({ */
    /*     tooltip: { */
    /*         trigger: 'item', */
    /*         formatter: "{a} <br/>{b}: {c} ({d}%)" */
    /*     }, */
    /*     legend: { */
    /*         orient: 'vertical', */
    /*         x: 'left', */
    /*         data:[] */
    /*     }, */
    /*     series: [ */
    /*         { */
    /*             name:'目标任务', */
    /*             type:'pie', */
    /*             selectedMode: 'single', */
    /*             radius: ['30%', '50%'], */

    /*             label: { */
    /*                 normal: { */
    /*                     position: 'inner' */
    /*                 } */
    /*             }, */
    /*             labelLine: { */
    /*                 normal: { */
    /*                     show: false */
    /*                 } */
    /*             }, */
    /*             data:[] */
    /*         }, */
    /*         { */
    /*             name:'预估时间', */
    /*             type:'pie', */
    /*             radius: ['55%', '70%'], */
    /*             data:[] */
    /*         } */
    /*     ] */
    /* }); */

    /* function setChartData(year, week) { */
    /*     $.get('/frontend/plan-api/chart/' + year + '/' + week).done(function (data) { */
    /*         chart.setOption({ */
    /*             legend: { */
    /*                 data: data.field_time_x */
    /*             }, */
    /*             series: [ */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '预估时间', */
    /*                     data: data.week_time */
    /*                 }, */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '目标任务', */
    /*                     data: data.field_time */
    /*                 }, */
    /*             ] */
    /*         }); */
    /*         sum_chart.setOption({ */
    /*             series: [ */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '计划分配', */
    /*                     data: [ data.week_time_sum ], */
    /*                 }, */
    /*                 { */
    /*                     // 根据名字对应到相应的系列 */
    /*                     name: '任务预估', */
    /*                     data: [ data.field_time_sum ], */
    /*                 }, */
    /*             ] */
    /*         }); */
    /*     }); */
    /* } */

    /* // 总和柱状图 */
    /* var sum_chart = echarts.init(document.getElementById('time_sum')); */
    /* sum_chart.title = "时间总和"; */
    /* sum_chart.setOption({ */
    /*     title: { */
    /*         text: '时间总和', */
    /*         subtext: '' */
    /*     }, */
    /*     tooltip: { */
    /*         trigger: 'axis', */
    /*         axisPointer: { */
    /*             type: 'shadow' */
    /*         } */
    /*     }, */
    /*     legend: { */
    /*         data: ['计划分配', '任务预估'] */
    /*     }, */
    /*     grid: { */
    /*         left: '3%', */
    /*         right: '4%', */
    /*         bottom: '3%', */
    /*         containLabel: true */
    /*     }, */
    /*     xAxis: { */
    /*         type: 'value', */
    /*         boundaryGap: [0, 0.01] */
    /*     }, */
    /*     yAxis: { */
    /*         type: 'category', */
    /*         data: ['时间'] */
    /*     }, */
    /*     series: [ */
    /*         { */
    /*             name: '计划分配', */
    /*             type: 'bar', */
    /*             data: [] */
    /*         }, */
    /*         { */
    /*             name: '任务预估', */
    /*             type: 'bar', */
    /*             data: [] */
    /*         } */
    /*     ] */
    /* }); */

    // 左下方执行与完成的list列表
    /* execList = new dhtmlXList({ */
    /*     container:"exec_list", */
    /*     type:{ */
    /*         template:"所属项目:#task_name#<br/>行动名称:(#text#)<br/>计划时间:#plan_time#", */
    /*         height:75, */
    /*     }, */
    /*     drag:true */
    /* }); */
    /* endList = new dhtmlXList({ */
    /*     container:"end_list", */
    /*     type:{ */
    /*         template:"所属项目:#task_name#<br/>行动名称:(#text#)<br/>计划时间:#plan_time#", */
    /*     } */
    /* }); */
    /* var exec_load_path = "/frontend/action-api/list/"; */
    /* execList.load(exec_load_path, "json"); */
</script>
