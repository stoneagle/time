<?php

use yii\db\Schema;
use yii\db\Migration;

class m170313_000157_assets_info_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%assets_info}}', [
            'id'          => Schema::TYPE_PK,
            'obj_id'      => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "领域对象id" ',
            'trade_num'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "交易次数" ',
            'head_count'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "人力资源数量" ',
            'time_span'   => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "日期间隔/天" ',
            'value'       => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "当前价值" ',
            'income_flow' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "现金流/元" ',
            'position'    => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "展示位置" ',
            'ctime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%assets}}');
    }
}
