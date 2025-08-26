<?php



	include "db_connect.php";
	include "session_handler.php";

	if($_GET["mode"]=="search"){
		$mode = "search";
		
	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}

	
	
	
	//yoes 20141007 -- also check permission
	if($sess_accesslevel == 1 || $sess_accesslevel == 2  ||  $sess_can_manage_user){	
		//can pass		
	}else{
		//nope
		header ("location: index.php");	
	}
	
	
	//yoes 20151025
	if(strlen($_GET["user_enabled"])>0){	
		$_POST[user_enabled] = $_GET["user_enabled"]*1;
	
	}
	

?>
<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0;"  >
                    Users ทั้งหมด
                    
                    
                  </h2>
                   
                    
                   
                    <div style="padding-top:10px; font-weight: bold;">
                        
                       ค้นหา users
                        
                   </div>
                    
                    
                    <form method="post">
                    <table style=" padding:10px 0 0px 0;">
                    
                    
                    
                    	<tr>
                    	  <td bgcolor="#efefef">สถานะ: </td>
                          <td >
                          
                          <?php 
						  
						  $ddl_user_enabled_show_blank = 1;
						  include "ddl_user_enabled.php";
						  
						  ?>
                          
                          </td>
                          <td colspan="2" ></td>
                   	  </tr>
                      
                      
                      
                    	<tr>
                        	
                            
                            
                    	  <td bgcolor="#efefef">User name:  </td>
                    	  <td>
                          	                     <input type="text" name="user_name" value="<?php echo $_POST["user_name"];?>" />     </td>
                        	<td >
                          </td>
                            <td>
                               </td>
                      </tr>
                      
                      <tr>
                        	
                            
                            
                    	 <td bgcolor="#efefef">
                           ชื่อ:</td>
                            <td>
                                <input type="text" name="FirstName" value="<?php echo $_POST["FirstName"];?>" /> 
                              </td>
                        	<td bgcolor="#efefef">
                           นามสกุล:</td>
                            <td>
                                <input type="text" name="LastName" value="<?php echo $_POST["LastName"];?>" /> 
                                
                                
                                 </td>
                      </tr>
                      
					  
					  <?php if($sess_accesslevel == 2 || $sess_accesslevel == 3){ ?>
					  
					  
					  <?php }else{ ?>
                    	<tr>
                    	  <td bgcolor="#efefef">
                          
	                        ชนิดของ user:
                          
                          </td>
                    	  <td>
						  	<?php
							
								$do_show_company = 1;
								include "ddl_access_level.php";
								
								?>
                           </td>
                          
                          
                          <td> </td>
                          <td></td>
                                                   
                          
                          
                   	  </tr>
					  
					  
					  <?php }?>
					  
					  
					  
                      
                      
                      
                      	<tr>
                    	  <td bgcolor="#efefef">ชื่อบริษัท / เลขที่บัญชีนายจ้าง <br />/ หน่วยงาน</td>
                          <td colspan="3"><input type="text" name="Department" value="<?php echo $_POST["Department"];?>" style="width: 250px;" /></td>
                          
                          
							                              
                          
                          
                   	  </tr>
                      	<tr>
                      	  <td bgcolor="#efefef">เป็นเจ้าหน้าที่สถานประกอบการ<br />ภายใต้พื้นที่การทำงาน</td>
                      	  <td colspan="3">
                          
                          
                          
                          <?php include "ddl_zone_list.php";?>
                          
                          
                          
                          </td>
                   	    </tr>
                        
                    	<tr>
                    	  <td colspan="6" align="right">
                          
                           
                            <input type="submit" value="แสดง" name="mini_search"/>
                            
                            
                          <hr />
                          
                          </td>
                   	  </tr>
                      
                      
                    </table>
                    </form>
                    
                    
                    <?php 
					
						//yoes 20141007 --> also set if this is not admin then can only see own's province
							if(($sess_can_manage_user && $sess_meta) && $sess_accesslevel == 3){	
								
								$filter_sql = " 
								
									and 
										(
											user_meta = '$sess_meta'
											or 
											
											(
												accessLevel = 4
												and
												user_meta in (
												
													select cid from company where province	= '$sess_meta'							
													
												
												)
											)
										)
										
										";
							
							}
							
							
							
							//yoes 20151021 ---< add more filter here
							$input_fields = array(
							
								'user_enabled'
								,'user_name'
								,'FirstName'
								,'LastName'
								,'AccessLevel'
								,'Department'
							
							);
							
							for($i = 0; $i < count($input_fields); $i++){
								
								if(strlen($_POST[$input_fields[$i]])>0){
									
									$use_condition = 1;
									
									
									if($input_fields[$i] == "Department"){
									
										$filter_sql .= " and 
															
															(
															
																Department like '%".doCleanInput($_POST[$input_fields[$i]])."%'
																or
																CompanyCode like '%".doCleanInput($_POST[$input_fields[$i]])."%'
																or
																CompanyNameThai like '%".doCleanInput($_POST[$input_fields[$i]])."%'
															
															)
															
															
															";
										
									}else{
									
										$filter_sql .= " and $input_fields[$i] like '%".doCleanInput($_POST[$input_fields[$i]])."%'";
									
									}
									
									
								}
							}	
							
							
							//YOes 20160117 --> add zone filters here
							if($_POST["zone_id"]){
								
								$my_zone = $_POST["zone_id"]*1;
								
								$zone_sql = "
								
									and
									District in (
								
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
								
								
								";
								
								$filter_sql .= $zone_sql;
								
							}
					
					
					?>
                    
                    
                    
                    <?php 
					
					
					//yoes 20161201 -- pmj only see Company's users
					
					//yoes 20180319 == ส่วนกลาง only see company users
					
					if($sess_accesslevel == 3 || $sess_accesslevel == 2){
						
						$filter_sql	.= " and AccessLevel = 4 ";
						
					}
					
					
					?>
					
					
					<?php
						
						
						//only show company users that เอาเฉพาะ User สถานประกอบการที่ไม่อนุมัติ 62 - เก่ากว่า ออกคับ (63 ขึ้นไปเก็บไว้ก่อน)
						//$filter_sql
						//yoes 20211213 -- open this for now
						//yoes 20220117 -- delete all these from DB
						/*$filter_sql	.= " 
						
										and a.user_id not in (

											select
												user_id
											from
												users
											where
												AccessLevel = 4
												and
												(
													user_enabled = 0
													or
													user_enabled = 9

												) 
											and
											year(user_created_date) <= 2019
											and
											(
												year(user_approved_date) <= 2019
												or
												year(user_approved_date) is null
											)
											and
											user_meta != '1214'
											and
											user_meta != '54475'
											and
											user_meta != '1997'
											and
											user_meta != '1432'
											and
											user_meta != '67016'
											
											
											
										)
						
						
										";	*/
										
						
						
					
					?>
                    
                    
                    
                    <div style="padding:0 0 5px 0">
	                    พบ users : <strong><?php 
						
						
						echo getFirstItem("
							select 
								count(user_id) 
							from 
								users a
								left outer join
												company b on a.user_meta = b.cid
								
							where 
								1=1 
							
							$filter_sql
							
							");
						
						
						?></strong> คน
						
						
						<?php
						
						$get_org_sql = "SELECT 
											*
										FROM 
											users a
											left outer join
												company b on a.user_meta = b.cid
										
										where 1=1
										
										$filter_sql
										
										order by user_id asc
										
										";
						
						//echo $get_org_sql ;
						
						?>
                        
                       
                    </div>
                    
                    
                    <?php if($sess_accesslevel == 1 ){  //yoes 20161201 -- only these ppl can add users || $sess_accesslevel == 2?>
                     <div style="padding:10px 0 10px 0"><a href="view_user.php?mode=add">+ เพิ่ม user ใหม่เข้าไปในระบบ</a></div>
                     <?php }?>
                    
                    <table border="1" width=100%  cellspacing="0" cellpadding="5" style="border-collapse:collapse;"  id="zero_config">
                    	
						 <thead>
						
						<tr bgcolor="#9C9A9C" align="center" >
                        	
           	  <td >
                            	<div align="center"><span class="column_header">ลำดับที่</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">User name</span> </div></td>
                     	<!--
                      <td>
                            	<div align="center"><span class="column_header">Password</span> </div></td>
                                -->
                                
                      <td>
                            	<div align="center"><span class="column_header">ชนิดของ user</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">ชื่อ-นามสกุล</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">หน่วยงาน/สถานประกอบการ</span> </div></td>
                      <td><div align="center"><span class="column_header">วันที่สมัคร</span></div></td>
                      <td><div align="center"><span class="column_header">วันที่อนุมัติ/อนุมัติโดย</span></div></td>
                      
                      <td><div align="center"><span class="column_header">สถานะ</span></div></td>
                             
                          
                           
                          <?php if($sess_accesslevel == 1 ){ //yoes 20161201 -- only these ppl can delete users || $sess_accesslevel == 2?>  
                          <td><div align="center"><span class="column_header">ลบข้อมูล</span></div></td>
                          <?php }?>
                          
                    	</tr>
						
						</thead>
						 <tbody>
                        
						
						</tbody>
						
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

<script language="javascript">

function checkOrUncheck(){
	if(document.getElementById('chk_all').checked == true){
		checkAll();
	}else{
		uncheckAll();
	}
}

function checkAll(){
	<?php echo $js_do_check; ?>
}

function uncheckAll(){
	<?php echo $js_do_uncheck; ?>
}
</script>

	<script src="./assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
	<script src="./assets/extra-libs/datatables.net-bs4/js/dataTables.responsive.min.js"></script>

    <!-- Datatable -->
    <!-- <script src="./dist/js/jquery-3.0.0.js"></script> -->
    <script src="./dist/js/jquery.dataTables.js"></script>
    
	<script>
    $(document).ready(function () {
        var filter_sql = <?php echo $filter_sql !== null ? json_encode($filter_sql) : '""'; ?>;
        
        var url = "hire_ws/getUsers.php?filter_sql=" + encodeURIComponent(filter_sql);
        console.log(filter_sql);
        $('#zero_config').DataTable({
            "processing": true,
            "serverSide": true,
			'pageLength': 50,
			'searching': false,
            'language': {
                'infoFiltered': '',
            },
            "ajax": {
                "type": "GET",
                "url": url
            }
        });
    });
</script>

</body>
</html>