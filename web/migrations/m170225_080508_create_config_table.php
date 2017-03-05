<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `config`.
 */
class m170225_080508_create_config_table extends Migration
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

        $this->createTable('{{%config}}', [
            'id'     => Schema::TYPE_PK,
            'name'   => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "名称" ',
            'type'   => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "类别"',
            'parent' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属配置"',
            'del'    => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "乱删除"',
            'ctime'  => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'  => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%config}}');
    }
}
