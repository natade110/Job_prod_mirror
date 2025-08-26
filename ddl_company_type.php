<select name="company_type" id="company_type" onchange="do_company_type_change();">
    <option value="0">-- ทั้งหมด --</option>
    
    <option <?php if($_POST["company_type"]=="1"){echo "selected='selected'";}?> value="1">โรงเรียนเอกชน</option>
    
    <option <?php if($_POST["company_type"]=="2"){echo "selected='selected'";}?> value="2">ไม่ใช่โรงเรียนเอกชน</option>                                
    
</select>