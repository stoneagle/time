<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\Error;
use app\models\GanttTasks;
use yii\filters\VerbFilter;

class ProjectController extends BaseController
{

    public function actionIndex()
    {
        $config_model       = new Config;
        $config_model->type = Config::TYPE_FIELD;
        $field_dict         = $config_model->getTypeDict(true);
        $config_model->type = Config::TYPE_PRIORITY;
        $priority_dict      = $config_model->getTypeDict(true);
        $type_raw           = Config::getTypeWithParentDict(Config::TYPE_ACTION, "dhtml");

        return $this->render('index', [
            "fieldDict"    => json_encode($field_dict),
            "priorityDict" => json_encode($priority_dict),
            "typeDict"     => json_encode($type_raw),
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
