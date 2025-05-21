<?php


require_once( __DIR__ . '/script.inc.php' );
require_once( $library_path . 'Nesote/SystemUtility.class.php' );

class main extends systemutility{
	function __construct() {
	}

	public function &getinstance() {

		static $instance = null;
		if (null === $instance)
		{
			$instance = new Main();
		}
		return $instance;
	}

	function dispatch($args = '') {
		$page = '';
		$params = '';
		$controller = 'index';
		$action = 'index';
		$count = 0;

		if ($args != '') {
            $page = $args;
        } elseif (isset( $_GET['page'] )) {
            $page = $_GET['page'];
        } elseif (!isset( $_GET['page'] )) {
            $page = 'index/index';
        }


		if ($page == 'index.php') {
			$page = '';
		}

		$params = explode( '/', $page );
		$count = count( $params );

		if ($params[0] != '') {
			$controller = $params[0];
		}


		if (1 < $count && $params[1] != '') {
			$action = $params[1];
		}

		$action = str_replace( '-', '_', $action );
		$controller = str_replace( '-', '_', $controller );
		$valueparams = [  ];
		$i = 0;

		while ($i < $count - 2) {
			$valueparams[$i] = $params[$i + 2];
			++$i;
		}

		include( __DIR__ . '/script.inc.php' );
		require_once( $library_path . 'Nesote/NesoteController.class.php' );
		$this->loadController( $controller . 'Controller' );
		$controllerInstance = $this->createControllerInstance( $controller . 'Controller' );
		$actionexists = 1;
		$actionMethod = $action . 'Action';

		if (!method_exists( $controllerInstance, $actionMethod )) {
			$actionexists = 0;

			if (!file_exists( '' . $view_path . $controller . '/' . $action . '.tpl.html' )) {
				$err_msg = '<br><strong>Error: </strong>Requested page was not found!';
				$ini_error_status = ini_get( 'error_reporting' );

				if ($ini_error_status != 0) {
					$err_msg .= '<br><strong>Details: </strong><strong>' . $action . '</strong> Action not found!';
				}

				$this->loadErrorTemplate( $err_msg );
				exit( 0 );
			}
		}


		if (strcasecmp( get_parent_class( $controllerInstance ), 'NesoteController' ) != 0) {
			$ini_error_status = ini_get( 'error_reporting' );

			if ($ini_error_status != 0) {
				echo '<br><strong>Error: ' . $controller . 'Controller</strong> should extend NesoteController.';
			}

			exit( 0 );
		}

		$shownoviewerror = 0;

		if (!file_exists( '' . $view_path . $controller . '/' . $action . '.tpl.html' )) {
			$shownoviewerror = 1;
			$controllerInstance->loadTemplate( '' . $view_path . $controller . '/' . $action . '.tpl.html', '' );
		}
		else {
			$controllerInstance->loadTemplate( '' . $view_path . $controller . '/' . $action . '.tpl.html' );
		}

		$controllerInstance->arguments( $valueparams );

		if ($actionexists != 0) {
			$controllerInstance->$actionMethod(  );
		}


		if ($shownoviewerror == 1) {
			$ini_error_status = ini_get( 'error_reporting' );

			if ($ini_error_status != 0) {
				echo '<br><strong>Error: </strong> View file for the ' . $action . ' action, <strong>' . $view_path . $controller . '/' . $action . '.tpl.html</strong> does not exist.';
			}

			exit( 0 );
		}

		$controllerInstance->includePage( $controllerInstance );
	}
}

?>
