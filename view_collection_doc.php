<?php
require "db_connect.php";
require "session_handler.php";
include "scrp_config.php";

if(is_numeric($_GET["id"])){
	$collection_id = $_GET["id"];
	$post_row = getFirstRow("select c.* , l.Year,f.file_name from collectiondocument c
							inner join lawfulness l on c.LID = l.LID 
							left join files f on c.CollectionID = f.file_for
							where c.CollectionID  = '$collection_id' limit 0,1");
	
	$output_fields = array('CollectionID','Year','RequestDate','RequestNo','GovDocumentNo','DocumentDetail','file_name','Reciever','RecievedDate','CreatedBy');
	
	for($i = 0; $i < count($output_fields); $i++){
		$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
	}
	
	$condition_sql = "c.GovDocumentNo = '".$output_values["GovDocumentNo"]."' 
					and c.RequestNo = ".$output_values["RequestNo"]." 
					and c.RequestDate = '".$output_values["RequestDate"]." 00:00:00' and l.Year = '".$output_values["Year"]."'";
	
}else{
	header("location: index.php");
}

?>

<?php include "header_html.php";?>
<script type="text/javascript" src="/kendo/kendo.all.min.js"></script>
<script type="text/javascript" src="/kendo/kendo.culture.th-TH.min.js"></script>
<script type="text/javascript" src="/kendo/kendo.calendar.custom.js"></script>
<script type="text/javascript">
	kendo.culture("th-TH");
</script>

		<td valign="top">
			<h2 class="default_h1" style="margin:0; padding:0 0 0px 0;">
				จดหมายทวงถาม ครั้งที่: <font color="#006699"><?php echo $output_values["RequestNo"];?></font></font> หนังสือเลขที่: <font color="#006699"><?php echo $output_values["GovDocumentNo"];?></font>
			</h2>
			
			<div style="padding:5px 0 10px 2px">
				<a href="collection_doc_list.php">จดหมายทวงถามทั้งหมด</a> > หนังสือเลขที่: <?php echo $output_values["GovDocumentNo"];?>
			</div>
			
			<form action="scrp_update_collection_doc.php" method="post" onsubmit="return validateCollectionDoc(this);" enctype="multipart/form-data">
				<input name="CollectionID" type="hidden" value="<?php echo ($output_values["CollectionID"]);?>"/>
				<table style="padding:10px 0 0px 0;" bgcolor="#ffffff">
					<tr>
                    	<td bgcolor="#efefef"> ประจำปี: </td>
                    	<td colspan="5"><?php include "ddl_year.php";?></td>
                   	</tr>
                   	<tr>
                   		<td bgcolor="#efefef"> วันที่: </td>
                   		<td>
							<input id="RequestDate" name="RequestDate" class="nep-datepicker" value="<?php echo formatDateThai($output_values["RequestDate"]);?>" />  
						</td>
						<td bgcolor="#efefef"> ครั้งที่: </td>
						<td>
							<input name="RequestNo" type="text" id="RequestNo" value="<?php echo ($output_values["RequestNo"]);?>" />
						</td>
						<td bgcolor="#efefef"> หนังสือเลขที่: </td>
						<td>
							<input name="GovDocumentNo" type="text" id="GovDocumentNo" value="<?php echo ($output_values["GovDocumentNo"]);?>" />
						</td>
                   	</tr>
                   	<tr>
	               		<td bgcolor="#efefef">ผู้ดำเนินการ:</td>
	               		<td colspan="5">
	               			<input name="CreatedBy" type="text" id="CreatedBy" value="<?php echo $output_values["CreatedBy"];?>" />
	               		</td>
               		</tr>
                   	<tr>
                   		<td bgcolor="#efefef" valign="top"> แนบเอกสาร:</td>
                   		<td colspan="5">
                   			<span style="padding: 10px 0 10px 0;">
                   				<div style="width:400px; padding-bottom:5px;">
		                   			<?php 
		                   				$this_id = $output_values["CollectionID"];
		                   				$file_type = "collector_doc";
		                   				include "doc_file_links.php";
		                   			?>
		                   		</div>
                   			</span>
                   			
                   			<div class="input-file-container single-file">
                       			<input id="collector_doc" name="collector_doc" type="file" />
                       		</div>
                   		</td>
                   	</tr>
                   	<tr>
                       	<td bgcolor="#efefef" valign="top"> รายละเอียด:</td>
                       	<td colspan="5">
                       		<textarea name="DocumentDetail" cols="50" rows="5" id="DocumentDetail"><?php echo $output_values["DocumentDetail"];?></textarea>
                       	</td>
                     </tr>
                     <tr>
                     	<td bgcolor="#efefef"> วันที่รับ:</td>
                     	<td>
                     		<input id="RecievedDate" name="RecievedDate" class="nep-datepicker" value="<?php echo formatDateThai($output_values["RecievedDate"]);?>" />
                     	</td>
                     </tr>
                     <tr>
                     	<td bgcolor="#efefef"> ผู้รับ:</td>
                     	<td>
                     		<input name="Reciever" type="text" id="Reciever" value="<?php echo ($output_values["Reciever"]);?>" />
                     	</td>
                     </tr>
                     <tr>
                     	<td colspan="6" >
                     		<div align="right">
							<?php if($sess_accesslevel != 5){?>
                        		<input type="submit" value="อัพเดทข้อมูล" />
                        	<?php }?>
                         	</div>
						</td>
					</tr>
				</table>
			</form>
			
			<form method="post">
				<!-- management page -->
			 	<?php
			 		$the_sql = "select count(com.CID) from collectiondocument c 
										inner join lawfulness l on c.LID = l.LID 
										left join company com on l.CID = com.CID
										where $condition_sql";
			 		
			 		$record_count_all = getFirstItem($the_sql);
			 	
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
				
				<h2 class="default_h1" style="margin:0; padding:0 0 10px 0;">สถานประกอบการในจดหมายทวงถาม</h2>
				
				<table border="0" width="100%">
					<tr>
						<td align="left">
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
				</table>
				
				<div style="padding:10px 0 10px 0" >
					<a href="javascript:void()" onclick="exportExcel(<?php echo $this_id;?>)" >+ export ข้อมูลเป็น excel</a> |
					<a href="javascript:void()" onclick="printPdf(<?php echo $this_id;?>)" >พิมพ์เอกสาร</a>
				</div>
				
				<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse;">
					<tr bgcolor="#9C9A9C" align="center" >  
						<td >
							<input name="chk_all" id="chk_all" type="checkbox" value="1" onclick="checkOrUncheck();" />
						</td>
						<td><div align="center"><span class="column_header">ลำดับ</span></div></td>
						<td><div align="center"><span class="column_header">รหัส</span></div></td>
						<td><div align="center"><span class="column_header">ชื่อ</span></div></td>
						<td><div align="center"><span class="column_header">สถานะ</span></div></td>
						<?php if($sess_accesslevel != 5){?>
							<td><div align="center"><span class="column_header">ลบข้อมูล</span></div></td>
						<?php }?>
					</tr>
					<?php 
						$total_records = 0;
						$the_limit = "limit $starting_index, $per_page";
						
						$get_collecion_sql = "select c.* ,l.Year,l.LID, l.CID,l.LawfulStatus as lawfulness_status ,com.CompanyCode,com.CompanyNameThai,com.CompanyTypeCode from collectiondocument c 
										inner join lawfulness l on c.LID = l.LID 
										left join company com on l.CID = com.CID
										where $condition_sql
										order by com.CompanyNameThai asc $the_limit";
						
						$collection_result = mysql_query($get_collecion_sql);
						// generate rows
						//$total_records = $starting_index;
						while ($post_row = mysql_fetch_array($collection_result)) {
							$total_records++;
					?>
					
					<tr bgcolor="#ffffff" align="center" >
					<?php 
						$js_do_check .= "document.getElementById('chk_$total_records').checked = true;";
						$js_do_uncheck .= "document.getElementById('chk_$total_records').checked = false;";
					?>
						<td>
							<input class="chk-collection" name="chk_<?php echo $total_records; ?>" id="chk_<?php echo $total_records; ?>" type="checkbox" value="<?php echo doCleanOutput($post_row["CID"]);?>" />
						</td>
						<td><?php echo $total_records;?> </td>
						<td><?php echo doCleanOutput($post_row["CompanyCode"]);?></td>
						<td><?php echo doCleanOutput(formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]));?></td>
						<td>
							<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"]));?></div>
						</td>
						<td>
							<div align="center">
								<a href="scrp_delete_collection_doc.php?id=<?php echo doCleanOutput($post_row["CollectionID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a>
							</div>
						</td>
					</tr>
					<?php } // end generate rows?>
				</table>
				
				<input name="total_records" id="total_records" type="hidden" value="<?php echo $total_records; ?>" />
			</form>
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
<script id='uploadFileTemplate' type='text/x-kendo-template'>
    <span class='k-progress'></span>
    <div class='file-wrapper'>
           <button type='button' class='k-upload-action'></button>   
           <span class='file-name'>#=name#</span>
           <a href='##' target='_blank' class='file-link' >#=name#</a>
    </div>
</script>

<script type="text/javascript">
	$(function () {
		 createDatePicker();
		 createUpload();
	});

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

		//searchOrgList();
	}
	
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

	function validateCollectionDoc(frm){
		var isValid = true;

		if(frm.RequestNo.value.length == 0){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ครั้งที่");
			frm.RequestNo.focus();
		}

		if(frm.GovDocumentNo.value.length == 0){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: หนังสือเลขที่");
			frm.GovDocumentNo.focus();
			
		}

		return isValid;
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

	
	function exportExcel(id){
		var url = "export_excel_collection_doc.php?id="+ id;
		var cids = getCIDSelected();
		if(cids != ""){
			url += "&cid=" + cids; 
			window.open(url,'_blank');
		}else{
			alert("กรุณาเลือกสถานประกอบการ");
		}	
	}

	function printPdf(id){
		var url = "generate_pdf_collection_doc.php?id="+ id;
		var cids = getCIDSelected();
		if(cids != ""){
			url += "&cid=" + cids; 
			window.open(url,'_blank');
		}else{
			alert("กรุณาเลือกสถานประกอบการ");
		}
	}

	function getCIDSelected(){
		
		var ids = [];
		$(".chk-collection").each(function(){
			if(this.checked){
				ids.push($(this).val());
			}
			
		});	
		return ids.join("_");
	}
</script>
<!-- end section script -->

</body>
</html>
<!-- end footer section -->