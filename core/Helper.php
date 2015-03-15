<?php
/**
 * @author Pedro Gabriel
*  Classe que contém algumas funções de tratamento de caso
 */

abstract class Helper
{

    static function getDays($arrayChunck) {
        $arrayDays  = array();

        foreach ($arrayChunck as $days) {
           $day = explode("=", self::clearHtml($days));
           if(isset($day[1])){
                /**
                    NOTE, POSITION 0 HAS DESCRIPTION DAY Ex: "Sábado", WE NOT USE IN APP
                */
                $arrayDays[self::clearHtml($day[1])] = self::clearHtml($day[1]);
           }
        }
        return $arrayDays;
    }

    static function getTimes($arrayChunck){
        $arrayHours = array();
        foreach ($arrayChunck as $hours) {

            $time = explode("=", self::clearHtml($hours));

            if(isset($time[1]))
                $arrayHours[self::clearHtml($time[1])] = self::clearHtml($time[0]);

        }
        return $arrayHours;
    }

    static function clearHtml($value)
    {
            return ucwords(strtolower(trim(preg_replace("/&#?[a-z0-9]{2,8};/i","",  strip_tags($value)))));
    }

    static function removeBlankSpaces($value)
    {
            return preg_replace('/\s+/', '', $value);
    }
}
