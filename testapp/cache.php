


6379


<?php
require './vendor/autoload.php';
Predis\Autoloader::register();
$client = new Predis\Client();
$client->set('foo','bar');
$value = $client->get('foo');
echo $value;
?>

