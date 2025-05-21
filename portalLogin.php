<?php

$username=trim((string) $_GET['u']);
$password=trim((string) $_GET['p']);
$service_id=trim((string) $_GET['d']);
//echo $status; exit;
$flag=0;
$portal_installation_url="portal_installation_url";
/*echo $password;
exit;
*/

//echo $username."...".$password;
include_once 'config/database.default.config.php';
mysql_connect($db_server,$db_username,$db_password);
mysql_select_db($db_name);

$result=mysql_query("SELECT * FROM ".$db_tableprefix."nesote_liberyus_users 

WHERE username='".$username."' and password='".$password."' and status=1 ");

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
		
		$a=mysql_query("SELECT lang_id FROM ".$db_tableprefix."nesote_email_usersettings WHERE userid='$userid' ");
		$b=mysql_fetch_row($a);
		if($b[0]!="")
		{
		setcookie("lang_mail","$b[0]", ['expires' => "0", 'path' => "/"]);		
		}
		else
		{
		$a1=mysql_query("SELECT value FROM ".$db_tableprefix."nesote_email_settings WHERE name='default_language' ");	
		$b1=mysql_fetch_row($a1);
		if ($b1[0]!="") {
            setcookie("lang_mail","$b1[0]", ['expires' => "0", 'path' => "/"]);
        } else {
            setcookie("lang_mail","eng", ['expires' => "0", 'path' => "/"]);
        }	  		
		}
		 
		
		if( setcookie("e_username",$username, ['expires' => 0, 'path' => "/"]) && setcookie("e_password",$password, ['expires' => 0, 'path' => "/"]) && setcookie("folderid","1", ['expires' => "0", 'path' => "/"]) && 	setcookie("page","1", ['expires' => "0", 'path' => "/"]) && setcookie("preload","0", ['expires' => "0", 'path' => "/"]) && setcookie("page_display","1", ['expires' => "0", 'path' => "/"]) && setcookie("crnt_mailid","0", ['expires' => "0", 'path' => "/"]) &&  setcookie("image_display","", ['expires' => "0", 'path' => "/"]) && setcookie("start","1", ['expires' => "0", 'path' => "/"]) && setcookie("folder","inbox", ['expires' => "0", 'path' => "/"])  )
		{
			//sleep(1);
			$flag=1;
			$url="$portal_installation_url/index.php?page=index/registrationprocess/".$service_id."/".$username."/".$password."/".$flag;
				
			header("location:".$url);
			
		}//if( setcookie("io_username",$username,0,"/") && setcookie("io_password",$password,0,"/") )
		else
		{
			$flag=0;
			$url="$portal_installation_url/index.php?page=index/registrationprocess/".$service_id."/".$username."/".$password."/".$flag;
			header("location:".$url);

		}
	   

	}//if($tot>0)
	else
	{
		$flag=0;
		$url="$portal_installation_url/index.php?page=index/registrationprocess/".$service_id."/".$username."/".$password."/".$flag;
			header("location:".$url);
	}//else


?>
<!--<html>
<head>
	<title>Portal Login</title>
	
</head>
<body>
	<script type="text/javascript">
	
		parent.updateStatus('<?php /*?><?php echo $service_id ?>','<?php echo $flag ?><?php */?>')
	
	</script>
</body>

</html>-->
