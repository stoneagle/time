<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `events`.
 */
class m170226_024509_create_events_table extends Migration
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
        $this->createTable('{{%events}}', [
            'id'         => Schema::TYPE_PK,
            'start_date' => Schema::TYPE_DATETIME.' NOT NULL COMMENT "开始时间" ',
            'end_date'   => Schema::TYPE_DATETIME.' NOT NULL COMMENT "结束时间" ',
            'text'       => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "内容" ',
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'task_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属任务" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%events}}');
    }
}
