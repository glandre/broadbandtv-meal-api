<?php
	
	/*
	* Function: Get information from website
	* Param: Receive url from website
	* Implemented by:@rossini
	*/
	function get_web_page($url) {
		$options = array(
			CURLOPT_RETURNTRANSFER => true,   // return web page
			CURLOPT_HEADER         => false,  // don't return headers
			CURLOPT_FOLLOWLOCATION => true,   // follow redirects
			CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
			CURLOPT_ENCODING       => "",     // handle compressed
			CURLOPT_USERAGENT      => "test", // name of client
			CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
			CURLOPT_TIMEOUT        => 120,    // time-out on response
		); 

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);

		$content  = curl_exec($ch);
		
		#Decoding json because the source is json
		$retorno = json_decode($content);
		
		curl_close($ch);

		return $retorno;
	}
	
	/*
	* Function: Search for a food with Food name
	* Param: Fodd
	* Implemented by:@rossini
	*/
	function get_food($food_name) {
		
		$api_key = "BaKxZk2ziMCjeBGPJLlN8vw3VLmf2ypZbA6InZik";
		$food_id = "01009";
		
		//Call get_web_page to get food with same name
		$response = get_web_page("http://api.nal.usda.gov/ndb/reports/?ndbno=".$food_id."&type=f&format=json&api_key=".BaKxZk2ziMCjeBGPJLlN8vw3VLmf2ypZbA6InZik."");
		
		$resArr = array();

		$resArr = $response;
		
		//Array to set information from json
		$food_array = array();
		
		
		foreach($resArr->list->item as $food){
			
			$food_array[] = array("name"=>$food->name, "ndbno" => $food->ndbno);
			
		}
		
		$food_return = json_encode($food_array));
		
		return $food_return;
		
	}

	
?>
