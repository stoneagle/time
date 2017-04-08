<?Php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class OrganizationLinks extends BaseActiveRecord
{
    const TABLE_NAME    = "organization_user_link";
    public $project_name;
    public $priority_id;

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['priority_id'], 'integer'],
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
            'entity_ids'   => '相关实体列表',
            'ctime'        => '创建时间',
            'utime'        => '更新时间',
        ];
    }

    public function getQuery()
    {
        $organization = self::tableName();
        $project_t   = Project::tableName();
        $query       = self::find()
            ->select("$organization.*, $project_t.text project_name, $project_t.id project_id")
            ->leftJoin($project_t, "$project_t.obj_id = $organization.id")
            ->andFilterWhere(["$project_t.field_id" => Config::FIELD_ORGANIZATION])
            ->andFilterWhere(["$project_t.text" => $this->project_name])
            ->andFilterWhere(["$organization.user_id" => $this->user_id]);
        return $query;
    }
}
