<?php

	/*include "db_connect.php";
	
	$result = mysql_query("SHOW FULL PROCESSLIST");
	while ($row=mysql_fetch_array($result)) {
		
		$i++;
		echo $i; 
	
	  $process_id=$row["Id"];
	  if ($row["Time"] > 5 ) {
		$sql="KILL $process_id";
		mysql_query($sql);
	  }
	}*/


?>-- All process cleared!? --

<?php
include "db_connect.php";

// Get all processes
$result = mysql_query("SHOW FULL PROCESSLIST");

// Initialize counter
$i = 0;

while ($row = mysql_fetch_array($result)) {
    // Check if it's a sleep process
    if (strtolower($row["Command"]) == "sleep") {
        // Check if it's been idle for more than 5 seconds
        if ($row["Time"] > 5) {
            $process_id = $row["Id"];
            $sql = "KILL $process_id";
            mysql_query($sql);
            $i++;
            
            // Optional: Log the killed process
            error_log("Killed sleeping process ID: $process_id, Idle time: {$row['Time']} seconds");
        }
    }
}

echo "Killed $i sleeping processes";
?>

