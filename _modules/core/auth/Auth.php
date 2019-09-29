<?php

/**
 * Class Auth <br>
 * Simple Authentication for user register and login.
 *
 * @author Ingo Andelhofs
 */
class Auth {
  // Crypto
  public static function hash_password(string $plain_password) : string {
    return password_hash($plain_password, PASSWORD_DEFAULT);
  }
  public static function verify_password(string $plain_password, string $hashed_password) : bool {
    return password_verify($plain_password, $hashed_password);
  }

  // Getters
  public static function get_user_session() : array {
    return $_SESSION['__auth_user'];
  }
  public static function is_user_logged_in() : bool {
    return isset($_SESSION['__auth_user']);
  }
  public static function is_user(string $type) : bool {
    return isset($_SESSION['__auth_user']['type']) ? $_SESSION['__auth_user']['type'] === $type : false;
  }

  // Setters
  public static function set_user_session(array $user_info) {
    $_SESSION['__auth_user'] = $user_info;
  }
};