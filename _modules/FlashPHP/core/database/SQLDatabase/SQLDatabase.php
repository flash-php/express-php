<?php

namespace FlashPHP\core\database\SQLDatabase;

class SQLDatabase {
  // Member variables
  private $database_name;
  private $database_driver;
  private $database_username;
  private $database_password;
  private $database_port;
  private $database_host;

  private static $database_instance = null;

  private function __construct() {}

  public static function get_database_instance() {
    if (is_null(self::$database_instance)) {
      self::$database_instance = new SQLDatabase();
    }

    return self::$database_instance;
  }


  public function generateWhere() : string {

  }

  public function generateOptions() : string {

  }
}