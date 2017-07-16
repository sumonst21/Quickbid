
<?php
require_once 'Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '1919051931675180',
    'app_secret' => '04d66c49449e53569a19a9ccef3c7b27',
    'default_graph_version' => 'v2.4']);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'user_likes']; // optional
$loginUrl = $helper->getLoginUrl('https://localhost/eBay/login-callback.php', $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
echo '<br/>Browse Google to Know more about Bidding and Selling: <a href="http://www.google.com">Google!</a>';
?>

