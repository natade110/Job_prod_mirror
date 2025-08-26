<?php		
	include "db_connect_testing.php";
	$requestHeader = "";
	$reponseHeader = "";
	$requestBody = "";
	$reponseBody = "";
	

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	//0105545129471

	$the_id = "";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}

	$the_id = addslashes(substr($the_id,0,13));
	
	$the_cid = $_POST["the_cid"] ? $_POST["the_cid"]*1 : $_GET["the_cid"]*1;

	
	$the_name = doCleanInput($_POST["the_name"] ? $_POST["the_name"] : $_GET["the_name"]);
	//echo $the_name;
	
	//WS setting
	$func_allow = array('juristicInfoListByJuristicName','juristicCertificate2DetailByJuristicID');
	$func_wsdl = array(
		"juristicInfoListByJuristicName" => array(
			"wsdl" => "DBD/juristicInfoListByJuristicName.wsdl", 
			"params" => "juristicName"
			
		),
		
		"juristicCertificate2DetailByJuristicID" => array(
			"wsdl" => "DBD/juristicCertificate2DetailByJuristicID.wsdl", 
			"params" => "juristicID"
		)		
		
	);
	function getData($func,$k){		
		global $requestHeader,$reponseHeader,$requestBody,$reponseBody;			
		/** -- disable SOAP Request 
		global $func_wsdl;	
		$options = array(
			//'login' => 'DEP@GOV62',
			//'password' => '$STo6254',
			'exceptions'=>1,
			'trace'=>1		
		);
		
		$wsdl = $func_wsdl[$func]['wsdl'];
		$params = new StdClass();
		$params->$func_wsdl[$func]['params'] = $k;	
		$client  = new SoapClient($wsdl,$options);
		
		try {
			$result = $client->$func($params);		
			//sendReturnData(0,"Success",$result,$client->__getLastResponse());
			$data = array();
			$data["return_code"] = $code;
			$data["return_mesg"] = $msg;
			$data["return_xml"] = $xml;
			
			return $data;
			
			
		}catch (SOAPFault $f) {
			//sendReturnData(2,$f->getMessage(),$client->__getLastResponse());        
			return 0;
		}	
		-- */
		
		$ConsumerKey = 'ffa053c6-0d1d-46ce-9df3-aa3c80d9a3b5';
		$ConsumerSecret = 'CyI1K9ILNKF';
		$AgentID = '1230400036254';
		// token request 
		$reqUrl = "https://api.egov.go.th/ws/auth/validate?ConsumerSecret=$ConsumerSecret&AgentID=$AgentID";
		$reqHeader = stream_context_create(array(
			"ignore_errors" => true,
			"http" => array(				
				"method" => "GET",
				"header" => "Consumer-Key: $ConsumerKey\r\n"
				
			)
		));		
		$ret = file_get_contents($reqUrl,false,$reqHeader);		
		$retJson = json_decode($ret);
		$TOKEN_KEY = $retJson->Result;
		
		
		// data request
		if($func == 'juristicInfoListByJuristicName')
			$reqUrl = "https://api.egov.go.th/ws/dbd/v2/juristic?JuristicID=$k";
		else
			$reqUrl = "https://api.egov.go.th/ws/dbd/v2/juristic?JuristicID=$k";
		
		
		//echo $reqUrl; exit();
		// "user_agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3\r\n" .
		$requestHeader = "Consumer-Key: $ConsumerKey\r\n" . 					
					"Content-Type: application/x-www-form-urlencoded\r\n" .
					"Token: $TOKEN_KEY\r\n";
		$reqHeader = stream_context_create(array(
			"ignore_errors" => true,
			"http" => array(				
				"method" => "GET",				
				"header" => $requestHeader
				
			)
		));		
		
		$ret = file_get_contents($reqUrl,false,$reqHeader);		
		
		//echo $ret;
		$resArray = json_decode($ret,true);  // to array
		$reponseHeader = implode("\n",$http_response_header);
		$reponseBody = $ret;
		
		if(!$ret){
			$ddd = curl_get_data($reqUrl,$requestHeader);
			printDebug($ddd);
		}
				
		
		
		//var_dump($retArray);
		$retArray = array(
			"TaxID" 			=> $resArray[JuristicID]
			,"CompanyNameThai" 	=> $resArray[JuristicName_TH]
			,"companyTypeText" 	=> $resArray[JuristicType]
			,"Moo"			   	=> $resArray[AddressInformations][0][Moo]
			,"Soi"			 	=> $resArray[AddressInformations][0][Soi]	
			,"Road"				=> $resArray[AddressInformations][0][Road]
			,"status"			=> $resArray[JuristicStatus]
			,"Address1"			=> $resArray[AddressInformations][0][AddressNo]
			,"District"			=> $resArray[AddressInformations][0][Ampur]
			,"subDistrict"		=> $resArray[AddressInformations][0][Tumbol]
			,"CompanyTypeCode"	=> null
			,"province_name"	=> $resArray[AddressInformations][0][Province]
			,"zipCode"			=> null
		);
		return $retArray;
		//return 0;
	}

	function curl_get_data($url,$h){
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($h && !empty($h)) {
			$headers = explode("\r\n",$h);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		return $data;		
		
	}

	function sendReturnData($code,$msg,$xml=""){
		$data = array();
		$data["return_code"] = $code;
		$data["return_mesg"] = $msg;
		$data["return_xml"] = $xml;
		/*
		if($xml){
			$xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soap_result);
			$xml = simplexml_load_string($xml);
			$json = json_encode($xml);
			$data["data"] = json_decode($json,true);		
		}
	*/	
		echo json_encode($data);	
		//exit;
	}
	
	
	if(!$the_id){
		
		
		//get data from dbd
		//yoes 20190927 -> this API won't work on DBD side for some reason -> remove it then
		/*$the_id = getData("juristicInfoListByJuristicName", $the_name);		
		print_r($the_id);*/
		
		//try get the_id from name
		
		//fall-back DBD
		if(!$the_id){
		
			$sql = "
			
				select
					TaxID
				from
					cs_company_dbd
				where
					CompanyNameThai = '$the_name'
				limit 0,1
			
			";
			
			$the_id = getFirstItem($sql);	
			
		}


		if(!$the_id){		
		
			echo "<b> <font color=orange>** ไม่พบช้อมูลของสถานประกอบการ: กรุณาตรวจสอบ เลขที่ประจำตัวผู้เสียภาษีอากร หรือ ชื่อสถานประกอบการ (ภาษาไทย) ให้ถูกต้อง</font></b>";
			
			
			//log this as "No name in DBD"
			$meta_sql = "
				
				replace into company_meta(
					meta_cid
					, meta_for
					, meta_value			
				)values(
					
					'$the_cid'
					, 'no_dbd_from_name'
					, '1'				
				)
			
			";
			
			mysql_query($meta_sql);
			
			exit();
		
		}else{
			
			//log this as "No name in DBD"
			$meta_sql = "
				
				delete from 
					company_meta
				where
					meta_cid ='$the_cid'
					and 
					meta_for = 'no_dbd_from_name'

			";
			
			mysql_query($meta_sql);
			
		}
	}

	
	
	$require_field_array = array(
		
		"CompanyCode"
		,"TaxID"
		,"CompanyNameThai"
		,"companyTypeText"
		,"Moo"
		,"Soi"
		,"Road"
		,"status"
		,"Address1"
		,"District"
		,"subDistrict"
		,"CompanyTypeCode"
		,"province_name"
		//,"zipCode"
	
	);
	
	
	//yoes 20200304
	//try get 24 hour data from DB first
	$sql_24 = "
		
		select
			*
		from
			cs_company_dbd
		where
			TaxId = '$the_id'
			and
			last_update_date >= NOW() - INTERVAL 1 DAY
		order by
			last_update_date desc
		limit 0,1
	
	";
	$the_row = getFirstRow($sql_24);
	
	
	//yoes 20200304 - get DBD if now found last 24 hours data
	//get DBD
	if(!$the_row){
		
		//echo $the_id; exit();
		
		$the_row = getData("juristicCertificate2DetailByJuristicID", $the_id);	
		$is_from_dbd = 1;
	}
	
	
	//echo "".$the_row[0];
	
	
	
	//get DBD-fallback
	if(!$the_row){
		$sql = "
		
			select
				*
			from
				cs_company_dbd
			where
				TaxId = '$the_id'
				
			limit 0,1
		
		";
		
		//echo $sql;
		
		$the_row = getFirstRow($sql);
	}
	
	
	//log the thing here
	/*
	$log_sql = "
	
		insert into dbd_log(
			
			log_ip
			, log_request
			, log_response
			, log_datetime
			, log_user
			
			, log_request_header
			, log_response_header
		
		)values(
		
			'".get_client_ip_dbd()."'
			,'".str_replace("'","",$sql ? $sql : $client->__getLastRequest())."'
			,'".str_replace("'","",$sql ? $sql : $client->__getLastResponse())."'
			, now()
			, '".($_POST[the_user]*1)."'
			
			
			,'".str_replace("'","",$sql ? $sql : $client->__getLastRequestHeaders())."'
			,'".str_replace("'","",$sql ? $sql : $client->__getLastResponseHeaders())."'
			
		)
	
	";
	*/
	if($is_from_dbd){
		$log_sql = "
		
			insert into dbd_log(
				
				log_ip
				, log_request
				, log_response
				, log_datetime
				, log_user
				
				, log_request_header
				, log_response_header
			
			)values(
			
				'".get_client_ip_dbd()."'
				,'".str_replace("'","",$sql ? $sql : $requestBody)."'
				,'".str_replace("'","",$sql ? $sql : $reponseBody)."'
				, now()
				, '".($_POST[the_user]*1)."'
				
				
				,'".str_replace("'","",$sql ? $sql : $requestHeader)."'
				,'".str_replace("'","",$sql ? $sql : $reponseHeader)."'
				
			)
		
		";	
		
		
		$log_sql = "
		
			insert into dbd_log(
				
				log_ip
				, log_request
				, log_response
				, log_datetime
				, log_user
				
				, log_request_header
				, log_response_header
			
			)values(
			
				'".get_client_ip_dbd()."'
				,'".str_replace("'","",$sql ? $sql : $requestBody)."'
				,''
				, now()
				, '".($_POST[the_user]*1)."'
				
				
				,'".str_replace("'","",$sql ? $sql : $requestHeader)."'
				,'".str_replace("'","",$sql ? $sql : $reponseHeader)."'
				
			)
		
		";
		
		//echo $sql; 
		mysql_query($log_sql);
		
		
		//yoes 20200304
		//input these requests into cs_company_dbd table
		
		$input_fields = array(
		
							'CompanyNameThai'
							,'companyTypeText'
							,'TaxID'
							,'Address1'
							,'District'
							,'subDistrict'
							,'province_name'
							,'zipCode'
							,'address_modified_date'
							,'status'
							,'status_message'
							,'status_message_date'
													
							);
							
		$special_fields = array("cid","last_update_date");
		$special_values = array("'".$the_cid."'","now()");			
		
		$sql = generateInsertSQL($the_row,"cs_company_dbd",$input_fields,$special_fields,$special_values,"replace");	
		mysql_query($sql);
		
	}
	
	
	if(($_POST["mode"] == "import" || $_GET["mode"] == "import") && ($_POST["the_cid"] || $_GET["the_cid"])){
		
		//do import here
		//echo "do_import";
		
		$the_cid = $_POST["the_cid"] ? $_POST["the_cid"] : $_GET["the_cid"];
		
		$the_province_id = getFirstItem("select province_id from provinces where province_name = '".trim(doCleanInput($the_row["province_name"]))."' ");
		
		$the_companyTypeCode = getFirstItem("select CompanyTypeCode from companytype where companyTypeName = '".trim(doCleanInput($the_row["companyTypeText"]))."' "); 
		
		if(trim($the_row["status"]) == "ยังดำเนินกิจการอยู่"){
			$the_status = 1;			
		}elseif(trim($the_row["status"]) == "เลิกกิจการ" || trim($the_row["status"]) == "เลิก"){
			$the_status = 0;			
		}elseif(trim($the_row["status"]) == "ย้าย"){
			$the_status = 2;			
		}elseif(trim($the_row["status"]) == "เสร็จการชำระบัญชี"){
			$the_status = 3;			
		}elseif(trim($the_row["status"]) == "แปรสภาพ"){
			$the_status = 4;			
		}elseif(trim($the_row["status"]) == "ควบ"){
			$the_status = 5;			
		}elseif(trim($the_row["status"]) == "คืนสู่ทะเบียน"){
			$the_status = 6;			
		}elseif(trim($the_row["status"]) == "พิทักษ์ทรัพย์เด็ดขาด"){
			$the_status = 7;			
		}elseif(trim($the_row["status"]) == "ร้าง"){
			$the_status = 8;			
		}elseif(trim($the_row["status"]) == "ล้มละลาย"){
			$the_status = 9;			
		}elseif(trim($the_row["status"]) == "สถานะนิติบุคคลตรวจสอบกับกรมพัฒฯ"){
			$the_status = 10;			
		}
		
		
		$import_sql = "
			
			update
				company
			set
				TaxID = '".doCleanInput($the_row["TaxID"])."'
				, CompanyNameThai = '".doCleanInput($the_row["CompanyNameThai"])."'
				
				, Address1 = '".doCleanInput($the_row["Address1"])."'
				, Moo = '".doCleanInput($the_row["Moo"])."'
				, Soi = '".doCleanInput($the_row["Soi"])."'
				, Road = '".doCleanInput($the_row["Road"])."'
				
				, District  = '".doCleanInput($the_row["District"])."'
				, subDistrict  = '".doCleanInput($the_row["subDistrict"])."'
				
						
		";
		
		////, Zip  = '".doCleanInput($the_row["zipCode"])."'
		
		if($the_province_id){			
			$import_sql .= " , Province = '$the_province_id' ";			
		}
		
		if($the_companyTypeCode){			
			$import_sql .= " , CompanyTypeCode  = '".doCleanInput($the_companyTypeCode)."' ";			
		}
		
		if($the_status){			
			$import_sql .= " , status  = '".doCleanInput($the_status)."' ";			
		}
		
		$import_sql .= "		
			where
				cid = '$the_cid'
		";
		
		include "db_connect_lite.php";
		
		mysql_query($import_sql, $connect_lite);
		
		
		//also log the thing here
		
		//log the thing here
		$log_sql = "
		
			insert into dbd_log(
				
				log_ip
				, log_request
				, log_response
				, log_datetime
				, log_user
				
				, log_request_header
				, log_response_header
			
			)values(
			
				'".get_client_ip_dbd()."'
				,'".str_replace("'","",$import_sql)."'
				,'".str_replace("'","",$import_sql)."'
				, now()
				, '".($_POST[the_user]*1)."'
				
				
				,''
				,''
				
			)
		
		";
		
		//echo $sql; 
		mysql_query($log_sql);
		
		exit();		
	}
	

	//if($the_count > 0){
	//print_r($the_row); exit();
	//yoes 20190927 fix this condition... -> only render table if have "CompanyNameThai"
	if($the_row[CompanyNameThai]){
		//echo $the_output;
	}else{
		echo "<b> <font color=orange>** ไม่พบข้อมูลจากกรมพัฒนาธุรกิจการค้า</font></b>";
		exit();
	}

	
	
	//function to get client ip here
	function get_client_ip_dbd() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	
	function printDebug($v) {
		echo "<!--- DEBUG \n";
		print_r($v);
		echo "\n--->";		
		
	}
	
	
	
?>
<table id="tbl_dbd_table_result"  >
	
	
	<tr>
		<td>
			ชื่อสถานประกอบการ:
			<span id="span_dbd_table_result" style="display: none;">1</span>
		</td>
		<td  style="color: blue;">
			<span id="dbd_CompanyNameThai">
				<?php echo $the_row[CompanyNameThai]; ?>
			</span>
		</td>
		<td>
			ประเภทธุรกิจ:
		</td>
		<td  style="color: blue;">
			<span id="dbd_companyTypeText">
				<?php echo $the_row[companyTypeText]; ?>
			</span>
		</td>
	</tr>
	
	<tr>
		<td>
			เลขที่ประจำตัวผู้เสียภาษีอากร:	
		</td>
		<td colspan=3  style="color: blue;">
			<span id="dbd_TaxID"><?php echo $the_row[TaxID]; ?></span>
		</td>
		
	</tr>



	<tr>
		<td>
			ที่ตั้งสำนักงานใหญ่:
		</td>
		<td colspan=3 style="color: blue;">
			<span id="dbd_Address1">
				<?php echo $the_row[Address1]; ?>
			</span>
		</td>
	</tr>								
	<tr>
		<td>
			อำเภอ/เขต:
		</td>
		<td style="color: blue;">
			<span id="dbd_District">
				<?php echo $the_row[District]; ?>
			</span>
		</td>
		<td>
			ตำบล/แขวง:
		</td>
		<td style="color: blue;">
			<span id="dbd_subDistrict">
				<?php echo $the_row[subDistrict]; ?>
			</span>
		</td>
	</tr>
	<tr>
		<td>
			จังหวัด:
		</td>
		<td style="color: blue;">
			<span id="dbd_province_name">
				<?php echo $the_row[province_name]; ?>
			</span>
		</td>
		<td>
			รหัสไปรษณีย์:
		</td>
		<td style="color: blue;">
			<span id="dbd_zipCode">
				<?php echo $the_row[zipCode]; ?>
			</span>
		</td>
		
	</tr>
	
	<?php if($dbd_data[address_modified_date] && 1==0){?>
	<tr>
		<td>
			วันที่เปลี่ยนแปลงที่ตั้งสำนักงานใหญ่:
		</td>
		<td colspan=3 style="color: blue;">
			<?php echo formatDateThai($dbd_data[address_modified_date]);?>   
		</td>
	</tr>
	<?php }?>
	<tr>
		<td>
			สถานะนิติบุคคล:
		</td>
		<td style="color: blue;">
			<span id="dbd_status">
				<?php echo $the_row[status]; ?>
		</td>
		
		
	</tr>
	<?php if(($dbd_data[status_message] || $dbd_data[status_message_date]) && 1==0){?>
	<tr>
		
		<td>
			หมายเหตุการ เลิก ร้าง ล้มละลาย:
		</td>
		<td style="color: blue;">
			<?php echo formatDateThai($dbd_data[status_message]);?>    
		</td>
		
		<td>
			วันที่หมายเหตุ:
		</td>
		<td style="color: blue;">
			<?php echo formatDateThai($dbd_data[status_message_date]);?>    
		</td>
		
	</tr>
	<?php }?>
	
	
	
</table>
