<?php

	include "db_connect.php";
	include "session_handler.php";
	
	//table name
	//$table_name = "company";
	//$this_id = doCleanInput($_POST["CID"]);
	
	$this_cid = $_POST["CID_LAW"];
	$this_year = $_POST["Year_LAW"];
	$Employees = $_POST["Employees_LAW"];
	$case_id = $_POST["case_id"];
	$law_eid = $_POST["law_eid"];
	
	
	//yoes 20160105
	//add log before doing anything
		//doCompanyFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]));	
		
		//doAddModifyHistory($sess_userid,$this_id,0);

		if($_POST["btn_law_accepts"]){
			
			//accepts update to lawfulness , update to law system (store with approve_status = 2)
			
				$request_law_done = 0;
				$request_law_lid = getFirstItem("select lid from lawfulness where cid = '$this_cid' and year = '$this_year'");	
														
															doLawfulnessFullLog($sess_userid,$request_law_lid,"getLawfulChangeRequest.php"); 
															
															$update_emp = "update lawfulness
																		   set Employees = $Employees
																		   where lid = '$request_law_lid'
																		   ";
															
															mysql_query($update_emp);
															
															//update approve 2 when hire accetp to law system
															$time_stamp = date('Y-m-d H:i:s');
															
															$data_law = array(
																		  'case_id' => $case_id,
																		  'law_eid' => $law_eid,
																		  'Approve_status' => "2",
																		  'Approve_accept' => $time_stamp
																		  );
																		  
															$content_list = json_encode($data_law);		
															
														
																$curl = curl_init("http://203.154.94.105/law_system/law_ws/postEmpFromLaw.php");
																curl_setopt($curl, CURLOPT_HEADER, false);
																curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
																curl_setopt($curl, CURLOPT_HTTPHEADER,
																		array("Content-type: application/json"));
																curl_setopt($curl, CURLOPT_POST, true);
																curl_setopt($curl, CURLOPT_POSTFIELDS, $content_list);

																$json_response = curl_exec($curl);

																$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
																
																curl_close($curl);
																
																if ($status == 200) {
																	
																	$request_law_done = 1;
																	
																} else {
																	$request_law_done = 2;
																}
																
																//echo $content_list;
	
		
		header("location: organization.php?id=$this_cid&year=$this_year&focus=lawful&updated=updated");
		
		}elseif($_POST["btn_law_reject"]){
			
			//reject back to law system db (store with approve_status = 3)
			
			$time_stamp = date('Y-m-d H:i:s');
															
															$data_law = array(
																		  'case_id' => $case_id,
																		  'law_eid' => $law_eid,
																		  'Approve_status' => "3",
																		  'Approve_reject' => $time_stamp
																		  );
																		  
															$content_list = json_encode($data_law);		
															
														
																$curl = curl_init("http://203.154.94.105/law_system/law_ws/postEmpFromLaw.php");
																curl_setopt($curl, CURLOPT_HEADER, false);
																curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
																curl_setopt($curl, CURLOPT_HTTPHEADER,
																		array("Content-type: application/json"));
																curl_setopt($curl, CURLOPT_POST, true);
																curl_setopt($curl, CURLOPT_POSTFIELDS, $content_list);

																$json_response = curl_exec($curl);

																$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
																
																curl_close($curl);
																
																if ($status == 200) {
																	
																	$request_law_done = 1;
																	
																} else {
																	$request_law_done = 2;
																}
		header("location: organization.php?id=$this_cid&year=$this_year&focus=lawful&updated=updated");	
		}
?>