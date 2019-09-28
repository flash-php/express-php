<?php

class AuthMiddleware {
  public static function is_user_logged_in($name='') {
    // Middleware
    return function() use($name) {
      if ($name === 'a') return true; // Middleware::NEXT()
      return false; // Middleware::BLOCK()
    };
  }
};