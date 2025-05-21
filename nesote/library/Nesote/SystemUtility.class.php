<?php

class systemutility {


	public function loadcontroller($classname = "") {

		require __DIR__ . "/script.inc.php";
		if ($classname == "")
		{
			$err_msg = "<br><strong>Error: </strong>Requested page was not found!";
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				$err_msg .= "<br><strong>Details: </strong>Specify parameter for the function loadController!";
			}
			$this->loadErrorTemplate($err_msg);
			die(0);
		}
		$classname[0] = strtoupper((string) $classname[0]);
		if (file_exists($controller_path . $classname . ".class.php"))
		{
			require_once $controller_path . $classname . ".class.php";
			return;
		}
		$err_msg = "<br><strong>Error: </strong>Requested page was not found!";
		$ini_error_status = ini_get("error_reporting");
		if ($ini_error_status != 0)
		{
			$err_msg .= '<br><strong>Details: </strong>Class <strong>' . $classname . "</strong> not found!";
		}
		$this->loadErrorTemplate($err_msg);
		die(0);
	}

	public function createcontrollerinstance($classname = "") {

		require __DIR__ . "/script.inc.php";
		if ($classname == "")
		{
			$err_msg = "<br><strong>Error: </strong>Requested page was not found!";
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				$err_msg .= "<br><strong>Details: </strong>Specify parameter for the function createControllerInstance!";
			}
			$this->loadErrorTemplate($err_msg);
			die(0);
		}
		$classname[0] = strtoupper((string) $classname[0]);
		if (file_exists($controller_path . $classname . ".class.php"))
		{
			require_once $controller_path . $classname . ".class.php";
			return new $classname();
		}
		$err_msg = "<br><strong>Error: </strong>Requested page was not found!";
		$ini_error_status = ini_get("error_reporting");
		if ($ini_error_status != 0)
		{
			$err_msg .= '<br><strong>Details: </strong>Class <strong>' . $classname . "</strong> not found!";
		}
		$this->loadErrorTemplate($err_msg);
		die(0);
	}

	public function loaderrortemplate($message) {

		require __DIR__ . "/script.inc.php";
		$echoerror = 0;
		if (file_exists("error/error.tpl.html"))
		{
			$content = "";
			$fp = fopen("error/error.tpl.html", "r");
			while (!(feof($fp)))
			{
				$content .= fgetc($fp);
			}
			fclose($fp);
			if (substr_count($content, "{\$error}") == 0)
			{
				$echoerror = 1;
			}
			 else 
			{
				echo str_replace("{\$error}", '' . $message, $content);
				die(0);
			}
		}
		 else 
		{
			$echoerror = 1;
		}
		echo $message;
		die(0);
	}

};


?>
