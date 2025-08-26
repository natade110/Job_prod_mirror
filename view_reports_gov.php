<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	
?>
<?php 
	include "header_html.php";
	
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                  
                รายงาน
                
                </h2>
                    
                    
                    
                      
                <table style="margin:0px 0 0 20px;" >
                  
                      <tr>
                     	<td width="20" valign="top" colspan="2" class="td_bordered">
                        
                       
                        
                           <table cellspacing="0">
                             <tr>
                                
                                  
                                 
                                  
                                  <td <?php echo $hide_style;?>>
                                      <a href="#tab1" onClick="showTab('one'); return false;">
                                      <div id="tab_one_black" class="white_on_black" style="width:160px;" align="center">รายงานหลัก</div>
                                      <div id="tab_one_grey" class="white_on_grey" style="width:160px; display:none; " align="center">รายงานหลัก</div>
                                      </a>
                                  </td>
                                  
                                   <td <?php echo $hide_style;?>>
                                      <a href="#tab2" onClick="showTab('two'); return false;">
                                      <div id="tab_two_black" class="white_on_black" style="width:175px;" align="center">รายงานสนับสนุนการปฏิบัติงาน</div>
                                      <div id="tab_two_grey" class="white_on_grey" style="width:175px; display:none; " align="center">รายงานสนับสนุนการปฏิบัติงาน</div>
                                      </a>
                                  </td>
                                  
                                  
                                  
                             </tr>
                          </table>   
                          
                         
                        </td>
                   	</tr>    
                  
                  </table>
                
				
            <table style="margin:0px 0 0 20px;" cellspacing="20" id="two">
        
        	
           
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onClick="toggleReport(112); return false;">
                            <strong>รายงานที่ 7: รายละเอียดการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการแยกตามประเภทหน่วยงาน</strong>
                        </a>
                      <form name="report_112_form" id="report_112_form" action="report_112_gov.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td colspan="2">ประเภทหน่วยงาน</td>
                            <td><?php include "ddl_org_type.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_112" onChange="toggleAction(112); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_112"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          
                          
                          
                          <?php } ?>
                          
                          <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                   
                   	<tr >
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onClick="toggleReport(12); return false;">
                            <strong>รายงานที่ 8: สรุปประเภทความพิการที่ทำงานอยู่ในหน่วยงานของรัฐ</strong>
                            </a>
                      <form name="report_12_form" id="report_12_form" action="report_12.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                        <tr>
                          <td>ประเภทหน่วยงาน</td>
                          <td colspan="6"><?php include "ddl_org_type.php";?></td>
                        </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_12" onChange="toggleAction(12); return false;">
                                  <option value="html">html</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_12"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                   
                    
                   
                    
                    
                    
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onClick="toggleReport(242); return false;">
                            <strong>รายงานที่ 9: รายละเอียดหน่วยงานภาครัฐที่ไม่เข้าข่าย</strong>
                            </a>
                                          
                      <form name="report_242_form" id="report_242_form" action="report_242_gov.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                        <tr>
                          <td>ประเภทหน่วยงาน</td>
                          <td colspan="6"><?php include "ddl_org_type.php";?></td>
                        </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_242" onChange="toggleAction(242); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                  
                              </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_242"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                          
                          <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                          
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                     <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onClick="toggleReport(25); return false;">
                            <strong>รายงานที่ 10: รายงานคนพิการปฏิบัติตามกฎหมายซ้ำซ้อน</strong>
                            </a>
                                          
                      <form name="report_25_form" id="report_25_form" action="report_25_gov.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                        <tr>
                          <td>ประเภทหน่วยงาน</td>
                          <td colspan="6"><?php include "ddl_org_type.php";?></td>
                        </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td>&nbsp;</td>
                            <td>มาตรา</td>
                            <td>
                            
                               <select name="report_type" id="report_type" >
                                  <option value="all">ทั้งหมด</option>
                                  <option value="33">มาตรา 33</option>
                                  <option value="35">มาตรา 35</option>
                                </select>    
                                
                                </td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_25" onChange="toggleAction(25); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                  
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_25"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                          
                          <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    
                    
                    
                    
                    
                    <!--
                     <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(26); return false;">
                            <strong>รายงานที่ 16: สรุปการปฎิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ</strong>
                            </a>
                                          
                      <form name="report_26_form" id="report_26_form" action="report_26.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_26" onchange="toggleAction(26); return false;">
                                  <option value="html">html</option>
                                   
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_26"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    -->
            	
        
        
        </table>    
                
                    
            <table style="margin:0px 0 0 20px;" cellspacing="20" id="one">
                  
                          
                  
                   	<tr>
                     	<td width="20" valign="top">
                        <img src="decors/pdf_small.jpg" />                        </td>
                    	<td style="line-height:25px">
                      	<a href="#" onClick="toggleReport(1); return false;"><span style="font-weight: bold">รายงานที่ 1: สถิติการปฏิบัติตามกฎหมายของ<?php echo $the_company_word;?>
                        
                        </span></a>
                      	<form name="report_1_form" id="report_1_form" action="report_1_gov.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          

                          <tr>
                            <td>ประเภทหน่วยงาน</td>
                            <td colspan="6"><?php include "ddl_org_type.php";?></td>
                          </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio4" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_1" onChange="toggleAction(1); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                              </select>                            </td>
                            <td><input name="input3" type="submit" value="เรียกดูรายงาน" /><input type="hidden" name="the_report" id="hiddenField"  value="report_1"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio5" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td></td>
                          </tr>
                          <?php } ?>
                          
                          
                           <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                          
                          
                        </table>
                        
                      </form>                      </td>
                    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      
                      <a href="#" onClick="toggleReport(2); return false;"><strong>รายงานที่ 2: รายละเอียดการปฏิบัติตามกฎหมาย ม.33</strong></a>
                      <form name="report_2_form" id="report_2_form" action="report_2.php" method="post" target="_blank">
                      <table width="100%" border="0">
                        <tr>
                          <td>ประเภทหน่วยงาน</td>
                          <td colspan="6"><?php include "ddl_org_type.php";?></td>
                          </tr>
                        <tr>
                          <td>ปี </td>
                          <td><?php include "ddl_year.php";?></td>
                          <td><label>
                            <input name="rad_area" type="radio" id="radio3" value="province" checked="checked" />
                          </label></td>
                          <td>จังหวัด</td>
                          <td><?php include "ddl_org_province_report.php";?></td>
                          <td>รูปแบบ
                            <select name="report_format" id="report_format_2" onChange="toggleAction(2); return false;">
                                <option value="html">html</option>
                                <option value="excel">excel</option>
                                <option value="pdf">pdf</option>
                                <option value="words">words</option>
                            </select>
                          </td>
                            
                          <td><input name="input2" type="submit" value="เรียกดูรายงาน" /><input type="hidden" name="the_report" id="hiddenField"  value="report_2"/></td>
                        </tr>
                        <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                        	<tr>
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  <td><input type="radio" name="rad_area" id="radio2" value="section" /></td>
                        	  <td>ภาค</td>
                        	  <td><?php include "ddl_org_section_report.php";?></td>
                        	  <td>&nbsp;</td>
                       	      <td></td>
                       	  </tr>
                          <?php } ?>
                          
                          <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                      </table>
                      </form>
                      
                      
                      
                      </td>
               	    </tr>
                   	
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                        <a href="#" onClick="toggleReport(4); return false;"><span style="font-weight: bold">รายงานที่ 3: รายละเอียดการปฏิบัติตามกฎหมาย ม.35                        </span></a>
                        <form name="report_4_form" id="report_4_form" action="report_4.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          <tr>
                            <td>ประเภทหน่วยงาน</td>
                            <td colspan="6"><?php include "ddl_org_type.php";?></td>
                          </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio6" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_4" onChange="toggleAction(4); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                              </select>
                            </td>
                            <td><input name="input4" type="submit" value="เรียกดูรายงาน" /><input type="hidden" name="the_report" id="hiddenField"  value="report_4"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio7" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                          
                          
                           <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                          
                        </table>
                        </form>
                        
                        
                      </td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onClick="toggleReport(6); return false;">
                      <strong>รายงานที่ 4: รายละเอียดการปฏิบัติตามกฎหมาย ม.33 และ ม.35</strong>
                      </a>
                      <form name="report_6_form" id="report_6_form" action="report_6.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          <tr>
                            <td>ประเภทหน่วยงาน</td>
                            <td colspan="6"><?php include "ddl_org_type.php";?></td>
                          </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_6" onChange="toggleAction(6); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                              </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_6"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                          
                           <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                          
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      
                      <a href="#" onClick="toggleReport(9); return false;">
                      <strong>รายงานที่ 5: รายละเอียด<?php echo $the_company_word; ?>ที่ไม่ปฏิบัติตามกฎหมาย</strong>
                      </a>
                      
                      <form name="report_9_form" id="report_9_form" action="report_9.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          <tr>
                            <td>ประเภทหน่วยงาน</td>
                            <td colspan="6"><?php include "ddl_org_type.php";?></td>
                          </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_9" onChange="toggleAction(9); return false;">
                                  <option value="html">html</option>
                                  
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_9"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                          
                           <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                          
                        </table>
                      </form></td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onClick="toggleReport(10); return false;">
                      <strong>รายงานที่ 6: รายละเอียด<?php echo $the_company_word;?>ปฏิบัติไม่ครบตามอัตราส่วน</strong>
                      </a>
                      
                      <form name="report_10_form" id="report_10_form" action="report_10_gov.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          <tr>
                            <td>ประเภทหน่วยงาน</td>
                            <td colspan="6"><?php include "ddl_org_type.php";?></td>
                          </tr>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_10" onChange="toggleAction(10); return false;">
                                  <option value="html">html</option>
                                  
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_10"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <?php } ?>
                          
                           <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่</td>
                            <td colspan="6">
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          
                          
                        </table>
                      </form></td>
               	    </tr>
                   	
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                     
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                </table>
                    
                  
                   
                   
                   
              </td>
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

</body>
</html>



<script>	
	function showTab(what){
		//toggle table on/off
		document.getElementById('one').style.display = 'none';		
		document.getElementById('two').style.display = 'none';	
		
		
		document.getElementById('tab_one_black').style.display = 'none';
		document.getElementById('tab_one_grey').style.display = '';
		
		
		document.getElementById('tab_two_black').style.display = 'none';
		document.getElementById('tab_two_grey').style.display = '';
	
		
		
		document.getElementById(what).style.display = '';
		
		document.getElementById('tab_'+what+'_black').style.display = '';
		document.getElementById('tab_'+what+'_grey').style.display = 'none';
		
	}
	
	showTab('one');
	
</script>

<script>

function toggleAction(for_what){
	
	if(document.getElementById("report_format_"+for_what).value == "pdf"){
		//alert("hello");
		document.getElementById("report_"+for_what+"_form").action = 'create_pdf_2.php?new_sess=<?php echo rand(100,999);?>';
		//document.report_2_form.submit();
	}else{
		document.getElementById("report_"+for_what+"_form").action = 'report_'+for_what+'.php';
	}
	
}
						

function toggleReport(what){

	document.getElementById("report_1_form").style.display = "none";
	document.getElementById("report_2_form").style.display = "none";
	document.getElementById("report_4_form").style.display = "none";
	
	document.getElementById("report_6_form").style.display = "none";
	document.getElementById("report_9_form").style.display = "none";
	document.getElementById("report_10_form").style.display = "none";
	
	document.getElementById("report_112_form").style.display = "none";
	document.getElementById("report_242_form").style.display = "none";
	
	document.getElementById("report_12_form").style.display = "none";
	document.getElementById("report_25_form").style.display = "none";
	//new reports
	
	
	if(what != 0){
		document.getElementById("report_"+what+"_form").style.display = "";
	}
}

toggleReport(1);

</script>