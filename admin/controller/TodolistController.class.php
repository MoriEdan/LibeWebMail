<?php
class TodolistController extends NesoteController
{
	
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

	function todolistAction()
	{

		$select=new NesoteDALController();
		$select->select("nesote_email_settings");
		$select->fields("value");
		$select->where("name=?",\TODOLIST);
		$result=$select->query();
		$rs=$select->fetchRow($result);
		$this->setValue("todolist",$rs[0]);


		if($_POST !== [])
		{

			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{

				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}

			$todolist=$_POST['todo'];
			
			$db=new NesoteDALController();
			$db->update("nesote_email_settings");
			$db->set("value=?",[$todolist]);
			$db->where("name=?",\TODOLIST);
			$db->query();
			 
				header("Location:".$this->url("message/success/1508/15"));//1 for path creation in message/success controller
				exit(0);

		}


	}

};
?>