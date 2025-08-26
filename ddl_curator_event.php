<select name="curator_event" id="curator_event">
									
                                    
                                    <?php if($is_report_page){?>

									<option value="0">-- select --</option>
									
                                    <?php }?>
                                    
                                    
                                    <option value="การให้สัมปทาน" 
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การให้สัมปทาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การให้สัมปทาน</option>
                                    <option value="จัดสถานที่จำหน่ายสินค้าหรือบริการ"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "จัดสถานที่จำหน่ายสินค้าหรือบริการ"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >จัดสถานที่จำหน่ายสินค้าหรือบริการ</option>
                                    <option value="จัดจ้างเหมาช่วงงาน"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "จัดจ้างเหมาช่วงงาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >จัดจ้างเหมาช่วงงาน</option>
                                    
                                    <option value="ฝึกงาน"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "ฝึกงาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >ฝึกงาน</option>
                                    
                                    <option value="การจัดให้มีอุปกรณ์หรือสิ่งอำนวยความสะดวก"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การจัดให้มีอุปกรณ์หรือสิ่งอำนวยความสะดวก"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การจัดให้มีอุปกรณ์หรือสิ่งอำนวยความสะดวก</option>
                                    
                                    
                                     <option value="การจัดให้มีบริการล่ามภาษามือ"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การจัดให้มีบริการล่ามภาษามือ"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การจัดให้มีบริการล่ามภาษามือ</option>
                                    
                                    
                                    
                                    <option value="การให้ความช่วยเหลืออื่นใด"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การให้ความช่วยเหลืออื่นใด"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การให้ความช่วยเหลืออื่นใด</option>
                            </select>
