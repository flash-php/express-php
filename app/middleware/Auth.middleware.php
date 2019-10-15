<?php

use FlashPHP\core\middleware\Middleware;

class AuthMiddleware {
  public static function is_user_logged_in($name='') {
    return function() use($name) {
      if ($name === 'a') return Middleware::next();
      return Middleware::block();
    };
  }
};