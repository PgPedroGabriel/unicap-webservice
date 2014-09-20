<?php  
	

	function printJson($status = false, $message = "Ocorreu um erro", $data = array()){

		$json = new stdClass();

		$json->status = $status;
		$json->message = $message;
		$json->data = $data;

		header('Content-type: application/json,  charset=utf-8');
		echo json_encode($json);
		exit(0);
	}


	function outRequiredParameters(){
		printJson(false, "Ausencia de parametros");
	}


	function generateParalellCurls($urlHash, $rotines){

		$curly = array();
		$result = array();

		$mh = curl_multi_init();

		foreach ($rotines as $i => $rotineNumber) {

			$curly[$i] = curl_init();

			curl_setopt($curly[$i], CURLOPT_URL,            $urlHash);
			curl_setopt($curly[$i], CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curly[$i], CURLOPT_POST,       1);
			curl_setopt($curly[$i], CURLOPT_POSTFIELDS, 'rotina='.$rotineNumber);

			curl_multi_add_handle($mh, $curly[$i]);
		}

		// execute the handles
		$running = null;
		do {
			curl_multi_exec($mh, $running);
		} while($running > 0);

		// get content and remove handles
		foreach($curly as $id => $c) {
			$result[$id] = curl_multi_getcontent($c);
			curl_multi_remove_handle($mh, $c);
		}

		curl_multi_close($mh);

		return $result;
	}


	function getContentRotines($urlHash, $rotine){

		$ch = curl_init();

		$url = 'http://www.unicap.br/PortalGraduacao/'.$urlHash;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
		            "rotina=".$rotine);

		$server_output = curl_exec ($ch);

		curl_close ($ch);

		preg_match_all('/<form method=\'post\' action=\'([^`]*?)\'/',$server_output, $conteudo);

		if(isset($conteudo[1][0]))
			return array('hash' => $conteudo[1][0], 'content' => $server_output);
		else
			return array();
	}

	function getUserName($content)
	{
		preg_match_all('/<td height="100" class="normal" valign="bottom">([^`]*?)<\/td>/',$content, $helloMessage);
		preg_match_all('/<b>([^`]*?)<\/b>/',$helloMessage[1][0], $userName);

		return utf8_encode($userName[1][0]);
	}


	function getUserPersonalData($content)
	{

		preg_match_all('/<table width="100%" border="0" height="140" cellspacing="0" align="center">([^`]*?)<\/table>/',$content, $table);
		preg_match_all('/<td.*?>([^`]*?)<\/td>/',$table[1][0], $tdText);

		$array = array();

		$i = 0;

		foreach ($tdText[1] as $key => $value) {
			if($key % 2 == 0)
				$array[$i]['type'] = utf8_encode(trim($value));
			else {
				$array[$i]['info'] = utf8_encode(trim($value));
				$i++;
			}
		}

		return $array;

	}


	function getTestCalendar($content)
	{
		
		preg_match_all('/<table align="center" border="1" width="100%" cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$content, $tableTestInformations);
		preg_match_all('/<td align="center" class="tab_texto">([^`]*?)<\/td>/',$tableTestInformations[1][0], $matterInformations);
		preg_match_all('/<td align="left"   class="tab_texto">([^`]*?)<\/td>/',$tableTestInformations[1][0], $matterName);

		// preg_match_all('/<table align=center border=1 width="100%" height=23 cellpadding=0 cellspacing=0>([^`]*?)<\/table>/',$content, $tableNoteDateInformations);
		// preg_match_all('/<td align="center" class="tab_texto">([^`]*?)<\/td>/',$tableNoteDateInformations[1][0], $matterInformationsNoteDate);
		// preg_match_all('/<td align="left"   class="tab_texto">([^`]*?)<\/td>/',$tableNoteDateInformations[1][0], $matterNameNoteDate);

		$array = array();

		$i = 0;

		$arrayChunck = array_chunk($matterInformations[1], 7);

		foreach ($matterName[1] as $key => $matter) {

			$array[$i]['matterName'] = trim(utf8_encode($matter));
			$array[$i]['matterCode'] = trim(utf8_encode($arrayChunck[$key][0]));
			$array[$i]['matterClass'] = trim(utf8_encode($arrayChunck[$key][1]));
			$array[$i]['testInformations']['firstGq'] = trim(utf8_encode($arrayChunck[$key][2]));
			$array[$i]['testInformations']['firstGq2Call'] = trim(utf8_encode($arrayChunck[$key][3]));
			$array[$i]['testInformations']['secondGq'] = trim(utf8_encode($arrayChunck[$key][4]));
			$array[$i]['testInformations']['final'] = trim(utf8_encode($arrayChunck[$key][5]));
			$array[$i]['testInformations']['final2Call'] = trim(utf8_encode($arrayChunck[$key][6]));

			$i++;
		}


		return $array;

	}

	function getPeriodNotes($content)
	{
		
		preg_match_all('/<table border="1" width="100%" cellpadding="0" cellspacing="0">([^`]*?)<\/table>/',$content, $tableTestInformations);
		preg_match_all('/<td align="center" class="tab_texto">([^`]*?)<\/td>/',$tableTestInformations[1][0], $matterInformations);

		$array = array();

		$i = 0;

		$arrayChunck = array_chunk($matterInformations[1], 8);

		foreach ($arrayChunck as $key => $arrayMatter) {
			$array[$i]['matterCode'] = trim(utf8_encode($arrayMatter[0]));
			$array[$i]['matterClass'] = trim(utf8_encode($arrayMatter[1]));
			$array[$i]['noteInformations']['firstGq'] = trim(utf8_encode($arrayMatter[2]));
			$array[$i]['noteInformations']['secondGq'] = trim(utf8_encode($arrayMatter[3]));
			$array[$i]['noteInformations']['average'] = trim(utf8_encode($arrayMatter[4]));
			$array[$i]['noteInformations']['final'] = trim(utf8_encode($arrayMatter[5]));
			$array[$i]['noteInformations']['finalAverage'] = trim(utf8_encode($arrayMatter[6]));
			$array[$i]['noteInformations']['finalSituation'] = trim(utf8_encode($arrayMatter[7]));

			$i++;
		}

		return $array;


	}

?>