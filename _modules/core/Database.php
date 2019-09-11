<?php

/**
 * Database Class
 * @author Ingo Andelhofs
 * 
 * A MySQL Database class that makes it easier to query, get, create, update, delete, duplicate and more.
 * 
 * @uses Constants -> (DB_DRIVER, DB_HOST, DB_PORT, DB_NAME, DB_USERNAME, DB_PASSWORD)
 * @uses DataBaseSchema class
 * @uses PDO class
 */
class DataBase {
  // Variables
  protected $dbh = null;
  protected $db_schemas = [];
  private static $new_functions;
  private static $database_schemas;

  // Constructor
  public function __construct($driver=DB_DRIVER, $hostname=DB_HOST, $port=DB_PORT, $database_name=DB_NAME, $username=DB_USERNAME, $password=DB_PASSWORD) {
    // Create database connection
    $dsn = "$driver:host=$hostname;";
    $dsn .= empty($port) ? '' : "port=$port;";
    $dsn .= empty($database_name) ? '' : "dbname=$database_name;";

    $options = [
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    try {
      $this->dbh = new PDO($dsn, $username, $password, $options);
    }
    catch(PDOException $error) {
      $error_msg = $error->getMessage();
      error_log($error);
      die("Database Error: $error_msg <br>");
    }

    // Import db schema's
    $this->import_schemas();
  }

  // Magic functions
  public function __call($function_name, $arguments) {
    // all
    if (isset(self::$new_functions[$function_name])) {
      $callback = &self::$new_functions[$function_name];
      $callback($this, ...$arguments);
      return;
    }

    // read
    if (substr($function_name, 0, 3) === 'get') {
      $function_name = substr($function_name, 3);
      $function_name_array = array_filter(explode('_', str_replace("By", "_", $function_name)));
      $fn_arr_len = count($function_name_array);

      if ($fn_arr_len === 1) { //Example: getUserBy(['id' => 3]);
        // TODO: Check in schema's
        $table = $function_name_array[0];
        return $this->get($table, $arguments[0]);
      }
      else if ($fn_arr_len === 2) { // Example: getUserById(3);
        // TODO: Check in schema's
        $table = $function_name_array[0];
        $attributes = $function_name_array[1];
        $attribute_array = array_filter(explode('_', str_replace("And", "_", $attributes)));
        $attribute_array_assoc = [];
        
        // Example: getUserByIdAndFirstname(5, 'Ingo');
        for($i=0, $l=count($attribute_array); $i < $l; ++$i) {
          $current_attribute = strtolower($attribute_array[$i]);
          $attribute_array_assoc[$current_attribute] = $arguments[$i];
        }

        return $this->get($table, $attribute_array_assoc);
      }
      else {
        echo "Database Error: Unkown functioncall from $function_name.";
      }

      return;
    }

    // create
    if (substr($function_name, 0, 6) === 'create') {
      
    }

    // update
    // delete
  }

  // Functions
  public function query($statement, $parameters=[]) {

    // DEVELOPING
    echo '<br><br>';
    echo $statement;
    echo '<pre>';
    print_r($parameters);
    echo '</pre>';
    return;
    // DEVELOPING


    $dbh = $this->dbh;
    $dbh->beginTransaction(); // IMPORTANT FOR lastInsertedId()

    $stmt = $dbh->prepare($statement);

    foreach(array_keys($parameters) as $name) {
      $data_type = $this->schema[$name] ?? PDO::PARAM_STR;
      $stmt->bindParam(":$name", $parameters[$name], $data_type);
    }
    
    $stmt->execute();

    if (strpos($statement, 'SELECT') !== false) {
      $results = $stmt->fetchAll();
      $dbh->commit(); // IMPORTANT (end the transaction)
      return $results;
    }
    
    if (strpos($statement, 'INSERT') !== false) {
      $id = $dbh->lastInsertId('id');
      $dbh->commit(); // IMPORTANT FOR lastInsertedId()
      return $id;
    }
    
    $dbh->commit(); // IMPORTANT (end the transaction)
    return true;
  }
  public static function new($name, $callback) {
    self::$new_functions[$name] = $callback;
  }


  // Advanced queries
  public function get($table_name, $where_assoc) {
    $where_str = $this->create_where_equals_str($where_assoc);
    return $this->query("SELECT * FROM $table_name WHERE $where_str;", $where_assoc);
  }
  
  public function create($table_name, $insert_assoc) {
    $values = array_keys($insert_assoc);
    $prepared_values = ':'.join($values, ', :');
    $values = join($values, ', ');

    return $this->query("INSERT INTO $table_name ($values) VALUES ($prepared_values);", $insert_assoc);
  }

  public function update($table_name, $where_assoc, $updated_assoc) {
    $updated_str = $this->create_where_equals_str($updated_assoc, ', ', 'updated_');
    $where_str = $this->create_where_equals_str($where_assoc, ' AND ', 'where_');

    $where_and_updated_assoc = array_merge($where_assoc, $updated_assoc);

    return $this->query("UPDATE $table_name SET $updated_str WHERE $where_str;", $where_and_updated_assoc);
  }
  
  public function delete($table_name, $where_assoc) {
    $where_str = $this->create_where_equals_str($where_assoc);
    return $this->query("DELETE FROM $table_name WHERE $where_str;", $where_assoc);
  }

  public function exists($table_name, $where_assoc) {
    $results = $this->get($table_name, $where_assoc);
    return !empty($results);
  }

  public function duplicate($table_name, $where_assoc, $primary_keys = ['id']) {
    $result = $this->get($table_name, $where_assoc);

    if (empty($result)) return -1;
    $insert_assoc = $result[0];
    foreach ($primary_keys as $key) {
      if (isset($insert_assoc[$key])) unset($insert_assoc[$key]);
    }
    return $this->create($table_name, $insert_assoc);
  }


  
  // Helper functions
  private function create_where_equals_str(&$parameters, $separator=' AND ', $prefix='') {
    // Create string id=:id AND 
    $where_array = [];
    foreach(array_keys($parameters) as $name) {
      if (is_array($parameters[$name])) {
        // Create where_str for query
        $attr_array = $parameters[$name];
        $where_part_array = [];
        $where_str = '(';
        for($i = 0, $l = count($attr_array); $i < $l; ++$i) {
          $where_part_array[] = "$name=:{$prefix}{$name}_{$i}";
          
          // Fix parameters array (flatten)
          $parameters["{$prefix}{$name}_{$i}"] = $attr_array[$i];
        }
        unset($parameters[$name]);
        $where_str .= join($where_part_array, ' OR ') . ')'; 
        $where_array[] = $where_str;
      }
      else {
        $where_array[] = "$name=:{$prefix}{$name}"; 
      }
    }

    prefix_array_keys($parameters, $prefix);

    return join($separator, $where_array);
  }
  private function import_schemas() {
    self::$database_schemas = DataBaseSchema::export();
  }
};


/**
 * DataBaseSchema Class
 * @author Ingo Andelhofs
 * 
 * A simple class that makes it possible to create simple database schema's. 
 * You can export them to your Database class.
 */
class DataBaseSchema {
  private static $database_schemas;

  public function __construct($table_name, $schema) {
    self::$database_schemas[$table_name] = $schema;
  }

  public static function export() {
    // Return: ['table_name' => ['col1' => PDO::PARAM_INT, ...]]
    return self::$database_schemas;
  }
};