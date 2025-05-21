<?php
class MailController extends NesoteController
{

	function attachcount($folder,$mail)
	{

		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		$db=new NesoteDALController();
		$db->select("nesote_email_attachments_$tablenumber");
		$db->fields("id");
		$db->where("folderid=? and mailid=? and attachment=?",[$folder,$mail,1]);
		$res=$db->query();
		return $db->numRows($res);

	}

	function short_subject($sub)
	{
		$sub=strip_tags((string) $sub);
		return substr($sub,0,20);
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
	function getstar_shortmail($mailid,$folderid)
	{
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
			
		$db=new NesoteDALController();
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
        } elseif ($folderid>=10) {
            $db->select("nesote_email_customfolder_mapping_$tablenumber");
        }
		$db->fields("mail_references");
		$db->where("id=?",[$mailid]);
		$rs=$db->query();
		$row=$db->fetchRow($rs);
		$references=$row[0];
		preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);

		$no=count($reply[1]);
		$w=0;
		for($i=0;$i<$no;$i++)
		{
			preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mail[$i]);
			preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folder[$i]);

			if ($folder[$i][1]==1) {
                $db->select("nesote_email_inbox_$tablenumber");
            } elseif ($folder[$i][1]==2) {
                $db->select("nesote_email_draft_$tablenumber");
            } elseif ($folder[$i][1]==3) {
                $db->select("nesote_email_sent_$tablenumber");
            } elseif ($folder[$i][1]==4) {
                $db->select("nesote_email_spam_$tablenumber");
            } elseif ($folder[$i][1]>=10) {
                $db->select("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db->fields("starflag");
			$db->where("id=?",[$mail[$i][1]]);
			$rs=$db->query();
			$rows=$db->fetchRow($rs);
			if($rows[0]==1)
			{
				$w=1;
				break;
			}
		}


		if ($w==0) {
            return "<a href=\"javascript:markstar($mailid,$folderid)\"><img src=\"../images/filler.gif\" alt=\" \" border=\"0\" align=\"absmiddle\" class=\"iconsCornner str-g\"/></a>";
        } else {
            return "<a href=\"javascript:unmarkstar($mailid,$folderid)\"><img src=\"../images/filler.gif\" alt=\" \" border=\"0\" align=\"absmiddle\" class=\"iconsCornner str-y\"/></a>";
        }
	}
	function validateUser()
	{
		$username=$_COOKIE['e_username'];
		$password=$_COOKIE['e_password'];
		$db=new NesoteDALController();
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


	function mailboxAction()
	{

		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

			$username=$_COOKIE['e_username'];
			$uname=$username;
			$this->setValue("uname",$uname);
			
		$tablenumber=$this->tableid($username);
		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='public_page_logo'");
		$result1=$db->query();
		$row1=$db->fetchRow($result1);
		$img=$row1[0];
		$imgpath="../admin/logo/".$img;

//$this->setValue("imgpath","images/banner.png");
		$this->setValue("imgpath",$imgpath);
		
		

		$id=$this->getId();
		$this->setValue("uid",$id);

		
			
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name='engine_name'");
		$result=$db->query();
		$row=$db->fetchRow($result);
		$servicename=$row[0];
		$this->setValue("servicename",$servicename);


		$db->select("nesote_email_customfolder");
		$db->fields("id,name");
		$db->where("userid=?",[$id]);
		$res1=$db->query();
		$i=0;
		while($rw=$db->fetchRow($res1))
		{
			$db1=new NesoteDALController();
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
			$customFolder[$i][3]=$count11;
			$countCookie="custom".$rw[0];
			setcookie($countCookie,(string) $count, ['expires' => "0", 'path' => "/"]);
			$i++;
		}
		$this->setValue("mpcount",$i);
		$this->setLoopValue("customfolders",$customFolder);

	
		$perpagesize=20;
			
		$all=0;
		$flag=0;
		$folder=$this->getParam(1);
		if (!isset($folder)) {
            $folder=1;
        }
		setcookie("folderid",$folder, ['expires' => 0, 'path' => "/"]);
		$this->setValue("fid",$folder);
		$this->setValue("perpagesize",$perpagesize);


		$page=$this->getParam(2);
		if(isset($page))
	{$pq=$page;	$this->setValue("page",$page);}
		else
		{$pq=1;$this->setValue("page",1);}
			
			


		$msg=$this->getParam(3);$more=0;$action="";

		if ($msg===0) {
            $msg="";
            $more=0;
        } elseif ($msg==-1) {
            $msg="";
            $more=1;
        } elseif ($msg=="r" || $msg=="d" || $msg=="m") {
            $action=$msg;
            $msg="";
        }
		$this->setValue("action",$action);
		$this->setValue("more",$more);
		if(isset($msg))
		{
			$p=base64_decode((string) $msg);
			$msg1=explode("@@",$p);
			if($msg1[1]!="")
			{
				$msg=$this->getmessage($msg1[0]);
				$msg=str_replace("{number}",$msg1[1],$msg);
			}
			else {
                $msg=$this->getmessage($msg1[0]);
            }

			$this->setValue("msg",$msg);
		}
		else {
            $this->setValue("msg","");
        }
    $submit=$_POST['submit']; $search="";
    if(isset($submit))
	 {
	$search=$_POST['search'];
	 }
		
		if($search!="")
		{
			$flag=1;

			$folder=$_POST['folder'];
			$this->setValue("searchflag",1);
			$this->setValue("allsearch",1);
			$this->setValue("search",$search);
			$this->setValue("searchresult",$folder);
		}
		else
		{
			$this->setValue("searchflag",0);

		}
		$heading=$this->getheading($folder,$search);
		
		$this->setValue("heading",$heading);
		$i=0;
		if($folder==1)
		{
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");
			$db->limit(0,1);
			$res=$db->query();
			$row=$db->fetchRow($res);
			$url=$this->url('mail/detailmail/'.$folder.'/'.$row[0]);
			
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");

			$res=$db->query();
			$max=$db->numRows($res);
			$page=$this->getParam(2);

			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");
			$startpage=($pq-1)*$perpagesize;
			$db->limit($startpage,$perpagesize);

			$res=$db->query();

			while($row=$db->fetchRow($res))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],1,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;

				$mail_1[$i][0]=$row[0];
				$mail_1[$i][1]=$row[1];
				$mail_1[$i][2]=$row[2];
				$mail_1[$i][3]=$row[3];
				$mail_1[$i][4]=$row[4];
				$mail_1[$i][5]=$row[5];
				$mail_1[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
				if ($cset1[2]!="") {
                    $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                }
				$mail_1[$i][7]=$row[7];
				
				$mail_1[$i][8]=$row[8];
				$mail_1[$i][9]=$row[9];
				$mail_1[$i][10]=$row[10];
				$mail_1[$i][11]=$row[11];
				$mail_1[$i][12]=$row[12];
				$mail_1[$i][13]=1;
				$mail_1[$i][14]=$row[13];
				$mail_1[$i][15]=$row[14];
				$mail_1[$i][16]=$flag1;
				$mail_1[$i][17]=$row[15];
				$i++;
			}
			$mail[0]=$mail_1[0];
            $counter = count($mail_1);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail);$a++)
				{
					if($mail_1[$r][15]==$mail[$a][15])
					{
						$flag0=1;
						if ($mail_1[$r][16]==1) {
                            $mail[$a][16]=1;
                        }
						if ($mail[$a][10]==1) {
                            $mail_1[$r][10]=1;
                        }
					}
				}
				if($flag0==0)
				{
					$mail[$w]=$mail_1[$r];
					$w++;
				}
			}

			if(!isset($page))
			{
				$page=1;
			}
			/*$p=$page;
			$index=($p-1)*$perpagesize;
			for($r=0;$r<$perpagesize;$r++,$index++)
			{
				if($index>=$max)
				break;
				$mail_new[$r]=$mail[$index];
			}*/


			$this->setValue("firstid",$mail[0][0]);
			$this->setLoopValue("mail",$mail);



			$nmbr=count($mail);
			if ($mail[0][0]=="") {
                $nmbr=0;
            }


			if($page>$w)
			{
				$page=1;
				$startpage=0;

			}
			$startpage=($page-1)*$perpagesize;

			if($startpage<0)//for firsttime
			{

				$page=1;
				$startpage=0;
			}


			$this->setValue("array_count",$nmbr);
			$this->setValue("number",$nmbr);



			$previouspage=$page-1;$s1=0;$s2=0;
			$pagelink="<div class=\"pagingtbl\">";
			if($page!=1)
			{
				$s1=1;	
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
			}
			$nextpage=$page+1;
			if($page*$perpagesize<($max))//if($page*10<=($total+10))
			{
					$s2=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
			}
			$pagelink.="</div>";$s=$s1+$s2;
			$this->setValue("show",$s);
			$this->setValue("pagelink",$pagelink);



		}
		elseif($folder==2)
		{

			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and just_insert=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$id,0]);
			}
			else
			{
				$db->where("userid=? and just_insert=?",[$id,0]);
			}
			$db->order("time desc");
			$db->limit(0, 1);
			$res=$db->query();
			$row=$db->fetchRow($res);
			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and just_insert=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$id,0]);
			}
			else
			{
				$db->where("userid=? and just_insert=?",[$id,0]);
			}
			$db->order("time desc");
			$res=$db->query();
            $no=$db->numRows($res);$nod=$no;

			

			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and just_insert=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$id,0]);
			}
			else
			{
				$db->where("userid=? and just_insert=?",[$id,0]);
			}
			$db->order("time desc");
			$page=$this->getParam(2);
			$startpage=($pq-1)*$perpagesize;
			$db->limit($startpage,$perpagesize);
			$res=$db->query();

			while($row=$db->fetchRow($res))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],2,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;
				$mail_1[$i][0]=$row[0];
				$mail_1[$i][1]=$row[1];
				$mail_1[$i][2]=$row[2];
				$mail_1[$i][3]=$row[3];
				$mail_1[$i][4]=$row[4];
				$mail_1[$i][5]=$row[5];
				$mail_1[$i][6]=$row[6];
				$mail_1[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail_1[$i][7]=$row[7];
				$mail_1[$i][8]=$row[8];
				$mail_1[$i][9]=$row[9];
				$mail_1[$i][10]=$row[10];
				$mail_1[$i][11]=$row[11];
				$mail_1[$i][12]=$row[12];
				$mail_1[$i][13]=2;
				$mail_1[$i][14]=$row[13];
				$mail_1[$i][15]=$row[14];
				$mail_1[$i][16]=$flag1;
				//$mail_1[$i][17]=$row[15];
				$i++;
			}
			$mail[0]=$mail_1[0];
            $counter = count($mail_1);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail);$a++)
				{
					if($mail_1[$r][15]==$mail[$a][15])
					{
						$flag0=1;
						if ($mail_1[$r][16]==1) {
                            $mail[$a][16]=1;
                        }
						if ($mail[$a][10]==1) {
                            $mail_1[$r][10]=1;
                        }
					}
				}
				if($flag0==0)
				{

					$mail[$w]=$mail_1[$r];
					$w++;
				}
			}

			if(!isset($page))
			{
				$page=1;
			}
			/*$p=$page;
			$index=($p-1)*$perpagesize;
			for($r=0;$r<$perpagesize;$r++,$index++)
			{
				if($index>=$w)
				break;
				$mail_new[$r]=$mail[$index];
			}*/


			if($page>$w)
			{
				$page=1;
				$startpage=0;

			}
			$startpage=($page-1)*$perpagesize;

			if($startpage<0)//for firsttime
			{

				$page=1;
				$startpage=0;
			}


			$this->setValue("firstid",$mail[0][0]);
			$this->setLoopValue("mail",$mail);
			$nmbr=count($mail);
			if ($mail[0][0]=="") {
                $nmbr=0;
            }
			$this->setValue("array_count",$nmbr);
			$this->setValue("number",$nmbr);

			$previouspage=$page-1;$s1=0;$s2=0;
			$pagelink="<div class=\"pagingtbl\">";
			if($page!=1)
			{
				$s1=1;	
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
			}
			

			$nextpage=$page+1;
			if($page*$perpagesize<($nod))
			{
				$s2=1;	
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
			}
			$pagelink.="</div>";
			$s=$s1+$s2;
            $this->setValue("show",$s);
			$this->setValue("pagelink",$pagelink);
			
		}
		elseif($folder==3)
		{

			$db->select("nesote_email_sent_$tablenumber");

			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);

			}
			$db->order("time desc");
			$db->limit(0,1);
			$res=$db->query();
			$row=$db->fetchRow($res);
			$db->select("nesote_email_sent_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");
			$res=$db->query();
		   $no=$db->numRows($res);$nos=$no;
			$db->select("nesote_email_sent_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");
			$page=$this->getParam(2);
			$startpage=($pq-1)*$perpagesize;
			$db->limit($startpage,$perpagesize);
			$res=$db->query();
			while($row=$db->fetchRow($res))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],3,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;
				$mail_1[$i][0]=$row[0];
				$mail_1[$i][1]=$row[1];
				$mail_1[$i][2]=$row[2];
				$mail_1[$i][3]=$row[3];
				$mail_1[$i][4]=$row[4];
				$mail_1[$i][5]=$row[5];
				$mail_1[$i][6]=$row[6];
				$mail_1[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail_1[$i][7]=$row[7];
				$mail_1[$i][8]=$row[8];
				$mail_1[$i][9]=$row[9];
				$mail_1[$i][10]=$row[10];
				$mail_1[$i][11]=$row[11];
				$mail_1[$i][12]=$row[12];
				$mail_1[$i][13]=3;
				$mail_1[$i][14]=$row[13];
				$mail_1[$i][15]=$row[14];
				$mail_1[$i][16]=$flag1;
				$mail_1[$i][17]=$row[15];
				$i++;
			}
			$mail[0]=$mail_1[0];
            $counter = count($mail_1);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail);$a++)
				{
					if($mail_1[$r][15]==$mail[$a][15])
					{
						$flag0=1;
						if ($mail_1[$r][16]==1) {
                            $mail[$a][16]=1;
                        }
						if ($mail[$a][10]==1) {
                            $mail_1[$r][10]=1;
                        }
					}
				}
				if($flag0==0)
				{

					$mail[$w]=$mail_1[$r];
					$w++;
				}
			}

			if(!isset($page))
			{
				$page=1;
			}
			/*$p=$page;
			$index=($p-1)*$perpagesize;
			for($r=0;$r<$perpagesize;$r++,$index++)
			{
				if($index>=$w)
				break;
				$mail_new[$r]=$mail[$index];
			}*/


			if($page>$w)
			{
				$page=1;
				$startpage=0;

			}
			$startpage=($page-1)*$perpagesize;

			if($startpage<0)//for firsttime
			{

				$page=1;
				$startpage=0;
				//$db->limit($startpage,$perpagesize);
			}


			$this->setLoopValue("mail",$mail);
			$this->setValue("firstid",$mail[0][0]);
			$nmbr=count($mail);
			if ($mail[0][0]=="") {
                $nmbr=0;
            }
			$this->setValue("array_count",$nmbr);
			$this->setValue("number",$nmbr);

			$previouspage=$page-1;$s1=0;$s2=0;
			$pagelink="<div class=\"pagingtbl\">";
			if($page!=1)
			{
					$s1=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
			}
			//else
			//$pagelink.="<td align=\"left\">&nbsp;</td>";

			$nextpage=$page+1;
			if($page*$perpagesize<($nos))//if($page*10<=($total+10))
			{
				$s2=1;	
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
			}
			//else
			//$pagelink.="<td align=\"right\" style=\"padding-right: 2px;\">&nbsp;</td>";
			$pagelink.="</div>";$s=$s1+$s2;
            $this->setValue("show",$s);
			$this->setValue("pagelink",$pagelink);
		}
		elseif($folder==4)
		{

			$db->select("nesote_email_spam_$tablenumber");

			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");
			$db->limit(0,1);
			$res=$db->query();
			$row=$db->fetchRow($res);
			$db->select("nesote_email_spam_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");
			$res=$db->query();
			$no=$db->numRows($res);$nosp=$no;
			$db->select("nesote_email_spam_$tablenumber");
			$db->fields("*");
			if($flag==1)
			{
				$db->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",$id);
			}
			else
			{
				$db->where("userid=?",$id);
			}
			$db->order("time desc");
			$page=$this->getParam(2);
			$startpage=($pq-1)*$perpagesize;
			$db->limit($startpage,$perpagesize);
			$res=$db->query();
			while($row=$db->fetchRow($res))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],4,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;
				$mail_1[$i][0]=$row[0];
				$mail_1[$i][1]=$row[1];
				$mail_1[$i][2]=$row[2];
				$mail_1[$i][3]=$row[3];
				$mail_1[$i][4]=$row[4];
				$mail_1[$i][5]=$row[5];
				$mail_1[$i][6]=$row[6];
				$mail_1[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail_1[$i][7]=$row[7];
				$mail_1[$i][8]=$row[8];
				$mail_1[$i][9]=$row[9];
				$mail_1[$i][10]=$row[10];
				$mail_1[$i][11]=$row[11];
				$mail_1[$i][12]=$row[12];
				$mail_1[$i][13]=4;
				$mail_1[$i][14]=$row[13];
				$mail_1[$i][15]=$row[14];
				$mail_1[$i][16]=$flag1;
				$mail_1[$i][17]=$row[15];
				$i++;
			}
			$mail[0]=$mail_1[0];
            $counter = count($mail_1);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail);$a++)
				{
					if($mail_1[$r][15]==$mail[$a][15])
					{
						$flag0=1;
						if ($mail_1[$r][16]==1) {
                            $mail[$a][16]=1;
                        }
						if ($mail[$a][10]==1) {
                            $mail_1[$r][10]=1;
                        }
					}
				}
				if($flag0==0)
				{

					$mail[$w]=$mail_1[$r];
					$w++;
				}
			}
			if(!isset($page))
			{
				$page=1;
			}
			/*$p=$page;
			$index=($p-1)*$perpagesize;
			for($r=0;$r<$perpagesize;$r++,$index++)
			{
				if($index>=$w)
				break;
				$mail_new[$r]=$mail[$index];
			}*/


			if($page>$w)
			{
				$page=1;
				$startpage=0;

			}
			$startpage=($page-1)*$perpagesize;

			if($startpage<0)//for firsttime
			{

				$page=1;
				$startpage=0;
				//$db->limit($startpage,$perpagesize);
			}
			$this->setLoopValue("mail",$mail);
			$this->setValue("firstid",$mail[0][0]);
			$nmbr=count($mail);
			if ($mail[0][0]=="") {
                $nmbr=0;
            }
			$this->setValue("array_count",$nmbr);
			$this->setValue("number",$nmbr);

			$previouspage=$page-1;$s1=0;$s2=0;
			$pagelink="<div class=\"pagingtbl\" >";
			if($page!=1)
			{
					$s1=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
			}
			//else
			//$pagelink.="<td align=\"left\">&nbsp;</td>";

			$nextpage=$page+1;
			if($page*$perpagesize<($nosp))//if($page*10<=($total+10))
			{
					$s2=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
			}
			//else
			//$pagelink.="<td align=\"right\" style=\"padding-right: 2px;\">&nbsp;</td>";
			$pagelink.="</div>";$s=$s1+$s2;
$this->setValue("show",$s);	
			$this->setValue("pagelink",$pagelink);
		}
		elseif($folder==5)
		{

			$db->select("nesote_email_trash_$tablenumber");


			$db->fields("*");

			$db->where("userid=?",$id);

			$db->order("time desc");
			$db->limit(0,1);
			$res=$db->query();
			$row=$db->fetchRow($res);
			$db->select("nesote_email_trash_$tablenumber");
			$db->fields("*");

			$db->where("userid=?",$id);

			$db->order("time desc");
			$res=$db->query();
			$no=$db->numRows($res);$not=$no;
			
			$db->select("nesote_email_trash_$tablenumber");
			$db->fields("*");
			$db->where("userid=?",$id);
			$db->order("time desc");
			$page=$this->getParam(2);
			$startpage=($pq-1)*$perpagesize;
			$db->limit($startpage,$perpagesize);
			$res=$db->query();
			while($row=$db->fetchRow($res))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],5,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;
				$mail_1[$i][0]=$row[0];
				$mail_1[$i][1]=$row[1];
				$mail_1[$i][2]=$row[2];
				$mail_1[$i][3]=$row[3];
				$mail_1[$i][4]=$row[4];
				$mail_1[$i][5]=$row[5];
				$mail_1[$i][6]=$row[6];
				$mail_1[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail_1[$i][7]=$row[7];
				$mail_1[$i][8]=$row[8];
				$mail_1[$i][9]=$row[9];
				$mail_1[$i][10]=$row[10];
				$mail_1[$i][11]=$row[11];
				$mail_1[$i][12]=$row[12];
				$mail_1[$i][13]=5;
				$mail_1[$i][14]=$row[13];
				$mail_1[$i][15]=$row[14];
				$mail_1[$i][16]=$flag1;
				$mail_1[$i][17]=$row[15];
				$i++;
			}
			$mail[0]=$mail_1[0];
            $counter = count($mail_1);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail);$a++)
				{
					if($mail_1[$r][15]==$mail[$a][15])
					{
						$flag0=1;
						if ($mail_1[$r][16]==1) {
                            $mail[$a][16]=1;
                        }
					}
				}
				if($flag0==0)
				{

					$mail[$w]=$mail_1[$r];
					$w++;
				}
			}

			if(!isset($page))
			{
				$page=1;
			}
			/*$p=$page;
			$index=($p-1)*$perpagesize;
			for($r=0;$r<$perpagesize;$r++,$index++)
			{
				if($index>=$w)
				break;
				$mail_new[$r]=$mail[$index];
			}*/


			if($page>$w)
			{
			$page=1;
			$startpage=0;

			}
			$startpage=($page-1)*$perpagesize;

			if($startpage<0)//for firsttime
			{

				$page=1;
				$startpage=0;
				//$db->limit($startpage,$perpagesize);
			}
			$this->setLoopValue("mail",$mail);
			$this->setValue("firstid",$mail[0][0]);
			$nmbr=count($mail);
			if ($mail[0][0]=="") {
                $nmbr=0;
            }
			$this->setValue("array_count",$nmbr);
			$this->setValue("number",$nmbr);

			$previouspage=$page-1;$s1=0;$s2=0;
			$pagelink="<div class=\"pagingtbl\">";
			if($page!=1)
			{$s1=1;
					
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
			}
			//else
			//$pagelink.="<td align=\"left\">&nbsp;</td>";

			$nextpage=$page+1;
			if($page*$perpagesize<($not))//if($page*10<=($total+10))
			{
				$s2=1;	
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
			}
			//else
			//$pagelink.="<td align=\"right\" style=\"padding-right: 2px;\">&nbsp;</td>";
			$pagelink.="</div>";$s=$s1+$s2;
$this->setValue("show",$s);	
			$this->setValue("pagelink",$pagelink);
		}

		elseif($folder==0)
		{
			$i=0;$s=0;$t=0;$d=0;$c=0;
			$all=1;

			$mail=[];
			//starred mail
			$db1=new NesoteDALController();
			$db1->select("nesote_email_inbox_$tablenumber");
			$db1->fields("*");
			$db1->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%')",[$id]);
			$db1->order("time asc");
			$res1=$db1->query();
			$no1=$db1->numRows($res1);

			if($no1!=0)
			{
				//echo $db1->getQuery();
				while($row=$db1->fetchRow($res1))
				{
					$db->select("nesote_email_attachments_$tablenumber");
					$db->fields("*");
					$db->where("mailid=? and folderid=? and attachment=?",[$row[0],1,1]);
					$rs=$db->query();
					$rw1=$db->numRows($rs);
					$flag1 = $rw1 > 0 ? 1 : 0;
					$mail_1[$i][0]=$row[0];
					$mail_1[$i][1]=$row[1];
					$mail_1[$i][2]=$row[2];
					$mail_1[$i][3]=$row[3];
					$mail_1[$i][4]=$row[4];
					$mail_1[$i][5]=$row[5];
					$mail_1[$i][6]=$row[6];
				    $mail_1[$i][6]=$this->short_subject($row[6]);
					preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
					$mail_1[$i][7]=$row[7];
					$mail_1[$i][8]=$row[8];
					$mail_1[$i][9]=$row[9];
					$mail_1[$i][10]=$row[10];
					$mail_1[$i][11]=$row[11];
					$mail_1[$i][12]=$row[12];
					$mail_1[$i][13]=1;
					$mail_1[$i][14]=$row[13];
					$mail_1[$i][15]=$row[14];
					$mail_1[$i][16]=$flag1;
					$mail_1[$i][17]=$row[15];
					$i++;
				}

				$mail1[0]=$mail_1[0];
                $counter = count($mail_1);

				for($r=1,$count1=1;$r<$counter;$r++)
				{

					$flag0=0;
					for($a=0;$a<count($mail1);$a++)
					{
						if($mail_1[$r][15]==$mail1[$a][15])
						{
							$flag0=1;
							if ($mail_1[$r][16]==1) {
                                $mail1[$a][16]=1;
                            }
						}
					}
					if($flag0==0)
					{

						$mail1[$count1]=$mail_1[$r];
						$count1++;
					}
				}
			}
			$db1->select("nesote_email_spam_$tablenumber");
			$db1->fields("*");
			$db1->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%')",[$id]);
			$db1->order("time asc");
			$res1=$db1->query();
			$no2=$db1->numRows($res1);
			if($no2!=0)
			{
				while($row=$db1->fetchRow($res1))
				{
					$db->select("nesote_email_attachments_$tablenumber");
					$db->fields("*");
					$db->where("mailid=? and folderid=? and attachment=?",[$row[0],4,1]);
					$rs=$db->query();
					$rw1=$db->numRows($rs);
					$flag1 = $rw1 > 0 ? 1 : 0;
					$mail_2[$s][0]=$row[0];
					$mail_2[$s][1]=$row[1];
					$mail_2[$s][2]=$row[2];
					$mail_2[$s][3]=$row[3];
					$mail_2[$s][4]=$row[4];
					$mail_2[$s][5]=$row[5];
					$mail_2[$s][6]=$row[6];
				    $mail_2[$i][6]=$this->short_subject($row[6]);
					preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
					$mail_2[$s][7]=$row[7];
					$mail_2[$s][8]=$row[8];
					$mail_2[$s][9]=$row[9];
					$mail_2[$s][10]=$row[10];
					$mail_2[$s][11]=$row[11];
					$mail_2[$s][12]=$row[12];
					$mail_2[$s][13]=4;
					$mail_2[$s][14]=$row[13];
					$mail_2[$s][15]=$row[14];
					$mail_2[$s][16]=$flag1;
					$mail_2[$s][17]=$row[15];
					$s++;
				}
				$mail2[0]=$mail_2[0];
                $counter = count($mail_2);
				for($r=1,$count2=1;$r<$counter;$r++)
				{

					$flag0=0;
					for($a=0;$a<count($mail2);$a++)
					{
						if($mail_2[$r][15]==$mail2[$a][15])
						{
							$flag0=1;
							if ($mail_2[$r][16]==1) {
                                $mail2[$a][16]=1;
                            }

						}
					}
					if($flag0==0)
					{

						$mail2[$count2]=$mail_2[$r];
						$count2++;
					}
				}
			}

			$db1->select("nesote_email_draft_$tablenumber");
			$db1->fields("*");
			$db1->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%')",[$id]);
			$db1->order("time asc");
			$res1=$db1->query();
			//echo $db1->getQuery();
			$no3=$db1->numRows($res1);
			if($no3!=0)
			{
				while($row=$db1->fetchRow($res1))
				{
					$db->select("nesote_email_attachments_$tablenumber");
					$db->fields("*");
					$db->where("mailid=? and folderid=? and attachment=?",[$row[0],2,1]);
					$rs=$db->query();
					$rw1=$db->numRows($rs);
					$flag1 = $rw1 > 0 ? 1 : 0;
					$mail_3[$d][0]=$row[0];
					$mail_3[$d][1]=$row[1];
					$mail_3[$d][2]=$row[2];
					$mail_3[$d][3]=$row[3];
					$mail_3[$d][4]=$row[4];
					$mail_3[$d][5]=$row[5];
					$mail_3[$d][6]=$row[6];
				    $mail_3[$i][6]=$this->short_subject($row[6]);
					preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
					$mail_3[$d][7]=$row[7];
					$mail_3[$d][8]=$row[8];
					$mail_3[$d][9]=$row[9];
					$mail_3[$d][10]=$row[10];
					$mail_3[$d][11]=$row[11];
					$mail_3[$d][12]=$row[12];
					$mail_3[$d][13]=2;
					$mail_3[$d][14]=$row[13];
					$mail_3[$d][15]=$row[14];
					$mail_3[$d][16]=$flag1;
					//$mail_3[$d][17]=$row[15];
					$d++;
				}
				$mail3[0]=$mail_3[0];
                $counter = count($mail_3);
				for($r=1,$count3=1;$r<$counter;$r++)
				{

					$flag0=0;
					for($a=0;$a<count($mail3);$a++)
					{
						if($mail_3[$r][15]==$mail3[$a][15])
						{
							$flag0=1;
							if ($mail_3[$r][16]==1) {
                                $mail3[$a][16]=1;
                            }
						}
					}
					if($flag0==0)
					{

						$mail3[$count3]=$mail_3[$r];
						$count3++;
					}
				}
			}
			$db1->select("nesote_email_sent_$tablenumber");
			$db1->fields("*");
			$db1->where("userid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%')",[$id]);
			$db1->order("time asc");
			$res1=$db1->query();
			$no4=$db1->numRows($res1);
			if($no4!=0)
			{
				while($row=$db1->fetchRow($res1))
				{
					$db->select("nesote_email_attachments_$tablenumber");
					$db->fields("*");
					$db->where("mailid=? and folderid=? and attachment=?",[$row[0],3,1]);
					$rs=$db->query();
					$rw1=$db->numRows($rs);
					$flag1 = $rw1 > 0 ? 1 : 0;
					$mail_4[$t][0]=$row[0];
					$mail_4[$t][1]=$row[1];
					$mail_4[$t][2]=$row[2];
					$mail_4[$t][3]=$row[3];
					$mail_4[$t][4]=$row[4];
					$mail_4[$t][5]=$row[5];
					$mail_4[$t][6]=$row[6];
				    $mail_4[$i][6]=$this->short_subject($row[6]);
					preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
					$mail_4[$t][7]=$row[7];
					$mail_4[$t][8]=$row[8];
					$mail_4[$t][9]=$row[9];
					$mail_4[$t][10]=$row[10];
					$mail_4[$t][11]=$row[11];
					$mail_4[$t][12]=$row[12];
					$mail_4[$t][13]=3;
					$mail_4[$t][14]=$row[13];
					$mail_4[$t][15]=$row[14];
					$mail_4[$t][16]=$flag1;
					$mail_4[$t][17]=$row[15];
					$t++;
				}
				$mail4[0]=$mail_4[0];
                $counter = count($mail_4);
				for($r=1,$count4=1;$r<$counter;$r++)
				{

					$flag0=0;
					for($a=0;$a<count($mail4);$a++)
					{
						if($mail_4[$r][15]==$mail4[$a][15])
						{
							$flag0=1;
							if ($mail_4[$r][16]==1) {
                                $mail4[$a][16]=1;
                            }
						}
					}
					if($flag0==0)
					{

						$mail4[$count4]=$mail_4[$r];
						$count4++;
					}
				}
			}
			$db->select("nesote_email_customfolder");
			$db->fields("id");
			$db->where("userid=?",[$id]);

			$res2=$db->query();
			while($row1=$db->fetchRow($res2))
			{

				$ids.=$row1[0].",";
			}
			$ids=substr($ids,0,-1);
			$number=$db->numRows($res2);
			if($number!=0)
			{
				$db1->select("nesote_email_customfolder_mapping_$tablenumber");
				$db1->fields("*");
				$db1->where("folderid in($ids) and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%')");
				$db1->order("time asc");

				$res1=$db1->query();
				//echo $db1->getQuery();exit;
				$no5 += $db1->numRows($res1);
				if($no5!=0)
				{
					while($row=$db1->fetchRow($res1))
					{
						$db->select("nesote_email_attachments_$tablenumber");
						$db->fields("*");
						$db->where("mailid=? and folderid=? and attachment=?",[$row[0],$row[1],1]);
						$rs=$db->query();
						$rw1=$db->numRows($rs);
						$flag1 = $rw1 > 0 ? 1 : 0;
						$mail_5[$c][0]=$row[0];
						$mail_5[$c][1]=$row[1];
						$mail_5[$c][2]=$row[2];
						$mail_5[$c][3]=$row[3];
						$mail_5[$c][4]=$row[4];
						$mail_5[$c][5]=$row[5];
						$mail_5[$c][6]=$row[6];
				        $mail_5[$i][6]=$this->short_subject($row[6]);
						preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
						$mail_5[$c][7]=$row[7];
						$mail_5[$c][8]=$row[8];
						$mail_5[$c][9]=$row[9];
						$mail_5[$c][10]=$row[10];
						$mail_5[$c][11]=$row[11];
						$mail_5[$c][12]=$row[12];
						$mail_5[$c][13]=$row[1];
						$mail_5[$c][14]=$row[13];
						$mail_5[$c][15]=$row[14];
						$mail_5[$c][16]=$flag1;
						$mail_5[$c][17]=$row[15];

						$c++;

					}
					$mail5[0]=$mail_5[0];
                    $counter = count($mail_5);
					for($r=1,$count5=1;$r<$counter;$r++)
					{

						$flag0=0;
						for($a=0;$a<count($mail5);$a++)
						{
							if($mail_5[$r][15]==$mail5[$a][15])
							{
								$flag0=1;
								if ($mail_5[$r][16]==1) {
                                    $mail5[$a][16]=1;
                                }
							}
						}
						if($flag0==0)
						{

							$mail5[$count5]=$mail_5[$r];
							$count5++;
						}
					}
				}
			}


			if ($i>0) {
                $i--;
            }
			if ($s>0) {
                $s--;
            }
			if ($t>0) {
                $t--;
            }
			if ($d>0) {
                $d--;
            }
			if ($c>0) {
                $c--;
            }
			$total=$no1+$no2+$no3+$no4+$no5;
			//$total--;//
			//echo $c;exit;
			for($k=$i,$l=$s,$m=$t,$n=$d,$p=0,$o=$c;$total>0;$total--,$p++)
			{
				//echo $no3."/$n";
				//echo $mail1[$k][8]."/";	echo $mail5[$o][8]."<br>";
				$loop=0;
				$num=0;
				$inbox_top=$mail1[$k][8];
				$spam_top=$mail2[$l][8];
				$sent_top=$mail4[$m][8];
				$draft_top=$mail3[$n][8];
				$cf_top=$mail5[$o][8];//echo $cf_top;

				$max=max($inbox_top,$spam_top,$sent_top,$draft_top,$cf_top);
				//echo $inbox_top."==<br>".$spam_top."==<br>".$sent_top."==<br>".$draft_top."==<br>".$cf_top."///<br>";
				//echo $max;//echo $max."=".$draft_top;
				if($max==$inbox_top && $loop === 0)
				{
					//$top=$inbox_top;
					//echo "i";
					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail1[$k][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail1[$k];
						$k--;
						$loop=1;
					}
					//continue;
				}
				if($max==$spam_top && $loop==0)
				{
					//echo "s";
					//$top=$inbox_top;
					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail2[$l][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail2[$l];
						$l--;
						$loop=1;
					}
					//continue;
				}
				if($max==$sent_top && $loop==0)
				{
					//echo "t";
					//$top=$inbox_top;
					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail4[$m][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail4[$m];
						$m--;
						$loop=1;
					}
					//continue;
				}
				if($max==$draft_top && $loop==0)
				{
					//echo "d";
					//$top=$inbox_top;
					//	echo "k";
					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail3[$n][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail3[$n];
						$n--;
						$loop=1;
					}
					//continue;
				}
				if($max==$cf_top && $loop==0)
				{
					//echo "c";
					//	$top=$inbox_top;
					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail5[$o][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail5[$o];
						$o--;
						$loop=1;
					}

				}

			}




			$total=$count1+$count2+$count3+$count4+$count5;


			for($k=$count1-1,$l=$count2-1,$m=$count4-1,$n=$count3-1,$p=0,$o=$count5-1;$total>0;$total--)
			{

				$loop=0;
				$num=0;
				$inbox_top=$mail1[$k][8];
				$spam_top=$mail2[$l][8];
				$sent_top=$mail4[$m][8];
				$draft_top=$mail3[$n][8];
				$cf_top=$mail5[$o][8];

				$max=max($inbox_top,$spam_top,$sent_top,$draft_top,$cf_top);


				if($max==$inbox_top && $loop === 0)
				{



					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail1[$k][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{

						$mail[$p]=$mail1[$k];
						$k--;
						$p++;
						$loop=1;
					}

				}
				if($max==$spam_top && $loop==0)
				{



					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail2[$l][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{

						$mail[$p]=$mail2[$l];
						$l--;
						$p++;
						$loop=1;
					}

				}
				if($max==$sent_top && $loop==0)
				{


					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail4[$m][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{

						$mail[$p]=$mail4[$m];

						$p++;
						$loop=1;
					}

					$m--;
				}
				if($max==$draft_top && $loop==0)
				{


					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail3[$n][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{

						$mail[$p]=$mail3[$n];
						$n--;
						$p++;
						$loop=1;
					}

				}
				if($max==$cf_top && $loop==0)
				{


					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail5[$o][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{

						$mail[$p]=$mail5[$o];
						$o--;
						$p++;
						$loop=1;
					}

				}

			}

			$no=count($mail);

			$page=$this->getParam(2);
			if(!isset($page))
			{
				$page=1;
			}
			$p=$page;
			$index=($p-1)*$perpagesize;
			for($r=0;$r<$perpagesize;$r++,$index++)
			{
				if ($index>=$no) {
                    break;
                }
				$mail_new[$r]=$mail[$index];
			}


			if($page>$no)
			{
				$page=1;
				$startpage=0;

			}
			$startpage=($page-1)*$perpagesize;

			if($startpage<0)//for firsttime
			{

				$page=1;
				$startpage=0;
				
			}
			$this->setLoopValue("mail",$mail_new);
			$this->setValue("firstid",$mail[0][0]);
			$nmbr=count($mail_new);
			if ($mail[0][0]=="") {
                $nmbr=0;
            }
			$this->setValue("number",$nmbr);
			$this->setValue("array_count",$nmbr);

			$previouspage=$page-1;$s1=0;$s2=0;
			$pagelink="<div class=\"pagingtbl\">";
			if($page!=1)
			{
					$s1=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
			}
			

			$nextpage=$page+1;
			if($page*$perpagesize<($no))
			{
					$s2=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
			}
			
			$pagelink.="</div>";$s=$s1+$s2;
$this->setValue("show",$s);	
			$this->setValue("pagelink",$pagelink);




		}
		elseif($folder==6)
		{
			$i=0;$s=0;$t=0;$d=0;$c=0;
			$all=1;

			$mail=[];
			//starred mail
			$db1=new NesoteDALController();
			$db1->select("nesote_email_inbox_$tablenumber");
			$db1->fields("*");
			if($flag==1)
			{
				$db1->where("userid=? and starflag=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$id,1]);
			}
			else
			{
				$db1->where("userid=? and starflag=?",[$id,1]);
			}

			$db1->order("time asc");
			$res1=$db1->query();
			 $no1=$db1->numRows($res1);
			//echo $db1->getQuery();
			while($row=$db1->fetchRow($res1))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],1,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;
				$mail_1[$i][0]=$row[0];
				$mail_1[$i][1]=$row[1];
				$mail_1[$i][2]=$row[2];
				$mail_1[$i][3]=$row[3];
				$mail_1[$i][4]=$row[4];
				$mail_1[$i][5]=$row[5];
				$mail_1[$i][6]=$row[6];
				$mail_1[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail_1[$i][7]=$row[7];
				$mail_1[$i][8]=$row[8];
				$mail_1[$i][9]=$row[9];
				$mail_1[$i][10]=$row[10];
				$mail_1[$i][11]=$row[11];
				$mail_1[$i][12]=$row[12];
				$mail_1[$i][13]=1;
				$mail_1[$i][14]=$row[13];
				$mail_1[$i][15]=$row[14];
				$mail_1[$i][16]=$flag1;
				$mail_1[$i][17]=$row[15];
				$i++;
			}
			$mail1[0]=$mail_1[0];
            $counter = count($mail_1);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail1);$a++)
				{
					if($mail_1[$r][15]==$mail1[$a][15])
					{
						$flag0=1;

					}
				}
				if($flag0==0)
				{

					$mail1[$w]=$mail_1[$r];
					$w++;
				}
			}
			$db1->select("nesote_email_draft_$tablenumber");
			$db1->fields("*");
			if($flag==1)
			{
				$db1->where("userid=? and starflag=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$id,1]);
			}
			else
			{
				$db1->where("userid=? and starflag=?",[$id,1]);
			}

			$db1->order("time asc");
			$res1=$db1->query();
			//echo $db1->getQuery();
			 $no3=$db1->numRows($res1);
			while($row=$db1->fetchRow($res1))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],2,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;
				$mail_3[$d][0]=$row[0];
				$mail_3[$d][1]=$row[1];
				$mail_3[$d][2]=$row[2];
				$mail_3[$d][3]=$row[3];
				$mail_3[$d][4]=$row[4];
				$mail_3[$d][5]=$row[5];
				$mail_3[$d][6]=$row[6];
			    $mail_3[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail_3[$d][7]=$row[7];
				$mail_3[$d][8]=$row[8];
				$mail_3[$d][9]=$row[9];
				$mail_3[$d][10]=$row[10];
				$mail_3[$d][11]=$row[11];
				$mail_3[$d][12]=$row[12];
				$mail_3[$d][13]=2;
				$mail_3[$d][14]=$row[13];
				$mail_3[$d][15]=$row[14];
				$mail_3[$d][16]=$flag1;
				//$mail_3[$d][17]=$row[15];
				$d++;
			}
			$mail3[0]=$mail_3[0];
            $counter = count($mail_3);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail3);$a++)
				{
					if($mail_3[$r][15]==$mail3[$a][15])
					{
						$flag0=1;

					}
				}
				if($flag0==0)
				{

					$mail3[$w]=$mail_3[$r];
					$w++;
				}
			}
			$db1->select("nesote_email_sent_$tablenumber");
			$db1->fields("*");
			if($flag==1)
			{
				$db1->where("userid=? and starflag=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$id,1]);
			}
			else
			{
				$db1->where("userid=? and starflag=?",[$id,1]);
			}

			$db1->order("time asc");
			$res1=$db1->query();
			 $no4=$db1->numRows($res1);
			while($row=$db1->fetchRow($res1))
			{
				$db->select("nesote_email_attachments_$tablenumber");
				$db->fields("*");
				$db->where("mailid=? and folderid=? and attachment=?",[$row[0],3,1]);
				$rs=$db->query();
				$rw1=$db->numRows($rs);
				$flag1 = $rw1 > 0 ? 1 : 0;
				$mail_4[$t][0]=$row[0];
				$mail_4[$t][1]=$row[1];
				$mail_4[$t][2]=$row[2];
				$mail_4[$t][3]=$row[3];
				$mail_4[$t][4]=$row[4];
				$mail_4[$t][5]=$row[5];
				$mail_4[$t][6]=$row[6];
			    $mail_4[$i][6]=$this->short_subject($row[6]);
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail_4[$t][7]=$row[7];
				$mail_4[$t][8]=$row[8];
				$mail_4[$t][9]=$row[9];
				$mail_4[$t][10]=$row[10];
				$mail_4[$t][11]=$row[11];
				$mail_4[$t][12]=$row[12];
				$mail_4[$t][13]=3;
				$mail_4[$t][14]=$row[13];
				$mail_4[$t][15]=$row[14];
				$mail_4[$t][16]=$flag1;
				$mail_4[$t][17]=$row[15];
				$t++;
			}
			$mail4[0]=$mail_4[0];
            $counter = count($mail_4);
			for($r=1,$w=1;$r<$counter;$r++)
			{

				$flag0=0;
				for($a=0;$a<count($mail4);$a++)
				{
					if($mail_4[$r][15]==$mail4[$a][15])
					{
						$flag0=1;
					}
				}
				if($flag0==0)
				{

					$mail4[$w]=$mail_4[$r];
					$w++;
				}
			}
			$db->select("nesote_email_customfolder");
			$db->fields("id");
			$db->where("userid=?",[$id]);

			$res2=$db->query();
			while($row1=$db->fetchRow($res2))
			{

				$ids.=$row1[0].",";
			}
			$ids=substr($ids,0,-1);
			$number=$db->numRows($res2);
			if($number!=0)
			{
				$db1->select("nesote_email_customfolder_mapping_$tablenumber");
				$db1->fields("*");
				if($flag==1)
				{
					$db1->where("folderid in($ids)  and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') and starflag=? ",[1]);
				}
				else
				{
					$db1->where("folderid in($ids) and starflag=?",[1]);
				}


				$db1->order("time asc");

				$res1=$db1->query();
				//echo $db1->getQuery();exit;
				 $no5 += $db1->numRows($res1);
				while($row=$db1->fetchRow($res1))
				{
					$db->select("nesote_email_attachments_$tablenumber");
					$db->fields("*");
					$db->where("mailid=? and folderid=? and attachment=?",[$row[0],$row[1],1]);
					$rs=$db->query();
					$rw1=$db->numRows($rs);
					$flag1 = $rw1 > 0 ? 1 : 0;
					$mail_5[$c][0]=$row[0];
					$mail_5[$c][1]=$row[1];
					$mail_5[$c][2]=$row[2];
					$mail_5[$c][3]=$row[3];
					$mail_5[$c][4]=$row[4];
					$mail_5[$c][5]=$row[5];
					$mail_5[$c][6]=$row[6];
				    $mail_5[$i][6]=$this->short_subject($row[6]);
					preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
					$mail_5[$c][7]=$row[7];
					$mail_5[$c][8]=$row[8];
					$mail_5[$c][9]=$row[9];
					$mail_5[$c][10]=$row[10];
					$mail_5[$c][11]=$row[11];
					$mail_5[$c][12]=$row[12];
					$mail_5[$c][13]=$row[1];
					$mail_5[$c][14]=$row[13];
					$mail_5[$c][15]=$row[14];
					$mail_5[$c][16]=$flag1;
					$mail_5[$c][17]=$row[15];
					$c++;
				}
				$mail5[0]=$mail_5[0];
                $counter = count($mail_5);
				for($r=1,$w=1;$r<$counter;$r++)
				{

					$flag0=0;
					for($a=0;$a<count($mail5);$a++)
					{
						if($mail_5[$r][15]==$mail5[$a][15])
						{
							$flag0=1;
						}
					}
					if($flag0==0)
					{

						$mail5[$w]=$mail_5[$r];
						$w++;
					}
				}
			}



			if ($i>0) {
                $i--;
            }
			if ($s>0) {
                $s--;
            }
			if ($t>0) {
                $t--;
            }
			if ($d>0) {
                $d--;
            }
			if ($c>0) {
                $c--;
            }
		    $total=$no1+$no2+$no3+$no4+$no5;

			for($k=$i,$l=$s,$m=$t,$n=$d,$p=0,$o=$c;$total>0;$total--,$p++)
			{
					
				$loop=0;
				$num=0;
				$inbox_top=$mail1[$k][8]."//";
				$spam_top=$mail2[$l][8];
				$sent_top=$mail4[$m][8];
				$draft_top=$mail3[$n][8];
				$cf_top=$mail5[$o][8];

				$max=max($inbox_top,$spam_top,$sent_top,$draft_top,$cf_top);
				if($max==$inbox_top && $loop === 0)
				{

					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail1[$k][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail1[$k];
						$k--;
						$loop=1;
					}

				}
				if($max==$spam_top && $loop==0)
				{

					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail2[$l][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail2[$l];
						$l--;
						$loop=1;
					}

				}
				if($max==$sent_top && $loop==0)
				{

					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail4[$m][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail4[$m];
						$m--;
						$loop=1;
					}

				}
				if($max==$draft_top && $loop==0)
				{

					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail3[$n][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail3[$n];
						$n--;
						$loop=1;
					}

				}
				if($max==$cf_top && $loop==0)
				{

					$count=count($mail);
					for($g=0;$g<$count;$g++)
					{
						if($mail[$g][15]==$mail5[$o][15])
						{
							$num=1;
							break;
						}
						else {
                            $num=0;
                        }
					}
					if($num==0)
					{
						$mail[$p]=$mail5[$o];
						$o--;
						$loop=1;
					}

				}
					
			}
		 $no=count($mail);
		 $page=$this->getParam(2);
			if(!isset($page))
			{
				$page=1;
			}
			 $p=$page;
			 $index=($p-1)*$perpagesize;
			for($r=0;$r<$perpagesize;$r++,$index++)
			{
				if ($index>=$no) {
                    break;
                }
				$mail_new[$r]=$mail[$index];
			}

			if($page>$no)
			{
				$page=1;
				$startpage=0;

			}
			$startpage=($page-1)*$perpagesize;

			if($startpage<0)//for firsttime
			{

				$page=1;
				$startpage=0;
				
			}
			
			$this->setLoopValue("mail",$mail_new);
			$this->setValue("firstid",$mail[0][0]);
			$nmbr=count($mail_new);
			//if($mail[0][0]=="")
			//$nmbr=0;
			$this->setValue("number",$nmbr);
			$this->setValue("array_count",$nmbr);
			
			
			$previouspage=$page-1;$s1=0;$s2=0;
			$pagelink="<div class=\"pagingtbl\">";
			if($page!=1)
			{
					$s1=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
			}
			

			$nextpage=$page+1;
			if($page*$perpagesize<($w))//if($page*10<=($total+10))
			{
					$s2=1;
				$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
			}
			
			$pagelink.="</div>";$s=$s1+$s2;
$this->setValue("show",$s);	
			$this->setValue("pagelink",$pagelink);



		}

		else
		{
			$id=$this->getId();

			$db->select("nesote_email_customfolder");
			$db->fields("id");
			$db->where("userid=? and id=?",[$id,$folder]);
			$res1=$db->query();
			$rw=$db->fetchRow($res1);
			$number=$db->numRows($res1);
			if($number!=0)
			{
				$db->select("nesote_email_customfolder_mapping_$tablenumber");
				$db->fields("*");
				if($flag==1)
				{
					$db->where("folderid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$folder]);
				}
				else
				{
					$db->where("folderid=?",[$folder]);
				}

				$db->order("time desc");
				$db->limit(0,1);
				$res=$db->query();
				
				$row=$db->fetchRow($res);
				$db->select("nesote_email_customfolder_mapping_$tablenumber");
				$db->fields("*");
				if($flag==1)
				{
					$db->where("folderid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$folder]);
				}
				else
				{
					$db->where("folderid=?",[$folder]);
				}
				$db->order("time desc");
				
					
				$res=$db->query();
				$nocust=$db->numRows($res);
				
				$db->select("nesote_email_customfolder_mapping_$tablenumber");
				$db->fields("*");
				if($flag==1)
				{
					$db->where("folderid=? and (subject like '%$search%' or body like '%$search%' or from_list like '%$search%') ",[$folder]);
				}
				else
				{
					$db->where("folderid=?",[$folder]);
				}
				$db->order("time desc");
				$startpage=($pq-1)*$perpagesize;
			    $db->limit($startpage,$perpagesize);
				$res=$db->query();
				while($row=$db->fetchRow($res))
				{
					$db->select("nesote_email_attachments_$tablenumber");
					$db->fields("*");
					$db->where("mailid=? and folderid=? and attachment=?",[$row[0],$row[1],1]);
					$rs=$db->query();
					$rw1=$db->numRows($rs);
					$flag1 = $rw1 > 0 ? 1 : 0;
					$mail_5[$i][0]=$row[0];
					$mail_5[$i][1]=$row[1];
					$mail_5[$i][2]=$row[2];
					$mail_5[$i][3]=$row[3];
					$mail_5[$i][4]=$row[4];
					$mail_5[$i][5]=$row[5];
					$mail_5[$i][6]=$row[6];
				    $mail_5[$i][6]=$this->short_subject($row[6]);
					preg_match('/<img(.+?)src=(.+?)>/i',(string) $row[7],$cset1);
						if ($cset1[2]!="") {
                            $row[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
					$mail_5[$i][7]=$row[7];
					$mail_5[$i][8]=$row[8];
					$mail_5[$i][9]=$row[9];
					$mail_5[$i][10]=$row[10];
					$mail_5[$i][11]=$row[11];
					$mail_5[$i][12]=$row[12];
					$mail_5[$i][13]=$row[1];
					$mail_5[$i][14]=$row[13];
					$mail_5[$i][15]=$row[14];
					$mail_5[$i][16]=$flag1;
					$mail_5[$i][17]=$row[15];
					$i++;
				}
				$mail[0]=$mail_5[0];
                $counter = count($mail_5);
				for($r=1,$w=1;$r<$counter;$r++)
				{

					$flag0=0;
					for($a=0;$a<count($mail);$a++)
					{
						if($mail_5[$r][15]==$mail[$a][15])
						{
							$flag0=1;
							if ($mail_5[$r][16]==1) {
                                $mail[$a][16]=1;
                            }
							if ($mail[$a][10]==1) {
                                $mail_5[$r][10]=1;
                            }
						}
					}
					if($flag0==0)
					{

						$mail[$w]=$mail_5[$r];
						$w++;
					}
				}
				if(!isset($page))
				{
					$page=1;
				}
				/*$p=$page;
				$index=($p-1)*$perpagesize;
				for($r=0;$r<$perpagesize;$r++,$index++)
				{
					if($index>=$w)
					break;
					$mail_new[$r]=$mail[$index];
				}*/
					

				if($page>$w)
				{
					$page=1;
					$startpage=0;

				}
				$startpage=($page-1)*$perpagesize;

				if($startpage<0)//for firsttime
				{

					$page=1;
					$startpage=0;
					//$db->limit($startpage,$perpagesize);
				}
				$this->setLoopValue("mail",$mail);
				$this->setValue("firstid",$mail[0][0]);
				$nmbr=count($mail);
				if ($mail[0][0]=="") {
                    $nmbr=0;
                }
				$this->setValue("number",$nmbr);
				$this->setValue("array_count",$nmbr);

				$previouspage=$page-1;$s1=0;$s2=0;
				$pagelink="<div class=\"pagingtbl\">";
				if($page!=1)
				{
$s1=1;
					$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$previouspage")."\" class=\"commonBtn1\">".$this->getmessage(432)."</a>";
				}
				

				$nextpage=$page+1;
				if($page*$perpagesize<($nocust))
				{
$s2=1;
					$pagelink.="<a href=\"".$this->url("mail/mailbox/$folder/$nextpage")."\" class=\"commonBtn1\">".$this->getmessage(433)."</a>";
				}
				
				$pagelink.="</div>";$s=$s1+$s2;
$this->setValue("show",$s);	
				$this->setValue("pagelink",$pagelink);
			}
		}
		$this->setValue("allsearch",$all);
		$folderid=$_COOKIE['folderid'];
		$this->setValue("folderid",$folderid);

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
	function gettolist($mailid,$folder)
	{//return $folder;
		
		$username=$_COOKIE['e_username'];
	    $tablenumber=$this->tableid($username);
		$ids="";
		$db1=new NesoteDALController();
		$db=new NesoteDALController();
		if ($folder==3) {
            $db1->select("nesote_email_sent_$tablenumber");
            $db1->fields("mail_references");
            $db1->where("id=?",[$mailid]);
            $rs1=$db1->query();
            //return $db1->getQuery();
            $row1=$db1->fetchRow($rs1);
            preg_match_all('/<item>(.+?)<\/item>/i',(string) $row1[0],$reply);
            $no=count($reply[1]);
            //return $no;
            $num = $no > 1 ? "(".$no.")" : "";
            $db->select("nesote_email_sent_$tablenumber");
            $db->fields("to_list,cc,bcc");
            $db->where("id=?",[$mailid]);
            $rs=$db->query();
            //return $db->getQuery();
            //$row=$db->fetchRow($rs);
            //				$db->select("nesote_email_sent");
            //				$db->fields("to_list,cc,bcc");
            //				$db->where("id=?",array($mailid));
            //				$rs=$db->query();
            $row=$db->fetchRow($rs);
            //return htmlspecialchars($row[0])."++++/".htmlspecialchars($row[1])."+++++++/".htmlspecialchars($row[2])."++++/";
            $to=htmlspecialchars(trim((string) $row[0]));
            $len=strlen($to);
            if ($to[($len-1)]==",") {
                $to=substr($to,0,-1);
            }
            $to_list=explode(",",$to);
            if (($row[0]!="")||($row[1]!="")||($row[2]!="")) {
                $ids=$this->getmessage(31).": ";
            }
            if ($to !== "") {
                $counter = count($to_list);
                for($i=0;$i<$counter;$i++)
				{
					//echo count($to_list)."to";
					$to_list[$i]=trim($to_list[$i]);
					if (str_starts_with($to_list[$i], "&nbsp;")) {
                        $to_list[$i]=str_replace("&nbsp;","",$to_list[$i]);
                    }
					preg_match('/(.+?)<(.+?)>/i',$to_list[$i],$name);
					if (count($name[1])==0) {
                        preg_match('/(.+?)&lt;(.+?)&gt;/i',$to_list[$i],$name);
                    }
					if (count($name[1])==1) {
                        $less_position=strpos($to_list[$i],"<");
                    }
					if ($less_position<1) {
                        $less_position=strpos($to_list[$i],"&lt;");
                    }
					//return $less_position;
					//if((count($name[1])==1)&&((strpos($to_list[$i],"<")!=0)||(strpos($to_list[$i],"&lt;")!=0)))
					if ($less_position>0) {
                        $ids.=$name[1].",";
                    } else {
                        $ids.=$to_list[$i].",";
                    }
				}
            }
            $cc= htmlspecialchars(trim((string) $row[1]));
            $len=strlen($cc);
            if ($cc[($len-1)]==",") {
                $cc=substr($cc,0,-1);
            }
            $cc_list=explode(",",$cc);
            if ($cc !== "") {
                $counter = count($cc_list);
                for($i=0;$i<$counter;$i++)
				{
					//echo count($cc_list)."cc";
					$cc_list[$i]=trim($cc_list[$i]);
					if (str_starts_with($cc_list[$i], "&nbsp;")) {
                        $cc_list[$i]=str_replace("&nbsp;","",$cc_list[$i]);
                    }
					preg_match('/(.+?)<(.+?)>/i',$cc_list[$i],$name);
					if (count($name[1])==0) {
                        preg_match('/(.+?)&lt;(.+?)&gt;/i',$cc_list[$i],$name);
                    }
					if (count($name[1])==1) {
                        $less_position=strpos($cc_list[$i],"<");
                    }
					if ($less_position<1) {
                        $less_position=strpos($cc_list[$i],"&lt;");
                    }
					//return $less_position;
					//if((count($name[1])==1)&&((strpos($cc_list[$i],"<")!=0)||(strpos($cc_list[$i],"&lt;")!=0)))
					if ($less_position>0) {
                        $ids.=$name[1].",";
                    } else {
                        $ids.=$cc_list[$i].",";
                    }
				}
            }
            $bcc= htmlspecialchars(trim((string) $row[2]));
            $len=strlen($bcc);
            if ($bcc[($len-1)]==",") {
                $bcc=substr($bcc,0,-1);
            }
            $bcc_list=explode(",",$bcc);
            if ($bcc !== "") {
                $counter = count($bcc_list);
                for($i=0;$i<$counter;$i++)
				{
					//echo count($bcc_list)."bcc";
					$bcc_list[$i]=trim($bcc_list[$i]);
					if (str_starts_with($bcc_list[$i], "&nbsp;")) {
                        $bcc_list[$i]=str_replace("&nbsp;","",$bcc_list[$i]);
                    }
					preg_match('/(.+?)<(.+?)>/i',$bcc_list[$i],$name);
					if (count($name[1])==0) {
                        preg_match('/(.+?)&lt;(.+?)&gt;/i',$bcc_list[$i],$name);
                    }
					if (count($name[1])==1) {
                        $less_position=strpos($bcc_list[$i],"<");
                    }
					if ($less_position<1) {
                        $less_position=strpos($bcc_list[$i],"&lt;");
                    }
					//return $less_position;
					//if((count($name[1])==1)&&((strpos($bcc_list[$i],"<")!=0)||(strpos($bcc_list[$i],"&lt;")!=0)))
					if ($less_position>0) {
                        $ids.=$name[1].",";
                    } else {
                        $ids.=$bcc_list[$i].",";
                    }
				}
            }
            $ids=substr($ids,0,-1);
            if (strlen($ids)>20) {
                return substr($ids,0,20)."...".$num;
            } else {
                return $ids.$num;
            }
        } elseif ($folder==2) {
            $db->select("nesote_email_draft_$tablenumber");
            $db->fields("to_list,cc,bcc");
            $db->where("id=?",[$mailid]);
            $rs=$db->query();
            $row=$db->fetchRow($rs);
            //return htmlspecialchars($row[0])."++++/".htmlspecialchars($row[1])."+++++++/".htmlspecialchars($row[2])."++++/";
            $to=htmlspecialchars(trim((string) $row[0]));
            $len=strlen($to);
            if ($to[($len-1)]==",") {
                $to=substr($to,0,-1);
            }
            $to_list=explode(",",$to);
            if (($row[0]!="")||($row[1]!="")||($row[2]!="")) {
                $ids=$this->getmessage(31).": ";
            }
            if ($to !== "") {
                $counter = count($to_list);
                for($i=0;$i<$counter;$i++)
				{
					//echo count($to_list)."to";
					$to_list[$i]=trim($to_list[$i]);
					if (str_starts_with($to_list[$i], "&nbsp;")) {
                        $to_list[$i]=str_replace("&nbsp;","",$to_list[$i]);
                    }
					preg_match('/(.+?)<(.+?)>/i',$to_list[$i],$name);
					if (count($name[1])==0) {
                        preg_match('/(.+?)&lt;(.+?)&gt;/i',$to_list[$i],$name);
                    }
					if (count($name[1])==1) {
                        $less_position=strpos($to_list[$i],"<");
                    }
					if ($less_position<1) {
                        $less_position=strpos($to_list[$i],"&lt;");
                    }
					//return $less_position;
					//if((count($name[1])==1)&&((strpos($to_list[$i],"<")!=0)||(strpos($to_list[$i],"&lt;")!=0)))
					if ($less_position>0) {
                        $ids.=$name[1].",";
                    } else {
                        $ids.=$to_list[$i].",";
                    }
				}
            }
            $cc= htmlspecialchars(trim((string) $row[1]));
            $len=strlen($cc);
            if ($cc[($len-1)]==",") {
                $cc=substr($cc,0,-1);
            }
            $cc_list=explode(",",$cc);
            if ($cc !== "") {
                $counter = count($cc_list);
                for($i=0;$i<$counter;$i++)
				{
					//echo count($cc_list)."cc";
					$cc_list[$i]=trim($cc_list[$i]);
					if (str_starts_with($cc_list[$i], "&nbsp;")) {
                        $cc_list[$i]=str_replace("&nbsp;","",$cc_list[$i]);
                    }
					preg_match('/(.+?)<(.+?)>/i',$cc_list[$i],$name);
					if (count($name[1])==0) {
                        preg_match('/(.+?)&lt;(.+?)&gt;/i',$cc_list[$i],$name);
                    }
					if (count($name[1])==1) {
                        $less_position=strpos($cc_list[$i],"<");
                    }
					if ($less_position<1) {
                        $less_position=strpos($cc_list[$i],"&lt;");
                    }
					//return $less_position;
					//if((count($name[1])==1)&&((strpos($cc_list[$i],"<")!=0)||(strpos($cc_list[$i],"&lt;")!=0)))
					if ($less_position>0) {
                        $ids.=$name[1].",";
                    } else {
                        $ids.=$cc_list[$i].",";
                    }
				}
            }
            $bcc= htmlspecialchars(trim((string) $row[2]));
            $len=strlen($bcc);
            if ($bcc[($len-1)]==",") {
                $bcc=substr($bcc,0,-1);
            }
            $bcc_list=explode(",",$bcc);
            if ($bcc !== "") {
                $counter = count($bcc_list);
                for($i=0;$i<$counter;$i++)
				{
					//echo count($bcc_list)."bcc";
					$bcc_list[$i]=trim($bcc_list[$i]);
					if (str_starts_with($bcc_list[$i], "&nbsp;")) {
                        $bcc_list[$i]=str_replace("&nbsp;","",$bcc_list[$i]);
                    }
					preg_match('/(.+?)<(.+?)>/i',$bcc_list[$i],$name);
					if (count($name[1])==0) {
                        preg_match('/(.+?)&lt;(.+?)&gt;/i',$bcc_list[$i],$name);
                    }
					if (count($name[1])==1) {
                        $less_position=strpos($bcc_list[$i],"<");
                    }
					if ($less_position<1) {
                        $less_position=strpos($bcc_list[$i],"&lt;");
                    }
					//return $less_position;
					//if((count($name[1])==1)&&((strpos($bcc_list[$i],"<")!=0)||(strpos($bcc_list[$i],"&lt;")!=0)))
					if ($less_position>0) {
                        $ids.=$name[1].",";
                    } else {
                        $ids.=$bcc_list[$i].",";
                    }
				}
            }
            $ids=substr($ids,0,-1);
            if (strlen($ids)>20) {
                return substr($ids,0,20)."...".$num;
            } else {
                return $ids.$num;
            }
        }
        return null;
	}
function gettolistnew($mailid,$folderid)
	{
	if ($folderid==6) {
        return "";
    }
	    $username=$_COOKIE['e_username'];
	 $tablenumber=$this->tableid($username);
		$db=new NesoteDALController();
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
        } elseif ($folderid>=10) {
            $db->select("nesote_email_customfolder_mapping_$tablenumber");
        }
		$db->fields("mail_references");
		$db->where("id=?",[$mailid]);
		$rs=$db->query();
		$row=$db->fetchRow($rs);
		$references=$row[0];

		preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);
		
		$no=count($reply[1]);
			
			return $no > 1 ? "(".$no.")" : "";
	}

	function getfromlist($mailid,$folderid,$x)
	{
	$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		$uid=$this->getId();
		$db=new NesoteDALController();
		if ($folderid==1) {
            $db->select("nesote_email_inbox_$tablenumber");
        } elseif ($folderid==4) {
            $db->select("nesote_email_spam_$tablenumber");
        } elseif ($folderid==5) {
            $db->select("nesote_email_trash_$tablenumber");
        } elseif ($folderid>=10) {
            $db->select("nesote_email_customfolder_mapping_$tablenumber");
        }
		$db->fields("mail_references");
		$db->where("id=?",[$mailid]);
		$rs=$db->query();
		$row=$db->fetchRow($rs);
		$references=$row[0];


		$from= [];


		preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);
		//print_r($reply);
		$no=count($reply[1]);//return $no."?/////////////";
		$fromlist="";
		$w=0;
		for($i=0;$i<$no;$i++)
		{
			preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mail[$i]);//return $reply[1][$i]."+++++";
			preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folder[$i]);//return $folder[$i]."//".$mail[$i]."+++";
			$db=new NesoteDALController();
			if ($folder[$i][1]==5) {
                $db->select("nesote_email_trash_$tablenumber");
                $db->fields("backreference");
                $db->where("id=?",[$mail[$i][1]]);
                $rs1=$db->query();
                //return $db->getQuery()."++++++++++";
                $row1=$db->fetchRow($rs1);
                //return $row1[0];
                $folder_back=$row1[0];
                $db->select("nesote_email_trash_$tablenumber");
                if(($folder_back==2)||($folder_back==3))
				{
					$db->fields("to_list");
				}
				else {
                    $db->fields(" distinct from_list");
                }
                $db->where("id=?",[$mail[$i][1]]);
                $rs=$db->query();
            } elseif ($folder[$i][1]==4) {
                $db->select("nesote_email_spam_$tablenumber");
                $db->fields("backreference");
                $db->where("id=?",[$mail[$i][1]]);
                $rs1=$db->query();
                //return $db->getQuery()."++++++++++";
                $row1=$db->fetchRow($rs1);
                //return $row1[0];
                $folder_back=$row1[0];
                $db->select("nesote_email_spam_$tablenumber");
                if(($folder_back==2)||($folder_back==3))
				{
					$db->fields("to_list");
				}
				else {
                    $db->fields(" distinct from_list");
                }
                $db->where("id=?",[$mail[$i][1]]);
                $rs=$db->query();
            } elseif (($folder[$i][1]!=2)&&($folder[$i][1]!=3)) {
                if ($folder[$i][1]==1) {
                    $db->select("nesote_email_inbox_$tablenumber");
                } elseif ($folder[$i][1]>=10) {
                    $db->select("nesote_email_customfolder_mapping_$tablenumber");
                }
                $db->fields(" distinct from_list");
                $db->where("id=?",[$mail[$i][1]]);
                $rs=$db->query();
                //return $db->getQuery()."++++++++++";
            } else
			{   if ($folder[$i][1]==2) {
                $db->select("nesote_email_draft_$tablenumber");
            } elseif ($folder[$i][1]==3) {
                $db->select("nesote_email_sent_$tablenumber");
            }
			$db->fields("to_list");
			$db->where("id=?",[$mail[$i][1]]);
			$rs=$db->query();//echo $db->getQuery();
			}

			$row=$db->fetchRow($rs);
			
			$flag0=0;
			$len=strpos(trim((string) $row[0]),"<");//echo $len."++++++==";
			if($len<1)
			{
				$len=strpos(trim((string) $row[0]),"&lt");//echo $len."====";
			}
			if($len<1)
			{
				$len=strpos(trim((string) $row[0]),"&amp;lt");//
			}//return $len."*******";
			if($len>0)
			{
				//return "xxxxxxx";
				$froms=substr((string) $row[0],0,($len-1));
			}
			else
			{ //return "aaaaaa";
				preg_match('/<(.+?)>/i',(string) $row[0],$fromz);
				if(count($fromz)==0)
				{
					preg_match('/&lt;(.+?)&gt;/i',(string) $row[0],$fromz);
				}
				if(count($fromz)==0)
				{
					preg_match('/&amp;lt;(.+?)&amp;gt;/i',(string) $row[0],$fromz);
				}
				$frm = count($fromz) != 0 ? $fromz[1] : $row[0]; //return $frm."++++++++";
				$db1=new NesoteDALController();
				$db1->select("nesote_email_contacts");
				$db1->fields("firstname");
				$db1->where("mailid=? and addedby=?",[$frm,$uid]);
				$res=$db1->query();//echo $db1->getQuery();
				$nums=$db1->numRows($res);
				$rw1=$db1->fetchRow($res);
				if(($nums!=0)&&($rw1[0]!=""))
				{
					$froms=$rw1[0];//return $froms."+++++++".$nums;
				}
				else
				{
					$db2=new NesoteDALController();
					$db2->select("nesote_email_settings");
					$db2->fields("value");
					$db2->where("name=?",["globaladdress_book"]);
					$res2=$db2->query();
					$rw2=$db2->fetchRow($res2);//echo $rw2[0]."+++";
					$a=explode("@",(string) $frm);//echo $a[1]."***";
					$db3=new NesoteDALController();
					$db3->select("nesote_email_settings");
					$db3->fields("value");
					$db3->where("name=?",["emailextension"]);
					$res3=$db3->query();
					$rw3=$db3->fetchRow($res3);//echo $rw3[0]."@@@";
					if(($a[1]==$rw3[0])&&($rw2[0]==1))
					{
						$db4=new NesoteDALController();
						$db4->select("nesote_liberyus_users");
						$db4->fields("name");
						$db4->where("username=?",[$a[0]]);
						$res4=$db4->query();
						$rw4=$db4->fetchRow($res4);
						$froms=$rw4[0];//return "++++global".$froms;
					}
					else
					{
						$froms=$frm;
						//return "++++normal".$froms;
					}
				}
			}
            //return $froms;
            $counter = count($from);

			//return $froms;
			for($j=0;$j<$counter;$j++)
			{

				if($froms==$from[$j])
				{

					$flag0=1;
					continue;

				}
			}
			if($flag0==0)
			{
				//if()

				if(trim($folder[$i][1])==3)
				{

					$fromlist.=$this->getmessage(284).",";
				}
				else {
                    $fromlist.=$froms.",";
                }
			}
			$from[$w]=$froms;
			$w++;
		}

		$fromlist=substr($fromlist,0,-1);
		if(strlen($fromlist)>30)
		{
			$fromlist=substr($fromlist,0,30);
			$fromlist .= "....";
		}
		if($no>1)
		{
			$fromlist=$fromlist."(".$no.")";
		}


		return $fromlist;
	}
	function briefmessage($body,$length)
	{
		$body=strip_tags((string) $body);
		while(1)
		{
			$a=substr(trim($body),0,6);
			if($a === "&nbsp;")
			{
				$body=substr(trim($body),6);

			}
			else {
                break;
            }
		}
        $brief = strlen($body) > $length ? substr($body,0,$length)."..." : $body;
		return htmlentities(substr($brief,0,$length),0,"UTF-8");
	}

	function getshortdate($date)
	{
		$db= new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",\TIME_ZONE_POSTION);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$position=$row[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",\TIME_ZONE_HOUR);
		$result1=$db->query();
		$row1=$db->fetchRow($result1);
		$hour=$row1[0];

		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",\TIME_ZONE_MINT);
		$result2=$db->query();
		$row2=$db->fetchRow($result2);
		$min=$row2[0];

		$diff=((3600*$hour)+(60*$min));

		$diff = $position == "Behind" ? -$diff : $diff;

		$ts=time()-$date-$diff;
        $userid=$this->getId();
		$db->select("nesote_email_usersettings");
		$db->fields("time_zone");
		$db->where("userid=?",[$userid]);
		$res3=$db3->query();
		$rw3=$db3->fetchRow($res3);

		$db->select("nesote_email_time_zone");
		$db->fields("value");
		$db->where("id=?",[$rw3[0]]);
		$res3=$db->query();
		$row3=$db->fetchRow($res3);
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
			$lang_id=$_COOKIE['lang_mail'];
		}
		else
		{
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?",'default_language');
			$result=$db->query();
			$data4=$db->fetchRow($result);
			$lang_id=$data4[0];
			$defaultlang_id=$data4[0];
		}

		$day=date(" j ",$date);
        $language_id=$this->getlang_id($lang_id);
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$language_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);

		if($ts>2419200)
		{
			$data[0]=date("M",$date);
			$val = $data[0].date(" j,Y ",$date);
		}
		elseif($ts>86400)
		{
			$data[0]=date("M",$date);
			$val =$data[0]. $day;
			//$val=$data[0].$day." (".round($ts/86400,0).' '. $this->getmessage(55).')';
		}
		else
		{
			$val = ' '.$data[0].date("j",$date) ;
			if ($ts>3600) {
                $val = ' '.round($ts/3600,0).' '.$this->getmessage(56).'';
            } elseif ($ts>60) {
                $val = ' '.round($ts/60,0).' '.$this->getmessage(57).'';
            } else {
                $val = ' '.$ts.' '.$this->getmessage(58).'';
            }
		}
		return $val;

	}

	function getheading($folder,$search)
	{

		if ($search!="") {
            $heading=$this->getmessage(163);
            $heading=str_replace("{search}",$search,$heading);
            if ($folder==1) {
                $f=$this->getmessage(19);
                $heading=str_replace("{folder}",$f,$heading);
            } elseif ($folder==2) {
                $f=$this->getmessage(20);
                $heading=str_replace("{folder}",$f,$heading);
            } elseif ($folder==3) {
                $f=$this->getmessage(21);
                $heading=str_replace("{folder}",$f,$heading);
            } elseif ($folder==4) {
                $f=$this->getmessage(12);
                $heading=str_replace("{folder}",$f,$heading);
            } elseif ($folder==5) {
                $f=$this->getmessage(22);
                $heading=str_replace("{folder}",$f,$heading);
            } elseif ($folder==6) {
                $f=$this->getmessage(205);
                $heading=str_replace("{folder}",$f,$heading);
            } elseif ($folder==0) {
                $f=$this->getmessage(164);
                $heading=str_replace("{folder}",$f,$heading);
            } elseif ($folder>=10) {
                $db=new NesoteDALController();
                $db->select("nesote_email_customfolder");
                $db->fields("name");
                $db->where("id=?",$folder);
                $rs=$db->query();
                $row=$db->fetchRow($rs);
                $f=$row[0];
                $heading=str_replace("{folder}",$f,$heading);
            }
        } elseif ($folder>=10) {
            $db=new NesoteDALController();
            $db->select("nesote_email_customfolder");
            $db->fields("name");
            $db->where("id=?",$folder);
            $rs=$db->query();
            $row=$db->fetchRow($rs);
            $heading=$row[0];
        } elseif ($folder==1) {
            $heading=$this->getmessage(19);
        } elseif ($folder==2) {
            $heading=$this->getmessage(20);
        } elseif ($folder==3) {
            $heading=$this->getmessage(21);
        } elseif ($folder==4) {
            $heading=$this->getmessage(12);
        } elseif ($folder==5) {
            $heading=$this->getmessage(22);
        } elseif ($folder==6) {
            $heading=$this->getmessage(205);
        } elseif ($folder==7) {
            $heading=$this->getmessage(360);
        } else
		{
			$heading="";
		}
		return $heading;
	}

	function detailmailAction()
	{

		$valid=$this->validateUser();
		$copy=[];$copy1=[];
		$userid=$this->getId();
		$subject="";
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
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name='public_page_logo'");
			$result1=$db->query();
			$row1=$db->fetchRow($result1);
			$img=$row1[0];
			$imgpath="../admin/logo/".$img;
			$this->setValue("imgpath",$imgpath);

			$db->select("nesote_email_usersettings");
			$db->fields("signature");
			$db->where("userid=?",$userid);
			$res=$db->query();
			$row=$db->fetchRow($res);
			$this->setValue("sign",$row[0]);

				
			$db->select("nesote_email_usersettings");
			$db->fields("display");
			$db->where("userid=?",$userid);
			$rs1=$db->query();
			$row1=$db->fetchRow($rs1);
			$this->setValue("display",$row1[0]);
			$external_content_display=0;

				
			$select=new NesoteDALController();
			$db1=new NesoteDALController();
			$folder=$this->getParam(1);//echo $folder;exit;
			$this->setValue("fid",$folder);
			$folderid=$_COOKIE['folderid'];
			if($folderid>=10)
			{

				$db->select("nesote_email_customfolder");
				$db->fields("name");
				$db->where("id=? and userid=?",[$folderid,$userid]);
				$rs1=$db->query();
				$row1=$db->fetchRow($rs1);
				$foldername=$row1[0];
			}
			$this->setValue("folderid",$folderid);
			$this->setValue("folderd",$folderid);
			$mailid=$this->getParam(2);
			$this->setValue("mailid",$mailid);
			$msgid=$this->getParam(3);

			$more=0;$pge="";

			if ($msgid===0) {
                $msgid="";
                $more=0;
            } elseif ($msgid==-1) {
                $msgid="";
                $more=1;
            } elseif (stristr(trim((string) $msgid),"p")!="") {
                $pge=str_replace("p","",$msgid);
                $msgid="";
            }
			$this->setValue("more",$more);
			$this->setValue("pge",$pge);


			$number=$this->getParam(4);$rdinfo=0;
			if($number=="read")
			{
				$rdinfo=1;
				$number="";
			}
			$from=$this->getParam(5);
			$to=$this->getParam(6);
			$db->select("nesote_email_attachments_$tablenumber");
			$db->fields("*");
			$db->where("mailid=? and folderid=? and attachment=?",[$mailid,$folder,1]);
			$res=$db->query();
			$num=$db->numRows($res);

			$this->setLoopValue("attach",$res->getResult());


			if(isset($msgid))
			{
				$p=base64_decode((string) $msgid);
				$msg=$this->getmessage($p);

				$this->setValue("msg",$msg);
			}
			else {
                $this->setValue("msg","");
            }

			if($mailid!=0)
			{
				if ($folder==1) {
                    $db->select("nesote_email_inbox_$tablenumber");
                    $db->fields("*");
                    $db->where("id=?",$mailid);
                    $res=$db->query();
                    $rw=$db->fetchRow($res);
                    $references=$rw[14];
                    $copy=$rw[3];
                    $copy1=$rw[4];
                    preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);
                    //print_r($reply);
                    $nom=count($reply[1]);
                    for($i=0;$i<$nom;$i++)
					{
						preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid1);
						preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid1);
						if ($folderid1[1]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderid1[1]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        }


						$db->fields("*");
						$db->where("id=?",$mailid1[1]);
						$res=$db->query();
						$rw=$db->fetchRow($res);

						$mail[$i][0]=$rw[0];
						$mail[$i][1]=$rw[1];
						$mail[$i][2]=$rw[2];
						$mail[$i][3]=$rw[3];
						$mail[$i][4]=$rw[4];
						$mail[$i][6]=$rw[6];
						preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
					//	if($cset1[2]!="")
					//	$rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
						$mail[$i][7]=$rw[7];
						$mail[$i][8]=$rw[8];
						$mail[$i][9]=$rw[9];
						$mail[$i][10]=$rw[10];
						$mail[$i][11]=$rw[11];
						$mail[$i][12]=$rw[12];
						$mail[$i][13]=$folderid1[1];
						$mail[$i][15]=$rw[13];
						$mail[$i][16]=$rw[14];
						$mail[$i][17]=$this->attachcount($folderid1[1],$rw[0]);
						$mail[$i][18]=$rw[15];
						$mail[$i][19]=0;

						if($rdinfo==0)
						{
							if ($folderid1[1]==1) {
                                $db->update("nesote_email_inbox_$tablenumber");
                            } elseif ($folderid1[1]==3) {
                                $db->update("nesote_email_sent_$tablenumber");
                            }
							$db->set("readflag=?",1);
							$db->where("id=?",$mailid1[1]);
							$res=$db->query();
						}
						$maild=$mail[$i][2];
						if (strpos((string) $maild,">")!="") {
                            preg_match('/<(.+?)>/i',(string) $maild,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,">")!="") {
                            preg_match('/<(.+?)>/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,"&lt;")!="") {
                            preg_match('/&lt;(.+?)&gt;/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,"&amp;lt;")!="") {
                            preg_match('/&amp;lt;(.+?)&amp;gt;/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } else {
                            $id=$maild;
                        }


						$subj=$mail[$i][6];
						$sub=explode(":",(string) $subj);
						$number1=count($sub);
						$subject=$sub[$number1-1];


						$this->setLoopValue("mail",$mail);

						$this->setValue("subject",$subj);
						$total=count($mail);
						$this->setValue("total",$total);
						if($rdinfo==0)
						{
							$db1->update("nesote_email_inbox_$tablenumber");
							$db1->set("readflag=?",1);
							$db1->where("id=?",$mailid);
							$res1=$db1->query();
						}

					}
                } elseif ($folder==2) {
                    $db->select("nesote_email_draft_$tablenumber");
                    $db->fields("*");
                    $db->where("id=?",$mailid);
                    $this->setValue("flag",0);
                    $res=$db->query();
                    $this->setLoopValue("mail",$res->getResult());
                } elseif($folder==3)
				{
					$db->select("nesote_email_sent_$tablenumber");
					$db->fields("*");
					$db->where("id=?",$mailid);
					$res=$db->query();
					$rw=$db->fetchRow($res);
					$references=$rw[14];
					$copy=$rw[3];
					$copy1=$rw[4];
					preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);

					$nom=count($reply[1]);

					for($i=0;$i<$nom;$i++)
					{
						preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid1);
						preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid1);
						if ($folderid1[1]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderid1[1]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folderid1[1]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",$mailid1[1]);
						$res=$db->query();
						$rw=$db->fetchRow($res);

						$mail[$i][0]=$rw[0];
						$mail[$i][1]=$rw[1];
						$mail[$i][2]=$rw[2];
						$mail[$i][3]=$rw[3];
						$mail[$i][4]=$rw[4];
						$mail[$i][6]=$rw[6];
						preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
					//	if($cset1[2]!="")
					//	$rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
						$mail[$i][7]=$rw[7];
						$mail[$i][8]=$rw[8];
						$mail[$i][9]=$rw[9];
						$mail[$i][10]=$rw[10];
						$mail[$i][11]=$rw[11];
						$mail[$i][12]=$rw[12];
						$mail[$i][13]=$folderid1[1];
						$mail[$i][15]=$rw[13];
						$mail[$i][16]=$rw[14];
						$mail[$i][17]=$this->attachcount($folderid1[1],$rw[0]);
						$mail[$i][18]=$rw[15];
						$mail[$i][19]=0;
						if($rdinfo==0)
						{
							if ($folderid1[1]==1) {
                                $db->update("nesote_email_inbox_$tablenumber");
                            } elseif ($folderid1[1]==3) {
                                $db->update("nesote_email_sent_$tablenumber");
                            } elseif ($folderid1[1]>=10) {
                                $db->update("nesote_email_customfolder_mapping_$tablenumber");
                            }
							$db->set("readflag=?",1);
							$db->where("id=?",$mailid1[1]);
							$res=$db->query();
						}
					}


					$subject=$mail[0][6];
						

					$this->setLoopValue("mail",$mail);

					$this->setValue("subject",$subject);
					$total=count($mail);
					$this->setValue("total",$total);
				} elseif($folder==4)
				{


					$db->select("nesote_email_spam_$tablenumber");
					$db->fields("*");
					$db->where("id=?",$mailid);
					$res=$db->query();
					$rw=$db->fetchRow($res);
					$references=$rw[14];
					$copy=$rw[3];
					$copy1=$rw[4];
					preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);

					$nom=count($reply[1]);



					for($i=0;$i<$nom;$i++)
					{
						preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid1);
						preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid1);
						if ($folderid1[1]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderid1[1]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folderid1[1]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } elseif ($folderid1[1]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        } elseif ($folderid1[1]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }

						$db->fields("*");
						$db->where("id=?",$mailid1[1]);
						$res=$db->query();
						$rw=$db->fetchRow($res);

						$mail[$i][0]=$rw[0];
						$mail[$i][1]=$rw[1];
						$mail[$i][2]=$rw[2];
						$mail[$i][3]=$rw[3];
						$mail[$i][4]=$rw[4];
						$mail[$i][6]=$rw[6];
						preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
					//	if($cset1[2]!="")
					//	$rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
						$mail[$i][7]=$rw[7];
						$mail[$i][8]=$rw[8];
						$mail[$i][9]=$rw[9];
						$mail[$i][10]=$rw[10];
						$mail[$i][11]=$rw[11];
						$mail[$i][12]=$rw[12];
						$mail[$i][13]=$folderid1[1];
						$mail[$i][15]=$rw[13];
						$mail[$i][16]=$rw[14];
						$mail[$i][17]=$this->attachcount($folderid1[1],$rw[0]);
						$mail[$i][18]=$rw[15];
						$mail[$i][19]=0;
						if($rdinfo==0)
						{
							$db->update("nesote_email_spam_$tablenumber");
							$db->set("readflag=?",1);
							$db->where("id=?",$mailid1[1]);
							$res=$db->query();
						}

						$maild=$mail[$i][2];
						if (strpos((string) $maild,">")!="") {
                            preg_match('/<(.+?)>/i',(string) $maild,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,">")!="") {
                            preg_match('/<(.+?)>/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,"&lt;")!="") {
                            preg_match('/&lt;(.+?)&gt;/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,"&amp;lt;")!="") {
                            preg_match('/&amp;lt;(.+?)&amp;gt;/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } else {
                            $id=$maild;
                        }
						if(strpos((string) $maild,">")!="")
						{
							preg_match('/<(.+?)>/i',(string) $maild,$new_mailid);
							$id=$new_mailid[1];
						}
						else {
                            $id=$maild;
                        }


						$subject=$mail[$i][6];
						$this->setValue("subject",$subject);
						$total=count($mail);
						$this->setValue("total",$total);
						$this->setLoopValue("mail",$mail);
						if($rdinfo==0)
						{
							$db1->update("nesote_email_spam_$tablenumber");
							$db1->set("readflag=?",[1]);
							$db1->where("id=?",$mailid);
							$res1=$db1->query();
						}


					}
				} elseif($folder==5)
				{
					$db->select("nesote_email_trash_$tablenumber");
					$db->fields("*");
					$db->where("id=?",$mailid);
					$res=$db->query();
					$rw=$db->fetchRow($res);

					$references=$rw[14];
					$copy=$rw[3];
					$copy1=$rw[4];
					preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);

					$nom=count($reply[1]);


					for($i=0;$i<$nom;$i++)
					{
						preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid1);
						preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid1);




						$db->select("nesote_email_trash_$tablenumber");

						$db->fields("*");
						$db->where("id=?",$mailid1[1]);
						$res=$db->query();
						$rw=$db->fetchRow($res);

						$mail[$i][0]=$rw[0];
						$mail[$i][1]=$rw[1];
						$mail[$i][2]=$rw[2];
						$mail[$i][3]=$rw[3];
						$mail[$i][4]=$rw[4];
						$mail[$i][6]=$rw[6];
						preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
					//	if($cset1[2]!="")
					//	$rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
						$mail[$i][7]=$rw[7];
						$mail[$i][8]=$rw[8];
						$mail[$i][9]=$rw[9];
						$mail[$i][10]=$rw[10];
						$mail[$i][11]=$rw[11];
						$mail[$i][12]=$rw[12];
						$mail[$i][13]=$folderid1[1];
						$mail[$i][15]=$rw[13];
						$mail[$i][16]=$rw[14];

						$mail[$i][17]=$this->attachcount($folderid1[1],$rw[0]);
						$mail[$i][18]=$rw[15];
						$mail[$i][19]=0;
						if($rdinfo==0)
						{
							$db->update("nesote_email_trash_$tablenumber");
							$db->set("readflag=?",1);
							$db->where("id=?",$mailid1[1]);
							$res=$db->query();
						}
					}


					$subject=$mail[0][6];
					$this->setValue("subject",$subject);
					$total=count($mail);

					$this->setValue("total",$total);

					$this->setLoopValue("mail",$mail);
					if($rdinfo==0)
					{
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("readflag=?",[1]);
						$db1->where("id=?",$mailid);
						$res1=$db1->query();
					}
				} else
				{
					$db->select("nesote_email_customfolder_mapping_$tablenumber");
					$db->fields("*");
					$db->where("id=?",$mailid);
					$res=$db->query();
					$rw=$db->fetchRow($res);
					$references=$rw[14];
					$copy=$rw[3];
					$copy1=$rw[4];
					preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);

					$nom=count($reply[1]);



					for($i=0;$i<$nom;$i++)
					{
						preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid1);
						preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid1);
						if ($folderid1[1]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folderid1[1]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folderid1[1]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } elseif ($folderid1[1]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        } elseif ($folderid1[1]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }

						$db->fields("*");
						$db->where("id=?",$mailid1[1]);
						$res=$db->query();
						$rw=$db->fetchRow($res);

						$mail[$i][0]=$rw[0];
						$mail[$i][1]=$rw[1];
						$mail[$i][2]=$rw[2];
						$mail[$i][3]=$rw[3];
						$mail[$i][4]=$rw[4];
						$mail[$i][6]=$rw[6];
						preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
				//		if($cset1[2]!="")
				//		$rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
						$mail[$i][7]=$rw[7];
						$mail[$i][8]=$rw[8];
						$mail[$i][9]=$rw[9];
						$mail[$i][10]=$rw[10];
						$mail[$i][11]=$rw[11];
						$mail[$i][12]=$rw[12];
						$mail[$i][13]=$folderid1[1];
						$mail[$i][15]=$rw[13];
						$mail[$i][16]=$rw[14];
						$mail[$i][17]=$this->attachcount($folderid1[1],$rw[0]);
						$mail[$i][18]=$rw[15];
						$mail[$i][19]=0;

						if($rdinfo==0)
						{
							if ($folderid1[1]==3) {
                                $db->update("nesote_email_sent_$tablenumber");
                            } elseif ($folderid1[1]>=10) {
                                $db->update("nesote_email_customfolder_mapping_$tablenumber");
                            }
							$db->set("readflag=?",1);
							$db->where("id=?",$mailid1[1]);
							$res=$db->query();
						}
						$maild=$mail[$i][2];
						if (strpos((string) $maild,">")!="") {
                            preg_match('/<(.+?)>/i',(string) $maild,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,">")!="") {
                            preg_match('/<(.+?)>/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,"&lt;")!="") {
                            preg_match('/&lt;(.+?)&gt;/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } elseif (strpos((string) $mailid,"&amp;lt;")!="") {
                            preg_match('/&amp;lt;(.+?)&amp;gt;/i',(string) $mailid,$new_mailid);
                            $id=$new_mailid[1];
                        } else {
                            $id=$maild;
                        }
						if(strpos((string) $maild,">")!="")
						{
							preg_match('/<(.+?)>/i',(string) $maild,$new_mailid);
							$id=$new_mailid[1];
						}
						else {
                            $id=$maild;
                        }
						$subject=$mail[$i][6];
						$this->setValue("subject",$subject);
						$total=count($mail);
						$this->setValue("total",$total);
						$this->setLoopValue("mail",$mail);
						if($rdinfo==0)
						{
							$db1->update("nesote_email_customfolder_mapping_$tablenumber");
							$db1->set("readflag=?",[1]);
							$db1->where("id=?",$mailid);
							$res1=$db1->query();
						}

					}
				}
			}
			$username=$_COOKIE['e_username'];

			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?",['emailextension']);
			$res=$db->query();
			$rs=$db->fetchRow($res);
			$extention=$rs[0];
            $fullid = str_starts_with((string) $extention, "@") ? $username.$extention : $username."@".$extention;
			$arry=$copy.",".$copy1;
			$array=explode(",",$arry);
			$arraycount=count($array);

			for($i=0;$i<$arraycount;$i++)
			{
				if($array[$i]==$fullid)
				{
					$array[$i]="";
				}
			}
			$length=count($array);
			foreach($array as $key => $value)
			{
				if($value === "" || $value === " " || is_null($value))
				{
					unset($array[$key]);
				}
			}
			for($j=0,$k=0;$j<$length;$j++,$k++)

			{
				if($array[$j]!="")
				{
					$testing[$k][0]=$array[$j];

				}
				else {
                    $k--;
                }



			}
			$this->setLoopValue("cc",$testing);

		}
	}

	function getstar($mailid,$folder)
	{

			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		//$uid=$this->getUserId();
		$db=new NesoteDALController();
		if ($folder==1) {
            $db->select("nesote_email_inbox_$tablenumber");
        } elseif ($folder==2) {
            $db->select("nesote_email_draft_$tablenumber");
        } elseif ($folder==3) {
            $db->select("nesote_email_sent_$tablenumber");
        } elseif ($folder==4) {
            $db->select("nesote_email_spam_$tablenumber");
        } elseif ($folder>=10) {
            $db->select("nesote_email_customfolder_mapping_$tablenumber");
        }
		$db->fields("starflag");
		$db->where("id=?",$mailid);
		$res=$db->query();
		$row=$db->fetchRow($res);
		if ($row[0]==0) {
            return "<a href=\"javascript:markstar($mailid,$folder)\"><img src=\"../images/greystar_sml.png\" border=\"0\" align=\"absmiddle\" /></a>";
        } else {
            return "<a href=\"javascript:unmarkstar($mailid,$folder)\"><img src=\"../images/fullstar_sml.png\" border=\"0\" align=\"absmiddle\" /></a>";
        }
	}
	function getdetaildate($date)
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

		$ts=time()-$date-$diff;
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
		$res3=$db->query();
		$row3=$db->fetchRow($res3);
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
			$lang_id=$_COOKIE['lang_mail'];
		}
		else
		{
			$select=new NesoteDALController();
			$select->select("nesote_email_settings");
			$select->fields("value");
			$select->where("name=?",'default_language');
			$result=$select->query();
			$data4=$select->fetchRow($result);
			$lang_id=$data4[0];
			$defaultlang_id=$data4[0];
		}
           
		$day=date(" j ",$date);
        $language_id=$this->getlang_id($lang_id);
		$db=new NesoteDALController();
		$db->select("nesote_email_months_messages");
		$db->fields("message");
		$db->where("month_id=? and lang_id=?",[$month_id,$language_id]);
		$result=$db->query();
		$data=$db->fetchRow($result);

		if($ts>2419200)
		{

			$val = $data[0].date(" j,Y ",$date);
		}
		elseif($ts>86400)
		{
			$val=$data[0].$day." (".round($ts/86400,0).' '. $this->getmessage(55).')';
		}
		else
		{
			$val = ' '.$data[0].date("j",$date) ;
			if ($ts>3600) {
                $val = ' '.round($ts/3600,0).' '.$this->getmessage(56).'';
            } elseif ($ts>60) {
                $val = ' '.round($ts/60,0).' '.$this->getmessage(57).'';
            } else {
                $val = ' '.$ts.' '.$this->getmessage(58).'';
            }
		}
		return $val;

	}
	function briefids($id1,$id2,$id3,$length)
	{//echo $id1."++++".$id2."++++";

		$leng=strlen((string) $id1);
		if ($id1[($leng-1)]==",") {
            $id1=substr((string) $id1,0,-1);
        }
		$body=$id1.",".$id2;
		$lengt=strlen($body);
		if ($body[($lengt-1)]==",") {
            $body=substr($body,0,-1);
        }
		if($id3!=0)
		{
			$body.=",".$id3;
		}
		while($body[0]==",")
		{
			$body=substr($body,1);

		}
		$len=strlen($body);
		while($body[$len-1]==",")
		{
			$body=substr($body,0,-1);
			$len--;
		}
		while($body[0]==",")
		{
			$body=substr($body,1);

		}

		$body=htmlspecialchars($body);
		//$body=htmlspecialchars($body,ENT_QUOTES,"UTF-8");
		$ids=explode(",",$body);//return $body."***********";
		$idz="";
        $counter = count($ids);

		for($a=0;$a<$counter;$a++)
		{//echo $ids[$a]."&&&&&&";
			//				while(1)
			//				{
			//				if(substr(trim($ids[$a]),0,6)=="&nbsp;")
			//				{
			//					$ids[$a]=substr(trim($ids[$a]),6);
			//				}
			//				else
			//				{
			//					break;
			//				}
			//				}
			//echo $ids[$a]."++++";
			$ids[$a]=str_replace("&amp;","&",$ids[$a]);//echo $ids[$a]."++++";
			$ids[$a]=str_replace("&nbsp;","a",$ids[$a]);//echo $ids[$a]."++++";

			//$ids[$a]=html_entity_decode($ids[$a]);
			//echo $ids[$a]."//////////";
			$aa=strpos(trim($ids[$a]),"<");//echo $aa."---";
			if ($aa=="") {
                $aa=strpos(trim($ids[$a]),"&lt;");
            }//echo $aa."---";
			//if(($aa=="FALSE")||($aa==""))
			//$aa=strpos(trim($ids[$a]),"&amp;lt;");
			//echo $aa."---";
			if($aa!="")
			{
				//echo $ids[$a]."+++".$aa;
				$idz.=substr(trim($ids[$a]),0,$aa).",";
				//echo $idz."@@@@@@@@@@@";
			}
			else
			{
				//preg_match('/<(.+?)>/i',$ids[$a],$name);
				//$idz.=$name[1].",";echo $idz."*************";
				$idz.=trim($ids[$a]).",";
				//echo $idz."*************";
			}
	}
	//echo $idz."+++++++";
	$idz=substr($idz,0,-1);
	//$body=strip_tags($body);
	$brief=substr($idz,0,$length);
	return $this->getmessage(31)." :".$brief;
}
function arrange_ids($to,$cc,$bcc,$time,$sub,$from)
{
	$from=htmlspecialchars((string) $from);
	$from=str_replace("&amp;","&",$from);
	$from=str_replace("&nbsp;"," ",$from);

	$to=htmlspecialchars((string) $to);
	$to=str_replace("&amp;","&",$to);
	$to=str_replace("&nbsp;"," ",$to);
	//$cc=htmlentities($cc);
	$len=strlen($to);
	if ($to[($len-1)]==",") {
        $to=substr($to,0,-1);
    }
	$ids=explode(",",$to);
	$no=count($ids);
	$string.="<div class=\"row\">";
	$string.="<div class=\"blackTxt\">".$this->getmessage(54).": </div>";
	$string.="<div class=\"row floatL\">";
	$string.="<div>".$from."</div>";

	$string.="</div>";
	$string.="</div><div class=\"clear\"></div>";

	$string.="<div class=\"row\">";
	$string.="<div class=\"blackTxt\">".$this->getmessage(31).": </div>";
	$string.="<div class=\"row floatL\">";

	if($to!="")
	{
		for($a=0;$a<$no;$a++)
		{
			while(1)
			{
				if(str_starts_with(trim($ids[$a]), "&nbsp;"))
				{
					$ids[$a]=substr(trim($ids[$a]),6);
				}
				else
				{
					break;
				}
			}

			$idz=$ids[$a];

			$string.="<div>".$idz."</div>";
		}
	}
	else {
        $string.="<div>".$idz."</div>";
    }

	$string.="</div>";
	$string.="</div><div class=\"clear\"></div>";

	$cc=htmlspecialchars((string) $cc);
	$cc=str_replace("&amp;","&",$cc);
	$cc=str_replace("&nbsp;"," ",$cc);
	$length=strlen($cc);
	if ($cc[($length-1)]==",") {
        $cc=substr($cc,0,-1);
    }
	$cclist=explode(",",$cc);
	$num=count($cclist);
	if ($cc=="") {
        $num=0;
    } else
	{
		$string.="<div class=\"row\">";
		$string.="<div class=\"blackTxt \">".$this->getmessage(32).": </div>";
		$string.="<div class=\"row floatL\">";
	}
	if($cc!="")
	{
		for($a=0;$a<$num;$a++)
		{
			$idz=$cclist[$a];

			$string.="<div>".$idz."</div>";
		}
		$string.="</div>";
		$string.="</div><div class=\"clear\"></div>";
	}

	if($bcc!=0)
	{
		$bcc=htmlspecialchars((string) $bcc);
		$bcc=str_replace("&amp;","&",$bcc);
		$bcc=str_replace("&nbsp;"," ",$bcc);
		$lngth=strlen($bcc);
		if ($bcc[($lngth-1)]==",") {
            $bcc=substr($bcc,0,-1);
        }
		$bcclist=explode(",",$bcc);
		$numb=count($bcclist);
		if ($bcc=="") {
            $numb=0;
        } else
		{
			$string.="<div class=\"row\">";
			$string.="<div class=\"blackTxt \">".$this->getmessage(33).":</div>";
			$string.="<div class=\"row floatL\">";
		}
		for($a=0;$a<$numb;$a++)
		{
			$idz=$bcclist[$a];

			$string.="<div>".$idz."</div>";

		}
		$string.="</div>";
		$string.="</div><div class=\"clear\"></div>";
	}

	$times=$this->timez($time);
	$string.="<div class=\"row\">";
	$string.="<div class=\"blackTxt \">".$this->getmessage(281).": </div>";

	$string.="<div class=\"row floatL\">";
	$string.=$times."";
	$string.="</div>";
	$string.="</div><div class=\"clear\"></div>";
	if($sub!="")
	{
		$string.="<div class=\"row\">";
		$string.="<span class=\"blackTxt \">".$this->getmessage(34).": </span>";

		$string.="<div class=\"row floatL\">";
		$string.=$sub."</div>";
		$string.="</div><div class=\"clear\"></div>";
	}
	return $string;
}

function getattachmentdetailsAction()
{
	$valid=$this->validateUser();

	if($valid!=TRUE)
	{
		header("Location:".$this->url("index/index"));
		exit(0);
	}

	
	$username=$_COOKIE['e_username'];
	$tablenumber=$this->tableid($username);
	$mailid=$this->getParam(2);
	$this->setValue("mailid",$mailid);
	$folderid=$this->getParam(1);
	$this->setValue("fid",$folderid);
	 $moreval=$this->getParam(3);
	$more=0;$ms="";

	if ($moreval===0) {
        $more=0;
    } elseif ($moreval==-1) {
        $more=1;
    } else
	{
	 $ms=base64_decode((string) $moreval);
	 $ms=$this->getmessage($ms);
	}
	$this->setValue("more",$more);
	$this->setValue("ms",$ms);

	$this->getId();

	$response="";
	$db= new NesoteDALController();
	$db->select("nesote_email_attachments_$tablenumber");
	$db->fields("name");
	$db->where("folderid=? and mailid=? and attachment=?",[$folderid,$mailid,1]);
	$result=$db->query();
	$num=$db->numRows($result);
	$this->setValue("num",$num);

	while($row=$db->fetchRow($result))
	{

		$filename=$row[0];

		$size=filesize("../attachments/$folderid/$tablenumber/$mailid/$filename");

		$check_string=substr((string) $filename, strrpos((string) $filename,'.')+1);
		$check_string=strtolower($check_string);
		$check_string=trim($check_string);
		$number=0;
		$selectz=new NesoteDALController();
		$selectz->select("nesote_email_settings");
		$selectz->fields("value");
		$selectz->where("name=?",'restricted_attachment_types');
		$resulta=$selectz->query();
		$dataz=$selectz->fetchRow($resulta);
		if($dataz[0]!="")
		{
			$r_img_formats=$dataz[0];
			$r_img_formats=str_replace(".","",$r_img_formats);
			$r_img_formats=strtolower($r_img_formats);
			$r_img_formats=trim($r_img_formats);
			$r_img_format=explode(",",$r_img_formats);
            $counter = count($r_img_format);

			for($b=0;$b<$counter;$b++)
			{
			    $ptr=strtolower($r_img_format[$b]);$ptr1=strtolower($check_string);
				if(($r_img_format[$b]==$check_string)||($ptr === $ptr1))
				{
					$number=1;
					break;
				}
			}
		}


		if($number==0)
		{
			$img_formats=$this->getimageformats();
			$img_format=explode(",",(string) $img_formats);

			$no=0;
            $counter = count($img_format);
			for($a=0;$a<$counter;$a++)
			{
			$ptr=strtolower($img_format[$a]);$ptr1=strtolower($check_string);
				if(($check_string==$img_format[$a])||($ptr === $ptr1))
				{
					$dimention=getimagesize("../attachments/$folderid/$tablenumber/$mailid/$filename");
					if($dimention[1]>100)
					{
						$new_height=100;
						$new_width=$dimention[0]/$dimention[1]*100;
					}
					else
					{
						$new_height=$dimention[1];
						$new_width=$dimention[0];
					}
					$var=strpos((string) $filename,"-");
					$namez = $var > 0 ? substr((string) $filename,($var+1)) : $filename;

					$new_height .= "px";
					$new_width .= "px";
					$url1=$this->url("mail/downloadattachment/$folderid/$mailid/$filename");
					$url2=$this->url("mail/showimage/$folderid/$mailid/$filename");

					$response.="<div class=\"row\" style=\"padding-left:2px;\"><span  class=\"attachedImage\"><img src=\"../attachments/$folderid/$tablenumber/$mailid/$filename\" height=\"$new_height\" width=\"$new_width\" ></span></div><div class=\"row\"><span class=\"attachments\"><a href=\"$url1\" >".$this->getmessage(101)."</a></span></div><br>";
					$no=1;

				}
			}

			if($no==0)
			{

				if($check_string === "qqq")
				{
					$filename=str_replace("qqq","exe",$filename);

				}
				$var=strpos((string) $filename,"-");
				$namez = $var > 0 ? substr((string) $filename,($var+1)) : $filename;
				$response.="<b>&nbsp;&nbsp;<img src='../images/files.png'></b><span class=\"attachments\"><a href=\"".$this->url("mail/downloadattachment/$folderid/$mailid/$filename")."\" border='0'>".$this->getmessage(101)."</a></span><br><br>";


			}


		}

	}

	$response.="<span class=\"attachments\"><a href=\"".$this->url("mail/detailmail/$folderid/$mailid")."\" border='0'>Back to message</a></span>";
	$this->setValue("response",$response);

	//return  $response;
}
function getattachment($mailid,$folderid)
{

$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
	$db= new NesoteDALController();
	$db->select("nesote_email_attachments_$tablenumber");
	$db->fields("name");
	$db->where("folderid=? and mailid=? and attachment=?",[$folderid,$mailid,1]);
	$result=$db->query();//return $db->getQuery();
	$num=$db->numRows($result);
	if($num>0)
	{
		$url1=$this->url("mail/getattachmentdetails/$folderid/$mailid");
		return "<div class=\"row\" style=\"padding-left:2px;\"><img  src=\"../images/filler.gif\" alt=\"\" border=\"0\" align=\"absmiddle\" class=\"iconsCornner attach-a\"><span class=\"attachments\" >&nbsp;&nbsp;$num&nbsp;<a href=\"$url1\" >".$this->getmessage(35)."</a></span></div>";
	}
	else {
        return "";
    }


}
function getimageformats()
{

	return "jpeg,jpg,png,gif,bmp,psd,thm,tif,yuv,3dm,pln";
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
				$path="../attachments/$folderid/$tablenumber/$mailid/$filename";		
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

$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
	$folderid=$this->getParam(1);
	$mailid=$this->getParam(2);
	$filename=$this->getParam(3);


	$path="../attachments/$folderid/$tablenumber/$mailid/$filename";
	$pathToServerFile=$path;
	$db= new NesoteDALController();
	$db->select("nesote_email_attachments_$tablenumber");
	$db->fields("type");
	$db->where("folderid=? and mailid=? and name=?",[$folderid,$mailid,$filename]);
	$result=$db->query();
	$row=$db->fetchRow($result);

	$var=strpos((string) $filename,"-");
    $namez = $var != "FALSE" ? substr((string) $filename,($var+1)) : $filename;

	header('Content-Type:'. $row[0].'; filename='.$namez);
	readfile($pathToServerFile);

	exit(0);

}
function timez1($time)
{
	$month_id = date("n",$time);
	if(isset ($_COOKIE['lang_mail']))
	{
		$lang_id=$_COOKIE['lang_mail'];
	}
	else
	{
		$select=new NesoteDALController();
		$select->select("nesote_email_settings");
		$select->fields("value");
		$select->where("name=?",'default_language');
		$result=$select->query();
		$data4=$select->fetchRow($result);
		$lang_id=$data4[0];
		$defaultlang_id=$data4[0];
	}

	date(" j ",$time);
    $language_id=$this->getlang_id($lang_id);
	$db=new NesoteDALController();
	$db->select("nesote_email_months_messages");
	$db->fields("message");
	$db->where("month_id=? and lang_id=?",[$month_id,$language_id]);
	$result=$db->query();
	$data=$db->fetchRow($result);
	return $data[0].date(" j,Y h:i:s A",$time);
}
function timez($time)
{ 
    $date=$time;
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
}
function replylinkAction()
{
	
	$valid=$this->validateUser();

	if($valid!=TRUE)
	{
		header("Location:".$this->url("index/index"));
		exit(0);
	}

	 $db=new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name='public_page_logo'");
			$result1=$db->query();
			$row1=$db->fetchRow($result1);
			$img=$row1[0];
			$imgpath="../admin/logo/".$img;
			$this->setValue("imgpath",$imgpath);
			
	$userid=$this->getId();

$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		
	$db->select("nesote_email_usersettings");
	$db->fields("signature,display");
	$db->where("userid=?",$userid);
	$res=$db->query();
	$row=$db->fetchRow($res);
	$this->setValue("sign",$row[0]);
	$this->setValue("display",$row[1]);

	$db=new NesoteDALController();
	new NesoteDALController();
	new NesoteDALController();
	$folder=$this->getParam(1);//echo $folder;exit;
	$mailid=$this->getParam(2);
	$this->setValue("mailid",$mailid);
	$msgid=$this->getParam(3);
	$number=$this->getParam(4);
	if ($folder=="newmail") {
        $this->setValue("newmail",1);
        $folder="";
        $mailid=0;
        $fldr=$this->getParam(2);
        $this->setValue("fldr",$fldr);
        $pge=$this->getParam(3);
        $this->setValue("pge",$pge);
        $action=$this->getParam(4);
        $msgid=$action;
        $this->setValue("act",$action);
        $crntfold=$this->getParam(5);
        $number=$crntfold;
        $this->setValue("crntfold",$crntfold);
        $crntid=$this->getParam(6);
        $this->setValue("crntid",$crntid);
        $ms=$this->getParam(7);
        $ms=base64_decode((string) $ms);
        $ms=$this->getmessage($ms);
        $this->setValue("ms",$ms);
        $this->setValue("to","");
    } elseif ($folder=="tomail") {
        $this->setValue("newmail",1);
        $folder="";
        $mailid=0;
        $fldr=$this->getParam(2);
        $this->setValue("fldr",$fldr);
        $pge=$this->getParam(3);
        $this->setValue("pge",$pge);
        $action=$this->getParam(4);
        $msgid=$action;
        $this->setValue("act",$action);
        $to=$this->getParam(5);
        $to=$this->getcnamenew($to);
        $number1=$to;
        //$to.="+";
        //$to=base64_decode($to);
        $this->setValue("to",$to);
        $this->setValue("crntfold",$number1);
        $contactid=$this->getParam(6);
        $this->setValue("contactid",$contactid);
        $this->setValue("crntid",$contactid);
        $this->setValue("ms","");
        $number=$this->getParam(7);
    } else
	{
		$this->setValue("newmail",0);

		$crntfold=$this->getParam(4);
		$this->setValue("crntfold",$crntfold);
		$crntid=$this->getParam(5);
		$this->setValue("crntid",$crntid);
		$ms=$this->getParam(6);
		$ms=base64_decode((string) $ms);$ms=$this->getmessage($ms);
		$this->setValue("ms",$ms);
	}


	$this->setValue("fid",$folder);
	$folderid=$_COOKIE['folderid'];
	if($folderid>=10)
	{
		$name=new NesoteDALController();
		$name->select("nesote_email_customfolder");
		$name->fields("name");
		$name->where("id=? and userid=?",[$folderid,$userid]);
		$rs1=$name->query();
		$row1=$name->fetchRow($rs1);
		$foldername=$row1[0];
	}
	$this->setValue("folderid",$folderid);
	$this->setValue("folderd",$folderid);


	if($msgid=="r" || $msgid=="ra" || $msgid=="f" || $msgid=="c" || $msgid=="t")
	{
		$this->setValue("action",$msgid);
		$msgid="";
	}
	else {
        $this->setValue("action","");
    }

	$more=0;

	if ($number==-2) {
        $number="";
        $more=0;
    } elseif ($number==-1) {
        $msgid="";
        $more=1;
    }
	$this->setValue("more",$more);
	//$from=$this->getParam(5);
	//$to=$this->getParam(6);
	$db->select("nesote_email_attachments_$tablenumber");
	$db->fields("*");
	$db->where("mailid=? and folderid=? and attachment=?",[$mailid,$folder,1]);
	$res=$db->query();
	$db->numRows($res);
	$this->setLoopValue("attach",$res->getResult());
	if(isset($msgid))
	{
		
			$msg=$this->getmessage($msgid);
			if($msgid=="221")
			{
				$msg=str_replace('{foldername}',$foldername,$msg);
			}
		}
		else
		{
			$msg="";
		}
		$this->setValue("msg",$msg);
		if($mailid!=0)
		{
			if ($folder==1) {
                $db->select("nesote_email_inbox_$tablenumber");
                $db->fields("*");
                $db->where("id=?",$mailid);
                $res=$db->query();
                $rw=$db->fetchRow($res);
                $copy=$rw[3];
                $copy1=$rw[4];
                $i=0;
                $mail[$i][0]=$rw[0];
                $mail[$i][1]=$rw[1];
                $mail[$i][2]=$rw[2];
                $mail[$i][3]=$rw[3];
                $mail[$i][4]=$rw[4];
                $mail[$i][6]=$rw[6];
                preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
                if ($cset1[2]!="") {
                    $rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                }
                $mail[$i][7]=$rw[7];
                $mail[$i][8]=$rw[8];
                $mail[$i][9]=$rw[9];
                $mail[$i][10]=$rw[10];
                $mail[$i][11]=$rw[11];
                $mail[$i][12]=$rw[12];
                $mail[$i][13]=$folder[1];
                $mail[$i][15]=$rw[13];
                $mail[$i][16]=$rw[14];
                $mail[$i][17]=$this->attachcount($folder[1],$rw[0]);
                $mail[$i][18]=$rw[15];
                $mail[$i][19]=0;
                $body=htmlentities($rw[7]);
                $subj=$mail[$i][6];
                $sub=explode(":",(string) $subj);
                $number1=count($sub);
                $subject=$sub[$number1-1];
                $mail[$i][14]=html_entity_decode($body);
                $this->setValue("flag",$external_content_display);
                $this->setLoopValue("mail",$mail);
                $this->setValue("subject",$subj);
                $total=count($mail);
                $this->setValue("total",$total);
            } elseif ($folder==2) {
                $db->select("nesote_email_draft_$tablenumber");
                $db->fields("*");
                $db->where("id=?",$mailid);
                $this->setValue("flag",0);
                $res=$db->query();
                $this->setLoopValue("mail",$res->getResult());
            } elseif($folder==3)
			{
				$db->select("nesote_email_sent_$tablenumber");
				$db->fields("*");
				$db->where("id=?",$mailid);
				$res=$db->query();
				$rw=$db->fetchRow($res);
				$copy=$rw[3];
				$copy1=$rw[4];


				$i=0;

				$mail[$i][0]=$rw[0];
				$mail[$i][1]=$rw[1];
				$mail[$i][2]=$rw[2];
				$mail[$i][3]=$rw[3];
				$mail[$i][4]=$rw[4];
				$mail[$i][6]=$rw[6];
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
						if ($cset1[2]!="") {
                            $rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail[$i][7]=$rw[7];
				$mail[$i][8]=$rw[8];
				$mail[$i][9]=$rw[9];
				$mail[$i][10]=$rw[10];
				$mail[$i][11]=$rw[11];
				$mail[$i][12]=$rw[12];
				$mail[$i][13]=$folder[1];
				$mail[$i][15]=$rw[13];
				$mail[$i][16]=$rw[14];
				$mail[$i][17]=$this->attachcount($folder[1],$rw[0]);
				$mail[$i][18]=$rw[15];
				$mail[$i][19]=0;
				$subject=$mail[0][6];
				$mail[0][14]=html_entity_decode($body);

				$this->setLoopValue("mail",$mail);

				$this->setValue("subject",$subject);
				$total=count($mail);
				$this->setValue("total",$total);
			} elseif($folder==4)
			{


				$db->select("nesote_email_spam_$tablenumber");
				$db->fields("*");
				$db->where("id=?",$mailid);
				$res=$db->query();
				$rw=$db->fetchRow($res);
				$copy=$rw[3];
				$copy1=$rw[4];$i=0;
				$mail[$i][0]=$rw[0];
				$mail[$i][1]=$rw[1];
				$mail[$i][2]=$rw[2];
				$mail[$i][3]=$rw[3];
				$mail[$i][4]=$rw[4];
				$mail[$i][6]=$rw[6];
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
						if ($cset1[2]!="") {
                            $rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail[$i][7]=$rw[7];
				$mail[$i][8]=$rw[8];
				$mail[$i][9]=$rw[9];
				$mail[$i][10]=$rw[10];
				$mail[$i][11]=$rw[11];
				$mail[$i][12]=$rw[12];
				$mail[$i][13]=$folder[1];
				$mail[$i][15]=$rw[13];
				$mail[$i][16]=$rw[14];
				$mail[$i][17]=$this->attachcount($folder[1],$rw[0]);
				$mail[$i][18]=$rw[15];
				$mail[$i][19]=0;



				$body=htmlentities($rw[7]);//echo $body;



				$subject=$mail[$i][6];
				$mail[$i][14]=htmlspecialchars_decode($body);
				$this->setValue("subject",$subject);
				$total=count($mail);
				$this->setValue("total",$total);
				$this->setValue("no",$noz);
				$this->setLoopValue("mail",$mail);

			} elseif($folder==5)
			{
				$db->select("nesote_email_trash_$tablenumber");
				$db->fields("*");
				$db->where("id=?",$mailid);
				$res=$db->query();
				$rw=$db->fetchRow($res);
				$copy=$rw[3];
				$copy1=$rw[4];
				$i=0;
				$mail[$i][0]=$rw[0];
				$mail[$i][1]=$rw[1];
				$mail[$i][2]=$rw[2];
				$mail[$i][3]=$rw[3];
				$mail[$i][4]=$rw[4];
				$mail[$i][6]=$rw[6];
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
						if ($cset1[2]!="") {
                            $rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail[$i][7]=$rw[7];
				$mail[$i][8]=$rw[8];
				$mail[$i][9]=$rw[9];
				$mail[$i][10]=$rw[10];
				$mail[$i][11]=$rw[11];
				$mail[$i][12]=$rw[12];
				$mail[$i][13]=$folder[1];
				$mail[$i][15]=$rw[13];
				$mail[$i][16]=$rw[14];
				$mail[$i][17]=$this->attachcount($folder[1],$rw[0]);
				$mail[$i][18]=$rw[15];
				$mail[$i][19]=0;

				$subject=$mail[0][6];
				$mail[0][14]=html_entity_decode($body);
				$this->setValue("subject",$subject);
				$total=count($mail);

				$this->setValue("total",$total);
			} else
			{
				$db->select("nesote_email_customfolder_mapping_$tablenumber");
				$db->fields("*");
				$db->where("id=?",$mailid);
				$res=$db->query();
				$rw=$db->fetchRow($res);
				$copy=$rw[3];
				$copy1=$rw[4];

				$i=0;
				$mail[$i][0]=$rw[0];
				$mail[$i][1]=$rw[1];
				$mail[$i][2]=$rw[2];
				$mail[$i][3]=$rw[3];
				$mail[$i][4]=$rw[4];
				$mail[$i][6]=$rw[6];
				$mail[$i][7]=$rw[7];
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $rw[7],$cset1);
						if ($cset1[2]!="") {
                            $rw[7]=str_replace("attachments/","../attachments/",$cset1[2]);
                        }
				$mail[$i][8]=$rw[8];
				$mail[$i][9]=$rw[9];
				$mail[$i][10]=$rw[10];
				$mail[$i][11]=$rw[11];
				$mail[$i][12]=$rw[12];
				$mail[$i][13]=$folder[1];
				$mail[$i][15]=$rw[13];
				$mail[$i][16]=$rw[14];
				$mail[$i][17]=$this->attachcount($folder[1],$rw[0]);
				$mail[$i][18]=$rw[15];
				$mail[$i][19]=0;
				$maild=$mail[$i][2];

				$body=htmlentities($rw[7]);

				$subject=$mail[$i][6];
				$mail[$i][14]=htmlspecialchars_decode($body);
				$this->setValue("subject",$subject);
				$total=count($mail);
				$this->setValue("total",$total);
				$this->setLoopValue("mail",$mail);
			}
		}
		$username=$_COOKIE['e_username'];
		$this->setValue("folderid",$folder);
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",['emailextension']);
		$res=$db->query();
		$rs=$db->fetchRow($res);
		$extention=$rs[0];
        $fullid = str_starts_with((string) $extention, "@") ? $username.$extention : $username."@".$extention;
		$arry=$copy.",".$copy1;
		$array=explode(",",$arry);
		$arraycount=count($array);

		for($i=0;$i<$arraycount;$i++)
		{
			if($array[$i]==$fullid)
			{
				$array[$i]="";
			}
		}
		$length=count($array);
		foreach($array as $key => $value)
		{
			if($value === "" || $value === " " || is_null($value))
			{
				unset($array[$key]);
			}
		}
		for($j=0,$k=0;$j<$length;$j++,$k++)

		{
			if($array[$j]!="")
			{
				$testing[$k][0]=$array[$j];

			}
			else {
                $k--;
            }



		}


	}

	function pre($x)
	{

		$db=new NesoteDALController();
		if ($x=="r" || $x=="ra") {
            $c="reply_sub_predecessor";
        } elseif ($x=="f") {
            $c="forward_sub_predecessor";
        }
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",[$c]);
		$res=$db->query();
		$row1=$db->fetchRow($res);
		echo $row1[0];


	}

	function tolist($to_list)
	{
		if ($to_list=="") {
            return "";
        }
		$address="";
		$this->getId();
		$username=$_COOKIE['e_username'];
		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",[\EMAILEXTENSION]);
		$res=$db->query();
		$rs=$db->fetchRow($res);
		$extention=$rs[0];

		$fullid = str_starts_with((string) $extention, "@") ? $username.$extention : $username."@".$extention;

		$to_list=trim((string) $to_list);

		$length=strlen($to_list);
		$a=$to_list[$length-1];
		if ($a === ",") {
            $to_list=substr($to_list,0,-1);
        }
		$to_list=trim($to_list);

		$addresses=explode(",",$to_list);
        $counter = count($addresses);
		for($i=0;$i<$counter;$i++)
		{
			$addresses[$i]=trim($addresses[$i]);
			$len[$i]=strlen($addresses[$i]);
			preg_match('/<(.+?)>/i',$addresses[$i],$adrs);
			if (count($adrs[1])==0) {
                preg_match('/&lt;(.+?)&gt;/i',$addresses[$i],$adrs);
            }
			if(($adrs[1]!=$fullid)&&($addresses[$i]!=$fullid))
			{
				$address.=$addresses[$i].",";
			}
		}
		if (trim($address) === ",") {
            $address="";
        }
		return $address;


	}
	function to($from,$to1)
	{
		//return $from."+++++++".$to;
		$tolist=explode(",",(string) $to1);//print_r($tolist);echo "----";
		$to2=$tolist[0];//echo $to."+++++++".$from."++";
		$this->getId();
		$username=$_COOKIE['e_username'];
		$db=new NesoteDALController();
		$db->select("nesote_email_settings");
		$db->fields("value");
		$db->where("name=?",[\EMAILEXTENSION]);
		$res=$db->query();
		$rs=$db->fetchRow($res);
		$extention=$rs[0];
		$fullid = str_starts_with((string) $extention, "@") ? $username.$extention : $username."@".$extention;
		$length=strpos((string) $from,"<");//echo $length."frm";
		if($length>1)
		{
			preg_match('/<(.+?)>/i',(string) $from,$fromarray);
			$from1=$fromarray[1];
		}
		else
		{
			$from1=$from;
		}
		$length1=strpos($to2,"<");//echo $length1."to";
		if($length1>1)
		{
			preg_match('/<(.+?)>/i',$to2,$toarray);
			$to=$toarray[1];
		}
		else
		{
			$to=$to2;
		}
		//echo $to."++++".$from1."+++";

		$to = $from1 == $fullid ? $to : $from;



		return $to=($to);
	}

	function replymailAction()
	{


			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		$folderid=$this->getParam(2);
		$mailid=$this->getParam(1);
		$action=$this->getParam(3);
		$crntfold=$this->getParam(4);
		$crntid=$this->getParam(5);


		$send=$_POST['send'];
		$draft=$_POST['draft'];
		$discard=$_POST['discard'];


		if (isset($send)) {
            $type=1;
        } elseif (isset($draft)) {
            $type=2;
        } elseif (isset($discard)) {
            $type=3;
        }

		$to=$_POST['to'];
		$to=str_replace("\"","",$to);
		$cc=$_POST['cc'];
		$cc=str_replace("\"","",$cc);
		$bcc=$_POST['bcc'];
		$bcc=str_replace("\"","",$bcc);

		if($to=="" && $cc=="" && $bcc=="")
		{
			$p=base64_encode(330);
			header("Location:".$this->url("mail/replylink/$mailid/$folderid/$action/$crntfold/$crntid/$p"));
			exit(0);
		}

		$db=new NesoteDALController();
	
		if ($folderid==1) {
            $db->select("nesote_email_inbox_$tablenumber");
        } elseif ($folderid==3) {
            $db->select("nesote_email_sent_$tablenumber");
        } elseif ($folderid==2) {
            $db->select("nesote_email_draft_$tablenumber");
        } elseif ($folderid==4) {
            $db->select("nesote_email_spam_$tablenumber");
        } elseif ($folderid>=10) {
            $db->select("nesote_email_customfolder_mapping_$tablenumber");
        }
		$db->fields("mail_references,message_id,body");
		$db->where("id=?", [$mailid]);
		$result=$db->query();
		$row=$db->fetchRow($result);
		$mail_reference=$row[0];
		$replyto=$row[1];
		
		$html=$_POST["newbody"];
		preg_match('/<img(.+?)src=(.+?)>/i',(string) $html,$cset1);
						if ($cset1[2]!="") {
                            $html=str_replace("../attachments/","attachments/",$cset1[2]);
                        }
			
			
		$html=htmlspecialchars($html);
		$subject=htmlspecialchars((string) $_POST["sub"]);
		
		
		if(isset($_POST["previousbody"]))
		{
			$html.="<br>".$row[2];
		}
		


		
		
		$username=$_COOKIE['e_username'];
		$id=$this->getId();

		$db->select("nesote_email_usersettings");
		$db->fields("signature,signatureflag");
		$db->where("userid=?",$id);
		$res=$db->query();
		$row=$db->fetchRow($res);
		if ($row[0]!="" && $row[1]==1) {
            $html.="<br>".$row[0];
        }
$html=str_replace("\n","<br>",$html);

		$magic=get_magic_quotes_gpc();
		if($magic==1)
		{
			$html=stripslashes($html);
			$to=stripslashes($to);
			$cc=stripslashes($cc);
			$bcc=stripslashes($bcc);
		}
		$html=htmlspecialchars_decode($html);
		$time=time();

		if ($type==1) {
            $ss=$this->smtp($to,$cc,$bcc,$subject,$html,$id,$mail_reference,$replyto,"",$folderid,$mailid);
            if(stristr(trim((string) $ss),"Invalid address")!="")
			{
				echo $ss;exit;
			}
            $this->saveLogs("Sent Mail",$username." has sent a mail");
            $p=base64_encode(173);
            header("Location:".$this->url("mail/detailmail/$folderid/$mailid/$p"));
            exit(0);
        } elseif ($type==2) {
            $fullid=$username.$this->getextension();
            $to=str_replace("&Acirc;","",$to);
            $to=str_replace("&lt;","<",$to);
            $to=str_replace("&gt;",">",$to);
            $cc=str_replace("&Acirc;","",$cc);
            $cc=str_replace("&lt;","<",$cc);
            $cc=str_replace("&gt;",">",$cc);
            $bcc=str_replace("&Acirc;","",$bcc);
            $bcc=str_replace("&lt;","<",$bcc);
            $bcc=str_replace("&gt;",">",$bcc);
            $time=$this->getusertime();
            $db->insert("nesote_email_draft_$tablenumber");
            $db->fields("userid,from_list,to_list,cc,bcc,subject,body,time,just_insert");
            $db->values([$id,$fullid,$to,$cc,$bcc,$subject,$html,$time,0]);
            $db->query();
            $last_id=$db->lastInsert();
            $var=time().$username.$last_id;
            $message_id="<".md5($var).$extention.">";
            $references="<references><item><mailid>$last_id</mailid><folderid>2</folderid></item></references>";
            $db->update("nesote_email_draft_$tablenumber");
            $db->set("message_id=?,mail_references=?",[$message_id,$references]);
            $db->where("id=?",$last_id);
            $res=$db->query();
            $this->saveLogs("Saved to Draft",$username." has saved a mail to draft");
            $p=base64_encode(449);
            header("Location:".$this->url("mail/detailmail/2/$last_id/$p"));
            exit(0);
        } elseif ($type === 3) {
            header("Location:".$this->url("mail/detailmail/$folderid/$mailid"));
            exit(0);
        }
	}

	function mailactionAction()
	{
		$spam=$_POST['spam'];
		$delete=$_POST['delete'];

		if (isset($spam)) {
            $type=1;
        } elseif (isset($delete)) {
            $type=2;
        }

		$folderid=$this->getParam(1);
		$page=$this->getParam(2);
		$perpagesize=$this->getParam(3);
		$mailpage=$this->getParam(4);
		$idstring="";

		for($i=0;$i<$perpagesize;$i++)
		{

			if($_POST['cb'.$i]!="")
			{
				$idstring.=$_POST['cb'.$i].",";
			}
		}

		$len=strlen($idstring);
		$ids = $idstring[$len-1] == "," ? substr($idstring,0,-1) : $idstring;
		$idz=explode(",",$ids);
		if($idz=="" || $len==0)
		{
			$p=base64_encode(304);
			header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
			exit(0);
		}


		if ($type==1) {
            $str=$this->makespam($folderid,$idstring,$page,$mailpage);
            echo $str;
            exit;
        } elseif ($type === 2) {
            $str=$this->delete($folderid,$idstring,$page,$mailpage);
            echo $str;
            exit;
        }




	}

	function delete($folderid,$idstring,$page,$mailpage)
	{

		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{
			$userid=$this->getId();
			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
			$len=strlen((string) $idstring);
			$ids = $idstring[$len-1] == "," ? substr((string) $idstring,0,-1) : $idstring;
			$idz=explode(",",(string) $ids);
			$number=count($idz);
			$db=new NesoteDALController();
			$db1=new NesoteDALController();
			$db2=new NesoteDALController();
			if ($folderid==1) {
                $db->select("nesote_email_inbox_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                $groups="";
                while($row=$db->fetchRow($ros))
				{

					$groups=$this->getgroups($row[0]);
					$d="";
					$mailids="";

					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);

						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        }

						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						while($rw=$db->fetchRow($rs))
						{
							$d.=$a.",";

							$db1->insert("nesote_email_trash_$tablenumber");
							$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
							$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$folder[$a]]);
							$db1->query();
							$maild=$db1->lastInsert();
							$body=$rw[7];
							$body=str_replace("attachments/".$folder[$a]."/".$tablenumber."/".$mailid[$a],"attachments/5/".$tablenumber."/".$maild,$body);
							$db1->update("nesote_email_trash_$tablenumber");
							$db1->set("body=?",[$body]);
							$db1->where("id=?",$maild);
							$rs1=$db1->query();
							$mailids.=$maild.",";
							$this->saveLogs("Deleted Mail",$username." has deleted a mail to trash");

							$db2->select("nesote_email_attachments_$tablenumber");
							$db2->fields("*");
							$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
							$rs2=$db2->query();
							while($row2=$db2->fetchRow($rs2))
							{

								$db->update("nesote_email_attachments_$tablenumber");
								$db->set("mailid=?,folderid=?",[$maild,5]);
								$db->where("id=?",$row2[0]);
								$res=$db->query();
								if((is_dir("../attachments/5/".$maild))!=TRUE)
								{
									mkdir("../attachments/5/".$maild,0777);

								}
								copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/5/".$tablenumber."/".$maild."/".$row2[2]);
								unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
								rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
							}



							$e=explode(",",$d);
							if($e[0]==$a)
							{

								$references=$this->new_reference($row[0],$folder[$a],5,$mailid[$a],$maild);
							}
							else
							{

								$references=$this->new_reference($references,$folder[$a],5,$mailid[$a],$maild);
							}

						}

						if ($folder[$a]==1) {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->delete("nesote_email_sent");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
					$ides=explode(",",$mailids);
					$num=count($ides);
					for($b=0;$b<$num;$b++)
					{

						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("mail_references=?",$references);
						$db1->where("id=?",[$ides[$b]]);
						$db1->query();
					}
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_inbox_$tablenumber");
                $db->fields("id");
                $db->where("userid=? and id<?",[$userid,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{

					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}

				}
				else
				{

					$db->select("nesote_email_inbox_$tablenumber");
					$db->fields("id");
					$db->where("userid=? and id>?",[$userid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
            } elseif ($folderid==2) {
                $db->select("nesote_email_draft_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                $groups="";
                while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);
					$d="";
					$mailids="";
					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);

						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==2) {
                            $db->select("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";
						$db1->insert("nesote_email_trash_$tablenumber");
						$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
						$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$folder[$a]]);
						$db1->query();
						$maild=$db1->lastInsert();
						$body=$rw[7];
						$body=str_replace("attachments/".$folder[$a]."/".$tablenumber."/".$mailid[$a],"attachments/5/".$tablenumber."/".$maild,$body);
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("body=?",[$body]);
						$db1->where("id=?",$maild);
						$rs1=$db1->query();
						$mailids.=$maild.",";
						$this->saveLogs("Deleted Mail",$username." has deleted a mail to trash");

						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->update("nesote_email_attachments_$tablenumber");
							$db->set("mailid=?,folderid=?",[$maild,5]);
							$db->where("id=?",$row2[0]);
							$res=$db->query();
							if((is_dir("../attachments/5/".$tablenumber."/".$maild))!=TRUE)
							{
								mkdir("../attachments/5/".$tablenumber."/".$maild,0777);

							}
							copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/5/".$tablenumber."/".$maild."/".$row2[2]);
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}



						$e=explode(",",$d);
						if ($e[0]==$a) {
                            $references=$this->new_reference($row[0],$folder[$a],5,$mailid[$a],$maild);
                        }
						$references=$this->new_reference($references,$folder[$a],5,$mailid[$a],$maild);
						if ($folder[$a]==2) {
                            $db->delete("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==1) {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->delete("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->delete("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->delete("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
					$ides=explode(",",$mailids);
					$num=count($ides);
					for($b=0;$b<$num;$b++)
					{
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("mail_references=?",$references);
						$db1->where("id=?",[$ides[$b]]);
						$db1->query();
					}
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_draft_$tablenumber");
                $db->fields("id");
                $db->where("userid=? and just_insert=? and id<?",[$userid,0,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
				else
				{

					$db->select("nesote_email_draft_$tablenumber");
					$db->fields("id");
					$db->where("userid=? and just_insert=? and id>?",[$userid,0,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
            } elseif ($folderid==3) {
                $db->select("nesote_email_sent_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                $groups="";
                while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);
					$d="";
					$mailids="";
					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);
						//					echo $ides;exit;
						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->select("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";
						$db1->insert("nesote_email_trash_$tablenumber");
						$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
						$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$folder[$a]]);
						$db1->query();
						$maild=$db1->lastInsert();
						$body=$rw[7];
						$body=str_replace("attachments/".$folder[$a]."/".$tablenumber."/".$mailid[$a],"attachments/5/".$tablenumber."/".$maild,$body);
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("body=?",[$body]);
						$db1->where("id=?",$maild);
						$rs1=$db1->query();
						$mailids.=$maild.",";
						$this->saveLogs("Deleted Mail",$username." has deleted a mail to trash");

						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->update("nesote_email_attachments_$tablenumber");
							$db->set("mailid=?,folderid=?",[$maild,5]);
							$db->where("id=?",$row2[0]);
							$res=$db->query();
							if((is_dir("../attachments/5/".$tablenumber."/".$maild))!=TRUE)
							{
								if((is_dir("../attachments/5/".$tablenumber))!=TRUE)
							    {	
							    	
							    if ((is_dir("../attachments/5/"))!=TRUE) {
                                    mkdir("../attachments/5/",0777);
                                }
				    
							    mkdir("../attachments/5/".$tablenumber,0777);
							    }
								mkdir("../attachments/5/".$tablenumber."/".$maild,0777);

							}
							copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/5/".$tablenumber."/".$maild."/".$row2[2]);
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}



						$e=explode(",",$d);
						if ($e[0]==$a) {
                            $references=$this->new_reference($row[0],$folder[$a],5,$mailid[$a],$maild);
                        }
						$references=$this->new_reference($references,$folder[$a],5,$mailid[$a],$maild);
						if ($folder[$a]==3) {
                            $db->delete("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==1) {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->delete("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->delete("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->delete("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
					$ides=explode(",",$mailids);
					$num=count($ides);
					for($b=0;$b<$num;$b++)
					{
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("mail_references=?",$references);
						$db1->where("id=?",[$ides[$b]]);
						$db1->query();
					}
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_sent_$tablenumber");
                $db->fields("id");
                $db->where("userid=? and id<?",[$userid,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
				else
				{

					$db->select("nesote_email_sent_$tablenumber");
					$db->fields("id");
					$db->where("userid=? and id>?",[$userid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
            } elseif ($folderid==4) {
                $db->select("nesote_email_spam_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                $groups="";
                while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);
					$d="";
					$mailids="";
					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);
						//					echo $ides;exit;
						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        }
						//
						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";
						$db1->insert("nesote_email_trash_$tablenumber");
						$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
						$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$rw[15]]);
						$db1->query();
						$maild=$db1->lastInsert();
						$body=$rw[7];
						$body=str_replace("attachments/".$folder[$a]."/".$mailid[$a],"attachments/5/".$maild,$body);
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("body=?",[$body]);
						$db1->where("id=?",$maild);
						$rs1=$db1->query();
						$mailids.=$maild.",";
						$this->saveLogs("Deleted Mail",$username." has deleted a mail to trash");

						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->update("nesote_email_attachments_$tablenumber");
							$db->set("mailid=?,folderid=?",[$maild,5]);
							$db->where("id=?",$row[0]);
							$res=$db->query();
							if((is_dir("../attachments/5/".$tablenumber."/".$maild))!=TRUE)
							{
								if((is_dir("../attachments/5/".$tablenumber))!=TRUE)
							    {
							    	if((is_dir("../attachments/5/"))!=TRUE)
							        {
							         mkdir("../attachments/5/",0777);
							        }
							        mkdir("../attachments/5/".$tablenumber,0777);
							    	
							    }
							
								
								mkdir("../attachments/5/".$tablenumber."/".$maild,0777);

							}
							copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/5/".$tablenumber."/".$maild."/".$row2[2]);
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}




						$e=explode(",",$d);
						if ($e[0]==$a) {
                            $references=$this->new_reference($row[0],$folder[$a],5,$mailid[$a],$maild);
                        }
						$references=$this->new_reference($references,$folder[$a],5,$mailid[$a],$maild);
						if ($folder[$a]==4) {
                            $db->delete("nesote_email_spam_$tablenumber");
                        }
						//
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
					$ides=explode(",",$mailids);
					$num=count($ides);
					for($b=0;$b<$num;$b++)
					{
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("mail_references=?",$references);
						$db1->where("id=?",[$ides[$b]]);
						$db1->query();
					}
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_spam_$tablenumber");
                $db->fields("id");
                $db->where("userid=? and id<?",[$userid,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
				else
				{

					$db->select("nesote_email_spam_$tablenumber");
					$db->fields("id");
					$db->where("userid=? and id>?",[$userid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
            } elseif ($folderid==5) {
                $db->select("nesote_email_trash_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                $groups="";
                while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);

					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);
						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        }

						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";
						$this->saveLogs("Deleted Mail",$username." has deleted a mail permenantly");

						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->delete("nesote_email_attachments_$tablenumber");
							$db->where("id=?",$row2[0]);
							$res=$db->query();
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}
						if ($folder[$a]==5) {
                            $db->delete("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->delete("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->delete("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->delete("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==1) {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_trash_$tablenumber");
                $db->fields("id");
                $db->where("userid=? and id<?",[$userid,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{
					if($number==1)
					{
						$p=base64_encode(182);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("181@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}


				}
				else
				{

					$db->select("nesote_email_trash_$tablenumber");
					$db->fields("id");
					$db->where("userid=? and id>?",[$userid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(182);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("181@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
            } elseif ($folderid>=10) {
                $db->select("nesote_email_customfolder_mapping_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                $groups="";
                while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);
					$d="";
					$mailids="";
					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);

						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";

						$db1->insert("nesote_email_trash_$tablenumber");
						$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
						$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$folder[$a]]);
						$db1->query();
						$maild=$db1->lastInsert();
						$body=$rw[7];
						$body=str_replace("attachments/".$folder[$a]."/".$mailid[$a],"attachments/5/".$maild,$body);
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("body=?",[$body]);
						$db1->where("id=?",$maild);
						$rs1=$db1->query();
						$mailids.=$maild.",";
						$this->saveLogs("Deleted Mail",$username." has deleted a mail to trash");

						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->update("nesote_email_attachments_$tablenumber");
							$db->set("mailid=?,folderid=?",[$maild,5]);
							$db->where("id=?",$row2[0]);
							$res=$db->query();
							if((is_dir("../attachments/5/".$tablenumber."/".$maild))!=TRUE)
							{
								if((is_dir("../attachments/5/".$tablenumber))!=TRUE)
							    {
							    	if((is_dir("../attachments/5/"))!=TRUE)
							        {
							        mkdir("../attachments/5/",0777);
							        }
							        mkdir("../attachments/5/".$tablenumber,0777);
							    }
								
								mkdir("../attachments/5/".$tablenumber."/".$maild,0777);

							}
							copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/5/".$tablenumber."/".$maild."/".$row2[2]);
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}




						$e=explode(",",$d);
						if ($e[0]==$a) {
                            $references=$this->new_reference($row[0],$folder[$a],5,$mailid[$a],$maild);
                        }
						$references=$this->new_reference($references,$folder[$a],5,$mailid[$a],$maild);
						if ($folder[$a]>=10) {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->delete("nesote_email_sent_$tablenumber");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
					$ides=explode(",",$mailids);
					$num=count($ides);
					for($b=0;$b<$num;$b++)
					{
						$db1->update("nesote_email_trash_$tablenumber");
						$db1->set("mail_references=?",$references);
						$db1->where("id=?",[$ides[$b]]);
						$db1->query();
					}
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_customfolder_mapping_$tablenumber");
                $db->fields("id");
                $db->where("folderid=? and id<?",[$folderid,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
				else
				{

					$db->select("nesote_email_customfolder_mapping_$tablenumber");
					$db->fields("id");
					$db->where("folderid=? and id>?",[$folderid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(174);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("177@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
            }
		}
	}


	function spamAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$folderid=$this->getParam(1);$idstring=$this->getParam(2);$page=$this->getParam(3);
		if($idstring=="")
		{
			$p=base64_encode(304);
			header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
			exit(0);
		}

		$this->makespam($folderid,$idstring,$page,'');exit;

	}

	function maildeleteAction()
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		$folderid=$this->getParam(1);$idstring=$this->getParam(2);$page=$this->getParam(3);


		if($idstring=="")
		{
			$p=base64_encode(304);
			header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
			exit(0);
		}
		$this->delete($folderid,$idstring,$page,'');

	}
	function makespam($folderid,$idstring,$page,$mailpage)  //$mailpage 1 for shortmail 2 for detailmail
	{
		$valid=$this->validateUser();
		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
		else
		{

			$userid=$this->getId();
			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
			$len=strlen((string) $idstring);
			$ids = $idstring[$len-1] == "," ? substr((string) $idstring,0,-1) : $idstring;
			$idz=explode(",",(string) $ids);
			$number=count($idz);
			if ($folderid==1) {
                $db=new NesoteDALController();
                $db->select("nesote_email_inbox_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                //echo $db->getQuery();exit;
                $groups="";
                $c=0;
                while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);
					$d="";
					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);
						//					echo $ides;exit;
						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->select("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";
						$db1=new NesoteDALController();
						$db1->insert("nesote_email_spam_$tablenumber");
						$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
						$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$folder[$a]]);
						$db1->query();
						$maild=$db1->lastInsert();
						$body=$rw[7];
						$body=str_replace("attachments/".$folder[$a]."/".$tablenumber."/".$mailid[$a],"attachments/4/".$tablenumber."/".$maild,$body);
						$db1->update("nesote_email_spam_$tablenumber");
						$db1->set("body=?",[$body]);
						$db1->where("id=?",$maild);
						$rs1=$db1->query();
						$mailids.=$maild.",";
						$this->saveLogs("Marked as Spam",$username." has marked a mail as spam");
						$this->removewhitelist($rw[2],$rw[1]);
						$this->setblacklist($rw[2],$rw[1]);


						$db2=new NesoteDALController();
						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->update("nesote_email_attachments_$tablenumber");
							$db->set("mailid=?,folderid=?",[$maild,4]);
							$db->where("id=?",$row2[0]);
							$res=$db->query();
							if((is_dir("../attachments/4/".$tablenumber."/".$maild))!=TRUE)
							{
								
								if((is_dir("../attachments/4/".$tablenumber))!=TRUE)
							    {
							    	if((is_dir("../attachments/4/"))!=TRUE)
							        {
							        mkdir("../attachments/4/",0777);		
							        }
							        mkdir("../attachments/4/".$tablenumber,0777);
							    }
								mkdir("../attachments/4/".$tablenumber."/".$maild,0777);

							}
							copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/4/".$tablenumber."/".$maild."/".$row2[2]);
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}


						$e=explode(",",$d);

						if ($e[0]==$a) {
                            $references=$this->new_reference($row[0],$folder[$a],4,$mailid[$a],$maild);
                        } else {
                            $references=$this->new_reference($references,$folder[$a],4,$mailid[$a],$maild);
                        }
						$c++;
						if ($folder[$a]==1) {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->delete("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->delete("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->delete("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->delete("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
				}
                $ides=explode(",",$mailids);
                $num=count($ides);
                for($b=0;$b<$num;$b++)
				{
					$db1->update("nesote_email_spam_$tablenumber");
					$db1->set("mail_references=?",$references);
					$db1->where("id=?",[$ides[$b]]);
					$db1->query();
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_inbox_$tablenumber");
                $db->fields("id");
                $db->where("userid=? and id<?",[$userid,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{
					if($number==1)
					{
						$p=base64_encode(191);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("192@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
				else
				{

					$db->select("nesote_email_inbox_$tablenumber");
					$db->fields("id");
					$db->where("userid=? and id>?",[$userid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);

					if($number==1)
					{
						$p=base64_encode(191);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("192@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
                //return $url.",".$folderid.",".$row1[0];
                //exit;
            } elseif ($folderid==5) {
                $db=new NesoteDALController();
                $db->select("nesote_email_trash_$tablenumber");
                $db->fields("mail_references");
                $db->where("id in($ids)");
                $ros=$db->query();
                $groups="";
                while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);
					$d="";
					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);
						//					echo $ides;exit;
						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->select("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";
						$db1=new NesoteDALController();
						$db1->insert("nesote_email_spam_$tablenumber");
						$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
						$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$rw[15]]);
						$db1->query();
						$maild=$db1->lastInsert();
						$body=$rw[7];
						$body=str_replace("attachments/".$folder[$a]."/".$tablenumber."/".$mailid[$a],"attachments/4/".$tablenumber."/".$maild,$body);
						$db1->update("nesote_email_spam_$tablenumber");
						$db1->set("body=?",[$body]);
						$db1->where("id=?",$maild);
						$rs1=$db1->query();
						$mailids.=$maild.",";
						$this->saveLogs("Marked as Spam",$username." has marked a mail as spam");
						$this->removewhitelist($rw[2],$rw[1]);
						$this->setblacklist($rw[2],$rw[1]);


						$db2=new NesoteDALController();
						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->update("nesote_email_attachments_$tablenumber");
							$db->set("mailid=?,folderid=?",[$maild,4]);
							$db->where("id=?",$row2[0]);
							$res=$db->query();
							
						   if((is_dir("../attachments/4/".$tablenumber."/".$maild))!=TRUE)
							{
								
								if((is_dir("../attachments/4/".$tablenumber))!=TRUE)
							    {
							    	if((is_dir("../attachments/4/"))!=TRUE)
							        {
							        mkdir("../attachments/4/",0777);		
							        }
							        mkdir("../attachments/4/".$tablenumber,0777);
							    }
								mkdir("../attachments/4/".$tablenumber."/".$maild,0777);

							}
							copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/4/".$tablenumber."/".$maild."/".$row2[2]);
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}



						$e=explode(",",$d);
						if ($e[0]==$a) {
                            $references=$this->new_reference($row[0],$folder[$a],4,$mailid[$a],$maild);
                        } else {
                            $references=$this->new_reference($references,$folder[$a],4,$mailid[$a],$maild);
                        }
						if ($folder[$a]==1) {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->delete("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->delete("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->delete("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->delete("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
				}
                $ides=explode(",",$mailids);
                $num=count($ides);
                for($b=0;$b<$num;$b++)
				{
					$db1->update("nesote_email_spam_$tablenumber");
					$db1->set("mail_references=?",$references);
					$db1->where("id=?",[$ides[$b]]);
					$db1->query();
				}
                $array=explode(",",(string) $ids);
                $least=$array[0];
                $array_count=count($array);
                for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
                $db->select("nesote_email_trash_$tablenumber");
                $db->fields("id");
                $db->where("userid=? and id<?",[$userid,$least]);
                $db->order("id desc");
                $db->limit(0,1);
                $res=$db->query();
                $row1=$db->fetchRow($res);
                $no=$db->numRows($res);
                if ($no!=0)
				{

					if($number==1)
					{
						$p=base64_encode(191);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("192@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}



				}
				else
				{

					$db->select("nesote_email_trash_$tablenumber");
					$db->fields("id");
					$db->where("userid=? and id>?",[$userid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(191);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("192@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
                //return $url.",".$folderid.",".$row1[0];
                //exit;
            } elseif($folderid>=10)
			{
				$db=new NesoteDALController();
				$db->select("nesote_email_customfolder_mapping_$tablenumber");
				$db->fields("mail_references");
				$db->where("id in($ids)");
				$ros=$db->query();
				$groups="";
				while($row=$db->fetchRow($ros))
				{
					$groups=$this->getgroups($row[0]);
					$d="";
					$combination=explode(",",(string) $groups);
                    $counter = count($combination);
					for($a=0;$a<$counter;$a++)
					{
						$combo[$a]=explode(":",$combination[$a]);
						//					echo $ides;exit;
						$folder[$a]=$combo[$a][0];
						$mailid[$a]=$combo[$a][1];
						if ($folder[$a]==1) {
                            $db->select("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->select("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->select("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->select("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->select("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->select("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->fields("*");
						$db->where("id=?",[$mailid[$a]]);
						$rs=$db->query();
						$rw=$db->fetchRow($rs);
						$d.=$a.",";
						$db1=new NesoteDALController();
						$db1->insert("nesote_email_spam_$tablenumber");
						$db1->fields("userid,from_list,to_list,cc,bcc,subject,body,time,status,readflag,starflag,memorysize,message_id,backreference");
						$db1->values([$userid,$rw[2],$rw[3],$rw[4],$rw[5],$rw[6],$rw[7],$rw[8],$rw[9],$rw[10],$rw[11],$rw[12],$rw[13],$folder[$a]]);
						$db1->query();
						$maild=$db1->lastInsert();
						$body=$rw[7];
						$body=str_replace("attachments/".$folder[$a]."/".$tablenumber."/".$mailid[$a],"attachments/4/".$tablenumber."/".$maild,$body);
						$db1->update("nesote_email_spam_$tablenumber");
						$db1->set("body=?",[$body]);
						$db1->where("id=?",$maild);
						$rs1=$db1->query();
						$mailids.=$maild.",";
						$this->saveLogs("Marked as Spam",$username." has marked a mail as spam");
						$this->removewhitelist($rw[2],$rw[1]);
						$this->setblacklist($rw[2],$rw[1]);



						$db2=new NesoteDALController();
						$db2->select("nesote_email_attachments_$tablenumber");
						$db2->fields("*");
						$db2->where("mailid=?and folderid=?",[$mailid[$a],$folder[$a]]);
						$rs2=$db2->query();
						while($row2=$db2->fetchRow($rs2))
						{

							$db->update("nesote_email_attachments_$tablenumber");
							$db->set("mailid=?,folderid=?",[$maild,4]);
							$db->where("id=?",$row2[0]);
							$res=$db->query();
						    if((is_dir("../attachments/4/".$tablenumber."/".$maild))!=TRUE)
							{
								
								if((is_dir("../attachments/4/".$tablenumber))!=TRUE)
							    {
							    	if((is_dir("../attachments/4/"))!=TRUE)
							        {
							        mkdir("../attachments/4/",0777);		
							        }
							        mkdir("../attachments/4/".$tablenumber,0777);
							    }
								mkdir("../attachments/4/".$tablenumber."/".$maild,0777);

							}
							copy("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2],"../attachments/4/".$tablenumber."/".$maild."/".$row2[2]);
							unlink("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]."/".$row2[2]);
							rmdir("../attachments/".$folder[$a]."/".$tablenumber."/".$rw[0]);
						}



						$e=explode(",",$d);
						if ($e[0]==$a) {
                            $references=$this->new_reference($row[0],$folder[$a],4,$mailid[$a],$maild);
                        } else {
                            $references=$this->new_reference($references,$folder[$a],4,$mailid[$a],$maild);
                        }
						if ($folder[$a]==1) {
                            $db->delete("nesote_email_inbox_$tablenumber");
                        } elseif ($folder[$a]==2) {
                            $db->delete("nesote_email_draft_$tablenumber");
                        } elseif ($folder[$a]==3) {
                            $db->delete("nesote_email_sent_$tablenumber");
                        } elseif ($folder[$a]==4) {
                            $db->delete("nesote_email_spam_$tablenumber");
                        } elseif ($folder[$a]==5) {
                            $db->delete("nesote_email_trash_$tablenumber");
                        } elseif ($folder[$a]>=10) {
                            $db->delete("nesote_email_customfolder_mapping_$tablenumber");
                        }
						$db->where("id=?",[$mailid[$a]]);
						$db->query();
					}
					$ides=explode(",",$mailids);
					$num=count($ides);
					for($b=0;$b<$num;$b++)
					{
						$db1->update("nesote_email_spam_$tablenumber");
						$db1->set("mail_references=?",$references);
						$db1->where("id=?",[$ides[$b]]);
						$db1->query();
					}
				}
				$array=explode(",",(string) $ids);
				$least=$array[0];
				$array_count=count($array);

				for($i=0;$i<$array_count;$i++)
				{
					if($least>$array[$i])
					{
						$least=$array[$i];
					}
				}
				$db->select("nesote_email_customfolder_mapping_$tablenumber");
				$db->fields("id");
				$db->where("folderid=? and id<?",[$folderid,$least]);
				$db->order("id desc");
				$db->limit(0,1);

				$res=$db->query();
				$row1=$db->fetchRow($res);
				$no=$db->numRows($res);
				if ($no!=0)
				{
					if($number==1)
					{
						$p=base64_encode(191);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("192@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}
				else
				{

					$db->select("nesote_email_customfolder_mapping_$tablenumber");
					$db->fields("id");
					$db->where("folderid=? and id>?",[$folderid,$least]);
					$db->limit(0,1);

					$res=$db->query();
					$row1=$db->fetchRow($res);
					if($number==1)
					{
						$p=base64_encode(191);
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);
					}
					else
					{
						$p=base64_encode("192@@$number");
						header("Location:".$this->url("mail/mailbox/$folderid/$page/$p"));
						exit(0);

					}
				}

				//return $url.",".$folderid.",".$row1[0];
				//exit;




			}

		}
	}

	function getgroups($references)
	{
		preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);
		//print_r($reply);
		$no=count($reply[1]);
		$idstring="";
		for($i=0;$i<$no;$i++)
		{
			preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mail[$i]);
			preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folder[$i]);
			$db=new NesoteDALController();
			$idstring.=$folder[$i][1].":".$mail[$i][1].",";
		}
		return substr($idstring,0,-1);
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
	function removeblacklist($mailid,$uid)
	{
		$db=new NesoteDALController();
		$db->select("nesote_email_blacklist_mail");
		$db->fields("id");
		$db->where("mailid=? and clientid=?",[$mailid,$uid]);
		$result=$db->query();
		$no=$db->numRows($result);
		$row=$db->fetchRow($result);
		if($no!=0)
		{
			$db->delete("nesote_email_blacklist_mail");
			$db->where("id=?",$row[0]);
			$result=$db->query();
		}
	}
	function removewhitelist($mailid,$uid)
	{
		$db=new NesoteDALController();
		$db->select("nesote_email_whitelist_mail");
		$db->fields("id");
		$db->where("mailid=? and clientid=?",[$mailid,$uid]);
		$result=$db->query();
		$no=$db->numRows($result);
		$row=$db->fetchRow($result);
		if($no!=0)
		{
			$db->delete("nesote_email_whitelist_mail");
			$db->where("id=?",$row[0]);
			$result=$db->query();
		}
	}
	function setblacklist($mailid,$uid)
	{
		$db=new NesoteDALController();
		$db->select("nesote_email_blacklist_mail");
		$db->fields("id");
		$db->where("mailid=? and clientid=?",[$mailid,$uid]);
		$result=$db->query();
		$no=$db->numRows($result);
		if($no==0)
		{
			$db->insert("nesote_email_blacklist_mail");
			$db->fields("mailid,clientid");
			$db->values([$mailid,$uid]);
			$res=$db->query();
		}
	}
	function new_reference($references,$folder_old,$folder_new,$old_mailid,$mailid)
	{

		preg_match_all('/<item>(.+?)<\/item>/i',(string) $references,$reply);
		$new_1="";
		$no=count($reply[1]);
		for($i=0;$i<$no;$i++)
		{

			preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folder[$i]);
			preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mail[$i]);

			if(($folder[$i][1]==$folder_old) && ($mail[$i][1]==$old_mailid))
			{
				$new_1=str_replace("<mailid>".$old_mailid."</mailid><folderid>".$folder_old."</folderid>","<mailid>".$mailid."</mailid><folderid>".$folder_new."</folderid>",$references);
				//$new_1=str_replace("<mailid>".$old_mailid."</mailid>","<mailid>".$mailid."</mailid>",$new);
			}

		}
		//echo $new_1;
		if($new_1=="")
		{
			$new_1=$references;
		}

		return $new_1;
	}
function getunreadcountnew($userid,$folder)
	{

$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		$db=new NesoteDALController();

		if($folder==1)
		{
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("distinct mail_references");
			$db->where("userid=? and readflag=?",[$userid,0]);
			$res=$db->query();
			$no=$db->numRows($res);
			if($no!=0)
			{
return "(".$no.")";
			}
			else
			{
				return "";
			}
		}

		if($folder==2)
		{
			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("distinct mail_references");

			$db->where("userid=? and just_insert=? ",[$userid,0]);
			$res=$db->query();
			$no=$db->numRows($res);

			if($no!=0)
			{

return "(".$no.")";
			}
			else
			{
				return "";
			}
		}
		if($folder==4)
		{
			$db->select("nesote_email_spam_$tablenumber");
			$db->fields("distinct mail_references");
			$db->where("userid=? and readflag=?",[$userid,0]);
			$res=$db->query();
			$no=$db->numRows($res);
			if($no!=0)
			{
return "(".$no.")";
			}
			else
			{
				return "";
			}
		}

		if($folder>=10)
		{
			$db->select("nesote_email_customfolder_mapping_$tablenumber");
			$db->fields("distinct mail_references");
			$db->where("folderid=? and readflag=?",[$folder,0]);
			$res1=$db->query();
			$no=$db->numRows($res1);
			if($no!=0)
			{
				return "(".$no.")";
			}
			else
			{
				return "";
			}
		}
        return null;

	}
	function getunreadcount($userid,$folder)
	{

$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		$db=new NesoteDALController();

		if($folder==1)
		{
			$db->select("nesote_email_inbox_$tablenumber");
			$db->fields("distinct mail_references");
			$db->where("userid=? and readflag=?",[$userid,0]);
			$res=$db->query();
			$no=$db->numRows($res);
			if($no!=0)
			{
return "<div class=\"countShow\"><div class=\"in\">".$no."</div></div>";
			}
			else
			{
				return "";
			}
		}

		if($folder==2)
		{
			$db->select("nesote_email_draft_$tablenumber");
			$db->fields("distinct mail_references");
			$db->where("userid=? and just_insert=? ",[$userid,0]);
			$res=$db->query();
			$no=$db->numRows($res);

			if($no!=0)
			{

				
return "<div class=\"countShow\"><div class=\"in\">".$no."</div></div>";
			}
			else
			{
				return "";
			}
		}
		if($folder==4)
		{
			$db->select("nesote_email_spam_$tablenumber");
			$db->fields("distinct mail_references");
			$db->where("userid=? and readflag=?",[$userid,0]);
			$res=$db->query();
			$no=$db->numRows($res);
			if($no!=0)
			{
				
return "<div class=\"countShow\"><div class=\"in\">".$no."</div></div>";
			}
			else
			{
				return "";
			}
		}

		if($folder>=10)
		{
			$db->select("nesote_email_customfolder_mapping_$tablenumber");
			$db->fields("distinct mail_references");
			$db->where("folderid=? and readflag=?",[$folder,0]);
			$res1=$db->query();
			$no=$db->numRows($res1);
			if($no!=0)
			{
				
return "<div class=\"countShow\"><div class=\"in\">".$no."</div></div>";
			}
			else
			{
				return "";
			}
		}
        return null;

	}
	function mailfooter1Action()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		$db=new NesoteDALController();
		$fid=$this->getParam(1);
		if (!isset($fid)) {
            $fid=1;
        }
		$this->setValue("fid",$fid);

		$whichpage=$this->getParam(2);
		$this->setValue("whichpage",$whichpage);

		$page=$this->getParam(3);
		if (isset($page)) {
            $this->setValue("page",$page);
        } else {
            $this->setValue("page",1);
        }

		$rlink=$this->getParam(4);
		if (isset($rlink)) {
            $this->setValue("rlink",$rlink);
        } else {
            $this->setValue("rlink","");
        }

		$crntfold=$this->getParam(5);
		$this->setValue("crntfold",$crntfold);
		$crntid=$this->getParam(6);
		$this->setValue("crntid",$crntid);


		$id=$this->getId();
		$this->setValue("uid",$id);
		$db->select("nesote_email_customfolder");
		$db->fields("id,name");
		$db->where("userid=?",[$id]);
		$res1=$db->query();
		$i=0;
		while($rw=$db->fetchRow($res1))
		{
			$db1=new NesoteDALController();
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
			$customFolder[$i][3]=$count11;
			$countCookie="custom".$rw[0];
			setcookie($countCookie,(string) $count, ['expires' => "0", 'path' => "/"]);
			$i++;
		}
		$this->setValue("mpcount",$i);
		$this->setLoopValue("customfolders",$customFolder);

		$memorymsg=$this->getmessage(351);
		$year=date("Y",time());
		$msg1=str_replace('{year}',$year,$memorymsg);
		$this->setValue("footer",$msg1);

	}
	function mailfooter2Action()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
		$db=new NesoteDALController();
		$fid=$this->getParam(1);
		if (!isset($fid)) {
            $fid=1;
        }
		$this->setValue("fid",$fid);

		$whichpage=$this->getParam(2);
		$this->setValue("whichpage",$whichpage);
		$page=$this->getParam(3);
		if (isset($page)) {
            $this->setValue("page",$page);
        } else {
            $this->setValue("page",1);
        }


		$rlink=$this->getParam(4);
		if (isset($rlink)) {
            $this->setValue("rlink",$rlink);
        } else {
            $this->setValue("rlink","");
        }


		$crntfold=$this->getParam(5);
		$this->setValue("crntfold",$crntfold);
		$crntid=$this->getParam(6);
		$this->setValue("crntid",$crntid);


		$id=$this->getId();
		$this->setValue("uid",$id);
		$db->select("nesote_email_customfolder");
		$db->fields("id,name");
		$db->where("userid=?",[$id]);
		$res1=$db->query();
		$i=0;
		while($rw=$db->fetchRow($res1))
		{
			$db1=new NesoteDALController();
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
			$customFolder[$i][3]=$count11;
			$countCookie="custom".$rw[0];
			setcookie($countCookie,(string) $count, ['expires' => "0", 'path' => "/"]);
			$i++;
		}
		$this->setValue("mpcount",$i);
		$this->setLoopValue("customfolders",$customFolder);

		$memorymsg=$this->getmessage(351);
		$year=date("Y",time());
		$msg1=str_replace('{year}',$year,$memorymsg);
		$this->setValue("footer",$msg1);

	}

	function newmailAction()
	{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

		$id=$this->getId();
		$username=$_COOKIE['e_username'];
		$tablenumber=$this->tableid($username);
		

		$send=$_POST['send'];
		$draft=$_POST['draft'];
		$discard=$_POST['discard'];


		if (isset($send)) {
            $type=1;
        } elseif (isset($draft)) {
            $type=2;
        } elseif (isset($discard)) {
            $type=3;
        }

		$fldr=$this->getParam(1);
		$pge=$this->getParam(2);
		$act=$this->getParam(3);
		$crntfold=$this->getParam(4);
		$crntid=$this->getParam(5);

		$db=new NesoteDALController();

		if ($type==1) {
            $to=$_POST['to'];
            $to=str_replace("\"","",$to);
            $cc=$_POST['cc'];
            $cc=str_replace("\"","",$cc);
            $bcc=$_POST['bcc'];
            $bcc=str_replace("\"","",$bcc);
            if($to=="" && $cc=="" && $bcc=="")
			{
				$p=base64_encode(330);
				header("Location:".$this->url("mail/replylink/newmail/$fldr/$pge/$act/$crntfold/$crntid/$p"));
				exit(0);
			}
            $subject=htmlspecialchars((string) $_POST['sub']);
            $body=$_POST['newbody'];
            if($subject === "" && $body=="")
			{
			$p=base64_encode(196);
				header("Location:".$this->url("mail/replylink/newmail/$fldr/$pge/$act/$crntfold/$crntid/$p"));
				exit(0);
			}
            preg_match('/<img(.+?)src=(.+?)>/i',(string) $body,$cset1);
            if ($cset1[2]!="") {
                $body=str_replace("../attachments/","attachments/",$cset1[2]);
            }
            $html=htmlspecialchars($body);
            $time=$this->getusertime();
            $db->select("nesote_email_usersettings");
            $db->fields("signature,signatureflag");
            $db->where("userid=?",$id);
            $res=$db->query();
            $row=$db->fetchRow($res);
            if ($row[0]!="" && $row[1]==1) {
                $html.="<br>".$row[0];
            }
            $html=str_replace("\n","<br>",$html);
            $magic=get_magic_quotes_gpc();
            if($magic==1)
			{
				$html=stripslashes($html);
				$to=stripslashes($to);
				$cc=stripslashes($cc);
				$bcc=stripslashes($bcc);
			}
            $html=htmlspecialchars_decode($html);
            $ss=$this->smtp($to,$cc,$bcc,$subject,$html,$id,"","","",2,"");
            if(stristr(trim((string) $ss),"Invalid address")!="")
			{
				echo $ss;exit;
			}
            $this->saveLogs("Sent Mail",$username." has sent a mail");
            $p=base64_encode(173);
            if ($act=="m") {
                header("Location:".$this->url("mail/mailbox/$fldr/$pge/$p"));
                exit(0);
            } elseif ($act=="d") {
                header("Location:".$this->url("mail/detailmail/$fldr/$pge/$p"));
                exit(0);
            } elseif ($act=="r") {
                header("Location:".$this->url("mail/replylink/$fldr/$pge/$act/$crntfold/$crntid/$p"));
                exit(0);
            } elseif ($act=="dw") {
                header("Location:".$this->url("mail/getattachmentdetails/$fldr/$pge/$p"));
                exit(0);
            }
        } elseif ($type==2) {
            $fullid=$username.$this->getextension();
            $to=$_POST['to'];
            $cc=$_POST['cc'];
            $bcc=$_POST['bcc'];
            $subject=$_POST['sub'];
            $magic=get_magic_quotes_gpc();
            if($magic==1)
				{
					$to=stripslashes((string) $to);
					$cc=stripslashes((string) $cc);
					$bcc=stripslashes((string) $bcc);
				}
            //$to=htmlentities($to);
            $to=str_replace("&Acirc;","",$to);
            $to=str_replace("&lt;","<",$to);
            $to=str_replace("&gt;",">",$to);
            //$cc=htmlentities($cc);
            $cc=str_replace("&Acirc;","",$cc);
            $cc=str_replace("&lt;","<",$cc);
            $cc=str_replace("&gt;",">",$cc);
            //$bcc=htmlentities($bcc);
            $bcc=str_replace("&Acirc;","",$bcc);
            $bcc=str_replace("&lt;","<",$bcc);
            $bcc=str_replace("&gt;",">",$bcc);
            $body=$_POST['newbody'];
            preg_match('/<img(.+?)src=(.+?)>/i',(string) $body,$cset1);
            if ($cset1[2]!="") {
                $body=str_replace("../attachments/","attachments/",$cset1[2]);
            }
            $db->select("nesote_email_usersettings");
            $db->fields("signature,signatureflag");
            $db->where("userid=?",$id);
            $res=$db->query();
            $row=$db->fetchRow($res);
            if ($row[0]!="" && $row[1]==1) {
                $body.="<br>".$row[0];
            }
            $body=str_replace("\n","<br>",$body);
            $time=$this->getusertime();
            $db->insert("nesote_email_draft_$tablenumber");
            $db->fields("userid,from_list,to_list,cc,bcc,subject,body,time,just_insert");
            $db->values([$id,$fullid,$to,$cc,$bcc,$subject,$body,$time,0]);
            $db->query();
            $last_id=$db->lastInsert();
            $var=time().$username.$last_id;
            $message_id="<".md5($var).$extention.">";
            $references="<references><item><mailid>$last_id</mailid><folderid>2</folderid></item></references>";
            $db->update("nesote_email_draft_$tablenumber");
            $db->set("message_id=?,mail_references=?",[$message_id,$references]);
            $db->where("id=?",$last_id);
            $res=$db->query();
            $this->saveLogs("Saved to Draft",$username." has saved a mail to draft");
            $p=base64_encode(449);
            header("Location:".$this->url("mail/detailmail/2/$last_id/$p"));
            exit(0);
        } elseif ($type === 3) {
            //header("Location:".$this->url("mail/detailmail/$folderid/$mailid"));
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

		function getstyle($userid)
		{
			$db=new NesoteDALController();
				
			$db->select("nesote_email_usersettings");
			$db->fields("theme_id");
			$db->where("userid=?",$userid);
			$result=$db->query();//echo $db->getQuery();
			$res=$db->fetchRow($result);
			$style_id=$res[0];
			if($style_id=="" || $style_id==0)
			{
				$db->select("nesote_email_settings");
				$db->fields("value");
				$db->where("name='themes'");
				$result=$db->query();//echo $select->getQuery();
				$res=$db->fetchRow($result);
				$style_id=$res[0];


			}
			$db->select("nesote_email_themes");
			$db->fields("name,style");
			$db->where("id=?",$style_id);
			$result=$db->query();
			$theme=$db->fetchRow($result);
			return $theme[1];
				
		}

		function starAction()
		{
			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
			$mailid=$this->getParam(2);
			$folder=$this->getParam(1);
			$flag=$this->getParam(3);
			$curntfid=$this->getParam(4);
			$curntmailid=$this->getParam(5);
			$db=new NesoteDALController();
			if($flag)
			{
				if ($folder==1) {
                    $db->update("nesote_email_inbox_$tablenumber");
                } elseif ($folder==2) {
                    $db->update("nesote_email_draft_$tablenumber");
                } elseif ($folder==3) {
                    $db->update("nesote_email_sent_$tablenumber");
                } elseif ($folder==4) {
                    $db->update("nesote_email_spam_$tablenumber");
                } elseif ($folder==5) {
                    $db->update("nesote_email_trash_$tablenumber");
                } elseif ($folder>=10) {
                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->set("starflag=?",0);
				$db->where("id=?",$mailid);
				$res=$db->query();
				$p=base64_encode(189);

			}
			else
			{
				if ($folder==1) {
                    $db->update("nesote_email_inbox_$tablenumber");
                } elseif ($folder==2) {
                    $db->update("nesote_email_draft_$tablenumber");
                } elseif ($folder==3) {
                    $db->update("nesote_email_sent_$tablenumber");
                } elseif ($folder==4) {
                    $db->update("nesote_email_spam_$tablenumber");
                } elseif ($folder==5) {
                    $db->update("nesote_email_trash_$tablenumber");
                } elseif ($folder>=10) {
                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->set("starflag=?",1);
				$db->where("id=?",$mailid);
				$res=$db->query();
				$p=base64_encode(185);

			}


			header("Location:".$this->url("mail/detailmail/$curntfid/$curntmailid/$p"));
			exit(0);


		}
		function getreaddetails($folderid,$mailid)
		{
		    $username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
			$response="";
			$db= new NesoteDALController();
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
            } elseif ($folderid>=10) {
                $db->select("nesote_email_customfolder_mapping_$tablenumber");
            }
			$db->fields("readflag");
			$db->where("id=?",[$mailid]);
			$result=$db->query();
			$num=$db->numRows($result);
			if($num>0)
			{
				$row=$db->fetchRow($result);
				if ($row[0]==0) {
                    $response="<div class=\"replyLink floatL\"><a href=\"".$this->url("mail/read/$folderid/$mailid/$row[0]")."\">".$this->getmessage(74)."</a></div>";
                } elseif ($row[0]==1) {
                    $response="<div class=\"replyLink floatL\"><a href=\"".$this->url("mail/read/$folderid/$mailid/$row[0]")."\">".$this->getmessage(75)."</a></div>";
                }
				
				//$response="<li><a href=\"".$this->url("mail/read/$folderid/$mailid/$row[0]")."\"><span class=\"attachments\">".$this->getmessage(74)."</span></a></li>";
				//$response="<li><a href=\"".$this->url("mail/read/$folderid/$mailid/$row[0]")."\"><span class=\"attachments\">".$this->getmessage(75)."</span></a></li>";

				return $response;
			}
			else {
                return "";
            }

		}

		function readAction()
		{

			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
			$mailid=$this->getParam(2);
			$folder=$this->getParam(1);
			$flag=$this->getParam(3);
			$db=new NesoteDALController();
			if($flag)
			{
				if ($folder==1) {
                    $db->update("nesote_email_inbox_$tablenumber");
                } elseif ($folder==2) {
                    $db->update("nesote_email_draft_$tablenumber");
                } elseif ($folder==3) {
                    $db->update("nesote_email_sent_$tablenumber");
                } elseif ($folder==4) {
                    $db->update("nesote_email_spam_$tablenumber");
                } elseif ($folder==5) {
                    $db->update("nesote_email_trash_$tablenumber");
                } elseif ($folder>=10) {
                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->set("readflag=?",0);
				$db->where("id=?",$mailid);
				$res=$db->query();
				$p=base64_encode(187);

			}
			else
			{
				if ($folder==1) {
                    $db->update("nesote_email_inbox_$tablenumber");
                } elseif ($folder==2) {
                    $db->update("nesote_email_draft_$tablenumber");
                } elseif ($folder==3) {
                    $db->update("nesote_email_sent_$tablenumber");
                } elseif ($folder==4) {
                    $db->update("nesote_email_spam_$tablenumber");
                } elseif ($folder==5) {
                    $db->update("nesote_email_trash_$tablenumber");
                } elseif ($folder>=10) {
                    $db->update("nesote_email_customfolder_mapping_$tablenumber");
                }
				$db->set("readflag=?",1);
				$db->where("id=?",$mailid);
				$res=$db->query();
				$p=base64_encode(183);

			}


			header("Location:".$this->url("mail/detailmail/$folder/$mailid/$p/read"));
			exit(0);


		}

		function smtp($to,$cc,$bcc,$subject,$html,$id,$mail_references,$in_reply_to,$draftid,$folders,$mails)
		{

			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
			$uid=$this->getId();
			$folder=-1;
			$maild=-1;$db=new NesoteDALController();
			if($in_reply_to!="")
			{

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

			$uname=$this->getusername($id);

			$mailextn_name=$this->getextension();
			$from=$uname.$mailextn_name;
			$mail_extension=$mailextn_name;

			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?", ["SMTP_host"]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$host_name=$row[0];

			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?", ["SMTP_port"]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$port_number=$row[0];


			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name=?", ["catchall_mail"]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$catch_all=$row[0];


			if($catch_all==1)
			{
				$db->select("nesote_email_settings");
				$db->fields("value");
				$db->where("name=?", ["SMTP_username"]);
				$result=$db->query();
				$row=$db->fetchRow($result);
				$SMTP_username=$row[0];

				$db->select("nesote_email_settings");
				$db->fields("value");
				$db->where("name=?", ["SMTP_password"]);
				$result=$db->query();
				$row=$db->fetchRow($result);
				$SMTP_password=$row[0];
			}
			else
			{
				$db->select("nesote_email_usersettings");
				$db->fields("server_password,smtp_username");
				$db->where("userid=?", [$uid]);
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


			require_once(__DIR__ . '/../class/class.phpmailer.php');


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
						
					$to=explode(",",(string) $to);

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
							$to_address.=$mailid[1]."<".$mailid[2].">,";
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
							$cc_address.=$mailid[1]."<".$mailid[2].">,";
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
							$bcc_address.=$mailid[1]."<".$mailid[2].">,";
							$this->addcontact($mailid[2],$mailid[1]);


						}
					}
				}

				$tme=time();
				$mail->SetFrom($from);
				$subjekt = "=?UTF-8?B?".base64_encode(strval($subject))."?=";
				$mail->Subject = $subjekt;
				$mail->SMTPSecure="ssl";
				$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically


				$db->insert("nesote_email_sent_$tablenumber");
				$db->fields("userid,from_list,to_list,cc,bcc,subject,status,readflag,starflag,memorysize,message_id,time");
				$db->values([$uid,$from,$to_address,$cc_address,$bcc_address,$subject,1,1,0,0,$message_id,$tme]);
				$res=$db->query();
				$lastid=$db->lastInsert();

				$mail->IsHTML(true);

				
				
				preg_match('/<img(.+?)src=(.+?)>/i',(string) $html,$cset1);
						if ($cset1[2]!="") {
                            $html=str_replace("../attachments/","attachments/",$cset1[2]);
                        }


				$mail->Body=$html;

				$mail->Send();


				$message_id=$mail->MessageID;


				$time=$this->getusertime();

				$mail_references=$this->modified_reference($mail_references,$lastid);
				
				$this->update_conversation($mail_references);
                $md5_mail_references=md5((string) $mail_references);  
				$size=0;

				$db->update("nesote_email_sent_$tablenumber");
				$db->set("mail_references=?,md5_references=?,body=?,time=?,memorysize=?",[$mail_references,$md5_mail_references,$html,$time,$size]);
				$db->where("id=?",$lastid);
				$res1=$db->query();

			} catch (phpmailerException $e) {
				
				
				 return $e->errorMessage();//Pretty error messages from PHPMailer
			} catch (Exception $e) {
				 return $e->getMessage();//Boring error messages from anything else!
			}

			return null;


		}

		function modified_reference($mail_references,$lastid)
		{
				
			if($mail_references=="")
			{
				$mail_references="<references><item><mailid>$lastid</mailid><folderid>3</folderid></item></references>";
			}
			else
			{
				preg_match_all('/<item>(.+?)<\/item>/i',(string) $mail_references,$reply);

				$no=count($reply[1]);
				for($i=0;$i<$no;$i++)
				{
					preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid);
					preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid);

					if($folderid[1]==2)
					{

						$replace="<item><mailid>$mailid[1]</mailid><folderid>2</folderid></item>";
						$mail_references=str_replace($replace,"",$mail_references);

					}
				}
				$references="<item><mailid>$lastid</mailid><folderid>3</folderid></item></references>";

				$mail_references=str_replace("</references>",$references,$mail_references);
			}

			return $mail_references;
		}

		function update_conversation($mail_references)
		{
				
			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
			preg_match_all('/<item>(.+?)<\/item>/i',(string) $mail_references,$reply);
				
			$no=count($reply[1]);
			for($i=0;$i<$no;$i++)
			{
				preg_match('/<mailid>(.+?)<\/mailid>/i',$reply[1][$i],$mailid);
				preg_match('/<folderid>(.+?)<\/folderid>/i',$reply[1][$i],$folderid);
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
					$db->set("mail_references=?",[$mail_references]);
					$db->where("id=?",[$mailid[1]]);
					$rs=$db->query();
				}
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

		function addcontact($mailid,$name)
		{
			$userid=$this->getId();
			$db=new NesoteDALController();
			$db->select("nesote_email_contacts");
			$db->fields("*");
			$db->where("mailid=? and addedby=?",[$mailid,$userid]);
			$result=$db->query();
			$no=$db->numRows($result);
			if($no==0)
			{

				if($name!="")
				{
					$db->insert("nesote_email_contacts");
					$db->fields("mailid,addedby,contactgroup,firstname");
					$db->values([$mailid,$userid,0,$name]);
					$db->query();
				}
				else
				{
					$db->insert("nesote_email_contacts");
					$db->fields("mailid,addedby,contactgroup");
					$db->values([$mailid,$userid,0]);
					$db->query();
				}
				return;
			}
			else {
                return;
            }
		}
		function contactsAction()
		{
				
			$valid=$this->validateUser();

			if($valid!=TRUE)
			{
				header("Location:".$this->url("index/index"));
				exit(0);
			}
				


			$userid=$this->getId();
			
				
			$search=$_POST['search'];
			$flag = isset($search) ? 1 : 0;
			$search=mysql_real_escape_string($search);
			
			$db=new NesoteDALController();
			$db->select("nesote_email_settings");
			$db->fields("value");
			$db->where("name='public_page_logo'");
			$result1=$db->query();
			$row1=$db->fetchRow($result1);
			$img=$row1[0];
			$imgpath="../admin/logo/".$img;
			$this->setValue("imgpath",$imgpath);
		
			
			$db->select("nesote_email_contacts");
			$db->fields("mailid,id");
			if ($flag==1) {
                $db->where("addedby=? and (mailid like '%$search%' or firstname like '%$search%' or lastname like '%$search%')",[$userid]);
            } else {
                $db->where("addedby=?",[$userid]);
            }
			$db->group("mailid");
			$db->order("mailid asc");
			$result=$db->query();
			$no=$db->numRows($result);
			$this->setValue("no",$no);
				
			$this->setLoopValue("contacts",$result->getResult());
				
			$memorymsg=$this->getmessage(351);
			$year=date("Y",time());
			$msg1=str_replace('{year}',$year,$memorymsg);
			$this->setValue("footer",$msg1);
		}

		function getcontactname($id)
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_contacts");
			$db->fields("mailid,firstname,lastname");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			if ($row[1]!="") {
                return $row[1]." &lt;".$row[0]."&gt;";
            } else {
                return "&lt;".$row[0]."&gt;";
            }
				
		}
		
		function getcnamenew($id)
		{
			$db=new NesoteDALController();
			$db->select("nesote_email_contacts");
			$db->fields("mailid,firstname,lastname");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			if ($row[1]!="") {
                return $row[1]." ". $row[2]."<".$row[0].">";
            } else {
                return $row[0];
            }
				
		}

		function viewcontactsAction()
		{
			$valid=$this->validateUser();

			if($valid!=TRUE)
			{
				header("Location:".$this->url("index/index"));
				exit(0);
			}
			$this->getId();
			$id=$this->getParam(1);
			$this->setValue("cid",$id);
				
			$db=new NesoteDALController();
			
			$db->select("nesote_email_contacts");
			$db->fields("mailid,firstname,lastname");
			$db->where("id=?",[$id]);
			$result=$db->query();
			$row=$db->fetchRow($result);
			$cname = $row[1] != "" ? trim((string) $row[1])." ".trim((string) $row[2])."<".trim((string) $row[0]).">" : trim((string) $row[0]);
				
			$cname1=base64_encode($cname);
			$this->setValue("cname",$cname);$this->setValue("cname1",$cname1);$this->setValue("mailid",$row[0]);
				
			$memorymsg=$this->getmessage(351);
			$year=date("Y",time());
			$msg1=str_replace('{year}',$year,$memorymsg);
			$this->setValue("footer",$msg1);
				
				
		}
		
		function homeAction()
		{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}

     $mobile_status=$this->mobile_device_detect();
	 $mob = $mobile_status == true ? 1 : 0;
	 $this->setValue("mob",$mob);
	 
			$username=$_COOKIE['e_username'];
				$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			
			$servicename=$settings->getValue("engine_name");
			$this->setValue("servicename",$servicename);
			
			$memorymsg=$this->getmessage(351);
			$year=date("Y",time());
			$msg1=str_replace('{year}',$year,$memorymsg);
			$this->setValue("footer",$msg1);
			$this->setValue("uname",$username);
			$id=$this->getId();
		    $this->setValue("uid",$id);
		}
		
		function headerAction()
		{
		$valid=$this->validateUser();

		if($valid!=TRUE)
		{
			header("Location:".$this->url("index/index"));
			exit(0);
		}
$mobile_status=$this->mobile_device_detect();
	 $mob = $mobile_status == true ? 1 : 0;
	 $this->setValue("mob",$mob);
			$username=$_COOKIE['e_username'];
		    $tablenumber=$this->tableid($username);
				$this->loadLibrary('Settings');
			$settings=new Settings('nesote_email_settings');
			$settings->loadValues();
			
			$servicename=$settings->getValue("engine_name");
			$this->setValue("servicename",$servicename);
			
			$memorymsg=$this->getmessage(351);
			$year=date("Y",time());
			$msg1=str_replace('{year}',$year,$memorymsg);
			$this->setValue("footer",$msg1);
			$uname=$username;
			$this->setValue("uname",$uname);
			
		
			
		$id=$this->getId();
		$this->setValue("uid",$id);
		$db=new NesoteDALController();	
		$db->select("nesote_email_customfolder");
		$db->fields("id,name");
		$db->where("userid=?",[$id]);
		$res1=$db->query();
		$i=0;
		while($rw=$db->fetchRow($res1))
		{
			$db1=new NesoteDALController();
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
			$customFolder[$i][3]=$count11;
			$countCookie="custom".$rw[0];
			setcookie($countCookie,(string) $count, ['expires' => "0", 'path' => "/"]);
			$i++;
		}
		$this->setValue("mpcount",$i);
		$this->setLoopValue("customfolders",$customFolder);
		 $folder=$this->getParam(1);
		 if ($folder=="contacts") {
             $heading=$this->getmessage(2);
             $folder="";
         } elseif ($folder=="new") {
             $heading=$this->getmessage(10);
             $folder="";
         } else
		 {
		if ($folder=="") {
            $folder=1;
        }
        $search=$this->getParam(2);

		if($search!="")
		{
			$folder=$this->getParam(3);
		}
		$this->setValue("fid",$folder);
		$heading=$this->getheading($folder,$search);
			}
		$this->setValue("heading",$heading);
		}
		
		function footerAction()
		{
		$fid=$this->getParam(1);$page=$this->getParam(2);
		$uid=$this->getId();
		$this->setValue("uid",$uid);
		$this->setValue("fid",$fid);
		$this->setValue("page",$page);
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