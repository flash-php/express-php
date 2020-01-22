<?php

namespace FlashPHP\core\engines;
use FlashPHP\interfaces\TemplatingEngineStrategy;

/**
 * Class STemplatingEngine <br>
 * The simple template engine that supports: variables, var_prefixing, templates, printing
 *
 * @author Ingo Andelhofs
 */
class STemplatingEngine extends NTemplatingEngine implements TemplatingEngineStrategy {
  public function compile_render(string $file, array $data) {
    $content_str = $this->get_content_file($file);

    // Compiling
    $content_str = $this->compile_template($content_str);
    $content_str = $this->compile_printing($content_str);

    // Rendering
    self::render_file($content_str, $data, true, true);
  }

  // Compiling
  protected function compile_template(string $content_file) : string {
    $template_content_array = $this->split_extends_from_content($content_file);
    if (empty($template_content_array))
      return $content_file;

    $template_name = $template_content_array[0];
    $content_str = $template_content_array[1] ?? '';

    $template_file = $this->get_template_file($template_name);
    $section_content_array = $this->split_sections_from_content($content_str);
    $filled_in_template = $this->fill_template($template_file, $section_content_array);

    return $this->clean_up_sections($filled_in_template);
  }
  protected static function compile_printing(string $content_file) : string {
    $pattern  = [
      '/(\{\{[^r!])\s*([$_0-9a-zA-Z> \-\[\]\(\)]{2,})\s*(;?)\s*(\}\})/', // {{ $var; }} -> htmlspecialchars
      '/(\{\{!)\s*([$_0-9a-zA-Z> \-\[\]\(\)]{2,})\s*(;?)\s*(\}\})/', // {{! $var; }} -> no htmlspecialchars
      '/(\{\{r)\s*([$_0-9a-zA-Z> \-\[\]\(\)]{2,})\s*(;?)\s*(\}\})/', // {{r $var; }} -> print_r
    ];
    $replacement = [
      '<?php print(htmlspecialchars($2)); ?>',
      '<?php print($2); ?>',
      '<?php (new Response())->send_r($2); ?>',
    ];

    return preg_replace($pattern, $replacement, $content_file);
  }

  // Getters
  private function get_template_file(string $template_name) : string {
    return $this->get_file_content($template_name, PATH_TEMPLATES, 'Template file does not exist');
  }
  protected function get_content_file(string $content_file_name) : string {
    return $this->get_file_content($content_file_name, PATH_VIEWS, 'View file does not exist');
  }

  // Split functions
  private function split_keyword_from_value_flat(string $string, string $regex_split, int $limit = -1) : array {
    $keyword_and_value = preg_split($regex_split, $string, $limit, PREG_SPLIT_DELIM_CAPTURE);

    // TODO: Check for empty @section, ... (dont trim section content away!)
    $keyword_and_value = array_filter($keyword_and_value, 'trim');
    $keyword_and_value = array_filter($keyword_and_value);

    // Check for wrong splitting
    $data = $keyword_and_value[0] ?? '';
    if (trim($data) === trim($string))
      return [];

    return array_values($keyword_and_value);
  }
  private function split_keyword_from_value(string $string, string $regex_split, int $limit = -1) : array {
    $keyword_and_value = $this->split_keyword_from_value_flat($string, $regex_split, $limit);

    $keyword_per_value = [];
    for($i = 0, $l = count($keyword_and_value); $i < $l - 1; $i += 2) {
      $keyword = $keyword_and_value[$i];
      $value = $keyword_and_value[$i + 1];

      $keyword_per_value[$keyword] = $value;
    }

    return $keyword_per_value;
  }

  private function split_sections_from_content(string $content_file) : array {
    return $this->split_keyword_from_value($content_file, '/@section (\w+)/');
  }
  private function split_extends_from_content(string $content_file) : array {
    return $this->split_keyword_from_value_flat($content_file, '/@extends (\w+)/', 2);
  }

  // Fill functions
  private function fill_template(string $template_file, array $section_content_array) : string {
    foreach ($section_content_array as $section => $content) {
      // TODO: Change preg_replace to preg_replace with array (pattern) and array (replacement)
      $template_file = preg_replace("/(@section $section)/", $content, $template_file);
    }
    return $template_file;
  }

  // Clean up
  private function clean_up_file(string $file, string $regex) : string {
    return preg_replace($regex, '', $file);
  }
  private function clean_up_sections(string $template_file) : string {
    return $this->clean_up_file($template_file, '/@section \w+/');
  }
};