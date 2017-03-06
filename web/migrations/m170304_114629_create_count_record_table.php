<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `count_record`.
 */
class m170304_114629_create_count_record_table extends Migration
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
        $this->createTable('{{%count_record}}', [
            'id'        => Schema::TYPE_PK,
            'action_id'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属任务" ',
            'status'    => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "状态" ',
            'init_time' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "起始时间" ',
            'user_id'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'ctime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%count_record}}');
    }
}
