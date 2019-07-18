<?php

class GetNullObj {
  public function __get($prop){
    return $this->$prop ?? null;
  }

  public static function create($values){
      if (is_array($values) !== true) return $values;

      $o = new static();
      foreach($values as $key => $value){
          $o->$key = static::create($value);
      }
      return $o;
  }
}