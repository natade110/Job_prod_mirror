<?php

	include "db_connect.php";
	include "scrp_config.php";
	require_once 'c2x_include.php';
	
?>
<?php 
	include "header_html.php";
	
?>
              <td valign="top">
                	
                <style type="text/css">
	                table.nep-tab-header td a {
	                	display: inline-block;
	                }
                	table.nep-tab-header td a > div{
                		/*display: inline-block;
                		height: 100%*/
                		display: inline-block;
                		width:100%;
                		white-space: nowrap;
                	}
                </style>
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                  
                รายงาน
                
                </h2>
                    
                    
                    
                      
                  <table style="margin:0px 0 0 20px;" >
                  
                      <tr>
                     	<td valign="top"class="td_bordered" >
                        
                       
                        
                           <table cellspacing="0" class="nep-tab-header">
                             <tr>
                                  <td <?php echo $hide_style;?>>
                                      <a href="#tab1" onClick="showTab('one'); return false;">
                                      <div id="tab_one_black" class="white_on_black" style="width:100px;" align="center">รายงานหลัก</div>
                                      <div id="tab_one_grey" class="white_on_grey" style="width:100px; display:none; " align="center">รายงานหลัก</div>
                                      </a>
                                  </td>
                                  
                                  <td <?php echo $hide_style;?>>
                                      <a href="#tab2" onClick="showTab('two'); return false;">
                                      <div id="tab_two_black" class="white_on_black" style="width:175px;" align="center">รายงานสนับสนุนการปฏิบัติงาน</div>
                                      <div id="tab_two_grey" class="white_on_grey" style="width:175px; display:none; " align="center">รายงานสนับสนุนการปฏิบัติงาน</div>
                                      </a>
                                  </td>
                                  
                                  <?php if($sess_accesslevel == 1){?>
                                  <td <?php echo $hide_style;?>>
                                      <a href="#tab3" onClick="showTab('three'); return false;">
                                      <div id="tab_three_black" class="white_on_black" style="width:175px; " align="center">รายงานตรวจสอบการปฏิบัติงาน</div>
                                      <div id="tab_three_grey" class="white_on_grey" style="width:175px; display:none; " align="center">รายงานตรวจสอบการปฏิบัติงาน</div>
                                      </a>
                                  </td>
                                  <?php }?>
                                  
                                  <td <?php echo $hide_style;?>>
                                      <a href="#tab4" onClick="showTab('four'); return false;">
                                      <div id="tab_four_black" class="white_on_black"  align="center">รายงานการดำเนินการตามกฏหมาย</div>
                                      <div id="tab_four_grey" class="white_on_grey"  display:none; " align="center">รายงานการดำเนินการตามกฏหมาย</div>
                                      </a>
                                  </td> 
                                </tr>
                          </table>   
                          
                         
                          </td>
                    	</tr>    
                  
                  </table>                                          
                    
        
        
        <table style="margin:0px 0 0 20px;" cellspacing="20" id="two">
        
        	
            <!--
           		 <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(11); return false;">
                            <strong>รายงานที่ 11: รายละเอียดการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการแยกตามประเภทกิจการ</strong>
                            </a>
                      <form name="report_11_form" id="report_11_form" action="report_11.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_11" onchange="toggleAction(11); return false;">
                                  <option value="html">html</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_11"/></td>
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
                    
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(112); return false;">
                            <strong>รายงานที่ 11: รายละเอียดการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการแยกตามประเภทกิจการ</strong>
                        </a>
                      <form name="report_112_form" id="report_112_form" action="report_112.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td colspan="2">ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan="2">ประเภทกิจการ</td>
                            <td><?php include "ddl_bus_type.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_112" onchange="toggleAction(112); return false;">
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
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" /></td>
                            <td>ภาค</td>
                            <td><?php include "ddl_org_section_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan="5">
                            
                            <input name="chk_from" type="checkbox" value="1" /> ข้อมูลระหว่างวันที่
                            
                            
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?>
                            
                            
                            </td>
                          </tr>
                          
                          
                          
                          <?php } ?>
                          
                          
                         
                          
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                   
                   	<tr >
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(12); return false;">
                            <strong>รายงานที่ 12: สรุปประเภทความพิการที่ทำงานอยู่ในสถานประกอบการ</strong>
                            </a>
                      <form name="report_12_form" id="report_12_form" action="report_12.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_12" onchange="toggleAction(12); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
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
                      
                      <a href="#" onclick="toggleReport(13); return false;">
                            <strong>รายงานที่ 13: สรุปอัตราส่วนที่สถานประกอบการจะต้องรับคนพิการเข้าทำงาน</strong>
                            </a>
                                          
                      <form name="report_13_form" id="report_13_form" action="report_13.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_13" onchange="toggleAction(13); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_13"/></td>
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
                    
                    
                    
                   
                    
                    
                    
                    
                    
                   
                    
                    <!--
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(24); return false;">
                            <strong>รายงานที่ 14: สถานประกอบการที่ไม่เข้าข่ายจำนวนลูกจ้างตามกฎหมาย</strong>
                            </a>
                                          
                      <form name="report_24_form" id="report_24_form" action="report_24.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_24" onchange="toggleAction(24); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                  
                              </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_24"/></td>
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
                    
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(242); return false;">
                            <strong>รายงานที่ 14: รายละเอียดสถานประกอบการที่ไม่เข้าข่าย</strong>
                            </a>
                                          
                      <form name="report_242_form" id="report_242_form" action="report_242.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_242" onchange="toggleAction(242); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                  
                              </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_242"/></td>
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
                      
                      <a href="#" onclick="toggleReport(25); return false;">
                            <strong>รายงานที่ 15: รายงานคนพิการปฏิบัติตามกฎหมายซ้ำซ้อน</strong>
                            </a>
                                          
                      <form name="report_25_form" id="report_25_form" action="report_25.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
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
                              <select name="report_format" id="report_format_25" onchange="toggleAction(25); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                  
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_25"/></td>
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
                    
                    <tr>
                       <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                       <td style="line-height:25px"><a href="#" onclick="toggleReport(262); return false;"> <strong>รายงานที่ 16: สรุปการปฎิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ </strong> - 5 Hiring_01Status</a>
                         <form name="report_262_form" id="report_262_form" action="report_262_mis.php" method="post" target="_blank" >
                           <table width="100%" border="0">
                             <tr>
                               <td>ปี </td>
                               <td><?php include "ddl_year.php";?></td>
                               <td><label>
                                 <input name="rad_area" type="radio" id="radio" value="province" checked="checked" />
                               </label></td>
                               <td>จังหวัด</td>
                               <td><?php include "ddl_org_province_report.php";?></td>
                               <td>รูปแบบ
                                 <select name="report_format2" id="report_format_262" onchange="toggleAction(262); return false;">
                                   <option value="html">html</option>
                                   <option value="excel">excel</option>
                                   <option value="pdf">pdf</option>
                                   <option value="words">words</option>
                                 </select></td>
                               <td><input name="input6" type="submit" value="เรียกดูรายงาน" />
                                 <input type="hidden" name="the_report2" id="the_report2"  value="report_262_mis"/></td>
                             </tr>
                             <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                             <tr>
                               <td>&nbsp;</td>
                               <td>&nbsp;</td>
                               <td><input type="radio" name="rad_area" id="radio10" value="section" /></td>
                               <td>ภาค</td>
                               <td><?php include "ddl_org_section_report.php";?></td>
                               <td>&nbsp;</td>
                               <td>&nbsp;</td>
                             </tr>
                             <?php } ?>
                             <tr>
                               <td ><input name="chk_from2" type="checkbox" value="1" />
                                 ข้อมูลระหว่างวันที่</td>
                               <td colspan="6"><?php 
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
                       <td style="line-height:25px"><a href="#" onclick="toggleReport(262); return false;"> <strong>รายงานที่ 16: สรุปการปฎิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ </strong> - 6 Hiring_02Occu</a>
                         <form name="report_262_form" id="report_262_form" action="report_262_mis-02.php" method="post" target="_blank" >
                           <table width="100%" border="0">
                             <tr>
                               <td>ปี </td>
                               <td><?php include "ddl_year.php";?></td>
                               <td><label>
                                 <input name="rad_area" type="radio" id="radio" value="province" checked="checked" />
                               </label></td>
                               <td>จังหวัด</td>
                               <td><?php include "ddl_org_province_report.php";?></td>
                               <td>รูปแบบ
                                 <select name="report_format2" id="report_format_262" onchange="toggleAction(262); return false;">
                                   <option value="html">html</option>
                                   <option value="excel">excel</option>
                                   <option value="pdf">pdf</option>
                                   <option value="words">words</option>
                                 </select></td>
                               <td><input name="input6" type="submit" value="เรียกดูรายงาน" />
                                 <input type="hidden" name="the_report2" id="the_report2"  value="report_262_mis"/></td>
                             </tr>
                             <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                             <tr>
                               <td>&nbsp;</td>
                               <td>&nbsp;</td>
                               <td><input type="radio" name="rad_area" id="radio10" value="section" /></td>
                               <td>ภาค</td>
                               <td><?php include "ddl_org_section_report.php";?></td>
                               <td>&nbsp;</td>
                               <td>&nbsp;</td>
                             </tr>
                             <?php } ?>
                             <tr>
                               <td ><input name="chk_from2" type="checkbox" value="1" />
                                 ข้อมูลระหว่างวันที่</td>
                               <td colspan="6"><?php 
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
                      
                      <a href="#" onclick="toggleReport(2622); return false;">
                            <strong>รายงานที่ 16.2: ยอดเงินค้างชำระแบ่งตามรายจังหวัด</strong>
                            </a>
                                          
                      <form name="report_2622_form" id="report_2622_form" action="report_2622.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_2622" onchange="toggleAction(2622); return false;">
                                  <option value="html">html</option>
                                   
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_2622"/></td>
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
                      
                      <a href="#" onclick="toggleReport(27); return false;">
                            <strong>รายงานที่ 17: รายงานการจ้างงานคนพิการในสถานประกอบการตามรายชื่อคนพิการ</strong>
                            </a>
                                          
                      <form name="report_27_form" id="report_27_form" action="report_27.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_27" onchange="toggleAction(27); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_27"/></td>
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
                          <tr>
                            <td colspan="7">
                            
                            
								<?php $this_lawful_year = 2013; 
								
								$is_report_page = 1;
								
								?>
							
                            	 <table width="100%" border="0">
                                 	<tr>
                                    	<td>
                                        	เลขบัตรประจําตัวคนพิการ
                                        </td>
                                        <td>
                                        	<input type="text" name="le_code" value="" />
                                        </td>
                                        <td>
                                        	ความพิการ
                                        </td>
                                        <td>
                                        	<?php include "ddl_disable_type.php";?>
                                        </td>
                                    </tr>
									<tr>
                                 	  <td>&nbsp;</td>
                                 	  <td>&nbsp;</td>
                                 	  <td>เพศ</td>
                                 	  <td>
                                      
                                      	<select name="le_gender">
                                        
                                        	<option value="">- ทั้งหมด -</option>
                                            <option value="m">ชาย</option>
                                            <option value="f">หญิง</option>
                                            <option value="n">ไม่ระบุเพศ</option>
                                        	
                                        </select>
                                        	
                                      
                                      </td>
                               	  </tr>
                                    <tr>
                                   	  <td>	
                                        	เลขทะเบียนนายจ้าง
                                      </td>
                                        <td><input type="text" name="CompanyCode" value="" />
                                        </td>
                                      <td>
                                        	ชื่อสถานประกอบการ
                                      </td>
                                        <td><input type="text" name="CompanyNameThai" value="" />
                                        </td>
                                    </tr>
                                  </table>
                            
                            </td>
                          </tr>
                        
                          <?php } ?>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(33); return false;">
                            <strong>รายงานที่ 17.1: รายละเอียดการปฏิบัติตามกฎหมาย ในมาตรา 35  </strong>
                            </a>
                                          
                      <form name="report_33_form" id="report_33_form" action="report_33.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_33" onchange="toggleAction(33); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_33"/></td>
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
                          
                          
                          <tr>
                            <td colspan="7">
                            
                            
								<?php $this_lawful_year = 2013; 
								
								$is_report_page = 1;
								
								?>
							
                            	 <table width="100%" border="0">
                                 	<tr>
                                    	<td>
                                        	เลขบัตรประจําตัวคนพิการ
                                        </td>
                                        <td>
                                        	<input type="text" name="le_code" value="" />
                                        </td>
                                        <td>
                                        	ความพิการ
                                        </td>
                                        <td>
                                        	<?php include "ddl_disable_type.php";?>
                                        </td>
                                    </tr>
                                    <tr>
                                    	 <td>
                                        	กิจกรรมตาม ม.35
                                        </td>
                                        <td colspan="3">
                                        	<?php 											
												include "ddl_curator_event.php";											
											?>
                                        </td>
                                       
                                    </tr>
                                    <tr>
                                   	  <td>	
                                        	เลขทะเบียนนายจ้าง
                                      </td>
                                        <td><input type="text" name="CompanyCode" value="" />
                                        </td>
                                      <td>
                                        	ชื่อสถานประกอบการ
                                      </td>
                                        <td><input type="text" name="CompanyNameThai" value="" />
                                        </td>
                                    </tr>
                                  </table>
                            
                            </td>
                          </tr>
                          
                          
                        
                          <?php } ?>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(32); return false;">
                            <strong>รายงานที่ 18: สถิติปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ </strong>
                            </a>
                                          
                      <form name="report_32_form" id="report_32_form" action="report_32.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_32" onchange="toggleAction(32); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_32"/></td>
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
                      
                      <a href="#" onclick="toggleReport(34); return false;">
                            <strong>รายงานที่ 19: รายงานแสดงรายการสถานประกอบการที่ชำระเงินและเจ้าหน้าที่ยังไม่ได้กรอกรายละเอียดข้อมูล มาตรา 33 35 </strong>
                            </a>
                                          
                      <form name="report_34_form" id="report_34_form" action="report_34.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year_org_list.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_34" onchange="toggleAction(34); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_34"/></td>
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
                          
                          <tr>
                            <td ><input name="chk_from" type="checkbox" value="1" /> ตรวจสอบระหว่างวันที่</td>
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
                          
                        
                          <?php } ?>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                     <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(113); return false;">
                            <strong>รายงานที่ 20: สถิติการจ้างงานคนพิการ ตามประเภทกิจการ แบ่งตามตำแหน่งงาน </strong>
                            </a>
                                          
                      <form name="report_113_form" id="report_113_form" action="report_113.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year_org_list.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_34" onchange="toggleAction(34); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_34"/></td>
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
        
        
        </table>
                    
                    
        <?php if($sess_accesslevel == 1){?>
        <table style="margin:0px 0 0 20px;" cellspacing="20" id="three">
                    
                  	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(28); return false;">
                            <strong>รายงานที่ 18 : รายงานการบันทึกข้อมูลสถานประกอบการ</strong>
                            </a>
                                          
                      
                      <form name="report_28_form" id="report_28_form" action="report_28.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td>เดือน/ปี </td>
                            <td><?php $selector_name = "mod_date"; include "date_selector_no_day.php";?></td>
                            <td>เลขที่บัญชีนายจ้าง</td>
                            <td><input type="text" name="CompanyCode" value="" /></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_28" onchange="toggleAction(28); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                              </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_28"/></td>
                          </tr>
                          
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>ชื่อสถานประกอบการ</td>
                            <td><input type="text" name="CompanyNameThai" value="" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>ชื่อผู้ใช้งาน</td>
                            <td><input type="text" name="user_name" value="" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                           <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>ประเภทข้อมูลที่ปรับปรุง</td>
                            <td colspan=3>
							
                            <select name="mod_type" id="mod_type">
                                
                                <?php
                                
                                
                                    $get_mod_type_sql = "
									
										SELECT 
											distinct(mod_type) 
										FROM 
											modify_history
										order by
											mod_type
										asc
									
                                        ";
                                
                               
                                        
                                    echo '<option value="">-- แสดงทั้งหมด --</option>';
                              
                                
                                //all photos of this profile
                                
                              
                                $mod_type_result = mysql_query($get_mod_type_sql);
                                
                                
                                while ($mod_type_row = mysql_fetch_array($mod_type_result)) {
                                
                                
                                ?>              
                                    <option value="<?php echo $mod_type_row["mod_type"];?>"><?php echo getModType($mod_type_row["mod_type"]);?></option>
                                
                                <?php
                                }
                                ?>
                                
                                
                            </select>

                            
                            </td>
                            
                          </tr>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    
                    
                     
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(14); return false;">
                            <strong>รายงานที่ 19: รายงานตรวจสอบบันทึกข้อมูลสถานประกอบการแต่ละผู้ใช้งานระบบ</strong>
                            </a>
                                          
                      <form name="report_14_form" id="report_14_form" action="report_14.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td >ข้อมูลระหว่างวันที่</td>
                            <td>
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          <tr>
                            <td >ชนิดของ User</td>
                            <td><?php include "ddl_access_level.php";?></td>
                          </tr>
                          <tr>
                            <td >ชื่อ User</td>
                            <td><label>
                              <input type="text" name="user_name" id="user_name" />
                            </label></td>
                          </tr>
                          <tr>
                            <td >&nbsp;</td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_14" onchange="toggleAction(14); return false;">
                                <option value="html">html</option>
                                
                                <option value="excel">excel</option>
                                <option value="pdf">pdf</option>
                                <option value="words">words</option>
                                </select>                            
                                
                                
                                
                                <input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_14"/>                                </td>
                          </tr>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(142); return false;">
                            <strong>รายงานที่ 19.2: รายงานตรวจสอบการเพิ่มสถานประกอบการของผู้ใช้งานระบบ</strong>
                            </a>
                                          
                      <form name="report_142_form" id="report_142_form" action="report_142.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td >ข้อมูลระหว่างวันที่</td>
                            <td>
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          <tr>
                            <td >ชนิดของ User</td>
                            <td><?php include "ddl_access_level.php";?></td>
                          </tr>
                          <tr>
                            <td >ชื่อ User</td>
                            <td><label>
                              <input type="text" name="user_name" id="user_name" />
                            </label></td>
                          </tr>
                          <tr>
                            <td >&nbsp;</td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_142" onchange="toggleAction(142); return false;">
                                <option value="html">html</option>
                                
                                <option value="excel">excel</option>
                                <option value="pdf">pdf</option>
                                <option value="words">words</option>
                                </select>                            
                                
                                
                                
                                <input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_142"/>                                </td>
                          </tr>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(20); return false;">
                            <strong>รายงานที่ 20 : การบันทึกข้อมูลเจ้าหน้าที่ของสถานประกอบการที่ใช้ระบบรายงานผลการจ้างงานคนพิการ</strong>
                        </a>                                          

                      <form name="report_20_form" id="report_20_form" action="report_20.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td>เดือน/ปี </td>
                            <td><?php $selector_name = "mod_date"; include "date_selector_no_day.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                           <td>ชื่อสถานประกอบการ</td>
                            <td><input type="text" name="register_org_name" value="" /></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_20" onchange="toggleAction(20); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                              </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_20"/></td>
                          </tr>
                          <tr>
                            <td>Username ของสถานประกอบการ</td>
                            <td><input type="text" name="register_name" value="" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(21); return false;">
                      <strong>รายงานที่ 21: รายละเอียดสถานประกอบการที่ไม่ปฏิบัติตามกฎหมาย แต่มีข้อมูล ม.33,34,35</strong>
                      </a>
                      <form name="report_21_form" id="report_21_form" action="report_21.php" method="post" target="_blank">
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
                              <select name="report_format" id="report_format_21" onchange="toggleAction(21); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_8"/></td>
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
                   	  <td valign="top"><img src="decors/pdf_small.jpg"></td>
                   	  <td style="line-height: 25px;">
                      <a onclick="toggleReport(22); return false;" href="#">
                      <strong>รายงานที่ 22: รายละเอียดของการชำระเงินผ่านธนาคาร</strong>
                      </a>
                      <form name="report_22_form" id="report_22_form" action="report_22.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          <tbody><tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year_with_blank.php";?></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_22" onchange="toggleAction(22); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input type="submit" value="เรียกดูรายงาน">
                                <input name="the_report" id="hiddenField2" type="hidden" value="report_22"></td>
                          </tr>
                          <tr>
                            <td><input name="chk_from" type="checkbox" value="1" />ข้อมูลระหว่างวันที่</td>
                            <td colspan="5">
							<?php 
								$selector_name = "txDateFrom";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "txDateTo";
								include "date_selector.php";?></td>
                          </tr>
                          <tr>
								<td>ชื่อบริษัท</td>
								<td colspan="2">
									<input type='text' name='CompanyName' />
								</td>
								<td>เลขบัญชีนายจ้าง</td>
								<td colspan="2">
									<input type='text' name='CompanyCode' maxlength="10" />
								</td>
							</tr>
							<tr>
								<td>ใบเสร็จเล่มที่</td>
								<td colspan="2">
									<input type='text' name='BookReceiptNo' />
								</td>
								<td>ใบเสร็จเลขที่</td>
								<td colspan="2">
									<input type='text' name='ReceiptNo' />
								</td>
							</tr>
							<tr>
								<td>สถานะ</td>
								<td colspan="2">
									<?php 
										$paymentStatusMapping = getPaymentStatusMapping();
										unset($paymentStatusMapping[0]);
										echo createDropDownListFromMapping('ddl_paymentstatus', $paymentStatusMapping, NULL, '--- สถานะ ---'); ?>
								</td>
								<td>เลขที่เช็ค</td>
								<td colspan="2">
									<input type='text' name='ChequeNo' maxlength="10" />
								</td>
							</tr>
							<tr>
								<td>ธนาคาร</td>
								<td colspan='3'>
									<?php include "ddl_bank.php"; ?>       
								</td>
								<td colspan="2"></td>
							</tr>
                        </tbody>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(143); return false;">
                            <strong>รายงานที่ 23: รายงานตรวจสอบการลบสถานประกอบการของแต่ละผู้ใช้งานระบบ</strong>
                            </a>
                                          
                      <form name="report_143_form" id="report_143_form" action="report_143.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td >ข้อมูลระหว่างวันที่</td>
                            <td>
							
							<?php 
								$selector_name = "date_from";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "date_to";
								include "date_selector.php";?></td>
                          </tr>
                          <tr>
                            <td >ชนิดของ User</td>
                            <td><?php include "ddl_access_level.php";?></td>
                          </tr>
                          <tr>
                            <td >ชื่อ User</td>
                            <td><label>
                              <input type="text" name="user_name" id="user_name" />
                            </label></td>
                          </tr>
                          <tr>
                            <td >&nbsp;</td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_143" onchange="toggleAction(143); return false;">
                                <option value="html">html</option>
                                
                                <option value="excel">excel</option>
                                <option value="pdf">pdf</option>
                                <option value="words">words</option>
                                </select>                            
                                
                                
                                
                                <input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_143"/>                                </td>
                          </tr>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    
                   <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(2422); return false;">
                            <strong>รายงานที่ 24: รายงานตรวจสอบสถานะการปฏิบัติตามกฎหมายที่ไม่ถูกต้องในระบบ</strong>
                            </a>
                                          
                      <form name="report_2422_form" id="report_2422_form" action="report_2422.php" method="post" target="_blank" >
                    
                    			
                                
                                 <table width="100%" border="0">
                          
							
                                      <tr>
                                        <td>ปี </td>
                                        <td><?php include "ddl_year_org_list.php";?></td>
                                        <td><label>
                                          <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                                        </label></td>
                                        <td>จังหวัด</td>
                                        <td><?php include "ddl_org_province_report.php";?></td>
                                        <td>รูปแบบ
                                          <select name="report_format" id="report_format_34" onchange="toggleAction(34); return false;">
                                              <option value="html">html</option>
                                              <option value="excel">excel</option>
                                              <option value="pdf">pdf</option>
                                            </select>                            </td>
                                        <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                            <input type="hidden" name="the_report" id="hiddenField2"  value="report_34"/></td>
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
                      
                      <a href="#" onclick="toggleReport(2522); return false;">
                            <strong>รายงานที่ 25: Transaction Log การจ่ายเงินผ่านระบบธนาคารกรุงไทย online</strong>
                            </a>
                                          
                      <form name="report_2522_form" id="report_2522_form" action="report_2522.php" method="post" target="_blank" >
                    
                    			
                                
                                <table width="100%" border="0">
                                  <tbody><tr>
                                    <td>ปี </td>
                                    <td><?php include "ddl_year_with_blank.php";?></td>
                                    <td>จังหวัด</td>
                                    <td><?php include "ddl_org_province_report.php";?></td>
                                    <td>รูปแบบ
                                      <select name="report_format" id="report_format_22" onchange="toggleAction(22); return false;">
                                          <option value="html">html</option>
                                          <option value="excel">excel</option>
                                          <option value="pdf">pdf</option>
                                          <option value="words">words</option>
                                        </select>                            </td>
                                    <td><input type="submit" value="เรียกดูรายงาน">
                                        <input name="the_report" id="hiddenField2" type="hidden" value="report_22"></td>
                                  </tr>
                                  <tr>
                                    <td><input name="chk_from" type="checkbox" value="1" />ข้อมูลระหว่างวันที่</td>
                                    <td colspan="5">
                                    <?php 
                                        $selector_name = "txDateFrom";
                                        $this_date_time = date("Y-m-d");
                                        include "date_selector.php";?> 
                                    
                                    &nbsp;&nbsp;ถึง&nbsp;&nbsp;
                                    
                                    <?php 
                                        $selector_name = "txDateTo";
                                        include "date_selector.php";?></td>
                                  </tr>
                                 
                                </tbody>
                                </table>
                    
                    
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                    
                    
                     <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg"></td>
                   	  <td style="line-height: 25px;">
                      <a onclick="toggleReport(2226); return false;" href="#">
                      <strong>รายงานที่ 26: รายละเอียดของการชำระเงินผ่านระบบธนาคารกรุงไทย Online</strong>
                      </a>
                      <form name="report_2226_form" id="report_2226_form" action="report_2226.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          <tbody><tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year_with_blank.php";?></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_2226" onchange="toggleAction(2226); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input type="submit" value="เรียกดูรายงาน">
                                <input name="the_report" id="hiddenField2" type="hidden" value="report_22"></td>
                          </tr>
                          <tr>
                            <td><input name="chk_from" type="checkbox" value="1" />ข้อมูลระหว่างวันที่</td>
                            <td colspan="5">
							<?php 
								$selector_name = "txDateFrom";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "txDateTo";
								include "date_selector.php";?></td>
                          </tr>
                          <tr>
								<td>ชื่อบริษัท</td>
								<td colspan="2">
									<input type='text' name='CompanyName' />
								</td>
								<td>เลขบัญชีนายจ้าง</td>
								<td colspan="2">
									<input type='text' name='CompanyCode' maxlength="10" />
								</td>
							</tr>
							<tr>
								<td>ใบเสร็จเล่มที่</td>
								<td colspan="2">
									<input type='text' name='BookReceiptNo' />
								</td>
								<td>ใบเสร็จเลขที่</td>
								<td colspan="2">
									<input type='text' name='ReceiptNo' />
								</td>
							</tr>
							<tr>
								<td>สถานะ</td>
								<td colspan="2">
									<?php 
										$paymentStatusMapping = getPaymentStatusMapping();
										unset($paymentStatusMapping[0]);
										echo createDropDownListFromMapping('ddl_paymentstatus', $paymentStatusMapping, NULL, '--- สถานะ ---'); ?>
								</td>
								<td>เลขที่เช็ค</td>
								<td colspan="2">
									<input type='text' name='ChequeNo' maxlength="10" />
								</td>
							</tr>
							<tr>
								<td>ธนาคาร</td>
								<td colspan='3'>
									<?php include "ddl_bank.php"; ?>       
								</td>
								<td colspan="2"></td>
							</tr>
                        </tbody>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                     <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(2722); return false;">
                            <strong>รายงานที่ 27: รายงานสรุปยอดเชำระเงินระบบกรุงไทย online แบ่งตามรายจังหวัด</strong>
                            </a>
                                          
                      <form name="report_2722_form" id="report_2722_form" action="report_2722.php" method="post" target="_blank" >
                       <table width="100%" border="0">
                          <tbody><tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_2226" onchange="toggleAction(2226); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input type="submit" value="เรียกดูรายงาน">
                                <input name="the_report" id="hiddenField2" type="hidden" value="report_22"></td>
                          </tr>
                        
                        </tbody>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                     <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg"></td>
                   	  <td style="line-height: 25px;">
                      <a onclick="toggleReport(2822); return false;" href="#">
                      <strong>รายงานที่ 28: รายงานการยกเลิการจ่ายเงินของระบบกรุงไทย online</strong>
                      </a>
                      <form name="report_2822_form" id="report_2822_form" action="report_2822.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          <tbody><tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year_with_blank.php";?></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_22" onchange="toggleAction(22); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input type="submit" value="เรียกดูรายงาน">
                                <input name="the_report" id="hiddenField2" type="hidden" value="report_22"></td>
                          </tr>
                          <tr>
                            <td><input name="chk_from" type="checkbox" value="1" />ข้อมูลระหว่างวันที่</td>
                            <td colspan="5">
							<?php 
								$selector_name = "txDateFrom";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "txDateTo";
								include "date_selector.php";?></td>
                          </tr>
                         
                        </tbody>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
                     <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(2922); return false;">
                            <strong>รายงานที่ 29: รายงานการทำ Reverse Transaction ระบบธนาคารกรุงไทย online</strong>
                            </a>
                                          
                      <form name="report_2922_form" id="report_2922_form" action="report_2922.php" method="post" target="_blank" >
                    
                    			
                                
                                <table width="100%" border="0">
                                  <tbody><tr>
                                    <td>ปี </td>
                                    <td><?php include "ddl_year_with_blank.php";?></td>
                                    <td>จังหวัด</td>
                                    <td><?php include "ddl_org_province_report.php";?></td>
                                    <td>รูปแบบ
                                      <select name="report_format" id="report_format_29" onchange="toggleAction(29); return false;">
                                          <option value="html">html</option>
                                          <option value="excel">excel</option>
                                          <option value="pdf">pdf</option>
                                          <option value="words">words</option>
                                        </select>                            </td>
                                    <td><input type="submit" value="เรียกดูรายงาน">
                                        <input name="the_report" id="hiddenField2" type="hidden" value="report_22"></td>
                                  </tr>
                                  <tr>
                                    <td><input name="chk_from" type="checkbox" value="1" />ข้อมูลระหว่างวันที่</td>
                                    <td colspan="5">
                                    <?php 
                                        $selector_name = "txDateFrom";
                                        $this_date_time = date("Y-m-d");
                                        include "date_selector.php";?> 
                                    
                                    &nbsp;&nbsp;ถึง&nbsp;&nbsp;
                                    
                                    <?php 
                                        $selector_name = "txDateTo";
                                        include "date_selector.php";?></td>
                                  </tr>
                                 
                                </tbody>
                                </table>
                    
                    
                        </form>
                      
                      
                      </td>
               	    </tr>
                    
                    
			</table>
            <?php }?>
                    
            <table style="margin:0px 0 0 20px;" cellspacing="20" id="one">
                  
                          
                  
                   	<tr>
                     	<td width="20" valign="top">
                        <img src="decors/pdf_small.jpg" />                        </td>
                    	<td style="line-height:25px">
                      	<a href="#" onclick="toggleReport(1); return false;"><span style="font-weight: bold">รายงานที่ 1: สถิติการปฏิบัติตามกฎหมายของ<?php echo $the_company_word;?>
                        
                        </span></a>
                      	<form name="report_1_form" id="report_1_form" action="report_1.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          

                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio4" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_1" onchange="toggleAction(1); return false;">
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
                             <td >&nbsp;</td>
                             <td colspan="6">
                             
                             
                             ประเภทสถานประกอบการ <?php include "ddl_company_type.php";?>
                             </td>
                           </tr>
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
                          
                          <?php if($sess_accesslevel == 1){?>
                           <tr>
                            <td ><input name="chk_details" type="checkbox" value="1" />
                              แสดงรายละเอียด</td>
                             
                              <td colspan="6"><input name="chk_non_ratio" type="checkbox" value="1" />
                              แสดงสถานประกอบการที่ไม่เข้าข่าย
                              
                              </td>
                           
                          </tr>
                           <tr>
                           
                             
                              <td colspan="7">
                              
                              <input name="chk_sum_company" type="checkbox" value="1" />
                              แสดงจำนวนสถานประกอบการที่ปฏิบัติตามกฎหมายครบตามอัตราส่วน แบ่งตาม ม.33,ม.34,ม.35</td>
                           
                          </tr>
                          
                          
                          
                          <?php }?>
                          
                        </table>
                        
                      </form>                      </td>
                    </tr>
                    
                    
                    
                    
                    
                    <?php if(1==1){?>
                    <tr>
                     	<td width="20" valign="top">
                        <img src="decors/pdf_small.jpg" />                        </td>
                    	<td style="line-height:25px">
                      	<a href="#" onclick="toggleReport('1_1'); return false;"><span style="font-weight: bold">รายงานที่ 1.1: เปรียบเทียบจำนวนสถานประกอบการ
                        
                        </span></a>
                      	<form name="report_1_1_form" id="report_1_1_form" action="report_1_compared.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          

                          <tr>
                            <td>ปี </td>
                           
                            
                            <td>
                            	<?php include "ddl_year.php";?>
                            </td>
                            
                             <td><input name="chk_non_ratio" type="checkbox" value="1" />
                              แสดงสถานประกอบการที่ไม่เข้าข่าย</td>
                            <td><input type="hidden" name="the_report" id="hiddenField"  value="report_1"/></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>แสดงรายละเอียดการเปรียบเทียบข้อมูล:<br>
                            
                            <select name="do_compare_01">
                            	
                                <option value="">---</option>
                                
                                <option value="1">วันที่ (<?php echo formatDateThai(date("Y-m-d"))?>)</option> 
                            
                            
                            	<?php if(1==1){?>
                            	<option value="2">วันที่ (<?php echo formatDateThai(date('Y-m-d',strtotime(date("Y-m-d") . "-1 days")))?>)</option> 
                                <?php }?>
                            	
                            </select>
                            
                            เปรียบเทียบกับ
                            
                            <select name="do_compare_02">
                            	
                                <option value="">---</option>
                                
                                <option value="2">วันที่ (<?php echo formatDateThai(date('Y-m-d',strtotime(date("Y-m-d") . "-1 days")))?>)</option> 
                                
                                <?php if(1==1){?>
                                 <option value="3">วันที่ (<?php echo formatDateThai(date('Y-m-d',strtotime(date("Y-m-d") . "-2 days")))?>)</option> 
                                 <?php }?>
                            
                            </select>
                            
                            </td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input name="input" type="submit" value="เรียกดูรายงาน" /></td>
                            <td>&nbsp;</td>
                          </tr>
                         
                          
                        </table>
                        
                      </form>                      </td>
                    </tr>
                    <?php }?>
                    
                    
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      
                      <a href="#" onclick="toggleReport(2); return false;"><strong>รายงานที่ 2: รายละเอียดการปฏิบัติตามกฎหมาย ม.33</strong></a>
                      <form name="report_2_form" id="report_2_form" action="report_2.php" method="post" target="_blank">
                      <table width="100%" border="0">
                        
                        
                        <?php if(1==0){?>
                        <tr>
                          <td>ข้อมูล</td>
                          <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
                         </tr>
                         <?php }?>
                          
                          
                        <tr>
                          <td>ปี </td>
                          <td><?php include "ddl_year.php";?></td>
                          <td><label>
                            <input name="rad_area" type="radio" id="radio3" value="province" checked="checked" />
                          </label></td>
                          <td>จังหวัด</td>
                          <td><?php include "ddl_org_province_report.php";?></td>
                          <td>รูปแบบ
                            <select name="report_format" id="report_format_2" onchange="toggleAction(2); return false;">
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
                              ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
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
                        <a href="#" onclick="toggleReport(3); return false;"><span style="font-weight: bold">รายงานที่ 3: รายละเอียดการปฏิบัติตามกฎหมาย ม.34                        </span></a>
                        <form name="report_3_form" id="report_3_form" action="report_3.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          

                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio4" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_3" onchange="toggleAction(3); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                              </select>                            </td>
                            <td><input name="input3" type="submit" value="เรียกดูรายงาน" /><input type="hidden" name="the_report" id="hiddenField"  value="report_3"/></td>
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
                          
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
                        <a href="#" onclick="toggleReport(4); return false;"><span style="font-weight: bold">รายงานที่ 4: รายละเอียดการปฏิบัติตามกฎหมาย ม.35                        </span></a>
                        <form name="report_4_form" id="report_4_form" action="report_4.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          
                         <?php if(1==0){?>
                            <tr>
                              <td>ข้อมูล</td>
                              <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
                             </tr>
                         <?php }?>
                         
                         
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio6" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_4" onchange="toggleAction(4); return false;">
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
                          
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
                      <a href="#" onclick="toggleReport(5); return false;">
                      <strong>รายงานที่ 5: รายละเอียดการปฏิบัติตามกฎหมาย ม.33 และ ม.34</strong>
                      </a>
                      <form name="report_5_form" id="report_5_form" action="report_5.php" method="post" target="_blank">
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
                              <select name="report_format" id="report_format_5" onchange="toggleAction(5); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_5"/></td>
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
                          
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
                      <a href="#" onclick="toggleReport(6); return false;">
                      <strong>รายงานที่ 6: รายละเอียดการปฏิบัติตามกฎหมาย ม.33 และ ม.35</strong>
                      </a>
                      <form name="report_6_form" id="report_6_form" action="report_6.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          
							
                            <?php if(1==0){?>
                            <tr>
                              <td>ข้อมูล</td>
                              <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
                             </tr>
                         <?php }?>
                            
                            
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_6" onchange="toggleAction(6); return false;">
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
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
                      <a href="#" onclick="toggleReport(7); return false;">
                      <strong>รายงานที่ 7: รายละเอียดการปฏิบัติตามกฎหมาย ม.34 และ ม.35</strong>
                      </a>
                      <form name="report_7_form" id="report_7_form" action="report_7.php" method="post" target="_blank">
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
                              <select name="report_format" id="report_format_7" onchange="toggleAction(7); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_7"/></td>
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
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
                      <a href="#" onclick="toggleReport(8); return false;">
                      <strong>รายงานที่ 8: รายละเอียดการปฏิบัติตามกฎหมาย ม.33, ม.34 และ ม.35</strong>
                      </a>
                      <form name="report_8_form" id="report_8_form" action="report_8.php" method="post" target="_blank">
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
                              <select name="report_format" id="report_format_8" onchange="toggleAction(8); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_8"/></td>
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
                          
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
                      
                      <a href="#" onclick="toggleReport(9); return false;">
                      <strong>รายงานที่ 9: รายละเอียดสถานประกอบการที่ไม่ปฏิบัติตามกฎหมาย</strong>
                      </a>
                      
                      <form name="report_9_form" id="report_9_form" action="report_9.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          
							<?php if(1==0){?>
                            <tr>
                              <td>ข้อมูล</td>
                              <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
                             </tr>
                         <?php }?>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_9" onchange="toggleAction(9); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
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
                      <a href="#" onclick="toggleReport(10); return false;">
                      <strong>รายงานที่ 10: รายละเอียดสถานประกอบการปฏิบัติไม่ครบตามอัตราส่วน</strong>
                      </a>
                      
                      <form name="report_10_form" id="report_10_form" action="report_10.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          
							<?php if(1==0){?>
                            <tr>
                              <td>ข้อมูล</td>
                              <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
                             </tr>
                         <?php }?>
                          <tr>
                            <td>ปี </td>
                            <td><?php include "ddl_year.php";?></td>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label></td>
                            <td>จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_10" onchange="toggleAction(10); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
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
                        	  <td>&nbsp;</td>
                        	  <td>&nbsp;</td>
                        	  
                        	  <td colspan=5>
								ประเภทสถานประกอบการ
                              <?php include "ddl_company_type.php";?>
                        	  </td>
                       	  </tr>
                          
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
				<table style="margin:0px 0 0 20px;" cellspacing="20" id="four">
	            <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(422); return false;">
                      <strong>รายงานที่ 22: รายงานข้อมูลสรุปการดำเนินการตามกฏหมาย</strong>
                      </a>
                      
                      <form name="report_422_form" id="report_422_form" action="report_422.php" method="post" target="_blank" >
                      <table width="100%" border="0">
                          
							
                          <tr>
                            <td><label>
                              <input name="rad_area" type="radio" id="radio8" value="province" checked="checked" />
                            </label>
                            จังหวัด</td>
                            <td><?php include "ddl_org_province_report.php";?></td>
                            <td>รูปแบบ
                              <select name="report_format" id="report_format_422" onchange="toggleAction(422); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_422"/></td>
                          </tr>
                          <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2){?>
                          <tr>
                            <td><input type="radio" name="rad_area" id="radio9" value="section" />
								ภาค</td>
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
                      
                      <a href="#" onclick="toggleReport(423); return false;">
                            <strong>รายงานที่ 23: รายงานสถานประกอบการที่ถูกดำเนินการตามกฏหมาย</strong>
					</a>
                                          
                      <form name="report_423_form" id="report_423_form" action="report_423.php" method="post" target="_blank" >
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
                              <select name="report_format" id="report_format_423" onchange="toggleAction(423); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
                                  <option value="words">words</option>
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="the_report"  value="report_423"/></td>
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
							<td>กระบวนการ: </td>
							<td colspan="6">
								<?php echo createDropDownListFromMapping('LawStatus', getLawStatusMapping(), NULL, 'ทั้งหมด') ?>
							</td>
                          </tr>
                          
                          
                          
                        </table>
                        </form>
							</td>
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
		
		<?php if($sess_accesslevel == 1){?>	
		document.getElementById('three').style.display = 'none';
		<?php }?>
		document.getElementById('four').style.display = 'none';		
		
		
		document.getElementById('tab_one_black').style.display = 'none';
		document.getElementById('tab_one_grey').style.display = '';
		
		
		document.getElementById('tab_two_black').style.display = 'none';
		document.getElementById('tab_two_grey').style.display = '';
	
		<?php if($sess_accesslevel != 4){?>
		//document.getElementById('official').style.display = 'none';
		//document.getElementById('tab_official_black').style.display = 'none';
		//document.getElementById('tab_official_grey').style.display = '';
		<?php } ?>
		
		document.getElementById(what).style.display = '';
		
		<?php if($sess_accesslevel == 1){?>
		document.getElementById('tab_three_black').style.display = 'none';
		document.getElementById('tab_three_grey').style.display = '';
		<?php }?>
		
		document.getElementById('tab_four_black').style.display = 'none';
		document.getElementById('tab_four_grey').style.display = '';
		document.getElementById('tab_'+what+'_black').style.display = '';
		document.getElementById('tab_'+what+'_grey').style.display = 'none';
		
	}
	
	showTab('one');
	
</script>


<script>

function toggleAction(for_what){
	
	//alert(document.getElementById("report_format_"+for_what).value);

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
	document.getElementById("report_3_form").style.display = "none";
	document.getElementById("report_4_form").style.display = "none";
	document.getElementById("report_5_form").style.display = "none";
	
	document.getElementById("report_6_form").style.display = "none";
	document.getElementById("report_7_form").style.display = "none";
	document.getElementById("report_8_form").style.display = "none";
	document.getElementById("report_9_form").style.display = "none";
	document.getElementById("report_10_form").style.display = "none";
	
	//document.getElementById("report_11_form").style.display = "none";
	
	//new reports
	document.getElementById("report_112_form").style.display = "none";
	
	//document.getElementById("report_24_form").style.display = "none";
	document.getElementById("report_242_form").style.display = "none";
	
	document.getElementById("report_12_form").style.display = "none";
	document.getElementById("report_13_form").style.display = "none";
	
	<?php if($sess_accesslevel == 1){?>
	document.getElementById("report_14_form").style.display = "none";
	document.getElementById("report_142_form").style.display = "none";
	document.getElementById("report_28_form").style.display = "none";
	document.getElementById("report_20_form").style.display = "none";
	document.getElementById("report_21_form").style.display = "none";
	document.getElementById("report_22_form").style.display = "none";
    
   
    document.getElementById("report_143_form").style.display = "none";
    document.getElementById("report_2422_form").style.display = "none";
    
    document.getElementById("report_2522_form").style.display = "none";
    document.getElementById("report_2226_form").style.display = "none";
    document.getElementById("report_2722_form").style.display = "none";
    document.getElementById("report_2822_form").style.display = "none";
    document.getElementById("report_2922_form").style.display = "none";

    
	<?php }?>
	
	
	document.getElementById("report_25_form").style.display = "none";
	//document.getElementById("report_26_form").style.display = "none";
	
	document.getElementById("report_27_form").style.display = "none";
	
	
	document.getElementById("report_262_form").style.display = "none";
	document.getElementById("report_2622_form").style.display = "none";

	document.getElementById("report_422_form").style.display = "none";
	document.getElementById("report_423_form").style.display = "none";
	
	document.getElementById("report_32_form").style.display = "none";
	document.getElementById("report_33_form").style.display = "none";
	document.getElementById("report_34_form").style.display = "none";
    
	document.getElementById("report_1_1_form").style.display = "none";
    document.getElementById("report_113_form").style.display = "none";
  
	
	if(what != 0){
		document.getElementById("report_"+what+"_form").style.display = "";
	}
}

toggleReport(0);

</script>