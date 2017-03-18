<?Php

namespace app\models;

use Yii;
use app\models\Task;
use app\models\Action;
use app\models\Config;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Project extends BaseActiveRecord
{
    const PROGRESS_START = 0;
    const PROGRESS_END   = 1;
    const LEVEL_PROJECT  = 0;
    const LEVEL_TASK     = 1;
    const LEVEL_ACTION   = 2;

    const TABLE_NAME    = "project";

    // 用来额外存储process的id
    public $process_id = null;

    public static $level_arr = [
        self::LEVEL_PROJECT => "project",
        self::LEVEL_TASK    => "task",
        self::LEVEL_ACTION  => "action",
    ];

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getQuery()
    {
        $project_t = self::tableName();
        $action_t  = Action::tableName();
        $task_t    = Task::tableName();
        $query     = self::find()
            ->orderBy("$project_t.ctime")
            ->leftJoin($task_t, "$project_t.id = $task_t.parent")
            ->leftJoin($action_t, "$task_t.id = $action_t.task_id");
        $query->andFilterWhere(["$project_t.del"      => Constants::SOFT_DEL_NO]);
        $query->andFilterWhere(["$project_t.user_id"  => $this->user_id]);
        $query->andFilterWhere(["$project_t.field_id" => $this->field_id]);
        $query->andFilterWhere(["$project_t.obj_id"   => $this->obj_id]);
        return $query;
    }

    public static function getMaxId()
    {
        $p_id = Project::find()->select("max(id) as max_id")->asArray()->one();
        $t_id = Task::find()->select("max(id) as max_id")->asArray()->one();
        $a_id = Action::find()->select("max(id) as max_id")->asArray()->one();
        $p_id = $p_id["max_id"];
        $t_id = $t_id["max_id"];
        $a_id = $a_id["max_id"];
        return max($p_id, $t_id, $a_id) + 1;
    }
}
