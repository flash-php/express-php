<?php

/**
 * Include all files in a directory (not recursive)
 */
function include_all($path) {
  foreach (glob("$path/*") as $filename) {
    include_once $filename; 
  }
}
function include_all_r($path) {
  foreach(glob("$path/*") as $filename) {
    is_dir($filename) ? include_all_r($filename) : include_once($filename);
  }
}


/**
 * Convert a snake_case_string into a camelCaseString
 */
function snake_to_camel($input_str) {

}

/**
 * Convert a camelCaseString into a snake_case_string
 */
function camel_to_snake($input_str) {

}

/**
 * 
 */
function prefix_array_keys(&$array, $prefix='') {
  if (empty($prefix)) return;

  foreach ($array as $key => $value) {
    $array[$prefix.$key] = $value;
    unset($array[$key]);
  }
}