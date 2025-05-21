<?php
class MessageController extends NesoteController
{
	function errorAction()
	{
		include(__DIR__ . "/messages.en.inc.php");

		$i=$this->getParam(1);
		if($i>0&&$msg[$i]!="")
		{
			if ($i==1080) {
                $i1=$this->getParam(2);
                $msg=str_replace('{extension}',$i1,$msg[$i]);
                $this->setValue("error",$msg);
            } elseif ($i==1134) {
                $i1=$this->getParam(2);
                $msg=str_replace('{length}',$i1,$msg[$i]);
                $this->setValue("error",$msg);
            } else {
                $this->setValue("error",$msg[$i]);
            }


		}
		else {
            $this->setValue("error","Invalid error code!");
        }
		$this->setValue("path",$this->url("home/controlpanel"));
	}

	function successAction()
	{
		include(__DIR__ . "/messages.en.inc.php");
		$i=$this->getParam(1);
		$flag=$this->getParam(2);
		if ($i>0&&$msg[$i]!="") {
            $this->setValue("success",$msg[$i]);
        } else {
            $this->setValue("error","Invalid message code!");
        }
		if ($flag==1) {
            $path=$this->url("user/userview");
        }
		if ($flag==2) {
            $path=$this->url("user/reservedmails");
        }
		if ($flag==3) {
            $path=$this->url("themes/manage");
        }
		if($flag==4)
		{
			$userid=$this->getParam(3);
			if(isset($userid))
			{
				if ($userid=="group") {
                    $path=$this->url("user/groups");
                } else
				{
					$path=$this->url("user/contacts/0/$userid");

					$path=$this->url("user/contacts/0");
				}
			}
			else {
                $path=$this->url("user/contacts/0");
            }
		}
		if ($flag==5) {
            $path=$this->url("language/manage");
        }
		if ($flag==6) {
            $path=$this->url("user/spamsettings");
        }
		if ($flag==7) {
            $path=$this->url("user/clientlogs");
        }
		if ($flag==8) {
            $path=$this->url("settings/basicsettings");
        }
        if ($flag==9) {
            $path=$this->url("tipoftheday/view_tip_ofthe_day");
        }
        if ($flag==10) {
            $path=$this->url("birthday/birthdaymailsettings");
        }
        if ($flag==11) {
            $path=$this->url("birthday/eventsettings");
        }
        if ($flag==12) {
            $path=$this->url("chatuser/chatsettings");
        }
        if ($flag==13) {
            $path=$this->url("shortcuts/shortcuts");
        }
        if ($flag==14) {
            $path=$this->url("calendar/calendar");
        }
        if ($flag==15) {
            $path=$this->url("todolist/todolist");
        }

		$this->setValue("path",$path);
	}
}
?>
