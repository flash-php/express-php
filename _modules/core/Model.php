<?php

class Model {
  // Variables
  private $singular_name;
  private $plural_name;
  private $restriction = null;
  
  private static $schemas_assoc_array;
  private static $table_names;

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
    $this->run($function_name, ...$function_arguments);
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

};