<?php
class LayoutController extends NesoteController
{
	
	
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



	function addfolderAction()
	{
		if(!$this->validateUser())
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
        $db=new NesoteDALController();
		$userid=$this->getId();$flag=1;
		$foldername=$this->getParam(1);
		
		$f1=strtolower((string) $foldername);
		
		
		if($foldername=="")
		{
			
			$id=$_COOKIE['folderid'];
			$msg1=$this->getmessage(473);
			$s="";
			echo "0/".$msg1;die;
		}
		if ($f1 === strtolower((string) $this->getmessage(19))) {
            $msg1=$this->getmessage(474);
            $s="";
            echo "0/".$msg1;
            die;
        } elseif ($f1 === strtolower((string) $this->getmessage(205))) {
            $msg1=$this->getmessage(474);
            $s="";
            echo "0/".$msg1;
            die;
        } elseif ($f1 === strtolower((string) $this->getmessage(429))) {
            $msg1=$this->getmessage(474);
            $s="";
            echo "0/".$msg1;
            die;
        } elseif ($f1 === strtolower((string) $this->getmessage(20))) {
            $msg1=$this->getmessage(474);
            $s="";
            echo "0/".$msg1;
            die;
        } elseif ($f1 === strtolower((string) $this->getmessage(21))) {
            $msg1=$this->getmessage(474);
            $s="";
            echo "0/".$msg1;
            die;
        } elseif ($f1 === strtolower((string) $this->getmessage(12))) {
            $msg1=$this->getmessage(474);
            $s="";
            echo "0/".$msg1;
            die;
        } elseif ($f1 === strtolower((string) $this->getmessage(22))) {
            $msg1=$this->getmessage(474);
            $s="";
            echo "0/".$msg1;
            die;
        }

		
		$db->select("nesote_email_customfolder");
		$db->fields("name");
		$db->where("userid=?",[$userid]);
		$rs=$db->query();
		while($rw=$db->fetchRow($rs))
		{
			if(trim((string) $foldername) === trim((string) $rw[0]))
			{
				$flag=0;break;
			}
		}
		if($flag==0)
		{
			$id=$_COOKIE['folderid'];
			$msg1=$this->getmessage(317);
			$s="";
			echo "0/".$msg1;die;
		}
        $db->insert("nesote_email_customfolder");
        $db->fields("name,userid");
        $db->values([$foldername,$userid]);
        $db->query();
        $lastinsertid=$db->lastInsert();
        //echo $lastinsertid;exit;
        mkdir("attachments/$lastinsertid",0777);
        $userid1=$this->getId();
        $username1=$this->getusername($userid1);
        $this->saveLogs("New mail folder","$username1 has added new mail folder");
        $msg=$this->getmessage(221);
        $msg1=str_replace('{foldername}',$foldername,$msg);
        //echo $insertid;die;
        $s="";
        echo $lastinsertid."/".$s."/".$msg1;
        die;

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
function getId()
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
	
function saveLogs($operation,$comment)
	{
		$userid=$this->getId();

		$insert=new NesoteDALController();
		$insert->insert("nesote_email_client_logs");
		$insert->fields("uid,operation,comment,time");
		$insert->values([$userid,$operation,$comment,time()]);
		$insert->query();
	}
	function editgroupAction()
	{
		$flag=1;$msg="";$msg1="";$userid=$this->getId();
		$id=$this->getParam(1);
		$name=$this->getParam(2);
		if($name=="")
		{
			$cgid="errmsg";
			$msg1=$this->getmessage(325);
			echo $cgid."/".$cgid."/".$msg1;die;
		}


		$cmpstr=strcmp(strtolower((string) $name),strtolower((string) $this->getmessage(209)));
		if($cmpstr==0)
		{


			$msg=$this->getmessage(230);
			$msg1=str_replace('{groupname}',$groupname,$msg);

			$magic=get_magic_quotes_gpc();
			if($magic==1)
			{
				$msg1=stripslashes($msg1);
			}

			$cgid="errmsg";
			echo $cgid."/".$cgid."/".$msg1;die;

		}


		$db=new NesoteDALController();
		$db->select("nesote_email_contactgroup");
		$db->fields("name");
		$db->where("userid=? and id!=?",[$userid,$id]);
		$rs1=$db->query();
		while($row1=$db->fetchRow($rs1))
		{
			if(trim((string) $row1[0]) === trim(stripslashes((string) $name)))
			{
				$flag=0;break;
			}
		}

		if($flag==0)
		{

			$msg=$this->getmessage(230);
			$msg1=str_replace('{groupname}',$name,$msg);

			
			$db->select("nesote_email_contacts");
			$db->fields("id");
			$db->where("contactgroup=? and addedby=?",[$id,$userid]);
			$db->order("mailid asc");
			$db->limit(0,1);
			$result30=$db->query();
			$num30=$db->numRows($result30);
			if($num30!=0)
			{
				$row30=$db->fetchRow($result30);
				$mailid30=$row30[0];
			}
			else
			{
				
				$db->select("nesote_liberyus_users");
				$db->fields("id");
				$db->where("status=?",[1]);
				$db->order("name asc");
				$db->limit(0,1);
				$result20=$db->query();
				$row20=$db->fetchRow($result20);
				$mailid30=$row20[0];
			}

			$magic=get_magic_quotes_gpc();
			if($magic==1)
			{
				$msg1=stripslashes($msg1);
			}

			echo $id."/".$mailid30."/".$msg1;die;
		}

		
		$db->select("nesote_email_contactgroup");
		$db->fields("name");
		$db->where("id=? and userid=?",[$id,$userid]);
		$rs=$db->query();
		$row=$db->fetchRow($rs);
		$groupname=$row[0];
		if(trim((string) $groupname) === trim((string) $name))
		{


			$flag=0;
			$msg=$this->getmessage(214);
			$msg1=str_replace('{groupname}',$name,$msg);

			$magic=get_magic_quotes_gpc();
			if($magic==1)
			{
				$msg1=stripslashes($msg1);
			}

			$db->select("nesote_email_contacts");
			$db->fields("id");
			$db->where("contactgroup=? and addedby=?",[$id,$userid]);
			$db->order("mailid asc");
			$db->limit(0,1);
			$result1=$db->query();
			$num1=$db->numRows($result1);
			if($num1!=0)
			{
				$row1=$db->fetchRow($result1);
				$mailid=$row1[0];
			}
			else
			{
				
				$db->select("nesote_liberyus_users");
				$db->fields("id");
				$db->where("status=?",[1]);
				$db->order("name asc");
				$db->limit(0,1);
				$result20=$db->query();
				$row20=$db->fetchRow($result20);
				$mailid=$row20[0];
			}
			$magic=get_magic_quotes_gpc();
			if($magic==1)
			{
				$msg1=stripslashes($msg1);
			}
			echo $id."/".$mailid."/".$msg1;die;
		}
        $db->update("nesote_email_contactgroup");
        $db->set("name=?",[$name]);
        $db->where("id=?",[$id]);
        $db->query();
        $userid=$this->getId();
        $username=$this->getusername($userid);
        $groupname1=mysql_REAL_escape_string($groupname);
        $this->saveLogs("Edit Contactgroup","$username has edited contactgroup $groupname1");
        //		$url=$this->url("layout/contactsleftpanel/$id");
        //		echo $url;die;
        $msg=$this->getmessage(214);
        $msg1=str_replace('{groupname}',$name,$msg);
        $magic=get_magic_quotes_gpc();
        if($magic==1)
			{
				$msg1=stripslashes($msg1);
			}


		$db->select("nesote_email_contacts");
		$db->fields("id");
		$db->where("contactgroup=? and addedby=?",[$id,$userid]);
		$db->order("mailid asc");
		$db->limit(0,1);
		$result1=$db->query();
		$num=$db->numRows($result1);
		if($num!=0)
		{
			$row1=$db->fetchRow($result1);
			$mailid=$row1[0];
		}
		else
		{
			
			$db->select("nesote_liberyus_users");
			$db->fields("id");
			$db->where("status=?",[1]);
			$db->order("name asc");
			$db->limit(0,1);
			$result20=$db->query();
			$row20=$db->fetchRow($result20);
			$mailid=$row20[0];
		}
		echo $id."/".$mailid."/".$msg1;die;
	}

	function editfolderAction()
	{
	
		$flag=1;$msg1="";
		$id=$this->getParam(1);
		$name=$this->getParam(2);
		$userid=$this->getId();

		if ($name=="") {
            $flag=0;
        }

		$db=new NesoteDALController();
		$db->select("nesote_email_customfolder");
		$db->fields("name");
		$db->where("userid=? and id!=?",[$userid,$id]);
		$rs1=$db->query();
		while($row1=$db->fetchRow($rs1))
		{
			if(trim((string) $row1[0]) === trim((string) $name))
			{
				$flag=0;break;
			}
		}

		if($flag==0)
		{

			$msg1=$this->getmessage(317);
			//$msg1=317;
			if ($row1[1]=="") {
                $row1[1]=1;
            }//inbox
			echo "0/".$msg1;die;
		}

		
		$db->select("nesote_email_customfolder");
		$db->fields("name");
		$db->where("userid=? and id=?",[$userid,$id]);
		$rs=$db->query();
		$row=$db->fetchRow($rs);
		$foldername=$row[0];

		if($foldername==$name)
		{
			$flag=0;
			
			$msg1=$this->getmessage(220);
			$msg1=str_replace('{foldername}',$name,$msg1);
			
		}

		if($flag==1)
		{
			
			$db->update("nesote_email_customfolder");
			$db->set("name=?",[$name]);
			$db->where("id=? and userid=?",[$id,$userid]);
			$rs=$db->query();

			$userid=$this->getId();
			$username=$this->getusername($userid);
			$this->saveLogs("Edit Customfolder","$username has edited customfolder $foldername ");

			//		$url=$this->url("layout/mailleftpanel/$id");
			//		echo $url;die;

			$msg1=$this->getmessage(220);
			$msg1=str_replace('{foldername}',$name,$msg1);
		}
		$db->select("nesote_email_customfolder");
		$db->fields("id");
		$db->where("id=? and userid=?",[$id,$userid]);
		$db->order("name asc");
		$db->limit(0,1);
		$result1=$db->query();
		$row1=$db->fetchRow($result1);

		echo $id."/".$row1[0]."/".$msg1;die;
	}

	function deletefolderAction(): never
	{
     
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		
		$id=$this->getParam(1);
		$userid=$this->getId();

		$folder=$this->getfoldername($id);

		$db=new NesoteDALController();
		$db->delete("nesote_email_customfolder");
		$db->where("id=? and userid=?",[$id,$userid]);
		$rs=$db->query();

		
		$db->delete("nesote_email_customfolder_mapping_$tablenumber");
		$db->where("folderid=?",[$id]);
		$rs=$db->query();

		
		$db->delete("nesote_email_attachments_$tablenumber");
		$db->where("folderid=?",[$id]);
		$rs=$db->query();


		$userid1=$this->getId();
		$username1=$this->getusername($userid1);
		$this->saveLogs("Delete mail folder","$username1 has deleted  mail folder $folder ");
		//header("Location:".$this->url("layout/mailleftpanel"));

		$msgid=222;

		//echo $insertid;die;

		
		$db->select("nesote_email_inbox_$tablenumber");
		$db->fields("id");
		$db->where("userid=?",[$userid]);
		$db->order("id desc");
		$db->limit(0,1);
		$result=$db->query();//echo $db->getQuery();
		$rs=$db->fetchRow($result);

		$mailid=$rs[0];
		//$url=$this->url('mail/detailmail/'.$folderid.'/'.$mailid.'/'.$msg);

		//echo $url.",".$folderid.",".$mailid;
		//exit;
		echo $mailid."/".$msgid;die;

		//echo "";exit(0);
	}

	function getfoldername($id)
	{//echo hai;
		//echo $id;
		$userid=$this->getId();

		$db=new NesoteDALController();
		$db->select("nesote_email_customfolder");
		$db->fields("name");
		$db->where("id=? and userid=?",[$id,$userid]);
		$result=$db->query();//echo $db->getQuery();
		$rs=$db->fetchRow($result);return $rs[0];
		//return $foldername;
	}

	function getfoldersname()
	{//echo hai;
		//echo $id;
		$userid=$this->getId();
		$id=$_COOKIE['folderid'];
		if($id>=10)
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_customfolder");
			$db->fields("name");
			$db->where("id=? and userid=?",[$id,$userid]);
			$result=$db->query();//echo $db->getQuery();
			$rs=$db->fetchRow($result);return $rs[0];
		}
		else
		{
			return "";
		}
		//return $foldername;
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
