<?php

class Model {
  // Variables
  private $singular_name;
  private $plural_name;
  private $restriction = null;
  
  private static $schemas_assoc_array;
  private static $table_names;
  private static $new_functions;

  // Constructor
  public function __construct($singular_name=null, $plural_name=null) {
    if (is_array($singular_name) && is_null($plural_name)) {
      $this->restriction = $singular_name;
    }
    else {
      $this->singular_name = $singular_name;
      $this->plural_name = $plural_name;
  
      self::$table_names[$plural_name] = $singular_name;

    }
  }

  // Functions
  public function schema($schema_assoc_array) {
    self::$schemas_assoc_array[$this->singular_name] = $schema_assoc_array;
    self::$schemas_assoc_array[$this->plural_name] = $schema_assoc_array;
  }

  public function __call($function_name, $function_arguments) {
    if (isset(self::$new_functions[$function_name])) {
      self::$new_functions[$function_name]($this, ...$function_arguments);
    }
    else {
      $this->run($function_name, ...$function_arguments);
    }
  }

  public function new($name, $callback) {
    self::$new_functions[$name] = $callback;
  }
  public function query($str) {
    echo "$str<br>";
  }

  private function run($model_func_name, $data=null, $options=[]) {
    $keywords = $this->splitOnUpperCase($model_func_name);
    $keyword_len = count($keywords);


    if ($keyword_len < 2) {
      echo 'ModelFunction Error: Too little params...';
    }
    else if($keyword_len === 2) {
      // Create Table
    }
    else if($keyword_len === 3) {
      // Exists Table Column
      // Get Table By
    }
    else if ($keyword_len === 4) {
      $action = $keywords[0];
      $table = $keywords[1];
      $attribute = strtolower($keywords[3]);
      $schemas = self::$schemas_assoc_array;

      if ($this->is_accessible($table) && isset($schemas[$table]) && array_key_exists($attribute, $schemas[$table])) {
        if ($action === 'get') {
          in_array($table, self::$table_names) ?
            $this->run_get_table_by_column($table, $attribute, $data, $options) :
            $this->run_get_tables_by_column(self::$table_names[$table], $attribute, $data, $options);
          return;
        }
        
        if ($action === 'update') {

          return;
        }

        if ($action === 'delete') {
          
          return;
        }
        



      }
      else { 
        $this->is_accessible($table) ? print('ModelFunction Error: Wrong TableName or Attribute.') : print('ModelFunction Error: No access.'); 
      }
    }
    else { echo 'ModelFunction Error: Too many params...'; }

  }

  private function run_get_table_by_column($table_name, $column_name, $column_value, $options) {
    $condition = is_array($column_value) ? $this->generateOr($column_value, $column_name) : "$column_name='$column_value'";
    echo "SELECT * FROM $table_name WHERE $condition LIMIT 1;";
  }
  
  private function run_get_tables_by_column($table_name, $column_name, $column_value, $options) {
    $condition = is_array($column_value) ? $this->generateOr($column_value, $column_name) : "$column_name='$column_value'";
    echo "SELECT * FROM $table_name WHERE $condition;";
  }

  private function generateOr($value_array, $column_name) {
    $str = '';
    foreach ($value_array as $value) {
      $str .= "$column_name='$value'";
      $str .= ' OR ';
    }

    return substr($str, 0, -4);
  }

  private function is_accessible($table_name) {
    return !(is_null($this->restriction) || !in_array($table_name, $this->restriction));
  }


  // Helper functions
  private function toCamelCase($input) {
    return str_replace('_', '', ucwords($input, '_'));
  }
  private function toSnakeCase($input) {
    $words = array_map('strtolower', $this->splitOnUpperCase($input));
    return join('_', $words);
  }
  private function splitOnUpperCase($input) {
    return preg_split('/(?=[A-Z])/',$input);
  }
};




class DataBase {
  // Variables
  protected $dbh = null;
  protected $db_schema = [];

  // Constructor
  public function __construct($driver=DB_DRIVER, $hostname=DB_HOST, $port=DB_PORT, $database_name=DB_NAME, $username=DB_USERNAME, $password=DB_PASSWORD) {
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
  }

  // Magic functions
  public function __call($function_name, $arguments) {
    if (substr($function_name, 0, 3) === 'get') {
      $function_name = substr($function_name, 3);
      $function_name_array = array_filter(explode('_', str_replace("By", "_", $function_name)));
      $fn_arr_len = count($function_name_array);

      if ($fn_arr_len === 1) {
        // TODO: Check in schema's
        $table = $function_name_array[0];
        return $this->getBy($table, $arguments[0]);
      }
      else if ($fn_arr_len === 2) {
        // TODO: Check in schema's
        $table = $function_name_array[0];
        $attributes = $function_name_array[1];
        $attribute_array = array_filter(explode('_', str_replace("And", "_", $attributes)));
        $attribute_array_assoc = [];
        
        for($i=0, $l=count($attribute_array); $i < $l; ++$i) {
          $current_attribute = strtolower($attribute_array[$i]);
          $attribute_array_assoc[$current_attribute] = $arguments[$i];
        }

        return $this->getBy($table, $attribute_array_assoc);
      }
      else {
        echo "Database Error: Unkown functioncall from $function_name.";
      }
    }

    // create
    // update
    // delete
  }

  // Functions
  public function query($statement, $parameters=[]) {

    // DEVELOPING
    echo $statement;
    echo '<pre>';
    print_r($parameters);
    echo '</pre>';
    return;
    // DEVELOPING


    $dbh = $this->dbh;
    $stmt = $dbh->prepare($statement);

    foreach(array_keys($parameters) as $name) {
      $data_type = $this->schema[$name] ?? PDO::PARAM_STR;
      $stmt->bindParam(":$name", $parameters[$name], $data_type);
    }
    
    $stmt->execute();

    if (strpos($statement, 'SELECT') !== false)
      return $stmt->fetchAll();

    // TODO: Use Transactions
    if (strpos($statement, 'INSERT') !== false)
      return $dbh->lastInsertId('id');

    return true;
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

    $this->prefix_array_keys($parameters, $prefix);

    return join($separator, $where_array);
  }

  // Global helper ???
  private function prefix_array_keys(&$array, $prefix='') {
    if ($prefix === '') return;

    foreach ($array as $k => $v) {
      $array[$prefix.$k] = $v;
      unset($array[$k]);
    }
  }
};