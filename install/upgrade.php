<?php

function validatelicense($license_key) {

	/* $scriptcode = "webmailadv";
	$lic_data = "";
	$php_self = $_SERVER["HTTP_HOST"];
	$serverarray[0] = "licval.liberyus.com";
	$serverarray[1] = "www.nesotelvs1.info";
	$serverarray[2] = "www.liberyus.com";
	$serverarray[3] = "www.nesotelvs2.info";
	$lcount = 0;
	while (count($serverarray) > $lcount)
	{
		$lic_data = "";
		if ($fp_license = fopen("http://" . $serverarray[$lcount] . "/validate_license/" . $scriptcode . "/" . ('' . $license_key) . "/" . ('' . $php_self), "r"))
		{
			while (!(feof($fp_license)))
			{
				$lic_data .= fgetc($fp_license);
				continue;
			}
			fclose($fp_license);
			if ($lic_data == "1")
			{
				return true;
			}
		}
		if ($lic_data != "0" && function_exists("curl_init"))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://" . $serverarray[$lcount] . "/validate_license/" . $scriptcode . "/" . ('' . $license_key) . "/" . ('' . $php_self));
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$content = curl_exec($ch);
			curl_close($ch);
			$lic_data = $content;
			if ($lic_data == "1")
			{
				return true;
			}
		}
		if ($lic_data == "")
		{
			$lic_data = "not available";
		}
		if ($lic_data == "0")
		{
			echo "<br>Failed to validate the license key. Please verify the license key that you have entered in your configuration file.<br>";
			die(0);
		}
		$lcount++;
		continue;
	}
	echo "<br>An error has occurred while validating your license. Please click the refresh button of your browser and try again. If you repeatedly getting the error please contact liberyus support desk.<br>";
	exit();
	return; */
	return true;
}


echo "<html>\r
<head><title>Upgrade Libe Web Mail Ultimate Version</title>\r
";
echo "<s";
echo "tyle>\r
a.logos {\r
\r
\r
font-size:12px;\r
font-weight:bold;\r
font-family:Verdana, Arial, Helvetica, sans-serif;\r
color:#104E8B;\r
text-decoration: underline;\r
\r
}\r
\r
a.logos:hover {\r
	font-size:12px;\r
	font-weight:bold;\r
	font-family:Verdana, Arial, Helvetica, sans-serif;\r
	color: #FF0000;\r
	text-decoration: none;\r
	}\r
.logossubtitle {\r
\r
font-size:18px;\r
font-weight:bold;\r
font-family:Verdana, Arial,";
echo " Helvetica, sans-serif;\r
color:#336565;\r
\r
\r
}\r
</style>\r
</head>\r
<body style=\"background-image: url('images/header.png');background-repeat: repeat-x;background-color: #ffffff;width: 100%;height: 100%;\">\r
\r
";
set_time_limit(0);
$yr = date("Y", time());
require __DIR__ . "/../script.inc.php";
require "../" . $config_path . "system.config.php";
validatelicense($license_key);
require __DIR__ . "/../script.inc.php";
include "../" . $config_path . "database.default.config.php";
error_reporting(0);
$link = mysql_connect($db_server, $db_username, $db_password);
mysql_select_db($db_name);
if (!(mysql_select_db($db_name)))
{
	echo "<br><br><span style=\"color:#ff0000;align:center;padding-left:30px;\">Database connection failed! Please verify the connection settings in the configuration file!!!</span>\r
						<div style=\"align:center;padding-left:30px;padding-top:10px;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;\r
color:#999999;\">Copyright &copy; " . $yr . " Libescripts.com. All Rights Reserved.</div>";
	die(0);
}
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_customfolder_mapping_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `folderid` int(11) NOT NULL,\r
  `from_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `to_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `cc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `bcc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` longblob NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `readflag` int(11) NOT NULL,\r
  `starflag` int(11) NOT NULL,\r
  `memorysize` bigint(20) NOT NULL,\r
  `message_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `mail_references` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `md5_references` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
"));
	mysql_query("CREATE INDEX md5_references\r
on `" . $db_tableprefix . ('nesote_email_customfolder_mapping_' . $i . "` (md5_references)"));
	mysql_query("CREATE INDEX folderid\r
on `" . $db_tableprefix . ('nesote_email_customfolder_mapping_' . $i . "` (folderid,message_id (256))"));
	$i++;
}
$resultset = mysql_query("select id,username  from " . $db_tableprefix . "nesote_users");
if ($row = mysql_fetch_row($resultset))
{
	$user_name = $row[1];
	include __DIR__ . "/../config.php";
	$number = $cluster_factor;
	$user_name = trim((string) $user_name);
	$mdsuser_name = md5($user_name);
	$mdsuser_name = str_replace("a", "", $mdsuser_name);
	$mdsuser_name = str_replace("b", "", $mdsuser_name);
	$mdsuser_name = str_replace("c", "", $mdsuser_name);
	$mdsuser_name = str_replace("d", "", $mdsuser_name);
	$mdsuser_name = str_replace("e", "", $mdsuser_name);
	$mdsuser_name = str_replace("f", "", $mdsuser_name);
	$digits = substr($mdsuser_name, -6);
	$modlusnumber = $digits % $number;
	$modlusnumber += 1;
	$numbers[$modlusnumber]++;
	$resultset1 = mysql_query("select *  from " . $db_tableprefix . ('nesote_email_customfolder_mapping where userid=' . $row["0"]));
	while (!($rw = mysql_fetch_row($resultset1))) {
    }
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_email_customfolder_mapping_' . $modlusnumber . "` (`id`,`folderid`,`from_list`,`to_list`,`cc`,`bcc`,`subject`,`body`,`time`,`status`,`readflag`,`starflag`,`memorysize`,`message_id`,`mail_references`,`md5_references`) VALUES('" . $rw["0"] . "'," . $rw["1"] . "','" . $rw["2"] . "','" . $rw["3"] . "','" . $rw["4"] . "','" . $rw["5"] . "','" . $rw["6"] . "','" . $rw["7"] . "','" . $rw["8"] . "','" . $rw["9"] . "','" . $rw["10"] . "','" . $rw["11"] . "','" . $rw["12"] . "','" . $rw["13"] . "','" . $rw["14"] . "','" . $rw["15"] . "')"));
	continue;
}
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_draft_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `from_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `to_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `cc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `bcc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` longblob NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `readflag` int(11) NOT NULL,\r
  `starflag` int(11) NOT NULL,\r
  `memorysize` bigint(20) NOT NULL,\r
  `message_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `mail_references` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `just_insert` int(11) NOT NULL,\r
  `md5_references` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
"));
	mysql_query("CREATE INDEX md5_references\r
on `" . $db_tableprefix . ('nesote_email_draft_' . $i . "` (md5_references)"));
	mysql_query("CREATE INDEX userid\r
on `" . $db_tableprefix . ('nesote_email_draft_' . $i . "` (userid,message_id (256))"));
	$i++;
}
$resultset = mysql_query("select id,username  from " . $db_tableprefix . "nesote_users");
if ($row = mysql_fetch_row($resultset))
{
	$user_name = $row[1];
	include __DIR__ . "/../config.php";
	$number = $cluster_factor;
	$user_name = trim((string) $user_name);
	$mdsuser_name = md5($user_name);
	$mdsuser_name = str_replace("a", "", $mdsuser_name);
	$mdsuser_name = str_replace("b", "", $mdsuser_name);
	$mdsuser_name = str_replace("c", "", $mdsuser_name);
	$mdsuser_name = str_replace("d", "", $mdsuser_name);
	$mdsuser_name = str_replace("e", "", $mdsuser_name);
	$mdsuser_name = str_replace("f", "", $mdsuser_name);
	$digits = substr($mdsuser_name, -6);
	$modlusnumber = $digits % $number;
	$modlusnumber += 1;
	$numbers[$modlusnumber]++;
	$resultset1 = mysql_query("select *  from " . $db_tableprefix . ('nesote_email_draft where userid=' . $row["0"]));
	while (!($rw = mysql_fetch_row($resultset1))) {
    }
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_email_draft_' . $modlusnumber . "` (`id`,`userid`,`from_list`,`to_list`,`cc`,`bcc`,`subject`,`body`,`time`,`status`,`readflag`,`starflag`,`memorysize`,`message_id`,`mail_references`,`just_insert`,`md5_references`) VALUES('" . $rw["0"] . "','" . $rw["1"] . "','" . $rw["2"] . "','" . $rw["3"] . "','" . $rw["4"] . "','" . $rw["5"] . "','" . $rw["6"] . "','" . $rw["7"] . "','" . $rw["8"] . "','" . $rw["9"] . "','" . $rw["10"] . "','" . $rw["11"] . "','" . $rw["12"] . "','" . $rw["13"] . "','" . $rw["14"] . "','" . $rw["16"] . "','" . $rw["15"] . "')"));
	continue;
}
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_inbox_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `from_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `to_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `cc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `bcc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` longblob NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `readflag` int(11) NOT NULL,\r
  `starflag` int(11) NOT NULL,\r
  `memorysize` bigint(20) NOT NULL,\r
  `message_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `mail_references` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `md5_references` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
"));
	mysql_query("CREATE INDEX md5_references\r
on `" . $db_tableprefix . ('nesote_email_inbox_' . $i . "` (md5_references)"));
	mysql_query("CREATE INDEX userid\r
on `" . $db_tableprefix . ('nesote_email_inbox_' . $i . "` (userid,message_id (256))"));
	$i++;
}
$resultset = mysql_query("select id,username  from " . $db_tableprefix . "nesote_users");
if ($row = mysql_fetch_row($resultset))
{
	$user_name = $row[1];
	include __DIR__ . "/../config.php";
	$number = $cluster_factor;
	$user_name = trim((string) $user_name);
	$mdsuser_name = md5($user_name);
	$mdsuser_name = str_replace("a", "", $mdsuser_name);
	$mdsuser_name = str_replace("b", "", $mdsuser_name);
	$mdsuser_name = str_replace("c", "", $mdsuser_name);
	$mdsuser_name = str_replace("d", "", $mdsuser_name);
	$mdsuser_name = str_replace("e", "", $mdsuser_name);
	$mdsuser_name = str_replace("f", "", $mdsuser_name);
	$digits = substr($mdsuser_name, -6);
	$modlusnumber = $digits % $number;
	$modlusnumber += 1;
	$numbers[$modlusnumber]++;
	$resultset1 = mysql_query("select *  from " . $db_tableprefix . ('nesote_email_inbox where userid=' . $row["0"]));
	while (!($rw = mysql_fetch_row($resultset1))) {
    }
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_email_inbox_' . $modlusnumber . "` (`id`,`userid`,`from_list`,`to_list`,`cc`,`bcc`,`subject`,`body`,`time`,`status`,`readflag`,`starflag`,`memorysize`,`message_id`,`mail_references`,`md5_references`) VALUES('" . $rw["0"] . "','" . $rw["1"] . "','" . $rw["2"] . "','" . $rw["3"] . "','" . $rw["4"] . "','" . $rw["5"] . "','" . $rw["6"] . "','" . $rw["7"] . "','" . $rw["8"] . "','" . $rw["9"] . "','" . $rw["10"] . "','" . $rw["11"] . "','" . $rw["12"] . "','" . $rw["13"] . "','" . $rw["14"] . "','" . $rw["15"] . "')"));
	continue;
}
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_sent_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `from_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `to_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `cc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `bcc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` longblob NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `readflag` int(11) NOT NULL,\r
  `starflag` int(11) NOT NULL,\r
  `memorysize` bigint(20) NOT NULL,\r
  `message_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `mail_references` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `md5_references` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
"));
	mysql_query("CREATE INDEX md5_references\r
on `" . $db_tableprefix . ('nesote_email_sent_' . $i . "` (md5_references)"));
	mysql_query("CREATE INDEX userid\r
on `" . $db_tableprefix . ('nesote_email_sent_' . $i . "` (userid,message_id (256))"));
	$i++;
}
$resultset = mysql_query("select id,username  from " . $db_tableprefix . "nesote_users");
if ($row = mysql_fetch_row($resultset))
{
	$user_name = $row[1];
	include __DIR__ . "/../config.php";
	$number = $cluster_factor;
	$user_name = trim((string) $user_name);
	$mdsuser_name = md5($user_name);
	$mdsuser_name = str_replace("a", "", $mdsuser_name);
	$mdsuser_name = str_replace("b", "", $mdsuser_name);
	$mdsuser_name = str_replace("c", "", $mdsuser_name);
	$mdsuser_name = str_replace("d", "", $mdsuser_name);
	$mdsuser_name = str_replace("e", "", $mdsuser_name);
	$mdsuser_name = str_replace("f", "", $mdsuser_name);
	$digits = substr($mdsuser_name, -6);
	$modlusnumber = $digits % $number;
	$modlusnumber += 1;
	$numbers[$modlusnumber]++;
	$resultset1 = mysql_query("select *  from " . $db_tableprefix . ('nesote_email_sent where userid=' . $row["0"]));
	while (!($rw = mysql_fetch_row($resultset1))) {
    }
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_email_sent_' . $modlusnumber . "` (`id`,`userid`,`from_list`,`to_list`,`cc`,`bcc`,`subject`,`body`,`time`,`status`,`readflag`,`starflag`,`memorysize`,`message_id`,`mail_references`,`md5_references`) VALUES('" . $rw["0"] . "','" . $rw["1"] . "','" . $rw["2"] . "','" . $rw["3"] . "','" . $rw["4"] . "','" . $rw["5"] . "','" . $rw["6"] . "','" . $rw["7"] . "','" . $rw["8"] . "','" . $rw["9"] . "','" . $rw["10"] . "','" . $rw["11"] . "','" . $rw["12"] . "','" . $rw["13"] . "','" . $rw["14"] . "','" . $rw["15"] . "')"));
	continue;
}
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_spam_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `from_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `to_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `cc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `bcc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` longblob NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `readflag` int(11) NOT NULL,\r
  `starflag` int(11) NOT NULL,\r
  `memorysize` bigint(20) NOT NULL,\r
  `message_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `mail_references` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `backreference` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `md5_references` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
"));
	mysql_query("CREATE INDEX md5_references\r
on `" . $db_tableprefix . ('nesote_email_spam_' . $i . "` (md5_references)"));
	mysql_query("CREATE INDEX userid\r
on `" . $db_tableprefix . ('nesote_email_spam_' . $i . "` (userid,message_id (256))"));
	$i++;
}
$resultset = mysql_query("select id,username  from " . $db_tableprefix . "nesote_users");
if ($row = mysql_fetch_row($resultset))
{
	$user_name = $row[1];
	include __DIR__ . "/../config.php";
	$number = $cluster_factor;
	$user_name = trim((string) $user_name);
	$mdsuser_name = md5($user_name);
	$mdsuser_name = str_replace("a", "", $mdsuser_name);
	$mdsuser_name = str_replace("b", "", $mdsuser_name);
	$mdsuser_name = str_replace("c", "", $mdsuser_name);
	$mdsuser_name = str_replace("d", "", $mdsuser_name);
	$mdsuser_name = str_replace("e", "", $mdsuser_name);
	$mdsuser_name = str_replace("f", "", $mdsuser_name);
	$digits = substr($mdsuser_name, -6);
	$modlusnumber = $digits % $number;
	$modlusnumber += 1;
	$numbers[$modlusnumber]++;
	$resultset1 = mysql_query("select *  from " . $db_tableprefix . ('nesote_email_spam where userid=' . $row["0"]));
	while (!($rw = mysql_fetch_row($resultset1))) {
    }
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_email_spam_' . $modlusnumber . "` (`id`,`userid`,`from_list`,`to_list`,`cc`,`bcc`,`subject`,`body`,`time`,`status`,`readflag`,`starflag`,`memorysize`,`message_id`,`mail_references`,`backreference`,`md5_references`) VALUES('" . $rw["0"] . "','" . $rw["1"] . "','" . $rw["2"] . "','" . $rw["3"] . "','" . $rw["4"] . "','" . $rw["5"] . "','" . $rw["6"] . "','" . $rw["7"] . "','" . $rw["8"] . "','" . $rw["9"] . "','" . $rw["10"] . "','" . $rw["11"] . "','" . $rw["12"] . "','" . $rw["13"] . "','" . $rw["14"] . "','" . $rw["16"] . "','" . $rw["15"] . "')"));
	continue;
}
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_trash_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `from_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `to_list` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `cc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `bcc` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` longblob NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `readflag` int(11) NOT NULL,\r
  `starflag` int(11) NOT NULL,\r
  `memorysize` bigint(20) NOT NULL,\r
  `message_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `mail_references` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `backreference` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `md5_references` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
"));
	mysql_query("CREATE INDEX md5_references\r
on `" . $db_tableprefix . ('nesote_email_trash_' . $i . "` (md5_references)"));
	mysql_query("CREATE INDEX userid\r
on `" . $db_tableprefix . ('nesote_email_trash_' . $i . "` (userid,message_id (256))"));
	$i++;
}
$resultset = mysql_query("select id,username  from " . $db_tableprefix . "nesote_users");
if ($row = mysql_fetch_row($resultset))
{
	$user_name = $row[1];
	include __DIR__ . "/../config.php";
	$number = $cluster_factor;
	$user_name = trim((string) $user_name);
	$mdsuser_name = md5($user_name);
	$mdsuser_name = str_replace("a", "", $mdsuser_name);
	$mdsuser_name = str_replace("b", "", $mdsuser_name);
	$mdsuser_name = str_replace("c", "", $mdsuser_name);
	$mdsuser_name = str_replace("d", "", $mdsuser_name);
	$mdsuser_name = str_replace("e", "", $mdsuser_name);
	$mdsuser_name = str_replace("f", "", $mdsuser_name);
	$digits = substr($mdsuser_name, -6);
	$modlusnumber = $digits % $number;
	$modlusnumber += 1;
	$numbers[$modlusnumber]++;
	$resultset1 = mysql_query("select *  from " . $db_tableprefix . ('nesote_email_trash where userid=' . $row["0"]));
	while (!($rw = mysql_fetch_row($resultset1))) {
    }
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_email_trash_' . $modlusnumber . "` (`id`,`userid`,`from_list`,`to_list`,`cc`,`bcc`,`subject`,`body`,`time`,`status`,`readflag`,`starflag`,`memorysize`,`message_id`,`mail_references`,`backreference`,`md5_references`) VALUES('" . $rw["0"] . "','" . $rw["1"] . "','" . $rw["2"] . "','" . $rw["3"] . "','" . $rw["4"] . "','" . $rw["5"] . "','" . $rw["6"] . "','" . $rw["7"] . "','" . $rw["8"] . "','" . $rw["9"] . "','" . $rw["10"] . "','" . $rw["11"] . "','" . $rw["12"] . "','" . $rw["13"] . "','" . $rw["14"] . "','" . $rw["16"] . "','" . $rw["15"] . "')"));
	continue;
}
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_attachments_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `mailid` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `name` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `type` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `folderid` int(11) NOT NULL,\r
  `attachment` int(11) NOT NULL,\r
  `userid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\r
"));
	mysql_query("CREATE INDEX mailid\r
on `" . $db_tableprefix . ('nesote_email_attachments_' . $i . "` (mailid (256),folderid,attachment,userid)"));
	$i++;
}
$resultset = mysql_query("select * from " . $db_tableprefix . "nesote_email_attachments");
while ($row = mysql_fetch_row($resultset))
{
	if ($row[4] == 1) {
        $tble = "nesote_email_inbox";
    } elseif ($row[4] == 2) {
        $tble = "nesote_email_draft";
    } elseif ($row[4] == 3) {
        $tble = "nesote_email_sent";
    } elseif ($row[4] == 4) {
        $tble = "nesote_email_spam";
    } elseif ($row[4] == 5) {
        $tble = "nesote_email_trash";
    } elseif ($row[4] >= 10) {
        $tble = "nesote_email_customfolder_mapping";
    }
	if ($tble != "nesote_email_customfolder_mapping")
	{
		$resultset1 = mysql_query("select userid from " . $db_tableprefix . ('' . $tble . " where id=" . $row["1"]));
	}
	 else 
	{
		$resultset1 = mysql_query("SELECT a.userid FROM " . $db_tableprefix . "nesote_email_customfolder_mapping i join " . $db_tableprefix . ('nesote_email_customfolder a on a.id=i.folderid where i.id=' . $row["1"]));
	}
	$rs = mysql_fetch_row($resultset1);
	$uid = $rs[0];
	$resultset2 = mysql_query("select username from " . $db_tableprefix . ('nesote_users where id=' . $uid));
	$rs1 = mysql_fetch_row($resultset2);
	$user_name = $rs1[0];
	include __DIR__ . "/../config.php";
	$number = $cluster_factor;
	$user_name = trim((string) $user_name);
	$mdsuser_name = md5($user_name);
	$mdsuser_name = str_replace("a", "", $mdsuser_name);
	$mdsuser_name = str_replace("b", "", $mdsuser_name);
	$mdsuser_name = str_replace("c", "", $mdsuser_name);
	$mdsuser_name = str_replace("d", "", $mdsuser_name);
	$mdsuser_name = str_replace("e", "", $mdsuser_name);
	$mdsuser_name = str_replace("f", "", $mdsuser_name);
	$digits = substr($mdsuser_name, -6);
	$modlusnumber = $digits % $number;
	$modlusnumber += 1;
	$numbers[$modlusnumber]++;
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_email_attachments_' . $modlusnumber . "` (`id`,`mailid`,`name`,`type`,`folderid`,`attachment`,`userid`) VALUES('" . $row["0"] . "','" . $row["1"] . "','" . $row["2"] . "','" . $row["3"] . "','" . $row["4"] . "','" . $row["5"] . "','" . $uid . "')"));
	if ($row[3] == 0)
	{
		$resultset3 = mysql_query("select body from " . $db_tableprefix . ('' . $tble . " where id=" . $row["3"]));
		$rs3 = mysql_fetch_row($resultset3);
		$body = $rs3[0];
		$body = str_replace("attachments/" . $row[4] . "/" . $row[1], "attachments/" . $row[4] . "/" . $modlusnumber . "/" . $row[1], $body);
		mysql_query("UPDATE `" . $db_tableprefix . ('' . $tble . "` SET `body`= '" . $body . "' where `id`='" . $row["1"] . "';"));
	}
	if (!(is_dir("../attachments")))
	{
		mkdir("../attachments", 511);
	}
	if (!(is_dir('../attachments/' . $row["4"])))
	{
		mkdir('../attachments/' . $row["4"], 511);
	}
	if (!(is_dir('../attachments/' . $row["4"] . "/" . $modlusnumber)))
	{
		mkdir('../attachments/' . $row["4"] . "/" . $modlusnumber, 511);
	}
	if (!(is_dir('../attachments/' . $row["4"] . "/" . $modlusnumber . "/" . $row["1"])))
	{
		mkdir('../attachments/' . $row["4"] . "/" . $modlusnumber . "/" . $row["1"], 511);
	}
	copy("../attachments/" . $row[4] . "/" . $row[1] . "/" . $row[2], "../attachments/" . $row[4] . "/" . $modlusnumber . "/" . $row[1] . "/" . $row[2]);
	unlink(".../attachments/" . $row[4] . "/" . $row[1] . "/" . $row[2]);
}
mysql_query("drop table " . $db_tableprefix . "nesote_email_attachments");
mysql_query("drop table " . $db_tableprefix . "nesote_email_inbox");
mysql_query("drop table " . $db_tableprefix . "nesote_email_draft");
mysql_query("drop table " . $db_tableprefix . "nesote_email_sent");
mysql_query("drop table " . $db_tableprefix . "nesote_email_spam");
mysql_query("drop table " . $db_tableprefix . "nesote_email_trash");
mysql_query("drop table " . $db_tableprefix . "nesote_email_customfolder_mapping");
mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_tip_of_the_day` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `title` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `message` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `time` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_todo_list` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `list` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `notes` tinytext collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_shortcuts` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `keyvalue` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `description` tinytext collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
mysql_query("TRUNCATE TABLE `" . $db_tableprefix . "nesote_email_shortcuts`");
mysql_query("INSERT INTO `" . $db_tableprefix . "nesote_email_shortcuts` (`keyvalue`, `description`) VALUES\r
('<strong>INS(Insert)+c</strong>', 'Compose'),\r
('<strong>INS(Insert)+r</strong>', 'Reply'),\r
('<strong>INS(Insert)+a</strong>', 'Reply All'),\r
('<strong>INS(Insert)+f</strong>', 'Forward'),\r
('<strong>INS(Insert)+q</strong>', 'Get Mail'),\r
('<strong>INS(Insert)+Space</strong>', 'Search'),\r
('<strong>INS(Insert)+h</strong>', 'Add New Folder'),\r
('<strong>INS(Insert)+w</strong>', 'Delete Folder'),\r
('<strong>INS(Insert)+d</strong>', 'Delete'),\r
('<strong>INS (Insert)+z</strong>', 'Send a Mail'),\r
('<strong>INS (Insert)+s</strong>', 'Save to Draft'),\r
('<strong>INS (Insert)+Esc</strong>', 'Discard a Mail'),\r
('<strong>INS(Insert)+p</strong>', 'Print a Mail'),\r
('<strong>Alt+r</strong>', 'Read'),\r
('<strong>Alt+u</strong>', 'Unread'),\r
('<strong>Alt+x</strong>', 'Star'),\r
('<strong>Alt+q</strong>', 'Unstar'),\r
('<strong>INS(Insert)+u</strong>', 'More Function'),\r
('<strong>INS(Insert)+v</strong>', 'Move to Function'),\r
('<strong>INS(Insert)+l</strong>', 'Empty Spam'),\r
('<strong>INS(Insert)+m</strong>', 'Empty Trash'),\r
('<strong>Alt+y</strong>', 'Select All'),\r
('<strong>Alt+z</strong>', 'Deselect All'),\r
('<strong>INS(Insert)+g</strong>', 'Add Contactgroup'),\r
('<strong>INS(Insert)+o</strong>', 'Delete Contact Group'),\r
('<strong>INS(Insert)+n</strong>', 'Add Contact Details'),\r
('<strong>Alt+m</strong>', 'Go to Mail Section'),\r
('<strong>Alt+c</strong>', 'Contact Section'),\r
('<strong>Alt+w</strong>', 'Go to Settings Sections'),\r
('<strong>INS(Insert)+t</strong>', 'Show Theme'),\r
('<strong>INS(Insert)+j</strong>', 'View Calendar'),\r
('<strong>INS(Insert)+k</strong>', 'To-do List'),\r
('<strong>INS(Insert)+y</strong>', 'Invite a Chat'),\r
('<strong>INS(Insert)+x</strong>', 'Logout');\r
");
$i = 1;
while ($i <= 100)
{
	mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_email_events_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `start` int(11) NOT NULL,\r
  `end` int(11) NOT NULL,\r
  `allday` int(11) NOT NULL,\r
  `title` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `timezone` int(11) NOT NULL,\r
  `description` tinytext collate utf8_unicode_ci NOT NULL,\r
  `remainder` int(11) NOT NULL,\r
  `remainder_time` int(11) NOT NULL,\r
  `emailsend` int(11) NOT NULL,\r
  `remainder_mode` int(11) NOT NULL,\r
  `bg_color` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
"));
	$i++;
}
mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_calendar_settings` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `value` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
mysql_query("ALTER TABLE `" . $db_tableprefix . "nesote_email_usersettings` ADD  `email_remainder` INT NOT NULL,ADD  `view_event` INT NOT NULL,ADD  `shortcuts` INT NOT NULL");
mysql_query("INSERT INTO `" . $db_tableprefix . "nesote_email_settings` (`name`, `value`) VALUES\r
('shortcuts', '0'),\r
('todolist', '1'),\r
('min_passwordlength', '2'),\r
('birthday_email_subject','Happy Birthday'),\r
('birthday_email_body','<span style=\"color: rgb(102, 0, 153);\">Dear {name},</span><br style=\"color: rgb(102, 0, 153);\"><span style=\"color: rgb(102, 0, 153);\">&nbsp;</span><br style=\"color: rgb(102, 0, 153);\"><span style=\"color: rgb(102, 0, 153);\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span><strong style=\"color: rgb(102, 0, 153);\">Wishing</strong><span style=\"color: rgb(102, 0, 153);\"> you great happiness and successes on your </span><strong style=\"color: rgb(102, 0, 153);\">Birthday</strong><br style=\"color: rgb(153, 0, 102);\"><span style=\"color: rgb(153, 0, 102);\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span><br style=\"color: rgb(153, 0, 102);\"><span style=\"color: rgb(153, 0, 102);\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br></span>'),\r
('birthday_mail',1),\r
('tip_ofthe_day',1),\r
('portal_status', '0'),\r
('portal_installation_url', '');\r
");
$resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='email_remainder'");
$row5 = mysql_fetch_row($resultset1);
$email_remainder = $row5[0];
$resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='view_event'");
$row6 = mysql_fetch_row($resultset2);
$view_event = $row6[0];
$resultset3 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='shortcuts'");
$row7 = mysql_fetch_row($resultset3);
$shortcuts = $row7[0];
$resultset = mysql_query("select id,username  from " . $db_tableprefix . "nesote_users");
while ($row = mysql_fetch_row($resultset))
{
	mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_usersettings` SET `email_remainder` = '" . $email_remainder . "',`view_event` = '" . $view_event . "',`shortcuts` = '" . $shortcuts . "' where id='" . $row[0] . "';");
}
mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_messages` SET `wordscript` = 'Calendar' where `msg_id`='3' and `lang_id`='1';");
mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_messages` SET `wordscript` = 'Pas de mails à afficher' where `msg_id`='30' and `lang_id`='3';");
mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_messages` SET `wordscript` = 'Joindre plusieurs fichiers' where `msg_id`='60' and `lang_id`='3';");
mysql_query("INSERT INTO `" . $db_tableprefix . "nesote_email_messages` (`msg_id`, `lang_id`, `wordscript`) VALUES\r
(600, 1, 'Your message has been discarded.'),\r
(601, 1, 'Back to message'),\r
(602, 1, 'Sign out'),\r
(603, 1, 'No contacts to display.'),\r
(604, 1, 'Report Spam'),\r
(605, 1, 'From date'),\r
(606, 1, 'From time'),\r
(607, 1, 'To date'),\r
(608, 1, 'To time'),\r
(609, 1, 'Description'),\r
(610, 1, 'Save'),\r
(611, 1, 'Add Event'),\r
(612, 1, 'Reminder'),\r
(613, 1, 'minutes'),\r
(614, 1, 'hours'),\r
(615, 1, 'days'),\r
(616, 1, 'Week'),\r
(617, 1, 'Time zone'),\r
(618, 1, '(No event)'),\r
(619, 1, 'Sorry, you cannot create an event that ends before it starts.'),\r
(620, 1, 'Edit event'),\r
(621, 1, 'Configure Your Calendar Settings Below'),\r
(622, 1, 'Email Reminder'),\r
(623, 1, 'View events on mouseover'),\r
(624, 1, 'Event Details'),\r
(625, 1, 'More Details'),\r
(626, 1, 'To-do List'),\r
(627, 1, 'Edit Details'),\r
(628, 1, 'Notes'),\r
(629, 1, 'Back to To-do list'),\r
(630, 1, 'Today'),\r
(631, 1, 'Completed'),\r
(632, 1, 'Pending'),\r
(633, 1, 'You Want to Perform This Operation. Press OK to Continue.'),\r
(634, 1, 'Today List'),\r
(635, 1, 'View Completed List'),\r
(636, 1, 'View Pending List'),\r
(637, 1, 'View All'),\r
(638, 1, 'Clear Today List'),\r
(639, 1, 'Clear Completed List'),\r
(640, 1, 'Clear Pending List'),\r
(641, 1, 'Clear List'),\r
(642, 1, 'Empty Spam'),\r
(643, 1, 'Empty Trash'),\r
(644, 1, 'This action will affect all conversations in Spam. Are you sure you want to continue?'),\r
(645, 1, 'This action will affect all conversations in Trash. Are you sure you want to continue?'),\r
(646, 1, 'Print'),\r
(647, 1, 'Keyboard Shortcuts'),\r
(648, 1, 'Keyboard Shortcuts On'),\r
(649, 1, 'Keyboard Shortcuts Off'),\r
(650, 1, 'Learn More'),\r
(651, 1, 'Keyboard shortcuts help you save time by allowing you to never take your hands off the keyboard to use the mouse.'),\r
(652, 1, 'Upcoming Events'),\r
(653, 1, 'Choose Events Color'),\r
(654, 1, 'No events added'),\r
(600, 2, 'Su mensaje ha sido descartado.'),\r
(601, 2, 'Volver al mensaje'),\r
(602, 2, 'salir'),\r
(603, 2, 'No hay contactos para mostrar.'),\r
(604, 2, 'Reportar Spam'),\r
(605, 2, 'partir de la fecha'),\r
(606, 2, 'de vez en'),\r
(607, 2, 'hasta la fecha'),\r
(608, 2, 'a tiempo'),\r
(609, 2, 'descripción'),\r
(610, 2, 'ahorrar'),\r
(611, 2, 'Agregar evento'),\r
(612, 2, 'recordatorio'),\r
(613, 2, 'minutos'),\r
(614, 2, 'horas'),\r
(616, 2, 'semana'),\r
(617, 2, 'huso horario'),\r
(618, 2, '(Sin eventos)'),\r
(619, 2, 'Lo sentimos, no se puede crear un evento que finaliza antes de que comience.'),\r
(620, 2, 'editar evento'),\r
(621, 2, 'Configurar las opciones de calendario de abajo'),\r
(622, 2, 'recordatorio por correo electrónico'),\r
(623, 2, 'Ver eventos de mouseover'),\r
(624, 2, 'Detalles del evento'),\r
(625, 2, 'más detalles'),\r
(626, 2, 'Lista de tareas pendientes'),\r
(627, 2, 'editar detalles'),\r
(628, 2, 'notas'),\r
(629, 2, 'Volver a la lista de tareas pendientes'),\r
(630, 2, 'hoy'),\r
(631, 2, 'terminado'),\r
(632, 2, 'pendiente'),\r
(633, 2, 'Que desea realizar esta operación. Pulse Aceptar para continuar.'),\r
(634, 2, 'hoy en día la lista de'),\r
(635, 2, 'Ver lista completa'),\r
(636, 2, 'Ver la lista de espera'),\r
(637, 2, 'Ver todos'),\r
(638, 2, 'Lista de Hoy Despejado'),\r
(639, 2, 'Borrar lista Completado'),\r
(640, 2, 'Borrar lista espera'),\r
(641, 2, 'Borrar lista'),\r
(642, 2, 'spam vacía'),\r
(643, 2, 'Vaciar papelera'),\r
(644, 2, 'Esta acción afectará a todas las conversaciones en la carpeta Spam. ¿Está seguro que desea continuar?'),\r
(645, 2, 'Esta acción afectará a todas las conversaciones en la papelera. ¿Está seguro que desea continuar?'),\r
(646, 2, 'imprimir'),\r
(647, 2, 'Atajos de teclado'),\r
(648, 2, 'Atajos del teclado en'),\r
(649, 2, 'Atajos de teclado Off'),\r
(650, 2, 'Más información'),\r
(651, 2, 'Atajos de teclado ayudan a ahorrar tiempo al permitir nunca que sacar las manos del teclado para utilizar el ratón.'),\r
(652, 2, 'próximos eventos'),\r
(653, 2, 'Elegir color Eventos'),\r
(654, 2, 'No hay eventos añadido'),\r
(600, 3, 'Votre message a été supprimé.'),\r
(601, 3, 'Retour au message'),\r
(602, 3, 'Inscription à'),\r
(603, 3, 'Pas de contacts à afficher.'),\r
(604, 3, 'Rapport sur le Spam'),\r
(605, 3, 'De la date de'),\r
(606, 3, 'De temps'),\r
(607, 3, 'À ce jour'),\r
(608, 3, 'Pour le temps'),\r
(609, 3, 'description de'),\r
(610, 3, 'Enregistrer'),\r
(611, 3, 'Ajouter un évènement'),\r
(612, 3, 'rappel'),\r
(613, 3, 'minutes'),\r
(614, 3, 'heures'),\r
(615, 3, 'jours'),\r
(616, 3, 'Semaine'),\r
(617, 3, 'fuseau horaire'),\r
(618, 3, '(Aucun événement)'),\r
(619, 3, 'Désolé, vous ne pouvez pas créer un événement qui se termine avant qu''il ne commence.'),\r
(620, 3, 'événement Modifier'),\r
(621, 3, 'Configurer les paramètres de votre calendrier ci-dessous'),\r
(622, 3, 'Rappel email'),\r
(623, 3, 'Voir les événements sur le mouseover'),\r
(624, 3, 'Détails de l''événement'),\r
(625, 3, 'Plus de détails'),\r
(626, 3, 'To-do List'),\r
(627, 3, 'Modifier les détails'),\r
(628, 3, 'Remarques'),\r
(629, 3, 'Retour à la liste À faire'),\r
(630, 3, 'aujourd''hui'),\r
(631, 3, 'Terminé'),\r
(632, 3, 'Dans l''attente'),\r
(633, 3, 'Vous souhaitez effectuer cette opération. Appuyez sur OK pour continuer.'),\r
(634, 3, 'Liste aujourd''hui'),\r
(635, 3, 'Voir la liste Terminé'),\r
(636, 3, 'Voir la liste attente'),\r
(637, 3, 'Voir tous'),\r
(638, 3, 'Liste Aujourd''hui Effacer'),\r
(639, 3, 'Effacer la liste Terminé'),\r
(640, 3, 'Effacer la liste attente'),\r
(641, 3, 'Effacer la liste'),\r
(642, 3, 'Spam vide'),\r
(643, 3, 'Vider la corbeille'),\r
(644, 3, 'Cette action aura une incidence sur toutes les conversations dans les spams. Etes-vous sûr de vouloir continuer?'),\r
(645, 3, 'Cette action aura une incidence sur toutes les conversations dans la corbeille. Etes-vous sûr de vouloir continuer?'),\r
(646, 3, 'Imprimer'),\r
(647, 3, 'Raccourcis clavier'),\r
(648, 3, 'Raccourcis clavier sur'),\r
(649, 3, 'Raccourcis clavier Off'),\r
(650, 3, 'En savoir plus'),\r
(651, 3, 'Les raccourcis clavier vous aider à gagner du temps en vous permettant de ne jamais prendre vos mains du clavier pour utiliser la souris.'),\r
(652, 3, 'Événements à venir'),\r
(653, 3, 'Choisir la couleur des événements'),\r
(654, 3, 'Aucun événement ajoutée'),\r
(655, 1, 'Keyword'),\r
(656, 1, 'Has attachment'),\r
(657, 1, 'Advanced Search'),\r
(658, 1, 'Hide advanced search'),\r
(659, 1, 'Not Spam'),\r
(660, 1, 'The selected conversations has been unmarked as spam'),\r
(661, 1, 'Discard Drafts'),\r
(655, 2, 'palabra clave'),\r
(656, 2, 'Contiene archivos adjuntos'),\r
(657, 2, 'Búsqueda avanzada'),\r
(658, 2, 'Ocultar búsqueda avanzada'),\r
(659, 2, 'no es spam'),\r
(660, 2, 'Las conversaciones seleccionado ha sido marcado como spam'),\r
(661, 2, 'descartar borradores'),\r
(655, 3, 'Mot-clé'),\r
(656, 3, 'a de l''attachement'),\r
(657, 3, 'Recherche avancée'),\r
(658, 3, 'Masquer la recherche avancée'),\r
(659, 3, 'Pas Spam'),\r
(660, 3, 'Les conversations sélectionné a été banalisée comme spam'),\r
(661, 3, 'Supprimer les brouillons'),\r
(662, 1, 'The selected conversation has been unmarked as spam'),\r
(663, 1, 'Folder'),\r
(662, 2, 'La conversación seleccionada ha sido marcado como spam'),\r
(663, 2, 'carpeta'),\r
(662, 3, 'La conversation sélectionnée a été banalisée comme spam'),\r
(663, 3, 'dossier'),\r
(664, 1, 'Import'),\r
(664, 2, 'importación'),\r
(664, 3, 'Importer'),\r
(665, 1, 'Enter login details to fetch your contacts'),\r
(665, 2, 'Ingrese a su cuenta en busca de sus contactos'),\r
(665, 3, 'Entrez les informations de connexion pour récupérer vos contacts'),\r
(666, 1, 'Fetch Contacts'),\r
(666, 2, 'buscar contactos'),\r
(666, 3, 'Fetch Contacts'),\r
(667, 1, 'Tip of the Day'),\r
(667, 2, 'Sugerencia del día'),\r
(667, 3, 'Astuce du jour'),\r
(668, 1, 'Invalid Search'),\r
(668, 2, 'inválida Buscar'),\r
(668, 3, 'Recherche non valide'),\r
(669, 1, 'Search Options'),\r
(669, 2, 'Opciones de Búsqueda'),\r
(669, 3, 'options de recherche'),\r
(750, 1, 'Get Libe Web Mail on Your Smartphone'),\r
(750, 2, 'Obtener Webmail Libe en tu Smartphone'),\r
(750, 3, 'Obtenez Webmail Libe sur ​​votre smartphone'),\r
(751, 1, 'Provide a good solution for those needing their emails on the go. Get your emails on your mobile from anywhere in the world.'),\r
(751, 2, 'Proporcionar una buena solución para aquellos que necesitan sus mensajes de correo electrónico sobre la marcha. Recibe tus correos electrónicos en tu móvil desde cualquier lugar del mundo.'),\r
(751, 3, 'Fournir une bonne solution pour ceux qui ont besoin de leurs e-mails en déplacement. Obtenez vos e-mails sur votre mobile à partir de n\\'importe où dans le monde.'),\r
(752, 1, 'Name'),\r
(752, 2, 'nombre'),\r
(752, 3, 'nom'),\r
(753, 1, 'Name cannot be left blank.'),\r
(753, 2, 'El nombre no puede dejarse en blanco.'),\r
(753, 3, 'Nom ne peut pas être laissée en blanc.'),\r
(754, 1, 'Forward your email to another email account.'),\r
(754, 2, 'Enviar su correo electrónico a otra cuenta de correo electrónico.'),\r
(754, 3, 'Veuillez faire parvenir votre courriel à un autre compte de messagerie.'),\r
(755, 1, 'Sends an automated reply to incoming messages.'),\r
(755, 2, 'Envía una respuesta automática a los mensajes entrantes.'),\r
(755, 3, 'Envoie un mail de réponse automatique aux messages entrants.'),\r
(756, 1, 'Select Theme'),\r
(756, 2, 'Seleccione Tema'),\r
(756, 3, 'Selectionnez Theme'),\r
(757, 1, 'Successfully Registered'),\r
(757, 2, 'registrado con éxito'),\r
(757, 3, 'Enregistré avec succès'),\r
(758, 1, 'to create an account.'),\r
(758, 2, 'para crear una cuenta.'),\r
(758, 3, 'pour créer un compte.'),\r
(759, 1, 'to Login.'),\r
(759, 2, 'para ingresar.'),\r
(759, 3, 'pour vous connecter.'),\r
(760, 1, 'Welcome'),\r
(760, 2, 'bienvenida'),\r
(760, 3, 'accueil'),\r
(761, 1, 'More'),\r
(761, 2, 'más'),\r
(761, 3, 'plus'),\r
(762, 1, 'Menu'),\r
(762, 2, 'menú'),\r
(762, 3, 'menu'),\r
(763, 1, 'Custom'),\r
(763, 2, 'costumbre'),\r
(763, 3, 'coutume'),\r
(764, 1, 'New'),\r
(764, 2, 'nuevo'),\r
(764, 3, 'nouveau'),\r
(765, 1, 'Include quoted text'),\r
(765, 2, 'Incluye texto de la cita'),\r
(765, 3, 'Inclure le texte cité'),\r
(766, 1, 'This file is 0 bytes, so it will not be attached.'),\r
(766, 2, 'Este archivo es de 0 bytes, por lo que no se adjunta.'),\r
(766, 3, 'Ce fichier est de 0 octets, de sorte qu\\'il ne sera pas attaché.'),\r
(767, 1, 'This file exceeds the maximum attachment size, so it will not be attached.'),\r
(767, 2, 'Este archivo supera el tamaño máximo de datos adjuntos, por lo que no se adjunta.'),\r
(767, 3, 'Ce fichier dépasse la taille maximale des pièces jointes, de sorte qu\\'il ne sera pas attaché.');\r
");
mysql_query("RENAME TABLE `" . $db_tableprefix . "nesote_email_themes` TO `" . $db_tableprefix . "nesote_email_themes_backup`;");
mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_themes` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,\r
  `style` longtext collate utf8_unicode_ci NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `thumb` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
\r
");
mysql_query("INSERT INTO `" . $db_tableprefix . "nesote_email_themes` (`name`, `style`, `status`, `thumb`) VALUES\r
('Theme1', 'url(images/themeDemo1.jpg) no-repeat right 0 #0e151d', 1, ''),\r
('Theme2', 'url(images/themeDemo2.jpg) no-repeat right 0 #5c739d', 1, ''),\r
('Theme3', 'url(images/themeDemo3.jpg) no-repeat right 0 #120809', 1, ''),\r
('Theme4', 'url(images/themeDemo4.jpg) no-repeat right 0 #091113', 1, ''),\r
('Theme5', 'url(images/themeDemo5.jpg) no-repeat right 0 #313131', 1, ''),\r
('Theme6', 'url(images/themeDemo6.jpg) no-repeat right 0 #124564', 1, ''),\r
('Theme7', 'url(images/themeDemo7.jpg) no-repeat right 0 #ffc5e9', 1, ''),\r
('Theme8', 'url(images/themeDemo8.jpg) no-repeat right 0 #030208', 1, ''),\r
('Theme9', 'url(images/themeDemo9.jpg) no-repeat right 0 #fee0a0', 1, ''),\r
('Theme10', 'url(images/themeDemo10.jpg) no-repeat right 0 #192511', 1, ''),\r
('Theme11', 'url(images/themeDemo11.jpg) no-repeat right 0 #cae2a6', 1, ''),\r
('Theme12', 'url(images/themeDemo12.jpg) no-repeat right 0 #957d5b', 1, ''),\r
('Theme13', 'url(images/themeDemo13.jpg) no-repeat right 0 #000000', 1, ''),\r
('Theme14', 'url(images/themeDemo14.jpg) no-repeat right 0 #000000', 1, ''),\r
('Theme15', 'url(images/themeDemo15.jpg) no-repeat right 0 #3f64b5', 1, ''),\r
('Theme16', 'url(images/themeDemo16.jpg) no-repeat right 0 #000000', 1, ''),\r
('Theme17', 'url(images/themeDemo17.jpg) no-repeat right 0 #5b8a22', 1, ''),\r
('Theme18', 'url(images/themeDemo18.jpg) no-repeat right 0 #091b29', 1, ''),\r
('Theme19', 'url(images/themeDemo19.jpg) no-repeat right 0 #d2e4e4', 1, ''),\r
('Theme20', 'url(images/themeDemo20.jpg) no-repeat right 0 #010206', 1, ''),\r
('Theme21', 'url(images/themeDemo21.jpg) no-repeat right 0 #010206', 1, ''),\r
('Theme22', 'url(images/themeDemo22.jpg) no-repeat right 0 #0f1901', 1, ''),\r
('Theme23', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -273px', 1, ''),\r
('Theme24', 'url(images/themeDemo-repeat.jpg) repeat-x 0 0', 1, ''),\r
('Theme25', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -544px', 1, ''),\r
('Theme26', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -681px', 1, ''),\r
('Theme27', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -818px', 1, ''),\r
('Theme28', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -137px', 1, ''),\r
('Theme29', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -955px', 1, ''),\r
('Theme30', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -1092px', 1, ''),\r
('Theme31', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -1229px', 1, ''),\r
('Theme32', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -408px', 1, ''),\r
('Theme33', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -1366px', 1, ''),\r
('Theme34', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -1503px', 1, ''),\r
('Theme35', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -1632px', 1, ''),\r
('Theme36', 'url(images/themeDemo-repeat.jpg) repeat-x 0 -1769px', 1, ''),\r
('Theme37', 'url(images/themeDemo2.png) no-repeat right 0 #1f2834', 1, '');\r
");
mysql_query("INSERT INTO `" . $db_tableprefix . "nesote_email_calendar_settings` (`name`, `value`) VALUES\r
('email_remainder', 1),\r
('view_event',0),\r
('calendar', 1);\r
");
mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_liberyus_users` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `username` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `password` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `name` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `email` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  `joindate` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
mysql_query("ALTER TABLE `" . $db_tableprefix . "nesote_email_usersettings` CHANGE `lang_id` `lang_id` varchar(256) collate utf8_unicode_ci NOT NULL");
mysql_query("ALTER TABLE `" . $db_tableprefix . "nesote_email_usersettings`\r
ADD (`sex` varchar(256) NOT NULL,\r
  `dateofbirth` mediumtext NOT NULL,\r
  `country` mediumtext NOT NULL,\r
  `remember_question` mediumtext NOT NULL,\r
  `remember_answer` mediumtext NOT NULL,\r
  `lastlogin` int(11) NOT NULL,\r
  `memorysize` float NOT NULL,\r
  `server_password` mediumtext NOT NULL,\r
  `time_zone` mediumtext NOT NULL,\r
  `smtp_username` mediumtext NOT NULL,\r
  `alternate_email` mediumtext NOT NULL );\r
  	\r
  	");
mysql_query("ALTER TABLE `" . $db_tableprefix . "nesote_email_languages`\r
ADD (`lang_code` varchar(256) NOT NULL);	\r
  	");
$resultset = mysql_query("select id,username,password,firstname,lastname,sex,dateofbirth,country,remember_question,remember_answer,createdtime,lastlogin,status,memorysize,server_password,time_zone,alternate_email,smtp_username  from " . $db_tableprefix . "nesote_users");
while ($rw = mysql_fetch_row($resultset))
{
	$name = $rw[3] . " " . $rw[4];
	mysql_query("INSERT INTO `" . $db_tableprefix . ('nesote_liberyus_users` (`id`,`username`,`password`,`name`,`email`,`joindate`,`status`) VALUES(\'' . $rw["0"] . "','" . $rw["1"] . "','" . $rw["2"] . "','" . $name . "','','" . $rw["10"] . "','" . $rw["12"] . "')"));
	mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_usersettings` SET `sex` = '" . $rw[5] . "',`dateofbirth` = '" . $rw[6] . "',`country` = '" . $rw[7] . "',`remember_question` = '" . $rw[8] . "',`remember_answer` = '" . $rw[9] . "',`lastlogin` = '" . $rw[11] . ('\',`memorysize` = \'' . $rw["13"] . "',`server_password` = '") . $rw[14] . "',`time_zone` = '" . $rw[15] . "',`smtp_username` = '" . $rw[17] . "',`alternate_email` = '" . $rw[16] . "' where userid='" . $rw[0] . "';");
}
echo "\r
\r
\r
<table  style=\"width:50%;height:50%;background-color: ;padding-left: 20px;padding-top: 50px;\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r
<tr><td>&nbsp;</td></tr>\r
<tr><td >";
echo "<s";
echo "pan class=\"logossubtitle\">Libe Web Mail Ultimate Version is successfully installed. Go to admin area and configure your language code. </span><div style=\"padding-left: 150px;padding-top: 20px;\"><a href=\"../admin/\" title=\"Login to Admin Area.\" class=\"logos\">Login to Admin Area.</a></div></td></tr>\r
<tr><td>&nbsp;</td></tr>\r
</table>\r
</body>\r
</html>";



?>
