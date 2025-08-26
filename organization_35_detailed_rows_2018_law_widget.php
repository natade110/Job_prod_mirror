<?php

//$this_lawful_status = $lawful_row["LawfulStatus"];
//yoes 20190211
if($sess_accesslevel == 4){
	$es_table_name_suffix = "_company";
	$es_field_name_suffix = "-es";
}


if(!$ajax_script_34_35 && $sess_accesslevel != 4){
	
	$ajax_script_34_35 = "
		
	<script>

		function doAssign3435(curator_id){

			
			var rid = $('#'+curator_id+'_3435_rid').val();
			var rid_amount = $('#'+curator_id+'_35_rid-amount').val();
			
			//alert(curator_id);
			//alert($('#'+curator_id+'_3435_rid').val());
			
			/**/
			if(rid == '---'){
				
				return false;
				
			}

			$.ajax({ url: './ajax_update_34_to_35.php',
				data: {curator_id: curator_id, rid: rid, rid_amount: rid_amount},
				type: 'post',
				success: function(output) {
				   //$( '#lawful_form' ).submit();
				   
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
		
		function populate_amount_35(what){
			
			
			var rid_amount;
			rid_amount = $('#'+what+'_3435_rid').find('option:selected').attr('rid_amount');			
			 $('#'+what+'_35_rid-amount').val(rid_amount);
		}
		
	</script>


	";
	
	echo $ajax_script_34_35;
	
}

// $is_extra_33 comes from organization.php
$is_extra_35 = getFirstItem("
							select 
								meta_value 
							from 
								curator_meta 
							where 
								meta_for = 'is_extra_35$es_field_name_suffix' and meta_curator_id = '".$post_row["curator_id"]."'");	
								
								

if($this_lawful_year >= 2018 && $this_lawful_year < 2500 && !$is_extra_35){
	$show_new_law_payment_details = 1;
}else{
	$show_new_law_payment_details = 0;
}

if($this_lawful_year >= 2018 && $show_new_law_payment_details){
															
	if($post_row["curator_start_date"] != '0000-00-00'){

		$the_end_date = $post_row["curator_end_date"];
		if(!$the_end_date || $the_end_date == '0000-00-00') {
			$the_end_date = $this_lawful_year ."-12-31";
		}

		//echo  $the_end_date;
		
		$deduct_35 = get35DeductionByCuratorIdArray($post_row["curator_id"], "", 1);
		
		
		//yoes 20190129
		//just show all related RID
				
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
				b.RID in (
				
					select
						meta_rid
					from
						receipt_meta
					where
						meta_for = '35_for'
						and
						meta_value = '".$post_row["curator_id"]."'
				
				)
		
			order by
				ReceiptDate asc, b.RID asc
		
		";
		
		$payment_list_result = mysql_query($payment_list_sql);
		
		while ($payment_list_row = mysql_fetch_array($payment_list_result)) {
	
			echo "<br><font color=blue>มีจ่ายเงินวันที่ ".formatDateThai($payment_list_row[ReceiptDate])
			
				." เล่มที่ ".$payment_list_row[BookReceiptNo]." ใบเสร็จเลขที่ ".$payment_list_row[ReceiptNo]." </font>";
	
		}
		
			
		//if(!$row_is_parent){
		/*if($deduct_35[this_m35_date_diff_before_deducted] && $this_lawful_status != 1){
			echo "<br><font color=orangered>(ต้องจ่ายเงินแทน "
				. number_format($deduct_35[this_m35_date_diff_before_deducted],0) ." วัน "
				. number_format($deduct_35[m34_to_pay_before],2) ." บาท ";
		
			if($deduct_35[interest_days_before]){
				echo " + ดอกเบี้ย "
				. $deduct_35[interest_days_before] . " วัน"
				. number_format($deduct_35[interest_amount_before],2) ." บาท";
				
				//echo " ดอกเบี้ยคิดจากวันที่ " . formatDateThai($deduct_35[interest_start_date]) . "";
			}
			
			echo ")</font>";	
		}
		
																		
		if($deduct_35[this_m35_date_diff_before] && $deduct_35[this_m35_date_diff_before]-$deduct_35[this_m35_date_diff_before_deductable_days] == 0){
			echo "<br><font color=blue>(รับแทนใน ". $deduct_35[this_m35_date_diff_before_deductable_days] ." วัน ไม่ต้องจ่าย "
				. number_format($deduct_35[m34_deductable],2) ." บาท ";
		
																				
			echo ")</font>";																	
			
		}
		
		
		echo "<br><font color=green>(แทน ม.34 ได้ "
			. $deduct_35[this_m35_date_no_pay] ." วัน "
			. number_format($deduct_35[m34_no_need_pay],2) ." บาท)</font>";
			
		
		

		if($deduct_35[this_m35_date_diff_after] && $this_lawful_status != 1){
			echo "<br><font color=orangered>(ต้องจ่ายเงินแทน "
				. number_format($deduct_35[this_m35_date_diff_after],0) ." วัน "
				. number_format($deduct_35[m34_to_pay_after],2) ." บาท ";
		
			if($deduct_35[interest_days_after]){
				echo " +ดอกเบี้ย "
				. $deduct_35[interest_days_after] . " วัน "
				. number_format($deduct_35[interest_amount_after],2) ." บาท";
				
				//echo " ดอกเบี้ยคิดจากวันที่ " . formatDateThai($deduct_35[interest_start_date]) . "";
			}
			
			echo ")</font>";	
		}		
		
		*/

		
		
		if($row_is_child){
			
			
			//$parent_row = getFirstRow("select * from curator where curator_id = '".$post_row["child_meta_value"]."'");			
			//echo "<br><font color='blue'>(ทำงานแทน ".$parent_row[le_name]." )</font>";
			
		}
		
		
		if($row_is_parent){
			
			//$parent_row = getFirstRow("select * from lawful_employees where curator_id = '".$post_row["parent_meta_leid"]."'");			
			//echo "<br><font color='#ff00ff'>(ทำงานแทนโดย ".$parent_row[le_name]." )</font>";
			
		}
		
		
		?>
		
		
		<?php if(($deduct_35[m34_to_pay_before] || $deduct_35[m34_to_pay_after]) && $sess_accesslevel != 4){?>
		
			<br><a href="#" onClick='$( "#<?php echo $post_row["curator_id"];?>_3435_span" ).toggle(); return false;' style="font-weight: normal; font-size: 11px;"><u>ปรับปรุงข้อมูลการจ่ายเงิน</u></a>
			
			<span id="<?php echo $post_row["curator_id"];?>_3435_span" style="display:none;">
			<br>จ่ายเงินแล้วโดย: 
				
				<select id="<?php echo $post_row["curator_id"];?>_3435_rid"
				
					onChange="populate_amount_35(<?php echo $post_row["curator_id"];?>);"
				
					>
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
					
							<option value="<?php echo $payment_list_row[RID]?>" 
							
							
								<?php if($payment_list_row[RID] == $deduct_35[latest_payment_rid]){ echo "selected";}?>
								
								rid_amount='<?php echo $payment_list_row[Amount]; ?>'							
							
							>
								ใบเสร็จเล่มที่ <?php echo $payment_list_row[BookReceiptNo]?> เลขที่ <?php echo $payment_list_row[ReceiptNo]?> (<?php echo number_format($payment_list_row[Amount],2);?> บาท)
							</option>
					
					
						<?php }?>
				
				</select>
				
				จำนวน <input type='text' value='' style='width: 100px;' id="<?php echo $post_row["curator_id"];?>_35_rid-amount" /> บาท
				
				
				
				<input type="button" value="เพิ่มข้อมูลการจ่ายเงิน" onClick="doAssign3435(<?php echo $post_row["curator_id"];?>);"/>
				</span>
				
		<?php }?>			
		
		
		
		
		<?php
		
		
		
	}

}

?>