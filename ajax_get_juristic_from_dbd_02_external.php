<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include necessary files
include "db_connect.php";
include "scrp_config.php";

// Response array
$response = array(
    "status" => "error",
    "message" => "",
    "data" => null
);

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

// WARNING: Hardcoded credentials - should be moved to config in production
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
    $response['message'] = "Authentication Required";
    echo json_encode($response);
    exit;
}

// Get Parameters from GET
$the_id = isset($_GET["the_id"]) ? $_GET["the_id"] : "";
$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
$the_cid = isset($_GET['the_cid']) ? $_GET['the_cid'] : '';

// Log access
if (!empty($the_cid)) {
    doCompanyFullLog($provided_username, $the_cid, "ajax_get_juristic_from_dbd_02_external.php");
}

// Set up stream context
$opts = array(
    'http' => array(
        'method' => 'GET',
        'header' => array(
            'Host: job.dep.go.th',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ),
        'timeout' => 30
    )
);

$context = stream_context_create($opts);

// Build target URL
$target_url = "http://10.0.116.6/dbd/ajax_get_juristic_from_dbd_02_raw.php?";
$target_url .= "the_id=" . urlencode($the_id);
if (!empty($mode)) $target_url .= "&mode=" . urlencode($mode);
if (!empty($the_cid)) $target_url .= "&the_cid=" . urlencode($the_cid);
$target_url .= "&the_user=" . urlencode($provided_username);

// Get data
try {
    $data = file_get_contents($target_url, false, $context);

    if ($data === false) {
        $response['message'] = "Failed to fetch data";
    } else {
        // Decode JSON response
        $decodedData = json_decode($data);

        // Check if TaxID exists
        if ($decodedData && isset($decodedData->TaxID)) {
            $response['status'] = "success";
            $response['data'] = $data;
            $response['message'] = "Data retrieved successfully";
        } else {
            $response['message'] = "Failed to fetch data: TaxID Not found";
        }
    }
} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
}

// Output response
echo json_encode($response);
?>