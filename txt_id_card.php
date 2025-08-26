<?php for($i=1;$i<=13;$i++){ ?>
                                 
                                 	<input 
                                    name="<?php echo $txt_id_card_prefix;?>id_<?php echo $i;?>" 
                                    id="<?php echo $txt_id_card_prefix;?>id_<?php echo $i;?>"
                                    
                                    <?php if($i > 1){?>
                                    onkeydown="deleteBefore(event, this, document.<?php echo $id_form_name;?>.<?php echo $txt_id_card_prefix;?>id_<?php echo $i-1;?>);"
                                    <?php }?>
                                    <?php if($i < 13){?>
                                    onKeyup="autotab(this, document.<?php echo $id_form_name;?>.<?php echo $txt_id_card_prefix;?>id_<?php echo $i+1;?>)" 
                                    <?php }?>
                                    
                                    
                                    
                                    type="text" 
                                    class="free_textbox" 
                                    style="width: 15px;"
                                    value="<?php echo substr($id_form_to_show,$i-1,1);?>" 
                                    maxlength="1" />
                                 
                                 	<?php if($i == 1 || $i == 5 || $i == 10 || $i == 12){?>
                                    -
                                    <?php }?>
                                 
                                 <?php }?>