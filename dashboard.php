<?php

	include "db_connect.php";
	include "session_handler.php";
	require_once 'c2x_constant.php';
	require_once 'c2x_function.php';
	
	
	if($_GET["mode"]=="search"){
		$mode = "search";
		
	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}
	
	//yoes 20170119 --> gov user just goto org_list.php
	if($sess_is_gov){
		header ("location: org_list.php");	exit();
	}
	
	//yoes 20141007 -- also check permission
	if($sess_accesslevel == 1 ||  $sess_accesslevel == 2 ||  $sess_accesslevel == 3 ||  $sess_accesslevel == 5 ||  $sess_accesslevel == 8){	
		//can pass		
		//yoes 20220607 - disable due to loads
		//header ("location: org_list.php");	exit();
	}else{
		//nope
		header ("location: index.php");	
	}
	
	
	//yoes 20151025
	if(strlen($_GET["user_enabled"])>0){	
		$_POST[user_enabled] = $_GET["user_enabled"]*1;
	
	}
	$canViewCollection = (hasViewRoleCollection()  || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี) || ($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้บริหาร));
	$canViewSequestration = (hasViewRoleSequestration() ||  ($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้บริหาร));
	$canCreateSequestration = hasCreateRoleSequestration();
?>
<?php include "header_html.php";?>

<?php include("Charts/Includes/FusionCharts.php"); ?>
<script type="text/javascript" src="Charts/FusionCharts.js"></script>

                <td valign="top" style="padding-left:5px;">
                	
                    
                     <?php 
	
					//echo $the_end_year;
					
					if($_POST[ddl_year]){
						$the_end_year = $_POST[ddl_year];						
					}else{
					
				
						if(date("m") >= 11){
							$the_end_year = date("Y")+1; //new year at month 9
						}else{
							$the_end_year = date("Y");
						}
						
						$_POST[ddl_year] = $the_end_year;
					
					}
					
					//$the_end_year = 2015;
					?>
                    
                    
                    <h2 class="default_h1" style="margin:0; padding:0;"  >ภาพรวมระบบปี <?php echo $the_end_year+543;?></h2>
                   
                    
                    <div style="padding:10px 0; font-weight: bold;">ดูข้อมูลปี 
                   
                    <form method="post" style="display: inline;">
                    	
                        <select name="ddl_year" onchange="this.form.submit()">
						
						
							<?php
							
								if(date("m") >= 10){
									$the_ddl_end_year = date("Y")+1; //new year at month 9
								}else{
									$the_ddl_end_year = date("Y");
								}
							
							?>
						
							<?php for($iii = $the_ddl_end_year; $iii >= 2013 ; $iii--){ 
								
									//yoes 20191226 -- normalize this into a loop
								?>
								
								<option value="<?php echo $iii;?>" <?php if($_POST[ddl_year]==$iii){?>selected="selected"<?php }?>><?php echo $iii+543; ?></option>
							
							<?php }?>								
								
							

                        </select>
                        
                        <?php 
						//yoes 20151130 -- admin allow to see province
						
						//exit();
						//yoes 20160118 -- allow พก to select provinces
						if($sess_accesslevel == 1 || $sess_accesslevel == 5 || $sess_accesslevel == 2){
						?>
                        จังหวัด
                       
                       		<?php include "ddl_org_province_auto_submit.php";?>
                       
                        <?php }?>
                        
                        
                        
                        <?php 
						//yoes 20151130 -- admin allow to see zone in province
						if($sess_accesslevel == 1 || $sess_accesslevel == 5){
							
							//get province code from id
							$selected_province_code = getFirstItem("select province_code from provinces where province_id = '".($_POST["Province"]*1)."'");
							
							//check if this province have a zone
							$selected_zone_row = getFirstRow("select * from zones where zone_province_code = '$selected_province_code'");
							
							
							if($selected_zone_row){						
							
							
						?>
                            พื้นที่การทำงาน
                           
                                 <select name="zone_id" id="zone_id" onchange="this.form.submit()">
                                                            
                                        
                                        <option value="">-- ไม่ระบุ --</option>
                                        <?php
                                        
                                        
                                        //also see if this user own this zone
                                        $my_zone = $zone_row[zone_id];
                                        
                                         $get_zone_sql = "select * from zones where zone_province_code = '$selected_province_code'";                                                                              
                                        
                                      
                                        $zone_result = mysql_query($get_zone_sql);                                        
                                        
                                        while ($zone_row = mysql_fetch_array($zone_result)) {                                        
                                        
                                        ?>              
                                            <option <?php if($_POST["zone_id"] == $zone_row["zone_id"]){echo "selected='selected'"; $zone_existed = 1;}?> value="<?php echo $zone_row["zone_id"];?>"><?php echo $zone_row["zone_name"];?></option>
                                        
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    
							<?php } //endif if($selected_zone_row){		?>
                       
                       		<?php if(!$zone_existed){
							
								$_POST["zone_id"] = 0;
								
							}?>
                       
                        <?php }?>
                        
                        
                    </form>
                    
                    </div>
                   
                   
                   <table width="100%" style="border: 1px solid #CCC;">
                        <tr>
                            <td width="350">
                            <?php
							
							
							//yoes 20151118 -- also define provinces
							if($sess_accesslevel == 3){
							
								$province_sql = " and z.province = '$sess_meta'";	
								$extra_link = "&Province=$sess_meta";
																
							}
							
							if($sess_accesslevel == 2){
							
								//yoes 20160118 -- allow พก to see all provinces
							
								//$province_sql = " and z.province = '1'";	
								//$extra_link = "&Province=1";
																
							}
							
							//yoes 20151130 - admin and exec can see province filter
							//yoes 20160118 - พก can see also
							if($_POST["Province"] && ($sess_accesslevel == 1 || $sess_accesslevel == 5 || $sess_accesslevel == 2)){
							
								$province_sql = " and z.province = '".($_POST["Province"]*1)."'";	
								$extra_link = "&Province=".($_POST["Province"]*1)."";
								
							}
							
							
							//also check if have zone attached....
							
							$my_zone = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'"); 
							
							
							
							//yoes 20160118- special case for พก
							if($_POST["Province"] && $sess_accesslevel == 2){
								$my_zone = "";	
							}
							
							
							//yoes 20151130 - admin and exec can see zone filter
							if($_POST["zone_id"] && ($sess_accesslevel == 1 || $sess_accesslevel == 5)){
							
								$my_zone = $_POST["zone_id"];
								$extra_link .= "&zone_id=".($my_zone*1)."";
								
							}
							
							//echo $my_zone;
							
							//yoes 20160118 -- make it so
							if($my_zone){								
								
								//build sql for this zone
								$zone_sql = "
								
									and
									(
										z.District in (
									
											select
												district_name
											from
												districts
											where
												district_area_code
												in (
										
													select
														district_area_code
													from
														zone_district
													where
														zone_id = '$my_zone'
												
												)
											
										)
										or
										z.district_cleaned in (
									
											select
												district_name
											from
												districts
											where
												district_area_code
												in (
										
													select
														district_area_code
													from
														zone_district
													where
														zone_id = '$my_zone'
												
												)
											
										)
									)
								
								
								";
								
								//echo $zone_sql; exit();
								
							}
							
							
							
							//yoes 20151117							
							
							
							/*echo "SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 
								$province_sql
								$zone_sql"; exit();*/
							
							$all_company_count = getFirstItem("
			
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 
								$province_sql
								$zone_sql
							
							
							");
							
							
							
							//exit();
							
							$lawful_company_count = getFirstItem("
			
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '1'
								
								and reopen_case_date >= close_case_date
								
								$province_sql
								$zone_sql
								
							
							");
							
							
							//
							$partial_company_count = getFirstItem("
			
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '2'
								and reopen_case_date >= close_case_date
								
								$province_sql
								$zone_sql
							
							
							");
							
							
							$unlawful_company_count = getFirstItem("
			
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '0'
								
								and reopen_case_date >= close_case_date
								
								$province_sql
								$zone_sql
							
							");
							
							
							
							$noneed_company_count = getFirstItem("
			
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '3'
								and reopen_case_date >= close_case_date
								
								$province_sql							
								$zone_sql
							
							");
							
							
							//yoes 20160201 -- case closed
							$lawful_company_count_closed = getFirstItem("

								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '1'
								
								and close_case_date > reopen_case_date
								
								$province_sql
								$zone_sql
								
							
							");		
							
							
							$partial_company_count_closed = getFirstItem("
							
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '2'
								and close_case_date > reopen_case_date
								
								$province_sql
								$zone_sql
							
							
							");
							
							
							$unlawful_company_count_closed = getFirstItem("
							
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '0'
								
								and close_case_date > reopen_case_date
								
								$province_sql
								$zone_sql
							
							");
							
							
							
							$noneed_company_count_closed = getFirstItem("
							
								SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '3'
								and close_case_date > reopen_case_date
								
								$province_sql							
								$zone_sql
							
							");
							
							
							
							$strXML  = "
								<chart xAxisName='การปฏิบัติตามกฎหมาย' yAxisName='จำนวนสถานประกอบการ' showValues='1' formatNumberScale='0' showBorder='1' yAxisMaxValue='100' 
								
								bgColor='ffffff'
								borderColor='f7f2ea' 
								borderthickness='0' 
								bgAlpha='100'
								
								chartTopMargin='10'
								chartRightMargin='10'
								chartBottomMargin='0'
								chartLeftMargin='10'
								
								> 
							
									<set 
									
									label='ปฏิบัติตามกฎหมาย' 
									
									value='$lawful_company_count' 
									
									link='org_list.php?LawfulFlag=1&ddl_year=$the_end_year".$extra_link."&dozone=1'
									
									color='04B404'
									
									/>
									<set 
									
									label='ปฏิบัติตามไม่ครบตามอัตราส่วน' 
									
									value='$partial_company_count' 
									
									link='org_list.php?LawfulFlag=2&ddl_year=$the_end_year".$extra_link."&dozone=1'
									
									color='F2F5A9'
									
									/>
									<set 
									
									label='ไม่ทำตามกฎหมาย' 
									
									value='$unlawful_company_count' 
									
									link='org_list.php?LawfulFlag=0&ddl_year=$the_end_year".$extra_link."&dozone=1'
									
									color='F78181'
									
									/>
									
									<set 
									
									label='ไม่เข้าข่าย' 
									
									value='$noneed_company_count' 
									
									link='org_list.php?LawfulFlag=3&ddl_year=$the_end_year".$extra_link."&dozone=1'
									
									color='81BEF7'
									
									/>
									
									
									
									
								</chart>
								";
								
								//$lawful_company_count_closed = 1000;
								
								
								//yoes 20170103
								//count dummy data
								
								$dummy_count = getFirstItem("
								
								
									select
										count(*)
									FROM 
										
										company z
										
									JOIN 
											lawfulness y 
												ON z.CID = y.CID
												
												and y.Year = '$the_end_year'
								
								
											left join (
		
												select
													le_cid
													, le_year
													, count(le_cid) as the_count_le_cid
												from
													lawful_employees
												where
													le_is_dummy_row = 1
												group by 
													le_cid
													, le_year
													
											) dl
												on 
												dl.le_cid = z.cid
												and
												dl.le_year = y.year
												
											left join (
						
												select
													curator_lid
													, count(curator_id) as the_count_curator_id
												from
													curator
												where
													curator_is_dummy_row = 1
												group by 
													curator_lid
													
											) dc
												on 
												dc.curator_lid = y.lid
												
									where
										 (
												
												the_count_curator_id > 0
												or
												the_count_le_cid > 0
												
												)
								
								");
								
								
								
							//yoes 20170108
							//นับ 33 35 ซ้ำซ้อน
							//$double3335_count = "270";
							//if change here then change below too
							
							$sql = "
									
							  select
			
									the_code
									
								  from
								
								  (
									select
									le_code as the_code
									, le_name as the_name
									, 'l' as the_type
									
								  from
									lawful_employees
										
									where le_year = '$the_end_year'
								
									union
								
									select
									  curator_idcard as the_code
									  , curator_name as the_name
									  , 'c' as the_type
									from
									  curator, lawfulness
									  where
									  curator_lid = lid
									  and
									  Year = '$the_end_year'
									  
									  and
									  curator_is_disable = 1
									  
								
									  )                    a
								
								
								group by the_code
								having count(the_code) > 1
								
								order by the_code asc
							
							";
							
							//echo $sql;
							//yoes 20220607 - disable due to loads
							//$submit_result = mysql_query($sql);
							
							$cur_year = $the_end_year;
							
							while($post_row = mysql_fetch_array($submit_result)){
								
								
								$sub_sql = "
																				
																					
									select
											a.cid
											, CompanyNameThai
											, CompanyTypeCode
											, Province
											, LawfulStatus
											, le_code		
											, '33' as the_type													
									  from
										company a
											join lawfulness b
										
											  on
												a.cid = b.cid
												and
												b.year = '$the_end_year'
												
											join
												lawful_employees c
													on a.cid = c.le_cid
													and
													b.year = c.le_year
																									 
									  where
										le_code = '".$post_row["the_code"]."'
										and
										CompanyTypeCode != '14'
										and
										CompanyTypeCode < 200
										
										
									union
									
									
									select
											a.cid
											, CompanyNameThai
											, CompanyTypeCode
											, Province
											, LawfulStatus
											, curator_idcard as le_code		
											, '35' as the_type
									  from
										company a
											join lawfulness b
										
											  on
												a.cid = b.cid
												and
												b.year = '$the_end_year'
												
											join
												curator c
													on b.lid = c.curator_lid
								
																									 
									  where
										curator_idcard = '".$post_row["the_code"]."'
										and
										curator_parent = 0
										and
										CompanyTypeCode != '14'
										and
										CompanyTypeCode < 200
										
										
										
								
								";
								
								
								//echo "<br>".$sub_sql;
								
								//yoes 20220607 - 20220607 - disable due to loads
								//$sub_result = mysql_query($sub_sql);
								
								
								while($sub_row = mysql_fetch_array($sub_result)){
									
									
									$double3335_count++;
									
								}
								
								
							}
								
								
								
								
							$strXML  = "
								<chart xAxisName='การปฏิบัติตามกฎหมาย' yAxisName='จำนวนสถานประกอบการ' showValues='1' formatNumberScale='0' showBorder='1' yAxisMaxValue='100' 
								
								bgColor='ffffff'
								borderColor='f7f2ea' 
								borderthickness='0' 
								bgAlpha='100'
								
								chartTopMargin='10'
								chartRightMargin='10'
								chartBottomMargin='0'
								chartLeftMargin='10'
								
								showLegend='0'
								
								> 
								
									<categories>
										<category label='ปฏิบัติตามกฎหมาย' />
										<category label='ปฏิบัติตามไม่ครบตามอัตราส่วน'  />
										<category label='ไม่ทำตามกฎหมาย'  />
										<category label='ไม่เข้าข่าย'  />
										
										<category label='จ่ายเงินแล้วแต่ไม่มี ม33 ม35'  />
										
										<category label='จำนวนสถานประกอบการที่มี ม33 ม35 ซ้ำซ้อน'  />
									
									</categories>
									
							
									<dataset seriesname='ยังไม่ปิดงาน'>
										
										<set 
										
										
										
											value='$lawful_company_count' 
											
											link='org_list.php?LawfulFlag=1&ddl_year=$the_end_year".$extra_link."&dozone=1'
											
											color='04B404'
										
										/>
										
										<set 
										
										
										
										value='$partial_company_count' 
										
										link='org_list.php?LawfulFlag=2&ddl_year=$the_end_year".$extra_link."&dozone=1'
										
										color='F2F5A9'
										
										/>
										<set 
										
										
										
										value='$unlawful_company_count' 
										
										link='org_list.php?LawfulFlag=0&ddl_year=$the_end_year".$extra_link."&dozone=1'
										
										color='F78181'
										
										/>
										
										<set 
										
										
										
										value='$noneed_company_count' 
										
										link='org_list.php?LawfulFlag=3&ddl_year=$the_end_year".$extra_link."&dozone=1'
										
										color='81BEF7'
										
										/>
										
										
										<set 
										
										
										
										value='$dummy_count' 
										
										link='#'
										
										/>
										
										
										<set 
										
										
										
										value='$double3335_count' 
										
										link='#'
										
										/>
										
									</dataset>
									
									
									<dataset seriesname='ปิดงานแล้ว'>
										<set 
										
										
										
										value='$lawful_company_count_closed' 
										
										link='org_list.php?LawfulFlag=1&ddl_year=$the_end_year".$extra_link."&dozone=1'
										
										color='40FF00'
										
										/>
										
									</dataset>
									
									
									
									
								</chart>
								";
							
						
                            // Create a Column 2D Chart with data from Data/Data.xml
                           
                            ?>
							
                            
                            <div align="center">
                            <?php  echo renderChart("Charts/StackedColumn2D.swf", "", "$strXML", "budget_monthly", 325, 250, false, true);?>
                            </div>
							
                            
                            
                            </td>
                            <td width="350">
                            
                            	<div align="center">
                            	<table>
                                
                                
                                
                                	<?php if(1==0){ // yoes 20151130 -- remove this for now?>
                                
                                	<tr>
                                    	<td colspan="2">
                                        <div style=" color:#006699">
                                        ข้อมูลผู้ใช้งาน
                                        </div>
                                        </td>
                                       
                                    </tr>
                                	<tr>
                                    	<td>
                                        <?php 
										
										$user_info_row = getFirstRow("select * from users where user_id = '$sess_userid'");
										
										?>
                                        User Name: 
                                        </td>
                                        <td>
                                       <?php echo $user_info_row[user_name];?>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>
                                       
                                        ชื่อ-นามสกุล
                                        </td>
                                        <td>
                                       <?php echo $user_info_row[FirstName];?> <?php echo $user_info_row[LastName];?>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>
                                       
                                       ตำแหน่ง
                                        </td>
                                        <td>
                                       <?php echo $user_info_row[user_position];?>
                                        </td>
                                    </tr>
                                    
                                    <?php }?>
                                    
                                    
                                    
                                    
                                    <?php 
									
									if($_POST["Province"] && !$_POST["zone_id"]){ //yoes 20151130 -- show staff for that province...
									
										if($_POST["Province"] == 1){
											$staff_sql = "select * from users where AccessLevel = '2' and user_enabled = 1";
										}else{
											$staff_sql = "select * from users where user_meta = '".$_POST["Province"]."' and AccessLevel = '3' and user_enabled = 1";
										}
										
										$staff_result = mysql_query($staff_sql);
										
										?>
                                        
                                        
											<?php if(mysql_num_rows ($staff_result )){?>
                                            <tr>
                                                <td colspan="2">
                                                <div style=" color:#060; padding: 5px 0;">
                                                ข้อมูลผู้รับผิดชอบ จังหวัด<?php 
												
												echo getFirstItem("select province_name from provinces where province_id = '".($_POST["Province"]*1)."'")
												
												?>
                                                
                                                <?php if(mysql_num_rows ($staff_result ) > 2){ ?>
                                                
	                                                <?php echo mysql_num_rows ($staff_result )?> คน
                                                
                                                <?php }?>
                                                
                                                </div>
                                                </td>
                                               
                                            </tr>
                                            <?php }?>
                                        
                                        <?php
										
										
										$count_staff = 0;
										
										while($staff_row = mysql_fetch_array($staff_result)){
											
											$count_staff++;
											
											
											//yoes 20151208 -- only show 2 staffs by default
											
											if($count_staff <= 2 ){
											
										?>
                                        
                                            <tr>
                                                <td>                                                
                                                User Name: 
                                                </td>
                                                <td>
                                               <?php echo $staff_row[user_name];?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                               
                                                ชื่อ-นามสกุล
                                                </td>
                                                <td>
                                               <?php echo $staff_row[FirstName];?> <?php echo $staff_row[LastName];?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 1px solid #CCC;">
                                               
                                               หน่วยงาน/ตำแหน่ง
                                               
                                                </td>
                                                <td style="border-bottom: 1px solid #CCC;">
                                               <?php echo $staff_row[Department]; ?>
                                                <?php echo $staff_row[user_position];?>
                                               
                                                </td>
                                            </tr>
                                        
                                        
                                        
                                    <?php
									
											}//ends if($count_staff <= 2 ){
												
												
											if($count_staff == 3 ){ 
									?>
                                    
                                    
                                    		 <tr >
                                                <td colspan="2" style="border-bottom: 1px solid #CCC;">      
                                                	<div align="center" style="padding: 5px;">
                                                    
                                                    <a href="#"  onclick="$('.hideme').toggle();">
                                                     ... แสดงรายชื่อผู้รับผิดชอบทั้งหมด
                                                     
                                                     </a>
                                                    
                                                    </div>                                               
                                                </td>
                                            </tr>
                                    
                                    <?php 
											}
												
											//yoes 20151208 --- show as "hidden" instead
											if($count_staff > 2 ){ 
									?>
									
                                    		 <tr class="hideme" style="display: none;">
                                                <td>                                                
                                                User Name: 
                                                </td>
                                                <td>
                                               <?php echo $staff_row[user_name];?>
                                                </td>
                                            </tr>
                                            <tr class="hideme" style="display: none;">
                                                <td>
                                               
                                                ชื่อ-นามสกุล
                                                </td>
                                                <td>
                                               <?php echo $staff_row[FirstName];?> <?php echo $staff_row[LastName];?>
                                                </td>
                                            </tr>
                                            <tr class="hideme" style="display: none;" >
                                                <td style="border-bottom: 1px solid #CCC;">
                                               
                                               หน่วยงาน/ตำแหน่ง
                                               
                                                </td>
                                                <td style="border-bottom: 1px solid #CCC;">
                                               <?php echo $staff_row[Department]; ?>
                                                <?php echo $staff_row[user_position];?>
                                               
                                                </td>
                                            </tr>		
											
											
									<?php	
											}//end if count_staff > 2
											
										}	//end while	
										
										?>
                                        
                                        <script>
										
											$('.hideme').hide();
										
										</script>
                                        
                                    <?php							
										
									
									} //end if POST provine
									
									?>
                                    
                                    
                                    
                                    
                                    <?php 
									
									//echo $_POST["zone_id"];
									
									if($_POST["zone_id"]){ //yoes 20151130 -- show staff for that zone...
									
										$staff_sql = "
											select 
												* 
											from 
												users a
													join zone_user b
														on a.user_id = b.user_id
														
											where
											
												(
												a.AccessLevel = '2' 
												or
												a.AccessLevel = '3' )
												and 
												a.user_enabled = 1
												and
												b.zone_id = '".($_POST["zone_id"]*1)."'
												
											";
										
										$staff_result = mysql_query($staff_sql);
										
										?>
                                        
                                        
											<?php if(mysql_num_rows ($staff_result )){?>
                                            <tr>
                                                <td colspan="2">
                                                <div style=" color:#006699">
                                                ข้อมูลผู้รับผิดชอบ พื้นที่การทำงาน <?php 
												
												echo getFirstItem("select zone_name from zones where zone_id = '".($_POST["zone_id"]*1)."'")
												
												?>
                                                </div>
                                                </td>
                                               
                                            </tr>
                                            <?php }else{?>
                                            
                                           
                                                <tr>
                                                    <td colspan="2">
                                                    <div style=" color:#F00">
                                                    ไม่มีผู้รับผิดชอบพื้นที่การทำงาน 
                                                    </div>
                                                    </td>
                                                   
                                                </tr>
                                            
                                            
                                            <?php }?>
                                        
                                        <?php
										
										
										while($staff_row = mysql_fetch_array($staff_result)){
											
										?>
                                        
                                            <tr>
                                                <td>                                                
                                                User Name: 
                                                </td>
                                                <td>
                                               <?php echo $staff_row[user_name];?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                               
                                                ชื่อ-นามสกุล
                                                </td>
                                                <td>
                                               <?php echo $staff_row[FirstName];?> <?php echo $staff_row[LastName];?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 1px solid #CCC;">
                                               
                                               ตำแหน่ง
                                               
                                                </td>
                                                <td style="border-bottom: 1px solid #CCC;">
                                               <?php echo $staff_row[user_position] . $staff_row[Department];?>
                                               
                                                </td>
                                            </tr>
                                        
                                        
                                        
                                    <?php
											
											
										}									
										
									
									}
									
									?>
                                    
                                    
                                    <?php if($sess_accesslevel == 3){ //only พมจ have province?>
                                    <tr>
                                    	<td>
                                       
                                       จังหวัด
                                        </td>
                                        <td>
                                       <?php echo getFirstItem("select province_name from provinces where province_id = '".$user_info_row[user_meta]."'");?>
                                        </td>
                                    </tr>
                                    <?php }?>
                                
                                	<tr>
                                    	<td colspan="2">
                                        <div style=" color:#060; padding: 5px 0;">
                                        ภาพรวมระบบปี <?php echo $the_end_year+543;?>
                                        </div>
                                        </td>
                                       
                                    </tr>
                                    
                                  <?php if($my_zone){?>  
                                	<tr>
                                	  <td>พื้นที่รับผิดชอบ</td>
                                	  <td><?php echo getFirstItem("select zone_name from zones where zone_id = '$my_zone' ") ;?></td>
                              	  </tr>
                                  <?php }?>
                                  
                                	<tr>
                                    	<td>
                                        สถานประกอบการ<br />ปฏิบัติตามกฎหมายแล้ว:
                                        </td>
                                        <td>
                                        <a href="org_list.php?LawfulFlag=1&ddl_year=<?php echo $the_end_year?>">
                                       <?php echo number_format($lawful_company_count,0);?> / <?php echo number_format($all_company_count,0);?>
                                       </a>
                                        แห่ง
                                        </td>
                                    </tr>
                                    
                                    <?php if($sess_accesslevel != 8){?>
                                    <tr>
                                    	<td>
                                        ผู้ใช้งานสถานประกอบการ รออนุมัติใช้งาน:
                                        </td>
                                        <td>
                                        <a href="user_list.php?user_enabled=0"><strong style="color:#900;">
                                       <?php 
									   
									   //yoes 20151118 -- non-admin wont see this for now
									   //echo $sess_accesslevel; echo $sess_can_manage_user;
									  
									   //yoes 20161201 -- change it so PMJ can see and edit all
									   //if($sess_accesslevel == 3 && $sess_can_manage_user){
									   if($sess_accesslevel == 3){	   
											//$user_filter_sql = " and 1=0";
											//yoes 20160118 - special for พมจ users
											//echo "----->". $sess_can_manage_user . "<---";
											//can see users under own province
											
											$user_filter_sql = " and b.Province = '$sess_meta'";
											$user_filter_sql_approval = " and z.Province = '$sess_meta'";
									   }
									   
									   //yoes 20161201 -- pmj only see Bangkok
									    if($sess_accesslevel == 2){	   
											//$user_filter_sql = " and 1=0";
											//yoes 20160118 - special for พมจ users
											//echo "----->". $sess_can_manage_user . "<---";
											//can see users under own province
											
											$user_filter_sql = " and b.Province = '1'";
											$user_filter_sql_approval = " and z.Province = '1'";
									   }
									   
								
										
										//yoes 20160215 -- disallow edit for พมจ who cant edit user	
										//yoes 20161201 -- change it so PMJ can see and edit all								   
									  /* if($sess_accesslevel == 3 && !$sess_can_manage_user){											
											$user_filter_sql = " and 1=0";
									   }*/
									   
									   
									   echo getFirstItem("
										
											SELECT count(*) FROM users a left outer join company b on a.user_meta = b.cid where user_enabled like '%0%' 
											$user_filter_sql
										
										");
									   
									   ?></strong></a> คน
                                        </td>
                                    </tr>                                    
                                    
                                    <tr>
                                    	<td>
                                       สถานประกอบการ<br /> ยื่นแบบออนไลน์:
                                        </td>
                                        <td>
                                        
                                        <a href="org_list_approve.php?lawful_submitted=1&ddl_year=<?php echo $the_end_year?>"><strong style="color:#900;">
                                       <?php 
									   
									   echo getFirstItem("
		
											SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '$the_end_year' left join lawfulness_company xxx on z.CID = xxx.CID where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and xxx.Year = '$the_end_year' and (xxx.lawful_submitted = '1')
										
										$province_sql
										
										");
									   
									   ?>
                                       </strong></a>
                                       
                                        แห่ง
                                        </td>
                                    </tr>
                                    
                                    <?php } //if($sess_accesslevel != 8){?>
                                </table>
                                </div>
                            
                            
                            
                            
                            
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                            
								
								<?php 
								
									// yoes 20210218
									if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3){
										include "modal_diff_lawful_employees_end_date.php";
									}
									
									//include "modal_diff_company_status.php";
									
								?>
                            	
                                <hr>
                                
                            	<div align="center">
                            	<table border="1" style="border:1px solid #CCC; border-collapse: collapse;<?php 								
									if($sess_accesslevel == 8){
										echo "display: none;";	
									}								
								?>">
                                	<tr>
                                    	<td colspan="4">
                                     	<div align="center" style=" color:#060">
                                        สถานประกอบการ ยื่นแบบออนไลน์
                                        </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td align="center">
                                        <div align="center">
                                     	 วันที่
                                          </div>
                                        </td>
                                    	<td align="center">
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                        </td>
                                    	<td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัด
                                          </div>
                                          
                                      </td>
                                    	<td align="center">
                                        <div align="center">
                                     	 สถานะ
                                          </div>
                                        </td>
                                    </tr>
                                    
                                    <?php 
									
									$sql = "
									
									
									SELECT 
										z.CID 
										, Province 
										, CompanyCode 
										, CompanyTypeName
										, z.CompanyTypeCode 
										, CompanyNameThai 
										, province_name 
										, LawfulFlag 
										, y.LawfulStatus as lawfulness_status 
										, y.Employees as lawful_employees 
									FROM 
										company z 
											LEFT outer JOIN companytype b 
												ON z.CompanyTypeCode = b.CompanyTypeCode 
											LEFT outer JOIN provinces c 
												ON z.province = c.province_id 
											JOIN lawfulness y 
												ON z.CID = y.CID and y.Year = '$the_end_year' 
												
											left join lawfulness_company xxx 
												on z.CID = xxx.CID 
												
									where 
										1=1 
										and z.CompanyTypeCode < 200 
										and BranchCode < 1 
										and xxx.Year = '$the_end_year' 
										and (xxx.lawful_submitted = '1') 
									
										$province_sql
									
									
									order by lawful_submitted_on asc
									
									";
									
									$submit_result = mysql_query($sql);
									
									$cur_year = $the_end_year;
									
									while($post_row = mysql_fetch_array($submit_result)){
										
									?>
                                    
                                    <tr>
                                   	  <td>
                                     	 
                                          <div align="center">
                                            
                                            <?php echo 
                                            
                                            
                                            formatDateThaiShort(
                                                getFirstItem("
                                                        select 
                                                            lawful_submitted_on 
                                                        from 
                                                            lawfulness_company 
                                                        where 
                                                            CID = '".$post_row["CID"]."' 
                                                            and 
                                                            Year = '$the_end_year'")
                                            );
                                            
                                            ?>
                                        </div>
                                         
                                      </td>
                                   	  <td>
                                     	 <a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&all_tabs=1&year=<?php echo $cur_year;?>"><?php 
										 
										 
										 
										 //echo doCleanOutput($post_row["CompanyNameThai"]);
										 
										 
										 echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);
										 
										 
										 
										 ?></a>                          
                                      </td>
                                   	  <td>
                                      <?php 
									  
									  
									  echo getFirstItem("select province_name from provinces where province_id = '".$post_row["Province"]."'");
									  
									  ?>
                                      
                                      </td>
                                   	  <td>
                                      
                                      <div align="center"><?php //echo $post_row["lawfulness_status"]; 
								
											echo getLawfulImage(($post_row["lawfulness_status"]));
								
										?></div>
                                     	 
                                      </td>
                                    </tr>
                                    
                                    <?php
										
										
									}																
									
									?>
                                </table>
                              </div>
                              
                              
                              
                               <hr />
                               
                                 <?php 
							  
							  //yoes 20170516 -- admin, pmj and some users can see this approval m34 things
							  if($sess_accesslevel == 1 || $sess_accesslevel == 3 || $sess_can_approve_34){
								
								  
							   ?>
                              
                              
                              <div align="center">
                            	<table border="1" style="border:1px solid #CCC; border-collapse: collapse;">
                                	<tr>
                                    	<td colspan="5">
                                     	<div align="center" style=" color:#060">
                                       คำขอต้องการเพิ่มใบเสร็จตามมาตรา 34
                                        </div>
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                    	                       
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 เล่มที่ใบเสร็จ
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center"> 
                                        
                                        <div align="center">
                                     	 เลขที่ใบเสร็จ
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
                                        ข้อมูลปี
                                        
                                        </div>
                                        </td>
                                        
                                        
                                        <td align="center">
                                        <div align="center">
                                     	 ประเภทการขอ
                                          </div>
                                        </td>
                                    	
                                    	<td align="center">
                                        <div align="center">
                                     	 ผู้ขอ
                                          </div>
                                        </td>
                                    </tr>
                                    
                                    
                                    <?php 
									
									//yoes 20170516
									if($sess_accesslevel == 3){
										
										//provincial staff only see self-provice
										$request_filter = " and d.Province = '$sess_meta'";	
										
									}
									
									$request_sql = "
										
										
										SELECT 
											*
										FROM 
											payment_request a
												join
													receipt_request b												
												on
													a.RID = b.RID		
													
												join lawfulness c
													on
													a.LID = c.LID		
												
												join company d
													on
													c.CID = d.CID							
										where
											request_status = 0
											
											$request_filter
									
									";
									
									//echo $request_sql;
									
									
									$request_result = mysql_query($request_sql);
									
									
									while($request_row = mysql_fetch_array($request_result)){
										
										$receipt_row = $request_row;
									
									?>
                                    
                                     <tr>
                                       <td align="center" style="text-align: center"><a href="view_payment.php?id=<?php echo $receipt_row[RID]?>&view=request" target="_blank">
                                       
                                       <?php 
									   	
										
														
										echo $receipt_row[BookReceiptNo];
									   
									   ?>
                                       
                                       </a></td>
                                       <td align="center" style="text-align: center"><a href="view_payment.php?id=<?php echo $receipt_row[RID]?>&view=request"> <?php 
									   	
										echo $receipt_row[ReceiptNo];
									   
									   ?></a></td>
                                       <td align="center" style="text-align: center"><?php 
									   	
										echo $receipt_row[ReceiptYear]+543;
									   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
										   echo "เพิ่มใบเสร็จ";
										   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
									    echo getFirstItem("select concat(FirstName, ' ', LastName) from users where user_id = '".$request_row[request_userid]."'") ;
									   
									   ?></td>
                                     </tr>
                                     
                                     <?php 
									 
									}
									 
									 ?>
                                    
                                    
                                 </table>
                              
                              
                              <hr />
                              
                            
                               
                              <div align="center">
                            	<table border="1" style="border:1px solid #CCC; border-collapse: collapse;">
                                	<tr>
                                    	<td colspan="5">
                                     	<div align="center" style=" color:#060">
                                       คำขอต้องการแก้ไขหรือยกเลิกข้อมูลใบเสร็จตามมาตรา 34
                                        </div>
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                    	                       
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 เล่มที่ใบเสร็จ
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center"> 
                                        
                                        <div align="center">
                                     	 เลขที่ใบเสร็จ
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
                                        ข้อมูลปี
                                        
                                        </div>
                                        </td>
                                        
                                        
                                        <td align="center">
                                        <div align="center">
                                     	 ประเภทการขอ
                                          </div>
                                        </td>
                                    	
                                    	<td align="center">
                                        <div align="center">
                                     	 ผู้ขอ
                                          </div>
                                        </td>
                                    </tr>
                                    
                                    
                                    <?php 
									
									$request_sql = "
										
										
										SELECT 
											b.RID
											, b.BookReceiptNo
											, b.ReceiptNo
											, b.ReceiptYear
											, b.edit_userid
											, b.Amount as edit_type
										FROM 
										
											payment a
												
												join												
													receipt_edit_request b
												on
													a.RID = b.RID
												
												
												join lawfulness c
													on
													a.LID = c.LID		
												
												join company d
													on
													c.CID = d.CID
												
												
										where
											b.edit_status = 0
											
											$request_filter
											
											
									
									";
									
									//$request_filter
									
									//echo $request_sql;
									
									
									$request_result = mysql_query($request_sql);
									
									
									while($request_row = mysql_fetch_array($request_result)){
										
										
										$receipt_row = getFirstRow(
											
															"select * from receipt where RID = '".$request_row[RID]."'"
														
														);
									
									?>
                                    
                                     <tr>
                                       <td align="center" style="text-align: center"><a href="view_payment.php?id=<?php echo $receipt_row[RID]?>" target="_blank">
                                       
                                       <?php 
									   	
										
														
										echo $receipt_row[BookReceiptNo];
									   
									   ?>
                                       
                                       </a></td>
                                       <td align="center" style="text-align: center"><a href="view_payment.php?id=<?php echo $receipt_row[RID]?>"> <?php 
									   	
										echo $receipt_row[ReceiptNo];
									   
									   ?></a></td>
                                       <td align="center" style="text-align: center"><?php 
									   	
										echo $receipt_row[ReceiptYear]+543;
									   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
										   if($request_row[edit_type]){
												
												echo "แก้ไขใบเสร็จ";   
										   }else{
											   	echo "ยกเลิกใบเสร็จ";
										   }
										   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
									    echo getFirstItem("select concat(FirstName, ' ', LastName) from users where user_id = '".$request_row[edit_userid]."'") ;
									   
									   ?></td>
                                     </tr>
                                     
                                     <?php 
									 
									}
									 
									 ?>
                                    
                                    
                                 </table>
                                 
                              <?php }//end m34 for admin and pmj?>   
                              							  
							  
							  
							  
							  
							  
							  <hr />
							 
							 <?php //bank add 20230601 new table for law request to change employees MA 2022 ข้อ 4 ?>
							 
							 <?php 
																	
																	
																	$url = "http://203.154.94.105/law_system/law_ws/getLawfulChangeRequest.php";


																	$ch = curl_init();
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_URL,$url);
																	$result=curl_exec($ch);
																	curl_close($ch);

																	
																	$array = json_decode($result, true);	
																	//$data = json_encode($array);
																	$data = $array["data"]["LawEditList"];
																	$total_law_edit = count($data);
																	
																	
															
																	
																	?>
																	
																	
							  
							  <?php if($sess_accesslevel == 1 ||  $sess_accesslevel == 2){?>
							  
							  <table border="1" style="border:1px solid #CCC; border-collapse: collapse;">

									<tr>
                                    	<td colspan="10">
                                     	<div align="center" style=" color:#060">
											 การแจ้งข้อเปลี่ยนจำนวนลูกจ้าง จากระบบงานคดี
                                        </div>
                                        </td>
                                    </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	 ชื่อบริษัท
                                          </div>
                                          
                                          </td>											   
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 ปีปฏิบัติตามกฎหมาย
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center"> 
                                        
                                        <div align="center">
                                     	 จำนวนลูกจ้างเดิม
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
										จำนวนลูกจ้างที่ขอเปลี่ยน 
                                        
                                        </div>
                                        </td>
                                        
                                         <td align="center">
                                        
                                        <div align="center">
                                        
										ชื่อ user งานคดีที่ขอเปลี่ยน 
                                        
                                        </div>
                                        </td>
                                     
										
										 <td align="center">
                                        
                                        <div align="center">
                                        
										วันที่ส่ง 
                                        
                                        </div>
                                        </td>
										
										 
										
										
                                    </tr>
                                    
									
                                    
                                    <?php 
									
									
									//echo $request_sql;
									
																		
									
									for ($i = 0; $i <= $total_law_edit; $i++)  { ?>
                                    
									
									
									<?php if($data[$i]["CompanyNameThai"]){ ?>
									
                                     <tr>
                                       <td align="center" style="text-align: center">
									   <a href="organization.php?id=<?php echo $data[$i]["CID"];?>&focus=lawful&year=<?php echo $data[$i]["Year"];?>">
                                       	<?php echo $data[$i]["CompanyNameThai"];?>
                                       </a>
									   
									   </td>
                                       <td align="center" style="text-align: center"> <?php 
									   	
										if($data[$i]["Year"]){
											echo formatYear($data[$i]["Year"]);
										}
									   
									   ?></a></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									
										echo $data[$i]["EmployeesOld"];	
										
										
	
									   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
										echo $data[$i]["Employees"];	
										   
									   ?></td>
									   
									   <td align="center" style="text-align: center"><?php 
									   
									   
										echo $data[$i]["user_name"];	
										   
									   ?></td>
									   
									   
									   <td align="center" style="text-align: center"><?php 
									   
									    
										echo formatDateThai($data[$i]["CreatedDateTime"],1,5);	
										   
									   ?></td>

                                     </tr>
                                     
									<?php } ?>
                                  <?php } ?>
                                    
                                    
                                 </table>
								 
							  <?php } ?>
							  
							  <?php // END bank add 20230601 new table for law request to change employees MA 2022 ข้อ 4 ?>
							  
							  
							  
							  
							  
							  
							  
							  
							  <hr />
							 
							 <?php //bank add 20230801 MA 2022 ข้อ 15 log request from law_system ?>
							 <?php //$prefix_province = getFirstItem("select province_name from provinces where province_id = '$sess_meta'");  ?>
							 
							 <?php 
																	
																	$url = "http://203.154.94.105/law_system/law_ws/getLawfulChangeRequest.php?mode=report";

																	
																	$ch = curl_init();
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_URL,$url);
																	$result=curl_exec($ch);
																	curl_close($ch);
																	
																	

																	
																	$array = json_decode($result, true);	
																	//$data = json_encode($array);
																	$data = $array["data"]["LawEditList"];
																	//$filter_data = getFirstItem("select concat(FirstName, ' ', LastName) from users where user_id = '".$data[CID]."'") ;

																	
																	
																	$law_array = array();
																	if($sess_meta == "0"){
																		
																		$total_law_edit = count($data);
																	   //$province_code_law = 1;
																	   
																	}else{
																		
																		$province_code_law = $sess_meta;
																		for ($i = 0; $i <= $total_law_edit; $i++)  {
																		
																		$filter_sql = "select * from company c where c.cid = '".$data[$i]["CID"]."' and c.Province = '$province_code_law'" ;
																		
																		$result = mysql_query($filter_sql);
									
									
																		while($law_row = mysql_fetch_array($result)){
																			
																			$law_array[] = $law_row;
																			
																			}
																		}
																		$total_law_edit = count($law_array);
																		
																	}
																	
																	
																	
																	//$data[1]["CID"]
																	//echo $total_law;
																	?>
																	
																	
							  <?php if($sess_accesslevel == 1 ||  $sess_accesslevel == 2){?>
							  <table border="1" style="border:1px solid #CCC; border-collapse: collapse;">

									<tr>
                                    	<td colspan="10">
                                     	<div align="center" style=" color:#060">
											 รายการข้อเปลี่ยนแปลงจำนวนลูกจ้างจากงานคดี
                                        </div>
                                        </td>
                                    </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	 ชื่อบริษัท
                                          </div>
                                          
                                          </td>											   
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 ปีปฏิบัติตามกฎหมาย
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center"> 
                                        
                                        <div align="center">
                                     	 จำนวนลูกจ้างเดิม
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
										จำนวนลูกจ้างที่ขอเปลี่ยน 
                                        
                                        </div>
                                        </td>
                                        
                                         <td align="center">
                                        
                                        <div align="center">
                                        
										ชื่อ user งานคดีที่ขอเปลี่ยน 
                                        
                                        </div>
                                        </td>
                                     
										
										 <td align="center">
                                        
                                        <div align="center">
                                        
										วันที่ส่ง 
                                        
                                        </div>
                                        </td>
										
										
										<td align="center">
                                        
                                        <div align="center">
                                        
										วันที่รับ
                                        
                                        </div>
                                        </td>
										 
										
										<td align="center">
                                        
                                        <div align="center">
                                        
										วันที่ยกเลิก
                                        
                                        </div>
                                        </td>
										
                                    </tr>
                                    
									
                                    
                                    <?php 
									
									
									//echo $request_sql;
									
																		
									
								for ($i = 0; $i <= $total_law_edit; $i++)  { ?>
                                    
									
								<?php 
								if($sess_meta == "0"){
									$province_law = 1;
									$prefix_cid = getFirstItem("select c.CID from company c where c.cid = '".$data[$i]["CID"]."' and c.Province = '$province_law'"); 
								
								}else{
									
									$province_law = $sess_meta;
									$prefix_cid = getFirstItem("select c.CID from company c LEFT JOIN Provinces p on c.Province = p.province_id where c.cid = '".$data[$i]["CID"]."' and c.Province = '$province_law'"); 
								}
								?>
	
								<?php if($data[$i]["CID"] == $prefix_cid){ ?>
									
                                     <tr>
                                       <td align="center" style="text-align: center">
									   <a href="organization.php?id=<?php echo $data[$i]["CID"];?>&focus=lawful&year=<?php echo $data[$i]["Year"];?>">
                                       	<?php echo $data[$i]["CompanyNameThai"];?>
                                       </a>
									   
									   </td>
                                       <td align="center" style="text-align: center"> <?php 
									   	
										if($data[$i]["Year"]){
											echo formatYear($data[$i]["Year"]);
										}
									   
									   ?></a></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									
										echo $data[$i]["EmployeesOld"];	
										
										
	
									   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
										echo $data[$i]["Employees"];	
										   
									   ?></td>
									   
									   <td align="center" style="text-align: center"><?php 
									   
									   
										echo $data[$i]["user_name"];	
										   
									   ?></td>
									   
									   
									   <td align="center" style="text-align: center"><?php 
									   
									    
										echo formatDateThai($data[$i]["CreatedDateTime"],1,5);	
										   
									   ?></td>
									   
									   
									   	<td align="center" style="text-align: center">
										
										<?php if($data[$i]["Approve_accept"] != '0000-00-00 00:00:00'){ ?>
										<?php echo formatDateThai($data[$i]["Approve_accept"],1,5); ?>
										<?php }else{ ?>
										<?php echo "-"; ?>
										<?php } ?>
										</td>
										
											<td align="center" style="text-align: center">
										
										<?php if($data[$i]["Approve_reject"] != '0000-00-00 00:00:00'){ ?>
										<?php echo formatDateThai($data[$i]["Approve_reject"],1,5); ?>
										<?php }else{ ?>
										<?php echo "-"; ?>
										<?php } ?>
										</td>
										
										
                                     </tr>
                                     
									 
								<?php } ?>
                              <?php } ?>
                                    
                                    
                                 </table>
								 
							  <?php }?>
							  
							  <?php // END //bank add 20230801 MA 2022 ข้อ 15 log request from law_system ?>
							  
							  
							  
							  
							  
							  
							  
							  
							  
							  
							  
							  
 							  <?php //break bank ?>
							  <hr />
							  
							        <table border="1" style="border:1px solid #CCC; border-collapse: collapse;">

									<tr>
                                    	<td colspan="10">
                                     	<div align="center" style=" color:#060">
											แจ้งการเปลี่ยนที่อยู่
                                        </div>
                                        </td>
                                    </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                          
                                          </td>											   
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัดที่ส่ง
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัดที่รับ
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
										วันที่ส่ง
                                        
                                        </div>
                                        </td>
                                        
                                        
                                     

                                        </td>
										
										
										
										
										
                                    </tr>
                                    
									
                                    
                                    <?php 
									if($sess_accesslevel == 3){
										
										$request_sql = "
											
											
											SELECT 
												a.cid
												, a.CompanyNameThai
												, a.CompanyTypeCode
												, a.Address1
												, a.Moo
												, a.Soi		
												, a.Road
												, a.Subdistrict
												, a.District
												, p.province_name
												, a.Zip
												, a.edit_date
												, a.Province_accepted
												, l.year
												
											FROM 
											
												company_edit_request a 

													
												join  company c
													on a.cid = c.cid
												join lawfulness l
														on a.cid = l.cid
												join provinces p 
														on c.Province = p.province_id
												where l.year = (select max(ll.year) from lawfulness ll where ll.cid = l.cid)
												and a.Province = $sess_meta
												and a.approve_date = '0000-00-00 00:00:00'
												and a.Province != c.Province
										
												
											
										
										";
										
									}else{
											$request_sql = "
											
												SELECT 
													a.cid
													, a.CompanyNameThai
													, a.CompanyTypeCode
													, a.Address1
													, a.Moo
													, a.Soi		
													, a.Road
													, a.Subdistrict
													, a.District
													, p.province_name
													, a.Zip
													, a.edit_date
													, a.Province_accepted
													, l.year
													
												FROM 
												
													company_edit_request a 
													join  company c
														on a.cid = c.cid
													join lawfulness l
														on a.cid = l.cid
													join provinces p 
														on c.Province = p.province_id	
													where l.year = (select max(ll.year) from lawfulness ll where ll.cid = l.cid)
													and a.approve_date = '0000-00-00 00:00:00'
													and a.Province != c.Province
										
											
										
										";
									}
									//$request_filter
									
									//echo $request_sql;
									
									
									$request_result = mysql_query($request_sql);
									
									
									while($request_row = mysql_fetch_array($request_result)){
										
										
									
									?>
                                    
									
									
		
									
                                     <tr>
                                       <td>
									   <a href="organization.php?id=<?php echo $request_row["cid"];?>&all_tabs=1&year=<?php echo $request_row["year"];?>">
                                       	<?php echo formatCompanyName($request_row["CompanyNameThai"],$request_row["CompanyTypeCode"]);?>
									   

                                       
                                       </a>
									   
									   </td>
                                       <td> <?php 
									   	
										echo $request_row[province_name];
									   
									   ?></a></td>
                                       <td align="center" style="text-align: center"><?php 
									   
										$province_accetp = getFirstItem("select province_name from provinces where province_id = '".$request_row["Province_accepted"]."'");
									   	if($province_accetp == '0'){
										
										echo ""; }
										else{
										echo getFirstItem("select province_name from provinces where province_id = '".$request_row["Province_accepted"]."'");	
										
										}
	
									   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
									   echo formatDateThai($request_row[edit_date],1,5);
										   
									   ?></td>
                       
							
                                     </tr>
                                     
                                     <?php 
									 
									}
									 
									 ?>
                                    
                                    
                                 </table>
  	  
								 <?php //bank add 20221222 new dashboard sent back from court ?>
							  <hr />
							  
							        <table border="1" style="border:1px solid #CCC; border-collapse: collapse;">

									<tr>
                                    	<td colspan="15">
                                     	<div align="center" style=" color:#060">
											งานคดีที่ถูกส่งกลับจากระบบการติดตามและดำเนินคดี
                                        </div>
                                        </td>
                                    </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                          
                                          </td>											   
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 ปีการปฏิบัติตามกฎหมาย
                                          </div>
                                          
                                          </td>
                                          
                                          
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 วันที่ส่งไปงานคดี
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
										วันที่งานคดีส่งกลับ
                                        
                                        </div>
                                        </td>

                                        </td>

                                    </tr>
                                    
									
                                    
                                    <?php 	
									//$request_filter
									
									//echo $request_sql;
									
									
									$courted_table_sql = "left join lawfulness_meta t on b.lid = t.meta_lid";
									//$courted_meta_sql = "and t.meta_for = 'courted_flag'";
									$courted_meta_sql = "and t.meta_for like '%-reject-%'";
									include_once "law_expire.php";
									
									
									//echo $main_sql_sent_back;
									$request_result_2 = mysql_query($main_sql_sent_back);
									$i = 0;
									while($request_row_1 = mysql_fetch_array($request_result_2)){
										$i = $i + 1;
										$get_date_sent = getFirstItem("Select MAX(meta_value) from lawfulness_meta where meta_lid = '".$request_row_1["LID"]."' and meta_for like '%courted_date%-reject-%'");
										$get_date_back = getFirstItem("Select MAX(STR_TO_DATE(SUBSTRING(meta_for, 21, 14), '%Y%m%d%H%i%s')) as meta_value from lawfulness_meta where meta_lid = '".$request_row_1["LID"]."' and meta_for like '%courted_date-rejec%'");
										
										$sql_sent_back = "replace into court_sent_back(court_id,lid, cid , year , CompanyNameThai , sent_date , sent_back)
														  VALUES ( $i
																, '".$request_row_1["LID"]."'
																, '".$request_row_1["CID"]."'
																, '".$request_row_1["Year"]."'
																, '".$request_row_1["CompanyNameThai"]."'
																, '$get_date_sent'
																, '$get_date_back'
														  )";
														  
										mysql_query($sql_sent_back);
										
									}
									
								
							
									//echo $sql_sent_back;
									if($sess_meta == "0"){
											
											$sent_back_qurey = "SELECT *
														FROM court_sent_back
														ORDER BY sent_date DESC
														LIMIT 0 , 30";
									}else{
										
											$cids = array();
											
											$province_law_back = $sess_meta;
											
											$prefix_cid_back = getFirstItem("select count(1) from company c LEFT JOIN Provinces p on c.Province = p.province_id where c.cid = '".$data[$i]["CID"]."' and c.Province = '$province_law_back'"); 
										
											for ($i = 1; $i <= $prefix_cid_back; $i++) {
												
												$cid_no = getFirstItem("select c.cid from company c LEFT JOIN Provinces p on c.Province = p.province_id where c.cid = '".$data[$i]["CID"]."' and c.Province = '$province_law_back'"); 
												
												$cids[] = $cid_no;
											}
											
											$in_condition = '(' . implode(',', $cids) . ')';

											$sent_back_qurey = "SELECT *
																FROM court_sent_back
																WHERE cid IN $in_condition
																ORDER BY sent_date DESC
																LIMIT 0, 30";
										
									}
									
									$request_result = mysql_query($sent_back_qurey);
										
									while($request_row = mysql_fetch_array($request_result)){?>

									 
                                     <tr>
                                       <td>
									   <div align="center" style="text-align: center">
									    <a href="organization.php?id=<?php echo $request_row["cid"];?>&focus=lawful&year=<?php echo $request_row["year"];?>">
                                       	<?php 
										$company_name_uses = formatCompanyName($request_row["CompanyNameThai"],$request_row["CompanyTypeCode"],$request_row["CompanyTypeName"]);
										
										echo $company_name_uses;?>
									   </a>

                                       </div>
                                       </a>
									   
									   </td>
                                       <td><div align="center" style="text-align: center">
									   <?php 
									   	
										echo $request_row["year"] + 543;
									   
									   ?></div></td>
                                       
									   <td align="center" style="text-align: center"><?php 
									   
									   	//$get_date_sent = getFirstItem("Select MAX(meta_value) from lawfulness_meta where meta_lid = '".$request_row["LID"]."' and meta_for like '%courted_date%-reject-%'");
										//echo formatDateThai($get_date_sent,1,5);
										echo formatDateThai($request_row["sent_date"],1,5);
									   ?></td>
									   
									   
									   
									   
                                       <td align="center" style="text-align: center"><?php 
									    
										//$sql = "Select meta_value from lawfulness_meta where meta_lid = '".$request_row["LID"]."' and meta_for like '%courted_%-reject-%'";
										//$get_date_back = getFirstItem("Select MAX(STR_TO_DATE(SUBSTRING(meta_for, 21, 14), '%Y%m%d%H%i%s')) as meta_value from lawfulness_meta where meta_lid = '".$request_row["LID"]."' and meta_for like '%courted_date-rejec%'");
										//echo formatDateThai($get_date_back,1,5);
										echo formatDateThai($request_row["sent_back"],1,5);
										   
									   ?></td>
                       
							
                                     </tr>
                                     
                                     
									 
									 <?php } ?>
									 
								
									 
									 
                                    
                                    
                                 </table>
							  
							                                 
							 <?php //bank add dashboard list org expire in 8m , 1y , 2y (top 50) ?>
							  <hr />
							  
							    <table border="1" style="border:1px solid #CCC; border-collapse: collapse;">

									<tr>
                                    	<td colspan="15">
                                     	<div align="center" style=" color:#060">
											สถานประกอบการที่เข้าข่ายต้องถูกส่งดำเนินคดี เนื่องจากคดีใกล้หมดอายุ
                                        </div>
                                        </td>
                                    </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                          
                                          </td>		

										<td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัด
                                          </div>
                                          
                                          </td>											  
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 ปีการปฏิบัติตามกฎหมาย
                                          </div>
                                          
                                          </td>
                                          
                                          
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 หมดอายุความใน
                                          </div>
                                          
                                          </td>
                                          
                                    

                                    </tr>
                                    
									
                                    
                                    <?php 	
									//$request_filter
									
									//echo $request_sql;
									
									$now = date("Y");
									$now_month = date("m");
									$now_test = date("Y-m-d");
									$current = $now - 10;	

										$expire2Year = $now - 8;
										$expire1Year = $now - 9;	
										
										if($now_month > 4){
											$raw_month = date('Y-m-d', strtotime($now_test. ' - 10 Year'));
											$expire8Month = date('Y', strtotime($raw_month. ' - 8 months'));
										}else{
											$expire8Month  = $now - 9;
										}
									
									//$expire2Year = $current + 2;
									//$expire1Year = $current + 1;
									//$raw_month = date('Y-m-d', strtotime($now_test. ' - 10 Year'));
									//$expire8Month = date('Y', strtotime($raw_month. ' - 8 months'));
									
									$courted_table_sql2 = "left join lawfulness_meta t on b.lid = t.meta_lid";
									
									$the_year = "2011";
									$selectedLawStatus = NULL;
									$now = date("Y");
									$now_month = date("m");
									$now_test = date("Y-m-d");
									$current = $now - 10;	

										$expire2Year = $now - 8;
										$expire1Year = $now - 9;	
										
										if($now_month > 4){
											$raw_month = date('Y-m-d', strtotime($now_test. ' - 10 Year'));
											$expire8Month = date('Y', strtotime($raw_month. ' - 8 months'));
										}else{
											$expire8Month  = $now - 9;
										}
																		
									$showAll = "AND b.Year in ($expire2Year,$expire1Year,$expire8Month)";
									
									if($_POST["CompanyTypeCode"] == "14"){
	
										$typecode_filter = " and a.CompanyTypeCode = '14'";
										$business_type = "หน่วยงานภาครัฐ";
											
									}else{
										$typecode_filter = " and a.CompanyTypeCode != '14'";
										$business_type = "สถานประกอบการ";
									}
									///yoes 201300813 - add GOV only filter
									if($sess_accesslevel == 6 || $sess_accesslevel == 7){
										
										$typecode_filter .= " and a.CompanyTypeCode >= 200  and a.CompanyTypeCode < 300";
										
									}else{
										
										$typecode_filter .= " and a.CompanyTypeCode < 200";
										
									}
									
									$condition_sql = " and b.LawfulStatus in (0,2)";
									
									//$courted_meta_sql2 = "and t.meta_lid not in (select tt.meta_lid from lawfulness_meta tt where tt.meta_lid = b.lid and tt.meta_for = 'courted_flag') ";
									//$showDash = 1;
									
									//bank fix add province filter 20230207
									if($sess_meta == "0"){
										$province_filter = ""; //show all
									}else{
										$province_filter = "and a.Province = ".$sess_meta.""; //show by sess_meta
									}
									
									include "law_expire.php";
									
									//echo $province_filter;
									//echo $main_sql2;
									
									$cnt = 0;
									$max = 50;
									$request_result = mysql_query($main_sql2);
									
									
									
									
									while($request_row = mysql_fetch_array($request_result)){?>
                                    
									
									
									<?php 	
										//bank 20230207 break when reach 50 record
										$cnt++;
											if($cnt == $max) {
												break;
											}
									?>
                                     <tr>
                                       <td>
									   <div align="center">
									   <a href="organization.php?id=<?php echo $request_row["the_company_cid"];?>&focus=lawful&year=<?php echo $request_row["Year"];?>">

                                       	<?php 
										
											$company_name_uses2 = formatCompanyName($request_row["CompanyNameThai"],$request_row["CompanyTypeCode"],$request_row["CompanyTypeName"]);

											
											echo $company_name_uses2;?>
									   
									  
                                       </div>
                                       </a>
									   
									   </td>
									   
									   <td><div align="center">
									   <?php 
									   	
										echo $request_row["province_name"];
									   
									   ?></div></td>
									   
                                       <td><div align="center">
									   <?php 
									   	
										echo $request_row["Year"] + 543;
									   
									   ?></div></td>
                                       
									   <td width="150">
									   <div align="center">
									   
									   <?php 
										echo $request_row["expireYear"];
										//echo $main_sql2;
									   ?></div></td>
									   
				
                       
							
                                     </tr>
                                   
									 
									 
                                     <?php 
									} 
									 ?>
                                    
                                    
                                 </table>
							  
							  
							  
		
							  
							  
							  
                              
                              <?php if(1==1){?>
                              <hr />
                              
                              <div align="center">
                            	<table border="1" style="border:1px solid #CCC; border-collapse: collapse;<?php 								
									if($sess_accesslevel == 8){
										echo "display: none;";	
									}								
								?>">
                                	<tr>
                                    	<td colspan="5">
                                     	<div align="center" style="color: #060; font-family: 'Microsoft Sans Serif';">
                                        รายชื่อสถานประกอบการที่ใช้ลูกจ้างคนพิการซ้ำซ้อนและผู้ดูแลซ้ำซ้อน
                                        </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	
                                    	<td align="center">
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                        </td>
                                    	<td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัด
                                          </div>
                                          
                                      </td>
                                    	<td align="center">
                                        
                                        <div align="center">
                                        
                                        เลขที่บัตรประชาชนที่ซ้ำซ้อน
                                        
                                        </div>
                                        </td>
                                    	<td align="center">
                                        
                                         <div align="center">
                                        
	                                        มาตราที่ซ้ำซ้อน
                                        
                                        </div>
                                        
                                        </td>
                                    	<td align="center">
                                        <div align="center">
                                     	 สถานะ
                                          </div>
                                        </td>
                                    </tr>
                                    
                                    <?php 
									
									$sql = "
									
															 
									  
									
									
									  select
					
											the_code
											
										  from
										
										  (
											select
											le_code as the_code
											, le_name as the_name
											, 'l' as the_type
											
										  from
											lawful_employees
												
											where le_year = '$the_end_year'
										
											union
										
											select
											  curator_idcard as the_code
											  , curator_name as the_name
											  , 'c' as the_type
											from
											  curator, lawfulness
											  where
											  curator_lid = lid
											  and
											  Year = '$the_end_year'
											  
											  and
											  curator_is_disable = 1
											  
										
											  )                    a
										
										
										group by the_code
										having count(the_code) > 1
										
										order by the_code asc
									
									";
									
									//echo $sql;
									//yoes 20220607 - disable due to loads
									//$submit_result = mysql_query($sql);
									
									$cur_year = $the_end_year;
									
									while($post_row = mysql_fetch_array($submit_result)){
										
									?>
                                                
                                                
                                                
                                                
                                                
                                                <?php 
												
												$sub_sql = "
												
													
													select
															a.cid
															, CompanyNameThai
															, CompanyTypeCode
															, Province
															, LawfulStatus
															, le_code		
															, '33' as the_type													
													  from
														company a
															join lawfulness b
														
															  on
																a.cid = b.cid
																and
																b.year = '$the_end_year'
																
															join
																lawful_employees c
																	on a.cid = c.le_cid
																	and
																	b.year = c.le_year
																													 
													  where
													  	le_code = '".$post_row["the_code"]."'
														and
														CompanyTypeCode != '14'
														and
														CompanyTypeCode < 200
														
														
													union
													
													
													select
															a.cid
															, CompanyNameThai
															, CompanyTypeCode
															, Province
															, LawfulStatus
															, curator_idcard as le_code		
															, '35' as the_type
													  from
														company a
															join lawfulness b
														
															  on
																a.cid = b.cid
																and
																b.year = '$the_end_year'
																
															join
																curator c
																	on b.lid = c.curator_lid

																													 
													  where
													  	curator_idcard = '".$post_row["the_code"]."'
														and
														curator_parent = 0
														and
														CompanyTypeCode != '14'
														and
														CompanyTypeCode < 200
														
														
														
												
												";
												
												
												//echo "<br>".$sub_sql;
												//yoes 20220607 - 20220607 - disable due to loads
												//$sub_result = mysql_query($sub_sql);
												
												
												while($sub_row = mysql_fetch_array($sub_result)){
												
												
												?>
                                                
                                                
                                                <tr>
                                                 
                                                  <td>
                                                  
                                                
                                                     
                                                     
                                                     <a href="organization.php?id=<?php echo $sub_row["cid"];?>&year=<?php echo $the_end_year;?>" target="_blank">
                                                     <?php 
                                                     
                                                     
                                                     
                                                     //echo doCleanOutput($post_row["CompanyNameThai"]);
                                                     
                                                     
                                                     echo formatCompanyName($sub_row["CompanyNameThai"],$sub_row["CompanyTypeCode"]);
                                                     
                                                     
                                                     
                                                     ?>
                                                     </a>
                                                                  
                                                  </td>
                                                  <td>
                                                  <?php 
                                                  
                                                  
                                                  echo getFirstItem("select province_name from provinces where province_id = '".$sub_row["Province"]."'");
                                                  
                                                  ?>
                                                  
                                                  
                                                  </td>
                                                  <td>
                                                  
                                                    <?php 
                                                        
                                                        echo $sub_row["le_code"];
                                                    
                                                    ?>
                                                  </td>
                                                  <td>
                                                  
                                                  
                                                  <div align="center">
                                                  
                                                  <?php 
												  
													  if($sub_row["the_type"] == "33"){
														$this_type = "มาตรา 33";				
													}else{
														$this_type = "มาตรา 35";
													}
													
													echo $this_type;
												  
												  ?>
                                                  
                                                  </div>
                                                  
                                                  </td>
                                                 
                                                  <td>
                                                  
                                                  <div align="center"><?php //echo $post_row["lawfulness_status"]; 
                                            
                                                        echo getLawfulImage(($sub_row["LawfulStatus"]));
                                            
                                                    ?></div>
                                                     
                                                     <?php 
                                                     
                                                        //echo getLawfulImageFromLID(($post_row["lawful_id"]));
                                                     ?>
                                                     
                                                  </td>
                                                </tr>
                                                
                                                
                                                
                                                <?php } //end sub-while?>
                                                
                                                
                                                
                                    
                                    <?php
										
										
									}																
									
									?>
                                </table>
                              </div>
							   <?php }?>  
                               
                               
                            
                               
                               
                               
                                <?php if(1==1){?>
                              <hr />
                              
                              <div align="center">
                            	<table border="1" style="border:1px solid #CCC; border-collapse: collapse;<?php 								
									if($sess_accesslevel == 8){
										echo "display: none;";	
									}								
								?>">
                                	<tr>
                                    	<td colspan="4">
                                     	<div align="center" style=" color:#060">
                                        สถานประกอบการที่มีการจ่ายเงินแล้ว แต่ไม่มีรายละเอียด ม.33 และ ม.35
                                        </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	
                                    	<td align="center">
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                        </td>
                                    	<td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัด
                                          </div>
                                          
                                      </td>
                                    	<td align="center">
                                        
                                        <div align="center">
                                        
                                        ข้อมูลปี
                                        
                                        </div>
                                        </td>
                                    	<td align="center">
                                        <div align="center">
                                     	 สถานะ
                                          </div>
                                        </td>
                                    </tr>
                                    
                                    <?php 
									
									$sql = "
									
									
									SELECT 
									
									z.CID , Province , CompanyCode , CompanyTypeName, z.CompanyTypeCode , CompanyNameThai , province_name 
									, LawfulFlag , y.LawfulStatus as lawfulness_status , y.Employees as lawful_employees 
									, y.Year as lawful_year
									, y.lid as lawful_id
									
									FROM 
										company z 
											LEFT outer JOIN companytype b 
												ON z.CompanyTypeCode = b.CompanyTypeCode 
											LEFT outer JOIN provinces c 
												ON z.province = c.province_id 
											JOIN lawfulness y 
												ON z.CID = y.CID and y.Year = '$the_end_year' 
														
																				
											where 
											
											z.CompanyTypeCode < 200 
											and BranchCode < 1											
											and 
											(
												z.cid in (
												
												
													select
														distinct(le_cid)
													from
														lawful_employees
													where
														le_is_dummy_row = 1
														and
														le_year > 2012
														and
														le_year < 3000
												
												)
												
												or 
												
												y.lid in (
												
													select
														curator_lid
													from
														lawfulness aa
															join
																curator bb
															on aa.lid = bb.curator_lid
															and
															aa.Year > 2012
															and
															aa.Year < 3000
													where
														curator_is_dummy_row = 1
														
												)
												
											)
											
									
									$province_sql
									
									
									order by CompanyNameThai asc
									
									";
									
									//echo $sql;
									
									$submit_result = mysql_query($sql);
									
									$cur_year = $the_end_year;
									
									while($post_row = mysql_fetch_array($submit_result)){
										
									?>
                                    
                                    <tr>
                                   	 
                                   	  <td>
                                      
                                      <?php if(1==0){?>
                                     	 <a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&all_tabs=1&year=<?php echo $cur_year;?>">
                                         
                                         <?php }?>
										 
										 
										 <?php 
										 
										 
										 
										 //echo doCleanOutput($post_row["CompanyNameThai"]);
										 
										 
										 echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);
										 
										 
										 
										 ?>
                                         
                                         <?php if(1==0){?>
                                         </a>           
                                         <?php }?>               
                                      </td>
                                   	  <td>
                                      <?php 
									  
									  
									  echo getFirstItem("select province_name from provinces where province_id = '".$post_row["Province"]."'");
									  
									  ?>
                                      
                                      </td>
                                   	  <td>
                                      
                                      
                                      <div align="center"><?php //echo $post_row["lawfulness_status"]; 
								
											//echo ($post_row["lawful_year"]);
											
											//yoes 20161206
											//what year to show here?
											$the_year_array = array();
											
											
											$sql = "select 
														distinct(le_year) 
													from
														lawful_employees
													where
														le_is_dummy_row = 1
														and
														le_cid = '".$post_row["CID"]."'
														and
														le_year > 2012
														and
														le_year < 3000
													";
													
											//echo $sql;
											
											$dummy_result = mysql_query($sql);
											
											while ($dummy_row = mysql_fetch_array($dummy_result)){
											
													array_push($the_year_array, $dummy_row[le_year]);
												
											}
											
											
											
											$sql = "select 
														distinct(a.Year)  as le_year
													from
														lawfulness a
															join
																curator b
															on 
																a.lid = b.curator_lid
													where
														curator_is_dummy_row = 1
														and
														a.CID = '".$post_row["CID"]."'
														and
														a.Year > 2012
														and
														a.Year < 3000
													";
											
											//echo $sql;
											
											$dummy_result = mysql_query($sql);
											
											while ($dummy_row = mysql_fetch_array($dummy_result)){
											
													array_push($the_year_array, $dummy_row[le_year]);
												
											}
											
											$the_year_array = array_unique($the_year_array);
											
											//print_r($the_year_array);
											
											
											for($iii=0;$iii<count($the_year_array);$iii++){
												
												
												if($iii >= 1){
													echo "<br>";	
												}
												
												echo ' <a href="organization.php?id='. doCleanOutput($post_row["CID"]) .'&all_tabs=1&year='. $the_year_array[$iii] .'">';
												
												echo $the_year_array[$iii]+543;
												
												echo "</a>";
											}
											
								
										?></div>
                                      
                                      </td>
                                   	  <td>
                                      
                                      <div align="center"><?php //echo $post_row["lawfulness_status"]; 
								
											echo getLawfulImage(($post_row["lawfulness_status"]),"_grey");
								
										?></div>
                                     	 
                                         <?php 
										 
										 	//echo getLawfulImageFromLID(($post_row["lawful_id"]));
										 ?>
                                         
                                      </td>
                                    </tr>
                                    
                                    <?php
										
										
									}																
									
									?>
                                </table>
                              </div>
							   <?php }?>  
                               
                               
                               
							   
						      <?php if($canViewCollection){?>							   						  
							   <div align="center"><!-- สรุปสถานประกอบการที่ต้องดำเนินการติดตามทวงถาม --> 
							    <hr />
								   <?php 
								   	  $filterZone = "";
								   	  $filterProvince = "";
								   	  $canCreateCollection = hasCreateRoleCollection();
								   	  $zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
								   	  
								   	  if((($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ)) && ($zone_user != null)){
								   	  		
								   	  		
								   	  		$filterZone = " AND
													(
														
														c.District in (
													
															select
																district_name
															from
																districts
															where
																district_area_code
																in (
														
																	select
																		district_area_code
																	from
																		zone_district
																	where
																		zone_id = '$zone_user'
																
																)
															
														)
														or
														c.district_cleaned in (
													
															select
																district_name
															from
																districts
															where
																district_area_code
																in (
														
																	select
																		district_area_code
																	from
																		zone_district
																	where
																		zone_id = '$zone_user'
																
																)
															
														)
													)";							   	  	
											
								   	  }
								   	  
								   	  if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
								   	  	  $filterProvince = " AND (c.Province = ".$sess_meta.")";
								   	  	  
								   	  }						   	
								   	  
								   
								   
									   $companyFilter = ($sess_accesslevel == 6 || $sess_accesslevel == 7)?
														   " AND (c.CompanyTypeCode >= 200)  AND (c.CompanyTypeCode < 300)" :
														   " AND (c.CompanyTypeCode < 200)";
									   $sequestrationSql = "
											   SELECT l0.Year AS YearL0 , l0.CountL0, l2.Year AS YearL2,  l2.CountL2
											   FROM(
												   SELECT l.Year, count(*)  AS CountL0
												   FROM lawfulness l
												   INNER JOIN company c on l.CID = c.CID
												   WHERE  (l.LawfulStatus = '0' or l.LawfulStatus is null)  AND (l.Year in(2011, 2012))												   		  
												   		 $companyFilter
												   		 $filterZone
												   		 $filterProvince
												   GROUP BY l.Year
											   	
											   )l0
											   LEFT OUTER JOIN
											   (
												   SELECT l.Year, count(*)  AS CountL2
												   FROM lawfulness l
												   INNER JOIN company c on l.CID = c.CID
												   WHERE   (l.LawfulStatus = '2') AND (l.Year in(2011, 2012)) 
												   		 $companyFilter
												   		 $filterZone
												   		 $filterProvince
												   GROUP BY l.Year
											   )l2 on l0.Year = l2.Year
											   UNION
											   SELECT l0.Year AS YearL0 , l0.CountL0, l2.Year AS YearL2,  l2.CountL2
											   FROM(
												   SELECT l.Year, count(*)  AS CountL0
												   FROM lawfulness l
												   INNER JOIN company c on l.CID = c.CID
												   WHERE  (l.LawfulStatus = '0' or l.LawfulStatus is null)  AND (l.Year >= 2013) AND (c.BranchCode < 1)												   		  
												   		 $companyFilter
												   		 $filterZone
												   		 $filterProvince
												   GROUP BY l.Year
											   	
											   )l0
											   LEFT OUTER JOIN
											   (
												   SELECT l.Year, count(*)  AS CountL2
												   FROM lawfulness l
												   INNER JOIN company c on l.CID = c.CID
												   WHERE   (l.LawfulStatus = '2') AND (l.Year >= 2013) AND (c.BranchCode < 1)	 
												   		 $companyFilter
												   		 $filterZone
												   		 $filterProvince
												   GROUP BY l.Year
											   )l2 on l0.Year = l2.Year
									   ";									  
									   $sequestrationResult = mysql_query($sequestrationSql);
								   ?>
								   
								   <table border="1" style="border:1px solid #CCC; border-collapse: collapse; width: 100%">
                                	<tr>
                                    	<td colspan="3">
	                                     	<div align="center" style=" color:#060">สรุปสถานประกอบการที่ต้องดำเนินการติดตามทวงถาม</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td class="text-center" style="width: 50px">ปี</td>
                                    	<td class="text-center">ไม่ปฏิบัติตามกฎหมาย</td>
                                    	<td class="text-center">ปฏิบัติตามกฎหมาย<br />แต่ไม่ครบตามอัตราส่วน</td>
                                    </tr>
                                    <?php 
                                    
                                    	$countSequestration = mysql_num_rows($sequestrationResult);
                                    	$sYear = ""; $sl0Amount = ""; $sl2Amount = "";
                                    	while ($row = mysql_fetch_array($sequestrationResult)){
                                    		$sYear = (!is_null($row["YearL0"]))? $row["YearL0"] : $row["YearL2"];
                                    		$sl0Amount = (!is_null($row["CountL0"]))? number_format ($row["CountL0"] , 0 , "." , ",") : "-";
                                    		$sl2Amount = (!is_null($row["CountL2"]))? number_format ($row["CountL2"] , 0 , "." , ",") : "-";
                                    		
                                    		$collectionLink0 = ($canCreateCollection)? '<a href="collection_create.php?for_year='.$sYear.'&LawfulFlag=0">'.$sl0Amount.'</a>' : $sl0Amount;
                                    		$collectionLink2 = ($canCreateCollection)? '<a href="collection_create.php?for_year='.$sYear.'&LawfulFlag=2">'.$sl2Amount.'</a>' : $sl2Amount;
                                    		?>
                                    		<tr>
                                    			<td class="text-center"><?php echo ($sYear + 543);?> </td>
		                                    	<td class="text-right"><?php echo $collectionLink0;?> </td>
		                                    	<td class="text-right"><?php echo $collectionLink2;?> </td>
                                    		</tr>
                                    		
                                    		<?php 
                                    	} 
                                    	
                                    	if($countSequestration == 0){
                                    		?>
                                    		<tr>
                                    			<td class="text-center">- </td>
		                                    	<td class="text-right">- </td>
		                                    	<td class="text-right">- </td>
                                    		</tr>                                    		
                                    		<?php 
                                    	}
                                    ?>
								   </table>							   
							   </div><!-- สรุปสถานประกอบการที่ต้องดำเนินการติดตามทวงถาม -->
							   <?php }?>
							   
							   <!-- สถานประกอบการที่ไม่ปฎิบัติตามกฎหมายครบ 4 ปี  -->							 
							   <?php if($canViewSequestration){									   		
								   	$sTheEndYear = 0;
								   	if(date("m") >= 9){
								   		$sTheEndYear = date("Y")+1; //new year at month 9
								   	}else{
								   		$sTheEndYear = date("Y");
								   	}
								   	
								   	//this default year
								   	$sLawfulYear = $sTheEndYear - 4;
								   	$lawfulyearFilter = " AND ((l.Year >= 2011) AND (l.Year <= $sLawfulYear))";
								   	
								   	$companyFilter = ($sess_accesslevel == 6 || $sess_accesslevel == 7)?
									   	" AND (c.CompanyTypeCode >= 200)  AND (c.CompanyTypeCode < 300)" :
									   	" AND (c.CompanyTypeCode < 200)";
								   	$sequestrationResult = "";
								   	
								   	$filterProvince = "";
								   	
								   	$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
								   	
								   	$filterZone = "";
							   		if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
								   	  	 $filterProvince = " AND (c.Province = ".$sess_meta.")";	

								   	  	 if($zone_user != null){
								   	  	 	$filterZone = " AND
								   	  	 	(
								   	  	 	
									   	  	 	c.District in (									   	  	 		
										   	  	 	select district_name
										   	  	 	from districts
										   	  	 	where district_area_code in (									   	  	 	
												   	  	 	select
												   	  	 	district_area_code
												   	  	 	from
												   	  	 	zone_district
												   	  	 	where
												   	  	 	zone_id = '$zone_user'
												   	  	 	
												   	  	 	)
								   	  	 		
								   	  	 		)
									   	  	 	or
									   	  	 	c.district_cleaned in (									   	  	 		
										   	  	 	select district_name
										   	  	 	from districts
										   	  	 	where district_area_code in (										   	  	 	
											   	  	 	select
												   	  	 	district_area_code
												   	  	 	from
												   	  	 	zone_district
												   	  	 	where
												   	  	 	zone_id = '$zone_user'
												   	  	 	
												   	  	 	)								   	  	 		
								   	  	 		)
								   	  	 	)";
								   	  	 }
								   	}if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
								   		$bangkokId = getBangkokProvinceIDByCode();
								   	  	$filterProvince = " AND (c.Province = ".$bangkokId.")";								   	  	  
								   	}
								   	
							   		if($sLawfulYear >= 2013){
								   		$lawfulyearFilterG1 = " AND (l.Year in(2011, 2012))";								   		
								   		$lawfulyearFilterG2 = " AND ((l.Year > 2012) AND (l.Year <= $sLawfulYear)) AND (c.BranchCode < 1)";
								   		
								   		$sequestrationSql = "
									   		SELECT l.Year, count(*)  AS Count
									   		FROM lawfulness l
									   		INNER JOIN company c on l.CID = c.CID
									   		WHERE  ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2')) AND (c.LawStatus = 1)
									   		$companyFilter
									   		$lawfulyearFilterG1
									   		$filterProvince
									   		$filterZone
									   		GROUP BY l.Year
									   		UNION
									   		SELECT l.Year, count(*)  AS Count
									   		FROM lawfulness l
									   		INNER JOIN company c on l.CID = c.CID
									   		WHERE  ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2')) AND (c.LawStatus = 1)
									   		$companyFilter
									   		$lawfulyearFilterG2
									   		$filterProvince
									   		$filterZone
									   		GROUP BY l.Year
								   		";
								   		
								   	}else{
								   		$lawfulyearFilter = " AND ((l.Year >= 2011) AND (l.Year <= $sLawfulYear))";
								   		$sequestrationSql = "
									   		SELECT l.Year, count(*)  AS Count
									   		FROM lawfulness l
									   		INNER JOIN company c on l.CID = c.CID
									   		WHERE  ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2'))  AND (c.LawStatus = 1)
									   		$companyFilter
									   		$lawfulyearFilter
									   		$filterProvince
									   		$filterZone
									   		GROUP BY l.Year
								   		";
								   	}								   
								   	$sequestrationResult = mysql_query($sequestrationSql);
							   	
							   	?>
							   <div>
							   <hr />
							   <table border="1" style="border:1px solid #CCC; border-collapse: collapse; width: 100%">
                                	<tr>
                                    	<td colspan="3">
	                                     	<div align="center" style=" color:#060">สถานประกอบการที่ไม่ปฎิบัติตามกฎหมายครบ 4 ปี</div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td class="text-center" style="width: 50px">ปี</td>
                                    	<td class="text-center">จำนวนสถานประกอบการ</td>
                                    	<?php if($canCreateSequestration){?>
                                    	<td></td>
                                    	<?php }?>
                                    </tr>
                                    <?php 
                                    
                                    	$countSequestration = mysql_num_rows($sequestrationResult);
                                    	$sYear = ""; $sAmount = ""; 
                                    	while ($row = mysql_fetch_array($sequestrationResult)){
                                    		$sYear = $row["Year"];
                                    		$sAmount = number_format ($row["Count"] , 0 , "." , ",");
                                    		?>
                                    		<tr>
                                    			<td class="text-center"><?php echo ($sYear + 543);?> </td>
	                                    		<td class="text-right"><?php echo $sAmount;?> </td>
	                                    		<?php if($canCreateSequestration){?>
	                                    		<td class="text-center"><a href="notice_create.php?for_year=<?php echo $sYear?>" >แจ้งโนติส</a></td>
	                                    		<?php }?>
                                    		</tr>
                                    		
                                    		<?php 
                                    	} 
                                    	
                                    	if($countSequestration == 0){
                                    		?>
                                    		<tr>
                                    			<td class="text-center">- </td>
		                                    	<td class="text-right">- </td>
		                                    	<td > </td>
                                    		</tr>                                    		
                                    		<?php 
                                    	}
                                    ?>
							   </table>								  								   
							   </div>
                              <?php }?><!-- สถานประกอบการที่ไม่ปฎิบัติตามกฎหมายครบ 4 ปี  -->	
                          </td>
                            <td  valign="top" width="50%">
                           
                           	
                            
                            
                            <div align="center">
                            
                            	<div align="center">
                                
                                <?php 
								
									
									
									//yoes 20170103
									//more graphs
									$users_count = getFirstItem("
									
										SELECT count(*) 
											FROM users a 
												left outer join company z 
													on a.user_meta = z.cid 
												
												join lawfulness zz
													on
													z.cid = zz.cid
													and
													zz.year = '$cur_year'
										
										where 
										
										1=1 										
										and AccessLevel like '%4%' 
										
										$user_filter_sql_approval
										
										$zone_sql
									
									
									");
									
									
									$completed_count = getFirstItem("
									
										SELECT 
											count(*) 
										FROM 
											users a 
												left outer join company z 
													on a.user_meta = z.cid 
													
												join lawfulness zz
													on
													z.cid = zz.cid
													and
													zz.year = '$cur_year'
												
												left outer join 
													
													
													(
														
														select
															file_for
															, count(file_for) as count_file_for
														from
															files
														where
															file_type in (
																		
																'register_doc_1'
																, 'register_doc_22'
																, 'register_employee_card'
																, 'register_company_card'
															
															)
														group by
															file_for
														having 
															count(file_for) >= 4
													
													) zzz
													on
													zzz.file_for = a.user_id
												
										where 
											1=1 
											
											and
											count_file_for >= 4
											
											and AccessLevel like '%4%' 
										
											$user_filter_sql_approval
										
											$zone_sql
									
									
									");
									
									
									$approved_users_count = getFirstItem("
									
										SELECT 
											count(*) 
										FROM 
											users a 
												left outer join company z 
													on a.user_meta = z.cid 
												
												
												join lawfulness zz
													on
													z.cid = zz.cid
													and
													zz.year = '$cur_year'
												
										where 
											1=1 
											
											and
											user_enabled = 1
											
											and AccessLevel like '%4%' 
										
											$user_filter_sql_approval
										
											$zone_sql									
									
									");
									
									
									$lawful_submitted_count = getFirstItem(
									
											"
											
											select
												count(*)
											from
												lawfulness_company
											where
												lawful_submitted in (1,2)
												
												and
												Year = '$the_end_year'
											
											"
										
									);
									
									$lawful_approved_count = getFirstItem(
									
											"
											
											select
												count(*)
											from
												lawfulness_company
											where
												lawful_submitted = 2
												
												and
												Year = '$the_end_year'
											
											"
										
									);
									
									
									
									$strXML  = "
								<chart xAxisName='การใช้งานระบบรายงานการปฏิบัติตามกฎหมายของสถานประกอบการ (e-service)' yAxisName='จำนวนสถานประกอบการ' showValues='1' formatNumberScale='0' showBorder='1' yAxisMaxValue='100' 
								
								bgColor='ffffff'
								borderColor='f7f2ea' 
								borderthickness='0' 
								bgAlpha='100'
								
								chartTopMargin='10'
								chartRightMargin='10'
								chartBottomMargin='0'
								chartLeftMargin='10'
								
								showLegend='0'
								
								> 
								
									<categories>
										<category label='สมัครใช้งาน' />
										<category label='ส่งหลักฐานการสมัครครบแล้ว'  />
										<category label='อนุมัติการใช้งานโดยเจ้าหน้าที่แล้ว'  />
										<category label='ยังไม่อนุมัติการใช้งาน'  />
										<category label='รายงานข้อมูลเข้าระบบแล้ว'  />
										<category label='เจ้าหน้าที่ยืนยันข้อมูลแล้ว'  />
										<category label='เจ้าหน้าที่ยังไม่ยืนยันข้อมูล'  />
									
									</categories>
									
							
									<dataset seriesname='ยังไม่ปิดงาน'>
										
										<set 
										
										
										
										value='$users_count' 
										
										link='#'
										
										
										
										/>
										 
										
										<set 
										
										
										
										value='$completed_count' 
										
										link='#'
										
										
										
										/>
										<set 
										
										
										
										value='$approved_users_count' 
										
										link='#'
										
										
										
										/>
										
										<set 
										
										
										
										value='".($users_count - $approved_users_count)."' 
										
										link='#'
										
										
										
										/>
										
										<set 
										
										
										
										value='$lawful_submitted_count' 
										
										link='#'
										
										
										
										/>
										
										
										<set 
										
										
										
										value='$lawful_approved_count' 
										
										link='#'
										
										
										
										/>
										
										<set 
										
										
										
										value='". ($lawful_submitted_count - $lawful_approved_count) ."' 
										
										link='#'
										
										
										
										/>
									</dataset>
									
									
									
									
									
									
									
								</chart>
								";
								
								
								
								?>
                                
                                
								<?php  echo renderChart("Charts/StackedColumn2D.swf", "", "$strXML", "eservice_users", 325, 250, false, true);?>
                                </div>
                                
                                
                                <div style="padding: 15px;">
                            
                                    <img src="decors/pdf_small.jpg" /> ดาวน์โหลด คู่มือการใช้งาน ระบบรายงานการปฏิบัติตามกฎหมายจ้างงานคนพิการผ่านทางอิเล็กทรอนิกส์ (e-service)
                                    
                                    <br />
                                
                                    <a href="hire_eservice_manual_for_staff.pdf" target="_blank">คู่มือสำหรับเจ้าหน้าที่</a> | <a href="hire_eservice_manual_for_company.pdf" target="_blank">คู่มือสำหรับสถานประกอบการ</a>
                                
                                </div>
								
								<table border="1" style="border:1px solid #CCC; border-collapse: collapse; ">
									
									<tr>
                                    	<td colspan="6">
                                     	<div align="center"  style=" color:#060">
                                        สถานประกอบการ ส่งข้อมูลแก้ไข Ejob
										<br>
										(แสดงรายการสถานประกอบการที่ส่งข้อมูลแก้ไข 200 แห่งล่าสุด)
                                        </div>
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                    	<td align="center">
                                        <div align="center">
                                     	 วันที่ส่งข้อมูล
                                          </div>
                                        </td>
                                    	<td align="center">
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                        </td>
                                    	<td align="center"><div align="center"> จังหวัด </div></td>
                                    											
                                    	<td align="center"><div align="center"> สถานะการปฏิบัติตามกฎหมาย </div></td>
										
                                    </tr>
									
									
									<?php 
									
									
										$sql = "

										SELECT 
											* 
											, b.meta_value as submit_datetime
										FROM 
											lawfulness_meta a 
												join
													lawfulness_meta b
													on
													a.meta_for = 'es-resubmit'
													and
													a.meta_value = 1
													and
													b.meta_for = 'es-resubmit-datetime'
													and
													a.meta_lid = b.meta_lid
													
												join
													lawfulness law
													on
													law.lid = a.meta_lid
												join
													company z
													on
													z.cid = law.cid
													
												join
													provinces zz
													on
													z.Province = zz.province_id
													
									
												
										

											$user_filter_sql_approval

											$zone_sql

											order by
												b.meta_value desc
											limit 
												0,200

										";

									

										//echo $sql;

										$resubmit_result = mysql_query($sql);


										while($post_row = mysql_fetch_array($resubmit_result)){


										?>
									
										<tr>
											<td align="center">
												<div align="center">
												 <?php echo formatDateThai($post_row[submit_datetime],1,1);?>
												  </div>
											</td>
											<td align="center">
												<div align="center">
													<a href="organization.php?id=<?php echo $post_row[CID];?>&focus=lawful&year=<?php echo $post_row[Year];?>">
												 		<?php echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);?>
													</a>
												  </div>
											</td>
											<td align="center"><div align="center"> <?php echo $post_row[province_name];?> </div></td>
																				
											<td align="center"><div align="center"> <?php echo getLawfulImage($post_row[LawfulStatus]);?> </div></td>

										</tr>
									
									
									
									<?php } ?>
									
								</table>
                            
                            	<table border="1" style="border:1px solid #CCC; border-collapse: collapse; <?php
									//if(($sess_accesslevel == 3 && !$sess_can_manage_user) || $sess_accesslevel == 8){
									if($sess_accesslevel == 8){
										
										//yoes 20161201 --> allow ALL except 8 to see this
										echo "display: none;";
									}
								?>">
                                	<tr>
                                    	<td colspan="6">
                                     	<div align="center"  style=" color:#060">
                                        สถานประกอบการ สมัครใช้งานระบบ
										<br>
										(แสดงสถานประกอบการที่ส่งเอกสารเข้ามาครบ 100 แห่งล่าสุดเท่านั้น - กรณีต้องการดูข้อมูลผู้ใช้งานทั้งหมด ให้ไปที่เมนู "ผู้ใช้งานระบบ" ทางด้านบน - สิทธิดูข้อมูลโดยเจ้าหน้าที่ส่วนกลางเท่านั้น)

                                            <?php


                                            //admin ส่วนกลาง เห็นแต่รายการที่ส่งมาครบแล้วเท่านั้น
                                            if($sess_accesslevel == 1){
                                            ?>
                                                <br>
                                                <font color="blue">** Admin ส่วนกลาง เห็นแต่รายการข้อมูลที่ส่งมาครบแล้วเท่านั้น</font>
                                            <?php }?>

                                        </div>
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                    	<td align="center">
                                        <div align="center">
                                     	 วันที่สมัคร
                                          </div>
                                        </td>
                                    	<td align="center">
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                        </td>
                                    	<td align="center"><div align="center"> จังหวัด </div></td>
                                    	<td align="center">
											<div align="center">
											 username
											  </div>
                                        </td>										
                                    	<td align="center"><div align="center"> จำนวนเอกสารยืนยันตนที่แนบมา </div></td>
										
										<td align="center" width=20%>
											<div align="center">
											 ผู้ใช้งานกด email ยืนยันตัวตนแล้ว?
											</div>
                                        </td>
                                    </tr>
                                    
                                    
                                    <?php 
									
									
									
									//yoes 20241223
                                    //admin ส่วนกลาง เห็นแต่รายการที่ส่งมาครบแล้วเท่านั้น
                                    if($sess_accesslevel == 1){

                                        $sql = "
										
                                            SELECT 
                                                *
                                            FROM   
                                                users a
                                                LEFT OUTER JOIN company z ON a.user_meta = z.cid
                                                LEFT JOIN (
                                                    SELECT
                                                        file_for,
                                                        COUNT(*) as the_file_count,
                                                        COUNT(CASE WHEN file_type = 'register_doc_1' THEN 1 END) as doc1_count,
                                                        COUNT(CASE WHEN file_type = 'register_doc_22' THEN 1 END) as doc22_count
                                                    FROM
                                                        files 
                                                    WHERE
                                                        file_type IN ('register_doc_1', 'register_doc_22')
                                                    GROUP BY
                                                        file_for
                                                ) aa ON a.user_id = aa.file_for
                                            WHERE  
                                                1 = 1
                                                AND (user_enabled LIKE '%0%' OR user_enabled LIKE '%9%')
                                                AND accesslevel LIKE '%4%'
                                                AND a.FirstName != ''
                                                AND a.LastName != ''
                                                AND a.user_telephone != ''
                                                AND a.user_position != ''
                                                AND a.FirstName_2 != ''
                                                AND a.LastName_2 != ''
                                                AND a.user_telephone_2 != ''
                                                AND a.user_position_2 != ''
                                                AND aa.doc1_count > 0  -- Must have register_doc_1
                                                AND aa.doc22_count > 0 -- Must have register_doc_22
                                                $user_filter_sql_approval
                                                $zone_sql
                                            ORDER BY 
                                                user_id DESC,
                                                CASE
                                                    WHEN the_file_count > 4 THEN 4
                                                    ELSE the_file_count
                                                END DESC
                                            LIMIT 0, 100
                                        
                                        ";


                                    }else {


                                        //yoes 20210201
                                        $sql = "
										
                                            sELECT 
                                                *
                                            FROM   
                                                users a
                                                   LEFT OUTER JOIN company z
                                                                ON a.user_meta = z.cid
                                                                
                                                    left join (
                                                    
                                                        select
                                                            file_for
                                                            , count(*) as the_file_count
                                                        from
                                                            files 
    
                                                        where
                                                            file_type in (
                                                                
                                                                
                                                                'register_doc_1'
                                                                , 'register_doc_22'
                                                                
                                                        )
                                                        group by
                                                            file_for
                                                    
                                                    ) aa
                                                    on 
                                                    a.user_id = aa.file_for
                                                    
                                            WHERE  1 = 1
                                                   AND ( user_enabled LIKE '%0%'
                                                          OR user_enabled LIKE '%9%' )
                                                   AND accesslevel LIKE '%4%'
                                                   
                                                   $user_filter_sql_approval
                                        
                                                    $zone_sql
                                                   
                                            
                                            ORDER  BY 
                                                user_id DESC
                                                , 
                                                case
                                                    when the_file_count > 4 then 4
                                                else
                                                    the_file_count
                                                end
                                                    desc
                                                
                                            
                                            
                                            LIMIT  0, 100  
                                        
                                        ";

                                    }
									
									/*
									
									and
									user_id in (
									
										select
												file_for
											from
												files 
											where
												file_type in (
													
													
													'register_doc_1'
													, 'register_doc_22'
													, 'register_employee_card'
													, 'register_company_card'
												
												)
												
										group by
											
											file_for
											
										having 
											count(*) >= 4
											
									
									)
									
									
									*/
									
									//echo $sql;
									
									$submit_result = mysql_query($sql);
									
									
									while($post_row = mysql_fetch_array($submit_result)){
									
									
									?>
                                    
                                    
                                    <tr>
                                    	<td >
                                       	<?php echo formatDateThaiShort($post_row[user_created_date]);?>
                                        </td>
                                    	<td >
                                       <?php 
									   
									   $this_company_row = getFirstRow("select * from company where cid = '".$post_row["user_meta"]."'");
										
										//echo formatCompanyName($this_company_row["CompanyNameThai"] , $this_company_row["CompanyTypeCode"]);
										//echo $this_company_row["CompanyNameThai"];
										
										echo formatCompanyName($this_company_row["CompanyNameThai"],$this_company_row["CompanyTypeCode"]);
									   
									   ?>
                                        </td>
                                    	<td ><?php 
									  
									  
									  echo getFirstItem("select province_name from provinces where province_id = '".$this_company_row["Province"]."'");
									  
									  ?></td>
                                        <td >
                                       <a href="view_user.php?id=<?php echo doCleanOutput($post_row["user_id"]);?>"><?php echo ($post_row["user_name"]);?></a>   
                                        </td>
                                        <td >
                                        
                                        <?php 
										
											$count_self_doc = getFirstItem("
															
															
																select
																	count(*)
																from
																	files 
																where
																	file_type in (
																		
																		
																		'register_doc_1'
																		, 'register_doc_22'
																		, 'register_employee_card'
																		, 'register_company_card'
																	
																	)
																	
																and
																
																file_for = '".$post_row["user_id"]."'
															
															
															");
															
															
												$the_font_color = "red";
												$the_doc_text = "ยังส่งเอกสารยืนยันตนไม่ครบ";
												$the_doc_text_2 = "";
												
												if($count_self_doc >= 2){
													
													$the_font_color = "green";
													$the_doc_text = "ส่งเอกสารยืนยันตนครบแล้ว";
													$the_doc_text_2 = "<br><b>สถานประกอบการส่งเอกสารยืนยันตนครบแล้ว รอการอนุมัติจากเจ้าหน้าที่</b>";
												}
										
										
												
										?>
                                        
                                        <div align="center" title="<?php echo $the_doc_text;?>">
                                        
                                        	<font color=<?php echo $the_font_color;?>>
											<?php 
                                                
                                                echo $count_self_doc ; //. $the_doc_text_2;
                                            
                                            ?> / 2
                                            
                                            </font>
                                        
                                        </div>
                                        
                                        </td>
										
										<td >
										
											<?php
												echo $post_row["user_enabled"]==9?"<font color=orangered>ยังไม่ยืนยัน</font>":"<font color=green>ยืนยันแล้ว</font>";
												echo " - " . $post_row["user_email"];
												
												/*$get_lastest_mail_stat = getFirstItem("
															
															
																select
																	log_type
																from
																	generic_log
																where
																	log_type like '%mail_%'
																	and
																	log_meta like '%".$post_row["user_email"]."%'
															
															
															");
												
												echo " - ".$get_lastest_mail_stat;*/
												
												
												?>
										
										</td>
									
										
                                    </tr>
                                    
                                    
                                    <?php }?>
									
									
									
									<tr>
                                    	<td colspan="6">
                                     	<div align="center"  style=" color:orangered">
										สถานประกอบการ สมัครใช้งานระบบ
										<br>
										(แสดงสถานประกอบการที่ส่งเอกสารเข้ามาครบ 200 แห่งล่าสุดเท่านั้น - กรณีต้องการดูข้อมูลผู้ใช้งานทั้งหมด ให้ไปที่เมนู "ผู้ใช้งานระบบ" ทางด้านบน - สิทธิดูข้อมูลโดยเจ้าหน้าที่ส่วนกลางเท่านั้น)
                                        </div>
                                        </td>
                                    </tr>
                                    
                                </table>
                             </div>
                           
                           
                           
                            </td>
                        </tr>
                   </table>
                    
                    
                    
                    
                    
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

</body>
</html>
