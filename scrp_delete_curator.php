<?php

	include "db_connect.php";
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$this_cid = doCleanInput($_GET["cid"]);
		$this_year = doCleanInput($_GET["year"]);
	}else{
		exit();
	}
	
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
	/*$the_sql = "
	
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
	mysql_query($the_sql);*/
	
	
	
	//then add this to history
	//$history_sql = "insert into modify_history values('$sess_userid','$this_cid',now(),5)";
	//mysql_query($history_sql);
	$lawful_id = getFirstItem("select lid from $lawful_table_name where year = '$this_year' and cid = '$this_cid'");
	
	//yoes 20170225
	//add function to update lawfulness here
	resetLawfulnessByLID($lawful_id);
	
	doAddModifyHistory($sess_userid,$this_cid,5,$lawful_id);
	
	if(is_numeric($this_cid)){
		
		if($this_year >= 2013 || $is_2013){
	
			header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&curate=curate");
		
		}else{
		
			//yoes 20151222
			//header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&curate=curate");
			header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&curate=curate");
		
		}
		
		
	}else{
		header("location: org_list.php");
	}

?>