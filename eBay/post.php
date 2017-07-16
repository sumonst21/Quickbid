<?php

session_start();
if(isset($_SESSION['username']) && !empty($_SESSION['username'])){

$data = array(
       'iname' => $_POST['iname'],
        'iprice' => $_POST['iprice'],
        'idesc' => $_POST['idesc'],
        'sstart' => $_POST['sstart'],
        'sstop' => $_POST['sstop'],
        'username' => $_SESSION['username']
);
//echo $data['sstart'];
$url = "https://localhost:444/testapp/post_item";
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
$result = curl_getinfo($curl);
$status = $result['http_code'];

echo $status;
curl_close($curl);

}
else{
	echo "baah";
}
?>
