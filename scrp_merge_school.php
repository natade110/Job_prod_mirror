<?php

	include "db_connect.php";
	
	
	$main_cid = $_POST[main_cid];
	
	//yoes 20160613 -- decide what is "duped" cid	
	
	if($_POST[main_cid_1] == $main_cid){
		$duped_cid = $_POST[main_cid_2];
	}else{
		$duped_cid = $_POST[main_cid_1];
	}
		
	
	if(!$main_cid){
	
		//no cid -> error
		header("location: merge_company.php?err=nomain&school_code=$duped_id");
		exit();
		
	}
	
	
	echo "<br>main_cid: $main_cid";
	echo "<br>duped_cid: $duped_cid"; //exit();
	
	
	//do mergin now...
	 $year_start = 2011;
						  
	if(date("m") >= 9 || $sess_accesslevel == 1 || $sess_accesslevel == 2){
		$the_end_year = date("Y")+1; //new year at month 9
	}else{
		$the_end_year = date("Y");
	}
	
			
	$sql = "
					
					select
						*
					from
						company a
					where
						cid = '$duped_cid'
				
				
				";
	
	
	$duped_result = mysql_query($sql);
	
	
	while($duped_row = mysql_fetch_array($duped_result)){
	
		//for each org...		
		$this_cid = $duped_row[CID];		
		
		
		//for each year..		
		for($i= $the_end_year;$i>=$dll_year_start;$i--){
			
			$this_year = $i;
			
			$main_lid = getFirstItem("select lid from lawfulness where cid = '".$main_cid."' and Year = '$this_year'"); 
			$main_is_school = getFirstItem("select meta_value from company_meta where meta_for = 'is_school' and meta_cid = '$main_cid' and meta_value = 1");
			
			$this_lid = getFirstItem("select lid from lawfulness where cid = '".$this_cid."' and Year = '$this_year'");
			$this_is_school = getFirstItem("select meta_value from company_meta where meta_for = 'is_school' and meta_cid = '$this_cid' and meta_value = 1");
			
			//echo "this_is_school: ". $this_is_school; exit();
			
			if(!$main_lid && $this_lid){
				
				//also check -> if no main_lid but have LID then create it
				$insert_sql = "insert into
								lawfulness(
										Year
										, CID
									)
								values(
									
										'$this_year'
										,'$main_cid'
									
									)
								";
								
				mysql_query($insert_sql) or die(mysql_error());
				
				$main_lid = mysql_insert_id();
			
			}
			
			//echo "<br>main_lid; " . $main_lid; exit();
			
			
			
			
	
			echo "<br>this_year: $this_year";
			
			echo "<br>main_lid: $main_lid";
			
			echo "<br>this_cid: $this_cid";
			echo "<br>this_lid: $this_lid";
			
			
			
			// (1)
			//move employees
			//yoes 20160623 --- merge employees instead
			
			
			$main_employees = getFirstItem("select Employees from lawfulness where LID = '".$main_lid."'");  
			$this_employees = getFirstItem("select Employees from lawfulness where LID = '".$this_lid."'");  
			
			
			//if($this_employees > $main_employees){
				
				$sql = "
				
					 update
						lawfulness
					set
						Employees = '". ($this_employees+$main_employees) ."'					
					where
						LID = '".$main_lid."'			
				
				";
				
				//echo $sql; exit();
				
				mysql_query($sql);
				
			//}
			
			
			//also merge any school meta...
			$meta_fields = array(
			
				'school_employees'
				,'school_teachers'
				,'school_contract_teachers'
				
			
			);
			
			
			for($metai=0;$metai<count($meta_fields);$metai++){
				
				//get main meta value (if any)
				$main_meta_value_sql = "
					
					select
						meta_value
					from
						lawfulness_meta
					where
						meta_for = '".$meta_fields[$metai]."'
						and
						meta_lid = '$main_lid'
				
				";			
				
				$main_meta_value = getFirstItem($main_meta_value_sql);
				
				
				//get sub meta value (if any)
				$this_meta_value_sql = "
					
					select
						meta_value
					from
						lawfulness_meta
					where
						meta_for = '".$meta_fields[$metai]."'
						and
						meta_lid = '$this_lid'
				
				";			
				
				$this_meta_value = getFirstItem($this_meta_value_sql);
				
				
				//also...if merge non-school to school...
				//add employees into meta
				if(!$this_is_school && $meta_fields[$metai] == 'school_employees'){
					
					//echo "what"; exit();
					
					$this_meta_value = $this_employees;
					
				}
				
				//yoes 20160907 -- fix bug if school meta is blank
				if(!$main_meta_value && $meta_fields[$metai] == 'school_employees'){
					
					//echo "what"; exit();					
					$main_meta_value = $main_employees;
					
				}
				
				
				
				//have values(?)
				
				$summed_meta_value = ($main_meta_value*1) + ($this_meta_value*1);
				
				//echo "summed_meta_value: ". $summed_meta_value; exit();
				
				if($summed_meta_value){
					
					//update this back to main
					$meta_sql = "
						replace into
						lawfulness_meta
						(
							meta_lid
							, meta_for
							, meta_value
						)values(
							
							'".$main_lid."'
							,'".$meta_fields[$metai]."'
							,'$summed_meta_value'
						)
							
						";
						
					mysql_query($meta_sql);
					
					//echo "<br>$meta_sql"; exit();
					
				}
				
			}
			
			//exit();
			
			//backup m33			
			$sql = "select * from lawful_employees where le_cid = '".$this_cid."' and le_year = '$this_year'";
			
			//echo $sql; exit();
			
			$m33_result = mysql_query($sql);
			
			while($m33_row = mysql_fetch_array($m33_result)){				
				//do full-log	
				
				
				$sql = "
				
					insert into
						lawful_employees(
							le_name
							,le_gender
							,le_age
							,le_code
							,le_disable_desc
							,le_start_date
							,le_wage
							,le_position
							,le_year
							,le_cid
							,le_wage_unit
							,le_from_oracle
							,le_position_group
							,le_education
							,le_is_dummy_row
							,le_created_date
							,le_created_by
						)
					select
						le_name
						,le_gender
						,le_age
						,le_code
						,le_disable_desc
						,le_start_date
						,le_wage
						,le_position
						,le_year
						,'$main_cid'
						,le_wage_unit
						,le_from_oracle
						,le_position_group
						,le_education
						,le_is_dummy_row
						,le_created_date
						,le_created_by
					from
						lawful_employees
					where 
						le_id = '".$m33_row[le_id]."'
				";
				
				//echo $sql; exit();
								
				mysql_query($sql);
				
				
				$employees_new_id = mysql_insert_id();
				
				
				doLawfulEmployeesFullLog($sess_userid, $employees_new_id, "scrp_merge_school.php");
				
				//also disable "current employees"
				$sql = "
					
					update
						lawful_employees
					set
						le_year = le_year+1000
					where 
						le_id = '".$m33_row[le_id]."'
				
				";
				
				mysql_query($sql);
				
			}
			
			//
			
			//"DUPLICATE" m33
			/*$sql = "
			
			 update
				lawful_employees
			set
				le_cid = '".$main_cid."'
			 where
				le_cid = '".$this_cid."'			
			
			";*/
			
			
			
			
			
			//move m34
			//yoes 20160608 --
			//just new to "replicate records" but still keep the old 34			
			
			
			//add payment meta to see on what changes
			
			
			 /*$sql = "
					
					update
						payment
					set
						LID = '$main_lid'
					where
						LID = '$this_lid'
							   
					";*/
					
			//mysql_query($sql);
			
			
			
			
			/*$sql = "
			
				insert into
					payment
				select
					*
				from
					payment
				where
					LID = '$this_lid'
			
			";
					
			mysql_query($sql);
			
			$new_payment_id = mysql_insert_id();
			
			//update LID
			$sql = "
					
					update
						payment
					set
						LID = '$main_lid'
					where
						PID = '$new_payment_id'
							   
					";
					
			mysql_query($sql);*/
			
			
			//"DUPLICATE" M34
			//$sql = "select * from payment where LID = '".$this_lid."'";
			$sql = "select 
						* 
					from 
						payment
							join
								receipt
							on
								payment.RID = receipt.RID
					where 
						LID = '".$this_lid."'
					order by 
						receipt.RID
					asc
					";
			
			$m34_result = mysql_query($sql);
			
			while($m34_row = mysql_fetch_array($m34_result)){
				
				
				$sql = "
					
					insert into
						receipt(
							BookReceiptNo
							,ReceiptNo
							,Amount
							,PaymentMethod
							,ReceiptNote
							,ReceiptYear
							,ReceiptDate
							,is_payback
							,NEPFundPaymentID
						)	
					select
						BookReceiptNo
						,ReceiptNo
						,Amount
						,PaymentMethod
						,ReceiptNote
						,ReceiptYear
						,ReceiptDate
						,is_payback
						,NEPFundPaymentID
					from
						receipt
					where
						RID = '".$m34_row[RID]."'
				
				";
				
				mysql_query($sql);
								
				//new receipt created..
				//create payment for these...
				
				$new_rid = mysql_insert_id();
				
				
				//yoes 20160615
				//also add records on payment's movement
				$sql = "replace into
					payment_meta(
						meta_pid
						, meta_for
						, meta_value
					)values(
						'".$m34_row[RID]."' 
						, 'rid_from_to'
						, '$new_rid'
					)						
					
				
				";
				
				mysql_query($sql);
				
				
				$sql = "select 
							* 
						from 
							payment								
						where 
							RID = '".$m34_row[RID]."'
						
						";
			
				$m34_2_result = mysql_query($sql);
				
				
				while($m34_2_row = mysql_fetch_array($m34_2_result)){
					
					
					$sql = "
						
						insert into
							payment(
								PaymentMethod
								,PaymentDate
								,RefNo
								,bank_id
								,Amount
								,RID
								,LID
								,main_flag
	
							)	
						select
							PaymentMethod
								,PaymentDate
								,RefNo
								,bank_id
								,Amount
								,'$new_rid'
								,'$main_lid'
								,main_flag
						from
							payment
						where
							PID = '".$m34_2_row[PID]."'
					
					";
					
					mysql_query($sql);
																				
				}
				
			}
			
			
			
			//"DUPLICATE" m35
			$sql = "select * from curator where curator_lid = '".$this_lid."' order by curator_parent asc";
			
			$m35_result = mysql_query($sql);
			
			while($m35_row = mysql_fetch_array($m35_result)){				
				//do full-log	
				
				if($m35_row[curator_parent] > 0){					
					//try get new parent	
					$current_parent_idcard = getFirstItem("select curator_idcard from curator where curator_id = '".$m35_row[curator_parent]."'");
					$new_parent_curator_id = getFirstItem("select curator_id from curator where curator_idcard = '".$current_parent_idcard."' and curator_lid = '$main_lid'");
					$parent_curator = $new_parent_curator_id;
				}else{
					$parent_curator = 0;	
				}
				
				$sql = "
				
					insert into
						curator(
							curator_name
							,curator_idcard
							,curator_gender
							,curator_age
							,curator_lid
							,curator_parent
							,curator_event
							,curator_event_desc
							,curator_disable_desc
							,curator_is_disable
							,curator_start_date
							,curator_end_date
							,curator_value
							,curator_from_oracle
							,curator_is_dummy_row
							,curator_created_date
							,curator_created_by
						)

					select
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,'$main_lid'
						,'$parent_curator'
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle
						,curator_is_dummy_row
						,curator_created_date
						,curator_created_by
					from
						curator
					where 
						curator_id = '".$m35_row[curator_id]."'
				";
				
				mysql_query($sql);
				
				
				//add fulllog for new record
				$new_curator_id = mysql_insert_id();
				doCuratorFullLog($sess_userid, $new_curator_id, "scrp_merge_school.php", 0);
			}
			
			
			/*$sql = "
			
			  update
					curator
				set
					curator_lid = '$main_lid'
				where
					curator_lid = '$this_lid'
					
			
			";*/
			
			//do re-calculate lawfulnesses
			resetLawfulnessByLID($main_lid);
			
			
			//yoes 20160608 -- disable "OLD" lawfulness
			/*
			
			//disable by change lawfulstatus?
			
			$sql = "
				
				update
					lawfulness
				set
					LawfulStatus = 99
				where
					LID = '$this_lid'			
			";
						
			*/
			
			//disable "OLD COMPANY" by changing year
			$sql = "
				
				update
					lawfulness
				set
					Year = Year+1000
				where
					LID = '$this_lid'			
			";
			
			mysql_query($sql);
			
			
			//Clear any "TEMP DATA" from new company
			$sql = "
				
				delete from
					lawfulness
				where
					Year > 3000
					and
					cid = '$main_cid'			
			";
			
			mysql_query($sql);
			
			$sql = "
				
				delete from
					lawful_employees
				where
					le_year > 3000
					and
					le_cid = '$main_cid'			
			";
			
			mysql_query($sql);
			
			
			//yoes 20060615 -- also mark company meta on "merged" company
			$sql = "
				
				replace into
					company_meta(
						meta_cid
						, meta_for
						, meta_value
					)values(
						
						'$duped_cid'
						, 'merged_to'
						, '$main_cid'
					
					),
					(
						'$duped_cid'
						, 'merged_date'
						, '".date("Y-m-d H:i:s")."'
					),
					(
						'$duped_cid'
						, 'merged_by'
						, '".$sess_userid."'
					)
					
			
			";
			
			mysql_query($sql);
			

			
			
		}//ends for($i= $the_end_year;$i>=$dll_year_start;$i--){
		
		
		//yoes 20160907 -- so far still good -
		//if main is not school but sub is school then -> move school info from sub to main
		if(!$main_is_school && $this_is_school){
			
			
			$sql = "
				
				insert into 
					company_meta
				select
					'$main_cid'
					, meta_for
					, meta_value
				from
					company_meta
				where
					(
						meta_for like 'school_%'
						or
						meta_for = 'is_school'
					)
					and
					meta_cid = '$this_cid'
			
			";
			
			mysql_query($sql);
			
			
			//yoes 20160907 -- resync everything again	
			$sql = "
				
				update
					company_meta
				set
					meta_value = meta_value + (
						
						select Employees from company where cid = '$main_cid'			
					
					)
				where
					meta_for = 'school_employees'
					and
					meta_cid = '$main_cid'			
			
			";
			
			mysql_query($sql);
			
			
			
			
		} //end if(!$main_is_school && $this_is_school){
		else{
			
			
			//yoes 20160907 -- resync everything for main is school	
			$sql = "
				
				update
					company_meta
				set
					meta_value = meta_value + (
						
						select Employees from company where cid = '$this_cid'			
					
					)
				where
					meta_for = 'school_employees'
					and
					meta_cid = '$main_cid'			
			
			";
			
			mysql_query($sql);
			
			
			
		}
		
			
		//yoes 20160907 -- resync everything again
		$sql = "
			
			update
				company
			set
				Employees = (
					
					select sum(meta_value) from company_meta
					
					where
										
						meta_for in ('school_employees', 'school_teachers', 'school_contract_teachers' )
						and
						meta_cid = '$main_cid'	
				
				)	
			where
				cid = '$main_cid'
		
		";
		
		mysql_query($sql);
		
		
			
		
		
		
	} //ends while($duped_row = mysql_fetch_array($duped_result)){
	
	header("location: merge_company.php?main_cid=".$main_cid ."&duped_cid=".$duped_cid."");
	exit();

?>