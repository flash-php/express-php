<?php

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
        return $this->getBy($table, $arguments[0]);
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

        return $this->getBy($table, $attribute_array_assoc);
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

    if (strpos($statement, 'SELECT') !== false)
      return $stmt->fetchAll();

    if (strpos($statement, 'INSERT') !== false)
      return $dbh->lastInsertId('id');
    
    $dbh->commit(); // IMPORTANT FOR lastInsertedId()

    return true;
  }
  public static function new($name, $callback) {
    self::$new_functions[$name] = $callback;
  }


  // READ
  public function getBy($table, $parameters) {
    $where_str = $this->create_where_equals_str($parameters);
    return $this->query("SELECT * FROM $table WHERE $where_str;", $parameters);
  }
  public function select($table, $data, $where_array) {
    $where_str = join(" AND ", $where_array);
    return $this->query("SELECT * FROM $table WHERE $where_str;", $data);
  }

  // CREATE
  public function create($table, $insert_data) {
    $values = array_keys($insert_data);
    $prepared_values = ':'.join($values, ', :');
    $values = join($values, ', ');

    $this->query("INSERT INTO $table ($values) VALUES ($prepared_values);", $insert_data);
    return true;
  }

  // UPDATE
  public function update($table, $where_data, $new_data) {
    $new_data_str = $this->create_where_equals_str($new_data, ', ', 'new_');
    $where_str = $this->create_where_equals_str($where_data, ' AND ', 'where_');

    $data = array_merge($where_data, $new_data);

    return $this->query("UPDATE $table SET $new_data_str WHERE $where_str;", $data);
  }

  // DELETE
  public function delete($table, $where_data) {
    $where_str = $this->create_where_equals_str($where_data);
    return $this->query("DELETE FROM $table WHERE $where_str;", $where_data);
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

class DataBaseSchema {
  private static $database_schemas;

  public function __construct($table_name, $schema) {
    self::$database_schemas[$table_name] = $schema;
  }

  public static function export() {
    return self::$database_schemas;
    // Return: ['table_name' => ['col1' => PDO::PARAM_INT, ...]]
  }
};