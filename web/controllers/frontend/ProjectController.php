<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Project;
use app\models\Constants;
use app\models\Error;
use app\models\GanttTasks;
use yii\filters\VerbFilter;

class ProjectController extends BaseController
{

    public function actionIndex()
    {
        $field_raw_dict         = Config::$field_arr;
        $field_dhtml_dict = [];
        foreach ($field_raw_dict as $index => $one) {
            $field_dhtml_dict[] = [
                "key" => $index,
                "label" => $one,
            ];
        }
        $priority_dict      = Config::$priority_dhtml_arr;
        $type_raw           = Config::getTypeWithParentDict(Config::TYPE_ACTION, "dhtml");

        $project_list   = Project::find()
            ->select("field_id, obj_id")
            ->andWhere(["del" => Constants::SOFT_DEL_NO])
            ->asArray()->all();
        $obj_arr = [];
        foreach ($project_list as $one) {
            if (!empty($one["obj_id"])) {
                $obj_arr[$one["field_id"]][] = $one["obj_id"];
            }
        }
        // todo，做成接口形式获取
        $entity_field_dict = Project::getEntityDictByFieldIndex($obj_arr, $this->user_obj->id);

        return $this->render('index', [
            "fieldDict"       => json_encode($field_dhtml_dict),
            "priorityDict"    => json_encode($priority_dict),
            "typeDict"        => json_encode($type_raw),
            "entityFieldDict" => json_encode($entity_field_dict),
        ]);
    }

    public function actionFinish($id)
    {
        $model = $this->findModel($id, GanttTasks::class);
        $model->progress = Project::PROGRESS_END;
        $model->modelValidSave();
        $code = Error::ERR_OK;
        return $this->packageJson(['id' => $model->id], $code, Error::msg($code));
    }
}
