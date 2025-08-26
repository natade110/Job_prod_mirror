<?php

echo $_SERVER['REMOTE_ADDR'];

echo " ... " . $_SERVER["HTTP_X_REAL_IP"];

print_r($_SERVER);


if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
    $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
}

echo " --- ".$ipAddress;

echo " ...> " . $_SERVER['REMOTE_ADDR'];
echo " ...> " . $_SERVER['HTTP_CLIENT_IP'];
echo " ...> " . $_SERVER['HTTP_X_FORWARDED_FOR'];


?>

