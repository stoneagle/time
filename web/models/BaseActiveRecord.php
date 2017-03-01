<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class BaseActiveRecord extends \yii\db\ActiveRecord
{
    public static $connection = false;

    public function __construct()
    {
        parent::__construct();
        self::$connection = \Yii::$app->db;
    }

    // 获取query的sql语句
    protected function getRawSql($query)
    {
        return $query->createCommand()->getRawSql();
    }

    /*
     * 对象的统一校验和存储
     */
    public function modelValidSave()
    {
        $validate_result = $this->validate();
        if (!$validate_result) {
            $msg = "";
            foreach ($this->getErrors() as $field => $info) {
                $msg .= $field . ":" . $info[0];
            }
            throw new  \Exception($msg, Error::ERR_VALID);
        }
        $save_result = $this->save();
        if (!$save_result) {
            throw new  \Exception(json_encode($this->getErrors()), Error::ERR_SAVE);
        }
        return true;
    }
}
