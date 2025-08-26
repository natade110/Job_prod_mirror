<?php

	include "db_connect.php";
	
	
	//
	function exportLogToFile($logId){
		$hasError = false;
		$errorMessage = NULL;
		
		$tempFileName = './nepfund_export/temp.txt';
		$outputFileName = sprintf('%s/hiring-to-nepfund-%05d.txt', "./nepfund_export/", $logId);
		$tempFileID = fopen($tempFileName, 'cb');
		if ($tempFileID === false){
			$hasError = true;
			$errorMessage = 'ไม่สามารถสร้างไฟล์ขึ้นมาได้';
			
			echo $errorMessage;
			exit();
		}
		
		$detailQuery = "
		SELECT
			ID
			, RawText
			, BillPaymentID
		FROM nepfund_export_log_detail
		WHERE NEPFundExportLogID = $logId
		ORDER BY SEQ ASC";
		
		$readResult = NULL;
		if (!$hasError){
			$readResult = mysql_query($detailQuery);
			
			if ($readResult === false){
				$hasError = true;
				$errorMessage = "ไม่สามารถอ่านข้อมูลจากฐานข้อมูล";
				
				echo $errorMessage;
			}
		}
		
		if (!$hasError){
			$seq = 1;
			while ($row = mysql_fetch_assoc($readResult)){
				$row['RawText'] = sprintf('%06d', $seq).substr($row['RawText'], 6);
				//executeUpdate('nepfund_export_log_detail', "ID={$row['ID']}", array('SEQ'=>$seq, 'RawText'=>$row['RawText']));
				//executeUpdate('bill_payment', "ID={$row['BillPaymentID']}", NULL, array('NEPFundExportDate'=>'NOW()'));
				
				if (fwrite($tempFileID, $row['RawText']."\n") === false){
					$hasError = true;
					$errorMessage = 'เกิดปัญหาในการเขียนไฟล์';
					break;
				}
				
				$seq++;
			}
			
			mysql_free_result($readResult);
		}
		
		if ($tempFileID !== false){
			fclose($tempFileID);
			
			if (rename($tempFileName, $outputFileName) === false){
				$hasError = true;
				$errorMessage = 'มีปัญหาในการสร้างไฟล์';
			}
		}
		//executeUpdate('nepfund_export_log', "ID = $logId", array('ExportStatus' => $hasError ? 2 : 1, 'ErrorMessage' => $errorMessage));
		echo $errorMessage;
	}

	
	
	$sql = 'SELECT ID FROM nepfund_export_log WHERE ID = 783';
	$result = mysql_query($sql);
	if ($result !== false){
		while ($item = mysql_fetch_assoc($result)){
			exportLogToFile($item['ID']);
		}
	}else{
		error_log("Query Error: " . mysql_error());
	}
	
	echo "!";