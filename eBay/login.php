<?php
// 
session_start();
$data = array(
        'username' => $_POST['username'],
        'password' => $_POST['password']
);
    
    $url = "https://localhost:444/testapp/login";
    $url2 ="https://localhost:444/testapp/email";
    $method = "POST";
    $curl = curl_init($url);
    $curl2 = curl_init($url2);
    $method2 = "GET";

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
    switch ($method2)
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
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_exec($curl2);
    curl_exec($curl);
    $errmsg = curl_error($curl);
    $result = curl_getinfo($curl);
    $status = $result['http_code'];
    if($status == "200"){
        $_SESSION['username'] = $_POST['username'];
    }
     $date = date("Y-m-d h:m:s");
    $file = __FILE__;
    $level = "info";
    $destination = "logs.log";
    $message = "[{$date}] [{$file}] [{$level}] lLogin Successful".PHP_EOL;
    error_log($message,3,$destination);
    echo $status;

// log to our default location
 
    curl_close($curl);

   // echo "after";

?>