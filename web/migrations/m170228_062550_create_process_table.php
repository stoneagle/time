<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `process`.
 */
class m170228_062550_create_process_table extends Migration
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
        $this->createTable('{{%process}}', [
            'id'       => Schema::TYPE_PK,
            'plan_num' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0 COMMENT "计划时间颗粒" ',
            'text'     => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "内容" ',
            'user_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'task_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属任务" ',
            'finish'   => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "完成情况" ',
            'ctime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%process}}');
    }
}
