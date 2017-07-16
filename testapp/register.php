<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App;
$app->post('/register', 'getDetails');

$app->run();

function connect(){

    $db = new PDO('mysql:host=localhost;dbname=ebay;charset=utf8mb4', 'root', 'password');
    return $db;
}


function getDetails (Request $request, Response $response){
    $username = null;
    $password = null;
    $fullname = null;
    $email = null;
    $phone = null;
    $address = null;
    $lastlogin = date("Y-m-d");
    $location = $geo["geoplugin_city"];
    $user_ip = getenv('REMOTE_ADDR');
    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
    $parseBody = $request->getParsedBody();
    foreach($parseBody as $key => $param){
        if($key == "username")
        {
            $username = $param; 
        }
        elseif ($key == "userpassword") {

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
$data = $conn->prepare("SELECT username FROM userprofile WHERE username=:username AND userpassword=:password");
$data->bindParam("username", $username,PDO::PARAM_STR) ;
$data->bindParam("password", $password,PDO::PARAM_STR);
$data->execute();
$count=$data->rowCount();
$data2=$data->fetch(PDO::FETCH_OBJ);
$db = null;
    if($count)
    {
    echo "We're Sorry!! User Already exists";
    }
     else {
            $query2 = "INSERT INTO userprofile(username,userpassword,fullname,email,phone,address,lastLogin,location) VALUES ('$username', '$password', '$fullname','$email',$phone,'$address','$lastlogin','$location')";
            $conn->exec($query2);
            echo "You're now registered user"; // take him to login page to login and so forth. 
           
    }

}
	
?>