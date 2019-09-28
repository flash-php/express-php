<?php

/**
 * Simple Templating Engine Class
 * @author Ingo Andelhofs
 * 
 * A class that handles compiling files in the view folder and rendering them.
 * This class can handle simple variables and templating. More soon... 
 * 
 * @uses /
 * @todo Change engine to non-static !!!!
 */
class SimpleTemplatingEngine implements TemplateEngineStrategy {
  public function compile_render($view_name, $view_data) {
    $view_path = trim(PATH_VIEWS, '/') . '/';
    $view_name = trim($view_name, '/');
    $full_view_path = $view_path.$view_name.'.php';

    $view_str = $this->compile_template($full_view_path);
    $this->render_data_from_string($view_str, $view_data);
  }

  /**
   * @used-by Component
   * @param $full_path
   * @param $data
   */
  public function render_data($full_path, $data) {
    foreach(array_keys($data) as $variable_name)
      eval("$$variable_name = \$data['$variable_name'];");

    include_once($full_path);
  }

  public function render_data_from_string($view_string, $view_data) {
    foreach(array_keys($view_data) as $variable_name)
      eval("$$variable_name = \$view_data['$variable_name'];");
    
    eval("?>$view_string<?php");
  }

  public function compile_template($full_view_path) {
    if (!file_exists($full_view_path)) {
      echo "STE: Error: File not found at '$full_view_path'.";
      return false;
    }

    $view_str = file_get_contents($full_view_path);
    $view_lines = explode("\r\n", $view_str);

    // Templating
    if (substr($view_str, 0, 8) === '@extends') {
      $view_str = $this->_compile_extends($view_str, $view_lines);
    }
    
    return $view_str;
  }

  private function _compile_extends(&$view_str, &$view_lines) {
    // TODO: optimize using strtok ($view_lines)

    // Get template
    $template_name = substr($view_lines[0], 9); 
    // echo "<br>---$template_name---<br>";

    // Replace sections
    $sections_array = [];
    $current_section = null;

    foreach($view_lines as $line) {
      if (substr($line, 0, 8) === '@section') {
        $current_section = substr($line, 9);
        $sections_array[$current_section] = '';
      }
      else if (!is_null($current_section)) {
        $sections_array[$current_section] .= "\n$line";
      }
    }

    // Generate 1 file/string
    if (file_exists("./components/$template_name.php")) {
      $template_layout = file_get_contents("./components/$template_name.php");

      foreach($sections_array as $key => $value) {
        if (strpos($template_layout, "@section $key") !== false) {
          $template_layout = str_replace("@section $key", $value, $template_layout);
        }
      }

      return $template_layout;
    }
    else {
      echo "STE: Error: Template not found";
    }
  }
};





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


// UNIQUE CODE PREFIXING
// $string = '( $hello ) tree';
// $pattern = '/(\$)/';
// $replacement = '$$'.uniqid().'private';
// echo '<pre>';
// echo preg_replace($pattern, $replacement, $string) . '<br>';
// echo '</pre>';


// UNIQUE CODE PREFIXING unique_id only
// $uniqid = uniqid();
// $string = '( $hello ) tree';
// $pattern = '/(\$\w*)/';
// $replacement = "$$$uniqid";
// echo '<pre>';
// echo preg_replace($pattern, $replacement, $string) . '<br>';
// echo '</pre>';


// $string = '{{ $hello }} tree';
// $pattern = ['/(\{\{)/', '/(\}\})/'];
// $replacement = ['>?= htmlspecialchars(', ') ?<'];
// echo '<pre>';
// echo preg_replace($pattern, $replacement, $string) . '<br>';
// echo '</pre>';


// -> PARSE (pefix_vars, template, IF*, direct_variable_print)
// -> REMOVE EXTRA <? php  &  ? > 