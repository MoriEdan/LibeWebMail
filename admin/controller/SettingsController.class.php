<?php

class settingscontroller extends NesoteController{
	function validatelicense($license_key) {
		/* $scriptcode = 'webmailadv';
		$lic_data = '';
		$php_self = $_SERVER['HTTP_HOST'];
		$serverarray[0] = 'licval.liberyus.com';
		$serverarray[1] = 'www.nesotelvs1.info';
		$serverarray[2] = 'www.liberyus.com';
		$serverarray[3] = 'www.nesotelvs2.info';
		$lcount = 0;

		while ($lcount < count( $serverarray )) {
			$lic_data = '';

			if ($fp_license = fopen( 'http://' . $serverarray[$lcount] . '/validate_license/' . $scriptcode . '/' . ( '' . $license_key ) . '/' . ( '' . $php_self ), 'r' )) {
				while (!feof( $fp_license )) {
					$lic_data .= fgetc( $fp_license );
				}

				fclose( $fp_license );

				if ($lic_data == '1') {
					return true;
				}
			}
			else {
				if (( $lic_data != '0' && function_exists( 'curl_init' ) )) {
					$ch = curl_init(  );
					curl_setopt( $ch, CURLOPT_URL, 'http://' . $serverarray[$lcount] . '/validate_license/' . $scriptcode . '/' . ( '' . $license_key ) . '/' . ( '' . $php_self ) );
					curl_setopt( $ch, CURLOPT_HEADER, 0 );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
					$content = curl_exec( $ch );
					curl_close( $ch );
					$lic_data = $content;

					if ($lic_data == '1') {
						return true;
					}
				}
			}


			if ($lic_data == '') {
				$lic_data = 'not available';
			}


			if ($lic_data == '0') {
				echo '<br>Failed to validate the license key. Please verify the license key that you have entered in your configuration file.<br>';
				exit( 0 );
			}

			++$lcount;
		}

		echo '<br>An error has occurred while validating your license. Please click the refresh button of your browser and try again. If you repeatedly getting the error please contact liberyus support desk.<br>';
		exit(  ); */
		
		return true;
	}

	function validuser() {
		$username = $_COOKIE['a_username'];
		$password = $_COOKIE['a_password'];
		$db = new NesoteDALController(  );
		$no = $db->total( 'nesote_email_admin', 'username=? and password=?', [ $username, $password ] );
        return $no != 0;
	}

	function changepasswordaction() {
		if ($this->validuser(  )) {
			$msg = '';
			$flag = 1;
			$uname = $_COOKIE['a_username'];
			$password = $_COOKIE['a_password'];

			if ($_POST !== []) {
				$password1 = $_POST['password1'];
				$cpassword = md5( (string) $_POST['password1'] );
				$cnpassword = $_POST['cpassword'];
				$npassword = $_POST['npassword'];
				require( __DIR__ . '/script.inc.php' );
				require( $config_path . 'system.config.php' );
				$this->validateLicense( $license_key );

				if (( $password1 == '' && $cnpassword == '' ) && $npassword == '') {
                    $flag = 0;
                    $msg = 'All Fields are empty!!!';
                } elseif ($password1 == '') {
                    $flag = 0;
                    $msg = 'Please enter the  current password!!!';
                } elseif ($npassword == '') {
                    $flag = 0;
                    $msg = 'Please enter the new  password!!!';
                } elseif ($cnpassword == '') {
                    $flag = 0;
                    $msg = 'Please enter the  confirm password!!!';
                } elseif ($cpassword != $password) {
                    $flag = 0;
                    header( 'Location:' . $this->url( 'message/error/1061' ) );
                    exit( 0 );
                } elseif ($cnpassword != $npassword) {
                    $flag = 0;
                    header( 'Location:' . $this->url( 'message/error/1062' ) );
                    exit( 0 );
                }


				if ($flag == 1) {
					if (( ( ( $_SERVER['HTTP_HOST'] == 'www.libewebportal.com' || $_SERVER['HTTP_HOST'] == 'libewebportal.com' ) || $_SERVER['HTTP_HOST'] == 'www.libewebmaildemo.com' ) || $_SERVER['HTTP_HOST'] == 'libewebmaildemo.com' )) {
						header( 'Location:' . $this->url( 'message/error/1023' ) );
						exit( 0 );
					}

					$cnpassword = md5( (string) $npassword );
					setcookie( 'a_password', $cnpassword, ['expires' => 0, 'path' => '/'] );
					$update = new NesoteDALController(  );
					$update->update( 'nesote_email_admin' );
					$update->set( 'password=?', $cnpassword );
					$update->where( 'username=? and password=?', [ $uname, $password ] );
					$result = $update->query(  );
					header( 'Location:' . $this->url( 'message/success/1072/8' ) );
					exit( 0 );
				}
			}

			$this->setValue( 'msg', $msg );
			return null;
		}

		header( 'location:' . $this->url( 'index/index' ) );
		exit( 0 );
	}

	function basicsettingsaction() {
		if (!$this->validUser(  )) {
			header( 'Location:' . $this->url( 'index/index' ) );
			exit( 0 );
		}

		$db = new NesoteDALController(  );
		$db->select( 'nesote_email_settings' );
		$db->fields( '*' );
		$res = $db->query(  );

		while ($row = $res->fetchRow(  )) {
			$this->setValue( '' . $row['1'], '' . $row['2'] );
		}

		$db->select( 'nesote_email_languages' );
		$db->fields( 'lang_code,language' );
		$db->where( 'status=?', 1 );
		$result1 = $db->query(  );
		$db->query(  );
		$this->setLoopValue( 'language', $result1->getResult(  ) );
		$db->select( 'nesote_email_themes' );
		$db->fields( 'id,name' );
		$db->where( 'status=?', 1 );
		$result = $db->query(  );
		$this->setLoopValue( 'design', $result->getResult(  ) );
		$hour = [  ];
		$i = 0;

		while ($i < 24) {
			$hour[$i][0] = $i;
			++$i;
		}

		$this->setLoopValue( 'hour', $hour );
		$mint = [  ];
		$j = 0;
		$i = 0;

		while ($i <= 55) {
			$mint[$j][0] = $i;
			$i += 5;
			++$j;
		}

		$this->setLoopValue( 'mint', $mint );
		$db->select( 'nesote_email_time_zone' );
		$db->fields( 'id,name,value' );
		$result = $db->query(  );
		$this->setLoopValue( 'adtimezone', $result->getResult(  ) );
	}

	function basicsettingsprocessaction() {
		require( __DIR__ . '/script.inc.php' );
		require( $config_path . 'system.config.php' );
		$this->validateLicense( $license_key );

		if (( ( ( $_SERVER['HTTP_HOST'] == 'www.libewebportal.com' || $_SERVER['HTTP_HOST'] == 'libewebportal.com' ) || $_SERVER['HTTP_HOST'] == 'www.libewebmaildemo.com' ) || $_SERVER['HTTP_HOST'] == 'libewebmaildemo.com' )) {
			header( 'Location:' . $this->url( 'message/error/1023' ) );
			exit( 0 );
		}

		$enginename = $_POST['enginename'];

		if ($enginename == '') {
			header( 'Location:' . $this->url( 'message/error/1101' ) );
			exit( 0 );
		}

		$affiliateid = $_POST['affiliateid'];
		$adminemail = $_POST['adminemail'];

		if ($adminemail == '') {
			header( 'Location:' . $this->url( 'message/error/1102' ) );
			exit( 0 );
		}

		$charset = $_POST['charset'];

		if ($charset == '') {
			header( 'Location:' . $this->url( 'message/error/1103' ) );
			exit( 0 );
		}

		$restricted_attachment_types = $_POST['restricted_attachment_types'];
		$attachment_size = $_POST['attachment_size'];

		if ($attachment_size == '') {
			header( 'Location:' . $this->url( 'message/error/1106' ) );
			exit( 0 );
		}

		$emailextension = $_POST['emailextension'];

		if ($emailextension == '') {
			header( 'Location:' . $this->url( 'message/error/1107' ) );
			exit( 0 );
		}

		$forward_sub_predecessor = $_POST['forward_sub_predecessor'];

		if ($forward_sub_predecessor == '') {
			header( 'Location:' . $this->url( 'message/error/1108' ) );
			exit( 0 );
		}

		$reply_sub_predecessor = $_POST['reply_sub_predecessor'];

		if ($reply_sub_predecessor == '') {
			header( 'Location:' . $this->url( 'message/error/1109' ) );
			exit( 0 );
		}

		$globaladdress_book = $_POST['globaladdress_book'];

		$globaladdress_book = $globaladdress_book == 'on' ? 1 : 0;

		$SMTP_host = $_POST['SMTP_host'];

		if ($SMTP_host == '') {
			header( 'Location:' . $this->url( 'message/error/1110' ) );
			exit( 0 );
		}

		$SMTP_port = $_POST['SMTP_port'];

		if ($SMTP_port == '') {
			header( 'Location:' . $this->url( 'message/error/1111' ) );
			exit( 0 );
		}

		$min_usernamelength = $_POST['min_usernamelength'];

		if ($min_usernamelength == '') {
			header( 'Location:' . $this->url( 'message/error/1133' ) );
			exit( 0 );
		}

		$min_passwordlength = $_POST['min_passwordlength'];

		if ($min_passwordlength == '') {
			header( 'Location:' . $this->url( 'message/error/1601' ) );
			exit( 0 );
		}

		$account_type = $_POST['account_type'];
		$time_zone = $_POST['time_zone'];
		$time_zone_hour = $_POST['time_zone_hour'];
		$time_zone_mint = $_POST['time_zone_mint'];
		$time_zone_postion = $_POST['time_zone_postion'];

		if ($account_type == 0) {
			$controlpanel = $_POST['controlpanel'];

			if ($controlpanel < 1) {
				header( 'Location:' . $this->url( 'message/error/1088' ) );
				exit( 0 );
			}


			if ($controlpanel == 1) {
                $cpanelversion = $_POST['cpanelversion'];
                $cpaneldomainusername = $_POST['cpaneldomainusername'];
                $cpaneldomainpassword = $_POST['cpaneldomainpassword'];
                $cpaneldomainname = $_POST['cpaneldomainname'];
                $cpaneldomainip = $_POST['cpaneldomainip'];
                if (( ( ( ( trim( (string) $cpanelversion ) === '' || trim( (string) $cpaneldomainusername ) === '' ) || trim( (string) $cpaneldomainpassword ) === '' ) || trim( (string) $cpaneldomainname ) === '' ) || trim( (string) $cpaneldomainip ) === '' )) {
					header( 'Location:' . $this->url( 'message/error/1086' ) );
					exit( 0 );
				}
            } elseif ($controlpanel == 2) {
                $pleskversion = $_POST['pleskversion'];
                $pleskdomainusername = $_POST['pleskdomainusername'];
                $pleskdomainpassword = $_POST['pleskdomainpassword'];
                $packetversion = $_POST['packetversion'];
                $pleskdomainid = $_POST['pleskdomainid'];
                $pleskdomainname = $_POST['pleskdomainname'];
                $pleskdomainip = $_POST['pleskdomainip'];
                if (( ( ( ( ( ( $pleskversion == '' || $pleskdomainusername == '' ) || $pleskdomainpassword == '' ) || $packetversion == '' ) || $pleskdomainid == '' ) || $pleskdomainname == '' ) || $pleskdomainip == '' )) {
						header( 'Location:' . $this->url( 'message/error/1087' ) );
						exit( 0 );
					}
            }
		}
		else {
			$SMTP_username = $_POST['SMTP_username'];

			if ($SMTP_username == '') {
				header( 'Location:' . $this->url( 'message/error/1112' ) );
				exit( 0 );
			}

			$SMTP_password = $_POST['SMTP_password'];

			if ($SMTP_password == '') {
				header( 'Location:' . $this->url( 'message/error/1113' ) );
				exit( 0 );
			}

			$db = new NesoteDALController(  );
			$db->select( 'nesote_email_settings' );
			$db->fields( 'value' );
			$db->where( 'name=?', 'controlpanel' );
			$res = $db->query(  );
			$row = $db->fetchRow( $res );
			$controlpanel = $row[0];
			$pop3_serveremail = $_POST['pop3_serveremail'];
			$pop3_serverpassword = $_POST['pop3_serverpassword'];

			if (( $pop3_serveremail == '' || $pop3_serverpassword == '' )) {
				header( 'Location:' . $this->url( 'message/error/1100' ) );
				exit( 0 );
			}
		}

		$automatic_creation = $_POST['automatic_creation'];

		$automatic_creation = $automatic_creation == 'on' ? 1 : 0;

		$pop3_servername = $_POST['pop3_servername'];

		if ($pop3_servername == '') {
			header( 'Location:' . $this->url( 'message/error/1127' ) );
			exit( 0 );
		}

		$public_registration = $_POST['public_registration'];

		$public_registration = $public_registration == 'on' ? 1 : 0;

		$display = 1;
		$lang = $_POST['lang'];
		$themes = $_POST['themes'];
		$public_page_logo1 = $_FILES['public_page_logo']['name'];
		$path_info = pathinfo( (string) $public_page_logo1 );
		$public_page_logotype = '.' . $path_info['extension'];
		$public_page_logo = 'logo/banner1' . $public_page_logotype;
		$tablepublic_page_logo = 'banner1' . $public_page_logotype;
		$user_page_logo1 = $_FILES['user_page_logo']['name'];
		$path_info1 = pathinfo( (string) $user_page_logo1 );
		$user_page_logotype = '.' . $path_info1['extension'];
		$user_page_logo = 'logo/banner2' . $user_page_logotype;
		$tableuser_page_logo = 'banner2' . $user_page_logotype;
		copy( $_FILES['public_page_logo']['tmp_name'], $public_page_logo );
		copy( $_FILES['user_page_logo']['tmp_name'], $user_page_logo );
		$mails_per_page = $_POST['mails_per_page'];
		$memoryusage_publicview = $_POST['memoryusage_publicview'];

		if ($memoryusage_publicview == 'on') {
			$memoryusage_publicview = 1;
			$memoryusage_publicview_area = $_POST['memoryusage_publicview_area'];
			$memorysize_format = 0;
		}
		else {
			$memoryusage_publicview = 0;
		}


		if ($time_zone == 1) {
			$time = $time_zone_hour * 60 * 60 + $time_zone_mint * 60;

			$time_difference = $time_zone_postion == 'Ahead' ? $time : 0 - $time;
		}
		else {
			$time_difference = 0;
		}

		$admin_timezone = $_POST['admin_timezone'];

		if ($admin_timezone == '') {
			header( 'Location:' . $this->url( 'message/error/1130' ) );
			exit( 0 );
		}

		$portal_status = $_POST['portal_status'];

		$portal_status = $portal_status == 'on' ? 1 : 0;

		$portal_installation_url = $_POST['portal_installation_url'];

		if (( $portal_status == 1 && $portal_installation_url == '' )) {
			header( 'Location:' . $this->url( 'message/error/1135' ) );
			exit( 0 );
		}

		$update = new NesoteDALController(  );
		$update->update( 'nesote_email_settings' );
		$update->set( 'value=?', [ $portal_status ] );
		$update->where( 'name=\'portal_status\'' );
		$update->query(  );
		$update->set( 'value=?', [ $portal_installation_url ] );
		$update->where( 'name=\'portal_installation_url\'' );
		$update->query(  );
		$update->set( 'value=?', [ $enginename ] );
		$update->where( 'name=\'engine_name\'' );
		$update->query(  );
		$update->set( 'value=?', [ $affiliateid ] );
		$update->where( 'name=\'affiliate_id\'' );
		$update->query(  );
		$update->set( 'value=?', [ $adminemail ] );
		$update->where( 'name=\'adminemail\'' );
		$update->query(  );
		$update->set( 'value=?', [ $charset ] );
		$update->where( 'name=\'charset\'' );
		$update->query(  );
		$update->set( 'value=?', [ $restricted_attachment_types ] );
		$update->where( 'name=\'restricted_attachment_types\'' );
		$update->query(  );
		$update->set( 'value=?', [ $attachment_size ] );
		$update->where( 'name=\'attachment_size\'' );
		$update->query(  );
		$update->set( 'value=?', [ $emailextension ] );
		$update->where( 'name=\'emailextension\'' );
		$update->query(  );
		$update->set( 'value=?', [ $forward_sub_predecessor ] );
		$update->where( 'name=\'forward_sub_predecessor\'' );
		$update->query(  );
		$update->set( 'value=?', [ $reply_sub_predecessor ] );
		$update->where( 'name=\'reply_sub_predecessor\'' );
		$update->query(  );
		$update->set( 'value=?', [ $globaladdress_book ] );
		$update->where( 'name=\'globaladdress_book\'' );
		$update->query(  );
		$update->set( 'value=?', [ $SMTP_host ] );
		$update->where( 'name=\'SMTP_host\'' );
		$update->query(  );
		$update->set( 'value=?', [ $SMTP_port ] );
		$update->where( 'name=\'SMTP_port\'' );
		$update->query(  );
		$update->set( 'value=?', [ $min_usernamelength ] );
		$update->where( 'name=\'min_usernamelength\'' );
		$update->query(  );
		$update->set( 'value=?', [ $min_passwordlength ] );
		$update->where( 'name=\'min_passwordlength\'' );
		$update->query(  );
		$update->set( 'value=?', [ $account_type ] );
		$update->where( 'name=\'catchall_mail\'' );
		$update->query(  );

		if ($account_type == 0) {
			$update->set( 'value=?', [ $pleskversion ] );
			$update->where( 'name=\'controlpanel_version\'' );
			$update->query(  );
			$update->set( 'value=?', [ $pleskdomainusername ] );
			$update->where( 'name=\'domain_username\'' );
			$update->query(  );
			$update->set( 'value=?', [ $pleskdomainpassword ] );
			$update->where( 'name=\'domain_password\'' );
			$update->query(  );
			$update->set( 'value=?', [ $packetversion ] );
			$update->where( 'name=\'plesk_packetversion\'' );
			$update->query(  );
			$update->set( 'value=?', [ $pleskdomainid ] );
			$update->where( 'name=\'plesk_domainid\'' );
			$update->query(  );
			$update->set( 'value=?', [ $pleskdomainname ] );
			$update->where( 'name=\'domain_name\'' );
			$update->query(  );
			$update->set( 'value=?', [ $pleskdomainip ] );
			$update->where( 'name=\'domain_ip\'' );
			$update->query(  );
			$update->set( 'value=?', [ $controlpanel ] );
			$update->where( 'name=\'controlpanel\'' );
			$update->query(  );
		}


		if ($account_type == 0) {
			if ($controlpanel == 1) {
                $update->set( 'value=?', [ $cpanelversion ] );
                $update->where( 'name=\'controlpanel_version\'' );
                $update->query(  );
                $update->set( 'value=?', [ $cpaneldomainusername ] );
                $update->where( 'name=\'domain_username\'' );
                $update->query(  );
                $update->set( 'value=?', [ $cpaneldomainpassword ] );
                $update->where( 'name=\'domain_password\'' );
                $update->query(  );
                $update->set( 'value=?', [ $cpaneldomainname ] );
                $update->where( 'name=\'domain_name\'' );
                $update->query(  );
                $update->set( 'value=?', [ $cpaneldomainip ] );
                $update->where( 'name=\'domain_ip\'' );
                $update->query(  );
            } elseif ($controlpanel == 2) {
                $update->set( 'value=?', [ $pleskversion ] );
                $update->where( 'name=\'controlpanel_version\'' );
                $update->query(  );
                $update->set( 'value=?', [ $pleskdomainusername ] );
                $update->where( 'name=\'domain_username\'' );
                $update->query(  );
                $update->set( 'value=?', [ $pleskdomainpassword ] );
                $update->where( 'name=\'domain_password\'' );
                $update->query(  );
                $update->set( 'value=?', [ $packetversion ] );
                $update->where( 'name=\'plesk_packetversion\'' );
                $update->query(  );
                $update->set( 'value=?', [ $pleskdomainid ] );
                $update->where( 'name=\'plesk_domainid\'' );
                $update->query(  );
                $update->set( 'value=?', [ $pleskdomainname ] );
                $update->where( 'name=\'domain_name\'' );
                $update->query(  );
                $update->set( 'value=?', [ $pleskdomainip ] );
                $update->where( 'name=\'domain_ip\'' );
                $update->query(  );
            }

			$update->set( 'value=?', [ $automatic_creation ] );
			$update->where( 'name=\'automatic_account_creation\'' );
			$update->query(  );
		}


		if ($account_type == 1) {
			$update->set( 'value=?', [ $pop3_serveremail ] );
			$update->where( 'name=\'pop3_serveremail\'' );
			$update->query(  );
			$update->set( 'value=?', [ $pop3_serverpassword ] );
			$update->where( 'name=\'pop3_serverpassword\'' );
			$update->query(  );
			$update->set( 'value=?', [ $SMTP_username ] );
			$update->where( 'name=\'SMTP_username\'' );
			$update->query(  );
			$update->set( 'value=?', [ $SMTP_password ] );
			$update->where( 'name=\'SMTP_password\'' );
			$update->query(  );
		}

		$update->set( 'value=?', [ $pop3_servername ] );
		$update->where( 'name=\'pop3_servername\'' );
		$update->query(  );
		$update->set( 'value=?', [ $public_registration ] );
		$update->where( 'name=\'public_registration\'' );
		$update->query(  );
		$update->set( 'value=?', [ $display ] );
		$update->where( 'name=\'display\'' );
		$update->query(  );
		$update->set( 'value=?', [ $lang ] );
		$update->where( 'name=\'default_language\'' );
		$update->query(  );
		$update->set( 'value=?', [ $themes ] );
		$update->where( 'name=\'themes\'' );
		$update->query(  );

		if ($public_page_logo1 != '') {
			$update->update( 'nesote_email_settings' );
			$update->set( 'value=?', [ $tablepublic_page_logo ] );
			$update->where( 'name=\'public_page_logo\'' );
			$update->query(  );
		}


		if ($user_page_logo1 != '') {
			$update->update( 'nesote_email_settings' );
			$update->set( 'value=?', [ $tableuser_page_logo ] );
			$update->where( 'name=\'user_page_logo\'' );
			$update->query(  );
		}

		$db1 = new NesoteDALController(  );
		$db1->select( 'nesote_email_settings' );
		$db1->fields( 'value' );
		$db1->where( 'name=\'public_page_logo\'' );
		$res1 = $db1->query(  );
		$row1 = $db1->fetchRow( $res1 );
		$db1->select( 'nesote_email_settings' );
		$db1->fields( 'value' );
		$db1->where( 'name=\'user_page_logo\'' );
		$res2 = $db1->query(  );
		$row2 = $db1->fetchRow( $res2 );

		if ($row1[0] == '' && $public_page_logo1 == '') {
            header( 'Location:' . $this->url( 'message/error/1115' ) );
            exit( 0 );
        }


		if ($row2[0] == '' && $user_page_logo1 == '') {
            header( 'Location:' . $this->url( 'message/error/1116' ) );
            exit( 0 );
        }

		$update->set( 'value=?', [ $mails_per_page ] );
		$update->where( 'name=\'mails_per_page\'' );
		$update->query(  );
		$update->set( 'value=?', [ $memoryusage_publicview ] );
		$update->where( 'name=\'memoryusage_publicview\'' );
		$update->query(  );
		$update->set( 'value=?', [ $memoryusage_publicview_area ] );
		$update->where( 'name=\'memoryusage_publicview_area\'' );
		$update->query(  );
		$update->set( 'value=?', [ $memorysize_format ] );
		$update->where( 'name=\'memorysize_format\'' );
		$update->query(  );
		$update->set( 'value=?', [ $time_difference ] );
		$update->where( 'name=\'time_difference\'' );
		$update->query(  );
		$update->set( 'value=?', [ $time_zone ] );
		$update->where( 'name=\'time_zone\'' );
		$update->query(  );

		if ($time_zone == 1) {
			$update->set( 'value=?', [ $time_zone_hour ] );
			$update->where( 'name=\'time_zone_hour\'' );
			$update->query(  );
			$update->set( 'value=?', [ $time_zone_mint ] );
			$update->where( 'name=\'time_zone_mint\'' );
			$update->query(  );
			$update->set( 'value=?', [ $time_zone_postion ] );
			$update->where( 'name=\'time_zone_postion\'' );
			$update->query(  );
		}

		$update->set( 'value=?', [ $admin_timezone ] );
		$update->where( 'name=\'admin_timezone\'' );
		$update->query(  );
		$adtype1 = trim( (string) $_POST['adtype1'] );
		$adtype2 = trim( (string) $_POST['adtype2'] );
		$db = new NesoteDALController(  );
		$db->update( 'nesote_email_settings' );
		$db->set( 'value=?', $adtype1 );
		$db->where( 'name=?', 'topadtype' );
		$db->query(  );
		$db->update( 'nesote_email_settings' );
		$db->set( 'value=?', $adtype2 );
		$db->where( 'name=?', 'rightadtype' );
		$db->query(  );
		header( 'Location:' . $this->url( 'message/success/1071/8' ) );
		exit( 0 );
	}

	function file_extension($filename) {
		$path_info = pathinfo( (string) $filename );
		return $path_info['extension'];
	}

	function deletelogoaction() {
		if (( ( ( $_SERVER['HTTP_HOST'] == 'www.libewebportal.com' || $_SERVER['HTTP_HOST'] == 'libewebportal.com' ) || $_SERVER['HTTP_HOST'] == 'www.libewebmaildemo.com' ) || $_SERVER['HTTP_HOST'] == 'libewebmaildemo.com' )) {
			header( 'Location:' . $this->url( 'message/error/1023' ) );
			exit( 0 );
		}

		$value = $this->getParam( 1 );
		$empty = '';
		$this->loadLibrary( 'Settings' );
		$settings = new Settings( 'nesote_email_settings' );
		$settings->loadValues(  );
		$public_page_logo = $settings->getValue( 'public_page_logo' );
		$public_page_logo = 'logo/' . $public_page_logo;
		$user_page_logo = $settings->getValue( 'user_page_logo' );
		$user_page_logo = 'logo/' . $user_page_logo;
		$db1 = new NesoteDALController(  );
		$db1->update( 'nesote_email_settings' );
		$db1->set( 'value=?', [ $empty ] );

		if ($value == 0) {
            $db1->where( 'name=\'public_page_logo\'' );
        } elseif ($value == 1) {
            $db1->where( 'name=\'user_page_logo\'' );
        }

		$db1->query(  );

		if ($value == 0) {
            unlink( $public_page_logo );
        } elseif ($value == 1) {
            unlink( $user_page_logo );
        }

		echo $value;
		exit(  );
	}

	function emailsettingsaction() {
		if (!$this->validUser(  )) {
			header( 'Location:' . $this->url( 'index/index' ) );
			exit( 0 );
		}

		$this->loadLibrary( 'Settings' );
		$settings = new Settings( 'nesote_email_settings' );
		$settings->loadValues(  );
		$welcome_subect = $settings->getValue( 'welcome_email_subject' );
		$welcome_body = $settings->getValue( 'welcome_email_body' );
		$this->setValue( 'welcomebody', $welcome_body );
		$this->setValue( 'welcomesubject', $welcome_subect );
        return null;
	}

	function emailsettingsprocessaction() {
		if (!$this->validUser(  )) {
			header( 'Location:' . $this->url( 'index/index' ) );
			exit( 0 );
		}


		if (( ( ( $_SERVER['HTTP_HOST'] == 'www.libewebportal.com' || $_SERVER['HTTP_HOST'] == 'libewebportal.com' ) || $_SERVER['HTTP_HOST'] == 'www.libewebmaildemo.com' ) || $_SERVER['HTTP_HOST'] == 'libewebmaildemo.com' )) {
			header( 'Location:' . $this->url( 'message/error/1023' ) );
			exit( 0 );
		}

		$welcomeemail = $_POST['welcomemessage'];
		$welcomesubject = $_POST['welcomesubject'];
		$update = new NesoteDALController(  );
		$update->update( 'nesote_email_settings' );
		$update->set( 'value=?', [ $welcomeemail ] );
		$update->where( 'name=\'welcome_email_body\'' );
		$update->query(  );
		$update->update( 'nesote_email_settings' );
		$update->set( 'value=?', [ $welcomesubject ] );
		$update->where( 'name=\'welcome_email_subject\'' );
		$update->query(  );
		header( 'Location:' . $this->url( 'message/success/1073/8' ) );
		exit( 0 );
	}

	function commonmailsettingsaction() {
		if (!$this->validUser(  )) {
			header( 'Location:' . $this->url( 'index/index' ) );
			exit( 0 );
		}

		$this->loadLibrary( 'Settings' );
		$settings = new Settings( 'nesote_email_settings' );
		$settings->loadValues(  );
		$commonmailsubject = $settings->getValue( 'common_email_subject' );
		$commonmailbody = $settings->getValue( 'common_email_body' );
		$this->setValue( 'commonmailmessage', $commonmailbody );
		$this->setValue( 'commonmailsubject', $commonmailsubject );
        return null;
	}

	function commonmailsettingsprocessaction() {
		if (!$this->validUser(  )) {
			header( 'Location:' . $this->url( 'index/index' ) );
			exit( 0 );
		}


		if (( ( ( $_SERVER['HTTP_HOST'] == 'www.libewebportal.com' || $_SERVER['HTTP_HOST'] == 'libewebportal.com' ) || $_SERVER['HTTP_HOST'] == 'www.libewebmaildemo.com' ) || $_SERVER['HTTP_HOST'] == 'libewebmaildemo.com' )) {
			header( 'Location:' . $this->url( 'message/error/1023' ) );
			exit( 0 );
		}

		$update = $_POST['Update'];
		$send = $_POST['Send'];
		$commonemail = $_POST['commonmailmessage'];
		$commonsubject = $_POST['commonmailsubject'];

		if (isset( $update )) {
			$update = new NesoteDALController(  );
			$update->update( 'nesote_email_settings' );
			$update->set( 'value=?', [ $commonemail ] );
			$update->where( 'name=\'common_email_body\'' );
			$update->query(  );
			$update = new NesoteDALController(  );
			$update->update( 'nesote_email_settings' );
			$update->set( 'value=?', [ $commonsubject ] );
			$update->where( 'name=\'common_email_subject\'' );
			$update->query(  );
			header( 'Location:' . $this->url( 'message/success/1073/8' ) );
			exit( 0 );
		}


		if (isset( $send )) {
			$to = [  ];
			$db = new NesoteDALController(  );
			$db->select( 'nesote_liberyus_users' );
			$db->fields( 'username,id' );
			$db->where( 'status=?', [ 1 ] );
			$result = $db->query(  );

			while ($row = $db->fetchRow( $result )) {
				$to[$i] = $row[0] . '/' . $row[1];
				++$i;
			}

			$this->smtp( $to, $commonsubject, $commonemail );
		}
        return null;

	}

	function gettimeval($userid, $username) {
		$this->loadLibrary( 'Settings' );
		$settings = new Settings( 'nesote_email_settings' );
		$settings->loadValues(  );
		new NesoteDALController(  );
		$position = $settings->getValue( 'time_zone_postion' );
		$hour = $settings->getValue( 'time_zone_hour' );
		$min = $settings->getValue( 'time_zone_mint' );
		$diff = 3600 * $hour + 60 * $min;
        $diff = $position == 'Behind' ? 0 - $diff : $diff;
		return time(  ) - $diff;
	}

	function getusertime($userid, $username) {
		$this->loadLibrary( 'Settings' );
		$settings = new Settings( 'nesote_email_settings' );
		$settings->loadValues(  );
		new NesoteDALController(  );
		$position = $settings->getValue( 'time_zone_postion' );
		$hour = $settings->getValue( 'time_zone_hour' );
		$min = $settings->getValue( 'time_zone_mint' );
		$diff = 3600 * $hour + 60 * $min;

		$diff = $position == 'Behind' ? 0 - $diff : $diff;

		$ts = time(  ) - $diff;
		$db3 = new NesoteDALController(  );
		$db3->select( 'nesote_email_usersettings' );
		$db3->fields( 'time_zone' );
		$db3->where( 'userid=?', [ $userid ] );
		$res3 = $db3->query(  );
		$row3 = $db3->fetchRow( $res3 );
		$db3->select( 'nesote_email_time_zone' );
		$db3->fields( 'value' );
		$db3->where( 'id=?', [ $row3[0] ] );
		$res3 = $db3->query(  );
		$row3 = $db3->fetchRow( $res3 );
		$timezone = $row3[0];
		$sign = trim( (string) $timezone[0] );
		$timezone1 = substr( (string) $timezone, 1 );
		$timezone1 = explode( ':', $timezone1 );
		$newtimezone = $timezone1[0] * 60 * 60 + $timezone1[1] * 60;


		if ($sign === '-') {
			$newtimezone = 0 - $newtimezone;
		}
		return $ts + $newtimezone;
	}

	function smtp($to, $subject, $html) {
		$this->loadLibrary( 'Settings' );
		$settings = new Settings( 'nesote_email_settings' );
		$settings->loadValues(  );
		$db = new NesoteDALController(  );
		$settings->getValue( 'SMTP_host' );
		$settings->getValue( 'SMTP_port' );
		$settings->getValue( 'SMTP_username' );
		$settings->getValue( 'SMTP_password' );
		$admin_email = $settings->getValue( 'adminemail' );
		$settings->getValue( 'engine_name' );

		if ($to != '') {
			foreach ($to as $address) {
				if ($address != '') {
					$address2 = explode( '/', (string) $address );
					$username = $address2[0];
					$tablenumber = $this->tableid( $username );
					$time = $this->gettimeval( $address2[1], $address2[0] );
					$to_adr = $address2[0] . $ext;
					$ext = $this->getextension(  );
					$db->insert( 'nesote_email_inbox_' . $tablenumber );
					$db->fields( 'userid,from_list,to_list,subject,body,time,status' );
					$db->values( [ $address2[1], $admin_email, $to_adr, $subject, $html, $time, 1 ] );
					$result = $db->query(  );
					$last = $db->lastInsert(  );
					$var = time(  ) . $address2[1] . $last;
					$message_id = '<' . md5( $var ) . $ext . '>';
					$mail_references = '<references><item><mailid>' . $last . '</mailid><folderid>1</folderid></item></references>';
					$md5_reference = md5( $mail_references );
					$db->update( 'nesote_email_inbox_' . $tablenumber );
					$db->set( 'mail_references=?,message_id=?,md5_references=?', [ $mail_references, $message_id, $md5_reference ] );
					$db->where( 'id=?', $last );
					$res1 = $db->query(  );
					continue;
				}
			}

			header( 'Location:' . $this->url( 'message/success/1074/8' ) );
		}

	}

	function getextension() {
		$db = new NesoteDALController(  );
		$db->select( 'nesote_email_settings' );
		$db->fields( 'value' );
		$db->where( 'name=\'emailextension\'' );
		$result = $db->query(  );
		$row = $db->fetchRow( $result );

		if (stristr( trim( (string) $row[0] ), '@' ) != '') {
			return $row[0];
		}

		return htmlentities( '@' . $row[0] );
	}

	function tableid($username) {
		$user_name = $username;
		include( __DIR__ . '/config.php' );
		$number = $cluster_factor;
		$user_name = trim( (string) $user_name );
		$mdsuser_name = md5( $user_name );
		$mdsuser_name = str_replace( 'a', '', $mdsuser_name );
		$mdsuser_name = str_replace( 'b', '', $mdsuser_name );
		$mdsuser_name = str_replace( 'c', '', $mdsuser_name );
		$mdsuser_name = str_replace( 'd', '', $mdsuser_name );
		$mdsuser_name = str_replace( 'e', '', $mdsuser_name );
		$mdsuser_name = str_replace( 'f', '', $mdsuser_name );
		$digits = substr( $mdsuser_name, -6 );
		$modlusnumber = $digits % $number;
		$modlusnumber += 1;
		++$numbers[$modlusnumber];
		return $modlusnumber;
	}
}

?>
