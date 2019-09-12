<?php

class GetNullObj {
  public function __get($prop) {
    return $this->$prop ?? null;
  }

  public static function create($values) {
      if (is_array($values) !== true) return $values;

      $o = new static();
      foreach($values as $key => $value) { $o->$key = static::create($value); }
      return $o;
  }
}


class AdvancedNullObject {
  public $assoc_array;

  public function __debugInfo() {
    return $this->assoc_array;
  }

  public function __construct(&$assoc_array = null) {
    $this->assoc_array = &$assoc_array;
  }

  public function __get($key) {
    $value = &$this->assoc_array[$key] ?? null;
    return (is_array($value) && array_values($value) !== $value) ? new AdvancedNullObject($value) : $value;
  }

  public function __set($key, $value) {
    $this->assoc_array[$key] = $value;
  }

  public function &get_array() {
    return $this->assoc_array;
  }
};

// TEST CASES
// $assoc_ref = ['test' => ['test' => 3]];
// $obj = new AdvancedNullObject($assoc_ref);
// $assoc2 = &$obj->get_array();
// $obj->test->more = 'testtttt2';
// print_r($obj->test->more); echo '<br>';
// print_r($obj->assoc_array); echo "<br>";
// print_r($assoc2); echo "<br>";
// print_r($assoc_ref); echo "<br>";