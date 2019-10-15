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
 * Prefix every key of the array
 */
function prefix_array_keys(&$array, $prefix='') {
  if (empty($prefix)) return;

  foreach ($array as $key => $value) {
    $array[$prefix.$key] = $value;
    unset($array[$key]);
  }
}

/**
 * Creates an array with the amount of letters in the word. 
 */
function create_word_analysis($string) {
  $letter_array = array_fill(0, 26, 0);
  $string = strtolower($string);

  foreach(str_split($string) as $letter) {
    $index = ord($letter) - ord('a');

    if ($index > 0 && $index < 26) {
      $letter_array[$index]  += 1;
    }
  }

  return $letter_array;
}

function calculate_word_score($word, $search) {
  $word = strtolower($word);
  $search = strtolower($search);

  $word_length = strlen($word);
  $search_length = strlen($search)
  ;
  $word_analysis = create_word_analysis($word);
  $search_analysis = create_word_analysis($search);


  $length_difference = abs($word_length - $search_length);
  if ($word_length === $search_length) {
    $length_difference -= 2; 
  }
  
  $matches = 0;
  for($i = 0, $l = count($word_analysis); $i < $l; ++$i) {
    $matches += min($word_analysis[$i], $search_analysis[$i]);
  }

  $correct_positions = 0;
  for($i = 0; $i < $word_length && $i < $search_length; ++$i) {
    if ($word[$i] === $search[$i]) $correct_positions += 1;
  }

  return ($correct_positions*2) + $matches - $length_difference;
}