<?php
		session_start();
		
		$username = $_SESSION['username'];
		$itemid = $_GET['itemid'];
		$url = "https://localhost:444/testapp/delete_post?username=".$username."&itemid=".$itemid;
		$method = "GET";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		echo $status;
		curl_close($curl);
		
		

?>