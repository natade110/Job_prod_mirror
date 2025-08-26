<?php
ini_set('display_errors',0);
$host = "localhost";
$db = "nep_web";
$username = "nep";
$password = "QDby1hRN";


$host = "localhost";
$db = "hire_project";
$username = "root";
$password = "password";
$password = "N9Af0a596f7Cb";


//-----close the DB as of 20131010
$host = "localhost";
$db = "hire_project";
$username = "rootFAUX";
$password = "password";
$password = "N9Af0a596f7CbFAUX";


$connect = mysql_connect($host,$username,$password) ;
mysql_select_db($db) or die(mysql_error()) ;
mysql_query("SET NAMES 'utf8'");

/*

$garbage_timeout = 600; // seconds
ini_set('session.gc_maxlifetime',$garbage_timeout);

session_cache_expire(10); //minutes
$cache_expire = session_cache_expire();

$cookie_path='/';
$cookie_timeout = 600; //  seconds
session_set_cookie_params($cookie_timeout, $cookie_path);

*/

session_start();
if(isset($_SESSION['sess_userid'])){
	
	$sess_userid = $_SESSION['sess_userid'];
	$sess_accesslevel = $_SESSION['sess_accesslevel'];
	$sess_meta = $_SESSION['sess_meta'];
	$sess_userfullname = $_SESSION['sess_userfullname'];
	
	//session handling?
	$this_script_name_array = explode("/",$_SERVER['SCRIPT_NAME']);
	$this_real_script_name = $this_script_name_array[count($this_script_name_array)-1];
	
	if($sess_accesslevel == 2){
	
		if(
			$this_real_script_name == "user_list.php"
			){
			header("location: index.php");
			exit();
		}
	
	}elseif($sess_accesslevel == 4){
	
		if(
			
			($this_real_script_name == "org_list.php" && $_GET["search_id"] != $sess_meta)
			
			){
			header("location: organization.php?id=$sess_meta");
			exit();
		}
	
	}
	
	//echo $this_real_script_name;
}

include "functions.php";

?>
