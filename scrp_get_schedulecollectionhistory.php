<?php
require_once 'db_connect.php';
require_once 'c2x_model.php';
require_once 'c2x_function.php';

class ScheduleCollectionHistoryDataAccess {
	/**
	 * @param int $cid รหัสบริษัท
	 * @return ScheduleCollectionHistoryResult
	 */
	public function getScheduleCollectionHistoryByCid($cid){
		$result = new ScheduleCollectionHistoryResult();
		$result->IsComplete = true;
		try {
			$sql = "SELECT l.Year, sH.SHID, sH.LID, sH.SentDate, sH.ReceivedDate, sH.Receiver
					FROM schedulecollectionhistory sH
					INNER JOIN lawfulness l ON sH.LID = l.LID
					WHERE l.CID = $cid
					ORDER BY l.Year, sH.SentDate";
			$sqlResult = mysql_query($sql);
			$error = mysql_error();
			if($error != ""){
				$result->IsComplete = false;
				$result->Message = $error;
			}else{
				$dataItems = array();
				while ($obj = mysql_fetch_object($sqlResult)){
					array_push($dataItems, $obj);
				}
				$result->Data = $dataItems;
			}
		} catch (Exception $ex) {
			handleUnexpectedException($ex);
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
		}
		return $result;
	}
}

class ScheduleCollectionHistoryResult extends ResultMessage{
	/**
	 * 
	 * @var ScheduleCollectionHistory[]
	 */
	public $Data;
}

class ScheduleCollectionHistory{
	/**
	 * @var int
	 */
	public $Year;
	
	/**
	 * @var int
	 */
	public $SHID;
	/**
	 * @var int
	 */
	public $LID;
	
	/**
	 * @var DateTime
	 */
	public $ReceivedDate;
	/**
	 * @var string
	 */
	public $Receiver;
	/**
	 * @var DateTime
	 */
	public $SentDate;	
}


?>