<?php

namespace FlashPHP\core\validation;
use Closure;

/**
 * Class Validator <br>
 * A simple validation class for validating form data.
 *
 * @author Ingo Andelhofs
 */
class Validator {
  private $errors;

  // Fallback
  const REQUIRED = false;
  const MIN_LENGTH = 0;
  const MAX_LENGTH = 32768;
  const REGEX = '/[\w\W]*/';
  const REGEX_CHARS = '/[\w\W]*/';

  // Constructor
  public function __construct() {
    $this->errors = [];
  }


  // ----- PUBLIC -----
  public function validate_submit(array $post_data, array $form_data) {
    foreach($form_data as $name => $validation_params) {
      // TODO: Check for unset post values instead of $post_data[$name] ?? ''
      $value = $post_data[$name] ?? '';
      $validation_params = is_array($validation_params) ? $validation_params : ['regex' => $validation_params];

      $this->validate_single($name, $value, $validation_params);
    }

    return $this->errors;
  }

  // Getters
  public function get_errors() : array {
    return $this->errors;
  }
  public function has_errors() : bool {
    return !empty($this->errors);
  }

  // Printing
  public function simple_display_errors() {
    $this->foreach_error(function($error) { $error->display(); });
  }

  // Chaining functions
  public function foreach_error(Closure $callback) : Validator {
    foreach($this->errors as $error_name => &$validation_errors) {
      foreach($validation_errors as &$validation_error)
        $callback($validation_error);
    }
    return $this;
  }
  public function finally(Closure $callback) {
    $callback();
  }
  public function end(string $message = '') {
    (new Response())->end($message);
  }


  // ----- PRIVATE -----
  // Error storage
  private function new_error(string $name, string $error_message) {
    // TODO: unset in post

    if (!isset($this->errors[$name]))
      $this->errors[$name] = [];

    $this->errors[$name][] = new ValidationError($name, $error_message);
  }

  // Validation
  private function validate_single(string $name, string $value, array $validation_params) {
    $value = trim($value);

    $this->validate_length($name, $value, $validation_params);
    $this->validate_regex($name, $value, $validation_params);
  }
  private function validate_length(string $name, string $value, array $validation_params) {
    $min_length = $validation_params['min'] ?? self::MIN_LENGTH;
    $max_length = $validation_params['max'] ?? self::MAX_LENGTH;
    $required = $validation_params['required'] ?? self::REQUIRED;
    $required = ($min_length > 1) ? false : $required;
    $length = strlen($value);

    if ($required && empty($value))
      $this->new_error($name, "Field is required.");
    else if ($length < $min_length)
      $this->new_error($name, "Field must be at least $min_length characters long.");
    else if ($length > $max_length)
      $this->new_error($name, "Field must be at most $max_length characters long.");
  }
  private function validate_regex(string $name, string $value, array $validation_params) {
    $regex = $validation_params['regex'] ?? self::REGEX;
    $regex_chars = $validation_params['regex_chars'] ?? self::REGEX_CHARS;

    if (!$this->match_regex($regex, $value))
      $this->new_error($name, "Regex validation failed.");

    if (!$this->match_regex($regex_chars, $value))
      $this->new_error($name, "Regex chars validation failed.");
  }

  // Regex
  private function match_regex(string $pattern, string $str) : bool {
    return preg_match($pattern, $str, $matches) === 1 && $matches[0] === $str;
  }
};