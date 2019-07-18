<?php

/**
 * Include all files in a directory (not recursive)
 */
function include_all($path) {
  foreach (glob("$path/*.php") as $filename)
    include $filename; 
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