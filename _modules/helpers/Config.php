<?php

class Config {
  private static $configurations;

  // Constants
  public static function default($name, $value) {
    defined($name) or define($name, $value);
  }
  public static function set($name, $value) {
    define($name, $value);
  }

  // Object Constants
  public static function default_obj($name, $value) {
    self::$configurations[$name]['default'] = $value;
  }
  public static function set_obj($name, $value) {
    self::$configurations[$name]['set'] = $value;
  }

  public static function __callStatic($name, $arguments) {
    if (!isset(self::$configurations[$name])) return null;
    return self::$configurations[$name]['set'] ?? self::$configurations[$name]['default'];
  }
}