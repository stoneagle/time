<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class FieldObjEntityLink extends BaseActiveRecord 
{
    const TABLE_NAME    = "field_obj_entity_link";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['obj_id', 'entity_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            //[['project_name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'obj_id'     => '领域对象ID',
            'entity_id'  => '相关实体ID',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $query = self::find();
        return $query;
    }

    public static function getResourceByObjAndEntity($obj_id, $field_id, $entity_id, $type = Constants::DICT_TYPE_MAP)
    {
        switch ($field_id) {
            case Config::FIELD_ART :
                $link_t = self::tableName();
                $work_t = ArtWork::tableName();
                $list = self::find()
                    ->select("$work_t.id, $work_t.name")
                    ->leftjoin($work_t, "$work_t.entity_id = $link_t.entity_id")
                    ->andWhere(["$link_t.obj_id" => $obj_id])
                    ->andWhere(["$link_t.entity_id" => $entity_id])
                    ->asArray()->all();
                    ;
                break;
            case Config::FIELD_KNOWLEDGE :
                $link_t = self::tableName();
                $area_link_t = AreaSkillLink::tableName();
                $skill_t = KnowledgeSkill::tableName();
                $list = self::find()
                    ->select("$skill_t.id, $skill_t.title as name")
                    ->leftjoin($area_link_t, "$area_link_t.area_id = $link_t.entity_id")
                    ->leftjoin($skill_t, "$skill_t.id = $area_link_t.skill_id")
                    ->andWhere(["$link_t.obj_id" => $obj_id])
                    ->andWhere(["$link_t.entity_id" => $entity_id])
                    ->asArray()->all();
                    ;
                break;
            case Config::FIELD_CHANLLEGE :
                break;
            case Config::FIELD_ORGANIZATION :
                break;
            case Config::FIELD_ASSET :
                $link_t = self::tableName();
                $sub_t = AssetsSub::tableName();
                $list = self::find()
                    ->select("$sub_t.id, $sub_t.name")
                    ->leftjoin($sub_t, "$sub_t.entity_id = $link_t.entity_id")
                    ->andWhere(["$link_t.obj_id" => $obj_id])
                    ->andWhere(["$link_t.entity_id" => $entity_id])
                    ->asArray()->all();
                    ;
                break;
        }

        switch ($type) {
            case Constants::DICT_TYPE_MAP :
                $dict = [];
                $dict[0] = "无";
                $dict = $dict + ArrayHelper::map($list, "id", "name");
               break;
            case Constants::DICT_TYPE_ARR :
                $dict = [];
                $dict[] = [
                    "id"   => 0,
                    "name" => "无",
                ];
                $dict = array_merge($dict, $list);
                break;
            case Constants::DICT_TYPE_DHX :
                $dict = [];
                $dict[] = [
                    "key"   => 0,
                    "label" => "无",
                ];
                foreach ($list as $one) {
                    $dict[] = [
                        "key"   => $one["id"],
                        "label" => $one["name"],
                    ];
                }
                break;
            case Constants::DICT_TYPE_SELECT2 : 
                $dict = [];
                $dict['data'][] = [
                    "id"   => 0,
                    "text" => "无",
                ];
                foreach ($list as $one) {
                    $dict['data'][] = [
                        "id"   => $one["id"],
                        "text" => $one["name"],
                    ];
                }
                break;
        }
        return $dict;
    }
}
