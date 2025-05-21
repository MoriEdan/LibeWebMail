<?php
class IncludeController extends NesoteController
{
	function headerAction()
	{
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

		$img=$settings->getValue("public_page_logo");
		$imgpath="logo/".$img;
		$this->setValue("imgpath",$imgpath);
		
		$engine_name=$settings->getValue("engine_name");
		$servicename=$engine_name." - Admin Area";
		$this->setValue("servicename",$servicename);
	    $portal_status=$settings->getValue("portal_status");
        $this->setValue("portal_status",$portal_status);	
		$portal_installation_url=$settings->getValue("portal_installation_url");
		$url=substr((string) $portal_installation_url,0,strrpos((string) $portal_installation_url,"/"));
		$url="$url/admin";
		$this->setValue("portal_url",$url);


	}
	function footerAction()
	{

		$year=date("Y",time());
		$this->setValue("year",$year);
	}

	function particularuserheaderAction()
	{
		$id=$this->getParam(1);
		$this->setValue("uid",$id);
	}
	function header1Action()
	{




	}
	function footer1Action()
	{

		$year=date("Y",time());
		$this->setValue("year",$year);
	}
};
?>
