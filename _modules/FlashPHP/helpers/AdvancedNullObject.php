<?php

namespace FlashPHP\helpers;

/**
 * Class AdvancedNullObject <br>
 * Creates an object from a given array. Invalid access returns NULL;
 *
 * @author Ingo Andelhofs
 */
class AdvancedNullObject {
  protected $assoc_array;

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