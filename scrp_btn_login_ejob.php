								<?php $company_row = getFirstRow("select CompanyNameThai,CompanyTypeCode from company where cid ='$this_cid'"); ?>
								<tr>
							 	 <td colspan="4">
									  <hr />
										<div align="center" id="ejob_login">											
												<input type="button" 
												   style="color: blue;"
												   value="เข้าใช้งานระบบ E-Service ฐานะผู้ใช้งาน <?php echo formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]); ?>"
													
													onClick="getEJobLoginLink();"
													  
													  v-if="the_progress==0"
												/>
												
												 
											
												<font v-if="the_progress==1" color=blue>... กำลังเตรียม Link การเข้าใช้ระบบ E-Service ...</font>
											
												<form v-if="the_progress==2"  method="post" action="https://ejob.dep.go.th/ejob/scrp_do_login_remote.php" target="_blank">
													<input type="hidden" name="trans_user_id" value="<?php echo $sess_userid;?>" />
													<input type="hidden" name="trans_meta_id" value="<?php echo $this_cid;?>" />
													<input v-model="trans_code" type="hidden" name="trans_code" value="" />
													<input type="submit" 
													   style="color: green;"
													   value="กดที่นี่เพื่อเข้าใช้งานระบบ E-Service ฐานะผู้ใช้งาน <?php echo formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]); ?>"
														
													/>
												</form>
											
											
										</div>
								 </td>
							</tr>
						
						
							<script>
									
									var vm = new Vue({
									  // options
										el: '#ejob_login'
										, data: {
											
											
											the_progress: 0
											, trans_code: 0
											// , pay_33_amount_1 : $('#pay_33_amount_1').val()
											
										}
										
										
										
									});
									
									
									function getEJobLoginLink(){
									
										if(!confirm('เข้าใช้งานระบบ E-Service ฐานะผู้ใช้งาน <?php echo formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]);?>')){
							
											return 0;
							
										}
										
										//alert('heh');										
										vm.the_progress = 1;
										
										axios
											.post('view_user_ws_prepare_ejob_login.php', "meta_id="+<?php echo $this_cid;?>)
											.then(response => {
											
												vm.the_progress = 2;
												var mm;		
												mm = response.data;	
												vm.trans_code = mm.trans_code;
												  
											
											})
										
									}
								
								</script>