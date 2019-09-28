<?php

class Config {
  public static function default($name, $value) {
    defined($name) or define($name, $value);
  }

  public static function set($name, $value) {
    define($name, $value);
  }
}