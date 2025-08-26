<?php 

	//
	

?>

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

	
	$user = 'opp$_dba'; //nep_card
	$password = "password";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.13)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = ORCL)
				)
			)";
			
	$connect = oci_connect($user, $password, $db, "TH8TISASCII");
	
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