<?php

    
        session_start();
        $data = array(
       'username' => $_POST['username'],
        'fullname' => $_POST['fullname'],
        'address' => $_POST['address'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'usrname' => $_SESSION['username']
);

        $url = "https://localhost:444/testapp/edit_acc";
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

?>