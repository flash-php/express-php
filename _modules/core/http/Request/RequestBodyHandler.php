<?php


class RequestBodyHandler extends AdvancedNullObject {
  // Constructor
  public function __construct() {
    parent::__construct($this->get_body_array());
  }
  private function &get_body_array() : array {
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        return $_GET;
        break;
      case 'POST':
        unset($_POST['REQUEST_METHOD']);
        return $_POST;
        break;
      case 'PUT':
        parse_str(file_get_contents('php://input'), $_PUT);
        return $_PUT;
        break;
      default:
        return [];
        break;
    }
  }

  // Functions
  public function validate(array $validation_config, Closure $error_callback = null, Closure $finally_callback = null) {
    $validator = new Validator();
    $validator->validate_submit($this->assoc_array, $validation_config);

    if (!is_null($error_callback) && $validator->has_errors())
      $validator->foreach_error($error_callback);

    if (!is_null($finally_callback))
      $finally_callback();

    return $validator;
  }
}