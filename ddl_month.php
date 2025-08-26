
<?php $this_selected_month = date("m");?>

<select name="ddl_month" size="1" class="inputf" id="ddl_month" style="width:auto;" >
                                                <option selected value="00">...</option>
                                                <option value="01" <?php if($this_selected_month=="01"){echo "selected='selected'";}?>><?php echo "มกราคม"?></option>
                                                <option value="02" <?php if($this_selected_month=="02"){echo "selected='selected'";}?>><?php echo "กุมภาพันธ์"?></option>
                                                <option value="03" <?php if($this_selected_month=="03"){echo "selected='selected'";}?>><?php echo "มีนาคม"?></option>
                                                <option value="04" <?php if($this_selected_month=="04"){echo "selected='selected'";}?>><?php echo "เมษายน"?></option>
                                                <option value="05" <?php if($this_selected_month=="05"){echo "selected='selected'";}?>><?php echo "พฤษภาคม"?></option>
                                                <option value="06" <?php if($this_selected_month=="06"){echo "selected='selected'";}?>><?php echo "มิถุนายน"?></option>
                                                <option value="07" <?php if($this_selected_month=="07"){echo "selected='selected'";}?>><?php echo "กรกฎาคม"?></option>
                                                <option value="08" <?php if($this_selected_month=="08"){echo "selected='selected'";}?>><?php echo "สิงหาคม"?></option>
                                                <option value="09" <?php if($this_selected_month=="09"){echo "selected='selected'";}?>><?php echo "กันยายน"?></option>
                                                <option value="10" <?php if($this_selected_month=="10"){echo "selected='selected'";}?>><?php echo "ตุลาคม"?></option>
                                                <option value="11" <?php if($this_selected_month=="11"){echo "selected='selected'";}?>><?php echo "พฤศจิกายน"?></option>
                                                <option value="12" <?php if($this_selected_month=="12"){echo "selected='selected'";}?>><?php echo "ธันวาคม"?></option>
                                              </select>
                                              
                                              
                                             