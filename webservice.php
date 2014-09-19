<?php  

	/* Rotinas executadas pelo site
	* 1 - Home do aluno
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
	*/


	include 'functions.php';

	$mat = @$_POST['matricula'] or outRequiredParameters();
	$digit = @$_POST['digito'] or outRequiredParameters();
	$pass = @$_POST['senha'] or outRequiredParameters();

	$ch = curl_init();
	$timeout = 0;
	curl_setopt($ch, CURLOPT_URL, 'http://www.unicap.br/PortalGraduacao/');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$server_output = curl_exec ($ch);
	curl_close($ch);

	preg_match_all('/<form method=\'post\' action=\'([^`]*?)\'/',$server_output, $conteudo);

	if(isset($conteudo[1][0])){

		$urlHash = $conteudo[1][0];

		$ch = curl_init();

		$url = 'http://www.unicap.br/PortalGraduacao/'.$urlHash;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
		            "Matricula=$mat&Digito=$digit&Senha=$pass&rotina=1");

		$server_output = curl_exec ($ch);

		curl_close ($ch);

		preg_match_all('/<form method=\'post\' action=\'([^`]*?)\'/',$server_output, $conteudo);

		$userName = getUserName($server_output);

		$result = array();

		$urlHash = $conteudo[1][0];

		$result = getContentRotines($urlHash, 2); // Dados pessoais

		$userPersonalData = getUserPersonalData($result['content']);
		
		$result = getContentRotines($result['hash'], 3);

		$testCalendar = getTestCalendar($result['content']);

		printJson(true, "Sucesso", array('userName' => $userName, 'userData' => $userPersonalData));

		// $result[] = getContentRotines($result[1]['hash'], 3);

		// $result[] = getContentRotines($result[2]['hash'], 4);

	} else {
		printJson();
	}

?>