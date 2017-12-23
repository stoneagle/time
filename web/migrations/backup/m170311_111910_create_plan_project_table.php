<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `plan_project`.
 */
class m170311_111910_create_plan_project_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%plan_project}}', [
            'id'         => Schema::TYPE_PK,
            'project_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "目标项目" ',
            'plan_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属计划" ',
            'hours'      => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "投资时间" ',
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%plan_project}}');
    }
}
