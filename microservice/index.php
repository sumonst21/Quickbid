<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require_once('PHPMailer/PHPMailer-master/PHPMailerAutoload.php');
$app = new \Slim\App;
$app->post('/post_item', 'getEntry');
$app->post('/login','checkLogin');
$app->post('/checkuser', 'checkUserName');
$app->post('/checkusername', 'checkUser');
$app->post('/update_login', 'updateLastLogin');
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
$app->get('/email','getHighestBidder');


$app->run();

function connect(){

	$db = new PDO('mysql:host=localhost;dbname=ebay;charset=utf8mb4', 'root', 'password');
	return $db;
}

function getDetails (Request $request, Response $response){

	$externalContent = file_get_contents('http://checkip.dyndns.com/');
	preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/',
	$externalContent, $m);
	$ip = $m[1];
	$a = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
	$location= $a['geoplugin_city'];
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
   // $location = $geo["geoplugin_city"];
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

	 $conn = connect();
            $query2 = "INSERT INTO userprofile(username,userpassword,fullname,email,phone,address,lastLogin,location) VALUES ('".$username."', '".$password."', '".$fullname."','".$email."','".$phone."','".$address."','".$lastlogin."','".$location."')";
            $conn->exec($query2);
            return $response;
            
   // }

}
function checkUser(Request $request, Response $response){

	$parseBody = $request->getParsedBody();
	$username = null;
	foreach($parseBody as $key => $param){
		if($key == "username"){ 
			$username = $param;
			}
	}
	//echo $username;

	$conn = connect();
	$data = $conn->prepare("SELECT username FROM userprofile WHERE username=:username");
	$data->bindParam("username", $username, PDO::PARAM_STR) ;
	$data->execute();
	$count = $data->rowCount();
	$data2 = $data->fetch(PDO::FETCH_OBJ);
	$db = null;

	if($count)
	{
		return $response->withStatus(302);
	}
	else{
		
		return $response->withStatus(200);
	}
}
function checkUserName(Request $request, Response $response){

	$parseBody = $request->getParsedBody();
	$username2 = null;
    $userpassword2= null;
	foreach($parseBody as $key => $param){
		if($key == "username"){ 
			$username2 = $param;
			}
		if($key == "password"){
			$userpassword2 = $param;
		}

	}
	
	$conn = connect();
	$data = $conn->prepare("SELECT username FROM userprofile WHERE username=:username2 AND userpassword=:userpassword2");
	$data->bindParam("username2", $username2, PDO::PARAM_STR) ;
	$data->bindParam("userpassword2", $userpassword2, PDO::PARAM_STR);
	$data->execute();
	$count = $data->rowCount();
	$data2 = $data->fetch(PDO::FETCH_OBJ);
	$db = null;

	if($count)
	{
		return $response->withStatus(200);
	}
	else{
		
		return $response->withStatus(302);
	}
}
function updateLastLogin(Request $request, Response $response){
	//echo "hi";
	$parseBody = $request->getParsedBody();
	$username2 = null;
    $userpassword2= null;
	foreach($parseBody as $key => $param){
		//echo $key;
		if($key == "username"){ 
			$username2 = $param;
			}
		}
	$db = connect();
	//echo $username2."hello";
	//echo getUserId($username2);
	$stmt = $db->prepare("UPDATE userprofile SET lastlogin = NOW() where username='".$username2."'");
	// echo $getUserId($username);
	// $stmt = $db->prepare("UPDATE userprofile SET lastlogin = NOW() where userid=2");
	//$stmt->bindParam("userid", $username2,PDO::PARAM_STR) ;
	$s = $stmt->execute();
	//echo $s->errorCode();
	if($s){
	//$response = $response->write("Successful Login");
	return $response;
	}
	else{
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

		$postDB = insertItem($itemName, $itemDes, $price);
		
		insertPost($postDB,$shelfStart,$shelfEnd,$username);

		return $response->withStatus(200);
	}


	function insertItem($itemName, $itemDes, $price){
		try{
			
			$db = connect();
			$sql = "INSERT INTO item (itemname,itemprice,itemdescription) VALUES ('".$itemName."','".$price."','".$itemDes."');";
			//echo $sql;
			//echo $sql;
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		 	$db->exec($sql);	
		 	//echo "Record inserted";
		 	$db = connect();
		    $stmt = $db->prepare("Select itemid from item where itemName=:itemName;");
		    $stmt->bindParam("itemName",$itemName,PDO::PARAM_STR);
		    $stmt->execute();
		    //$itemId = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
		    foreach($stmt->fetchAll() as $k=>$v){
		     $itemId = $v["itemid"] ;
		    }
		    //echo $itemId["itemid"];
		    $db = null;
		 	return $itemId;

		}
		catch(PDOException $e)
		{
			return -1;
			//echo $sql . "<br>" . $e->getMessage();
		}
		$db = null;
	}	

	function insertPost($itemId,$shelfStart,$shelfEnd,$sellerId){
		$db = connect();
		$conn = connect();
		try{
			$data = $conn->prepare("SELECT userid FROM userprofile WHERE username=:username");
			$data->bindParam("username", $sellerId,PDO::PARAM_STR) ;
			$data->execute();
			foreach($data->fetchAll() as $k=>$v){
		     $sellerId = $v["userid"] ;
		    }
			
			$sql = "INSERT INTO post (itemid,sellerid,shelf_start,shelf_stop) VALUES ('".$itemId."','".$sellerId."','".$shelfStart."','".$shelfEnd."');";
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->exec($sql);
		//return 1;
		//echo "Items table updated";
		}
		catch(Exception $e) {
			//return -1;
    //echo 'Caught exception: ',  $e->getMessage(), "\n";
}
		


	}

	function getItems(Request $request, Response $response ){

		$db = connect();
		try{
		$rows = '{"items":[';
		$stmt = $db->prepare("SELECT i.itemname, i.itemdescription, i.itemprice , p.shelf_stop from item as i ,post as p where i.itemid = p.itemid");
		$stmt->execute();
		$count = $stmt->rowCount();
		$result = $stmt->fetchAll();
		if($count>0){
			foreach($result as $row){
   			$rows = $rows.'{"itemname":"'.$row['itemname'].'","itemdescription":"'.$row['itemdescription'].'","itemprice":"'.$row['itemprice'].'", "shelf_stop":"'.$row['shelf_stop'].'"}, ';
			}
			$newrows=rtrim($rows,", ");
			$rows = $newrows.']}';
			$response->write($rows."::");
		}
		}
		catch(Exception $e){
			echo "Caught exception", $e->getMessage(),"\n";

		}
		
		
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
		//echo $req->params("searchstring");
		// if (isset($_GET['searchstring']))
		// {
  //       	$test = $_GET('searchstring');
		//$paramValue = $request->get('searchstring');
		//$paramValue = $app->request()->get('searchstring');
			//echo $test;
		// }
		//$searchstring = $request->get("searchstring");
		//$criteria = $request->get("criteria");
		$db = connect();
		try{
		$rows = '{"items":[';
		if($criteria == "itemname"){
			$stmt = $db->prepare("SELECT i.itemid,i.itemname, i.itemdescription, i.itemprice , p.shelf_stop from item as i ,post as p where i.itemid = p.itemid and i.itemname  LIKE '%".$searchstring."'");
		}
		elseif ($criteria == "itemdescription") {
			$stmt = $db->prepare("SELECT i.itemid,i.itemname, i.itemdescription, i.itemprice , p.shelf_stop from item as i ,post as p where i.itemid = p.itemid and i.itemdescription  LIKE '%".$searchstring."'");
		}
		elseif ($criteria == "itemprice") {
			$stmt = $db->prepare("SELECT i.itemid,i.itemname, i.itemdescription, i.itemprice , p.shelf_stop from item as i ,post as p where i.itemid = p.itemid and i.itemprice  LIKE '%".$searchstring."'");
		}
		elseif ($criteria == "shelf_stop") {
			$stmt = $db->prepare("SELECT i.itemid,i.itemname, i.itemdescription, i.itemprice , p.shelf_stop from item as i ,post as p where i.itemid = p.itemid and p.shelf_stop  LIKE '%".$searchstring."'");
		}
		$stmt->execute();
		$count = $stmt->rowCount();
		$result = $stmt->fetchAll();
		if($count>0){
			foreach($result as $row){
   			$rows = $rows.'{"itemid":"'.$row['itemid'].'","itemname":"'.$row['itemname'].'","itemdescription":"'.$row['itemdescription'].'","itemprice":"'.$row['itemprice'].'", "shelf_stop":"'.$row['shelf_stop'].'"}, ';
			}
			$newrows=rtrim($rows,", ");
			$rows = $newrows.']}';
			$response->write($rows."::");
		}
		}
		catch(Exception $e){
			echo "Caught exception", $e->getMessage(),"\n";

		}
		
	}

	function getItemsSearchID(Request $request, Response $response ){
		$allGetVars = $request->getQueryParams();
		foreach($allGetVars as $key => $param){
			if($key == "productID"){
					$productID = $param;
			}
		}
		$db = connect();
		try{
		$rows = '';
		$stmt = $db->prepare("SELECT i.itemname, i.itemdescription, i.itemprice , p.shelf_stop from item as i ,post as p where i.itemid = p.itemid and i.itemid= '".$productID."'");
		$stmt->execute();
		$count = $stmt->rowCount();
		$result = $stmt->fetchAll();
		if($count>0){
			foreach($result as $row){
   			$rows = $rows.'{"itemname":"'.$row['itemname'].'","itemdescription":"'.$row['itemdescription'].'","itemprice":"'.$row['itemprice'].'", "shelf_stop":"'.$row['shelf_stop'].'"}';
			}
			//ewrows=rtrim($rows,", ");
			//$rows = $newrows.']}';
			$response->write($rows."::");
		}
		}
		catch(Exception $e){
			echo "Caught exception", $e->getMessage(),"\n";

		}
		
		
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
			$userName = $param;
		}
		//echo $param;
	}
	//echo $itemPrice;
	 try{
	 	//echo "hi";
		$db = connect();
		$userId = null;
		//echo $userName;
		$stmt = $db->prepare("SELECT userid FROM userprofile WHERE username='".$userName."'");
		//$stmt->bindParam("username", $userName,PDO::PARAM_STR) ;
		$stmt->execute();
		foreach($stmt->fetchAll() as $k=>$v){
		     $userId = $v["userid"] ;
		    }
		//echo $userId;
		// $data = $stmt->fetch(PDO::FETCH_OBJ);
		$db = null;
		$conn = connect();
		$sql = "INSERT INTO bid (itemid,userid,price) VALUES ('".$itemId."','".$userId."','".$itemPrice."');";
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->exec($sql);
		return $response->withStatus(200);
	 }
	 catch(Exception $e)
	 {
	 	echo "Caught exception", $e->getMessage(),"\n";
	 }

}

function getMyBids(Request $request, Response $response ){
		$allGetVars = $request->getQueryParams();
		foreach($allGetVars as $key => $param){
			if($key == "username"){
					$username = $param;
			}
		}
		$db = connect();
		try{
		$rows = '{"items":[';
		$stmt = $db->prepare("SELECT i.itemid, i.itemname,  b.price ,p.shelf_stop  from item as i ,bid as b, userprofile as u, post as p where i.itemid = b.itemid and i.itemid = p.itemid and b.userid = u.userid and u.username ='".$username."'");
		$stmt->execute();
		$count = $stmt->rowCount();
		$result = $stmt->fetchAll();
		if($count>0){
			foreach($result as $row){
			$today = $expiry_date = date("Y-m-d", strtotime("now"));
			if ($row['shelf_stop'] < $today)
			{
				$status = "expired";
			}
			else{
				$status = "active";
			}

   			$rows = $rows.'{"itemid":"'.$row['itemid'].'","itemname":"'.$row['itemname'].'","itemprice":"'.$row['price'].'","status":"'.$status.'"}, ';
			}
			$newrows=rtrim($rows,", ");
			$rows = $newrows.']}';
			$response->write($rows."::");
		}
	}
		catch(Exception $e){
			echo "Caught exception", $e->getMessage(),"\n";

		}
		
		
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
	$db = connect();
		$userId = null;
		//echo $userName;
		$stmt = $db->prepare("SELECT userid FROM userprofile WHERE username='".$username."'");
		//$stmt->bindParam("username", $userName,PDO::PARAM_STR) ;
		$stmt->execute();
		foreach($stmt->fetchAll() as $k=>$v){
		     $userId = $v["userid"] ;
		    }
		//echo $userId;
		// $data = $stmt->fetch(PDO::FETCH_OBJ);
		 $db = null;
	try {
    $conn = connect();
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // sql to delete a record
    $sql = "DELETE FROM bid WHERE itemid='".$itemid."' and userid = '".$userId."' and price='".$price."' ";

    // use exec() because no results are returned
    $conn->exec($sql);
   //echo "Record deleted successfully";
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

$conn = null;
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
	$db = connect();
		$userId = null;
		//echo $userName;
		$stmt = $db->prepare("SELECT userid FROM userprofile WHERE username='".$username."'");
		//$stmt->bindParam("username", $userName,PDO::PARAM_STR) ;
		$stmt->execute();
		foreach($stmt->fetchAll() as $k=>$v){
		     $userId = $v["userid"] ;
		    }
		//echo $userId;
		// $data = $stmt->fetch(PDO::FETCH_OBJ);
		 $db = null;
	try {
    $conn = connect();
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // sql to delete a record
    $sql = "DELETE FROM post WHERE itemid='".$itemid."' and sellerid = '".$userId."' ";

    // use exec() because no results are returned
    $conn->exec($sql);
   //echo "Record deleted successfully";
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

$conn = null;
	}

function getUserId($username){
		$conn = connect();
		$userid = null;
		try{
			$data = $conn->prepare("SELECT userid FROM userprofile WHERE username='".$username."'");
			//$data->bindParam("username", $username,PDO::PARAM_STR) ;
			$data->execute();
			foreach($data->fetchAll() as $k=>$v){
		     $userid = $v["userid"] ;
		   }
		  return $userid;
		}
		catch(PDOException $e){
		}
}

function editAccount(Request $request, Response $response){
	 $parseBody = $request->getParsedBody();
    $username = null;
    $fullname = null;
    $phone = null;
    $address = null;
    $email = null;
    $username2 = null;
   // echo "here";
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
		elseif ($key == "phone") {

				$phone = $param;
		}
		elseif ($key == "usrname") {

				$username2 = $param;
		}
		
	}
	try{

		$db = connect();
		$conn = connect();

		$data = $conn->prepare("SELECT userid FROM userprofile WHERE username=:username");
		$data->bindParam("username", $username2,PDO::PARAM_STR) ;
		$data->execute();
		$sellerId = null;
		
		foreach($data->fetchAll() as $k=>$v){
		     $sellerId = $v["userid"] ;
		    }
		//echo $sellerId."herrrrreeee";
		$sql = "UPDATE userprofile SET username='".$username."',fullname='".$fullname."',email='".$email."',phone='".$phone."',address='".$address."' where userid='".$sellerId."'";
		//echo $sql;
		$stmt = $db->prepare($sql);
		
		
		if($stmt->execute())
		{
			return $response;
		}
		else{

			return $response->withStatus(302);
		}
		

	}
	catch(PDOException $e)
		{
			echo $sql . "<br>" . $e->getMessage();
		}


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
	$db = connect();
	$stmt = $db->prepare("SELECT * from userprofile where username=:username;");
	$stmt->bindParam("username", $userName,PDO::PARAM_STR) ;
	$stmt->execute();
	$count=$stmt->rowCount();
	//echo $count;
	$result = $stmt->fetchAll();
	if($count > 0){
		foreach($result as $row){
			//echo $row;
			$rows = '{"username":"'.$row['username'].'","userpassword":"'.$row['userpassword'].'","fullname":"'.$row['fullname'].'","email":"'.$row['email'].'","address":"'.$row['address'].'","phone":"'.$row['phone'].'","lastlogin":"'.$row['lastLogin'].'","location":"'.$row['location'].'"}';
			//$rows[] = $row;
 		}
 		//$data = json_encode($rows);
 		//echo $rows;
 		return $response->write($rows."::");
	}
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
		$db = connect();
		$userId = null;
		$stmt = $db->prepare("SELECT userid FROM userprofile WHERE username='".$userName."'");
		$stmt->execute();
		foreach($stmt->fetchAll() as $k=>$v){
		     $userId = $v["userid"];
		    }
		   // echo $userId;
		$rows = '{"items":[';
		$stmt = $db->prepare("SELECT i.itemid, i.itemname, i.itemdescription, i.itemprice , p.shelf_stop, p.shelf_start from item as i ,post as p where i.itemid = p.itemid and p.sellerid= '".$userId."'");
		$stmt->execute();
		$count = $stmt->rowCount();
		$result = $stmt->fetchAll();
		if($count>0){
			foreach($result as $row){
   			$rows = $rows.'{"itemid":"'.$row['itemid'].'","itemname":"'.$row['itemname'].'","itemdescription":"'.$row['itemdescription'].'","itemprice":"'.$row['itemprice'].'", "shelf_stop":"'.$row['shelf_stop'].'","shelf_start":"'.$row['shelf_start'].'"}, ';
			}
			$newrows=rtrim($rows,", ");
			$rows = $newrows.']}';
			$response->write($rows."::");
		}

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
		$db = connect();
		$userId = null;
		$stmt = $db->prepare("SELECT userid FROM userprofile WHERE username='".$userName."'");
		$stmt->execute();
		foreach($stmt->fetchAll() as $k=>$v){
		     $userId = $v["userid"];
		    }
		   // echo $userId;

		$rows = '{"items":[';
		$stmt = $db->prepare("SELECT itemid from post where sellerid = '".$userId."'");
		$stmt->execute();
		$count = $stmt->rowCount();
		$result = $stmt->fetchAll();
		if($count>0){
			foreach($result as $row){
				$itemId = $row['itemid'];
				$stmt1 = $db->prepare("SELECT i.itemname, i.itemdescription, i.itemprice , u.fullname, b.price from item as i ,bid as b,userprofile as u where i.itemid = b.itemid and b.userid = u.userid and b.itemid= '".$itemId."'");
				$stmt1->execute();
				$counts= $stmt1->rowCount();
				$results= $stmt1->fetchAll();
				if($counts>0){
					foreach($results as $rowss){
						$rows = $rows.'{"itemname":"'.$rowss['itemname'].'","itemdescription":"'.$rowss['itemdescription'].'","itemprice":"'.$rowss['itemprice'].'", "sellerid":"'.$rowss['fullname'].'","bid":"'.$rowss['price'].'"}, ';
					}
				$newrows=rtrim($rows,", ");
				$rows = $newrows.']}';
				$response->write($rows."::");
						}
		
				}
		}

	}

function getHighestBidder(){
  $curr_Date = date("Y-m-d");
 // echo $curr_Date;
  $db = connect();
  $conn = connect();
  

  // try{
  $statement = "SELECT i.itemname, p.itemid, b.userid,p.sellerid, max(b.price)as mprice from post as p, bid as b, item as i where i.itemid = p.itemid and p.itemid = b.itemid and  p.shelf_stop = '".$curr_Date."' group by p.itemid  ;";
  //echo $statement;
   $sql = $db->prepare($statement);
    $sql->bindParam("curr_Date", $curr_Date,PDO::PARAM_STR) ;
    $sql->execute();
   $count = $sql->rowCount();
   $data = $sql->fetchAll();
  // echo $count."hiiiii";
    if($count>0)
    { //echo "here".$count;
    	foreach($data as $k=>$v){
    		
    		receipt($v['itemid'],$v['userid'],$v['mprice'],$v['sellerid'],$v['itemname']);
         }
    	
      
        }

    }

function receipt($ids,$user_id,$price,$sellerid,$itemname){
  try{
  $db = connect();
 // echo "we are here";
  $seller_email = null;
  $buyer_email = null;
  $stmt1 = $db->prepare("SELECT email from userprofile where userid =:user_id ;");
  $stmt1->bindParam("user_id", $user_id,PDO::PARAM_STR);
  $stmt1->execute();
  $data = $stmt1->fetchAll();
  foreach($data as $rows){
    $seller_email = $rows['email'];
  }
  $stmt2 = $db->prepare("SELECT email from userprofile where userid =:sellerid ;");
  $stmt2->bindParam("sellerid", $sellerid,PDO::PARAM_STR);
  $stmt2->execute();
  $data1 = $stmt2->fetchAll();
  foreach($data1 as $rows){
    $buyer_email = $rows['email'];
  }
 // echo $seller_email."<<<<>>>>".$buyer_email;
 sendMail($seller_email,$buyer_email,$price,$itemname);
}
catch(Exception $e){
 // echo "Error message".$e->getMessage();
}
}



function sendMail($seller_email,$buyer_email,$price,$itemname){
    $mail1 = $seller_email;
    //$mail1 = "vinayak.0792@gmail.com";
    $mail2 = $buyer_email;
    //$mail2 = "sri21arun@gmail.com";
    $mailSub = 'Notification for '.$itemname;
    $mailMsg = 'This is to notify that the product is sold for a price of'.$price.' the item is on its way';
 //  require 'PHPMailer/PHPMailer-master/PHPMailerAutoload.php';
   $mail = new PHPMailer();
   $mail ->IsSmtp();
   $mail ->SMTPDebug = 0;
   $mail ->SMTPAuth = true;
   $mail ->SMTPSecure = 'ssl';
   $mail ->Host = "smtp.gmail.com";
   $mail ->Port = 465; // or 587
   $mail ->IsHTML(true);
   $mail ->Username = "ebaymodulo3@gmail.com";
   $mail ->Password = "wplmodulo3";
   $mail ->SetFrom("ebaymodulo3@gmail.com");
   $mail ->Subject = $mailSub;
   $mail ->Body = $mailMsg;
   $mail ->AddAddress($mail1);
   $mail ->AddAddress($mail2);
   if(!$mail->Send())
   {
      // echo "Mail Not Sent";
   }
   else
   {
     //  echo "Mail Sent";
   }
}

?>