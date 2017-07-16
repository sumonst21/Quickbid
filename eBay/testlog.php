<?php
	//echo "hi";
    $date = date("Y-m-d h:m:s");
    $file = __FILE__;
    $level = "warning";
    $destination = "logs.log";
    $message = "[{$date}] [{$file}] [{$level}] Put your message here".PHP_EOL;
// log to our default location
    error_log($message,3,$destination);
   // echo "after";
    ?>