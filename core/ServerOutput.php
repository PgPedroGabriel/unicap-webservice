<?php
/**
 * @author Pedro Gabriel
 * Classse que retorna e trata todo HTML da resposta do servidor da UNICAP
 */
class ServerOutput
{
    private $html;

    public function __construct($html) {
        $this->html = utf8_encode($html);
        $this->isTryLimit();
        $this->incorrectLogin();
        $this->isMaintence();
    }

    public function isTryLimit()
    {

        if(strstr($this->onlyCharacters(), 'estourouolimitedetentativas'))
            JsonResult::error("Você estourou o limite de tentativas, tente amanhã.");

        return;
    }

    public function incorrectLogin()
    {
        if(strstr($this->onlyCharacters(), 'senhainválida'))
            JsonResult::error("Login incorreto");

        return;
    }

    public function isMaintence()
    {

        if(strstr($this->onlyCharacters(), 'nomomentoestamosemmanutenção'))
            JsonResult::error("o servidor UNICAP está em manutenção");

        return;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function onlyCharacters()
    {
        $html = strip_tags($this->html);
        return Helper::removeBlankSpaces($html);
    }

    public function getSessionHash()
    {
        preg_match_all('/<form method=\'post\' action=\'([^`]*?)\'/',$this->getHtml(), $activeSession);

        if(isset($activeSession[1][0]))
            return $activeSession[1][0];
        else
            JsonResult::error("Falha ao se conectar com o portal do aluno.");

    }


    public function userContent()
    {

        preg_match_all('/<table width="100%" border="0" height="140" cellspacing="0" align="center">([^`]*?)<\/table>/',$this->getHtml(), $tableContent); // Tabela principal

        if(!isset($tableContent[1][0]))
            JsonResult::error("Senha incorreta.");

        preg_match_all('/<td.*?>([^`]*?)<\/td>/',$tableContent[1][0], $tdText); // Tds da tabela

        if(!isset($tdText[1]))
            JsonResult::error("Senha incorreta.");

        $result = array();
        $arrayChunck = array_chunk($tdText[1], 2); // Divide array on pieces | Type, Value

        foreach ($arrayChunck as $key => $value) {
            if(isset($value[0]) && isset($value[1])){
                $type = Helper::clearHtml($value[0]);
                if(!empty($type))
                    $result[$type] = Helper::clearHtml($value[1]);
            }

        }

        return $result;
    }

    public function getMattersBasicData()
    {

        preg_match_all('/<table align=center border=1 width="100%" height=35 cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$this->getHtml(), $tableMatter);

        if(!isset($tableMatter[1][0]))
            JsonResult::error("Erro em encontrar suas diciplinas");

        preg_match_all('/<table width="100%%" border="0">([^`]*?)<\/table>/',$this->getHtml(), $tableHorary);
        if(!isset($tableHorary[1][0]))
            JsonResult::error("Erro em encontrar sua tabela de horario");

        preg_match_all('/<td.*?>([^`]*?)<\/td>/',$tableMatter[1][0], $matterInformations);

        if(!isset($matterInformations[1][0]))
            JsonResult::error("Erro em encontrar suas diciplinas");

        preg_match_all('/<td.*?>([^`]*?)<\/td>/',$tableHorary[1][0], $timeInformations);

        if(!isset($timeInformations[1][0]))
            JsonResult::error("Erro em encontrar suas diciplinas");

        $result = array();

        $i = 0;

        $arrayChunck = array_chunk($matterInformations[1], 8); // Each Discipline has 8 positions in the table, divide all content in pieces of 8

        unset($arrayChunck[ count($arrayChunck) - 1 ]); // Unset last Chunck because is the sum of total credits, and i dont need this.

        $chunkTime = array_chunk($timeInformations[1], 20); // we has 20 days of horary in array result;

        $days = Helper::getDays($chunkTime[1]); // Get a array of days Dinamic  2 to 7
        $schedules = Helper::getTimes($chunkTime[0]); // Return the horaries of UNICAP

        foreach($arrayChunck as $arrayMatter){

            $result[$i]['matterCode'] = Helper::clearHtml($arrayMatter[0]);
            $result[$i]['matterName'] = Helper::clearHtml($arrayMatter[1]);
            $result[$i]['matterClass'] = Helper::clearHtml($arrayMatter[2]);

            $matterRoomString = preg_replace('/\s+/', '-', Helper::clearHtml($arrayMatter[3]));

            $result[$i]['matterRoom'] = "Bloco ".$matterRoomString[0].", sala ".$matterRoomString;
            $result[$i]['matterRoomShort'] = $matterRoomString;

            $result[$i]['initialLetters']   = "";

            $words = preg_split("/[\s,_-]+/", $result[$i]['matterName']);

            foreach ($words as $index => $word) {
              if(count($words) == 1)
                $result[$i]['initialLetters']   .= $word[0].$word[1];
              else if(count($words) >= 2 && ($index == 2 || $index == 0) )
                $result[$i]['initialLetters']   .= $word[0];
              else if(count($words) == 2)
                $result[$i]['initialLetters']   .= $word[0];
            }

            $timeExploded = explode(" ",trim($arrayMatter[4]));

            /*
            * Time exploded returns for example
            *array(3) {
            *      [0]=>
            *      string(3) "3NO"
            *      [1]=>
            *      string(3) "4NO"
            *      [2]=>
            *      string(3) "6NO"
            *   }
            *  We need Get the Day (first Positiion in string) And the Horary (The continous...)
            */

            foreach ($timeExploded as $key => $value) {
                for($j = 1; $j < strlen($value); $j++)
                    $result[$i]['days'][$days[$value[0]]][] = $schedules[$value[$j]];
            }

            $result[$i]['matterTime'] = Helper::clearHtml($arrayMatter[4]);
            $result[$i]['matterPeriod'] = Helper::clearHtml($arrayMatter[7]);

            $i++;
        }

        /*

        The Result is some like this.
        array(6) {
            [0]=>
            array(8) {
                ["matterCode"]=>
                string(7) "Eng1115"
                ["matterName"]=>
                string(29) "Des Tec Assist Por Computador"
                ["matterClass"]=>
                string(6) "Ny36.0"
                ["matterRoom"]=>
                string(25) "Bloco D, sala D0003-D0206"
                ["matterRoomShort"]=>
                string(11) "D0003-D0206"
                ["days"]=>
                array(3) {
                  [3]=>
                  array(2) {
                    [0]=>
                    string(13) "18:30 - 19:20"
                    [1]=>
                    string(13) "19:20 - 20:10"
                  }
                  [4]=>
                  array(2) {
                    [0]=>
                    string(13) "18:30 - 19:20"
                    [1]=>
                    string(13) "19:20 - 20:10"
                  }
                  [6]=>
                  array(2) {
                    [0]=>
                    string(13) "18:30 - 19:20"
                    [1]=>
                    string(13) "19:20 - 20:10"
                  }
                }
                ["matterTime"]=>
                string(11) "3no 4no 6no"
                ["matterPeriod"]=>
                string(2) "03"
              },
             ....
        }

        */

        return $result;
    }

    public function getMattersCalendar()
    {

        preg_match_all('/<table align="center" border="1" width="100%" cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$this->getHtml(), $tableTestInformations);

        if (!isset($tableTestInformations[1][0]))
            JsonResult::error("Falha em resgatar o calendário de provas");

        preg_match_all('/<td align="center" class="tab_texto">([^`]*?)<\/td>/',$tableTestInformations[1][0], $matterInformations);

        if (!isset($matterInformations[1]))
            JsonResult::error("Falha em resgatar o calendário de provas");

        preg_match_all('/<td align="left"   class="tab_texto">([^`]*?)<\/td>/',$tableTestInformations[1][0], $matterName);

        if (!isset($matterName[1]))
            JsonResult::error("Falha em resgatar o calendário de provas");

        $result = array();

        $i = 0;

        $arrayChunck = array_chunk($matterInformations[1], 7);

        foreach ($matterName[1] as $key => $matter) {
            $matterCode = Helper::clearHtml($arrayChunck[$key][0]);
            $result[$matterCode]['firstGq'] = Helper::clearHtml($arrayChunck[$key][2]);
            $result[$matterCode]['firstGq2Call'] = Helper::clearHtml($arrayChunck[$key][3]);
            $result[$matterCode]['secondGq'] = Helper::clearHtml($arrayChunck[$key][4]);
            $result[$matterCode]['final'] = Helper::clearHtml($arrayChunck[$key][5]);
            $result[$matterCode]['final2Call'] = Helper::clearHtml($arrayChunck[$key][6]);

            $i++;
        }

        /* This returns
        array(5) {
          ["Eng1115"]=>
          array(1) {
            ["testInformations"]=>
            array(5) {
              ["firstGq"]=>
              string(10) "08/04/2015"
              ["firstGq2Call"]=>
              string(10) "10/04/2015"
              ["secondGq"]=>
              string(10) "10/06/2015"
              ["final"]=>
              string(10) "16/06/2015"
              ["final2Call"]=>
              string(10) "17/06/2015"
            }
        },
            ....
    }
         */

        return $result;
    }


    public function getMattersNotes()
    {
        preg_match_all('/<table border="1" width="100%" cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$this->getHtml(), $tableTestInformations);
        if (!isset($tableTestInformations[1][0]))
            JsonResult::error("Falha em resgatar as notas");

        preg_match_all('/<td align="center" class="tab_texto">([^`]*?)<\/td>/',$tableTestInformations[1][0], $matterInformations);

        if (!isset($matterInformations[1]))
            JsonResult::error("Falha em resgatar as notas");

        $result = array();

        $arrayChunck = array_chunk($matterInformations[1], 8);

        foreach ($arrayChunck as $key => $arrayMatter) {
            $matterCode = Helper::clearHtml($arrayMatter[0]);
            $result[$matterCode]['firstGq'] = Helper::clearHtml($arrayMatter[2]);
            $result[$matterCode]['secondGq'] = Helper::clearHtml($arrayMatter[3]);
            $result[$matterCode]['average'] = Helper::clearHtml($arrayMatter[4]);
            $result[$matterCode]['final'] = Helper::clearHtml($arrayMatter[5]);
            $result[$matterCode]['finalAverage'] = Helper::clearHtml($arrayMatter[6]);
            $result[$matterCode]['finalSituation'] = Helper::clearHtml($arrayMatter[7]);
        }
        return $result;
    }

}