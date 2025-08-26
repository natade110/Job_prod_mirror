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
                    
                    
                    
                  <table style="margin:0px 0 0 20px;" cellspacing="20">
                   	<tr>
                     	<td width="20" valign="top">
                        <img src="decors/pdf_small.jpg" />                        </td>
                    	<td style="line-height:25px">
                      	<a href="#" onclick="toggleReport(1); return false;"><span style="font-weight: bold">รายงานที่ 1: สรุปการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ
และหน่วยงานภาครัฐ            			</span></a>
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
                        </table>
                        
                        </form>                      </td>
                    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      
                      <a href="#" onclick="toggleReport(2); return false;"><strong>รายงานที่ 2: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายตามมาตรา 33</strong></a>
                      <form name="report_2_form" id="report_2_form" action="report_2.php" method="post" target="_blank">
                      <table width="100%" border="0">
                        
                        
                        <tr>
                          <td>ข้อมูล</td>
                          <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
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
                            <select name="report_format" id="report_format_2" onchange="toggleAction(2); return false;">
                                <option value="html">html</option>
                                <option value="excel">excel</option>
                                <option value="pdf">pdf</option>
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
                      </table>
                      </form>
                      
                      
                      
                      </td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                        <a href="#" onclick="toggleReport(3); return false;"><span style="font-weight: bold">รายงานที่ 3: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายตามมาตรา 34                        </span></a>
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
                        </table>
                        
                        </form>
                        
                      </td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                        <a href="#" onclick="toggleReport(4); return false;"><span style="font-weight: bold">รายงานที่ 4: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายตามมาตรา 35                        </span></a>
                        <form name="report_4_form" id="report_4_form" action="report_4.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          
                          <tr>
                            <td>ข้อมูล</td>
                            <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
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
                              <select name="report_format" id="report_format_4" onchange="toggleAction(4); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
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
                        </table>
                        </form>
                        
                        
                      </td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(5); return false;">
                      <strong>รายงานที่ 5: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายตามมาตรา 33 และ 34</strong>
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
                                </select>                            </td>
                            <td><input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_5"/></td>
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
                      <a href="#" onclick="toggleReport(6); return false;">
                      <strong>รายงานที่ 6: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายตามมาตรา 33 และ 35</strong>
                      </a>
                      <form name="report_6_form" id="report_6_form" action="report_6.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          
							<tr>
                            <td>ข้อมูล</td>
                            <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
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
                              <select name="report_format" id="report_format_6" onchange="toggleAction(6); return false;">
                                  <option value="html">html</option>
                                  <option value="excel">excel</option>
                                  <option value="pdf">pdf</option>
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
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(7); return false;">
                      <strong>รายงานที่ 7: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายตามมาตรา 34 และ 35</strong>
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
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(8); return false;">
                      <strong>รายงานที่ 8: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายตามมาตรา 33, 34 และ 35</strong>
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
                                  
                                  <option value="pdf">pdf</option>
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
                          
							<tr>
                            <td>ข้อมูล</td>
                            <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
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
                              <select name="report_format" id="report_format_9" onchange="toggleAction(9); return false;">
                                  <option value="html">html</option>
                                  
                                  <option value="pdf">pdf</option>
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
                        </table>
                        </form></td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(10); return false;">
                      <strong>รายงานที่ 10: รายละเอียดสถานประกอบการที่ปฏิบัติตามกฎหมายไม่ครบตามอัตราส่วน</strong>
                      </a>
                      
                      <form name="report_10_form" id="report_10_form" action="report_10.php" method="post" target="_blank">
                      <table width="100%" border="0">
                          
							<tr>
                            <td>ข้อมูล</td>
                            <td colspan="6"><?php include "ddl_report_data_type.php";?></td>
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
                              <select name="report_format" id="report_format_10" onchange="toggleAction(10); return false;">
                                  <option value="html">html</option>
                                  
                                  <option value="pdf">pdf</option>
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
                        </table>
                        </form></td>
               	    </tr>
                   	<tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(11); return false;">
                            <strong>รายงานที่ 11: สรุปการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการครบตามอัตราส่วน<br />โดยแยกตามประเภทกิจการ</strong>
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
                    
                   
                   	<tr >
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">
                      <a href="#" onclick="toggleReport(12); return false;">
                            <strong>รายงานที่ 12: สรุปประเภทความพิการที่ทำงานอยู่ในสถานประกอบการและหน่วยงานของรัฐ</strong>
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
                                  <option value="pdf">pdf</option>
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
                            <strong>รายงานที่ 13: สรุปอัตราส่วนที่สถานประกอบการและหน่วยงานภาครัฐจะต้องรับคนพิการเข้าทำงาน</strong>
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
                                  <option value="pdf">pdf</option>
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
                    
                    <?php if($sess_accesslevel == 1){?>
                    <tr>
                   	  <td valign="top"><img src="decors/pdf_small.jpg" /></td>
                   	  <td style="line-height:25px">  
                      
                      <a href="#" onclick="toggleReport(14); return false;">
                            <strong>รายงานที่ 14: รายงานตรวจสอบบันทึกข้อมูลสถานประกอบการแต่ละผู้ใช้งานระบบ</strong>
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
                              <select name="report_format" id="report_format_13" onchange="toggleAction(14); return false;">
                                <option value="html">html</option>
                                <option value="excel">excel</option>
                                </select>                            
                                
                                
                                
                                <input name="input5" type="submit" value="เรียกดูรายงาน" />
                                <input type="hidden" name="the_report" id="hiddenField2"  value="report_14"/>                                </td>
                          </tr>
                        </table>
                        </form>
                      
                      
                      </td>
               	    </tr>
                    <?php }?>
                    
                    
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
	document.getElementById("report_3_form").style.display = "none";
	document.getElementById("report_4_form").style.display = "none";
	document.getElementById("report_5_form").style.display = "none";
	
	document.getElementById("report_6_form").style.display = "none";
	document.getElementById("report_7_form").style.display = "none";
	document.getElementById("report_8_form").style.display = "none";
	document.getElementById("report_9_form").style.display = "none";
	document.getElementById("report_10_form").style.display = "none";
	
	document.getElementById("report_11_form").style.display = "none";
	document.getElementById("report_12_form").style.display = "none";
	document.getElementById("report_13_form").style.display = "none";
	<?php if($sess_accesslevel == 1){?>
	document.getElementById("report_14_form").style.display = "none";
	<?php }?>
	
	
	if(what != 0){
		document.getElementById("report_"+what+"_form").style.display = "";
	}
}

toggleReport(0);

</script>