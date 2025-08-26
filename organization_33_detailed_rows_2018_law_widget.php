<?php

// yoes 20190103
//check if have payment ...

//echo "last_payment_date: ".$last_payment_date;
if($sess_accesslevel == 4){
	$es_table_name_suffix = "_company";
	$es_field_name_suffix = "-es";
}


//$this_lawful_status = $lawful_row["LawfulStatus"];
if(!$ajax_script_34_33 && $sess_accesslevel != 4){
	
	$ajax_script_34_33 = "
		
	<script>

		function doAssign3433(leid){

			//alert(le_id);
			//alert($('#'+le_id+'_34_rid').val());
			
			var rid = $('#'+leid+'_34_rid').val();
			
			var rid_amount = $('#'+leid+'_34_rid-amount').val();
			
			if(rid == '---'){
				
				return false;
				
			}

			$.ajax({ url: './ajax_update_34_to_33.php',
				data: {leid: leid, rid: rid, rid_amount: rid_amount},
				type: 'post',
				success: function(output) {
					
				    var obj = jQuery.parseJSON(output);
					//alert(obj.bedug_code);
					
					
					if(obj.resp_code == 500){
						
						alert('มีการระบุจำนวนเงินเกินจำนวนใบเสร็จ: \\nยอดเงินในใบเสร็จ '+obj.receipt_amount+' บาท: \\nใช้ไปแล้ว '+obj.receipt_used_amount+'บาท \\nเหลือใช้ได้ไม่เกิน '+obj.rid_balance+' บาท');
						// (ใช้เกิน '+obj.excess_amount+' บาท)' \\n ต้องการใช้อีก '+obj.rid_amount+' บาท 
						
					}else{
						
						$( '#lawful_form' ).submit();
						
					}
				}
			});


		}
		
		
		
		function populate_amount(what){
			
			//alert(what);
			var rid_amount;
			rid_amount = $('#'+what+'_34_rid').find('option:selected').attr('rid_amount');
			//alert(rid_amount);
			 $('#'+what+'_34_rid-amount').val(rid_amount);
		}
		
		
	</script>

	
	
	

	";
	
	echo $ajax_script_34_33;
	
}



// $is_extra_33 comes from organization.php
$is_extra_33 = getFirstItem("
							select 
								meta_value 
							from 
								lawful_employees_meta 
							where 
								meta_for = 'is_extra_33$es_field_name_suffix' and meta_leid = '".$post_row["le_id"]."'");	
								
								

if($this_lawful_year >= 2018 && $this_lawful_year < 2500 && !$is_extra_33){
	$show_new_law_payment_details = 1;
	
}elseif(

	$post_row["le_cid"] == 52427
	&&
	$post_row["le_year"] == 2017
	
	){
	
	//print_r($post_row);	
	$show_new_law_payment_details = 1;
	
}else{
	$show_new_law_payment_details = 0;
}

/*
if($lawful_row["LawfulStatus"] == 1 && $last_payment_date){	
	$show_new_law_payment_details = 1;
}
*/

if($show_new_law_payment_details){
	
	
															
	if($post_row["le_start_date"] != '0000-00-00'){

		$the_end_date = $post_row["le_end_date"];
		if(!$the_end_date || $the_end_date == '0000-00-00') {
			$the_end_date = $this_lawful_year ."-12-31";
		}

		//echo  $the_end_date;
		
		//$day_deducted = dateDiffTs(strtotime($post_row["le_start_date"]),strtotime($the_end_date),1);																
		//$total_day_deducted += $day_deducted;

		
		$deduct_33 = get33DeductionByLeidArray($post_row["le_id"],"",1);
		
		//yoes 20190125
		/*if($deduct_33[latest_payment_rid]){
			
			$deduct_receipt_row = getFirstRow("select ReceiptNo, BookReceiptNo from receipt where rid = '".$deduct_33[latest_payment_rid]."'");
			
			echo "<br><font color=blue>มีจ่ายเงินวันที่ ".formatDateThai($deduct_33[latest_payment_date])
			
				." เล่มที่ ".$deduct_receipt_row[BookReceiptNo]." ใบเสร็จเลขที่ ".$deduct_receipt_row[ReceiptNo]." </font>";
				
		}*/
		
		//yoes 20190129
		//just show all related RID
				
		//query RID for this LID
		/*$payment_list_sql = "
			
			select
				*
			from
				payment a
					join receipt b
						on
						a.RID = b.RID
			where
				b.RID in (
				
					select
						meta_rid
					from
						receipt_meta
					where
						meta_for = '33_for'
						and
						meta_value = '".$post_row["le_id"]."'
				
				)
		
			order by
				ReceiptDate asc, b.RID asc
		
		";
		
		$payment_list_result = mysql_query($payment_list_sql);
		
		while ($payment_list_row = mysql_fetch_array($payment_list_result)) {
	
				//yoes 20190220 --> move all this to functions_new_law*
				
				echo "<br><font color=blue>มีจ่ายเงินวันที่ ".formatDateThai($payment_list_row[ReceiptDate])
			
				." เล่มที่ ".$payment_list_row[BookReceiptNo]." ใบเสร็จเลขที่ ".$payment_list_row[ReceiptNo]." จำนวนเงิน ".number_format($payment_list_row[Amount],2)." บาท </font>";
				
	
		}
		
		
		*/
			
		//if(!$row_is_parent){
			
			
		if($deduct_33[this_m33_date_diff_before_deducted] ){
			
			//yoes 20190220 --> move all this to functions_new_law*
			/*
			echo "<br><font color=orangered>(ต้องจ่ายเงินแทนก่อนการรับเข้าทำงาน "
				. number_format($deduct_33[this_m33_date_diff_before_deducted],0) ." วัน "
				. number_format($deduct_33[m34_to_pay_before],2) ." บาท ";
		
			if($deduct_33[interest_days_before]){
				echo " + ดอกเบี้ย "
				. $deduct_33[interest_days_before] . " วัน"
				. number_format($deduct_33[interest_amount_before],2) ." บาท";
				
				echo " ดอกเบี้ยคิดจากวันที่ " . formatDateThai($deduct_33[interest_start_date]) . "";
				echo " ถึงวันที่ " . formatDateThai($deduct_33[interest_end_date]) . "";
			}
			
			echo ")</font>";	
			*/
		}
		
																		
		//echo "$deduct_33[this_m33_date_diff_before] && $deduct_33[this_m33_date_diff_before]-$deduct_33[this_m33_date_diff_before_deductable_days] == 0";																
																		
		//yoes 20190220 --> move all this to functions_new_law*
		/*
		if($deduct_33[this_m33_date_diff_before] && $deduct_33[this_m33_date_diff_before]-$deduct_33[this_m33_date_diff_before_deductable_days] == 0){
			
			echo "<br><font color=blue>(รับแทนใน ". $deduct_33[this_m33_date_diff_before_deductable_days] ." วัน ไม่ต้องจ่าย "
				. number_format($deduct_33[m34_deductable],2) ." บาท ";			
			echo ")</font>";																	
			
		}
		*/
		
		
		/*	
			echo "<br><font color=green>(แทน ม.34 ได้ "
			. $deduct_33[this_m33_date_no_pay] ." วัน "
			. number_format($deduct_33[m34_no_need_pay],2) ." บาท)</font>";
			*/
			
		

		/*
		if($deduct_33[this_m33_date_diff_after] ){
			echo "<br><font color=orangered>(ต้องจ่ายเงินแทนส่วนที่เหลือหลังจากออกงาน "
				. number_format($deduct_33[this_m33_date_diff_after],0) ." วัน "
				. number_format($deduct_33[m34_to_pay_after],2) ." บาท ";
		
			if($deduct_33[interest_days_after]){
				echo " +ดอกเบี้ย "
				. $deduct_33[interest_days_after] . " วัน "
				. number_format($deduct_33[interest_amount_after],2) ." บาท";
				
				echo " ดอกเบี้ยคิดจากวันที่ " . formatDateThai($deduct_33[interest_start_date_after]) . "";
				echo " ถึงวันที่ " . formatDateThai($deduct_33[interest_end_date_after]) . "";
			}
			
			echo ")</font>";	
		}
		*/

		
		//yoes 20190218
		/*
		if($deduct_33[m34_total_paid] ){
					
			echo "<br>จ่ายเงินแล้ว " . number_format($deduct_33[m34_total_paid],2) . " บาท";			
			
		}
		
		if($deduct_33[m34_to_pay_pending]  ){ //|| 1==1
		
			echo "<br>คงเหลือ " . number_format($deduct_33[m34_to_pay_pending],2) . " บาท";
			
		}
		*/
		

		/*
		$total_33_array = get33AmountToPayByLeid($post_row["le_id"]);
		
		if($total_33_array[total_amount] != 0){
			
			
			if($total_33_array[total_interest]){
				$interest_word = "+ ดอกเบี้ย";
			}else{
				$interest_word = "";
			}
			
					
			echo "<br><font color=red>(รวมต้องจ่ายเงินต้น $interest_word = <b>".number_format($total_33_array[total_amount]+$total_33_array[total_interest],2). "</b> บาท)</font>";
			
			
		}elseif($total_33_array[total_amount] == 0){
			
			echo "<br><font color=green>ปฏิบัติทดแทนครบแล้ว</font>";
			
		}
		*/
		
		
		
		
		
		/*
		if($row_is_child){
			
			
			$parent_row = getFirstRow("select * from lawful_employees$es_table_name_suffix where le_id = '".$post_row["child_meta_value"]."'");
			
			echo "<br><font color='blue'>(ทำงานแทน ".$parent_row[le_name]." )</font>";
			
		}
		
		
		
		if($row_is_parent){
			
			$parent_row = getFirstRow("select * from lawful_employees$es_table_name_suffix where le_id = '".$post_row["parent_meta_leid"]."'");
			
			echo "<br><font color='#ff00ff'>(ทำงานแทนโดย ".$parent_row[le_name]." )</font>";
			
		}
		*/
		
		
		//yoes 20190129
		//select bills here
		
		?>
		
		<?php if(
		
				($deduct_33[m34_to_pay_before] || $deduct_33[m34_to_pay_after] || 1 == 1) //yoes 20190318 -- allow to fix payment info anyway				
			
				&& $sess_accesslevel != 4){ 
				?>
		
			<br><a href="#" onClick='$( "#<?php echo $post_row["le_id"];?>_34_span" ).toggle(); return false;' style="font-weight: normal; font-size: 11px;"><u>ปรับปรุงข้อมูลการจ่ายเงิน</u></a>
			
			<span id="<?php echo $post_row["le_id"];?>_34_span" style="display:none;">
			
			
			
			
			
			<br>จ่ายเงินแล้วโดย: 
				
				<select id="<?php echo $post_row["le_id"];?>_34_rid" onChange="populate_amount(<?php echo $post_row["le_id"];?>);" >
					<option value="xxx">
						-- ไม่มีการจ่ายเงิน --
					</option>
					
					
					<?php 
					
						//query RID for this LID
						$payment_list_sql = "
							
							select
								*
							from
								payment a
									join receipt b
										on
										a.RID = b.RID
							where
								a.LID = '$this_lid'
						
						
						";
						
						$payment_list_result = mysql_query($payment_list_sql);
						
						while ($payment_list_row = mysql_fetch_array($payment_list_result)) {
					
					?>
					
							<option 
								value="<?php echo $payment_list_row[RID]?>" <?php if($payment_list_row[RID] == $deduct_33[latest_payment_rid]){ echo "selected";}?> 
								rid_amount='<?php echo $payment_list_row[Amount]; ?>'
							>
								ใบเสร็จเล่มที่ <?php echo $payment_list_row[BookReceiptNo]?> เลขที่ <?php echo $payment_list_row[ReceiptNo]?> (<?php echo number_format($payment_list_row[Amount],2);?> บาท)
							</option>
					
					
						<?php }?>
				
				</select>
				
				
				จำนวน <input type='text' value='' style='width: 100px;' id="<?php echo $post_row["le_id"];?>_34_rid-amount" /> บาท
				
				
				<input type="button" value="เพิ่มข้อมูลการจ่ายเงิน" onClick="doAssign3433(<?php echo $post_row["le_id"];?>);"/>
				</span>
				
		<?php }?>			
		
		<?php
		
	}

}

?>