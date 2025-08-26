<?php
require_once 'db_connect.php';
require_once 'session_handler.php';

if(is_numeric($_GET["search_id"])){
	$have_search_id = 1;

	$company_name_row = getFirstRow("select CompanyNameThai,CompanyTypeCode from company where CID = '".$_GET["search_id"]."'");

	$company_name_to_use = formatCompanyName($company_name_row["CompanyNameThai"],$company_name_row["CompanyTypeCode"]);

	if(strlen($_GET["for_year"])==4){
		$for_year = $_GET["for_year"];
	}
}


if(($_GET["LawfulFlag"] == "0") || ($_GET["LawfulFlag"] == "2" )){ 		
	$_POST["LawfulFlag"] = $_GET["LawfulFlag"]*1;
	$_POST["mini_search"] = 1;

}
?>

<?php include "header_html.php";?>
<td valign="top" style="padding-left: 5px;">
	<h2 class="default_h1" style="margin:0; padding:0;">การส่งจดหมายทวงถาม</h2>
	<div style="padding-top:10px; font-weight: bold;">
		1. ค้นหาสถานประกอบการที่ต้องการส่งจดหมายทวงถาม
	</div>
	<!-- form -->
	<form method="post" >
		<table style="padding:10px 0 0px 0;">
	 		<tr>
        		<td bgcolor="#efefef">ประจำปี:</td>
            	<td><?php include "ddl_year.php";?></td>
            	<td colspan="2"></td>
     		</tr>
     		<tr>
     			<td bgcolor="#efefef">สถานะ:</td>
     			<td>
     				<select name="LawfulFlag" id="LawfulFlag_search">
    					<option value="" selected="selected">-- all --</option>
					    <option value="0" <?php if($_POST["LawfulFlag"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
					    <option value="2" <?php if($_POST["LawfulFlag"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน</option>
					</select>
     			</td>
     			<td colspan="2"></td>
     		</tr>
     		<tr>
     			<td bgcolor="#efefef">ชื่อ:</td>
     			<td>
     				<input type="text" name="CompanyNameThai" value="<?php echo $_POST["CompanyNameThai"];?>" />
     			</td>
     			<td bgcolor="#efefef"><?php echo $the_code_word;?>:</td>
     			<td>
     				<input type="text" name="CompanyCode" value="<?php echo $_POST["CompanyCode"];?>" />
     			</td>
     		</tr>
     		<tr>
     			<td bgcolor="#efefef">
     				<?php  if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){ ?>
								ประเภทหน่วยงาน:
                    <?php }else{ ?>
			                	ประเภทธุรกิจ:
                    <?php } ?>
     			</td>
     			<td><?php include "ddl_org_type.php";?></td>
     			<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
     			
     			<?php }else{?>
     				<td bgcolor="#efefef">ประเภทกิจการ:</td>
     				<td><?php include "ddl_bus_type.php";?></td>
     			<?php }?>
     		</tr>
     		<tr>
            	<td bgcolor="#efefef"> จังหวัด: </td>
                <td><?php include "ddl_org_province.php";?></td>
                <td colspan="2"></td>
            </tr>
            <tr>
           		<td colspan="4" align="right">
           			<input type="submit" value="แสดง" name="mini_search"/>
           			<hr />
           		</td>
            </tr>
	 	</table>
	 	
	 	<!-- management criteria -->
	 	<?php 
		 	$input_fields = array(
		 			'Employees'
		 			,'CompanyCode'
		 			,'CompanyNameEng'
		 			,'Address1'
		 			,'Moo'
		 			,'Soi'
		 			,'Road'
		 			,'Subdistrict'
		 			,'District'
		 			,'Province'
		 			,'Zip'
		 			,'Telephone'
		 			,'email'
		 			,'TaxID'
		 			,'CompanyTypeCode'
		 			,'BusinessTypeCode'
		 			,'BranchCode'
		 			,'org_website'
		 	);
		 	
		 	$use_condition = 0;
		 	$condition_sql = "1=1";
		 	
		 	if($sess_accesslevel == 6 || $sess_accesslevel == 7){
		 		$condition_sql .= " and z.CompanyTypeCode >= 200  and z.CompanyTypeCode < 300";
		 	}else{
		 		$condition_sql .= " and z.CompanyTypeCode < 200";
		 	}
		 	
		 	for($i = 0; $i < count($input_fields); $i++){
		 		if(strlen($_POST[$input_fields[$i]])>0){
		 			$use_condition = 1;
		 				
		 			if($input_fields[$i] == "Province"  ){
		 				$condition_sql .= " and z.$input_fields[$i] = ".doCleanInput($_POST[$input_fields[$i]]);
		 			}elseif($input_fields[$i] == "Employees" ){
		 				$condition_sql .= " and y.$input_fields[$i] >= '".doCleanInput($_POST[$input_fields[$i]])."'";
		 			}else{
		 				$condition_sql .= " and z.$input_fields[$i] like '%".doCleanInput($_POST[$input_fields[$i]])."%'";
		 			}
		 		}
		 	}
		 	
		 	//--- ประจำปี
		 	$cur_year = date("Y");
		 	
		 	if(isset($_POST['ddl_year'])){
		 		$cur_year = $_POST['ddl_year'];
		 	}
		 	 
		 	if($for_year){
		 		$cur_year = $for_year;
		 	}
		 	
		 	if($cur_year >= 2013){
		 		//show main branch only
		 		$condition_sql .= " and BranchCode < 1";
		 		$is_2013 = 1;
		 	}
		 	
		 	//--- สถานะ
		 	if(strlen($_POST["LawfulFlag"]) > 0){
		 		$lawful_condition = " and y.LawfulStatus = '".$_POST["LawfulFlag"]."'";
		 	
		 		//if non-lawful then also show records that didn't have lawfulness
		 		if($_POST["LawfulFlag"] == "0"){
		 			$lawful_condition = " and (y.LawfulStatus = '0' or y.LawfulStatus is null)";
		 		}
		 	}else {
		 		$lawful_condition = " and (y.LawfulStatus = '0' or y.LawfulStatus is null or y.LawfulStatus = '2')";
		 	}

		 	//--- ชื่อ
		 	if(strlen($_POST["CompanyNameThai"]) > 0){
		 		$name_exploded_array = explode(" ",doCleanInput($_POST["CompanyNameThai"]));

		 		for($i=0; $i<count($name_exploded_array);$i++){
		 			if(strlen(trim($name_exploded_array[$i]))>0){
		 				$use_condition = 1;
		 				$condition_sql .= " and z.CompanyNameThai like '%".doCleanInput($name_exploded_array[$i])."%'";
		 	
		 			}
		 		}
		 	}

		 	if($have_search_id){
		 		$use_condition = 1;
		 		$condition_sql .= " and z.CID = '".doCleanInput($_GET["search_id"])."'";
		 	}
	 	
		 	$the_sql = " SELECT count(z.CID) FROM company z
						 LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
						 LEFT outer JOIN provinces c ON z.Province = c.province_id
						 JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year'
						 where $condition_sql $lawful_condition ";
		 	
		 	if($is_2013)
		 	{
		 		$the_sql = "SELECT count(*) FROM company z
							LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
		 					LEFT outer JOIN provinces c ON z.province = c.province_id
		 					JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year'
		 					where $condition_sql $lawful_condition ";
		 	}
		 	
			if($is_2013){
				$record_count_all = (getFirstItem($the_sql));
			}else{			
				$record_count_all = getFirstItem($the_sql);
			}
	 	?>
	 	<!-- end management criteria -->
	 	
	 	<!-- management page -->
	 	<?php
			//pagination stuffs
			$per_page = 20;
			$num_page = ceil($record_count_all/$per_page);
			
			$cur_page = 1;
			if(is_numeric($_POST["start_page"]) && $_POST["start_page"] <= $num_page && $_POST["start_page"] > 0){
				$cur_page = $_POST["start_page"];
			}
			
			$starting_index = 0;
			if($cur_page > 1){
				$starting_index = ($cur_page-1) * $per_page;						
			}
		?>
		<!-- end management page -->
	 	
	 	<table border="0" width="100%" >
	 	<?php 
	 		if(!isset($_POST["mini_search"]) && !$have_search_id && !isset($_POST["start_page"])){
	 			$do_hide_company_list = 1;
	 		}
	 		
	 		if($have_search_id || $do_hide_company_list){
	 			//dont show this if have above conditions
	 		}else{
 		?>
	 	
	 		<tr>
	 			<td align="left">
	 				<div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือกสถานประกอบการที่ต้องการส่งจดหมายทวงถาม</div>
	 				
	 				<font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font> 
	 			</td>
	 			<td align="right" valign="bottom">
	 				<div style="padding:5px 0 0px 0;" align="right">
	 					แสดงข้อมูล:
		 				<select name="start_page" onchange="this.form.submit()">
	                        <?php for($i = 1; $i <= $num_page; $i++){?>
	                            	<option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
    						<?php }?> 
	                    </select>
	 				</div>
	 			</td>
	 		</tr>
	 		<tr>
		 		<td colspan="2" align="left" style="padding-bottom:5px" valign="middle">
		 			<?php if(!$do_hide_company_list && $sess_accesslevel != 4){ ?>
		 			<table border="0" style="color: #006699">
                    	<tr>
                        	<td>
                            	<img src="decors/green.gif" alt="ทำตามกฎหมาย" title="ทำตามกฎหมาย">
                            </td>
                            <td valign="middle">= ทำตามกฎหมาย </td>
                            <td>
                            	<img src="decors/red.gif" alt="ไม่ทำตามกฎหมาย" title="ไม่ทำตามกฎหมาย">
                            </td>
                            <td valign="middle">= ไม่ทำตามกฎหมาย </td>
                            <td>
                                <img src="decors/yellow.gif" alt="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน" title="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน">
                            </td>
                            <td valign="middle">= ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน </td>
                            <td>
                            	<img src="decors/blue.gif" alt="ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>" title="ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>">
                            </td>
                            <td valign="middle">= ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?> </td>
                    	</tr>
                	</table>
                    <?php } ?>        
		 		</td>
	 		</tr>
	 		<?php }?>
	 	</table>
	</form>
	<!-- end form --> 
	
	<!-- form -->
	<form method="post" action="scrp_add_collection_doc.php" onsubmit="return validateAddCollectionDoc(this);" enctype="multipart/form-data">
		<?php if($have_search_id == 1){?>
        	<input name="search_id" type="hidden" value="<?php echo $_GET["search_id"];?>">
        <?php } ?>
        
        <?php if(!$do_hide_company_list){ ?>
	    <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; <?php if($sess_accesslevel == 4){?>display:none;<?php }?>">
	        <tr bgcolor="#9C9A9C" align="center" >
				<td >
					<input name="chk_all" id="chk_all" type="checkbox" value="1" onclick="checkOrUncheck();" 
					<?php
						if($have_search_id){
							echo "checked='checked'";
						}
					?>
					/>
				</td>
				<td >
					<div align="center"><span class="column_header"><?php echo $the_code_word;?> </span></div>
				</td>
				<td>
					<div align="center">
						<span class="column_header">
						<?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
							ประเภท
						<?php }else{ ?>
							ประเภทกิจการ
						<?php }?>
						</span>
					</div>
				</td>
				<td>
					<div align="center">
						<span class="column_header"> ชื่อ
							<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
								
							<?php }else{?>                            
								นายจ้างหรือ
							<?php }?>
							<?php echo $the_company_word;?>
						</span>
					</div>
				</td>
				<td>
					<div align="center"><span class="column_header">จังหวัด</span></div>
				</td>
				<td>
					<div align="center">
						<span class="column_header">
							<?Php if($is_2013){?>
								<?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
									จำนวน<?php echo $the_employees_word;?>
								<?php }else{ ?>
									จำนวน<?php echo $the_employees_word;?><br>รวมทุกสาขา
								<?php }?>
							<?php }else{?>
								จำนวน<?php echo $the_employees_word;?>
							<?php }?>
						</span>
					</div>
				</td>
				<td>
					<div align="center"><span class="column_header">สถานะ</span></div>
				</td>
	        </tr>
	        
	        <!-- generate rows -->
	        <?php
			$the_limit = "limit $starting_index, $per_page";
						
			$get_org_sql = "SELECT 
								z.employees as company_employees
								, z.CID
								, Province
								, CompanyCode
								, CompanyTypeName
								, CompanyNameThai
								, province_name
								, LawfulFlag
								, y.LawfulStatus as lawfulness_status
								, y.Employees as lawful_employees
								, email
							FROM company z
							LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
							LEFT outer JOIN provinces c ON z.province = c.province_id
							JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
							where $condition_sql $lawful_condition
							order by CompanyNameThai asc $the_limit";

			if($is_2013){
				$get_org_sql = "SELECT 
									z.CID
									, Province
									, CompanyCode
									, CompanyTypeName
									, CompanyNameThai
									, province_name
									, LawfulFlag
									, y.LawfulStatus as lawfulness_status
									, y.Employees as lawful_employees
									, email
								FROM company z
								LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
								LEFT outer JOIN provinces c ON z.province = c.province_id
								JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
								where $condition_sql $lawful_condition
								order by CompanyNameThai asc $the_limit";
			}

			$org_result = mysql_query($get_org_sql);
					
			//total records 
			$total_records = 0;
					
			while ($post_row = mysql_fetch_array($org_result)) {
				$total_records++;
				$this_province = $post_row["Province"];
				$employee_to_use = $post_row["lawful_employees"];
				
				if($employee_to_use == 0){
					$employee_to_use = $post_row["company_employees"];
						
					if($is_2013){
						//sum mployees from all brances
						$sum_sql = "select sum(Employees) from company where CompanyCode = '".$post_row["CompanyCode"]."'";
						$employee_to_use = getFirstItem($sum_sql);
					}
				}
		?>
		
			<tr bgcolor="#ffffff" align="center">
				<?php 
					$js_do_check .= "document.getElementById('chk_$total_records').checked = true;";
					$js_do_uncheck .= "document.getElementById('chk_$total_records').checked = false;";
				?>
				<td >
					<input name="chk_<?php echo $total_records; ?>" id="chk_<?php echo $total_records; ?>" type="checkbox" value="<?php echo doCleanOutput($post_row["CID"]);?>"
						<?php
							if($have_search_id){
								echo "checked='checked'";
							}
						?>
					/>
				</td>
				<td >
					<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?><?php
							if(!$is_2013){
								echo "&all_tabs=1";
							}
					?>&year=<?php echo $cur_year;?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>                          
				</td>
				<td><?php echo doCleanOutput($post_row["CompanyTypeName"]);?></td>
				<td><?php echo doCleanOutput($post_row["CompanyNameThai"]);?></td>
				<td><?php echo doCleanOutput($post_row["province_name"]);?></td>
				<td align="right"><div align="right"><?php echo number_format(doCleanOutput(default_value($employee_to_use,0)));?></div></td>
				<td>
					<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"])); ?></div>                         
				</td>
			</tr>
		<?php } //end loop to generate rows ?>
		</table>          
		<?php } //end if(!$do_hide_company_list) ?>
		
		<input name="total_records" type="hidden" value="<?php echo $total_records; ?>" />
		
		<?php  if(!$do_hide_company_list){ ?>
			<hr />
			<strong>3. ส่งจดหมายทวงถาม</strong>
			<?php if(($type != "hold") && $record_count_all > 20){?>
				<div style="padding:5px 0 0 0">
			    	<input name="cur_year" type="hidden" value="<?php echo $cur_year?>">
			        <input name="send_to_all" type="checkbox" value="
			        	<?php 
							$the_all_condition_sql = $condition_sql . " " . $lawful_condition;
							echo $the_all_condition_sql;
						?>
					"> 
					<strong style="color:#006699">ส่งแจ้งทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
	            </div>
			<?php } ?>
			
			<table style=" padding:10px 0 0px 0;">
            	<tr>
                	<td bgcolor="#efefef">ประจำปี: </td>
                    <td colspan="5"><?php include "ddl_year.php";?></td>
               	</tr>
               	<tr>
               		<td bgcolor="#efefef">วันที่: </td>
                    <td>  
						<?php		   
						   $selector_name = "RequestDate";
						   
						   $this_date_time = date("Y-m-d");
						 
						   if($this_date_time != "0000-00-00"){
							   $this_selected_year = date("Y", strtotime($this_date_time));
							   $this_selected_month = date("m", strtotime($this_date_time));
							   $this_selected_day = date("d", strtotime($this_date_time));
						   }
						   
						   include ("date_selector.php");
						   ?>         
					</td>
                    <td bgcolor="#efefef">ครั้งที่: </td>
                    <td><input name="RequestNum" type="text" id="RequestNum" value="" /></td>
                    <td bgcolor="#efefef">หนังสือเลขที่: </td>
                    <td><input name="GovDocumentNo" type="text" id="GovDocumentNo" value="" /></td>
               	</tr>
               	<tr>
               		<td bgcolor="#efefef">ผู้ดำเนินการ:</td>
               		<td colspan="5">
               			<input name="CreatedBy" type="text" id="CreatedBy" value="<?php echo $sess_userfullname;?>"/>
               		</td>
               	</tr>
               	<tr>
                	<td bgcolor="#efefef">แนบเอกสาร:</td>
                    <td colspan="5">
                    	<div class="input-file-container single-file">
                       		<input id="collector_doc" name="collector_doc" type="file" />
                       	</div> 
                	</td>
            	</tr>
            	<tr>
            		<td valign="top" bgcolor="#efefef">รายละเอียด:</td>
                    <td colspan="5">
                    	<textarea name="DocumentDetail" cols="50" rows="4" id="DocumentDetail"></textarea>
                	</td>
            	</tr>
           	</table>
           	
           	<div style="padding-top: 10px;">
				<input type="submit" value="สร้างจดหมาย" />
            </div>
		<?php } //end 3. ส่งจดหมายทวงถาม?>
	</form>
	<!-- enf form -->
</td>
</tr>

<!-- footer section -->
<tr>
	<td align="right" colspan="2">
    	<?php include "bottom_menu.php";?>
    </td>
</tr>
</table>
</td>
</tr>
</table>
</div>
<!--end page cell-->
</td>
</tr>
</table>

<!-- section script -->
<script type="text/javascript" src="/kendo/kendo.all.min.js"></script>

<script id='uploadFileTemplate' type='text/x-kendo-template'>
    <span class='k-progress'></span>
    <div class='file-wrapper'>
           <button type='button' class='k-upload-action'></button>   
           <span class='file-name'>#=name#</span>
           <a href='##' target='_blank' class='file-link' >#=name#</a>
    </div>
</script>

<script language="javascript">
	$(function () {
		createUpload();
		createDatePicker();
	});

	function createDatePicker(){
		var datepicker = $(".nep-datepicker");
		$.each(datepicker, function(){
			$(this).kendoDatePicker({               
                format: "dd MMMM yyyy",
                parseFormats: ["dd MMMM yyyy"],
                culture: "th-TH",
                footer: cale.footerTemplate, 
                open: cale.onDatePickerOpen
            });
		});
    }

	function createUpload() {
		var sendingLetterExistingFiles = [];
		var letterFileControl = $('#collector_doc');   
		letterFileControl.kendoUpload({
	        enabled: true,
	        multiple: false,
	        async: {
	            saveUrl: './attachmenthandler/upload.php',
	            removeUrl: './attachmenthandler/remove.php',
	            autoUpload: true
	        },
	        files: sendingLetterExistingFiles,
	        template: kendo.template(document.getElementById('uploadFileTemplate').innerHTML),
	        localization: {
	            select: "เลือกไฟล์"
	        }        
	    });

		searchOrgList();
	}
						   
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

	function searchOrgList(){
		var year = '<?php echo $_GET["year"]?>';
		
		if(year != ''){
			var options = $('#ddl_year').find('option');
			var value;
			var isYearSearched = false;
			$.each(options, function(){
				value = $(this).val();
				
				if((value == year) && (!this.selected)){
					$(this).attr('selected', 'selected');		
					isYearSearched = true;			
				}
			});

			if(isYearSearched){
				$("[name='mini_search'").click();
			}	
		}		
	}

	function validateAddCollectionDoc(frm){
		var isValid = true;

		if(frm.RequestNum.value.length == 0){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ครั้งที่");
			frm.RequestNum.focus();
		}

		if(frm.GovDocumentNo.value.length == 0){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: หนังสือเลขที่");
			frm.GovDocumentNo.focus();
			
		}

		return isValid;
	}
</script>
<!-- end section script -->
		
</body>
</html>
<!-- end footer section -->