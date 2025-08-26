<?php
require "db_connect.php";

$id = is_null($_GET["id"]) ? "" : $_GET["id"];

if($id != ""){
	$sql_del = "DELETE FROM collectiondocument WHERE CollectionID = $id";
	mysql_query($sql_del);
}

header("location: collection_doc_list.php?deleted=true");
?>