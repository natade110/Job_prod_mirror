<?php


set_time_limit ( 300 );

$host = "localhost";
$db = "hire_project";
$username = "root";
$password = "password";
$password = "N9Af0a596f7Cb";

//** local
$host = "localhost";
$db = "hire_project_20130520";
$username = "sanroku";
$password = "qwerty789";




//** local
$host = "localhost";
$db = "hire_project_20130520";
$username = "sanroku";
$password = "qwerty789";

$host = "localhost";
$db = "nep_web";
$username = "nep";
$password = "hKXj8tne";

$connect = mysql_connect($host,$username,$password) ;
mysql_select_db($db) or die(mysql_error()) ;
mysql_query("SET NAMES 'utf8'");

//handle session
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
