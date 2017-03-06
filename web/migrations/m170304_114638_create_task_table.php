<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `task`.
 */
class m170304_114638_create_task_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%task}}', [
            'id'         => $this->primaryKey(),
            'text'       => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "内容" ',
            'start_date' => Schema::TYPE_DATETIME.' NOT NULL COMMENT "开始日期" ',
            'start_date' => Schema::TYPE_DATETIME.' NOT NULL COMMENT "开始日期" ',
            'duration'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "持续时间" ',
            'progress'   => Schema::TYPE_FLOAT . ' NOT NULL DEFAULT 0 COMMENT "进度" ',
            'parent'     => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "父id" ',
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'del'        => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "软删除"',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable("{{%task}}");
    }
}
