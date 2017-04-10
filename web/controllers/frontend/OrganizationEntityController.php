<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\OrganizationEntity;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class OrganizationEntityController extends BaseFieldController
{
    public function actionValid()
    {
        try {
            $model = new OrganizationEntity();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        return $this->baseEntityIndex(ArtEntity::class);
    }

    public function actionCreate()
    {
        try {
            return $this->baseEntityCreate(OrganizationEntity::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            return $this->baseEntityUpdate(OrganizationEntity::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionDelete()
    {
        try {
            return $this->baseEntityDelete(OrganizationEntity::class);
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
}
