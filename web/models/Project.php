<?Php

namespace app\models;

use Yii;
use app\models\Task;
use app\models\Action;
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
            [['duration', 'progress', 'user_id', 'target_id', 'del'], 'integer'],
            [['start_date', 'ctime', 'utime'], 'safe'],
            [['text'], 'string', 'max' => 255],
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
            ->leftJoin($task_t, "$project_t.id = $task_t.parent")
            ->leftJoin($action_t, "$task_t.id = $action_t.task_id")
            ->orderBy("$project.ctime")
            ;
        $query->andFilterWhere(["$project_t.del"      => Constants::SOFT_DEL_NO]);
        $query->andFilterWhere(["$task_t.del"         => Constants::SOFT_DEL_NO]);
        $query->andFilterWhere(["$project_t.user_id"  => $this->user_id]);
        return $query;
    }

    public function getTargetQuery()
    {
        $project_t = self::tableName();
        $target_t  = Target::tableName();
        $query     = self::find()
            ->select("$project_t.*, $target_t.field_id, $target_t.priority_id, $target_t.id target_id")
            ->leftJoin($target_t, "$target_t.id = $project_t.target_id")
            ->andFilterWhere(["$project_t.del"      => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$project_t.user_id"  => $this->user_id])
            ;
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

    public static function getEntityDictByFieldIndex($obj_id_arr, $user_id)
    {
        $ret = [];
        $field_obj_model = new FieldObj;
        foreach ($obj_id_arr as $field_id => $obj_ids) {
            $dict = [];
            $entity_t = $field_obj_model->getEntityTableName($field_id);
            $field_obj_t      = FieldObj::tableName();
            $field_obj_link_t = FieldObjEntityLink::tableName();
            $entity_list      = FieldObjEntityLink::find()
                ->select("
                $field_obj_t.field_id, $field_obj_t.id as obj_id, 
                $field_obj_link_t.entity_id, $field_obj_link_t.id, 
                $entity_t.name as entity_name")
                ->leftJoin($field_obj_t, "$field_obj_t.id = $field_obj_link_t.obj_id")
                ->leftJoin($entity_t, "$entity_t.id = $field_obj_link_t.entity_id")
                ->andWhere(["$field_obj_t.field_id" => $field_id])
                ->andWhere(["$field_obj_t.id" => $obj_ids])
                ->andWhere(["$field_obj_t.user_id" => $user_id])
                ->asArray()->all();
            $result = [];
            foreach ($entity_list as $one) {
                $result[$one["obj_id"]][] = [
                    "key"   => $one["entity_id"],
                    "label" => $one["entity_name"]
                ]; 
            }

            $ret[$field_id] = $result;
        }
        return $ret;
    }
}
