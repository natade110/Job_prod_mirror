<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';

if(!hasViewRoleCollection()){
	header ("location: index.php");
}

$condition_sql = "";
// ประจำปี
if(strlen($_POST["ddl_year"]) > 0){
	$condition_sql .= " and l.Year = '".doCleanInput($_POST["ddl_year"])."'";
}

// ตั้งแต่วันที่  ถึงวันที่
	$this_from_time = $_POST["FromDate"];
	$this_end_time = $_POST["EndDate"];

if(($_POST["FromDate"] != "") && ($_POST["EndDate"] != "")){
	$sql_from_date = convertThaiDateToSqlFormat($this_from_time);
	$sql_end_date = convertThaiDateToSqlFormat($this_end_time);	
	$condition_sql .= " and date(s.SentDate) between DATE_FORMAT('$sql_from_date','%Y-%m-%d') and DATE_FORMAT('$sql_end_date','%Y-%d-%m') ";
}else if ($_POST["FromDate"] != ""){
	$sql_from_date = convertThaiDateToSqlFormat($this_from_time);
	$condition_sql = "and date(s.SentDate) = DATE_FORMAT('$sql_from_date','%Y-%m-%d')";
}else if ($_POST["EndDate"] != ""){
	$sql_end_date = convertThaiDateToSqlFormat($this_end_time);
	$condition_sql = "and date(s.SentDate) = DATE_FORMAT('$sql_end_date','%Y-%m-%d')";
}else{
	$this_from_time = "0000-00-00";
}


$company_filter = "";
$zone = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
if((($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ)) && ($zone != null)){
	
	$company_filter = " and
		(
	
		com.District in (
			select district_name
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
	$company_filter = " and com.Province = '$sess_meta'";
}

// initialization valiables
$per_page = 20;
$cur_page = 1;
$starting_index = 0;




$the_sql = "select count(distinct date(s.SentDate) ,l.Year )  from schedulecollectionhistory s
			left join lawfulness l on s.LID = l.LID 
			left join company com on l.CID = com.CID 
			where 1=1 $condition_sql $company_filter
			group by date(s.SentDate) ,l.Year";
//echo($the_sql);
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

$get_collection_sql = "select date(s.SentDate) SentDate ,l.Year Year  from schedulecollectionhistory s
						left join lawfulness l on s.LID = l.LID 
						left join company com on l.CID = com.CID 
						where 1=1 $condition_sql  $company_filter
						group by date(s.SentDate) ,l.Year
						order by date(s.SentDate)  , l.Year $the_limit";
error_log($get_collection_sql);
$collection_result = mysql_query($get_collection_sql);
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
                
	<td valign="top" style="padding-left: 5px;">
		<h2 class="default_h1" style="margin:0; padding:0;">ประวัติการส่งอีเมลล์ทวงถามทั้งหมด</h2>
		<br />
		
		<form method="post" action="schedulecollectionemail_list.php">
			<table style=" padding:10px 0 0px 0;">
                <tr>
                	<td bgcolor="#efefef">ประจำปี:</td>
                    <td><?php include "ddl_year_with_blank.php";?></td>
                    <td bgcolor="#efefef">ตั้งแต่วันที่:</td>
                    <td>
						<input id="FromDate" name="FromDate" class="nep-datepicker" />  
					</td>
                    <td bgcolor="#efefef">ถึงวันที่:</td>
                    <td>
						<input id="EndDate" name="EndDate" class="nep-datepicker" />  
					</td>					
                    <td bgcolor="#efefef"><input type="submit" value="แสดง" name="mini_search"/></td>
				</tr>
                <tr>
                	<td colspan="5">
                    	<div align="left">
                        	<select name="start_page" onchange="this.form.submit()">
                            	<?php  for($i = 1; $i <= $num_page; $i++){ ?>
                                    <option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
                                <?php } ?> 
                            </select>
                        </div>                            
                    </td>
				</tr>
        	</table>

		
		<table border="1"  cellspacing="0" cellpadding="5" style="border-collapse:collapse; " width="100%">
        	<tr bgcolor="#9C9A9C" align="center" >
           	  	<td >
                	<div align="center"><span class="column_header">ลำดับที่</span></div>
                </td>        	
           	  	<td >
                	<div align="center"><span class="column_header">วันที่ส่ง</span></div>
                </td>
                <td>
                	<div align="center"><span class="column_header">ประจำปี</span></div>
                </td>
                <td>
                	<div align="center"><span class="column_header">จำนวนสถานประกอบการ</span></div>
                </td>
    		</tr>
    		
    		<?php 
    			$total_records = $starting_index;
    			// generate rows
    			If ($collection_result != null){
    			while ($post_row = mysql_fetch_array($collection_result)) {
    				$total_records++;
    		?>
    		<tr bgcolor="#ffffff" align="center" >
    			<td>
    				<div align="center">
    					<?php echo $total_records;?>
    				</div>
    			</td>
    			<td>
    				<a href="schedulecollectionemail_view.php?sentdate=<?php echo doCleanOutput($post_row["SentDate"]);?>&year=<?php echo doCleanOutput($post_row["Year"]);?>"><?php echo formatDateThai($post_row["SentDate"]);?></a>
    			</td>
    			<td>
    				<?php echo formatYear($post_row["Year"]); ?>
    			</td>
    			<td>
    				<?php 
    					$the_count = getFirstItem(" select count(*) from schedulecollectionhistory s
													left join lawfulness l on s.LID = l.LID 
													left join company com on l.CID = com.CID 
													where l.Year = '".$post_row["Year"]."' $company_filter
    												and date(s.SentDate) = '".$post_row["SentDate"]."'");
    				?>    			
    				<font <?php echo $styleCount;?>>
                    	<div align="right"><?php echo number_format($the_count,0,".",",");?> แห่ง</div>                          
                    </font>
    			</td>
                	
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