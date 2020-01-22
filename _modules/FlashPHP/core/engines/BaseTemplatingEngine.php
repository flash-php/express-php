<?php

namespace FlashPHP\core\engines;
use FlashPHP\interfaces\TemplatingEngineStrategy;
use FlashPHP\core\http\Response\Response;

/**
 * Class BaseTemplatingEngine <br>
 * The base template engine is used by all the other engines. By default this engine renders a view with data without any extra features.
 *
 * @author Ingo Andelhofs
 */
class BaseTemplatingEngine implements TemplatingEngineStrategy {
  protected static $unique_data_prefix = null;

  // Compile And Render
  public function compile_render(string $file, array $data) {
    try {
      $content_file = $this->get_file_content($file, PATH_VIEWS, 'View file does not exist');
      $this->render_file($content_file, $data, false);
    }
    catch (FlashTemplatingEngineException $e) {
      (new Response())->error("@BaseTemplatingEngine: " . $e);
    }
  }

  // Rendering
  protected static function render_file(string $file, array $data, bool $var_prefixing = true, bool $demo_mode = false) {
    if ($demo_mode)
      return self::demo_render_file($file, $data, $var_prefixing);

    // IMPORTANT: first run 'new_unique_data_prefix' to generate prefix.
    $prefix = $var_prefixing ? self::new_unique_data_prefix() : '';
    $file = $var_prefixing ? self::prefix_file_variables($file) : $file;

    foreach(array_keys($data) as $variable_name)
      // TODO: Replace with dynamic variables
      eval($prefix ."$variable_name = \$data['$variable_name'];");

    // BUG: Fatal error: Uncaught Error: Class 'Component'?? not found in
    // C:\Users\Ingo Andelhofs\Documents\git\express-php\_modules\FlashPHP\core\engines\BaseTemplatingEngine.php(40)
    eval("use FlashPHP\\core\\engines\\Component; ?> $file");
  }
  private static function demo_render_file(string $file, array $data, bool $var_prefixing = true) {
    $variable_header = '--- vars ---<br>';
    // IMPORTANT: first run 'new_unique_data_prefix' to generate prefix.
    $prefix = $var_prefixing ? self::new_unique_data_prefix() : '';
    $file = $var_prefixing ? self::prefix_file_variables($file) : $file;

    print($variable_header);
    foreach($data as $variable_name => $variable_value) {
      print($prefix."$variable_name = ");
      print_r($variable_value);
      print(";<br>");
    }
    print($variable_header);

    (new Response())->send_r(htmlspecialchars($file));
  }

  // Getters
  protected static function get_full_path(string $base_path, string $name) : string {
    return trim($base_path, '/').'/'.trim($name, '/').'.php';
  }
  protected static function get_file_content(string $file, string $base_path, string $error_msg) : string {
    $full_path = self::get_full_path($base_path, $file);

    if (!file_exists($full_path))
      throw new FlashTemplatingEngineException("$error_msg @ '$full_path'.");

    return file_get_contents($full_path);
  }

  // Clean up
  protected static function regex_clean_up_file(string $file, string $regex) : string {
    return preg_replace($regex, '', $file);
  }

  // Unique data
  private static function new_unique_data_prefix() : string {
    self::$unique_data_prefix = uniqid('$_').'_';
    return self::$unique_data_prefix;
  }
  private static function prefix_file_variables(string $file) : string {
    return preg_replace('/(\$)(\w+)/', self::$unique_data_prefix.'$2', $file);
  }
}