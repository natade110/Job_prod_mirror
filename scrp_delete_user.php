<?php

	include "db_connect.php";
	
	//yoes 20141007 -- also check permission
	if($sess_accesslevel == 1 ||  $sess_can_manage_user){	
		//can pass		
	}else{
		//nope
		header ("location: index.php");	
	}
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	
	//yoes 20141007 --> also set if this is not admin then can only see own's province
	if(($sess_can_manage_user && $sess_meta) && $sess_accesslevel == 3){	
		
		$filter_sql = " and 
							user_meta = '$sess_meta'
							or 
									
							(
								accessLevel = 4
								and
								user_meta in (
								
									select cid from company where province	= '$sess_meta'							
									
								
								)
							)
							
							";
	
	}
	
	doUsersFullLog($sess_userid, $this_id, "scrp_delete_user.php");
	
	$the_sql = "
				delete from users
				where 
					user_id = '$this_id'
					$filter_sql
			
				limit 1
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	header("location: user_list.php");
	
	

?>