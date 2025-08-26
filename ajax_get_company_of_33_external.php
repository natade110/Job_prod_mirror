<?php


    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Get auth header
    $auth_header = null;
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $auth_header = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
    } else {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if(isset($headers['Authorization'])) {
                $auth_header = $headers['Authorization'];
            }
        }
    }

    // Authentication credentials
    $valid_username = '7a8b9c0d1e2f3a4b';
    $valid_password = 'c1d2e3f4a5b6c7d8e9f0a1b2c3d4e5f6';

    $provided_username = null;
    $provided_password = null;

    // Process authentication
    if (!empty($auth_header) && strpos($auth_header, 'Basic ') === 0) {
        $auth_string = base64_decode(substr($auth_header, 6));
        if ($auth_string && strpos($auth_string, ':') !== false) {
            list($provided_username, $provided_password) = explode(':', $auth_string, 2);
        }
    }

    // Authentication Check
    if ($provided_username !== $valid_username || $provided_password !== $valid_password) {
        print json_encode(array(
            "status" => "error",
            "message" => "Authentication Required",
            "data" => null
        ));
        exit;
    }

    // --
	//header("Access-Control-Allow-Origin: http://job.dep.go.th");

	include "db_connect.php";
	include "ajax_allowed_ip.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	
	//$the_id = "5200501031625";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	if($_GET["agent"] && is_numeric($_GET["agent"])){
		$the_agent = $_GET["agent"];
	}
	
	
	
	if(!$the_id){
		$the_id = 1;
	}
	
	if($the_agent != '3'){
		exit();
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	$sql = "
		select
			
			a.le_code as person_code
			, a.le_name as person_name
			
			, b.Year as lawful_year
			
			, c.companyCode as company_sso_code
			, c.TaxId as company_tax_id
			
			, a.le_disable_desc
			
			, c.companyNameThai as company_name
			, c.CompanyTypeCode
			, ctype.CompanyTypeName as company_type
			, le_start_date as start_date
			, le_end_date as end_date
			, province_code
			, province_name
			
			, c.*
		from
			lawful_employees a
				join
					lawfulness b
					on
					a.le_cid = b.cid
					and
					a.le_year = b.year
				join
					company c
					on
					b.cid = c.cid
				join
					provinces d
					on
					d.province_id = c.province
					
				left join
					companytype ctype
					on
					ctype.CompanyTypeCode = c.CompanyTypeCode
					
		where
			a.le_code = '$the_id'
		order by
			le_year desc,
			le_start_date desc
		limit 
			0,100
	";
	
	//echo $sql."<br>";
	$result = mysql_query($sql);
	$json_result = array();
	while($r = mysql_fetch_assoc($result)) {
		
		$array_to_push = array();
		
		$company_name_full = formatCompanyName($r[company_name], $r[CompanyTypeCode]);
		
		$company_full_address = getAddressText($r);
		
		
		$array_to_push[person_code] = $r[person_code];
		
		$array_to_push[disable_desc] = $r[le_disable_desc];
		
		
		$array_to_push[person_name] = $r[person_name];
		$array_to_push[company_sso_code] = $r[company_sso_code];
		
		$array_to_push[company_sso_branch_code] = $r[BranchCode];
		
		
		$array_to_push[lawful_year] = $r[lawful_year];
		$array_to_push[company_tax_id] = $r[company_tax_id];
		$array_to_push[company_name] = $r[company_name];
		$array_to_push[company_type] = $r[company_type];
		
		$array_to_push[company_name_full] = $company_name_full;
		
		$array_to_push[start_date] = $r[start_date];
		$array_to_push[end_date] = $r[end_date];
		$array_to_push[province_code] = $r[province_code];
		$array_to_push[province_name] = $r[province_name];
		
		$array_to_push[company_full_address] = $company_full_address;
		
		//echo "<br>"; print_r($array_to_push);
		
		$json_result[] = $array_to_push;
	}
	
	//yoes 20210315 --> do the log
	$sql = "insert into ws_logs(
		
		LogTime
		,Username
		,IPAddress
		,FunctionCall
		
		,Request		
		,Response
		
	)values(
	
		now()
		,'003'
		,'".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
		,'ajax_get_company_of_33_external'
		
		,'$the_id'
		,'".print_r($json_result, true)."'
	
	)";
	
	mysql_query($sql);
	
	//print_r($json_result);
	
	print json_encode($json_result);