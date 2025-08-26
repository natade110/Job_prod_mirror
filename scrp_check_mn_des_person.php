<?php


	if($_SERVER['SERVER_ADDR'] == "127.0.0.1" || $_SERVER['SERVER_ADDR'] == "203.146.215.187"){
		
		$have_record_in_oracle = 1;
		
	}else{
	
		class DesPersonRequest {
			public $username;
			public $password;
			public $person_code;
		};

		$req = new DesPersonRequest();
		$req->username = 'jobdepgoth';
		$req->password = ']y[l6fpvf';
		$req->person_code = $le_id;
		
		//echo $le_id; exit();
		
		

		$ws = new SoapClient("http://161.82.250.36/ws/services.php?func=01",array("trace" => 1, "exception" => 0));
		$result = $ws->getDesPerson($req);

		
		
		if($result->return_code != 0){
			//echo "Error: ".$result->return_message;
			//exit;
		}
		
		
		
		$dat = $result->maimad_details->maimad;
			
		if($dat->first_name_thai){
			$have_record_in_oracle = 1;	
		}
	}

?>