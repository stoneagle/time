<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Target;
use app\models\Area;
use app\models\Project;
use app\models\Constants;
use app\models\Error;
use app\models\GanttTasks;
use yii\filters\VerbFilter;

class ProjectController extends BaseController
{

    public function actionIndex()
    {
        $target_list = Target::find()
            ->select("name, id")
            ->andWhere([ "user_id" => $this->user_obj->id ])
            ->asArray()->all();
        $target_dict = \ArrDict::getDictByType(Constants::DICT_TYPE_DHX, $target_list);
        return $this->render('index', [
            "targetDict" => json_encode($target_dict),
        ]);
    }

    public function actionFinish($id)
    {
        $model           = $this->findModel($id, GanttTasks::class);
        $model->progress = Project::PROGRESS_END;
        $model->modelValidSave();
        $code = Error::ERR_OK;
        return $this->packageJson(['id' => $model->id], $code, Error::msg($code));
    }
}
