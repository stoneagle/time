<?php

class DateUtil
{
    public static function daysBetween($date1, $date2){  
        $date1 = strtotime($date1);  
        $date2 = strtotime($date2);  
        $days = ceil(abs($date1 - $date2)/86400);  
        return $days;  
    } 

    public static function minuteBetween($startdate, $enddate)
    {
        $minute = floor((strtotime($enddate)-strtotime($startdate))%86400/60);
        return $minute;
    }

    public static function getWeekNum($date)
    {
        $weekarray = array("日","一","二","三","四","五","六");
        $num = date("w", strtotime($date));
        return "星期".$weekarray[$num];
    }
}
