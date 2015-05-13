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

    static function upperRomanString($string)
    {

        $words = preg_split("/[\s,_-]+/", $string);

        $lastWord = $words[count($words)-1];

        preg_match('/(IX|IV|V?I{0,3})/i', $lastWord, $matches);

        if(!empty($matches[0])){
            $oldName = $string;
            $string = @substr($string, 0, strpos($string, $matches[0])).mb_strtoupper($matches[0]);
            if(empty($string) || $string == null){
                $string = $oldName;
            }
        }

        return $string;

    }

    static function getInitialLetters($string){

        $words = preg_split("/[\s,_-]+/", $string);

        $result = "";
        foreach ($words as $index => $word) {
          if(count($words) == 1 ){
            if(!isset($word[1]))
                $result .= $word[0];
            else
                $result   .= $word[0].mb_strtoupper($word[1]);
          }
          else if(count($words) >= 2 && ($index == 2 || $index == 0) )
            $result   .= $word[0];
          else if(count($words) == 2)
            $result   .= $word[0];
        }

        return $result;

    }

    static function clearHtml($value)
    {
            return ucwords(strtolower(trim(preg_replace("/&#?[a-z0-9]{2,8};/i","",  strip_tags($value)))));
    }

    static function removeBlankSpaces($value)
    {
            return preg_replace('/\s+/', '', $value);
    }


    static function getMesExtenso($mes){

        switch ($mes) {
            case '1':
                return "Janeiro";
                break;
            case '2':
                return "Fevereiro";
                break;
            case '3':
                return "Março";
                break;
            case '4':
                return "Abril";
                break;
            case '5':
                return "Maio";
                break;
            case '6':
                return "Junho";
                break;
            case '7':
                return "Julho";
                break;
            case '8':
                return "Agosto";
                break;
            case '9':
                return "Setembro";
                break;
            case '10':
                return "Outubro";
                break;
            case '11':
                return "Novembro";
                break;
            case '12':
                return "Dezembro";
                break;
            default:
                return "Mês não especificado";
                break;
        }

    }
}
