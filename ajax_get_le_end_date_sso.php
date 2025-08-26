<?php

	//yoes 20220607 - disable due to loads
	//exit();



	/*
	
	sql to fix wrong lawful_employees_sso_end_date
	
		select
			*
		from
		update
		
			lawful_employees_sso_end_date a
				join
					lawful_employees b
						on
						a.le_id = b.le_id
		set
			
			a.le_checked_end_date = '0000-00-00'
			, a.le_checked_datetime = '1001-01-01'
		where
			le_checked_end_date != '0000-00-00'
			and
			le_checked_end_date < le_start_date
	
	
	*/
	
	
	//yoes 20210715 -- disable this for now bacazue it slows ??
	//real question is -> why ajax cause page to slow down
	//exit();
	
	
	include_once "db_connect.php";		
	//$the_id = "3300200049153"; //TEST
	//$ssoDat = getSSOData($the_id);
	//print_r($ssoDat);	
	//exit;
	
	/*yoes 20210403 --> add work hours for script*/
	$the_day = date('D');
	$the_hour = date('H');
	
	//echo "<br>the_day is: " . $the_day;
	//echo "<br>the_hour is: " . $the_hour;
	//echo "<br>-----------";
	
	
	/*yoes 20210403 --> END add work hours for script*/
	

	$the_result_array = array();


	$show_debug = $_GET[show_debug];

	if(is_numeric($_GET[le_id]) && is_numeric($_GET[the_cid])){
		
		$the_company_row = getFirstRow("select * from company where cid = '".$_GET[the_cid]."'");
		$the_company_code = $the_company_row[CompanyCode];
		
		//
		$sql = "select
					a.le_code
					
					, c.companyCode
					, a.le_id
				from
					lawful_employees_company a						
						join
							company c
							on
							a.le_cid = c.cid
				where
					a.le_id = '".$_GET[le_id]."'
				order by
					a.le_id asc
				limit
					0,1
			";
			
		//echo $sql; exit();
		$the_result = mysql_query($sql);
		
		while ($the_row = mysql_fetch_array($the_result)) {    
			
			$the_result_array[] = $the_row;
		
		}
		
		$the_mode = "render_own_company";
		
		if(!count($the_result_array) || !$the_company_code){echo "- ไม่พบข้อมูล -"; exit();}
			
		
	}else{
		
		
		//yoes 20210525
		//yoes 20220607 - disable due to loads
		//echo "stop script for now because finished 2020 run ";
		//exit();
		
		//yoes 20220223 --> starts sso run again for 2021
	
	
		//this page doestnt need session
		
		
		if($_GET[the_le_id]){
			
			$filter = "and b.le_id = '".($_GET[the_le_id]*1)."'";
			
		}
		
		//
		$sql = "select
					a.le_code
					, a.le_checked_end_date
					, c.companyCode
					, a.le_id
					, b.le_start_date
					, b.le_end_date
					, b.le_year
					, a.le_origin_name
					, a.le_origin_end_date
					, c.cid
					, b.le_age
				from
					lawful_employees_sso_end_date a
						join
							lawful_employees b
							on
							a.le_id = b.le_id
						join
							company c
							on
							b.le_cid = c.cid
				where
					1 = 1
					$filter
					
					and
					c.CompanyCode not in (
					
						'9999900014'
						,'9999900016'
						,'9999900002'
						,'9999900123'
						,'9999900015'
						,'9999900010'
						,'9999900017'
						,'9999900012'
					
					)
					
					AND le_checked_datetime = '0000-00-00 00:00:00'
					-- and 1=0
					and a.le_id not in (
					
						select
							meta_leid
						from
							lawful_employees_meta
						where
							meta_for = 'is_extra_33'
							and
							meta_value = 1
					
					)
					
					-- and a.le_code = '3910100029538'
					
				order by
					le_checked_datetime asc
					, b.le_id asc
				limit
					0,1
			";
		//-- AND le_checked_datetime = '0000-00-00 00:00:00'
		//echo $sql; exit();
		$the_result = mysql_query($sql);
		
		while ($the_row = mysql_fetch_array($the_result)) {    
			
			$the_result_array[] = $the_row;
		
		}
		
		$show_debug = 1;
	
	}
	
	
	//print_r(getSSOdata(3300200049153,"5200061954"));
	
	if($the_mode == "render_own_company"){
		
	}elseif($_GET[the_le_id] == 455521){
		
	}else{
		
		//yoes 20210517 --> do this all the time to meet the deadline
		if($the_day == "Sat" || $the_day == "Sun"){
			//echo "this is off-day -> not running script"; 
			exit();
		}
		if($the_hour < 8 || $the_hour > 18){
			//echo "this is off-hour -> not running script"; 
			exit();
		}
	}
	
	                                   
	for ($ii = 0; $ii < count($the_result_array) ; $ii++) {
	
		$the_row = $the_result_array[$ii];
		
		//yoes 20221122
		$the_le_age = $the_row[le_age];
		$the_le_end_date = $the_row[le_end_date];
		
		//echo $the_le_age . $the_le_end_date;
			   
		if($show_debug){
			echo "<br>le_code: " . $the_row[le_id] ;
			echo "<br>checking le_code: " . $the_row[le_code] ;
			echo "<br>of company: " . $the_row[companyCode] ;
			echo "<br>employees_end_date is: " . $the_row[le_checked_end_date] ;
		}
		
		//yoes 20220915 -- sso data is down
		//echo "getSSOdata(".$the_row[le_code].",". $the_row[companyCode].")";
		$ar_sso = getSSOdata($the_row[le_code], $the_row[companyCode]);
		
		//$ar_sso = array();
		
		//print_r($ar_sso);
		
		
		//yoes 20221205
		if(count($ar_sso) == 0){
			
			//yoes 20221205
			//no result from sso (at all)?
			//do nothing about this row for now
			//just update last checked datetime
			$update_sso_end_date_sql = "
			
				update
					lawful_employees_sso_end_date
					
				set
					le_checked_datetime = now()
					, warning_mail_sent = 99
				where
					le_id = '".$the_row[le_id]."'
			";
			
			mysql_query($update_sso_end_date_sql);
			
			
			$row_matched = 1;
			
		}else{		
			$row_matched = 0;
		}
		
		for($i = 0; $i < count($ar_sso); $i++){
			
			$the_start_date = $ar_sso[$i][StartDate]?$ar_sso[$i][StartDate]:"0000-00-00";
			$the_resigned_date = $ar_sso[$i][ResignDate]?$ar_sso[$i][ResignDate]:"0000-00-00";
			
			if($show_debug){
				echo "<br><br>for company: ";
				echo $ar_sso[$i][CompanyCode];
				echo "<br> start date is: ";
				echo $the_start_date;
				
				echo "<br> resign date is: ";
				echo $the_resigned_date;
			}
			
			if(
				$ar_sso[$i][CompanyCode] == $the_row[companyCode]
				&&
				!$row_matched
				//yoes 20210325
				//--also check for record witing that year
				&& (
					(
						$the_resigned_date >= $the_row[le_start_date]	
						&& substr($the_resigned_date, 0, 4) >= $the_row[le_year]
					)
					
					|| 
					
					(
						$the_resigned_date == "0000-00-00"
						//&& $the_start_date <= $the_row[le_start_date]
						&& substr($the_start_date, 0, 4) <= $the_row[le_year]
					)
				)
			){
				
				if($the_resigned_date == $the_row[le_checked_end_date]){
				
					if($show_debug){ echo " - Matched";}
					$row_matched = 1;
				
					//just update last checked datetime
					$update_sso_end_date_sql = "
					
						update
							lawful_employees_sso_end_date
							
						set
							le_checked_datetime = now()
						where
							le_id = '".$the_row[le_id]."'
					";
					
					mysql_query($update_sso_end_date_sql);
					
					
				}
				
				$empResignDate_yyyymmdd = $the_resigned_date;
				
			}
			
			
		}
		
		//echo "<br>::: AGE ".$the_le_age . " / current_end_date: " . $the_le_end_date;
		
		if(!$row_matched){
			
			if($show_debug){ echo " - Unmatched";}
			
			$empResignDate_yyyymmdd = $empResignDate_yyyymmdd?$empResignDate_yyyymmdd:"9999-12-31";
			
					
			//DANG can use this sql to update data back to DB
			$update_sso_end_date_sql = "
			
				update
					lawful_employees_sso_end_date
					
				set
					le_checked_end_date = '$empResignDate_yyyymmdd'
					, le_checked_datetime = now()
				where
					le_id = '".$the_row[le_id]."'
			";
			
			//echo $update_sso_end_date_sql; exit();
			
			mysql_query($update_sso_end_date_sql);
			
			//yoes 20221009
			//end date and checked date not the same then send out an email
			$end_date_not_matched = getFirstItem("
									select
										count(*)
									from 
										lawful_employees_sso_end_date
									where
										le_id = '".$the_row[le_id]."'
										and
										le_origin_end_date != le_checked_end_date
										
									");
			
			
			
			if($end_date_not_matched){
				
				//send out email
				$mail_address = getFirstItem("
								
								select
									user_email
								from
									users
								where
									user_enabled = 1
									and
									user_meta = '".$the_row[cid]."'
									and
									AccessLevel = 4
									
									");
									
				
				//yoes 20221122 -- only send out
				//if age <= 60
				if($mail_address && $the_le_age <= 60 && $the_le_end_date != "2022-12-31"){
					
					$company_row = getFirstRow("select * from company where cid = '".$the_row[cid]."'");
					
					$the_company_name = formatCompanyName($company_row[CompanyNameThai], $company_row[CompanyTypeCode]);
						
					$the_header = "พบคนพิการออกจากงาน ปี ".($the_row[le_year]+543).": ระบบรายงานผลการจ้างงานคนพิการในสถานประกอบการ";
					
					$the_body = "<table><tr><td>เรียน $the_company_name <br>";	
					$the_body .= "เลขที่บัญชีนายจ้าง " .$company_row[CompanyCode] . "<br><br>";
					
					$the_body .= "ระบบรายงานผลการจ้างงานคนพิการในสถานประกอบการ: ตรวจพบคนพิการออกจากงาน ปี ".($the_row[le_year]+543).":<br><br>";
					
					
					if($the_row[le_origin_end_date] == "0000-00-00"){						
						$origin_end_date_text = "ยังทำงานอยู่";						
					}else{
						
						$origin_end_date_text = formatDateThai($the_row[le_origin_end_date]);						
					}
					
					if($the_row[le_checked_end_date] == "0000-00-00" || $the_row[le_checked_end_date] == "9999-12-31"){						
						$checked_end_date_text = "ไม่พบข้อมูลการทำงาน";						
					}else{						
						$checked_end_date_text = formatDateThai($the_row[le_checked_end_date]);						
					}
					
					$the_body .= "ชื่อ-สกุลคนพิการ: <b>".$the_row[le_origin_name] . "</b> 
								<br>วันที่ออกจากงาน ตามที่ได้รายงาน: <b>".$origin_end_date_text."</b>
								<br>วันที่ออกจากงาน จากการตรวจสอบข้อมูลประกันสังคม: <b>".$checked_end_date_text."</b>
								
								
								<br><br>";
					
					if($company_row["Province"] != 1){
						//$the_body .= "<b>** กรุณาทำการยื่นแบบแจ้งเปลี่ยนแปลงข้อมูลการจ้างงาน จพ.7 แก่เจ้าหน้าที่ ณ สำนักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัดหรือศูนย์บริการคนพิการจังหวัด </b> <br><br>";
						$the_body .= "<b>** กรุณาดำเนินการแจ้งเปลี่ยนแปลงข้อมูลการจ้างงานคนพิการ
<br>1) กรณียื่นรายงานประจำปีผ่านทางระบบอิเล็กทรอนิกส์ ให้แก้ไขข้อมูลผ่านระบบฯ
<br>2) กรณียื่นรายงานประจำปีกับเจ้าหน้าที่กองทุนฯ ให้ยื่นแบบ จพ.7 แก่เจ้าหน้าที่กองทุนฯหรือ พมจ.
<br><br>ข้อความนี้เป็นข้อความอัตโนมัติ ขออภัยหากท่านได้ดำเนินการเรียบร้อยแล้ว						</b> <br><br>";
					}else{
						//$the_body .= "<b>** กรุณาทำการยื่นแบบแจ้งเปลี่ยนแปลงข้อมูลการจ้างงาน จพ.7  แก่เจ้าหน้าที่ ณ กองกองทุนส่งเสริมความเสมอภาคคนพิการ </b> <br><br>";
						
						$the_body .= "<b>** กรุณาดำเนินการแจ้งเปลี่ยนแปลงข้อมูลการจ้างงานคนพิการ
<br>1) กรณียื่นรายงานประจำปีผ่านทางระบบอิเล็กทรอนิกส์ ให้แก้ไขข้อมูลผ่านระบบฯ
<br>2) กรณียื่นรายงานประจำปีกับเจ้าหน้าที่กองทุนฯ ให้ยื่นแบบ จพ.7 แก่เจ้าหน้าที่กองทุนฯหรือ พมจ.
<br><br>ข้อความนี้เป็นข้อความอัตโนมัติ ขออภัยหากท่านได้ดำเนินการเรียบร้อยแล้ว						</b> <br><br>";
					}
					
					$the_body .= "ขอแสดงความนับถือ<br>";
					
					$the_body .= "กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>";
					$the_body .= "โทรศัพท์ 02-106-9300, 02-106-9327-31<br>";
					
					
					//$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
					$the_body .= "</td></tr></table>";
	
					//echo $the_body;
					
					//send out email
					//yoes 20220512
					doSendMail($mail_address, $the_header, $the_body);	
					
				}
				
			}
			
		}
		
		
		//ถ้ามีค่าวันที่ออก ให้แปลค่าเป็น yyyy-mm-dd เพื่อเตรียม update เข้า mysql
		/*if($empResignDate){
			$empResignDate_yyyymmdd = "?";
		}else{
			//ไม่มีค่า (คนยังทำงานใน บ. นั้นอยู่ - ให้ใส่ date เป็น 0000-00-00
			$empResignDate_yyyymmdd = "0000-00-00";
			
			
		}*/
		
		 
		
		//uncomment row below to execute query
		//mysql_query($update_sso_end_date_sql);
		
   
	}
	
	
	/*
		input: personal_id
		outpur: array
				[comp_code] (
						[comp_code] => 
						[branch]	=>
						[startDate]	=> YYYY-MM-DD
						[endDate]	=> YYYY-MM-DD
				)
	
	*/
	
	//yoes 20210222 --> also render the result
	
	if($the_mode == "render_own_company"){
		
		if(count($ar_sso) == 0){
			//yoes 20220915 -- sso data is down
			echo "<font color=orangered>ไม่พบข้อมูลการทำงาน</font>";
			//echo "<font color=orangered>-----</font>";
		}
		
		
		echo "<font color=blue>";
		for($i = 0; $i < count($ar_sso); $i++){
			
			//print_r($ar_sso[$i]);
			
			$the_start_date = $ar_sso[$i][StartDate]?$ar_sso[$i][StartDate]:"0000-00-00";
			$the_resigned_date = $ar_sso[$i][ResignDate]?$ar_sso[$i][ResignDate]:"0000-00-00";
			
			if($ar_sso[$i][CompanyCode] == $the_company_code){
				
				
				if($i > 0){
					echo "<br>";
				}
				echo "เริ่มงาน " . formatDateThai($the_start_date);
				if($the_resigned_date != "0000-00-00"){
					echo " ออกจากงาน: " . formatDateThai($the_resigned_date);
				}else{
					echo " - ยังทำงานอยู่";
				}
			}
			
		}
		echo "</font>";
		
	}
	
	function getSSODataNew($the_id,$CompanyCode="5200061954"){
		//http://203.154.94.108/ajax_get_sso.php		
		$post = "name=John&location=Boston&the_id=$the_id&CompanyCode=$CompanyCode";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://203.154.94.108/ajax_get_sso.php");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);
		curl_close($ch);						
		
		//echo $response;		
		table2array($response);
		
	}
	
	function table2array($html){
		$skip = 2;
		$DOM = new DOMDocument;
		$DOM->loadHTML($html);
		$items = $DOM->getElementsByTagName('tr');
		$r = 1; $ret = array();
		foreach ($items as $node) {
			if($skip > 0) {$skip--; continue;}			
			foreach($node->childNodes as $td){		
					$v = trim($td->nodeValue);					
					$ret[$r][] = $v;					
					//echo trim($td->nodeValue )." : ";					
			}	
			$r++;
			//echo "\n";			
		}		
		
		// get En date
		$sDate = array();
		if(preg_match_all('/<input id="sso_start_date_(\w+)_(\d+)" type="hidden\" value="(\d+)"/',$html,$mm)){	
			for($i=0;$i < count($mm[0]); $i++)
				$sDate[$mm[2][$i]][$mm[1][$i]] = $mm[3][$i];				
		}			
		
		$eDate = array();
		if(preg_match_all('/<input id="sso_end_date_(\w+)_(\d+)" type="hidden\" value="(\d+)"/',$html,$mm)){	
			for($i=0;$i < count($mm[0]); $i++)
				$eDate[$mm[2][$i]][$mm[1][$i]] = $mm[3][$i];				
		}				

		
		$ret2 = array();
		for($i=1;$i <= count($ret); $i++){
			if($sDate[$i]) $ret[$i][11] = implode("-",array($sDate[$i][year],$sDate[$i][month],$sDate[$i][day]));
			if($eDate[$i]) $ret[$i][13] = implode("-",array($eDate[$i][year],$eDate[$i][month],$eDate[$i][day]));
			$comp = $ret[$i][4];
			$ret2[$comp]['comp_code'] 	= $comp;
			$ret2[$comp]['branch'] 		= $ret[$i][6];
			$ret2[$comp]['startDate'] 	= $ret[$i][11];
			$ret2[$comp]['endDate'] 	= $ret[$i][13];
			
		}
		print_r($ret2);
		

	}
          
?>