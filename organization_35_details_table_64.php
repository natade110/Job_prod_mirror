<?php 

if($_GET["ws"]){
	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	include "scrp_add_curator.php";

	$this_id=$_GET["id"];
	$this_year=$_GET["year"];
	$this_lid=$_GET["lid"];
	$this_lawful_year =$_GET["year"];

	$search_string = $_GET["search_string"];

	$conditions = " and Year = '$this_year'";
	
	
	if($search_string){
		
		//$search_filter = " and (curator_name like '%$search_string%' or curator_idcard like '%$search_string%')";
		
	}

	

	if($sess_accesslevel == 4){
								
								
		$lawful_row = getFirstRow("select * 
		from 
			lawfulness_company
		where 
			CID  = '$this_id'
			
			$conditions
			
		order by LID desc
		
		limit 0,1");
		
	}else{
		
		$lawful_row = getFirstRow("select * 
		from 
			lawfulness
		where 
			CID  = '$this_id'
			
			$conditions
			
		order by LID desc
		
		limit 0,1");
			
	}	

	$lawful_values["LID"] = $lawful_row["LID"];


}

?>

<?php
if($sess_accesslevel == 1){
	//$starttime = microtime(true);
}?>

<?php if(!$_GET["ws"]){ ?>
<div id="organization_35_details_table_64_div">
<?php } ?>	

<table id="organization_35_details_table_64<?php echo $is_extra_table;?>"  border="1" cellspacing="0" cellpadding="3" style="border-collapse:collapse; 

<?php if(!$_GET["ws"]){?>display:none;<?php }?>" align="center">                        


						<!--<tr >
							<td colspan=14>
								<div align=right style='padding: 5px 5px 5px 5px;'>
									<input type=text id="myTable35_search" name="myTable35_search"/> 
									<input type=button onClick="updateCuratorList(); return false;" value="ค้นหาข้อมูล" />
								</div>
							</td>
						</tr>-->
                        
                        
                         <tr bgcolor="#efefef">
                             <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                              <td><div align="center">ชื่อ-นามสกุล</div></td>
                              <td><div align="center">เพศ</div></td>
                              <td><div align="center">อายุ</div></td>
                              <td><div align="center">เลขที่บัตรประชาชน</div></td>
							 
                              <td><div align="center">ผู้ใช้สิทธิเป็น</div></td>
                              <td><div align="center">เลขที่สัญญา</div></td>
                              <td><div align="center">วันเริ่มต้นสัญญา-วันสิ้นสุดสัญญา</div></td>
                              <td><div align="center">ระยะเวลา</div></td>
                              <td><div align="center">กิจกรรม</div></td>
							 
                              <td><div align="center">มูลค่า (บาท)</div></td>
                              <td><div align="center">รายละเอียด</div></td>
                              
                               <?php 
							   
							   
							   //echo "$submitted_company_lawful && $sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && (!$case_closed || $is_extra_table";
							   
							   //yoes 20160318 -- fix this condition for compnay
							   if($sess_accesslevel != 4){ // non-company never submit company lawfu...
								   $company_lawful_submitted_for_m35 = 0;
							   }else{
									$company_lawful_submitted_for_m35 = 1;   
							   }
							   
							   if(!$company_lawful_submitted_for_m35  && $sess_accesslevel != 5 && !$is_read_only && (!$case_closed || $is_extra_table)){
							   ?>
                             
                              <td><div align="center">ลบข้อมูล</div></td>
                              <td><div align="center">แก้ไขข้อมูล</div></td>
                              <?php }?>
                              
                        </tr> 
                        
                                    
                        <?php
                       
                            //get main curator
							//yoes 20181107 --> on get parents curator (no_meta)
                            $sql = "
									select 
										* 
									from 
										$curator_table_name 
									where 
										curator_lid = '".$lawful_values["LID"]."' 
										and 
										curator_parent = 0 
										
										and
										curator_id not in (
										
											select
												meta_curator_id
											from
												curator_meta
											where
												meta_for = 'child_of'
												and
												meta_value != 0
										
										)
										
										$search_filter
										
										
									order by 
										curator_id asc";
                            
							//yoes 20220418
							//echo $sql;						

                            
                            $org_result = mysql_query($sql);
                            $total_records = 0;
							
							$m35_rows_array = array();
							
                            while ($post_row = mysql_fetch_array($org_result)) {

								array_push($m35_rows_array, $post_row);

							}
							
							//print_r($m35_rows_array);
							
							
							
							
							for($i_m35 = 0; $i_m35 < count($m35_rows_array) ; $i_m35++){
                                
								$post_row = $m35_rows_array[$i_m35];
								
								//print_r($post_row);
								
								$total_records++;
                        
								if($the_bg == "bgcolor='#ffffff'"){
									$the_bg = "bgcolor='#F8F8F8'";
								}else{
									$the_bg = "bgcolor='#ffffff'";
								}
								
								
																
								
								//start render row								
								//include "organization_35_detailed_rows_64.php";
								//yoes 20181108 -> see if have child
								/*
								
								<tr >
												
											<td >
												<div align="center"><?php echo $i_m35?></div>

											</td>


										</tr>
								
								*/
								
									
								?>
										
										
										<tr >
												
											<td colspan="14" id="td_35_<?php echo $post_row[curator_id]?>">
												<div align="center">...* กำลังดึงข้อมูล <?php echo $post_row[curator_id]?> *...</div>

											</td>


										</tr>
	
						<?php
									
								//yoes 20211028		
									
								$post_row[this_id] = $this_id;
								$post_row[this_lawful_year] = $this_lawful_year;
								$post_row[this_lid] = $this_lid;
								
								$post_row[total_records] = $total_records;
								
								//yoes 20220918 --> reset subcount text
								$sub_count_text = "";
								$post_row[sub_count_text] = $sub_count_text;
								$post_row[curator_table_name] = $curator_table_name;
								
								$curator_vue_call .= " getCuratorTds($post_row[curator_id],".json_encode($post_row).");";
								
								
								
								$child_curator_id_array = array();
								$child_curator_id_array = getChildrenOfCurator($post_row[curator_id]);
								
								//echo " -- -- ". $post_row[curator_id];
								//print_r($child_curator_id_array);
								
								for($i_child = 0; $i_child < count($child_curator_id_array); $i_child++){
									
									//yoes 20220918 -- fix subcount text
									$sub_count_text = ".".($i_child+1);
									//$post_row[sub_count_text] = ".".$sub_count_text;
									
									$post_row = getFirstRow("select * from curator where curator_id = '".$child_curator_id_array[$i_child]."'");
									//include "organization_35_detailed_rows_64.php"; 
									
									/*
									<tr >
												
											<td >
												<div align="center"><?php echo $sub_count_text?></div>

											</td>


										</tr>*/
									
						?>
									
										
										<tr >
												
											<td colspan="14" id="td_35_<?php echo $post_row[curator_id]?>">
												<div align="center">...* กำลังดึงข้อมูล <?php echo $post_row[curator_id]?> *...</div>

											</td>


										</tr>
						
						<?php
							
									//yoes 20211028		
									$post_row[this_id] = $this_id;
									$post_row[this_lawful_year] = $this_lawful_year;
									$post_row[this_lid] = $this_lid;
									
									$post_row[total_records] = $total_records;
									$post_row[sub_count_text] = $sub_count_text;									
									$post_row[curator_table_name] = $curator_table_name;
																		
									$curator_vue_call .= " getCuratorTds($post_row[curator_id],".json_encode($post_row).");";
									
									
								}
                                
							
						
							}//end loop for curator 
					   
					   
					   ?>
                        
                      </table>
<?php if($_GET["ws"]){ ?>
	<script id="updateCuratorTds"><?php echo $curator_vue_call;?></script>
<?php exit; } ?>	
</div>
<script id="vue_organization_35_table">	
	
	<?php echo $curator_vue_call;?>
	
	function getCuratorTds(id, json, isFade=false){
								
		$.ajax({
		  method: "POST",
		  url: "organization_35_detailed_rows_64_modal.php",
		  data: json
		})
		  .done(function( html ) {				
			//alert(html);
			//my_popup["content_"+id] = "<tr><td>--"+html+"--</td></tr>";
			//$("#content_"+id).html("<tr><td>--"+html+"--</td></tr>");
			//$("#content_"+id).append(html);
			//$("#content_"+id).html("<tr><td>--ทดสอบ--</td></tr>");
			//$("#content_"+id).html("<tr><td>--"+html+"--</td></tr>");			
			/*if(isFade){
				// backup cell prop
				var rowColor = $("#"+id+"_main").attr('bgcolor');
				var rowNo = $("#"+id+"_main").find("td:eq(0)").html();				
				
				$("#"+id+"_main").replaceWith(html);

				// recover cell prop
				$("#"+id+"_main").attr('bgcolor',rowColor);
				$("#"+id+"_main").find("td:eq(0)").html(rowNo);

				$("#"+id+"_main").hide();
				$("#"+id+"_main").fadeIn("slow");
			} else  {
				$("#td_"+id).parent().replaceWith(html);
			}*/
			
			$("#td_35_"+id).parent().replaceWith(html);

		  });
		
		//my_popup["content_"+id] = "<tr><td>"+id+"</td></tr>";

	}

	//$("#span_le_row_437642").html('<td colspan="13"><div align="center">...* กำลังดึงข้อมูล  *...</div></td>');
	
	function getCuratorForm(curator_id=null){

		//alert(curator_id);
		//$('#35_popup').show();
		
		
		//use with "organization_35_popup.php"
		///var lid = $("input[name=the_lid]").val();
		//yoes 20220419 -> this seems broken...?
		//var lid = $("input[name=the_lid]").val();
		<?php
		if($this_lid){?>
			var lid = <?php echo $this_lid;?>;
		<?php
		}else{?>
			var lid = $("input[name=the_lid]").val();
		<?php } ?>
		
		var year = $("input[name=the_year]").val();

		axios
			.get("organization_35_popup.php?ws=1&curator_id="+curator_id+"&year="+year+"&lid="+lid)
			.then(response => {
				$("#35_popup").html(response.data);				
				//console.log(response.data);
			});
		
		return false;
		
		axios
			.post('organization_35_detail_table_ws_get_curator_row.php', "curator_id=" + curator_id)//{ step: ""+what+"", the_id: ""+id+""}) "leid=" + leid
			.then(response => {
			
				var mm;		
				mm = response.data;	
			
				//alert(mm);
				//use with "organization_35_popup.php"
				//$("#curator_name").val('*****');
				
				$("#curator_name").val(mm.curator_user.curator_name);				
				setPersonalIdValue("#id_",mm.curator_user.curator_idcard);
				setSelectedVal("#curator_gender",mm.curator_user.curator_gender);
				$("#curator_age").val(mm.curator_user.curator_age);
				if(mm.curator_user.curator_is_disable == "0")
					$("#r1").prop("checked", true); 
				else 
					$("#r2").prop("checked", true); 
				$("#curator_contract_number").val(mm.curator_user.curator_contract_number);
				setDateValue2("#curator_start_date",mm.curator_user.curator_start_date);
				setDateValue2("#curator_end_date",mm.curator_user.curator_end_date);

				$("#usee_name").val(mm.curator_usee.curator_name);
			
			})

	}

	

	function setDateValue2(objId,val){
		var dd = val.split(" ")[0].split("-");													
		setSelectedVal(objId+"_day",dd[2]);
		setSelectedVal(objId+"_month",dd[1]);
		setSelectedVal(objId+"_year",dd[0]);
												
	}	

	function setSpinner(id,txt="กำลังปรับปรุงข้อมูล",height=200) {
		$("#"+id).html('<div style="height='+height+'"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> '+txt+'</div>');

	}

</script>
                      
<?php 

if($sess_accesslevel == 1){
	//$endtime = microtime(true);
	//$timediff = $endtime - $starttime;
	
	//echo $timediff;
}

?>