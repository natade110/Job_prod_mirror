<?php

	//$host = "testing_db"; 
	$host = "production_db"; 
	$db="hire_project";
	$username = "dba";
	$password = "db@dmin+";
	
	function getFirstRow($sql){
		$result = mysql_query($sql);
		$row    = mysql_fetch_array($result);
		return $row;
	}
	
	function doCleanInput($input_string){
		return htmlspecialchars(mysql_real_escape_string($input_string));
	}

	
	$connect = mysql_connect($host,$username,$password) ;
	mysql_select_db($db) or die(mysql_error()) ;
	mysql_query("SET NAMES 'utf8'");
	
	//yoes 20151118 -- aloow to specify return url
	$return_url = "http://ejob.nep.go.th/?page_id=5036";
	
	if($_POST[return_url]){
		$return_url = $_POST[return_url];
	}
	
	
	//print_r($_POST);
	
	if(strlen(trim($_POST["user_name"]))==0 || strlen(trim($_POST["password"]))==0){
	
		//nologin
		//echo "what"; exit();
		
		header("Location: http://ejob.nep.go.th/?page_id=5036&mode=error_pass"); exit();
	
	}else{
		
		//echo "yes"; exit();
	
		//do login
		$user_name = doCleanInput($_POST["user_name"]);
		$password = doCleanInput($_POST["password"]);
		
		$query="SELECT * 
				FROM users
				WHERE user_name = '$user_name' 
				and user_password = '$password'";
		
		//echo $query; exit();
		$post_row = getFirstRow($query);
		
		
		if ($post_row["user_id"]=="") {
			//no login
			header("Location: http://ejob.nep.go.th/?page_id=5036&mode=error_pass");
			exit();
		}elseif ($post_row["user_enabled"] == 0) {
			//pending
			header("Location: http://ejob.nep.go.th/?page_id=5036&mode=pending");
			exit();
		}elseif ($post_row["user_enabled"] == 2) {
			//disabled
			header("Location: http://ejob.nep.go.th/?page_id=5036&mode=disabled");
			exit();
		}else{
			//have login
			session_start();
			$_SESSION['sess_userid'] = $post_row["user_id"];
			$_SESSION['sess_accesslevel'] = $post_row["AccessLevel"];
			$_SESSION['sess_meta'] = $post_row["user_meta"];
			
			
			
			
			//yoes 20141007
			$_SESSION['sess_can_manage_user'] = $post_row["user_can_manage_user"];
			
			if($post_row["FirstName"]){
				$_SESSION['sess_userfullname'] = $post_row["FirstName"] ." ". $post_row["LastName"];
			}else{
				$_SESSION['sess_userfullname'] = $post_row["user_name"];
			}
			
			
			
			
			//update last login datetime
			mysql_query("update users set LastLoginDatetime = now() where user_id = '".$post_row["user_id"]."'");
			
			
			//yoes 20151117
			//for admin -> go to dashboard first
			if($_SESSION['sess_accesslevel'] == 1 || $_SESSION['sess_accesslevel'] == 2 || $_SESSION['sess_accesslevel'] == 3 || $_SESSION['sess_accesslevel'] == 5){
				header("Location: http://job.nep.go.th/dashboard.php");
				exit();
			}
			
			if($_POST["cont"]){
				header("Location: http://job.nep.go.th/organization.php?id=".$_POST["cont"]);
				exit();
			}else{			
				header("Location: http://job.nep.go.th/org_list.php");
				exit();
			}
		}
	
	}
	

?>