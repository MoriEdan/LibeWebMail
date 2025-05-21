<?php
class BirthdayController extends NesoteController
{
	function validuser()
	{
		$username=$_COOKIE['a_username'];
		$password=$_COOKIE['a_password'];

		$db=new NesoteDALController();

		$no=$db->total("nesote_email_admin","username=? and password=?",[$username,$password]);
		if ($no!=0) {
            return true;
        } else {
            return false;
        }

	}

	function file_extension($filename)
	{
		$path_info = pathinfo((string) $filename);
		return $path_info['extension'];
	}
	
 function birthdaymailsettingsAction()
 {
 	
  if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

        $this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

		
			$birthdaysubject=$settings->getValue("birthday_email_subject");
		
			$birthdaybody=$settings->getValue("birthday_email_body");
			$this->setValue("birthdaysubject",$birthdaysubject);
			$this->setValue("birthdaybody",$birthdaybody);
		
 	
 }
 function birthdaymailsettingsprocessAction()
 {
 
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{

				header("Location:".$this->url("message/error/1023"));
				exit(0);
			}
			$birthdaysubject=$_POST['birthdaysubject'];
			$birthdaybody=$_POST['birthdaybody'];
			
			if ($birthdaysubject=="") {
                header("Location:".$this->url("message/error/1500"));
                exit;
            } elseif ($birthdaybody=="") {
                header("Location:".$this->url("message/error/1501"));
                exit;
            }
			$update=new NesoteDALController();
			$update->update("nesote_email_settings");
			$update->set("value=?",[$birthdaysubject]);
			$update->where("name='birthday_email_subject'");
			$update->query();

			$update=new NesoteDALController();
			$update->update("nesote_email_settings");
			$update->set("value=?",[$birthdaybody]);
			$update->where("name='birthday_email_body'");
			$update->query();

			
			header("Location:".$this->url("message/success/1505/10"));
			exit(0);

		}
 }

 function birthdayAction()
 {   
 	session_start();
   
 	        $this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			
 	   
		 $settings->getValue("adminemail");

 	 $birthday_mail= $settings->getValue("birthday_mail");
 		$extension=$this->getextension();
 	if($birthday_mail==1)
 	{ 	
		 	$time=time();
				
		 	$current_month=date("m",$time);
		 	$current_day=date("d",$time);//echo $current_day."+";
                        $current_year=date("Y",$time);
                        $session_time=mktime(0,0,0,$current_month,$current_day,$current_year);
                        
                      // $session_time="10/01/2012";
		 	$select=new NesoteDALController();
		 	$select->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
		 	$select->fields("b.dateofbirth,a.username,a.name,a.id");
		 	$select->where("status=?",1);
		 	$res=$select->query();
		 	//unset($_SESSION['birthday']);
          if(($_SESSION['birthday']=="")||($_SESSION['birthday']!=$session_time))
		  {
		 	while($result=$select->fetchRow($res))
		 	{
		 		$userid=$result[3];
		 		
		 	//	$curnt_user_time=$this->getusertime($userid,$result[1]);
		      //  $crnt_user_month=date("m",$curnt_user_time);//echo $crnt_user_month."--";
		        //$crnt_user_day=date("d",$curnt_user_time); //echo $crnt_user_day."+++";
		 		
		 		 $bday_month=date("m",$result[0]); 
		 		 $bday_day=date("d",$result[0]);//echo $bday_day."/";
		 		if(($bday_month === $current_month) &&($bday_day === $current_day))
		 		{
		 		
		 			$bday_subject=$settings->getValue("birthday_email_subject");
		 					 				 			
		 			$bday_body=$settings->getValue("birthday_email_body");
		 			$name=ucfirst((string) $result[2]);
		 			$bday_body=str_replace("{name}","$name","$bday_body");
		 			$to=$result[1].$extension;
		 			 

   $this->birthday_smtp($to,$bday_subject,$bday_body,$userid);
                                    
		 		}		 			 			 		
		 	}
		 	
		 	$_SESSION['birthday']=$session_time;
                   }
 	}
 	 echo ""; exit;         
 }	

 
function birthday_smtp($to,$subject,$body,$userid)
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

		$db=new NesoteDALController();
	
		$settings->getValue("SMTP_host");
	
		$settings->getValue("SMTP_port");

	    $settings->getValue("SMTP_username");

		$settings->getValue("SMTP_password");

		$admin_email=$settings->getValue("adminemail");

		$settings->getValue("engine_name");
		

		if($to!='')
		{

$address=$to;
			//foreach ($to as $address)
			//{ echo "2";exit;

				if($address!='')
				{

					$address2=explode("@",(string) $address);//print_r($address2);
                    $username=$address2[0];//echo substr_replace($username,"",-17); 
                   // $username=substr_replace($username,"",-17);echo $username;

                    $tablenumber=$this->tableid($username);
					$time=$this->gettimeval($userid,$address2[0]);
                    //$time=$this->getusertime($userid,$username);

					$db->insert("nesote_email_inbox_$tablenumber");
					$db->fields("userid,from_list,to_list,subject,body,time,status");
					$db->values([$userid,$admin_email,$to,$subject,$body,$time,1]);
					$result=$db->query();//echo $db->getQuery();
					$last=$db->lastInsert();

					$var=time().$userid.$last;
					$ext=$this->getextension();
					$message_id="<".md5($var).$ext.">";

					$mail_references="<references><item><mailid>$last</mailid><folderid>1</folderid></item></references>";
                    $md5_reference=md5($mail_references);

					$db->update("nesote_email_inbox_$tablenumber");
					$db->set("mail_references=?,message_id=?,md5_references=?",[$mail_references,$message_id,$md5_reference]);
					$db->where("id=?",$last);
					$res1=$db->query();

				}

			//}echo hii;

			//header("Location:".$this->url("message/success/1074/8"));exit;
		}

return "";

	}
 
function gettimeval($userid,$username)
	{

		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		new NesoteDALController();
		
		$position=$settings->getValue("time_zone_postion");

	
		$hour=$settings->getValue("time_zone_hour");


		$min=$settings->getValue("time_zone_mint");

		$diff=((3600*$hour)+(60*$min));
        $diff = $position == "Behind" ? -$diff : $diff;
		return time()-$diff;
	}

	function getusertime($userid,$username)
	{

		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		new NesoteDALController();
		
		$position=$settings->getValue("time_zone_postion");

	
		$hour=$settings->getValue("time_zone_hour");


		$min=$settings->getValue("time_zone_mint");

		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=time()-$diff;
		//echo $ts."++++++";
		$db3= new NesoteDALController();
		$db3->select("nesote_email_usersettings");
		$db3->fields("time_zone");
		$db3->where("userid=?",[$userid]);
		$res3=$db3->query();//echo $db3->getQuery();
		$row3=$db3->fetchRow($res3);
		//echo "**".$row3[0];
		$db3->select("nesote_email_time_zone");
		$db3->fields("value");
		$db3->where("id=?",[$row3[0]]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);
		$timezone=$row3[0];
		//echo $timezone."__________";
		$sign=trim((string) $timezone[0]);
		$timezone1=substr((string) $timezone,1);

		$timezone1=explode(":",$timezone1);
		$newtimezone=($timezone1[0]*60*60)+($timezone1[1]*60);
        if ($sign === "-") {
            $newtimezone=-$newtimezone;
        }
		return $ts+$newtimezone;
	}

	
	
	function eventsettingsAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("*");
		$res=$db->query();
		//echo $db->getQuery();
		while($row=$res->fetchRow())
		{
			//echo $row[2];
			$this->setValue("$row[1]","$row[2]");
		}
		
		
	}
	
	function eventsettingsprocessAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		$tip=$_POST['tip'];
		$birthdaymail=$_POST['bday'];
		
		$update=new NesoteDALController();
		
		$update->update("nesote_email_settings");
		
		$update->set("value=?",[$tip]);
		$update->where("name='tip_ofthe_day'");
		$update->query();
		
		$update->set("value=?",[$birthdaymail]);
		$update->where("name='birthday_mail'");
		$update->query();
		
		header("Location:".$this->url("message/success/1506/11"));
		exit(0);
	}

	function getextension()
	{
		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='emailextension'");
		$result=$db->query();
		$row=$db->fetchRow($result);
		if (stristr(trim((string) $row[0]),"@")!="") {
            return $row[0];
        } else {
            return htmlentities("@".$row[0]);
        }
	}
	function tableid($username)
    {
		$user_name=$username;
		include(__DIR__ . "/../config.php");
		$number=$cluster_factor;
		
		$user_name=trim((string) $user_name);
		$mdsuser_name=md5($user_name);
		$mdsuser_name=str_replace("a","",$mdsuser_name);
		$mdsuser_name=str_replace("b","",$mdsuser_name);
		$mdsuser_name=str_replace("c","",$mdsuser_name);
		$mdsuser_name=str_replace("d","",$mdsuser_name);
		$mdsuser_name=str_replace("e","",$mdsuser_name);
		$mdsuser_name=str_replace("f","",$mdsuser_name);
		
		$digits=substr($mdsuser_name,-6);
		
		$modlusnumber=$digits % $number;
		$modlusnumber += 1;
		$numbers[$modlusnumber]++;
		return $modlusnumber;
    } 
};

?>