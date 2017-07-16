<?php
session_start();
$data = array(
        'itemId' => $_POST['itemid'],
        'itemPrice' => $_POST['itemprice'],
        'userName' => $_SESSION['username']
);

$url = "https://localhost:444/testapp/bid";
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
    echo $status;
    curl_close($curl);


?>