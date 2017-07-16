<?php
//define the memcached host and port to connect to
//phpinfo();
$memcache_host = '127.0.0.1';
$memcache_port = '11211';

//connect to memcached server
$memcache = new Memcache;
$is_cache = $memcache->connect($memcache_host, $memcache_port);


if($is_cache){

  $things = $memcache->get('things');

  if($things == null){ //if data hasn't been cached yet
      echo"hi";
      $memcache->set('things', 'hello', false, 86400); //cache the data
  }
  $things = $memcache->get('things');
  echo $things;
  //do something with the $things, output it, mess with it, whatever.
}
?>