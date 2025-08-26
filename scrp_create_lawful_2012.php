<?php

	include "db_connect.php";
	
	if($_GET["mode"] == "count"){
	
		$sql = "
					
			select
				count(*)
			from
				lawfulness
			where
				year = 2011
				and
				cid not in (
					select
						cid
					from
						lawfulness
					where
						year = 2012
				
				)
				and
				cid in (
				
					select cid from company
				
				
				)";
		
		$the_count = getFirstItem($sql);
		
		echo "the script will insert $the_count lawfulness into year 2012";
		
	
	}
	
	
	if($_GET["mode"] == "update"){
	
	
		$sql = "
		
			insert ignore into
				lawfulness(
				
					Year
					,CID
				
				)
			
			select
				2012
				,cid
			from
				lawfulness
			where
				year = 2011
				and
				cid not in (
					select
						cid
					from
						lawfulness
					where
						year = 2012
				
				)
				and
				cid in (
				
					select cid from company
				
				
				)
		
		";
		
		mysql_query($sql) or die(mysql_error());
		
		echo "missing lawfulness created for year 2012";
	
	}

?>