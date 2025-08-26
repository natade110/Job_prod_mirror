<?php

	include "db_connect.php";
	include "session_handler.php";
	require_once 'c2x_constant.php';
	require_once 'c2x_function.php';

	include_once "ajax_dashboard_ejob_new.php";

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
<?php include "header_html_new.php";?>

<?php //exit(); //yoes 20180104 - this page is slow so cut it off when needed ?>

<?php include("Charts/Includes/FusionCharts.php"); ?>
<script type="text/javascript" src="Charts/FusionCharts.js"></script>

                <td valign="top" style="padding-left:5px;">


                     <?php


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
                            <option value="2018" <?php if($_POST[ddl_year]==2018){?>selected="selected"<?php }?>>2561</option>
							<option value="2017" <?php if($_POST[ddl_year]==2017){?>selected="selected"<?php }?>>2560</option>
                            <option value="2016" <?php if($_POST[ddl_year]==2016){?>selected="selected"<?php }?>>2559</option>
                            <option value="2015" <?php if($_POST[ddl_year]==2015){?>selected="selected"<?php }?>>2558</option>
                            <option value="2014" <?php if($_POST[ddl_year]==2014){?>selected="selected"<?php }?>>2557</option>
                            <option value="2013" <?php if($_POST[ddl_year]==2013){?>selected="selected"<?php }?>>2556</option>

                           <?php if(1==0){?>
                            <option value="2012" <?php if($_POST[ddl_year]==2012){?>selected="selected"<?php }?>>2555</option>
                            <option value="2011" <?php if($_POST[ddl_year]==2011){?>selected="selected"<?php }?>>2554</option>
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
												<input type="hidden" id="user_filter_sql_approval" value="<?php echo $user_filter_sql_approval;?>">
												<input type="hidden" id="zone_sql" value="<?php echo $zone_sql;?>">
                    </form>

                    </div>


                   <table width="100%" style="border: 1px solid #CCC;">
                        <tr>

                       	  <td style="padding: 20px;">


                        		<?php

									//yoes 20181031 -> new table format goes here

								?>
                    			<style>

									.dashboard_new{

										font-size: 16px;
										font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Helvetica,Arial,sans-serif;
									}

									.dashboard_new_small{

										font-size: 11px;
										font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Helvetica,Arial,sans-serif;
									}

									tr.dashboard_new_border_bottom td  {

										border-bottom: 1px solid #e0e6e8;
									}

									.dashboard_section{

										font-size: 16px; border-bottom: 1px solid #000; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Helvetica,Arial,sans-serif; line-height: 26px;

										color: #DD181B;

										margin-top: 20px;
									}

									.numberCircle {
										width: 20px;
										line-height: 20px;
										border-radius: 50%;
										text-align: center;
										font-size: 18px;
										border: 2px solid red;
										background-color: red;
										color: white;
										display: inline-block;
									}

								</style>

								<div class='dashboard_section'>
											<strong> <div class="numberCircle" ><?php getPaymentResult($the_end_year,999999999,0,true); ?></div> :: ผลการชำระเงินของสถานประกอบการ e-service ::</strong>
								</div>
								<?php /** ----------  func: get_org_on_submit			------- **/?>
								<table border="0" width="100%" cellpadding="5px 0;" id="get_payment_result">
										<?php getPaymentResult($the_end_year); ?>
								</table>

               			    <div class='dashboard_section'>

                       				<strong> <div class="numberCircle" ><?php getOrgOnlineSubmit($the_end_year,999999999,0,true); ?></div> :: สถานประกอบการยื่นแบบออนไลน์ ::</strong>
                   			</div>


												<?php /** ----------  func: get_org_on_submit			------- **/?>
									<table border="0" width="100%" cellpadding="5px 0;" id="get_org_on_submit">
										<?php getOrgOnlineSubmit($the_end_year); ?>
									</table>


									<div class='dashboard_section'>
												<strong> <div class="numberCircle" ><?php getRequestAddM34($the_end_year,999999999,0,true); ?></div> :: คำขอต้องการเพิ่มใบเสร็จตามมาตรา 34 ::</strong>
									</div>
									<?php /** ----------  func: get_request_add_m34			------- **/?>
									<table border="0" width="100%" cellpadding="5px 0;" id="get_request_add_m34">
											<?php getRequestAddM34($the_end_year); ?>
									</table>

									<div class='dashboard_section'>
												<strong> <div class="numberCircle" ><?php getRequestEditM34($the_end_year,999999999,0,true); ?></div> :: คำขอต้องการแก้ไขหรือยกเลิกข้อมูลใบเสร็จตามมาตรา 34 ::</strong>
									</div>
									<?php /** ----------  func: get_request_add_m34			------- **/?>
									<table border="0" width="100%" cellpadding="5px 0;" id="get_request_edit_m34">
											<?php getRequestEditM34($the_end_year); ?>
									</table>

									<div class='dashboard_section'>
												<strong> <div class="numberCircle" ><?php getCompanyDup($the_end_year,999999999,0,true); ?></div> :: รายชื่อสถานประกอบการที่ใช้ลูกจ้างคนพิการซ้ำซ้อนและผู้ดูแลซ้ำซ้อน ::</strong>
									</div>
									<?php /** ----------  func: get_company_dup			------- **/?>
									<table border="0" width="100%" cellpadding="5px 0;" id="get_company_dup">
											<?php getCompanyDup($the_end_year); ?>
									</table>

									<div class='dashboard_section'>
												<strong> <div class="numberCircle" ><?php getCompanyMissingM33M35($the_end_year,999999999,0,true); ?></div> :: สถานประกอบการที่มีการจ่ายเงินแล้ว แต่ไม่มีรายละเอียด ม.33 และ ม.35 ::</strong>
									</div>
									<?php /** ----------  func: get_company_missing_m33_m35			------- **/?>
									<table border="0" width="100%" cellpadding="5px 0;" id="get_company_missing_m33_m35">
											<?php getCompanyMissingM33M35($the_end_year); ?>
									</table>

               				<div class='dashboard_section' style="color: #ff8000">

                   				<strong><div class="numberCircle" style="background-color: #ff8000"><?php getApprovalList($the_end_year,999999999,0,true); ?></div> :: สถานประกอบการ สมัครใช้งานระบบ ::</strong>
                       			</div>

														<?php /** ----------  func: get_approval_list			------- **/?>
                   				  <table border="0" width="100%" cellpadding="5px 0;" id="get_approval_list">
															<?php getApprovalList($the_end_year); ?>
								  				</table>


                   				  <div class='dashboard_section' style="color: #ff4000"><strong>
                   				    <div class="numberCircle" style="background-color: #ff4000" >1</div>
                   				    :: คนพิการออกจากงานก่อนหมดอายุสัญญาจ้าง ::</strong></div>
                                 <table border="0" width="100%" cellpadding="5px 0;">
                                    <tr class="dashboard_new_border_bottom">
                                      <td width="50%" valign="middle" class="dashboard_new"></td>
                                      <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right"></td>
                                    </tr>
                                   
								  
								  <?php 
								  
									//yoes 20181120
									//get company that "paid" but already satify 33/35
									//echo $the_end_year;
									$sql = "
									
										select
											*
										from
											company a
												join
													lawfulness b
													on
													a.cid = b.cid
												join
													lawful_employees c
													on
													a.cid = c.le_cid
													and
													b.year = c.le_year
												join
													lawful_employees_meta d
													on
													c.le_id = d.meta_leid
													and
													meta_for = 'sso_failed'
															
												left join
														provinces pp
															on
															a.province = pp.province_id
												
										where
											year = '$the_end_year'
									
									";
									
									//echo $sql;
									//echo $the_end_year;
									
									
									$dashboard_result = mysql_query($sql);
									
									while($post_row = mysql_fetch_array($dashboard_result)){
										
										
										//print_r($post_row);
										
										?>
										
										 <tr class="dashboard_new_border_bottom hover" data-url="organization.php?id=<?php echo $post_row[CID]?>&year=<?php echo $post_row[Year]?>">
										  <td width="50%" valign="middle" class="dashboard_new">
											<span style="color: blue; " ><?php echo $post_row[province_name]?> : </span><?php echo $post_row[CompanyNameThai]?>
											
											:
											
											<font color=blue>
												<?php echo $post_row[le_code]?> : <?php echo $post_row[le_name]?>
											</font>
											
											
												- วันที่ออกจากงานในระบบฯ <font color=cc00cc><?php echo formatDateThai($post_row[le_end_date]);?></font> vs ข้อมูลจากประกันสังคม <font color=red><?php echo formatDateThai($post_row[meta_value]);?></font>
											
											
										  </td>
										  <td width="10%" valign="middle" class="dashboard_new"><div align="right" style="color: blue; font-size: 14px;"></div></td>
										</tr>
                                 
										
									<?php
										
									}
								  
								  
								  ?>
								   </table>


                            <div class='dashboard_section' style="color: #009900"><strong>
                                    <div class="numberCircle" style="background-color: #009900" >1</div>
                                    :: คืนเงินสถานประกอบการ ::</strong></div>
                                  
								  
								  <table border="0" width="100%" cellpadding="5px 0;">
                                    <tr class="dashboard_new_border_bottom">
                                      <td width="50%" valign="middle" class="dashboard_new"></td>
                                      <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right"></td>
                                    </tr>
                                   
								  
								  <?php 
								  
									//yoes 20181120
									//get company that "paid" but already satify 33/35
									//echo $the_end_year;
									$sql = "
									
										select
											a.cid
											, a.companyNameThai
											, b.lid
											, b.lawfulStatus
											, b.employees
											, b.Year
											, IF(b.employees < 100, 0, round(b.employees/100,0)) as ratio_to_hire
											, b.Hire_NumofEmp
											, c.curator_count
											, floor(d.pay_amount/(365*300)) as m34_count
											
											, ifnull(b.Hire_NumofEmp,0) +ifnull(c.curator_count,0) + ifnull(floor(d.pay_amount/(365*300)),0) as total_satisfy
											
											, d.pay_amount
											, e.payback_amount
											
											, floor((ifnull(d.pay_amount,0) - ifnull(e.payback_amount,0))/(365*300)) as balance
											
											, pp.province_name
										from
											company a
												
												join
													lawfulness b
														on
														a.cid = b.cid
														
												left join (
												
															select
																curator_lid
																, count(*) as curator_count
															from
																curator
															where
																curator_parent = 0
															group by
																curator_lid
														
														) c
													
														on														
														b.lid = c.curator_lid
												
												left join (
												
															
															select
																payment.LID
																, sum(receipt.amount) as pay_amount
															from
																payment
																	join receipt
																		on
																		payment.RID = receipt.RID
																		and
																		receipt.is_payback = 0
															group by
																payment.LID
												
														) d
														
														on														
														b.lid = d.lid
														
												left join (
												
															
															select
																payment.LID
																, sum(receipt.amount) as payback_amount
															from
																payment
																	join receipt
																		on
																		payment.RID = receipt.RID
																		and
																		receipt.is_payback = 1
															group by
																payment.LID
												
														) e
														
														on														
														b.lid = e.lid
														
													left join
														provinces pp
															on
															a.province = pp.province_id
															
												
										where
											year = '$the_end_year'
											and
											lawfulstatus in (1,3)
											and											
											
												ifnull(b.Hire_NumofEmp,0) +ifnull(c.curator_count,0) + ifnull(floor(d.pay_amount/(365*300)),0)
													> IF(b.employees < 100, 0, round(b.employees/100,0)) 
									
											and
											
											floor(
												
												(ifnull(d.pay_amount,0) - ifnull(e.payback_amount,0))/(365*300)
												
												)  > 0
									
									";
									
									
									//echo $the_end_year;
									
									
									$dashboard_result = mysql_query($sql);
									
									while($post_row = mysql_fetch_array($dashboard_result)){
										?>
										
										 <tr class="dashboard_new_border_bottom hover" data-url="organization.php?id=<?php echo $post_row[cid]?>&year=<?php echo $post_row[Year]?>">
										  <td width="50%" valign="middle" class="dashboard_new"><span style="color: blue; " ><?php echo $post_row[province_name]?> : </span><?php echo $post_row[companyNameThai]?></td>
										  <td width="10%" valign="middle" class="dashboard_new"><div align="right" style="color: blue; font-size: 14px;"></div></td>
										</tr>
                                 
										
									<?php
										
									}
								  
								  
								  ?>
								   </table>
								  
								  
                            <div class='dashboard_section' style="color: #800000"><strong>
                            <div class="numberCircle" style="background-color: #800000">1</div>
                                    :: ติดตามเอกสารสถานประกอบการ ::</strong> </div>
                            <table border="0" width="100%" cellpadding="5px 0;">
                              <tr class="dashboard_new_border_bottom">
                                <td valign="middle" >&nbsp;</td>
                                <td valign="middle" class="dashboard_new">&nbsp;</td>
                                <td valign="middle" class="dashboard_new_small" style="text-align: right"> เอกสาร</td>
                              </tr>
                              <?php 
								  
									//yoes 20181120
									//get company that "paid" but already satify 33/35
									//echo $the_end_year;
									$sql = "
									
										select
											*
										from
											company a
												join
													lawfulness b
													on
													a.cid = b.cid
															
												left join
														provinces pp
															on
															a.province = pp.province_id
												
										where
											year = '$the_end_year'
											and
											lawfulStatus = 1
											and
											lid not in (
											
												select
													file_for
												from
													files
												where
													file_type = 'company_33_docfile_4_adm'
											
											
											)
										limit 
											0 ,20
									
									";
									
									//echo $sql;
									//echo $the_end_year;
									
									
									$dashboard_result = mysql_query($sql);
									
									while($post_row = mysql_fetch_array($dashboard_result)){
										
										
										//print_r($post_row);
										
										?>
										
										 <tr class="dashboard_new_border_bottom hover" data-url="organization.php?id=<?php echo $post_row[CID]?>&year=<?php echo $post_row[Year]?>">
										  <td width="50%" valign="middle" class="dashboard_new">
											
											<img src="decors/green.gif" border="0" alt="ทำตามกฏหมาย" title="ทำตามกฏหมาย" />
											
											<span style="color: blue; " ><?php echo $post_row[province_name]?> : </span><?php echo $post_row[CompanyNameThai]?>
											
											
											
										  </td>
										  <td width="10%" valign="middle" class="dashboard_new"><div align="right" style="color: blue; font-size: 14px;"></div></td>
										  <td width="20%" valign="middle" class="dashboard_new"><div align="right" style="color: orangered; font-size: 14px;"> สปส 1-10 ส่วนที่ 2</div></td>
										</tr>
                                 
										
									<?php
										
									}
								  
								  
								  ?>
                            </table></td>
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
<script>
	function getAllData(func){
			$.ajax({
					type: "POST",
					url: "ajax_dashboard_ejob_new.php",
					data: "the_limit=-1&the_end_year="+<?php echo $the_end_year;?>+"&func="+func,
					cache: false,
					success: function(html){
						var s = '#'+func;
							$('#'+func).html("");
							$('#'+func).append(html);
						addHover();	
					}
			});
	}
	
	function addHover(){
		$('.hover').hover(
			function() {  
				$(this).css("background-color","#eeeeee")
			},
			function() {  
				$(this).css("background-color","#ffffff")
			}
		)
		
		$('.hover').click(function(){
			if($(this).data("url"))
				window.open($(this).data("url"));
		})		
	}
	
	$(document).ready(function() {
		addHover();
		
	});

</script>
</body>
</html>
