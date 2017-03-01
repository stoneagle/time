<?php

class DateUtil
{
    public static function daysBetween($date1, $date2){  
        $date1 = strtotime($date1);  
        $date2 = strtotime($date2);  
        $days = ceil(abs($date1 - $date2)/86400);  
        return $days;  
    } 

}
