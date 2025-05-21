<?php

//echo $config_path;

class AccountController extends NesoteController
{
	function helpAction()
	{
		$id=$this->getParam(1);
		$this->setValue("helpid",$id);
		//echo $id;
		$year=date("Y",time());
		$this->setValue("year",$year);
	}

};
?>
