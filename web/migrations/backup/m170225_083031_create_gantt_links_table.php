<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `gantt_links`.
 */
class m170225_083031_create_gantt_links_table extends Migration
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
        $this->createTable('{{%gantt_links}}', [
            'id'      => Schema::TYPE_PK,
            'source'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "来源" ',
            'target'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "目标" ',
            'type'    => 'varchar(1) NOT NULL DEFAULT 0 COMMENT "类别" ',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'del'     => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "乱删除"',
            'ctime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%gantt_links}}');
    }
}
