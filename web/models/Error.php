<?php

namespace app\models;

class Error
{
    const ERR_OK                          = 0;
    const ERR_PARAMS                      = 1;
    const ERR_SERVER                      = 2;
    const ERR_MODEL                       = 3;
    const ERR_VALID                       = 100;
    const ERR_SAVE                        = 101;
    const ERR_DEL                         = 102;
    const ERR_GANTT_TASKS_DURATION_CHANGE = 1000;

    public static $err_msg = [
        self::ERR_OK     => "操作成功",
        self::ERR_PARAMS => "参数错误",
        self::ERR_SERVER => "服务端错误",
        self::ERR_MODEL  => "无法找到对象",
        self::ERR_SAVE   => "存储失败",
        self::ERR_DEL    => "删除失败",
    ];

    public static function msg($code)
    {
        $ret = "错误信息不存在";
        if (isset(self::$err_msg[$code])) {
            $ret = self::$err_msg[$code];
        }
        return $ret; 
    }
}
