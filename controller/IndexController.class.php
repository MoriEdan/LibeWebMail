<?php
class IndexController extends NesoteController
{
	function indexAction()
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$portal_status=$settings->getValue("portal_status");
		 $db=new NesoteDALController();
		if($portal_status==1)
		{
			header("Location:".$this->url("mail/mailbox"));
				exit(0);
			
		}
		else
		{
		    $mobile_status=$this->mobile_device_detect();
			$path=$_SERVER['SCRIPT_NAME'];
			$path=substr((string) $path,0,strrpos((string) $path,"/"));
			if($mobile_status==true)
			{
			header("location:".$path."/mobile/");
			exit(0);
			}
	
	        $db=new NesoteDALController();
	
			$style_id=$settings->getValue("themes");
						
			$db->select("nesote_email_themes");
			$db->fields("name,style");
			$db->where("id=?",$style_id);
			$result=$db->query();
			$theme=$db->fetchRow($result);
	
			$this->setValue("style",$theme[1]);
			
			if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
			{
	
				$users=$this->avilableusers();
				$curntuser=array_rand($users);
				$demouserid=$users[$curntuser];
				$demouser=$this->getuname($demouserid);
				
				
				if($demouser=="")
	            {
	                $users= ["demouser1", "demouser2", "demouser3", "demouser4", "demouser5", "demouser6", "demouser7", "demouser8", "demouser9", "demouser10"];
	
	                $demouser="demouser";
	                $curntuser=array_rand($users);
	
	                $curntuser += 1;
	
	                $demouser .= $curntuser;//echo $demouser;
	            }
	
	
				$this->setValue("user","$demouser");
				$this->setValue("pass","demodemo");
			}
			else
			{
				$this->setValue("user","");
				$this->setValue("pass","");
			}
			
			$memorymsg=$this->getmessage(351);
			$year=date("Y",time());
			$msg1=str_replace('{year}',$year,$memorymsg);
			$this->setValue("footer",$msg1);
	
			$signupvalue=0;
			$msg=$this->getParam(1);
			if ($msg=="errlog") {
                $msg=$this->getmessage(200);
            }
			$this->setValue("msg",$msg);
	
			$servicename=$settings->getValue("engine_name");
			$this->setValue("servicename",$servicename);
	
			$account_type=$settings->getValue("catchall_mail");
	
			$lang_cookie=$_COOKIE['lang_mail'];
	
	
			$langid=$settings->getValue("default_language");
			if ($langid=="") {
                $langid='eng';
            }
			$this->setValue("defaultlang",$langid);
	
		
			$db->select("nesote_email_settings");
			$db->fields("*");
			$res=$db->query();
			//echo $db->getQuery();
			while($row=$res->fetchRow())
			{
				//echo $row[2];
				$this->setValue("$row[1]","$row[2]");
			}
			
	
			$img=$settings->getValue("public_page_logo");
			$imgpath="admin/logo/".$img;
			$this->setValue("imgpath",$imgpath);
			
			$db->select("nesote_email_languages");
			$db->fields("lang_code,language");
			$db->where("status=?",[1]);
			$db->order("id asc");
			$result=$db->query();
			$this->setLoopValue("lang",$result->getResult());
	
			if($account_type==1)// catchall
			{	
				$public_registration=$settings->getValue("public_registration");
				if($public_registration==1)
				{
					$signupvalue=1;// display signup link
				}
				else {
                    $signupvalue=0;
                }
			}	
			else //individual
			{
			
	            $automatic_account_creation=$settings->getValue("automatic_account_creation");
				if($automatic_account_creation==1)// for automatic account creation
				{
					$signupvalue=0;
	
					$public_registration=$settings->getValue("public_registration");
					if($public_registration==1)
					{
						$signupvalue=1;// display signup link
					}
					else {
                        $signupvalue=0;
                    }
				}
				else {
                    //manually account creation
                    $signupvalue=0;
                }
			}
			$this->setValue("signupvalue",$signupvalue);
			$username=$_COOKIE['e_username'];
			$password=$_COOKIE['e_password'];
	
			$no=$db->total("nesote_liberyus_users","username=? and password=? and status=?",[$username,$password,1]);
			if ($_COOKIE["e_username"] && $no != 0) {
                header("Location:".$this->url("mail/mailbox"));
                exit(0);
            }
		}
	}
	function logincheckAction()
	{
		$username=$_POST['username'];$username=strtolower((string) $username);
		if(strpos($username,"@")!="")
		{
			$uname=explode("@",$username);
			$extn=$this->getextension();
			$udomain="@".$uname[1];
			if($extn!=$udomain)
			{
				$msg="errlog";
				header("Location:".$this->url("index/index/$msg"));
				exit(0);
			}
			else {
                $username=$uname[0];
            }
		}
		$pasword=$_POST['password'];
		$password=md5((string) $pasword);

		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("*");
		$db->where("username=? and password=? and status=?",[$username,$password,1]);
		$result=$db->query();$rr1=$db->fetchRow($result);
		$no=$db->numRows($result);
		//$no=$db->total("nesote_liberyus_users","username=? and password=? and status=?",array($username,$password,1));
		if($no!=0)
		{
			
		        $db->select("nesote_email_usersettings");
				$db->fields("time_zone,server_password,smtp_username,lang_id");
				$db->where("userid=?",$rr1[0]);
				$res=$db->query();
				$result=$db->fetchRow($res);
				if($result[0]=="" || $result[1]=="" )
				{
				header("Location:".$this->url("index/index/errlog"));
				exit(0);
				}
				
				$this->loadLibrary('Settings');
		        $settings=new Settings('nesote_email_settings');
		        $settings->loadValues();
				$default_language=$settings->getValue("default_language");
				if ($default_language===0 || $default_language=="") {
                    $default_language='eng';
                }
				if (is_numeric($default_language)) {
                    $default_language='eng';
                }
		
				if (is_numeric($result[3])) {
                    $result[3]=$default_language;
                }
				
			setcookie("lang_mail","$result[3]", ['expires' => "0", 'path' => "/"]);
			setcookie("e_username","$username", ['expires' => "0", 'path' => "/"]);
			setcookie("e_password","$password", ['expires' => "0", 'path' => "/"]);
			
			setcookie("folderid","1", ['expires' => "0", 'path' => "/"]);
			setcookie("page","1", ['expires' => "0", 'path' => "/"]);
			setcookie("preload","0", ['expires' => "0", 'path' => "/"]);
			setcookie("page_display","1", ['expires' => "0", 'path' => "/"]);
			setcookie("crnt_mailid","0", ['expires' => "0", 'path' => "/"]);
			setcookie("image_display","", ['expires' => "0", 'path' => "/"]);
			setcookie("start","1", ['expires' => "0", 'path' => "/"]);
			setcookie("folder","inbox", ['expires' => "0", 'path' => "/"]);

			$uid=$this->getId($username);
			
			$db->update("nesote_email_usersettings");
			$db->set("lastlogin=?",[1]);
			$db->where("userid=?", [$uid]);
			$db->query();	
			
			$db->update("nesote_chat_users");
			$db->set("logout_status=?,lastupdatedtime=?",[0,time()]);
			$db->where("userid=?", [$uid]);
			$db->query();//echo $db->getQuery();//exit;
			
		                               require(__DIR__ . "/script.inc.php");
                                       include($config_path."database.default.config.php");
                                      
                                       //include("../config/database.default.config.php");
                                       error_reporting(1);
                                       $link =mysql_connect($db_server,$db_username,$db_password);
                                       //mysql_query("set names utf8 collate utf8_unicode_ci");
                                       
                                      mysql_select_db($db_name);
                                      $time=time();
                                      $m=date("m",$time);
                                      
                                      $y=date("Y",$time);
                                       
			  mysql_query("CREATE TABLE IF NOT EXISTS `".$db_tableprefix."nesote_email_ip_".$m.$y."` ( `id` int(11) NOT NULL auto_increment,
			   `userid` int(11) NOT NULL,
			 `ip` varchar(256) NOT NULL,
			 `time` int(11) NOT NULL,
			 `country` varchar(256) NOT NULL,
			 PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
			");
			  
			 include(__DIR__ . "/geo/geoip.inc");
			 $gi = geoip_open("geo/GeoIP.dat",GEOIP_STANDARD);
			 $public_ip=$this->GetUserIP(); //$public_ip="195.117.168.1";
			 $record = geoip_country_code_by_addr($gi, $public_ip);
			 
			 
			 $db->insert("nesote_email_ip_".$m.$y." ");
			 $db->fields("id,userid,ip,time,country");
			 $db->values([0,$uid,$public_ip,$time,$record]);
			 $db->query();
			  

			header("Location:".$this->url("mail/mailbox"));
			exit(0);
		}
		else
		{
			$msg="errlog";
			header("Location:".$this->url("index/index/$msg"));
			exit(0);
		}


	}
		function GetUserIP()
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = $this->CheckIP($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $ip;
            }
            if (isset($_SERVER['HTTP_CLIENT_IP']) && $ip = $this->CheckIP($_SERVER['HTTP_CLIENT_IP'])) {
                return $ip;
            }
            return $_SERVER['REMOTE_ADDR'];
        }


	function CheckIP($ip)
	 {
	   if (empty($ip) ||
	   ($ip >= '10.0.0.0' && $ip <= '10.255.255.255') ||
	   ($ip >= '172.16.0.0' && $ip <= '172.31.255.255') ||
	   ($ip >= '192.168.0.0' && $ip <= '192.168.255.255') ||
	   ($ip >= '169.254.0.0' && $ip <= '169.254.255.255')) {
           return false;
       }
	   return $ip;
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
	function getUID()
	{
		$username=$_COOKIE['e_username'];
		$password=$_COOKIE['e_password'];
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("*");
		$db->where("username=? and password=?", [$username,$password]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		return $rs[0];

	}

	function logoutAction()
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$uid=$this->getUID();$userid=$this->getUID();
		$newtime=time()-90;
		$db1=new NesoteDALController();
		$db=new NesoteDALController();
		
		$db->update("nesote_email_usersettings");
		$db->set("lastlogin=?",[0]);
		$db->where("userid=?", [$uid]);
		$db->query();	
		
		$db->update("nesote_chat_users");
		$db->set("logout_status=?,lastupdatedtime=?",[1,$newtime]);
		$db->where("userid=?", [$uid]);
		$db->query();
	

		$db->select(["u"=>"nesote_chat_session","c"=>"nesote_chat_session_users"]);
		$db->fields("distinct u.id");
		$db->where("u.id=c.chat_id and c.user_id=?",$userid);
		$result=$db->query();//echo $db->getQuery();

		while($row=$db->fetchRow($result))
		{
			$chat_id=$row[0];
			
			$db1->select("nesote_chat_session");
			$db1->fields("group_status");
			$db1->where("id=?", $chat_id);
			$result1=$db1->query();
			$row1=$db1->fetchRow($result1);

			if($row1[0]==1)//group chat
			{
				$fullname=$this->getname($userid);
				$msg=$this->getmessage(428);
				$msg=str_replace("{fullname}","$fullname",$msg);

				$message="\n $msg";
				$db1->select("nesote_chat_session_users");
				$db1->fields("user_id");
				$db1->where("chat_id=? and active_status=? and user_id!=?",[$chat_id,1,$userid]);
				$rs1=$db1->query();
				while($row1=$db1->fetchRow($rs1))
				{

					
					$db->insert("nesote_chat_temporary_messages");
					$db->fields("chat_id,sender,responders,message,time,read_flag");
					$db->values([$chat_id,0,$row1[0],$message,time(),0]);
					$result=$db->query();

				}

			}
		}

	
		$db->select("nesote_chat_session_users");
		$db->fields("id,active_status");
		$db->where("user_id=?",$userid);
		$res0=$db->query();
		$num=$db->numRows($res0);


		if($num>0)
		{
			while($row10=$db->fetchRow($res0))
			{
				
				$db1->update("nesote_chat_session_users");
				$db1->set("active_status=?,typing_status=?",[0,0]);
				$db1->where("user_id=? and id=?",[$userid,$row10[0]]);
				$db1->query();


			}
			//echo $db1->getQuery();
		}

		

		$mails_per_page=$settings->getValue("mails_per_page");
		if (($mails_per_page=="")|| ($mails_per_page==0)) {
            $mails_per_page=25;
        }


		$default_language=$settings->getValue("default_language");
		if ($default_language===0 || $default_language=="") {
            $default_language='eng';
        }


		$themes=$settings->getValue("themes");
		if ($themes==0) {
            $themes=1;
        }

		$display=$settings->getValue("display");
		if ($display==0) {
            $display=1;
        }

		$username="demouser";$password=md5("demodemo");

	
		$db->select("nesote_liberyus_users");
		$db->fields("*");
		$db->where("username=? and password=?",[$username,$password]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		$userid=$rs[0];

		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{
			
			$db->update("nesote_email_usersettings");
			$db->set("lang_id=?,theme_id=?,display=?,mails_per_page=?,signatureflag=?,signature=?",[$default_language,$themes,$display,$mails_per_page,0,""]);
			$db->where("userid=?",[$userid]);
			$result=$db->query();

			setcookie("lang_mail",(string) $default_language, ['expires' => "0", 'path' => "/"]);
		}
        setcookie("lang_mail",(string) $default_language, ['expires' => "0", 'path' => "/"]);
		setcookie("e_username","", ['expires' => "0", 'path' => "/"]);
		setcookie("e_password","", ['expires' => "0", 'path' => "/"]);
		setcookie("image_display","", ['expires' => "0", 'path' => "/"]);
		setcookie("preload","0", ['expires' => "0", 'path' => "/"]);
		setcookie("folderid","0", ['expires' => "0", 'path' => "/"]);
		setcookie("page_display","1", ['expires' => "0", 'path' => "/"]);
		
		
		
		header("Location:".$this->url("index/index"));
		exit(0);
	}

	function getname($id)
	{


		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("name");
		$db->where("id=?",[$id]);
		$rs1=$db->query();
		$row=$db->fetchRow($rs1);
		//return $row[0]." ".$row[1];
		return $row[0];
	}
	function getuname($id)
	{

		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("username");
		$db->where("id=?",[$id]);
		$rs1=$db->query();
		$row=$db->fetchRow($rs1);
		return $row[0];
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

	function forgotpasswordAction()
	{
		

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$select=new NesoteDALController();

		$style_id=$settings->getValue("themes");	

		$select->select("nesote_email_themes");
		$select->fields("name,style");
		$select->where("id=?",$style_id);
		$result=$select->query();
		$theme=$select->fetchRow($result);

		$this->setValue("style",$theme[1]);
			
		$footermsg=$this->getmessage(351);
		$year=date("Y",time());
		$footer=str_replace('{year}',$year,$footermsg);
		$this->setValue("footer",$footer);

		$extension=$this->getextension();
		$this->setValue("extension",$extension);
		$msg=$this->getParam(1);

		if(isset($msg))
		{
			if ($msg=="u") {
                $msg=$this->getmessage(270);
            } elseif ($msg=="e") {
                $msg=$this->getmessage(320);
            } elseif ($msg=="iu") {
                $msg=$this->getmessage(271);
            } elseif ($msg=="ia") {
                $msg=$this->getmessage(340);
            }

			$this->setValue("msg",$msg);
		}
		else {
            $this->setValue("msg",$msg);
        }

		$img=$settings->getValue("public_page_logo");
		$imgpath="admin/logo/".$img;
		$this->setValue("imgpath",$imgpath);


	}
	function forgotpasswordprocessAction()
	{//echo(mktime(0,0,0,04,28,1986));
		//echo md5(sibin);
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$db=new NesoteDALController();

		$style_id=$settings->getValue("themes");
			
		$db->select("nesote_email_themes");
		$db->fields("name,style");
		$db->where("id=?",$style_id);
		$result=$db->query();
		$theme=$db->fetchRow($result);

		$this->setValue("style",$theme[1]);
		$footermsg=$this->getmessage(351);
		$year=date("Y",time());
		$footer=str_replace('{year}',$year,$footermsg);
		$this->setValue("footer",$footer);
		$msg=$this->getParam(2);
		$y=[];

		for($i=$year,$j=0;$i>=1900;$i--,$j++)
		{
			$y[$j][0]=$i;
		}

		$this->setLoopValue("YY",$y);
		
		$d=[];

		for($i=1,$j=0;$i<=31;$i++,$j++)
		{
			if ($i<10) {
                $i="0".$i;
            }
			$d[$j][0]=$i;
		}

		$this->setLoopValue("DD",$d);

		$lang_id=$_COOKIE['lang_mail'];
		if (isset($lang_id)) {
            $lang=$lang_id;
        } else
		{

			$default_lang=$settings->getValue("default_language");
			$lang = $default_lang != "" ? $default_lang : "eng";
		}
		
		 $lang=$this->getlang_id($lang);
		 
		$img=$settings->getValue("public_page_logo");
		$imgpath="admin/logo/".$img;
		$this->setValue("imgpath",$imgpath);

		
		$db->select("nesote_email_months_messages");
		$db->fields("month_id,message");
		$db->where("lang_id=?",[$lang]);
		$result1=$db->query();
		$this->setLoopValue("month",$result1->getResult());

		if(isset($msg))
		{
			if ($msg=="ans") {
                $msg=$this->getmessage(277);
            } elseif ($msg=="q") {
                $msg=$this->getmessage(125);
            } elseif ($msg=="i") {
                $msg=$this->getmessage(149);
            } elseif ($msg=="img") {
                $msg=$this->getmessage(148);
            } elseif ($msg=="e") {
                $msg=$this->getmessage(158);
            } elseif ($msg=="em") {
                $msg=$this->getmessage(159);
            } elseif ($msg=="d") {
                $msg=$this->getmessage(128);
            } elseif ($msg=="m") {
                $msg=$this->getmessage(129);
            } elseif ($msg=="y") {
                $msg=$this->getmessage(130);
            } elseif ($msg=="iu") {
                //user either ans/alt email or dob wrong or both
                $msg=$this->getmessage(280);
            } elseif ($msg=="ia") {
                $msg=$this->getmessage(340);
            }

			$this->setValue("msg",$msg);

			$this->setValue("question",$qusetion);
			$userid=$this->getParam(1);//echo $username;
			$username=$this->getusername($userid);//echo $username;
		}
		else {
            $username=$_POST['username'];
        }
		$flag=1;$err="";


		$userid="";$answer="";

		if($username=="")
		{
			$flag=0;
			$err="u";
			header("Location:".$this->url("index/forgotpassword/$err"));
			exit(0);
			//$msg=$this->getmessage(270);

		}

		else
		{
			if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && ($username == "demouser1" || $username == "demouser2" || $username == "demouser3" || $username == "demouser4" || $username == "demouser5" || $username == "demouser6" || $username == "demouser7" || $username == "demouser8" || $username == "demouser9" || $username == "demouser10")) {
                $msg="ia";
                header("Location:".$this->url("index/forgotpassword/$msg"));
                exit(0);
            }

			$uname=$username.$this->getextension();
			if($this->isEmail($uname)==false)
			{
				$flag=0;
				$err="e";
				header("Location:".$this->url("index/forgotpassword/$err"));
				exit(0);
			}
			else
			{
				
				$db->select("nesote_liberyus_users");
				$db->fields("*");
				$db->where("username=? and status=?",[$username,1]);
				$result=$db->query();//echo $db->getQuery();
				$row=$db->fetchRow($result);
				$num=$db->numRows($result);
				if ($num==0) {
                    $flag=0;
                    $err="iu";
                    header("Location:".$this->url("index/forgotpassword/$err"));
                    exit(0);
                } elseif ($num==1) {
                    $userid=$row[0];
                    $db->select("nesote_email_usersettings");
                    $db->fields("remember_answer,alternate_email");
                    $db->where("userid=?",[$userid]);
                    $result=$db->query();
                    $row=$db->fetchRow($result);
                    $answer=$row[0];
                    $alteremail=$row[1];
                }
			}

		}

		$this->setValue("msg",$msg);
		$this->setValue("uid",$userid);
		$this->setValue("uname",$username);
		//$this->setValue("question",$qusetion);
		$this->setValue("answer",$answer);
		$this->setValue("alteremail",$alteremail);

	}

	function getId($username)
	{

		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("id");
		$db->where("username=?", [$username]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		return $rs[0];

	}

	 function avilableusers()
    {
        $str=[];

        $db=new NesoteDALController();
        $db->select("nesote_liberyus_users");
        $db->fields("id");
        $db->where("id<=12 and id>=3  and status=?", [1]);
        $result=$db->query();
        while($rs=$db->fetchRow($result))
        {
            $st.=$rs[0].",";
        }

        $st.=substr($st,0,-1);

        
        $db->select("nesote_chat_users");
        $db->fields("distinct userid");
		if ($st !== "") {
            $db->where("logout_status=? and userid IN($st)", [1]);
        } else {
            $db->where("logout_status=?", [1]);
        }
		$result=$db->query();$i=0;
        while($rs=$db->fetchRow($result))
        {
            $str[$i]=$rs[0];$i++;
        }
        return $str;

    }
	
	function setlanguageAction()
       {
               $lang=$this->getParam(1); $url="";

               if($lang!="")
               {
                       //echo $lang;
                       //setcookie("lang_id",$lang,"0","/");
                       $url=$this->url("index/index");
                       echo $url."{".$lang;die;
               }
               else
               {

                       $url=$this->url("index/index");
                       echo $url;die;
               }
       }
function forgotyourpasswordAction()
	{
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$db=new NesoteDALController();	
		
		$style_id=$settings->getValue("themes");
				
		$db->select("nesote_email_themes");
		$db->fields("name,style");
		$db->where("id=?",$style_id);
		$result=$db->query();
		$theme=$db->fetchRow($result);

		$this->setValue("style",$theme[1]);
		$footermsg=$this->getmessage(351);
		$year=date("Y",time());
		$footer=str_replace('{year}',$year,$footermsg);
		$this->setValue("footer",$footer);

		$flag=1;
		$username=$_POST['uname'];



		$userid=$this->getId($username);

		if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && ($username == "demouser1" || $username == "demouser2" || $username == "demouser3" || $username == "demouser4" || $username == "demouser5" || $username == "demouser6" || $username == "demouser7" || $username == "demouser8" || $username == "demouser9" || $username == "demouser10")) {
            $msg="ia";
            header("Location:".$this->url("index/forgotpasswordprocess/$userid/$msg"));
            exit(0);
        }

		$uid=$_POST['uid'];
		$whichspan=$_POST['whichspan'];
		$this->setValue("msg",$msg);
		if ($whichspan=='qstnanswerid') {
            $question=trim((string) $_POST['question']);
            $myqst="";
            $answer=$_POST['answer'];
            $day=$_POST['day'];
            $month=$_POST['month'];
            $year=$_POST['year'];
            $image=$_POST['image'];
            if ($question === "") {
                $flag=0;
                $err="q";
                header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                exit(0);
                //277
            } elseif ($question==1) {
                $myqst=trim((string) $_POST['myqst']);
                if ($myqst === "") {
                    $flag=0;
                    $err="q";
                    header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                    exit(0);
                    //277
                } elseif ($answer=="") {
                    $flag=0;
                    $err="ans";
                    header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                    exit(0);
                    //277
                }
            }
            if ($answer=="") {
                $flag=0;
                $err="ans";
                header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                exit(0);
                //277
            } elseif ($day=="") {
                $flag=0;
                $err="d";
                header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                exit(0);
                //128
            } elseif ($month=="") {
                $flag=0;
                $err="m";
                header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                exit(0);
                //129
            } elseif ($year=="") {
                $flag=0;
                $err="y";
                header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                exit(0);
                //130
            } elseif ($image=="") {
                $flag=0;
                $err="i";
                header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                exit(0);
                //149
            } else
			{
					

				$image=trim((string) $_POST['image']);

				$enc_image=md5($image);
				$random=$_COOKIE['random'];

				if($random!=$enc_image)
				{
					$flag=0;
					$err="img";
					header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
					exit(0);//148
				}

				else
				{
					$dob=mktime(0,0,0,$month,$day,$year);//echo $month."/".$day."/".$year."/".$dob."/".$answer;exit;

					// update password
					$oldpassword="";
					if ($question==1) {
                        $question=trim((string) $_POST['myqst']);
                    }
					
					$db->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
					$db->fields("a.password,b.remember_answer,b.remember_question");
					$db->where("a.username=? and a.status=? and a.id=? and b.dateofbirth=? and a.id=b.userid",[$username,1,$uid,$dob]);
					$result8=$db->query();//echo $db->getQuery();
					$row8=$db->fetchRow($result8);
					$num=$db->numRows($result8);

					if($num==0)
					{
						$flag=0;
						$err="iu";
						header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
						exit(0);
					}
					else
					{

						$table_qstn=trim((string) $row8[2]);
						$user_qstn=trim($question);

						$table_answer=trim((string) $row8[1]);
						$user_answer=trim((string) $answer);

						$table_qstn=strtolower($table_qstn);$user_qstn=strtolower($user_qstn);
						$user_qstn=trim($user_qstn);$table_qstn=trim($table_qstn);
						$user_qstn=str_replace(" ",'',$user_qstn);$table_qstn=str_replace(" ",'',$table_qstn);

						$table_answer=strtolower($table_answer);$user_answer=strtolower($user_answer);
						$user_answer=trim($user_answer);$table_answer=trim($table_answer);
						$user_answer=str_replace(" ",'',$user_answer);$table_answer=str_replace(" ",'',$table_answer);


						if (strcmp($table_qstn,$user_qstn)!=0) {
                            $flag=0;
                            $err="iu";
                            header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                            exit(0);
                        } elseif (strcmp($table_answer,$user_answer)!=0) {
                            $flag=0;
                            $err="iu";
                            header("Location:".$this->url("index/forgotpasswordprocess/$userid/$err"));
                            exit(0);
                        } else {
                            $oldpassword=$row8[0];
                        }
					}
					if($oldpassword!="")
					{
						$str="";
						$chr1= ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
						$numbers= ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
						$CHR2=['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q','R', 'S', 'T', 'U','V', 'W','X','Y','Z'];
						$spl=["!","@","#","$","%","^","&","*"];

						$c1=array_rand($CHR2);
						$c2=array_rand($CHR2);
						while (trim($c1) === trim($c2))
						{
							$c1=array_rand($CHR2);
							$c2=array_rand($CHR2);
						}

						$c3=array_rand($chr1);
						$c4=array_rand($chr1);
						while (trim($c3) === trim($c4))
						{
							$c3=array_rand($chr1);
							$c4=array_rand($chr1);

						}

						$s1=array_rand($spl);
						$s2=array_rand($spl);
						while (trim($s1) === trim($s2))
						{
							$s1=array_rand($spl);
							$s2=array_rand($spl);

						}

						$no1=array_rand($numbers);
						$no2=array_rand($numbers);
						while (trim($no1) === trim($no2))
						{
							$no1=array_rand($numbers);
							$no2=array_rand($numbers);

						}

						$str=$chr1[$c3].$spl[$s1].$CHR2[$c1].$numbers[$no1].$CHR2[$c2].$spl[$s2].$numbers[$no2].$chr1[$c4];


						//echo $str;exit;
						$newpassword=$str;
						$new_serverpassword=base64_encode($newpassword);
						//$newpassword=substr($oldpassword,0,9);//echo $newpassword;

						$encnewpassword=md5($newpassword);//echo $encnewpassword;exit;

					
                        $account_type=$settings->getValue("catchall_mail");
						if ($account_type==1) {
                            $db->update("nesote_email_usersettings");
                            $db->set("server_password=?",$new_serverpassword);
                            $db->where("userid=?",$uid);
                            $result=$db->query();
                            $db->update("nesote_liberyus_users");
                            $db->set("password=?",[$encnewpassword]);
                            $db->where("username=? and password=? and id=?",[$username,$oldpassword,$uid]);
                            $result=$db->query();
                            //$userid=$this->getId();
                            $username=$this->getusername($uid);
                            $this->saveLogs("Settings Updation","$username has updated his/her settings",$uid);
                            header("Location:".$this->url("index/passworddetails/$uid"));
                            exit(0);
                        } elseif ($account_type==0) {
                            //							
                            $automatic_account_creation=$settings->getValue("automatic_account_creation");
                            if($automatic_account_creation==1)// for automatic account creation
							{

								// api calling
								$username=$this->getusername($uid);
//								
								$controlpanel=$settings->getValue("controlpanel");

								if ($controlpanel==1) {
                                    $this->capnelaction(1,$username,$newpassword);
                                    // 1 for change password
                                } elseif ($controlpanel==2) {
                                    $this->pleskaction(1,$username,$newpassword);
                                    // 1 for change password
                                }

								
								$db->update("nesote_email_usersettings");
								$db->set("server_password=?",$new_serverpassword);
								$db->where("userid=?",$uid);
								$result=$db->query();
							
								$db->update("nesote_liberyus_users");
								$db->set("password=?",[$encnewpassword]);
								$db->where("username=? and password=? and id=?",[$username,$oldpassword,$uid]);
								$result=$db->query();

								//$userid=$this->getId();
								$username=$this->getusername($uid);
								$this->saveLogs("Settings Updation","$username has updated his/her settings",$uid);


								header("Location:".$this->url("index/passworddetails/$uid"));
								exit(0);

							}
                        }

					}
				}
			}
        } elseif ($whichspan=='altemailid') {
            $alternatemail=$_POST['alternatemail'];
            $day1=$_POST['day'];
            $month1=$_POST['month'];
            $year1=$_POST['year'];
            $image=$_POST['image'];
            if ($alternatemail=="") {
                $flag=0;
                $err="e";
                header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
                exit(0);
                //158
            } elseif ($this->isEmail($alternatemail)==false) {
                $flag=0;
                $err="em";
                header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
                exit(0);
                //159
            } elseif ($day1=="") {
                $flag=0;
                $err="d";
                header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
                exit(0);
                //128
            } elseif ($month1=="") {
                $flag=0;
                $err="m";
                header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
                exit(0);
                //129
            } elseif ($year1=="") {
                $flag=0;
                $err="y";
                header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
                exit(0);
                //130
            } elseif ($image=="") {
                $flag=0;
                $err="i";
                header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
                exit(0);
                //149
            } else
			{

				$image=trim((string) $_POST['image']);

				$enc_image=md5($image);
				$random=$_COOKIE['random'];

				if($random!=$enc_image)
				{
					$flag=0;
					$err="img";
					header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
					exit(0);
				}
				else
				{
					$dob1=mktime(0,0,0,$month1,$day1,$year1);
					//upadte password
					
					$db->select(["a"=>"nesote_liberyus_users","b"=>"nesote_email_usersettings"]);
					$db->fields("a.password,a.joindate");
					//if($whichmail==1)
				   $db->where("a.username=? and a.status=? and a.id=? and b.dateofbirth=? and b.alternate_email=? and a.id=b.userid",[$username,1,$uid,$dob1,$alternatemail]);
					//else
					//$db->where("a.username=? and a.status=? and a.id=? and b.dateofbirth=?  and a.id=b.userid",array($username,1,$uid,$dob1));
					$result1=$db->query();
					$no1=$db->numRows($result1);
					$row=$db->fetchRow($result1);
					if($no1==0)
					{
						$flag=0;
						$err="iu";
						header("Location:".$this->url("index/forgotpasswordprocess/$username/$err"));
						exit(0);
					}
					else
					{
						$oldpassword1=$row[0];
						$creattime=$row[1];
					}
					if($oldpassword1!="")
					{

						$str="";
						$chr1= ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
						$numbers= ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
						$CHR2=['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q','R', 'S', 'T', 'U','V', 'W','X','Y','Z'];
						$spl=["!","@","#","$","%","^","&","*"];

						$c1=array_rand($CHR2);
						$c2=array_rand($CHR2);
						while (trim($c1) === trim($c2))
						{
							$c1=array_rand($CHR2);
							$c2=array_rand($CHR2);
						}

						$c3=array_rand($chr1);
						$c4=array_rand($chr1);
						while (trim($c3) === trim($c4))
						{
							$c3=array_rand($chr1);
							$c4=array_rand($chr1);

						}

						$s1=array_rand($spl);
						$s2=array_rand($spl);
						while (trim($s1) === trim($s2))
						{
							$s1=array_rand($spl);
							$s2=array_rand($spl);

						}

						$no1=array_rand($numbers);
						$no2=array_rand($numbers);
						while (trim($no1) === trim($no2))
						{
							$no1=array_rand($numbers);
							$no2=array_rand($numbers);

						}

						$str=$chr1[$c3].$spl[$s1].$CHR2[$c1].$numbers[$no1].$CHR2[$c2].$spl[$s2].$numbers[$no2].$chr1[$c4];


						//echo $str;exit;
						$newpassword1=$str;
						$new_serverpassword1=base64_encode($newpassword1);
						$encnewpassword1=md5($newpassword1);

						

						$account_type=$settings->getValue("catchall_mail");

						if ($account_type==1) {
                            $forgotpassword_msg=$settings->getValue("forgotpassword_msg");
                            $forgotpassword_msg=htmlspecialchars((string) $forgotpassword_msg);
                            $forgotpassword_msg=html_entity_decode($forgotpassword_msg);
                            $subject=$this->getmessage(286);
                            $message=$this->getmessage(287);
                            $click=$this->getmessage(288);
                            $value2=trim($uid.$creattime);
                            $value3=md5($value2);
                            $from=$settings->getValue("adminemail");
                            $link="<a href=\"";
                            $link.=$this->url("index/updatepassword/$value3");
                            $link.="\" target=\"_blank\" >$click</a>";
                            $mail=str_replace('{Subject}',$subject,$forgotpassword_msg);
                            $mail=str_replace('{msg}',$message,$mail);
                            $mail=str_replace('{link}',$link,$mail);
                            $mail=nl2br($mail);
                            $headers = "From:". $from. "\r\n";
                            $headers .= "MIME-Version: 1.0\r\n";
                            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                            //---------------------------for high priority-----------------------
                            $headers .= "X-Priority: 1 (Highest)\n";
                            $headers .= "X-MSMail-Priority: High\n";
                            $headers .= "Importance: High\n";
                            //---------------------------------------------------------------------
                            $headers .= 'Reply-To:'.$from;
                            $p=mail((string) $alternatemail,(string) $subject,$mail,$headers);
                            if ($p==1) {
                                header("Location:".$this->url("index/passwordprocesscontinue"));
                            } else {
                                header("Location:".$this->url("index/forgotpassword"));
                            }
                            exit(0);
                        } elseif ($account_type==0) {
                            $automatic_account_creation=$settings->getValue("automatic_account_creation");
                            if($automatic_account_creation==1)// for automatic account creation
							{

								// api calling




								$forgotpassword_msg=$settings->getValue("forgotpassword_msg");
								$forgotpassword_msg=htmlspecialchars((string) $forgotpassword_msg);
								$forgotpassword_msg=html_entity_decode($forgotpassword_msg);

								$subject=$this->getmessage(286);
								$message=$this->getmessage(287);
								$click=$this->getmessage(288);
								$value2=trim($uid.$creattime);
								$value3=md5($value2);

								$from=$settings->getValue("adminemail");


								$link="<a href=\"";
								$link.=$this->url("index/updatepassword/$value3");
								$link.="\" target=\"_blank\" >$click</a>";


								$mail=str_replace('{Subject}',$subject,$forgotpassword_msg);
								$mail=str_replace('{msg}',$message,$mail);
								$mail=str_replace('{link}',$link,$mail);

								$mail=nl2br($mail);

								$headers = "From:". $from. "\r\n";
								$headers .= "MIME-Version: 1.0\r\n";

								$headers .= "Content-type: text/html; charset=UTF-8\r\n";

								//---------------------------for high priority-----------------------
								$headers .= "X-Priority: 1 (Highest)\n";
								$headers .= "X-MSMail-Priority: High\n";
								$headers .= "Importance: High\n";
								//---------------------------------------------------------------------
								$headers .= 'Reply-To:'.$from;

								$p=mail((string) $alternatemail,(string) $subject,$mail,$headers);
								if ($p==1) {
                                    header("Location:".$this->url("index/passwordprocesscontinue"));
                                } else {
                                    header("Location:".$this->url("index/forgotpassword"));
                                }
								exit(0);

							}
                        }

					}


				}//upadtion complete

			}
        }
	}

	function updatepasswordAction()
	{
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$db=new NesoteDALController();

		$style_id=$settings->getValue("themes");
			
		
		$db->select("nesote_email_themes");
		$db->fields("name,style");
		$db->where("id=?",$style_id);
		$result=$db->query();
		$theme=$db->fetchRow($result);

		$this->setValue("style",$theme[1]);
		$footermsg=$this->getmessage(351);
		$year=date("Y",time());
		$footer=str_replace('{year}',$year,$footermsg);
		$this->setValue("footer",$footer);


		$img=$settings->getValue("public_page_logo");
		$imgpath="admin/logo/".$img;
		$this->setValue("imgpath",$imgpath);


		$pwd=$this->getParam(1);//echo $pwd; exit;
		$this->setValue("pwd",$pwd);
		if($_POST !== [])
		{
			$button=$_POST['submit1'];
			$value=$_POST['pwd'];//echo $value;

			if($button)
			{
				//echo $button;echo hai;
				//md5(id+createdtime);
				$id="";$flag=0;
				
				$db->select("nesote_liberyus_users");
				$db->fields("id,joindate");
				$result=$db->query();
				$num=$db->numRows($result);
				if($num!=0)
				{
					while($row=$db->fetchRow($result))
					{
						$c=$row[0].$row[1];
						$checkvalue=md5($c);
						if(trim($checkvalue) === trim((string) $value))
						{
							$flag=1;
							$id=$row[0];break;
						}
					}
				}
				if (($_SERVER['HTTP_HOST'] == "www.libewebportal.com" || $_SERVER['HTTP_HOST'] == "libewebportal.com" || $_SERVER['HTTP_HOST'] == "www.libewebmaildemo.com" || $_SERVER['HTTP_HOST'] == "libewebmaildemo.com") && $id <= 5) {
                    $msg="ia";
                    header("Location:".$this->url("index/forgotpassword/$msg"));
                    exit(0);
                }
				if(($id!="") && ($flag==1))
				{
					
					$db->select("nesote_liberyus_users");
					$db->fields("password,username");
					$db->where("id=?",[$id]);
					$result1=$db->query();//echo $db->getQuery();
					$no1=$db->numRows($result1);
					$row=$db->fetchRow($result1);
					if ($no1!=0) {
                        $oldpassword1=$row[0];
                    }

					if($oldpassword1!="")
					{

						$str="";
						$chr1= ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
						$numbers= ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
						$CHR2=['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q','R', 'S', 'T', 'U','V', 'W','X','Y','Z'];
						$spl=["!","@","#","$","%","^","&","*"];

						$c1=array_rand($CHR2);
						$c2=array_rand($CHR2);
						while (trim($c1) === trim($c2))
						{
							$c1=array_rand($CHR2);
							$c2=array_rand($CHR2);
						}

						$c3=array_rand($chr1);
						$c4=array_rand($chr1);
						while (trim($c3) === trim($c4))
						{
							$c3=array_rand($chr1);
							$c4=array_rand($chr1);

						}

						$s1=array_rand($spl);
						$s2=array_rand($spl);
						while (trim($s1) === trim($s2))
						{
							$s1=array_rand($spl);
							$s2=array_rand($spl);

						}

						$no1=array_rand($numbers);
						$no2=array_rand($numbers);
						while (trim($no1) === trim($no2))
						{
							$no1=array_rand($numbers);
							$no2=array_rand($numbers);

						}

						$str=$chr1[$c3].$spl[$s1].$CHR2[$c1].$numbers[$no1].$CHR2[$c2].$spl[$s2].$numbers[$no2].$chr1[$c4];


						//echo $str;exit;
						$newpassword1=$str;
						$new_serverpassword1=base64_encode($newpassword1);



						$encnewpassword1=md5($newpassword1);
						$username=$this->getusername($id);

						

						$controlpanel=$settings->getValue("controlpanel");

						if ($controlpanel==1) {
                            $this->capnelaction(1,$username,$newpassword1);
                            // 1 for change password
                        } elseif ($controlpanel==2) {
                            $this->pleskaction(1,$username,$newpassword1);
                            // 1 for change password
                        }

						$db->update("nesote_email_usersettings");
						$db->set("server_password=?",[$new_serverpassword1]);
						$db->where("userid=?",[$id]);
						$result2=$db->query();
						
						$db->update("nesote_liberyus_users");
						$db->set("password=?",[$encnewpassword1]);
						$db->where("id=? and password=?",[$id,$oldpassword1]);
						$result2=$db->query();//echo $update->getQuery();
						

						$this->saveLogs("Settings Updation","$username has updated his/her password",$id);

						header("Location:".$this->url("index/passworddetails/$id"));
						exit(0);

					}

				}
				else
				{
					header("Location:".$this->url("index/passworddetails/err"));
					exit(0);
				}

			}
		}

	}

	function passworddetailsAction()
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$db=new NesoteDALController();
		$style_id=$settings->getValue("themes");
				
		$db->select("nesote_email_themes");
		$db->fields("name,style");
		$db->where("id=?",$style_id);
		$result=$db->query();
		$theme=$db->fetchRow($result);

		$this->setValue("style",$theme[1]);
		$footermsg=$this->getmessage(351);
		$year=date("Y",time());
		$footer=str_replace('{year}',$year,$footermsg);
		$this->setValue("footer",$footer);

		$img=$settings->getValue("public_page_logo");
		$imgpath="admin/logo/".$img;
		$this->setValue("imgpath",$imgpath);

		$id=$this->getParam(1);//echo $id;
		$msg="";
		if(isset($id))
		{
			if($id=="err")
			{
				$msg=$this->getmessage(285);//echo $msg;
			}
			else
			{
				
				$db->select("nesote_email_usersettings");
				$db->fields("server_password");
				$db->where("userid=?",[$id]);
				$result=$db->query();//echo $db->getQuery();
				$row=$db->fetchRow($result);

				$new_serverpassword=base64_decode((string) $row[0]);
				$this->setValue("password",$new_serverpassword);
			}
		}
		if ($msg!="") {
            $this->setValue("msg",$msg);
        }//echo $msg;

	}

	function passwordprocesscontinueAction()
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		
		$select=new NesoteDALController();

		$style_id=$settings->getValue("themes");	
		
		$select->select("nesote_email_themes");
		$select->fields("name,style");
		$select->where("id=?",$style_id);
		$result=$select->query();
		$theme=$select->fetchRow($result);

		$this->setValue("style",$theme[1]);
		$footermsg=$this->getmessage(351);
		$year=date("Y",time());
		$footer=str_replace('{year}',$year,$footermsg);
		$this->setValue("footer",$footer);

		$img=$settings->getValue("public_page_logo");
		$imgpath="admin/logo/".$img;
		$this->setValue("imgpath",$imgpath);
	}

	function capnelaction($execte,$username,$value)
	{


		include_once __DIR__ . '/class/xmlapi.php';
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

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
		if($execte==1) //for password change
		{
			$xmlapi->api1_query($account, "Email", "passwdpop", [$email_user, $value, $email_quota, $email_domain] );
		}

	}

	function pleskaction($execte,$username,$value)
	{

		include_once __DIR__ . '/class/mail_plesk.php';
           
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

		$host=$settings->getValue("domain_name");

		$login=$settings->getValue("domain_username");

		$password=$settings->getValue("domain_password");

		$plesk_packetversion=$settings->getValue("plesk_packetversion");

		$plesk_domainid=$settings->getValue("plesk_domainid");

		if($execte==1)// for change password
		{

			$change="<?xml version='1.0' encoding='UTF-8' ?>
			<packet version=$plesk_packetversion>
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
		}


		$curl = curlInit($host, $login, $password);
		try {

			// echo GET_PROTOS;
			$response = sendRequest($curl, $action);//echo $response;
			$responseXml = parseResponse($response);
			checkResponse($responseXml);
		} catch (ApiRequestException $e) {
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
function isEmail($email)
	{
		$result =true;
		if(!preg_match("#^[_a-z0-9\\-]+(\\.[_a-z0-9\\-]+)*@[a-z0-9\\-]+(\\.[a-z0-9\\-]+)*(\\.[a-z]{2,4})\$#mi", (string) $email))
		{
			return false;
		}

		return $result;
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

	function saveLogs($operation,$comment,$userid)
	{
		//$userid=$this->getId();

		$insert=new NesoteDALController();
		$insert->insert("nesote_email_client_logs");
		$insert->fields("uid,operation,comment,time");
		$insert->values([$userid,$operation,$comment,time()]);
		$insert->query();
	}  
function mobile_device_detect($iphone=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false){

        $mobile_browser   = false;
        $user_agent       = $_SERVER['HTTP_USER_AGENT']; 
        $accept           = $_SERVER['HTTP_ACCEPT'];

        switch(true){ 

            case (preg_match('#ipod#mi',(string) $user_agent)||preg_match('#iphone#mi',(string) $user_agent)||preg_match('#iPhone#mi',(string) $user_agent)); 
            $mobile_browser = $iphone; 
            if(str_starts_with((string) $iphone, 'http')){ 
                $mobileredirect = $iphone;
            }
            break;
            case (preg_match('#android#mi',(string) $user_agent));
            $mobile_browser = $android; 
            if(str_starts_with((string) $android, 'http')){ 
                $mobileredirect = $android; 
            } 
            break; 
            case (preg_match('#opera mini#mi',(string) $user_agent));
            $mobile_browser = $opera; 
            if(str_starts_with((string) $opera, 'http')){
                $mobileredirect = $opera;
            }
            break; 
            case (preg_match('#blackberry#mi',(string) $user_agent));
            $mobile_browser = $blackberry;
            if(str_starts_with((string) $blackberry, 'http')){
                $mobileredirect = $blackberry;
            }
            break; 
            case (preg_match('/(palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',(string) $user_agent));
            $mobile_browser = $palm;
            if(str_starts_with((string) $palm, 'http')){ 
                $mobileredirect = $palm;
            }
            break; 
            case (preg_match('/(windows ce; ppc;|windows ce; smartphone;|windows ce; iemobile)/i',(string) $user_agent));
            $mobile_browser = $windows; 
            if(str_starts_with((string) $windows, 'http')){
                $mobileredirect = $windows;
            }
            break;

            case preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|pda|psp|treo)/i',(string) $user_agent): 
            case (strpos((string) $accept,'text/vnd.wap.wml')>0)||(strpos((string) $accept,'application/vnd.wap.xhtml+xml')>0): 
            case isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE']):
            case (in_array(strtolower(substr((string) $user_agent,0,4)),['1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','airn'=>'airn','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','bilb'=>'bilb','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','comp'=>'comp','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','iris'=>'iris','jata'=>'jata','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-f'=>'lg-f','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge/'=>'lge/','lynx'=>'lynx','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','merc'=>'merc','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','pn-2'=>'pn-2','pt-g'=>'pt-g','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sc01'=>'sc01','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sava'=>'sava','scoo'=>'scoo','smit'=>'smit','soft'=>'soft','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','ts70'=>'ts70','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','topl'=>'topl','up.b'=>'up.b','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapv'=>'wapv','wapy'=>'wapy','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',])); // check against a list of trimmed user agents to see if we find a match
            $mobile_browser = true; 
            break;

        }
        header('Cache-Control: no-transform');
        header('Vary: User-Agent, Accept');
        return $mobile_browser; 
    }   
};
?>