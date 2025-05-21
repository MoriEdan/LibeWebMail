<?php


require_once $library_path . "Nesote/Template.class.php";
require_once $library_path . "Nesote/NesoteDALController.class.php";
require_once $library_path . "Nesote/NesoteModel.class.php";
require_once $library_path . "Nesote/NesoteForm.class.php";
require_once $library_path . "Nesote/Paging.class.php";


class nesotecontroller {

	public $templateObject;
	public $argumentvalues = [];

	public function loadtemplate($tplpath, $status = "required") {

		$this->templateObject = new Template();
		$this->templateObject->loadTemplate($tplpath, $status);
	}

	public function showpage() {

		$this->templateObject->showPage();
	}

	public function seo($var) {

		return preg_replace("/[^A-Za-z\\-0-9]/", "", strip_tags(str_replace(" ", "-", $var)));
	}

	public function setredirect($path) {

		setcookie("nesotefw_redirect", (string) $path);
		return true;
	}

	public function getredirect() {

		if (isset($_COOKIE["nesotefw_redirect"]))
		{
			$ret = $_COOKIE["nesotefw_redirect"];
			$this->setRedirect("/");
			return $ret;
		}
		return "/";
	}

	public function arguments($argumentvalues) {

		$this->argumentvalues = $argumentvalues;
	}

	public function getparam($position) {

		if ($position > count($this->argumentvalues))
		{
			return null;
		}
		return $this->argumentvalues[$position - 1];
	}

	public function setvalue($tplvariable, $value) {

		$this->templateObject->setValue($tplvariable, $value);
	}

	public function includepage($myinstance) {

		$this->templateObject->includePage($myinstance);
	}

	public function executepage($page) {

		$maininst = (new main())->getinstance();
		$maininst->dispatch($page);
	}

	public function setloopvalue($loopvar, $arrayvar) {

		$this->templateObject->setLoopValue($loopvar, $arrayvar);
	}

	public function url($url) {

		require __DIR__ . "/script.inc.php";
		require $config_path . "system.config.php";
		$urlreplaces = [];
		$var = $_SERVER["SCRIPT_NAME"];
		$var = substr((string) $var, 0, strrpos((string) $var, "/"));
		$hostdir = "http://" . $_SERVER["HTTP_HOST"] . "" . $var . "/";
		if ($mod_rewrite)
		{
			return $hostdir . $url;
		}
		return $hostdir . "index.php?page=" . $url;
	}

	public function modelinstance($modelname) {

		require __DIR__ . "/script.inc.php";
		if (!(file_exists($model_path . $modelname . ".model.php")))
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo '<br><strong>Error: </strong>The requested model <strong>' . $modelname . "</strong> not found.";
			}
			die(0);
		}
		require_once $model_path . $modelname . ".model.php";
		$mdlobj = new $modelname();
		$mdlobj->NesoteModel($modelname);
		if (strcasecmp(get_parent_class($mdlobj), "NesoteModel") != 0)
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo '<br><strong>Error: </strong><strong>' . $modelname . "</strong> Model should extend NesoteModel.";
			}
			die(0);
		}
		return $mdlobj;
	}

	public function loadlibrary($name) {

		require __DIR__ . "/script.inc.php";
		if ($handle = opendir($library_path . "" . ucfirst((string) $name) . "/"))
		{
			while (false !== $file = readdir($handle))
			{
				if (!($file !== "." && $file !== ".."))
				{
					continue;
				}
				if (is_dir($file))
				{
					continue;
				}
				require_once $library_path . "/" . ucfirst((string) $name) . "/" . $file;
			}
			closedir($handle);
		}
	}

};


?>
