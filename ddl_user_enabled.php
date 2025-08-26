<select name="user_enabled" id="select">
	<?php if( $ddl_user_enabled_show_blank){?>
	<option value="">-- ทุกสถานะ --</option>
    <?php }?>
    <option value="0" <?php if($output_values["user_enabled"] == "0" || $_POST["user_enabled"] == "0"){echo "selected='selected'";}?>>รอเปิดใช้งาน</option>
    <option value="1" <?php if($output_values["user_enabled"] == 1 || $_POST["user_enabled"] == 1){echo "selected='selected'";}?>>เปิดให้ใช้งาน</option>
    <option value="2" <?php if($output_values["user_enabled"] == 2 || $_POST["user_enabled"] == 2){echo "selected='selected'";}?>>ไม่อนุญาตให้ใช้งาน</option>
	
</select>