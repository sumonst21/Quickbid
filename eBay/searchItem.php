<?php

	if(isset($_GET['productID'])){
		//do something
		//$data = array('searchstring' => $_GET['searchstring'], 'criteria' => $_GET['criteria'] );
	
		$url = "https://localhost:444/testapp/get_items_id?productID=".$_GET['productID'];
		$method = "GET";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		//echo "ooga";
		echo $status;
		curl_close($curl);
	}
?>