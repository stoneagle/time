<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\ChanllegeEntity;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class ChanllegeEntityController extends BaseFieldController
{
    public function actionValid()
    {
        try {
            $model = new ChanllegeEntity();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        return $this->baseEntityIndex(ChanllegeEntity::class);
    }

    public function actionCreate()
    {
        try {
            return $this->baseEntityCreate(ChanllegeEntity::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            return $this->baseEntityUpdate(ChanllegeEntity::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionDelete()
    {
        try {
            return $this->baseEntityDelete(ChanllegeEntity::class);
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
}
