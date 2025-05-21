<?php

class template {

	public $filepath;
	public $cachepath;
	public $filecontent;
	public $tplvalues = [];
	public $loopvalues = [];

	public function loadtemplate($file, $status) {

		include __DIR__ . "/script.inc.php";
		if ($status == "required")
		{
			$this->filepath = $file;
			$this->cachepath = '' . $cache_path . "/" . md5((string) $file) . ".php";
			if (file_exists($this->cachepath) == true && $cache_templates == true)
			{
				require_once $this->cachepath;
				$this->filecontent = "";
				return;
			}
			$fp = fopen($this->filepath, "r");
			$this->filecontent = fread($fp, filesize($this->filepath));
			fclose($fp);
			return;
		}
		$this->filecontent = "";
		$this->cachepath = "";
	}

	public function setvalue($variable, $value) {

		$this->tplvalues[$variable] = $value;
	}

	public function templatereplacer() {

		$tmpvar = "";
		$replacertable = [];
		preg_match_all("/(({if)|({elseif)|({fn:)|({showpage:)|({cfn:))(.+?)(\\})/i", (string) $this->filecontent, $ifarray);
		$i = 0;
		while (count($ifarray[0]) > $i)
		{
			$replacertable[$i][0] = $ifarray[0][$i];
			$replacertable[$i][1] = $ifarray[0][$i];
			$i += 1;
		}
		$arraykeys = array_keys($this->tplvalues);
		rsort($arraykeys);
		reset($arraykeys);
		$i = 0;
		while (count($arraykeys) > $i)
		{
			$this->filecontent = str_replace("{\$" . $arraykeys[$i] . "}", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo htmlentities(\$this->tplvalues['" . $arraykeys[$i] . "'],0,'UTF-8');?>", $this->filecontent);
			$this->filecontent = str_replace("{noescape:\$" . $arraykeys[$i] . "}", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo \$this->tplvalues['" . $arraykeys[$i] . "'];?>", $this->filecontent);
			$this->filecontent = str_replace("{seo:\$" . $arraykeys[$i] . "}", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo preg_replace('/[^A-Za-z\\-0-9]/', '',strip_tags(str_replace(\" \",\"-\",\$this->tplvalues['" . $arraykeys[$i] . "'])));?>", $this->filecontent);
			$j = 0;
			while (count($replacertable) > $j)
			{
				if (str_starts_with($replacertable[$j][0], "{showpage:"))
				{
					$tmpvar = str_replace("\$" . $arraykeys[$i], "\".\$this->tplvalues['" . $arraykeys[$i] . "'].\"", $replacertable[$j][1]);
				}
				 else 
				{
					$tmpvar = str_replace("\$" . $arraykeys[$i], "\$this->tplvalues['" . $arraykeys[$i] . "']", $replacertable[$j][1]);
				}
				if ($tmpvar != $replacertable[$j][0])
				{
					$replacertable[$j][1] = $tmpvar;
				}
				$j += 1;
			}
			$i++;
		}
		$replacertablecount = count($replacertable);
		$this->filecontent = preg_replace("/(\\{loopstart:)(\\w+)(:)(\\d+)(\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ for(\$\$2=0; \$\$2<\$4 && \$\$2 < count(\$this->loopvalues['\$2']); \$\$2++){?>", (string) $this->filecontent);
		$this->filecontent = preg_replace("/(\\{loopend:)(\\w+)(\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ } ?>", (string) $this->filecontent);
		$this->filecontent = preg_replace("/(\\{loop:)(\\w+)([+|-]*)(\\d*)(\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo \$\$2 \$3 \$4 ; ?>", (string) $this->filecontent);
		$this->filecontent = preg_replace("/(\\{include:)(.+?)(\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ require(\"\$2\"); ?>", (string) $this->filecontent);
		$arraykeys = array_keys($this->loopvalues);
		$i = 0;
		while (count($arraykeys) > $i)
		{
			$this->filecontent = preg_replace("/(\\{\\\$" . $arraykeys[$i] . "\\[)(\\d+)(\\]\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo htmlentities(\$this->loopvalues['" . $arraykeys[$i] . "'][\$" . $arraykeys[$i] . "]" . ('[$2]') . ",0,'UTF-8');?>", (string) $this->filecontent);
			$this->filecontent = preg_replace("/(\\{noescape:\\\$" . $arraykeys[$i] . "\\[)(\\d+)(\\]\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo \$this->loopvalues['" . $arraykeys[$i] . "'][\$" . $arraykeys[$i] . "]" . ('[$2]') . ";?>", (string) $this->filecontent);
			$this->filecontent = preg_replace("/(\\{seo:\\\$" . $arraykeys[$i] . "\\[)(\\d+)(\\]\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo preg_replace('/[^A-Za-z\\-0-9]/', '',strip_tags(str_replace(\" \",\"-\",\$this->loopvalues['" . $arraykeys[$i] . "'][\$" . $arraykeys[$i] . "]" . ('[$2]') . ")));?>", (string) $this->filecontent);
			$j = 0;
			while ($replacertablecount > $j)
			{
				if (str_starts_with($replacertable[$j][0], "{showpage:"))
				{
					$tmpvar = preg_replace("/(\\\$" . $arraykeys[$i] . "\\[)(\\d+)(\\])/i", "\".\$this->loopvalues['" . $arraykeys[$i] . "'][\$" . $arraykeys[$i] . "]" . ('[$2]') . ".\"", (string) $replacertable[$j][1]);
				}
				 else 
				{
					$tmpvar = preg_replace("/(\\\$" . $arraykeys[$i] . "\\[)(\\d+)(\\])/i", "\$this->loopvalues['" . $arraykeys[$i] . "'][\$" . $arraykeys[$i] . "]" . ('[$2]') . "", (string) $replacertable[$j][1]);
				}
				if ($tmpvar != $replacertable[$j][0])
				{
					$replacertable[$j][1] = $tmpvar;
				}
				$j += 1;
			}
			$i++;
		}
		$j = 0;
		while ($replacertablecount > $j)
		{
			$replacertable[$j][1] = str_replace("{if", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ if", $replacertable[$j][1]);
			$replacertable[$j][1] = str_replace("{elseif", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ } elseif", $replacertable[$j][1]);
			$replacertable[$j][1] = preg_replace("/(\\{fn:)(.+?)(\\)\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo \$2);  ?>", $replacertable[$j][1]);
			$replacertable[$j][1] = preg_replace("/(\\{cfn:)(.+?)(\\)\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ echo \$controllerinstance->\$2);  ?>", (string) $replacertable[$j][1]);
			$replacertable[$j][1] = str_replace(")}", ") { ?>", $replacertable[$j][1]);
			$replacertable[$j][1] = preg_replace("/(\\{showpage:)(.+?)(\\})/i", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ \$this->executePage(\"\$2\"); ?>", $replacertable[$j][1]);
			$j += 1;
		}
		$this->filecontent = str_replace("{else}", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ } else { ?>", $this->filecontent);
		$i = 0;
		while ($replacertablecount > $i)
		{
			$this->filecontent = str_replace($replacertable[$i][0], $replacertable[$i][1], $this->filecontent);
			$i += 1;
		}
		$this->filecontent = str_replace("{endif}", "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ } ?>", $this->filecontent);
		require __DIR__ . "/script.inc.php";
		require $config_path . "system.config.php";
		$urlreplaces = [];
		$var = $_SERVER["SCRIPT_NAME"];
		$var = substr((string) $var, 0, strrpos((string) $var, "/"));
		$currenturl = "http://" . $_SERVER["HTTP_HOST"] . "" . $var . "/";
		$hostdir = $currenturl;
		if ($mod_rewrite)
		{
			$this->filecontent = preg_replace("/(\\{url:\\()([^\\}]*)(\\)\\})/ix", $hostdir . str_replace(" ", " ", '$2'), $this->filecontent);
		}
		 else 
		{
			$this->filecontent = preg_replace("/(\\{url:\\()([^\\}]*)(\\)\\})/ix", $hostdir . "index.php?page=" . str_replace(" ", " ", '$2'), $this->filecontent);
		}
		preg_match_all("/\\{validate:(\\w+):(\\w+)\\((.+?)\\)\\}/i", (string) $this->filecontent, $validate);
		$validatecount = count($validate[1]);
		$i = 0;
		while ($validatecount > $i)
		{
			if ($validate[2][$i] == "isNotNull" || $validate[2][$i] == "isEmail" || $validate[2][$i] == "isPositive") {
                if (preg_match("/(.*),(.*)/i", $validate[3][$i], $messagepattern))
				{
					$message = trim($messagepattern[2]);
					if ($message[0] != "'")
					{
						$message = str_replace("'", "\\'", $message);
						if ($message[0] == "\"")
						{
							$message[0] = "'";
							$msglen = strlen($message);
							$message[$msglen - 1] = "'";
						}
						 else 
						{
							$message = "'" . $message . "'";
						}
						$messagepattern[1] = str_replace("'", "\\'", $messagepattern[1]);
						$messagepattern[1] = str_replace("\"", "'", $messagepattern[1]);
						$argstring = $messagepattern[1] . "," . $message;
						$validate[3][$i] = $argstring;
					}
				}
            } elseif ($validate[2][$i] == "isNotShort" || $validate[2][$i] == "isOverMin" || $validate[2][$i] == "isSame") {
                if (preg_match("/(.*),(.*),(.*)/i", $validate[3][$i], $messagepattern))
					{
						$message = trim($messagepattern[3]);
						if ($message[0] != "'")
						{
							$message = str_replace("'", "\\'", $message);
							if ($message[0] == "\"")
							{
								$message[0] = "'";
								$msglen = strlen($message);
								$message[$msglen - 1] = "'";
							}
							 else 
							{
								$message = "'" . $message . "'";
							}
							$messagepattern[1] = str_replace("'", "\\'", $messagepattern[1]);
							$messagepattern[1] = str_replace("\"", "'", $messagepattern[1]);
							$messagepattern[2] = str_replace("'", "\\'", $messagepattern[2]);
							$messagepattern[2] = str_replace("\"", "'", $messagepattern[2]);
							$argstring = $messagepattern[1] . "," . $messagepattern[2] . "," . $message;
							$validate[3][$i] = $argstring;
						}
					}
            }
			$i++;
		}
		preg_match_all("/\\{formstart:(\\w+):(.+?):(\\w+)\\}/i", (string) $this->filecontent, $pageforms);
		$formcount = count($pageforms[1]);
		$formobj = [];
		$i = 0;
		while ($formcount > $i)
		{
			$formname = $pageforms[1][$i];
			$formobj[$i] = new NesoteForm($formname, $pageforms[2][$i], $pageforms[3][$i]);
			$j = 0;
			while ($validatecount > $j)
			{
				if ($formname == $validate[1][$j])
				{
					$str = "\$formobj[\$i]->\$validate[2][\$j](" . $validate[3][$j] . ");";
					eval($str);
				}
				$j++;
			}
			$this->filecontent = str_replace($pageforms[0][$i], $formobj[$i]->formStart(), $this->filecontent);
			$i++;
		}
		$i = 0;
		while ($validatecount > $i)
		{
			$this->filecontent = str_replace($validate[0][$i], "", $this->filecontent);
			$i++;
		}
		$this->filecontent = preg_replace("/\\{formend:\\w+\\}/i", "</form>", (string) $this->filecontent);
	}

	public function executepage($page) {

		$maininst = (new main())->getinstance();
		$maininst->dispatch($page);
	}

	public function setloopvalue($loopvar, $arrayvar) {

		$this->loopvalues[$loopvar] = $arrayvar;
	}

	public function validatelicense($license_key) {

		/* $scriptcode = "webmailadv";
		$lic_data = "";
		$php_self = $_SERVER["HTTP_HOST"];
		$serverarray[0] = "licval.liberyus.com";
		$serverarray[1] = "www.nesotelvs1.info";
		$serverarray[2] = "www.liberyus.com";
		$serverarray[3] = "www.nesotelvs2.info";
		$lcount = 0;
		while (count($serverarray) > $lcount)
		{
			$lic_data = "";
			if ($fp_license = fopen("http://" . $serverarray[$lcount] . "/validate_license/" . $scriptcode . "/" . ('' . $license_key) . "/" . ('' . $php_self), "r"))
			{
				while (!(feof($fp_license)))
				{
					$lic_data .= fgetc($fp_license);
					continue;
				}
				fclose($fp_license);
				if ($lic_data == "1")
				{
					return true;
				}
			}
			if ($lic_data != "0" && function_exists("curl_init"))
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://" . $serverarray[$lcount] . "/validate_license/" . $scriptcode . "/" . ('' . $license_key) . "/" . ('' . $php_self));
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$content = curl_exec($ch);
				curl_close($ch);
				$lic_data = $content;
				if ($lic_data == "1")
				{
					return true;
				}
			}
			if ($lic_data == "")
			{
				$lic_data = "not available";
			}
			if ($lic_data == "0")
			{
				echo "<br>Failed to validate the license key. Please verify the license key that you have entered in your configuration file.<br>";
				die(0);
			}
			$lcount++;
			continue;
		}
		echo "<br>An error has occurred while validating your license. Please click the refresh button of your browser and try again. If you repeatedly getting the error please contact liberyus support desk.<br>";
		exit(); 
		return; */
		
		return true;
	}

	public function includepage($controllerinstance) {

		require __DIR__ . "/script.inc.php";
		require $config_path . "system.config.php";
		$checklicense = 0;
		$pagename = "";
		if (isset($_GET["page"]))
		{
			$pagename = $_GET["page"];
		}
		if (substr_count((string) $pagename, "user/addnewuser") > 0) {
            $checklicense = 1;
        } elseif (substr_count((string) $pagename, "user/userview") > 0) {
            $checklicense = 1;
        } elseif (substr_count((string) $pagename, "user/newreservedmail") > 0) {
            $checklicense = 1;
        } elseif (substr_count((string) $pagename, "user/spamsettings") > 0) {
            $checklicense = 1;
        } elseif (substr_count((string) $pagename, "user/newspam") > 0) {
            $checklicense = 1;
        } elseif (substr_count((string) $pagename, "settings/basicsettings") > 0) {
            $checklicense = 1;
        } elseif (substr_count((string) $pagename, "settings/emailsettings") > 0) {
            $checklicense = 1;
        }
		if ($checklicense == 1)
		{
			$this->validateLicense($license_key);
		}
		if (file_exists($this->cachepath) == true && $cache_templates == true)
		{
			require $this->cachepath;
			return;
		}
		$this->templateReplacer();
		if ($cache_templates == true)
		{
			if ($fp = fopen($this->cachepath, "w"))
			{
				fwrite($fp, (string) $this->filecontent);
				fclose($fp);
			}
			 else 
			{
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo "<br> <b>Error: Cannot write into CACHE folder:</b> Change permission of the <b>" . substr((string) $cache_path, 0, -1) . "</b> folder to writable by script.";
				}
				die(0);
			}
			require_once $this->cachepath;
			return;
		}
		eval("?>" . $this->filecontent . "<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	2.0.0.3
* @	Author			:	DeZender
* @	Release on		:	06.05.2013
* @	Official site	:	http://DeZender.Net
*
*/ ");
	}

	public function showpage() {

		echo $this->filecontent;
	}

	public function getpage() {

		return $this->filecontent;
	}

};


?>
