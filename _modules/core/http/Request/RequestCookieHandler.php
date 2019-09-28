<?php


class RequestCookieHandler extends AdvancedNullObject {
  // Constructor
  public function __construct() {
    parent::__construct($this->get_cookie_array());
  }
  private function &get_cookie_array() {
    return $_COOKIE;
  }

  public function __set($key, $value) {
    setcookie($key, $value, strtotime("+1 month"));
  }

  // Functions
  public function set(string $key, string $value, string $expires="+1 month", bool $secure=true, bool $httponly=true) {
    setcookie($key, $value, strtotime($expires), '', '', $secure, $httponly);
  }
}