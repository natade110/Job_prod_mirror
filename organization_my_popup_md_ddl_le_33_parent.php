<?php			

include_once "db_connect.php";
include_once "scrp_config.php";
include_once "session_handler.php";

if(!$post_row){
	$post_row = $_POST;
	
	//print_r($_POST);
	$this_id = $post_row[this_id];
	$this_lawful_year = $post_row[this_lawful_year];
	$leid = $post_row[leid];
	$meta_value = $post_row["meta_value"]; // $leid_row["meta_value"];
	
	if(!$this_id || !$this_lawful_year){
		exit();
	}
	
}


										
		//select 33 of this company that has left and not a parent of any other 33 yet
		$sub_33_sql = "
		
			select
				*
			from
				lawful_employees
			where
				le_end_date != '0000-00-00'
				and le_cid = '$this_id'
				and le_year = '$this_lawful_year'
				and
			
				(
					le_id not in (
					
						select
							meta_value
						from
							lawful_employees_meta
						where
							meta_for = 'child_of'
							
					
					)
					or
					le_id in (
					
						select
							meta_value
						from
							lawful_employees_meta
						where
							meta_for = 'child_of'
							and
							meta_leid = '".($leid*1)."'
					
					)
				)
				
				and
				le_id != '".($leid*1)."'

		
		";
		
		$sub_33_result = mysql_query($sub_33_sql);
		
		$result_array = array();															
		
		while($sub_33_row = mysql_fetch_array($sub_33_result)){
		
			$the_id = $sub_33_row['le_id'];
		
			$result_array[$the_id][the_value] = $sub_33_row['le_id'];
			$result_array[$the_id][the_text] = $sub_33_row['le_code']." : ". $sub_33_row['le_name']." : จ้างงานวันที่ ".formatDateThaiShort($sub_33_row['le_start_date'],0)." ถึง ".formatDateThaiShort($sub_33_row['le_end_date'],0);
			$result_array[$the_id][the_left_date] = $sub_33_row['le_end_date'];
			if($meta_value == $sub_33_row['le_id']){
				$result_array[$the_id][the_selected] = "selected";
			}else{
				$result_array[$the_id][the_selected] = "";
			}
		
			/*<option left_date='<?php echo $sub_33_row['le_end_date'];?>' value='<?php echo $sub_33_row['le_id']?>' <?php if($meta_value == $sub_33_row['le_id']){echo "selected=selected";}?>>
				<?php echo $sub_33_row['le_code']." : ". $sub_33_row['le_name']." : จ้างงานวันที่ ".formatDateThaiShort($sub_33_row['le_start_date'],0)." ถึง ".formatDateThaiShort($sub_33_row['le_end_date'],0)?>
			</option>*/
		
		
			
		}
		
		echo json_encode($result_array);
		
	