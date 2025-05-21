<?php
class HomeController extends NesoteController
{
	
function ControlpanelAction()
	{
		$val=$this->validuser();
		if($val!=true)
		{
			header("Location:".$this->url("index/index"));
			exit(0);

		}
	}
	
	function Controlpanel1Action()
	{
		$val=$this->validuser();
		if($val!=true)
		{
			header("Location:".$this->url("index/index"));
			exit(0);

		}
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

};
?>