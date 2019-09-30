<?php

/**
 * Class ValidationError
 *
 * @author Ingo Andelhofs
 */
class ValidationError {
  public $name;
  private $message;
  private $type;
  private $color;

  // Fallback constants
  const NAME = 'field';
  const MESSAGE = 'An error occurred.';
  const TYPE = 'error';
  const COLOR = 'red';

  // Constructor
  public function __construct(string $name=self::NAME, string $message=self::MESSAGE, string $type = self::TYPE, string $color = self::COLOR) {
    $this->name = $name;
    $this->message = $message;
    $this->type = $type;
    $this->color = $color;
  }

  // Getters
  public function getMessage(): string {
    return $this->message;
  }
  public function getName(): string {
    return $this->name;
  }
  public function getType(): string {
    return $this->type;
  }
  public function getColor(): string {
    return $this->color;
  }

  // Printing
  public function display() {
    $type = $this->type;
    $message = $this->message;
    $name = ucwords($this->name);

    (new Response())->send_r("<p class='$type'>$name: $message</p>");
  }
}