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
    CONST TYPE_FIELD    = 1;
    CONST TYPE_ACTION   = 2;
    CONST TYPE_PRIORITY = 3;

    public static $type_arr = [
        self::TYPE_FIELD    => "领域",
        self::TYPE_ACTION   => "行为",
        self::TYPE_PRIORITY => "优先级",
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
            default :
                $ret[$one['parent']][$one['id']] = $one['name'];
                break;
            }
        }
        return $ret;
    }
}
