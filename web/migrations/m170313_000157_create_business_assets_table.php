<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `business_assets`.
 */
class m170313_000157_create_business_assets_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%business_assets}}', [
            'id'          => Schema::TYPE_PK,
            'name'        => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "名称" ',
            'type_id'     => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "资产类别" ',
            'access_unit' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "评估单位，现金流是日期间隔，交易是次数，泡沫是人力" ',
            'value'       => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "价值" ',
            'time'        => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "总投入时间，单位分钟" ',
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'position'    => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "展示位置" ',
            'del'         => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "软删除"',
            'ctime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%business_assets}}');
    }
}
