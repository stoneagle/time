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
            [['type'], 'integer'],
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
            'id'    => 'ID',
            'name'  => '名称',
            'type'  => '类别',
            'ctime' => '创建时间',
            'utime' => '更新时间',
        ];
    }

    public function getQuery()
    {
        $config_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["$config_t.id"   => $this->id]);
        $query->andFilterWhere(["$config_t.type" => $this->type]);
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
}
