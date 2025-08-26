<?php if($post_row["le_is_dummy_row"] == "0"){?>

	 <?php 
                             
		             
                                              
        //yoes 20151201 -- cut this from organization.php
      
        //see if this le_id already in another ID
        
        //yoes 20151201 -- but on do so if this is not a dyummy record...
        
        //input as below
        $this_le_code = $post_row["le_code"];
        $this_le_id = $post_row["le_id"];
        $this_le_cid = $post_row["le_cid"];
        $this_le_year = $post_row["le_year"];
        
		$this_lawful_year = $this_lawful_year;
        
        
        $sql = "select 
					* 
				from 
					lawful_employees a
						join
							company b
							on
							le_cid = cid												
							and 
							b.CompanyTypeCode < 200
				
				where 
					le_code = '$this_le_code'
                    and le_id != '$this_le_id' 
					and le_year = '$this_lawful_year'                    
                    and le_is_dummy_row = 0
                    
                    ";
		
		if($this_lawful_year >= 2018 && $this_lawful_year < 2500){
			
			//check if there are other LEID within own time perios
			
			$dupe_le_start_date = $post_row["le_start_date"];
			$dupe_le_end_date = $post_row["le_end_date"];
			
			if($dupe_le_end_date == "0000-00-00"){
				$dupe_le_end_date = "2500-01-01";
			}
			
			$sql .= " 
			
				and
				(
					(le_start_date BETWEEN '".$dupe_le_start_date."' AND '".$dupe_le_end_date."')
					or
					(le_end_date BETWEEN '".$dupe_le_start_date."' AND '".$dupe_le_end_date."')
					or
					(le_start_date <= '".$dupe_le_start_date."' and le_end_date = '0000-00-00')
					or
					(le_start_date <= '".$dupe_le_start_date."' AND le_end_date >= '".$dupe_le_end_date."')
					
				)
				";
		}
      
		//echo $sql;
	  
        $le_result = mysql_query($sql);
        
        while ($le_row = mysql_fetch_array($le_result)) {
			
			
			//yoes 20160201
			//variable here
			$have_duplicate_33 = 1;    
    
      
      ?>
      
      <div>
        
        <?php 
        
        //yoes 20151118 -- make it so company can see link
        if($sess_accesslevel == 4){
        
        ?>
        
        
        
        
        <?php 
				
				//yoes 20160503 --- more detailed message
				if($post_row["le_cid"] == $le_row["le_cid"]){					
					?>
                    
                     <font color="#FF6600"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.33 แล้ว</strong></font>
                    
                    <?php
					
				}else{
					
					?>
                    
                    	  <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                           ! คนพิการนี้มีการทำมาตรา 33 ในสถานประกอบการอื่นแล้ว
                            </span>
                    
                <?php
					
				}
			
			?>
      
      
        
        
        <?php }else{ ?>
        
        
        	<?php 
				
				//yoes 20160503 --- more detailed message
				if($post_row["le_cid"] == $le_row["le_cid"]){					
					?>
                    
                     <font color="#FF6600"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.33 แล้ว</strong></font>
                    
                    <?php
					
				}else{
					
					?>
                    
                    	<a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">
            
                            ! มีการทำงานซ้ำซ้อนใน <?php echo $the_company_word; ?> อื่น
                                    
                        </a>
                    
                <?php
					
				}
			
			?>
        	
        
        
            
        
        
        <?php }?>
        
      </div>
      
    <?php } //end while?>
	
    
<?php }//end if?>