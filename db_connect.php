<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);

//ini_set('post_max_size', '5M');
ini_set('upload_max_filesize', '5M');

//yoes 20141007
$server_ip=$_SERVER[SERVER_ADDR];

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


$host = "localhost";
$db = "nep_web";
$username = "nep";
$password = "hKXj8tne";

//** local

//echo $server_ip; exit();


if ($server_ip == "127.0.0.1" || $server_ip == "::1"){

 $host = "203.150.91.218";
 $db = "hire_project";

 $username = "dba";
 $password = "v&A9/5290r*t";

 $law_system_url = "http://localhost/law_system/";

}else{

 //produciton
 /**/
 $host = "203.150.91.218";
 $db="hire_project";
 $username = "dba";
 $password = "v&A9/5290r*t";
 $law_system_url = "http://law.dep.go.th";

}

//echo "website is under maintenance"; exit();
//echo $host;
//exit();

///

$connect = mysql_connect($host,$username,$password) ;
mysql_select_db($db) or die(mysql_error()) ;
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

//handle session
session_start();
if(isset($_SESSION['sess_userid'])){

 $sess_userid = $_SESSION['sess_userid'];

 //yoes 20200925
 $sess_user_name = $_SESSION['sess_user_name'];

 $sess_accesslevel = $_SESSION['sess_accesslevel'];
 $sess_meta = $_SESSION['sess_meta'];
 $sess_userfullname = $_SESSION['sess_userfullname'];
 $sess_userposition = $_SESSION['sess_userposition'];
 $sess_islawyer = $_SESSION['sess_islawyer'];

 //yoes 20140710
 $sess_can_manage_user = $_SESSION['sess_can_manage_user'];

 //session handling?
 $this_script_name_array = explode("/",$_SERVER['SCRIPT_NAME']);
 $this_real_script_name = $this_script_name_array[count($this_script_name_array)-1];

 if($sess_accesslevel == 1){

  $sess_can_approve_34 = 1;

 }elseif($sess_accesslevel == 2){

  if(
   $this_real_script_name == "user_list.php"
   ){
   //header("location: index.php");
   //exit();
  }

  //yoes 20170516
  if(in_array($sess_userid,array(56,59,70,72,83,2822))){

   $sess_can_approve_34 = 1;

  }

  //echo $sess_userid;
  //echo $sess_can_approve_34;

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



require_once "functions.php";

$usage_log_sql = "

 insert into 
  usage_full_log(
   user_id
   , user_name
   , user_ip
   , script_name
   , script_get
   , access_datetime
  ) values (
   '".($sess_userid*1)."'
   , '".substr(doCleanInput($sess_user_name),0,255)."'
   , '".substr(doCleanInput($_SERVER['REMOTE_ADDR']),0,255)."'
   , '".substr(doCleanInput(basename($_SERVER["SCRIPT_FILENAME"])),0,255)."'
   , '".substr(doCleanInput(basename(print_r($_GET,TRUE))),0,255)."'
   , now() 
  
  )
";

//yoes 20220607 - disable due to loads
mysql_query($usage_log_sql);

?>
