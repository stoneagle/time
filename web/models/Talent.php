<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "config".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $ctime
 * @property string $utime
 */
class Config extends BaseActiveRecord 
{
    CONST TYPE_ACTION   = 2;

    CONST FIELD_KNOWLEDGE    = 1;
    CONST FIELD_ASSET        = 2;
    CONST FIELD_ART          = 3;
    CONST FIELD_ORGANIZATION = 4;
    CONST FIELD_CHANLLEGE    = 5;
    CONST FIELD_GENERAL      = 6;

    CONST PRIORITY_IMPORTANT_URGENT = 1;
    CONST PRIORITY_IMPORTANT_UNURG  = 2;
    CONST PRIORITY_UNIM_URGENT      = 3;
    CONST PRIORITY_UNIM_UNURG       = 4;

    CONST AREA_ENGINEER = 1;
    CONST AREA_BUSINESS = 2;
    CONST AREA_ART      = 3;
    CONST AREA_POLITICS = 4;
    CONST AREA_PHYSICAL = 5;

    public static $area_arr = [
        self::AREA_ENGINEER => "工程",
        self::AREA_BUSINESS => "商业",
        self::AREA_ART      => "艺术",
        self::AREA_POLITICS => "政治",
        self::AREA_PHYSICAL => "运动",
    ];

    public static $area_en_arr = [
        self::AREA_ENGINEER => "engineer",
        self::AREA_BUSINESS => "business",
        self::AREA_ART      => "art",
        self::AREA_POLITICS => "politics",
        self::AREA_PHYSICAL => "physical",
    ];

    public static $field_arr = [
        self::FIELD_KNOWLEDGE    => "知识",
        self::FIELD_ASSET        => "资产",
        self::FIELD_ART          => "艺术",
        self::FIELD_ORGANIZATION => "组织",
        self::FIELD_CHANLLEGE    => "挑战",
        self::FIELD_GENERAL      => "通用",
    ];

    public static $priority_arr = [
        self::PRIORITY_IMPORTANT_URGENT => "重要且紧急",
        self::PRIORITY_IMPORTANT_UNURG  => "重要不紧急",
        self::PRIORITY_UNIM_URGENT      => "紧急不重要",
        self::PRIORITY_UNIM_UNURG       => "不重要且不紧急",
    ];

    public static $priority_dhtml_arr = [
        [
            "key" => self::PRIORITY_IMPORTANT_URGENT,
            "label" => "重要且紧急",
        ],
        [
            "key" => self::PRIORITY_IMPORTANT_UNURG,
            "label" => "重要不紧急",
        ],
        [
            "key" => self::PRIORITY_UNIM_URGENT,
            "label" => "紧急不重要",
        ],
        [
            "key" => self::PRIORITY_UNIM_UNURG,
            "label" => "不重要且不紧急",
        ],
    ];

    public static $type_arr = [
        self::TYPE_ACTION   => "行为",
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'parent'], 'integer'],
            [['ctime', 'utime'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'     => 'ID',
            'name'   => '名称',
            'type'   => '类别',
            'parent' => '所属类别',
            'ctime'  => '创建时间',
            'utime'  => '更新时间',
        ];
    }

    public function getQuery()
    {
        $config_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["$config_t.id"     => $this->id]);
        $query->andFilterWhere(["$config_t.type"   => $this->type]);
        $query->andFilterWhere(["$config_t.parent" => $this->parent]);
        return $query;
    }

    public function getTypeDict($dxl_style = false)
    {
        $query = $this->getQuery();
        if ($dxl_style) {
            $ret = $query->select("id as key, name as label")->asArray()->all();
        } else {
            $result = $query->select("id, name")->asArray()->all();
            $ret = ArrayHelper::map($result, 'id', 'name');
        }
        return $ret;
    }

    public static function getTypeWithParentDict($type, $lib = false)
    {
        $query = self::find() 
            ->andWhere(["Not", ["parent" => 0]])
            ->andWhere(["type" => $type]);
        $result = $query->asArray()->all();
        $ret = [];
        foreach ($result as $one) {
            switch ($lib) {
            case "select2" :
                $ret[$one['parent']]['data'][] = [
                    'id'   => $one['id'],
                    'text' => $one['name'],

                ];
                break;
            case "dhtml" : 
                $ret[$one['parent']][] = [
                    "key" => $one['id'],
                    "label" => $one['name'],
                ];
                break;
            default :
                $ret[$one['parent']][$one['id']] = $one['name'];
                break;
            }
        }
        return $ret;
    }
}
