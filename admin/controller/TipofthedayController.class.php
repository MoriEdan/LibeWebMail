<?php
class TipofthedayController extends NesoteController
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
	
 	function add_tip_ofthe_dayAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
		$title=$_POST['title'];
		$message=$_POST['message'];
		$time=time();
		$db=new NesoteDALController();
		$db->insert("nesote_tip_of_the_day");
		$db->fields("id,title,message,time");
		$db->values(["0",$title,$message,$time]);
		$res=$db->query();
		header("Location:".$this->url("message/success/1502/9"));
	    exit(0);
		}
	}
	function view_tip_ofthe_dayAction()
	{ 
		
		$db=new NesoteDALController();
		$tot=$db->total("nesote_tip_of_the_day");
		$perpagesize=10;
		$currentpage=1;
		if (isset($_POST['pagenumber'])&&trim((string) $_POST['pagenumber']) !== "") {
            $currentpage=$_POST['pagenumber'];
        }
		$paging= new Paging();
			//$out=$paging->page($num,$perpagesize,"page",1,1,1,"","","",$_POST);
		$out=$paging->page($tot,$perpagesize,"test",1,1,1,"","","",$_POST);
		$this->setValue("pagingtop",$out);
		$this->setValue("count",$tot);
		//echo $tot;exit;
		
		$db->select("nesote_tip_of_the_day");
		$db->fields("id,title,message");
		$db->order("time desc");
		$db->limit(($currentpage-1)*$perpagesize, $perpagesize);
		$res=$db->query();
		
		$this->setLoopValue("tips",$res->getResult());
			
			
	}
	function deletetipsAction(): never
	{
		$ids=$this->getParam(1);
		$id=substr((string) $ids,0,-1);
		$db=new NesoteDALController();
		$db->delete("nesote_tip_of_the_day");
        //$db->where("id=?",$id);
        $db->where("id IN($id)");
        $db->query();
        header("Location:".$this->url("message/success/1503/9"));exit;
	}
	function edit_tipsAction()
	{
		if(!$this->validUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
		$id=$this->getParam(1);
		$this->setValue("id",$id);
		$db=new NesoteDALController();
		$db->select("nesote_tip_of_the_day");
		$db->fields("title,message");
		$db->where("id=?",$id);
		$res=$db->query();
		$result=$db->fetchrow($res);
		$title=$result[0];
		$message=$result[1];
		$this->setValue("title",$title);
		$this->setValue("message",$message);
		}
		
	}
	function edit_tipsprocessAction()
	{
		$id=$this->getParam(1);
		$title=$_POST['title'];
		$message=$_POST['message'];
		$update=new NesoteDALController();
		$update->update("nesote_tip_of_the_day");
		$update->set("title=?,message=?",[$title,$message]);
		$update->where("id=?",$id);
		$update->query();
		header("Location:".$this->url("message/success/1504/9"));
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
		$db3->where("userid=? ",[$userid]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);
		//return $row3[0];
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

	function smtp($to,$subject,$html)
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


			foreach ($to as $address)
			{
				//print_r($address);
				if($address!='')
				{

					$address2=explode("/",(string) $address);
                    $username=$address2[0];//echo substr_replace($username,"",-17);
                    $username=substr_replace($username,"",-17);
                    $tablenumber=$this->tableid($username);
					$time=$this->getusertime($address2[1],$address2[0]);
         
					
					$db->insert("nesote_email_inbox_$tablenumber");
					$db->fields("userid,from_list,to_list,subject,body,time,status");
					$db->values([$address2[1],$admin_email,$address2[0],$subject,$html,$time,1]);
					$result=$db->query();
					$last=$db->lastInsert();

					$var=time().$id."1";
					$ext=$this->getextension();
					$message_id="<".md5($var).$ext.">";

					$mail_references="<references><item><mailid>$last</mailid><folderid>1</folderid></item></references>";

					
					$db->update("nesote_email_inbox_$tablenumber");
					$db->set("mail_references=?,message_id=?",[$mail_references,$message_id]);
					$db->where("id=?",$last);
					$res1=$db->query();

				}

			}

			header("Location:".$this->url("message/success/1074/8"));
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