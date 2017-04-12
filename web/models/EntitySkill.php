<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class EntitySkill extends EntityBase 
{
    const TABLE_NAME  = "entity_skill";
    const ENTITY_TYPE = Area::FIELD_KNOWLEDGE;

    CONST TYPE_BASIC   = 1;
    CONST TYPE_TOOL    = 2;
    CONST TYPE_SERVICE = 3;
    CONST TYPE_THEORY  = 4;

    CONST DEFAULT_LEVEL_MAX = 4;

    CONST SKILL_WIDTH_NUM   = 6;
    CONST SKILL_WIDTH       = 150;
    CONST SKILL_HEIGHT      = 100;
    CONST SKILL_INIT_HEIGHT = 50;
    CONST SKILL_TYPE_HEIGHT = 180;

    public static $type_arr = [
        self::TYPE_BASIC   => "基础",
        self::TYPE_TOOL    => "工具组件",
        self::TYPE_SERVICE => "业务场景",
        self::TYPE_THEORY  => "理论概念",
    ];

    public static $default_level_desc = [
        1 => "是什么，了解概念",
        2 => "怎么用，掌握使用方法",
        3 => "为什么，了解原理",
        4 => "怎么改，明晓优劣，进行改进",
    ];

    public $talent_ids;
    public $depend_ids;
    public $area_ids;

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['max_points', 'type_id'], 'integer', 'min' => 1],
            [['area_ids', "name", "type_id"], 'required'],
            [['ctime', 'utime', 'depend_ids', 'talent_ids'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['description', 'rank_desc', 'img_url'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => '技能名称',
            'description' => '技能描述',
            'rank_desc'   => '级别描述',
            'img_url'     => '图片地址',
            'max_points'  => '最大级别',
            'type_id'     => '所属类别',
            'area_id'     => '所属领域',
            'area_ids'    => '所属领域',
            'talent_ids'  => '相关天赋',
            'depend_ids'  => '前置技能',
            'ctime'       => '创建时间',
            'utime'       => '更新时间',
        ];
    }

    public function getQuery()
    {
        $entity_t = self::tableName();
        $query    = static::find()
            ->select("$entity_t.*")
            ->andFilterWhere(["$entity_t.name"    => $this->name])
            ->andFilterWhere(["$entity_t.type_id" => $this->type_id]);
        return $query;
    }
}
