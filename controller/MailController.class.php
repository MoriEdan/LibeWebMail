<?php
class MailController extends NesoteController
{
	
	function getloggedheader()
		{
			$io_username=$_COOKIE["e_username"];
			$io_password=$_COOKIE["e_password"];
			$lang_code=$_COOKIE["lang_mail"];
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
				
				
				
				
				$url=$portal_installation_url."/index.php?page=index/loggedcommonheader/".$lang_code."/".$servicekey."/".$result[0];

				

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

	function encode($input)
	{
		$temp = '';
		$length = strlen((string) $input);
		for($i = 0; $i < $length; $i++)
		$temp .= '%' . bin2hex((string) $input[$i]);
		return $temp;
	}
function getservicename()
	{
	   $this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		return $settings->getValue("engine_name");
	}
	function printmailAction()
	{
	
		$name=$this->getParam(1);$name=explode("_",(string) $name);$name=$name[0];
		$mailId=$this->getParam(2);
		$folderid=$this->getfolderid($name);
		$id=$this->getId();
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$select=new NesoteDALController();

		if ($folderid==1) {
            $select->select("nesote_email_inbox_$tablenumber");
        } elseif ($folderid==2) {
            $select->select("nesote_email_draft_$tablenumber");
        } elseif ($folderid==3) {
            $select->select("nesote_email_sent_$tablenumber");
        } elseif ($folderid==4) {
            $select->select("nesote_email_spam_$tablenumber");
        } elseif ($folderid==5) {
            $select->select("nesote_email_trash_$tablenumber");
        } elseif ($folderid>=10) {
            $select->select("nesote_email_customfolder_mapping_$tablenumber");
        }
		$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
		if ($folderid<10) {
            $select->where("id=? and userid=?",[$mailId,$id]);
        } else {
            $select->where("id=?",[$mailId]);
        }
		$result=$select->query();
		$row=$select->fetchRow($result);

		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$img=$settings->getValue("user_page_logo");
			
		$imgpath="admin/logo/$img";


		$name=$this->getName($id)." <$username".$this->getextension().">";
		$this->setValue("name",$name);

		$to="";$cc="";$bcc="";
		$time1=$this->gettimeinside($row[9]);
		if ($row[2]!="") {
            $to=$this->getmessage(31).": ".$row[2];
        }
		
		
		$from = $row[1];
        if ($row[3]!="") {
            $cc = $this->getmessage(32).": ".$row[3];
        }
		if ($row[4]!="") {
            $bcc = $this->getmessage(33).": ".$row[4];
        }
		
		
		$subj = $row[5];
		$subjtitle = $subj != "" ? " - ".$subj : $subj;
		$body = $row[6];
		$this->setValue("subjtitle",$subjtitle);$this->setValue("subj",$subj);
		$this->setValue("from",$from);$this->setValue("time1",$time1);
		$this->setValue("to",$to);
		$this->setValue("imgpath",$imgpath);
		

		$attachcnt=$this->getattachcountforPrint($row[11]);
		$this->getmessage(35);
		$att_src="";
		if($attachcnt!=0 && $attachcnt!="")
		{

			
			//$body.="<br><b>".$atchdtls."</b>";
			
			$body.="<br clear=\"all\"><div style=\"width:50%;border-top:1px #AAAAAA solid\"></div>";

			$attach=$this->getattachmentIconforPrint($mailId,$folderid);
			if($attach!="")
			{
				$arr=explode(",",(string) $attach);
				for($i=0;$i<$attachcnt;$i++)
				{
					$att=explode("::",$arr[$i]);

                    if ($att[2]==1) {
                        $att_src="<br><img src=\"$att[0]\" border=\"0\"  width=\"230\" height=\"170\"> <b>".$att[1]."</b>";
                    } else {
                        $att_src="<br><img src=\"$att[0]\" border=\"0\" > <b>".$att[1]."</b>";
                    }
					$body.=$att_src;
				}
				
			}
			
		}
		$this->setValue("body",$body);
		

	}
	
	function mailboxAction()
	{
		
		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }

	    require(__DIR__ . "/script.inc.php");
        include($config_path."database.default.config.php");
		
				
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$portal_status=$settings->getValue("portal_status");
		
		$valid=$this->validateUser();	

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{ //echo "he";
		$id=$this->getId();$userid=$id;
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$totalusagesize=$settings->getValue("user_memory_size");
		$memoryusage_publicview=$settings->getValue("memoryusage_publicview");
		if($memoryusage_publicview==1)
		{
			$memoryusage_publicview_area=$settings->getValue("memoryusage_publicview_area");
			if(($memoryusage_publicview_area==0))
			{
				$memoryusage_publicview_area=1;
			}
			$memorysize_format=$settings->getValue("memorysize_format");
			
		}
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

		$this->setValue("memoryusage_publicview_area",$memoryusage_publicview_area);
			
			if ($portal_status==1) {
                $this->setlogindetails();
            }
			// tip of day

			$select1=new NesoteDALController();
			$x=mysql_query("SELECT * FROM ".$db_tableprefix."nesote_tip_of_the_day ORDER BY RAND() LIMIT 0,1");
			$v=mysql_fetch_row($x);
			$tip_of_day_title=$v[1];
			$tip_of_day_msg=$v[2];
			$this->setValue("tip_of_day_title",$tip_of_day_title);

			$this->setValue("tip_of_day_msg",$tip_of_day_msg);


			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			$portal_status=$settings->getValue("portal_status");
			$this->setValue("portal_status",$portal_status);
				
			$todo=$settings->getValue("todolist");
			if ($todo=="") {
                $todo=0;
            }
			$this->setValue("todo",$todo);
				
			$select1->select("nesote_email_calendar_settings");
			$select1->fields("value");
			$select1->where("name=?",\CALENDAR);
			$result=$select1->query();
			$rs=$select1->fetchRow($result);
			$this->setValue("calendar_enable",$rs[0]);
				
				
			$tip_ofthe_day=$settings->getValue("tip_ofthe_day");
			$this->setValue("tip_ofthe_day",$tip_ofthe_day);

			$servicename=$settings->getValue("engine_name");
			$this->setValue("servicename",$servicename);

			$username .= $this->getextension();
			$this->setValue("username",$username);
			$url=$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
			if(strpos($url,"/index.php")!="")
			{
				$url=str_replace("/index.php","",$url);

			}
			$url="http://".$url;
			$this->setValue("path",$url);
			$img=$settings->getValue("user_page_logo");
			$imgpath="admin/logo/$img";
			$this->setValue("imgpath",$imgpath);
			// Logo end

			$reply_sub_predecessor=$settings->getValue("reply_sub_predecessor");
			$this->setValue("reply_pre",$reply_sub_predecessor);

			$forward_sub_predecessor=$settings->getValue("forward_sub_predecessor");
			$this->setValue("forward_pre",$forward_sub_predecessor);

			$select1=new NesoteDALController();
			$db1=new NesoteDALController();
			//customfolder loop start
			
			$select1=new NesoteDALController();
			$select1->select("nesote_email_customfolder");
			$select1->fields("id,name");
			$select1->where("userid=?",[$id]);
			$res1=$select1->query();
			$i=0;
			while($rw=$select1->fetchRow($res1))
			{

				$db1->select("nesote_email_customfolder_mapping_$tablenumber");
				$db1->fields("distinct mail_references");
				$db1->where("folderid=?",[$rw[0]]);
				$db1->order("time desc");
				$result1=$db1->query();
				$count=$db1->numRows($result1);



				$db1->select("nesote_email_customfolder_mapping_$tablenumber");
				$db1->fields("distinct mail_references");
				$db1->where("folderid=? and readflag=?",[$rw[0],0]);
				$db1->order("time desc");
				$result1=$db1->query();
				$count1=$db1->numRows($result1);

				$customFolder[$i][0]=$rw[0];
				$customFolder[$i][1]=$rw[1];
				$customFolder[$i][2]=$count;
				$customFolder[$i][3]=$count1;
				$countCookie="custom".$rw[0];
				setcookie($countCookie,(string) $count, ['expires' => "0", 'path' => "/"]);
				$i++;
			}
			$this->setValue("mpcount",$i);
			$this->setLoopValue("customfolders",$customFolder);
			//customfolder loop end


			$select1->select("nesote_email_inbox_$tablenumber");
			$select1->fields("id");
			$select1->where("userid=?",$id);
			$select1->order("time desc");
			$select1->limit(0,1);
			$result1=$select1->query();
			$latestidz=$select1->fetchRow($result1);
			$this->setValue("latest",$latestidz[0]);
			//finding the latest inbox mailid


			$select1->select("nesote_email_inbox_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=? and readflag=?",[$id,0]);
			$select1->order("time desc");
			$result1=$select1->query();
			$inboxCount1=$select1->numRows($result1);
			$this->setValue("inboxUnread",$inboxCount1);


			$select1->select("nesote_email_spam_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=? and readflag=?",[$id,0]);
			$select1->order("time desc");
			$result1=$select1->query();
			$spamCount1=$select1->numRows($result1);
			$this->setValue("spamUnread",$spamCount1);


			$select1->select("nesote_email_inbox_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=?",$id);
			$select1->order("time desc");
			$result1=$select1->query();
			$inboxCount=$select1->numRows($result1);
			$this->setValue("totalInbox",$inboxCount);
			setcookie('inbox',(string) $inboxCount, ['expires' => "0", 'path' => "/"]);



			$select1->select("nesote_email_draft_$tablenumber");
			$select1->fields("id");
			$select1->where("userid=? and just_insert=?",[$id,0]);
			$select1->order("time desc");
			$result1=$select1->query();
			$draftCount=$select1->numRows($result1);
			$this->setValue("totalDraft",$draftCount);
			setcookie('draft',(string) $draftCount, ['expires' => "0", 'path' => "/"]);




			$select1->select("nesote_email_sent_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=?",$id);
			$select1->order("time desc");
			$result1=$select1->query();
			$sentCount=$select1->numRows($result1);
			$this->setValue("totalSent",$sentCount);
			setcookie('sent',(string) $sentCount, ['expires' => "0", 'path' => "/"]);


			$select1=new NesoteDALController();
			$select1->select("nesote_email_spam_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=?",$id);
			$select1->order("time desc");
			$result1=$select1->query();
			$spamCount=$select1->numRows($result1);
			$this->setValue("totalSpam",$spamCount);
			setcookie('spam',(string) $spamCount, ['expires' => "0", 'path' => "/"]);



			$select1->select("nesote_email_trash_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=?",$id);
			$select1->order("time desc");
			$result1=$select1->query();
			$trashCount=$select1->numRows($result1);
			$this->setValue("totalTrash",$trashCount);
			setcookie('trash',(string) $trashCount, ['expires' => "0", 'path' => "/"]);


			//starred count start

			$select1->select("nesote_email_inbox_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=? and starflag=?",[$id,1]);
			$select1->order("time desc");
			$result1=$select1->query();
			$starredInboxCount=$select1->numRows($result1);


			$select1->select("nesote_email_draft_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=? and starflag=? and just_insert=?",[$id,1,0]);
			$select1->order("time desc");
			$result1=$select1->query();
			$starredDraftCount=$select1->numRows($result1);


			$select1->select("nesote_email_sent_$tablenumber");
			$select1->fields("distinct mail_references");
			$select1->where("userid=? and starflag=?",[$id,1]);
			$select1->order("time desc");
			$result1=$select1->query();
			$starredSentCount=$select1->numRows($result1);


			$select1->select("nesote_email_customfolder");
			$select1->fields("id,name");
			$select1->where("userid=?",[$id]);
			$res1=$select1->query();
			$i=0;
			$starredCustomFolderCount=0;
			while($rw=$select1->fetchRow($res1))
			{

				$db1->select("nesote_email_customfolder_mapping_$tablenumber");
				$db1->fields("distinct mail_references");
				$db1->where("folderid=? and starflag=?",[$rw[0],1]);
				$db1->order("time desc");
				$result1=$db1->query();
				$count=$db1->numRows($result1);
				$starredCustomFolderCount += $count;
				$i++;
			}
			$starredCount=$starredInboxCount+$starredDraftCount+$starredSentCount+$starredCustomFolderCount;
			$this->setValue("totalStarred",$starredCount);
			setcookie('starred',$starredCount, ['expires' => "0", 'path' => "/"]);
			//starred count end


			//mails per page start

			$select1->select("nesote_email_usersettings");
			$select1->fields("mails_per_page");
			$select1->where("userid=?",$id);
			$result1=$select1->query();
			$data1=$select1->fetchRow($result1);
			$perpagesize=$data1[0];
			if ($perpagesize==0) {
                $perpagesize=25;
            }
			if((!isset($perpagesize))||($perpagesize==0))
			{

				$perpagesize=$settings->getValue("mails_per_page");
			}
			$this->setValue("sizeMail",$perpagesize);
			//mails per page end

			//footer start
			$memorymsg=$this->getmessage(351);
			$year=date("Y",time());
			$msg1=str_replace('{year}',$year,$memorymsg);
			$this->setValue("footer",$msg1);
			//footer end


			//Calendar
			//$select1->select("nesote_email_calendar_settings");
			//$select1->fields("value");
			//$select1->where("name=?",calendar);
			//$result=$select1->query();
			//$rs=$select1->fetchRow($result);
			//$this->setValue("calendar_enable",$rs[0]);


			//signature


			$select1->select("nesote_email_usersettings");
			$select1->fields("signature,signatureflag,shortcuts,email_remainder,view_event");
			$select1->where("userid=?",$id);

			$result1=$select1->query();
			$sign=$select1->fetchRow($result1);
			$signature=	addslashes((string) $sign[0]);
			//$signature=htmlentities($sign[0]);
			$this->setValue("signature",$signature);
			$this->setValue("signatureflag",$sign[1]);
				
			$this->setValue("email_remainder",$sign[3]);
			$this->setValue("view_event",$sign[4]);
			$shallow=0;
			if ($sign[2]=="") {
                $sign[2]=0;
            }
			if($sign[2]==1)
			{
				$db1->select("nesote_email_settings");
				$db1->fields("value");
				$db1->where("name='shortcuts'");
				$result=$db1->query();
				$row=$result->fetchRow();
				if ($row[0]=="") {
                    $row[0]=0;
                }
				if ($row[0]==1) {
                    $shallow=1;
                }
			}
			$this->setValue("shallow",$shallow);


			// contacts


			$no=$db1->total("nesote_email_contacts","addedby=? and contactgroup=?",[$id,0]);
			$this->setValue("mycontact_total",$no);

			$db1->select("nesote_email_contactgroup");
			$db1->fields("*");
			$db1->where("userid=? ",[$id]);
			$db1->order("name asc");

			$result1=$db1->query();
			$no1=$db1->numRows($result1);

			$k=0;
			while($row=$db1->fetchRow($result1))
			{
				$select1->select("nesote_email_contacts");
				$select1->fields("*");
				$select1->where("addedby=? and contactgroup=?",[$id,$row[0]]);
				$result=$select1->query();
				$no=$select1->numRows($result);
				$groups[$k][0]=$row[0];
				$groups[$k][1]=$row[1];
				$groups[$k][2]=$no;
				$k++;
			}
			$this->setLoopValue("groups",$groups);
			//$this->setValue("groupname",$row1[1]);
			$this->setValue("gpcount",$no1);



			$addressbook=$settings->getValue("globaladdress_book");
			$this->setValue("addressbook",$addressbook);




			//			$db1->select("nesote_email_themes");
			//			$db1->fields("*");
			//			$db1->where("status=?",1);
			//			$result=$db1->query();
			//			$this->setLoopValue("theme",$result->getResult());

            $select1->select("nesote_email_usersettings");
			$select1->fields("theme_id");
			$select1->where("userid=?",$id);
			$res=$select1->query();
			$result=$select1->fetchRow($res);
			$themeid=$result[0];
			if(($result[0]=="")||($result[0]==0))
			{
			$themeid=$settings->getValue("themes");	
			if (($themeid=="")||($themeid==0)) {
                $themeid=2;
            }			
			}
			
			
			
			
			
			$select1->select("nesote_email_themes");
			$select1->fields("style");
			$select1->where("id=? and status=? ",[$themeid,1]);
			$res=$select1->query();
			$result=$select1->fetchRow($res);
			$headerstyle = $result[0] == "" ? "url(images/themeDemo2.jpg) no-repeat right 0 #192511" : $result[0];
			$header_theme=".header { background:".$headerstyle."}";
		    //.header { background:url(images/themeDemo8.jpg) no-repeat right 0 #192511}
			$this->setValue("header_theme",$header_theme);
			
			
			$override_themes=$settings->getValue("override_themes");
			$this->setValue("override_themes",$override_themes);

			$select1->select("nesote_email_themes");
			$select1->fields("id,style,status");
			$select1->where("status=?",1);
			$result=$select1->query();
			$this->setLoopValue("themestyle",$result->getResult());
  
			$g_contact=$select1->total("nesote_liberyus_users","status=?",[1]);
			$this->setValue("g_contact",$g_contact);


			///////////chat//////////////////

			$select1->select("nesote_chat_users");
			$select1->fields("sounds,soundspath,smileys");
			$select1->where("userid=?",$id);
			$res1=$select1->query();
			$result1=$select1->fetchRow($res1);
			$this->setValue("sounds",$result1[0]);

			if ($result1[1]!="") {
                $soundpath="userdata/$id/$result1[1]";
            } else
			{
				//$db1=new NesoteDALController();
				$db1->select("nesote_chat_settings");
				$db1->fields("value");
				$db1->where("name='chat_sound_file'");
				$dbres=$db1->query();
				$dbresult=$db1->fetchRow($dbres);
				$soundpath="admin/logo/chat_sound/$dbresult[0]";
			}
			$this->setValue("soundpath",$soundpath);



			$this->setValue("smileys",$result1[2]);






			//$sp="defaultSound=".$soundpath."&userSound=".$soundpath;
			$sp="defaultSound=".$soundpath;
			$this->setValue("sp",$sp);



			$chatCount=$select1->total("nesote_chat_message_$tablenumber","userid=?",[$id]);
			$this->setValue("totalchat",$chatCount);
			setcookie('chats',(string) $chatCount, ['expires' => "0", 'path' => "/"]);


			$select1->select("nesote_chat_settings");
			$select1->fields("value");
			$select1->where("name='chat_enable'");
			$result1=$select1->query();
			$rs1=$select1->fetchRow($result1);
			$this->setValue("chat_enable",$rs1[0]);




			$this->setValue("userid",$id);

			///////chat end////////////////

			/////////////////////////////////////// Sponsored Ads ///////////////////////////////


			$installurl=$settings->getValue("adserver_installurl");
			$authcode=$settings->getValue("adserver_authenticationcode");
			$displayurl=$settings->getValue("adserver_displayurl");
			$topadcount=$settings->getValue("adserver_topadcount");
			$rightadcount=$settings->getValue("adserver_rightadcount");
			$this->setValue("installurl",$installurl);
			$this->setValue("authcode",$authcode);
			$this->setValue("displayurl",$displayurl);
			$this->setValue("topadcount",$topadcount);
			$this->setValue("rightadcount",$rightadcount);
			//$count=$topadcount+$rightadcount;
			$topadtype=$settings->getValue("topadtype");
			$this->setValue("topadtype",$topadtype);
			$rightadtype=$settings->getValue("rightadtype");
			$this->setValue("rightadtype",$rightadtype);
			$publicadstatus=$settings->getValue("publicadstatus");
			$this->setValue("publicadstatus",$publicadstatus);
			$adserver_installurl=$settings->getValue("adserver_installurl");
			$this->setValue("adserver_installurl",$adserver_installurl);


			$select1->select("nesote_email_inbox_$tablenumber");
			$select1->fields("subject");
			$select1->where("userid=?",[$id]);
			$select1->group("mail_references");
			$select1->order("time desc");
			$select1->limit(0,5);
			$result1=$select1->query();//echo $select->getQuery();
			$keyword="";
			while($row1=$select1->fetchRow($result1))
			{
				$keyword.=$row11[0].",";
			}
			$keyword=substr($keyword,0,-1);

			if($rightadtype!=0)
			{
				if($rightadtype==1)
				{
					//
					$rightads=$settings->getValue("rightads_code");
					$this->setValue("rightads",$rightads);
				}
				if($rightadtype==2)
				{
					$sponsoredurl2=$installurl."xml-ads.php?user_id=0&auth_code=".$authcode."&start_record=".$topadcount."&count=".$rightadcount."&keywords=".urlencode($keyword)."&user_ip=".$_SERVER['REMOTE_ADDR'] ;

					//echo "<br>".$sponsoredurl2;
					$sponlinks2=$this->xmlsponsoredlinks($sponsoredurl2);

					//	echo $sponlinks2;
					$sponlinks2=str_replace("\r\n","",$sponlinks2);
					$sponlinks2=str_replace("\n","",$sponlinks2);
					$sponlinks2=str_replace("\r","",$sponlinks2);
					preg_match_all("/<ad><title>(.+?)<\/title><description>(.+?)<\/description><displayurl>(.+?)<\/displayurl><targeturl>(.+?)<\/targeturl><clickbid>(.+?)<\/clickbid><\/ad>/i",$sponlinks2,$sponresults2);

					//print_r($sponresults2);
					if(count($sponresults2[0])!=0)
					{
						$j=0;
                        $counter = count($sponresults2[0]);
						for($i=0;$i<$counter;$i++)//right array
						{
							$var=str_replace("<![CDATA[","",$sponresults2[1][$i]);
							$var=str_replace("]]>","",$var);
							$spresult2[$j][0]=$this->makebold(strip_tags($var),$keyword,0);
							$var=str_replace("<![CDATA[","",$sponresults2[2][$i]);
							$var=str_replace("]]>","",$var);
							$spresult2[$j][1]=$this->makebold(strip_tags($var),$keyword,1);
							$var=str_replace("<![CDATA[","",$sponresults2[3][$i]);
							$var=str_replace("]]>","",$var);
							$spresult2[$j][2]=html_entity_decode($var);
							$var=str_replace("<![CDATA[","",$sponresults2[4][$i]);
							$var=str_replace("]]>","",$var);
							$spresult2[$j][3]=html_entity_decode($var);
							$j++;
						}
							
							
						$j=0;
						for($i=0; $i<$rightadcount;$i++)
						{
							if($spresult2[0][0]!="")
							{
								$rightarray[$j][0]=$spresult2[$i][0];
								$rightarray[$j][1]=$spresult2[$i][1];
								$rightarray[$j][2]=$spresult2[$i][2];
								$rightarray[$j][3]=$spresult2[$i][3];
								$j++;
							}
						}
						// echo $rightadcount;
						//print_r($rightarray);
						if(count($rightarray)!=0)
						{
							$this->setValue("rpc","1");
							shuffle($rightarray);
							$this->setLoopValue("rightarray",$rightarray);
						}
						else {
                            $this->setValue("rpc","0");
                        }
					}

					if((count($rightarray)==0)&&($publicadstatus==1))
					{

						//$url=$installurl."xml-ads.php?user_id=0&auth_code=".$authcode."&start_record=0&count=".$count."&keywords=".urlencode($keyword)."&user_ip=".$_SERVER['REMOTE_ADDR']."&ps=2" ;
						$publicurl=$installurl."xml-ads.php?user_id=0&auth_code=".$authcode."&start_record=0&count=".$rightadcount."&user_ip=".$_SERVER['REMOTE_ADDR']."&ps=2" ;

						// echo $publicurl;
						//  exit;

						//   echo "<br>".count($rightarray).$publicurl;
						$publiclinks=$this->xmlsponsoredlinks($publicurl);


						$publiclinks=str_replace("\r\n","",$publiclinks);
						$publiclinks=str_replace("\n","",$publiclinks);
						$publiclinks=str_replace("\r","",$publiclinks);

						//  print_r( $publiclinks);

						preg_match_all("/<ad><title>(.+?)<\/title><description>(.+?)<\/description><displayurl>(.+?)<\/displayurl><targeturl>(.+?)<\/targeturl><clickbid>(.+?)<\/clickbid><\/ad>/i",$publiclinks,$pubresults);
					}

					if(count($pubresults[0])!=0)
					{
						$j=0;
                        $counter = count($pubresults[0]);
						for($i=0;$i<=$counter;$i++)
						{
							$var=str_replace("<![CDATA[","",$pubresults[1][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][0]=$this->makebold(strip_tags($var),$keyword,0);
							$var=str_replace("<![CDATA[","",$pubresults[2][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][1]=$this->makebold(strip_tags($var),$keyword,1);
							$var=str_replace("<![CDATA[","",$pubresults[3][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][2]=html_entity_decode($var);
							$var=str_replace("<![CDATA[","",$pubresults[4][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][3]=html_entity_decode($var);
							$j++;
						}
						//$db= new NesoteDALController();
						//$this->loadLibrary('Settings');
						//$settings=new Settings('nesote_email_settings');
						//$settings->loadValues();
						$rightadcount=$settings->getValue("adserver_rightadcount");
						$j=0;
						for($i=0; $i<$rightadcount;$i++)
						{

							if($publicresult[$i][0]!="")
							{
								$publicarray[$j][0]=$publicresult[$i][0];
								$publicarray[$j][1]=$publicresult[$i][1];
								$publicarray[$j][2]=$publicresult[$i][2];
								$publicarray[$j][3]=$publicresult[$i][3];
								$j++;
							}
						}

						if(count($publicarray)!=0)
						{
							$this->setValue("rpc","1");
							//shuffle($publicarray);
							$this->setLoopValue("rightarray",$publicarray);
						}
							
					}
				}
			}

			//echo $topadtype;
			if($topadtype!=0)
			{
				if($topadtype==1)
				{
					$topads=$settings->getValue("topads_code");
					$this->setValue("topads",$topads);
				}
				if($topadtype==2)
				{
					$sponsoredurl=$installurl."xml-ads.php?user_id=0&auth_code=".$authcode."&start_record=0&count=".$topadcount."&keywords=".urlencode($keyword)."&user_ip=".$_SERVER['REMOTE_ADDR'] ;

					$sponsoredlinks=$this->xmlsponsoredlinks($sponsoredurl);
					$sponsoredlinks=str_replace("\r\n","",$sponsoredlinks);
					$sponsoredlinks=str_replace("\n","",$sponsoredlinks);
					$sponsoredlinks=str_replace("\r","",$sponsoredlinks);
					preg_match_all("/<ad><title>(.+?)<\/title><description>(.+?)<\/description><displayurl>(.+?)<\/displayurl><targeturl>(.+?)<\/targeturl><clickbid>(.+?)<\/clickbid><\/ad>/i",$sponsoredlinks,$sponresults);

					//  print_r($sponresults);
					// exit;
					if(count($sponresults[0])!=0)//top array
					{
							
						$j=0;
                        $counter = count($sponresults[0]);
						for($i=0;$i<$counter;$i++)
						{
							$var=str_replace("<![CDATA[","",$sponresults[1][$i]);
							$var=str_replace("]]>","",$var);
							$spresult[$j][0]=$this->makebold(strip_tags($var),$keyword,0);
							$var=str_replace("<![CDATA[","",$sponresults[2][$i]);
							$var=str_replace("]]>","",$var);
							$spresult[$j][1]=$this->makebold(strip_tags($var),$keyword,1);
							$var=str_replace("<![CDATA[","",$sponresults[3][$i]);
							$var=str_replace("]]>","",$var);
							$spresult[$j][2]=html_entity_decode($var);
							$var=str_replace("<![CDATA[","",$sponresults[4][$i]);
							$var=str_replace("]]>","",$var);
							$spresult[$j][3]=html_entity_decode($var);
							$j++;
						}
							
						$j=0;
						for($i=0; $i<$topadcount;$i++)
						{
							if($spresult[$i][0]!="")
							{
								$toparray[$j][0]=$spresult[$i][0];
								$toparray[$j][1]=$spresult[$i][1];
								$toparray[$j][2]=$spresult[$i][2];
								$toparray[$j][3]=$spresult[$i][3];
								$j++;
							}


						}
						if(count($toparray)!=0)
						{
							$this->setValue("tpc","1");
							shuffle($toparray);
							$this->setLoopValue("toparray",$toparray);
						}
						else {
                            $this->setValue("tpc","0");
                        }

					}

					if((count($toparray)==0)&&($publicadstatus==1))
					{

						//$url=$installurl."xml-ads.php?user_id=0&auth_code=".$authcode."&start_record=0&count=".$count."&keywords=".urlencode($keyword)."&user_ip=".$_SERVER['REMOTE_ADDR']."&ps=2" ;
						$publicurl=$installurl."xml-ads.php?user_id=0&auth_code=".$authcode."&start_record=0&count=".$rightadcount."&user_ip=".$_SERVER['REMOTE_ADDR']."&ps=2" ;

						// echo $publicurl;
						//  exit;

						//   echo "<br>".count($rightarray).$publicurl;
						$publiclinks=$this->xmlsponsoredlinks($publicurl);


						$publiclinks=str_replace("\r\n","",$publiclinks);
						$publiclinks=str_replace("\n","",$publiclinks);
						$publiclinks=str_replace("\r","",$publiclinks);

						//  print_r( $publiclinks);

						preg_match_all("/<ad><title>(.+?)<\/title><description>(.+?)<\/description><displayurl>(.+?)<\/displayurl><targeturl>(.+?)<\/targeturl><clickbid>(.+?)<\/clickbid><\/ad>/i",$publiclinks,$pubresults);
					}

					if(count($pubresults[0])!=0)
					{
						$j=0;
                        $counter = count($pubresults[0]);
						for($i=0;$i<=$counter;$i++)
						{
							$var=str_replace("<![CDATA[","",$pubresults[1][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][0]=$this->makebold(strip_tags($var),$keyword,0);
							$var=str_replace("<![CDATA[","",$pubresults[2][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][1]=$this->makebold(strip_tags($var),$keyword,1);
							$var=str_replace("<![CDATA[","",$pubresults[3][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][2]=html_entity_decode($var);
							$var=str_replace("<![CDATA[","",$pubresults[4][$i]);
							$var=str_replace("]]>","",$var);
							$publicresult[$j][3]=html_entity_decode($var);
							$j++;
						}
						$db= new NesoteDALController();
						$this->loadLibrary('Settings');
						$settings=new Settings('nesote_email_settings');
						$settings->loadValues();
						$rightadcount=$settings->getValue("adserver_topadcount");
						$j=0;
						for($i=0; $i<$rightadcount;$i++)
						{

							if($publicresult[$i][0]!="")
							{
								$publicarray1[$j][0]=$publicresult[$i][0];
								$publicarray1[$j][1]=$publicresult[$i][1];
								$publicarray1[$j][2]=$publicresult[$i][2];
								$publicarray1[$j][3]=$publicresult[$i][3];
								$j++;
							}
						}

						if(count($publicarray1)!=0)
						{
							$this->setValue("rpc","1");
							//shuffle($publicarray);
							$this->setLoopValue("toparray",$publicarray1);
						}
							
					}
				}
			}
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
	

	function getbreak($url)
	{
		$len=strlen((string) $url);
		if ($len>35) {
            return "<br>";
        } else {
            return "";
        }
	}
	function xmlsponsoredlinks($url)
	{
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
		return $xmldata;
	}
	function makebold($content,$keyword,$wordstatus=0)
	{
		return $content;
	}
	function noofmailsAction(): never
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$folderid=$this->getParam(1);
		$db=new NesoteDALController();
		$db->select("nesote_email_customfolder_mapping_$tablenumber");
		$db->fields("id");
		$db->where("folderid=?",$folderid);
		$result=$db->query();
		$no=$db->numRows($result);
		echo $no;exit;
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
	


	function movetoselectAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$folder=$_POST['folder'];//echo $folder;exit;
			$ids=$_POST['ids'];
			$ids=substr((string) $ids,0,-11);
			$uid=$this->getId();
			$string="<div class=\"in\">";
			if ((strpos((string) $folder,"earch_")==1) || (strpos((string) $folder,"rom")==1)) {
                $string.="<a href=\"javascript:moveTo('inbox')\">".$this->getmessage(19)."</a>";
                $string=$string."<a href=\"javascript:moveTo('spam')\">".$this->getmessage(12)."</a>";
                $string=$string."<a href=\"javascript:deletemail(1)\">".$this->getmessage(22)."</a>";
                $db=new NesoteDALController();
                $db->select("nesote_email_customfolder");
                $db->fields("*");
                $db->where("userid=?",$uid);
                $res=$db->query();
                while($rw=$db->fetchRow($res))
				{
					$string=$string."<a href=\"javascript:moveTo('custom$rw[0]')\">".$rw[1]."</a>";
				}
            } elseif (strpos((string) $folder,"ustom")==1) {
                $string.="<a href=\"javascript:moveTo('inbox')\">".$this->getmessage(19)."</a>";
                $string=$string."<a href=\"javascript:moveTo('spam')\">".$this->getmessage(12)."</a>";
                $string=$string."<a href=\"javascript:deletemail(1)\">".$this->getmessage(22)."</a>";
                $folderid=str_replace("custom","",$folder);
                $db=new NesoteDALController();
                $db->select("nesote_email_customfolder");
                $db->fields("*");
                $db->where("userid=?",$uid);
                $res=$db->query();
                while($rw=$db->fetchRow($res))
				{
					if ($folderid!=$rw[0]) {
                        $string=$string."<a href=\"javascript:moveTo('custom$rw[0]')\">".$rw[1]."</a>";
                    }
				}
            } else
			{
				$folderid=$this->getfolderid($folder);
				if (($folderid==2)||($folderid==3)) {
                    $string=$string."<a href=\"javascript:moveTo('spam')\">".$this->getmessage(12)."</a>";
                    $string=$string."<a href=\"javascript:deletemail(1)\">".$this->getmessage(22)."</a>";
                } elseif ($folderid==5) {
                    $idz=explode("{nesote:+;}",$ids);
                    $draftCount=0;
                    $counter = count($idz);
                    for($i=0;$i<$counter;$i++)
					{
						$db1=new NesoteDALController();
						$db1->select("nesote_email_trash_$tablenumber");
						$db1->fields("backreference");
						$db1->where("id=?",$idz[$i]);
						$res1=$db1->query();
						$row=$db1->fetchRow($res1);
						if($row[0]==2)
						{
							$draftCount += 1;
							break;
						}
					}
                    if($draftCount==0)
					{
						$string=$string."<a href=\"javascript:moveTo('inbox')\">".$this->getmessage(19)."</a>";
						$string=$string."<a href=\"javascript:moveTo('spam')\">".$this->getmessage(12)."</a>";
						$db=new NesoteDALController();
						$db->select("nesote_email_customfolder");
						$db->fields("*");
						$db->where("userid=?",$uid);
						$res=$db->query();
						while($rw=$db->fetchRow($res))
						{
							$string=$string."<a href=\"javascript:moveTo('custom$rw[0]')\">".$rw[1]."</a>";
						}
					}
                } else
				{

					if ($folderid!=1) {
                        $string=$string."<a href=\"javascript:moveTo('inbox')\">".$this->getmessage(19)."</a>";
                    }
					if ($folderid!=4) {
                        $string=$string."<a href=\"javascript:moveTo('spam')\">".$this->getmessage(12)."</a>";
                    }
					if ($folderid!=5) {
                        $string=$string."<a href=\"javascript:deletemail(1)\">".$this->getmessage(22)."</a>";
                    }
					$db=new NesoteDALController();
					$db->select("nesote_email_customfolder");
					$db->fields("*");
					$db->where("userid=?",$uid);
					$res=$db->query();
					while($rw=$db->fetchRow($res))
					{
						$string=$string."<a href=\"javascript:moveTo('custom$rw[0]')\">".$rw[1]."</a>";
					}
				}

			}
$string .= "</div>";
			echo $string;exit;
		}
	}

	function insertion_sort($arr)
	{

		$counter = count($arr);
        for($j=1; $j < $counter; $j++) {
			$tmp = $arr[$j];
			$i = $j;
			while(($i >= 0) && ($arr[$i-1] > $tmp)) {
				$arr[$i] = $arr[$i-1];
				$i--;
			}
			$arr[$i] = $tmp;
		}

	}

	function getmailsbodyAction()
	{
		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$name=$this->getParam(1);
			$page=$this->getParam(2);
			$size=$this->getParam(3);
			$fieldArray=$this->getParam(4);
			$total=$this->getParam(5);
			$mailid_array=$this->getParam(6);
			
			$mailid_array=substr_replace($mailid_array,"",-1);
			$mailid_array1=explode(",",$mailid_array);
			$id=$this->getId();
			$fieldz="";
			$fieldsArry=explode(",",(string) $fieldArray);
			$strt=($page-1)*$size;
			if (($strt+$size)>$total) {
                $size=$total-$strt+1;
            }
			$no_pages=$total/$size;
			$flds=substr($fieldz,0,-1);
			$select1=new NesoteDALController();
			$select=new NesoteDALController();
			$select->select("nesote_email_usersettings");
			$select->fields("external_content");
			$select->where("userid=?",[$id]);
			$result=$select->query();
			$row2=$select->fetchRow($result);
			$external_content=$row2[0];
			if($external_content!=0)
			{
				$external_content_flag=0;
				$external_content_display=1;
			}
			else
			{

				$external_content_flag=1;
				$external_content_display=0;
				$select->select("nesote_image_display");
				$select->fields("*");
				$select->where("userid=? and mailid=?",[$id,$mailId]);
				$res3=$select->query();
				$rw3=$select->fetchRow($res3);
				$no2=$select->numRows($res3);//echo $no2."@@@@@@@@@@@";
				if($no2==1)
				{
					$external_content_display=1;
				}
				else
				{
					$cooky=$_COOKIE["image_display"];
					$cookys=explode(",",(string) $cooky);
					$nos=count($cookys);
					$new=0;
					for($r=0;$r<$nos;$r++)
					{
						$combo[$r]=explode(":",$cookys[$r]);
						if(($combo[$r][0]==$fldrid)&&($combo[$r][1]==$mailId))
						{
							$new=1;
						}
					}
					if ($new==1) {
                        $external_content_display=1;
                    }
				}
			}
			

			if ($name=="inbox") {
                $arry="";
                $arry1="";
                $var=0;
                $returnstr="";
                $counter = count($mailid_array1);
                for($u=0;$u<=$counter;$u++)
				{

					$fldr_a=$mailid_array1[$u];
					$fldr_array=explode("_",$fldr_a);//print_r($fldr_array);

					if ($fldr_array[0]=="inbox") {
                        $select->select("nesote_email_inbox_$tablenumber");
                    }

					$select->fields("mail_references");

					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();//echo $select->getQuery();

					$row=$select->fetchRow($result);
					// $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);//print_r($folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);//print_r($mailidArray);

					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
						//
							

							
						if($j==count($folderArray[1])-1)
						{

							if ($folderArray[1][$j]==1) {
                                $select1->select("nesote_email_inbox_$tablenumber");
                            } else {
                                $select1->select("nesote_email_sent_$tablenumber");
                            }
							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{

								$arry.=$row1[0]."{nesote_,}";//echo $arry;
									
								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);	//echo jjl;
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";
								$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
									
							}
						}

							
					}

					$arry.="{nesote_separator}";$var++;
				}
                $returnstr=substr($arry,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="draft") {
                $arry="";
                $arry1="";
                $var=0;
                $returnstr="";
                $counter = count($mailid_array1);
                for($u=0;$u<$counter;$u++)
				{

					$fldr_a=$mailid_array1[$u];
					$fldr_array=explode("_",$fldr_a);


					$select->select("nesote_email_draft_$tablenumber");

					$select->fields("mail_references");
					//$select->fields("mail_references");
					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();

					$row=$select->fetchRow($result);
					//echo $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
							
						if($j==count($folderArray[1])-1)
						{

							$select1->select("nesote_email_draft_$tablenumber");
							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{

								$arry.=$row1[0]."{nesote_,}";//echo $arry;

								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";
								$arry.="draft{nesote_,}";

							}
						}

							
					}

					$arry.="{nesote_separator}";$var++;
				}
                $returnstr=substr($arry,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="sent") {
                //print_r($mailid_array1);
                $arry="";
                $arry1="";
                $var=0;
                $returnstr="";
                $counter = count($mailid_array1);
                for($u=0;$u<$counter;$u++)
				{

					$fldr_a=$mailid_array1[$u];
					$fldr_array=explode("_",$fldr_a);//print_r($fldr_array);

					//if($fldr_array[1]=="inbox")
					//	$select->select("nesote_email_inbox_$tablenumber");
					//	else if($fldr_array[1]=="sent")
					$select->select("nesote_email_sent_$tablenumber");
					//	else
					//		$select->select("nesote_email_customfolder_mapping_$tablenumber");

					$select->fields("mail_references");
					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();

					$row=$select->fetchRow($result);
					//echo $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);//print_r($folderArray)."<br>";
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);

					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
							
						if($j==count($folderArray[1])-1)
						{
							// $folderArray[1][$j];
							if ($folderArray[1][$j]==1) {
                                $select1->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $select1->select("nesote_email_sent_$tablenumber");
                            } else {
                                $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                            }

							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{

								$arry.=$row1[0]."{nesote_,}";//echo $arry;

								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";
								$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";

							}
						}

							
					}

					$arry.="{nesote_separator}";$var++;
				}
                $returnstr=substr($arry,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="spam") {
                $arry="";
                $arry1="";
                $var=0;
                $returnstr="";
                $counter = count($mailid_array1);
                for($u=0;$u<$counter;$u++)
				{

					$fldr_a=$mailid_array1[$u];
					$fldr_array=explode("_",$fldr_a);
					$select->select("nesote_email_spam_$tablenumber");

					$select->fields("mail_references");
					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();

					$row=$select->fetchRow($result);
					//echo $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);//print_r($folderArray);print_r($mailidArray);
					$select1=new NesoteDALController();
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
							
						if($j==count($folderArray[1])-1)
						{

							$select1->select("nesote_email_spam_$tablenumber");
							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{
								$arry.=$row1[0]."{nesote_,}";//echo $arry;

								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";
								$arry.="spam{nesote_,}";

							}
						}

							
					}//echo $arry."/////";

					$arry.="{nesote_separator}";$var++;
				}
                //echo $arry."//////";exit;
                $returnstr=substr($arry,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="trash") {
                $arry="";
                $arry1="";
                $var=0;
                $returnstr="";
                $counter = count($mailid_array1);
                for($u=0;$u<$counter;$u++)
				{

					$fldr_a=$mailid_array1[$u];
					$fldr_array=explode("_",$fldr_a);
					$select->select("nesote_email_trash_$tablenumber");

					$select->fields("mail_references");
					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();

					$row=$select->fetchRow($result);
					//echo $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					$select1=new NesoteDALController();
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
							
						if($j==count($folderArray[1])-1)
						{


							$select1->select("nesote_email_trash_$tablenumber");
							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{

								$arry.=$row1[0]."{nesote_,}";//echo $arry;

								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";
								$arry.="trash{nesote_,}";

							}
						}

							
					}

					$arry.="{nesote_separator}";$var++;
				}
                $returnstr=substr($arry,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="starred") {
                $arry="";
                $arry1="";
                $var=0;
                $returnstr="";
                $counter = count($mailid_array1);
                for($u=0;$u<$counter;$u++)
				{
					$foldr_obj=explode(":",$mailid_array1[$u]);
					$fldr_a=$foldr_obj[1];

					$fldr_array=explode("_",$fldr_a);

					if ($fldr_array[0]=="inbox") {
                        $select->select("nesote_email_inbox_$tablenumber");
                    } elseif ($fldr_array[0]=="draft") {
                        $select->select("nesote_email_draft_$tablenumber");
                    } elseif ($fldr_array[0]=="sent") {
                        $select->select("nesote_email_sent_$tablenumber");
                    } else {
                        $select->select("nesote_email_customfolder_mapping_$tablenumber");
                    }

					$select->fields("mail_references");
					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();

					$row=$select->fetchRow($result);
					//echo $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);

					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
							
						if($j==count($folderArray[1])-1)
						{

							if ($folderArray[1][$j]==1) {
                                $select1->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==2) {
                                $select1->select("nesote_email_draft_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $select1->select("nesote_email_sent_$tablenumber");
                            } else {
                                $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                            }

							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{

								$arry.=$row1[0]."{nesote_,}";//echo $arry;

								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";
								$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";

							}
						}

							
					}

					$arry.="{nesote_separator}";$var++;
				}
                $returnstr=substr($arry,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ((str_starts_with((string) $name, "search"))||(str_starts_with((string) $name, "from"))) {
                //print_r($mailid_array1);
                //echo count($mailid_array1);
                //$search=substr_replace($search,"",-3);
                //print_r($mailid_array1);
                $arry="";
                $arry1="";
                $var=0;
                $returnstr="";
                $counter = count($mailid_array1);
                for($u=0;$u<$counter;$u++)
				{

					$fldr_a=$mailid_array1[$u];//echo $fldr_a;
					//$fldr_a=str_replace("starred:","",$fldr_a);
					$pos=strpos($fldr_a,">");//echo $fldr_a."ddd";
					$fldr_a=substr_replace($fldr_a,"",0,$pos+1);//echo $fldr_a;
					$fldr_array=explode("_",$fldr_a);

					if ($fldr_array[0]=="inbox") {
                        $select->select("nesote_email_inbox_$tablenumber");
                    } elseif ($fldr_array[0]=="draft") {
                        $select->select("nesote_email_draft_$tablenumber");
                    } elseif ($fldr_array[0]=="sent") {
                        $select->select("nesote_email_sent_$tablenumber");
                    } elseif ($fldr_array[0]=="spam") {
                        $select->select("nesote_email_spam_$tablenumber");
                    } elseif ($fldr_array[0]=="trash") {
                        $select->select("nesote_email_trash_$tablenumber");
                    } else {
                        $select->select("nesote_email_customfolder_mapping_$tablenumber");
                    }

					$select->fields("mail_references");
					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();//echo $select->getQuery();

					$row=$select->fetchRow($result);//echo $row[0];
					//echo $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					$select1=new NesoteDALController();//print_r($folderArray);
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
							
						if($j==count($folderArray[1])-1)
						{

							if ($folderArray[1][$j]==1) {
                                $select1->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==2) {
                                $select1->select("nesote_email_draft_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $select1->select("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$j]==4) {
                                $select1->select("nesote_email_spam_$tablenumber");
                            } elseif ($folderArray[1][$j]==5) {
                                $select1->select("nesote_email_trash_$tablenumber");
                            } else {
                                $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                            }

							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{

								$arry.=$row1[0]."{nesote_,}";//echo $arry;

								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";

							}
						}
							


							
					}
					$arry.="{nesote_separator}";$var++;

				}
                $returnstr=substr($arry,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } else
			{	//echo "dd";
				$arry="";$arry1="";$var=0;$returnstr="";
                $counter = count($mailid_array1);
				for($u=0;$u<$counter;$u++)
				{



					$fldr_a=$mailid_array1[$u];
					$fldr_array=explode("_",$fldr_a);

					//					if($fldr_array[1]=="inbox")
					//						$select->select("nesote_email_inbox_$tablenumber");
					//						else if($folderArray[1][$j]==2)
					//						$select->select("nesote_email_draft_$tablenumber");



					$select->select("nesote_email_customfolder_mapping_$tablenumber");

					$select->fields("mail_references");
					$select->where("id=?",[$fldr_array[1]]);

					$result=$select->query();//echo $select->getQuery();

					$row=$select->fetchRow($result);
					//echo $select->getQuery();
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);

					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
							
						if($j==count($folderArray[1])-1)
						{

							//							if($folderArray[1][$j]==1)
							//							$select1->select("nesote_email_inbox_$tablenumber");
							//							else if($folderArray[1][$j]==2)
							//							$select1->select("nesote_email_draft_$tablenumber");
							if ($folderArray[1][$j]==3) {
                                $select1->select("nesote_email_sent_$tablenumber");
                            } else {
                                $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                            }

							$select1->fields("id,body,from_list");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();//echo $select1->getQuery();
							if($row1=$select1->fetchRow($result1))
							{

								$arry.=$row1[0]."{nesote_,}";//echo $arry;

								$external=$this->getExternalcontentFlag($row1[1],$row1[2]);
								$externals=explode("{nesote_comma}",(string) $external);//print_r($externals)."//";
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[1]."{nesote_,}";
								$arry.=$mailid_array1[$u]."{nesote_,}";
								$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";


							}
						}

							
					}

					$arry.="{nesote_separator}";$var++;
				}
					

				$returnstr=substr($arry,0,-18);
				$returnstr=$returnstr."{nesote_count}".$var;echo $returnstr;exit;

			}



		}
	}

	function getmailsAction()
	{

		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$name=$this->getParam(1);
			$page=$this->getParam(2);
			$size=$this->getParam(3);
			$fieldArray=$this->getParam(4);
			$total=$this->getParam(5);
			$advance_search=$this->getParam(7);
			$search1=explode("*",(string) $advance_search);
			$search1_fr=explode("?",$search1[0]);
			$search1_from=trim($search1_fr[1]);
			$search1_t=explode("?",$search1[1]);
			$search1_to=trim($search1_t[1]);

			$search1_sub=explode("?",$search1[2]);
			$search1_subject=trim($search1_sub[1]);

			$search1_key=explode("?",$search1[3]);
			$search1_keywords=trim($search1_key[1]);
			$search1_fol=explode("?",$search1[4]);
			$search1_folderid=trim($search1_fol[1]);
			$search1_at=explode("?",$search1[5]);
			$search1_attach=trim($search1_at[1]);

			$id=$this->getId();
			$fieldz="";
			$fieldsArry=explode(",",(string) $fieldArray);
			$strt=($page-1)*$size;
			if (($strt+$size)>$total) {
                $size=$total-$strt+1;
            }
			$no_pages=$total/$size;
			$flds=substr($fieldz,0,-1);

			$select=new NesoteDALController();
			$select1=new NesoteDALController();
			$select->select("nesote_email_usersettings");
			$select->fields("external_content");
			$select->where("userid=?",[$id]);
			$result=$select->query();//echo $select->getQuery();
			$row2=$select->fetchRow($result);//echo $external_content."--------------";
			$external_content=$row2[0];//echo $external_content."--------------";
			if($external_content!=0)
			{
				$external_content_flag=0;
				$external_content_display=1;
			}
			else
			{

				$external_content_flag=1;
				$external_content_display=0;
				$select->select("nesote_image_display");
				$select->fields("*");
				$select->where("userid=? and mailid=?",[$id,$mailId]);
				$res3=$select->query();
				$rw3=$select->fetchRow($res3);
				$no2=$select->numRows($res3);//echo $no2."@@@@@@@@@@@";
				if($no2==1)
				{
					$external_content_display=1;
				}
				else
				{
					$cooky=$_COOKIE["image_display"];
					$cookys=explode(",",(string) $cooky);
					$nos=count($cookys);
					$new=0;
					for($r=0;$r<$nos;$r++)
					{
						$combo[$r]=explode(":",$cookys[$r]);
						if(($combo[$r][0]==$fldrid)&&($combo[$r][1]==$mailId))
						{
							$new=1;
						}
					}//print_r($combo);exit;
					//echo $new."&&&&&&&&&&&&&&&";
					if ($new==1) {
                        $external_content_display=1;
                    }
				}
			}

			if($advance_search!="")
			{
					
				$id=$this->getId();
				$search="";

				if ($search1_from !== "") {
                    $search.="and from_list like '%$search1_from%'";
                }
				//	$to=$_POST['to'];
				if ($search1_to !== "") {
                    $search.="and (to_list like '%$search1_to%' or cc like '%$search1_to%' or bcc like '%$search1_to%')";
                }

				if ($search1_subject !== "") {
                    $search.="and subject like '%$search1_subject%'";
                }

				if ($search1_keywords !== "") {
                    $search.="and body like '%$search1_keywords%' ";
                }

				//$search=substr_replace($search,"",-3);//echo $search;exit;
				$folderid=$search1_folderid;
					

					
					
				$userid=$this->getId();
				$var=0;$arry="";
				$db=new NesoteDALController();
				$select=new NesoteDALController();
				if ($folderid==0) {
                    $returnstr="";
                    if($search1_attach==1)
					{
						$db->select("nesote_email_attachments_$tablenumber");
						$db->fields("distinct mailid,folderid");
						$db->where("attachment=? and userid=?",[1,$id]);
						$atres=$db->query();//echo $db->getQuery();
						while($atresult=$db->fetchRow($atres))
						{



							if ($atresult[1]==1) {
                                $select->select("nesote_email_inbox_$tablenumber");
                            } elseif ($atresult[1]==2) {
                                $select->select("nesote_email_draft_$tablenumber");
                            } elseif ($atresult[1]==3) {
                                $select->select("nesote_email_sent_$tablenumber");
                            } elseif ($atresult[1]==4) {
                                $select->select("nesote_email_spam_$tablenumber");
                            } elseif ($atresult[1]==5) {
                                $select->select("nesote_email_trash_$tablenumber");
                            }
							//else if($atresult[1]>=10)
							//$select->select("nesote_email_customfolder_mapping");

							$select->fields("mail_references,time");
							//if($atresult[1]<10)
							$select->where("userid=? and id=? $search",[$id,$atresult[0]]);
							//else
							//$select->where("folderid=? and id=? $search",array($atresult[1],$atresult[0]));
							$select->group("mail_references");
							$select->order("time desc");
							if($atresult[1]<10)
							{
								$result=$select->query();


								while($row=$select->fetchRow($result))
								{
									$refArry[$var][0]=$row[0];
									$refArry[$var][1]=$row[1];
									$var++;
								}

							}
							if($atresult[1]>=10)
							{
									
								$db->select("nesote_email_customfolder");
								$db->fields("id");
								$db->where("userid=? ",[$userid]);
								$rslt=$db->query();
								$no=$db->numRows($rslt);
								while($rows=$db->fetchRow($rslt))
								{
									$select->select("nesote_email_customfolder_mapping_$tablenumber");
									$select->fields("mail_references,time");
									$select->where("folderid=? and id=? $search",[$rows[0],$atresult[0]]);
									$select->group("mail_references");
									$select->order("time desc");
									$result=$select->query();//echo $select->getQuery();
									while($row=$select->fetchRow($result))
									{
										$refArry[$var][0]=$row[0];
										$refArry[$var][1]=$row[1];
										$var++;
									}

								}
							}
						}
					}

					else
					{

						$select->select("nesote_email_inbox_$tablenumber");
						$select->fields("mail_references,time");
						$select->where("userid=? $search ",[$id]);
						$select->group("mail_references");
						$select->order("time desc");
						$result=$select->query();

						//$var=0;
						while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}

						//print_r($refArry);exit;

						$select->select("nesote_email_sent_$tablenumber");
						$select->fields("mail_references,time");
						$select->where("userid=? $search",[$id]);
						$select->group("mail_references");
						$select->order("time desc");
						$result=$select->query();
						while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}



						$select->select("nesote_email_draft_$tablenumber");
						$select->fields("mail_references,time");
						$select->where("userid=? and just_insert=? $search",[$id,0]);
						$select->order("time desc");
						$result=$select->query();
						while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}

						$select->select("nesote_email_spam_$tablenumber");
						$select->fields("mail_references,time");
						$select->where("userid=? $search",[$id]);
						$select->group("mail_references");
						$select->order("time desc");
						$result=$select->query();
						while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}

						$select->select("nesote_email_trash_$tablenumber");
						$select->fields("mail_references,time");
						$select->where("userid=? $search",[$id]);
						$select->group("mail_references");
						$select->order("time desc");
						$result=$select->query();
						while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}


						$userid=$this->getId();
						//$folderid=substr($name,6);

						$db->select("nesote_email_customfolder");
						$db->fields("id");
						$db->where("userid=? ",[$userid]);
						$rslt=$db->query();
						$no=$db->numRows($rslt);

						while($rows=$db->fetchRow($rslt))
						{
							$select->select("nesote_email_customfolder_mapping_$tablenumber");
							$select->fields("mail_references,time");
							$select->where("folderid=? $search",[$rows[0]]);
							$select->group("mail_references");
							$select->order("time desc");
							$result=$select->query();
							while($row=$select->fetchRow($result))
							{
								$refArry[$var][0]=$row[0];
								$refArry[$var][1]=$row[1];
								$var++;
							}

						}
					}
                    $var1 = $var != 0 ? 1 : 0;
                    $refrArray[0][0]=$refArry[0][0];
                    $refrArray[0][1]=$refArry[0][1];
                    //echo $refArry[0][0];exit;
                    for($i=1;$i<$var;$i++)
					{
						$check=0;
						for($j=0;$j<$var1;$j++)
						{
							if ($refrArray[$j][0]==$refArry[$i][0]) {
                                $check=1;
                            }
						}
						if($check==0)
						{
							$refrArray[$var1][0]=$refArry[$i][0];
							$refrArray[$var1][1]=$refArry[$i][1];
							$var1++;
						}
					}
                    //print_r($refArry);
                    for($i=0;$i<$var1;$i++)
					{
						for($j=$i+1;$j<$var;$j++)
						{
							if($refrArray[$j][1]>$refrArray[$i][1])
							{
								$temp[0]=$refrArray[$j][0];
								$temp[1]=$refrArray[$j][1];
								$refrArray[$j][0]=$refrArray[$i][0];
								$refrArray[$j][1]=$refrArray[$i][1];
								$refrArray[$i][0]=$temp[0];
								$refrArray[$i][1]=$temp[1];
							}
						}
					}
                    //print_r($refrArray);exit;
                    $end=$strt+$size;
                    $count=0;
                    if ($var1<$end) {
                        $end=$var1;
                    }
                    //echo $end."/".$strt."/".$var1;exit;
                    for($z=$strt;$z<$end;$z++)
					{
						$arry="";$arry1="";$in_flag=0;$se_flag=0;$cu_flag=0;$dr_flag=0;$sp_flag=0;$tr_flag=0;$object_search="";
						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $refrArray[$z][0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $refrArray[$z][0],$mailidArray);
						for($j=count($folderArray[1])-1;$j>=0;$j--)
						{

							if ($folderArray[1][$j]==1) {
                                $select->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==2) {
                                $select->select("nesote_email_draft_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $select->select("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$j]==4) {
                                $select->select("nesote_email_spam_$tablenumber");
                            } elseif ($folderArray[1][$j]==5) {
                                $select->select("nesote_email_trash_$tablenumber");
                            } else {
                                $select->select("nesote_email_customfolder_mapping_$tablenumber");
                            }
							$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
							$select->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select->query(); //echo $select1->getQuery();
							$row1=$select->fetchRow($result1);

							if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                                $objects1="inbox_".$row1[0];
                                $in_flag=1;
                            } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                $objects2="sent_".$row1[0];
                                $se_flag=1;
                            } elseif (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                $objects3=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                $cu_flag=1;
                            } elseif (($dr_flag==0)&&($folderArray[1][$j]==2)) {
                                $objects6="draft_".$row1[0];
                                $dr_flag=1;
                            } elseif (($sp_flag==0)&&($folderArray[1][$j])==4) {
                                $objects4="spam_".$row1[0];
                                $sp_flag=1;
                            } elseif (($tr_flag==0)&&($folderArray[1][$j]==5)) {
                                $objects5="trash_".$row1[0];
                                $tr_flag=1;
                            }

							if($j==count($folderArray[1])-1)
							{
								$object_id=$row1[0];
								$object="search".$folderid."_".$advance_search.">".$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];

								for($i=0;$i<8;$i++)
								{   if ($i==0) {
                                    $arry1.="{nesote_id}{nesote_,}";
                                } elseif ($i==5) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
								}
								$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
								$arry1.=$this->gettime($row1[9])."{nesote_,}";
								$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
								$arry1.=$row1[9]."{nesote_,}";
								$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry1.=count($folderArray[1])."{nesote_,}";
								$arry1.="adsearch{nesote}{nesote_,}";
								$arry1.="adsearch_objects";

								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="1{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="adsearch{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="adsearch_objects";

							}
							else
							{
									
									
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="adsearch{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="adsearch_objects";

							}
							$arry.="{nesote_ref}";
						}
						if ($in_flag==1) {
                            $object_search.=$objects1.".";
                        }
						if ($se_flag==1) {
                            $object_search.=$objects2.".";
                        }
						if ($cu_flag==1) {
                            $object_search.=$objects3.".";
                        }
						if ($dr_flag==1) {
                            $object_search.=$objects6.".";
                        }
						if ($sp_flag==1) {
                            $object_search.=$objects4.".";
                        }
						if ($tr_flag==1) {
                            $object_search.=$objects5.".";
                        }
						$arry=str_replace("adsearch_objects",$object_search,$arry);
						$arry1=str_replace("adsearch_objects",$object_search,$arry1);
						$arry1=str_replace("{nesote_id}",$object_id,$arry1);
						$arry=str_replace("adsearch{nesote}",$object,$arry);
						$arry1=str_replace("adsearch{nesote}",$object,$arry1);
						$arry=substr($arry,0,-12);
						$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
						$count++;//echo "kkk".$count;
					}
                    //echo $arry;exit;
                    $returnstr=substr($returnstr,0,-18);
                    $returnstr=$returnstr."{nesote_count}".$count;
                    echo $returnstr;
                    exit;
                } elseif ($folderid<10) {
                    if($search1_attach==1)
				 {
				 	$db->select("nesote_email_attachments_$tablenumber");
				 	$db->fields("distinct mailid");
				 	$db->where("attachment=? and folderid=? and userid=?",[1,$folderid,$id]);
				 	$atres=$db->query();	$returnstr="";
				 	while($atresult=$db->fetchRow($atres))
				 	{

							if ($folderid==1) {
                                $select->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderid==2) {
                                $select->select("nesote_email_draft_$tablenumber");
                            } elseif ($folderid==3) {
                                $select->select("nesote_email_sent_$tablenumber");
                            } elseif ($folderid==4) {
                                $select->select("nesote_email_spam_$tablenumber");
                            } elseif ($folderid==5) {
                                $select->select("nesote_email_trash_$tablenumber");
                            }
							$select->fields("mail_references");
							$select->where("userid=? and id=? $search",[$id,$atresult[0]]);
							$select->group("mail_references");
							$select->order("time desc");
							$select->limit($strt,$size);
							$result=$select->query();//echo $select->getQuery();





							while($row=$select->fetchRow($result))
							{
								$arry="";$arry1="";$in_flag=0;$se_flag=0;$dr_flag=0;$sp_flag=0;$tr_flag=0;$object_search="";
								preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
								preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
								for($j=(count($folderArray[1])-1);$j>=0;$j--)//print_r($mailidArray);
								{

									if ($folderArray[1][$j]==1) {
                                        $select->select("nesote_email_inbox_$tablenumber");
                                    } elseif ($folderArray[1][$j]==2) {
                                        $select->select("nesote_email_draft_$tablenumber");
                                    } elseif ($folderArray[1][$j]==3) {
                                        $select->select("nesote_email_sent_$tablenumber");
                                    } elseif ($folderArray[1][$j]==4) {
                                        $select->select("nesote_email_spam_$tablenumber");
                                    } elseif ($folderArray[1][$j]==5) {
                                        $select->select("nesote_email_trash_$tablenumber");
                                    }
									$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
									$select->where("id=?",[$mailidArray[1][$j]]);
									$result1=$select->query();//echo $select->getQuery();
									$row1=$select->fetchRow($result1);

									if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                                        $objects1="inbox_".$row1[0];
                                        $in_flag=1;
                                    } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                        $objects2="sent_".$row1[0];
                                        $se_flag=1;
                                    } elseif (($dr_flag==0)&&($folderArray[1][$j]==2)) {
                                        $objects3="draft_".$row1[0];
                                        $dr_flag=1;
                                    } elseif (($sp_flag==0)&&($folderArray[1][$j])==4) {
                                        $objects4="spam_".$row1[0];
                                        $sp_flag=1;
                                    } elseif (($tr_flag==0)&&($folderArray[1][$j]==5)) {
                                        $objects5="trash_".$row1[0];
                                        $tr_flag=1;
                                    } elseif (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                        $objects6=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                        $cu_flag=1;
                                    }

									if($j==count($folderArray[1])-1)
									{
										$object_id=$row1[0];
										$object="search".$folderid."_".$advance_search.">".$this->getfoldernamenew($folderid)."_".$row1[0];
											
											
										for($i=0;$i<8;$i++)
										{
											if ($i==0) {
                                                $arry1.="{nesote_id}{nesote_,}";
                                            } elseif ($i==5) {
                                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                            } elseif ($i==6) {
                                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                            } else {
                                                $arry1.=$row1[$i]."{nesote_,}";
                                            }

										}
										$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
										$arry1.=$this->gettime($row1[9])."{nesote_,}";
										$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
										$arry1.=$row1[9]."{nesote_,}";
										$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
										$arry1.=count($folderArray[1])."{nesote_,}";
										//	$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
										$arry1.="adsearch{nesote}{nesote_,}";
										$arry1.="adsearch_objects";
											
										for($i=0;$i<(9);$i++)
										{
											if($i==6)
											{
												$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
												$externals=explode("{nesote_comma}",(string) $external);
												$arry.=$externals[0]."{nesote_,}";
												$extnl_flg=$externals[1];
											}
											else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }

										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
										$arry.="1{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										//	$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
										$arry.="adsearch{nesote}{nesote_,}";
										$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
										$arry.="adsearch_objects";
											
											
											
									}
									else
									{
											
											
										for($i=0;$i<(9);$i++)
										{
											if ($i==5) {
                                                $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                            } elseif ($i==6) {
                                                $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                            } else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
										$arry.="adsearch{nesote}{nesote_,}";
										$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
										$arry.="adsearch_objects";

									}
									$arry.="{nesote_ref}";

								}
								if ($in_flag==1) {
                                    $object_search.=$objects1.".";
                                }
								if ($se_flag==1) {
                                    $object_search.=$objects2.".";
                                }
								if ($dr_flag==1) {
                                    $object_search.=$objects3.".";
                                }
								if ($sp_flag==1) {
                                    $object_search.=$objects4.".";
                                }
								if ($tr_flag==1) {
                                    $object_search.=$objects5.".";
                                }
								if ($cu_flag==1) {
                                    $object_search.=$objects6.".";
                                }
								$arry=str_replace("adsearch_objects",$object_search,$arry);
								$arry1=str_replace("adsearch_objects",$object_search,$arry1);
								$arry1=str_replace("{nesote_id}",$object_id,$arry1);
								$arry=str_replace("adsearch{nesote}",$object,$arry);
								$arry1=str_replace("adsearch{nesote}",$object,$arry1);
								$arry=substr($arry,0,-12);  //echo "(((".$arry.")))";
								$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
								$var++;

									
							}
				 	}
				 }
				 else
				 {

				 	if ($folderid==1) {
                         $select->select("nesote_email_inbox_$tablenumber");
                     } elseif ($folderid==2) {
                         $select->select("nesote_email_draft_$tablenumber");
                     } elseif ($folderid==3) {
                         $select->select("nesote_email_sent_$tablenumber");
                     } elseif ($folderid==4) {
                         $select->select("nesote_email_spam_$tablenumber");
                     } elseif ($folderid==5) {
                         $select->select("nesote_email_trash_$tablenumber");
                     }
				 	$select->fields("mail_references");
				 	$select->where("userid=? $search",[$id]);
				 	$select->group("mail_references");
				 	$select->order("time desc");
				 	$select->limit($strt,$size);
				 	$result=$select->query();
				 		
				 		
				 		
				 	$var=0;$returnstr="";
				 	while($row=$select->fetchRow($result))
				 	{
				 		$arry="";$arry1="";$in_flag=0;$se_flag=0;$dr_flag=0;$sp_flag=0;$tr_flag=0;$object_search="";
							preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
							preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
							for($j=(count($folderArray[1])-1);$j>=0;$j--)
							{

								if ($folderArray[1][$j]==1) {
                                    $select->select("nesote_email_inbox_$tablenumber");
                                } elseif ($folderArray[1][$j]==2) {
                                    $select->select("nesote_email_draft_$tablenumber");
                                } elseif ($folderArray[1][$j]==3) {
                                    $select->select("nesote_email_sent_$tablenumber");
                                } elseif ($folderArray[1][$j]==4) {
                                    $select->select("nesote_email_spam_$tablenumber");
                                } elseif ($folderArray[1][$j]==5) {
                                    $select->select("nesote_email_trash_$tablenumber");
                                }
								$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
								$select->where("id=?",[$mailidArray[1][$j]]);
								$result1=$select->query();
								$row1=$select->fetchRow($result1);
									
								if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                                    $objects1="inbox_".$row1[0];
                                    $in_flag=1;
                                } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                    $objects2="sent_".$row1[0];
                                    $se_flag=1;
                                } elseif (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                    $objects3=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                    $cu_flag=1;
                                } elseif (($dr_flag==0)&&($folderArray[1][$j]==2)) {
                                    $objects6="draft_".$row1[0];
                                    $dr_flag=1;
                                } elseif (($sp_flag==0)&&($folderArray[1][$j])==4) {
                                    $objects4="spam_".$row1[0];
                                    $sp_flag=1;
                                } elseif (($tr_flag==0)&&($folderArray[1][$j]==5)) {
                                    $objects5="trash_".$row1[0];
                                    $tr_flag=1;
                                }
									
								if($j==count($folderArray[1])-1)
								{   $object_id=$row1[0];
								$object="search".$folderid."_".$advance_search.">".$this->getfoldernamenew($folderid)."_".$row1[0];
									
								for($i=0;$i<8;$i++)
								{   if ($i==0) {
                                    $arry1.="{nesote_id}{nesote_,}";
                                } elseif ($i==5) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
								}
								$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
								$arry1.=$this->gettime($row1[9])."{nesote_,}";
								$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
								$arry1.=$row1[9]."{nesote_,}";
								$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry1.=count($folderArray[1])."{nesote_,}";
								//	$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry1.="adsearch{nesote}{nesote_,}";
								$arry1.="adsearch_objects";
									

								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="1{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="adsearch{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="adsearch_objects";

								}
								else
								{


									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									//	$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
									$arry.="adsearch{nesote}{nesote_,}";
									$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
									$arry.="adsearch_objects";

								}
								$arry.="{nesote_ref}";

							}
							if ($in_flag==1) {
                                $object_search.=$objects1.".";
                            }
							if ($se_flag==1) {
                                $object_search.=$objects2.".";
                            }
							if ($cu_flag==1) {
                                $object_search.=$objects3.".";
                            }
							if ($dr_flag==1) {
                                $object_search.=$objects6.".";
                            }
							if ($sp_flag==1) {
                                $object_search.=$objects4.".";
                            }
							if ($tr_flag==1) {
                                $object_search.=$objects5.".";
                            }
							$arry=str_replace("adsearch_objects",$object_search,$arry);
							$arry1=str_replace("adsearch_objects",$object_search,$arry1);
							$arry1=str_replace("{nesote_id}",$object_id,$arry1);
							$arry=str_replace("adsearch{nesote}",$object,$arry);
							$arry1=str_replace("adsearch{nesote}",$object,$arry1);
							$arry=substr($arry,0,-12);
							$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
							$var++;
							//echo $returnstr."*************";
				 	}
				 }
                    $returnstr=substr($returnstr,0,-18);
                    //echo $returnstr."*************";
                    $returnstr=$returnstr."{nesote_count}".$var;
                    echo $returnstr;
                    exit;
                } elseif ($search1_attach==1) {
                    $db->select("nesote_email_attachments_$tablenumber");
                    $db->fields("distinct mailid,folderid");
                    $db->where("attachment=? and userid=? ",[1,$id]);
                    $atres=$db->query();
                    while($atresult=$db->fetchRow($atres))
						{

							$no=$select->total("nesote_email_customfolder","id=? and userid=?",[$folderid,$userid]);
							if($no==1)
							{
								$select->select("nesote_email_customfolder_mapping_$tablenumber");
								$select->fields("mail_references");
								$select->where("folderid=? and id=? $search",[$folderid,$atresult[0]]);
								$select->group("mail_references");
								$select->order("time desc");
								$select->limit($strt,$size);
								$result=$select->query();
								$var=0;$returnstr="";
									
								while($row=$select->fetchRow($result))
								{
									$arry="";$arry1="";$cu_flag=0;$se_flag=0;$object_search="";
									preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
									preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
									for($j=(count($folderArray[1])-1);$j>=0;$j--)
									{

										if ($folderArray[1][$j]==3) {
                                        $select1->select("nesote_email_sent_$tablenumber");
                                    } else {
                                        $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                                    }
										$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
										$select1->where("id=?",[$mailidArray[1][$j]]);
										$result1=$select1->query();
										$row1=$select1->fetchRow($result1);

										if (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                        $object_id=$row1[0];
                                        $objects1=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                        $cu_flag=1;
                                    } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                        $objects2="sent_".$row1[0];
                                        $se_flag=1;
                                    }

										if($j==count($folderArray[1])-1)
										{  // $object_id=$row1[0];
											$object="search".$folderid."_".$advance_search.">".$this->getfoldernamenew($folderid)."_{objectid}";


											for($i=0;$i<8;$i++)
											{
												if ($i==0) {
                                                $arry1.="{nesote_id}{nesote_,}";
                                            } elseif ($i==5) {
                                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                            } elseif ($i==6) {
                                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                            } else {
                                                $arry1.=$row1[$i]."{nesote_,}";
                                            }
											}
											$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
											$arry1.=$this->gettime($row1[9])."{nesote_,}";
											$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
											$arry1.=$row1[9]."{nesote_,}";
											$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
											$arry1.=count($folderArray[1])."{nesote_,}";
											//	$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
											$arry1.="adsearch{nesote}{nesote_,}";
											$arry1.="adsearch_objects";


											for($i=0;$i<(9);$i++)
											{
												if($i==6)
												{
													$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
													$externals=explode("{nesote_comma}",(string) $external);
													$arry.=$externals[0]."{nesote_,}";
													$extnl_flg=$externals[1];
												}
												else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
											}

											$arry.=$this->gettime($row1[9])."{nesote_,}";
											$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
											$arry.="1{nesote_,}";
											$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
											$arry.=$row1[6]."{nesote_,}";
											$arry.=$extnl_flg."{nesote_,}";
											$arry.=$row1[9]."{nesote_,}";
											$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
											//	$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
											$arry.="adsearch{nesote}{nesote_,}";
											$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
											$arry.="adsearch_objects";

										}
										else
										{


											for($i=0;$i<(9);$i++)
											{
												if ($i==5) {
                                                $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                            } elseif ($i==6) {
                                                $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                            } else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
											}

											$arry.=$this->gettime($row1[9])."{nesote_,}";
											$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
											$arry.="0{nesote_,}";
											$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
											$arry.=$row1[6]."{nesote_,}";
											$arry.=$extnl_flg."{nesote_,}";
											$arry.=$row1[9]."{nesote_,}";
											$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
											//	$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
											$arry.="adsearch{nesote}{nesote_,}";
											$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
											$arry.="adsearch_objects";
										}
										$arry.="{nesote_ref}";
											
									}
									if ($cu_flag==1) {
                                    $object_search.=$objects1.".";
                                }
									if ($se_flag==1) {
                                    $object_search.=$objects2.".";
                                }
									$arry=str_replace("adsearch_objects",$object_search,$arry);
									$arry1=str_replace("adsearch_objects",$object_search,$arry1);
									$arry1=str_replace("{nesote_id}",$object_id,$arry1);
									$object=str_replace("{objectid}",$object_id,$object);
									$arry=str_replace("adsearch{nesote}",$object,$arry);
									$arry1=str_replace("adsearch{nesote}",$object,$arry1);
									$arry=substr($arry,0,-12);
									$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
									$var++;
									//echo $returnstr."*************";
								}
								$returnstr=substr($returnstr,0,-18);
								$returnstr=$returnstr."{nesote_count}".$var;echo $returnstr;exit;
							}
						}
                } else
					{
							

						//						$db->select("nesote_email_customfolder");
						//						$db->fields("id");
						//						$db->where("id=? and userid=? ",array($folderid,$userid));
						//						$rslt=$db->query();
						//						$no=$db->numRows($rslt);
						$no=$db->total("nesote_email_customfolder","id=? and userid=? ",[$folderid,$userid]);
						if($no==1)
						{
							$select->select("nesote_email_customfolder_mapping_$tablenumber");
							$select->fields("mail_references");
							$select->where("folderid=? $search",[$folderid]);
							$select->group("mail_references");
							$select->order("time desc");
							$select->limit($strt,$size);
							$result=$select->query();
							$var=0;$returnstr="";

							while($row=$select->fetchRow($result))
							{
								$arry="";$arry1="";$cu_flag=0;$se_flag=0;$object_search="";
								preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
								preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
								for($j=(count($folderArray[1])-1);$j>=0;$j--)
								{

									if ($folderArray[1][$j]==3) {
                                    $select1->select("nesote_email_sent_$tablenumber");
                                } else {
                                    $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                                }
									$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
									$select1->where("id=?",[$mailidArray[1][$j]]);
									$result1=$select1->query();
									$row1=$select1->fetchRow($result1);

									if (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                    $object_id=$row1[0];
                                    $objects1=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                    $cu_flag=1;
                                } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                    $objects2="sent_".$row1[0];
                                    $se_flag=1;
                                }

									if($j==count($folderArray[1])-1)
									{
										$object="search".$folderid."_".$advance_search.">".$this->getfoldernamenew($folderid)."_{objectid}";

										for($i=0;$i<8;$i++)
										{
											if ($i==0) {
                                            $arry1.="{nesote_id}{nesote_,}";
                                        } elseif ($i==5) {
                                            $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry1.=$row1[$i]."{nesote_,}";
                                        }
										}
										$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
										$arry1.=$this->gettime($row1[9])."{nesote_,}";
										$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
										$arry1.=$row1[9]."{nesote_,}";
										$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
										$arry1.=count($folderArray[1])."{nesote_,}";
										//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
										$arry1.="adsearch{nesote}{nesote_,}";
										$arry1.="adsearch_objects";

										for($i=0;$i<(9);$i++)
										{
											if($i==6)
											{
												$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
												$externals=explode("{nesote_comma}",(string) $external);
												$arry.=$externals[0]."{nesote_,}";
												$extnl_flg=$externals[1];
											}
											else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
										$arry.="1{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										//	$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
										$arry.="adsearch{nesote}{nesote_,}";
										$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
										$arry.="adsearch_objects";


									}
									else
									{


										for($i=0;$i<(9);$i++)
										{
											if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
										$arry.="adsearch{nesote}{nesote_,}";
										$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
										$arry.="adsearch_objects";

									}
									$arry.="{nesote_ref}";

								}
								if ($cu_flag==1) {
                                $object_search.=$objects1.".";
                            }
								if ($se_flag==1) {
                                $object_search.=$objects2.".";
                            }
								$arry=str_replace("adsearch_objects",$object_search,$arry);
								$arry1=str_replace("adsearch_objects",$object_search,$arry1);
								$arry1=str_replace("{nesote_id}",$object_id,$arry1);
								$object=str_replace("{objectid}",$object_id,$object);
								$arry=str_replace("adsearch{nesote}",$object,$arry);
								$arry1=str_replace("adsearch{nesote}",$object,$arry1);
								$arry=substr($arry,0,-12);
								$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
								$var++;
								//echo $returnstr."*************";
							}
							$returnstr=substr($returnstr,0,-18);
							$returnstr=$returnstr."{nesote_count}".$var;echo $returnstr;exit;
						}
					}
			}
			$select=new NesoteDALController();
			$select1=new NesoteDALController();
			if ($name=="inbox") {
                //include("config/database.default.config.php");
                require(__DIR__ . "/script.inc.php");
                include($config_path."database.default.config.php");
                //echo time()."//";
                //$query_string="select mail_references,md5_references from (SELECT mail_references,md5_references,time FROM `".$db_tableprefix."nesote_email_inbox_$tablenumber` where userid=".$id."  ORDER BY time DESC) x  group by md5_references order by time desc limit ".$strt.",".$size;
                $query_string="SELECT mail_references,md5_references,time FROM `".$db_tableprefix."nesote_email_inbox_$tablenumber` where userid=".$id."    group by md5_references order by time desc limit ".$strt.",".$size;
                //	echo $query_string;
                $select->setSelectsql($query_string);
                //$select->fields("mail_references");
                //$select->where("userid=?",array($id));
                //$select->group("mail_references");
                //$select->order("time desc");
                //$select->limit($strt,$size);
                $result=$select->query();
                //echo time();
                //echo $select->getQuery();
                $var=0;
                $returnstr="";
                while($row=$select->fetchRow($result))
				{ $flag=0;	$unread_flag=1;$in_flag=0;$se_flag=0;$all_objects="";
				$arry="";$arry1="";
				preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
				preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);//print_r($folderArray);
				for($j=(count($folderArray[1])-1);$j>=0;$j--)
				{

					if ($folderArray[1][$j]==1) {
                        $select1->select("nesote_email_inbox_$tablenumber");
                    } else {
                        $select1->select("nesote_email_sent_$tablenumber");
                    }
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
					$select1->where("id=?",[$mailidArray[1][$j]]);
					$result1=$select1->query();

					if($row1=$select1->fetchRow($result1))
					{     //echo $row1[0]."<br>";
						if(($flag==0)&&(($folderArray[1][$j]==1)))
						{

							$time=$row1[9];

							$object_id=$row1[0];$flag=1;
						}


						if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                            $objects1="inbox_".$row1[0];
                            $in_flag=1;
                        } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                            $objects2="sent_".$row1[0];
                            $se_flag=1;
                        }

							
							
						if($j==count($folderArray[1])-1)
						{

							for($i=0;$i<8;$i++)
							{
								if ($i==0) {
                                    $arry1.="{nesote_id}{nesote_,}";
                                } elseif ($i==5) {
                                    $arry1.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    $arry1.="unread_{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
							}
							$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
							$arry1.="{nesote_time}{nesote_,}";
							//$arry1.=$this->gettime($row1[9])."{nesote_,}";
							$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
							$arry1.=$row1[9]."{nesote_,}";
							$arry1.="inbox{nesote_,}";
							$arry1.=count($folderArray[1])."{nesote_,}";
							$arry1.="inbox_{nesote}{nesote_,}";
							$arry1.="allobjects";




							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									//$arry.="{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="1{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="inbox_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

							$var++;

						}

						else
						{


							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    //echo $row1[$i]
                                    $arry.=$row1[$i]."{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="inbox_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
					}
					$arry.="{nesote_ref}";




				}
				if ($in_flag==1) {
                    $all_objects.=$objects1.".";
                }
				if ($se_flag==1) {
                    $all_objects.=$objects2.".";
                }

				$arry=str_replace("allobjects",$all_objects,$arry);
				$arry1=str_replace("allobjects",$all_objects,$arry1);
				$arry1=str_replace("{nesote_id}",$object_id,$arry1);
					
				$arry=str_replace("inbox_{nesote}","inbox_".$object_id,$arry);
				$arry1=str_replace("unread_{nesote_,}",$unread_flag."{nesote_,}",$arry1);
				$arry1=str_replace("inbox_{nesote}","inbox_".$object_id,$arry1);
				$time=$this->gettime($time);
				$arry1=str_replace("{nesote_time}",$time,$arry1);
				$arry=substr($arry,0,-12);
				$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";

				//echo $returnstr."*************";
				}
                //echo $arry1;exit;
                $returnstr=substr($returnstr,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="draft") {
                $select->select("nesote_email_draft_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $select->where("userid=? and just_insert=?",[$id,0]);
                $select->order("time desc");
                $select->limit($strt,$size);
                $result=$select->query();
                $var=0;
                $returnstr="";
                while($row=$select->fetchRow($result))
				{
					$arry="";$arry1="";

					$select1->select("nesote_email_draft_$tablenumber");
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
					$select1->where("id=?",[$row[0]]);
					$result1=$select1->query();
					$row1=$select1->fetchRow($result1);
					for($i=0;$i<8;$i++)
					{
						if ($i==5) {
                            $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                        } elseif ($i==6) {
                            $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                        } else {
                            $arry1.=$row1[$i]."{nesote_,}";
                        }
					}
					$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
					$arry1.=$this->gettime($row1[9])."{nesote_,}";
					$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
					$arry1.=$row1[9]."{nesote_,}";
					$arry1.="draft{nesote_,}";
					$arry1.="1{nesote_,}";
					$arry1.="draft_".$row1[0]."{nesote_,}";
					$arry1.="draft_".$row1[0].".";


					for($i=0;$i<(9);$i++)
					{
						$arry.=$row1[$i]."{nesote_,}";
					}

					$arry.=$this->gettime($row1[9])."{nesote_,}";
					$arry.=$this->getattachmentIcon($row1[0],2)."{nesote_,}";
					$arry.="1{nesote_,}";
					$arry.="draft{nesote_,}";
					$arry.="{nesote_,}";
					//$arry.=$row1[6]."{nesote_,}";
					$arry.=$extnl_flg."{nesote_,}";
					$arry.=$row1[9]."{nesote_,}";
					$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
					$arry.="draft_".$row1[0]."{nesote_,}";
					$arry.="draft_".$row1[0].".";

					$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";

					$var++;
				}
                $returnstr=substr($returnstr,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="sent") {
                require(__DIR__ . "/script.inc.php");
                include($config_path."database.default.config.php");
                $query_string="SELECT mail_references,md5_references,time FROM `".$db_tableprefix."nesote_email_sent_$tablenumber` where userid=".$id."    group by md5_references order by time desc limit ".$strt.",".$size;
                //$query_string="select mail_references,md5_references  from (SELECT mail_references,md5_references FROM `".$db_tableprefix."nesote_email_sent_".$tablenumber."` where userid=".$id." ORDER BY time DESC) x group by md5_references limit ".$strt.",".$size;
                //$query_string="select mail_references,md5_references  from ((SELECT mail_references,time,userid,md5_references FROM `".$db_tableprefix."nesote_email_sent_$tablenumber`  ) UNION (SELECT a.mail_references,a.time,a.userid,a.md5_references FROM `".$db_tableprefix."nesote_email_inbox_$tablenumber` a, `".$db_tableprefix."nesote_email_sent_$tablenumber` b where a.md5_references=b.md5_references group by a.id) ORDER BY time DESC) x where userid=".$id." group by md5_references order by time desc limit ".$strt.",".$size;
                //print $query_string;
                //echo $query_string;
                $select->setSelectsql($query_string);
                //$select->setSql(array("x"=>$query_string));
                //$select->fields("mail_references");
                //$select->where("userid=?",array($id));
                //$select->group("mail_references");
                //$select->order("time desc");
                //	$select->limit($strt,$size);
                $result=$select->query();
                //$select->select("nesote_email_sent");
                //$select->fields("mail_references");
                //$select->where("userid=?",array($id));
                //$select->group("mail_references");
                //$select->order("time desc");
                ///$select->limit($strt,$size);
                //$result=$select->query();
                $var=0;
                $returnstr="";
                while($row=$select->fetchRow($result))
				{$flag=0;$in_flag=0;$se_flag=0;$cu_flag=0;$all_objects="";
				$arry="";$arry1="";
				preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
				preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);//print_r($mailidArray);
				for($j=(count($folderArray[1])-1);$j>=0;$j--)
				{

					//$select1=new NesoteDALController();
					if ($folderArray[1][$j]==1) {
                        $select1->select("nesote_email_inbox_$tablenumber");
                    } elseif ($folderArray[1][$j]==3) {
                        $select1->select("nesote_email_sent_$tablenumber");
                    } else {
                        $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                    }
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
					$select1->where("id=?",[$mailidArray[1][$j]]);
					$result1=$select1->query();//echo $select1->getQuery();
					if($row1=$select1->fetchRow($result1))
					{
						if(($flag==0)&&(($folderArray[1][$j]==3)))
						{
							$time=$row1[9];$object_id=$row1[0];
							$flag=1;
						}
							
							
						if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                            $objects1="inbox_".$row1[0];
                            $in_flag=1;
                        } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                            $objects2="sent_".$row1[0];
                            $se_flag=1;
                        } elseif (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                            $objects3=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                            $cu_flag=1;
                        }
							
						if($j==count($folderArray[1])-1)
						{
							for($i=0;$i<8;$i++)
							{

								if ($i==0) {
                                    $arry1.="{nesote_id}{nesote_,}";
                                } elseif ($i==5) {
                                    $arry1.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
							}
							$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
							$arry1.="{nesote_time}{nesote_,}";
							//$arry1.=$this->gettime($row1[9])."{nesote_,}";
							$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
							$arry1.=$row1[9]."{nesote_,}";
							$arry1.="sent{nesote_,}";
							$arry1.=count($folderArray[1])."{nesote_,}";
							$arry1.="sent_{nesote}{nesote_,}";
							$arry1.="allobjects";


							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="1{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							$arry.="{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="sent_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";
							$var++;
						}

						else
						{
							
							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							$arry.="{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="sent_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
					}
					$arry.="{nesote_ref}";

				}
				if ($in_flag==1) {
                    $all_objects.=$objects1.".";
                }
				if ($se_flag==1) {
                    $all_objects.=$objects2.".";
                }
				if ($cu_flag==1) {
                    $all_objects.=$objects3.".";
                }
				$arry=str_replace("allobjects",$all_objects,$arry);
				$arry1=str_replace("allobjects",$all_objects,$arry1);
				$arry1=str_replace("{nesote_id}",$object_id,$arry1);
				$arry=str_replace("sent_{nesote}","sent_".$object_id,$arry);
				$arry1=str_replace("sent_{nesote}","sent_".$object_id,$arry1);
				$time=$this->gettime($time);
				$arry1=str_replace("{nesote_time}",$time,$arry1);
				//echo $arry."______________________".$arry1."|||||||||||||||||";
				$arry=substr($arry,0,-12);
				$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";

				//echo $returnstr."*************";
				}
                $returnstr=substr($returnstr,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="spam") {
                $select->select("nesote_email_spam_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=?",[$id]);
                $select->group("md5_references");
                $select->order("time desc");
                $select->limit($strt,$size);
                $result=$select->query();
                $var=0;
                $returnstr="";
                while($row=$select->fetchRow($result))
				{
					$arry="";$arry1="";
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);//print_r($mailidArray);
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{

						$select1->select("nesote_email_spam_$tablenumber");
						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
						$select1->where("id=?",[$mailidArray[1][$j]]);
						$result1=$select1->query();//echo $select1->getQuery();
						if($row1=$select1->fetchRow($result1))
						{
							//							if($j==count($folderArray[1])-1)
							//						    {
							//						    	$object_id=$row1[0];
							//						    }


							if($j==count($folderArray[1])-1)
							{
								$object_id=$row1[0];

								for($i=0;$i<8;$i++)
								{
									if ($i==5) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry1.=$row1[$i]."{nesote_,}";
                                    }
								}
								$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
								$arry1.=$this->gettime($row1[9])."{nesote_,}";
								$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
								$arry1.=$row1[9]."{nesote_,}";
								$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry1.=count($folderArray[1])."{nesote_,}";
								//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry1.="spam_{nesote}{nesote_,}";
								$arry1.='spam_{nesote}.';
								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.="{nesote_,}";
										//$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="1{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								//$arry.=$row1[6]."{nesote_,}";
								$arry.="{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="spam_{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.='spam_{nesote}.';
								$var++;
									
							}
							else
							{
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.="{nesote_,}";
								//$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="spam_{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.='spam_{nesote}.';

							}
						}
						$arry.="{nesote_ref}";

					}
					$arry=str_replace("spam_{nesote}","spam_".$object_id,$arry);
					$arry1=str_replace("spam_{nesote}","spam_".$object_id,$arry1);
					$arry=substr($arry,0,-12);
					$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";

					//echo $returnstr."*************";
				}
                $returnstr=substr($returnstr,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="trash") {
                $select->select("nesote_email_trash_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=?",[$id]);
                $select->group("mail_references");
                $select->order("time desc");
                $select->limit($strt,$size);
                $result=$select->query();
                $var=0;
                $returnstr="";
                while($row=$select->fetchRow($result))
				{
					$arry="";$arry1="";
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
						$select1->select("nesote_email_trash_$tablenumber");
						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
						$select1->where("id=?",[$mailidArray[1][$j]]);
						$result1=$select1->query();
						if($row1=$select1->fetchRow($result1))
						{
						 if($j==count($folderArray[1])-1)
						 { $object_id=$row1[0];
							//$select1=new NesoteDALController();

						 for($i=0;$i<8;$i++)
						 {
						 	//									if($i==1)
						 	//									{
						 	//										$arry1.="{nesote_id}{nesote_,}";
						 	//									}
						 	if ($i==5) {
                                 $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                             } elseif ($i==6) {
                                 $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                             } else {
                                 $arry1.=$row1[$i]."{nesote_,}";
                             }
						 }
						 $arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
						 $arry1.=$this->gettime($row1[9])."{nesote_,}";
						 $arry1.=$this->getattachcount($row1[12])."{nesote_,}";
						 $arry1.=$row1[9]."{nesote_,}";
						 $arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
						 $arry1.=count($folderArray[1])."{nesote_,}";
						 //$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
						 $arry1.="trash_{nesote}{nesote_,}";
						 $arry1.='trash_{nesote}.';



						 for($i=0;$i<(9);$i++)
						 {
						 	if($i==6)
						 	{
						 		$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
						 		$externals=explode("{nesote_comma}",(string) $external);
						 		$arry.=$externals[0]."{nesote_,}";
						 		$extnl_flg=$externals[1];
						 	}
						 	else {
                                 $arry.=$row1[$i]."{nesote_,}";
                             }
						 }

						 $arry.=$this->gettime($row1[9])."{nesote_,}";
						 $arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
						 $arry.="1{nesote_,}";
						 $arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
						 $arry.=$row1[6]."{nesote_,}";
						 $arry.=$extnl_flg."{nesote_,}";
						 $arry.=$row1[9]."{nesote_,}";
						 $arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
						 //$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
						 $arry.="trash_{nesote}{nesote_,}";
						 $arry.=$this->gettimeinside($row1[9])."{nesote_,}";
						 $arry.='trash_{nesote}.';
						 $var++;

						 }
						 else
						 {
						 		
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="trash_{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="trash_{nesote}";

						 }
						}
						$arry.="{nesote_ref}";

					}
					$arry=str_replace("trash_{nesote}","trash_".$object_id,$arry);
					$arry1=str_replace("trash_{nesote}","trash_".$object_id,$arry1);
					$arry=substr($arry,0,-12);
					$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";

					//echo $returnstr."*************";
				}
                $returnstr=substr($returnstr,0,-18);
                $returnstr=$returnstr."{nesote_count}".$var;
                echo $returnstr;
                exit;
            } elseif ($name=="starred") {
                $returnstr="";
                $select->select("nesote_email_inbox_$tablenumber");
                $select->fields("mail_references,time");
                $select->where("userid=? and starflag=?",[$id,1]);
                $select->group("md5_references");
                $select->order("time desc");
                $result=$select->query();
                $var=0;
                while($row=$select->fetchRow($result))
				{
					$refArry[$var][0]=$row[0];
					$refArry[$var][1]=$row[1];
					$var++;
				}
                $select->select("nesote_email_sent_$tablenumber");
                $select->fields("mail_references,time");
                $select->where("userid=? and starflag=?",[$id,1]);
                $select->group("md5_references");
                $select->order("time desc");
                $result=$select->query();
                while($row=$select->fetchRow($result))
				{
					$refArry[$var][0]=$row[0];
					$refArry[$var][1]=$row[1];
					$var++;
				}
                $select->select("nesote_email_draft_$tablenumber");
                $select->fields("mail_references,time");
                $select->where("userid=? and just_insert=? and starflag=?",[$id,0,1]);
                $select->order("time desc");
                $result=$select->query();
                while($row=$select->fetchRow($result))
				{
					$refArry[$var][0]=$row[0];
					$refArry[$var][1]=$row[1];
					$var++;
				}
                $userid=$this->getId();
                $folderid=substr((string) $name,6);
                //$db=new NesoteDALController();
                $select1->select("nesote_email_customfolder");
                $select1->fields("id");
                $select1->where("userid=? ",[$userid]);
                $rslt=$select1->query();
                $no=$select1->numRows($rslt);
                while($rows=$select1->fetchRow($rslt))
				{
					$select->select("nesote_email_customfolder_mapping_$tablenumber");
					$select->fields("mail_references,time");
					$select->where("folderid=? and starflag=?",[$rows[0],1]);
					$select->group("md5_references");
					$select->order("time desc");
					$result=$select->query();
					while($row=$select->fetchRow($result))
					{
						$refArry[$var][0]=$row[0];
						$refArry[$var][1]=$row[1];
						$var++;
					}

				}
                $var1=1;
                $refrArray[0][0]=$refArry[0][0];
                $refrArray[0][1]=$refArry[0][1];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];
						$refrArray[$var1][1]=$refArry[$i][1];
						$var1++;
					}
				}
                for($i=0;$i<$var1;$i++)
				{
					for($j=$i+1;$j<$var;$j++)
					{
						if($refrArray[$j][1]>$refrArray[$i][1])
						{
							$temp[0]=$refrArray[$j][0];
							$temp[1]=$refrArray[$j][1];
							$refrArray[$j][0]=$refrArray[$i][0];
							$refrArray[$j][1]=$refrArray[$i][1];
							$refrArray[$i][0]=$temp[0];
							$refrArray[$i][1]=$temp[1];
						}
					}
				}
                //print_r($refrArray);
                $end=$strt+$size;
                if ($var1<$end) {
                    $end=$var1;
                }
                $str_count=0;
                for($z=$strt;$z<$end;$z++)
				{
					$arry="";$arry1="";$in_flag=0;$se_flag=0;$cu_flag=0;$dr_flag=0;$all_objects="";$str_count++;
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $refrArray[$z][0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $refrArray[$z][0],$mailidArray);
					for($j=count($folderArray[1])-1;$j>=0;$j--)
					{


						if ($folderArray[1][$j]==1) {
                            $select1->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderArray[1][$j]==2) {
                            $select1->select("nesote_email_draft_$tablenumber");
                        } elseif ($folderArray[1][$j]==3) {
                            $select1->select("nesote_email_sent_$tablenumber");
                        } else {
                            $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
						$select1->where("id=?",[$mailidArray[1][$j]]);
						$result1=$select1->query();//echo $select1->getQuery();
						$row1=$select1->fetchRow($result1);

						if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                            $objects1="inbox_".$row1[0];
                            $in_flag=1;
                        } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                            $objects2="sent_".$row1[0];
                            $se_flag=1;
                        } elseif (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                            $objects3=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                            $cu_flag=1;
                        } elseif (($dr_flag==0)&&($folderArray[1][$j]==2)) {
                            $objects4="draft_".$row1[0];
                            $dr_flag=1;
                        }

						if($j==count($folderArray[1])-1)
						{
							//$select1=new NesoteDALController();
							$object_id=$row1[0];
							$object=$this->getfoldernamenew($folderArray[1][$j])."_".$object_id;
							for($i=0;$i<8;$i++)
							{
								if ($i==5) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
							}
							$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
							$arry1.=$this->gettime($row1[9])."{nesote_,}";
							$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
							$arry1.=$row1[9]."{nesote_,}";
							$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							$arry1.=count($folderArray[1])."{nesote_,}";
							//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
							// $arry1.="starred:".$this->getfoldernamenew($folderArray[1][$j])."_{nesote}";
							$arry1.="starred:{nesote}{nesote_,}";
							$arry1.="allobjects";
							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="1{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
							//$arry.="starred:".$this->getfoldernamenew($folderArray[1][$j])."_{nesote}{nesote_,}";
							$arry.="starred:{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
						else
						{
							//$select1=new NesoteDALController();

							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
							//$arry.="starred:".$this->getfoldernamenew($folderArray[1][$j])."_{nesote}{nesote_,}";
							$arry.="starred:{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
						$arry.="{nesote_ref}";
					}

					if ($in_flag==1) {
                        $all_objects.=$objects1.".";
                    }
					if ($se_flag==1) {
                        $all_objects.=$objects2.".";
                    }
					if ($cu_flag==1) {
                        $all_objects.=$objects3.".";
                    }
					if ($dr_flag==1) {
                        $all_objects.=$objects4.".";
                    }
					$arry1=str_replace("allobjects",$all_objects,$arry1);
					$arry=str_replace("allobjects",$all_objects,$arry);
					$arry1=str_replace("{nesote}",$object,$arry1);
					$arry=str_replace("{nesote}",$object,$arry);
					$arry=substr($arry,0,-12);
					$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
				}
                $returnstr=substr($returnstr,0,-18);
                $returnstr=$returnstr."{nesote_count}".$str_count;
                echo $returnstr;
                exit;
            } elseif (str_starts_with((string) $name, "search")) {
                if($valid!=TRUE)
				{
					header("Location:".$this->url("index/index"));
					exit(0);
				}
				else
				{
					$namezz=substr((string) $name,6);
					$len=strpos($namezz,"_");
					$folderid=substr($namezz,0,$len);
					$keyword=substr($namezz,($len+1));
					$valid=$this->validateUser();
					$userid=$this->getId();
					$var=0;$arry="";
					if ($folderid==0) {
                        $returnstr="";
                        $select->select("nesote_email_inbox_$tablenumber");
                        $select->fields("mail_references,time");
                        $select->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$id]);
                        $select->group("md5_references");
                        $select->order("time desc");
                        $result=$select->query();
                        $var=0;
                        while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}
                        $select->select("nesote_email_sent_$tablenumber");
                        $select->fields("mail_references,time");
                        $select->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$id]);
                        $select->group("md5_references");
                        $select->order("time desc");
                        $result=$select->query();
                        while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}
                        $select->select("nesote_email_draft_$tablenumber");
                        $select->fields("mail_references,time");
                        $select->where("userid=? and just_insert=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$id,0]);
                        $select->order("time desc");
                        $result=$select->query();
                        while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}
                        $select->select("nesote_email_spam_$tablenumber");
                        $select->fields("mail_references,time");
                        $select->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$id]);
                        $select->group("md5_references");
                        $select->order("time desc");
                        $result=$select->query();
                        while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}
                        $select->select("nesote_email_trash_$tablenumber");
                        $select->fields("mail_references,time");
                        $select->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$id]);
                        $select->group("md5_references");
                        $select->order("time desc");
                        $result=$select->query();
                        while($row=$select->fetchRow($result))
						{
							$refArry[$var][0]=$row[0];
							$refArry[$var][1]=$row[1];
							$var++;
						}
                        $userid=$this->getId();
                        //$folderid=substr($name,6);
                        //$db=new NesoteDALController();
                        $select1->select("nesote_email_customfolder");
                        $select1->fields("id");
                        $select1->where("userid=? ",[$userid]);
                        $rslt=$select1->query();
                        $no=$select1->numRows($rslt);
                        while($rows=$select1->fetchRow($rslt))
						{
							$select->select("nesote_email_customfolder_mapping_$tablenumber");
							$select->fields("mail_references,time");
							$select->where("folderid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$rows[0]]);
							$select->group("md5_references");
							$select->order("time desc");
							$result=$select->query();
							while($row=$select->fetchRow($result))
							{
								$refArry[$var][0]=$row[0];
								$refArry[$var][1]=$row[1];
								$var++;
							}

						}
                        $var1=1;
                        $refrArray[0][0]=$refArry[0][0];
                        $refrArray[0][1]=$refArry[0][1];
                        for($i=1;$i<$var;$i++)
						{
							$check=0;
							for($j=0;$j<$var1;$j++)
							{
								if ($refrArray[$j][0]==$refArry[$i][0]) {
                                    $check=1;
                                }
							}
							if($check==0)
							{
								$refrArray[$var1][0]=$refArry[$i][0];
								$refrArray[$var1][1]=$refArry[$i][1];
								$var1++;
							}
						}
                        for($i=0;$i<$var1;$i++)
						{
							for($j=$i+1;$j<$var;$j++)
							{
								if($refrArray[$j][1]>$refrArray[$i][1])
								{
									$temp[0]=$refrArray[$j][0];
									$temp[1]=$refrArray[$j][1];
									$refrArray[$j][0]=$refrArray[$i][0];
									$refrArray[$j][1]=$refrArray[$i][1];
									$refrArray[$i][0]=$temp[0];
									$refrArray[$i][1]=$temp[1];
								}
							}
						}
                        $end=$strt+$size;
                        $count=0;
                        if ($var1<$end) {
                            $end=$var1;
                        }
                        for($z=$strt;$z<$end;$z++)
						{$in_flag=0;$se_flag=0;$cu_flag=0;$dr_flag=0;$sp_flag=0;$tr_flag=0;$object_search="";
						$arry="";$arry1="";
						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $refrArray[$z][0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $refrArray[$z][0],$mailidArray);
						for($j=count($folderArray[1])-1;$j>=0;$j--)
						{
							if ($folderArray[1][$j]==1) {
                                $select1->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==2) {
                                $select1->select("nesote_email_draft_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $select1->select("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$j]==4) {
                                $select1->select("nesote_email_spam_$tablenumber");
                            } elseif ($folderArray[1][$j]==5) {
                                $select1->select("nesote_email_trash_$tablenumber");
                            } else {
                                $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                            }
							$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();
							$row1=$select1->fetchRow($result1);
							if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                                $objects1="inbox_".$row1[0];
                                $in_flag=1;
                            } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                $objects2="sent_".$row1[0];
                                $se_flag=1;
                            } elseif (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                $objects3=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                $cu_flag=1;
                            } elseif (($dr_flag==0)&&($folderArray[1][$j]==2)) {
                                $objects6="draft_".$row1[0];
                                $dr_flag=1;
                            } elseif (($sp_flag==0)&&($folderArray[1][$j])==4) {
                                $objects4="spam_".$row1[0];
                                $sp_flag=1;
                            } elseif (($tr_flag==0)&&($folderArray[1][$j]==5)) {
                                $objects5="trash_".$row1[0];
                                $tr_flag=1;
                            }


							if($j==count($folderArray[1])-1)
							{
								$object_id=$row1[0];
								$object="search".$folderid."_".$keyword.">".$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
								//$select1=new NesoteDALController();

								for($i=0;$i<8;$i++)
								{
									if ($i==0) {
                                        $arry1.="{nesote_id}{nesote_,}";
                                    } elseif ($i==5) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry1.=$row1[$i]."{nesote_,}";
                                    }
								}
								$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
								$arry1.=$this->gettime($row1[9])."{nesote_,}";
								$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
								$arry1.=$row1[9]."{nesote_,}";
								$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry1.=count($folderArray[1])."{nesote_,}";
								//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry1.="search{nesote}{nesote_,}";
								$arry1.="search_object";

								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="1{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="search{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="search_object";
							}
							else
							{
								//$select1=new NesoteDALController();

								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="search{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="search_object";

							}
							$arry.="{nesote_ref}";
						}
						if ($in_flag==1) {
                            $object_search.=$objects1.".";
                        }
						if ($se_flag==1) {
                            $object_search.=$objects2.".";
                        }
						if ($cu_flag==1) {
                            $object_search.=$objects3.".";
                        }
						if ($dr_flag==1) {
                            $object_search.=$objects6.".";
                        }
						if ($sp_flag==1) {
                            $object_search.=$objects4.".";
                        }
						if ($tr_flag==1) {
                            $object_search.=$objects5.".";
                        }

						//$object_search=substr_replace($object_search,"",-1);//echo $object_search;
						$arry=str_replace("search_object",$object_search,$arry);
						$arry1=str_replace("search_object",$object_search,$arry1);
						$arry1=str_replace("{nesote_id}",$object_id,$arry1);
						$arry=str_replace("search{nesote}",$object,$arry);
						$arry1=str_replace("search{nesote}",$object,$arry1);
						$arry=substr($arry,0,-12);
						$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
						$count++;
						}
                        $returnstr=substr($returnstr,0,-18);
                        $returnstr=$returnstr."{nesote_count}".$count;
                        echo $returnstr;
                        exit;
                    } elseif ($folderid<10) {
                        if ($folderid==1) {
                            $select->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderid==2) {
                            $select->select("nesote_email_draft_$tablenumber");
                        } elseif ($folderid==3) {
                            $select->select("nesote_email_sent_$tablenumber");
                        } elseif ($folderid==4) {
                            $select->select("nesote_email_spam_$tablenumber");
                        } elseif ($folderid==5) {
                            $select->select("nesote_email_trash_$tablenumber");
                        }
                        $select->fields("mail_references");
                        $select->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$id]);
                        $select->group("md5_references");
                        $select->order("time desc");
                        $select->limit($strt,$size);
                        $result=$select->query();
                        $var=0;
                        $returnstr="";
                        while($row=$select->fetchRow($result))
						{ $in_flag=0;$se_flag=0;$dr_flag=0;$sp_flag=0;$tr_flag=0;$cu_flag=0;$all_objects="";//$cu_flag=0;
						$arry="";$arry1="";
						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						for($j=(count($folderArray[1])-1);$j>=0;$j--)
						{

							if ($folderArray[1][$j]==1) {
                                $select1->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==2) {
                                $select1->select("nesote_email_draft_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $select1->select("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$j]==4) {
                                $select1->select("nesote_email_spam_$tablenumber");
                            } elseif ($folderArray[1][$j]==5) {
                                $select1->select("nesote_email_trash_$tablenumber");
                            } elseif ($folderArray[1][$j]>=10) {
                                $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                            }
							$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
							$select1->where("id=?",[$mailidArray[1][$j]]);
							$result1=$select1->query();
							$row1=$select1->fetchRow($result1);

							if (($in_flag==0)&&($folderArray[1][$j]==1)) {
                                $objects1="inbox_".$row1[0];
                                $in_flag=1;
                            } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                $objects2="sent_".$row1[0];
                                $se_flag=1;
                            } elseif (($dr_flag==0)&&($folderArray[1][$j]==2)) {
                                $objects3="draft_".$row1[0];
                                $dr_flag=1;
                            } elseif (($sp_flag==0)&&($folderArray[1][$j])==4) {
                                $objects4="spam_".$row1[0];
                                $sp_flag=1;
                            } elseif (($tr_flag==0)&&($folderArray[1][$j]==5)) {
                                $objects5="trash_".$row1[0];
                                $tr_flag=1;
                            } elseif (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                $objects6=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                $cu_flag=1;
                            }




							if($j==count($folderArray[1])-1)
							{
								$object_id=$row1[0];
								$object="search".$folderid."_".$keyword.">".$this->getfoldernamenew($folderid)."_".$row1[0];

								for($i=0;$i<8;$i++)
								{
									if ($i==0) {
                                        $arry1.="{nesote_id}{nesote_,}";
                                    } elseif ($i==5) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry1.=$row1[$i]."{nesote_,}";
                                    }
								}
								$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
								$arry1.=$this->gettime($row1[9])."{nesote_,}";
								$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
								$arry1.=$row1[9]."{nesote_,}";
								$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry1.=count($folderArray[1])."{nesote_,}";
								//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry1.="object{nesote}{nesote_,}";
								$arry1.="search_object";

								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="1{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="object{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="search_object";

							}
							else
							{

								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
								$arry.="object{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="search_object";

							}
							$arry.="{nesote_ref}";

						}
						if ($in_flag==1) {
                            $all_objects.=$objects1.".";
                        }
						if ($se_flag==1) {
                            $all_objects.=$objects2.".";
                        }
						if ($dr_flag==1) {
                            $all_objects.=$objects3.".";
                        }
						if ($sp_flag==1) {
                            $all_objects.=$objects4.".";
                        }
						if ($tr_flag==1) {
                            $all_objects.=$objects5.".";
                        }
						if ($cu_flag==1) {
                            $all_objects.=$objects6.".";
                        }
						$arry1=str_replace("{nesote_id}",$object_id,$arry1);
						$arry=str_replace("search_object",$all_objects,$arry);
						$arry1=str_replace("search_object",$all_objects,$arry1);
						$arry=str_replace("object{nesote}",$object,$arry);
						$arry1=str_replace("object{nesote}",$object,$arry1);
						$arry=substr($arry,0,-12);
						$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
						$var++;
						//echo $returnstr."*************";
						}
                        $returnstr=substr($returnstr,0,-18);
                        $returnstr=$returnstr."{nesote_count}".$var;
                        echo $returnstr;
                        exit;
                    } else
					{
						//						$db=new NesoteDALController();
						//						$db->select("nesote_email_customfolder");
						//						$db->fields("id");
						//						$db->where("id=? and userid=? ",array($folderid,$userid));
						//						$rslt=$db->query();
						//						$no=$db->numRows($rslt);
						$no=$select->total("nesote_email_customfolder","id=? and userid=?",[$folderid,$userid]);

						if($no==1)
						{
							$select->select("nesote_email_customfolder_mapping_$tablenumber");
							$select->fields("mail_references");
							$select->where("folderid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%')",[$folderid]);
							$select->group("md5_references");
							$select->order("time desc");
							$select->limit($strt,$size);
							$result=$select->query();
							$var=0;$returnstr="";
							while($row=$select->fetchRow($result))
							{ $cu_flag=0;$se_flag=0;$all_objects="";
							$arry="";$arry1="";
							preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
							preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
							for($j=(count($folderArray[1])-1);$j>=0;$j--)
							{


								if ($folderArray[1][$j]==3) {
                                    $select1->select("nesote_email_sent_$tablenumber");
                                } else {
                                    $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                                }
								$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
								$select1->where("id=?",[$mailidArray[1][$j]]);
								$result1=$select1->query();
								$row1=$select1->fetchRow($result1);


								if (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                    $object_id=$row1[0];
                                    $objects1=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                    $cu_flag=1;
                                } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                    $objects2="sent_".$row1[0];
                                    $se_flag=1;
                                }
								if($j==count($folderArray[1])-1)
								{

									$object="search".$folderid."_".$keyword.">".$this->getfoldernamenew($folderid)."_{objectid}";

									for($i=0;$i<8;$i++)
									{
										if ($i==0) {
                                            $arry1.="{nesote_id}{nesote_,}";
                                        } elseif ($i==5) {
                                            $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry1.=$row1[$i]."{nesote_,}";
                                        }
									}
									$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
									$arry1.=$this->gettime($row1[9])."{nesote_,}";
									$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
									$arry1.=$row1[9]."{nesote_,}";
									$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
									$arry1.=count($folderArray[1])."{nesote_,}";
									//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
									$arry1.="custom{nesote}{nesote_,}";
									$arry1.="search_objects";

									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
									$arry.="1{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
									$arry.="custom{nesote}{nesote_,}";
									$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
									$arry.="search_objects";
								}
								else
								{

									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
									$arry.="custom{nesote}{nesote_,}";
									$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
									$arry.="search_objects";

								}
								$arry.="{nesote_ref}";

							}
							if ($cu_flag==1) {
                                $all_objects.=$objects1.".";
                            }
							if ($se_flag==1) {
                                $all_objects.=$objects2.".";
                            }
							$arry1=str_replace("{nesote_id}",$object_id,$arry1);
							$arry=str_replace("search_objects",$all_objects,$arry);
							$arry1=str_replace("search_objects",$all_objects,$arry1);
							$object=str_replace("{objectid}",$object_id,$object);
							$arry=str_replace("custom{nesote}",$object,$arry);
							$arry1=str_replace("custom{nesote}",$object,$arry1);
							$arry=substr($arry,0,-12);
							$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
							$var++;
							//echo $returnstr."*************";
							}
							$returnstr=substr($returnstr,0,-18);
							$returnstr=$returnstr."{nesote_count}".$var;echo $returnstr;exit;
						}
					}
				}
            } else
			{
				$userid=$this->getId();
				$folderid=substr((string) $name,6);
				//				$db=new NesoteDALController();
				//				$db->select("nesote_email_customfolder");
				//				$db->fields("id");
				//				$db->where("id=? and userid=? ",array($folderid,$userid));
				//				$rslt=$db->query();
				//				$no=$db->numRows($rslt);
				$no=$select->total("nesote_email_customfolder","id=? and userid=? ",[$folderid,$userid]);

				if($no==1)
				{

					require(__DIR__ . "/script.inc.php");
					include($config_path."database.default.config.php");



					$query_string="SELECT mail_references,md5_references,time FROM `".$db_tableprefix."nesote_email_customfolder_mapping_$tablenumber` where folderid=".$folderid."    group by md5_references order by time desc limit ".$strt.",".$size;

					//"select mail_references,md5_references  from (SELECT mail_references,md5_references FROM `".$db_tableprefix."nesote_email_inbox_$tablenumber` where userid=".$id."  ORDER BY time DESC) x  group by md5_references  limit ".$strt.",".$size;
					//$query_string="select mail_references,md5_references  from (SELECT mail_references,time,folderid,md5_references FROM `".$db_tableprefix."nesote_email_customfolder_mapping_$tablenumber` where folderid=".$folderid."  ORDER BY time DESC) x group by md5_references desc limit ".$strt.",".$size;
					$select->setSelectsql($query_string);

					//$select->setSql(array("x"=>$query_string));
					//$select->fields("mail_references");
					//$select->where("folderid=?",array($folderid));
					//$select->group("mail_references");
					//$select->order("time desc");
					//$select->limit($strt,$size);
					$result=$select->query();//echo $select->getQuery();

					//$select->select("nesote_email_customfolder_mapping");
					//$select->fields("mail_references");
					//$select->where("folderid=? ",array($folderid));
					//$select->group("mail_references");
					//$select->order("time desc");
					//$select->limit($strt,$size);
					//$result=$select->query();
					$var=0;$returnstr="";
					while($row=$select->fetchRow($result))
					{ $flag=0;$unread_flag=1;$cu_flag=0;$se_flag=0;$all_objects="";
					$arry="";$arry1="";
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);//print_r($mailidArray);
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{

						//$select1=new NesoteDALController();
						if ($folderArray[1][$j]==3) {
                            $select1->select("nesote_email_sent_$tablenumber");
                        } else {
                            $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
						$select1->where("id=?",[$mailidArray[1][$j]]);
						$result1=$select1->query();//echo $select1->getQuery();
						if($row1=$select1->fetchRow($result1))
						{
							if(($flag==0)&&(($folderArray[1][$j]>=10)))
							{
								$time=$row1[9];
								$object_id=$row1[0];
								$folder=$this->getfoldernamenew($folderArray[1][$j]);
								$flag=1;
							}

							if (($cu_flag==0)&&($folderArray[1][$j]>=10)) {
                                $objects1=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
                                $cu_flag=1;
                            } elseif (($se_flag==0)&&($folderArray[1][$j]==3)) {
                                $objects2="sent_".$row1[0];
                                $se_flag=1;
                            }

							if($j==count($folderArray[1])-1)
							{

								for($i=0;$i<8;$i++)
								{

									if ($i==0) {
                                        $arry1.="{nesote_id}{nesote_,}";
                                    } elseif ($i==5) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } elseif ($i==7) {
                                        $arry1.="unread_{nesote_,}";
                                        if ($row1[$i]==0) {
                                            $unread_flag=0;
                                        }
                                    } else {
                                        $arry1.=$row1[$i]."{nesote_,}";
                                    }
								}
								$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
								$arry1.="{nesote_time}{nesote_,}";
								//$arry1.=$this->gettime($row1[9])."{nesote_,}";
								$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
								$arry1.=$row1[9]."{nesote_,}";
								$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry1.=count($folderArray[1])."{nesote_,}";
								//$arry1.=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
								$arry1.="custom_{nesote}{nesote_,}";
								$arry1.="allobjects";

								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="1{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
								$arry.="custom_{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="allobjects";

							}
							else
							{
								
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } elseif ($i==7) {
                                        $arry.=$row1[$i]."{nesote_,}";
                                        if ($row1[$i]==0) {
                                            $unread_flag=0;
                                        }
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								//$arry.=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];//echo $this->getfoldernamenew($folderArray[1][$j]);
								$arry.="custom_{nesote}{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="allobjects";
							}
						}
						$arry.="{nesote_ref}";

					}
					if ($cu_flag==1) {
                        $all_objects.=$objects1.".";
                    }
					if ($se_flag==1) {
                        $all_objects.=$objects2.".";
                    }

					$arry1=str_replace("{nesote_id}",$object_id,$arry1);
					$arry=str_replace("allobjects",$all_objects,$arry);
					$arry1=str_replace("allobjects",$all_objects,$arry1);
					$arry1=str_replace("unread_{nesote_,}",$unread_flag."{nesote_,}",$arry1);
					$arry=str_replace("custom_{nesote}",$folder."_".$object_id,$arry);
					$arry1=str_replace("custom_{nesote}",$folder."_".$object_id,$arry1);
					$time=$this->gettime($time);
					$arry1=str_replace("{nesote_time}",$time,$arry1);
					$arry=substr($arry,0,-12);
					$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
					$var++;
					//echo $returnstr."*************";
					}
					$returnstr=substr($returnstr,0,-18);
					$returnstr=$returnstr."{nesote_count}".$var;echo $returnstr;
					exit;
				}

			}



			echo $returnstr;
			exit;
		}
	}

	function getorginalmailAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$name=$this->getParam(1);
			$mailId=$this->getParam(2);
			$object=$this->getParam(4);
			$search_object=$this->getParam(5);
			$folderid=$this->getfolderid($name);
			$id=$this->getId();



			$select=new NesoteDALController();
			if($folderid<10)
			{
				if ($folderid==1) {
                    $select->select("nesote_email_inbox_$tablenumber");
                } elseif ($folderid==2) {
                    $select->select("nesote_email_draft_$tablenumber");
                } elseif ($folderid==3) {
                    $select->select("nesote_email_sent_$tablenumber");
                } elseif ($folderid==4) {
                    $select->select("nesote_email_spam_$tablenumber");
                } elseif ($folderid==5) {
                    $select->select("nesote_email_trash_$tablenumber");
                }
				$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
				$select->where("id=? and userid=?",[$mailId,$id]);
				$result=$select->query();
			}
			else
			{
				$select20=new NesoteDALController();
				$select20->select("nesote_email_customfolder");
				$select20->fields("id");
				$select20->where("id=? and userid=? ",[$folderid,$id]);
				$result20=$select20->query();
				while($row20=$select20->fetchRow($result20))
				{
					$select->select("nesote_email_customfolder_mapping_$tablenumber");
					$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select->where("id=? and folderid=?",[$mailId,$row20[0]]);
					$result=$select->query();
				}
			}
			$row1=$select->fetchRow($result);
			for($i=0;$i<9;$i++)
			{
				if ($i==6) {
                    $external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
                    $externals=explode("{nesote_comma}",(string) $external);
                    $arry.=html_entity_decode($externals[0])."{nesote_,}";
                    $extnl_flg=$externals[1];
                } elseif ($i==5) {
                    $arry.=html_entity_decode((string) $row1[$i])."{nesote_,}";
                } else {
                    $arry.=$row1[$i]."{nesote_,}";
                }
			}
			$arry.=$this->gettime($row1[9])."{nesote_,}";
			$arry.=$this->getattachmentIcon($row1[0],$folderid)."{nesote_,}";
			$arry.=$row1[11]."{nesote_,}";
			$arry.=$name."{nesote_,}";
			$arry.=html_entity_decode((string) $row1[6])."{nesote_,}";
			$arry.=$extnl_flg."{nesote_,}";
			$arry.=$row1[9]."{nesote_,}";
			$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
			$arry.=$object."{nesote_,}";
			$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
			$arry.=$search_object;


			echo $arry;exit;
		}
	}

	function getindividualmailsAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$name=$this->getParam(1);
			$mailId=$this->getParam(2);
			$loop=$this->getParam(3);
			$names=explode("_",(string) $name);
			if (count($names)>1) {
                $name=$names[0];
            }
			$folderid=$this->getfolderid($name);
			$id=$this->getId();

			$select=new NesoteDALController();
			$select->select("nesote_email_usersettings");
			$select->fields("external_content");
			$select->where("userid=?",[$id]);
			$result=$select->query();//echo $select->getQuery();
			$row2=$select->fetchRow($result);//echo $external_content."--------------";
			$external_content=$row2[0];//echo $external_content."--------------";
			if($external_content!=0)
			{
				$external_content_flag=0;
				$external_content_display=0;
			}
			else
			{

				$external_content_flag=1;
				$external_content_display=0;
				$select->select("nesote_image_display");
				$select->fields("*");
				$select->where("userid=? and mailid=?",[$id,$mailId]);
				$res3=$select->query();
				$rw3=$select->fetchRow($res3);
				$no2=$select->numRows($res3);//echo $no2."@@@@@@@@@@@";
				if($no2==1)
				{
					$external_content_display=1;
				}
				else
				{
					$cooky=$_COOKIE["image_display"];
					$cookys=explode(",",(string) $cooky);
					$nos=count($cookys);
					$new=0;
					for($r=0;$r<$nos;$r++)
					{
						$combo[$r]=explode(":",$cookys[$r]);
						if(($combo[$r][0]==$folderid)&&($combo[$r][1]==$mailId))
						{
							$new=1;
						}
					}//print_r($combo);exit;
					//echo $new."&&&&&&&&&&&&&&&";
					if ($new==1) {
                        $external_content_display=1;
                    }
				}
			}


			$select1=new NesoteDALController();
			if($name=="starred")
			{
				$select1->select("nesote_email_inbox_$tablenumber");
				$select1->fields("mail_references");
				$select1->where("id=? and starflag=? and userid=?",[$mailId,1,$id]);
				$result1=$select1->query();
				$noz=$select1->numRows($result1);
				if($noz==1)
				{
					$row2=$select1->fetchRow($result1);
				}
				else
				{
					$select1->select("nesote_email_draft_$tablenumber");
					$select1->fields("mail_references");
					$select1->where("id=? and starflag=? and userid=? and just_insert=?",[$mailId,1,$id,0]);
					$result1=$select1->query();
					$noz=$select1->numRows($result1);
					if ($noz==1) {
                        $row2=$select1->fetchRow($result1);
                    } else
					{
						$select1->select("nesote_email_sent_$tablenumber");
						$select1->fields("mail_references");
						$select1->where("id=? and starflag=? and userid=?",[$mailId,1,$id]);
						$result1=$select1->query();
						$noz=$select1->numRows($result1);
						if ($noz==1) {
                            $row2=$select1->fetchRow($result1);
                        } else
						{
							$select1->select("nesote_email_spam_$tablenumber");
							$select1->fields("mail_references");
							$select1->where("id=? and starflag=? and userid=?",[$mailId,1,$id]);
							$result1=$select1->query();
							$noz=$select1->numRows($result1);
							if ($noz==1) {
                                $row2=$select1->fetchRow($result1);
                            } else
							{
								$select21=new NesoteDALController();
								$select21->select("nesote_email_customfolder_mapping_$tablenumber");
								$select21->fields("folderid");
								$select21->where("id=? and starflag=?",[$mailId,1]);
								$result21=$select21->query();
								$row21=$select21->fetchRow($result21);

								//$select20=new NesoteDALController();
								$select1->select("nesote_email_customfolder");
								$select1->fields("userid");
								$select1->where("id=? ",[$row21[0]]);
								$result20=$select1->query();
								$row20=$select1->fetchRow($result20);

								if($row20[0]==$id)
								{
									$select1->select("nesote_email_customfolder_mapping_$tablenumber");
									$select1->fields("mail_references");
									$select1->where("id=? ",[$mailId]);
									$result1=$select1->query();
									$noz=$select1->numRows($result1);
									if ($noz==1) {
                                        $row2=$select1->fetchRow($result1);
                                    }
								}
							}
						}
					}
				}
			}
			else
			{
				if ($name=="inbox") {
                    $select1->select("nesote_email_inbox_$tablenumber");
                    $select1->fields("mail_references");
                    $select1->where("id=? and userid=?",[$mailId,$id]);
                    $result1=$select1->query();
                } elseif ($name=="draft") {
                    $select1->select("nesote_email_draft_$tablenumber");
                    $select1->fields("mail_references");
                    $select1->where("id=? and just_insert=? and userid=?",[$mailId,1,$id]);
                    $result1=$select1->query();
                } elseif ($name=="sent") {
                    $select1->select("nesote_email_sent_$tablenumber");
                    $select1->fields("mail_references");
                    $select1->where("id=? and userid=?",[$mailId,$id]);
                    $result1=$select1->query();
                } elseif ($name=="spam") {
                    $select1->select("nesote_email_spam_$tablenumber");
                    $select1->fields("mail_references");
                    $select1->where("id=? and userid=?",[$mailId,$id]);
                    $result1=$select1->query();
                } elseif ($name=="trash") {
                    $select1->select("nesote_email_trash_$tablenumber");
                    $select1->fields("mail_references");
                    $select1->where("id=? and userid=?",[$mailId,$id]);
                    $result1=$select1->query();
                } else
				{
					//$select1=new NesoteDALController();
					$select1->select("nesote_email_customfolder_mapping_$tablenumber");
					$select1->fields("folderid");
					$select1->where("id=?",[$mailId]);
					$result21=$select1->query();
					$row21=$select1->fetchRow($result21);

					//$select=new NesoteDALController();
					$select->select("nesote_email_customfolder");
					$select->fields("userid");
					$select->where("id=? ",[$row21[0]]);
					$result20=$select->query();
					$row20=$select->fetchRow($result20);

					if($row20[0]==$id)
					{
						$select1->select("nesote_email_customfolder_mapping_$tablenumber");
						$select1->fields("mail_references");
						$select1->where("id=? ",[$mailId]);
						$result1=$select1->query();
					}
				}
				$row2=$select1->fetchRow($result1);
			}
			preg_match_all('/<item>(.+?)<\/item>/i',(string) $row2[0],$itemArray);
            $counter = count($itemArray[0]);
			for($itr=0;$itr<$counter;$itr++)
			{
				$ref=$itemArray[1][$loop];
				preg_match('/<folderid>(.+?)<\/folderid>/i',$ref,$folderArray);
				preg_match('/<mailid>(.+?)<\/mailid>/i',$ref,$mailidArray);
			}
			$foldrd=$folderArray[1];$maild=$mailidArray[1];
			//echo $mailidArray[1];echo "nnnn";

			$select=new NesoteDALController();

			if ($foldrd === "1") {
                $select->select("nesote_email_inbox_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("id=? ",[$maild]);
                $result=$select->query();
            } elseif ($foldrd === "2") {
                $select->select("nesote_email_draft_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("id=? and just_insert=?",[$maild,1]);
                $result=$select->query();
            } elseif ($foldrd === "3") {
                $select->select("nesote_email_sent_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("id=? ",[$maild]);
                $result=$select->query();
            } elseif ($foldrd === "4") {
                $select->select("nesote_email_spam_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("id=? ",[$maild]);
                $result=$select->query();
            } elseif ($foldrd === "5") {
                $select->select("nesote_email_trash_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("id=? ",[$maild]);
                $result=$select->query();
            } else
			{
				//$select=new NesoteDALController();
				$select->select("nesote_email_customfolder_mapping_$tablenumber");
				$select->fields("folderid");
				$select->where("id=? ",[$maild]);
				$result11=$select->query();
				$row11=$select->fetchRow($result11);

				//$select1=new NesoteDALController();
				$select1->select("nesote_email_customfolder");
				$select1->fields("userid");
				$select1->where("id=? ",[$row11[0]]);
				$result10=$select1->query();
				$row10=$select1->fetchRow($result10);

				if($row10[0]==$id)
				{
					$select->select("nesote_email_customfolder_mapping_$tablenumber");
					$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select->where("id=? ",[$maild]);
					$result=$select->query();
				}
			}
			$row1=$select->fetchRow($result);
			for($i=0;$i<9;$i++)
			{
				if ($i==6) {
                    $external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
                    $externals=explode("{nesote_comma}",(string) $external);
                    $arry.=html_entity_decode($externals[0]).":;,";
                    $extnl_flg=$externals[1];
                } elseif ($i==5) {
                    $arry.=html_entity_decode((string) $row1[$i]).":;,";
                } else {
                    $arry.=$row1[$i].":;,";
                }
			}
			$arry.=$this->gettime($row1[9]).":;,";
			$arry.=$this->getattachmentIcon($row1[0],$folderid).":;,";
			$arry.="1:;,";
			$arry.=$this->getfoldername($foldrd).":;,";
			$arry.=html_entity_decode((string) $row1[6]).":;,";
			$arry.=$extnl_flg.":;,";
			$arry.=$row1[9].":;,";
			$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50);
			echo $arry;exit;
			//exit;

		}
	}
	function getdetailedmailsAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$name=$this->getParam(1);
			$mailId=$this->getParam(2);
			$fieldArray=$this->getParam(3);
			//$name=$this->getParam(4);
			$org_folder=$this->getParam(4);
			$object=$this->getParam(6);
			//$mailId=explode("_",$object);//echo $mailId[1];
			$fldrid=$this->getfolderid($name);
			$id=$this->getId();
			$fieldz="";
			$fieldsArry=explode(",",(string) $fieldArray);
            $counter = count($fieldsArry);
			for($i=0;$i<$counter;$i++)
			{
				$fieldz.=$fieldsArry[$i].",";
			}
			$flds=substr($fieldz,0,-1);

			$select=new NesoteDALController();
			$select->select("nesote_email_usersettings");
			$select->fields("external_content");
			$select->where("userid=?",[$id]);
			$result=$select->query();//echo $select->getQuery();
			$row2=$select->fetchRow($result);//echo $external_content."--------------";
			$external_content=$row2[0];//echo $external_content."--------------";
			if($external_content!=0)
			{
				$external_content_flag=0;
				$external_content_display=1;
			}
			else
			{

				$external_content_flag=1;
				$external_content_display=0;
				$select->select("nesote_image_display");
				$select->fields("*");
				$select->where("userid=? and mailid=?",[$id,$mailId]);
				$res3=$select->query();
				$rw3=$select->fetchRow($res3);
				$no2=$select->numRows($res3);//echo $no2."@@@@@@@@@@@";
				if($no2==1)
				{
					$external_content_display=1;
				}
				else
				{
					$cooky=$_COOKIE["image_display"];
					$cookys=explode(",",(string) $cooky);
					$nos=count($cookys);
					$new=0;
					for($r=0;$r<$nos;$r++)
					{
						$combo[$r]=explode(":",$cookys[$r]);
						if(($combo[$r][0]==$fldrid)&&($combo[$r][1]==$mailId))
						{
							$new=1;
						}
					}//print_r($combo);exit;
					//echo $new."&&&&&&&&&&&&&&&";
					if ($new==1) {
                        $external_content_display=1;
                    }
				}
			}
			$select1=new NesoteDALController();
			//$select=new NesoteDALController();
			if ($name=="inbox") {
                $select->select("nesote_email_inbox_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                $var=0;
                $arry="";
                $row=$select->fetchRow($result);
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                $array_count=count($folderArray[1]);
                $r=0;
                while($r<$array_count)
				{

					if($folderArray[1][$r]==1)
					{
						$select1->select("nesote_email_inbox_$tablenumber");
					}
					else
					{
						$select1->select("nesote_email_sent_$tablenumber");
					}


					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
					$result1=$select1->query();
					while($row1=$select1->fetchRow($result1))
					{
						if($r === $array_count - 1)
						{
							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							//$arry.=$this->getfoldername($folderArray[1][$r])."_".$row1[0]."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";
						}
						else
						{
							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							// $arry.=$this->getfoldername($folderArray[1][$r])."_".$row1[0]."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";
						}
					}
					if($folderArray[1][$r]==1)
					{
						$select1->update("nesote_email_inbox_$tablenumber");
					}
					else
					{
						$select1->update("nesote_email_sent_$tablenumber");
					}
					$select1->set("readflag=?",1);
					$select1->where("id=?",[$mailidArray[1][$r]]);
					$result1=$select1->query();
					$r++;
				}
                $arry=substr($arry,0,-12);
            } elseif ($name=="draft") {
                $select->select("nesote_email_draft_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("userid=? and id=? and just_insert=?",[$id,$mailId,0]);
                $result=$select->query();
                $var=0;
                $arry="";
                while($row1=$select->fetchRow($result))
				{
					for($i=0;$i<9;$i++)
					{
						$arry.=$row1[$i]."{nesote_,}";
					}
					$arry.=$this->gettime($row1[9])."{nesote_,}";
					$arry.=$this->getattachmentIcon($row1[0],2)."{nesote_,}";
					$arry.="0{nesote_,}";
					$arry.="draft{nesote_,}";
					$arry.=$row1[6]."{nesote_,}";
					$arry.="0{nesote_,}";
					$arry.=$row1[9]."{nesote_,}";
					$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
					$arry.=$object."{nesote_,}";
					$arry.=$this->gettimeinside($row1[9])."{nesote_,}";

					$var++;
				}
                $arry=substr($arry,0,-10);
            } elseif ($name=="sent") {
                $select->select("nesote_email_sent_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                //echo $select->getQuery();
                $var=0;
                $arry="";
                $row=$select->fetchRow($result);
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                $array_count=count($folderArray[1]);
                $r=0;
                while($r<$array_count)
				{
					$select1=new NesoteDALController();
					if ($folderArray[1][$r]==1) {
                        $select1->select("nesote_email_inbox_$tablenumber");
                    } elseif ($folderArray[1][$r]==3) {
                        $select1->select("nesote_email_sent_$tablenumber");
                    } else
					{
						$select1->select("nesote_email_customfolder_mapping_$tablenumber");
					}
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select1->where("id=? ",[$mailidArray[1][$r]]);
					$result1=$select1->query();
					$var1=0;
					while($row1=$select1->fetchRow($result1))
					{

						if($r === $array_count - 1)
						{
							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";
						}
						else
						{
							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";
						}
					}
					if($folderArray[1][$r]==1)
					{
						$select1->update("nesote_email_inbox_$tablenumber");
					}
					else
					{
						$select1->update("nesote_email_sent_$tablenumber");
					}
					$select1->set("readflag=?",1);
					$select1->where("id=?",[$mailidArray[1][$r]]);
					$result1=$select1->query();
					$r++;
				}
                $arry=substr($arry,0,-12);
            } elseif ($name=="spam") {
                $select->select("nesote_email_spam_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                $var=0;
                $arry="";
                $row=$select->fetchRow($result);
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                $array_count=count($folderArray[1]);
                $r=0;
                while($r<$array_count)
				{
					$select->select("nesote_email_spam_$tablenumber");
					$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select->where("userid=? and id=?",[$id,$mailidArray[1][$r]]);
					$result=$select->query();

					while($row1=$select->fetchRow($result))
					{
						if($r === $array_count - 1)
						{
							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";//echo $arry."------";
						}
						else
						{
							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";//echo $arry."++++";
						}
					}

					//$select1=new NesoteDALController();
					$select1->update("nesote_email_spam_$tablenumber");
					$select1->set("readflag=?",1);
					$select1->where("id=?",[$mailId]);
					$result1=$select1->query();
					$r++;
				}
                $arry=substr($arry,0,-12);
            } elseif ($name=="trash") {
                $select->select("nesote_email_trash_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                $var=0;
                $arry="";
                $row=$select->fetchRow($result);
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                $array_count=count($folderArray[1]);
                $r=0;
                while($r<$array_count)
				{
					$select->select("nesote_email_trash_$tablenumber");
					$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select->where("userid=? and id=?",[$id,$mailidArray[1][$r]]);
					$result=$select->query();

					while($row1=$select->fetchRow($result))
					{
						if($r === $array_count - 1)
						{
							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],5)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";
						}
						else
						{
							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],5)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.=$object."{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="{nesote_///}";
						}
					}
					//$select1=new NesoteDALController();
					$select1->update("nesote_email_trash_$tablenumber");
					$select1->set("readflag=?",1);
					$select1->where("id=?",[$mailId]);
					$result1=$select1->query();
					$r++;
				}
                $arry=substr($arry,0,-12);
            } elseif ($name=="starred") {
                $select->select("nesote_email_inbox_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=? and starflag=?",[$id,$mailId,1]);
                $result=$select->query();
                $var=0;
                $arry="";
                $no=$select->numRows($result);
                if($no!=0)
				{
					$row=$select->fetchRow($result);

					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					$array_count=count($folderArray[1]);
					$r=0;
					while($r<$array_count)
					{
						//$select1=new NesoteDALController();
						if($folderArray[1][$r]==1)
						{
							$select1->select("nesote_email_inbox_$tablenumber");
						}
						else
						{
							$select1->select("nesote_email_sent_$tablenumber");
						}


						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
						$result1=$select1->query();
						$var1=0;
						while($row1=$select1->fetchRow($result1))
						{

							if($r === $array_count - 1)
							{
								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.=$object."{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="{nesote_///}";
							}
							else
							{
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.=$object."{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="{nesote_///}";
							}
						}

						$r++;
					}
				}
                $select->select("nesote_email_draft_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("userid=? and id=? and just_insert=? and starflag=?",[$id,$mailId,0,1]);
                $result=$select->query();
                $var=0;
                while($row1=$select->fetchRow($result))
				{
					for($i=0;$i<9;$i++)
					{
						$arry.=$row1[$i]."{nesote_,}";
					}
					$arry.=$this->gettime($row1[9])."{nesote_,}";
					$arry.=$this->getattachmentIcon($row1[0],2)."{nesote_,}";
					$arry.="0{nesote_,}";
					$arry.="draft{nesote_,}";
					$arry.=html_entity_decode((string) $row1[6])."{nesote_,}";
					$arry.="0{nesote_,}";
					$arry.=$row1[9]."{nesote_,}";
					$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
					$arry.=$object."{nesote_,}";
					$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
					$arry.="{nesote_///}";
					$var++;
				}
                $select->select("nesote_email_sent_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=? and starflag=?",[$id,$mailId,1]);
                $result=$select->query();
                $no=$select->numRows($result);
                if($no!=0)
				{
					$row=$select->fetchRow($result);

					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					$array_count=count($folderArray[1]);
					$r=0;
					while($r<$array_count)
					{
						//$select1=new NesoteDALController();
						if($folderArray[1][$r]==1)
						{
							$select1->select("nesote_email_inbox_$tablenumber");
						}
						else
						{
							$select1->select("nesote_email_sent_$tablenumber");
						}


						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
						$result1=$select1->query();
						$var1=0;
						while($row1=$select1->fetchRow($result1))
						{

							if($r === $array_count - 1)
							{
								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.=$object."{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="{nesote_///}";
							}
							else
							{
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.=$object."{nesote_,}";
								$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
								$arry.="{nesote_///}";
							}
						}

						$r++;
					}
				}
                $db3=new NesoteDALController();
                $db3->select("nesote_email_customfolder");
                $db3->fields("id");
                $db3->where("userid=?",$id);
                $rs3=$db3->query();
                while($row3=$db3->fetchRow($rs3))
				{
					$select->select("nesote_email_customfolder_mapping_$tablenumber");
					$select->fields("mail_references");
					$select->where("folderid=? and id=? and starflag=?",[$row3[0],$mailId,1]);
					$result=$select->query();
					$no=$select->numRows($result);
					while($row=$select->fetchRow($result))
					{
						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						$array_count=count($folderArray[1]);
						$r=0;
						while($r<$array_count)
						{
							//$select1=new NesoteDALController();
							if($folderArray[1][$r]>=10)
							{
								$select1->select("nesote_email_customfolder_mapping_$tablenumber");
								$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
								$select1->where("folderid=? and id=? ",[$folderArray[1][$r],$mailidArray[1][$r]]);
								$result1=$select1->query();
							}
							else
							{
								$select1->select("nesote_email_sent_$tablenumber");
								$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
								$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
								$result1=$select1->query();
							}

							$var1=0;
							while($row1=$select1->fetchRow($result1))
							{

								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.=$object."{nesote_,}";
									$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.=$object."{nesote_,}";
									$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}

							$r++;
						}
					}

				}
                $arry=substr($arry,0,-12);
            } elseif ((str_starts_with((string) $name, "search"))||(str_starts_with((string) $name, "from"))) {
                $namezz=substr((string) $name,6);
                $len=strpos($namezz,"_");
                $folderidSearch=substr($namezz,0,$len);
                if ($folderidSearch==6) {
                    $select->select("nesote_email_inbox_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=? and starflag=?",[$id,$mailId,1]);
                    $result=$select->query();
                    $var=0;
                    $arry="";
                    $no=$select->numRows($result);
                    if($no!=0)
					{
						$row=$select->fetchRow($result);

						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						$array_count=count($folderArray[1]);
						$r=0;
						while($r<$array_count)
						{
							//$select1=new NesoteDALController();
							if($folderArray[1][$r]==1)
							{
								$select1->select("nesote_email_inbox_$tablenumber");
							}
							else
							{
								$select1->select("nesote_email_sent_$tablenumber");
							}


							$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
							$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
							$result1=$select1->query();
							$var1=0;
							while($row1=$select1->fetchRow($result1))
							{

								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}

							$r++;
						}
					}
                    $select->select("nesote_email_draft_$tablenumber");
                    $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                    $select->where("userid=? and id=? and just_insert=? and starflag=?",[$id,$mailId,0,1]);
                    $result=$select->query();
                    $var=0;
                    while($row1=$select->fetchRow($result))
					{
						for($i=0;$i<9;$i++)
						{
							$arry.=$row1[$i]."{nesote_,}";
						}
						$arry.=$this->gettime($row1[9])."{nesote_,}";
						$arry.=$this->getattachmentIcon($row1[0],2)."{nesote_,}";
						$arry.="0{nesote_,}";
						$arry.="draft{nesote_,}";
						$arry.=html_entity_decode((string) $row1[6])."{nesote_,}";
						$arry.="0{nesote_,}";
						$arry.=$row1[9]."{nesote_,}";
						$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
						$arry.="{nesote_///}";
						$var++;
					}
                    $select->select("nesote_email_sent_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=? and starflag=?",[$id,$mailId,1]);
                    $result=$select->query();
                    $no=$select->numRows($result);
                    if($no!=0)
					{
						$row=$select->fetchRow($result);

						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						$array_count=count($folderArray[1]);
						$r=0;
						while($r<$array_count)
						{
							//$select1=new NesoteDALController();
							if($folderArray[1][$r]==1)
							{
								$select1->select("nesote_email_inbox_$tablenumber");
							}
							else
							{
								$select1->select("nesote_email_sent_$tablenumber");
							}


							$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
							$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
							$result1=$select1->query();
							$var1=0;
							while($row1=$select1->fetchRow($result1))
							{

								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}

							$r++;
						}
					}
                    $db3=new NesoteDALController();
                    $db3->select("nesote_email_customfolder");
                    $db3->fields("id");
                    $db3->where("userid=?",$id);
                    $rs3=$db3->query();
                    while($row3=$db3->fetchRow($rs3))
					{
						$select->select("nesote_email_customfolder_mapping_$tablenumber");
						$select->fields("mail_references");
						$select->where("folderid=? and id=? and starflag=?",[$row3[0],$mailId,1]);
						$result=$select->query();
						$no=$select->numRows($result);
						while($row=$select->fetchRow($result))
						{
							preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
							preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
							$array_count=count($folderArray[1]);
							$r=0;
							while($r<$array_count)
							{
								//$select1=new NesoteDALController();
								if($folderArray[1][$r]>=10)
								{
									$select1->select("nesote_email_customfolder_mapping_$tablenumber");
									$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
									$select1->where("folderid=? and id=? ",[$folderArray[1][$r],$mailidArray[1][$r]]);
									$result1=$select1->query();
								}
								else
								{
									$select1->select("nesote_email_sent_$tablenumber");
									$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
									$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
									$result1=$select1->query();
								}

								$var1=0;
								while($row1=$select1->fetchRow($result1))
								{

									if($r === $array_count - 1)
									{
										for($i=0;$i<(9);$i++)
										{
											if($i==6)
											{
												$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
												$externals=explode("{nesote_comma}",(string) $external);
												$arry.=$externals[0]."{nesote_,}";
												$extnl_flg=$externals[1];
											}
											else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
									else
									{
										for($i=0;$i<(9);$i++)
										{
											if ($i==5) {
                                                $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                            } elseif ($i==6) {
                                                $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                            } else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],1)."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
								}

								$r++;
							}
						}

					}
                } elseif ($folderidSearch==0) {
                    $select->select("nesote_email_inbox_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    $var=0;
                    $arry="";
                    $no=$select->numRows($result);
                    if($no!=0)
					{
						$row=$select->fetchRow($result);

						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						$array_count=count($folderArray[1]);
						$r=0;
						while($r<$array_count)
						{
							//$select1=new NesoteDALController();
							if($folderArray[1][$r]==1)
							{
								$select1->select("nesote_email_inbox_$tablenumber");
							}
							else
							{
								$select1->select("nesote_email_sent_$tablenumber");
							}


							$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
							$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
							$result1=$select1->query();
							$var1=0;
							while($row1=$select1->fetchRow($result1))
							{

								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}

							$r++;
						}
					}
                    $select->select("nesote_email_draft_$tablenumber");
                    $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                    $select->where("userid=? and id=? and just_insert=?",[$id,$mailId,0]);
                    $result=$select->query();
                    $var=0;
                    while($row1=$select->fetchRow($result))
					{
						for($i=0;$i<9;$i++)
						{
							$arry.=$row1[$i]."{nesote_,}";
						}
						$arry.=$this->gettime($row1[9])."{nesote_,}";
						$arry.=$this->getattachmentIcon($row1[0],2)."{nesote_,}";
						$arry.="0{nesote_,}";
						$arry.="draft{nesote_,}";
						$arry.=html_entity_decode((string) $row1[6])."{nesote_,}";
						$arry.="0{nesote_,}";
						$arry.=$row1[9]."{nesote_,}";
						$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
						$arry.="{nesote_///}";
						$var++;
					}
                    $select->select("nesote_email_sent_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    //echo $select->getQuery();
                    $no=$select->numRows($result);
                    if($no!=0)
					{
						$row=$select->fetchRow($result);

						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						$array_count=count($folderArray[1]);
						$r=0;
						while($r<$array_count)
						{
							//$select1=new NesoteDALController();
							if ($folderArray[1][$r]==1) {
                                $select1->select("nesote_email_inbox_$tablenumber");
                                $select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                                $select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
                            } elseif ($folderArray[1][$r]==3) {
                                $select1->select("nesote_email_sent_$tablenumber");
                                $select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                                $select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
                            } elseif ($folderArray[1][$r]>=10) {
                                $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                                $select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                                $select1->where("folderid=? and id=? ",[$folderArray[1][$r],$mailidArray[1][$r]]);
                            }


								
							$result1=$select1->query();
							$var1=0;
							while($row1=$select1->fetchRow($result1))
							{

								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}

							$r++;
						}
					}
                    $select->select("nesote_email_spam_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    $row=$select->fetchRow($result);
                    preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                    preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                    $array_count=count($folderArray[1]);
                    $r=0;
                    while($r<$array_count)
					{
						$select->select("nesote_email_spam_$tablenumber");
						$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select->where("userid=? and id=?",[$id,$mailidArray[1][$r]]);
						$result=$select->query();
						$var=0;
						while($row1=$select->fetchRow($result))
						{
							if($r === $array_count - 1)
							{
								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.="{nesote_///}";
							}
							else
							{
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry1.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry1.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.="{nesote_///}";
							}
						}
						//$select1=new NesoteDALController();
						$select1->update("nesote_email_spam_$tablenumber");
						$select1->set("readflag=?",1);
						$select1->where("id=?",[$mailId]);
						$result1=$select1->query();
						$r++;
					}
                    $select->select("nesote_email_trash_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    $row=$select->fetchRow($result);
                    preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                    preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                    $array_count=count($folderArray[1]);
                    $r=0;
                    while($r<$array_count)
					{
						$select->select("nesote_email_trash_$tablenumber");
						$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select->where("userid=? and id=?",[$id,$mailidArray[1][$r]]);
						$result=$select->query();
						$var=0;
						while($row1=$select->fetchRow($result))
						{
							if($r === $array_count - 1)
							{
								for($i=0;$i<(9);$i++)
								{
									if($i==6)
									{
										$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
										$externals=explode("{nesote_comma}",(string) $external);
										$arry.=$externals[0]."{nesote_,}";
										$extnl_flg=$externals[1];
									}
									else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.="{nesote_///}";
							}
							else
							{
								for($i=0;$i<(9);$i++)
								{
									if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
								}

								$arry.=$this->gettime($row1[9])."{nesote_,}";
								$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
								$arry.="0{nesote_,}";
								$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
								$arry.=$row1[6]."{nesote_,}";
								$arry.=$extnl_flg."{nesote_,}";
								$arry.=$row1[9]."{nesote_,}";
								$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
								$arry.="{nesote_///}";
							}
						}
						//$select1=new NesoteDALController();
						$select1->update("nesote_email_trash_$tablenumber");
						$select1->set("readflag=?",1);
						$select1->where("id=?",[$mailId]);
						$result1=$select1->query();
						$r++;
					}
                    $db3=new NesoteDALController();
                    $db3->select("nesote_email_customfolder");
                    $db3->fields("id");
                    $db3->where("userid=?",$id);
                    $rs3=$db3->query();
                    while($row3=$db3->fetchRow($rs3))
					{
						$select->select("nesote_email_customfolder_mapping_$tablenumber");
						$select->fields("mail_references");
						$select->where("folderid=? and id=?",[$row3[0],$mailId]);
						$result=$select->query();
						$no=$select->numRows($result);
						while($row=$select->fetchRow($result))
						{
							preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
							preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
							$array_count=count($folderArray[1]);
							$r=0;
							while($r<$array_count)
							{
								//$select1=new NesoteDALController();
								if($folderArray[1][$r]>=10)
								{
									$select1->select("nesote_email_customfolder_mapping_$tablenumber");
									$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
									$select1->where("folderid=? and id=? ",[$folderArray[1][$r],$mailidArray[1][$r]]);
									$result1=$select1->query();
								}
								else
								{
									$select1->select("nesote_email_sent_$tablenumber");
									$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
									$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
									$result1=$select1->query();
								}

								$var1=0;
								while($row1=$select1->fetchRow($result1))
								{

									if($r === $array_count - 1)
									{
										for($i=0;$i<(9);$i++)
										{
											if($i==6)
											{
												$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
												$externals=explode("{nesote_comma}",(string) $external);
												$arry.=$externals[0]."{nesote_,}";
												$extnl_flg=$externals[1];
											}
											else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
									else
									{
										for($i=0;$i<(9);$i++)
										{
											if ($i==5) {
                                                $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                            } elseif ($i==6) {
                                                $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                            } else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
								}

								$r++;
							}
						}

					}
                } elseif ($folderidSearch>=10) {
                    $db3=new NesoteDALController();
                    $db3->select("nesote_email_customfolder");
                    $db3->fields("id");
                    $db3->where("userid=?",$id);
                    $rs3=$db3->query();
                    while($row3=$db3->fetchRow($rs3))
					{
						$select->select("nesote_email_customfolder_mapping_$tablenumber");
						$select->fields("mail_references");
						$select->where("folderid=? and id=?",[$row3[0],$mailId]);
						$result=$select->query();
						$no=$select->numRows($result);
						while($row=$select->fetchRow($result))
						{
							preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
							preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
							$array_count=count($folderArray[1]);
							$r=0;
							while($r<$array_count)
							{
								//$select1=new NesoteDALController();
								if($folderArray[1][$r]>=10)
								{
									$select1->select("nesote_email_customfolder_mapping_$tablenumber");
									$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
									$select1->where("folderid=? and id=? ",[$folderArray[1][$r],$mailidArray[1][$r]]);
									$result1=$select1->query();
								}
								else
								{
									$select1->select("nesote_email_sent_$tablenumber");
									$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
									$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
									$result1=$select1->query();
								}

								$var1=0;
								while($row1=$select1->fetchRow($result1))
								{

									if($r === $array_count - 1)
									{
										for($i=0;$i<(9);$i++)
										{
											if($i==6)
											{
												$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
												$externals=explode("{nesote_comma}",(string) $external);
												$arry.=$externals[0]."{nesote_,}";
												$extnl_flg=$externals[1];
											}
											else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
									else
									{
										for($i=0;$i<(9);$i++)
										{
											if ($i==5) {
                                                $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                            } elseif ($i==6) {
                                                $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                            } else {
                                                $arry.=$row1[$i]."{nesote_,}";
                                            }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
								}

								$r++;
							}
						}

					}
                } elseif ($folderidSearch==1) {
                    $select->select("nesote_email_inbox_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    $var=0;
                    $arry="";
                    $no=$select->numRows($result);
                    if($no!=0)
						{
							$row=$select->fetchRow($result);

							preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
							preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
							$array_count=count($folderArray[1]);
							$r=0;
							while($r<$array_count)
							{
								//$select1=new NesoteDALController();
								if($folderArray[1][$r]==1)
								{
									$select1->select("nesote_email_inbox_$tablenumber");
								}
								else
								{
									$select1->select("nesote_email_sent_$tablenumber");
								}


								$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
								$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
								$result1=$select1->query();
								$var1=0;
								while($row1=$select1->fetchRow($result1))
								{

									if($r === $array_count - 1)
									{
										for($i=0;$i<(9);$i++)
										{
											if($i==6)
											{
												$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
												$externals=explode("{nesote_comma}",(string) $external);
												$arry.=$externals[0]."{nesote_,}";
												$extnl_flg=$externals[1];
											}
											else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
									else
									{
										for($i=0;$i<(9);$i++)
										{
											if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
								}

								$r++;
							}
						}
                } elseif ($folderidSearch==2) {
                    $select->select("nesote_email_draft_$tablenumber");
                    $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                    $select->where("userid=? and id=? and just_insert=?",[$id,$mailId,0]);
                    $result=$select->query();
                    $var=0;
                    while($row1=$select->fetchRow($result))
						{
							for($i=0;$i<9;$i++)
							{
								$arry.=$row1[$i]."{nesote_,}";
							}
							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],2)."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.="draft{nesote_,}";
							$arry.=html_entity_decode((string) $row1[6])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="{nesote_///}";
							$var++;
						}
                } elseif ($folderidSearch==3) {
                    $select->select("nesote_email_sent_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    $no=$select->numRows($result);
                    if($no!=0)
						{
							$row=$select->fetchRow($result);

							preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
							preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
							$array_count=count($folderArray[1]);
							$r=0;
							while($r<$array_count)
							{
								//$select1=new NesoteDALController();
								if($folderArray[1][$r]==1)
								{
									$select1->select("nesote_email_inbox_$tablenumber");
								}
								else
								{
									$select1->select("nesote_email_sent_$tablenumber");
								}


								$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
								$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
								$result1=$select1->query();
								$var1=0;
								while($row1=$select1->fetchRow($result1))
								{

									if($r === $array_count - 1)
									{
										for($i=0;$i<(9);$i++)
										{
											if($i==6)
											{
												$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
												$externals=explode("{nesote_comma}",(string) $external);
												$arry.=$externals[0]."{nesote_,}";
												$extnl_flg=$externals[1];
											}
											else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
									else
									{
										for($i=0;$i<(9);$i++)
										{
											if ($i==5) {
                                            $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                        } elseif ($i==6) {
                                            $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                        } else {
                                            $arry.=$row1[$i]."{nesote_,}";
                                        }
										}

										$arry.=$this->gettime($row1[9])."{nesote_,}";
										$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
										$arry.="0{nesote_,}";
										$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
										$arry.=$row1[6]."{nesote_,}";
										$arry.=$extnl_flg."{nesote_,}";
										$arry.=$row1[9]."{nesote_,}";
										$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
										$arry.="{nesote_///}";
									}
								}

								$r++;
							}
						}
                } elseif ($folderidSearch==4) {
                    $select->select("nesote_email_spam_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    $var=0;
                    $arry="";
                    $row=$select->fetchRow($result);
                    preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                    preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                    $array_count=count($folderArray[1]);
                    $r=0;
                    while($r<$array_count)
						{
							$select->select("nesote_email_spam_$tablenumber");
							$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
							$select->where("userid=? and id=?",[$id,$mailidArray[1][$r]]);
							$result=$select->query();
							$var=0;$arry="";
							while($row1=$select->fetchRow($result))
							{
								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],4)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],4)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}
							//$select1=new NesoteDALController();
							$select1->update("nesote_email_spam_$tablenumber");
							$select1->set("readflag=?",1);
							$select1->where("id=?",[$mailId]);
							$result1=$select1->query();
							$r++;
						}
                } elseif ($folderidSearch==5) {
                    $select->select("nesote_email_trash_$tablenumber");
                    $select->fields("mail_references");
                    $select->where("userid=? and id=?",[$id,$mailId]);
                    $result=$select->query();
                    $var=0;
                    $arry="";
                    $row=$select->fetchRow($result);
                    preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                    preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                    $array_count=count($folderArray[1]);
                    $r=0;
                    while($r<$array_count)
						{
							$select->select("nesote_email_trash_$tablenumber");
							$select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
							$select->where("userid=? and id=?",[$id,$mailidArray[1][$r]]);
							$result=$select->query();
							$var=0;$arry="";
							while($row1=$select->fetchRow($result))
							{
								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],5)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],5)."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}
							//$select1=new NesoteDALController();
							$select1->update("nesote_email_trash_$tablenumber");
							$select1->set("readflag=?",1);
							$select1->where("id=?",[$mailId]);
							$result1=$select1->query();
							$r++;
						}
                } else
					{
						$folderid=substr((string) $name,6);
						$select->select("nesote_email_customfolder_mapping_$tablenumber");
						$select->fields("mail_references");
						$select->where("folderid=? and id=?",[$folderid,$mailId]);
						$result=$select->query();
						$var=0;$arry="";
						$row=$select->fetchRow($result);

						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						$array_count=count($folderArray[1]);
						$r=0;
						while($r<$array_count)
						{
							//$select1=new NesoteDALController();
							if($folderArray[1][$r]>=10)
							{
								$select1->select("nesote_email_customfolder_mapping_$tablenumber");
								$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
								$select1->where("folderid=? and id=? ",[$folderArray[1][$r],$mailidArray[1][$r]]);
								$result1=$select1->query();
							}
							else
							{
								$select1->select("nesote_email_sent_$tablenumber");
								$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
								$select1->where("id=? ",[$mailidArray[1][$r]]);
								$result1=$select1->query();
							}



							$var1=0;
							while($row1=$select1->fetchRow($result1))
							{

								if($r === $array_count - 1)
								{
									for($i=0;$i<(9);$i++)
									{
										if($i==6)
										{
											$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
											$externals=explode("{nesote_comma}",(string) $external);
											$arry.=$externals[0]."{nesote_,}";
											$extnl_flg=$externals[1];
										}
										else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
								else
								{
									for($i=0;$i<(9);$i++)
									{
										if ($i==5) {
                                        $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                    } elseif ($i==6) {
                                        $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                    } else {
                                        $arry.=$row1[$i]."{nesote_,}";
                                    }
									}

									$arry.=$this->gettime($row1[9])."{nesote_,}";
									$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
									$arry.="0{nesote_,}";
									$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
									$arry.=$row1[6]."{nesote_,}";
									$arry.=$extnl_flg."{nesote_,}";
									$arry.=$row1[9]."{nesote_,}";
									$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
									$arry.="{nesote_///}";
								}
							}

							$r++;
							if($folderArray[1][$r]>=10)
							{
								$select1->update("nesote_email_customfolder_mapping_$tablenumber");
							}
							else
							{
								$select1->update("nesote_email_sent");
							}
							$select1->set("readflag=?",1);
							$select1->where("id=?",[$mailidArray[1][$r]]);
							$result1=$select1->query();
						}
					}
                $arry=substr($arry,0,-12);
            } else
			{
				$folderid=substr((string) $name,6);
				$select->select("nesote_email_customfolder_mapping_$tablenumber");
				$select->fields("mail_references");
				$select->where("folderid=? and id=?",[$folderid,$mailId]);
				$result=$select->query();
				$var=0;$arry="";
				$row=$select->fetchRow($result);

				preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
				preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
				$array_count=count($folderArray[1]);
				$r=0;
				while($r<$array_count)
				{
					//$select1=new NesoteDALController();
					if($folderArray[1][$r]>=10)
					{
						$select1->select("nesote_email_customfolder_mapping_$tablenumber");
						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select1->where("folderid=? and id=? ",[$folderArray[1][$r],$mailidArray[1][$r]]);
						$result1=$select1->query();
					}
					else
					{
						$select1->select("nesote_email_sent_$tablenumber");
						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select1->where("id=? ",[$mailidArray[1][$r]]);
						$result1=$select1->query();
					}



					$var1=0;
					while($row1=$select1->fetchRow($result1))
					{
						if($r === $array_count - 1)
						{
							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="{nesote_///}";
						}
						else
						{
							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$r])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="{nesote_///}";
						}
					}

					$r++;
					if($folderArray[1][$r]>=10)
					{
						$select1->update("nesote_email_customfolder_mapping_$tablenumber");
					}
					else
					{
						$select1->update("nesote_email_sent_$tablenumber");
					}
					$select1->set("readflag=?",1);
					$select1->where("id=?",[$mailidArray[1][$r]]);
					$result1=$select1->query();
				}
				$arry=substr($arry,0,-12);
			}
			echo substr($arry,0,-10);
			exit;
		}
	}

	function deletemailAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$foldername=$_POST['foldernane'];
			$mailid=$_POST['mailid'];
			$mailids=explode(",",(string) $mailid);
			$userid=$this->getId();
            $counter = count($mailids);
			for($i=0;$i<$counter;$i++)
			{
				$db4=new NesoteDALController();
				if ($foldername=='inbox') {
                    $db4->select("nesote_email_inbox_$tablenumber");
                } elseif ($foldername=='draft') {
                    $db4->select("nesote_email_draft_$tablenumber");
                } elseif ($foldername=='sent') {
                    $db4->select("nesote_email_sent_$tablenumber");
                } elseif ($foldername=='spam') {
                    $db4->select("nesote_email_spam_$tablenumber");
                } elseif ($foldername=='trash') {
                    $db4->select("nesote_email_trash_$tablenumber");
                } else {
                    $db4->select("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db4->fields("mail_references");
				$db4->where("id=?",$mailids[$i]);
				$rs4=$db4->query();
				$row4=$db4->fetchRow($rs4);

				preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row4[0],$folderArray);
				preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row4[0],$mailidArray);
				$array_count=count($folderArray[1]);
				$r=$array_count-1;
				$db=new NesoteDALController();
				while($r>=0)
				{
					$folderArray[1][$r]=$this->getfoldername($folderArray[1][$r]);
					if($folderArray[1][$r]=='trash')
					{

						$db->delete("nesote_email_trash");
						$db->where("id=?",$mailidArray[1][$r]);
						$rs1=$db->query();
					}
					else
					{

						//$db2=new NesoteDALController();
						//$db1=new NesoteDALController();
						if ($folderArray[1][$r]=='inbox') {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderArray[1][$r]=='draft') {
                            $db->select("nesote_email_draft_$tablenumber");
                        } elseif ($folderArray[1][$r]=='sent') {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folderArray[1][$r]=='spam') {
                            $db->select("nesote_email_spam_$tablenumber");
                        } else {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",$mailidArray[1][$r]);
						$rs2=$db->query();
						$row2=$db->fetchRow($rs2);

						if ($folderArray[1][$r]=='spam') {
                            $backRef=$row2[15];
                        } elseif ($folderArray[1][$r]=='inbox') {
                            $backRef=1;
                        } elseif ($folderArray[1][$r]=='draft') {
                            $backRef=2;
                        } elseif ($folderArray[1][$r]=='sent') {
                            $backRef=3;
                        } else
						{

							$db->select("nesote_email_customfolder");
							$db->fields("id");
							$db->where("name=?",$folderArray[1][$r]);
							$rs3=$db->query();
							$row3=$db->fetchRow($rs3);
							$backRef=$row3[0];
						}



						$db->insert("nesote_email_trash_$tablenumber");
						$db->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,mail_references,backreference");
						$db->values([$userid,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13],$ref,$backRef]);
						$result2=$db->query();
						$crnt_id=$db->lastInsert();

						$this->updatedreferences($row2[14],$folderArray[1][$r],$mailidArray[1][$r],'trash',$crnt_id);

						if ($folderArray[1][$r]=='inbox') {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folderArray[1][$r]=='draft') {
                            $db->delete("nesote_email_draft_$tablenumber");
                        } elseif ($folderArray[1][$r]=='sent') {
                            $db->delete("nesote_email_sent_$tablenumber");
                        } elseif ($folderArray[1][$r]=='spam') {
                            $db->delete("nesote_email_spam_$tablenumber");
                        } else {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->where("id=?",$mailids[$i]);
						$rs1=$db->query();

					}
					$r--;
				}

			}
			echo 1;exit;
		}
	}

	function backreferencesAction()
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		$idstring=$this->getParam(1);

		$db=new NesoteDALController();
		$return="";
		$string=explode(",",(string) $idstring);
        $counter = count($string);

		for($i=0;$i<$counter;$i++)
		{
			$mailid=explode("?",$string[$i]);
			if ($mailid[1]=="spam") {
                $db->select("nesote_email_spam_$tablenumber");
            } elseif ($mailid[1]=="trash") {
                $db->select("nesote_email_trash_$tablenumber");
            }
			$db->fields("backreference");
			$db->where("id=?",$mailid[0]);
			$res=$db->query();
			$result=$db->fetchRow($res);
			//$return=$result[0];
			$foldrname=$this->getfoldername($result[0]);
			$return.=$mailid[0]."?".$foldrname.",";

		}
		$return=substr($return,0,-1);
		echo $return;exit;
	}
	function getarray($mail_reference)
	{
		//echo $mail_reference;
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		$select1=new NesoteDALController();
		$in_flag=0;$se_flag=0;$cu_flag=0;$sp_flag=0;$tr_flag=0;$all_objects="";
		preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $mail_reference,$folderArray1);
		preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $mail_reference,$mailidArray1);

			
		for($z=(count($folderArray1[1])-1);$z>=0;$z--)
		{

			if (($in_flag==0)&&($folderArray1[1][$z]==1)) {
                $object.="inbox_".$mailidArray1[1][$z].".";
                $in_flag=1;
            } elseif (($se_flag==0)&&($folderArray1[1][$z]==3)) {
                $object.="sent_".$mailidArray1[1][$z].".";
                $se_flag=1;
            } elseif (($cu_flag==0)&&($folderArray1[1][$z]>=10)) {
                $object.=$this->getfoldernamenew($folderArray1[1][$z])."_".$mailidArray1[1][$z].".";
                $cu_flag=1;
            } elseif (($dr_flag==0)&&($folderArray1[1][$z]==2)) {
                $object.="draft_".$mailidArray1[1][$z].".";
                $dr_flag=1;
            } elseif (($sp_flag==0)&&($folderArray1[1][$z])==4) {
                $object.="spam_".$mailidArray1[1][$z].".";
                $sp_flag=1;
            } elseif (($tr_flag==0)&&($folderArray1[1][$z]==5)) {
                $object.="trash_".$mailidArray1[1][$z].".";
                $tr_flag=1;
            }
		}
		$object=substr($object,0,-1);
		$object1=explode(".",$object);
        $counter = count($object1);
		for($var=0;$var<($counter);$var++)
		{
			$object_new=explode("_",$object1[$var]);//echo $object1[$var];//print_r($object_new);
			if ($object_new[0]=="inbox") {
                $flag=0;
                $unread_flag=1;
                $inb_flag=0;
                $sen_flag=0;
                $arry="";
                $arry1="";
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $mail_reference,$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $mail_reference,$mailidArray);
                //print_r($folderArray);
                for($j=(count($folderArray[1])-1);$j>=0;$j--)
				{

					if ($folderArray[1][$j]==1) {
                        $select1->select("nesote_email_inbox_$tablenumber");
                    } else {
                        $select1->select("nesote_email_sent_$tablenumber");
                    }
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
					$select1->where("id=?",[$mailidArray[1][$j]]);
					$result1=$select1->query();

					if($row1=$select1->fetchRow($result1))
					{     //echo $row1[0]."<br>";
						if(($flag==0)&&(($folderArray[1][$j]==1)))
						{

							$time=$row1[9];

							$object_id=$row1[0];$flag=1;
						}

						//						          if(($inb_flag==0)&&($folderArray[1][$j]==1))
						//									{
						//										$objects1="inbox_".$row1[0];
						//										$inb_flag=1;
						//									}
						//									else if(($sen_flag==0)&&($folderArray[1][$j]==3))
						//									{
						//										$objects2="sent_".$row1[0];
						//										$sen_flag=1;
						//									}



						if($j==count($folderArray[1])-1)
						{

							for($i=0;$i<8;$i++)
							{
								if ($i==0) {
                                    $arry1.="{nesote_id}{nesote_,}";
                                } elseif ($i==5) {
                                    $arry1.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    $arry1.="unread_{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
							}
							$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
							$arry1.="{nesote_time}{nesote_,}";
							//$arry1.=$this->gettime($row1[9])."{nesote_,}";
							$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
							$arry1.=$row1[9]."{nesote_,}";
							$arry1.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							$arry1.=count($folderArray[1])."{nesote_,}";
							$arry1.="inbox_{nesote}{nesote_,}";
							$arry1.="allobjects";




							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									//$arry.="{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="1{nesote_,}";
							$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="inbox_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";



						}

						else
						{


							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    //echo $row1[$i]
                                    $arry.=$row1[$i]."{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="inbox_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
					}
					$arry.="{nesote_ref}";




				}
                if ($in_flag==1) {
                    $all_objects.=$object1[$var].".";
                }
                $arry1=str_replace("{nesote_id}",$object_id,$arry1);
                $arry=str_replace("inbox_{nesote}","inbox_".$object_id,$arry);
                $arry1=str_replace("unread_{nesote_,}",$unread_flag."{nesote_,}",$arry1);
                $arry1=str_replace("inbox_{nesote}","inbox_".$object_id,$arry1);
                $time=$this->gettime($time);
                $arry1=str_replace("{nesote_time}",$time,$arry1);
            } elseif ($object_new[0]=="sent") {
                $flag=0;
                $unread_flag=1;
                $inb_flag=0;
                $sen_flag=0;
                $cus_flag=0;
                $arry="";
                $arry1="";
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $mail_reference,$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $mail_reference,$mailidArray);
                //print_r($folderArray);
                for($j=(count($folderArray[1])-1);$j>=0;$j--)
				{

					if ($folderArray[1][$j]==1) {
                        $select1->select("nesote_email_inbox_$tablenumber");
                    } elseif ($folderArray[1][$j]==3) {
                        $select1->select("nesote_email_sent_$tablenumber");
                    } elseif ($folderArray[1][$j]>=10) {
                        $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                    }
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select1->where("id=?",[$mailidArray[1][$j]]);
					$result1=$select1->query();

					if($row1=$select1->fetchRow($result1))
					{
						if(($flag==0)&&(($folderArray[1][$j]==3)))
						{

							$time=$row1[9];

							$object_id=$row1[0];$flag=1;
						}

						//							       if(($inb_flag==0)&&($folderArray[1][$j]==1))
						//									{
						//										$objects1="inbox_".$row1[0];
						//										$inb_flag=1;
						//									}
						//									else if(($sen_flag==0)&&($folderArray[1][$j]==3))
						//									{
						//										$objects2="sent_".$row1[0];
						//										$sen_flag=1;
						//									}
						//						            else if(($cus_flag==0)&&($folderArray[1][$j]>=10))
						//									{
						//										$objects3=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
						//										$cus_flag=1;
						//									}

							




						if($j==count($folderArray[1])-1)
						{

							for($i=0;$i<8;$i++)
							{
								if ($i==0) {
                                    $arry1.="{nesote_id}{nesote_,}";
                                } elseif ($i==5) {
                                    $arry1.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    $arry1.="unread_{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
							}
							$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
							$arry1.="{nesote_time}{nesote_,}";
							//$arry1.=$this->gettime($row1[9])."{nesote_,}";
							$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
							$arry1.=$row1[9]."{nesote_,}";
							$arry1.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							$arry1.=count($folderArray[1])."{nesote_,}";
							$arry1.="sent_{nesote}{nesote_,}";
							$arry1.="allobjects";




							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									//$arry.="{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="1{nesote_,}";
							$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="sent_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";



						}

						else
						{


							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    //echo $row1[$i]
                                    $arry.=$row1[$i]."{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="sent_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
					}
					$arry.="{nesote_ref}";




				}
                if ($se_flag==1) {
                    $all_objects.=$object1[$var].".";
                }
                //					$arry=str_replace("allobjects",$all_objects,$arry);
                //					$arry1=str_replace("allobjects",$all_objects,$arry1);
                $arry1=str_replace("{nesote_id}",$object_id,$arry1);
                $arry=str_replace("sent_{nesote}","sent_".$object_id,$arry);
                $arry1=str_replace("unread_{nesote_,}",$unread_flag."{nesote_,}",$arry1);
                $arry1=str_replace("sent_{nesote}","sent_".$object_id,$arry1);
                $time=$this->gettime($time);
                $arry1=str_replace("{nesote_time}",$time,$arry1);
                //					$arry=substr($arry,0,-12);
                //					$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
            } elseif ($object_new[0]=="spam") {
                $arry="";
                $arry1="";
                $spa_flag=0;
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $mail_reference,$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $mail_reference,$mailidArray);
                for($j=(count($folderArray[1])-1);$j>=0;$j--)
				{
					$select1->select("nesote_email_spam_$tablenumber");
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
					$select1->where("id=?",[$mailidArray[1][$j]]);
					$result1=$select1->query();
					if($row1=$select1->fetchRow($result1))
					{
							
						//						 if(($spa_flag==0)&&($folderArray[1][$j]==4))
						//							{
						//							$objects4="spam_".$row1[0];
						//							$spa_flag=1;
						//							}
						if($j==count($folderArray[1])-1)
						{ $object_id=$row1[0];
							
							
							

						for($i=0;$i<8;$i++)
						{
							if ($i==5) {
                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                            } elseif ($i==6) {
                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                            } else {
                                $arry1.=$row1[$i]."{nesote_,}";
                            }
						}
						$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
						$arry1.=$this->gettime($row1[9])."{nesote_,}";
						$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
						$arry1.=$row1[9]."{nesote_,}";
						$arry1.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
						$arry1.=count($folderArray[1])."{nesote_,}";
						//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
						$arry1.="spam_{nesote}{nesote_,}";
						$arry1.="allobjects";



						for($i=0;$i<(9);$i++)
						{
							if($i==6)
							{
								$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
								$externals=explode("{nesote_comma}",(string) $external);
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
							}
							else {
                                $arry.=$row1[$i]."{nesote_,}";
                            }
						}

						$arry.=$this->gettime($row1[9])."{nesote_,}";
						$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
						$arry.="1{nesote_,}";
						$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
						$arry.=$row1[6]."{nesote_,}";
						$arry.=$extnl_flg."{nesote_,}";
						$arry.=$row1[9]."{nesote_,}";
						$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
						//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
						$arry.="spam_{nesote}{nesote_,}";
						$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
						$arry.="allobjects";

							
						}
						else
						{

							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
							$arry.="spam_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
					}
					$arry.="{nesote_ref}";

				}
                if ($sp_flag==1) {
                    $all_objects.=$object1[$var].".";
                }
                //					    $arry=str_replace("allobjects",$all_objects,$arry);
                //					    $arry1=str_replace("allobjects",$all_objects,$arry1);
                $arry=str_replace("spam_{nesote}","spam_".$object_id,$arry);
                $arry1=str_replace("spam_{nesote}","spam_".$object_id,$arry1);
                //						$arry=substr($arry,0,-12);
                //						$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
            } elseif ($object_new[0]=="trash") {
                $arry="";
                $arry1="";
                $tra_flag=0;
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $mail_reference,$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $mail_reference,$mailidArray);
                for($j=(count($folderArray[1])-1);$j>=0;$j--)
				{
					$select1->select("nesote_email_trash_$tablenumber");
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
					$select1->where("id=?",[$mailidArray[1][$j]]);
					$result1=$select1->query();
					if($row1=$select1->fetchRow($result1))
					{
						//						 if(($tra_flag==0)&&($folderArray[1][$j]==5))
						//							{
						//							$objects5="trash_".$row1[0];
						//							$tra_flag=1;
						//							}
							
						if($j==count($folderArray[1])-1)
						{  $object_id=$row1[0];

						for($i=0;$i<8;$i++)
						{
							if ($i==5) {
                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),30)."{nesote_,}";
                            } elseif ($i==6) {
                                $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                            } else {
                                $arry1.=$row1[$i]."{nesote_,}";
                            }
						}
						$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
						$arry1.=$this->gettime($row1[9])."{nesote_,}";
						$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
						$arry1.=$row1[9]."{nesote_,}";
						$arry1.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
						$arry1.=count($folderArray[1])."{nesote_,}";
						//$arry1.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
						$arry1.="trash_{nesote}{nesote_,}";
						$arry1.="allobjects";



						for($i=0;$i<(9);$i++)
						{
							if($i==6)
							{
								$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
								$externals=explode("{nesote_comma}",(string) $external);
								$arry.=$externals[0]."{nesote_,}";
								$extnl_flg=$externals[1];
							}
							else {
                                $arry.=$row1[$i]."{nesote_,}";
                            }
						}

						$arry.=$this->gettime($row1[9])."{nesote_,}";
						$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
						$arry.="1{nesote_,}";
						$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
						$arry.=$row1[6]."{nesote_,}";
						$arry.=$extnl_flg."{nesote_,}";
						$arry.=$row1[9]."{nesote_,}";
						$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
						//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
						$arry.="trash_{nesote}{nesote_,}";
						$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
						$arry.="allobjects";

							
						}
						else
						{

							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							$arry.=$row1[6]."{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							//$arry.=$this->getfoldername($folderArray[1][$j])."_".$row1[0];
							$arry.="trash_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
					}
					$arry.="{nesote_ref}";

				}
                if ($tr_flag==1) {
                    $all_objects.=$object1[$var].".";
                }
                //					    $arry=str_replace("allobjects",$all_objects,$arry);
                //					    $arry1=str_replace("allobjects",$all_objects,$arry1);
                $arry=str_replace("trash_{nesote}","trash_".$object_id,$arry);
                $arry1=str_replace("trash_{nesote}","trash_".$object_id,$arry1);
                //						$arry=substr($arry,0,-12);
                //						$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
            } elseif (strpos($object_new[0],"ustom")==1) {
                $flag=0;
                $unread_flag=1;
                $cus_flag=0;
                $sen_flag=0;
                $arry="";
                $arry1="";
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $mail_reference,$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $mail_reference,$mailidArray);
                //print_r($folderArray);
                for($j=(count($folderArray[1])-1);$j>=0;$j--)
				{

					if ($folderArray[1][$j]>=10) {
                        $select1->select("nesote_email_customfolder_mapping_$tablenumber");
                    } else {
                        $select1->select("nesote_email_sent_$tablenumber");
                    }
					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,message_id,mail_references");
					$select1->where("id=?",[$mailidArray[1][$j]]);
					$result1=$select1->query();

					if($row1=$select1->fetchRow($result1))
					{     //echo $row1[0]."<br>";
							
						//						          if(($sen_flag==0)&&($folderArray[1][$j]==3))
						//									{
						//										$objects2="sent_".$row1[0];
						//										$sen_flag=1;
						//									}
						//						            else if(($cus_flag==0)&&($folderArray[1][$j]>=10))
						//									{
						//										$objects3=$this->getfoldernamenew($folderArray[1][$j])."_".$row1[0];
						//										$cus_flag=1;
						//									}
							
						if(($flag==0)&&(($folderArray[1][$j]>=10)))
						{

							$time=$row1[9];

							$object_id=$row1[0];

							$flag=1;
						}





						if($j==count($folderArray[1])-1)
						{

							for($i=0;$i<8;$i++)
							{
								if ($i==0) {
                                    $arry1.="{nesote_id}{nesote_,}";
                                } elseif ($i==5) {
                                    $arry1.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    $arry1.="unread_{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry1.=$row1[$i]."{nesote_,}";
                                }
							}
							$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
							$arry1.="{nesote_time}{nesote_,}";
							//$arry1.=$this->gettime($row1[9])."{nesote_,}";
							$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
							$arry1.=$row1[9]."{nesote_,}";
							$arry1.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							$arry1.=count($folderArray[1])."{nesote_,}";
							$arry1.="custom_{nesote}{nesote_,}";
							$arry1.="allobjects";




							for($i=0;$i<(9);$i++)
							{
								if($i==6)
								{
									$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
									$externals=explode("{nesote_comma}",(string) $external);
									$arry.=$externals[0]."{nesote_,}";
									//$arry.="{nesote_,}";
									$extnl_flg=$externals[1];
								}
								else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="1{nesote_,}";
							$arry.=$this->getfoldername($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="custom_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";



						}

						else
						{


							for($i=0;$i<(9);$i++)
							{
								if ($i==5) {
                                    $arry.=$this->substringMail($row1[$i],30)."{nesote_,}";
                                } elseif ($i==6) {
                                    $arry.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                                } elseif ($i==7) {
                                    //echo $row1[$i]
                                    $arry.=$row1[$i]."{nesote_,}";
                                    if ($row1[$i]==0) {
                                        $unread_flag=0;
                                    }
                                } else {
                                    $arry.=$row1[$i]."{nesote_,}";
                                }
							}

							$arry.=$this->gettime($row1[9])."{nesote_,}";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$j])."{nesote_,}";
							$arry.="0{nesote_,}";
							$arry.=$this->getfoldernamenew($folderArray[1][$j])."{nesote_,}";
							//$arry.=$row1[6]."{nesote_,}";
							$arry.="{nesote_,}";
							$arry.=$extnl_flg."{nesote_,}";
							$arry.=$row1[9]."{nesote_,}";
							$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
							$arry.="custom_{nesote}{nesote_,}";
							$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
							$arry.="allobjects";

						}
					}
					$arry.="{nesote_ref}";




				}
                if ($cu_flag==1) {
                    $all_objects.=$object1[$var].".";
                }
                //					$arry=str_replace("allobjects",$all_objects,$arry);
                //					$arry1=str_replace("allobjects",$all_objects,$arry1);
                $arry1=str_replace("{nesote_id}",$object_id,$arry1);
                $arry=str_replace("custom_{nesote}",$object_new[0]."_".$object_id,$arry);
                $arry1=str_replace("unread_{nesote_,}",$unread_flag."{nesote_,}",$arry1);
                $arry1=str_replace("custom_{nesote}",$object_new[0]."_".$object_id,$arry1);
                $time=$this->gettime($time);
                $arry1=str_replace("{nesote_time}",$time,$arry1);
                //					$arry=substr($arry,0,-12);
                //					$returnstr.=$arry1."{nesote_:}".$arry."{nesote_separator}";
            }







		}
		//echo $arry."--------------".$all_objects;
		$arry=str_replace("allobjects",$all_objects,$arry);
		$arry1=str_replace("allobjects",$all_objects,$arry1);

		$arry=substr($arry,0,-12);
			
		return $returnstr . ($arry1 . "{nesote_:}" . $arry);
	}


	function movetoAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$foldername=$_POST['foldernane'];
			$mailid=$_POST['mailid'];
			$tofoldername=$_POST['tofoldernane'];	//echo $tofoldername;

			//	if((strpos($foldername,"earch")==1) || (strpos($foldername,"rom")==1))
			//{
			//$foldername='search';
			//} echo $foldername;
			$folderid=$this->getfolderid($foldername);//echo $folderid."===";

			$foldername=$this->getfoldername($folderid);

			$userid=$this->getId();


			$db=new NesoteDALController();
			$db2=new NesoteDALController();
			$db3=new NesoteDALController();


			$returnstr="";
			$tofolderid=$this->getfolderid($tofoldername);
			$tofoldrnam=$this->getfoldername($tofolderid);
			//	echo $tofoldername;exit;
			if($tofoldername=="")
			{

				$backref_string=$_POST["backref_string"];
				if($backref_string!="")///////////notspam/////////
				{
					$backref_string1=explode(",",(string) $backref_string);
                    $counter = count($backref_string1);
					for($i=0;$i<$counter;$i++)
					{
						$backref_string2=explode("?",$backref_string1[$i]);
						$back_folder=$this->getfolderid($backref_string2[1]);
						$db->select("nesote_email_spam_$tablenumber");
						$db->fields("mail_references");
						$db->where("id=?",$backref_string2[0]);
						$rs=$db->query();
						$no=$db->numRows($rs);
						$row=$db->fetchRow($rs);
						$arry="";$arry1="";$in_flag=0;$se_flag=0;$tr_flag=0;$cu_flag=0;$all_objects="";
						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						//  print_r($folderArray);print_r($mailidArray);
						for($j=(count($folderArray[1])-1);$j>=0;$j--)
						{
							//					     	 $array_count=count($folderArray[0]);//echo $array_count;
							//				             $r=$array_count-1;
							//				             while($r>=0)
							//				             {
							$db2->select("nesote_email_spam_$tablenumber");
							$db2->fields("*");
							$db2->where("id=?",[$mailidArray[1][$j]]);
							$res2=$db2->query();
							$result2=$db2->fetchRow($res2);
							//echo "backref".$result2[15];
							if (($result2[15]<10)&&($result2[15]!=5)) {
                                if ($result2[15]==1) {
                                    $db2->insert("nesote_email_inbox_$tablenumber");
                                } elseif ($result2[15]==3) {
                                    $db2->insert("nesote_email_sent_$tablenumber");
                                }
                                $db2->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
                                $db2->values([$userid,$result2[2],$result2[3],$result2[4],$result2[5],$result2[6],$result2[7],$result2[8],$result2[9],$result2[10],$result2[11],$result2[12],$result2[13]]);
                            } elseif ($result2[15]>=10) {
                                $db2->insert("nesote_email_customfolder_mapping_$tablenumber");
                                $db2->fields("folderid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
                                $db2->values([$back_folder,$result2[2],$result2[3],$result2[4],$result2[5],$result2[6],$result2[7],$result2[8],$result2[9],$result2[10],$result2[11],$result2[12],$result2[13]]);
                            }
							//	                        	else if($result2[15]==5)
							//	                        	{
							//	                        	$db2->insert("nesote_email_trash_$tablenumber");
							//								$db2->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,	backreference");
							//								$db2->values(array($userid,$result2[2],$result2[3],$result2[4],$result2[5],$result2[6],$result2[7],$result2[8],$result2[9],$result2[10],$result2[11],$result2[12],$result2[13],4));
							//	                        	}
							$db2->query();//echo $db2->getQuery();
							$crnt_id=$db2->lastInsert();



							////deleting from blacklist mail////////

							preg_match('/<(.+?)>/i',(string) $result2[2],$fromid);
							if ($fromid[1]=="") {
                                preg_match('/&lt;(.+?)&gt;/i',(string) $result2[2],$fromid);
                            }
							if ($fromid[1]=="") {
                                preg_match('/&amp;&lt;(.+?)&amp;&gt;/i',(string) $result2[2],$fromid);
                            }
$tot=$db->total("nesote_email_blacklist_mail","mailid=? and clientid=?",[$fromid[1],$userid]);
if($tot>0)
{
							$db->delete("nesote_email_blacklist_mail");
							$db->where("clientid=? and mailid=?",[$userid,$fromid[1]]);
							$db->query();
}

							$db2->select("nesote_email_attachments_$tablenumber");
							$db2->fields("id,name");
							$db2->where("folderid=? and mailid=? and userid=?",[4,$mailidArray[1][$j],$userid]);
							$res3=$db2->query();
							$num3=$db2->numRows($res3);
							if ($num3==0) {
                                $attach="";
                            } else
							{
								$attach="<img src=\"images/attachment.png\" border=\"0\">";
								$body=str_replace("attachments/4/".$tablenumber."/".$mailidArray[1][$j],"attachments/".$result2[15]."/".$tablenumber."/".$crnt_id,$result2[7]);
								if ($result2[15]==1) {
                                    $db2->update("nesote_email_inbox_$tablenumber");
                                } elseif ($result2[15]==3) {
                                    $db2->update("nesote_email_sent_$tablenumber");
                                } elseif ($result2[15]>=10) {
                                    $db2->update("nesote_email_customfolder_mapping_$tablenumber");
                                }
								$db2->set("body=?",$body);
								$db2->where("id=?",$crnt_id);
								$resu2=$db2->query();
								while($result3=$db2->fetchRow($res3))
								{
									$db3->update("nesote_email_attachments_$tablenumber");
									$db3->set("folderid=?,mailid=?",[$result2[15],$crnt_id]);
									$db3->where("id=?",[$result3[0]]);
									$rs9=$db3->query();

									if((is_dir("attachments/".$result2[15]))!=TRUE)
									{
										mkdir("attachments/".$result2[15],0777);
									}
									if((is_dir("attachments/".$result2[15]."/".$tablenumber))!=TRUE)
									{
										mkdir("attachments/".$result2[15]."/".$tablenumber,0777);
									}

									if((is_dir("attachments/".$result2[15]."/".$tablenumber."/".$crnt_id))!=TRUE)
									{
										mkdir("attachments/".$result2[15]."/".$tablenumber."/".$crnt_id,0777);
									}

									copy("attachments/4/".$tablenumber."/".$mailidArray[1][$j]."/".$result3[1],"attachments/".$result2[15]."/".$tablenumber."/".$crnt_id."/".$result3[1]);
									unlink("attachments/4/".$tablenumber."/".$mailidArray[1][$j]."/".$result3[1]);
									rmdir("attachments/4/".$tablenumber."/".$mailidArray[1][$j]);
								}
							}
							$back_foldername=$this->getfoldername($result2[15]);

							$new_references=$this->updatedreferences($result2[14],'spam',$mailidArray[1][$j],$back_foldername,$crnt_id);
							

							$db2->delete("nesote_email_spam_$tablenumber");
							$db2->where("id=?",$mailidArray[1][$j]);
							$db2->query();


							//				             	$r--;
							//				             }


						}


						$returnString=$this->getarray($new_references);
						$returnString2.=$returnString."{nesote_separator}";

					}
					$returnString2=substr($returnString2,0,-18);
					echo $returnString2;exit;

				}
				else////////////////////discard drafts/////////////////////////////////
				{
					$mailidz=explode(",",(string) $mailid);
                    $counter = count($mailidz);
					for($i=0;$i<$counter;$i++)
					{
						$mailids=explode("?",$mailidz[$i]);
						$db->select("nesote_email_attachments_$tablenumber");
						$db->fields("id,name");
						$db->where("folderid=? and mailid=? and userid=?",[2,$mailids[0],$userid]);
						$res=$db->query();
						while($result=$db->fetchRow($res))
						{
							unlink("attachments/2/".$tablenumber."/".$mailids[0]."/".$result[1]);
							rmdir("attachments/2/".$tablenumber."/".$mailids[0]);
							$db2->delete("nesote_email_attachments_$tablenumber");
							$db2->where("id=?",$result[0]);
							$db2->query();
						}

						$db->delete("nesote_email_draft_$tablenumber");
						$db->where("id=?",[$mailids[0]]);
						$db->query();

					}

				}





			}
			else///////////////////moveto//////
			{
					
				$mailidz=explode(",",(string) $mailid);
                $counter = count($mailidz);
				for($i=0;$i<$counter;$i++)
				{
					$mailids=explode("?",$mailidz[$i]);

					if ($folderid==1) {
                        $db->select("nesote_email_inbox_$tablenumber");
                    } elseif ($folderid==2) {
                        $db->select("nesote_email_draft_$tablenumber");
                    } elseif ($folderid==3) {
                        $db->select("nesote_email_sent_$tablenumber");
                    } elseif ($folderid==4) {
                        $db->select("nesote_email_spam_$tablenumber");
                    } elseif ($folderid==5) {
                        $db->select("nesote_email_trash_$tablenumber");
                    } else {
                        $db->select("nesote_email_customfolder_mapping_$tablenumber");
                    }
					$db->fields("mail_references");
					$db->where("id=?",$mailids[0]);
					$rs=$db->query();
					$row=$db->fetchRow($rs);
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);//print_r($folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);//print_r($mailidArray);
					$array_count=count($folderArray[0]);
					$r=$array_count-1;
					while($r>=0)
					{ //echo "hi.....";
						$tofolderid=$this->getfolderid($tofoldername);
						$update_ref_flag=0;
						// echo "tofoldername".$tofoldername;echo "tofolderid".$tofolderid;
						if (($tofoldername=="spam")||($tofoldername=="trash")) {
                            if ($folderArray[1][$r]==1) {
                                $db->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$r]==3) {
                                $db->select("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$r]==4) {
                                $db->select("nesote_email_spam_$tablenumber");
                            } elseif ($folderArray[1][$r]==5) {
                                $db->select("nesote_email_trash_$tablenumber");
                            } else {
                                $db->select("nesote_email_customfolder_mapping_$tablenumber");
                            }
                            $db->fields("*");
                            $db->where("id=?",$mailidArray[1][$r]);
                            $rs2=$db->query();
                            $row2=$db->fetchRow($rs2);
                            $db->select("nesote_email_attachments_$tablenumber");
                            $db->fields("id");
                            $db->where("folderid=? and mailid=? and userid=?",[$folderArray[1][$r],$mailidArray[1][$r],$userid]);
                            $rs14=$db->query();
                            $row14=$db->numRows($rs14);
                            $attach = $row14 == 0 ? "" : "<img src=\"images/attachment.png\" border=\"0\">";
                            if ($folderArray[1][$r]==4) {
                                $backRef=$row2[15];
                            } elseif ($folderArray[1][$r]==1) {
                                $backRef=1;
                            } elseif ($folderArray[1][$r]==3) {
                                $backRef=3;
                            } elseif ($folderArray[1][$r]==5) {
                                $backRef=$row2[15];
                            } elseif ($folderArray[1][$r]>=10) {
                                $backRef=$folderArray[1][$r];
                            }
                            if (($tofoldername=='trash')&&($folderArray[1][$r]!=5)) {
                                $db->insert("nesote_email_trash_$tablenumber");
                            } elseif ($tofoldername=='spam') {
                                $db->insert("nesote_email_spam_$tablenumber");
                            }
                            $db->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
                            $db->values([$userid,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13],$backRef]);
                            $result2=$db->query();
                            $crnt_id=$db->lastInsert();
                            if(($tofoldername=='spam'))
							{
								
						$address=str_replace("\\","",$row2[2]);
						preg_match("/(.+?)<(.+?)>/i",$address,$m);
						if (count($m[2])==0) {
                            preg_match("/(.+?)&lt;(.+?)&gt;/i",$address,$m);
                        }
						if($m[2]=="")
						{
							$m[2]=$address;
						}
						
						
								$tot=$db->total("nesote_email_blacklist_mail","mailid=? and clientid=?",[$m[2],$userid]);
								if($tot==0)
								{
								    $db->insert("nesote_email_blacklist_mail");
									$db->fields("mailid,clientid");
									$db->values([$m[2],$userid]);
								    $db->query();
								}
							}
                            $body=str_replace("attachments/".$folderArray[1][$r]."/".$tablenumber."/".$mailidArray[1][$r],"attachments/".$tofolderid."/".$tablenumber."/".$crnt_id,$row2[7]);
                            if (($tofoldername=='trash')&&($folderArray[1][$r]!=5)) {
                                $db->update("nesote_email_trash_$tablenumber");
                            } elseif ($tofoldername=='spam') {
                                $db->update("nesote_email_spam_$tablenumber");
                            }
                            $db->set("body=?",$body);
                            $db->where("id=?",$crnt_id);
                            $result2=$db->query();
                            $update_ref_flag=1;
                            $new_references=$this->updatedreferences($row2[14],$this->getfoldernamenew($folderArray[1][$r]),$mailidArray[1][$r],$tofoldername,$crnt_id);
                            if ($folderArray[1][$r]==1) {
                                $db->delete("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$r]==3) {
                                $db->delete("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$r]==4) {
                                $db->delete("nesote_email_spam_$tablenumber");
                            } elseif ($folderArray[1][$r]==5) {
                                $db->delete("nesote_email_trash_$tablenumber");
                            } elseif ($folderArray[1][$r]>=10) {
                                $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                            }
                            $db->where("id=?",$mailidArray[1][$r]);
                            $rs1=$db->query();
                        } elseif (($tofolderid==1)||($tofolderid>=10)) {
                            if ($folderArray[1][$r]==1) {
                                $db->select("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$r]==3) {
                                $db->select("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$r]==5) {
                                $db->select("nesote_email_trash_$tablenumber");
                            } elseif ($folderArray[1][$r]>=10) {
                                $db->select("nesote_email_customfolder_mapping_$tablenumber");
                            }
                            $db->fields("*");
                            $db->where("id=?",$mailidArray[1][$r]);
                            $rs2=$db->query();
                            //echo "/////".$db->getQuery();
                            $row2=$db->fetchRow($rs2);
                            $db->select("nesote_email_attachments_$tablenumber");
                            $db->fields("id");
                            $db->where("folderid=? and mailid=? and userid=?",[$folderArray[1][$r],$mailidArray[1][$r],$userid]);
                            $rs14=$db->query();
                            $row14=$db->numRows($rs14);
                            $attach = $row14 == 0 ? "" : "<img src=\"images/attachment.png\" border=\"0\">";
                            if($foldername=='trash')
							{
								$backRef=$row2[15];

								$tofolderids = $backRef == 3 ? 3 : $tofolderid;
									
							}
                            if ($foldername=='trash') {
                                if ($tofolderids==1) {
                                    $db->insert("nesote_email_inbox_$tablenumber");
                                    $db->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
                                    $db->values([$userid,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13]]);
                                } elseif ($tofolderids==3) {
                                    $db->insert("nesote_email_sent_$tablenumber");
                                    $db->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
                                    $db->values([$userid,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13]]);
                                } elseif ($tofolderids>=10) {
                                    $db->insert("nesote_email_customfolder_mapping_$tablenumber");
                                    $db->fields("folderid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
                                    $db->values([$tofolderids,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13]]);
                                }
                                $result2=$db->query();
                                $crnt_id=$db->lastInsert();
                                $tofolderid=$tofolderids;
                                $update_ref_flag=1;
                                $body=str_replace("attachments/".$folderArray[1][$r]."/".$tablenumber."/".$mailidArray[1][$r],"attachments/".$tofolderids."/".$tablenumber."/".$crnt_id,$row2[7]);
                                if ($tofolderids==1) {
                                    $db->update("nesote_email_inbox_$tablenumber");
                                } elseif ($tofolderids==3) {
                                    $db->update("nesote_email_sent_$tablenumber");
                                } elseif ($tofolderids>=10) {
                                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                                }
                                $db->set("body=?",$body);
                                $db->where("id=?",$crnt_id);
                                $result2=$db->query();
                                $tofoldername1=$this->getfoldername($tofolderids);
                                // echo $tofoldername1;
                                $new_references=$this->updatedreferences($row2[14],$this->getfoldernamenew($folderArray[1][$r]),$mailidArray[1][$r],$tofoldername1,$crnt_id);
                            } elseif ($foldername=='inbox') {
                                if($tofolderid>=10)
								{
									if($folderArray[1][$r]==1)
									{
										$db->insert("nesote_email_customfolder_mapping_$tablenumber");
										$db->fields("folderid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
										$db->values([$tofolderid,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13]]);
										$result2=$db->query();
										$crnt_id=$db->lastInsert();$update_ref_flag=1;
											
										$body=str_replace("attachments/".$folderArray[1][$r]."/".$tablenumber."/".$mailidArray[1][$r],"attachments/".$tofolderid."/".$tablenumber."/".$crnt_id,$row2[7]);
									}
									if ($tofolderid>=10) {
                                        $db->update("nesote_email_customfolder_mapping_$tablenumber");
                                    }
									$db->set("body=?",$body);
									$db->where("id=?",$crnt_id);
									$result2=$db->query();


								}
                                $tofoldername1=$this->getfoldername($tofolderid);
                                if ($update_ref_flag==1) {
                                    $new_references=$this->updatedreferences($row2[14],$this->getfoldernamenew($folderArray[1][$r]),$mailidArray[1][$r],$tofoldername1,$crnt_id);
                                }
                            } elseif ($folderid>=10) {
                                if ($tofolderid==1) {
                                    if($folderArray[1][$r]>=10)
									{
										$db->insert("nesote_email_inbox_$tablenumber");
										$db->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
										$db->values([$userid,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13]]);
										$result2=$db->query();// echo $db->getQuery();
										$crnt_id=$db->lastInsert();$update_ref_flag=1;
											
											
									}
                                } elseif (($tofolderid>=10)&&($folderid!=$tofolderid)) {
                                    $db->insert("nesote_email_customfolder_mapping_$tablenumber");
                                    $db->fields("folderid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id");
                                    $db->values([$tofolderid,$row2[2],$row2[3],$row2[4],$row2[5],$row2[6],$row2[7],$row2[8],$row2[9],$row2[10],$row2[11],$row2[12],$row2[13]]);
                                    $result2=$db->query();
                                    $crnt_id=$db->lastInsert();
                                    $update_ref_flag=1;
                                }
                                if($update_ref_flag==1)
								{
									$body=str_replace("attachments/".$folderArray[1][$r]."/".$tablenumber."/".$mailidArray[1][$r],"attachments/".$tofolderid."/".$tablenumber."/".$crnt_id,$row2[7]);
									 
									if ($tofolderid==1) {
                                        $db->update("nesote_email_inbox_$tablenumber");
                                    } elseif ($tofolderid>=10) {
                                        $db->update("nesote_email_customfolder_mapping_$tablenumber");
                                    }
									$db->set("body=?",$body);
									$db->where("id=?",$crnt_id);
									$result2=$db->query();//echo $db->getQuery();
								}
                                $tofoldername1=$this->getfoldername($tofolderid);
                                if ($update_ref_flag==1) {
                                    $new_references=$this->updatedreferences($row2[14],$this->getfoldernamenew($folderArray[1][$r]),$mailidArray[1][$r],$tofoldername1,$crnt_id);
                                }
                            }
                            //                                    $tofoldername1=$this->getfoldername($tofolderid);
                            //									if($update_ref_flag==1)
                            //									$new_references=$this->updatedreferences($row2[14],$this->getfoldernamenew($folderArray[1][$r]),$mailidArray[1][$r],$tofoldername1,$crnt_id);
                            if ($folderArray[1][$r]==1) {
                                $db->delete("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$r]>=10) {
                                $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                            } elseif ($folderArray[1][$r]==5) {
                                $db->delete("nesote_email_trash_$tablenumber");
                            }
                            $db->where("id=?",$mailidArray[1][$r]);
                            $rs1=$db->query();
                        }
						if($update_ref_flag==1)
						{
							$db->select("nesote_email_attachments_$tablenumber");
							$db->fields("id,name");
							$db->where("folderid=? and mailid=? and userid=?",[$folderArray[1][$r],$mailidArray[1][$r],$userid]);
							$rs4=$db->query();
							while($row7=$db->fetchRow($rs4))
							{
								$db2->update("nesote_email_attachments_$tablenumber");
								$db2->set("folderid=?,mailid=?",[$tofolderid,$crnt_id]);
								$db2->where("id=?",[$row7[0]]);
								$rs9=$db2->query();

								if((is_dir("attachments/".$tofolderid))!=TRUE)
								{
									mkdir("attachments/".$tofolderid,0777);
								}
								if((is_dir("attachments/".$tofolderid."/".$tablenumber))!=TRUE)
								{
									mkdir("attachments/".$tofolderid."/".$tablenumber,0777);
								}

								if((is_dir("attachments/".$tofolderid."/".$tablenumber."/".$crnt_id))!=TRUE)
								{
									mkdir("attachments/".$tofolderid."/".$tablenumber."/".$crnt_id,0777);
								}

								copy("attachments/".$folderArray[1][$r]."/".$tablenumber."/".$mailidArray[1][$r]."/".$row7[1],"attachments/".$tofolderid."/".$tablenumber."/".$crnt_id."/".$row7[1]);
								unlink("attachments/".$folderArray[1][$r]."/".$tablenumber."/".$mailidArray[1][$r]."/".$row7[1]);
								rmdir("attachments/".$folderArray[1][$r]."/".$tablenumber."/".$mailidArray[1][$r]);
							}

						}
							
							
							
							
						$r--;
							
					}

					$returnString=$this->getarray($new_references);
					$returnString2.=$returnString."{nesote_separator}";
				}
			}
			$returnString2=substr($returnString2,0,-18);
			echo $returnString2;exit;

		}
	}


	function updatedreferences($reference,$from,$frm_id,$to,$to_id)
	{  //echo $reference."///".$from."////".$frm_id."////".$to."////".$to_id;
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		$from=$this->getfolderid($from);
		$to=$this->getfolderid($to);
		$reference=str_replace("<mailid>".$frm_id."</mailid><folderid>".$from."</folderid>","<mailid>".$to_id."</mailid><folderid>".$to."</folderid>",$reference);
		preg_match_all('/<folderid>(.+?)<\/folderid>/i',$reference,$folderArray);
		preg_match_all('/<mailid>(.+?)<\/mailid>/i',$reference,$mailidArray);
		$array_count=count($folderArray[1]);
		$r=$array_count-1;
		$md5_references=md5($reference);
		while($r>=0)
		{
			$folderArray[1][$r]=$this->getfoldername($folderArray[1][$r]);
			$db5=new NesoteDALController();
			if ($folderArray[1][$r]=='inbox') {
                $db5->update("nesote_email_inbox_$tablenumber");
            } elseif ($folderArray[1][$r]=='draft') {
                $db5->update("nesote_email_draft_$tablenumber");
            } elseif ($folderArray[1][$r]=='sent') {
                $db5->update("nesote_email_sent_$tablenumber");
            } elseif ($folderArray[1][$r]=='spam') {
                $db5->update("nesote_email_spam_$tablenumber");
            } elseif ($folderArray[1][$r]=='trash') {
                $db5->update("nesote_email_trash_$tablenumber");
            } else {
                $db5->update("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db5->set("mail_references=?,md5_references=?",[$reference,$md5_references]);
			$db5->where("id=?",$mailidArray[1][$r]);
			$rs5=$db5->query();
			$r--;
		}
		return $reference;
	}

	function readAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$foldername=$_POST['foldernane'];
			$mailid=$_POST['mailid'];
			//echo $mailid.$foldername;
			$mailid=substr((string) $mailid,0,-1);
			$type=$_POST['type'];
			//echo $foldername;

			$mailids=explode(",",$mailid);
            $counter = count($mailids);

			for($i=0;$i<$counter;$i++)
			{
				//echo $mailids[$i];
				if(str_contains($mailids[$i],">"))
				{
					$se_object=explode(">",$mailids[$i]);
					$maild1=$se_object[1];
				}
				else
				{
					$maild1=$mailids[$i];
				}
				$mailid1=explode("_",$maild1);
				//if(substr($foldername,0,6)=="search")
				$folderid=$this->getfolderid($mailid1[0]);
				//else
				//$folderid=$this->getfolderid($foldername);
				if($type==1)
				{



					//					$db1=new NesoteDALController();
					//					if($folderid==1)
					//					$db1->update("nesote_email_inbox_$tablenumber");
					//					else if($folderid==2)
					//					$db1->update("nesote_email_draft_$tablenumber");
					//					else if($folderid==3)
					//					$db1->update("nesote_email_sent_$tablenumber");
					//					else if($folderid==4)
					//					$db1->update("nesote_email_spam_$tablenumber");
					//					else if($folderid==5)
					//					$db1->update("nesote_email_trash_$tablenumber");
					//					else
					//					$db1->update("nesote_email_customfolder_mapping_$tablenumber");
					//					$db1->set("readflag=?",1);
					//					$db1->where("id=?",$mailid1[1]);
					//					$rs1=$db1->query();
					//					$row1=$db1->fetchRow($rs1);

					$db4=new NesoteDALController();
					if ($folderid==1) {
                        $db4->select("nesote_email_inbox_$tablenumber");
                    } elseif ($folderid==2) {
                        $db4->select("nesote_email_draft_$tablenumber");
                    } elseif ($folderid==3) {
                        $db4->select("nesote_email_sent_$tablenumber");
                    } elseif ($folderid==4) {
                        $db4->select("nesote_email_spam_$tablenumber");
                    } elseif ($folderid==5) {
                        $db4->select("nesote_email_trash_$tablenumber");
                    } else {
                        $db4->select("nesote_email_customfolder_mapping_$tablenumber");
                    }
					$db4->fields("mail_references");
					$db4->where("id=? ",[$mailid1[1]]);
					$rs4=$db4->query();//echo $db4->getQuery();
					$row4=$db4->fetchRow($rs4);

				}
				else
				{
					$db4=new NesoteDALController();
					if ($folderid==1) {
                        $db4->select("nesote_email_inbox_$tablenumber");
                    } elseif ($folderid==2) {
                        $db4->select("nesote_email_draft_$tablenumber");
                    } elseif ($folderid==3) {
                        $db4->select("nesote_email_sent_$tablenumber");
                    } elseif ($folderid==4) {
                        $db4->select("nesote_email_spam_$tablenumber");
                    } elseif ($folderid==5) {
                        $db4->select("nesote_email_trash_$tablenumber");
                    } else {
                        $db4->select("nesote_email_customfolder_mapping_$tablenumber");
                    }
					$db4->fields("mail_references");
					$db4->where("id=? ",[$mailid1[1]]);
					$rs4=$db4->query();//echo "--".$db4->getQuery();
					$row4=$db4->fetchRow($rs4);
				}
				preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row4[0],$folderArray);
				preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row4[0],$mailidArray);
				$array_count=count($folderArray[1]);
				$r=$array_count-1;
				while($r>=0)
				{
					if($type==0)
					{
						$db1=new NesoteDALController();
						if ($folderArray[1][$r]==1) {
                            $db1->update("nesote_email_inbox_$tablenumber");
                        } elseif ($folderArray[1][$r]==2) {
                            $db1->update("nesote_email_draft_$tablenumber");
                        } elseif ($folderArray[1][$r]==3) {
                            $db1->update("nesote_email_sent_$tablenumber");
                        } elseif ($folderArray[1][$r]==4) {
                            $db1->update("nesote_email_spam_$tablenumber");
                        } elseif ($folderArray[1][$r]==5) {
                            $db1->update("nesote_email_trash_$tablenumber");
                        } else {
                            $db1->update("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db1->set("readflag=?",0);
						$db1->where("id=?",$mailidArray[1][$r]);
						$rs1=$db1->query();//echo $db1->getQuery();
						//$row1=$db1->fetchRow($rs1);
					}
					else
					{
						$db1=new NesoteDALController();
						if ($folderArray[1][$r]==1) {
                            $db1->update("nesote_email_inbox_$tablenumber");
                        } elseif ($folderArray[1][$r]==2) {
                            $db1->update("nesote_email_draft_$tablenumber");
                        } elseif ($folderArray[1][$r]==3) {
                            $db1->update("nesote_email_sent_$tablenumber");
                        } elseif ($folderArray[1][$r]==4) {
                            $db1->update("nesote_email_spam_$tablenumber");
                        } elseif ($folderArray[1][$r]==5) {
                            $db1->update("nesote_email_trash_$tablenumber");
                        } else {
                            $db1->update("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db1->set("readflag=?",1);
						$db1->where("id=?",$mailidArray[1][$r]);
						$rs1=$db1->query();//echo $db1->getQuery();
					}
					$r--;
				}
			}
		}
		echo "";exit;

	}

	

	function starAction()///id string
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);
				
			$db=new NesoteDALController();
			$foldernames=$_POST['foldernane'];
			$mailid=$_POST['mailid'];
			$object=$_POST['object'];
			$type=$_POST['type'];
			$flag=$_POST['flag'];
			$object=substr((string) $object,0,-1);
				
				
			$objects=explode(",",$object);
            //$objects=str_replace("starred:","",$objects);
            $counter = count($objects);
				
			//$objects=str_replace("starred:","",$objects);
			for($i=0;$i<$counter;$i++)
			{

				if (str_contains($objects[$i],">")) {
                    $se_object=explode(">",$objects[$i]);
                    $new_object=$se_object[1];
                } elseif (str_contains($objects[$i],":")) {
                    $st_object=explode(":",$objects[$i]);
                    $new_object=$st_object[1];
                } else
				{
					$new_object=$objects[$i];
				}
				$new_objects=explode("_",$new_object);
				$folderid=$this->getfolderid($new_objects[0]);

				if ($folderid==1) {
                    $db->select("nesote_email_inbox_$tablenumber");
                } elseif ($folderid==2) {
                    $db->select("nesote_email_draft_$tablenumber");
                } elseif ($folderid==3) {
                    $db->select("nesote_email_sent_$tablenumber");
                } elseif ($folderid==4) {
                    $db->select("nesote_email_spam_$tablenumber");
                } else {
                    $db->select("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->fields("starflag");
				$db->where("id=?",$new_objects[1]);
				$rs8=$db->query();
				while($row8=$db->fetchRow($rs8))
				{
					if ($row8[0]==1) {
                        $star += 1;
                    } else {
                        $unstar += 1;
                    }
				}



				if($flag==1)
				{
					if($type==1)
					{
						if ($folderid==1) {
                            $db->update("nesote_email_inbox_$tablenumber");
                        } elseif ($folderid==2) {
                            $db->update("nesote_email_draft_$tablenumber");
                        } elseif ($folderid==3) {
                            $db->update("nesote_email_sent_$tablenumber");
                        } elseif ($folderid==4) {
                            $db->update("nesote_email_spam_$tablenumber");
                        } else {
                            $db->update("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->set("starflag=?",1);
						$db->where("id=?",$new_objects[1]);
						$db->query();//echo $db->getQuery();


					}
					else
					{
						if ($folderid==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderid==2) {
                            $db->select("nesote_email_draft_$tablenumber");
                        } elseif ($folderid==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folderid==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } else {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("mail_references");
						$db->where("id=?",$new_objects[1]);
						$res=$db->query();
						$row=$db->fetchRow($res);
						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						for($j=(count($folderArray[1])-1);$j>=0;$j--)
						{
							if ($folderArray[1][$j]==1) {
                                $db->update("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==2) {
                                $db->update("nesote_email_draft_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $db->update("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$j]==4) {
                                $db->update("nesote_email_spam_$tablenumber");
                            } else {
                                $db->update("nesote_email_customfolder_mapping_$tablenumber");
                            }
							$db->set("starflag=?",0);
							$db->where("id=?",$mailidArray[1][$j]);
							$rs1=$db->query();//echo $db->getQuery();
								


						}


					}
				}
				else
				{
						
					if ($folderid==1) {
                        $db->select("nesote_email_inbox_$tablenumber");
                    } elseif ($folderid==2) {
                        $db->select("nesote_email_draft_$tablenumber");
                    } elseif ($folderid==3) {
                        $db->select("nesote_email_sent_$tablenumber");
                    } elseif ($folderid==4) {
                        $db->select("nesote_email_spam_$tablenumber");
                    } else {
                        $db->select("nesote_email_customfolder_mapping_$tablenumber");
                    }
					$db->fields("mail_references");
					$db->where("id=?",$new_objects[1]);
					$res=$db->query();
					$row=$db->fetchRow($res);
					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					for($j=(count($folderArray[1])-1);$j>=0;$j--)
					{
						if ($type==1) {
                            if($mailid==$mailidArray[1][$j])
							{
								if ($folderArray[1][$j]==1) {
                                    $db->update("nesote_email_inbox_$tablenumber");
                                } elseif ($folderArray[1][$j]==2) {
                                    $db->update("nesote_email_draft_$tablenumber");
                                } elseif ($folderArray[1][$j]==3) {
                                    $db->update("nesote_email_sent_$tablenumber");
                                } elseif ($folderArray[1][$j]==4) {
                                    $db->update("nesote_email_spam_$tablenumber");
                                } else {
                                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                                }
								$db->set("starflag=?",1);
								$db->where("id=?",$mailid);
								$db->query();//echo $db->getQuery();
							}
                        } elseif ($mailid==$mailidArray[1][$j]) {
                            if ($folderArray[1][$j]==1) {
                                $db->update("nesote_email_inbox_$tablenumber");
                            } elseif ($folderArray[1][$j]==2) {
                                $db->update("nesote_email_draft_$tablenumber");
                            } elseif ($folderArray[1][$j]==3) {
                                $db->update("nesote_email_sent_$tablenumber");
                            } elseif ($folderArray[1][$j]==4) {
                                $db->update("nesote_email_spam_$tablenumber");
                            } else {
                                $db->update("nesote_email_customfolder_mapping_$tablenumber");
                            }
                            $db->set("starflag=?",0);
                            $db->where("id=?",$mailid);
                            $db->query();
                            //echo $db->getQuery();
                        }
					}
				}

			}
			$starCookie=$_COOKIE['starred'];
			if ($type==1) {
                $starCookie1=$starCookie+$unstar;
            } elseif ($starCookie>0) {
                $starCookie1=$starCookie-$star;
            }
			setcookie('starred',$starCookie1, ['expires' => 0, 'path' => "/"]);
			echo $starCookie1;exit;
				
				
				
				
		}






	}


	function searchmailsAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{

			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$keyword=$this->getParam(1);
			$folderid=$this->getParam(2);
			$userid=$this->getId();
			$var=0;$arry="";
			if ($folderid==0) {
                $db4=new NesoteDALController();
                $db4->select("nesote_email_inbox_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(1).":;,///";
					$var++;
				}
                $db4->select("nesote_email_draft_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and just_insert=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,0]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(2).":;,///";
					$var++;
				}
                $db4->select("nesote_email_sent_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(3).":;,///";
					$var++;

				}
                $db4->select("nesote_email_spam_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(4).":;,///";
					$var++;
				}
                $db4->select("nesote_email_trash_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(5).":;,///";
					$var++;
				}
                $db=new NesoteDALController();
                $db->select("nesote_email_customfolder");
                $db->fields("id");
                $db->where("userid=?",$userid);
                $rs=$db->query();
                while($rw=$db->fetchRow($rs))
				{
					$db4->select("nesote_email_customfolder_mapping_$tablenumber");
					$db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,folderid,mail_references");
					$db4->where("folderid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$rw[0]]);
					$rs4=$db4->query();
					while($row1=$db4->fetchRow($rs4))
					{
						for($i=0;$i<9;$i++)
						{
							if ($i==5) {
                                $arry.=html_entity_decode((string) $row1[$i]).":;,";
                            } elseif ($i==6) {
                                $arry.=html_entity_decode((string) $row1[$i]).":;,";
                            } else {
                                $arry.=$row1[$i].":;,";
                            }
						}
						$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
						$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
						$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
						$arry.=$row1[count($fieldsArry)-3].":;,";
						$arry.=$this->getfoldername($folderid).":;,///";
						$var++;
					}
				}
            } elseif ($folderid==6) {
                $db4=new NesoteDALController();
                $db4->select("nesote_email_inbox_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(1).":;,///";
					$var++;
				}
                $db4->select("nesote_email_draft_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and just_insert=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,0,1]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(2).":;,///";
					$var++;
				}
                $db4->select("nesote_email_sent_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(3).":;,///";
					$var++;
				}
                $db4->select("nesote_email_spam_$tablenumber");
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername(4).":;,///";
					$var++;
				}
                $db=new NesoteDALController();
                $db->select("nesote_email_customfolder");
                $db->fields("id");
                $db->where("userid=?",$userid);
                $rs=$db->query();
                while($rw=$db->fetchRow($rs))
				{
					$db4->select("nesote_email_customfolder_mapping_$tablenumber");
					$db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,folderid,mail_references");
					$db4->where("folderid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$rw[0],1]);
					$rs4=$db4->query();
					while($row1=$db4->fetchRow($rs4))
					{
						for($i=0;$i<9;$i++)
						{
							if ($i==5) {
                                $arry.=html_entity_decode((string) $row1[$i]).":;,";
                            } elseif ($i==6) {
                                $arry.=html_entity_decode((string) $row1[$i]).":;,";
                            } else {
                                $arry.=$row1[$i].":;,";
                            }
						}
						$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
						$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
						$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
						$arry.=$row1[count($fieldsArry)-3].":;,";
						$arry.=$this->getfoldername($rw[0]).":;,///";
						$var++;
					}
				}
            } elseif ($folderid<10) {
                $db4=new NesoteDALController();
                if ($folderid==1) {
                    $db4->select("nesote_email_inbox_$tablenumber");
                } elseif ($folderid==2) {
                    $db4->select("nesote_email_draft_$tablenumber");
                } elseif ($folderid==3) {
                    $db4->select("nesote_email_sent_$tablenumber");
                } elseif ($folderid==4) {
                    $db4->select("nesote_email_spam_$tablenumber");
                } elseif ($folderid==5) {
                    $db4->select("nesote_email_trash_$tablenumber");
                }
                $db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                while($row1=$db4->fetchRow($rs4))
				{
					for($i=0;$i<9;$i++)
					{
						if ($i==5) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } elseif ($i==6) {
                            $arry.=html_entity_decode((string) $row1[$i]).":;,";
                        } else {
                            $arry.=$row1[$i].":;,";
                        }
					}
					$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
					$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
					$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
					$arry.=$row1[count($fieldsArry)-3].":;,";
					$arry.=$this->getfoldername($folderid).":;,///";
					$var++;
				}
            } else
			{
				$db=new NesoteDALController();
				$db->select("nesote_email_customfolder");
				$db->fields("id");
				$db->where("userid=?",$userid);
				$rs=$db->query();
				while($rw=$db->fetchRow($rs))
				{
					$db4=new NesoteDALController();
					$db4->select("nesote_email_customfolder_mapping_$tablenumber");
					$db4->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,folderid,mail_references");
					$db4->where("folderid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$rw[0]]);
					$rs4=$db4->query();
					while($row1=$db4->fetchRow($rs4))
					{
						for($i=0;$i<9;$i++)
						{
							if ($i==5) {
                                $arry.=html_entity_decode((string) $row1[$i]).":;,";
                            } elseif ($i==6) {
                                $arry.=html_entity_decode((string) $row1[$i]).":;,";
                            } else {
                                $arry.=$row1[$i].":;,";
                            }
						}
						$arry.=$this->getstar($row1[count($fieldsArry)],$row1[0],1).":;,";
						$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
						$arry.=$this->getattachcount($row1[count($fieldsArry)]).":;,";
						$arry.=$row1[9].":;,";
						$arry.=$this->getfoldername($folderid).":;,///";
						$var++;
					}
				}
			}
			$arrys=substr($arry,0,-6);
			$finalArry=$arrys."{endstring_mails}".$var;
			echo $finalArry;exit;

		}
	}


	function findnumberAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{

			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$keyword=$this->getParam(1);
			$folderid=$this->getParam(2);
			$userid=$this->getId();
			$var=0;
			$db4=new NesoteDALController();

			if ($folderid==0) {
                $db4->select("nesote_email_inbox_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_draft_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and just_insert=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,0]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_sent_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_spam_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_trash_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db1=new NesoteDALController();
                $db1->select("nesote_email_customfolder");
                $db1->fields("id");
                $db1->where("userid=?",[$userid]);
                $rs1=$db1->query();
                while($row=$db1->fetchRow($rs1))
				{

					$db4->select("nesote_email_customfolder_mapping_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("folderid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$row[0]]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					$var += $count;
				}
            } elseif ($folderid==6) {
                $db4->select("nesote_email_inbox_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_draft_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and starflag=? and just_insert=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1,0]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_sent_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_spam_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db4->select("nesote_email_trash_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,1]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
                $db1=new NesoteDALController();
                $db1->select("nesote_email_customfolder");
                $db1->fields("id");
                $db1->where("userid=?",[$userid]);
                $rs1=$db1->query();
                while($row=$db1->fetchRow($rs1))
				{
					$db4=new NesoteDALController();
					$db4->select("nesote_email_customfolder_mapping_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("folderid=? and starflag=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$row[0],1]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					$var += $count;
				}
            } elseif ($folderid==1) {
                $db4->select("nesote_email_inbox_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
            } elseif ($folderid==2) {
                $db4->select("nesote_email_draft_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and just_insert=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid,0]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
            } elseif ($folderid==3) {
                $db4->select("nesote_email_sent_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
            } elseif ($folderid==4) {
                $db4->select("nesote_email_spam_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
            } elseif ($folderid==5) {
                $db4->select("nesote_email_trash_$tablenumber");
                $db4->fields("distinct mail_references");
                $db4->where("userid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$userid]);
                $rs4=$db4->query();
                $count=$db4->numRows($rs4);
                $var += $count;
            } else
			{
				$db1=new NesoteDALController();
				$db1->select("nesote_email_customfolder");
				$db1->fields("id");
				$db1->where("userid=?",[$userid]);
				$rs1=$db1->query();
				while($row=$db1->fetchRow($rs1))
				{

					$db4->select("nesote_email_customfolder_mapping_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("folderid=? and (subject like '%$keyword%' or body like '%$keyword%' or from_list like '%$keyword%') ",[$folderid]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					if ($folderid==$row[0]) {
                        $var += $count;
                    }
				}
			}
		}
		echo $var;exit;
	}

	function findnumbers_adAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);


			$from=trim((string) $this->getParam(1));
			$to=trim((string) $this->getParam(2));
			$subject=trim((string) $this->getParam(3));
			$body=trim((string) $this->getParam(4));
			$folderid=$this->getParam(5);
			$attach=$this->getParam(6);
			//echo $from.$to.$subject.$body.$folderid.$attach;exit;

			$userid=$this->getId();
			$search="";
			if ($from !== "") {
                $search.="and from_list like '%$from%' ";
            }
			if ($to !== "") {
                $search.="and (to_list like '%$to%' or cc like '%$to%' or bcc like '%$to%') ";
            }
			if ($subject !== "") {
                $search.="and subject like '%$subject%' ";
            }
			if ($body !== "") {
                $search.="and body like '%$body%' ";
            }//echo $search;exit;
			//$search=substr_replace($search,"",-3);//echo $search;exit;

			$db5=new NesoteDALController();
			$db1=new NesoteDALController();
			$db4=new NesoteDALController();
			$var=0;
			if ($folderid==0) {
                if($attach==1)
				{
					$db5->select("nesote_email_attachments_$tablenumber");
					$db5->fields("distinct mailid, folderid");
					$db5->where("attachment=? and userid=?",[1,$userid]);
					$rs5=$db5->query();
					while($result5=$db5->fetchRow($rs5))
					{

						if ($result5[1]==1) {
                            $db4->select("nesote_email_inbox_$tablenumber");
                        } elseif ($result5[1]==2) {
                            $db4->select("nesote_email_draft_$tablenumber");
                        } elseif ($result5[1]==3) {
                            $db4->select("nesote_email_sent_$tablenumber");
                        } elseif ($result5[1]==4) {
                            $db4->select("nesote_email_spam_$tablenumber");
                        } elseif ($result5[1]==5) {
                            $db4->select("nesote_email_trash_$tablenumber");
                        }
						//else if($result5[1]>=10)
						//$db4->select("nesote_email_customfolder_mapping");

						$db4->fields("distinct mail_references");
						//							if($result5[1]>=10)
						//							{
						//							$db1=new NesoteDALController();
						//							$count=$db1->total("nesote_email_customfolder","id=? and userid=?",array($result5[1],$userid));
						//							if($count>0)
						//							$db4->where("folderid=? and id=? $search ",array($result5[1],$result5[0]));
						//							}
						//							else
						//							{
						$db4->where("userid=? and id=? $search ",[$userid,$result5[0]]);
						//}

						if ($result5[1]<10) {
                            $rs4=$db4->query();
                            while($result4=$db4->fetchRow($rs4))
							{
								$refArry[$var][0]=$result4[0];
								$var++;
							}
                        } elseif ($result5[1]>=10) {
                            $count=$db1->total("nesote_email_customfolder","id=? and userid=?",[$result5[1],$userid]);
                            if($count>0)
							{

								$db4->select("nesote_email_customfolder_mapping_$tablenumber");
								$db4->fields("distinct mail_references");
								$db4->where("folderid=? and id=? $search ",[$result5[1],$result5[0]]);
								$rs4=$db4->query();
								//$count=$db4->numRows($rs4);
								while($result4=$db4->fetchrow($rs4))
								{
									$refArry[$var][0]=$result4[0];
									$var++;
								}

							}
                        }

					}

				}
				else
				{

					$db5->select("nesote_email_inbox_$tablenumber");
					$db5->fields("distinct mail_references");
					$db5->where("userid=? $search ",[$userid]);
					$rs4=$db5->query();
					$count=$db5->numRows($rs4);//$res4=$db4->fetchRow($rs4);print_r($res4);exit;
					//$var=$var+$count;
					while($result4=$db5->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}


					$db5->select("nesote_email_draft_$tablenumber");
					$db5->fields("distinct mail_references");
					$db5->where("userid=? and just_insert=? $search ",[$userid,0]);
					$rs4=$db5->query();
					$count=$db5->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db5->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}


					$db5->select("nesote_email_sent_$tablenumber");
					$db5->fields("distinct mail_references");
					$db5->where("userid=? $search ",[$userid]);
					$rs4=$db5->query();
					$count=$db5->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db5->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}


					$db5->select("nesote_email_spam_$tablenumber");
					$db5->fields("distinct mail_references");
					$db5->where("userid=? $search ",[$userid]);
					$rs4=$db5->query();
					$count=$db5->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db5->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}


					$db5->select("nesote_email_trash_$tablenumber");
					$db5->fields("distinct mail_references");
					$db5->where("userid=? $search ",[$userid]);
					$rs4=$db5->query();
					$count=$db5->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db5->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}


					$db1->select("nesote_email_customfolder");
					$db1->fields("id");
					$db1->where("userid=?",[$userid]);
					$rs1=$db1->query();
					while($row=$db1->fetchRow($rs1))
					{

						$db5->select("nesote_email_customfolder_mapping_$tablenumber");
						$db5->fields("distinct mail_references");
						$db5->where("folderid=? $search ",[$row[0]]);
						$rs4=$db5->query();
						$count=$db5->numRows($rs4);
						//$var=$var+$count;
						while($result4=$db5->fetchRow($rs4))
						{

							$refArry[$var][0]=$result4[0];
							$var++;
						}


					}


				}
                $var1 = $var != 0 ? 1 : 0;
                $refrArray[0][0]=$refArry[0][0];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}
                // echo $var."//".$var1;exit;
            } elseif ($folderid==6) {
                //$var=0;
                if($attach==1)
				{
					$db5->select("nesote_email_attachments_$tablenumber");
					$db5->fields("distinct mailid,folderid");
					$db5->where("attachment=? and userid=?",[1,$userid]);
					$rs5=$db5->query();
					while($result5=$db5->fetchRow($rs5))
					{
						if ($result5[1]<4) {
                            if ($result5[1]==1) {
                                $db4->select("nesote_email_inbox_$tablenumber");
                            } elseif ($result5[1]==2) {
                                $db4->select("nesote_email_draft_$tablenumber");
                            } elseif ($result5[1]==3) {
                                $db4->select("nesote_email_sent_$tablenumber");
                            }
                            //else if($result5[1]>=10)
                            //$db4->select("nesote_email_customfolder_mapping");
                            $db4->fields("distinct mail_references");
                            $db4->where("userid=? and starflag=? and id=? $search ",[$userid,1,$result5[0]]);
                            $rs4=$db4->query();
                            //echo $nu=$db4->numRows($rs4)."***";
                            while($result4=$db4->fetchRow($rs4))
							{//echo "q";
								$refArry[$var][0]=$result4[0];
								$var++;
							}
                        } elseif ($result5[1]>=10) {
                            $count=$db1->total("nesote_email_customfolder","id=? and userid=?",[$result5[1],$userid]);
                            if($count>0)
							{

								$db4->select("nesote_email_customfolder_mapping_$tablenumber");
								$db4->fields("distinct mail_references");
								$db4->where("folderid=? and starflag=? and id=? $search ",[$result5[1],1,$result5[0]]);
								$rs4=$db4->query();//echo $db4->getQuery();
								//echo $count=$db4->numRows($rs4)."+++";
								//$var=$var+$count;
								while($result4=$db4->fetchRow($rs4))
								{
									$refArry[$var][0]=$result4[0];
									$var++;
								}

							}
                        }//echo $db4->getQuery();
					}//exit;
					//echo "=".$var;exit;
				}


				else
				{

					$db4->select("nesote_email_inbox_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? and starflag=? $search ",[$userid,1]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}


					$db4->select("nesote_email_draft_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? and starflag=? and just_insert=? $search ",[$userid,1,0]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}


					$db4->select("nesote_email_sent_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? and starflag=? $search ",[$userid,1]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}

					//					$db4=new NesoteDALController();
					//					$db4->select("nesote_email_spam");
					//					$db4->fields("distinct mail_references");
					//					$db4->where("userid=? and starflag=? $search ",array($userid,1));
					//					$rs4=$db4->query();
					//					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					// while($result4=$db4->fetchRow($rs4))
					//{

					//	$refArry[$var][0]=$result4[0];
					//	$var++;
					//}

					//					$db4=new NesoteDALController();
					//					$db4->select("nesote_email_trash");
					//					$db4->fields("distinct mail_references");
					//					$db4->where("userid=? and starflag=? $search ",array($userid,1));
					//					$rs4=$db4->query();
					//					$count=$db4->numRows($rs4);
					//					//$var=$var+$count;
					//					while($result4=$db4->fetchRow($rs4))
					//					{
					//
					//						$refArry[$var][0]=$result4[0];
					//						$var++;
					//					}


					$db1->select("nesote_email_customfolder");
					$db1->fields("id");
					$db1->where("userid=?",[$userid]);
					$rs1=$db1->query();
					while($row=$db1->fetchRow($rs1))
					{

						$db4->select("nesote_email_customfolder_mapping_$tablenumber");
						$db4->fields("distinct mail_references");
						$db4->where("folderid=? and starflag=? $search ",[$row[0],1]);
						$rs4=$db4->query();
						$count=$db4->numRows($rs4);
						//$var=$var+$count;
						while($result4=$db4->fetchRow($rs4))
						{

							$refArry[$var][0]=$result4[0];
							$var++;
						}

					}
				}
                $var1 = $var != 0 ? 1 : 0;
                $refrArray[0][0]=$refArry[0][0];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}
            } elseif ($folderid==1) {
                if($attach==1)
				{
					$db5->select("nesote_email_attachments_$tablenumber");
					$db5->fields("distinct mailid");
					$db5->where("attachment=? and folderid=? and userid=?",[1,1,$userid]);
					$rs5=$db5->query();
					while($result5=$db5->fetchRow($rs5))
					{

						$db4->select("nesote_email_inbox_$tablenumber");
						$db4->fields("distinct mail_references");
						$db4->where("userid=? and id=? $search ",[$userid,$result5[0]]);
						$rs4=$db4->query();
						$count=$db4->numRows($rs4);
						//$var=$var+$count;
						while($result4=$db4->fetchRow($rs4))
						{
							$refArry[$var][0]=$result4[0];
							$var++;
						}
					}
				}
				else
				{

					$db4->select("nesote_email_inbox_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? $search ",[$userid]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}
				}
                $var1 = $var != 0 ? 1 : 0;
                $refrArray[0][0]=$refArry[0][0];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}
            } elseif ($folderid==2) {
                if($attach==1)
				{
					$db5->select("nesote_email_attachments_$tablenumber");
					$db5->fields("distinct mailid");
					$db5->where("attachment=? and folderid=? and userid=?" ,[1,2,$userid]);
					$rs5=$db5->query();
					while($result5=$db5->fetchRow($rs5))
					{

						$db4->select("nesote_email_draft_$tablenumber");
						$db4->fields("distinct mail_references");
						$db4->where("userid=? and just_insert=? and id=? $search ",[$userid,0,$result5[0]]);
						$rs4=$db4->query();
						$count=$db4->numRows($rs4);
						//$var=$var+$count;
						while($result4=$db4->fetchRow($rs4))
						{
							$refArry[$var][0]=$result4[0];
							$var++;
						}
					}

				}
				else
				{

					$db4->select("nesote_email_draft_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? and just_insert=? $search ",[$userid,0]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}
				}
                $var1 = $var != 0 ? 1 : 0;
                $refrArray[0][0]=$refArry[0][0];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}
            } elseif ($folderid==3) {
                if($attach==1)
				{
					$db5->select("nesote_email_attachments_$tablenumber");
					$db5->fields("distinct mailid");
					$db5->where("attachment=? and folderid=? and userid=?",[1,3,$userid]);
					$rs5=$db5->query();
					while($result5=$db5->fetchRow($rs5))
					{


						$db4->select("nesote_email_sent_$tablenumber");
						$db4->fields("distinct mail_references");
						$db4->where("userid=? and id=? $search ",[$userid,$result5[0]]);
						$rs4=$db4->query();
						$count=$db4->numRows($rs4);
						//$var=$var+$count;
						while($result4=$db4->fetchRow($rs4))
						{

							$refArry[$var][0]=$result4[0];
							$var++;
						}
					}
				}
				else
				{

					$db4->select("nesote_email_sent_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? $search ",[$userid]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}
				}
                $var1 = $var != 0 ? 1 : 0;
                $refrArray[0][0]=$refArry[0][0];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}
            } elseif ($folderid==4) {
                if($attach==1)
				{
					$db5->select("nesote_email_attachments_$tablenumber");
					$db5->fields("distinct mailid");
					$db5->where("attachment=? and folderid=? and userid=?",[1,4,$userid]);
					$rs5=$db5->query();
					while($result5=$db5->fetchRow($rs5))
					{

						$db4->select("nesote_email_spam_$tablenumber");
						$db4->fields("distinct mail_references");
						$db4->where("userid=? and id=? $search ",[$userid,$result5[0]]);
						$rs4=$db4->query();
						$count=$db4->numRows($rs4);
						//$var=$var+$count;
						//echo $db4->getQuery();
						while($result4=$db4->fetchRow($rs4))
						{

							$refArry[$var][0]=$result4[0];
							$var++;
						}

					}
				}
				else
				{

					$db4->select("nesote_email_spam_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? $search ",[$userid]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{

						$refArry[$var][0]=$result4[0];
						$var++;
					}
				}
                $var1 = $var != 0 ? 1 : 0;
                $refrArray[0][0]=$refArry[0][0];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}
            } elseif ($folderid==5) {
                if($attach==1)
				{
					$db5->select("nesote_email_attachments_$tablenumber");
					$db5->fields("distinct mailid");
					$db5->where("attachment=? and folderid=? and userid=?",[1,5,$userid]);
					$rs5=$db5->query();//echo $db5->getQuery();
					while($result5=$db5->fetchRow($rs5))
					{

						$db4->select("nesote_email_trash_$tablenumber");
						$db4->fields("distinct mail_references");
						$db4->where("userid=? and id=? $search ",[$userid,$result5[0]]);
						$rs4=$db4->query();
						$count=$db4->numRows($rs4);
						//$var=$var+$count;
						while($result4=$db4->fetchRow($rs4))
						{

							$refArry[$var][0]=$result4[0];
							$var++;
						}
					}
				}
				else
				{

					$db4->select("nesote_email_trash_$tablenumber");
					$db4->fields("distinct mail_references");
					$db4->where("userid=? $search ",[$userid]);
					$rs4=$db4->query();
					$count=$db4->numRows($rs4);
					//$var=$var+$count;
					while($result4=$db4->fetchRow($rs4))
					{
						$refArry[$var][0]=$result4[0];
						$var++;
					}
				}
                $var1 = $var != 0 ? 1 : 0;
                $refrArray[0][0]=$refArry[0][0];
                for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}
            } else
			{

				//$db1->select("nesote_email_customfolder");
				//$db1->fields("id");
				//$db1->where("userid=?",array($userid));
				//$rs1=$db1->query();
				$tot=$db1->total("nesote_email_customfolder","userid=? and id=?",[$userid,$folderid]);
				//echo $tot."/";
				//while($row=$db1->fetchRow($rs1))

				if($tot==1)
				{
					if($attach==1)
					{
						$db5->select("nesote_email_attachments_$tablenumber");
						$db5->fields(" mailid,folderid");
						$db5->where("attachment=? and folderid=? and userid=?",[1,$folderid,$userid]);
						$db5->group("mailid,folderid");
						$rs5=$db5->query();echo $db5->getQuery();
						while($result5=$db5->fetchRow($rs5))
						{

							$db4->select("nesote_email_customfolder_mapping_$tablenumber");
							$db4->fields("distinct mail_references");
							$db4->where("folderid=? and id=? $search ",[$folderid,$result5[0]]);

							$rs4=$db4->query();
							$count=$db4->numRows($rs4);
							//if($folderid==$row[0])
							//$var=$var+$count;
							// {
							while($result4=$db4->fetchRow($rs4))
							{
								$refArry[$var][0]=$result4[0];
								$var++;
							}
							// }
						}//print_r($refArry);

					}
					else
					{

						$db4->select("nesote_email_customfolder_mapping_$tablenumber");
						$db4->fields("distinct mail_references");
						$db4->where("folderid=? $search ",[$folderid]);
						$rs4=$db4->query();
						$count=$db4->numRows($rs4);
						// if($folderid==$row[0])
						//$var=$var+$count;
						// {
						while($result4=$db4->fetchRow($rs4))
						{
							$refArry[$var][0]=$result4[0];
							$var++;
						}
						// }
					}
				}
				$var1 = $var != 0 ? 1 : 0;
				$refrArray[0][0]=$refArry[0][0];

				for($i=1;$i<$var;$i++)
				{
					$check=0;
					for($j=0;$j<$var1;$j++)
					{
						if ($refrArray[$j][0]==$refArry[$i][0]) {
                            $check=1;
                        }
					}
					if($check==0)
					{
						$refrArray[$var1][0]=$refArry[$i][0];

						$var1++;
					}
				}


			}
		}
		echo $var1;exit;


	}

	function searchdetailmailAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$mailId=$this->getParam(1);
			$name=$this->getParam(2);
			$id=$this->getId();
			$select=new NesoteDALController();
			if ($name=="inbox") {
                $select->select("nesote_email_inbox_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                $var=0;
                $arry="";
                $row=$select->fetchRow($result);
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                $array_count=count($folderArray[1]);
                $r=$array_count-1;
                while($r>=0)
				{
					$select1=new NesoteDALController();
					if($folderArray[1][$r]==1)
					{
						$select1->select("nesote_email_inbox_$tablenumber");
					}
					else
					{
						$select1->select("nesote_email_sent_$tablenumber");
					}


					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
					$result1=$select1->query();
					$var1=0;
					while($row1=$select1->fetchRow($result1))
					{

						for($i=0;$i<9;$i++)
						{
							if($i==6)
							{
								$arry.=html_entity_decode((string) $row1[$i]).":;,";
							}
							else {
                                $arry.=$row1[$i].":;,";
                            }
						}

						$arry.=$this->gettime($row1[9]).":;,";
						$arry.=$this->getattachmentIcon($row1[0],1).":;,";
						$arry.=$this->$row1[9].":;,";
						$arry.=$this->getfoldername($folderArray[1][$r]).":;,";
						$arry.="///";
						$var1++;
					}

					$r--;
				}
                $arry=substr($arry,0,-3);
            } elseif ($name=="draft") {
                $select->select("nesote_email_draft_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("userid=? and id=? and just_insert=?",[$id,$mailId,0]);
                $result=$select->query();
                $var=0;
                $arry="";
                while($row1=$select->fetchRow($result))
				{
					for($i=0;$i<9;$i++)
					{
						$arry.=$row1[$i].":;,";
					}
					$arry.=$this->gettime($row1[9]).":;,";
					$arry.=$this->getattachmentIcon($row1[0],2).":;,";
					$arry.=$row1[9].":;,";
					$arry.="draft:;,";
					$arry.="///";
					$var++;
				}
            } elseif ($name=="sent") {
                $select->select("nesote_email_sent_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                $var=0;
                $arry="";
                $row=$select->fetchRow($result);
                preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
                preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
                $array_count=count($folderArray);
                $r=$array_count-1;
                while($r>=0)
				{
					$select1=new NesoteDALController();
					if($folderArray[1][$r]==1)
					{
						$select1->select("nesote_email_inbox_$tablenumber");
					}
					else
					{
						$select1->select("nesote_email_sent_$tablenumber");
					}


					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
					$result1=$select1->query();
					$var1=0;
					while($row1=$select1->fetchRow($result1))
					{

						for($i=0;$i<9;$i++)
						{
							if($i==6)
							{
								$arry.=html_entity_decode((string) $row1[$i]).":;,";
							}
							else {
                                $arry.=$row1[$i].":;,";
                            }
						}
						$arry.=$this->gettime($row1[9]).":;,";
						$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r]).":;,";
						$arry.=$row1[9].":;,";
						$arry.=$this->getfoldername($folderArray[1][$r]).":;,";
						$arry.="///";
						$var1++;
					}

					$r--;
				}
                $arry=substr($arry,0,-3);
            } elseif ($name=="spam") {
                $select->select("nesote_email_spam_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                $var=0;
                $arry="";
                while($row1=$select->fetchRow($result))
				{
					for($i=0;$i<12;$i++)
					{
						$arry.=$row1[$i].":;,";
					}
					$var++;
				}
            } elseif ($name=="trash") {
                $select->select("nesote_email_trash_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("userid=? and id=?",[$id,$mailId]);
                $result=$select->query();
                $var=0;
                $arry="";
                while($row1=$select->fetchRow($result))
				{
					for($i=0;$i<12;$i++)
					{
						$arry.=$row1[$i].":;,";
					}
					$var++;
				}
            } elseif ($name=="starred") {
                $select->select("nesote_email_inbox_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=? and starflag=?",[$id,$mailId,1]);
                $result=$select->query();
                $var=0;
                $arry="";
                $no=$select->numRows($result);
                if($no!=0)
				{
					$row=$select->fetchRow($result);

					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					$array_count=count($folderArray[1]);
					$r=$array_count-1;
					while($r>=0)
					{
						$select1=new NesoteDALController();
						if($folderArray[1][$r]==1)
						{
							$select1->select("nesote_email_inbox_$tablenumber");
						}
						else
						{
							$select1->select("nesote_email_sent_$tablenumber");
						}


						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
						$result1=$select1->query();
						$var1=0;
						while($row1=$select1->fetchRow($result1))
						{

							for($i=0;$i<9;$i++)
							{
								if($i==6)
								{
									$arry.=html_entity_decode((string) $row1[$i]).":;,";
								}
								else {
                                    $arry.=$row1[$i].":;,";
                                }
							}
							$arry.=$this->gettime($row1[count($fieldsArry)-3]).":;,";
							$arry.=$this->getattachmentIcon($row1[0],$folderArray[1][$r]).":;,";
							$arry.=$row1[9].":;,";
							$arry.=$this->getfoldername($folderArray[1][$r]).":;,";
							$arry.="///";
							$var1++;
						}

						$r--;
					}
				}
                $select->select("nesote_email_draft_$tablenumber");
                $select->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
                $select->where("userid=? and id=? and just_insert=? and starflag=?",[$id,$mailId,0,1]);
                $result=$select->query();
                $var=0;
                while($row1=$select->fetchRow($result))
				{
					for($i=0;$i<9;$i++)
					{
						$arry.=$row1[$i].":;,";
					}
					$arry.=$this->gettime($row1[9]).":;,";
					$arry.=$this->getattachmentIcon($row1[0],1).":;,";
					$arry.=$row1[9].":;,";
					$arry.="draft:;,";
					$arry.="///";
					$var++;
				}
                $select->select("nesote_email_sent_$tablenumber");
                $select->fields("mail_references");
                $select->where("userid=? and id=? and starflag=?",[$id,$mailId,1]);
                $result=$select->query();
                $no=$select->numRows($result);
                if($no!=0)
				{
					$row=$select->fetchRow($result);

					preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
					preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
					$array_count=count($folderArray[1]);
					$r=$array_count-1;
					while($r>=0)
					{
						$select1=new NesoteDALController();
						if($folderArray[1][$r]==1)
						{
							$select1->select("nesote_email_inbox_$tablenumber");
						}
						else
						{
							$select1->select("nesote_email_sent_$tablenumber");
						}


						$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
						$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
						$result1=$select1->query();
						$var1=0;
						while($row1=$select1->fetchRow($result1))
						{

							for($i=0;$i<9;$i++)
							{
								if($i==6)
								{
									$arry.=html_entity_decode((string) $row1[$i]).":;,";
								}
								else {
                                    $arry.=$row1[$i].":;,";
                                }
							}
							$arry.=$this->gettime($row1[9]).":;,";
							$arry.=$this->getattachmentIcon($row1[0],1).":;,";
							$arry.=$row1[9].":;,";
							$arry.=$this->getfoldername($folderArray[1][$r]).":;,";
							$arry.="///";
							$var1++;
						}

						$r--;
					}
				}
                $db3=new NesoteDALController();
                $db3->select("nesote_email_customfolder");
                $db3->fields("id");
                $db3->where("userid=?",$id);
                $rs3=$db3->query();
                while($row3=$db3->fetchRow($rs3))
				{
					$select->select("nesote_email_customfolder_mapping_$tablenumber");
					$select->fields("mail_references");
					$select->where("folderid=? and id=? and starflag=?",[$folderid,$mailId,1]);
					$result=$select->query();
					$no=$select->numRows($result);
					if($no!=0)
					{
						$row=$select->fetchRow($result);

						preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
						preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
						$array_count=count($folderArray[1]);
						$r=$array_count-1;
						while($r>=0)
						{
							$select1=new NesoteDALController();
							if($folderArray[1][$r]==1)
							{
								$select1->select("nesote_email_customfolder_mapping_$tablenumber");
							}
							else
							{
								$select1->select("nesote_email_sent_$tablenumber");
							}


							$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
							$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
							$result1=$select1->query();
							$var1=0;
							while($row1=$select1->fetchRow($result1))
							{

								for($i=0;$i<9;$i++)
								{
									if($i==6)
									{
										$arry.=html_entity_decode((string) $row1[$i]).":;,";
									}
									else {
                                        $arry.=$row1[$i].":;,";
                                    }
								}
								$arry.=$this->gettime($row1[9]).":;,";
								$arry.=$this->getattachmentIcon($row1[0],1).":;,";
								$arry.=$row1[9].":;,";
								$arry.=$this->getfoldername($folderArray[1][$r]).":;,";
								$arry.="///";
								$var1++;
							}

							$r--;
						}
					}

				}
            } else
			{
				$folderid=substr((string) $name,6);
				$select->select("nesote_email_customfolder_mapping_$tablenumber");
				$select->fields("mail_references");
				$select->where("folderid=? and id=? and starflag=?",[$folderid,$mailId,1]);
				$result=$select->query();
				$var=0;$arry="";
				$row=$select->fetchRow($result);

				preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
				preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
				$array_count=count($folderArray[1]);
				$r=$array_count-1;
				while($r>=0)
				{
					$select1=new NesoteDALController();
					if($folderArray[1][$r]==1)
					{
						$select1->select("nesote_email_customfolder_mapping_$tablenumber");
					}
					else
					{
						$select1->select("nesote_email_sent_$tablenumber");
					}


					$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,mail_references");
					$select1->where("userid=? and id=? ",[$id,$mailidArray[1][$r]]);
					$result1=$select1->query();
					$var1=0;
					while($row1=$select1->fetchRow($result1))
					{

						for($i=0;$i<9;$i++)
						{
							if($i==6)
							{
								$arry.=html_entity_decode((string) $row1[$i]).":;,";
							}
							else {
                                $arry.=$row1[$i].":;,";
                            }
						}
						$arry.=$this->gettime($row1[9]).":;,";
						$arry.=$this->getattachmentIcon($row1[0],1).":;,";
						$arry.=$this->getfoldername($folderArray[1][$r]).":;,";
						$arry.="///";
						$var1++;
					}

					$r--;
				}
			}
			echo substr($arry,0,-3);
			exit;
		}
	}


	function changereadflagAction()
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		//$foldername=$this->getparam(1);echo $foldername;
		//	$folderId=$this->getfolderid($foldername);
		$object=$this->getparam(2);
		if(str_contains((string) $object,">"))
		{
			$se_object=explode(">",(string) $object);
			$new_object=$se_object[1];
		}
		else
		{
			$new_object=$object;
		}

		$mail=explode("_",(string) $new_object);
		$mailId=$mail[1];
		$folderId=$this->getfolderid($mail[0]);
		$db=new NesoteDALController();
		if ($folderId==1) {
            $db->select("nesote_email_inbox_$tablenumber");
        } elseif ($folderId==2) {
            $db->select("nesote_email_draft_$tablenumber");
        } elseif ($folderId==3) {
            $db->select("nesote_email_sent_$tablenumber");
        } elseif ($folderId==4) {
            $db->select("nesote_email_spam_$tablenumber");
        } elseif ($folderId==5) {
            $db->select("nesote_email_trash_$tablenumber");
        } else {
            $db->select("nesote_email_customfolder_mapping_$tablenumber");
        }
		$db->fields("mail_references");
		$db->where("id=?",$mailId);
		$rs1=$db->query();//echo $db->getQuery();
		$row=$db->fetchRow($rs1);
		preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
		preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $row[0],$mailidArray);
        $counter = count($folderArray[1]);
		for($i=0;$i<$counter;$i++)
		{
			$db1=new NesoteDALController();
			if ($folderArray[1][$i]==1) {
                $db1->update("nesote_email_inbox_$tablenumber");
            } elseif ($folderArray[1][$i]==2) {
                $db1->update("nesote_email_draft_$tablenumber");
            } elseif ($folderArray[1][$i]==3) {
                $db1->update("nesote_email_sent_$tablenumber");
            } elseif ($folderArray[1][$i]==4) {
                $db1->update("nesote_email_spam_$tablenumber");
            } elseif ($folderArray[1][$i]==5) {
                $db1->update("nesote_email_trash_$tablenumber");
            } else {
                $db1->update("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db1->set("readflag=?",[1]);
			$db1->where("id=?",$mailidArray[1][$i]);
			$res1=$db1->query();//echo $db1->getQuery();
		}
		exit;
	}

	function changeChatreadflagAction(): never
	{

		$mailId=$this->getparam(1);
		$username=$_COOKIE['e_username'];
		$modlusnumber=$this->tableid($username);

		$db1=new NesoteDALController();
		$db1->update("nesote_chat_message_$modlusnumber");
		$db1->set("read_flag=?",1);
		$db1->where("id=?",$mailId);
		$rs1=$db1->query();
		$db1->fetchRow($rs1);//echo $db1->getQuery();

		exit;
	}

	function getfolderid($foldername)
	{

		if ($foldername=='inbox') {
            return 1;
        } elseif ($foldername=='draft') {
            return 2;
        } elseif ($foldername=='sent') {
            return 3;
        } elseif ($foldername=='spam') {
            return 4;
        } elseif ($foldername=='trash') {
            return 5;
        } elseif ($foldername=='starred') {
            return 6;
        } elseif (strpos((string) $foldername,"ustom")==1) {
            return str_replace("custom","",$foldername);
        } elseif (strpos((string) $foldername,"earch")==1) {
            return str_replace("search","",$foldername);
        } else
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_customfolder");
			$db->fields("id");
			$db->where("name=?",$foldername);
			$rs1=$db->query();
			$row=$db->fetchRow($rs1);
			return $row[0];
		}


	}

	function getfoldername($folderid)
	{

		if ($folderid==1) {
            return 'inbox';
        } elseif ($folderid==2) {
            return 'draft';
        } elseif ($folderid==3) {
            return 'sent';
        } elseif ($folderid==4) {
            return 'spam';
        } elseif ($folderid==5) {
            return 'trash';
        } elseif ($folderid==6) {
            return 'starred';
        } else
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_customfolder");
			$db->fields("name");
			$db->where("id=?",$folderid);
			$rs1=$db->query();
			$row=$db->fetchRow($rs1);
			return $row[0];
		}

	}

	function getfoldernamenew($folderid)
	{

		if ($folderid==1) {
            return 'inbox';
        } elseif ($folderid==2) {
            return 'draft';
        } elseif ($folderid==3) {
            return 'sent';
        } elseif ($folderid==4) {
            return 'spam';
        } elseif ($folderid==5) {
            return 'trash';
        } elseif ($folderid==6) {
            return 'starred';
        } else {
            return "custom".$folderid;
        }

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
	function getstar($references,$mailids,$folderids)
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $references,$folderArray);
		preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $references,$mailidArray);
		$no=count($folderArray[1]);
		$w=0;
		for($i=0;$i<$no;$i++)
		{
			if ($folderArray[1][$i]==5) {
                return 0;
            } else
			{
				$db=new NesoteDALController();
				if ($folderArray[1][$i]==1) {
                    $db->select("nesote_email_inbox_$tablenumber");
                } elseif ($folderArray[1][$i]==2) {
                    $db->select("nesote_email_draft_$tablenumber");
                } elseif ($folderArray[1][$i]==3) {
                    $db->select("nesote_email_sent_$tablenumber");
                } elseif ($folderArray[1][$i]==4) {
                    $db->select("nesote_email_spam_$tablenumber");
                } elseif ($folderArray[1][$i]>=10) {
                    $db->select("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->fields("starflag");
				$db->where("id=?",[$mailidArray[1][$i]]);
				$rs=$db->query();
				$rows=$db->fetchRow($rs);
				if($rows[0]==1)
				{
					$w=1;
					break;
				}

			}

		}
		return $w;

	}

	function gettime1($date)
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();


		$position=$settings->getValue("time_zone_postion");


		$hour=$settings->getValue("time_zone_hour");


		$min=$settings->getValue("time_zone_mint");

		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=time()-$date-$diff;

		$db3= new NesoteDALController();
		$userid=$this->getId();
		$db3->select("nesote_email_usersettings");
		$db3->fields("time_zone");
		$db3->where("userid=?",[$userid]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);

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
		$ts=$newtimezone+$ts;


		$month_id = date("n",$date);
		if(isset ($_COOKIE['lang_mail']))
		{
			$lang_code=$_COOKIE['lang_mail'];
		}
		else
		{

			$lang_code=$settings->getValue("default_language");
			//$defaultlang_id=$lang_id;
		}
         $lang_id=$this->getlang_id($lang_code);
		$day=date(" j ",$date);

		$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$lang_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);
		if($date[0]=="")
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_months_messages");
			$db->fields("message");
			$db->where("month_id=? and lang_id=?",[$month_id,1]);
			$result=$db->query();
			$data=$db->fetchRow($result);
		}
		$v1=time();
		$v2=mktime(0, 0, 0, date("m", time()), date("d", time()), date("Y", time()));
		$v3=$v1-$v2-$diff;
		$v3=$newtimezone+$v3;
		if($ts>2419200)
		{

			$val = $data[0].date(" j,Y ",$date);

		}
		elseif($ts>$v3)
		{
			$val=$data[0]. $day;

			//$val=$data[0].$day;
		}
		else
		{

			$val =date("h:i A ",$date);
			//if($ts>3600)
			//$val = ' '.round($ts/3600,0).' '.$this->getmessage(56).'';
			//else if($ts>60)
			//$val = ' '.round($ts/60,0).' '.$this->getmessage(57).'';
			//else
			//$val = ' '.$ts.' '.$this->getmessage(58).'';

		}
		return $val;

	}



	function gettime($date)
	{   // $date=1327686058;
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		//return date("h:i A ",$date);

		$position=$settings->getValue("time_zone_postion");


		$hour=$settings->getValue("time_zone_hour");



		$min=$settings->getValue("time_zone_mint");


		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		//	$ts=$date;
		$tsa=time()-$date+$diff;
		$dates=$date+$diff;
		$year1= date("Y",$dates);
		$year2= date("Y",time());
		$userid=$this->getId();
		$db3= new NesoteDALController();
		$db3->select("nesote_email_usersettings");
		$db3->fields("time_zone");
		$db3->where("userid=?",[$userid]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);

		$db3->select("nesote_email_time_zone");
		$db3->fields("value");
		$db3->where("id=?",[$row3[0]]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);
		$timezone=$row3[0];//echo $timezone;

		//$sign=trim($timezone[0]);
		$sign=substr((string) $timezone,0,1);
			
		$timezone1=substr((string) $timezone,1);//echo $timezone1."bbbbbbbbbbb";exit;

		$timezone1=explode(":",$timezone1);
		$newtimezone=($timezone1[0]*60*60)+($timezone1[1]*60);
        if ($sign === "-") {
            $newtimezone=-$newtimezone;
        }
		$ts=$date+$newtimezone;

		//$tsa=$date+$newtimezone;
		//	echo date("d/m/y h:i:s",$date);  echo date(" d/m/y h:i:s",$ts);exit;
		$date=$ts;
 
		$month_id = date("n",$date);
		if(isset ($_COOKIE['lang_mail']))
		{
			$lang_code=$_COOKIE['lang_mail'];
		}
		else
		{

			$lang_code=$settings->getValue("default_language");
			//$defaultlang_id=$lang_id;
		}
		$lang_id=$this->getlang_id($lang_code);

		date(" j ",$date);

		$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$lang_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);
		if($data[0]=="")
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_months_messages");
			$db->fields("message");
			$db->where("month_id=? and lang_id=?",[$month_id,1]);
			$result=$db->query();
			$data=$db->fetchRow($result);
		}
		//	$v1=time()-$diff+$newtimezone;
			
		//	$v2=mktime(0, 0, 0, date("m",$v1), date("d",$v1), date("Y",$v1));
		if ($tsa>86400) {
            $val = $year1 === $year2 ? $data[0].date(" j ",$date) : date(" d/m/y ",$date);
        } else
		{
			$val =date("h:i A ",$date);
		}

		return $val;
	}
	function gettimetype1($date)
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		//return date("h:i A ",$date);

		$position=$settings->getValue("time_zone_postion");


		$hour=$settings->getValue("time_zone_hour");



		$min=$settings->getValue("time_zone_mint");


		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=$date;

		$tsa=time()-$date+$diff;

		$db3= new NesoteDALController();
		$userid=$this->getId();
		$db3->select("nesote_email_usersettings");
		$db3->fields("time_zone");
		$db3->where("userid=?",[$userid]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);

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
		$ts += $newtimezone;
		$tsa += $newtimezone;

		$date=$ts;

		$month_id = date("n",$date);
		if(isset ($_COOKIE['lang_mail']))
		{
			$lang_code=$_COOKIE['lang_mail'];
		}
		else
		{
			//
			$lang_code=$settings->getValue("default_language");
			//$defaultlang_id=$lang_id;
		}
        $lang_id=$this->getlang_id($lang_code);
		date(" j ",$date);

		$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$lang_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);
		if($data[0]=="")
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_months_messages");
			$db->fields("message");
			$db->where("month_id=? and lang_id=?",[$month_id,1]);
			$result=$db->query();
			$data=$db->fetchRow($result);
		}
		$v1=time()-$diff+$newtimezone;
		$v2=mktime(0, 0, 0, date("m",$v1), date("d",$v1), date("Y",$v1));
		if($tsa>2419200)
		{
			$val = date(" h:i A  ",$date);
		}
		elseif($ts<$v2)
		{
			$val =date(" h:i A ",$date);
		}
		else
		{
			$val =date("h:i A ",$date);
		}
		return $val;
	}
	function gettimetype2($date)
	{
		//return date("h:i A ",$date);
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		//return date("h:i A ",$date);

		$position=$settings->getValue("time_zone_postion");


		$hour=$settings->getValue("time_zone_hour");



		$min=$settings->getValue("time_zone_mint");


		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=$date;

		$tsa=time()-$date+$diff;

		$db3= new NesoteDALController();
		$userid=$this->getId();
		$db3->select("nesote_email_usersettings");
		$db3->fields("time_zone");
		$db3->where("userid=?",[$userid]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);

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
		$ts += $newtimezone;
		$tsa += $newtimezone;

		$date=$ts;

		$month_id = date("n",$date);
		if(isset ($_COOKIE['lang_mail']))
		{
			$lang_code=$_COOKIE['lang_mail'];
		}
		else
		{
			$select=new NesoteDALController();
			$select->select("nesote_email_settings");
			$select->fields("value");
			$select->where("name=?",'default_language');
			$result=$select->query();
			$data4=$select->fetchRow($result);
			$lang_code=$data4[0];
			//$defaultlang_id=$data4[0];
		}
		$lang_id=$this->getlang_id($lang_code);

		date(" j ",$date);

		$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$lang_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);
		if($data[0]=="")
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_months_messages");
			$db->fields("message");
			$db->where("month_id=? and lang_id=?",[$month_id,1]);
			$result=$db->query();
			$data=$db->fetchRow($result);
		}
		$v1=time()-$diff+$newtimezone;
		$v2=mktime(0, 0, 0, date("m",$v1), date("d",$v1), date("Y",$v1));
		if($tsa>2419200)
		{
			$val =date(" F Y h:i:s A",$date);
		}
		elseif($ts<$v2)
		{
			$val =date("F Y h:i:s A",$date);
		}
		else
		{
			$val =date("F Y h:i:s A",$date);
		}
		return $val;
	}


	function gettimedetail($date)
	{
		$db= new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",\TIME_ZONE_POSTION);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$position=$row[0];

		$db1= new NesoteDALController();
		$db1->select("nesote_email_settings");
		$db1->fields("value");
		$db1->where("name=?",\TIME_ZONE_HOUR);
		$result1=$db1->query();
		$row1=$db1->fetchRow($result1);
		$hour=$row1[0];

		$db2= new NesoteDALController();
		$db2->select("nesote_email_settings");
		$db2->fields("value");
		$db2->where("name=?",\TIME_ZONE_MINT);
		$result2=$db2->query();
		$row2=$db2->fetchRow($result2);
		$min=$row2[0];

		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=$date-$diff;

		time();

		$db3= new NesoteDALController();
		$userid=$this->getId();
		$db3->select("nesote_email_usersettings");
		$db3->fields("time_zone");
		$db3->where("userid=?",[$userid]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);

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
		$ts += $newtimezone;

		$date=$ts;

		$month_id = date("n",$date);
		if(isset ($_COOKIE['lang_mail']))
		{
			$lang_code=$_COOKIE['lang_mail'];
		}
		else
		{
			$select=new NesoteDALController();
			$select->select("nesote_email_settings");
			$select->fields("value");
			$select->where("name=?",'default_language');
			$result=$select->query();
			$data4=$select->fetchRow($result);
			$lang_code=$data4[0];
		//	$defaultlang_id=$data4[0];
		}
        $lang_id=$this->getlang_id($lang_code);
		date(" j ",$date);

		$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$lang_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);
		if($data[0]=="")
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_months_messages");
			$db->fields("message");
			$db->where("month_id=? and lang_id=?",[$month_id,1]);
			$result=$db->query();
			$data=$db->fetchRow($result);
		}
		$v1=time()-$diff+$newtimezone;
        mktime(0, 0, 0, date("m",$v1), date("d",$v1), date("Y",$v1));
		return $data[0].date(" j,Y :- h:i:s a",$date);

	}


	function getattachcount($references)
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$userid=$this->getId();
		preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $references,$folderArray);
		preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $references,$mailidArray);
		$no=count($folderArray[1]);
		$w=0;
		$db= new NesoteDALController();
		for($i=0;$i<$no;$i++)
		{
			$db->select("nesote_email_attachments_$tablenumber");
			$db->fields("id");
			$db->where("mailid=? and folderid=? and attachment=? and userid=?",[$mailidArray[1][$i],$folderArray[1][$i],1,$userid]);
			$result=$db->query();
			$no1=$db->numRows($result);
			$w += $no1;
		}
		if ($w==0) {
            return "";
        } else {
            return "<img src=\"images/attachment.png\" border=\"0\">";
        }
	}

	function upload_frame_replyAction()
	{
		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
		
		
		$draftid=$this->getParam(1);
		$filevar=$this->getParam(2);
		$loop=$this->getParam(3);
		$this->setValue("draftid",$draftid);
		$this->setValue("filevar",$filevar);
		$this->setValue("loop",$loop);
	}

	function upload_forwardAction()
	{
		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }

		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		$userid=$this->getId();
		$select=new NesoteDALController();
		$draftid=$this->getParam(1);
		$loop=$this->getParam(2);
		$just_insert=$this->getParam(3);
		$att=[];

		$select->select("nesote_email_attachments_$tablenumber");
		$select->fields("*");
		$select->where("folderid=? and mailid=? and attachment=? and userid=?",[2,$draftid,1,$userid]);
		$rs=$select->query();

		$this->setValue("aa",$just_insert);
		$filevar=$select->numRows($rs);
		$temp=$loop."_".$filevar;
		$i=0;

		while($row=$select->fetchRow($rs))
		{
			$filename=$row[2];
			$name=explode(".",(string) $filename);
			$len=count($name);
			$extn=$name[($len-1)];
			if ($extn === "qqq") {
                $filename=str_replace("qqq","exe",$filename);
            }
			$var=strpos((string) $filename,"-");
			$namez = $var != "FALSE" || $var > 0 ? substr((string) $filename,($var+1)) : $filename;
			$att[$i][0]=$namez;
			$att[$i][2]=$row[0];
					$filesize=filesize("attachments/2/$tablenumber/$draftid/$row[2]");
			$filesize=ceil($filesize/1024);
			$filesize=ceil($filesize);
			//echo $filesize."mkn";
			$att[$i][1]=$filesize;
			$total_size += $filesize;
			$i++;

		}

		$select->update("nesote_email_draft_$tablenumber");
		$select->set("memorysize=?",[$total_size]);
		$select->where("id=?",$draftid);
		$select->query();
		$this->url("mail/delete_attachment/$draftid");//exit;
		$this->setLoopValue("attach",$att);
		$this->setValue("i",$i);
		$this->setValue("draftid",$draftid);
		$this->setValue("temp",$temp);

		$this->setValue("loop",$loop);
	}

	function delete_forwardattachmentAction()
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$userid=$this->getId();
		$string="";
		$mailid=$this->getParam(1);
		$id=$this->getParam(2);

		$db=new NesoteDALController();
		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("name");
		$db->where("mailid=? and id=? and folderid=? and userid=?" ,[$mailid,$id,2,$userid]);
		$re1=$db->query();
		$row1=$db->fetchRow($re1);
		$name=$row1[0];

		$db->select("nesote_email_draft_$tablenumber");
		$db->fields("memorysize");
		$db->where("id=?" ,[$mailid]);
		$re3=$db->query();
		$row3=$db->fetchRow($re3);
		$file_size=$row3[0];
				$size=filesize("attachments/2/$tablenumber/$mailid/$name");
		$size=ceil($size/1024);
		$filesize1=$file_size-$size;

		$db->update("nesote_email_draft_$tablenumber");
		$db->set("memorysize=?",$filesize1);
		$db->where("id=?" ,[$mailid]);
		$db->query();


		$db->delete("nesote_email_attachments_$tablenumber");
		$db->where("mailid=? and id=? and folderid=? and userid=?" ,[$mailid,$id,2,$userid]);
		$db->query();
		unlink("attachments/2/$tablenumber/$mailid/$name");


		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("*");
		$db->where("folderid=? and mailid=? and userid=?",[2,$mailid,$userid]);
		$rs=$db->query();
		$db->numRows($rs);
		$i=0;
		while($row=$db->fetchRow($rs))
		{
			
			$pos=strpos((string) $row[2],"-");//echo $pos;
			$filename = $pos != "FALSE" || $pos > 0 ? substr((string) $row[2],($pos+1)) : $row[2];
			$filesize=filesize("attachments/2/$tablenumber/$mailid/$row[2]");
			$filesize=ceil($filesize/1024);

			$string.="<table><tr><td><input type=\"checkbox\" name=\"select_$i\" id=\"select_$i\" checked=\"true\" onclick=\" delete_item($row[0])\"></td><td>$filename($filesize kb)</td></tr></table>";
			$i++;
		}


		echo $string;
		exit;

	}

	function attachdivcontentAction()
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$userid=$this->getId();
		$folderName=$this->getParam(1);
		$mailid=$this->getParam(2);
		$folderid=$this->getfolderid($folderName);
		$no=0;$names="";
		$db= new NesoteDALController();
		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("name");
		$db->where("mailid=? and folderid=? and attachment=? and userid=?",[$mailid,$folderid,1,$userid]);
		$result=$db->query();
		while($row=$db->fetchRow($result))
		{
			$names.=$row[0].",";
			$no++;
		}
		if($no==0)
		{
			echo $no;
			exit;
		}
		else
		{
			$names=substr($names,0,-1);
			echo $no."/".$names;
			exit;
		}
	}

	function getattachmentIcon($mail,$folder)
	{
		$url=$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
		if(strpos($url,"/index.php")!="")
		{
			$url=str_replace("/index.php","",$url);

		}
		$url="http://".$url;
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$userid=$this->getId();
		$attach="";
		$db= new NesoteDALController();
		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("name");
		$db->where("mailid=? and folderid=? and attachment=? and userid=?",[$mail,$folder,1,$userid]);
		$result=$db->query();
		while($row=$db->fetchRow($result))
		{
			$pos=(strpos((string) $row[0],"-")+1);
			if($pos<=4)
			{
				$name=substr((string) $row[0],$pos);
				$pos1=(strpos($name,"-")+1);
				$name=substr((string) $row[0],$pos1);
			}
			else {
                $name=substr((string) $row[0],$pos);
            }
			$format=$this->checkImage($row[0]);
			if ($format==1) {
                $attach.=$url."/attachments/".$folder."/".$tablenumber."/".$mail."/".$row[0]."::".$name."::".$folder."::".$mail."::".$row[0]."::1,";
            } elseif ($format==2) {
                $attach.=$url."/images/video.png::".$name."::".$folder."::".$mail."::".$row[0]."::0,";
            } elseif ($format==3) {
                $attach.=$url."/images/audio.png::".$name."::".$folder."::".$mail."::".$row[0]."::0,";
            } else {
                $attach.=$url."/images/other.png::".$name."::".$folder."::".$mail."::".$row[0]."::0,";
            }
		}
		if ($attach !== "") {
            $attach=substr($attach,0,-1);
        }
		return $attach;
	}

	function checkImage($name)
	{
		$parts=explode(".",(string) $name);
		$db= new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",['imageformats']);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$types=explode(",",(string) $row[0]);
        $counter = count($types);
		for($i=0;$i<($counter);$i++)
		{
		    $ptr=strtolower($parts[1]);$ptr1=strtolower($types[$i]);
			if(($parts[1]==$types[$i])||($ptr === $ptr1))
			{
				return 1;
			}
		}
		
		$types1=explode(",","3gp,mpeg,mpg,wmv,mpeg-4,mp4");
        $counter = count($types1);
		for($i=0;$i<($counter);$i++)
		{
		 $ptr=strtolower($parts[1]);$ptr1=strtolower($types1[$i]);
			if(($parts[1]==$types1[$i]) || ($ptr === $ptr1))
			{
				return 2;
			}
		}
		
		$types2=explode(",","mp3,amr,wav");
        $counter = count($types2);
		for($i=0;$i<($counter);$i++)
		{
		$ptr=strtolower($parts[1]);$ptr1=strtolower($types2[$i]);
			if(($parts[1]==$types2[$i]) ||($ptr === $ptr1))
			{
				return 3;
			}
		}
		return 4;

	}

	function ajaxlivesearchAction()
	{

		$loop=$this->getParam(1);
		$this->setValue("loopcount",$loop);
	}
	function ajaxlivesearchbccAction()
	{
		$loop=$this->getParam(1);
		$this->setValue("loopcount",$loop);
	}
	function ajaxlivesearchccAction()
	{

		$loop=$this->getParam(1);
		$this->setValue("loopcount",$loop);

	}

	function createdraftAction(): never
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();


		$time=time();

		$db= new NesoteDALController();

		$position=$settings->getValue("time_zone_postion");

		$username=$_COOKIE['e_username'];


		$hour=$settings->getValue("time_zone_hour");


		$min=$settings->getValue("time_zone_mint");

		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? $diff : -$diff;
		$time += $diff;
		// echo date("d/m/y H:i:s",$time);
		//$time=$this->getParam(1);
		$id=$this->getId();
		$username=$_COOKIE['e_username'];
		$username .= $this->getextension();
		$db= new NesoteDALController();
		$db->insert("nesote_email_draft_$tablenumber");
		$db->fields("time,userid,from_list,just_insert,readflag");
		$db->values([$time,$id,$username,1,1]);
		$db->query();
		$lasTid=$db->lastInsert();
		echo $lasTid;exit;
	}

	function sendmailAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);

			$uid=$this->getId();
			$to=$_POST['to'];
			//print_r($_POST);
			$cc=$_POST['cc'];
			$bcc=$_POST['bcc'];
			$subject=$_POST['subject'];
			$content=$_POST['content'];//echo "********".$content."***********";exit;
			$magic=get_magic_quotes_gpc();
			if($magic==1)
			{
				$content=stripcslashes((string) $content);
			}


			$draftid=$_POST['draftid'];
			$mailid=$_POST['mailid'];
			$folder=$_POST['folder'];
			$folder=$this->getfolderid($folder);

			$db=new NesoteDALController();
			if ($folder==1) {
                $db->select("nesote_email_inbox_$tablenumber");
            } elseif ($folder==2) {
                $db->select("nesote_email_draft_$tablenumber");
            } elseif ($folder==3) {
                $db->select("nesote_email_sent_$tablenumber");
            } elseif ($folder==4) {
                $db->select("nesote_email_spam_$tablenumber");
            } elseif ($folder==5) {
                $db->select("nesote_email_trash_$tablenumber");
            } else {
                $db->select("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db->fields("mail_references,message_id");
			$db->where("id=?", [$mailid]);
			$result=$db->query();
			$row1=$db->fetchRow($result);

			if ($mailid==0) {
                $this->smtp($to,$cc,$bcc,$subject,$content,$uid,"","",$draftid,2,0);
            } else {
                $this->smtp($to,$cc,$bcc,$subject,$content,$uid,$row1[0],$row1[1],$draftid,$folder,$mailid);
            }

		}
	}

	function savemailAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);
			$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();

			$to=$_POST['to'];
			$cc=$_POST['cc'];
			$bcc=$_POST['bcc'];
			$subject=$_POST['subject'];
			$content=$_POST['content'];
			$draftid=$_POST['draftid'];
			$time=time();

			$db= new NesoteDALController();

			$position=$settings->getValue("time_zone_postion");

			$username=$_COOKIE['e_username'];


			$hour=$settings->getValue("time_zone_hour");


			$min=$settings->getValue("time_zone_mint");

			$diff=((3600*$hour)+(60*$min));

			$diff = $position == "Behind" ? $diff : -$diff;
			$time += $diff;

			//$username=$_POST['e_username'];
			//$db->select("nesote_users");
			//$db->fields("time_zone");
			//$db->where("username=?",array($username));
			//$res1=$db->query();
			//$row1=$db->fetchRow($res1);
			//$db->select("nesote_email_time_zone");
			//$db->fields("value");
			//$db->where("id=?",array($row1[0]));
			//$res1=$db->query();
			//$row1=$db->fetchRow($res1);
			//$timezone=$row1[0];
			//$sign=trim($timezone[0]);
			//$timezone1=substr($timezone,1);
			//$timezone1=explode(":",$timezone1);
			//$newtimezone=($timezone1[0]*60*60)+($timezone1[1]*60);
			//if($sign=="+")
			//$newtimezone=$newtimezone;
			//if($sign=="-")
			//$newtimezone=-$newtimezone;
			//$time=$time+$newtimezone;

			$reference="<references><item><mailid>".$draftid."</mailid><folderid>2</folderid></item></references>";


			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("just_insert");
			$db->where("id=?",[$draftid]);
			$rs1=$db->query();
			$row=$db->fetchRow($rs1);//echo $db->getQuery();
			$md5_references=md5($reference);

			$db->update("nesote_email_draft_$tablenumber");
			$db->set("to_list=?,cc=?,bcc=?,subject=?,body=?,time=?,just_insert=?,mail_references=?,md5_references=?",[$to,$cc,$bcc,$subject,$content,$time,0,$reference,$md5_references]);
			$db->where("id=?",[$draftid]);
			$rs=$db->query();
			$attach=$this->getattachmentIcon($draftid,2);
			$time=$this->gettime($time);
			$short_content=$this->substringMail($content,100);
			echo $row[0]."{nesote_;}".$attach."{nesote_;}".$time."{nesote_;}".$short_content;exit;
		}
	}

	function discardmailAction(): never
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$draftid=$_POST['draftid'];
		$db=new NesoteDALController();
		$db->delete("nesote_email_draft_$tablenumber");
		$db->where("id=?",[$draftid]);
		$db->query();
		echo $draftid;exit;
	}
	function smtp($to,$cc,$bcc,$subject,$html,$id,$mail_references,$in_reply_to,$draftid,$folders,$mails)
	{
		
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$uid=$this->getId();
		$folder=-1;
		$maild=-1;
		if($in_reply_to!="")
		{
			//echo $in_reply_to."jgfjyu";exit;
			$db=new NesoteDALController();
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("*");
			$db->where("message_id=? and userid=?", [$in_reply_to,$uid]);
			$result=$db->query();
			$row1=$db->fetchRow($result);
			$no=$db->numRows($result);
			if($no!=0)
			{
				$folder=1;
				$maild=$row1[0];

			}
			$db->select("nesote_email_sent_$tablenumber");
			$db->fields("*");
			$db->where("message_id=? and userid=?", [$in_reply_to,$uid]);
			$result=$db->query();
			$row1=$db->fetchRow($result);
			$no=$db->numRows($result);
			if($no!=0)
			{
				$folder=3;
				$maild=$row1[0];

			}
			$db->select("nesote_email_spam_$tablenumber");
			$db->fields("*");
			$db->where("message_id=? and userid=?", [$in_reply_to,$uid]);
			$result=$db->query();
			$row1=$db->fetchRow($result);
			$no=$db->numRows($result);
			if($no!=0)
			{
				$folder=4;
				$maild=$row1[0];

			}
			$db->select("nesote_email_trash_$tablenumber");
			$db->fields("*");
			$db->where("message_id=? and userid=?", [$in_reply_to,$uid]);
			$result=$db->query();
			$row1=$db->fetchRow($result);
			$no=$db->numRows($result);
			if($no!=0)
			{
				$folder=5;
				$maild=$row1[0];

			}
			$db->select("nesote_email_customfolder_mapping_$tablenumber");
			$db->fields("*");
			$db->where("message_id=?", [$in_reply_to]);
			$result=$db->query();
			$row1=$db->fetchRow($result);
			$no=$db->numRows($result);
			if($no!=0)
			{
				$folder=$row1[1];
				$maild=$row1[0];

			}
		}
	
		$uname=$this->getusername($id);//echo $uname;
		$db=new NesoteDALController();
		
		$mailextn_name=$this->getextension();
		$at=substr((string) $mailextn_name,0,1);
		if($at === "@")
		{
			$from=$uname.$mailextn_name;
			$mail_extension=$mailextn_name;
		}
		else
		{
			$from=$uname."@".$mailextn_name;
			$mail_extension="@".$mailextn_name;
		}

		
		$host_name=$settings->getValue("SMTP_host");

		
		$port_number=$settings->getValue("SMTP_port");

		$catch_all=$settings->getValue("catchall_mail");


		if($catch_all==1)
		{
			
			$SMTP_username=$settings->getValue("SMTP_username");

			$SMTP_password=$settings->getValue("SMTP_password");
		}
		else
		{
			$db->select("nesote_email_usersettings");
			$db->fields("server_password,smtp_username");
			$db->where("userid=?", [$id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$password=$row[0];
			$SMTP_password=base64_decode((string) $password);
			$SMTP_username=$row[1];
		}

		$db->select("nesote_email_sent_$tablenumber");
		$db->fields("id");
		$db->order("id desc");
		$db->limit(0,1);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$last_sentid=$row[0];
		$var=time().$id.$last_sentid;
		$msg_id=md5($var).$mail_extension;
		$message_id="<".$msg_id.">";
		//echo $SMTP_username."++++".$SMTP_password;

		require_once(__DIR__ . '/class/class.phpmailer.php');
		//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP

		try {
			$mail->Host       = $host_name; // SMTP server
			$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->Port       = $port_number;                    // set the SMTP port for the GMAIL server
			$mail->Username   = $SMTP_username; // SMTP account username
			$mail->Password   = $SMTP_password;
			$mail->MessageID  = $message_id;
			// SMTP account password
//echo $from;
			$mail->AddReplyTo($from);
			$mail->SetFrom($from);
			if ($in_reply_to!="") {
                $mail->AddCustomHeader("In-Reply-To:$in_reply_to");
            }
			$to_address="";
			$cc_address="";
			$bcc_address="";

					if($to!='')
					{
						$to=" ".$to;
						$to=explode(",",$to);

						foreach ($to as $address)
						{
							if(trim($address) !== '')
							{
								$address=" ".$address;
								
								$address=str_replace("\\","",$address);

								preg_match("/(.+?)<(.+?)>/i",$address,$mailid);
								if (count($mailid[2])==0) {
                                    preg_match("/(.+?)&lt;(.+?)&gt;/i",$address,$mailid);
                                }
								if($mailid[2]=="")
								{
									
									$mailid[2]=$address;
									$mailid[1]="";
								}
								$mailid[1]=str_replace("\"","",$mailid[1]);
								
								$mail->AddAddress($mailid[2],$mailid[1]);
								$to_address.=$mailid[1]."< ".$mailid[2].">,";
								$this->addcontact($mailid[2],$mailid[1]);
							}
						}
					}$to_address=trim($to_address);

					if($cc!='')
					{
						$cc=explode(",",(string) $cc);

						foreach ($cc as $address1)
						{
							if(trim($address1) !== '')
							{
								$address1=str_replace("\\","",$address1);

								preg_match("/(.+?)<(.+?)>/i",$address1,$mailid);
								if (count($mailid[2])==0) {
                                    preg_match("/(.+?)&lt;(.+?)&gt;/i",$address1,$mailid);
                                }

								if($mailid[2]=="")
								{

									$mailid[2]=$address1;
									$mailid[1]="";
								}
								$mailid[1]=str_replace("\"","",$mailid[1]);
								$mail->AddCC($mailid[2],$mailid[1]);
								$cc_address.=$mailid[1]."< ".$mailid[2].">,";
								$this->addcontact($mailid[2],$mailid[1]);


							}
						}
					}
			if($bcc!='')
			{
				$bcc=explode(",",(string) $bcc);
				foreach ($bcc as $address2)
				{
					if(trim($address2) !== '')
					{
						$address2=str_replace("\\","",$address2);

						preg_match("/(.+?)<(.+?)>/i",$address2,$mailid);
						if (count($mailid[2])==0) {
                            preg_match("/(.+?)&lt;(.+?)&gt;/i",$address2,$mailid);
                        }
						if($mailid[2]=="")
						{

							$mailid[2]=$address2;
							$mailid[1]="";
						}
						$mailid[1]=str_replace("\"","",$mailid[1]);
						$mail->AddBCC($mailid[2],$mailid[1]);
						$bcc_address.=$mailid[1]."< ".$mailid[2].">,";
						$this->addcontact($mailid[2],$mailid[1]);


					}
				}
			}
			
			$tme=time();

			$db= new NesoteDALController();
			
			$position=$settings->getValue("time_zone_postion");
			$username=$_COOKIE['e_username'];

		
			$hour=$settings->getValue("time_zone_hour");

		
			$min=$settings->getValue("time_zone_mint");

			$diff=((3600*$hour)+(60*$min));

			$diff = $position == "Behind" ? $diff : -$diff;

			$tme += $diff;
			


			$mail->SetFrom($from);
			$subjekt = "=?UTF-8?B?".base64_encode(strval($subject))."?=";
			$mail->Subject = $subjekt;
			$mail->SMTPSecure="ssl";
			$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically

			if ($folders==4) {
                $to_attach_id=4;
            } elseif ($folders==5) {
                $to_attach_id=5;
            } else {
                $to_attach_id=3;
            }

			if ($folders==4) {
                $db->insert("nesote_email_spam_$tablenumber");
                $db->fields("userid,from_list,to_list,cc,bcc,subject,status,readflag,starflag,memorysize,message_id,time,backreference");
                $db->values([$uid,$from,$to_address,$cc_address,$bcc_address,$subject,1,1,0,0,$message_id,$tme,3]);
            } elseif ($folders==5) {
                $db->insert("nesote_email_trash_$tablenumber");
                $db->fields("userid,from_list,to_list,cc,bcc,subject,status,readflag,starflag,memorysize,message_id,time,backreference");
                $db->values([$uid,$from,$to_address,$cc_address,$bcc_address,$subject,1,1,0,0,$message_id,$tme,3]);
            } else
			{
				$db->insert("nesote_email_sent_$tablenumber");
				$db->fields("userid,from_list,to_list,cc,bcc,subject,status,readflag,starflag,memorysize,message_id,time");
				$db->values([$uid,$from,$to_address,$cc_address,$bcc_address,$subject,1,1,0,0,$message_id,$tme]);
			}
			$res=$db->query();
			$lastid=$db->lastInsert();

			$mail->IsHTML(true);


			$p=0;
			//setcookie("draftid","1","0","/");
			$db2=new NesoteDALController();
			$db2->select("nesote_email_attachments_$tablenumber");
			$db2->fields("*");
			$db2->where("mailid=? and folderid=? and attachment=? and userid=?", [$draftid,2,1,$uid]);
			$result2=$db2->query();//echo $db2->getQuery();
			while($rw=$db2->fetchRow($result2))
			{
				$file_name[$p]=$rw[2];
				$flnam=explode(".",(string) $file_name[$p]);
				$extention=$flnam[1];
				$ac=0;
				$img_formats=$this->getimageformats();
				$img_format=explode(",",(string) $img_formats);
                $counter = count($img_format);
				for($a=0;$a<$counter;$a++)
				{
					if($extention==$img_format[$a])
					{
						$type="image/".$extention;
						$ac=1;
						break;
					}
				}
				if($ac==0)
				{
					$type="other/".$extention;
				}

				if ($file_name[$p]!= "." && $file_name[$p]!= "..")
				{
					$db3=new NesoteDALController();
					$db3->select("nesote_email_attachments_$tablenumber");
					$db3->fields("attachment");
					$db3->where("mailid=? and folderid=? and name=? and userid=?", [$draftid,2,$file_name[$p],$uid]);
					$result3=$db3->query();
					$rw1=$db3->fetchRow($result3);
							$mail->AddAttachment("attachments/2/$tablenumber/$draftid/$file_name[$p]",$file_name[$p],"base64",$type);
				}

				$p++;

			}

			//echo $draftid;
			//echo $mails;
			//if($folders!=2)
			//{
			$p=0;
			$db21=new NesoteDALController();
			$db21->select("nesote_email_attachments_$tablenumber");
			$db21->fields("*");
			$db21->where("mailid=? and folderid=? and attachment=? and userid=?", [$mails,$folders,0,$uid]);
			$result21=$db21->query();
			//echo $db21->getQuery();
			//echo $folders;
			$size=1;
			$new_html=$html;
			while($rw21=$db21->fetchRow($result21))
			{
				//echo $p;
				$file_names[$p]=$rw21[2];//echo $file_names[$p];



				if ($file_names[$p] != "." && $file_names[$p] != ".." && strpos((string) $new_html,"attachments/".$folders."/".$tablenumber."/".$mails."/".$file_names[$p]) != "FALSE") {
                    $url=$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
                    if(strpos($url,"/index.php")!="")
						{
							$url=str_replace("/index.php","",$url);

						}
                    $url1="http://".$url."/attachments/".$folders."/".$tablenumber."/".$mails."/".$file_names[$p];
                    //echo $url1;exit;
                    //	$mail->AddEmbeddedImage("http://www.overnight.co.za/images_clean/logo.jpg","overnight-logo","http://www.overnight.co.za/images_clean/logo.jpg","base64","image/jpg");
                    $cid=$p."_".$msg_id;
                    $mail->AddEmbeddedImage("attachments/".$folders."/".$tablenumber."/".$mails."/".$file_names[$p], $cid, $file_names[$p], "base64", "image/jpeg");
                    //	$mail->AddEmbeddedImage($url1,$cid,$file_names[$p]);
                    //echo $new_html."++++++++++++++";
                    $new_html=str_replace($url1,"cid:".$cid,$new_html);
                    //echo $new_html;
                    if((is_dir("attachments/".$to_attach_id))!=TRUE)
								{
									mkdir("attachments/".$to_attach_id,0777);
								}
                    if((is_dir("attachments/".$to_attach_id."/".$tablenumber))!=TRUE)
								{
									mkdir("attachments/".$to_attach_id."/".$tablenumber,0777);
								}
                    if((is_dir("attachments/".$to_attach_id."/".$tablenumber."/".$lastid))!=TRUE)
								{
									mkdir("attachments/".$to_attach_id."/".$tablenumber."/".$lastid,0777);
								}
                    //							if((is_dir("attachments/".$to_attach_id."/".$lastid))!=TRUE)
                    //							{
                    //								if((is_dir("attachments/".$to_attach_id))!=TRUE)
                    //								{
                    //									mkdir("attachments/".$to_attach_id,0777);
                    //								}
                    //								mkdir("attachments/".$to_attach_id."/".$lastid,0777);
                    //
                    //							}
                    copy("attachments/".$folders."/".$tablenumber."/".$mails."/".$file_names[$p],"attachments/".$to_attach_id."/".$tablenumber."/".$lastid."/".$file_names[$p]);
                    $filesize=filesize("attachments/".$folders."/".$tablenumber."/".$mails."/".$file_names[$p]);
                    $filesize=ceil($filesize/1024);
                    $size += $filesize;
                    $extention=explode(".",$rw[2]);
                    $len=count($extention);
                    $extention=$extention[($len-1)];
                    $extention=trim($extention);
                    $img_formats=$this->getimageformats();
                    $img_format=explode(",",(string) $img_formats);
                    $type="image/".$extention;
                    $db1=new NesoteDALController();
                    $db1->insert("nesote_email_attachments_$tablenumber");
                    $db1->fields("mailid,userid,name,folderid,attachment,type");
                    $db1->values([$lastid,$uid,$file_names[$p],$to_attach_id,0,$type]);
                    $res=$db1->query();
                    $new_html=str_replace($url1,"http://".$url."/attachments/".$to_attach_id."/".$tablenumber."/".$lastid."/".$file_names[$p],$new_html);
                    $mail->Body=$new_html;
                }
				$p++;
				//$d->close();
				//setcookie("draftid","1","0","/");
			}//echo $p;exit;
			if ($p==0) {
                $mail->Body=$html;
            }
			//}


			
			$mail->Send();


			$message_id=$mail->MessageID;


			$time=$this->getusertime();//echo $time;exit;

			//echo "Message Sent OK</p>\n";






			$mail_references=$this->modified_reference($mail_references,$lastid);
			//echo $mail_references;
			//echo $lastid;exit;


			//echo $html;
			$this->update_conversation($mail_references);

			$db2=new NesoteDALController();
			$db2->select("nesote_email_attachments_$tablenumber");
			$db2->fields("*");
			$db2->where("mailid=? and folderid=? and attachment=? and userid=?", [$draftid,2,1,$uid]);
			$result2=$db2->query();//echo $db2->getQuery();
			$num=$db2->numRows($result2);

				while($rw=$db2->fetchRow($result2))
				{
					if((is_dir("attachments/".$to_attach_id))!=TRUE)
					{
						mkdir("attachments/".$to_attach_id,0777);
					}
				 if((is_dir("attachments/".$to_attach_id."/".$tablenumber))!=TRUE)
				 {
				 	mkdir("attachments/".$to_attach_id."/".$tablenumber,0777);
				 }
				 if((is_dir("attachments/".$to_attach_id."/".$tablenumber."/".$lastid))!=TRUE)
				 {
				 	mkdir("attachments/".$to_attach_id."/".$tablenumber."/".$lastid,0777);
				 }

				
					//echo $entry;
					$filesize=filesize("attachments/2/".$tablenumber."/".$draftid."/".$rw[2]);
					$filesize=ceil($filesize/1024);
					$size += $filesize;
					$extention=explode(".",(string) $rw[2]);
					$len=count($extention);
					$extention=$extention[($len-1)];
					$extention=trim($extention);
					$acc=0;
					$img_formats=$this->getimageformats();
					$img_format=explode(",",(string) $img_formats);
                    $counter = count($img_format);
					for($a=0;$a<$counter;$a++)
					{
						if($extention==$img_format[$a])
						{
							$type="image/".$extention;
							$acc=1;
							break;
						}
					}
					if($acc==0)
					{
						$type="other/".$extention;
					}

					$filename = $extention === "exe" ? str_replace("exe","qqq",$rw[2]) : $rw[2];

					copy("attachments/2/".$tablenumber."/".$draftid."/".$filename,"attachments/".$to_attach_id."/".$tablenumber."/".$lastid."/".$filename);
					unlink("attachments/2/".$tablenumber."/".$draftid."/".$filename);
					//echo $filename."+++++++++++".$rw[5];
					$db1=new NesoteDALController();
					$db1->insert("nesote_email_attachments_$tablenumber");
					$db1->fields("mailid,userid,name,folderid,attachment,type");
					$db1->values([$lastid,$uid,$filename,$to_attach_id,$rw[5],$type]);
					$res=$db1->query();//echo $db1->getQuery();
					$db5=new NesoteDALController();
					$db5->delete("nesote_email_attachments_$tablenumber");
					$db5->where("id=? ",[$rw[0]]);
					$db5->query();
				}
				if($num!=0)
				{
					rmdir("attachments/2/".$tablenumber."/".$draftid);

					//rmdir($mydir);


					$db3=new NesoteDALController();
					$db3->delete("nesote_email_attachments_$tablenumber");
					$db3->where("mailid=? and folderid=? and userid=?",[$draftid,2,$uid]);
					$res=$db3->query();
			}

			//echo $html."+++++++".$num;exit;
			$md5_mail_references=md5((string) $mail_references);
			$returnstring="";
			$db1=new NesoteDALController();
			if ($folders==4) {
                $db1->update("nesote_email_spam_$tablenumber");
            } elseif ($folders==5) {
                $db1->update("nesote_email_trash_$tablenumber");
            } else {
                $db1->update("nesote_email_sent_$tablenumber");
            }
			$db1->set("mail_references=?,md5_references=?,body=?,time=?,memorysize=?",[$mail_references,$md5_mail_references,$html,$time,$size]);
			$db1->where("id=?",$lastid);
			$res1=$db1->query();
			$db3=new NesoteDALController();
			$db3->delete("nesote_email_draft_$tablenumber");
			$db3->where("id=?",$draftid);
			$res=$db3->query();

			//$returnstring=$this->getarray($mail_references);echo $returnstring;


			$select1=new NesoteDALController();
			//			if($folders==4)
			//			$select1->select("nesote_email_spam_$tablenumber");
			//			else if($folders==5)
			//			$select1->select("nesote_email_trash_$tablenumber");
			//			else
			$select1->select("nesote_email_sent_$tablenumber");
			$select1->fields("id,from_list,to_list,cc,bcc,subject,body,readflag,starflag,time,status,userid,mail_references");
			$select1->where("id=?",[$lastid]);
			$result1=$select1->query();$arry1="";

			if($row1=$select1->fetchRow($result1))
			{
				for($i=0;$i<8;$i++)
				{
					if ($i==5) {
                        $arry1.=$this->substringMail($row1[$i],30)."{nesote_,}";
                    } elseif ($i==6) {
                        $arry1.=$this->substringMail(html_entity_decode((string) $row1[$i]),50)."{nesote_,}";
                    } else {
                        $arry1.=$row1[$i]."{nesote_,}";
                    }
				}
				//preg_match_all('/<item>(.+?)<\/item>/i',$row1[12],$reply);//print_r($reply);
				$arry1.=$this->getstar($row1[12],$row1[0],1)."{nesote_,}";
				$arry1.=$this->gettime($row1[9])."{nesote_,}";
				$arry1.=$this->getattachcount($row1[12])."{nesote_,}";
				$arry1.=$row1[9]."{nesote_,}";
				$arry1.="sent{nesote_,}";
				$arry1.="1{nesote_,}";
				$arry1.="sent_".$row1[0]."{nesote_,}";
				$arry1.="sent_".$row1[0];





				for($i=0;$i<(9);$i++)
				{
					if($i==6)
					{
						$external=$this->getExternalcontentFlag($row1[$i],$row1[1]);
						$externals=explode("{nesote_comma}",(string) $external);
						$arry.=$externals[0]."{nesote_,}";
						//$arry.="{nesote_,}";
						$extnl_flg=$externals[1];
					}
					else {
                        $arry.=$row1[$i]."{nesote_,}";
                    }
				}

				$arry.=$this->gettime($row1[9])."{nesote_,}";
				$arry.=$this->getattachmentIcon($row1[0],3)."{nesote_,}";
				$arry.="1{nesote_,}";
				$arry.="sent{nesote_,}";
				//$arry.=$row1[6]."{nesote_,}";
				$arry.="{nesote_,}";
				$arry.=$extnl_flg."{nesote_,}";
				$arry.=$row1[9]."{nesote_,}";
				$arry.=$this->substringMail(html_entity_decode((string) $row1[6]),50)."{nesote_,}";
				$arry.="sent_".$row1[0]."{nesote_,}";
				$arry.=$this->gettimeinside($row1[9])."{nesote_,}";
				$arry.="sent_".$row1[0];


			}
			$arry.="{nesote_ref}";
			$arry=substr($arry,0,-12);
			$returnstring.=$arry1."{nesote_:}".$arry."{nesote_separator}";
			$returnstring=substr($returnstring,0,-18);
			$returnstring .= "{nesote_count}1";
			
			$this->saveLogs("Sent Mail",$username." has sent a mail");

		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
		}
		echo "****".$returnstring;exit;


	}
	function update_conversation($mail_references)
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		preg_match_all('/<item>(.+?)<\/item>/i',(string) $mail_references,$reply);
		//print_r($reply);
		$no=count($reply[1]);
		$md5_references=md5((string) $mail_references);
		for($i=0;$i<$no;$i++)
		{
			preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid);
			preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid);//echo $mailid[1]."p".$folderid[1]."n";
			$db=new NesoteDALController();
			if($folderid[1]!=2)
			{
				if ($folderid[1]==1) {
                    $db->update("nesote_email_inbox_$tablenumber");
                } elseif ($folderid[1]==3) {
                    $db->update("nesote_email_sent_$tablenumber");
                } elseif ($folderid[1]==4) {
                    $db->update("nesote_email_spam_$tablenumber");
                } elseif ($folderid[1]==5) {
                    $db->update("nesote_email_trash_$tablenumber");
                } elseif ($folderid[1]>=10) {
                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->set("mail_references=?,md5_references=?",[$mail_references,$md5_references]);
				$db->where("id=?",[$mailid[1]]);
				$rs=$db->query();
			}
		}
	}
	function modified_reference($mail_references,$lastid)
	{
		//echo $mail_references;exit;
		if($mail_references=="")
		{
			$mail_references="<references><item><mailid>$lastid</mailid><folderid>3</folderid></item></references>";
		}
		else
		{
					$trash_flag=0;$spam_flag=0;
			preg_match_all('/<item>(.+?)<\/item>/i',(string) $mail_references,$reply);
			//print_r($reply);
			$no=count($reply[1]);
			for($i=0;$i<$no;$i++)
			{
				preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid);
				preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid);//echo $mailid[1]."p".$folderid[1]."n";
						if ($folderid[1]==4) {
                            $spam_flag=1;
                        } elseif ($folderid[1]==5) {
                            $trash_flag=1;
                        }
						
				if($folderid[1]==2)
				{

					$replace="<item><mailid>$mailid[1]</mailid><folderid>2</folderid></item>";
					$mail_references=str_replace($replace,"",$mail_references);

				}
			}
					if ($spam_flag==1) {
                        $references="<item><mailid>$lastid</mailid><folderid>4</folderid></item></references>";
                    } elseif ($trash_flag==1) {
                        $references="<item><mailid>$lastid</mailid><folderid>5</folderid></item></references>";
                    } else {
                        $references="<item><mailid>$lastid</mailid><folderid>3</folderid></item></references>";
                    }

			$mail_references=str_replace("</references>",$references,$mail_references);
		}

		return $mail_references;
	}
	function getusertime()
	{
		    $db= new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?",\TIME_ZONE_POSTION);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$position=$row[0];

			$db1= new NesoteDALController();
			$db1->select("nesote_email_settings");
			$db1->fields("value");
			$db1->where("name=?",\TIME_ZONE_HOUR);
			$result1=$db1->query();
			$row1=$db1->fetchRow($result1);
			$hour=$row1[0];

			$db2= new NesoteDALController();
			$db2->select("nesote_email_settings");
			$db2->fields("value");
			$db2->where("name=?",\TIME_ZONE_MINT);
			$result2=$db2->query();
			$row2=$db2->fetchRow($result2);
			$min=$row2[0];

			$diff=((3600*$hour)+(60*$min));
            $diff = $position == "Behind" ? -$diff : $diff;
			return time()-$diff;
		
		
		
	}
	function getimageformats()
	{

		return "jpeg,jpg,png,gif,bmp,psd,thm,tif,yuv,3dm,pln";
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
	function addcontact($mailid,$name)
	{
	    $mailid=trim((string) $mailid);
	    $name=trim((string) $name);
		$userid=$this->getId();
		$select=new NesoteDALController();
		$no=$select->total("nesote_email_contacts","mailid=? and addedby=?",[$mailid,$userid]);
		if($no==0)
		{

			if($name !== "")
			{
				$select->insert("nesote_email_contacts");
				$select->fields("mailid,addedby,contactgroup,firstname");
				$select->values([$mailid,$userid,0,$name]);
				$select->query();
			}
			else
			{
				$select->insert("nesote_email_contacts");
				$select->fields("mailid,addedby,contactgroup");
				$select->values([$mailid,$userid,0]);
				$select->query();
			}
			return;
		}
		else {
            return;
        }
	}
	function livesearchAction()
	{

		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
		$string=$this->getParam(1);
		$loop=$this->getParam(2);
		$target="_$loop";
		$string=htmlentities((string) $string);
		$magic=get_magic_quotes_gpc();
		if($magic==1)
		{

			$string=stripslashes($string);
		}
		//$x=$string;
		$id=$this->getId();
		//$xmlDoc = new DOMDocument();
		//$xmlDoc->load("ajax/links.xml");

		//$x=$xmlDoc->getElementsByTagName('link');

		//get the q parameter from URL
		$q=$_GET["q"];

		$db= new NesoteDALController();
		$db->select("nesote_email_contacts");
		$db->fields("distinct mailid,firstname,lastname");
		$db->where("mailid like '%$q%' and addedby=? ",[$id]);
		$res=$db->query();
		$hint="";
		$j=0;
		$result=[];
		while($row=$db->fetchRow($res))
		{



			$result[$j]=$row[0];

			$value=$string." ";

			$mail="";
			if($row[1]!="" || $row[2]!="")
			{

				$value.="&quot;".$row[1]." ".$row[2]."&quot;";
				$mail="&quot;".$row[1]." ".$row[2]."&quot;";
			}

			$value.="&lt;".$row[0]."&gt;";
			//$value.=htmlspecialchars_decode($l);
			//	$setvalue=htmlspecialchars_decode($value);
			$mail.="&lt;".$row[0]."&gt;";
			$loop1=$loop."_".$j;
			$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$loop1\"  onclick=\"javascript:setvaluefortb('$value','$target','$loop')\" style='color:#666666;' >$mail<input type='hidden' id=\"livesearch_h_$loop1\" value=\"&quot;$row[1]  $row[2]&quot;&lt;$row[0]&gt;\"><input type='hidden' id=\"result_$loop1\" value=\"$value\"></div></td></tr></table>";

			//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string$row[0]')\" style='color:#666666;' >&nbsp;".$row[0]."<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";
			$j++;
		}


		$db->select("nesote_email_contactgroup");
		$db->fields("name,id");
		$db->where("name like '%$q%' and userid=?  ",[$id]);
		$res=$db->query();

		$db1= new NesoteDALController();
		while($row =$db->fetchRow($res))
		{
			$result[$j]=$row[0];
			$groupid=$row[1];

			//$x=$string;

			$db1->select("nesote_email_contacts");
			$db1->fields("distinct mailid,firstname,lastname");
			$db1->where(" contactgroup =? and addedby=?  ",[$groupid,$id]);
			$res1=$db1->query();
			$t=$db1->getQuery();
			$k=0;
			$loop1=$loop."_".$j;
			//$value1="$string&nbsp;";
			while($row1 =$db1->fetchRow($res1))
			{

				if(substr($x,0,-1) !== "," && $k != 0)
				{
					$x.=",";
				}

				$value1 = $row1[1] != "" || $row1[2] != "" ? "&quot;".$row1[1]." ".$row1[2]."&quot;" : "";


				$mail1.="$row[0]";
				$x.=$value1."&lt;".$row1[0]."&gt;";//

				//	$y="&quot;$row1[1]&nbsp;$row1[2]&quot;&lt;$row1[0]&gt".",";
				$k++;
				//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string&quot;$row1[1]&nbsp;$row1[2]&quot;&lt;$row1[0]&gt;','$loop')\" style='color:#666666;' >&nbsp;".$row[0]."(group)<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";

			}

			$y=$string." ".$x;

			$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$loop1\"  onclick=\"javascript:setvaluefortb('$y','$target','$loop')\" style='color:#666666;' > ".$row[0]."(".$this->getmessage(49)."))<input type='hidden' id=\"livesearch_h_$loop1\" value=\"$row[0]\"><input type='hidden' id=\"result_$loop1\" value=\"$x\"></div></td></tr></table>";

			$j++;
		}



		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='globaladdress_book'");
		$result=$db->query();//echo $select->getQuery();
		$res5=$db->fetchRow($result);
		$addressbook=$res5[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='emailextension'");
		$result4=$db->query();
		$row4=$db->fetchRow($result4);
		$emailextension = stristr(trim((string) $row4[0]),"@") != "" ? $row4[0] : "@".$row4[0];

		if($addressbook==1)
		{


			$db->select("nesote_liberyus_users");
			//$db->fields("username,firstname,lastname");
            $db->fields("username,name");
			$db->where("username like '%$q%' and status=? ",1);
			$res2=$db->query();

			while($row2 =$db->fetchRow($res2))
			{
				$usern=$row2[0].$emailextension;


				$db1->select("nesote_email_contacts");
				$db1->fields("mailid");
				$db1->where(" contactgroup =? and addedby=? and mailid=?   ",[$groupid,$id,$usern]);
				$res3=$db1->query();
				$mNo=$db1->numRows($res3);
				if($mNo==0)
				{

					$loop1=$loop."_".$j;

					$value2="$string ";

					if($row2[1]!="")
					{
						$value2.="&quot;".$row2[1]."&quot;";
						$mail2="&quot;".$row2[1]."&quot;";

					}
					$value2.="&lt;".$row2[0].$emailextension."&gt;";
					$mail2.="&lt;".$row2[0].$emailextension."&gt;";

					$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$loop1\"  onclick=\"javascript:setvaluefortb('$value2','$target','$loop')\" style='color:#666666;' >$mail2<input type='hidden' id=\"livesearch_h_$loop1\" value=\"&quot;$row2[1]&quot;&lt;".$row2[0].$emailextension."&gt;\"><input type='hidden' id=\"result_$loop1\" value=\"$value2\"></div></td></tr></table>";

					//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string$row[0]')\" style='color:#666666;' >&nbsp;".$row[0]."<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";
					$j++;
				}
			}
		}
		if ($hint === "")
		{
			$response="<div id=nomatch_$loop style='color:#999999;background-color: #eeeeee'>No match for '$q'</div>";
		}
		else
		{
			$response=$hint;
		}
		$response.="<input type='hidden' id=\"total_$loop\" value=\"$j\">";
		//output the response
		echo $loop."}".$response;
		exit(0);
	}
	function livesearchbccAction()
	{

		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
		$string=$this->getParam(1);
		$loop=$this->getParam(2);
		$target="bcc_$loop";
		$string=htmlentities((string) $string);
		$magic=get_magic_quotes_gpc();
		if($magic==1)
		{

			$string=stripslashes($string);
		}
		//$x=$string;
		$id=$this->getId();
		//$xmlDoc = new DOMDocument();
		//$xmlDoc->load("ajax/links.xml");

		//$x=$xmlDoc->getElementsByTagName('link');

		//get the q parameter from URL
		$q=$_GET["q"];

		$db= new NesoteDALController();
		$db->select("nesote_email_contacts");
		$db->fields("distinct mailid,firstname,lastname");
		$db->where("mailid like '%$q%' and addedby=? ",[$id]);
		$res=$db->query();
		$hint="";
		$j=0;
		$result=[];
		while($row=$db->fetchRow($res))
		{



			$result[$j]=$row[0];
			$mail="";
			$value=$string." ";

			if($row[1]!="" || $row[2]!="")
			{
				$value.="&quot;".$row[1]." ".$row[2]."&quot;";
				$mail="&quot;".$row[1]." ".$row[2]."&quot;";
			}
			$value.="&lt;$row[0]&gt;";
			$mail.="&lt;$row[0]&gt;";
			$loop1=$loop."_".$j;
			$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearchbcc_a_$loop1\"  onclick=\"javascript:setvaluefortb2('$value','$target','$loop')\" style='color:#666666;' >$mail<input type='hidden' id=\"livesearchbcc_h_$loop1\" value=\"&quot;$row[1] $row[2]&quot;&lt;$row[0]&gt;\"><input type='hidden' id=\"resultbcc_$loop1\" value=\"$value\"></div></td></tr></table>";

			//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string$row[0]')\" style='color:#666666;' > ".$row[0]."<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";
			$j++;
		}

		$db= new NesoteDALController();
		$db->select("nesote_email_contactgroup");
		$db->fields("name,id");
		$db->where("name like '%$q%' and userid=?  ",[$id]);
		$res=$db->query();


		while($row =$db->fetchRow($res))
		{
			$result[$j]=$row[0];
			$groupid=$row[1];

			//$x=$string;
			$db1= new NesoteDALController();
			$db1->select("nesote_email_contacts");
			$db1->fields("distinct mailid,firstname,lastname");
			$db1->where(" contactgroup =? and addedby=?  ",[$groupid,$id]);
			$res1=$db1->query();
			$t=$db1->getQuery();
			$k=0;
			$loop1=$loop."_".$j;
			while($row1 =$db1->fetchRow($res1))
			{

				if(substr($x,0,-1) !== "," && $k != 0)
				{
					$x.=",";
				}
				//$value1="$string&nbsp;";
				$value1 = $row1[1] != "" || $row1[2] != "" ? "&quot;".$row1[1]." ".$row1[2]."&quot;" : "";


				$mail1.="$row[0]";
				$x.="$value1&lt;$row1[0]&gt;";//

				//	$y="&quot;$row1[1]&nbsp;$row1[2]&quot;&lt;$row1[0]&gt".",";
				$k++;
				//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string&quot;$row1[1]&nbsp;$row1[2]&quot;&lt;$row1[0]&gt;','$loop')\" style='color:#666666;' >&nbsp;".$row[0]."(group)<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";

			}
			$y="$string $x";
			$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearchbcc_a_$loop1\"  onclick=\"javascript:setvaluefortb2('$y','$target','$loop')\" style='color:#666666;' > ".$row[0]."(".$this->getmessage(49)."))<input type='hidden' id=\"livesearchbcc_h_$loop1\" value=\"$row[0]\"><input type='hidden' id=\"resultbcc_$loop1\" value=\"$x\"></div></td></tr></table>";

			$j++;
		}


		$select=new NesoteDALController();
		$select->select("nesote_email_settings");
		$select->fields("value");
		$select->where("name='globaladdress_book'");
		$result=$select->query();//echo $select->getQuery();
		$res5=$select->fetchRow($result);
		$addressbook=$res5[0];
		$db4=new NesoteDALController();
		$db4->select("nesote_email_settings");
		$db4->fields("value");
		$db4->where("name='emailextension'");
		$result4=$db4->query();
		$row4=$db4->fetchRow($result4);
		$emailextension = stristr(trim((string) $row4[0]),"@") != "" ? $row4[0] : "@".$row4[0];
		if($addressbook==1)
		{

			$db2= new NesoteDALController();
			$db2->select("nesote_liberyus_users");
			$db2->fields("username,name");
			$db2->where("username like '%$q%' and status=? ",1);
			$res2=$db2->query();
			while($row2 =$db2->fetchRow($res2))
			{
				$usern=$row2[0].$emailextension;

				$db3= new NesoteDALController();
				$db3->select("nesote_email_contacts");
				$db3->fields("mailid");
				$db3->where(" contactgroup =? and addedby=? and mailid=?   ",[$groupid,$id,$usern]);
				$res3=$db3->query();
				$mNo=$db3->numRows($res3);
				if($mNo==0)
				{
					$loop1=$loop."_".$j;

					$value2=$string." ";
					if($row2[1]!="" || $row2[2]!="")
					{
						$value2.="&quot;".$row2[1]."&quot;";
						$mail2="&quot;".$row2[1]."&quot;";

					}
					$value2.="&lt;".$row2[0].$emailextension."&gt;";
					$mail2.="&lt;".$row2[0].$emailextension."&gt;";

					$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearchbcc_a_$loop1\"  onclick=\"javascript:setvaluefortb2('$value2','$target','$loop')\" style='color:#666666;' >$mail2<input type='hidden' id=\"livesearchbcc_h_$loop1\" value=\"&quot;$row2[1] &quot;&lt;".$row2[0].$emailextension."&gt;\"><input type='hidden' id=\"resultbcc_$loop1\" value=\"$value2\"></div></td></tr></table>";

					//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string$row[0]')\" style='color:#666666;' >&nbsp;".$row[0]."<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";
					$j++;
				}
			}
		}
        $response = $hint === "" ? "<div id=nomatch style='color:#999999'>".$this->getmessage(414)." '$q'</div>" : $hint;
		$response.="<input type='hidden' id=\"totalbcc_$loop\" value=\"$j\">";
		//output the response
		echo "$loop}$response";
		exit(0);
	}
	function livesearchccAction()
	{
		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
		$string=$this->getParam(1);
		$loop=$this->getParam(2);
		$target="cc_$loop";
		$string=htmlentities((string) $string);
		$magic=get_magic_quotes_gpc();
		if($magic==1)
		{

			$string=stripslashes($string);
		}
		//$x=$string;
		$id=$this->getId();
		//$xmlDoc = new DOMDocument();
		//$xmlDoc->load("ajax/links.xml");

		//$x=$xmlDoc->getElementsByTagName('link');

		//get the q parameter from URL
		$q=$_GET["q"];

		$db= new NesoteDALController();
		$db->select("nesote_email_contacts");
		$db->fields("distinct mailid,firstname,lastname");
		$db->where("mailid like '%$q%' and addedby=? ",[$id]);
		$res=$db->query();
		$hint="";
		$j=0;
		$result=[];
		while($row=$db->fetchRow($res))
		{



			$result[$j]=$row[0];

			$value=$string." ";
			$mail="";
			if($row[1]!="" || $row[2]!="")
			{
				$value.="&quot;".$row[1]." ".$row[2]."&quot;";
				$mail="&quot;".$row[1]." ".$row[2]."&quot;";
			}
			$value.="&lt;$row[0]&gt;";
			$mail.="&lt;$row[0]&gt;";
			$loop1=$loop."_".$j;
			$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearchcc_a_$loop1\"  onclick=\"javascript:setvaluefortb1('$value','$target','$loop')\" style='color:#666666;' >$mail<input type='hidden' id=\"livesearchcc_h_$loop1\" value=\"&quot;$row[1] $row[2]&quot;&lt;$row[0]&gt;\"><input type='hidden' id=\"resultcc_$loop1\" value=\"$value\"></div></td></tr></table>";

			//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string$row[0]')\" style='color:#666666;' > ".$row[0]."<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";
			$j++;
		}

		$db= new NesoteDALController();
		$db->select("nesote_email_contactgroup");
		$db->fields("name,id");
		$db->where("name like '%$q%' and userid=?  ",[$id]);
		$res=$db->query();


		while($row =$db->fetchRow($res))
		{
			$result[$j]=$row[0];
			$groupid=$row[1];

			//$x=$string;
			$db1= new NesoteDALController();
			$db1->select("nesote_email_contacts");
			$db1->fields("distinct mailid,firstname,lastname");
			$db1->where(" contactgroup =? and addedby=?  ",[$groupid,$id]);
			$res1=$db1->query();
			$t=$db1->getQuery();
			$k=0;
			$loop1=$loop."_".$j;
			while($row1 =$db1->fetchRow($res1))
			{

				if(substr($x,0,-1) !== "," && $k != 0)
				{
					$x.=",";
				}
				//$value1="$string&nbsp;";
				$value1 = $row1[1] != "" || $row1[2] != "" ? "&quot;".$row1[1]." ".$row1[2]."&quot;" : "";


				$mail1.="$row[0]";
				$x.="$value1&lt;$row1[0]&gt;";//

				//	$y="&quot;$row1[1]&nbsp;$row1[2]&quot;&lt;$row1[0]&gt".",";
				$k++;
				//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string&quot;$row1[1]&nbsp;$row1[2]&quot;&lt;$row1[0]&gt;','$loop')\" style='color:#666666;' >&nbsp;".$row[0]."(group)<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";

			}
			$y="$string $x";
			$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearchcc_a_$loop1\"  onclick=\"javascript:setvaluefortb1('$y','$target','$loop')\" style='color:#666666;' > ".$row[0]."(".$this->getmessage(49)."))<input type='hidden' id=\"livesearchcc_h_$loop1\" value=\"$row[0]\"><input type='hidden' id=\"resultcc_$loop1\" value=\"$x\"></div></td></tr></table>";

			$j++;
		}


		$select=new NesoteDALController();
		$select->select("nesote_email_settings");
		$select->fields("value");
		$select->where("name='globaladdress_book'");
		$result=$select->query();//echo $select->getQuery();
		$res5=$select->fetchRow($result);
		$addressbook=$res5[0];
		$db4=new NesoteDALController();
		$db4->select("nesote_email_settings");
		$db4->fields("value");
		$db4->where("name='emailextension'");
		$result4=$db4->query();
		$row4=$db4->fetchRow($result4);
		$emailextension = stristr(trim((string) $row4[0]),"@") != "" ? $row4[0] : "@".$row4[0];
		if($addressbook==1)
		{

			$db2= new NesoteDALController();
			$db2->select("nesote_liberyus_users");
			$db2->fields("username,name");
			$db2->where("username like '%$q%' and status=? ",1);
			$res2=$db2->query();
			while($row2 =$db2->fetchRow($res2))
			{
				$usern=$row2[0].$emailextension;

				$db3= new NesoteDALController();
				$db3->select("nesote_email_contacts");
				$db3->fields("mailid");
				$db3->where(" contactgroup =? and addedby=? and mailid=?   ",[$groupid,$id,$usern]);
				$res3=$db3->query();
				$mNo=$db3->numRows($res3);
				if($mNo==0)
				{
					$loop1=$loop."_".$j;

					$value2=$string." ";
					if($row2[1]!="" || $row2[2]!="")
					{
						$value2.="&quot;".$row2[1]."&quot;";
						$mail2="&quot;".$row2[1]."&quot;";

					}
					$value2.="&lt;".$row2[0].$emailextension."&gt;";
					$mail2.="&lt;".$row2[0].$emailextension."&gt;";

					$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color: #eeeeee\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearchcc_a_$loop1\"  onclick=\"javascript:setvaluefortb1('$value2','$target','$loop')\" style='color:#666666;' >$mail2<input type='hidden' id=\"livesearchcc_h_$loop1\" value=\"&quot;$row2[1] &quot;&lt;".$row2[0].$emailextension."&gt;\"><input type='hidden' id=\"resultcc_$loop1\" value=\"$value2\"></div></td></tr></table>";

					//$hint.="<table  cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td  nowrap=\"nowrap\"><div  id=\"livesearch_a_$i\"  onclick=\"javascript:setvaluefortb('$string$row[0]')\" style='color:#666666;' > ".$row[0]."<input type='hidden' id=\"livesearch_a_$i\" value=\"$row[0]\"></div></td></tr></table>";
					$j++;
				}
			}
		}
        $response = $hint === "" ? "<div id=nomatch style='color:#999999'>".$this->getmessage(414)." '$q'</div>" : $hint;
		$response.="<input type='hidden' id=\"totalcc_$loop\" value=\"$j\">";
		//output the response
		echo "$loop}$response";
		exit(0);
	}
	function upload_frameAction()
	{
		//setcookie("draftid","0","0","/");
		if (substr_count((string) $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== 0) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }

		$this->getId();

		$draftid=$this->getParam(1);
		$filevar=$this->getParam(2);
		$designId=$this->getParam(3);
		$fl=1;
		if ($designId=="0_0") {
            $fl=0;
        }
		$this->setValue("fl",$fl);
		$this->setValue("draftid",$draftid);
		$this->setValue("filevar",$filevar);
		$this->setValue("designid",$designId);
	}
	function file_addprocessAction()
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		$userid=$this->getId();
		$select=new NesoteDALController();
	

		$draftid=$_POST['draftid'];
		$designid=$_POST['designid'];
		$this->setValue("designid",$designid);
		$fl=1;
		if ($designid=="0_0") {
            $fl=0;
        }
		$this->setValue("fl",$fl);


		$select->select("nesote_email_draft_$tablenumber");
		$select->fields("memorysize");
		$select->where("id=? and just_insert=?",[$draftid,0]);
		$rs=$select->query();
		$row=$select->fetchRow($rs);
		$filesize=$row[0];
		$filevar=$_POST['filevar'];
		$uid=$this->getId();
		$size=$_FILES['filez']['size']/1024;
		
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		$restric_types=$settings->getValue("restricted_attachment_types");

		$allowed_attachment_size=$settings->getValue("attachment_size");
		
		$cal_size=$size/1024;
		 if ($allowed_attachment_size<$cal_size) {
             $this->setValue("filename",-3);
         } elseif ($cal_size<=0) {
             $this->setValue("filename",-2);
         } else
  	   {
  		$size=ceil($size);
  		$filesize += $size;
  		$time=time();
  		$filename=$time."-".$_FILES["filez"]["name"];
  		$file=explode(".",$filename);
  		$no=count($file);
  		$filetype=trim($file[($no-1)]);
  
  		$len=strlen((string) $restric_types);
  		if ($restric_types[($len-1)]==",") {
              $restric_types=substr((string) $restric_types,0,-1);
          }
  		$types=explode(",",(string) $restric_types);
  		$length=count($types);
  		$restricted=0;
  		for($a=0;$a<$length;$a++)
  		{
  			$b=trim($types[$a]);
  			if($b[0]==".")
  			{
  				$b=substr($b,1);
  				$b=trim($b);
  			}
  			if($filetype === $b)
  			{
  				$restricted=1;
  				break;
  			}
  		}
  		//echo $restricted."jgddds";exit;
  		if($restricted==0)
  		{
  			$var=strpos($filename,"-");
  			$namez = $var != "FALSE" ? substr($filename,($var+1)) : $filename;
  			$this->setValue("filename",$namez);
  			if ($filetype === "exe") {
                  $filename=str_replace("exe","qqq",$filename);
              }
  			$references="<references><item><mailid>$draftid</mailid><folderid>2</folderid></item></references>";
  
  			$select->update("nesote_email_draft_$tablenumber");
  			$select->set("userid=?,memorysize=?,mail_references=?",[$uid,$filesize,$references]);
  			$select->where("id=?",$draftid);
  			$res=$select->query();
  			$select->insert("nesote_email_attachments_$tablenumber");
  			$select->fields("mailid,userid,folderid,name,attachment");
  			$select->values([$draftid,$userid,2,$filename,1]);
  			$res=$select->query();
  			$id=$select->lastInsert();
  
  
  					setcookie("file_$filevar",0, ['expires' => "0", 'path' => "/"]);
  
  					if((is_dir("attachments/2"))!=TRUE)
  					{
  						mkdir("attachments/2",0777);
  					}
  					if((is_dir("attachments/2/".$tablenumber))!=TRUE)
  					{
  						mkdir("attachments/2/".$tablenumber,0777);
  
  					}
  					if((is_dir("attachments/2/".$tablenumber."/".$draftid))!=TRUE)
  					{
  						mkdir("attachments/2/".$tablenumber."/".$draftid,0777);
  					}
  						
  
  
  					copy($_FILES['filez']['tmp_name'],"attachments/2/".$tablenumber."/".$draftid."/".$filename);
  
  			$url=$this->url("mail/delete_attachment/$draftid/$id");
  
  
  			setcookie("file_$filevar",1, ['expires' => "0", 'path' => "/"]);
  			$this->setValue("dratfid",$draftid);
  			$this->setValue("filevar",$filevar);
  
  			$this->setValue("url",$url);
  			setcookie("file_$filevar",1, ['expires' => "0", 'path' => "/"]);
  		}
  		else
  		{
  			$this->setValue("filename",-1);
  		}
  		}
	}
	function delete_attachmentAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$username=$_COOKIE['e_username'];
			$tablenumber=$this->tableid($username);
			$userid=$this->getId();
			$mailid=$this->getParam(1);
			$id=$this->getParam(2);

			$db=new NesoteDALController();
			$db->select("nesote_email_attachments_$tablenumber");
			$db->fields("name");
			$db->where("mailid=? and id=? and folderid=? and userid=?",[$mailid,$id,2,$userid]);
			$res=$db->query();
			$row=$db->fetchRow($res);
			$name=$row[0];
			$size=filesize("attachments/2/$mailid/$name");
			$size=ceil($size/1024);

			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("memorysize");
			$db->where("id=? and just_insert=?",[$mailid,0]);
			$res2=$db->query();
			$row2=$db->fetchRow($res2);
			$file_size=$row2[0];
			$filesize=$file_size-$size;


			$db->update("nesote_email_draft_$tablenumber");
			$db->set("memorysize=?",[$filesize]);
			$db->where("id=?",$mailid);
			$res=$db->query();

			$db->delete("nesote_email_attachments_$tablenumber");
			$db->where("mailid=? and id=? and userid=?",[$mailid,$id,$userid]);
			$re=$db->query();
					unlink("attachments/2/$tablenumber/$mailid/$name");
			//			echo $mailid;
			exit(0);

		}
	}
	function substringMail($content,$count)
	{
		$count=100;
		$content=str_replace("<br>","&nbsp;",$content);
		$content=trim(strip_tags($content));
		
		$content = preg_replace('/\s\s+/', ' ', $content);
		//$content=str_replace("&nbsp;","",$content);
		return $content=substr((string) $content,0,$count);
	}
	function createfolderlistAction()
	{
		$userid=$this->getId();
		$db=new NesoteDALController();
		$db->select("nesote_email_customfolder");
		$db->fields("id,name");
		$db->where("userid=?",$userid);
		$res=$db->query();
		$string="<select
					id=\"folders\" class=\"searchSelect\">
					<option value=\"0\">".$this->getmessage(164)."</option>
					<option value=\"1\">".$this->getmessage(19)."</option>
					<option value=\"6\">".$this->getmessage(205)."</option>
					<option value=\"4\">".$this->getmessage(12)."</option>
					<option value=\"3\">".$this->getmessage(21)."</option>
					<option value=\"2\">".$this->getmessage(20)."</option>";
		while($row=$db->fetchRow($res))
		{

			$string.="<option value=".$row[0].">".$row[1]."</option>";
		}
		$string.="</select>";
		echo $string;exit;
	}

	function objFolder($fldrName)
	{
		if ($fldrName=='inbox') {
            return 'inbox';
        } elseif ($fldrName=='draft') {
            return 'draft';
        } elseif ($fldrName=='sent') {
            return 'sent';
        } elseif ($fldrName=='spam') {
            return 'spam';
        } elseif ($fldrName=='starred') {
            return 'starred';
        } elseif ($fldrName=='trash') {
            return 'trash';
        } elseif (str_starts_with((string) $fldrName, "custom")) {
            return $fldrName;
        } else
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_customfolder");
			$db->fields("id");
			$db->where("name=?",$fldrName);
			$res=$db->query();
			$row=$db->fetchRow($res);
			return "custom".$row[0];
		}
	}

	function getfoldernameAction(): never
	{
		$id=$this->getParam(1);
		$db=new NesoteDALController();
		$db->select("nesote_email_customfolder");
		$db->fields("name");
		$db->where("id=?",$id);
		$res=$db->query();
		$row=$db->fetchRow($res);
		echo $row[0];exit;
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



	function downloadattachmentAction(): never
	{
				$username=$_COOKIE['e_username'];
				$tablenumber=$this->tableid($username);
				$folderid=$this->getParam(1);
				$mailid=$this->getParam(2);
				$filename=$this->getParam(3);

				//$filename=str_replace(" ","+_+",$filename);
				$flnam=explode(".",(string) $filename);
				$extn=$flnam[1];
				$path="attachments/$folderid/$tablenumber/$mailid/$filename";
		
		$filenam=$filename;
		$filenam=str_replace("qqq ","exe",$filenam);
		$var=strpos($filenam,"-");
        $namez = $var != "FALSE" ? substr($filenam,($var+1)) : $filenam;
		$pathToServerFile=$path;
		header('Content-type: application/'.$extn);
		header('Content-disposition: attachment; filename='.$namez);
		readfile($pathToServerFile);
		exit;
	}
	function showimageAction(): never
	{

		$folderid=$this->getParam(1);
		$mailid=$this->getParam(2);
		$filename=$this->getParam(3);
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$userid=$this->getId();

				$path="attachments/$folderid/$tablenumber/$mailid/$filename";
		$pathToServerFile=$path;
		$db= new NesoteDALController();
		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("type");
		$db->where("folderid=? and mailid=? and name=? and userid=?",[$folderid,$mailid,$filename,$userid]);
		$result=$db->query();
		$row=$db->fetchRow($result);

		$var=strpos((string) $filename,"-");
        $namez = $var != "FALSE" ? substr((string) $filename,($var+1)) : $filename;

		header('Content-Type:'. $row[0].'; filename='.$namez);
		readfile($pathToServerFile);

		exit(0);

	}
	function getExternalcontentFlag($body,$mailId1)
	{

		$id=$this->getId();

		if (strpos((string) $mailId1,">")!="") {
            preg_match('/<(.+?)>/i',(string) $mailId1,$new_mailid);
            $mailId=$new_mailid[1];
        } elseif (strpos((string) $mailId1,"&lt;")!="") {
            preg_match('/&lt;(.+?)&gt;/i',(string) $mailId1,$new_mailid);
            $mailId=$new_mailid[1];
        } elseif (strpos((string) $mailId1,"&amp;lt;")!="") {
            preg_match('/&amp;lt;(.+?)&amp;gt;/i',(string) $mailId1,$new_mailid);
            $mailId=$new_mailid[1];
        } else {
            $mailId=$mailId1;
        }
		$select=new NesoteDALController();
		$select->select("nesote_email_usersettings");
		$select->fields("external_content");
		$select->where("userid=?",[$id]);
		$result=$select->query();//echo $select->getQuery();
		$row2=$select->fetchRow($result);//echo $external_content."--------------";
		$external_content=$row2[0];//echo $external_content."--------------";
		if($external_content!=0)
		{
			$external_content_flag=0;
			$external_content_display=0;
		}
		else
		{

			$external_content_flag=1;
			$external_content_display=0;
			$select->select("nesote_image_display");
			$select->fields("*");
			$select->where("userid=? and mailid=?",[$id,$mailId]);
			$res3=$select->query();
			$rw3=$select->fetchRow($res3);
			$no2=$select->numRows($res3);//echo $no2."@@@@@@@@@@@";
			if($no2!=0)
			{
				$external_content_display=1;
			}
			else
			{
				$cooky=$_COOKIE["image_display"];
				$new=0;
				if($cooky!="")
				{
					$cookys=explode(",",(string) $cooky);
					$nos=count($cookys);

					for($r=0;$r<$nos;$r++)
					{
						$combo[$r]=explode(":",$cookys[$r]);
						if(($combo[$r][0]==$folderid)&&($combo[$r][1]==$mailId))
						{
							$new=1;
						}
					}//print_r($combo);exit;
					//echo $new."&&&&&&&&&&&&&&&";

				}
				if ($new==1) {
                    $external_content_display=1;
                }
			}
		}


		//echo $external_content_display;

		if($external_content_display==1)
		{
			return $body."{nesote_comma}0";
		}
		else
		{
			//echo "here";
			$url=$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
			if(strpos($url,"/index.php")!="")
			{
				$url=str_replace("/index.php","",$url);

			}
			preg_match_all('/img(.+?)src=(.+?)alt=(.+?)>/i',(string) $body,$imagename);
			preg_match_all('/img(.+?)alt=(.+?)src=(.+?)>/i',(string) $body,$imagename1);
			preg_match_all('/img(.+?)src=(.+?)>/i',(string) $body,$imagenames);

			$noz=count($imagename[2]);
			$numz=count($imagename1[2]);
			$numberz=count($imagenames[2]);

			for($z=0;$z<$noz;$z++)
			{

				if((strpos($imagename[2][$z],$url)=="")||(str_starts_with($imagename[2][$z], $url)))
				{
					$mail[$i][19]=1;
					$body=str_replace($imagename[2][$z],$imagename[3][$z],$body);

				}
			}

			for($z=0;$z<$numz;$z++)
			{

				if((strpos($imagename1[3][$z],$url)=="FALSE")||(strpos($imagename1[3][$z],$url)==""))
				{
					$mail[$i][19]=1;
					$body=str_replace($imagename1[3][$z],$imagename1[2][$z],$body);
				}
			}

			for($z=0;$z<$numberz;$z++)
			{

				if((strpos($imagenames[2][$z],$url)=="")||(str_starts_with($imagenames[2][$z], $url)))
				{
					$mail[$i][19]=1;
					$body=str_replace($imagenames[2][$z],"",$body);

				}
			}
			$numbr=$noz+$numz+$numberz+$noz11;
			return $body."{nesote_comma}".$numbr;
		}
	}
	function getattachlistAction()
	{
				$username=$_COOKIE['e_username'];
				$tablenumber=$this->tableid($username);
		$folderName=$this->getParam(1);
		$mailId=$this->getParam(2);
		$draftId=$this->getParam(3);
		$folderId=$this->getfolderid($folderName);$userid=$this->getId();
		$string="";
		$db= new NesoteDALController();
		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("*");
		$db->where("folderid=? and mailid=? and attachment=? and userid=?",[$folderId,$mailId,1,$userid]);
		$result=$db->query();
		$no=$db->numRows($result);
		while($row=$db->fetchRow($result))
		{
					//$last_id=$db1->lastInsert();


					if((is_dir("attachments/2/"))!=TRUE)
					{

						mkdir("attachments/2/",0777);
					}
					if((is_dir("attachments/2/".$tablenumber))!=TRUE)
					{
						mkdir("attachments/2/".$tablenumber,0777);
					}
					if((is_dir("attachments/2/".$tablenumber."/".$draftId))!=TRUE)
					{
						mkdir("attachments/2/".$tablenumber."/".$draftId,0777);
					}
					$string.=$row[2]."{nesote_,}";
					copy("attachments/".$folderId."/".$tablenumber."/".$mailId."/".$row[2],"attachments/2/".$tablenumber."/".$draftId."/".$row[2]);
			$db2=new NesoteDALController();
					$db2->insert("nesote_email_attachments_$tablenumber");
			$db2->fields("mailid,folderid,name,attachment,userid");
			$db2->values([$draftId,2,$row[2],$row[5],$userid]);
			$res2=$db2->query();
		}
		$string=substr($string,0,-10);
		echo $no."{nesote_:}".$string;exit;


	}
	function adddisplayAction()
	{
		$x=$this->validateUser();
		if($x!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$maild=$this->getParam(2);
			$folder1=$this->getParam(1);
			$from=trim((string) $this->getParam(3));
			$folder=$this->getfolderid($folder1);
			if($from !== "")
			{
				//echo $maild."+++".$folder;exit;
				//	$db=new NesoteDALController();
				//if($folder==1)
				///$db->select("nesote_email_inbox");
				//else if($folder==4)
				//$db->select("nesote_email_spam");
				//else if($folder==5)
				//$db->select("nesote_email_trash");
				//else if($folder>=10)
				//$db->select("nesote_email_customfolder_mapping");
				//$db->fields("from_list");
				//$db->where("id=?",$maild);
				//$res=$db->query();
				//$row=$db->fetchRow($res);
				//$mailid=$row[0];//echo $mailid."DDD";
				$mailid=$from;
				if (strpos($mailid,">")!="") {
                    preg_match('/<(.+?)>/i',$mailid,$new_mailid);
                    $id=$new_mailid[1];
                } elseif (strpos($mailid,"&lt;")!="") {
                    preg_match('/&lt;(.+?)&gt;/i',$mailid,$new_mailid);
                    $id=$new_mailid[1];
                } elseif (strpos($mailid,"&amp;lt;")!="") {
                    preg_match('/&amp;lt;(.+?)&amp;gt;/i',$mailid,$new_mailid);
                    $id=$new_mailid[1];
                } else {
                    $id=$mailid;
                }

				$userid=$this->getId();$id=trim($id);
				$db=new NesoteDALController();
				$TOT=$db->total("nesote_image_display","userid=? and mailid=?",[$userid,$id]);
				if($TOT==0)
				{
					$db->insert("nesote_image_display");
					$db->fields("userid,mailid");
					$db->values([$userid,$id]);
					$res=$db->query();
				}
				echo "";
				exit;
			}
		}

	}
	function adddisplaynowAction()
	{
		$x=$this->validateUser();
		if($x!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$mailid=$this->getParam(2);
			$folder1=$this->getParam(1);
			$folder=$this->getfolderid($folder1);
			$var=$_COOKIE["image_display"];
			$vars=explode(",",(string) $var);
			$no=count($vars);
			$z=0;
			for($i=0;$i<$no;$i++)
			{
				$combo[$i]=explode(":",$vars[$i]);
				if(($combo[$i][0]==$folder)&&($combo[$i][1]==$mailid))
				{
					$z=1;
				}
			}
			if ($var=="") {
                $new=$folder.":".$mailid;
            } elseif ($z!=1) {
                $new=$var.",".$folder.":".$mailid;
            } else {
                $new=$var;
            }
			setcookie("image_display",(string) $new, ['expires' => "0", 'path' => "/"]);

			echo "";
			exit;
		}
	}
	function getconvrstnsAction()
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);

		$mailid=$this->getParam(2);
		$folder1=$this->getParam(1);
		$folder=$this->getfolderid($folder1);
		$userid=$this->getId();
		$db=new NesoteDALController();
		if($folder!=6)
		{
			if ($folder==1) {
                $db->select("nesote_email_inbox_$tablenumber");
            } elseif ($folder==2) {
                $db->select("nesote_email_draft_$tablenumber");
            } elseif ($folder==3) {
                $db->select("nesote_email_sent_$tablenumber");
            } elseif ($folder==4) {
                $db->select("nesote_email_spam_$tablenumber");
            } elseif ($folder==5) {
                $db->select("nesote_email_trash_$tablenumber");
            } elseif ($folder>=10) {
                $db->select("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db->fields("mail_references");
			$db->where("id=?",$mailid);
			$res=$db->query();
			$row=$db->fetchRow($res);
			preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
			$no=count($folderArray[1]);
			echo $no;exit;
		}
		else
		{
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("mail_references");
			$db->where("id=? and starflag=? and userid=?",[$mailid,1,$userid]);
			$res=$db->query();
			$row=$db->fetchRow($res);
			preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $row[0],$folderArray);
			$no=count($folderArray[1]);
			echo $no;exit;
		}
	}
	/////Chat STart//////////////////


	function lookupnameAction()
	{
		$chat_id=$this->getParam(1);
		$userid=$this->getParam(2);
		$flag=$this->getParam(3);
		$chatcnt=$this->getParam(5);

		$sender=$this->getId();

		if($flag==0)
		{

			$db1=new NesoteDALController();
			$db1->select("nesote_chat_session");
			$db1->fields("group_status");
			$db1->where("id=?", $chat_id);
			$result1=$db1->query();
			$row1=$db1->fetchRow($result1);

			if($row1[0]==1)//group chat
			{
				$fullname=$this->gettitlename($sender);
				$message="\n $fullname has left";

				$db1=new NesoteDALController();
				$db1->select("nesote_chat_session_users");
				$db1->fields("user_id");
				$db1->where("chat_id=? and active_status=? and user_id!=?",[$chat_id,1,$sender]);
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



			$db=new NesoteDALController();
			$db->update("nesote_chat_session_users");
			$db->set("active_status=? and typing_status=?",[0,0]);
			$db->where("chat_id=? and user_id=?",[$chat_id,$sender]);
			$result=$db->query();//echo $db->getQuery();exit;


		}

		$db11=new NesoteDALController();
		$db11->select("nesote_chat_session_users");
		$db11->fields("chat_id");
		$db11->where("active_status=? and user_id=?",[1,$sender]);
		$db11->order("present_identified_time desc");
		$db11->limit(0,$chatcnt);
		$rs11=$db11->query();
		$nn=$db11->numRows($rs11);
		while($row11=$db11->fetchRow($rs11))
		{
			$ids.=$row11[0].",";
		}
		$ids=substr($ids,0,-1);

		if($nn>0 && $ids !== "")
		{
			$db=new NesoteDALController();
			$db->update("nesote_chat_session_users");
			$db->set("active_status=? and typing_status=?",[0,0]);
			$db->where("chat_id NOT IN(".$ids.") and user_id=?",[$sender]);
			$result=$db->query();
		}

		$db=new NesoteDALController();
//		$db->select(array("u"=>"nesote_users","c"=>"nesote_chat_users"));
//		$db->fields("c.id,c.userid,c.image,c.custom_message,c.logout_status,c.chat_status,c.login_time,c.chatframesize,c.idle,u.id,u.username,u.password,u.firstname,u.lastname,u.sex,u.dateofbirth,u.country,u.remember_question,u.remember_answer,u.createdtime,u.lastlogin,u.status,u.memorysize,u.server_password,u.time_zone,u.alternate_email,u.smtp_username,c.signout");
//		$db->where("u.id=? and u.id=c.userid",$userid);
		
		$db->select(["u"=>"nesote_liberyus_users","c"=>"nesote_chat_users","s"=>"nesote_email_usersettings"]);
		$db->fields("c.id,c.userid,c.image,c.custom_message,c.logout_status,c.chat_status,c.login_time,c.chatframesize,c.idle,u.id,u.username,u.password,u.name,s.sex,s.dateofbirth,s.country,s.remember_question,s.remember_answer,u.joindate,s.lastlogin,u.status,s.memorysize,s.server_password,s.time_zone,s.alternate_email,s.smtp_username,c.signout");
		$db->where("u.id=? and u.id=c.userid and u.id=s.userid",$userid);
		$result=$db->query();
		$result1=$db->fetchRow($result);

		//$name=$result1[12]." ".$result1[13];
        $name=$result1[12];
		
		$img="";
		if ($result1[26]==1) {
            $img="images/status-offline.png";
        } elseif ($result1[4]==1) {
            $img="images/status-offline.png";
        } elseif ($result1[8]==1) {
            $img="images/status-away.png";
        } elseif ($result1[5]==1) {
            $img="images/status_available.png";
        } elseif ($result1[5]==2) {
            $img="images/status-busy.png";
        } elseif ($result1[5]==3) {
            $img="images/status-away.png";
        } elseif ($result1[5]==4) {
            $img="images/status-offline.png";
        } elseif ($result1[5]==5) {
            $img="images/status-offline.png";
        }

		$db=new NesoteDALController();
		$db->select("nesote_chat_session");
		$db->fields("group_status");
		$db->where("id=?",[$chat_id]);
		$result=$db->query();
		$row10=$db->fetchRow($result);

		if($row10[0]==1)
		{


			$db=new NesoteDALController();
			$db->select("nesote_chat_session_users");
			$db->fields("user_id");
			$db->where("chat_id=? and active_status=? and user_id!=?", [$chat_id,1,$sender]);
			$result=$db->query();$title1=$this->firstname($sender).",";$i=1;
			$num=$db->numRows($result);
			if($num>1)
			{
				$img="images/groupchat.png";

				while($row=$db->fetchRow($result))
				{

					$title1.=$this->firstname($row[0]).",";$i++;
				}

				$title=substr($title1,0,-1);
				$title="(".$i.") ".$title;

				$length=strlen($title);
				if ($length>12) {
                    $title=substr($title,0,12)."...";
                }
				$img="images/groupchat.png";
				$title="<img src=\"$img\" border=\"0\">$title";
				$st=$title;
				echo $st."+*+".$chat_id;exit(0);
			}


		}

		$length=strlen((string) $name);
		if ($length>12) {
            $name=substr((string) $name,0,12)."...";
        }


		$st="<img src=\"$img\" border=\"0\">$name";


		echo $st."+*+".$chat_id."+*+".$ids;exit(0);
	}


	function gettitlename($id)
	{
		if ($id==0) {
            return "";
        }
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("name");
		$db->where("id=?",[$id]);
		$rs1=$db->query();
		$row=$db->fetchRow($rs1);
		return $row[0];
	}

	function getfullname($username)
	{
		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("name");
		$db->where("username=?",[$username]);
		$rs1=$db->query();
		$row=$db->fetchRow($rs1);
		return $row[0];
	}

	function firstname($id)
	{

		$db=new NesoteDALController();
		$db->select("nesote_liberyus_users");
		$db->fields("name");
		$db->where("id=?", [$id]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		return $rs[0];

	}

	function getuserid($chat_id)
	{
		$sender=$this->getId();
		$db=new NesoteDALController();
		$db->select("nesote_chat_session_users");
		$db->fields("user_id");
		$db->where("chat_id=? and user_id!=?", [$chat_id,$sender]);
		$result=$db->query();
		$rs=$db->fetchRow($result);

		return $rs[0];

	}

	function setactivestatus($chatid,$userid)
	{
		$db1=new NesoteDALController();
		$db1->update("nesote_chat_session_users");
		$db1->set("active_status=?",[1]);
		$db1->where("chat_id=? and active_status=?",[$chatid,0]);
		//echo $db1->getQuery();
		$db1->query();

		return 1;
	}


	function readChatAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$foldername=$_POST['foldernane'];
			$mailid=$_POST['mailid'];

			$mailid=substr((string) $mailid,0,-1);
			$type=$_POST['type'];


			$username=$_COOKIE['e_username'];
			$modlusnumber=$this->tableid($username);

			$mailids=explode(",",$mailid);
            //print_r($mailids);
            $counter = count($mailids);//print_r($mailids);
			for($i=0;$i<$counter;$i++)
			{
				if($type==1)
				{
					$db1=new NesoteDALController();
					$db1->update("nesote_chat_message_$modlusnumber");
					$db1->set("read_flag=?",1);
					$db1->where("id=?",$mailids[$i]);
					$rs1=$db1->query();
					$row1=$db1->fetchRow($rs1);//echo $db1->getQuery();
				}
				else
				{
					$db1=new NesoteDALController();
					$db1->update("nesote_chat_message_$modlusnumber");
					$db1->set("read_flag=?",0);
					$db1->where("id=?",$mailids[$i]);
					$rs1=$db1->query();
					$row1=$db1->fetchRow($rs1);
				}
			}
			echo "";exit;
		}
	}

	function getallusers($matches)
	{
		return $matches;
	}

	function getchatAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$name=$this->getParam(1);
			$page=$this->getParam(2);
			$size=$this->getParam(3);
			$fieldArray=$this->getParam(4);
			$total=$this->getParam(5);
			$id=$this->getId();
			$fieldz="";
			$fieldsArry=explode(",",(string) $fieldArray);
			$strt=($page-1)*$size;
            if (($strt+$size)>$total) {
                $size=$total-$strt+1;
            }
            $counter = count($fieldsArry);
			for($i=0;$i<$counter;$i++)
			{
				$fieldz.=$fieldsArry[$i].",";
			}
			//print_r($fieldsArry);
			$no_pages=$total/$size;
			$flds=substr($fieldz,0,-1);


			$user_name=$_COOKIE['e_username'];


			$user_name=trim((string) $user_name);
			$modlusnumber=$this->tableid($user_name);


			$userid=$this->getId();

			$db=new NesoteDALController();
			$db->select("nesote_chat_message_$modlusnumber");
			$db->fields("*");
			$db->where("userid=?",$userid);
			$db->order("time desc");
			$db->limit($strt,$size);//echo $db->getQuery();
			$gethistory=$db->query();
			$tot=$db->numRows($gethistory);$i=0;$j=0;$me=$this->getmessage(284);
			while($gethistory1=$db->fetchRow($gethistory))

			{

				$receivers=$gethistory1[3];

				$receivers=explode(",",(string) $receivers);

				$numberingthereciver=count($receivers);$name="";$name1="";$name2="";$name3="";

				for($nn=1;$nn<$numberingthereciver;$nn++)
				{
					$rece[$nn]=$receivers[$nn];
					$db->select("nesote_liberyus_users");
					$db->fields("username,name");
					$db->where("id=? and username!=?",[$rece[$nn],$user_name]);
					$temp=$db->query();
					$temp1=$db->fetchRow($temp);

					$rev[$nn]=$temp1[0];

					$extn=$this->getextension();
					if($rev[$nn]!="")
					{
						$p1=$rev[$nn].",";
						$p2=$temp1[1].",";
						$p=$rev[$nn].$extn.",";
					}
					else
					{
						$p="";$p1="";$p2="";
					}
					$name.=$p2;
					$name1.=$p1;
					$name2.=$p2;
					$name3.=$p;



				}




				$xml=$gethistory1[4];

				$str = $xml;
				$chars = preg_split('/<item>/', (string) $str,-1, PREG_SPLIT_OFFSET_CAPTURE);
				$count=count($chars);
				$lines=$count-1;
				$chars[$i][0]=str_replace("\n","<br>",$chars[$i][0]);
				$subject=$chars[1][0];


				$pattern = '/<id>(.+?)<\/id><time>(.+?)<\/time><sender>(.+?)<\/sender><message>(.+?)<\/message>/i';
				preg_match($pattern,$subject,$matches);

				//$chattime=$matches[2];

				$chattime=$gethistory1[5];

				if ($matches[3]==0) {
                    $matches[3]=$userid;
                }
				$db->select("nesote_liberyus_users");
				$db->fields("username");
				$db->where("id=?",$matches[3]);
				$jet=$db->query();
				$jet1=$db->fetchRow($jet);

				$sendername=$jet1[0];


				if($sendername==$user_name)
				{
					$firstsender=$this->getmessage(284);

				}
				else
				{


					$firstsender=$sendername;

					if ($name === "") {
                        $name.=$this->getfullname($sendername);
                    }

					if ($name1 === "") {
                        $name1.=$this->getfullname($sendername);
                    }
					if ($name2 === "") {
                        $name2.=$this->getfullname($sendername);
                    }
					if ($name3 === "") {
                        $name3.=$sendername.$this->getextension();
                    }


				}



				$reverse = strrev($name);
				if ($reverse[0]==",") {
                    $name=substr($name,0,-1);
                }

				$chat_messages[$j][0]=$matches[1];
				$chat_messages[$j][1]=$chattime;
				$chat_messages[$j][2]=$sendername;
				$chat_messages[$j][3]=$matches[4];
				$chat_messages[$j][4]=$firstsender;
				$chat_messages[$j][5]=$name;
				$chat_messages[$j][6]=$gethistory1[0];
				$chat_messages[$j][7]=$lines;
				$chat_messages[$j][8]=$gethistory1[6];

				$reverse1 = strrev($name1);
				if ($reverse1[0]==",") {
                    $name1=substr($name1,0,-1);
                }

				$reverse2 = strrev($name2);
				if ($reverse2[0]==",") {
                    $name2=substr($name2,0,-1);
                }

				$reverse3 = strrev($name3);
				if ($reverse3[0]==",") {
                    $name3=substr($name3,0,-1);
                }



				$tableid=$gethistory1[0];
				$chatid=$matches[1];
				$from=$name;$cnt=0;

				if (strpos($from,",")!="") {
                    $cnt1=explode(",",$from);
                    $cnt=count($cnt1);
                } elseif ($from !== "") {
                    $cnt=1;
                }
				if ($cnt>1) {
                    $from=$this->getmessage(284);
                    $fromopen=$this->getname($userid);
                    $to=$name1;
                    $todtls=$name3;
                    $fromopendtls=$this->getusername($userid).$this->getextension();
                    $toreply=$todtls;
                } elseif ($cnt==1) {
                    if($firstsender==$me)
					{
						$fromopen=$name2;
						$fromopendtls=$name3;
					}
					else
					{
						$fromopen=$this->getfullname($firstsender);
						$fromopendtls=$firstsender.$this->getextension();
					}
                    $to=$me;
                    $todtls=$_COOKIE['e_username'].$this->getextension();
                    $toreply=$fromopendtls;
                } elseif ($cnt === 0) {
                    $from=$this->getmessage(284);
                    $fromopen=$this->getname($userid);
                    $to=$this->getname($userid);
                    $todtls=$this->getusername($userid).$this->getextension();
                    $fromopendtls=$this->getusername($userid).$this->getextension();
                    $toreply=$todtls;
                }


				if($name3!=$me || $name3!=="")
				{
					$fromall=$name3.",".$user_name.$this->getextension();

				}
				else
				{
					$fromall=$user_name.$this->getextension();

				}

				$subj=$this->getmessage(384)." ".$name2."  (".$lines." ".$this->getmessage(430).")";
				$subj1=$this->getmessage(382)." ".$name2;
				$subj2=$this->getmessage(382)." ".$name2.",".$this->gettitlename($userid);

				$time=$this->gettime($chattime);
				//$time=$chattime;
				$readflag=$gethistory1[6];
				$responders=$firstsender." - ".$chat_messages[$j][3];

				//$msg=$chat_messages[$j][3];

				$msg=$this->getchatmsg($tableid,$modlusnumber);
				$msg1=strip_tags((string) $msg);




				$contacts.=trim((string) $tableid)."{nesote_t}";//0
				$contacts.=trim($chatid)."{nesote_t}";//1

				$contacts.=trim((string) $from)."{nesote_t}";//2
				$contacts.=trim($subj)."{nesote_t}";//3
				$contacts.=trim((string) $time)."{nesote_t}";//4
				$contacts.=trim((string) $readflag)."{nesote_t}";//5
				$contacts.=trim($responders)."{nesote_t}";//6
				$contacts.=trim((string) $to)."{nesote_t}";//7
				$contacts.=trim((string) $msg)."{nesote_t}";//8
				$contacts.=trim((string) $fromopen)."{nesote_t}";//9
				$contacts.=trim($subj1)."{nesote_t}";//10
				$contacts.=trim($fromall)."{nesote_t}";//11
				$contacts.=trim($subj2)."{nesote_t}";//12
				$contacts.=trim($fromopendtls)."{nesote_t}";//13
				$contacts.=trim($todtls)."{nesote_t}";//14
				$contacts.=trim($toreply)."{nesote_t}";//15
				$contacts.=trim($msg1)."{nesote_t}";//16
				$i++;$j++;

			}



			$contacts=substr($contacts,0,-10);
			print_r($contacts);
			exit;
		}
	}

	function getchatmsg($tableid,$modlusnumber)
	{
		$username=$_COOKIE['e_username'];




		$indexid=$tableid;
		if($indexid=="")
		{

			return null;
		}$str1="";



		$db=new NesoteDALController();
		$db->select("nesote_chat_message_$modlusnumber");
		$db->fields("*");
		$db->where("id=?",$indexid);
		$db->order("time desc");
		$gethistory=$db->query();
		$gethistory1=$db->fetchRow($gethistory);



		$receivers=$gethistory1[3];$me=$this->getmessage(284);

		$receivers=explode(",",(string) $receivers);


		$numberingthereciver=count($receivers);


		for($nn=1;$nn<$numberingthereciver;$nn++)
		{
			$rece[$nn]=$receivers[$nn];

			$db=new NesoteDALController();
			$db->select("nesote_liberyus_users");
			$db->fields("username");
			$db->where("id=?",$rece[$nn]);
			$temp=$db->query();
			$temp1=$db->fetchRow($temp);

			$rev[$nn]=$temp1[0];

			$name[$i].=$rev[$nn]." " .","." ";


		}
		$long=strlen($name[$i]);
		$long1=$long-2;

		$name[$i]=substr($name[$i],0,$long1);


		$this->setValue("towhom",$name[$i]);
		$xml=$gethistory1[4];
		$same=0;


		$str = $xml;
		$chars = preg_split('/<item>/', (string) $str,-1, PREG_SPLIT_OFFSET_CAPTURE);
		$count=count($chars);

		$str1="<table border=0 class=\"chatContent\" >";
		$flag=0;
		for($i=0;$i<$count;$i++)
		{



			$chars[$i][0]=str_replace("\n","<br>",$chars[$i][0]);
			$subject=$chars[$i][0];



			$pattern = '/<id>(.+?)<\/id><time>(.+?)<\/time><sender>(.+?)<\/sender><message>(.+?)<\/message>/i';

			preg_match($pattern,$subject,$matches);



			$db=new NesoteDALController();
			$db->select("nesote_liberyus_users");
			$db->fields("username");
			$db->where("id=?",$matches[3]);
			$jet=$db->query();

			$jet1=$db->fetchRow($jet);


			$sendername[$i]=$jet1[0]; $sendername1[$i]=$this->gettitlename($matches[3]);

			if ($matches[3]==0) {
                $sendername[$i]="";
            }

			if($sendername[$i]==$username)
			{
				$sendername[$i]=$me; $sendername1[$i]=$me;
			}
			//1
			$sendersname[$i]=$sendername[$i];$sendersname1[$i]=$sendername1[$i];

			$getsmileyvalue=$this->getsmileyvalue();
			$msg=$matches[4];
$msg=str_replace("e0d71f32e332df0bf09e2f879dd14d77"," ",$msg);
			if($getsmileyvalue==1)
			{
				$msg=str_ireplace(":)","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-1\">",$msg);
				$msg=str_ireplace(":(","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-2\">",$msg);
				$msg=str_ireplace(":d","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-3\">",$msg);
				$msg=str_ireplace(":P","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-4\">",$msg);
				$msg=str_ireplace("(*)","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-5\">",$msg);
				$msg=str_ireplace("(-)","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-6\">",$msg);
				$msg=str_ireplace(":|","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-7\">",$msg);
				$msg=str_ireplace("(;","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-8\">",$msg);
				$msg=str_ireplace(":-*","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-9\">",$msg);
				$msg=str_ireplace(":-v","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-10\">",$msg);
				$msg=str_ireplace(":*)","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-11\">",$msg);
				$msg=str_ireplace("B-)","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-12\">",$msg);
				$msg=str_ireplace("x-(","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-13\">",$msg);
				$msg=str_ireplace(":*B","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-14\">",$msg);
				$msg=str_ireplace("*:A","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-15\">",$msg);
				$msg=str_ireplace(":-$","<img src=\"images/filler.gif\" border=\"0\" align=\"absmiddle\" class=\"smileyCornner smiley-16\">",$msg);
				//$msg=str_ireplace(":-^","<img src=\"smile/17.gif\" border=\"0\">",$msg);
				//$msg=str_ireplace(":-!","<img src=\"smile/18.gif\" border=\"0\">",$msg);
				//$msg=str_ireplace(":-D","<img src=\"smile/19.gif\" border=\"0\">",$msg);
				//$msg=str_ireplace(":X","<img src=\"smile/20.gif\" border=\"0\">",$msg);
				//$msg=str_ireplace(":=)","<img src=\"smile/21.gif\" border=\"0\">",$msg);
				//$msg=str_ireplace("?=)","<img src=\"smile/22.gif\" border=\"0\">",$msg);
				//$msg=str_ireplace(":-o","<img src=\"smile/23.gif\" border=\"0\">",$msg);
				//$msg=str_ireplace(":-Z","<img src=\"smile/24.gif\" border=\"0\">",$msg);
			}

			$message[$i]=$msg;
			if($flag==1)
			{

				if($sendername[$i]==$username)
				{
					$firstsender=$me;
					$this->setValue("firstsender",$firstsender);


				}
				else
				{


					$firstsender=$sendername[$i];
					$this->setValue("firstsender",$firstsender);

				}

			}

			if($flag>0)
			{

				if($matches[2]!="" || $matches[2]!=0)
				{
					//$time[$i]=date("h:i A",$matches[2]);
					$time[$i]=$this->gettimetype1($matches[2]);
				}
				else {
                    $time[$i]="";
                }


				if($flag==1)
				{

					//$hour=date("F Y h:i:s A",$matches[2]);
					$hour=$this->gettimetype2($matches[2]);
					$this->setValue("hour",$hour);
					$chattime=$matches[2];

					$this->setValue("chattime",$chattime);

				}



				if($time[$i-1]==$time[$i])
				{
					$messagetime[$i]="";


					if($sendername[$i-1]==$sendername[$i])
					{
						$same=1;
						$sendersname[$i]=""; $sendersname1[$i]="";

						$message[$i-1].=$message[$i];

					}
					else
					{
						//2
						$sendersname[$i]=$sendername[$i];$sendersname1[$i]=$sendername1[$i];

					}

				}
				else
				{
					$messagetime[$i]=$time[$i];

				} $sender=$sendersname1[$i];
				if ($sender!="") {
                    $sender .= "<b>: </b>";
                }

				$str1.="<tr>";
				if ($messagetime[$i]!="") {
                    $str1.="<td style=\"float:left;text-align: left;padding:5px 0px 0px 10px;\"><span class=\"chattitleTime\">$messagetime[$i]</span></td>";
                } else {
                    $str1.="<td style=\"float:left;text-align: left;padding:5px 0px 0px 61px;\"> </td>";
                }
				$str1.="<td style=\"float:left;text-align: left;padding:5px 0px 0px 10px;\"><strong>$sender</strong>";
				if ($same==0) {
                    $str1.="<b> </b>";
                }
				$str1.="$message[$i] </td></tr>";



			}

			$flag++;

		}
		$str1.="</table>";
		//$chat_messages=html_entity_decode($chat_messages);
		$str1=html_entity_decode($str1);
		return $str1;




	}
	function getsmileyvalue()
	{
		$userid=$this->getId();
		$select=new NesoteDALController();
		$select->select("nesote_chat_users");
		$select->fields("smileys");
		$select->where("userid=?",[$userid]);
		$result=$select->query();
		$rs=$select->fetchRow($result);
		return $rs[0];
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
	function gettimeinside($date)
	{
		$this->loadLibrary('Settings');
		$settings=new Settings('nesote_email_settings');
		$settings->loadValues();
		//return date("h:i A ",$date);

		$position=$settings->getValue("time_zone_postion");


		$hour=$settings->getValue("time_zone_hour");



		$min=$settings->getValue("time_zone_mint");


		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=$date;

	
			
			$userid=$this->getId();
		$db3= new NesoteDALController();
		$db3->select("nesote_email_usersettings");
		$db3->fields("time_zone");
		$db3->where("userid=?",[$userid]);
		$res3=$db3->query();
		$row3=$db3->fetchRow($res3);

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
		$ts=$date+$newtimezone;
		//$tsa=$tsa+$newtimezone;

		$date=$ts;

		$month_id = date("n",$date);
		if(isset ($_COOKIE['lang_mail']))
		{
			$lang_code=$_COOKIE['lang_mail'];
		}
		else
		{
			$select=new NesoteDALController();
			$select->select("nesote_email_settings");
			$select->fields("value");
			$select->where("name=?",'default_language');
			$result=$select->query();
			$data4=$select->fetchRow($result);
			$lang_code=$data4[0];
			//$defaultlang_id=$data4[0];
		}
          $lang_id=$this->getlang_id($lang_code);
		date(" j ",$date);

		$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$lang_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);
		if($data[0]=="")
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_months_messages");
			$db->fields("message");
			$db->where("month_id=? and lang_id=?",[$month_id,1]);
			$result=$db->query();
			$data=$db->fetchRow($result);
		}
		$v1=time()-$diff+$newtimezone;
        mktime(0, 0, 0, date("m",$v1), date("d",$v1), date("Y",$v1));
		return date("D M d Y h:i:s A",$date);
		//$timeformat=date("D, M d, Y ",$time)."at".date(" h:i:s a",$time);
		//return $timeformat;
	}

	//////////////caht End//////////
	function getattachcountforPrint($references)
	{

		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		preg_match_all('/<folderid>(.+?)<\/folderid>/i',(string) $references,$folderArray);
		preg_match_all('/<mailid>(.+?)<\/mailid>/i',(string) $references,$mailidArray);
		$no=count($folderArray[1]);
		$w=0;
		for($i=0;$i<$no;$i++)
		{
			$db= new NesoteDALController();
			$db->select("nesote_email_attachments_$tablenumber");
			$db->fields("id");
			$db->where("mailid=? and folderid=? and attachment=?",[$mailidArray[1][$i],$folderArray[1][$i],1]);
			$result=$db->query();
			$no=$db->numRows($result);
			$w += $no;
		}

		return $w;
	}


	function getattachmentIconforPrint($mail,$folder)
	{

		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$attach="";
		$db= new NesoteDALController();
		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("name");
		$db->where("mailid=? and folderid=? and attachment=?",[$mail,$folder,1]);
		$result=$db->query();
		while($row=$db->fetchRow($result))
		{
			$pos=(strpos((string) $row[0],"-")+1);
			if($pos<=4)
			{
				$name=substr((string) $row[0],$pos);
				$pos1=(strpos($name,"-")+1);
				$name=substr((string) $row[0],$pos1);
			}
			else {
                $name=substr((string) $row[0],$pos);
            }
			$format=$this->checkImage($row[0]);
			if ($format==1) {
                $attach.="attachments/".$folder."/".$tablenumber."/".$mail."/".$row[0]."::".$name."::1,";
            } elseif ($format==2) {
                $attach.="images/video.png::".$name."::0,";
            } elseif ($format==3) {
                $attach.="images/audio.png::".$name."::0,";
            } else {
                $attach.="images/other.png::".$name."::0,";
            }
		}
		if ($attach !== "") {
            $attach=substr($attach,0,-1);
        }
		return $attach;
	}

	function emptyspamAction()
	{
		$x=$this->validateUser();
		if($x!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$uid=$this->getId();
		$db=new NesoteDALController();
		$db1=new NesoteDALController();
		$db2=new NesoteDALController();
		
		$db->select("nesote_email_spam_$tablenumber");
		$db->fields("id");
		$db->where("userid=?",$uid);
		$rs=$db->query();
		while($row=$db->fetchRow($rs))
		{
			$db1->select("nesote_email_attachments_$tablenumber");
			$db1->fields("name");
			$db1->where("userid=? and mailid=?",[$uid,$row[0]]);
			$rs1=$db1->query();
			$n0=$db1->numRows($rs1);
			if($n0>0)
			{
				while($rw1=$db1->fetchRow($rs1))
				{
					unlink("attachments/4/".$tablenumber."/".$row[0]."/".$rw1[0]);
				}
				rmdir("attachments/4/".$tablenumber."/".$row[0]);
				$db2->delete("nesote_email_attachments_$tablenumber");
		        $db2->where("mailid=?",$row[0]);
		        $db2->query();
			}
			
		}	
				
		$db->delete("nesote_email_spam_$tablenumber");
		$db->where("userid=?",$uid);
		$db->query();
		echo "";exit(0);

	}

	function emptytrashAction()
	{
		$x=$this->validateUser();
		if($x!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$uid=$this->getId();
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$db=new NesoteDALController();
		$db1=new NesoteDALController();
		$db2=new NesoteDALController();
		
		$db->select("nesote_email_trash_$tablenumber");
		$db->fields("id");
		$db->where("userid=?",$uid);
		$rs=$db->query();
		while($row=$db->fetchRow($rs))
		{
			$db1->select("nesote_email_attachments_$tablenumber");
			$db1->fields("name");
			$db1->where("userid=? and mailid=?",[$uid,$row[0]]);
			$rs1=$db1->query();
			$n0=$db1->numRows($rs1);
			if($n0>0)
			{
				while($rw1=$db1->fetchRow($rs1))
				{
					unlink("attachments/5/".$tablenumber."/".$row[0]."/".$rw1[0]);
				}
				rmdir("attachments/5/".$tablenumber."/".$row[0]);
				$db2->delete("nesote_email_attachments_$tablenumber");
		        $db2->where("mailid=?",$row[0]);
		        $db2->query();
			}
			
		}	
		$db->delete("nesote_email_trash_$tablenumber");
		$db->where("userid=?",$uid);
		$db->query();
		echo "";exit(0);

	}
	function setlogindetails()
	{
		        

                $db=new NesoteDALController();
                $uid=$this->getId();
		        $db->select("nesote_email_usersettings");
				$db->fields("lastlogin");
				$db->where("userid=?",$uid);
				$res=$db->query();
				$result=$db->fetchRow($res);
				if($result[0]==0)
				{

				$db->update("nesote_email_usersettings");
				$db->set("lastlogin=?",1);
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
	
	function setThemeAction(): never
	{
		$themeid=$this->getParam(1);
		$userid=$this->getId();
		$db=new NesoteDALController();
		$db->update("nesote_email_usersettings");
		$db->set("theme_id=?",[$themeid]);
		$db->where("userid=?",[$userid]);
		$db->query();
		
		echo "";exit;
		
		
	}
	

};
?>