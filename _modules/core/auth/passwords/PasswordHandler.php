<?php

class PasswordHandler {
  private $password_strategies = [];

  // Constructor
  public function __construct(string $name = '', PasswordStrategy $passwordStrategy = null) {
    if (!is_null($passwordStrategy) && $name !== '')
      $this->add_password_strategy($name, $passwordStrategy);
  }

  // Setters
  public function add_password_strategy(string $name, PasswordStrategy $strategy) {
    $this->password_strategies[$name] = $strategy;
  }

  // Functions
  public function authenticate(string $strategy_name) {
    $strategy = $this->password_strategies[$strategy_name];

    // $strategy->login_auth();
    // $strategy->register_auth();
  }
};