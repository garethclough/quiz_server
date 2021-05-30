<?php
  namespace Lib;

    class Database {
      private static $db = null;
      
      public static function init($settings)
      {
        self::$db = new \PDO("mysql:host=".$settings['dbhost'].";dbname=".$settings['dbname'], $settings['dbuser'], $settings['dbpassword'] );
      }
     
      public static function get()
      {
        return self::$db;
      }        
    
    }