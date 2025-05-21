<?php
class CalendarController extends NesoteController
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

	function calendarAction()
	{

		$select=new NesoteDALController();
		$select->select("nesote_email_calendar_settings");
		$select->fields("value");
		$select->where("name=?",\CALENDAR);
		$result=$select->query();
		$rs=$select->fetchRow($result);
		$this->setValue("calendar_enable",$rs[0]);
		
		$select->select("nesote_email_calendar_settings");
		$select->fields("value");
		$select->where("name=?",\EMAIL_REMAINDER);
		$result=$select->query();
		$rs=$select->fetchRow($result);
		$this->setValue("emailremainder",$rs[0]);
			
		$select->select("nesote_email_calendar_settings");
		$select->fields("value");
		$select->where("name=?",\VIEW_EVENT);
		$result1=$select->query();
		$rs1=$select->fetchRow($result1);
		$this->setValue("viewevent",$rs1[0]);


		if($_POST !== [])
		{

			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )			{

				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}

			$calendar_enable=$_POST['calendar_enable'];
			$emailremainder=$_POST['emailremainder'];
			$viewevent=$_POST['viewevent'];
			
			$db=new NesoteDALController();
			$db->update("nesote_email_calendar_settings");
			$db->set("value=?",[$calendar_enable]);
			$db->where("name=?",\CALENDAR);
			$db->query();
			
			$db->update("nesote_email_calendar_settings");
			$db->set("value=?",[$emailremainder]);
			$db->where("name=?",\EMAIL_REMAINDER);
			$db->query();//echo $db->getQuery();
			
			$db->update("nesote_email_calendar_settings");
			$db->set("value=?",[$viewevent]);
			$db->where("name=?",\VIEW_EVENT);
			$db->query();//echo $db->getQuery();
			 
				header("Location:".$this->url("message/success/1600/14"));//1 for path creation in message/success controller
				exit(0);

		}


	}

};
?>