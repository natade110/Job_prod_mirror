<?php
include "db_connect.php";
include "session_handler.php";
require_once 'c2x_include.php';

$sentdate_get = (isset($_GET["sentdate"]))? $_GET["sentdate"] : "0000-00-00";
$year_get = (isset($_GET["year"]))? $_GET["year"] : 0;
//echo($year_get);

/// START BUILDING CONDITIONS


//--- ประจำปี
// วันที่ส่ง mail
if($sentdate_get  != ""){
	$condition_sql .= " and date(s.SentDate) = DATE_FORMAT('$sentdate_get','%Y-%m-%d')";
}

// ประจำปี
if($year_get > 0){
	$condition_sql .= " and l.Year = '$year_get'";
}

if($year_get >= 2013){
	$condition_sql .= " and BranchCode < 1";
	$is_2013 = 1;
}

if($sess_accesslevel == 6 || $sess_accesslevel == 7){

	$condition_sql .= " and com.CompanyTypeCode >= 200  and com.CompanyTypeCode < 300";

}else{

	$condition_sql .= " and com.CompanyTypeCode < 200";

}

if(is_numeric($_GET["LawfulFlag"])){
	$_POST["LawfulStatus"] = $_GET["LawfulFlag"];
}

$input_fields = array(
		'LawfulStatus'
		,'Province'
		,'CompanyTypeCode'
		,'CompanyCode'		
		,'BusinessTypeCode'
);

for($i = 0; $i < count($input_fields); $i++){

	if(strlen($_POST[$input_fields[$i]])>0){
			
		$use_condition = 1;
			
		if($input_fields[$i] == "Province"  ){
			$condition_sql .= " and com.$input_fields[$i] like '".mysql_real_escape_string($_POST[$input_fields[$i]])."'";
		}else if($input_fields[$i] == "LawfulStatus"  ){
			$condition_sql .= " and l.$input_fields[$i] like '%".mysql_real_escape_string($_POST[$input_fields[$i]])."%'";
		}else{
			$condition_sql .= " and com.$input_fields[$i] like '%".mysql_real_escape_string($_POST[$input_fields[$i]])."%'";
		}
			
	}
}


if(strlen($_POST["CompanyNameThai"]) > 0){

	$name_exploded_array = explode(" ",mysql_real_escape_string($_POST["CompanyNameThai"]));

	for($i=0; $i<count($name_exploded_array);$i++){

		if(strlen(trim($name_exploded_array[$i]))>0){
			$condition_sql .= " and com.CompanyNameThai like '%".mysql_real_escape_string($name_exploded_array[$i])."%'";
		}
	}
}


$lawfulyear_condition = " and (l.Year >= 2011) ";

$company_condition = "";
$zone = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
if((($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ)) && ($zone != null)){
	
	$company_condition = " and
		(	
			com.District in (select district_name
				from districts
				where
				district_area_code
				in (
					select district_area_code
					from zone_district
					where zone_id = '$zone'
				)
			)
			or
			com.district_cleaned in (
				select district_name
				from districts
				where district_area_code
				in (
					select district_area_code
					from zone_district
					where zone_id = '$zone'
				)
			)
		)";

}
if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
	$company_condition = " and com.Province = '$sess_meta'";
}



// initialization valiables
$per_page = 20;
$cur_page = 1;
$starting_index = 0;




$the_sql = "select count(*)  from schedulecollectionhistory s
			left join lawfulness l on s.LID = l.LID
			left join company com on l.CID = com.CID
			where 1=1 $condition_sql
			$lawfulyear_condition
			$company_condition";
// echo($the_sql);
$record_count_all = getFirstItem($the_sql);
$num_page = ceil($record_count_all/$per_page);

// page on select
if(is_numeric($_POST["start_page"]) && $_POST["start_page"] <= $num_page && $_POST["start_page"] > 0){
	$cur_page = $_POST["start_page"];
}

if($cur_page > 1){
	$starting_index = ($cur_page-1) * $per_page;
}

$the_limit = "limit $starting_index, $per_page";

$get_schedule_sql = "select 	com.employees as company_employees
						, com.CID
						, Province
						, CompanyCode
						, CompanyTypeName
						, CompanyNameThai
						, province_name
						, LawfulFlag
						, l.LawfulStatus as lawfulness_status
						, l.Employees as lawful_employees
						, com.email
						, SentDate
						, ReceivedDate					
						from schedulecollectionhistory s
						left join lawfulness l on s.LID = l.LID
						left join company com on l.CID = com.CID
						left join companytype comtype on com.CompanyTypeCode = comtype.CompanyTypeCode
						left outer JOIN provinces c ON com.Province = c.province_id
						where 1=1 $condition_sql
						$lawfulyear_condition
						$company_condition
						order by s.SentDate $the_limit";

						//$schedule_result = mysql_query($get_schedule_sql);
						// end management paging
					
		?>

<?php include "header_html.php";?>
<?php include "global.js.php";?>

<script type="text/javascript" src="/kendo/kendo.all.min.js"></script>
<script type="text/javascript" src="/kendo/kendo.culture.th-TH.min.js"></script>
<script type="text/javascript" src="/kendo/kendo.calendar.custom.js"></script>
<script type="text/javascript">
	kendo.culture("th-TH");
</script>

<form method="post" action="schedulecollectionemail_view.php?sentdate=<?php echo doCleanOutput($_GET["sentdate"]);?>&year=<?php echo doCleanOutput($_GET["year"]);?>"></a>
	<td valign="top" style="padding-left: 5px;">
		<h2 class="default_h1" style="margin:0; padding:0 0 0px 0;">
			ประวัติการส่งอีเมลล์ทวงถามวันที่: <font color="#006699"><?php echo formatDateThai($sentdate_get);?></font></font> ประจำปี: <font color="#006699"><?php echo formatYear($year_get);?></font>
		</h2>
		<div style="padding:5px 0 10px 2px">
			<a href="schedulecollectionemail_list.php">ประวัติการส่งอีเมลล์ทวงถามทั้งหมด</a> > วันที่:  <?php echo formatDateThai($sentdate_get)?>  ปี:  <?php echo formatYear($year_get)?>
		</div>
		


			<table style=" padding:10px 0 0px 0;">
		<tr>
 	        <td bgcolor="#efefef">สถานะ:  </td>           
     		<td>
     			<select name="LawfulStatus" id="LawfulFlag_search">
    				<option value="" selected="selected">-- all --</option>
    				<option value="1" <?php if($_POST["LawfulStatus"] == "1"){echo "selected='selected'";}?>>ทำตามกฏหมาย</option>
    				<option value="0" <?php if($_POST["LawfulStatus"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
    				<option value="2" <?php if($_POST["LawfulStatus"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน</option>
    				<option value="3" <?php if($_POST["LawfulStatus"] == "3"){echo "selected='selected'";}?>>ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?></option>
				</select>
			</td>	            	
     	</tr>						
	    <tr>	                        	
	        <td bgcolor="#efefef">ชื่อ:  </td>
	        <td>
	             <input type="text" name="CompanyNameThai" value="<?php echo writeHtml($_POST["CompanyNameThai"]);?>" />     
             </td>
             <td bgcolor="#efefef"><?php echo $the_code_word;?>:</td>
             <td>
                  <input type="text" name="CompanyCode" value="<?php echo writeHtml($_POST["CompanyCode"]);?>" />                      
             </td>
         </tr>
         <tr>
             <td bgcolor="#efefef">
                          
              <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                    	ประเภทหน่วยงาน:
              <?php }else{?>
                       	 ประเภทธุรกิจ:
              <?php }?>
                          
              </td>
              <td><?php include "ddl_org_type.php";?>  </td>
               <?php if($sess_accesslevel == 6 ||  $sess_accesslevel == 7){?> 
               <?php }else{?>
               <td bgcolor="#efefef"> ประเภทกิจการ:</td>
               <td><?php include "ddl_bus_type.php";?></td>
               <?php }?>                                  
          </tr>
          <tr>
               <td bgcolor="#efefef"> จังหวัด: </td>
               <td><?php include "ddl_org_province.php";?></td>
               <td >&nbsp;</td>
               <td>&nbsp;</td>
          </tr>
          <td bgcolor="#efefef"><input type="submit" value="แสดง" name="mini_search"/></td>
       	</table>

	<table style="width: 100%">
		<tr>
			<td>
				<font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font> 
			</td>
			<td>
				<div style="padding:5px 0 0px 0;" align="right">
				                            แสดงข้อมูล:						                            
	                            <select name="start_page" onchange="changePagination()">
	                            	<?php 
							for($i = 1; $i <= $num_page; $i++){
						?>
	                            	<option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
	    							<?php
	                                    }
									?> 
	                            </select>									
							</div>
						</td>
			</tr>
		</table>	        	
		
		<table class="nep-grid" width="100%">
		<tr bgcolor="#9C9A9C" align="center" >
			<th >
				<div align="center"><span class="column_header"><?php echo $the_code_word;?> </span></div>
			</th>
			<th>
				<div align="center">
					<span class="column_header">
					<?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
						ประเภท
					<?php }else{ ?>
						ประเภทกิจการ
					<?php }?>
					</span>
				</div>
			</th>
			<th>
				<div align="center">
					<span class="column_header"> ชื่อ
						<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
							
						<?php }else{?>                            
							นายจ้างหรือ
						<?php }?>
						<?php echo $the_company_word;?>
					</span>
				</div>
			</th>
			<th>
				<div align="center"><span class="column_header">จังหวัด</span></div>
			</th>
			<th>
				<div align="center"><span class="column_header">สถานะ</span></div>
			</th>
			<th>
				<div align="center"><span class="column_header">วันที่ส่ง mail</span></div>
			</th>				
			<th>
				<div align="center"><span class="column_header">วันที่รับ mail</span></div>
			</th>			
        </tr>
    		
    		<?php 
    			$total_records = $starting_index;
    			// generate rows

    			If ($get_schedule_sql != null){ 	
    				//echo($get_schedule_sql);
    				$schedule_result = mysql_query($get_schedule_sql);
    				while ($post_row = mysql_fetch_array($schedule_result)) {
    					$total_records++;
    					$this_province = $post_row["Province"];
    					$employee_to_use = $post_row["lawful_employees"];
    					
    				/*	if($employee_to_use == 0){
    						echo($post_row["company_employees"]);
    						$employee_to_use = $post_row["company_employees"];
    							
    						if($is_2013){
    							//sum employees from all brances
    							$sum_sql = "select sum(Employees) from company where CompanyCode = '".$post_row["CompanyCode"]."'";
    							$employee_to_use = getFirstItem($sum_sql);
    						}
    					}*/
    		?>
        <tr>   
			<td><?php writeHtml($post_row["CompanyCode"]);?></td>
			<td><?php echo doCleanOutput($post_row["CompanyTypeName"]);?></td>
			<td><?php echo doCleanOutput($post_row["CompanyNameThai"]);?></td>  
			<td><?php echo doCleanOutput($post_row["province_name"]);?></td>
			<td>
				<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"])); ?></div>     
			</td>
			<td><?php echo doCleanOutput(formatDateThai($post_row["SentDate"]));?></td>  	
			<td><?php echo doCleanOutput(formatDateThai($post_row["ReceivedDate"]));?></td>  	      		
        </tr>   
    		<?php }} // end generate rows?>
 		
    	</table>
      </form> <!-- end form -->
	</td><!-- End Content Block -->
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

</div><!--end page cell-->
</td>
</tr>
</table>

<!-- section script -->
<script type="text/javascript">
	$(function () {
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

</script>
<!-- end section script -->

</body>
</html>
<!-- end footer section -->