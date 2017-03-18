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

    CONST FIELD_KNOWLEDGE = 1;
    CONST FIELD_ASSET     = 2;
    CONST FIELD_ART       = 3;
    CONST FIELD_POLICY    = 4;
    CONST FIELD_PHYSICAL  = 5;

    CONST PRIORITY_IMPORTANT_URGENT = 1;
    CONST PRIORITY_IMPORTANT_UNURG  = 2;
    CONST PRIORITY_UNIM_URGENT      = 3;
    CONST PRIORITY_UNIM_UNURG       = 4;

    public static $field_arr = [
        self::FIELD_KNOWLEDGE => "工",
        self::FIELD_ASSET     => "商",
        self::FIELD_ART       => "文",
        self::FIELD_POLICY    => "政",
        self::FIELD_PHYSICAL  => "体",
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

    public static function getParentList()
    {
        $query = self::find()
            ->andWhere(["parent" => 0]);
        $result = $query->asArray()->all();
        $ret = [];
        foreach ($result as $one) {
            $type_name = self::$type_arr[$one['type']];
            $ret[$type_name][$one['id']] = $one['name'];
        }
        return $ret;
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
