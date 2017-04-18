<?php
use app\models\Constants;

class ArrDict
{
    public static function getDictByType($type, $list, $multi_flag = false, $multi_index = "", $id_index = "id", $name_index = "name")
    {
        $dict = [];
        switch ($type) {
            case Constants::DICT_TYPE_MAP :
                foreach ($list as $one) {
                    if ($multi_flag) {
                        $dict[$one[$multi_index]][$one[$id_index]] = $one[$name_index];
                    } else {
                        $dict[$one[$id_index]] = $one[$name_index];
                    }
                }
                break;
            case Constants::DICT_TYPE_ARR :
                foreach ($list as $one) {
                    if ($multi_flag) {
                        $dict[$one[$multi_index]][] = [
                            "id"   => $one[$id_index],
                            "name" => $one[$name_index],
                        ];
                    } else {
                        $dict[] = [
                            "id"   => $one[$id_index],
                            "name" => $one[$name_index],
                        ];
                    }
                }
                break;
            case Constants::DICT_TYPE_DHX :
                foreach ($list as $one) {
                    if ($multi_flag) {
                        $dict[$one[$multi_index]][] = [
                            "key"   => $one[$id_index],
                            "label" => $one[$name_index],
                        ];
                    } else {
                        $dict[] = [
                            "key"   => $one[$id_index],
                            "label" => $one[$name_index],
                        ];
                    }
                }
                break;
            case Constants::DICT_TYPE_SELECT2 : 
                foreach ($list as $one) {
                    if ($multi_flag) {
                        $dict['data'][$one[$multi_index]][] = [
                            "id"   => $one[$id_index],
                            "text" => $one[$name_index],
                        ];
                    } else {
                        $dict['data'][] = [
                            "id"   => $one[$id_index],
                            "text" => $one[$name_index],
                        ];
                    }
                }
                break;
        }
        return $dict;
    }

}
