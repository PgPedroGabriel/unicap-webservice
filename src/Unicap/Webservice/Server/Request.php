<?php
/**
 * @author Pedro Gabriel
 * Classse que faz comunicação entre servidor da católica e faz requisições de suas rotinas
    * Rotinas executadas pelo site
    * 1 - Home do aluno (Responsável também pelo Login)
    * 2 - Dados pessoais
    * 3 - Calendario de provas
    * 4 - Notas do periodo
    * 14 - Disciplinas do periodo
    * 5 - Disciplinas cursadas
    * 7 - Disciplinas Eletivas da unicap
    * 6 - Disciplinas a cursar
    * 8 - Disciplinas Eletivas do curso
    * 9 - Disciplinas eletivas do departamento
    * 10 - Atividades complementares
    * 11 - imprimir boletos
    * 12 - emitir via do boleto
 */

namespace Unicap\Webservice\Server;

use Unicap\Webservice\Helper\JsonResult;

class Request
{

    private $url;
    public static $staticUrl = "http://www.unicap.br/PortalGraduacao/";
    private $curlHandler;
    private $session;
    private $serverOutput;
    private $mat;
    private $pass;

    public function __construct()
    {
        $this->prepareCurl();
    }

    public function prepareCurl($url = "", $postParams = "")
    {
        if(!empty($url))
            $this->url = self::$staticUrl.$url;
        else
            $this->url = self::$staticUrl;

        $this->curlHandler = curl_init();
        curl_setopt($this->curlHandler, CURLOPT_URL, $this->url);
        curl_setopt($this->curlHandler, CURLOPT_POST, 1);
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandler, CURLOPT_CONNECTTIMEOUT, 0);

        if(!empty($postParams))
            curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $postParams);

        return;
    }

    public function getServerOutput()
    {
        return $this->serverOutput;
    }

    public function setSession()
    {
        $this->session = $this->serverOutput->getSessionHash();
        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function hasSession()
    {
        return !empty($this->session);
    }

    public function login($mat, $pass, $digit = '5')
    {

         if($this->hasSession())
             JsonResult::error("Você ja esta logado!");

         $this->run();
         $this->mat = $mat;
         $this->pass = $pass;

         $this->prepareCurl($this->getSession(), $this->commonPost("1")); // Home do aluno (Responsável também pelo Login)
         $this->run();
    }

    public function run($setSession = true)
    {
         $html = curl_exec ($this->curlHandler);

         curl_close($this->curlHandler);

         $this->serverOutput = new ServerOutput($html);

         if($setSession)
            $this->setSession();

         return;
    }

    public function getUserData()
    {
        $this->prepareCurl($this->getSession(), $this->commonPost("2")); //Dados pessoais
        $this->run();
        return $this->serverOutput->userContent();
    }

    public function commonPost($routine)
    {
        return "Matricula=".$this->mat."&Digito=5&Senha=".$this->pass."&rotina=".$routine;
    }


    public function getMatterData()
    {
        $this->prepareCurl($this->getSession(), $this->commonPost("14")); //Disciplinas do periodo
        $this->run();

        $basicDataFromMatters = $this->serverOutput->getMattersBasicData();

        $this->prepareCurl($this->getSession(), $this->commonPost("3")); // Calendário de provas
        $this->run();

        $mattersCalendar = $this->serverOutput->getMattersCalendar();

        $this->prepareCurl($this->getSession(), $this->commonPost("4")); // Notas do periodo
        $this->run();

        $mattersNotes = $this->serverOutput->getMattersNotes();

        /**
        * Merge arrays
        */

        foreach ($basicDataFromMatters as $key => $value) {
                $basicDataFromMatters[$key]['testInformations'] = $mattersCalendar[$value["matterCode"]];
                $basicDataFromMatters[$key]['noteInformations'] = $mattersNotes[$value["matterCode"]];
        }

        return $basicDataFromMatters;

    }


    public function getDocketsData()
    {
        $this->prepareCurl($this->getSession(), $this->commonPost("11"));
        $this->run();

        $docketsContent = $this->serverOutput->getDockets();

        return $docketsContent;
    }

    public function getToCourseMatters($cursingMatters = array())
    {
        $this->prepareCurl($this->getSession(), $this->commonPost('6'));
        $this->run();

        $toCourseContent = $this->serverOutput->getToCourseMatters($cursingMatters);

        return $toCourseContent;
    }

    public function getCoursedMatters($cursingMatters = array())
    {
        $this->prepareCurl($this->getSession(), $this->commonPost('5'));
        $this->run();

        $coursed = $this->serverOutput->getCoursedMatters($cursingMatters);

        return $coursed;
    }


    public function downloadDocket($number = null)
    {

        if($number == null)
            JsonResult::error("Falha em fazer o download do boleto.");

        $this->prepareCurl($this->getSession(), $this->commonPost("12")."&Parcela=".$number);
        $this->run(false);

        header('Cache-Control: public');
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="new.pdf"');
        header('Content-Length: '.strlen($this->serverOutput->getHtmlDecoded()));

        echo $this->serverOutput->getHtmlDecoded();
    }
}