<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Target extends BaseActiveRecord 
{
    const TABLE_NAME    = "target";

    CONST PRIORITY_IMPORTANT_URGENT = 1;
    CONST PRIORITY_IMPORTANT_UNURG  = 2;
    CONST PRIORITY_UNIM_URGENT      = 3;
    CONST PRIORITY_UNIM_UNURG       = 4;

    public static $priority_arr = [
        self::PRIORITY_IMPORTANT_URGENT => "重要且紧急",
        self::PRIORITY_IMPORTANT_UNURG  => "重要不紧急",
        self::PRIORITY_UNIM_URGENT      => "紧急不重要",
        self::PRIORITY_UNIM_UNURG       => "不重要且不紧急",
    ];

    public $entity_ids;

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['priority_id', 'field_id', 'user_id'], 'integer'],
            [['entity_ids', 'ctime', 'utime'], 'safe'],
            [['name', 'desc'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => '目标名称',
            'field_id'    => '所属范畴',
            'user_id'     => '用户',
            'priority_id' => '优先级',
            'entity_ids'  => '相关实体列表',
            'ctime'       => '创建时间',
            'utime'       => '更新时间',
        ];
    }

    public function getQuery()
    {
        $target_t = self::tableName();
        $project_t   = Project::tableName();

        $query     = self::find()
            ->select("$target_t.*, $project_t.id as project_id")
            ->leftJoin($project_t, "$project_t.target_id = $target_t.id")
            ->andFilterWhere(["$target_t.field_id"    => $this->field_id])
            ->andFilterWhere(["$target_t.priority_id" => $this->priority_id])
            ->andFilterWhere(["$target_t.user_id"     => $this->user_id]);
        return $query;
    }

    public function checkDeleteStatus()
    {
        $result = $this->getQuery()
            ->asArray()->one();
        $ret = true;
        if (!is_null($result["project_id"])) {
            $ret = false;
        }
        return $ret;
    }

    public function getDictByObjId($field_id, $target_id)
    {
        $entity_t = $this->getEntityTableName($field_id);
        $field_obj_link_t = FieldObjEntityLink::tableName();
        $query = FieldObjEntityLink::find()
            ->select("$entity_t.id, $entity_t.name")
            ->leftJoin($entity_t, "$field_obj_link_t.entity_id = $entity_t.id")
            ->andWhere(["$field_obj_link_t.target_id" => $target_id]);
        $result = $query->asArray()->all();
        return ArrayHelper::map($result, "id", "name");
    }

    public function addTargetAndLink()
    {
        $this->modelValidSave();
        foreach ($this->entity_ids as $entity_id) {
            $target_entity_link_model            = new TargetEntityLink;
            $target_entity_link_model->entity_id = $entity_id;
            $target_entity_link_model->target_id = $this->id;
            $target_entity_link_model->modelValidSave();
        }
        return true;
    }

    public function updateTargetAndLink()
    {
        // 删除原有关联
        TargetEntityLink::deleteAll(
            "target_id = :target_id",
            [":target_id" => $this->id]
        );
        $this->addTargetAndLink();
        return true;
    }

    public function rmTargetAndLink()
    {
        // 删除关联entity
        TargetEntityLink::deleteAll(
            "target_id = :target_id",
            [":target_id" => $this->id]
        );
        // 删除自身
        $result = $this->delete();
        if (!$result) {
            throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
        }
        return true;
    }

    public function getTargetEntityDict($type = Constants::DICT_TYPE_ARR)
    {
        $target_t = self::tableName();
        $link_t = TargetEntityLink::tableName();
        $entity_ids_list = self::find()
            ->select("$target_t.name, $target_t.id as target_id, GROUP_CONCAT($link_t.entity_id) as entity_ids, $target_t.field_id")
            ->leftJoin($link_t, "$link_t.target_id = $target_t.id")
            ->andWhere(["$target_t.user_id" => $this->user_id])
            ->groupBy("$target_t.id")
            ->asArray()->all();
        $field_entity_arr = [];
        $target_entity_arr = [];
        foreach ($entity_ids_list as $one) {
            if (!isset($field_entity_arr[$one["field_id"]])) {
                $field_entity_arr[$one["field_id"]] = [];
            }
            if (!isset($target_entity_arr[$one["field_id"]][$one["target_id"]])) {
                $target_entity_arr[$one["field_id"]][$one["target_id"]] = [];
            }
            $field_entity_arr[$one["field_id"]] = array_merge($field_entity_arr[$one["field_id"]], array_unique(explode(',', $one["entity_ids"])));
            $target_entity_arr[$one["field_id"]][$one['target_id']] = array_merge($target_entity_arr[$one["field_id"]][$one["target_id"]], array_unique(explode(',', $one["entity_ids"])));
        }
        $field_entity_dict = [];
        $entity_model = new EntityBase;
        foreach ($field_entity_arr as $field_id => $entity_arr) {
            $field_entity_dict[$field_id] = $entity_model->getEntityDict($field_id, Constants::DICT_TYPE_MAP, array_unique($entity_arr), false);
        }

        $result = [];

        foreach ($target_entity_arr as $field_id => $one) {
            foreach ($one as $target_id => $entity_ids_arr) {
                $tmp_list = [];
                foreach ($entity_ids_arr as $entity_id) {
                    $tmp_list[] = [
                        "id"   => $entity_id,
                        "name" => $field_entity_dict[$field_id][$entity_id],
                    ];
                } 
                $result[$target_id] = \ArrDict::getDictByType($type, $tmp_list);
            }
        }
        return $result;
    }
}
