<?php
//echo "vvv".$_COOKIE['libe_username'];exit(0);
/*echo $_SERVER['SCRIPT_NAME'];
			$var=$_SERVER['SCRIPT_NAME'];
			echo strrpos ($var,"/");
			$var=substr($var,0,strrpos($var,"/")); 
			echo "<br>xxx".$var;*/
//exit(0);
//Error Reporting
error_reporting(1);

// Including script directory configuration
include(__DIR__ . "/script.inc.php");

// Loading the main class
include($library_path."Nesote/Main.class.php");

// Creating a singleton class object.
$main=(new Main())->getInstance();

// Dispatching the page.
$main->dispatch();

?>