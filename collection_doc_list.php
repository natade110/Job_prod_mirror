<?php
require_once 'db_connect.php';
require_once 'session_handler.php';

$issuccess = is_null($_GET["issuccess"]) ? "" : $_GET["issuccess"];
$isDeleted = is_null($_GET["deleted"])? "" : $_GET["deleted"];
$style = "style='display:none'";
$resultMsg = "";

if($issuccess == true){
	$style = "style='color:#006600; padding:5px 0 0 0; font-weight: bold;'";
	$resultMsg = "* บันทึกข้อมูลสำเร็จ";
}

if($isDeleted == true){
	$style = "style='color:#006600; padding:5px 0 0 0; font-weight: bold;'";
	$resultMsg = "* ลบข้อมูลสำเร็จ";
}

// หนังสือเลขที่
if(strlen($_POST["GovDocumentNo"]) > 0){
	$condition_sql .= "and c.GovDocumentNo like '%".doCleanInput($_POST["GovDocumentNo"])."%'";
}

// ครั้งที่
if(strlen($_POST["RequestNo"]) > 0){
	$condition_sql .= " and c.RequestNo = '".doCleanInput($_POST["RequestNo"])."'";
}

// ประจำปี
if(strlen($_POST["ddl_year"]) > 0){
	$condition_sql .= " and l.Year = '".doCleanInput($_POST["ddl_year"])."'";
}

// วันที่
if($_POST["RequestDate"] != ""){
	$this_date_time = $_POST["RequestDate"];
	$sql_request_date = convertThaiDateToSqlFormat($this_date_time);
	$condition_sql .= " and c.RequestDate = '$sql_request_date'";
}else{
	$this_date_time = "0000-00-00";
}

// management paging
// initialization valiables
$per_page = 20;
$cur_page = 1;
$starting_index = 0;

$the_sql = "select count(*) from collectiondocument c 
			inner join lawfulness l on c.LID = l.LID 
			where 1=1 $condition_sql group by c.GovDocumentNo,c.RequestNo,c.RequestDate";

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

$get_collection_sql = "select c.* , l.Year from collectiondocument c
						inner join lawfulness l on c.LID = l.LID
						where 1=1 $condition_sql group by c.GovDocumentNo,c.RequestNo,c.RequestDate order by CollectionID $the_limit";

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
		<h2 class="default_h1" style="margin:0; padding:0;">จดหมายทวงถามทั้งหมด</h2>
		<br />
		
		<form method="post" action="collection_doc_list.php">
			<span id="displaymsg" <?php echo ($style);?>><?php echo $resultMsg;?></span>
			
			<table style=" padding:10px 0 0px 0;">
            	<tr>
                	<td bgcolor="#efefef">หนังสือเลขที่:</td>
                    <td><input type="text" name="GovDocumentNo" value="<?php echo $_POST["GovDocumentNo"];?>" /></td>
                    <td bgcolor="#efefef">ครั้งที่: </td>
                    <td><input type="text" name="RequestNo" style="width:100px;" value="<?php echo $_POST["RequestNo"];?>" /></td>
                    <td></td>
                </tr>
                <tr>
                	<td bgcolor="#efefef">ประจำปี:</td>
                    <td><?php include "ddl_year_with_blank.php";?></td>
                    <td bgcolor="#efefef">วันที่:</td>
                    <td>
						<input id="RequestDate" name="RequestDate" class="nep-datepicker" />  
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
		</form> <!-- end form -->
		
		<table border="1"  cellspacing="0" cellpadding="5" style="border-collapse:collapse; " width="100%">
        	<tr bgcolor="#9C9A9C" align="center" >
	            <td>
	            	<div align="center"><span class="column_header">ลำดับที่</span></div>
	            </td>     
           	  	<td >
                	<div align="center"><span class="column_header">หนังสือเลขที่</span></div>
                </td>
                <td>
                	<div align="center"><span class="column_header">ครั้งที่</span></div>
                </td>
                <td>
                	<div align="center"><span class="column_header">ประจำปี</span></div>
                </td>
                <td>
                	<div align="center"><span class="column_header">วันที่</span> </div>
                </td>
                <td>
                	<div align="center"><span class="column_header">จำนวนสถานประกอบการ</span></div>
                </td>
                <?php if($sess_accesslevel != 5){ //exec wont see these?>
                <td><div align="center"><span class="column_header">ลบข้อมูล</span> </div></td>
                <?php }?>
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
    				<a href="view_collection_doc.php?id=<?php echo doCleanOutput($post_row["CollectionID"]);?>"><?php echo doCleanOutput($post_row["GovDocumentNo"]);?></a>
    			</td>
    			<td>
    				<?php echo doCleanOutput($post_row["RequestNo"]);?>
    			</td>
    			<td>
    				<?php echo formatYear($post_row["Year"]); ?>
    			</td>
    			<td>
    				<?php echo formatDateThai($post_row["RequestDate"]);?>
    			</td>
    			<td>
    				<?php 
    					$the_count = getFirstItem("select count(*) from collectiondocument c
    												inner join lawfulness l on c.LID = l.LID
    												where c.GovDocumentNo = '".$post_row["GovDocumentNo"]."' 
    												and c.RequestNo = ".$post_row["RequestNo"]."
    												and c.RequestDate = '".$post_row["RequestDate"]."'
    												and l.Year = '".$post_row["Year"]."'");
    					
    					if($the_count < 1){
    						$styleCount = "color='#FF0000'";
    					}else{
    						$styleCount = "";
    					}
    				?>
    				
    				<font <?php echo $styleCount;?>>
                    	<div align="right"><?php echo number_format($the_count,0,".",",");?> แห่ง</div>                          
                    </font>
    			</td>
    			<td>
                	<div align="center">
                		<a href="scrp_delete_collection_doc.php?id=<?php echo doCleanOutput($post_row["CollectionID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" /></a> 
                	</div>
                </td>
    		</tr>
    		<?php }} // end generate rows?>
    	</table>
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