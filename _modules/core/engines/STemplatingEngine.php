<?php

/**
 * Class STemplatingEngine <br>
 * A class that handles compiling and rendering files.
 * This class can handle simple variables and templating. More soon...
 *
 * @author Ingo Andelhofs
 */
class STemplatingEngine implements TemplateEngineStrategy {
  private $unique_data_prefix = null;

  public function compile_render(string $file, array $data) {
    $this->new_unique_data_prefix();

    // Load content file
    $view_file = $this->get_content_file($file);

    // Compiling
    $final_view_file = $this->compile_template($view_file);

    // Rendering
    $this->demo_render_file($final_view_file, $data);
  }

  // NEW FUNCTIONS
  // Compile
  public function compile_template(string $content_file) : string {
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
  public function compile_conditionals(string $content_file) : string {
    // IF ELSE ELSEIF ENDIF PARSING
    // $string = '
    // @if(true):
    //   echo "test";
    // @else( expression() ):
    // @elif( expression() ):
    // @endif';
    // $pattern = ['/(@if\()/', '/(@else\()/', '/(@elif\()|(@else if\()/', '/(@endif)/', '/(\):)/'];
    // $replacement = ['?php if(', '} else(', '} else if(', '} ? >', ') {'];
    // echo '<pre>';
    // echo preg_replace($pattern, $replacement, $string) . '<br>';
    // echo '</pre>';
  }
  public function compile_printing(string $content_file) : string {
    // $string = '{{ $hello }} tree';
    // $pattern = ['/(\{\{)/', '/(\}\})/'];
    // $replacement = ['>?= htmlspecialchars(', ') ?<'];
    // echo '<pre>';
    // echo preg_replace($pattern, $replacement, $string) . '<br>';
    // echo '</pre>';
  }

  // Render
  public function render_file(string $file, array $data) {
    $prefixed_file = $this->prefix_file_variables($file);
    foreach(array_keys($data) as $variable_name)
      eval($this->unique_data_prefix ."$variable_name = \$data['$variable_name'];");

    eval("?>$prefixed_file<?php");
  }
  public function demo_render_file(string $file, array $data) {
    print("-- variables --<br>");
    foreach($data as $variable_name => $variable_value) {
      print($this->unique_data_prefix ."$variable_name = ");
      print_r($variable_value);
      print(";<br>");
    }
    print("-- variables --<br><br>");

    $prefixed_file = $this->prefix_file_variables($file);
    (new Response())->send_r(htmlspecialchars($prefixed_file));
  }


  // Getters
  private function get_full_path(string $base_path, string $name) : string {
    return trim($base_path, '/').'/'.trim($name, '/').'.php';
  }
  private function get_file_content(string $file, string $base_path, string $error_msg) : string {
    $full_path = $this->get_full_path($base_path, $file);

    if (!file_exists($full_path))
      throw new FlashTemplateEngineException("$error_msg @ '$full_path'.");

    return file_get_contents($full_path);
  }

  private function get_template_file(string $template_name) : string {
    return $this->get_file_content($template_name, PATH_TEMPLATES, 'Template file does not exist');
  }
  private function get_content_file(string $content_file_name) : string {
    return $this->get_file_content($content_file_name, PATH_VIEWS, 'View file does not exist');
  }

  // Split functions
  private function split_keyword_from_value_flat(string $string, string $regex_split, int $limit = -1) : array {
    $keyword_and_value = preg_split($regex_split, $string, $limit, PREG_SPLIT_DELIM_CAPTURE);

    // TODO: Check for empty @section, ... (dont trim section content away!)
    $keyword_and_value = array_filter($keyword_and_value, 'trim');
    $keyword_and_value = array_filter($keyword_and_value);
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

  // Template clean up
  private function clean_up_file(string $file, string $regex) : string {
    return preg_replace($regex, '', $file);
  }
  private function clean_up_sections(string $template_file) : string {
    return $this->clean_up_file($template_file, '/@section \w+/');
  }
  private function clean_up_php_tags(string $file) : string {}

  // Unique data
  private function new_unique_data_prefix() : string {
    $this->unique_data_prefix = uniqid('$_').'_';
    return $this->unique_data_prefix;
  }
  private function prefix_file_variables(string $file) : string {
    return preg_replace('/(\$)(\w+)/', $this->unique_data_prefix.'$2', $file);
  }
};