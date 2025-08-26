<?php

// yoes 20190103
//check if have payment ...

//echo "last_payment_date: ".$last_payment_date;
if($sess_accesslevel == 4){
	$es_table_name_suffix = "_company";
	$es_field_name_suffix = "-es";
}


//$this_lawful_status = $lawful_row["LawfulStatus"];
if(!$ajax_script_34_35_2020 && $sess_accesslevel != 4){
	
	$ajax_script_34_35_2020 = "
		
	<script>

		function doAssign3435_2020(leid, leid_to, the_lid){
			

			var rid = $('#'+leid+leid_to+'_34_2020_rid').val();
			
			var rid_amount = $('#'+leid+leid_to+'_34_2020_rid-amount').val();
			
			if(rid == '---'){
				
				return false;
				
			}

			$.ajax({ url: './ajax_update_34_to_35_2020.php',
				data: {leid: leid, rid: rid, rid_amount: rid_amount, leid_to: leid_to,  the_lid: the_lid},
				type: 'post',
				success: function(output) {
					
				    var obj = jQuery.parseJSON(output);
					//alert(obj.bedug_code);
					
					
					if(obj.resp_code == 500){
						
						alert('มีการระบุจำนวนเงินเกินจำนวนใบเสร็จ: \\nยอดเงินในใบเสร็จ '+obj.receipt_amount+' บาท: \\nใช้ไปแล้ว '+obj.receipt_used_amount+'บาท \\nเหลือใช้ได้ไม่เกิน '+obj.rid_balance+' บาท');
						// (ใช้เกิน '+obj.excess_amount+' บาท)' \\n ต้องการใช้อีก '+obj.rid_amount+' บาท 
						
					}else if(obj.resp_code == 501){
						
						alert('not all parameters are sent');
						
					}else{
						
						$( '#lawful_form' ).submit();
						
					}
				}
			});


		}
		
		
		
		function populate_amount35_2020(what){
			
			//alert(what);
			var rid_amount;
			rid_amount = $('#'+what+'_34_2020_rid').find('option:selected').attr('rid_amount');
			
			 $('#'+what+'_34_2020_rid-amount').val(rid_amount);
		}
		
		
	</script>

	
	
	

	";
	
	echo $ajax_script_34_35_2020;
	
}



// $is_extra_33 comes from organization.php
$is_extra_35 = getFirstItem("
							select 
								meta_value 
							from 
								curator_meta 
							where 
								meta_for = 'is_extra_35$es_field_name_suffix' and meta_curator_id = '".$post_row["curator_id"]."'");	
								
								

if($this_lawful_year >= 2018 && $this_lawful_year < 2500 && !$is_extra_33){
	$show_new_law_payment_details = 1;
	
}else{
	$show_new_law_payment_details = 0;
}



if($show_new_law_payment_details){
	
																	
		
	?>
	
		<br><a href="#" onClick='$( "#<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_2020_span" ).toggle(); return false;' style="font-weight: normal; font-size: 11px;"><u>ปรับปรุงข้อมูลการจ่ายเงิน</u></a>
		
		<span id="<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_2020_span" style="display:none;">
		
		
		
		
		
		<br>จ่ายเงินแล้วโดย: 
			
			<select id="<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_2020_rid" onChange="populate_amount35_2020('<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>');" >
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
			
			
			<?php //echo $payment_list_sql;?>
			
			จำนวน <input type='text' value='' style='width: 100px;' id="<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_2020_rid-amount" /> บาท
			
			
			<input type="button" value="เพิ่มข้อมูลการจ่ายเงิน" onClick="doAssign3435_2020(<?php echo $principal_row[p_from];?>, <?php echo $principal_row[p_to];?>, <?php echo $this_lid;?>);"/>
			</span>
			
			
	
	<?php
		
	

}

?>