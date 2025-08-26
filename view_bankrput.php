<table class="table no-wrap user-table mb-0" style="font-size: 12.25px;" >
<thead >
	
	
	<div class="card">
											 
												
													<div class="form-body"  >
												
												  
													  <div class="card-body" style="max-width: 1000px">
															
															
															<?php //include "view_case_general_info.php";?>
															
															
															<?php //include "view_case_after_sue.php";?>
															
															<form  method="post" id="bankrupt_form" enctype="multipart/form-data">
															
															<div class="row">
															  <div class="col-md-12">
																<div class="form-group row">
																  <label class="control-label text-left col-md-3">สถานะ: <font color=red>ล้มละลาย</font></label>
																  

																  
																 
																  
																  
																</div>
															  </div>
															  
															</div>
															
															
															<?php $bankrupt_hire = getFirstRow("select * from bankrupt where reference_id = '$this_id'"); ?>
															
																<div class="row">
																  <div class="col-md-12">
																	<div class="form-group row">
																	  <label class="control-label text-left col-md-3">
																		วันที่พิทักษ์ทรัพย์เด็ดขาด:
																		</label>
																	  <div class="col-md-3">
																			<?php echo formatDateThai($bankrupt_hire["bankrupt_date"]); ?>
																			
																	  </div>
																	  
																	  <label class="control-label text-left col-md-3">
																		ระยะเวลายื่นคำขอรับชำระหนี้:
																		</label>
																	  <div class="col-md-3">
																			<?php echo $bankrupt_hire["bankrupt_postpone_days"]; ?> วัน
																	  </div>
																	  
																	  
																	  
																	  
																	</div>
																  </div>
																  
																</div>
																  
																  
																  
																  
																  <div class="row">
																	  <div class="col-md-12">
																		<div class="form-group row">
																		
																		  <label class="control-label text-left col-md-3">
																			วันตรวจนัดขอรับชำระหนี้:
																			</label>
																		  <div class="col-md-3">
																		  
																		  <?php echo formatDateThai($bankrupt_hire["bankrupt_next_date"]); ?>
																		  
																		  </div>
																		 
																		  
																		</div>
																	  </div>
																	  
																	</div>
																	
																	
																	
																	
																	<div class="row">
																	  <div class="col-md-12">
																		<div class="form-group row">
																		
																		  <label class="control-label text-left col-md-3">
																			จำนวนเงินคดีล้มละลาย:
																			</label>
																		  <div class="col-md-3">
																		  
																			<?php echo formatMoney($bankrupt_hire["bankrupt_amount"]); ?> บาท
																			
																		  </div>
																		  
																		  
																		 
																		  
																		</div>
																	  </div>
																	  
																	</div>
																	
																	<div class="row" style="display: none;">
																	  <div class="col-md-12">
																		<div class="form-group row">
																		  <label class="control-label text-left col-md-3">เอกสารแนบ:</label>
																		  <div class="col-md-9">
																			<p class="form-control-static text-left"> 
																			
																				
																				<?php 
														
																					//$disable_delete = $case_status!=8?0:1;
																					
																					$fl_id = $bankrupt_row[bankrupt_id];                       
																					$fl_type = "bankrupt_docfile";
																					include "doc_file_links.php";
																				 
																				?>
																				
																				<input type="file" name="bankrupt_docfile" class="form-control-file" id="exampleInputFile">
																				
																			</p>
																		  </div>
																		</div>
																	  </div>
																	  
																	</div>
																	
																	<div class="row">
																	  <div class="col-md-12" align=center style="display: none;">
																			
																			
																			<button type="submit" class="btn btn-info"> <i class="mr-2 mdi mdi-content-save"></i> บันทึกข้อมูล</button>
																			<input type=hidden name="case_id" value="<?php echo $the_id;?>">
																			<input type=hidden name="bankrupt_id" value="<?php echo $bankrupt_row[bankrupt_id]?$bankrupt_row[bankrupt_id]:-1;?>">
																			<input type=hidden name="focus" value="tab_bankrupt">
																			
																			
															
																	  </div>
																	  
																	</div>
															
															</form>			

															<hr>
														
															
															  
															</div>
															
														</div>
													</div>
	
	
	
	
</thead>
</table>
	



