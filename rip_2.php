<?php 
	
	//echo "hello";
	
	//defines array of router
	//this array hold information of neighbors router
	// there are six router - with each array indicated neighboring routers
	$routers = array(
	
		array(1,3)
		,array(0,2,3)
		,array(1,5)
		,array(0,1,4)
		,array(3,5)
		,array(2,4)
	);
		
	//router name - just for render display
	$router_name = array(
	
		"A"
		, "B"
		, "C"
		, "D"
		, "E"
		, "F"
	);
	
	//routing table of each router (initialy = only know that there are neighbors)
	$routing_table = array(
	
		array(1,3)
		,array(0,2,3)
		,array(1,5)
		,array(0,1,4)
		,array(3,5)
		,array(2,4)
	
	);
	
	
	//cost between routers
	$routing_cost = array( 
	
		array(1,1)
		,array(1,1,1)
		,array(1,1)
		,array(1,1,1)
		,array(1,1)
		,array(1,1)
	
	);
	
	//next hop of routers
	$routing_hop = array(
	
		array(1,3)
		,array(0,2,3)
		,array(1,5)
		,array(0,1,4)
		,array(3,5)
		,array(2,4)
	
	);
	
	print_r($routers);
	
	// loop for X time (in real-life, 1 loop = 30 seconds)
	for($i = 0; $i <= 10; $i++){
		
		echo "<font color=red><br>T=".$i."</font>";
		
		for($my_router = 0; $my_router < count($routers); $my_router++){
			
			echo "<br><br>---- Router=".$my_router;
			echo " which have current routing_table = " ;
			print_r($routing_table[$my_router]);
			echo " and cost = ";
			print_r($routing_cost[$my_router]);
			//for each router, ask friend for their routing table
			
			$neighbor_array = $routers[$my_router];
			
			for($neighbor_id = 0;$neighbor_id < count($neighbor_array); $neighbor_id++){
				
				//for each connected router -> get routing table of that connected router
				
				$neighbor_name = $neighbor_array[$neighbor_id];
				
				echo "<br>Router=".$my_router;
				echo " get information from neighbor " . $neighbor_name . " which is ";
				
				//V2
				$my_routing_table = $routing_table[$my_router];
				$my_routing_cost = $routing_cost[$my_router];
				
				$neighbor_routing_table = $routing_table[$neighbor_name];
				$neighbor_routing_cost = $routing_cost[$neighbor_name];
				
				
				print_r($neighbor_routing_table);
				echo " and cost = ";
				print_r($neighbor_routing_cost);
				
				//do merge array
				//$routing_table[$my_router] = array_merge($routing_table[$my_router],$routing_table[$neighbor_name]);
				//$routing_table[$my_router] = $routing_table[$my_router] + $routing_table[$neighbor_name];
				for($j = 0; $j < count($my_routing_table); $j++){
					
					if(!in_array($my_routing_table[$j],$routing_table[$neighbor_name]) && $my_routing_table[$j] != $neighbor_name){
						
						array_push($routing_table[$neighbor_name], $my_routing_table[$j]);
						
						array_push($routing_cost[$neighbor_name], $my_routing_cost[$j]+1);
						
						array_push($routing_hop[$neighbor_name], $my_router );	
						
					}elseif(in_array($my_routing_table[$j],$routing_table[$neighbor_name]) && $my_routing_table[$j] != $neighbor_name){
						
						echo "<font color=green> - ".$my_routing_table[$j]." existed in neighbor </font>";
						echo "<font color=green> at position ".array_search($my_routing_table[$j], $routing_table[$neighbor_name])." - </font>";
						
						$my_cost = $my_routing_cost[$j];
						
						echo "<font color=green> my cost is <b>".$my_cost."</b></font>";
						
						$neighbor_cost_array = $routing_cost[$neighbor_name];
						
						$neighbor_cost = $neighbor_cost_array[array_search($my_routing_table[$j], $routing_table[$neighbor_name])];
						
						
						if($neighbor_cost > $my_cost+1){
							
							
							$routing_cost[$neighbor_name][array_search($my_routing_table[$j], $routing_table[$neighbor_name])] = $my_cost+1;							
							$routing_hop[$neighbor_name][array_search($my_routing_table[$j], $routing_table[$neighbor_name])] = $my_router ;
							//$routing_cost[$my_router][array_search($neighbor_routing_table[$j], $routing_table[$my_router])] = $new_cost+1;							
							//$routing_hop[$my_router][array_search($neighbor_routing_table[$j], $routing_table[$my_router])] = $neighbor_name ;
						}
						
						
					}
					
				}
				
				
				
				
								
			}
			
			echo "<br>result array is: ";
			print_r($routing_table[$my_router]);
			echo "<br>next hop is: ";
			print_r($routing_hop[$my_router]);
			echo "<br>result cost is: ";
			print_r($routing_cost[$my_router]);

			
		}
		
		
	}
?>



<?php

//render tables
for($my_router = 0; $my_router < count($routers); $my_router++){
	
	echo "<br><br><font color='#006600'>for router " . $router_name[$my_router] ."</font>";
?>
    
    <table border="1">
      <tr>
        <td>Router</td>
        <td>Next Hop</td>
        <td>Cost</td>
      </tr>
    
    
     <?php for($mm = 0; $mm < count($routing_table[$my_router]); $mm++){?>
        <tr>
        <td><?php echo $router_name[$routing_table[$my_router][$mm]]?></td>
        <td><?php echo $router_name[$routing_hop[$my_router][$mm]]?></td>
        <td><?php echo $routing_cost[$my_router][$mm]?></td>
      </tr>
      <?php }?>
      
    </table>

<?
	
}


?>


