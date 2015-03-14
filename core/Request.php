<?php
class Request  {

    private $url;
    public static $staticUrl = "http://www.unicap.br/PortalGraduacao/";
    private $curlHandler;
    private $session;

    public function __construct($url = null)
    {
        if($url)
            $this->url = $url;
        else
            $this->url = $staticUrl;

        $this->curlHandler = curl_init();
        self::startCurlConfig();
    }

    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }

    public function hasSession()
    {
        return !empty($this->session);
    }

    public function startCurlConfig()
    {
        curl_setopt($this->curlHandler, CURLOPT_URL, $this->url);
        curl_setopt($this->curlHandler, CURLOPT_POST, 1);
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandler, CURLOPT_CONNECTTIMEOUT, 0);

        return;
    }

    public function login($mat, $pass, $digit = '5')
    {

         if(self::$hasSession)
             JsonResult::error("Você ja esta logado!");

         $output = self::run();
         preg_match_all('/<form method=\'post\' action=\'([^`]*?)\'/',$server_output, $conteudo);

         curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, "Matricula=$mat&Digito=$digit&Senha=$pass&rotina=1");
         $output = self::run();
         preg_match_all('/<form method=\'post\' action=\'([^`]*?)\'/',$output, $actionSession);
         if(isset($actionSession[1][0])) {
            self::setSession($actionSession[1][0]);
         die('ae');
            return $this;
        } else
            JsonResult::error("Falha ao se conectar com o portal do aluno.");

    }

    public function setCurlPost($param = "")
    {
         curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $param);
         return $this;
    }

    public function run()
    {
         $server_output = curl_exec ($this->curlHandler);
         curl_close($this->curlHandler);
         return $server_output;
    }
}