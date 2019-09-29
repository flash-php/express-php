<?php

/**
 * Class Component <br>
 * Create simple components using the component name of the Render method.
 *
 * @author Ingo Andelhofs
 */
class Component extends BaseTemplateEngine {
  public static function render(string $name, array $data) {
    try {
      $file = self::get_file_content($name, PATH_COMPONENTS, 'Component does not exist');
      self::render_file($file, $data);
    }
    catch (FlashTemplateEngineException $e) {
      (new Response())->error("@Component: ".$e);
    }
  }
  public static function __callStatic($name, $arguments) {
    self::render($name, $arguments[0] ?? []);
  }
};