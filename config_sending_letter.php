<?php
include "db_connect.php";
include "session_handler.php";

$issuccess = is_null($_GET["issuccess"]) ? "" : $_GET["issuccess"];
$style = "style='display:none'";

if($issuccess == true){
	$style = "style='color:#006600; padding:5px 0 0 0; font-weight: bold;'";
}

function displayFormatDate($strDate){
	$arr = str_split($strDate,2);
	$strReturn = "";
	$month = $arr[0];
	switch ($month){
		case "01":
			$strReturn = $arr[1]." มกราคม";
			break;
		case "02":
			$strReturn = $arr[1]." กุมภาพันธ์";
			break;
		case "03":
			$strReturn = $arr[1]." มีนาคม";
			break;
		case "04":
			$strReturn = $arr[1]." เมษายน";
			break;
		case "05":
			$strReturn = $arr[1]." พฤษภาคม";
			break;
		case "06":
			$strReturn = $arr[1]." มิถุนายน";
			break;
		case "07":
			$strReturn = $arr[1]." กรกฏาคม";
			break;
		case "08":
			$strReturn = $arr[1]." สิงหาคม";
			break;
		case "09":
			$strReturn = $arr[1]." กันยายน";
			break;
		case "10":
			$strReturn = $arr[1]." ตุลาคม";
			break;
		case "11":
			$strReturn = $arr[1]." พฤศจิกายน";
			break;
		case "12":
			$strReturn = $arr[1]." ธันวาคม";
			break;
	}
		
	return $strReturn;
}


$get_schedule = mysql_query("select * from schedulecollection order by SID desc limit 1");
$post_row = mysql_fetch_array($get_schedule);
$cur_year = date("Y");

$beginyear = "";
$endyear = "";

if($post_row != null){
	$beginyear = $post_row["BeginYear"];
	$endyear = is_null($post_row["EndYear"])? "" :$post_row["EndYear"];
}
$_POST["beginyear"] = $beginyear;
$_POST["endyear"] = $endyear;

?>


<?php include "header_html.php";?>
				<td valign="top" style="padding-left:5px;"><!-- Start Content Block -->
					<h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
						จัดการช่วงเวลาส่งจดหมายเตือนสถานประกอบการ
					</h2>
					
					<div>
						<hr />
						<strong>จัดการช่วงเวลาส่งจดหมายเตือนสถานประกอบการ</strong>
					</div>

					<form action="scrp_add_schedule_collection.php" method="post">
						<span id="displaymsg" <?php echo ($style);?>>* บันทึกข้อมูลสำเร็จ</span>
					
						<div style="padding-top: 10px;"><!-- รายการ config -->
							<table width="50%" border="0">
								<tr>
								<td>ตั้งแต่ปี </td>
                            		<td><?php $selector_name ="beginyear"; include "ddl_year_withid.php";?> *</td>
								<td>ถึงปี </td>
                            		<td><?php $selector_name ="endyear"; include "ddl_year_withid.php";?></td>                            		
                            	</tr>
                            <td><label>
							</table>
							<table border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; background-color: #fff; " id="configSendingLetterList" >
								<tr bgcolor="#9C9A9C" align="center" >
									<td width="50px">
										<div align="center"><span class="column_header">ครั้งที่</span> </div>
									</td>
									<td width="200px">
										<div align="center"><span class="column_header">วันที่</span> </div>
									</td>
								</tr>
								<?php
									// generate row
									
								
									for ($i = 1; $i < 5;$i++){
								?>
										<tr>
											<td>
												<div align="center"><?php echo $i; ?></div>
											</td>
											<td>
												<div>
													<?php 
														if($post_row != null){
															$date = $post_row["SentNo$i"];
															if($date != null){
																$split_date = str_split($date,2);
																$this_date_time = $cur_year."-".$split_date[0]."-".$split_date[1];
															}else {
																$this_date_time = "00-00";
															}
														}else {
															$this_date_time = "00-00";
														}
	
														if($this_date_time != "" && $this_date_time != "00-00"){
															$this_selected_month = date("m", strtotime($this_date_time));
															$this_selected_day = date("d", strtotime($this_date_time));
														}else{
															$this_selected_month = 0;
															$this_selected_day = 0;
														}
													?>
													
													<!-- select day -->
													<select name="day_<?php echo $i?>" size="1" class="inputf" id="day_<?php echo $i;?>" style="width:auto;" >
	                                                  <option selected value="00">...</option>
	                                                  	<?php
			                                                $out = "";
			                                                for ($j=1; $j<=31; $j++) {
			                                                    $cur_j = sprintf("%02d", $j);
			                                                    $selected =  ($cur_j == $this_selected_day) ? " SELECTED" : "";
			                                                    echo '<option  value="'.$cur_j.'" '.$selected.'>'.$cur_j.'</option>';
			                                                }
			                                                echo $out;
		                                                ?>
	                                                </select>
	                                                <!-- end select day -->
	                                                
	                                                <!-- select month -->
	                                                <select name="month_<?php echo $i;?>" size="1" class="inputf" id="month_<?php echo $i;?>" style="width:auto;" >
		                                                <option selected value="00">...</option>
		                                                <option value="01" <?php if($this_selected_month=="01"){echo "selected='selected'";}?>><?php echo "มกราคม"?></option>
		                                                <option value="02" <?php if($this_selected_month=="02"){echo "selected='selected'";}?>><?php echo "กุมภาพันธ์"?></option>
		                                                <option value="03" <?php if($this_selected_month=="03"){echo "selected='selected'";}?>><?php echo "มีนาคม"?></option>
		                                                <option value="04" <?php if($this_selected_month=="04"){echo "selected='selected'";}?>><?php echo "เมษายน"?></option>
		                                                <option value="05" <?php if($this_selected_month=="05"){echo "selected='selected'";}?>><?php echo "พฤษภาคม"?></option>
		                                                <option value="06" <?php if($this_selected_month=="06"){echo "selected='selected'";}?>><?php echo "มิถุนายน"?></option>
		                                                <option value="07" <?php if($this_selected_month=="07"){echo "selected='selected'";}?>><?php echo "กรกฎาคม"?></option>
		                                                <option value="08" <?php if($this_selected_month=="08"){echo "selected='selected'";}?>><?php echo "สิงหาคม"?></option>
		                                                <option value="09" <?php if($this_selected_month=="09"){echo "selected='selected'";}?>><?php echo "กันยายน"?></option>
		                                                <option value="10" <?php if($this_selected_month=="10"){echo "selected='selected'";}?>><?php echo "ตุลาคม"?></option>
		                                                <option value="11" <?php if($this_selected_month=="11"){echo "selected='selected'";}?>><?php echo "พฤศจิกายน"?></option>
		                                                <option value="12" <?php if($this_selected_month=="12"){echo "selected='selected'";}?>><?php echo "ธันวาคม"?></option>
	                                              	</select>
                                                	<!-- end select month -->
												</div>
											</td>
										</tr>
								<?php } //end for loop ?>
							</table>
						</div><!-- รายการ config -->	
						
						<div style="padding-top:10px;">
							<input type="submit" value="บันทึกข้อมูล" onclick="return validateConfigSendingLetterForm();" />
						</div>
					</form>
				</td><!-- End Content Block -->
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
<script type="text/javascript">
	function validateConfigSendingLetterForm() {
		var isValid = true;

		var beginyear = $("#beginyear").val();
		if(beginyear == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ตั้งแต่ปี");

			$("#beginyear").focus();
		}

		if(isValid){
			for(i = 1;i < 5;i++){
				var dSelect = "#day_"+i;
				var mSelect = "#month_"+i;
				var day = $(dSelect).val();
				var month = $(mSelect).val();

				if(i == 1){
					if((day == "00") || (month == "00"))
					{
						isValid = false;
						alert("กรุณาใส่ข้อมูล ครั้งที่ 1");
						if(day == "00"){
							$(dSelect).focus();
						}else{
							$(mSelect).focus();
						}
						break;
					}
				}else{
					if(day != "00" && month == "00")
					{
						isValid = false;
						alert("กรุณาใส่ข้อมูล: วันที่");
						$(mSelect).focus();
						break;
					}else if (day == "00" && month != "00") {
						isValid = false;
						alert("กรุณาใส่ข้อมูล: วันที่");
						$(dSelect).focus();
						break;
					}
				}
			}
		}
			
		
		
		return isValid;				
	}
</script>