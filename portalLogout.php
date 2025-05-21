<?php

$username=trim((string) $_GET['u']);
$password=trim((string) $_GET['p']);
$service_id=trim((string) $_GET['d']);
$flag=0;
$portal_installation_url="portal_installation_url";
//echo $username; exit;

include_once 'config/database.default.config.php';

mysql_connect($db_server,$db_username,$db_password);
mysql_select_db($db_name);

$result=mysql_query("SELECT * FROM ".$db_tableprefix."nesote_liberyus_users WHERE username='".$username."' and password='".$password."' and status=1 ");

$uidresult=mysql_fetch_row($result);
$userid=$uidresult[0];

$tot=mysql_num_rows($result);

$result2=mysql_query("SELECT * FROM ".$db_tableprefix."nesote_email_settings WHERE name='".$portal_installation_url."'");
$tot2=mysql_num_rows($result2);
$result2=mysql_fetch_row($result2);
$portal_installation_url=$result2[2];
$portal_installation_url=substr((string) $portal_installation_url,0,strrpos((string) $portal_installation_url,"/"));
if($tot>0)
{
	$newtime=time()-90;

	mysql_query("update ".$db_tableprefix."nesote_chat_users set logout_status=1,lastupdatedtime='$newtime' where userid='$userid' ");
	
    mysql_query("update ".$db_tableprefix."nesote_email_usersettings set lastlogin=0 where userid='$userid' ");

	$a=mysql_query("select distinct u.id  from ".$db_tableprefix."nesote_chat_session u join ".$db_tableprefix."nesote_chat_session_users c   on  u.id=c.chat_id where  c.user_id='$userid' ");
	while($b=mysql_fetch_row($a))
	{
		$chat_id=$b[0];
			
		$a1=mysql_query("select group_status from ".$db_tableprefix."nesote_chat_session  where id='$chat_id' ");
		$b1=mysql_fetch_row($a1);
		if($b1[0]==1)//group chat
		{
			$fullname=$uidresult[3];
			$lang_code = $_COOKIE['lang_mail'] ?? 'eng';

			$a2=mysql_query("select id from ".$db_tableprefix."nesote_email_languages  where lang_code='$lang_code' ");
			$b2=mysql_fetch_row($a2);
			$lang_id=$b2[0];

			$a3=mysql_query("select wordscript from ".$db_tableprefix."nesote_email_messages  where lang_id='$lang_id' and msg_id='428' ");
			$b3=mysql_fetch_row($a3);
			if($b3[0]!="")
			{
				$msg=$b3[0];
					
			}
			else
			{
				$a4=mysql_query("select wordscript from ".$db_tableprefix."nesote_email_messages  where lang_id='1' and msg_id='428' ");
				$b4=mysql_fetch_row($a4);
				$msg=$b4[0];
					
			}

			$msg=str_replace("{fullname}","$fullname",$msg);

			$message="\n $msg";


			$a5=mysql_query("select user_id from ".$db_tableprefix."nesote_chat_session_users  where chat_id='$chat_id' and active_status='1' and user_id!='$userid' ");
			while($b5=mysql_fetch_row($a5))
			{
				$time=time();
				mysql_query("insert into ".$db_tableprefix." nesote_chat_temporary_messages(chat_id,sender,responders,message,time,read_flag) values('$chat_id','0','$b1[0]','$message','$time','0')");
			}


		}
			
	}

	$a6=mysql_query("select id,active_status from ".$db_tableprefix."nesote_chat_session_users  where user_id='$userid' ");
	$tot6=mysql_num_rows($a6);
	if($tot6>0)
	{
		while($b6=mysql_fetch_row($a6))
		{
			mysql_query("update ".$db_tableprefix."nesote_chat_session_users set active_status=0,typing_status='0' where user_id='$userid' and id='$b6[0]' ");
		}

	}

setcookie("e_username","", ['expires' => 0, 'path' => "/"]);
setcookie("e_password","", ['expires' => 0, 'path' => "/"]);
setcookie("image_display","", ['expires' => "0", 'path' => "/"]);
setcookie("preload","0", ['expires' => "0", 'path' => "/"]);
setcookie("folderid","0", ['expires' => "0", 'path' => "/"]);
setcookie("page_display","1", ['expires' => "0", 'path' => "/"]);
		$flag=1;
		$url="$portal_installation_url/index.php?page=index/logout/".$username."/".$service_id."/".$flag;
		//echo $url; exit;
		header("location:".$url);exit;
}
else
{
	$flag=0;
	$url="$portal_installation_url/index.php?page=index/logout/".$username."/".$service_id."/".$flag;

	header("location:".$url);
}
?>
<html>
<head>
<title>Portal Login</title>

</head>
<body>
<script type="text/javascript">
	
		parent.updateStatus('<?php echo $service_id ?>','<?php echo $flag ?>')
	
	</script>
</body>

</html>