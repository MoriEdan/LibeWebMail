<?php
class IndexController extends NesoteController
{
	function indexAction()
	{
	 $mobile_status=$this->mobile_device_detect();
	 $mob = $mobile_status == true ? 1 : 0;
	 $this->setValue("mob",$mob);
		
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


			$this->setValue("demouser",$demouser);
			$this->setValue("demopass","demodemo");
		}
		else
		{
			$this->setValue("demouser","");
			$this->setValue("demopass","");
		}
		
		
                $this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
                $account_type=$settings->getValue("catchall_mail");
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


		$select=new NesoteDALController();
		$select->select("nesote_email_settings");
		$select->fields("value");
		$select->where("name='themes'");
		$result=$select->query();//echo $select->getQuery();
		$res=$select->fetchRow($result);
		$style_id=$res[0];
			
		
		$select->select("nesote_email_themes");
		$select->fields("name,style");
		$select->where("id=?",$style_id);
		$result=$select->query();
		$theme=$select->fetchRow($result);

		$this->setValue("style",$theme[1]);
		
		$memorymsg=$this->getmessage(351);
		$year=date("Y",time());
		$msg1=str_replace('{year}',$year,$memorymsg);
		$this->setValue("footer",$msg1);

		$signupvalue=0;
		$msg=$this->getParam(1);
		if ($msg=="u") {
            $msg=$this->getmessage(103);
        } elseif ($msg=="p") {
            $msg=$this->getmessage(104);
        } elseif ($msg=="errlog") {
            $msg=$this->getmessage(200);
        }
		//else if($msg=="s")
		//$msg=$this->getmessage(766);
		
		$this->setValue("msg",$msg);
		$user=$this->getParam(2);
		$this->setValue("user",$user);
		

		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='engine_name'");
		$result=$db->query();
		$row=$db->fetchRow($result);
		$servicename=$row[0];
		$this->setValue("servicename",$servicename);

		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='catchall_mail'");
		$result=$db->query();
		$row=$db->fetchRow($result);
		$account_type=$row[0];



		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='default_language'");
		$result1=$db->query();
		$row1=$db->fetchRow($result1);
		$langid=$row1[0];
		if ($langid=="") {
            $langid="eng";
        }
		$this->setValue("defaultlang",$langid);

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

		
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='public_page_logo'");
		$result1=$db->query();
		$row1=$db->fetchRow($result1);
		$img=$row1[0];
		$imgpath="../admin/logo/".$img;

//$this->setValue("imgpath","images/banner.png");
		$this->setValue("imgpath",$imgpath);


		$db= new NesoteDALController();
		$db->select("nesote_email_languages");
		$db->fields("lang_code,language");
		$db->where("status=?",[1]);
		$db->order("id asc");
		$result=$db->query();
		$this->setLoopValue("lang",$result->getResult());

		if($account_type==1)// catchall
		{

			$db=new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name='public_registration'");
			$result=$db->query();
			$row=$db->fetchRow($result);
			$public_registration=$row[0];
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
			$db=new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name='automatic_account_creation'");
			$result=$db->query();
			$row=$db->fetchRow($result);
			$automatic_account_creation=$row[0];

			if($automatic_account_creation==1)// for automatic account creation
			{
				$signupvalue=0;
				$db=new NesoteDALController();
				$db->select("nesote_email_settings");
				$db->fields("value");
				$db->where("name='public_registration'");
				$result=$db->query();
				$row=$db->fetchRow($result);
				$public_registration=$row[0];
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
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("*");
		$db->where("username=? and password=? and status=?", [$username,$password,1]);
		$result=$db->query();
		$no=$db->numRows($result);
		if ($_COOKIE["e_username"] && $no != 0) {
            header("Location:".$this->url("mail/home"));
            exit(0);
        }



	}
	function logincheckAction()
	{
		$username=$_POST['username'];
		$pasword=$_POST['password'];
		if($username=="")
		{
				
				header("Location:".$this->url("index/index/u"));
				exit(0);
		}
		 if($pasword=="")
		{
				
				header("Location:".$this->url("index/index/p/$username"));
				exit(0);
		}
		
		if(strpos((string) $username,"@")!="")
		{
			$uname=explode("@",(string) $username);
			$extn=$this->getextension();
			$udomain="@".$uname[1];
			if($extn!=$udomain)
			{
				
				header("Location:".$this->url("index/index/errlog/$username"));
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
		if($no!=0)
		{
			
				$db->select("nesote_email_usersettings");
				$db->fields("time_zone,server_password,smtp_username");
				$db->where("userid=?",$rr1[0]);
				$res=$db->query();
				$result=$db->fetchRow($res);
				if($result[0]=="" || $result[1]=="" )
				{
				header("Location:".$this->url("index/index/errlog"));
				exit(0);
				}
				
				
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
			//$this->executePage("mime/getmail");
			
			
		


			$uid=$this->getId($username);
			$db=new NesoteDALController();
			$db->update("nesote_chat_users");
			$db->set("logout_status=?,lastupdatedtime=?",[0,time()]);
			$db->where("userid=?", [$uid]);
			$db->query();//echo $db->getQuery();//exit;

			header("Location:".$this->url("mail/home"));
			exit(0);
		}
		else
		{
			
			header("Location:".$this->url("index/index/errlog/$username"));
			exit(0);
		}


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
		$uid=$this->getUID();$userid=$this->getUID();
		$newtime=time()-90;
		$db=new NesoteDALController();
		$db->update("nesote_chat_users");
		$db->set("logout_status=?,lastupdatedtime=?",[1,$newtime]);
		$db->where("userid=?", [$uid]);
		$db->query();

		

		$db=new NesoteDALController();
		$db->select(["u"=>"nesote_chat_session","c"=>"nesote_chat_session_users"]);
		$db->fields("distinct u.id");
		$db->where("u.id=c.chat_id and c.user_id=?",$userid);
		$result=$db->query();//echo $db->getQuery();

		while($row=$db->fetchRow($result))
		{
			$chat_id=$row[0];


			$db1=new NesoteDALController();
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
				//$message=str_replace("\n","<br>",$message);

				$db1=new NesoteDALController();
				$db1->select("nesote_chat_session_users");
				$db1->fields("user_id");
				$db1->where("chat_id=? and active_status=? and user_id!=?",[$chat_id,1,$userid]);
				$rs1=$db1->query();
				while($row1=$db1->fetchRow($rs1))
				{

					$db=new NesoteDALController();
					$db->insert("nesote_chat_temporary_messages");
					$db->fields("chat_id,sender,responders,message,time,read_flag");
					$db->values([$chat_id,0,$row1[0],$message,time(),0]);
					$result=$db->query();

				}

			}
		}

		$db10=new NesoteDALController();
		$db10->select("nesote_chat_session_users");
		$db10->fields("id,active_status");
		$db10->where("user_id=?",$userid);
		$res0=$db10->query();
		$num=$db10->numRows($res0);


		if($num>0)
		{
			while($row10=$db10->fetchRow($res0))
			{
				$db1=new NesoteDALController();
				$db1->update("nesote_chat_session_users");
				$db1->set("active_status=?,typing_status=?",[0,0]);
				$db1->where("user_id=? and id=?",[$userid,$row10[0]]);
				$db1->query();


			}
			//echo $db1->getQuery();
		}

		$db1=new NesoteDALController();
		$db1->select("nesote_email_settings");
		$db1->fields("value");
		$db1->where("name='mails_per_page'");
		$result1=$db1->query();
		$row1=$db1->fetchRow($result1);
		$mails_per_page=$row1[0];
		if (($mails_per_page=="")|| ($mails_per_page==0)) {
            $mails_per_page=25;
        }

		$db2=new NesoteDALController();
		$db2->select("nesote_email_settings");
		$db2->fields("value");
		$db2->where("name='default_language'");
		$result2=$db2->query();
		$row2=$db2->fetchRow($result2);
		$default_language=$row2[0];
		if ($default_language=="") {
            $default_language='eng';
        }

		$db3=new NesoteDALController();
		$db3->select("nesote_email_settings");
		$db3->fields("value");
		$db3->where("name='themes'");
		$result3=$db3->query();
		$row3=$db3->fetchRow($result3);
		$themes=$row3[0];
		if ($themes==0) {
            $themes=1;
        }

		$db4=new NesoteDALController();
		$db4->select("nesote_email_settings");
		$db4->fields("value");
		$db4->where("name='display'");
		$result4=$db4->query();
		$row4=$db4->fetchRow($result4);
		$display=$row4[0];
		if ($display==0) {
            $display=1;
        }

		$username="demouser";$password=md5("demodemo");

		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("*");
		$db->where("username=? and password=?",[$username,$password]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		$userid=$rs[0];

		if( ($_SERVER['HTTP_HOST']=="www.libewebportal.com") || ($_SERVER['HTTP_HOST']=="libewebportal.com")||($_SERVER['HTTP_HOST']=="www.libewebmaildemo.com") || ($_SERVER['HTTP_HOST']=="libewebmaildemo.com") )
		{
			$db=new NesoteDALController();
			$db->update("nesote_email_usersettings");
			$db->set("lang_id=?,theme_id=?,display=?,mails_per_page=?,signatureflag=?,signature=?",[$default_language,$themes,$display,$mails_per_page,0,""]);
			$db->where("userid=?",[$userid]);
			$result=$db->query();

			setcookie("lang_mail",(string) $default_language, ['expires' => "0", 'path' => "/"]);
		}

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

        $db=new NesoteDALController();
        $db->select("nesote_chat_users");
        $db->fields("distinct userid");
        $db->where("logout_status=? and userid IN($st)", [1]);
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

	function capnelaction($execte,$username,$value)
	{


		include_once __DIR__ . '/../class/xmlapi.php';

		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["domain_ip"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$ip=$row[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["domain_password"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$root_pass=$row[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["domain_name"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$email_domain=$row[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["domain_username"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$domain_username=$row[0];


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

		include_once __DIR__ . '/../class/mail_plesk.php';

		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["domain_name"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$host=$row[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["domain_username"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$login=$row[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["domain_password"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$password=$row[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["plesk_packetversion"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$plesk_packetversion=$row[0];


		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?", ["plesk_domainid"]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$plesk_domainid=$row[0];

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
    function registrationAction()
    {
    	$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

 $account_type=$settings->getValue("catchall_mail");
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


		$db=new NesoteDALController();
		$portal_status=$settings->getValue("portal_status");
		if($portal_status==1)
		{
			$portal_installation_url=$settings->getValue("portal_installation_url");
			$servicekey=strrev((string) $portal_installation_url);
			$servicekey=substr($servicekey,0,strpos($servicekey,"/"));
			$servicekey=strrev($servicekey);
			$portal_installation_url=substr((string) $portal_installation_url,0,strrpos((string) $portal_installation_url,"/"));
				
			$path=$portal_installation_url."/index.php?page=member/myaccount";
			header("Location:".$path);exit;
		}
		else
		{

			
				 $mobile_status=$this->mobile_device_detect();
				 $mob = $mobile_status == true ? 1 : 0;
				 $this->setValue("mob",$mob);
			
			$min_passwordlength=$settings->getValue("min_passwordlength");
			$this->setValue("min_passwordlength",$min_passwordlength);

			$chatsettings=new Settings('nesote_chat_settings');
			$chatsettings->loadValues();

			$db=new NesoteDALController();

			
			$footermsg=$this->getmessage(351);
			$year=date("Y",time());
			$footer=str_replace('{year}',$year,$footermsg);
			$this->setValue("footer",$footer);
			$m=$this->getmessage(137);$f=$this->getmessage(138);
			$this->setValue("m",$m);
			$this->setValue("f",$f);


			$this->setValue("f1",$this->getmessage(141));
			$this->setValue("f2",$this->getmessage(142));
			$this->setValue("f3",$this->getmessage(143));
			$this->setValue("f4",$this->getmessage(144));
			$this->setValue("f5",$this->getmessage(145));
			/*******/

			$d=[];

			for($i=1,$j=0;$i<=31;$i++,$j++)
			{
				if ($i<10) {
                    $i="0".$i;
                }
				$d[$j][0]=$i;
			}

			$this->setLoopValue("DD",$d);


			$y=[];

			for($i=$year,$j=0;$i>=1900;$i--,$j++)
			{
				$y[$j][0]=$i;
			}

			$this->setLoopValue("YY",$y);
			/*******/

			$msg="";


			$userpanel=$settings->getValue("controlpanel");
			$this->setValue("controlpanel",$userpanel);

			$min_usernamelength=$settings->getValue("min_usernamelength");
			$this->setValue("min_usernamelength",$min_usernamelength);


			$this->setValue("msg",$msg);
			$msg1="";
			$this->setValue("msg1",$msg1);
			$msg2=$this->getParam(1);
			$this->setValue("msg2",$msg2);
			//$firstname="";$lastname="";
			$name="";
			$gender="";$day="";$month="";$year="";$country="";$timezone="";$loginname="";$question1="";$qs="";$answer="";$alternateemail="";
			$flag=1;

			//$this->setValue("firstname",$firstname);
			//$this->setValue("lastname",$lastname);
			$this->setValue("name",$name);
			$this->setValue("gr",$gender);
			$this->setValue("day",$day);
			$this->setValue("tt",$month);
			$this->setValue("yr",$year);
			$this->setValue("cntr",$country);
			$this->setValue("tzone",$timezone);
			$this->setValue("loginname",$loginname);
			$this->setValue("qst",$question1);
			$this->setValue("qs",$qs);
			$this->setValue("answer",$answer);
			$this->setValue("alternateemail",$alternateemail);

				
			$emailextension=$settings->getValue("emailextension");
			$emailextension = stristr(trim((string) $emailextension),"@") != "" ? $emailextension : "@".$emailextension;
			$this->setValue("emailextension",$emailextension);


			$db->select("nesote_email_country");
			$db->fields("name");
			$db->order("name asc");
			$result=$db->query();
			$this->setLoopValue("country",$result->getResult());

			$db->select("nesote_email_time_zone");
			$db->fields("id,name,value");
			$result=$db->query();
			$this->setLoopValue("timezone",$result->getResult());


			$lang_id=$_COOKIE['lang_mail'];
			if (isset($lang_id)) {
                $lang=$lang_id;
            } else
			{

				$default_lang_id=$settings->getValue("default_language");
				$lang = $default_lang_id != "" ? $default_lang_id : 'eng';
			}
			$lang_idnew=$this->getlang_id($lang);
			$db->select("nesote_email_months_messages");
			$db->fields("month_id,message");
			$db->where("lang_id=?",[$lang_idnew]);
			$result1=$db->query();
			$this->setLoopValue("kkk",$result1->getResult());

			$img=$settings->getValue("public_page_logo");
			$imgpath="admin/logo/".$img;
			$this->setValue("imgpath",$imgpath);

			$msgs=$this->getmessage(109);
			$minlengtherr=str_replace('{min_usernamelength}',$min_usernamelength,$msgs);
			$this->setValue("minlengtherr",$minlengtherr);
			$controlpanel=$settings->getValue("controlpanel");

			if($_POST !== [])
			{
				$tot=0;$flag=1;$server_password="";
				//$firstname=$_POST['firstname'];
				$name=$_POST['name'];
				if($name=="")
				{
					$flag=0;
					$msg2=$this->getmessage(753);
					$this->setValue("msg2",$msg2);
					//header("Location:".$this->url("user/registration/$msg2"));
					//exit(0);

				}
				
				if($flag==1)
				{
					$gender=$_POST['gender'];
					if($gender=="")
					{
						$flag=0;
						$msg2=$this->getmessage(127);
						$this->setValue("msg2",$msg2);
						//header("Location:".$this->url("user/registration/$msg2"));
						//exit(0);

					}
				}
				if($flag==1)
				{
					$day=$_POST['day'];
					if($day=="")
					{
						$flag=0;
						$msg2=$this->getmessage(128);
						$this->setValue("msg2",$msg2);
						//header("Location:".$this->url("user/registration/$msg2"));
						//exit(0);

					}
				}
				if($flag==1)
				{
					$month=$_POST['month'];
					if($month=="")
					{
						$flag=0;
						$msg2=$this->getmessage(129);
						$this->setValue("msg2",$msg2);
						//header("Location:".$this->url("user/registration/$msg2"));
						//exit(0);

					}
				}
				if($flag==1)
				{
					$year=$_POST['year'];
					if($year=="")
					{
						$flag=0;
						$msg2=$this->getmessage(130);
						$this->setValue("msg2",$msg2);
						//header("Location:".$this->url("user/registration/$msg2"));
						//exit(0);

					}
				}
				if($flag==1)
				{
					$dob=mktime(0,0,0,$month,$day,$year);
					$country=$_POST['country'];
					if($country=="")
					{
						$flag=0;
						$msg2=$this->getmessage(131);
						$this->setValue("msg2",$msg2);
						//header("Location:".$this->url("user/registration/$msg2"));
						//exit(0);

					}
				}
				if($flag==1)
				{
					$timezone=$_POST['time_zone'];
                     //$timezone=$settings->getValue("admin_timezone");
					if($timezone=="")
					{
						$flag=0;
						$msg2=$this->getmessage(231);
						$this->setValue("msg2",$msg2);
						//header("Location:".$this->url("user/registration/$msg2"));
						//exit(0);

					}
				}
				if($flag==1)
				{
					$loginname=strtolower((string) $_POST['loginname']);
					if($loginname === "")
					{
						$flag=0;
						$msg2=$this->getmessage(114);
						$this->setValue("msg2",$msg2);
						//header("Location:".$this->url("user/registration/$msg2"));
						//exit(0);

					}
				}
				if ($flag == 1 && preg_match ('/[^a-z0-9,_]/i', $loginname)) {
                    $msg2=$this->getmessage(327);
                    $this->setValue("msg2",$msg2);
                }
					
				if($flag==1)
				{
					$p=substr($loginname,0,1);
					if((is_numeric($loginname))||(is_numeric($p)))
					{
						$msg2=$this->getmessage(327);
						$this->setValue("msg2",$msg2);
					}
				}
				if($flag==1)
				{
					$loginname=strtolower($loginname);

					$count=strlen($loginname);
					if ($count<$min_usernamelength) {
                        $flag=0;
                        $msg=$this->getmessage(109);
                        $msg=str_replace('{min_usernamelength}',$min_usernamelength,$msg);
                        $this->setValue("msg2",$msg);
                    } elseif ($count>32) {
                        $flag=0;
                        $msg=$this->getmessage(108);
                        $this->setValue("msg2",$msg);
                    } else
					{
						
						$account_type=$settings->getValue("catchall_mail");
                        //0
                        $automatic_account_creation=$settings->getValue("automatic_account_creation");
                        //1
                        $public_registration=$settings->getValue("public_registration");
                        //1
                        if ($controlpanel==1 && $account_type==0 && $automatic_account_creation==1 && $public_registration==1) {
                            $exist_mail=$this->mailaccexist($loginname);
                            if($exist_mail!=0)
								{
									
									$msg1=$this->getmessage(229);
									$this->setValue("msg2",$msg1);
									
									$flag=0;
								}
                        } elseif ($controlpanel==2 && $account_type==0 && $automatic_account_creation==1 && $public_registration==1) {
                            $exist_mail=$this->pleskmailaccexist($loginname);
                            if($exist_mail=="ok")
								{
									
									$msg1=$this->getmessage(229);
									$this->setValue("msg2",$msg1);
									
									$flag=0;
								}
                        }
                        if($flag==1)
						{
							$msg=$this->lognameavailable($loginname);
							if($msg!="")
							{

								$this->setValue("msg2",$msg);
								$flag=0;
							}
							$username=$loginname;
						}
				}
		}
		if($flag==1)
		{

			$tot=$db->total("nesote_liberyus_users","username=?",[$loginname]);
			if($tot!=0)
			{
				$msg1=$this->getmessage(229);
				$this->setValue("msg2",$msg1);
				//$this->setValue("msg1",$msg1);
				$flag=0;
			}
		}
		if($flag==1)
		{
			$password=$_POST['password'];
			if($password=="")
			{
				$msg1=$this->getmessage(289);
				$this->setValue("msg2",$msg1);
				//$this->setValue("msg1",$msg1);
				$flag=0;
			}
		}
		if ($flag == 1 && $password == $loginname) {
            $msg1=$this->getmessage(354);
            $this->setValue("msg2",$msg1);
            //$this->setValue("msg1",$msg1);
            $flag=0;
        }
		if($flag==1)
		{
//			$pwdcount=$_POST['pwdcnt'];//echo $pwdcount;
            $pwdcount=strlen((string) $password);
			if($pwdcount<4)
			{
				$msg1=$this->getmessage(290);
				$this->setValue("msg2",$msg1);
				//$this->setValue("msg1",$msg1);
				$flag=0;
			}

		}
		if($flag==1)
		{
			$cpassword=$_POST['cpassword'];

			
			

				if($cpassword=="")
				{
					$msg1=$this->getmessage(118);
					$this->setValue("msg2",$msg1);
					$flag=0;

				}
				
				
		}
		if($flag==1)
		{
			

			$count2=strlen((string) $cpassword);
			

				if($password!=$cpassword)
				{
					$msg1=$this->getmessage(132);
					$this->setValue("msg2",$msg1);
					$flag=0;

				}
				else
				{
					$encpassword=md5((string) $cpassword);
					$server_password=base64_encode((string) $cpassword);
				}
			}
			if($flag==1)
			{
			 $question=$_POST['question'];
				if($question=="")
				{
					$flag=0;
					$msg2=$this->getmessage(125);
					$this->setValue("msg2",$msg2);
//					header("Location:".$this->url("user/registration/$msg2"));
//					exit(0);

				}
			}
//			
			if($flag==1)
			{
	 	    $answer=$_POST['answer'];
				if($answer=="")
				{ 
					$flag=0;
					$msg2=$this->getmessage(126);
					$this->setValue("msg2",$msg2);
					//header("Location:".$this->url("user/registration/$msg2"));
					//exit(0);

				}
			}
			if($answer=="")
			{
				$question="";
			}
			if($flag==1)
			{
				$alternateemail=$_POST['alternatemail'];
				if ($alternateemail != "" && $this->isValid($alternateemail) == 'FALSE') {
                    $flag=0;
                    $msg2=$this->getmessage(159);
                    $this->setValue("msg2",$msg2);
                }
			}
			$time=time();


			if($flag==1)
			{
				$image=trim((string) $_POST['image']);

				$enc_image=md5($image);
				$random=$_COOKIE['random'];

				if($random!=$enc_image)
				{
					$flag=0;
					$msgx=$this->getmessage(148);
					$this->setValue("msg2",$msgx);
				}
			}

			$extension=$this->getextension();
			$extension1=substr((string) $extension,0,1);
			if ($extension1 === "@") {
                $extension=substr((string) $extension,1,strlen((string) $extension));
            }

			$smtp_username="";
				
			$controlpanel=$settings->getValue("controlpanel");

			if ($controlpanel==1) {
                $smtp_username=$username."+".$extension;
            } elseif ($controlpanel==2) {
                $smtp_username=$username."@".$extension;
            }

			if($flag==1)
			{
				////email settings///
				 
				$mails_per_page=$settings->getValue("mails_per_page");
				if (($mails_per_page=="")|| ($mails_per_page==0)) {
                    $mails_per_page=20;
                }


				$default_language=$settings->getValue("default_language");
				if ($default_language==""  || $default_language===0) {
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


				////chat settings///

				$chathistory=$chatsettings->getValue("chat_history");

					
				$sounds=$chatsettings->getValue("default_chat_sound");

				//
				$smileys=$chatsettings->getValue("chat_smiley");

					
				//
				$deafault_chatwindow_size=$chatsettings->getValue("deafault_chatwindow_size");

					
					
				$account_type=$settings->getValue("catchall_mail");
				if($account_type==1)// for catch all
				{
					$public_registration=$settings->getValue("public_registration");
					if($public_registration==1)//public registration
					{
						

						$db->insert("nesote_liberyus_users");
						$db->fields("username,password,name,joindate,status");
						$db->values([$username,$encpassword,$name,$time,1]);
						$result=$db->query();
						$last=$db->lastInsert();


						$shortcuts=$settings->getValue("shortcuts");
						if ($shortcuts=="") {
                            $shortcuts=0;
                        }
							

						 
						$db->select("nesote_email_calendar_settings");
						$db->fields("value");
						$db->where("name=?",\EMAIL_REMAINDER);
						$result=$db->query();
						$rs=$db->fetchRow($result);
						$email_remainder=$rs[0];
						 
						$db->select("nesote_email_calendar_settings");
						$db->fields("value");
						$db->where("name=?",\VIEW_EVENT);
						$result1=$db->query();
						$rs1=$db->fetchRow($result1);
						$view_event=$rs1[0];
							

						$db->insert("nesote_email_usersettings");
						$db->fields("userid,lang_id,theme_id,display,mails_per_page,email_remainder,view_event,shortcuts,sex,dateofbirth,country,remember_answer,remember_question,server_password,time_zone,smtp_username,alternate_email");
						$db->values([$last,$default_language,$themes,$display,$mails_per_page,$email_remainder,$view_event,$shortcuts,$gender,$dob,$country,$answer,$question,$server_password,$timezone,$smtp_username,$alternateemail]);
						$result=$db->query();


						////Chat Start////////////
							
						$db->insert("nesote_chat_users");
						$db->fields("userid,chathistory,sounds,smileys,chat_status,chatwindowsize");
						$db->values([$last,$chathistory,$sounds,$smileys,1,$deafault_chatwindow_size]);
						$result=$db->query();

						$attachments_path="userdata";

						if(!is_dir("$attachments_path/$last"))

						{

							mkdir("$attachments_path/$last",0777);

						}

						/////////Chat End///////////



						//						SETCOOKIE("e_username",$username,0,"/");
						//						SETCOOKIE("e_password",$encpassword,0,"/");
						$this->welcomemessage($last,$username);//echo $flag;exit;
						$this->saveLogs("New Client","$username has registred as new client",$last);

					}
				}
				else //for individual
				{
						

					$automatic_account_creation=$settings->getValue("automatic_account_creation");

					if($automatic_account_creation==1)// for automatic account creation
					{

						$public_registration=$settings->getValue("public_registration");
						if($public_registration==1)//public registration
						{

							//---api calling------

							$controlpanel=$settings->getValue("controlpanel");
							if ($controlpanel==1) {
                                $this->cpanelaccountcreate($username,$password);
                            } elseif ($controlpanel==2) {
                                $this->pleskaccountcreate($username,$password);
                            }

							$db->insert("nesote_liberyus_users");
							$db->fields("username,password,name,joindate,status");
							$db->values([$username,$encpassword,$name,$time,1]);
							$result=$db->query();
							$last=$db->lastInsert();
						
								
							$shortcuts=$settings->getValue("shortcuts");
							if ($shortcuts=="") {
                                $shortcuts=0;
                            }
								

							 
							$db->select("nesote_email_calendar_settings");
							$db->fields("value");
							$db->where("name=?",\EMAIL_REMAINDER);
							$result=$db->query();
							$rs=$db->fetchRow($result);
							$email_remainder=$rs[0];
							 
							$db->select("nesote_email_calendar_settings");
							$db->fields("value");
							$db->where("name=?",\VIEW_EVENT);
							$result1=$db->query();
							$rs1=$db->fetchRow($result1);
							$view_event=$rs1[0];
								
								
								
							$db->insert("nesote_email_usersettings");
							$db->fields("userid,lang_id,theme_id,display,mails_per_page,email_remainder,view_event,shortcuts,sex,dateofbirth,country,remember_answer,remember_question,server_password,time_zone,smtp_username,alternate_email");
							$db->values([$last,$default_language,$themes,$display,$mails_per_page,$email_remainder,$view_event,$shortcuts,$gender,$dob,$country,$answer,$question,$server_password,$timezone,$smtp_username,$alternateemail]);
							$result=$db->query();
							////Chat Start////////////
								
								
							$db->insert("nesote_chat_users");
							$db->fields("userid,chathistory,sounds,smileys,chat_status,chatwindowsize");
							$db->values([$last,$chathistory,$sounds,$smileys,1,$deafault_chatwindow_size]);
							$result=$db->query();
								
							$attachments_path="userdata";

							if(!is_dir("$attachments_path/$last"))

							{

								mkdir("$attachments_path/$last",0777);

							}

							/////////Chat End///////////

							//							SETCOOKIE("e_username",$username,0,"/");
							//							SETCOOKIE("e_password",$encpassword,0,"/");
							$this->welcomemessage($last,$username);
							$this->saveLogs("New Client","$username has registred as new client",$last);

						}
					}

				}

setcookie("e_username","$username", ['expires' => "0", 'path' => "/"]);
setcookie("e_password","$encpassword", ['expires' => "0", 'path' => "/"]);

header("Location:".$this->url("mail/home"));
exit(0);
				
				//header("Location:".$this->url("index/index/s"));
				//exit(0);
			}

			}
			//$this->setValue("firstname",$firstname);
			//$this->setValue("lastname",$lastname);
			$this->setValue("name",$name);
			$this->setValue("gr",$gender);
			$this->setValue("day",$day);
			$this->setValue("tt",$month);
			$this->setValue("yr",$year);
			$this->setValue("cntr",$country);
			$this->setValue("tzone",$timezone);
			$this->setValue("loginname",$loginname);
			$this->setValue("qst",$question);
			
			$this->setValue("answer",$answer);

			$this->setValue("alternateemail",$alternateemail);
		}
    	
    }
    
    
    
function pleskmailaccexist($username)// username normal
{
		
		include_once __DIR__ . '/../class/mail_plesk.php';
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$host=$settings->getValue("domain_name");
		$login=$settings->getValue("domain_username");
		$password=$settings->getValue("domain_password");
		$plesk_packetversion=$settings->getValue("plesk_packetversion");
		$plesk_domainid=$settings->getValue("plesk_domainid");
		$portal_status=$settings->getValue("portal_status");
		
		$create = '<packet version="1.6.0.0"><mail><get_info><filter><domain_id>'.$plesk_domainid.'</domain_id><name>'.$username.'</name></filter></get_info></mail></packet>';

		$curl = curlInit($host, $login, $password);
		try {

			// echo GET_PROTOS;
			$response = sendRequest($curl, $create);
			preg_match('/<status>(.+?)<\/status>/i',$response,$folderArray);
			return $folderArray[1];
		} catch (ApiRequestException) {

			$msg2=$this->getmessage(440);
			$this->setValue("msg2",$msg2);
		
				header("Location:".$this->url("index/registration/$msg2"));
				exit(0);
		}
				foreach ($responseXml->xpath('/packet/domain/get/result') as $resultNode) {
			echo "Domain id: " . $resultNode->id . " ";
			echo $resultNode->data->gen_info->name . " (" .
			$resultNode->data->gen_info->dns_ip_address . ")\n";
		}
}

function mailaccexist($username)// username->uu@domain.com format
		{

			$extension=$this->getextension();
			$username .= $extension;
			include_once __DIR__ . '/../class/xmlapi.php';

			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();

			$ip=$settings->getValue("domain_ip");

			$root_pass=$settings->getValue("domain_password");

			$email_domain=$settings->getValue("domain_name");

			$domain_username=$settings->getValue("domain_username");

			$account = "cptest";

			$email_query = '10';
			$xmlapi = new xmlapi($ip);
			/* IF the port no is 2083 then uncomment the below sentence*/
			//$xmlapi->set_port(2083);
			$xmlapi->password_auth($domain_username,$root_pass);
			$xmlapi->set_output('json');

			$xmlapi->set_debug(1);
			$arr = $xmlapi->api2_query($account,"Email", "listpopswithdisk", [\DOMAIN=>$email_domain] );

			$json_o=json_decode((string) $arr);
			$e_arr=[];$i=0;
			foreach($json_o->cpanelresult->data as $p)
			{
				if ($username==$p->login) {
                    $i++;
                }
			}
			return $i;
		}
		
		function cpanelaccountcreate($username,$password)
	{


		include_once __DIR__ . '/../class/xmlapi.php';

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

		$ip=$settings->getValue("domain_ip");

		$root_pass=$settings->getValue("domain_password");

		$email_domain=$settings->getValue("domain_name");

		$domain_username=$settings->getValue("domain_username");
		$portal_status=$settings->getValue("portal_status");

		$account = "cptest";
		$email_user = $username;
		$email_password = $password;
		$email_query = '10';
		$xmlapi = new xmlapi($ip);
		/* IF the port no is 2083 then uncomment the below sentence*/
		//$xmlapi->set_port(2083);
		$xmlapi->password_auth($domain_username,$root_pass);
		$xmlapi->set_output('xml');

		$xmlapi->set_debug(1);
		//print $arr = $xmlapi->api2_query($account, "Email", "addpop", array(domain=>$email_domain, email=>$email_user, password=>$email_password, quota=>0) );

		try {
		 $arr = $xmlapi->api2_query($account, "Email", "addpop", [\DOMAIN=>$email_domain, \EMAIL=>$email_user, \PASSWORD=>$email_password, \QUOTA=>0] );

		}

		catch (Exception) {
				
			$msg2=$this->getmessage(440);
			
				header("Location:".$this->url("index/registration/$msg2"));
				exit(0);
			
		}
	}
		
function pleskaccountcreate($username,$user_password)
	{
		include_once __DIR__ . '/../class/mail_plesk.php';

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();

		$host=$settings->getValue("domain_name");

		$login=$settings->getValue("domain_username");

		$password=$settings->getValue("domain_password");

		$plesk_packetversion=$settings->getValue("plesk_packetversion");

		$plesk_domainid=$settings->getValue("plesk_domainid");
		$portal_status=$settings->getValue("portal_status");


		$create="<?xml version='1.0' encoding='UTF-8' ?>
		<packet version='$plesk_packetversion'>
		<mail>
		<create>
		<filter>
		<domain_id>$plesk_domainid</domain_id>
		<mailname>
		<name>$username</name>
		<mailbox>
		<enabled>true</enabled>
		</mailbox>
		<password>$user_password</password>
		<password_type>plain</password_type>

		</mailname>

		</filter>
		</create>
		</mail>
		</packet>
";

		$curl = curlInit($host, $login, $password);
		try {

			// echo GET_PROTOS;
			$response = sendRequest($curl, $create);
			$responseXml = parseResponse($response);
			checkResponse($responseXml);
		} catch (ApiRequestException) {

			$msg2=$this->getmessage(440);
			$this->setValue("msg2",$msg2);
			
				header("Location:".$this->url("index/registration/$msg2"));
				exit(0);
		}
		// Explore the result
		foreach ($responseXml->xpath('/packet/domain/get/result') as $resultNode) {
			echo "Domain id: " . $resultNode->id . " ";
			echo $resultNode->data->gen_info->name . " (" .
			$resultNode->data->gen_info->dns_ip_address . ")\n";
		}



	}
	
function lognameavailable($name)
	{
		$tot=0;$mailid="";


		$db=new NesoteDALController();
		$db->select("nesote_email_reservedemail");
		$db->fields("name");
		$result=$db->query();
		while($row=$db->fetchRow($result))
		{
			if(stristr(trim((string) $row[0]),"@")!="")
			{
				$username=explode("@",(string) $row[0]);
				$uname=$username[0];
			}
			else {
                $uname=$row[0];
            }
			if ($name==$uname) {
                $tot+=1;
            }

		}

		if($tot==0)
		{
				
			$db->select("nesote_liberyus_users");
			$db->fields("*");
			$db->where("username=?",[$name]);
			$result=$db->query();//echo $db1->getQuery();exit;
			$num1=$db->numRows($result);
			$tot=$num1;
		}
		if($tot==0)
		{
			//			$msg=$this->getmessage(110);
			//			$mailid=str_replace('{mailid}',$name,$msg);
			$mailid="";
		}
		else
		{
			$msg=$this->getmessage(111);
			$mailid=str_replace('{mailid}',$name,$msg);
		}

		return $mailid;


	}
	
   function isValid($email)
	{
		$result = 'TRUE';
		if(!preg_match("#^[_a-z0-9\\-]+(\\.[_a-z0-9\\-]+)*@[a-z0-9\\-]+(\\.[a-z0-9\\-]+)*(\\.[a-z]{2,4})\$#mi", (string) $email))
		{
			$result = 'FALSE';
		}
		return $result;
	}
	
	
	function welcomemessage($id,$username)
	{

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();


		$welcome_email=$settings->getValue("welcome_email_body");


		$welcome_subject=$settings->getValue("welcome_email_subject");


		$adminemail=$settings->getValue("adminemail");

		$mailid=$username.$this->getextension();

		$time=$this->getusertime($id,$username);
		$tablenumber=$this->tableid($username);

		$db=new NesoteDALController();
		$db->insert("nesote_email_inbox_$tablenumber");
		$db->fields("userid,from_list,to_list,subject,body,time,status");
		$db->values([$id,$adminemail,$mailid,$welcome_subject,$welcome_email,$time,1]);
		$db->query();
		$last=$db->lastInsert();

		$var=time().$id."1";
		$ext=$this->getextension();
		$message_id="<".md5($var).$ext.">";

		$mail_references="<references><item><mailid>$last</mailid><folderid>1</folderid></item></references>";
		$md5_references=md5($mail_references);

		$db->update("nesote_email_inbox_$tablenumber");
		$db->set("mail_references=?,md5_references=?,message_id=?",[$mail_references,$md5_references,$message_id]);
		$db->where("id=?",$last);
        $db->query();
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
	
function getusertime($id,$username)
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
			


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
		$db3->where("id=?",[$id]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);
		//return $row3[0];
		$db3->select("nesote_email_time_zone");
		$db3->fields("value");
		$db3->where("id=?",[$row3[0]]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);
		$timezone=$row3[0];
		
		$sign=trim((string) $timezone[0]);
		$timezone1=substr((string) $timezone,1);

		$timezone1=explode(":",$timezone1);
		$newtimezone=($timezone1[0]*60*60)+($timezone1[1]*60);
        if ($sign === "-") {
            $newtimezone=-$newtimezone;
        }
		return $ts+$newtimezone;
	}
	
    
};
?>