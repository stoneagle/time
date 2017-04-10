<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class FieldObj extends BaseActiveRecord 
{
    const TABLE_NAME    = "field_obj";
    public $project_name;
    public $priority_id;
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
            [['project_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'project_name' => '项目名称',
            'priority_id'  => '优先级',
            'field_id'     => '领域',
            'user_id'      => '用户ID',
            'entity_ids'   => '相关实体ID列表',
            'ctime'        => '创建时间',
            'utime'        => '更新时间',
        ];
    }

    public function getQuery()
    {
        $field_obj_t = self::tableName();
        $project_t   = Project::tableName();

        $query     = self::find()
            ->leftJoin($project_t, "$project_t.obj_id = $field_obj_t.id")
            ->andFilterWhere(["$project_t.field_id" => $this->field_id])
            ->andFilterWhere(["$project_t.text"     => $this->project_name])
            ->andFilterWhere(["$field_obj_t.user_id"   => $this->user_id]);

        $entity_t    = $this->getEntityTableName($this->field_id);
        //todo 增加entity的显示
        /* if (!empty($entity_t)) { */
        /*     $link_t      = FieldObjEntityLink::tableName(); */
        /*     $query */
        /*         ->leftJoin($entity_t, "$entity_t.id = $link_t.entity_id") */
        /*         ->leftJoin($link_t, "$link_t.obj_id = $field_obj_t.id"); */
        /*     $select_prefix = ""; */
        /* } */
        switch ($this->field_id) {
            case Config::FIELD_ASSET :
                $assets_info_t = AssetsInfo::tableName();
                $select = "$field_obj_t.*, $project_t.text as project_name, $project_t.id project_id, 
                    $assets_info_t.position,
                    $assets_info_t.value,
                    $assets_info_t.head_count,
                    $assets_info_t.income_flow,
                    $assets_info_t.time_span
                    ";
                $query->select($select);
                $query->leftJoin($assets_info_t, "$assets_info_t.obj_id = $field_obj_t.id");
                break;
            default :
                $select = "$field_obj_t.*, $project_t.text project_name, $project_t.id project_id";
                $query->select($select);
                break;
        }
        return $query;
    }

    public function checkDeleteStatus()
    {
        $project_t = Project::tableName();
        $task_t    = Task::tableName();
        $link_t    = self::tableName();
        $result    = self::find()
            ->select("$link_t.*, $task_t.id as task_id")
            ->leftJoin($project_t, "$project_t.obj_id = $link_t.id")
            ->leftJoin($task_t, "$task_t.parent = $project_t.id")
            ->andWhere(["$link_t.id" => $this->id])
            ->andWhere(["$project_t.field_id" => $this->field_id])
            ->asArray()->one();
        $ret = true;
        if (!is_null($result["task_id"])) {
            $ret = false;
        }
        return $ret;
    }

    public function getEntityDict($field_id, $type = BaseEntity::DICT_TYPE_ARR)
    {
        switch ($field_id) {
            case Config::FIELD_ART :
                $out = ArtEntity::getChildDict($type); 
                break;
            case Config::FIELD_KNOWLEDGE :
                $area      = new KnowledgeArea;
                $area->del = Constants::SOFT_DEL_NO;
                $out       = $area->getAreaLeafDict(true, $type);
                break;
            case Config::FIELD_CHANLLEGE :
                $out = ChanllegeEntity::getChildDict($type); 
                break;
            case Config::FIELD_ORGANIZATION :
                $out = OrganizationEntity::getChildDict($type); 
                break;
            case Config::FIELD_ASSET :
                $out = AssetsEntity::getChildDict($type); 
                break;
        }
        return $out;
    }

    public function getDictByObjId($field_id, $obj_id)
    {
        $entity_t = $this->getEntityTableName($field_id);
        $field_obj_link_t = FieldObjEntityLink::tableName();
        $query = FieldObjEntityLink::find()
            ->select("$entity_t.id, $entity_t.name")
            ->leftJoin($entity_t, "$field_obj_link_t.entity_id = $entity_t.id")
            ->andWhere(["$field_obj_link_t.obj_id" => $obj_id]);
        $result = $query->asArray()->all();
        return ArrayHelper::map($result, "id", "name");
    }

    public function getEntityTableName($field_id)
    {
        switch ($field_id) {
            case Config::FIELD_ART :
                $entity_t = ArtEntity::tableName(); 
                break;
            case Config::FIELD_KNOWLEDGE :
                $entity_t = KnowledgeArea::tableName();
                break;
            case Config::FIELD_CHANLLEGE :
                $entity_t = ChanllegeEntity::tableName(); 
                break;
            case Config::FIELD_ORGANIZATION :
                $entity_t = OrganizationEntity::tableName(); 
                break;
            case Config::FIELD_ASSET :
                $entity_t = AssetsEntity::tableName(); 
                break;
        }
        return $entity_t;
    }
}
