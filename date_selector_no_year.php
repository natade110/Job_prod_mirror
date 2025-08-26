<?php

	if($this_date_time != "" && $this_date_time != "0000-00-00"){
	   $this_selected_year = date("Y", strtotime($this_date_time));
	   $this_selected_month = date("m", strtotime($this_date_time));
	   $this_selected_day = date("d", strtotime($this_date_time));
	}else{
	   $this_selected_year = 0;
	   $this_selected_month = 0;
	   $this_selected_day = 0;
	}

?><select name="<?php echo $selector_name;?>_day" size="1" class="inputf" id="<?php echo $selector_name;?>_day" style="width:auto;" >
                                                  <option selected value="00">...</option>
                                                  <?php
                                                $out = "";
                                                for ($i=1; $i<=31; $i++) {
                                                    $cur_i = sprintf("%02d", $i);
                                                    $selected =  ($cur_i == $this_selected_day) ? " SELECTED" : "";
                                                    echo '<option  value="'.$cur_i.'" '.$selected.'>'.$cur_i.'</option>';
                                                }
                                                echo $out;
                                                ?>
                                                </select>
                                                <select name="<?php echo $selector_name;?>_month" size="1" class="inputf" id="<?php echo $selector_name;?>_month" style="width:auto;" >
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