<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'vendor/autoload.php';

$app = new \Slim\App;
$app->post('/login',"checkLogin");

$app->run();


function connect()
{
	$db = new PDO('mysql:host=localhost;dbname=ebay;charset=utf8mb4', 'root', 'password');
	return $db;
}


function checkLogin(Request $request, Response $response){

	session_start();
	$parseBody = $request->getParsedBody();
	$username2 = null;
    $userpassword2= null;
	foreach($parseBody as $key => $param){
		if($key == "username"){ 
			$username2 = $param;
			}
		if($key == "userpassword2"){
			$userpassword2 = $param;
		}

	}

	$conn = connect();
	$data = $conn->prepare("SELECT username FROM userprofile WHERE username=:username2 AND userpassword=:userpassword2");
	$data->bindParam("username2", $username2,PDO::PARAM_STR) ;
	$data->bindParam("userpassword2", $userpassword2,PDO::PARAM_STR);
	$data->execute();
	$count = $data->rowCount();
	$data2 = $data->fetch(PDO::FETCH_OBJ);
	$db = null;

	if($count)
	{
	$_SESSION["username"]=$username;// Storing user session value
	echo "Welcome ".$username2; //Here we need to redirect it to Welcome page and add micro server redirection here
	return true;
	}
	else
	{
		echo "You're not registered yet";
	return false;
	}

}    
   

 
?>