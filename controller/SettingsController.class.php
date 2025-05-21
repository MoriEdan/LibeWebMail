<?php
class SettingsController extends NesoteController
{

	
	
	function getloggedheader()
		{
			$io_username=$_COOKIE["e_username"];
			$io_password=$_COOKIE["e_password"];
			$lang_code=$_COOKIE["lang_mail"];
		//echo $lang_code; exit;
			$io_password=$_COOKIE["e_password"];	
			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			$portal_status=$settings->getValue("portal_status");
		if($portal_status==1)
		{
			if($io_username!=""||$io_password!="")
			{
				
				$db= new NesoteDALController();
				$db->select("nesote_liberyus_users");
				$db->fields("*");
				$db->where("username=? and password=?",[$io_username,$io_password]);
				$res=$db->query();
				$result=$db->fetchRow($res);

				$this->loadLibrary('Settings');
				$settings=new Settings('nesote_email_settings');
				$settings->loadValues();
				$portal_installation_url=$settings->getValue("portal_installation_url");
				
					$servicekey=strrev((string) $portal_installation_url); 
					$servicekey=substr($servicekey,0,strpos($servicekey,"/"));
					$servicekey=strrev($servicekey);
					
				$portal_installation_url=substr((string) $portal_installation_url,0,strrpos((string) $portal_installation_url,"/"));
				
				
				
				//$lang_code="engl";	
				$url=$portal_installation_url."/index.php?page=index/loggedcommonheader/".$lang_code."/".$servicekey."/".$result[0];

				//echo $url;
				//exit;
				
				//sleep(2);

				if (function_exists('curl_init'))
				{

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL,$url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$xmldata = curl_exec($ch);
					curl_close($ch);


				}
				elseif($fp=fopen($url,"r"))
				{
					while(!feof($fp))
					{
						$xmldata.=fgetc($fp);
					}
					fclose($fp);

				}
				else
				{

					echo "Error in file open, please enable curl or fopen";
					exit;
				}
				
				
				
				echo $xmldata;
			}//if($io_username!=""||$io_password!="")
			else
			{
				$this->loadLibrary('Settings');
				$settings=new Settings('nesote_email_settings');
				$portal_installation_url=$settings->getValue("portal_installation_url");
				
					$servicekey=strrev((string) $portal_installation_url); 
					$servicekey=substr($servicekey,0,strpos($servicekey,"/"));
					$servicekey=strrev($servicekey);
					
				$portal_installation_url=substr((string) $portal_installation_url,0,strrpos((string) $portal_installation_url,"/"));
				
				$url=$portal_installation_url."/index.php?page=index/commonheader/".$lang_code."/".$servicekey;

				sleep(2);
				
				//echo $url;
				//exit;
				
				if (function_exists('curl_init'))
				{

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL,$url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$xmldata = curl_exec($ch);
					curl_close($ch);


				}
				elseif($fp=fopen($url,"r"))
				{
					while(!feof($fp))
					{
						$xmldata.=fgetc($fp);
					}
					fclose($fp);

				}
				else
				{
					

					echo "Error in file open, please enable curl or fopen";
					exit;
				}
				echo $xmldata;
			}//else
				
		}

		}//function getlogedheader()
	function settingsAction()
	{
		$folder=$this->getParam(1);
		$this->setValue("folder",$folder);
	}
	function setthemeAction(): never
	{
			$userid=$this->getId();
			$themeid=$this->getParam(1);
			$db=new NesoteDALController();
			$db->update("nesote_email_usersettings");
			$db->set("theme_id=?",[$themeid]);
			$db->where("userid=?",[$userid]);
			$db->query();
			echo $db->getQuery();
			exit;

	}
	function portal_editprofileAction()
	{
	    $valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		
		$userid=$this->getId();$msg="";
		$lang_code=$_COOKIE['lang_mail'];
		if($lang_code=="")
		{
			$lang_code='eng';
			setcookie("lang_mail",$lang_code, ['expires' => "0", 'path' => "/"]);
		}
        $lang_id=$this->getlang_id($lang_code);
		$msg=$this->getParam(1);//echo $msg;
		//$this->setValue("folder",$folder);
		$this->setValue("msg",$msg);

		$select=new NesoteDALController();
		$select->select("nesote_email_usersettings");
		$select->fields("theme_id");
		$select->where("userid=?",$userid);
		$result=$select->query();//echo $select->getQuery();
		$res=$select->fetchRow($result);
		$style_id=$res[0];
		if($style_id=="")
		{
			
			$select->select("nesote_email_settings");
			$select->fields("value");
			$select->where("name='themes'");
			$result=$select->query();//echo $select->getQuery();
			$res=$select->fetchRow($result);
			$style_id=$res[0];
				
				
		}
		
		$select->select("nesote_email_themes");
		$select->fields("name,style");
		$select->where("id=?",$style_id);
		$result=$select->query();
		$theme=$select->fetchRow($result);

		$this->setValue("style",$theme[1]);
		$this->setValue("uid",$userid);$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("month_id,message");
		$db->where("lang_id=?",[$lang_id]);
		$result1=$db->query();
		$this->setLoopValue("month",$result1->getResult());



		$uname=$_COOKIE['e_username'];
		$pwd=$_COOKIE['e_password'];
		
		$select->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
		$select->fields("a.name,b.sex,b.dateofbirth,b.country");
		$select->where("a.id=? and a.username=? and a.password=? and a.status=? and a.id=b.userid",[$userid,$uname,$pwd,1]);
		$result=$select->query();//echo $select->getQuery();
		$this->setLoopValue("profile",$result->getResult());
		$rs=$select->fetchRow($result);
		$select->numRows($result);
		$dob=date("d/n/Y",$rs[2]);
		$dob1=explode("/",$dob);
		$day=$dob1[0];$mnth=$dob1[1];$year=$dob1[2];
		$this->setValue("dd",$day);$this->setValue("mn",$mnth);$this->setValue("yr123",$year);

		$male=$this->getmessage(137);$female=$this->getmessage(138);
		$this->setValue("male",$male);$this->setValue("female",$female);
		$year=date("Y",time());
		for($i=$year,$j=0;$i>=1900;$i--,$j++)
		{
			$yr[$j][0]=$i;
		}
		$this->setLoopValue("yrs",$yr);
			
		for($i1=01,$j1=0;$i1<=31;$i1++,$j1++)
		{
			if ($i1<10) {
                $i1="0".$i1;
            }
			$days[$j1][0]=$i1;
		}
		$this->setLoopValue("days",$days);
			
		
		$select->select("nesote_email_country");
		$select->fields("name");
		$select->order("name asc");
		$result=$select->query();
		$this->setLoopValue("country",$result->getResult());


		if($_POST !== [])
		{



			$userid=$this->getId();$flag=1;$msg="";

//			$fname=trim($_POST['fname']);
//			if($fname=="")
//			{
//				$flag=0;
//				if($msg=="")
//				$msg=$this->getmessage(112);
//			}
//			$lname=trim($_POST['lname']);
//			if($lname=="")
//			{
//				$flag=0;
//				if($msg=="")
//				$msg=$this->getmessage(113);
//			}
             $name=$_POST['name'];
		    if($name=="")
			{
				$flag=0;
				$msg=$this->getmessage(669);
			}
			$gender=$_POST['gender'];
			if($gender=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(127);
                }
			}
			$dd=$_POST['day'];//echo $dd;
			if($dd=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(128);
                }
			}
			$mn=$_POST['month'];//echo $mn;
			if($mn=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(129);
                }
			}
			$yr=$_POST['year'];//echo $yr;
			if($yr=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(130);
                }
			}

			$country=$_POST['country'];
			if($country=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(131);
                }
			}
//			$alternamteemail=$_POST['alternatemail'];
//
//			if($alternamteemail!="")
//			{
//				$rslt=$this->isValid($alternamteemail);
//				if($rslt=="true")
//				{
//					$flag=1;
//				}
//				else
//				{
//					$flag=0;
//					if($msg=="")
//					$msg=$this->getmessage(159);
//				}
//			}

			$uname=$_COOKIE['e_username'];
			$pwd=$_COOKIE['e_password'];
			if($flag==1)
			{

				if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
				{

					$msg=$this->getmessage(340);
					header("Location:".$this->url("settings/webmailsettings/$msg"));
					exit(0);
				}


				if ($mn<10) {
                    $mm="0".$mn;
                }
				$dob=mktime(0,0,0,$mm,$dd,$yr);//echo $dob;exit;

				
				$select->update("nesote_liberyus_users");
				$select->set("name=?",[$name]);
				$select->where("id=? and username=? and password=? and status=?",[$userid,$uname,$pwd,1]);
				$select->query();//echo $db->getQuery();exit;
				$select->update("nesote_email_usersettings");
				$select->set("sex=?,dateofbirth=?,country=?",[$gender,$dob,$country]);
				$select->where("userid=?",[$userid]);
				$select->query();//ech


				$username=$this->getusername($userid);
				$this->saveLogs("Edit Profile","$username has updated his/her profile");

				$msg=$this->getmessage(362);
			}

			header("Location:".$this->url("settings/portal_editprofile/$msg"));
			exit(0);
		}
	}
	function editprofileAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
		
		$userid=$this->getId();$msg="";
		$lang_code=$_COOKIE['lang_mail'];
		if($lang_code=="")
		{
			$lang_code='eng';
			setcookie("lang_mail",$lang_code, ['expires' => "0", 'path' => "/"]);
		}
		$lang_id=$this->getlang_id($lang_code);

		$msg=$this->getParam(1);
		$this->setValue("msg",$msg);

		$select=new NesoteDALController();
		$this->setValue("uid",$userid);$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("month_id,message");
		$db->where("lang_id=?",[$lang_id]);
		$result1=$db->query();
		$this->setLoopValue("month",$result1->getResult());



		$uname=$_COOKIE['e_username'];
		$pwd=$_COOKIE['e_password'];
		
		$select->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
		$select->fields("a.name,b.sex,b.dateofbirth,b.country,b.alternate_email");
		$select->where("a.id=? and a.username=? and a.password=? and a.status=? and a.id=b.userid",[$userid,$uname,$pwd,1]);
		$result=$select->query();//echo $select->getQuery();
		$this->setLoopValue("profile",$result->getResult());
		$rs=$select->fetchRow($result);
		$select->numRows($result);
		$dob=date("d/n/Y",$rs[2]);
		$dob1=explode("/",$dob);
		$day=$dob1[0];$mnth=$dob1[1];$year=$dob1[2];
		$this->setValue("dd",$day);$this->setValue("mn",$mnth);$this->setValue("yr123",$year);

		$male=$this->getmessage(137);$female=$this->getmessage(138);
		$this->setValue("male",$male);$this->setValue("female",$female);
		$year=date("Y",time());
		for($i=$year,$j=0;$i>=1900;$i--,$j++)
		{
			$yr[$j][0]=$i;
		}
		$this->setLoopValue("yrs",$yr);
			
		for($i1=01,$j1=0;$i1<=31;$i1++,$j1++)
		{
			if ($i1<10) {
                $i1="0".$i1;
            }
			$days[$j1][0]=$i1;
		}
		$this->setLoopValue("days",$days);
			
		
		$select->select("nesote_email_country");
		$select->fields("name");
		$select->order("name asc");
		$result=$select->query();
		$this->setLoopValue("country",$result->getResult());


		if($_POST !== [])
		{



			$userid=$this->getId();$flag=1;$msg="";

			$name=trim((string) $_POST['name']);
		   if($name === "")
			{
				$flag=0;
				$msg=$this->getmessage(669);
			}
			
//			if($fname=="")
//			{
//				$flag=0;
//				if($msg=="")
//				$msg=$this->getmessage(112);
//			}
//			$lname=trim($_POST['lname']);
//			if($lname=="")
//			{
//				$flag=0;
//				if($msg=="")
//				$msg=$this->getmessage(113);
//			}
			$gender=$_POST['gender'];
			if($gender=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(127);
                }
			}
			$dd=$_POST['day'];//echo $dd;
			if($dd=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(128);
                }
			}
			$mn=$_POST['month'];//echo $mn;
			if($mn=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(129);
                }
			}
			$yr=$_POST['year'];//echo $yr;
			if($yr=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(130);
                }
			}

			$country=$_POST['country'];//echo $country;
			if($country=="")
			{
				$flag=0;
				if ($msg=="") {
                    $msg=$this->getmessage(131);
                }
			}
			$alternamteemail=$_POST['alternatemail'];

			if($alternamteemail!="")
			{
				$rslt=$this->isValid($alternamteemail);
				if($rslt=="true")
				{
					$flag=1;
				}
				else
				{
					$flag=0;
					if ($msg=="") {
                        $msg=$this->getmessage(159);
                    }
				}
			}

			$uname=$_COOKIE['e_username'];
			$pwd=$_COOKIE['e_password'];
			if($flag==1)
			{

				if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
				{

					$msg=$this->getmessage(340);
					header("Location:".$this->url("settings/webmailsettings/$msg"));
					exit(0);
				}


				if ($mn<10) {
                    $mm="0".$mn;
                }
				$dob=mktime(0,0,0,$mm,$dd,$yr);//echo $dob;exit;

				
				$select->update("nesote_liberyus_users");
				$select->set("name=?",[$name]);
				$select->where("id=? and username=? and password=? and status=?",[$userid,$uname,$pwd,1]);
				$select->query();//echo $db->getQuery();exit;
				$select->update("nesote_email_usersettings");
				$select->set("sex=?,dateofbirth=?,country=?,alternate_email=?",[$gender,$dob,$country,$alternamteemail]);
				$select->where("userid=?",[$userid]);
				$select->query();//echo $select->getQuery();


				$username=$this->getusername($userid);
				$this->saveLogs("Edit Profile","$username has updated his/her profile");

				$msg=$this->getmessage(362);
			}

			header("Location:".$this->url("settings/editprofile/$msg"));
			exit(0);
		}
	}

	function webmailsettingsAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		    $url=$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
			if(strpos($url,"/index.php")!="")
			{
				$url=str_replace("/index.php","",$url);

			}
			$url="http://".$url;
			$this->setValue("path",$url);
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$portal_status=$settings->getValue("portal_status");
		$this->setValue("portal_status",$portal_status);
		
		
		$userid=$this->getId();$folder="";$msg="";
		
		$select=new NesoteDALController();
		$select->select("nesote_email_usersettings");
		$select->fields("theme_id,shortcuts");
		$select->where("userid=?",$userid);
		$result=$select->query();//echo $select->getQuery();
		$res=$select->fetchRow($result);
		$style_id=$res[0];
		$shortcuts=$res[1];
		if ($shortcuts=="") {
            $shortcuts=0;
        }
		$this->setValue("shval",$shortcuts);
		
		$lang_code=$_COOKIE['lang_mail'];
		if($lang_code=="")
		{
			$lang_code='eng';
			setcookie("lang_mail",$lang_code, ['expires' => "0", 'path' => "/"]);
		}
		$langid=$this->getlang_id($lang_code);

		$this->setValue("qq",$lang_code);
		//$folder=$this->getParam(1);//echo "dvdvck".$folder;exit;
		$msg=$this->getParam(1);
		//$this->setValue("folder",$folder);
		$this->setValue("msg",$msg);

		
		$this->setValue("uid",$userid);
          		
		$select->select("nesote_email_usersettings");
		$select->fields("*");
		$select->where("userid=?",[$userid]);
		$result=$select->query();//echo $select->getQuery();
		$rs=$select->fetchRow($result);
		$num=$select->numRows($result);
		$this->setValue("num",$num);
		$this->setLoopValue("settings",$result->getResult());
        $displayview = $num == 0 ? $settings->getValue("display") : $rs[6];
		$this->setValue("displayview",$displayview);

		//$select1=new NesoteDALController();

		$select->select("nesote_email_languages");
		$select->fields("lang_code,language");
		$select->where("status=?",[1]);
		$result1=$select->query();
		$numl=$select->numRows($result1);
		$this->setValue("numl",$numl);
		$this->setLoopValue("medium",$result1->getResult());

		

		$select->select("nesote_email_themes");
		$select->fields("*");
		$select->where("status=?",[1]);
		$result2=$select->query();
		$numt=$select->numRows($result2);
		$this->setValue("numt",$numt);
		$this->setLoopValue("themes",$result2->getResult());

		
		$override_themes=$settings->getValue("override_themes");
		$this->setValue("override_themes",$override_themes);


		$totalusagesize=$settings->getValue("user_memory_size");


		$memoryusage_publicview=$settings->getValue("memoryusage_publicview");

		if($memoryusage_publicview==1)
		{


			$memoryusage_publicview_area=$settings->getValue("memoryusage_publicview_area");
			if(($memoryusage_publicview_area==1)||($memoryusage_publicview_area==0))
			{
				$memoryusage_publicview_area=1;
			}


			$memorysize_format=$settings->getValue("memorysize_format");

		}
		$this->setValue("memoryusage_publicview_area",$memoryusage_publicview_area);
		 require(__DIR__ . "/script.inc.php");
        include($config_path."database.default.config.php");
		
		$sum=0;
	$x=mysql_query("SELECT sum(OCTET_LENGTH(body)) FROM ".$db_tableprefix."nesote_email_inbox_$tablenumber where userid=$userid");
	$v=mysql_fetch_row($x);
	 $sum += $v[0];
	 $x=mysql_query("SELECT sum(OCTET_LENGTH(body)) FROM ".$db_tableprefix."nesote_email_sent_$tablenumber where userid=$userid");
	$v=mysql_fetch_row($x);
	 $sum += $v[0];
	 $x=mysql_query("SELECT sum(OCTET_LENGTH(body)) FROM ".$db_tableprefix."nesote_email_draft_$tablenumber where userid=$userid");
	$v=mysql_fetch_row($x);
	 $sum += $v[0];
	 $x=mysql_query("SELECT sum(OCTET_LENGTH(body)) FROM ".$db_tableprefix."nesote_email_trash_$tablenumber where userid=$userid");
	$v=mysql_fetch_row($x);
	 $sum += $v[0];
	 $x=mysql_query("SELECT sum(OCTET_LENGTH(body)) FROM ".$db_tableprefix."nesote_email_spam_$tablenumber where userid=$userid");
	$v=mysql_fetch_row($x);
	$sum += $v[0];
	 $db=new NesoteDALController();
			$db->select("nesote_email_customfolder");
			$db->fields("id,name");
			$db->where("userid=?",[$userid]);
			$res1=$db->query();
			$i=0;
			while($rw=$db->fetchRow($res1))
			{
			 $x=mysql_query("SELECT sum(OCTET_LENGTH(body)) FROM ".$db_tableprefix."nesote_email_customfolder_mapping_$tablenumber where folderid=$rw[0]");
			$v=mysql_fetch_row($x);
			$sum += $v[0];
			}
$x=mysql_query("SELECT a.mailid,a.name FROM ".$db_tableprefix."nesote_email_inbox_$tablenumber i join ".$db_tableprefix."nesote_email_attachments_$tablenumber a on a.mailid=i.id where a.folderid=1 and i.userid=$userid and a.userid=$userid");
	while($v=mysql_fetch_row($x))
	{
	$filesize=filesize("attachments/1/$tablenumber/$v[0]/$v[1]");
	$sum += $filesize;	
    }
    $x=mysql_query("SELECT a.mailid,a.name FROM ".$db_tableprefix."nesote_email_draft_$tablenumber i join ".$db_tableprefix."nesote_email_attachments_$tablenumber a on a.mailid=i.id where a.folderid=2 and i.userid=$userid and a.userid=$userid");
	while($v=mysql_fetch_row($x))
	{
	$filesize=filesize("attachments/2/$tablenumber/$v[0]/$v[1]");
	$sum += $filesize;	
    }
		$x=mysql_query("SELECT a.mailid,a.name FROM ".$db_tableprefix."nesote_email_sent_$tablenumber i join ".$db_tableprefix."nesote_email_attachments_$tablenumber a on a.mailid=i.id where a.folderid=3 and i.userid=$userid and a.userid=$userid");
	while($v=mysql_fetch_row($x))
	{
	$filesize=filesize("attachments/3/$tablenumber/$v[0]/$v[1]");
	$sum += $filesize;	
    }
		$x=mysql_query("SELECT a.mailid,a.name FROM ".$db_tableprefix."nesote_email_spam_$tablenumber i join ".$db_tableprefix."nesote_email_attachments_$tablenumber a on a.mailid=i.id where a.folderid=4 and i.userid=$userid and a.userid=$userid");
	while($v=mysql_fetch_row($x))
	{
	$filesize=filesize("attachments/4/$tablenumber/$v[0]/$v[1]");
	$sum += $filesize;	
    }
		$x=mysql_query("SELECT a.mailid,a.name FROM ".$db_tableprefix."nesote_email_trash_$tablenumber i join ".$db_tableprefix."nesote_email_attachments_$tablenumber a on a.mailid=i.id where a.folderid=5 and i.userid=$userid and a.userid=$userid");
	while($v=mysql_fetch_row($x))
	{
	$filesize=filesize("attachments/5/$tablenumber/$v[0]/$v[1]");
	$sum += $filesize;	
    }
	 
	$x=mysql_query("SELECT id FROM ".$db_tableprefix."nesote_email_customfolder where userid=$userid");
	while($v=mysql_fetch_row($x))
	{
		
	$x1=mysql_query("SELECT a.mailid,a.name FROM ".$db_tableprefix."nesote_email_customfolder_mapping_$tablenumber i join ".$db_tableprefix."nesote_email_attachments_$tablenumber a on a.mailid=i.id where a.folderid=$v[0] and a.userid=$userid");
	while($v1=mysql_fetch_row($x1))
	{
	$filesize=filesize("attachments/$v[0]/$tablenumber/$v1[0]/$v1[1]");
	$sum += $filesize;	
    }
    
	}
	
	 $size=round($sum/(1024*1024),2);


			 $percent=$size/$totalusagesize;
			 $percent=round($percent*100);
			
			if ($memorysize_format==1) {
                $memorymsg=$this->getmessage(318);
                $msg1=str_replace('{percent}',$percent,$memorymsg);
                $msg1=str_replace('{totalmemory}',$totalusagesize,$msg1);
                $this->setValue("memorymsg",$msg1);
            } elseif ($memorysize_format==0 || $memorysize_format=="") {
                $memorymsg=$this->getmessage(313);
                $msg1=str_replace('{memoryused}',$size,$memorymsg);
                //$msg1=str_replace('{totalmemory}',$totalusagesize,$msg1);
                $this->setValue("memorymsg",$msg1);
            }
		
		$select->select("nesote_email_time_zone");
		$select->fields("id,name,value");
		$resulto=$select->query();
		$numz=$select->numRows($resulto);
		$this->setValue("numz",$numz);
		$this->setLoopValue("tmzone",$resulto->getResult());

		
		$select->select("nesote_email_usersettings");
		$select->fields("time_zone");
		$select->where("userid=?",[$userid]);
		$result=$select->query();
		$usertimezone=$select->fetchRow($result);
		$this->setValue("usertimezone",$usertimezone[0]);
		
		$admin_sh=$settings->getValue("shortcuts");
		if ($admin_sh=="") {
            $admin_sh=0;
        }
		$this->setValue("admin_sh",$admin_sh);

		if($_POST !== [])
		{

			$userid=$this->getId();
			//						if( $_SERVER['HTTP_HOST']=="www.libewebmail.com" || $_SERVER['HTTP_HOST']=="libewebmail.com" )
			//						{
			//
			//							$msg=$this->getmessage(340);
			//							header("Location:".$this->url("settings/webmailsettings/$msg"));
			//							exit(0);
			//						}

			//$defaultview=$_POST['defaultview'];//echo $defaultview;

			$defaultview=1;
			$this->setValue("displayview",$defaultview);
			$signflag=$_POST['signflag'];
			$langid=$_POST['language']; 
			$themeid=$_POST['themes'];
			$mails_per_page=$_POST['mails_per_page'];
			$timezone=$_POST['timezone'];
			$signature = $signflag == 1 ? $_POST['signature'] : "";

			if ($signature=="") {
                $signflag=0;
            }

			$signature=html_entity_decode((string) $signature,ENT_QUOTES,"UTF-8");
			$userid=$this->getId();

			
			$select->update("nesote_email_usersettings");
			$select->set("signature=?,signatureflag=?,lang_id=?,theme_id=?,mails_per_page=?,display=?,time_zone=?",[$signature,$signflag,$langid,$themeid,$mails_per_page,$defaultview,$timezone]);
			$select->where("userid=?",[$userid]);
			$select->query();

			
//			$select->update("nesote_email_usersettings");
//			$select->set("time_zone=?",array($timezone));
//			$select->where("id=?",array($userid));
//			$select->query();

			$myFile = "style1.css";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = "";
			fwrite($fh, $stringData);

		
			$select->select("nesote_email_themes");
			$select->fields("style");
			$select->where("id=?",[$themeid]);
			$result=$select->query();
			$stringData1=$select->fetchRow($result);

			$stringData = $stringData1[0];
			fwrite($fh, (string) $stringData);
			fclose($fh);


			$username=$this->getusername($userid);
			$this->saveLogs("Settings Updation","$username has updated his/her settings");
			setcookie("lang_mail",(string) $langid, ['expires' => "0", 'path' => "/"]);
			$msg=$this->getmessage(195);

			header("Location:".$this->url("settings/webmailsettings/$msg"));
			//header("Location:".$this->url("settings/settings"));
			exit(0);
		}

	}

	function getsignatureAction(): never
	{
		$id=$this->getId();
		$select1=new NesoteDALController();
		$select1->select("nesote_email_usersettings");
		$select1->fields("signature");
		$select1->where("userid=?",$id);

		$result1=$select1->query();
		$sign=$select1->fetchRow($result1);
		$signature=$sign[0];
		//$signature=html_entity_decode($signature);
          
		echo $signature=$signature;exit;
	}
	
	function changepwdportalAction()
	{
		$username=$_COOKIE['e_username'];
		//$old_password=$_COOKIE['e_password'];
		$userid=trim((string) $this->getParam(1));   //base64_ encode
		$password=trim((string) $this->getParam(2));
		
		$userid=base64_decode($userid);
		$password=base64_decode($password);
		
		$userid=str_replace("_*#@","",$userid);
		$password=str_replace("_*#@","",$password);
		
		
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$account_type=$settings->getValue("catchall_mail");
		
		$server_password=base64_encode($password);
	    
		$md5_password=md5($password);
		setcookie("e_password","$md5_password", ['expires' => "0", 'path' => "/"]);


        $select=new NesoteDALController();
        
					$account_type=$settings->getValue("catchall_mail");

					$username=$this->getusername($userid);
					
					if ($account_type==1) {
                        $select->update("nesote_email_usersettings");
                        $select->set("server_password=?",[$server_password]);
                        $select->where("userid=?",[$userid]);
                        $result=$select->query();
                        $this->saveLogs("Settings Updation","$username has updated his/her settings");
                        //$msg=$this->getmessage(161);
                        //header("location:".$this->url("settings/webmailsettings"));exit(0);
                        //header("location:".$this->url("settings/changepassword/161/success"));exit(0);
                    } elseif ($account_type==0) {
                        $automatic_account_creation=$settings->getValue("automatic_account_creation");
                        if($automatic_account_creation==1)// for automatic account creation
						{

							$public_registration=$settings->getValue("public_registration");
							if($public_registration==1)
							{
								// api calling


                                $controlpanel=$settings->getValue("controlpanel");
								if ($controlpanel==1) {
                                    $this->cpanelaction(1,$username,$password);
                                    // 1 for change password
                                } elseif ($controlpanel==2) {
                                    $this->pleskaction(1,$username,$password);
                                    // 1 for change password
                                }

							
								

								$select->update("nesote_email_usersettings");
								$select->set("server_password=?",[$server_password]);
								$select->where("userid=?",[$userid]);
								$result=$select->query();
								
							

								
								
								$this->saveLogs("Settings Updation","$username has updated his/her settings");
								$reload=10;//for settings left panel reloading
							


							}
						}
                    }
					echo "success"; exit;
		
		
			
			
	}
	
	
	function changepasswordAction()
	{

		$valid=$this->validateUser();
		
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$portal_status=$settings->getValue("portal_status");
		$this->setValue("portal_status",$portal_status);
		if($portal_status==0)
		{
		$userpanel="";
		$select=new NesoteDALController();
		$db=new NesoteDALController();
		
		$userpanel=$settings->getValue("controlpanel");
		$this->setValue("controlpanel",$userpanel);
		
		$min_passwordlength=$settings->getValue("min_passwordlength");
		$this->setValue("min_passwordlength",$min_passwordlength);

		$msg='';$status='';
		$msgid=$this->getParam(1);
		$status=$this->getParam(2);
		if ($msgid!="") {
            $msg=$this->getmessage($msgid);
        }
		$this->setValue("msg",$msg);
		$this->setValue("status",$status);
		$flag=1;
		$id=$this->getId();
		
		$this->setValue("uid",$id);

		$user=$this->getusername($id);
		$this->setValue("username",$user);
		if($_POST !== [])

		{

			$userid=$this->getId();
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
					
				$msg=$this->getmessage(340);
				header("location:".$this->url("settings/changepassword/340/e"));
				exit(0);
			}
			else
			{
				$uid=$_POST['uid'];

				
				$db->select("nesote_liberyus_users");
				$db->fields("*");
				$db->where("id=?",[$uid]);
				$result2=$db->query();
				$id1=$db->fetchRow($result2);
				$uname=$id1[1];
				$password=$id1[2];

				$password1=$_POST['password1'];
				$cpassword=md5((string) $password1);//echo $cpassword;//52f5972aee0aa1a446e711bd9407ab72
				$cnpassword=$_POST['cpassword'];
				$npassword=$_POST['npassword'];
				$pwdcount=$_POST['pwdcnt'];


				if ($password1=="" && $cnpassword=="" && $npassword=="") {
                    $flag=0;
                    $msg=$this->getmessage(241);
                    header("location:".$this->url("settings/changepassword/241/e"));
                    exit(0);
                } elseif ($password1=="") {
                    $flag=0;
                    $msg=$this->getmessage(242);
                    header("location:".$this->url("settings/changepassword/242/e"));
                    exit(0);
                } elseif ($npassword=="") {
                    $flag=0;
                    $msg=$this->getmessage(243);
                    header("location:".$this->url("settings/changepassword/243/e"));
                    exit(0);
                } elseif ($npassword==$uname) {
                    $flag=0;
                    $msg=$this->getmessage(355);
                    header("location:".$this->url("settings/changepassword/355/e"));
                    exit(0);
                } elseif ($cnpassword=="") {
                    $flag=0;
                    $msg=$this->getmessage(244);
                    header("location:".$this->url("settings/changepassword/244/e"));
                    exit(0);
                } elseif ($cpassword!=$password) {
                    $flag=0;
                    $msg=$this->getmessage(245);
                    header("location:".$this->url("settings/changepassword/245/e"));
                    exit(0);
                } elseif ($cnpassword!=$npassword) {
                    $flag=0;
                    $msg=$this->getmessage(246);
                    header("location:".$this->url("settings/changepassword/246/e"));
                    exit(0);
                } elseif ($pwdcount<2) {
                    $flag=0;
                    $msg=$this->getmessage(290);
                    header("location:".$this->url("settings/changepassword/290/e"));
                    exit(0);
                    //$this->setValue("msg2",$msg1);
                    //$this->setValue("msg1",$msg1);
                    //$flag=0;
                }
                $server_password=base64_encode((string) $npassword);
                //echo $server_password;
                $cnpassword=md5((string) $npassword);
                //echo $cnpassword;exit;
                setcookie("e_password","$cnpassword", ['expires' => "0", 'path' => "/"]);
                $account_type=$settings->getValue("catchall_mail");
                if ($account_type==1) {
                    $select->update("nesote_liberyus_users");
                    $select->set("password=?",[$cnpassword]);
                    $select->where("username=? and password=?",[$uname,$password]);
                    $result=$select->query();
                    $select->update("nesote_email_usersettings");
                    $select->set("server_password=?",[$server_password]);
                    $select->where("userid=?",[$uid]);
                    $result=$select->query();
                    $userid=$this->getId();
                    $username=$this->getusername($userid);
                    $this->saveLogs("Settings Updation","$username has updated his/her settings");
                    $msg=$this->getmessage(161);
                    //header("location:".$this->url("settings/webmailsettings"));exit(0);
                    header("location:".$this->url("settings/changepassword/161/success"));
                    exit(0);
                } elseif ($account_type==0) {
                    $automatic_account_creation=$settings->getValue("automatic_account_creation");
                    if($automatic_account_creation==1)// for automatic account creation
						{

							$public_registration=$settings->getValue("public_registration");
							if($public_registration==1)
							{
								// api calling


                            $controlpanel=$settings->getValue("controlpanel");
								if ($controlpanel==1) {
                                $this->cpanelaction(1,$uname,$npassword);
                                // 1 for change password
                            } elseif ($controlpanel==2) {
                                $this->pleskaction(1,$uname,$npassword);
                                // 1 for change password
                            }

								//$update=new NesoteDALController();
								$select->update("nesote_liberyus_users");
								$select->set("password=?",[$cnpassword]);
								$select->where("username=? and password=?",[$uname,$password]);
								$result=$select->query();
								

								$select->update("nesote_email_usersettings");
								$select->set("server_password=?",[$server_password]);
								$select->where("userid=?",[$uid]);
								$result=$select->query();
								
								$msg=$this->getmessage(161);

								$userid=$this->getId();
								$username=$this->getusername($userid);
								$this->saveLogs("Settings Updation","$username has updated his/her settings");
								$reload=10;//for settings left panel reloading
								//header("location:".$this->url("settings/webmailsettings"));exit(0);
								header("location:".$this->url("settings/changepassword/161/success"));exit(0);


							}
						}
                }


			}
		}
		$this->setValue("msg",$msg);
		}
	}

	function cpanelaction($execte,$username,$value)
	{
		
		include_once __DIR__ . '/class/xmlapi.php';

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$userid=$this->getId();
		

		$ip=$settings->getValue("domain_ip");


		$root_pass=$settings->getValue("domain_password");


		$email_domain=$settings->getValue("domain_name");

		$domain_username=$settings->getValue("domain_username");


		$account = "cptest";
		$email_user = $username;
		$email_password = $value;
		$email_query = '10';
		$xmlapi = new xmlapi($ip);
		/* IF the port no is 2083 then uncomment the below sentence*/
		//$xmlapi->set_port(2083);
		$xmlapi->password_auth($domain_username,$root_pass);
		$xmlapi->set_output('xml');
		$email_quota=0;

		$xmlapi->set_debug(1);
			
		if ($execte==1) {
            try {
				$xmlapi->api1_query($account, "Email", "passwdpop", [$email_user, $value, $email_quota, $email_domain] );
			}
			catch (Exception) {


				header("Location:".$this->url("settings/changepassword/441/e"));
				exit(0);

			}
        } elseif ($execte==2) {
            $cnt=count($value);
            for($i=0;$i<$cnt;$i++)
			{
				$forward_email=$value[$i];
				if($forward_email!="")
				{
					try {
						$result = $xmlapi->api2_query($account, "Email", "addforward", [\DOMAIN=>$email_domain, \EMAIL=>$email_user, \FWDOPT=>"fwd", \FWDEMAIL=>$forward_email]);
					}
					catch (Exception) {

						$msg2=$this->getmessage(441);

						header("Location:".$this->url("settings/mailoptions/$msg2"));
						exit(0);
							
					}
				}
			}
            return;
        } elseif ($execte==3) {
            $autoreply=explode("/",(string) $value);
            $from=$this->getname($userid);
            $autoreply_subject=$autoreply[0];
            $autoreply_msg=$autoreply[1];
            try {
				$xmlapi->api1_query($account, "Email", "addautoresponder", [$email_user, $from, $autoreply_subject,$autoreply_msg, $email_domain,1,"UTF-8"] );
			}
			catch (Exception) {

				$msg2=$this->getmessage(441);

				header("Location:".$this->url("settings/mailoptions/$msg2"));
				exit(0);

			}
            return;
        } elseif ($execte==4) {
            $new_mail=$this->getusername($userid).$this->getextension();
            $cnt=count($value);
            for($i=0;$i<$cnt;$i++)
			{
				$forward_mail=$value[$i];
				if($forward_mail!="")
				{
					$del_mail=$new_mail."=".$forward_mail;
					try {
						$xmlapi->api1_query($account, "Email", "delforward", [$del_mail]);//echo $cnt; 
					}

					catch (Exception) {

						$msg2=$this->getmessage(441);
						header("Location:".$this->url("settings/mailoptions/$msg2"));
						exit(0);
							
					}
				}

			}
            return;
        } elseif ($execte==5) {
            $mailid=$this->getusername($userid).$this->getextension();
            try {
				$xmlapi->api1_query($account, "Email", "delautoresponder", [$mailid]);
			}
			catch (Exception) {

				$msg2=$this->getmessage(441);
				header("Location:".$this->url("settings/mailoptions/$msg2"));
				exit(0);
					
			}
        }

	}

	function pleskaction($execte,$username,$value)
	{

		include_once __DIR__ . '/class/mail_plesk.php';
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$userid=$this->getId();

		$host=$settings->getValue("domain_name");


		$login=$settings->getValue("domain_username");


		$password=$settings->getValue("domain_password");

		$plesk_packetversion=$settings->getValue("plesk_packetversion");

		$plesk_domainid=$settings->getValue("plesk_domainid");

		if ($execte==1) {
            $change="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version='$plesk_packetversion'>
			<mail>
			<update>
			<set>
			<filter><domain_id>$plesk_domainid</domain_id>
			<mailname>
			<name>$username</name>
			<mailbox>
			<enabled>true</enabled>
			</mailbox>
			<password>$value</password>
			<password_type>plain</password_type>

			</mailname>
			</filter>
			</set>
			</update>
			</mail>
			</packet>
			";
            $action=$change;
            $update=new NesoteDALController();
        } elseif ($execte==2) {
            $cnt=count($value);
            for($i=0;$i<$cnt;$i++)
			{
				$forward_email=$value[$i];
				if($forward_email!="")
				{
					$forward="<?xml version='1.0' encoding='UTF-8' ?>
					<packet version='$plesk_packetversion'>
					<mail>
					<update>
					<add>
					<filter><domain_id>$plesk_domainid</domain_id>
					<mailname>
					<name>$username</name>
					<redirect>
					<enabled>true</enabled>
					<address>$forward_email</address>
					</redirect>
					</mailname>

					</filter>
					</add>
					</update>
					</mail>

					</packet>

			";
					$action=$forward;
				}
			}
        } elseif ($execte==3) {
            $autoreply=explode("/",(string) $value);
            $from=$this->getname($userid);
            //firstname lastname
            $autoreply_subject=$autoreply[0];
            $autoreply_msg=$autoreply[1];
            $autoresponder="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version='$plesk_packetversion'>
			<mail>
			<update>
			<add>
			<filter><domain_id>$plesk_domainid</domain_id>
			<mailname>
			<name>$username</name>
			<autoresponders>
			<enabled>true</enabled>
			<autoresponder>
			<name>$from</name>
			<subject>$autoreply_subject</subject>
			<text>$autoreply_msg</text>
			</autoresponder>
			</autoresponders>
			</mailname>
			</filter>
			</add>
			</update>
			</mail>

			</packet>
			";
            $action=$autoresponder;
        } elseif ($execte==4) {
            $forward_delete="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version='$plesk_packetversion'>
			<mail>
			<update>
			<set>
			<filter><domain_id>$plesk_domainid</domain_id>
			<mailname>
			<name>$username</name>
			<redirect>
			<enabled>false</enabled>
			<address>$value</address>
			</redirect>
			</mailname>

			</filter>
			</set>
			</update>
			</mail>

			</packet>
			";
            $action=$forward_delete;
        } elseif ($execte==5) {
            $autoreply=explode("/",(string) $value);
            $from=$this->getname($userid);
            //firstname lastname
            $autoreply_subject=$autoreply[0];
            $autoreply_msg=$autoreply[1];
            $autoresponder_delete="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version='$plesk_packetversion'>
			<mail>
			<update>
			<remove>
			<filter><domain_id>$plesk_domainid</domain_id>
			<mailname>
			<name>$username</name>
			<autoresponders>
			<enabled>false</enabled>
			<autoresponder>
			<name>$from</name>
			<subject>$autoreply_subject</subject>
			<text>$autoreply_msg</text>
			</autoresponder>
			</autoresponders>
			</mailname>
			</filter>
			</remove>
			</update>
			</mail>

			</packet>
			";
            $action=$autoresponder_delete;
        }


		$curl = curlInit($host, $login, $password);
		try {

			// echo GET_PROTOS;
			$response = sendRequest($curl, $action);//if($execte==3)print_r($response);//if($execte==3){echo $response;exit;}
			$responseXml = parseResponse($response);
			checkResponse($responseXml);
		} catch (ApiRequestException $e) {

			if($execte==1)
			{
				header("Location:".$this->url("settings/changepassword/441/e"));
				exit(0);
			}
			else
			{
				$msg2=$this->getmessage(441);
				header("Location:".$this->url("settings/mailoptions/$msg2"));
				exit(0);
			}
			echo $e;
			die();
		}
		// Explore the result
		foreach ($responseXml->xpath('/packet/domain/get/result') as $resultNode) {
			echo "Domain id: " . $resultNode->id . " ";
			echo $resultNode->data->gen_info->name . " (" .
			$resultNode->data->gen_info->dns_ip_address . ")\n";
		}

	}

	function emailfilterAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$userid=$this->getId();
		$msg=$this->getParam(1);
		$this->setValue("msg",$msg);
		$flag=1;

		$select=new NesoteDALController();
		$select->select("nesote_email_emailfilters");
		$select->fields("*");
		$select->where("userid=?",[$userid]);
		$select->order("id desc");
		$result=$select->query();
		$row=$select->fetchRow($result);
		$num=$select->numRows($result);
		$this->setValue("cnt",$num);


		$this->setLoopValue("filteredmails",$result->getResult());


		
		$select->select("nesote_email_customfolder");
		$select->fields("*");
		$select->where("userid=?",[$userid]);
		$result1=$select->query();
		$row=$select->fetchRow($result1);
        $select->numRows($result1);
		$this->setLoopValue("customfolders",$result1->getResult());



		if($_POST !== [])
		{


			$userid=$this->getId();
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
					
				$msg=$this->getmessage(340);
			}
			else
			{
				$excute=$_POST['execute'];
				$folderid=$_POST['moveto'];
				$from=trim((string) $_POST['from_id']);
				$fromflag = $from === "" ? 0 : trim((string) $_POST['fromflag']);

				$subject=trim((string) $_POST['subject']);
				$subjectflag = $subject === "" ? 0 : trim((string) $_POST['subjectflag']);

				$body=trim((string) $_POST['body']);
				$bodyflag = $body === "" ? 0 : trim((string) $_POST['bodyflag']);


				if((trim($from === ""))&&(trim($subject === ""))&&(trim($body === "")))
				{
					$flag=0;
					$msg=$this->getmessage(196);
				}

				
				$select->select("nesote_email_emailfilters");
				$select->fields("*");
				$select->where("userid=?",[$userid]);
				$result=$select->query();
				while($row=$select->fetchRow($result))
				{

					if(($from==$row[1])&&($subject==$row[2])&&($body==$row[3])&&($fromflag==$row[4])&&($subjectflag==$row[5])&&($bodyflag==$row[6]))
					{//echo hai;
						$flag=0;
						$msg=$this->getmessage(166);
					}

				}


				if($flag==1)
				{

					
					if ($excute==0) {
                        $select->insert("nesote_email_emailfilters");
                        $select->fields("from_id,subject,body,fromflag,subjectflag,bodyflag,folderid,userid");
                        $select->values([$from,$subject,$body,$fromflag,$subjectflag,$bodyflag,$folderid,$userid]);
                        $select->query();
                        //echo $db->getQuery();exit;
                        $msg=$this->getmessage(195);
                    } elseif ($excute==1) {
                        $id=$_POST['mailfilterid'];
                        $select->update("nesote_email_emailfilters");
                        $select->set("from_id=?,subject=?,body=?,fromflag=?,subjectflag=?,bodyflag=?,folderid=?,userid=?",[$from,$subject,$body,$fromflag,$subjectflag,$bodyflag,$folderid,$userid]);
                        $select->where("id=?",[$id]);
                        $select->query();
                        $msg=$this->getmessage(195);
                    }
					$userid=$this->getId();
					$username=$this->getusername($userid);
					$this->saveLogs("Settings Updation","$username has updated his/her settings");

					header("Location:".$this->url("settings/emailfilter/$msg"));
					exit(0);

				}


			}

		}//post
		$this->setValue("msg",$msg);
	}

	function getfilterfolders($id)
	{
		$userid=$this->getId();
		$db=new NesoteDALController();
		$db->select("nesote_email_emailfilters");
		$db->fields("*");
		$db->where("id=? and userid=?",[$id,$userid]);
		$db->order("id asc");
		$result=$db->query();//echo $db->getQuery();
		$row=$db->fetchRow($result);
		$num=$db->numRows($result);
		$this->setValue("num",$num);

		$folder=$row[7];//echo $folder;
		if ($row[7]==1) {
            $folder=$this->getmessage(19);
        } elseif ($row[7]==2) {
            $folder=$this->getmessage(20);
        } elseif ($row[7]==3) {
            $folder=$this->getmessage(21);
        } elseif ($row[7]==4) {
            $folder=$this->getmessage(12);
        } elseif ($row[7]==5) {
            $folder=$this->getmessage(22);
        } elseif ($row[7]>=10) {
            $db->select("nesote_email_customfolder");
            $db->fields("name");
            $db->where("id=? and userid=?", [$row[7],$userid]);
            $result=$db->query();
            $row1=$db->fetchRow($result);
            $folder=$row1[0];
        }

		return $folder;

	}


	function deleteemailfiltersACtion()
	{

		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{

			$msg=$this->getmessage(340);
			header("Location:".$this->url("settings/emailfilter/$msg"));
			exit(0);
		}
		else
		{
			$userid=$this->getId();

			$string=$this->getParam(1);

			$str=substr((string) $string,0,-1);

			if($str === '')
			{
				header("Location:".$this->url("message/error/1004"));
				exit(0);
			}

			$db=new NesoteDALController();
			$db->delete("nesote_email_emailfilters");
			$db->where("id IN($str) and userid=?",[$userid]);
			$result=$db->query();

			$userid=$this->getId();
			$username=$this->getusername($userid);
			$this->saveLogs("Settings Updation","$username has updated his/her settings");

			$msg=$this->getmessage(239);
			header("Location:".$this->url("settings/emailfilter/$msg"));
			exit(0);

		}

	}

	function editemailfilterAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{

			$editrule="off";
		}
		else {
            $editrule="on";
        }

		$id=$this->getParam(1);
		$userid=$this->getId();

		$db=new NesoteDALController();
		$db->select("nesote_email_emailfilters");
		$db->fields("*");
		$db->where("id=? and userid=?",[$id,$userid]);
		$result=$db->query();//echo $db->getQuery();
		$row=$db->fetchRow($result);//echo $row[1];
		//echo $row[2];
		
		$db->select("nesote_email_customfolder");
		$db->fields("*");
		$db->where("userid=?",[$userid]);
		$result1=$db->query();//echo $db->getQuery();

		$db->numRows($result1);
		$this->setLoopValue("customfolders",$result1->getResult());

		$str="";


		$str=$str."<div id=\"filtersedit_$id\">&nbsp;</div><div id=\"addfilter\" class=\"settings_details_table\" >
		<div class=\"row\">
		<div class=\"coloum120 floatL\">".$this->getmessage(99)."</div>
		<div class=\"coloum300 floatL\">&nbsp;</div>
		<div class=\"coloum100 floatL\">".$this->getmessage(13)."</div>
		<div class=\"coloum120 floatL\"><span class=\"padding_td2\" >  <select name=\"moveto\" id=\"moveto\" class=\"SelectContact\">
		     <option value=\"0\" selected=\"selected\">".$this->getmessage(136)."</option>";
		if ($row[7]==1) {
            $str=$str."<option value=\"1\" selected=\"selected\">".$this->getmessage(19)."</option>";
        } else {
            $str=$str."<option value=\"1\"  selected=\"selected\" >".$this->getmessage(19)."</option>";
        }
		
		if ($row[7]==4) {
            $str=$str."<option value=\"4\"  selected=\"selected\" >".$this->getmessage(12)."</option>";
        } else {
            $str=$str."<option value=\"4\" >".$this->getmessage(12)."</option>";
        }
		if ($row[7]==5) {
            $str=$str."<option value=\"5\"  selected=\"selected\">".$this->getmessage(22)."</option>";
        } else {
            $str=$str."<option value=\"5\">".$this->getmessage(22)."</option>";
        }
		while($folder=$db->fetchRow($result1))
		{
			if ($row[7]==$folder[0]) {
                $str .= "<option value=\"$folder[0]\" selected=\"selected\">$folder[1]</option>";
            } else {
                $str .= "<option value=\"$folder[0]\" >$folder[1]</option>";
            }
		}
		$str .= "</select></span></div>
		</div>";
		$str .= "
		<div class=\"row\" style=\"border-bottom:1px solid #CCCCCC;width: 52%\">&nbsp;</div>
	<div class=\"row marginT10\">
	</div>";
		
		$str .= "<div class=\"filter_newrule_table\">";
		if ($msg!="") {
            $str .= "<div><span class=\"success\">$msg</span></div>";
        }

		$str=$str." <div class=\"row marginT10\">
				<div class=\"coloum30 floatL\">&nbsp;</div>
				<div class=\"coloum100 floatL\">".$this->getmessage(54)."</div>

		<div class=\"coloum300 floatL\"><input name=\"from_id\" type=\"text\" id=\"from_id\" size=\"33\" value=\"$row[1]\"></div>
		<div class=\"coloum100 floatL\">
		<select name=\"fromflag\" class=\"SelectContact\">";
		if ($row[4]==1) {
            $str=$str."<option value=\"1\" selected=\"selected\">".$this->getmessage(89)."</option>";
        } else {
            $str=$str."<option value=\"1\">".$this->getmessage(89)."</option>";
        }
		if ($row[4]==2) {
            $str=$str."<option value=\"2\" selected=\"selected\">".$this->getmessage(90)."</option>";
        } else {
            $str=$str."<option value=\"2\" >".$this->getmessage(90)."</option>";
        }
		if ($row[4]==3) {
            $str=$str."<option value=\"3\" selected=\"selected\">".$this->getmessage(91)."</option>";
        } else {
            $str=$str."<option value=\"3\">".$this->getmessage(91)."</option>";
        }
		if ($row[4]==4) {
            $str=$str."<option value=\"4\" selected=\"selected\">".$this->getmessage(92)."</option>";
        } else {
            $str=$str."<option value=\"4\" >".$this->getmessage(92)."</option>";
        }
		$str .= "</select>
		
				</div></div>";

		$str=$str."<div class=\"row marginT10\">
				<div class=\"coloum30 floatL\">&nbsp;</div>
				<div class=\"coloum100 floatL\">".$this->getmessage(34)."</div>
		<div class=\"coloum300 floatL\"><input name=\"subject\" type=\"text\" id=\"subject\" size=\"33\" value=\"$row[2]\"></div>
		<div class=\"coloum100 floatL\"><select name=\"subjectflag\" class=\"SelectContact\">";
		if ($row[5]==1) {
            $str=$str."<option value=\"1\" selected=\"selected\">".$this->getmessage(89)."</option>";
        } else {
            $str=$str."<option value=\"1\">".$this->getmessage(89)."</option>";
        }
		if ($row[5]==2) {
            $str=$str."<option value=\"2\" selected=\"selected\">".$this->getmessage(90)."</option>";
        } else {
            $str=$str."<option value=\"2'>".$this->getmessage(90)."</option>";
        }
		if ($row[5]==3) {
            $str=$str."<option value=\"3\" selected=\"selected\">".$this->getmessage(91)."</option>";
        } else {
            $str=$str."<option value=\"3\">".$this->getmessage(91)."</option>";
        }
		if ($row[5]==4) {
            $str=$str."<option value=\"4\" selected=\"selected\">".$this->getmessage(92)."</option>";
        } else {
            $str=$str."<option value=\"4\">".$this->getmessage(92)."</option>";
        }
		$str .= "</select></div></div>";

		$str=$str."<div class=\"row marginT10\">
				<div class=\"coloum30 floatL\">&nbsp;</div>
				<div class=\"coloum100 floatL\">".$this->getmessage(88)."</div>
		<div class=\"coloum300 floatL\"><input type=\"text\" name=\"body\" id=\"body\" value=\"$row[3]\"  size=\"33\"></div>
		<div class=\"coloum100 floatL\"><select name=\"bodyflag\" class=\"SelectContact\">";
		if ($row[6]==1) {
            $str=$str."<option value=\"1\" selected=\"selected\">".$this->getmessage(89)."</option>";
        } else {
            $str=$str."<option value=\"1\" >".$this->getmessage(89)."</option>";
        }
		if ($row[6]==2) {
            $str=$str."<option value=\"2\" selected=\"selected\">".$this->getmessage(90)."</option>";
        } else {
            $str=$str."<option value=\"2\">".$this->getmessage(90)."</option>";
        }
		if ($row[6]==3) {
            $str=$str."<option value=\"3\" selected=\"selected\">".$this->getmessage(91)."</option>";
        } else {
            $str=$str."<option value=\"3\">".$this->getmessage(91)."</option>";
        }
		if ($row[6]==4) {
            $str=$str."<option value=\"4\" selected=\"selected\">".$this->getmessage(92)."</option>";
        } else {
            $str=$str."<option value=\"4\">".$this->getmessage(92)."</option>";
        }
		$str .= "</select></div></div></div>";

		$str .= "<div class=\"row marginT10\">
	</div>
	
	<div class=\"row\" style=\"border-bottom:1px solid #CCCCCC;width: 52%\">&nbsp;</div>";
		$str=$str."<div class=\"coloum30 floatL\">&nbsp;</div>
		<div class=\"coloum30 floatL\">&nbsp;</div>
		<div class=\"coloum30 floatL\">&nbsp;</div>
		<div class=\"coloum30 floatL\">&nbsp;</div>
		<div class=\"coloum30 floatL\">&nbsp;</div>
		<div class=\"row marginT10\">
		<input type=\"hidden\" name=\"mailfilterid\" id=\"mailfilterid\" value=\"$id\"></td>
		<input type=\"submit\" value='".$this->getmessage(79)."' name=\"submit\" class=\"cursor_select\">
				</div>";





		echo $str;die;

	}

	function mailoptionsAction()
	{

		if(!$this->validateUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$userid=$this->getId();
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$select=new NesoteDALController();
		$msg123=$this->getParam(1);//echo $msg123;

		if ($msg123!="") {
            $msg=$msg123;
        }

		$flag=1;
		$msg="";
		$forwardmailidflag=0;
		$forwardmailid="";
		$autoreplymsgflag=0;
		$autoreplymsg="";


		
		$select->select("nesote_email_usersettings");
		$select->fields("forward_flag,forward_mail,autoreply_flag,autoreply_subject,autoreply_msg");
		$select->where("userid=?", [$userid]);
		$result=$select->query();
		$row=$select->fetchRow($result);

		$forwardmailidflag=$row[0];
		$forwardmailid=htmlentities((string) $row[1]);
		$autoreplymsgflag=$row[2];
		$autoreplysubj=$row[3];
		$autoreplymsg=$row[4];

		$this->setValue("forwardmailidflag",$forwardmailidflag);
		$this->setValue("forwardmailid",$forwardmailid);
		$this->setValue("autoreplymsgflag",$autoreplymsgflag);
		$this->setValue("autoreplysubj",$autoreplysubj);
		$this->setValue("autoreplymsg",$autoreplymsg);




		if($_POST !== [])
		{

			$userid=$this->getId();
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
					
				$msg=$this->getmessage(340);
			}
			else
			{
				$forwardmailidflag=$_POST['forwardmail'];
				$forwardmailid=$_POST['forwardmailid'];
				$autoreplymsgflag=$_POST['forwardmsg'];
				$autoreplysubj=$_POST['forwardsubj'];
				$autoreplymsg=$_POST['forwardmsgid'];





				if(($forwardmailidflag==0) && ($autoreplymsgflag==0))
				{
					$msg=$this->getmessage(161);
					$flag=0;
				}

					
				if ($forwardmailidflag == 1 && $forwardmailid == "") {
                    $msg=$this->getmessage(260);
                    $flag=0;
                }

				if($autoreplymsgflag==1)
				{
					if ($autoreplysubj=="") {
                        $msg=$this->getmessage(262);
                        $flag=0;
                    } elseif ($autoreplymsg=="") {
                        $msg=$this->getmessage(261);
                        $flag=0;
                    }
				}
				$forwardmailidarray=[];
				if($forwardmailid!="")
				{

					$count=strlen((string) $forwardmailid);
					$q=substr((string) $forwardmailid,$count-1,1);
					$forwardmailid1 = $q !== "," ? $forwardmailid."," : $forwardmailid;
					$forwardmailidarray=explode(",",(string) $forwardmailid1);$r=count($forwardmailidarray)-1;//print_r($forwardmailidarray);exit;
					for($i=0;$i<$r;$i++)
					{
						if($this->isEmail($forwardmailidarray[$i])==false)
						{
							$flag=0;
							$msg=$this->getmessage(159);

						}

						for($j=$i+1;$j<$r;$j++)
						{
							if(trim($forwardmailidarray[$i]) === trim($forwardmailidarray[$j]))
							{
								$flag=0;
								$msg=$this->getmessage(258);
							}
						}
					}
				}

					
				$userid=$this->getId();
				$username=$this->getusername($userid);
					


				$account_type=$settings->getValue("catchall_mail");

				if ($account_type==1) {
                    $select->update("nesote_email_usersettings");
                    $select->set("forward_flag=?,forward_mail=?,autoreply_flag=?,autoreply_subject=?,autoreply_msg=?",[$forwardmailidflag,$forwardmailid,$autoreplymsgflag,$autoreplysubj,htmlentities((string) $autoreplymsg)]);
                    $select->where("userid=?",[$userid]);
                    $select->query();
                    //echo $db->getQuery();
                    $msg=$this->getmessage(161);
                } elseif ($account_type==0) {
                    //					
                    $automatic_account_creation=$settings->getValue("automatic_account_creation");
                    if($automatic_account_creation==1)// for automatic account creation
					{

						
						$public_registration=$settings->getValue("public_registration");
						
						if($public_registration==1)
						{
							// api calling



                            $controlpanel=$settings->getValue("controlpanel");
							if ($controlpanel==1) {
                                //if(($forwardmailidflag==0) && ($forwardmailid==""))
                                //{
                                $value="";
                                $select->select("nesote_email_usersettings");
                                $select->fields("forward_mail");
                                $select->where("userid=?",[$userid]);
                                $result=$select->query();
                                $row=$select->fetchRow($result);
                                $forwardmail=$row[0];
                                $forwardmailarray=explode(",",(string) $forwardmail);
                                $value=$forwardmailarray;
                                if ($value!="") {
                                    $this->cpanelaction(4,$username,$value);
                                }
                                //4 for delete email forwarding
                                //}
                                if(($autoreplymsgflag==0) && ($autoreplymsg=="")  && ($autoreplysubj==""))
								{
									$value="";
									$this->cpanelaction(5,$username,$value);//5 for delete autoreply
								}
                                if(($forwardmailidflag!=0) && ($forwardmailid!=""))
								{

									$this->cpanelaction(2,$username,$forwardmailidarray);// 2 for email forwarding
								}
                                if(($autoreplymsgflag!=0) && ($autoreplymsg!="")  && ($autoreplysubj!=""))
								{
									$value=$autoreplysubj."/".$autoreplymsg;//both subj and msg
									$this->cpanelaction(3,$username,$value);// 3 for autoreply

								}
                            } elseif ($controlpanel==2) {
                                //if(($forwardmailidflag==0) && ($forwardmailid==""))
                                //{
                                $value="";
                                $this->pleskaction(4,$username,$value);
                                //4 for delete email forwarding
                                //}
                                if (($forwardmailidflag!=0) && ($forwardmailid!="")) {
                                    $this->pleskaction(2,$username,$forwardmailidarray);
                                }
                                // 2 for email forwarding
                                $value="";
                                $this->pleskaction(5,$username,$value);
                                //5 for delete autoreply
                                if(($autoreplymsgflag!=0) && ($autoreplymsg!="")  && ($autoreplysubj!=""))
								{
									
									
									$value=$autoreplysubj."/".$autoreplymsg;//both subj and msg
									
									$this->pleskaction(3,$username,$value);// 3 for autoreply
								}
                            }
						}

					}
                    $select->update("nesote_email_usersettings");
                    $select->set("forward_flag=?,forward_mail=?,autoreply_flag=?,autoreply_subject=?,autoreply_msg=?",[$forwardmailidflag,$forwardmailid,$autoreplymsgflag,$autoreplysubj,htmlentities((string) $autoreplymsg)]);
                    $select->where("userid=?",[$userid]);
                    $select->query();
                    //echo $db->getQuery();
                    $msg=$this->getmessage(161);
                }






			}

		} //post

		$this->setValue("msg",$msg);
		//		header("Location:".$this->url("settings/mailoptions"));
		//		exit(0);
		$this->setValue("forwardmailidflag",$forwardmailidflag);
		$this->setValue("forwardmailid",$forwardmailid);
		$this->setValue("autoreplymsgflag",$autoreplymsgflag);
		$this->setValue("autoreplysubj",$autoreplysubj);
		$this->setValue("autoreplymsg",$autoreplymsg);
		$userid1=$this->getId();
		$username1=$this->getusername($userid1);
		$this->saveLogs("Settings Updation","$username1 has updated his/her settings");

	}
	function calendarAction()
	{

		if(!$this->validateUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$userid=$this->getId();$emailremainder=0;$viewevent=0;
		$select=new NesoteDALController();
		$select->select("nesote_email_usersettings");
		$select->fields("theme_id,email_remainder,view_event");
		$select->where("userid=?",$userid);
		$result=$select->query();//echo $select->getQuery();
		$res=$select->fetchRow($result);
		$emailremainder=$res[1];
		$viewevent=$res[2];
		$msg="";
		
		$db=new NesoteDALController();
		$db->select("nesote_email_usersettings");
		$db->fields("email_remainder,view_event");
		$db->where("userid=?", [$userid]);
		$result=$db->query();
		$row=$db->fetchRow($result);

		$emailremainder=$row[0];
		$viewevent=$row[1];
		
		
		
		$db->select("nesote_email_calendar_settings");
		$db->fields("value");
		$db->where("name=?",'email_remainder');
		$result=$db->query();
		$data4=$db->fetchRow($result);
		$email_remainder=$data4[0];
		
		
		$db->select("nesote_email_calendar_settings");
		$db->fields("value");
		$db->where("name=?",'view_event');
		$result=$db->query();
		$data4=$db->fetchRow($result);
		$view_event=$data4[0];
		$this->setValue("remainder_flag",$email_remainder);
		$this->setValue("overflag",$view_event);
		
		if($_POST !== [])
		{

			$userid=$this->getId();
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
					
				$msg=$this->getmessage(340);
			}
			else
			{
				$emailremainder=$_POST['emailremainder'];
				$viewevent=$_POST['viewevent'];
				
				$db=new NesoteDALController();
				$db->update("nesote_email_usersettings");
				$db->set("email_remainder=?,view_event=?",[$emailremainder,$viewevent]);
				$db->where("userid=?",[$userid]);
				$db->query();//echo $db->getQuery();
				$msg=$this->getmessage(161);
				}
			}
		$this->setValue("msg",$msg);
		
		$this->setValue("emailremainder",$emailremainder);
		$this->setValue("viewevent",$viewevent);
		$username1=$this->getusername($userid);
		$this->saveLogs("Settings Updation","$username1 has updated his/her settings");

	}

	function antispamsettingsAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$msg=$this->getParam(1);

		$wlistreturn=$this->getParam(2);

		$blistreturn=$this->getParam(3);
		$this->setValue("msg",$msg);

		$userid=$this->getId();$string1="";$string2="";
		$select=new NesoteDALController();
		
		if($wlistreturn!="")
		{
			$string1=$wlistreturn;
		}
		else
		{
			
			$select->select("nesote_email_whitelist_mail");
			$select->fields("mailid");
			$select->where("clientid=?",[$userid]);
			$result2=$select->query();//echo $select->getQuery();
			//$wcount=$select->numRows($result);
			while($row=$select->fetchRow($result2))
			{
				$string1.=$row[0].",";

			}

			
			$select->select("nesote_email_whitelist_server");
			$select->fields("server");
			$select->where("clientid=?",[$userid]);
			$result3=$select->query();//echo $select->getQuery();
			//$bcount=$select->numRows($result3);
			while($row1=$select->fetchRow($result3))
			{
				$string1.=$row1[0].",";

			}
		}
		$this->setValue("whitelist",$string1);

		if($blistreturn!="")
		{
			$string2=$blistreturn;
		}
		else
		{
			
			$select->select("nesote_email_blacklist_mail");
			$select->fields("mailid");
			$select->where("clientid=?",[$userid]);
			$result4=$select->query();//echo $select->getQuery();
			//$wcount=$select->numRows($result);
			while($row=$select->fetchRow($result4))
			{
				$string2.=$row[0].",";

			}

			
			$select->select("nesote_email_blacklist_server");
			$select->fields("server");
			$select->where("clientid=?",[$userid]);
			$result5=$select->query();//echo $select->getQuery();
			//$bcount=$select->numRows($result1);
			while($row1=$select->fetchRow($result5))
			{
				$string2.=$row1[0].",";

			}
		}
		$this->setValue("blacklist",$string2);


		if($_POST !== [])
		{

			$userid=$this->getId();
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{

				$msg=$this->getmessage(340);
				header("Location:".$this->url("settings/antispamsettings/$msg"));
				exit(0);
			}
			$flag1=1;//for whitelist email
			$flag2=1;//forwhitelist domain
			$flag3=1;//for blacklist email
			$flag4=1;//for black list domain
			$wdomaincount=0;$wemailcount=0;$bdomaincount=0;$bemailcount=0;
			$whitelistdomain=[];$blacklistdomain=[];$whitelistmail=[];$blacklistmail=[];
			$m=0;$n=0;$p=0;$q=0;$r=0;

			
			$select->select("nesote_email_whitelist_mail");
			$select->fields("mailid");
			$select->where("clientid=?",[$userid]);
			$select->group("mailid");
			$result=$select->query();
			$row=$select->fetchRow($result);
			//$wcont=$select->numRows($result);

			
			$select->select("nesote_email_blacklist_mail");
			$select->fields("mailid");
			$select->where("clientid=?",[$userid]);
			$result1=$select->query();
			$row1=$select->fetchRow($result1);
			//$bcont=$select->numRows($result1);
			$blist=$row1[0];

			$whitelist=$_POST['whitelist'];//echo $whitelist;exit;
			$email=[];


			if($whitelist!="")
			{
				$whitelistreturn=$whitelist;
				$wcount=strlen((string) $whitelist);
				$q=substr((string) $whitelist,$wcount-1,1);
				if ($q !== ",") {
                    $whitelist .= ",";
                }//echo $whitelist;
				$whitelistarray=explode(",",(string) $whitelist);$q=count($whitelistarray);
				//print_r($whitelistarray);exit;



				for($i=0;$i<($q-1);$i++)
				{
					$email=strpos($whitelistarray[$i],"@");
					if($email=="")
					{
						//check valid domain
						$isdomain=$this->checkDomain($whitelistarray[$i]);//echo $isdomain;

						$domain=explode("/",(string) $isdomain);//print_r($domain);exit;
						if($domain[1]=="false")
						{
							$flag2=0;
							$msg=$domain[0];
						}
						else
						{
							$wdomaincount++;
							$whitelistdomain[$p]=$whitelistarray[$i];$p++;
						}

					}
					else
					{
						//check valid email;

						$isemail=$this->isValid($whitelistarray[$i]);
						$emailid=explode("/",(string) $isemail);
						if($emailid[1]=='false')
						{
							$flag1=0;
							$msg=$emailid[0];
						}
						else
						{
							$wemailcount++;
							$whitelistmail[$r]=$whitelistarray[$i];$r++;
						}
					}
					//echo $email;
				}


				for($i=0;$i<($p-1);$i++)
				{
					for($j=$i+1;$j<$p;$j++)
					{
						if(trim($whitelistdomain[$i]) === trim($whitelistdomain[$j]))
						{
							$flag=0;
							$msg=$this->getmessage(247);
							header("Location:".$this->url("settings/antispamsettings/$msg/$whitelistreturn/$blacklistreturn"));
							exit(0);
						}
					}
				}

				for($i=0;$i<($r-1);$i++)
				{
					for($j=$i+1;$j<$r;$j++)
					{
						if(trim($whitelistmail[$i]) === trim($whitelistmail[$j]))
						{
							$flag=0;
							$msg=$this->getmessage(248);
							header("Location:".$this->url("settings/antispamsettings/$msg/$whitelistreturn/$blacklistreturn"));
							exit(0);
						}
					}
				}

				//$whitelistcount=count($whitelistarray);//echo substr($whitelistcount,0,-1);
			}

			$blacklist=$_POST['blacklist'];//echo $blacklist;
			if($blacklist!="")
			{
				$blacklistreturn=$blacklist;
				$bcount=strlen((string) $blacklist);
				$r=substr((string) $blacklist,$bcount-1,1);
				if ($r !== ",") {
                    $blacklist .= ",";
                }

				$blacklistarray=explode(",",(string) $blacklist);$p=count($blacklistarray);
				$blacklistcount=count($blacklistarray);//echo $blacklistcount;


				for($i=0;$i<($p-1);$i++)
				{
					$email=strpos($blacklistarray[$i],"@");
					if($email=="")
					{
						//check valid domain
						$isdomain=$this->checkDomain($blacklistarray[$i]);//echo $isdomain;

						$domain=explode("/",(string) $isdomain);//print_r($domain);
						if($domain[1]=="false")
						{
							$flag4=0;
							$msg=$domain[0];
						}
						else
						{
							$bdomaincount++;
							$blacklistdomain[$m]=$blacklistarray[$i];$m++;
						}

					}
					else
					{
						//check valid email;

						$isemail=$this->isValid($blacklistarray[$i]);
						$emailid=explode("/",(string) $isemail);
						if($emailid[1]=='false')
						{
							$flag3=0;
							$msg=$emailid[0];
						}
						else
						{
							$bemailcount++;
							$blacklistmail[$n]=$blacklistarray[$i];$n++;
						}
					}
					//echo $email;
				}

				for($i=0;$i<($n-1);$i++)
				{
					for($j=$i+1;$j<$n;$j++)
					{
						if(trim($blacklistmail[$i]) === trim($blacklistmail[$j]))
						{
							$flag=0;
							$msg=$this->getmessage(249);
							header("Location:".$this->url("settings/antispamsettings/$msg/$whitelistreturn/$blacklistreturn"));
							exit(0);
						}
					}
				}

				for($i=0;$i<($m-1);$i++)
				{
					for($j=$i+1;$j<$m;$j++)
					{
						if(trim($blacklistdomain[$i]) === trim($blacklistdomain[$j]))
						{
							$flag=0;
							$msg=$this->getmessage(250);
							header("Location:".$this->url("settings/antispamsettings/$msg/$whitelistreturn/$blacklistreturn"));
							exit(0);
						}
					}
				}
				//print_r($blacklistdomain);print_r($whitelistdomain);
			}

			if(($flag1==1)&&($flag2==1)&&($flag3==1)&&($flag4==1))
			{


				for($i=0;$i<$wdomaincount;$i++)
				{
					for($j=0;$j<$bdomaincount;$j++)
					{

						if(trim($whitelistdomain[$i]) === trim($blacklistdomain[$j]))
						{
							$msg=$this->getmessage(193);
							$msg=str_replace('{domain}',$whitelistdomain[$i],$msg);
							//$userid1=$this->getId();
							//$username1=$this->getusername($userid1);
							$this->saveLogs("Settings Updation","$username1 has updated his/her settings");

							header("Location:".$this->url("settings/antispamsettings/$msg/$whitelistreturn/$blacklistreturn"));
							exit(0);

						}
					}
				}

				for($i=0;$i<$wemailcount;$i++)
				{
					for($j=0;$j<$bemailcount;$j++)
					{

						if(trim($whitelistmail[$i]) === trim($blacklistmail[$j]))
						{
							$msg=$this->getmessage(194);
							$msg=str_replace('{email}',$whitelistmail[$i],$msg);
							header("Location:".$this->url("settings/antispamsettings/$msg/$whitelistreturn/$blacklistreturn"));
							exit(0);

						}
					}
				}


				
				$select->delete("nesote_email_whitelist_mail");
				$select->where("clientid=?",[$userid]);
				$select->query();

				
				$select->delete("nesote_email_whitelist_server");
				$select->where("clientid=?",[$userid]);
				$select->query();

				
				$select->delete("nesote_email_blacklist_mail");
				$select->where("clientid=?",[$userid]);
				$select->query();

				
				$select->delete("nesote_email_blacklist_server");
				$select->where("clientid=?",[$userid]);
				$select->query();


				
				//print_r($whitelistdomain);
				for($i=0;$i<$wdomaincount;$i++)
				{
					if($whitelistdomain[$i]!="")
					{
						//echo "do";
						$select->insert("nesote_email_whitelist_server");
						$select->fields("server,clientid");
						$select->values([$whitelistdomain[$i],$userid]);
						$select->query();//echo $db->getQuery();exit;


					}
				}

				for($i=0;$i<$bdomaincount;$i++)
				{
					if($blacklistdomain[$i]!="")
					{
						$select->insert("nesote_email_blacklist_server");
						$select->fields("server,clientid");
						$select->values([$blacklistdomain[$i],$userid]);
						$select->query();//echo $db->getQuery();exit;
					}
				}

				for($i=0;$i<$wemailcount;$i++)
				{

					if($whitelistmail[$i]!="")
					{


						$select->insert("nesote_email_whitelist_mail");
						$select->fields("mailid,clientid");
						$select->values([$whitelistmail[$i],$userid]);
						$select->query();//echo $db->getQuery();exit;
					}
				}

				for($i=0;$i<$bemailcount;$i++)
				{

					if($blacklistmail[$i]!="")
					{
						$select->insert("nesote_email_blacklist_mail");
						$select->fields("mailid,clientid");
						$select->values([$blacklistmail[$i],$userid]);
						$select->query();
					}
				}

				$msg=$this->getmessage(195);
				$userid1=$this->getId();
				$username1=$this->getusername($userid1);
				$this->saveLogs("Settings Updation","$username1 has updated his/her settings");

				header("Location:".$this->url("settings/antispamsettings/$msg"));
				exit(0);
			}
			else
			{

				header("Location:".$this->url("settings/antispamsettings/$msg"));
				exit(0);
			}
		}
	}


function getlang_id($lang_code)
    {
	
	    $db=new NesoteDALController();
		$db->select("nesote_email_languages");
		$db->fields("id");
		$db->where("lang_code=?",$lang_code);
		$result=$db->query();
		$data=$db->fetchRow($result);
		$lang_id=$data[0];
		if ($lang_id=="") {
            $lang_id=1;
        }
		
		return $lang_id;
    }	
	
	function getmessage($msg_id)
	{

		$db=new NesoteDALController();

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",'default_language');
		$result=$db->query();
		$data4=$db->fetchRow($result);
		$defaultlang_code=$data4[0];
		if ($defaultlang_code=="") {
            $defaultlang_code='eng';
        }
		

		
		if(isset ($_COOKIE['lang_mail']))
		{
			$lang_code=$_COOKIE['lang_mail'];
			$lang_id=$this->getlang_id($lang_code);

		}
		else
		{

			$lang_id=$this->getlang_id($defaultlang_code);
			setcookie("lang_mail",$lang_code, ['expires' => 0, 'path' => "/"]);

		}

		if($lang_id!="")
		{
           

			$tot=$db->total("nesote_email_messages","msg_id=? and lang_id=?",[$msg_id,$lang_id]);
			//echo $db->getQuery();
			if($tot!=0)
			{

				$db->select("nesote_email_messages");
				$db->fields("wordscript");
				$db->where("msg_id=? and lang_id=?", [$msg_id,$lang_id]);
				$result=$db->query();
				$row=$db->fetchRow($result);
				return html_entity_decode((string) $row[0]);
			}
			else
			{
				$tot=$db->total("nesote_email_messages","msg_id=? and lang_id=?",[$msg_id,$lang_id]);
				if($tot!=0)
				{

					$db->select("nesote_email_messages");
					$db->fields("wordscript");
					$db->where("msg_id=? and lang_id=?", [$msg_id,$lang_id]);
					$result=$db->query();
					$row=$db->fetchRow($result);
					return html_entity_decode((string) $row[0]);
				}

				else
				{
					$db->select("nesote_email_messages");
					$db->fields("wordscript");
					$db->where("msg_id=? and lang_id=?", [$msg_id,1]);
					$result=$db->query();
					$row=$db->fetchRow($result);
					return html_entity_decode((string) $row[0]);
				}
			}

		}
		else
		{

			$db->select("nesote_email_messages");
			$db->fields("wordscript");
			$db->where("msg_id=? and lang_id=?", [$msg_id,$lang_id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			return html_entity_decode((string) $row[0]);
		}

	}
//	function getmessage($msg_id)
//	{
//
//		$db=new NesoteDALController();
//
//		$db->select("nesote_email_settings");
//		$db->fields("value");
//		$db->where("name=?",'default_language');
//		$result=$db->query();
//		$data4=$db->fetchRow($result);
//		$defaultlang_id=$data4[0];
//		if($defaultlang_id=="")
//		$defaultlang_id=1;
//
//		
//
//		if(isset ($_COOKIE['lang_id']))
//		{
//			$lang_id=$_COOKIE['lang_id'];
//		}
//		else
//		{
//
//			$lang_id=$defaultlang_id;
//
//			setcookie("lang_id",$lang_id,0,"/");
//
//		}
//		if($lang_id!="")
//		{
//
//			
//			$tot=$db->total("nesote_email_messages","msg_id=? and lang_id=?",array($msg_id,$lang_id));
//			//echo $db->getQuery();
//			if($tot!=0)
//			{
//
//				$db->select("nesote_email_messages");
//				$db->fields("wordscript");
//				$db->where("msg_id=? and lang_id=?", array($msg_id,$lang_id));
//				$result=$db->query();
//				$row=$db->fetchRow($result);
//				return $row[0];
//			}
//			else
//			{
//				$tot=$db->total("nesote_email_messages","msg_id=? and lang_id=?",array($msg_id,$defaultlang_id));
//				if($tot!=0)
//				{
//
//					$db->select("nesote_email_messages");
//					$db->fields("wordscript");
//					$db->where("msg_id=? and lang_id=?", array($msg_id,$defaultlang_id));
//					$result=$db->query();
//					$row=$db->fetchRow($result);
//					return $row[0];
//				}
//
//				else
//				{
//					$db->select("nesote_email_messages");
//					$db->fields("wordscript");
//					$db->where("msg_id=? and lang_id=?", array($msg_id,1));
//					$result=$db->query();
//					$row=$db->fetchRow($result);
//					return $row[0];
//				}
//			}
//
//		}
//		else
//		{
//		
//			$db->select("nesote_email_messages");
//			$db->fields("wordscript");
//			$db->where("msg_id=? and lang_id=?", array($msg_id,$defaultlang_id));
//			$result=$db->query();
//			$row=$db->fetchRow($result);
//			return $row[0];
//		}
//	}
		function validateUser()
	{
		    $db=new NesoteDALController();
		    $this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			$portal_status=$settings->getValue("portal_status");
		if($portal_status==0)
		{		
			$username=$_COOKIE['e_username'];
			$password=$_COOKIE['e_password'];
			
			$db->select("nesote_liberyus_users");
			$db->fields("*");
			$db->where("username=? and password=? and status=?", [$username,$password,1]);
			$result=$db->query();
			$no=$db->numRows($result);
			if($no!=1)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$password=$_COOKIE['e_password'];
           
			$db->select("nesote_liberyus_users");
			$db->fields("*");
			$db->where("username=? and password=? and status=?", [$username,$password,1]);
			$result=$db->query();//echo $db->getQuery();
			$results=$db->fetchRow($result);
			
			 $no=$db->numRows($result);
			if($no>0)
			{ 
	            $userid=$results[0];
					
				$db->select("nesote_email_usersettings");
				$db->fields("time_zone,server_password,smtp_username");
				$db->where("userid=?",$userid);
				$res=$db->query();
				$result=$db->fetchRow($res);
				if ($result[0]!="" && $result[1]!="") {
                    return TRUE;
                } else
				{
				header("Location:".$this->url("user/portal_registration"));
				exit(0);
				}
			}
			else
			{
				$this->loadLibrary('Settings');
				$settings=new Settings('nesote_email_settings');
				$settings->loadValues();
				$portal_status=$settings->getValue("portal_status");
				$portal_installation_url=$settings->getValue("portal_installation_url");
				
		        $servicekey_rev=strrev((string) $portal_installation_url); 
				$servicekey=substr($servicekey_rev,0,strpos($servicekey_rev,"/"));
				$servicekey1=$servicekey;
				$servicekey=str_replace($servicekey1,"",$servicekey_rev);
				$servicekey=strrev($servicekey)."index.php?page=index/login";
				header("Location:".$servicekey);
				     exit(0);
			}
		}
	}
	
	function getId()
	{
		$username=$_COOKIE['e_username'];
		$password=$_COOKIE['e_password'];
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("*");
		$db->where("username=? and password=? and status=?", [$username,$password,1]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		return $rs[0];

	}

	function getname($id)
	{
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("name");
		$db->where("id=?", [$id]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		return $rs[0];
	}
	function setlanguageAction()
	{
		$lang=$this->getParam(1); $url="";$userid=$this->getId();

		if($lang!="")
		{
			$db=new NesoteDALController();
			$db->update("nesote_email_usersettings");
			$db->set("lang_id=?",[$lang]);
			$db->where("userid=?",[$userid]);//echo $db->getQuery();
			$db->query();

			//setcookie("lang_id",$lang,"0","/");
			$url=$this->url("settings/settings/1");
			echo $url;die;
		}
		else
		{

			$url=$this->url("settings/settings/1");
			echo $url;die;
		}
	}
	function getmailid($id)
	{


		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='emailextension'");
		$result=$db->query();
		$row=$db->fetchRow($result);
		$emailextension = stristr(trim((string) $row[0]),"@") != "" ? trim((string) $row[0]) : "@".trim((string) $row[0]);

		
		$db->select("nesote_liberyus_users");
		$db->fields("username");
		$db->where("id=?", [$id]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		return $rs[0].$emailextension;

	}

	function getfilters($id)
	{
		$userid=$this->getId();

		$db=new NesoteDALController();
		$db->select("nesote_email_emailfilters");
		$db->fields("*");
		$db->where("id=? and userid=?", [$id,$userid]);
		$result=$db->query();
		$row=$db->fetchRow($result);

		$ruletype1 = $row[1] != "" ? $this->getmessage(54) : "";

		$ruletype2 = $row[2] != "" ? $this->getmessage(34) : "";

		$ruletype3 = $row[3] != "" ? $this->getmessage(88) : "";

		if ($row[4]==1) {
            $from_match=$this->getmessage(89);
        } elseif ($row[4]==2) {
            $from_match=$this->getmessage(90);
        } elseif ($row[4]==3) {
            $from_match=$this->getmessage(91);
        } elseif ($row[4]==4) {
            $from_match=$this->getmessage(92);
        }


		if ($row[5]==1) {
            $subject_match=$this->getmessage(89);
        } elseif ($row[5]==2) {
            $subject_match=$this->getmessage(90);
        } elseif ($row[5]==3) {
            $subject_match=$this->getmessage(91);
        } elseif ($row[5]==4) {
            $subject_match=$this->getmessage(92);
        }


		if ($row[6]==1) {
            $body_match=$this->getmessage(89);
        } elseif ($row[6]==2) {
            $body_match=$this->getmessage(90);
        } elseif ($row[6]==3) {
            $body_match=$this->getmessage(91);
        } elseif ($row[6]==4) {
            $body_match=$this->getmessage(92);
        }

		//echo $match;
		$from_value=$row[1];//echo $value;
		$subject_value=$row[2];
		$body_value=$row[3];

		$folder=$row[7];//echo $folder;
		if ($row[7]==1) {
            $folder=$this->getmessage(19);
        } elseif ($row[7]==2) {
            $folder=$this->getmessage(20);
        } elseif ($row[7]==3) {
            $folder=$this->getmessage(21);
        } elseif ($row[7]==4) {
            $folder=$this->getmessage(12);
        } elseif ($row[7]==5) {
            $folder=$this->getmessage(22);
        } elseif ($row[7]>=10) {
            $db->select("nesote_email_customfolder");
            $db->fields("name");
            $db->where("id=? and userid=?", [$row[7],$userid]);
            $result=$db->query();
            $row1=$db->fetchRow($result);
            $folder=$row1[0];
        }
		$folder="<b><span class='movedfolder'>$folder</span></b>";
		$s=$this->getmessage(96);
		$str=str_replace("{rule_from}",$ruletype1,$s);
		if($from_value!="")
		{
			$str=str_replace('/',"",$str);
			$str=str_replace('{from_match}',$from_match,$str);
			$str=str_replace('{from_value}',$from_value,$str);
			$str=str_replace('{end1}',"<br>",$str);
		}
		else
		{
			$str=str_replace('{from_match}',"",$str);
			$str=str_replace("'{from_value}'","",$str);
			$str=str_replace('{end1}',"",$str);
		}


		$str=str_replace('{rule_subject}',$ruletype2,$str);
		if($subject_value!="")
		{

			$str=str_replace('/',"",$str);
			$str=str_replace('{subject_match}',$subject_match,$str);

			$str=str_replace('{subject_value}',$subject_value,$str);
			$str=str_replace('{end2}',"<br>",$str);

		}
		else
		{
			$str=str_replace('{subject_match}',"",$str);
			$str=str_replace("'{subject_value}'","",$str);
			$str=str_replace('{end2}',"",$str);
		}


		$str=str_replace('{rule_body}',$ruletype3,$str);
		if($body_value!="")
		{


			$str=str_replace('{body_match}',$body_match,$str);

			$str=str_replace('{body_value}',$body_value,$str);

		}
		else
		{
			$str=str_replace('{body_match}',"",$str);

			$str=str_replace("'{body_value}'","",$str);
		}

		$str=str_replace('{folder}',$folder,$str);echo $str;
	}

	function checkwhitelistrepeat($mailid,$wtable)
	{
		$userid=$this->getId();

		if($wtable==1)
		{
			$table1="nesote_email_whitelist_server";
			$table2="nesote_email_blacklist_server";
			$value="server";
		}
		else
		{
			$table1="nesote_email_whitelist_mail";
			$table2="nesote_email_blacklist_mail";
			$value="mailid";
		}
		$db=new NesoteDALController();

		$no=$db->total("$table1","$value=?",[$mailid]);
		$no1=$db->total("$table2","$value=?",[$mailid]);
		if($no==0 && $no1==0)
		{
			$db->delete("$table1");
			$db->where("$value=? and clientid=?",[$mailid,$userid]);
			$db->query();echo $db->getQuery();exit;
		}
		else {
            return false;
        }
	}

	function checkblacklistrepeat($mailid,$btable)
	{

		if($wtable==1)
		{
			$table1="nesote_email_blacklist_server";
			$table2="nesote_email_whitelist_server";
			$value="server";
		}
		else
		{
			$table1="nesote_email_blacklist_mail";
			$table2="nesote_email_whitelist_mail";
			$value="mailid";
		}

		$db=new NesoteDALController();

		$no=$db->total("$table1","$value=?",[$mailid]);
		$no1=$db->total("$table2","$value=?",[$mailid]);
		if($no==0 && $no1==0)
		{

			$db->delete("$table1");
			$db->where("$value=? and clientid=?",[$mailid,$userid]);
			$db->query();echo $db->getQuery();exit;
		}
		else {
            return false;
        }
	}
	function getusername($id)
	{

		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("username");
		$db->where("id=?",[$id]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		return $row[0];
	}
	function saveLogs($operation,$comment)
	{
		$userid=$this->getId();

		$insert=new NesoteDALController();
		$insert->insert("nesote_email_client_logs");
		$insert->fields("uid,operation,comment,time");
		$insert->values([$userid,$operation,$comment,time()]);
		$insert->query();
	}

	function isValid($email)
	{
		$result ="true";$msg="";$false="false";
		if(!preg_match("#^[_a-z0-9\\-]+(\\.[_a-z0-9\\-]+)*@[a-z0-9\\-]+(\\.[a-z0-9\\-]+)*(\\.[a-z]{2,4})\$#mi", (string) $email))
		{
			$msg=$this->getmessage(159);
			$result =$msg."/".$false;
		}
		;
		return $result;
	}

	function checkDomain($nname)
	{

		$arr=[];

		$arr1 ="
.com,.net,.org,.biz,.coop,.info,.museum,.name,.pro,.edu,.gov,.int,.mil,.ac,.ad,.ae,.af,.ag,
.ai,.al,.am,.an,.ao,.aq,.ar,.as,.at,.au,.aw,.az,.ba,.bb,.bd,.be,.bf,.bg,.bh,.bi,.bj,.bm,
.bn,.bo,.br,.bs,.bt,.bv,.bw,.by,.bz,.ca,.cc,.cd,.cf,.cg,.ch,.ci,.ck,.cl,.cm,.cn,.co,.cr,
.cu,.cv,.cx,.cy,.cz,.de,.dj,.dk,.dm,.do,.dz,.ec,.ee,.eg,.eh,.er,.es,.et,.fi,.fj,.fk,.fm,
.fo,.fr,.ga,.gd,.ge,.gf,.gg,.gh,.gi,.gl,.gm,.gn,.gp,.gq,.gr,.gs,.gt,.gu,.gv,.gy,.hk,.hm,
.hn,.hr,.ht,.hu,.id,.ie,.il,.im,.in,.io,.iq,.ir,.is,.it,.je,.jm,.jo,.jp,.ke,.kg,.kh,.ki,
.km,.kn,.kp,.kr,.kw,.ky,.kz,.la,.lb,.lc,.li,.lk,.lr,.ls,.lt,.lu,.lv,.ly,.ma,.mc,.md,.mg,
.mh,.mk,.ml,.mm,.mn,.mo,.mp,.mq,.mr,.ms,.mt,.mu,.mv,.mw,.mx,.my,.mz,.na,.nc,.ne,.nf,.ng,
.ni,.nl,.no,.np,.nr,.nu,.nz,.om,.pa,.pe,.pf,.pg,.ph,.pk,.pl,.pm,.pn,.pr,.ps,.pt,.pw,.py,
.qa,.re,.ro,.rw,.ru,.sa,.sb,.sc,.sd,.se,.sg,.sh,.si,.sj,.sk,.sl,.sm,.sn,.so,.sr,.st,.sv,
.sy,.sz,.tc,.td,.tf,.tg,.th,.tj,.tk,.tm,.tn,.to,.tp,.tr,.tt,.tv,.tw,.tz,.ua,.ug,.uk,.um,
.us,.uy,.uz,.va,.vc,.ve,.vg,.vi,.vn,.vu,.ws,.wf,.ye,.yt,.yu,.za,.zm,.zw";//print_r($arr1);
		$arr=explode(",",$arr1);//print_r($arr);


		$mai=$nname;$dot=0;$length=0;$dname="";
		$val=true;
		$length=strlen((string) $mai);
		$dot =strripos((string) $mai,".");//echo $dot;
		$dname =substr((string) $mai,0,$dot);//echo $dname;

		$ext =substr((string) $mai,$dot,$length);//echo $ext;

		$msg="";$false="false";
		//alert(ext);

		if (($dot>2) && ($dot<57)) {
            $counter = count($arr);
            for($i=0; $i<$counter; $i++)
			{
				if(trim($ext) === trim($arr[$i]))
				{
					$val ="true";
					break;
				}

				else
				{
					$val ="false";
				}
			}
            //echo $val;
            if($val === "false")
			{

				$msg=$this->getmessage(251);
				$msg=str_replace('{extension}',$ext,$msg);
				return $msg."/".$false;
			}
			else
			{
				for($j=0; $j<strlen($dname); $j++)

				{
					$dh =substr($dname,$j,1);
					$hh = ord($dh);
					if((($hh > 47) && ($hh<59)) || (($hh > 64) && ($hh<91)) || (($hh > 96) && ($hh<123)) || ($hh==45) || ($hh==46))
					{
						if(($j==0 || $j==strlen($dname)-1) && ($hh == 45))

						{
							$msg=$this->getmessage(252);
							return $msg."/".$false;
						}
					}

					else	{
						$msg=$this->getmessage(253);
						return $msg."/".$false;
					}
				}
			}
        } else
		{
			$msg=$this->getmessage(254);
			return $msg."/".$false;
		}



		return "true";
	}

	function isEmail($email)
	{
		$result =true;
		if(!preg_match("#^[_a-z0-9\\-]+(\\.[_a-z0-9\\-]+)*@[a-z0-9\\-]+(\\.[a-z0-9\\-]+)*(\\.[a-z]{2,4})\$#mi", (string) $email))
		{
			return false;
		}
		;
		return $result;
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

	function uploadsoundAction()
	{
		$userid=$this->getId();
		$uploaddir = "userdata/$userid/";
		if(!is_dir("$uploaddir"))

		{

			mkdir("$uploaddir",0777);

		}
		$db= new NesoteDALController();
		$alertsound=$_FILES['alertsound']['name'];
		$file = $uploaddir . basename((string) $_FILES['alertsound']['name']);

		if (move_uploaded_file($_FILES['alertsound']['tmp_name'], $file))
		{

			$attachments_path=$uploaddir;



			$db->select("nesote_chat_users");
			$db->fields("soundspath");
			$db->where("userid=?",[$userid]);
			$result=$db->query();
			$row=$db->fetchRow($result);

			
			$db->update("nesote_chat_users");
			$db->set("soundspath=?",$alertsound);
			$db->where("userid=?",[$userid]);
			$db->query();//echo $db->getQuery();

			unlink($uploaddir."/".$row[0]);

			echo "success"; exit;


		}

		else
		{
			echo "error";exit;

		}

	}
	function chatsettingsAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$userid=$this->getId();$msg="";


		$userid=$this->getId();
		$select=new NesoteDALController();
		$select->select("nesote_chat_users");
		$select->fields("*");
		$select->where("userid=?",[$userid]);
		$result=$select->query();
		$select->fetchRow($result);
		$num=$select->numRows($result);
		$this->setValue("num",$num);
		$this->setLoopValue("settings",$result->getResult());

		
		$select->select("nesote_chatwindow_settings");
		$select->fields("*");
		$fet=$select->query();
		$this->setLoopValue("window",$fet->getResult());

		
		$select->select("nesote_chat_users");
		$select->fields("chatwindowsize,image");
		$select->where("userid=?",$userid);
		$fet=$select->query();
		$rowf=$select->fetchRow($fet);
		$this->setValue("windowsize",$rowf[0]);
		$imgpath = $rowf[1] == "" ? "images/nophoto.gif" : "userdata/$userid/$rowf[1]";
		$this->setValue("imgpath",$imgpath);
		
		$select->select("nesote_chat_settings");
		$select->fields("value");
		$select->where("name=?",\PICTURE_FORMAT);
		$select=$select->query();
		$rs3=$select->fetchRow($result3);
		$this->setValue("picture_format",$rs3[0]);
		$picturevalidformats=$rs3[0];


		if($_POST !== [])
		{

if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
					
				$msg=$this->getmessage(340);
				header("location:".$this->url("settings/chatsettings/340"));
				exit(0);
			}
			$userid=$this->getId();

			$chathistory=$_POST['chathistory'];//echo $defaultview;
			$windowsize=$_POST['windowsize'];
			$sounds=$_POST['sounds'];

			$smileys=$_POST['smileys'];//echo $smileys;exit;


			$attachments_path="userdata";
			
			$picture=$_FILES['picture']['name'];
			$picturefilename=$_FILES['picture']['name'];
			$picturetemp=$_FILES['picture']['tmp_name'];

			//$picturevalidformats="jpeg,jpg,png,gif";

			$select=new NesoteDALController();
			$flag=1;
			if($picturefilename!="")
			{


				if(!is_dir("$attachments_path/$userid"))

				{

					mkdir("$attachments_path/$userid",0777);

				}

				$p_name_length=-(strlen((string) $picturefilename)-1);

				$check_string=substr((string) $picturefilename, strrpos((string) $picturefilename,'.')+1);

				$substrng=strtolower($check_string);

				$picturefilename= substr_replace($picturefilename, $substrng,strrpos((string) $picturefilename,'.')+1);

				$fileext=explode(",",(string) $picturevalidformats);

				$check_string=substr($picturefilename, strrpos($picturefilename,'.')+1);

				$flag=0;
                $counter = count($fileext);

				for($i=0;$i<$counter;$i++)

				{

					if(trim($fileext[$i]) === $check_string)

					{

						if (move_uploaded_file($picturetemp, "$attachments_path/$userid/".$picturefilename))
						{
							$flag=1;

							break;
						}
						//$picturetemp=copy($picturetemp,"$attachments_path/$userid/".$picturefilename);//echo $temp;exit;

						else
						{
							
							header("Location:".$this->url("settings/chatsettings/479"));
							exit(0);
						}

					}

				}
			}

			if ($flag==0) {
                header("Location:".$this->url("settings/chatsettings/413"));
                exit(0);
            } elseif ($flag === 1) {
                $select->update("nesote_chat_users");
                $select->set("chathistory=?,chatwindowsize=?,sounds=?,smileys=?",[$chathistory,$windowsize,$sounds,$smileys]);
                $select->where("userid=?",[$userid]);
                $select->query();
                //echo $db->getQuery();
                if($picture!="")
				{
					
					$select->select("nesote_chat_users");
					$select->fields("image");
					$select->where("userid=?",[$userid]);
					$result=$select->query();
					$row=$select->fetchRow($result);
					$select->update("nesote_chat_users");
					$select->set("image=?",$picture);
					$select->where("userid=?",[$userid]);
					$select->query();//echo $db->getQuery();

					unlink("userdata/".$userid."/".$row[0]);
				}
                $username=$this->getusername($userid);
                $this->saveLogs("Settings Updation","$username has updated his/her chat settings");
                //$msg=$this->getmessage(195);
                header("Location:".$this->url("settings/chatsettings/195"));
                exit(0);
            }



		}
		else
		{
			$msg="";
			$msg1=$this->getParam(1);
			if ($msg1!="") {
                $msg=$this->getmessage($msg1);
            }
			$this->setValue("msg",$msg);
			
		}

	}

	function updatewebsettingsAction()
	{ 
		$langid=$_POST['language'];
		$themeid=$_POST['themes'];
		$mails_per_page=$_POST['mails_per_page'];
		$timezone=$_POST['timezone'];
		$signature=$_POST['content'];
		$signflag=$_POST['signval'];
		$shortcuts=$_POST['shortcuts'];

		$signature=html_entity_decode((string) $signature,ENT_QUOTES,"UTF-8");
		$userid=$this->getId();
		
        $this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$portal_status=$settings->getValue("portal_status");
		$override_themes=$settings->getValue("override_themes");
		
		$db=new NesoteDALController();
		$db->update("nesote_email_usersettings");
		if ($portal_status==0) {
            if ($signflag==1) {
                if ($override_themes==1) {
                    $db->set("signature=?,signatureflag=?,lang_id=?,theme_id=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signature,$signflag,$langid,$themeid,$mails_per_page,$shortcuts,$timezone]);
                } else {
                    $db->set("signature=?,signatureflag=?,lang_id=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signature,$signflag,$langid,$mails_per_page,$shortcuts,$timezone]);
                }
            } elseif ($signflag==0) {
                if ($override_themes==1) {
                    $db->set("signatureflag=?,lang_id=?,theme_id=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signflag,$langid,$themeid,$mails_per_page,$shortcuts,$timezone]);
                } else {
                    $db->set("signatureflag=?,lang_id=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signflag,$langid,$mails_per_page,$shortcuts,$timezone]);
                }
            }
        } elseif ($signflag==1) {
            if ($override_themes==1) {
                $db->set("signature=?,signatureflag=?,theme_id=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signature,$signflag,$themeid,$mails_per_page,$shortcuts,$timezone]);
            } else {
                $db->set("signature=?,signatureflag=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signature,$signflag,$mails_per_page,$shortcuts,$timezone]);
            }
        } elseif ($signflag==0) {
            if ($override_themes==1) {
                $db->set("signatureflag=?,theme_id=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signflag,$themeid,$mails_per_page,$shortcuts,$timezone]);
            } else {
                $db->set("signatureflag=?,mails_per_page=?,shortcuts=?,time_zone=?",[$signflag,$mails_per_page,$shortcuts,$timezone]);
            }
        }
		$db->where("userid=?",[$userid]);
		$db->query();
		
//		$db->update("nesote_liberyus_users");
//		$db->set("time_zone=?",array($timezone));
//		$db->where("id=?",array($userid));
//		$db->query();

		$username=$this->getusername($userid);
		$this->saveLogs("Settings Updation","$username has updated his/her settings");
		if($portal_status==0)
		{
		setcookie("lang_mail",(string) $langid, ['expires' => "0", 'path' => "/"]);
		}
		$this->getmessage(195);
		echo "success";exit;
	}
	function shortcutkeysAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		
			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();			
			$servicename=$settings->getValue("engine_name");
			$this->setValue("servicename",$servicename);
			
			$db= new NesoteDALController();
			$db->select("nesote_email_shortcuts");
			$db->fields("keyvalue,description,id");
			$result=$db->query();
			$db->fetchRow($result);
			$this->setLoopValue("sh",$result->getResult());
			
			
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='public_page_logo'");
		$result1=$db->query();
		$row1=$db->fetchRow($result1);
		$img=$row1[0];
		$imgpath="admin/logo/".$img;
		$this->setValue("imgpath",$imgpath);
			
			$memorymsg=$this->getmessage(351);
			$year=date("Y",time());
			$msg1=str_replace('{year}',$year,$memorymsg);
			$this->setValue("footer",$msg1);
			
			$select=new NesoteDALController();
			$select->select("nesote_email_usersettings");
			$select->fields("theme_id");
			$select->where("userid=?",$userid);
			$result=$select->query();//echo $select->getQuery();
			$res=$select->fetchRow($result);
			$style_id=$res[0];
			if($style_id=="")
			{
				
				$select->select("nesote_email_settings");
				$select->fields("value");
				$select->where("name='themes'");
				$result=$select->query();//echo $select->getQuery();
				$res=$select->fetchRow($result);
				$style_id=$res[0];
					
					
			}
		
			$select->select("nesote_email_themes");
			$select->fields("name,style");
			$select->where("id=?",$style_id);
			$result=$select->query();
			$theme=$select->fetchRow($result);

			$this->setValue("style",$theme[1]);
	}
	function tableid($username)
    {
		$user_name=$username;
		include(__DIR__ . "/config.php");
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