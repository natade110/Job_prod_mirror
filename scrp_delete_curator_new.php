<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$this_cid = doCleanInput($_GET["cid"]);
		$this_year = doCleanInput($_GET["year"]);
		$this_return_id = doCleanInput($_GET["return_id"]);
		//extra table?
		$is_extra_table = doCleanInput($_GET["extra"]);
	}else{
		exit();
	}
	
	//echo $this_return_id; exit();
	
	//table name
	$table_name = "curator";
	$lawful_table_name = "lawfulness";
	
	$auto_post = 1;
	
	//for company, record this to company table instead
	if($sess_accesslevel == 4){
		
			$table_name = "curator_company";
			$lawful_table_name = "lawfulness_company";			
			$auto_post = 0;
			
	}
	
	
	if($is_extra_table){
		
		$table_name = "curator_extra";
		$lawful_table_name = "";	//nothing
		$auto_post = 0;	
		
	}
	
	/////////////////////////////
	
	
	//yoes 20160105
	//add log before delete
	doCuratorFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]), 0);	
	doCuratorFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]), 1);	
	
	
	$the_sql = "
	
				delete from $table_name
				where 
					curator_id = '$this_id'
				
				";
	//echo $the_sql; exit();
	mysql_query($the_sql);
		
	
	//also delete all child inside this parent
	
	$the_sql = "
	
				delete from $table_name
				where
				curator_parent = '$this_id'
	
			";
			
	mysql_query($the_sql);
	
	
	//yoes 20210721 -> also delete all related metas
	$the_sql = "
	
		delete
		from
		curator_meta
		where
		meta_curator_id = '$this_id'
		and
		meta_for in (
		
			'child_of'
		
		)
	
	";
	mysql_query($the_sql);
	
				
	//then add this to history
	//$history_sql = "insert into modify_history values('$sess_userid','$this_cid',now(),5)";
	//mysql_query($history_sql);
	$lawful_id = getFirstItem("select lid from lawfulness where year = '$this_year' and cid = '$this_cid'");
	doAddModifyHistory($sess_userid,$this_cid,5,$lawful_id);
	
	//yoes 20160208
	resetLawfulnessByLID($lawful_id);
	
	
	if(is_numeric($this_cid)){
		
		if($this_year >= 2013 || $is_2013){
	
			header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&auto_post=$auto_post");
		
		}else{
		
			////yoes 20151222
			//header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year");
			header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&auto_post=$auto_post");
		}
		
		
	}elseif(is_numeric($this_return_id)){
		
		header("location: view_curator.php?curator_id=$this_return_id&del=del");
		
		
	}else{
		header("location: org_list.php");
	}

?>