<?php

// yoes 20190103
//check if have payment ...

//echo "last_payment_date: ".$last_payment_date;
if($sess_accesslevel == 4){
	$es_table_name_suffix = "_company";
	$es_field_name_suffix = "-es";
}


//$this_lawful_status = $lawful_row["LawfulStatus"];
if(!$ajax_script_34_33_2020 && $sess_accesslevel != 4){
	
	$ajax_script_34_33_2020 = "
		
	<script>

		function doAssign3433_2020(leid, leid_to, the_lid){
			

			var rid = $('#'+leid+leid_to+'_34_2020_rid').val();
			
			var rid_amount = $('#'+leid+leid_to+'_34_2020_rid-amount').val();
			
			if(rid == '---'){
				
				return false;
				
			}

			$.ajax({ url: './ajax_update_34_to_33_2020.php',
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
						//updateListTable(leid);
						
					}
				}
			});


		}
		
		
		
		function populate_amount_2020(what){
			
			//alert(what);
			var rid_amount;
			rid_amount = $('#'+what+'_34_2020_rid').find('option:selected').attr('rid_amount');
			
			 $('#'+what+'_34_2020_rid-amount').val(rid_amount);
		}
		
		
	</script>

	
	
	

	";
	
	echo $ajax_script_34_33_2020;
	
	
	//yoes 20200817 -- add modals
	?>
	
	<div id="lawful_34_to_33_modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="vcenter">น.ส.ภัทรวดี ยานา </h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<h4>
						รวมต้องชำระ 
							<font color=blue>
							{{p_pending_amount+p_pending_interests}}
							</font>
						บาท
						
						<br> จ่ายเงินแล้วโดย:
						
						
						<select id="<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_202ss0_rid" onChange="('<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>');" >
							<option value="xxx">
								-- ไม่มีการจ่ายเงิน --
							</option>
							
							
							
							
									<option 
										value="<?php echo $payment_list_row[RID]?>" <?php if($payment_list_row[RID] == $deduct_33[latest_payment_rid]){ echo "selected";}?> 
										rid_amount='<?php echo $payment_list_row[Amount]; ?>'
									>
										ใบเสร็จเล่มที่ <?php echo $payment_list_row[BookReceiptNo]?> เลขที่ <?php echo $payment_list_row[ReceiptNo]?> (<?php echo number_format($payment_list_row[Amount],2);?> บาท)
									</option>
							
							
						
						</select>
						
						<br> 
						
						จำนวน <input  value='' style='width: 100px;' id="<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_202sss0_rid-amount" type="number" step="0.01"/> บาท
						
					</h4>
					<p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<script>
	
		var lawful_34_to_33_modal = new Vue({
			  el: '#lawful_34_to_33_modal',
			  data: {
				p_pending_amount: 0
				, p_pending_interests: 0				
				
			  }
			})
			
			
		function get_lawful_34_to_33_modal(p_from, p_to){
						
			axios
				.post('ajax_get_lawful_33_principals.php', "p_from=" + p_from+"&p_to="+p_to)//{ step: ""+what+"", the_id: ""+id+""})
				.then(response => {

					var mm;		
					mm = response.data[0];
					console.log(mm);										
					//alert(mm.p_pending_amount);					
					lawful_34_to_33_modal["p_pending_amount"] = Number(mm.p_pending_amount);
					lawful_34_to_33_modal["p_pending_interests"] = Number(mm.p_pending_interests);
					
					
				})
			
		}
		
		//get_lawful_34_to_33_modal(0, 346137);
	
	</script>
	
	
<?php
	
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



if($show_new_law_payment_details){
	
																	
		
	?>
	
		<br>
		
		<a href="#" onClick='$( "#<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_2020_span" ).toggle(); return false;' style="font-weight: normal; font-size: 11px;"><u>ปรับปรุงข้อมูลการจ่ายเงิน</u></a>
		
		<?php if(1==0){?>
		<br>
		<img src="icon_report2.jpg"/>
		<a href="#" alt="default" data-toggle="modal" data-target="#lawful_34_to_33_modal" class="model_img img-fluid" style="font-weight: normal; font-size: 11px; "
		
			onClick="get_lawful_34_to_33_modal(<?php echo $principal_row[p_from];?>, <?php echo $principal_row[p_to];?>);";
		
		>ปรับปรุงข้อมูลการจ่ายเงิน (หน้าจอแบบใหม่)</a>
		<?php }?>
		
		
		
		<span id="<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_2020_span" style="display:none;">
		
		
		
		
		
		<br>จ่ายเงินแล้วโดย: 
			
			<select id="<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>_34_2020_rid" onChange="populate_amount_2020('<?php echo $principal_row[p_from];?><?php echo $principal_row[p_to];?>');" >
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
			
			
			<input type="button" value="เพิ่มข้อมูลการจ่ายเงิน" onClick="doAssign3433_2020(<?php echo $principal_row[p_from];?>, <?php echo $principal_row[p_to];?>, <?php echo $this_lid;?>);"/>
			</span>
			
			
	
	<?php
		
	

}

?>