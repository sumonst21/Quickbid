
<?php
session_start();
require_once 'Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '1919051931675180',
    'app_secret' => '04d66c49449e53569a19a9ccef3c7b27',
    'default_graph_version' => 'v2.4'
    ]);

$helper = $fb->getRedirectLoginHelper();
 $_SESSION['FBRLH_state']=$_GET['state'];
try {
  $accessToken = $helper->getAccessToken();
echo "Hi";
$servername = "localhost";
$dbname = "ebay";
$username = "root";
$password = "password";
#$cache = new Memcached;
#$cache->connect('localhost',11211);

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully \n"; 
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

 $username = 'sri21arun';
    $password = 'wpl';
    $fullname = 'Sridhar Arun';
    $email = 'sri21arun@gmail.com';
    $phone = '1224';
    $address = 'PPlace';
    $lastlogin = '2017-04-22 00:00:00';
    $location = 'Richardson';

$data = array(
        'username' =>$username,
        'password' => $password,
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'address' => $address
); 
//     $data = $conn->prepare("SELECT username FROM userprofile WHERE username=:username AND userpassword=:password");
// $data->bindParam("username", $username,PDO::PARAM_STR) ;
// $data->bindParam("password", $password,PDO::PARAM_STR);
// $data->execute();
// $count=$data->rowCount();
// $data2=$data->fetch(PDO::FETCH_OBJ);
// $db = null;
// if($count)
// {
// echo "We're Sorry!! User Already exists";
// }
//  else {
//     $query2 = "INSERT INTO userprofile(username,userpassword,fullname,email,phone,address,lastLogin,location) VALUES ('$username', '$password', '$fullname','$email',$phone,'$address','$lastlogin','$location')";
//      $conn->exec($query2);
// echo "You're now registered user";
// }

$url = "https://localhost:444/testapp/register";
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
    curl_exec($curl);
    $errmsg = curl_error($curl);
    $result = curl_getinfo($curl);
    $status = $result['http_code'];
    echo $status;
    curl_close($curl);
    $_SESSION['username']=$username;
 include('products.html');
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  //include('products.html');
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (isset($accessToken)) {
  // Logged in!
  $_SESSION['facebook_access_token'] = (string) $accessToken;

  // Now you can redirect to another page and use the
  // access token from $_SESSION['facebook_access_token']
} elseif ($helper->getError()) {
  // The user denied the request
  exit;
}

?>

