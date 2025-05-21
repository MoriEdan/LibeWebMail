<?php


class indexcontroller extends NesoteController {
	function indexaction() {
		require( __DIR__ . '/script.inc.php' );
		require( $config_path . 'system.config.php' );
		$this->validateLicense( $license_key );

		if (( ( ( $_SERVER['HTTP_HOST'] == 'www.libewebportal.com' || $_SERVER['HTTP_HOST'] == 'libewebportal.com' ) || $_SERVER['HTTP_HOST'] == 'www.libewebmaildemo.com' ) || $_SERVER['HTTP_HOST'] == 'libewebmaildemo.com' )) {
			$this->setValue( 'username', 'admin' );
			$this->setValue( 'password', 'admin' );
		}
		else {
			$this->setValue( 'username', '' );
			$this->setValue( 'password', '' );
		}

		$msg = '';
		$flag = 1;
		$msg = $this->getParam( 1 );

		if ($msg == 'errlog') {
            $msg = 'Please enter the admin details';
        } elseif ($msg == 'erru') {
            $msg = 'Please enter the user name';
        } elseif ($msg == 'errp') {
            $msg = 'Please enter the password';
        } elseif ($msg == 'invalid') {
            $msg = 'Invalid Login Details';
        } elseif ($msg == 'logout') {
            $msg = 'You have successfully logged out';
        }

		$this->setValue( 'msg', $msg );
		$db = new NesoteDALController(  );
		$db->select( 'nesote_email_settings' );
		$db->fields( 'value' );
		$db->where( 'name=\'engine_name\'' );
		$result = $db->query(  );
		$row = $db->fetchRow( $result );
		$servicename = $row[0] . ' - Admin Login';
		$this->setValue( 'servicename', $servicename );
	}

	function loginaction() {
		$flag = 1;
		$uname = $_POST['username'];
		$password = trim( (string) $_POST['password'] );
		$encpassword = md5( $password );

		if ($uname == '' && $password === '') {
            $flag = 0;
            $msg = 'errlog';
            header( 'location:' . $this->url( 'index/index/' . $msg ) );
            exit( 0 );
        } elseif ($uname == '') {
            $flag = 0;
            $msg = 'erru';
            header( 'location:' . $this->url( 'index/index/' . $msg ) );
            exit( 0 );
        } elseif ($password === '') {
            $flag = 0;
            $msg = 'errp';
            header( 'location:' . $this->url( 'index/index/' . $msg ) );
            exit( 0 );
        }
        $table = new NesoteDALController(  );
        $table->select( 'nesote_email_admin' );
        $table->fields( '*' );
        $table->where( 'username=? and password=? ', [ $uname, $encpassword ] );
        $result = $table->query(  );
        $num = $table->numRows( $result );
        if ($num != 0) {
				setcookie( 'a_username', (string) $uname, ['expires' => 0, 'path' => '/'] );
				setcookie( 'a_password', $encpassword, ['expires' => 0, 'path' => '/'] );
				header( 'Location:' . $this->url( 'home/controlpanel' ) );
				exit( 0 );
			}
        header( 'Location:' . $this->url( 'index/index/invalid' ) );
        exit( 0 );

	}

	function aboutaction() {
	}

	function logoutaction(): never {
		setcookie( 'a_username', '', ['expires' => 0, 'path' => '/'] );
		setcookie( 'a_password', '', ['expires' => 0, 'path' => '/'] );
		header( 'location:' . $this->url( 'index/index/logout' ) );
		exit( 0 );
	}

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
}

?>
