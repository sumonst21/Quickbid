<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
//require_once('PHPMailer/PHPMailer-master/PHPMailerAutoload.php');
$app = new \Slim\App;
$app->post('/post_item', 'getEntry');
$app->post('/login','checkLogin');
$app->post('/register','getDetails');
$app->get('/get_items','getItems');
$app->get('/get_items_search','getItemsSearch');
$app->get('/get_items_id','getItemsSearchID');
$app->post('/bid','bidItem');
$app->get('/get_my_bids', 'getMyBids');
$app->get('/delete_bid', 'DeleteBid');
$app->get('/delete_post', 'DeletePost');
$app->get('/get_posts','getPosts'); //get my posts
$app->get('/get_bids','getBids'); //get bids for my posts
$app->get('/account','getAccount'); //get account details 
$app->post('/edit_acc','editAccount');
$app->get('/email','sendEmail');

$app->run();
function checkCache($username){
	//echo $username;
	$memcache_host = '127.0.0.1';
	$memcache_port = '11211';
	$memcache = new Memcache;
	$is_cache = $memcache->connect($memcache_host, $memcache_port);


	if($is_cache){
	//$uname = $memcache->get('hi');
	  $uname = $memcache->get("'".$username."'");
	  if($uname == null)
	  { //if data hasn't been cached yet
	      //cache miss'
		 $date = date("Y-m-d h:m:s");
	    $file = __FILE__;
	    $level = "info";
	    $destination = "logs.log";
	    $message = "[{$date}] [{$file}] [{$level}] cache miss".PHP_EOL;
	// log to our default location
	    error_log($message,3,$destination);
	      $memcache->set("'".$username."'", "'".$username."'", false, 86400); //cache the data
	  }
	  else{
	  	//cache hit
	  	$date = date("Y-m-d h:m:s");
	    $file = __FILE__;
	    $level = "info";
	    $destination = "logs.log";
	    $message = "[{$date}] [{$file}] [{$level}] cache hit".PHP_EOL;
	// log to our default location
	    error_log($message,3,$destination);
	  }
	  //do something with the $things, output it, mess with it, whatever.
	}	
}
function sendEmail(){

		$url = "https://localhost:444/microservice/email";
		$method = "GET";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		//echo $status;
		curl_close($curl);
}

function connect(){

	$db = new PDO('mysql:host=localhost;dbname=ebay;charset=utf8mb4', 'root', 'password');
	return $db;
}

function getDetails (Request $request, Response $response){

	$user_ip = $_SERVER['REMOTE_ADDR'];
	$content = "http://www.geoplugin.net/php.gp?ip=".$user_ip;
	//echo $user_ip."IP Address";
    $geo = unserialize(file_get_contents($content));
    $username = null;
    $password = null;
    $fullname = null;
    $email = null;
    $phone = null;
    $address = null;
    $lastlogin = date("Y-m-d");
    $location = $geo["geoplugin_city"];
    $parseBody = $request->getParsedBody();
   
    foreach($parseBody as $key => $param){
    	
        if($key == "username")
        {
            $username = $param; 
        }
        elseif ($key == "password") {

            $password = $param;
            
        }
        elseif ($key == "fullname") {

                $fullname = $param;
        }
        elseif ($key == "email") {

                $email = $param;
        }
        elseif ($key == "phone") {

            $phone = $param;
        }
        elseif ($key == "address") {

                $address = $param;
        }
    }

	$data = array(
         'username' => $username,
	); 

    $url = "https://localhost:444/microservice/checkusername";
    $method = "POST";
   // echo $data["username"];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_exec($curl);
    $errmsg = curl_error($curl);
    $result = curl_getinfo($curl);
    $status = $result['http_code'];
    //echo $status."hi";
    if ($status =="200") {
    	//echo "here";
    	$data = array(
         'username' => $username,
        'password' => $password,
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'address' => $address
		); 
	    //echo $data;
	    $url = "https://localhost:444/microservice/register";
	    $method = "POST";
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_exec($curl);
	    $errmsg = curl_error($curl);
	    $result = curl_getinfo($curl);
	    $status = $result['http_code'];
	    return $response->withStatus(200);
	}
	else{
		// echo explode("::",$status)[0];
		return $response->withStatus(302);
	}

}
function checkLogin(Request $request, Response $response){
	$parseBody = $request->getParsedBody();
	$username = null;
    $password= null;
	foreach($parseBody as $key => $param){
		if($key == "username"){ 
			$username = $param;
			}
		if($key == "password"){
			$password = $param;
		}

	}
	//echo $username;
	//checkCache($username);
	$data = array(
        'username' => $username,
        'password' => $password
		);
    
    $url = "https://localhost:444/microservice/checkuser";
    $method = "POST";
   // echo $data["username"];
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_exec($curl);
    $errmsg = curl_error($curl);
    $result = curl_getinfo($curl);
    $status = $result['http_code'];
    //echo $status."hi";
    if ($status =="200") {
    	//echo "here";
    	$data = array(
        'username' => $username
		);
	    //echo $data;
	    $url = "https://localhost:444/microservice/update_login";
	    $method = "POST";
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_exec($curl);
	    $errmsg = curl_error($curl);
	    $result = curl_getinfo($curl);
	    $status = $result['http_code'];
	    return $response->withStatus(200);
	}
	else{
		// echo explode("::",$status)[0];
		return $response->withStatus(302);
	}

}   

function getEntry (Request $request, Response $response) {
	//echo "here";
    $parseBody = $request->getParsedBody();
    $itemDes = null;
    $itemName = null;
    $price = null;
    $shelfStart = null;
    $shelfEnd = null;

	foreach($parseBody as $key => $param){
		//echo $param;
		if($key == "iname")
		{
			$itemName = $param;	
			//echo $itemName."item name";
		}

		elseif ($key == "idesc") {

			$itemDes = $param;
		}
		elseif ($key == "sstart") {

			$shelfStart = $param;
		}
		elseif ($key == "sstop") {

				$shelfEnd = $param;
		}
		elseif ($key == "iprice") {

				$price = $param;
		}
		elseif($key = "username")
		{
			$username = $param;

		}
		//echo $param;

	}

	$data = array(
       'iname' => $itemName,
        'iprice' => $price,
        'idesc' => $itemDes,
        'sstart' => $shelfStart,
        'sstop' => $shelfEnd,
        'username' => $username
);
//echo $data['sstart'];
		$url = "https://localhost:444/microservice/post_item";
		$method = "POST";
		$curl = curl_init($url);

		$curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_exec($curl);
	    $errmsg = curl_error($curl);
	    $result = curl_getinfo($curl);
	    $status = $result['http_code'];
	    if($status == "200"){
	    	return $response->withStatus(200);
	    }
	    else{
	    	return $response->withStatus(302);
	    }
	}


// 	function insertItem($itemName, $itemDes, $price){
// 		try{
			
// 			$db = connect();
// 			$sql = "INSERT INTO item (itemname,itemprice,itemdescription) VALUES ('".$itemName."','".$price."','".$itemDes."');";
// 			//echo $sql;
// 			//echo $sql;
// 			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// 		 	$db->exec($sql);	
// 		 	//echo "Record inserted";
// 		 	$db = connect();
// 		    $stmt = $db->prepare("Select itemid from item where itemName=:itemName;");
// 		    $stmt->bindParam("itemName",$itemName,PDO::PARAM_STR);
// 		    $stmt->execute();
// 		    //$itemId = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
// 		    foreach($stmt->fetchAll() as $k=>$v){
// 		     $itemId = $v["itemid"] ;
// 		    }
// 		    //echo $itemId["itemid"];
// 		    $db = null;
// 		 	return $itemId;

// 		}
// 		catch(PDOException $e)
// 		{
// 			return -1;
// 			//echo $sql . "<br>" . $e->getMessage();
// 		}
// 		$db = null;
// 	}	

// 	function insertPost($itemId,$shelfStart,$shelfEnd,$sellerId){
// 		$db = connect();
// 		$conn = connect();
// 		try{
// 			$data = $conn->prepare("SELECT userid FROM userprofile WHERE username=:username");
// 			$data->bindParam("username", $sellerId,PDO::PARAM_STR) ;
// 			$data->execute();
// 			foreach($data->fetchAll() as $k=>$v){
// 		     $sellerId = $v["userid"] ;
// 		    }
			
// 		$sql = "INSERT INTO post (itemid,sellerid,shelf_start,shelf_stop) VALUES ('".$itemId."','".$sellerId."','".$shelfStart."','".$shelfEnd."');";
// 		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// 		$db->exec($sql);
// 		//return 1;
// 		//echo "Items table updated";
// 		}
// 		catch(Exception $e) {
// 			//return -1;
//     //echo 'Caught exception: ',  $e->getMessage(), "\n";
// }
		


// 	}

	function getItems(Request $request, Response $response ){

		$url = "https://localhost:444/microservice/get_items";
		$method = "GET";
		$curl = curl_init($url);
		switch ($method)
		    {
		        case "POST":
		            curl_setopt($curl, CURLOPT_POST, 1);
		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
		        case "PUT":
		            curl_setopt($curl, CURLOPT_PUT, 1);
		            break;
		        default:
		            if (isset($data) && $data)
		                $url = sprintf("%s?%s", $url, http_build_query($data));
		    }
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		echo $status;
		curl_close($curl);
	}

	function getItemsSearch(Request $request, Response $response ){
		//echo "hi";
		$allGetVars = $request->getQueryParams();
		foreach($allGetVars as $key => $param){
			if($key == "searchstring"){
					$searchstring = $param;
			}
			elseif ($key == "criteria") {
				$criteria = $param;
			}
		}
		$data = array('searchstring' => $searchstring, 'criteria' => $criteria );
	
		$url = "https://localhost:444/microservice/get_items_search?searchstring=".$_GET['searchstring']."&criteria=".$_GET['criteria'];
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

	function getItemsSearchID(Request $request, Response $response ){


		$allGetVars = $request->getQueryParams();
		foreach($allGetVars as $key => $param){
			if($key == "productID"){
					$productID = $param;
			}
		}
		$url = "https://localhost:444/microservice/get_items_id?productID=".$productID;
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

function bidItem(Request $request,Response $response){
	$parseBody = $request->getParsedBody();
	$itemId = null;
    $itemPrice = null;
    $userName = null;
	foreach($parseBody as $key => $param){
		if($key == "itemId")
		{
			$itemId = $param;	
		}
		elseif ($key == "itemPrice") {

			$itemPrice = $param;
		}
		elseif ($key == "userName") {
			//echo "here";
			$userName = $param;
		}
		//echo $param;
	}
	//echo $itemPrice;

	$data = array(
        'itemId' => $itemId,
        'itemPrice' => $itemPrice,
        'userName' =>$userName
	);

	$url = "https://localhost:444/microservice/bid";
	$method = "POST";
	 $curl = curl_init($url);

	    switch ($method)
	    {
	        case "POST":
	            
	            curl_setopt($curl, CURLOPT_POST, 1);

	            if ($data)
	                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "PUT":
	            curl_setopt($curl, CURLOPT_PUT, 1);
	            break;
	        default:
	            if ($data)
	                $url = sprintf("%s?%s", $url, http_build_query($data));
	    }
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    $result = curl_exec($curl);
	    $errmsg = curl_error($curl);
	    $status = $result['http_code'];
	    echo "200";
	    return $response->withStatus(200);
	    curl_close($curl);

}

function getMyBids(Request $request, Response $response ){
		$allGetVars = $request->getQueryParams();
		foreach($allGetVars as $key => $param){
			if($key == "username"){
					$username = $param;
			}
		}
		$url = "https://localhost:444/microservice/get_my_bids?username=".$username;
		$method = "GET";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		echo $status;
		curl_close($curl);
		
	}

	function DeleteBid(Request $request, Response $response)
	{
		$allGetVars = $request->getQueryParams();
		foreach($allGetVars as $key => $param){
			if($key == "username"){
					$username = $param;
			}
			elseif ($key == "itemid") {
					$itemid = $param;
			}
			elseif ($key == "itemprice") {
					$price = $param;
			}
		}
		$url = "https://localhost:444/microservice/delete_bid?username=".$username."&itemid=".$itemid."&itemprice=".$price;
		$method = "GET";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		echo $status;
		curl_close($curl);
		
	}

	function DeletePost(Request $request, Response $response)
	{
		$allGetVars = $request->getQueryParams();
		foreach($allGetVars as $key => $param){
			if($key == "username"){
					$username = $param;
			}
			elseif ($key == "itemid") {
					$itemid = $param;
			}
		}
		$url = "https://localhost:444/microservice/delete_post?username=".$username."&itemid=".$itemid;
		$method = "GET";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		echo $status;
		curl_close($curl);
		
	}

function editAccount(Request $request, Response $response){
	 $parseBody = $request->getParsedBody();
    $username = null;
    $fullname = null;
    $phone = null;
    $address = null;
    $email = null;
    $username2 = null;

	foreach($parseBody as $key => $param){
		//echo $param;
		if($key == "username")
		{
			$username = $param;	
			//echo $itemName."item name";
		}

		elseif ($key == "fullname") {

			$fullname = $param;
		}
		elseif ($key == "address") {

			$address = $param;
		}
		elseif ($key == "email") {

				$email = $param;
		}
		elseif($key == "phone"){
				$phone = $param;
		}
		elseif ($key == "usrname") {

				$username2 = $param;
		}
		//echo $param;
		elseif ($key == "phone") {

	}
}
	 $data = array(
       'username' => $username,
        'fullname' => $fullname,
        'address' => $address,
        'email' => $email,
        'phone' => $phone,
        'usrname' => $username2
		);

        $url = "https://localhost:444/microservice/edit_acc";
        $method = "POST";
        $curl = curl_init($url);
        switch ($method)
            {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);
                    if ($data)
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    break;
                case "PUT":
                    curl_setopt($curl, CURLOPT_PUT, 1);
                    break;
                default:
                    if (isset($data) && $data)
                        $url = sprintf("%s?%s", $url, http_build_query($data));
            }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_exec($curl);
        $result = curl_getinfo($curl);
        $status = $result['http_code'];
        echo $status;
        curl_close($curl);
}

function getAccount(Request $request, Response $response){
	$allGetVars = $request->getQueryParams();
		$userName = null;
		foreach($allGetVars as $key => $param){
			if($key == "userName"){
					$userName = $param;
			}
		}
		//echo $userName;
	$url = "https://localhost:444/microservice/account?userName=".$userName;
        $method = "GET";
        $curl = curl_init($url);
        switch ($method)
            {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);
                    if ($data)
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    break;
                case "PUT":
                    curl_setopt($curl, CURLOPT_PUT, 1);
                    break;
                default:
                    if (isset($data) && $data)
                        $url = sprintf("%s?%s", $url, http_build_query($data));
            }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_exec($curl);
        $result = curl_getinfo($curl);
        $status = $result['http_code'];
        echo $status;
        curl_close($curl);
	}


function getPosts(Request $request, Response $response){
		//echo "here";
		$allGetVars = $request->getQueryParams();
		$userName = null;
		foreach($allGetVars as $key => $param){
			if($key == "userName"){
					$userName = $param;
			}
		}
		$url = "https://localhost:444/microservice/get_posts?userName=".$userName;
		$method = "GET";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		echo $status;
		curl_close($curl);

	}
	function getBids(Request $request, Response $response){
		//echo "here";
		$allGetVars = $request->getQueryParams();
		$userName = null;
		foreach($allGetVars as $key => $param){
			if($key == "userName"){
					$userName = $param;
			}
		}
		$url = "https://localhost:444/microservice/get_bids?userName=".$userName;
		$method = "GET";
		$curl = curl_init($url);
		switch ($method)
		    {
		        case "POST":
		            curl_setopt($curl, CURLOPT_POST, 1);
		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
		        case "PUT":
		            curl_setopt($curl, CURLOPT_PUT, 1);
		            break;
		        default:
		            if (isset($data) && $data)
		                $url = sprintf("%s?%s", $url, http_build_query($data));
		    }
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_exec($curl);
		$result = curl_getinfo($curl);
		$status = $result['http_code'];
		echo $status;
		curl_close($curl);

	}




?>