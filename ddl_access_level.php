<select name="AccessLevel" id="AccessLevel" onchange="doToggleLevel();">
    <option value="">-- เลือก --</option>
    
    <option value="">-- ผู้ใช้งานสถานประกอบการ --</option>
    
    <option <?php if($output_values["AccessLevel"]=="3"){echo "selected='selected'";}?> value="3">เจ้าหน้าที่ พมจ.</option>
    <option <?php if($output_values["AccessLevel"]=="2"){echo "selected='selected'";}?> value="2">เจ้าหน้าที่ พก.</option>
    <option <?php if($output_values["AccessLevel"]=="1"){echo "selected='selected'";}?> value="1">ผู้ดูแลระบบ</option>
    
    <option <?php if($output_values["AccessLevel"]=="5"){echo "selected='selected'";}?> value="5">ผู้บริหาร</option>
    <option <?php if($output_values["AccessLevel"]=="8"){echo "selected='selected'";}?> value="8">เจ้าหน้าที่งานคดี</option>
    
    <?php if($do_show_company){?>
    <option <?php if($output_values["AccessLevel"]=="4"){echo "selected='selected'";}?> value="4">เจ้าหน้าที่สถานประกอบการ</option>
    <?php }?>
    
    <option value="">-- ผู้ใช้งานหน่วยงานภาครัฐ --</option>
    
    <option <?php if($output_values["AccessLevel"]=="6"){echo "selected='selected'";}?> value="6">ผู้ดูแลระบบ สศส.</option>    
    <option <?php if($output_values["AccessLevel"]=="7"){echo "selected='selected'";}?> value="7">เจ้าหน้าที่ สศส.</option>
    
</select>