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


set_time_limit(0);
$yr = date("Y", time());
echo "<s";
echo "tyle type=\"text/css\">\r
body {\r
	background-image: url(images/bg.gif);\r
	font-family: Verdana, Arial, Helvetica, sans-serif;\r
	font-size: 12px;\r
}\r
\r
table tr,td {\r
	font-family: Verdana, Arial, Helvetica, sans-serif;\r
	font-size: 13px;\r
	font-weight: bold;\r
}\r
\r
.bgstyle {\r
	background-image: url(images/bg1.gif);\r
	background-repeat: no-repeat;\r
}\r
\r
.headerstyle {\r
	font-family: Verdana, Arial, H";
echo "elvetica, sans-serif;\r
	font-size: 16px;\r
	font-weight: bold;\r
	color: #FFFFFF;\r
}\r
\r
.bgstylefooter {\r
	font-family: Verdana, Arial, Helvetica, sans-serif;\r
	font-size: 12px;\r
	color: #FFFFFF;\r
}\r
</style>\r
<body>\r
\r
<table width=\"100%\"  height=\"100%\" border=\"0\">\r
  <tr  height=\"17%\">\r
    <td width=\"25%\">&nbsp;</td>\r
    <td width=\"50%\">&nbsp;</td>\r
    <td width=\"25%\">&nbsp;</td>\r
  </tr>\r
  <tr  height=\"5";
echo "0%\">\r
    <td>&nbsp;</td>\r
    <td>\r
\r
<table height=\"439\" width=\"697\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"bgstyle\">\r
			<tr>\r
				<td width=\"40\" height=\"100px\">&nbsp;</td>\r
				<td width=\"602\" class=\"headerstyle\">";
echo "<s";
echo "trong>Welcome to Libe\r
				Webmail Ultimate Version Installation </strong></td>\r
				<td width=\"40\">&nbsp;</td>\r
			</tr>\r
\r
			<tr>\r
				<td height=\"250px\">&nbsp;</td>\r
				<td valign=\"top\" align=\"left\" style=\"padding-left: 50px;\">";
$step = $_GET["step"];
if ($step == 1 || $step == "") {
    require __DIR__ . "/../script.inc.php";
    include "../" . $config_path . "database.default.config.php";
    error_reporting(0);
    $link = mysql_connect($db_server, $db_username, $db_password);
    if (!(mysql_select_db($db_name)))
	{
		echo "<span style=\"color:#ff0000;\">Database connection failed! Please verify the connection settings in the configuration file</span>\r
\r
			</td>\r
			\r
			  <td>&nbsp;</td>\r
			</tr>\r
			<tr height=\"110px\">\r
			<td>&nbsp;</td>\r
			<td valign=\"top\" align=\"right\">\r
			\r
			</td>\r
			<td>&nbsp;</td>\r
			</tr>\r
			</table>\r
			\r
			<table width=\"100%\"  height=\"20px;\"  class=\"bgstylefooter\">\r
			<tr>\r
			<td align=\"center\">Copyright &copy;" . $yr . "Libescripts.com. All Rights Reserved.\r
			</td>\r
			</tr>\r
			</table>\r
			</td>\r
				<td>&nbsp;</td>\r
			  </tr>\r
			  <tr  height=\"25%\">\r
				<td >&nbsp;</td>\r
				<td>&nbsp;</td>\r
				<td>&nbsp;</td>\r
			  </tr>\r
			</table>\r
			";
		die(0);
	}
    $folder_already = is_dir("../attachments") ? 1 : 0;
    if (mkdir("../attachments/temp_attachments", 511))
	{
		sleep(2);
		rmdir("../attachments/temp_attachments");
		include "../config/system.config.php";
		mkdir("../attachments", 511);
		mkdir("../attachments/1", 511);
		mkdir("../attachments/2", 511);
		mkdir("../attachments/3", 511);
		mkdir("../attachments/4", 511);
		mkdir("../attachments/5", 511);
		if (!(is_dir("../admin/logo")))
		{
			mkdir("../admin/logo", 511);
		}
		if (!(is_dir("../admin/logo/chat_sound")))
		{
			mkdir("../admin/logo/chat_sound", 511);
		}
		if (!(is_dir("../userdata")))
		{
			mkdir("../userdata", 511);
		}
		if ($license_key != "")
		{
			print "*&nbsp; <span style=\"color:#000;\">Database connection settings has been  verified successfully</span><br><br>";
			print "*&nbsp; <span style=\"color:#000;\">Folder permissions has been  verified successfully</span><br><br>";
			mysql_close($link);
			$next = "2";
			$previous = "0";
		}
		 else 
		{
			echo "<span style=\"color:#ff0000;\">You may please specify the license key value in the system.config.php file in  config folder! Then only you are allowed   to access the script</span>\r
									\r
												</td>\r
												\r
												  <td>&nbsp;</td>\r
												</tr>\r
												<tr height=\"110px\">\r
												<td>&nbsp;</td>\r
												<td valign=\"top\" align=\"right\">\r
												\r
												</td>\r
												<td>&nbsp;</td>\r
												</tr>\r
												</table>\r
												\r
												<table width=\"100%\"  height=\"20px\" class=\"bgstylefooter\" >\r
												<tr>\r
												<td align=\"center\">Copyright &copy; " . $yr . " Libescripts.com. All Rights Reserved.\r
												</td>\r
												</tr>\r
												</table>\r
												</td>\r
													<td>&nbsp;</td>\r
												  </tr>\r
												  <tr  height=\"25%\">\r
													<td >&nbsp;</td>\r
													<td>&nbsp;</td>\r
													<td>&nbsp;</td>\r
												  </tr>\r
												</table>\r
												";
		}
	}
	 else 
	{
		echo "<span style=\"color:#ff0000;\">Folder access failed! Please verify the permission of attachments folder as 0777 in the root folder of the script </span>\r
				\r
						</td>\r
							\r
							  <td>&nbsp;</td>\r
							</tr>\r
							<tr height=\"110px\">\r
							<td>&nbsp;</td>\r
							<td valign=\"top\" align=\"right\">\r
							\r
							</td>\r
							<td>&nbsp;</td>\r
							</tr>\r
							</table>\r
							<table width=\"100%\"  height=\"20px\" class=\"bgstylefooter\" >\r
							<tr>\r
							<td align=\"center\">Copyright &copy; " . $yr . " Libescripts.com. All Rights Reserved.\r
							</td>\r
							</tr>\r
							</table>\r
							\r
							</td>\r
								<td>&nbsp;</td>\r
							  </tr>\r
							  <tr  height=\"25%\">\r
								<td >&nbsp;</td>\r
								<td>&nbsp;</td>\r
								<td>&nbsp;</td>\r
							  </tr>\r
							</table>\r
							";
		die(0);
	}
} elseif ($step == 2) {
    require __DIR__ . "/../script.inc.php";
    require "../" . $config_path . "system.config.php";
    require "../config/system.config.php";
    validatelicense($license_key);
    require __DIR__ . "/../script.inc.php";
    include "../" . $config_path . "database.default.config.php";
    error_reporting(0);
    $link = mysql_connect($db_server, $db_username, $db_password);
    mysql_query("set names utf8 collate utf8_unicode_ci");
    mysql_select_db($db_name);
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_admin` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `username` varchar(256) NOT NULL,\r
  `password` varchar(256) NOT NULL,\r
  `lastlogin` int(11) NOT NULL,\r
  `status` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\r
");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_blacklist_mail` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `mailid` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `clientid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_blacklist_server` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `server` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `clientid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_client_logs` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `uid` int(11) NOT NULL,\r
  `operation` varchar(256) NOT NULL,\r
  `comment` varchar(256) NOT NULL,\r
  `time` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_contactgroup` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `userid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_contacts` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `mailid` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `addedby` int(11) NOT NULL,\r
  `contactgroup` int(11) NOT NULL,\r
  `firstname` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `lastname` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `date_of_birth` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `title` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `company` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `phone` bigint(20) NOT NULL,\r
  `address` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `website` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE INDEX addedby\r
on `" . $db_tableprefix . "nesote_email_contacts` (addedby,contactgroup)");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_country` (\r
  `code` varchar(10) collate utf8_unicode_ci NOT NULL,\r
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`code`)\r
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_customfolder` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `userid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;\r
");
    mysql_query("CREATE INDEX userid\r
on `" . $db_tableprefix . "nesote_email_customfolder` (userid)");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_emailfilters` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `from_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `fromflag` int(11) NOT NULL,\r
  `subjectflag` int(11) NOT NULL,\r
  `bodyflag` int(11) NOT NULL,\r
  `folderid` int(11) NOT NULL,\r
  `userid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_languages` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `language` varchar(255) collate utf8_unicode_ci NOT NULL,\r
  `language_script` mediumtext collate utf8_unicode_ci NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `char_encoding` varchar(255) collate utf8_unicode_ci default NULL,\r
  `lang_alignment` smallint(6) NOT NULL default '1',\r
  `image` varchar(255) collate utf8_unicode_ci default NULL,
  `lang_code` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_messages` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `msg_id` int(11) NOT NULL,\r
  `lang_id` int(11) NOT NULL,\r
  `wordscript` mediumtext collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_months_messages` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `month_id` int(11) NOT NULL,\r
  `lang_id` int(11) NOT NULL,\r
  `message` mediumtext collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_reservedemail` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `status` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_settings` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `value` longtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_spamserver_settings` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_spam_settings` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `from_id` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `body` longtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `fromflag` int(11) NOT NULL,\r
  `subjectflag` int(11) NOT NULL,\r
  `bodyflag` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_time_zone` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `value` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
\r
");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_usersettings` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `signature` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,
  `signatureflag` int(11) NOT NULL,
  `lang_id` varchar(256) collate utf8_unicode_ci NOT NULL,
  `theme_id` int(11) NOT NULL,
  `display` int(11) NOT NULL,
  `forward_flag` int(11) NOT NULL,
  `forward_mail` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,
  `autoreply_flag` int(11) NOT NULL,
  `autoreply_msg` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,
  `autoreply_subject` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,
  `autoreply_send_flag` int(11) NOT NULL,
  `external_content` int(11) NOT NULL,
  `mails_per_page` int(11) NOT NULL,
  `totalinvitation` int(11) NOT NULL,
  `email_remainder` int(11) NOT NULL,
  `view_event` int(11) NOT NULL,
  `shortcuts` int(11) NOT NULL,
  `sex` varchar(256) NOT NULL,
  `dateofbirth` mediumtext NOT NULL,
  `country` mediumtext NOT NULL,
  `remember_question` mediumtext NOT NULL,
  `remember_answer` mediumtext NOT NULL,
  `lastlogin` int(11) NOT NULL,
  `memorysize` float NOT NULL,
  `server_password` mediumtext NOT NULL,
  `time_zone` mediumtext NOT NULL,
  `smtp_username` mediumtext NOT NULL,
  `alternate_email` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_whitelist_mail` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `mailid` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `clientid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_whitelist_server` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `server` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `clientid` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_image_display` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `mailid` mediumtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
");
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
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_liberyus_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(256) collate utf8_unicode_ci NOT NULL,
  `password` varchar(256) collate utf8_unicode_ci NOT NULL,
  `name` varchar(256) collate utf8_unicode_ci NOT NULL,
  `email` varchar(256) collate utf8_unicode_ci NOT NULL,
  `joindate` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
\r
");
    mysql_query("CREATE INDEX status\r
on `" . $db_tableprefix . "nesote_liberyus_users` (status)");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_chatwindow_settings` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` varchar(256) NOT NULL,\r
  `width` int(255) NOT NULL,\r
  `height` int(255) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_chat_contact` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `sender` int(11) NOT NULL,\r
  `receiver` int(11) NOT NULL,\r
  `nickname` varchar(256) NOT NULL,\r
  `status` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
			");
    $i = 1;
    while ($i <= 100)
		{
			mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . ('nesote_chat_message_' . $i . "` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `chat_id` int(11) NOT NULL,\r
  `receivers` varchar(256) NOT NULL,\r
  `message` longtext character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `read_flag` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
			"));
			$i++;
		}
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_chat_session` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `time` int(11) NOT NULL,\r
  `xml_status` int(11) NOT NULL,\r
  `group_status` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
			");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_chat_session_users` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `chat_id` int(11) NOT NULL,\r
  `user_id` int(11) NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `xml_status` int(11) NOT NULL,\r
  `typing_status` int(11) NOT NULL,\r
  `active_status` int(11) NOT NULL,\r
  `present_identified_time` int(11) NOT NULL,\r
  `initiators` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
			");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_chat_settings` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` varchar(256) NOT NULL,\r
  `value` varchar(256) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
			");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_chat_temporary_messages` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `chat_id` int(11) NOT NULL,\r
  `sender` int(11) NOT NULL,\r
  `responders` varchar(256) NOT NULL,\r
  `message` text character set utf8 collate utf8_unicode_ci NOT NULL,\r
  `time` int(11) NOT NULL,\r
  `read_flag` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
\r
			");
    mysql_query("CREATE INDEX chat_id\r
on `" . $db_tableprefix . "nesote_chat_temporary_messages` (chat_id,sender,responders,read_flag)");
    mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_chat_users` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `userid` int(11) NOT NULL,\r
  `image` varchar(256) NOT NULL,\r
  `custom_message` longtext NOT NULL,\r
  `logout_status` int(11) NOT NULL,\r
  `chat_status` int(11) NOT NULL,\r
  `login_time` int(11) NOT NULL,\r
  `chatframesize` int(11) NOT NULL,\r
  `idle` int(11) NOT NULL,\r
  `lastupdatedtime` int(11) NOT NULL,\r
  `chatwindowsize` int(11) NOT NULL,\r
  `chathistory` int(11) NOT NULL,\r
  `sounds` int(11) NOT NULL,\r
  `soundspath` varchar(256) NOT NULL,\r
  `smileys` int(11) NOT NULL,\r
  `signout` int(11) NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r
			\r
			\r
			");
    mysql_query("CREATE INDEX userid\r
on `" . $db_tableprefix . "nesote_chat_users` (userid)");
    include __DIR__ . "/support.php";
    $next = "3";
    $previous = "1";
    echo " ";
    echo "<s";
    echo "cript type=\"text/javascript\">\r
					 \r
					function trim(stringValue)\r
					{\r
									return stringValue.replace(/(^\\s*|\\s*\$)/, \"\");\r
					}\r
					\r
					function isNotNull(strString)\r
				 \r
				   {\r
				\r
				   \r
									if (trim(strString).length == 0) return false;\r
				   \r
					\r
					\r
					}\r
				\r
					  function isEmail(emailString)\r
					\r
					  {\r
					   \r
								 if (emailStr";
    echo "ing.length == 0) return true;\r
							   \r
								 var filter=/^([\\w-]+(?:\\.[\\w-]+)*)@((?:[\\w-]+\\.)*\\w[\\w-]{0,66})\\.([a-z]{2,6}(?:\\.[a-z]{2})?)\$/i\r
								 \r
									if (filter.test(emailString))\r
										return true;\r
									else\r
										return false;					\r
						\r
					}\r
\r
					function isSame(strString1, strString2)\r
					//checks wheteher both field values are same\r
					{   \r
					";
    echo "			if (strString1 != strString2 ) return false;\r
					}\r
\r
\r
					function verifyForm_editprofile()\r
					{\r
					\r
								if(isNotNull(document.editprofile.username.value)==false)\r
								{	\r
									alert(\"Username cannot be null.\");\r
									document.editprofile.username.focus();\r
									return false;\r
								}\r
								\r
								if(isNotNull(document.editprofile.password.value)==false) ";
    echo "\r
								{	\r
									alert(\"Password cannot be null.\");\r
									document.editprofile.password.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.editprofile.confirmpassword.value)==false) \r
								{	\r
									alert(\"Confirm password cannot be null.\");\r
									document.editprofile.confirmpassword.focus();\r
									return false;\r
								}\r
								if(isSame(document";
    echo ".editprofile.password.value,document.editprofile.confirmpassword.value)==false) \r
								{	\r
									alert(\"confirm password must be same as password\");\r
									document.editprofile.password.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.editprofile.email_extension.value)==false)\r
								 {	\r
									alert(\"Email Extension cannot be null.\");\r
									document.edi";
    echo "tprofile.email_extension.focus();\r
									return false;\r
								}\r
								\r
								\r
								\r
								if(isNotNull(document.editprofile.enginename.value)==false)\r
								 {	\r
									alert(\"Enginename cannot be null.\");\r
									document.editprofile.enginename.focus();\r
									return false;\r
								}	\r
								\r
								\r
								if(isNotNull(document.editprofile.adminemail.value)==fal";
    echo "se)\r
								 {	\r
							\r
									alert(\"Email cannot be null.\");\r
									document.editprofile.adminemail.focus();\r
									return false;\r
								}	\r
												\r
								if(isEmail(document.editprofile.adminemail.value)==false)\r
								 {	\r
								 	\r
									alert(\"Please enter a valid email address.\");\r
									document.editprofile.adminemail.focus();\r
									return false;\r
							";
    echo "	}\r
								\r
								\r
									document.getElementById('editprofile').submit();\r
									return false;			\r
								\r
								\r
\r
}\r
\r
					\r
					\r
					\r
					\r
</script> ";
    if ($step == 2)
		{
			require __DIR__ . "/../script.inc.php";
			include "../" . $config_path . "database.default.config.php";
			error_reporting(0);
			$link = mysql_connect($db_server, $db_username, $db_password);
			mysql_select_db($db_name);
			$resultset = mysql_query("select username from  " . $db_tableprefix . "nesote_email_admin");
			$row = mysql_fetch_row($resultset);
			$resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='emailextension'");
			$row1 = mysql_fetch_row($resultset1);
			$resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='engine_name'");
			$row2 = mysql_fetch_row($resultset2);
			$resultset3 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='adminemail'");
			$row3 = mysql_fetch_row($resultset3);
		}
    echo "\r
				<form name=\"editprofile\" id=\"editprofile\"\r
					enctype=\"multipart/form-data\" method=\"post\"\r
					action=\"install.php?step=3\">\r
\r
				<table>\r
					<tr>\r
						<td valign=\"top\" height=\"32px\" align=\"right\">&nbsp;</td>\r
						<td valign=\"top\" height=\"32px\" align=\"left\">Administrator\r
						Information</td>\r
					</tr>\r
					<tr>\r
						<td valign=\"top\" height=\"32px\" align=\"right\" colspan=\"2\">";
    echo $msg;
    echo "</td>\r
					</tr>\r
					<tr>\r
						<td valign=\"top\" height=\"32px\" align=\"right\">\r
						Username&nbsp;&nbsp;:&nbsp;&nbsp;</td>\r
						<td valign=\"top\" height=\"32px\" align=\"left\"><input name=\"username\"\r
							type=\"text\" size=\"40\" value=\"";
    echo $row[0];
    echo "\"></td>\r
					</tr>\r
					<tr>\r
\r
						<td valign=\"top\" height=\"32px\" align=\"right\">\r
						Password&nbsp;&nbsp;:&nbsp;&nbsp;</td>\r
						<td valign=\"top\" height=\"32px\" align=\"left\"><input name=\"password\"\r
							type=\"password\" size=\"40\" value=\"\"></td>\r
					</tr>\r
\r
					<tr>\r
\r
						<td valign=\"top\" height=\"32px\" align=\"right\">Confirm\r
						Password&nbsp;&nbsp;:&nbsp;&nbsp;</td>\r
						<td valign=\"top\" h";
    echo "eight=\"32px\" align=\"left\"><input\r
							name=\"confirmpassword\" type=\"password\" size=\"40\" value=\"\"></td>\r
					</tr>\r
\r
					<tr>\r
\r
						<td valign=\"top\" height=\"32px\" align=\"right\">Email\r
						Extension&nbsp;&nbsp;:&nbsp;&nbsp;</td>\r
						<td valign=\"top\" height=\"32px\" align=\"left\"><input\r
							name=\"email_extension\" type=\"text\" size=\"40\"\r
							value=\"";
    echo $row1[0];
    echo "\"></td>\r
					</tr>\r
\r
					<tr>\r
\r
						<td valign=\"top\" height=\"32px\" align=\"right\">Webmail Service\r
						Name&nbsp;&nbsp;:&nbsp;&nbsp;</td>\r
						<td valign=\"top\" height=\"32px\" align=\"left\"><input\r
							name=\"enginename\" type=\"text\" size=\"40\"\r
							value=\"";
    echo $row2[0];
    echo "\"></td>\r
					</tr>\r
\r
					<tr>\r
\r
						<td valign=\"top\" height=\"32px\" align=\"right\">Admin\r
						email&nbsp;&nbsp;:&nbsp;&nbsp;</td>\r
						<td valign=\"top\" height=\"32px\" align=\"left\"><input\r
							name=\"adminemail\" type=\"text\" size=\"40\"\r
							value=\"";
    echo $row3[0];
    echo "\"> <input type=\"hidden\"\r
							id=\"install_step\" name=\"install_step\" value=\"3\"></td>\r
					</tr>\r
\r
\r
				</table>\r
				</form>\r
\r
\r
\r
				";
} elseif ($step == 3) {
    $username = trim((string) $_POST["username"]);
    $password = trim((string) $_POST["password"]);
    $email_extension = trim((string) $_POST["email_extension"]);
    $enginename = trim((string) $_POST["enginename"]);
    $adminemail = trim((string) $_POST["adminemail"]);
    if ($username !== "")
			{
				if ($password !== "")
				{
					if ($email_extension !== "")
					{
						if ($enginename !== "")
						{
							if ($adminemail !== "")
							{
								$t = time();
								$passwordmd5 = md5($password);
								require __DIR__ . "/../script.inc.php";
								include "../" . $config_path . "database.default.config.php";
								error_reporting(0);
								$link = mysql_connect($db_server, $db_username, $db_password);
								mysql_select_db($db_name);
								mysql_query("INSERT `" . $db_tableprefix . ('nesote_email_admin` (`username`,`password`,`lastlogin`,`status`) VALUES(\'' . $username . "','" . $passwordmd5 . "','" . $t . "','1')"));
								mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $email_extension . "' where name='emailextension';\r
										");
								mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $enginename . "' where name='engine_name';\r
										");
								mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $adminemail . "' where name='adminemail';\r
										");
							}
							 else 
							{
								$msg = "Admin email cannot be blank";
							}
						}
						 else 
						{
							echo $msg = "Enginename cannot be blank";
						}
					}
					 else 
					{
						echo $msg = "Email cannot be blank";
					}
				}
				 else 
				{
					echo $msg = "Password cannot be blank";
				}
			}
			 else 
			{
				echo $msg = "Username cannot be blank";
			}
    require __DIR__ . "/../script.inc.php";
    include "../" . $config_path . "database.default.config.php";
    error_reporting(0);
    $link = mysql_connect($db_server, $db_username, $db_password);
    mysql_select_db($db_name);
    $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='catchall_mail'");
    $catch_all = mysql_fetch_row($resultset2);
    if ($catch_all[0] == 1) {
        $resultset = mysql_query("select value from " . $db_tableprefix . "nesote_email_settings where name='SMTP_username'");
        $catch_smtp_username = mysql_fetch_row($resultset);
        $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='SMTP_password'");
        $catch_smtp_password = mysql_fetch_row($resultset1);
        $resultset = mysql_query("select value from " . $db_tableprefix . "nesote_email_settings where name='SMTP_host'");
        $catch_smtp_host = mysql_fetch_row($resultset);
        $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='SMTP_port'");
        $catch_smtp_port = mysql_fetch_row($resultset1);
        $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='pop3_servername'");
        $catch_pop3_servername = mysql_fetch_row($resultset2);
        $resultset3 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='pop3_serveremail'");
        $catch_pop3_serveremail = mysql_fetch_row($resultset3);
        $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='pop3_serverpassword'");
        $catch_pop3_serverpassword = mysql_fetch_row($resultset1);
    } elseif ($catch_all[0] == 0) {
        $resultset3 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='automatic_account_creation'");
        $auto_acc_creation = mysql_fetch_row($resultset3);
        if ($auto_acc_creation[0] == 1) {
            $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='controlpanel'");
            $controlpanel = mysql_fetch_row($resultset1);
            if ($controlpanel[0] == 1) {
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='controlpanel_version'");
                $cpanel_controlpanel_version = mysql_fetch_row($resultset2);
                $resultset3 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_username'");
                $cpanel_domain_username = mysql_fetch_row($resultset3);
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_password'");
                $cpanel_domain_password = mysql_fetch_row($resultset2);
                $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_name'");
                $cpanel_domain_name = mysql_fetch_row($resultset1);
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_ip'");
                $cpanel_domain_ip = mysql_fetch_row($resultset2);
                $resultset = mysql_query("select value from " . $db_tableprefix . "nesote_email_settings where name='SMTP_host'");
                $cpanel_smtp_host = mysql_fetch_row($resultset);
                $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='SMTP_port'");
                $cpanel_smtp_port = mysql_fetch_row($resultset1);
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='pop3_servername'");
                $cpanel_pop3_servername = mysql_fetch_row($resultset2);
            } elseif ($controlpanel[0] == 2) {
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='controlpanel_version'");
                $plesk_controlpanel_version = mysql_fetch_row($resultset2);
                $resultset3 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='plesk_packetversion'");
                $plesk_packetversion = mysql_fetch_row($resultset3);
                $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='plesk_domainid'");
                $plesk_domainid = mysql_fetch_row($resultset1);
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_password'");
                $plesk_domain_password = mysql_fetch_row($resultset2);
                $resultset3 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_username'");
                $plesk_domain_username = mysql_fetch_row($resultset3);
                $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_name'");
                $plesk_domain_name = mysql_fetch_row($resultset1);
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='domain_ip'");
                $plesk_domain_ip = mysql_fetch_row($resultset2);
                $resultset = mysql_query("select value  from " . $db_tableprefix . "nesote_email_settings where name='SMTP_host'");
                $plesk_smtp_host = mysql_fetch_row($resultset);
                $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='SMTP_port'");
                $plesk_smtp_port = mysql_fetch_row($resultset1);
                $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='pop3_servername'");
                $plesk_pop3_servername = mysql_fetch_row($resultset2);
            }
        } elseif ($auto_acc_creation[0] == 0) {
            $resultset = mysql_query("select value  from " . $db_tableprefix . "nesote_email_settings where name='SMTP_host'");
            $manually_smtp_host = mysql_fetch_row($resultset);
            $resultset1 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='SMTP_port'");
            $manually_smtp_port = mysql_fetch_row($resultset1);
            $resultset2 = mysql_query("select value from  " . $db_tableprefix . "nesote_email_settings where name='pop3_servername'");
            $manually_pop3_servername = mysql_fetch_row($resultset2);
        }
    }
    $next = "4";
    $previous = "2";
    echo " ";
    echo "<s";
    echo "cript type=\"text/javascript\">\r
			function catchallfun()\r
			{\r
			document.getElementById('mode1').value=1;\r
			document.getElementById(\"catchall\").style.display=\"\";\r
			document.getElementById(\"individual\").style.display=\"none\";\r
		\r
			}\r
			function individualfun()\r
			{\r
		\r
			document.getElementById('mode1').value=2;\r
			document.getElementById(\"catchall\").style.display=\"none\";	\r
			documen";
    echo "t.getElementById(\"individual\").style.display=\"\";\r
			}\r
			function automaticfun()\r
			{\r
			document.getElementById('mode2').value=1;\r
			document.getElementById(\"manually\").style.display=\"none\";\r
			document.getElementById(\"automatic\").style.display=\"\";\r
			if(document.getElementById(\"mode3\").value==1)\r
			document.getElementById(\"cpanel\").style.display=\"\";\r
			else\r
			document.getElementById(\"";
    echo "plesk\").style.display=\"\";\r
			\r
		\r
			}\r
			function manuallyfun()\r
			{\r
			document.getElementById('mode2').value=2;\r
			document.getElementById(\"automatic\").style.display=\"none\";\r
			document.getElementById(\"plesk\").style.display=\"none\";\r
			document.getElementById(\"manually\").style.display=\"\";\r
		\r
			}\r
			function cpanelfun()\r
			{\r
			document.getElementById('mode3').value=1;\r
			document.";
    echo "getElementById(\"cpanel\").style.display=\"\";\r
			document.getElementById(\"plesk\").style.display=\"none\";\r
		\r
			}\r
			function pleskfun()\r
			{\r
			document.getElementById('mode3').value=2;\r
			document.getElementById(\"cpanel\").style.display=\"none\";\r
			document.getElementById(\"plesk\").style.display=\"\";\r
		\r
			}\r
			function trim(stringValue)\r
					{\r
									return stringValue.replace(/(^\\s*|\\s*\$";
    echo ")/, \"\");\r
					}\r
					\r
					function isNotNull(strString)\r
				 \r
				   {\r
				\r
				   \r
									if (trim(strString).length == 0) return false;\r
				   \r
					\r
					\r
					}\r
				\r
					  function isEmail(emailString)\r
					\r
					  {\r
					   \r
								 if (emailString.length == 0) return true;\r
							   \r
								 var filter=/^([\\w-]+(?:\\.[\\w-]+)*)@((?:[\\w-]+\\.)*\\w[\\w-]{0,66})\\.([a-z]{2,6";
    echo "}(?:\\.[a-z]{2})?)\$/i\r
								 \r
									if (filter.test(emailString))\r
										return true;\r
									else\r
										return false;					\r
						\r
					}\r
\r
					function isSame(strString1, strString2)\r
					//checks wheteher both field values are same\r
					{   \r
								if (strString1 != strString2 ) return false;\r
					}\r
			function verifyForm_finalform()\r
			{\r
		\r
			if(document.getElemen";
    echo "tById('mode1').value==1)//catch all\r
								{	\r
\r
								\r
				if(isNotNull(document.finalform.smtp_username.value)==false)\r
								{				\r
									alert(\"Smtp Username cannot be null.\");\r
									document.finalform.smtp_username.focus();\r
									return false;\r
								}\r
							\r
								if(isNotNull(document.finalform.smtp_password.value)==false) \r
								{	\r
									alert(\"Smtp Password";
    echo " cannot be null.\");\r
									document.finalform.smtp_password.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.finalform.smtp_host_catchall.value)==false) \r
								{	\r
									alert(\"Smtp Host cannot be null.\");\r
									document.finalform.smtp_host_catchall.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.finalform.smtp_port_catchall.value)=";
    echo "=false)\r
								 {	\r
									alert(\"Smtp Port cannot be null.\");\r
									document.finalform.smtp_port_catchall.focus();\r
									return false;\r
								}\r
								\r
								\r
								\r
								if(isNotNull(document.finalform.pop3_servername.value)==false)\r
								 {	\r
									alert(\"Pop3 Servername cannot be null.\");\r
									document.finalform.pop3_servername.focus();\r
									return fal";
    echo "se;\r
								}	\r
								\r
								\r
								if(isNotNull(document.finalform.pop3_serveremail.value)==false)\r
								 {	\r
							\r
									alert(\"Pop3 Serveremail cannot be null.\");\r
									document.finalform.pop3_serveremail.focus();\r
									return false;\r
								}	\r
								\r
								if(isEmail(document.finalform.pop3_serveremail.value)==false)\r
								 {	\r
							\r
									alert(\"Pleas";
    echo "e Enter a valid Pop3 Serveremail.\");\r
									document.finalform.pop3_serveremail.focus();\r
									return false;\r
								}	\r
												\r
								if(isNotNull(document.finalform.pop3_serverpassword.value)==false)\r
								 {	\r
								 	\r
									alert(\"Pop3 Serverpassword cannot be null.\");\r
									document.finalform.pop3_serverpassword.focus();\r
									return false;\r
								}\r
							";
    echo "}\r
							else if(document.getElementById('mode1').value==2)//individual\r
							{\r
							if(document.getElementById('mode2').value==1)//automatically\r
							{\r
							if(document.getElementById('mode3').value==1)//cpanel\r
							{\r
								\r
								\r
							if(isNotNull(document.finalform.cPanel_version.value)==false)\r
								{	\r
									alert(\"cPanel Version cannot be null.\");\r
									doc";
    echo "ument.finalform.cPanel_version.focus();\r
									return false;\r
								}\r
								\r
								if(isNotNull(document.finalform.cPanel_username.value)==false) \r
								{	\r
									alert(\"cPanel Username cannot be null.\");\r
									document.finalform.cPanel_username.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.finalform.cPanel_password.value)==false) \r
								{	\r
			";
    echo "						alert(\"cPanel Password cannot be null.\");\r
									document.finalform.cPanel_password.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.finalform.cPanel_name.value)==false)\r
								 {	\r
									alert(\"Domain Name cannot be null.\");\r
									document.finalform.cPanel_name.focus();\r
									return false;\r
								}\r
								\r
								\r
								\r
								if(isNotNu";
    echo "ll(document.finalform.cPanel_ip.value)==false)\r
								 {	\r
									alert(\"Domain IP cannot be null.\");\r
									document.finalform.cPanel_ip.focus();\r
									return false;\r
								}	\r
								\r
								\r
								if(isNotNull(document.finalform.cPanel_port.value)==false)\r
								 {	\r
							\r
									alert(\"Smtp Port cannot be null.\");\r
									document.finalform.cPanel_port.focus();\r
			";
    echo "						return false;\r
								}	\r
												\r
								if(isNotNull(document.finalform.cPanel_host.value)==false)\r
								 {	\r
								 	\r
									alert(\"Smtp Host cannot be null.\");\r
									document.finalform.cPanel_host.focus();\r
									return false;\r
									}\r
									\r
									if(isNotNull(document.finalform.cPanel_pop3servername.value)==false)\r
								 {	\r
							\r
									alert(\"";
    echo "Pop3 Server name cannot be null.\");\r
									document.finalform.cPanel_pop3servername.focus();\r
									return false;\r
								}\r
								\r
							}\r
							else if(document.getElementById('mode3').value==2)//plesk\r
							{\r
								\r
							if(isNotNull(document.finalform.plesk_version.value)==false)\r
								{	\r
									alert(\"Plesk Version cannot be null.\");\r
									document.finalform.ple";
    echo "sk_version.focus();\r
									return false;\r
								}\r
								\r
								if(isNotNull(document.finalform.plesk_packetversion.value)==false) \r
								{	\r
									alert(\"Plesk Packet Version cannot be null.\");\r
									document.finalform.plesk_packetversion.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.finalform.plesk_domainid.value)==false) \r
								{	\r
									al";
    echo "ert(\"Plesk Domain ID cannot be null.\");\r
									document.finalform.plesk_domainid.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.finalform.plesk_username.value)==false)\r
								 {	\r
									alert(\"Plesk Username cannot be null.\");\r
									document.finalform.plesk_username.focus();\r
									return false;\r
								}\r
								\r
								\r
								\r
								if(isNotNu";
    echo "ll(document.finalform.plesk_password.value)==false)\r
								 {	\r
									alert(\"Plesk Password cannot be null.\");\r
									document.finalform.plesk_password.focus();\r
									return false;\r
								}	\r
								\r
								\r
								if(isNotNull(document.finalform.plesk_name.value)==false)\r
								 {	\r
							\r
									alert(\"Domain Name cannot be null.\");\r
									document.finalform.plesk_nam";
    echo "e.focus();\r
									return false;\r
								}	\r
												\r
								if(isNotNull(document.finalform.plesk_ip.value)==false)\r
								 {	\r
								 	\r
									alert(\"Domain IP cannot be null.\");\r
									document.finalform.plesk_ip.focus();\r
									return false;\r
								\r
							}\r
							if(isNotNull(document.finalform.smtp_port_plesk.value)==false)\r
								 {	\r
							\r
									alert(\"Sm";
    echo "tp Port cannot be null.\");\r
									document.finalform.smtp_port_plesk.focus();\r
									return false;\r
								}	\r
												\r
								if(isNotNull(document.finalform.smtp_host_plesk.value)==false)\r
								 {	\r
								 	\r
									alert(\"Smtp Host cannot be null.\");\r
									document.finalform.smtp_host_plesk.focus();\r
									return false;\r
								\r
							}\r
							\r
							\r
							if";
    echo "(isNotNull(document.finalform.plesk_pop3servername.value)==false)\r
								 {	\r
							\r
									alert(\"Pop3 Server name cannot be null.\");\r
									document.finalform.plesk_pop3servername.focus();\r
									return false;\r
								}	\r
							\r
							}\r
							\r
							}\r
							\r
							\r
							else if(document.getElementById('mode2').value==2)//manually\r
							{\r
								\r
								if(isNotNu";
    echo "ll(document.finalform.pop3_servername_manually.value)==false)\r
								{	\r
									alert(\"Pop3 Servername cannot be null.\");\r
									document.finalform.pop3_servername_manually.focus();\r
									return false;\r
								}\r
								\r
								if(isNotNull(document.finalform.smtp_host_manually.value)==false) \r
								{	\r
									alert(\"Smtp Host cannot be null.\");\r
									document.finalform.smt";
    echo "p_host_manually.focus();\r
									return false;\r
								}\r
								if(isNotNull(document.finalform.smtp_port_manually.value)==false) \r
								{	\r
									alert(\"Smtp Port cannot be null.\");\r
									document.finalform.smtp_port_manually.focus();\r
									return false;\r
								}\r
								\r
							}\r
							\r
							\r
							}\r
			\r
			document.getElementById('finalform').submit();	\r
					ret";
    echo "urn true;			\r
			\r
			}\r
			</script>\r
\r
				<form name=\"finalform\" id=\"finalform\" enctype=\"multipart/form-data\"\r
					method=\"post\" action=\"install.php?step=4\">\r
				<table border=\"0\">\r
					<tr>\r
						<td valign=\"top\" height=\"32px\" align=\"right\">&nbsp;</td>\r
						<td valign=\"top\" height=\"32px\" align=\"left\">Administrator\r
						Information</td>\r
					</tr>\r
					<tr>\r
						<td valign=\"top\" height=\"32px\" ";
    echo "align=\"right\" colspan=\"2\">";
    echo $msg;
    echo "</td>\r
					</tr>\r
					<tr>\r
\r
						<td valign=\"\" height=\"32px\" align=\"left\">Choose your option</td>\r
						<td valign=\"\" height=\"32px\" align=\"right\"><input name=\"mode1\"\r
							id=\"mode1\" type=\"radio\" size=\"40\" onClick=\"individualfun()\"\r
							checked=\"checked\" value=\"2\">Individual Account <input name=\"mode1\" id=\"mode1\" type=\"radio\" onClick=\"catchallfun()\" value=\"1\">Catch\r
						All Account</td>\r
					";
    echo "</tr>\r
					<tr>\r
						<td colspan=\"2\">\r
						<div style=\"display: none;\" id=\"catchall\">\r
						<table>\r
							<tr>\r
								<td valign=\"\" height=\"32px\" align=\"left\">Smtp Username</td>\r
								<td><input name=\"smtp_username\" type=\"text\" size=\"40\"\r
									value=\"";
    echo $catch_smtp_username[0];
    echo "\"></td>\r
							</tr>\r
							<tr>\r
								<td valign=\"\" height=\"32px\" align=\"left\">Smtp Password</td>\r
								<td><input name=\"smtp_password\" type=\"text\" size=\"40\"\r
									value=\"";
    echo $catch_smtp_password[0];
    echo "\"></td>\r
							</tr>\r
							<tr>\r
								<td valign=\"\" height=\"32px\" align=\"left\">Smtp Host</td>\r
								<td><input name=\"smtp_host_catchall\" type=\"text\" size=\"40\"\r
									value=\"";
    echo $catch_smtp_host[0];
    echo "\"></td>\r
							</tr>\r
							<tr>\r
								<td valign=\"\" height=\"32px\" align=\"left\">Smtp Port</td>\r
								<td><input name=\"smtp_port_catchall\" type=\"text\" size=\"40\"\r
									value=\"";
    echo $catch_smtp_port[0];
    echo "\"></td>\r
							</tr>\r
							<tr>\r
								<td valign=\"\" height=\"32px\" align=\"left\">Pop3 Server Name</td>\r
								<td><input name=\"pop3_servername\" type=\"text\" size=\"40\"\r
									value=\"";
    echo $catch_pop3_servername[0];
    echo "\"></td>\r
							</tr>\r
\r
							<tr>\r
								<td valign=\"\" height=\"32px\" align=\"left\">Pop3 Server Email</td>\r
								<td><input name=\"pop3_serveremail\" type=\"text\" size=\"40\"\r
									value=\"";
    echo $catch_pop3_serveremail[0];
    echo "\"></td>\r
							</tr>\r
							<tr>\r
								<td valign=\"\" height=\"32px\" align=\"left\">Pop3 Server Password</td>\r
								<td><input name=\"pop3_serverpassword\" type=\"text\" size=\"40\"\r
									value=\"";
    echo $catch_pop3_serverpassword[0];
    echo "\"></td>\r
							</tr>\r
						</table>\r
						</div>\r
						</td>\r
					</tr>\r
					<tr>\r
						<td colspan=\"2\">\r
						<div align=\"\" id=\"individual\">\r
						<table border=\"0\">\r
							<tr>\r
								<td>\r
								<div><input name=\"mode2\" id=\"mode2\" checked=\"checked\"\r
									type=\"radio\" size=\"40\" onClick=\"automaticfun()\" value=\"1\">Allow\r
								to create email account automatically <input name=\"mode2\"\r
							";
    echo "		id=\"mode2\" type=\"radio\" size=\"40\" onClick=\"manuallyfun()\"\r
									value=\"2\">Allow to create email account manually</div>\r
								</td>\r
							</tr>\r
							<tr>\r
								<td colspan=\"2\"></td>\r
							</tr>\r
							<tr>\r
								<td colspan=\"2\">\r
								<div id=\"automatic\">\r
								<div align=\"left\"><input name=\"mode3\" id=\"mode3\"\r
									checked=\"checked\" type=\"radio\" size=\"40\" onClick=\"cpanelfun()\"\r";
    echo "
									value=\"1\">CPanel Account <input name=\"mode3\" id=\"mode3\"\r
									type=\"radio\" size=\"40\" onClick=\"pleskfun()\" value=\"2\">Plesk\r
								Account</div>\r
								<div id=\"cpanel\">\r
								<table>\r
									<tr>\r
										<td>&nbsp;</td>\r
										<td>&nbsp;</td>\r
									</tr>\r
\r
\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">cPanel Version</td>\r
										<td><input name=\"cPanel_v";
    echo "ersion\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_controlpanel_version[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">cPanel Username</td>\r
										<td><input name=\"cPanel_username\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_domain_username[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">cPanel Password</td>\r
										<td><input name=\"cPanel_password\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_domain_password[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Domain Name</td>\r
										<td><input name=\"cPanel_name\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_domain_name[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Domain IP</td>\r
										<td><input name=\"cPanel_ip\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_domain_ip[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Smtp Port</td>\r
										<td><input name=\"cPanel_port\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_smtp_port[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Smtp Host</td>\r
										<td><input name=\"cPanel_host\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_smtp_host[0];
    echo "\"></td>\r
									</tr>\r
\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Pop3 Server Name</td>\r
										<td><input name=\"cPanel_pop3servername\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $cpanel_pop3_servername[0];
    echo "\"></td>\r
									</tr>\r
\r
								</table>\r
								</div>\r
								\r
								</td>\r
							</tr>\r
							<tr>\r
								<td colspan=\"2\">\r
								<div id=\"plesk\" style=\"display: none\">\r
								<table>\r
									<tr>\r
										<td>&nbsp;</td>\r
										<td>&nbsp;</td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">plesk Version</td>\r
										<td><input name=\"plesk_version\" type";
    echo "=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_controlpanel_version[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">plesk Packetversion</td>\r
										<td><input name=\"plesk_packetversion\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_packetversion[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">plesk Domain ID</td>\r
										<td><input name=\"plesk_domainid\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_domainid[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">plesk Username</td>\r
										<td><input name=\"plesk_username\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_domain_username[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">plesk Password</td>\r
										<td><input name=\"plesk_password\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_domain_password[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Domain Name</td>\r
										<td><input name=\"plesk_name\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_domain_name[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Domain IP</td>\r
										<td><input name=\"plesk_ip\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_domain_ip[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Smtp Port</td>\r
										<td><input name=\"smtp_port_plesk\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_smtp_port[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Smtp Host</td>\r
										<td><input name=\"smtp_host_plesk\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_smtp_host[0];
    echo "\"></td>\r
									</tr>\r
\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Pop3 Server Name</td>\r
										<td><input name=\"plesk_pop3servername\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $plesk_pop3_servername[0];
    echo "\"></td>\r
									</tr>\r
\r
								</table>\r
								</div>\r
								</td>\r
							</tr>\r
							<tr>\r
\r
								<td colspan=\"2\">\r
								<div id=\"manually\" style=\"display: none\">\r
								<table>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Pop3 Servername</td>\r
										<td><input name=\"pop3_servername_manually\" type=\"text\"\r
											size=\"40\" value=\"";
    echo $manually_pop3_servername[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Smtp Host</td>\r
										<td><input name=\"smtp_host_manually\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $manually_smtp_host[0];
    echo "\"></td>\r
									</tr>\r
									<tr>\r
										<td valign=\"\" height=\"32px\" align=\"left\">Smtp Port</td>\r
										<td><input name=\"smtp_port_manually\" type=\"text\" size=\"40\"\r
											value=\"";
    echo $manually_smtp_port[0];
    echo "\"></td>\r
									</tr>\r
\r
								</table>\r
								</div>\r
								</td>\r
							</tr>\r
						</table>\r
						</div>\r
						<input type=\"hidden\" id=\"install_step1\" name=\"install_step1\"\r
							value=\"4\"></td>\r
					</tr>\r
				</table>\r
\r
				</form>\r
\r
				";
} elseif ($step == 4) {
    $next = "5";
    $previous = "3";
    $accounttype = trim((string) $_POST["mode1"]);
    if ($accounttype == 1) {
        $smtp_username = trim((string) $_POST["smtp_username"]);
        $smtp_password = trim((string) $_POST["smtp_password"]);
        $smtp_host = trim((string) $_POST["smtp_host_catchall"]);
        $smtp_port = trim((string) $_POST["smtp_port_catchall"]);
        $pop3_servername = trim((string) $_POST["pop3_servername"]);
        $pop3_serveremail = trim((string) $_POST["pop3_serveremail"]);
        $pop3_serverpassword = trim((string) $_POST["pop3_serverpassword"]);
        if ($smtp_username !== "")
					{
						if ($smtp_password !== "")
						{
							if ($smtp_host !== "")
							{
								if ($smtp_port !== "")
								{
									if ($pop3_servername !== "")
									{
										if ($pop3_serveremail !== "")
										{
											if ($pop3_serverpassword !== "")
											{
												require __DIR__ . "/../script.inc.php";
												include "../" . $config_path . "database.default.config.php";
												error_reporting(0);
												$link = mysql_connect($db_server, $db_username, $db_password);
												mysql_select_db($db_name);
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='controlpanel' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='controlpanel_version' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='plesk_packetversion' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='plesk_domainid' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_password' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_username' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_name' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_ip' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '1' where name='catchall_mail' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '0' where name='public_registration' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_username . "' where name='SMTP_username' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_password . "' where name='SMTP_password' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_host . "' where name='SMTP_host' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_port . "' where name='SMTP_port' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $pop3_servername . "' where name='pop3_servername' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $pop3_serveremail . "' where name='pop3_serveremail' ;\r
																			");
												mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $pop3_serverpassword . "' where name='pop3_serverpassword' ;\r
																			");
												echo "Installation has been successfully completed. Please click the Finish button below to login to your admin area.<br><br>\r
																					Please remove the install directory from the server.	";
											}
											 else 
											{
												$msg = "Pop3 Server Password cannot be blank";
											}
										}
										 else 
										{
											echo $msg = "Pop3 Server Mail cannot be blank";
										}
									}
									 else 
									{
										echo $msg = "Pop3 Server Name cannot be blank";
									}
								}
								 else 
								{
									echo $msg = "Smtp Port cannot be blank";
								}
							}
							 else 
							{
								echo $msg = "Smtp Host cannot be blank";
							}
						}
						 else 
						{
							echo $msg = "Smtp Password cannot be blank";
						}
					}
					 else 
					{
						echo $msg = "Smtp Username cannot be blank";
					}
    } elseif ($accounttype == 2) {
        $createmode = trim((string) $_POST["mode2"]);
        $controlpanel = trim((string) $_POST["mode3"]);
        if ($createmode == 1) {
            if ($controlpanel == 1) {
                $cPanel_version = trim((string) $_POST["cPanel_version"]);
                $cPanel_username = trim((string) $_POST["cPanel_username"]);
                $cPanel_password = trim((string) $_POST["cPanel_password"]);
                $cPanel_name = trim((string) $_POST["cPanel_name"]);
                $cPanel_ip = trim((string) $_POST["cPanel_ip"]);
                $cPanel_port = trim((string) $_POST["cPanel_port"]);
                $cPanel_host = trim((string) $_POST["cPanel_host"]);
                $cPanel_pop3servername = trim((string) $_POST["cPanel_pop3servername"]);
                if ($cPanel_version !== "")
								{
									if ($cPanel_username !== "")
									{
										if ($cPanel_password !== "")
										{
											if ($cPanel_name !== "")
											{
												if ($cPanel_ip !== "")
												{
													if ($cPanel_port !== "")
													{
														if ($cPanel_host !== "")
														{
															if ($cPanel_pop3servername !== "")
															{
																require __DIR__ . "/../script.inc.php";
																include "../" . $config_path . "database.default.config.php";
																error_reporting(0);
																$link = mysql_connect($db_server, $db_username, $db_password);
																mysql_select_db($db_name);
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '0' where name='catchall_mail' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '1' where name='automatic_account_creation' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '1' where name='public_registration' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '1' where name='controlpanel' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_version . "' where name='controlpanel_version' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='plesk_packetversion' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='plesk_domainid' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_username . "' where name='domain_username' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_password . "' where name='domain_password' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_name . "' where name='domain_name' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_ip . "' where name='domain_ip' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_port . "' where name='SMTP_port' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_host . "' where name='SMTP_host' ;\r
																			");
																mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $cPanel_pop3servername . "' where name='pop3_servername' ;\r
																			");
																echo "Installation has been successfully completed. Please click the Finish button below to login to your admin area.<br><br>\r
																					Please remove the install directory from the server.	";
															}
															 else 
															{
																$msg = "Pop3 Server name cannot be blank";
															}
														}
														 else 
														{
															$msg = "Smtp Host cannot be blank";
														}
													}
													 else 
													{
														echo $msg = "Smtp Port cannot be blank";
													}
												}
												 else 
												{
													echo $msg = "Domain IP cannot be blank";
												}
											}
											 else 
											{
												echo $msg = "Domain Name cannot be blank";
											}
										}
										 else 
										{
											echo $msg = "cPanel Password cannot be blank";
										}
									}
									 else 
									{
										echo $msg = "cPanel Username cannot be blank";
									}
								}
								 else 
								{
									echo $msg = "cPanel Version cannot be blank";
								}
            } elseif ($controlpanel == 2) {
                $plesk_version = trim((string) $_POST["plesk_version"]);
                $plesk_packetversion = trim((string) $_POST["plesk_packetversion"]);
                $plesk_domainid = trim((string) $_POST["plesk_domainid"]);
                $plesk_username = trim((string) $_POST["plesk_username"]);
                $plesk_password = trim((string) $_POST["plesk_password"]);
                $plesk_name = trim((string) $_POST["plesk_name"]);
                $plesk_ip = trim((string) $_POST["plesk_ip"]);
                $smtp_port = trim((string) $_POST["smtp_port_plesk"]);
                $smtp_host = trim((string) $_POST["smtp_host_plesk"]);
                $plesk_pop3servername = trim((string) $_POST["plesk_pop3servername"]);
                if ($plesk_version !== "")
									{
										if ($plesk_packetversion !== "")
										{
											if ($plesk_domainid !== "")
											{
												if ($plesk_username !== "")
												{
													if ($plesk_password !== "")
													{
														if ($plesk_name !== "")
														{
															if ($plesk_ip !== "")
															{
																if ($smtp_port !== "")
																{
																	if ($smtp_host !== "")
																	{
																		if ($plesk_pop3servername !== "")
																		{
																			require __DIR__ . "/../script.inc.php";
																			include "../" . $config_path . "database.default.config.php";
																			error_reporting(0);
																			$link = mysql_connect($db_server, $db_username, $db_password);
																			mysql_select_db($db_name);
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '0' where name='catchall_mail' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '1' where name='automatic_account_creation' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '1' where name='public_registration' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '2' where name='controlpanel' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_version . "' where name='controlpanel_version' ;\r
								   						\r
								   						\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_packetversion . "' where name='plesk_packetversion' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_domainid . "' where name='plesk_domainid' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_username . "' where name='domain_username' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_password . "' where name='domain_password' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_name . "' where name='domain_name' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_ip . "' where name='domain_ip' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_port . "' where name='SMTP_port' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_host . "' where name='SMTP_host' ;\r
																			");
																			mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $plesk_pop3servername . "' where name='pop3_servername' ;\r
																			");
																			echo "Installation has been successfully completed. Please click the Finish button below to login to your admin area.<br><br>\r
																					Please remove the install directory from the server.	";
																		}
																		 else 
																		{
																			$msg = "pop3 Server name cannot be blank";
																		}
																	}
																	 else 
																	{
																		$msg = "Smtp Host cannot be blank";
																	}
																}
																 else 
																{
																	echo $msg = "Smtp Port cannot be blank";
																}
															}
															 else 
															{
																echo $msg = "Domain IP cannot be blank";
															}
														}
														 else 
														{
															echo $msg = "Domain Name cannot be blank";
														}
													}
													 else 
													{
														echo $msg = "Plesk Password cannot be blank";
													}
												}
												 else 
												{
													echo $msg = "Plesk Username cannot be blank";
												}
											}
											 else 
											{
												echo $msg = "Plesk Domain ID cannot be blank";
											}
										}
										 else 
										{
											echo $msg = "Plesk Packet Version cannot be blank";
										}
									}
									 else 
									{
										echo $msg = "Plesk Version cannot be blank";
									}
            }
        } elseif ($createmode == 2) {
            $pop3_servername = trim((string) $_POST["pop3_servername_manually"]);
            $smtp_host = trim((string) $_POST["smtp_host_manually"]);
            $smtp_port = trim((string) $_POST["smtp_port_manually"]);
            if ($pop3_servername !== "")
								{
									if ($smtp_host !== "")
									{
										if ($smtp_port !== "")
										{
											require __DIR__ . "/../script.inc.php";
											include "../" . $config_path . "database.default.config.php";
											error_reporting(0);
											$link = mysql_connect($db_server, $db_username, $db_password);
											mysql_select_db($db_name);
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='controlpanel' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='controlpanel_version' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='plesk_packetversion' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='plesk_domainid' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_password' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_username' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_name' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '' where name='domain_ip' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '0' where name='catchall_mail' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '0' where name='automatic_account_creation' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '0' where name='public_registration' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $pop3_servername . "' where name='pop3_servername' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_host . "' where name='SMTP_host' ;\r
																			");
											mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_settings` SET `value` = '" . $smtp_port . "' where name='SMTP_port' ;\r
																			");
											echo "Installation has been successfully completed. Please click the Finish button below to login to your admin area.<br><br>\r
																					Please remove the install directory from the server.	";
										}
										 else 
										{
											echo $msg = "Smtp Port cannot be blank";
										}
									}
									 else 
									{
										echo $msg = "Smtp Host cannot be blank";
									}
								}
								 else 
								{
									echo $msg = "Pop3 Servername cannot be blank";
								}
        }
    }
}
echo "\r
\r
				</td>\r
\r
				<td>&nbsp;</td>\r
			</tr>\r
			<tr height=\"110px\">\r
				<td>&nbsp;</td>\r
				<td valign=\"top\" align=\"right\">";
if ($previous != 0)
{
	echo " <a href=\"install.php?step=";
	echo $previous;
	echo "\"><img\r
							src=\"images/previousnew.gif\" border=\"0\" align=\"absmiddle\"></a> ";
}
echo " &nbsp;&nbsp;&nbsp; ";
if ($next == 2) {
    echo " <a href=\"install.php?step=";
    echo $next;
    echo "\"><img\r
							src=\"images/nextnew.gif\" border=\"0\" align=\"absmiddle\"></a> ";
} elseif ($next == 3) {
    echo " <a href=\"install.php?step=";
    echo 3;
    echo "\"\r
							onclick=\"return verifyForm_editprofile()\"><img\r
							src=\"images/nextnew.gif\" border=\"0\" align=\"absmiddle\"></a> ";
} elseif ($next == 4) {
    echo " <a href=\"#\" onClick=\"return verifyForm_finalform()\"><img\r
							src=\"images/nextnew.gif\" border=\"0\" align=\"absmiddle\"></a> ";
} elseif ($next == 5) {
    echo " <a href=\"../admin/\" ><img\r
							src=\"images/finish.gif\" border=\"0\" align=\"absmiddle\"></a> ";
}
echo "</td>\r
				<td>&nbsp;</td>\r
			</tr>\r
			<tr>\r
				<td colspan=\"3\"></td>\r
			</tr>\r
		</table>\r
\r
		<table width=\"100%\" height=\"20px\" class=\"bgstylefooter\">\r
			<tr>\r
				<td align=\"center\">Copyright &copy; ";
echo $yr;
echo " Libescripts.com. All\r
				Rights Reserved.</td>\r
			</tr>\r
		</table>\r
\r
\r
		</td>\r
		<td>&nbsp;</td>\r
	</tr>\r
	<tr height=\"25%\">\r
		<td>&nbsp;</td>\r
		<td>&nbsp;</td>\r
		<td>&nbsp;</td>\r
	</tr>\r
</table>";



?>
