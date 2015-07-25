<?php
/**
 * @author Pedro Gabriel
 * Classse que retorna e trata todo HTML da resposta do servidor da UNICAP
 */
namespace Unicap\Webservice\Server;

use Unicap\Webservice\Helper\JsonResult;
use Unicap\Webservice\Helper\StringHelper;

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

    public function getHtmlDecoded()
    {
        return utf8_decode($this->html);
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function checkNotRegistered()
    {
        if(strstr($this->onlyCharacters(), "Alunonãomatriculado"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function onlyCharacters()
    {
        $html = strip_tags($this->html);
        return StringHelper::removeBlankSpaces($html);
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
                $type = StringHelper::clearHtml($value[0]);
                if(!empty($type))
                    $result[$type] = StringHelper::clearHtml($value[1]);
            }

        }

        return $result;
    }

    public function getMattersBasicData()
    {

        preg_match_all('/<table align=center border=1 width="100%" height=35 cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$this->getHtml(), $tableMatter);

        if(!isset($tableMatter[1][0])){
            JsonResult::error("Erro em encontrar suas diciplinas");
        }

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

        $days = StringHelper::getDays($chunkTime[1]); // Get a array of days Dinamic  2 to 7
        $schedules = StringHelper::getTimes($chunkTime[0]); // Return the horaries of UNICAP

        foreach($arrayChunck as $arrayMatter){

            $result[$i]['matterCode'] = StringHelper::clearHtml($arrayMatter[0]);
            $result[$i]['matterName'] = StringHelper::upperRomanString(StringHelper::clearHtml($arrayMatter[1]));
            $result[$i]['matterClass'] = StringHelper::clearHtml($arrayMatter[2]);

            if($result[$i]['matterClass'] == "." || empty($result[$i]['matterClass']))
            {
                $result[$i]['matterClass'] = "Não informado";
            }

            $matterRoomString = preg_replace('/\s+/', '-', StringHelper::clearHtml($arrayMatter[3]));
            if(!empty($matterRoomString))
            {
                $result[$i]['matterRoom'] = "Bloco ".$matterRoomString[0].", sala ".$matterRoomString;
                $result[$i]['matterRoomShort'] = $matterRoomString;
            }
            else
            {
                $result[$i]['matterRoom'] = "Não informado";
                $result[$i]['matterRoomShort'] = "Não informado";
            }


            $result[$i]['initialLetters']   = StringHelper::getInitialLetters($result[$i]['matterName']);


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


            if(!empty($timeExploded) && count($timeExploded) >= 1)
            {
                foreach ($timeExploded as $key => $value) {
                    if(strlen($value) > 1)
                    {
                        for($j = 1; $j < strlen($value); $j++)
                            $result[$i]['days'][$days[$value[0]]][] = $schedules[$value[$j]];

                        $quantityHoraries = count($result[$i]['days'][$days[$value[0]]]);

                        if($quantityHoraries > 1){
                            $firstHorary = $result[$i]['days'][$days[$value[0]]][0];
                            $lastHorary = end($result[$i]['days'][$days[$value[0]]]);

                            $explodeFirstHorary = explode('-', $firstHorary);
                            $explodeLastHorary = explode('-', $lastHorary);

                            if(isset($explodeFirstHorary[0]) && isset($explodeLastHorary[1]))
                                $result[$i]['days'][$days[$value[0]]] = $explodeFirstHorary[0].'às'.$explodeLastHorary[1];
                        }
                    }

                }
            }
            if(!isset($result[$i]['days']) || empty($result[$i]['days']))
                $result[$i]['days'] = new \stdClass();

            $result[$i]['matterTime'] = StringHelper::clearHtml($arrayMatter[4]);
            $result[$i]['matterPeriod'] = StringHelper::clearHtml($arrayMatter[7]);

            if($result[$i]['matterTime'] == '.' || empty($result[$i]['matterTime']))
            {
                $result[$i]['matterTime'] = "Não informado";
            }

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

        if (!isset($tableTestInformations[1][0])){
            if($this->checkNotRegistered())
                return array();
            else
                JsonResult::error("Falha em resgatar o calendário de provas");
        }

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
            $matterCode = StringHelper::clearHtml($arrayChunck[$key][0]);
            $result[$matterCode]['firstGq'] = StringHelper::clearHtml($arrayChunck[$key][2]);
            $result[$matterCode]['firstGq2Call'] = StringHelper::clearHtml($arrayChunck[$key][3]);
            $result[$matterCode]['secondGq'] = StringHelper::clearHtml($arrayChunck[$key][4]);
            $result[$matterCode]['final'] = StringHelper::clearHtml($arrayChunck[$key][5]);
            $result[$matterCode]['final2Call'] = StringHelper::clearHtml($arrayChunck[$key][6]);

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
        if (!isset($tableTestInformations[1][0])){
            if($this->checkNotRegistered())
            {
                return array();
            }
            else
            {
                JsonResult::error("Falha em resgatar as notas");
            }
        }

        preg_match_all('/<td align="center" class="tab_texto">([^`]*?)<\/td>/',$tableTestInformations[1][0], $matterInformations);

        if (!isset($matterInformations[1]))
            JsonResult::error("Falha em resgatar as notas");

        $result = array();

        $arrayChunck = array_chunk($matterInformations[1], 8);

        foreach ($arrayChunck as $key => $arrayMatter) {
            $matterCode = StringHelper::clearHtml($arrayMatter[0]);
            $result[$matterCode]['firstGq'] = StringHelper::clearHtml($arrayMatter[2]);
            $result[$matterCode]['secondGq'] = StringHelper::clearHtml($arrayMatter[3]);
            $result[$matterCode]['average'] = StringHelper::clearHtml($arrayMatter[4]);
            $result[$matterCode]['final'] = StringHelper::clearHtml($arrayMatter[5]);
            $result[$matterCode]['finalAverage'] = StringHelper::clearHtml($arrayMatter[6]);
            $result[$matterCode]['finalSituation'] = StringHelper::clearHtml($arrayMatter[7]);
        }
        return $result;
    }

    public function getDockets()
    {

        preg_match_all('/<table width="100%" border="0" cellspacing="0" cellpadding="3" align="center">([^`]*?)<\/table>/',$this->getHtml(), $tableDockets);

        if (!isset($tableDockets[1][0]))
            JsonResult::error("Falha em baixar os boletos de pagamento.");

        preg_match_all('/<td width="(.*) align="center" class="tab_texto">([^`]*?)<\/td>/',$tableDockets[1][0], $dockets);

        if (!isset($dockets[2]))
            JsonResult::error("Falha em x os boletos de pagamento.");


        $result = array();
        $arrayChunck = array_chunk($dockets[2], 3);

        // setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        // date_default_timezone_set('America/Sao_Paulo');

        foreach ($arrayChunck as $docket) {

            if(isset($docket[1])){
                $result[] = array('Vencimento' => $docket[2], 'Mês' => StringHelper::getMesExtenso($docket[1]), 'Parcela' => $docket[1]);
            }
        }

        return $result;
    }

    public function getToCourseMatters($cursingMatters = array())
    {

        preg_match_all('/<table border=1 width="100%" height=15 cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$this->getHtml(), $tableToCourse);

        if (!isset($tableToCourse[1][0]))
            JsonResult::error("Falha em baixar as matérias a cursar.");

        preg_match_all('/<td align="(.*) class="tab_texto">([^`]*?)<\/td>/',$tableToCourse[1][0], $toCourse);


        if (!isset($toCourse[2]))
            JsonResult::error("Falha em baixar as matérias a cursar. Contacte o desenvolvedor");


        $result = array();
        $arrayChunck = array_chunk($toCourse[2], 7);

        foreach ($arrayChunck as $matter) {
            $matterCode = StringHelper::clearHtml($matter[2]);

            if(!isset($cursingMatters[$matterCode])) {

                $matterName = StringHelper::upperRomanString(StringHelper::clearHtml($matter[4]));

                $result[] = array('period' => StringHelper::clearHtml($matter[0]),
                                'matterCode' => $matterCode,
                                'name' => $matterName,
                                'initialLetters' => StringHelper::getInitialLetters($matterName),
                                'credits' => StringHelper::clearHtml($matter[5]),
                                );
            }
        }

        return $result;
    }

    public function getCoursedMatters()
    {

        preg_match_all('/<table border=1 width="100%" height=15 cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$this->getHtml(), $table);

        if (!isset($table[1][0]))
            JsonResult::error("Falha em baixar as matérias cursadas.");

        preg_match_all('/<td align="(.*) class="tab_texto">([^`]*?)<\/td>/',$table[1][0], $coursed);


        if (!isset($coursed[2]))
            JsonResult::error("Falha em baixar as matérias cursadas. Contacte o desenvolvedor");

        $result = array();
        $arrayChunck = array_chunk($coursed[2], 5);


        foreach ($arrayChunck as $matter) {

            $period = str_split(StringHelper::clearHtml($matter[0]), 4);
            if(isset($period[1]))
                $period = "Cursada em ".$period[0]." no ".$period[1]."º Semestre";
            else if(isset($period[0]))
                $period = "Cursada em ".$period[0];
            else
                $period = "Cursada em ".StringHelper::clearHtml($matter[0]);

            $matterCode = StringHelper::clearHtml($matter[1]);

            $matterName = StringHelper::upperRomanString(StringHelper::clearHtml($matter[2]));


            $situation = StringHelper::clearHtml($matter[4]);

            $result[] = array('period' => $period,
                            'matterCode' => $matterCode,
                            'name' => $matterName,
                            'initialLetters' => StringHelper::getInitialLetters($matterName),
                            'average' => StringHelper::clearHtml($matter[3]),
                            'yearComplet' => StringHelper::clearHtml($matter[0]),
                            'situation' => $situation,
                        );
        }

        return $result;

    }

}