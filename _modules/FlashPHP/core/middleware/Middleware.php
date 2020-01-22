<?php

namespace FlashPHP\core\middleware;

class Middleware {
  public static function next() {
    return true;
  }
  public static function block() {
    return false;
  }
}