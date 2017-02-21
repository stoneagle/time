<?php
use yii\helpers\Html;
use app\assets\AppAsset;

$this->title                   = '项目管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerJsFile('@web/js/lib/dhtmlxgantt.js', ['position'=>$this::POS_HEAD]); 
?>

<div>
    <h1><?= Html::encode($this->title); ?></h1>
    <div id="gantt_here" style='width:100%; height:800px;'></div>
    <script type="text/javascript">
        gantt.init("gantt_here");
        gantt.parse("/frontend/project/data");

        /* gantt.parse(); */

        var dp = new gantt.dataProcessor("/frontend/project");
        dp.init(gantt);
        dp.setTransactionMode("REST");
        /* gantt.init("gantt_here"); */
        /* var tasks = { */
        /*     data:[ */
        /*         {id:1, text:"Project #1",start_date:"01-04-2013", duration:11, */
        /*         progress: 0.6, open: true}, */
        /*         {id:2, text:"Task #1",   start_date:"03-04-2013", duration:5, */ 
        /*         progress: 1,   open: true, parent:1}, */
        /*         {id:3, text:"Task #2",   start_date:"02-04-2013", duration:7, */ 
        /*         progress: 0.5, open: true, parent:1}, */
        /*         {id:4, text:"Task #2.1", start_date:"03-04-2013", duration:2, */ 
        /*         progress: 1,   open: true, parent:3}, */
        /*         {id:5, text:"Task #2.2", start_date:"04-04-2013", duration:3, */ 
        /*         progress: 0.8, open: true, parent:3}, */
        /*         {id:6, text:"Task #2.3", start_date:"05-04-2013", duration:4, */ 
        /*         progress: 0.2, open: true, parent:3} */
        /*     ], */
        /*     links:[ */
        /*         {id:1, source:1, target:2, type:"1"}, */
        /*         {id:2, source:1, target:3, type:"1"}, */
        /*         {id:3, source:3, target:4, type:"1"}, */
        /*         {id:4, source:4, target:5, type:"0"}, */
        /*         {id:5, source:5, target:6, type:"0"} */
        /*     ] */
        /* }; */
        /* gantt.parse (tasks); */
    </script>
</div>

