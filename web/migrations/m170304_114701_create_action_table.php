<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `action`.
 */
class m170304_114701_create_action_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%action}}', [
            'id'         => Schema::TYPE_PK,
            'start_date' => Schema::TYPE_DATETIME.' NOT NULL COMMENT "开始时间" ',
            'end_date'   => Schema::TYPE_DATETIME.' NOT NULL COMMENT "结束时间" ',
            'text'       => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "内容" ',
            'duration'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "持续时间" ',
            'progress'   => Schema::TYPE_FLOAT . ' NOT NULL DEFAULT 0 COMMENT "进度" ',
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'task_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属任务" ',
            'type_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "行为类别" ',
            'plan_time'  => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "计划时间" ',
            'exec_time'  => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "实际时间" ',
            'status'     => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "状态" ',
            'desc'       => Schema::TYPE_TEXT . ' COMMENT "描述" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%action}}');
    }
}
