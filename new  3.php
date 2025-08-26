<?php //
	echo $_SERVER[SERVER_ADDR];
	echo " - db:" . $dbserver; ?>

	<form action="" method="post">
    	<textarea name="qq" style="width: 500px; height: 500px;"><?php echo $_POST["qq"];?></textarea>
        <input name="pass" type="text" />
        <input name="" type="submit" />
    </form>


<?php 

	if(!$_POST["qq"]){
	
		echo "<br>no command.....";	
		exit();
		
	}
	
	if($_POST["pass"] != "yoes"){
		echo "<br>invalid pass.....";	
		exit();
	}

	
	$dbserver="192.168.3.178";
	$dbport="1521";
	$dbname="orcl";//SID
	$dbuser="nepcap";//schema
	$dbpwd="password";	
	
	
	
	
	//new connection as of 20131010
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = $dbserver)(PORT = $dbport))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = $dbname)
				)
			)";
			
	$connect = oci_connect($dbuser, $dbpwd, $db, "AL32UTF8"); //TH8TISASCII
	
	if($connect){
		echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		echo "<br><font color='red'>connection failed!</font>";
		exit;
	}
	
	



		
	$qq = $_POST["qq"];



	echo "<br>command is: ".$qq;
	
	$ss = oci_parse($connect, $qq);
	$rr = oci_execute($ss);
	
	
			
	if (!$rr) {
		$e = oci_error($ss);  // For oci_execute errors pass the statement handle
		print htmlentities($e['message']);
		print "\n<pre>\n";
		print htmlentities($e['sqltext']);
		printf("\n%".($e['offset']+1)."s", "^");
		print  "\n</pre>\n";
		
		$the_error++;
		
	}else{
	
		
		echo '<table>';
		while (oci_fetch($ss)) {
		
			echo "<tr>";
				echo "<td>".oci_result($ss, strtoupper("name"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("geo_id"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("contract_status_code"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("contract_number"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("code"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("seq"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("contract_year"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("contract_date"))."</td>";
				
				echo "<td>".oci_result($ss, strtoupper("borrower_card_id"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("borrower_title"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("borrower_first_name"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("borrower_last_name"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("agreement_amount"))."</td>";
				echo "<td>".oci_result($ss, strtoupper("paid_amount"))."</td>";
			echo "</tr>";
		
		}
		echo '</table>';
		
		/*
		$nrows = oci_fetch_all($ss, $res);
		//var_dump($res);		
		
		
		//exit();
		echo '<table>';
		foreach( $res as $data ) {
		  echo "<tr>";
		  foreach( $data as $value ) {
			echo "<td>".$value."</td>";
		  }
		  
		  echo "</tr>";
		}
		echo '</table>';
		*/
	}
	
	
		
	
	

echo "<br>query executed $the_count queries / have $the_error errors ";


$qq = " 
	
		commit
		
		";
	
	
	
		echo "<br>".$qq;
		
		$ss = oci_parse($connect, $qq);
		$rr = oci_execute($ss);


echo "<br>commit done ";

?>