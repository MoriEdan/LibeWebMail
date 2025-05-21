<?php
class AdsController extends NesoteController
{
	function adscodeAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		$this->loadLibrary("settings");
		$set=new settings("nesote_email_settings");
		$set->loadValues();
			
		$rightads_code=$set->getValue("rightads_code");
		$topads_code=$set->getValue("topads_code");
			
		$this->setValue("rightads_code",$rightads_code);
		$this->setValue("topads_code",$topads_code);
	}

	function adscodeprocessAction()
	{

		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{

			header("Location:".$this->url("message/error/1023"));
			exit(0);
		}

		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}


		$rightads_code=$_POST['rightads_code'];
		$topads_code=$_POST['topads_code'];

		$db= new NesoteDALController();
		$db->update("nesote_email_settings");
		$db->set("value=?",[$rightads_code]);
		$db->where("name='rightads_code'");
		$db->query();
		$db->set("value=?",[$topads_code]);
		$db->where("name='topads_code'");
		$db->query();
		//echo $db->getQuery();
		header("Location:".$this->url("message/success/1060/8"));
		exit(0);
	}


	function analyticscodeAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}


		$this->loadLibrary("settings");
		$set=new settings("nesote_email_settings");
		$set->loadValues();
		$analystics_code=$set->getValue("analystics_code");
		$this->setValue("analystics_code",$analystics_code);
	}

	function analyticscodeprocessAction()
	{

		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{

			header("Location:".$this->url("message/error/1023"));
			exit(0);
		}

		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		$analystics_code=trim((string) $_POST['analystics_code']);

		$db=new NesoteDALController();
		$db->update("nesote_email_settings");
		$db->set("value=?",[$analystics_code]);
		$db->where("name='analystics_code'");
		$db->query();

		header("Location:".$this->url("message/success/1020/3"));
		exit(0);
	}



	function validuser()
	{
		$username=$_COOKIE['a_username'];
		$password=$_COOKIE['a_password'];

		$db=new NesoteDALController();

		$no=$db->total("nesote_email_admin","username=? and password=? and status=?",[$username,$password,1]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }

	}
	function sponsoredlinksAction()
	{
		$server=$_SERVER['HTTP_HOST'];
		new NesoteDALController();
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$installurl=$settings->getValue("adserver_installurl");
		$authcode=$settings->getValue("adserver_authenticationcode");
		$topadcount=$settings->getValue("adserver_topadcount");
		$rightadcount=$settings->getValue("adserver_rightadcount");
		$publicadstatus=$settings->getValue("publicadstatus");
		if($server=="www.libe-search-ultimate.com" || $server=="libe-search-ultimate.com" || $server=="www.libe-search.com" || $server=="libe-search.com")
		{
			$this->setValue("installurl","YOUR LIBE ADSERVER INSTALL URL");
			$this->setValue("authcode","YOUR LIBE ADSERVER AUTHENTICATION CODE");
			$this->setValue("topadcount",$topadcount);
			$this->setValue("rightadcount",$rightadcount);
			$this->setValue("publicadstatus",$publicadstatus);
		}
		else
		{
			$this->setValue("installurl",$installurl);
			$this->setValue("authcode",$authcode);
			$this->setValue("topadcount",$topadcount);
			$this->setValue("rightadcount",$rightadcount);
			$this->setValue("publicadstatus",$publicadstatus);
		}
	}
	function sponsoredprocessAction()
	{
		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{

			header("Location:".$this->url("message/error/1023"));
			exit(0);
		}

		
			
		$installurl = trim((string) $_POST['installurl']);
		$authcode = trim((string) $_POST['authcode']);
		$topadcount = trim((string) $_POST['topadcount']);
		$rightadcount = trim((string) $_POST['rightadcount']);
		$publicadstatus=trim((string) $_POST['publicadstatus']);
		//echo $publicadstatus;
		$publicadstatus = $publicadstatus === "on" ? 1 : 0;
		//echo $installurl." ".$authcode." ".$displayurl." "; exit;
		if($installurl === "" || $authcode === "" || $topadcount === "" || $rightadcount === "")
		{
			header("Location:".$this->url("message/error/1017"));
			exit(0);
		}
		else
		{
			$db= new NesoteDALController();
			$db->update("nesote_email_settings");
			$db->set("value=?",$installurl);
			$db->where("name=?","adserver_installurl");
			$db->query();
			$db->update("nesote_email_settings");
			$db->set("value=?",$authcode);
			$db->where("name=?","adserver_authenticationcode");
			$db->query();
			$db->update("nesote_email_settings");
			$db->set("value=?",$topadcount);
			$db->where("name=?","adserver_topadcount");
			$db->query();
			$db->update("nesote_email_settings");
			$db->set("value=?",$rightadcount);
			$db->where("name=?","adserver_rightadcount");
			$db->query();
			$db->update("nesote_email_settings");
			$db->set("value=?",$publicadstatus);
			$db->where("name=?","publicadstatus");
			$db->query();
			header("Location:".$this->url("message/success/1018/8"));
			exit(0);
		}
			
	}

}
?>