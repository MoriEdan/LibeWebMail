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
<head><title>Integrate Libe Web Mail Ultimate Version to Libe Webportal</title>\r
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
mysql_query("INSERT INTO `" . $db_tableprefix . "nesote_email_settings` (`name`, `value`) VALUES\r
('portal_status', '0'),\r
('portal_installation_url', '');\r
");
mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_messages` SET `wordscript` = 'Pas de mails à afficher' where `msg_id`='30' and `lang_id`='3';");
mysql_query("UPDATE `" . $db_tableprefix . "nesote_email_messages` SET `wordscript` = 'Joindre plusieurs fichiers' where `msg_id`='60' and `lang_id`='3';");
mysql_query("INSERT INTO `" . $db_tableprefix . "nesote_email_messages` (`msg_id`, `lang_id`, `wordscript`) VALUES\r
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
mysql_query("RENAME TABLE `" . $db_tableprefix . "nesote_email_themes` TO `" . $db_tableprefix . "nesote_email_themes_backup1`;");
mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_tableprefix . "nesote_email_themes` (\r
  `id` int(11) NOT NULL auto_increment,\r
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,\r
  `style` longtext collate utf8_unicode_ci NOT NULL,\r
  `status` int(11) NOT NULL,\r
  `thumb` varchar(256) collate utf8_unicode_ci NOT NULL,\r
  PRIMARY KEY  (`id`)\r
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;\r
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
echo "<table  style=\"width:50%;height:50%;background-color: ;padding-left: 20px;padding-top: 50px;\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r
<tr><td>&nbsp;</td></tr>\r
<tr><td >";
echo "<s";
echo "pan class=\"logossubtitle\">Libe Web Mail Ultimate Version to Libe Webportal Integration is successfully installed. Go to the Libe Web Mail Admin area for the Portal set up.</span><div style=\"padding-left: 150px;padding-top: 20px;\"></div></td></tr>\r
<tr><td>&nbsp;</td></tr>\r
</table>\r
</body>\r
</html>";



?>
