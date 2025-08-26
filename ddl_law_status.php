<select name="law_status_filter" id="law_status_filter">
	<option value="" selected="selected">-- all --</option>
    <option <?php if(strlen($_POST["law_status_filter"]) && $_POST["law_status_filter"]==0){?>selected="selected"<?php }?> value=0 >ส่งเอกสารเพื่อดำเนินการตามกฎหมาย</option>
    <option <?php if($_POST["law_status_filter"]==-1){?>selected="selected"<?php }?> value=-1 >ส่งเรื่องกลับหน่วยงานต้นเรื่อง เพื่อขอเอกสารเพิ่ม</option>
    <option <?php if($_POST["law_status_filter"]==1){?>selected="selected"<?php }?> value=1 >ตรวจเอกสารแล้ว</option>
    <option <?php if($_POST["law_status_filter"]==2){?>selected="selected"<?php }?> value=2 >ส่งหนังสือแจ้งแล้ว</option>
	<option <?php if($_POST["law_status_filter"]==3){?>selected="selected"<?php }?> value=3 >นำเรื่องส่งอัยการ</option>
	<option <?php if($_POST["law_status_filter"]==4){?>selected="selected"<?php }?> value=4 >ส่งฟ้องร้องดำเนินคดี</option>
	<option <?php if($_POST["law_status_filter"]==5){?>selected="selected"<?php }?> value=5 >อยู่ระหว่างพิจารณาคำพิพากษา</option>
	<option <?php if($_POST["law_status_filter"]==6){?>selected="selected"<?php }?> value=6 >พิพากษาแล้ว</option>
	<option <?php if($_POST["law_status_filter"]==7){?>selected="selected"<?php }?> value=7 >คดียุติแล้ว</option>
	<option <?php if($_POST["law_status_filter"]==8){?>selected="selected"<?php }?> value=8 >ระหว่างนำเรื่องส่งอัยการ</option>
</select>
