<select name="LawfulFlag" id="select">
    <option value="" selected="selected">-- all --</option>
    <option value="1" <?php if($_POST["LawfulFlag"] == "1"){echo "selected='selected'";}?>>ทำตามกฏหมาย</option>
    <option value="0" <?php if($_POST["LawfulFlag"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
    <option value="2" <?php if($_POST["LawfulFlag"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน</option>
    <option value="3" <?php if($_POST["LawfulFlag"] == "3"){echo "selected='selected'";}?>>ไม่เข้าข่ายจำนวนลูกจ้าง</option>
</select>