<?php

	include "db_connect.php";
	include "session_handler.php";
	


	$the_id = $_GET[id]*1;
	
	$output_values = getFirstRow("select * from company where cid = '$the_id'");	
	
	
	if(!$the_id || ($sess_accesslevel != 1 && $sess_accesslevel != 2 && $sess_accesslevel != 6) || !$output_values){
	
		header("location: index.php");	exit();
		
	}

?>
 


<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0;"  >Log การแก้ไขข้อมูล <?php $company_name_to_use = formatCompanyName($output_values["CompanyNameThai"],$output_values["CompanyTypeCode"]); echo $company_name_to_use;?></h2>
                    <br />
                    
                   
                   <div style="padding: 10px 0">
	                   ชนิดของ log: 
                       
                       <a href="#" onclick="doShow('company_full_log'); return false;">ข้อมูลสถานประกอบการ</a> | 
                       <a href="#" onclick="doShow('lawfulness_full_log'); return false;">การปฏิบัติตามกฎหมาย</a> | 
                       <a href="#" onclick="doShow('lawful_employees_full_log'); return false;">มาตรา 33 จ้างคนพิการเข้าทำงาน</a> | 
                       <a href="#" onclick="doShow('curator_full_log'); return false;">มาตรา 35 ให้สัมปทานฯ</a>
                       
                   </div>
                    
                    <?php 
					
						$log_sql = "							
							select
								log_id
								, log_date
								, 'ข้อมูลสถานประกอบการ' as log_type
								, log_by
								, log_ip
								, log_source
							from
								company_full_log
							where
								cid = $the_id 
							
						   
							order by
								log_date desc
								, log_id desc
						";
						
						$table_id = "company_full_log";
					
						include "organization_log_table.php";
						
						?>
                        
                        
                    
                    
                    <?php 
					
						$log_sql = "							
							select
								log_id
								, log_date
								, 'การปฏิบัติตามกฎหมาย' as log_type
								, log_by
								, log_ip
								, log_source
							from
								lawfulness_full_log
							where
								cid = $the_id 
						   
							order by
								log_date desc
								, log_id desc
						";
						
						$table_id = "lawfulness_full_log";
					
						include "organization_log_table.php";
						
						?>
                        
                      <?php 
					
						/*$log_sql = "							
							
							(
							select
								log_id
								, log_date
								, 'มาตรา 33 จ้างคนพิการเข้าทำงาน' as log_type
								, log_by
								, log_ip
								, log_source
							from
								lawful_employees_full_log
							where
								le_cid = $the_id 
								
							)
								
							union
							
							(
								
							select
								0 as log_id
								, '2999-01-05 17:38:22' as log_date
								, 'มาตรา 33 จ้างคนพิการเข้าทำงาน' as log_type
								, 1 as log_by
								, 1 as log_ip
								, 1 as log_source
								
							from
								dual
								
							)
							
							order by
								log_date desc
								, log_id desc
						";*/
						
						$log_sql = "							
							
							
							select
								log_id
								, log_date
								, 'มาตรา 33 จ้างคนพิการเข้าทำงาน' as log_type
								, log_by
								, log_ip
								, log_source
							from
								lawful_employees_full_log
							where
								le_cid = $the_id 
								
						
							
							
							order by
								le_id desc
								, log_date desc
								, log_id desc
						";
						
						$table_id = "lawful_employees_full_log";
					
						//echo $log_sql; exit();
					
						include "organization_log_table.php";
						
						?>
                        
                      <?php 
					
						$log_sql = "							
							select
								log_id
								, log_date
								, 'มาตรา 35 ให้สัมปทานฯ' as log_type
								, log_by
								, log_ip
								, log_source
							from
								curator_full_log
							where
								curator_lid in (
								
									select
										lid 
									from
										lawfulness
									where
										cid  = $the_id
								
								)
							order by
								
								curator_id desc
								, log_date desc
								, log_id desc
						";
						
						$table_id = "curator_full_log";
					
						include "organization_log_table.php";
						
						?>
                   
                    
                  
              </td>
			</tr>
             
             <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
            </tr>  
            
		</table>                            
       
        </td>
    </tr>
    
</table>    

</div><!--end page cell-->
</td>
</tr>
</table>

<script language="javascript">

	function doShow(what){

		$('#company_full_log').hide();
		$('#lawfulness_full_log').hide();
		$('#lawful_employees_full_log').hide();
		$('#curator_full_log').hide();
		
		$('#'+what).show();

	}
	

	doShow('company_full_log');
	
</script>
</body>
</html>