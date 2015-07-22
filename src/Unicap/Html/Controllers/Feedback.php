<?php

namespace Unicap\Html\Controllers;

use Unicap\Webservice\Common\Core,
    Unicap\Webservice\Helper\JsonResult,
    Unicap\DataSource\Files\LogTxt;

class Feedback
{

	private static $fileName = 'Views/feedback.html';

	public function __construct()
	{

		if($_SERVER['REQUEST_METHOD'] == "POST")
		{
			$core = new Core();

			$log = new LogTxt('feedback-'.date('Y-m-d H:i'));

			$message = @$_POST['message'];

			$log->putContent($core->serialize().":\n".$message);

			$log->flush();

			JsonResult::success(array());
		}
		else
		{
			$this->renderTemplate();			
		}
	}

	private function renderTemplate()
	{
		
		chdir(__DIR__);
		chdir('../');

		echo file_get_contents(self::$fileName);
	}
}