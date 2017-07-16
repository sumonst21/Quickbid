function checkCache($username){
	echo $username;
	$memcache_host = '127.0.0.1';
	$memcache_port = '11211';
	$memcache = new Memcache;
	$is_cache = $memcache->connect($memcache_host, $memcache_port);


	if($is_cache){
	//$uname = $memcache->get('hi');
	  $uname = $memcache->get("'".$username."'");
	  if($uname == null)
	  { //if data hasn't been cached yet
	      //cache miss'
		 $date = date("Y-m-d h:m:s");
	    $file = __FILE__;
	    $level = "info";
	    $destination = "logs.log";
	    $message = "[{$date}] [{$file}] [{$level}] cache miss".PHP_EOL;
	// log to our default location
	    error_log($message,3,$destination);
	      $memcache->set("'".$username."'", "'".$username."'", false, 86400); //cache the data
	  }
	  else{
	  	//cache hit
	  	$date = date("Y-m-d h:m:s");
	    $file = __FILE__;
	    $level = "info";
	    $destination = "logs.log";
	    $message = "[{$date}] [{$file}] [{$level}] cache hit".PHP_EOL;
	// log to our default location
	    error_log($message,3,$destination);
	  }
	  //do something with the $things, output it, mess with it, whatever.
	}	
}