<?php
require(__DIR__ . "/script.inc.php");
include($config_path."database.default.config.php");
echo "DB : ".$db_name."<br><br>";
$username=$_COOKIE['e_username'];
if($username!="")
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
		echo "Username : ".$username." and Table ID: ".$modlusnumber."<br><br>";
}
echo getcwd();
echo phpinfo();

?>