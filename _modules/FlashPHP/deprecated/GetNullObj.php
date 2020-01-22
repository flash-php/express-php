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




// TEST CASES
// $assoc_ref = ['test' => ['test' => 3]];
// $obj = new AdvancedNullObject($assoc_ref);
// $assoc2 = &$obj->get_array();
// $obj->test->more = 'testtttt2';
// print_r($obj->test->more); echo '<br>';
// print_r($obj->assoc_array); echo "<br>";
// print_r($assoc2); echo "<br>";
// print_r($assoc_ref); echo "<br>";