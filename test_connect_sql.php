<?php
	
		//$db_host = "103.13.229.228:33899";
		$db_host = "103.13.229.228";
		//$db_host = "localhost";
		//$db_name = "NEP_FINAL";
		$db_name = "PDMO";
		$db_user = "sa";
		$db_pass = "Qazwsx1234";
	
		try 
		{
			//$conn = new PDO("sqlsrv:Server=$db_host;Database=$db_name");
			$conn = new PDO("sqlsrv:Server=$db_host;Database=$db_name", 'sa', 'Goodgame@229');
			//$conn = new PDO("odbc:PDMO_DEV", "sa", 'Goodgame@229');
			
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);	
			
			print_r($conn->errorInfo());
			
		}  
		catch( PDOException $e ) {die( "Error al conectar con SQL Server".$e->getMessage() ); }
	
		if (!$conn) {
			die('Something went wrong while connecting to MSSQL');
		}else{
			
			echo "connected ok successful";
			
		}
		
		
		$sql = " select
			count(*) as the_count
		from
			[tb].[y_contracts]
		  
		 ";

		echo "<br>".$sql;

			foreach ($conn->query($sql) as $row) {
				print "<br>".$row['the_count'];
			}
			
		echo "<br>finished script.";

	
?>ee