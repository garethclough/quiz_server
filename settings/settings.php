<?php

if (!isset($_SERVER['SERVER_NAME']) || stripos($_SERVER['SERVER_NAME'],"localhost") !== false) {
    return [
      "dbhost" => "localhost",
      "dbname" => "quiz",
      "dbuser" => "root",
      "dbpassword" => ""
    ];
} else {
    return [
      "dbhost" => "localhost",
      "dbname" => "quiz",
      "dbuser" => "quiz",
      "dbpassword" => "gRNiDjjn6XiblZLL"
    ];    
}