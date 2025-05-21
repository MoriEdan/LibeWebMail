<?php

$service_id=trim((string) $_GET['d']);
$language_code=trim((string) $_GET['lan']);
$flag=0;
//echo $language_code; exit;
$portal_installation_url="portal_installation_url";


include_once 'config/database.default.config.php';

mysql_connect($db_server,$db_username,$db_password);
mysql_select_db($db_name);

$result=mysql_query("SELECT * FROM ".$db_tableprefix."nesote_email_languages WHERE lang_code='".$language_code."'");

$tot=mysql_num_rows($result);
$result=mysql_fetch_row($result);

$language_code=$result[7];

			$result2=mysql_query("SELECT * FROM ".$db_tableprefix."nesote_email_settings WHERE name='".$portal_installation_url."'");
			$tot2=mysql_num_rows($result2);
			$result2=mysql_fetch_row($result2);
			$portal_installation_url=$result2[2];
			$portal_installation_url=substr((string) $portal_installation_url,0,strrpos((string) $portal_installation_url,"/"));

	if($tot>0)
	{
		
		$username=$_COOKIE['e_username'];
		$password=$_COOKIE['e_password'];
		$a=mysql_query("SELECT * FROM ".$db_tableprefix."nesote_liberyus_users WHERE username='".$username."' and password='".$password."' and status=1 ");
		$b=mysql_fetch_row($a);
		
		 $userid=$b[0];
		
		mysql_query("update ".$db_tableprefix."nesote_email_usersettings set lang_id='$language_code' where userid='$userid' ");
		
		if( setcookie("lang_mail",(string) $language_code, ['expires' => 0, 'path' => "/"]))
		{

			//sleep(1);

			$flag=1;

			$url="$portal_installation_url/index.php?page=index/scriptslanguage/".$language_code."/".$service_id."/".$flag;

			header("location:".$url);
			
		}//if( setcookie("io_username",$username,0,"/") && setcookie("io_password",$password,0,"/") )
		else
		{
			$flag=0;
			$url="$portal_installation_url/index.php?page=index/scriptslanguage/".$language_code."/".$service_id."/".$flag;
			header("location:".$url);

		}
	   

	}//if($tot>0)
	else
	{
		$flag=0;
		$url="$portal_installation_url/index.php?page=index/scriptslanguage/".$language_code."/".$service_id."/".$flag;
			header("location:".$url);
	}//else


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
