<?php

namespace FlashPHP\core\engines;
use FlashPHP\interfaces\TemplatingEngineStrategy;

/**
 * Class MTemplatingEngine <br>
 * The medium template engine that supports: variables, var_prefixing, templates, printing, conditionals, components
 *
 * @author Ingo Andelhofs
 */
class MTemplatingEngine extends STemplatingEngine implements TemplatingEngineStrategy {
  private $expressions = '\s*([$_0-9a-zA-Z>\'"\.\,\n\r\-\[\]\(\)\{\}\*\+\=\/;\\\ ]*)\s*';

  public function compile_render(string $file, array $data) {
    $content_str = $this->get_content_file($file);

    // Compiling
    $content_str = $this->compile_template($content_str);
    $content_str = $this->compile_printing($content_str);
    $content_str = $this->compile_conditionals($content_str);
    $content_str = $this->compile_components($content_str);

    // Rendering
    self::render_file($content_str, $data, true, false);
  }

  // Compiling
  public function compile_conditionals(string $content_file) : string {
    $expressions = $this->expressions;

    $pattern  = [
      // If ... else ... else if
      '/(@)if\(' . $expressions . '\):/',
      '/(@)else:/',
      '/(@)else\s*if\(' . $expressions . '\):|(@)elif\(' . $expressions . '\):/',
      '/(@)endif(;?)/',

      // Foreach
      '/(@)foreach\(' . $expressions . '\):/',
      '/(@)endforeach(;?)/',
    ];
    $replacement = [
      // If ... else ... else if
      '<?php if($2): ?>',
      '<?php else: ?>',
      '<?php else if($2): ?>',
      '<?php endif; ?>',

      // Foreach
      '<?php foreach($2): ?>',
      '<?php endforeach; ?>',
    ];

    return preg_replace($pattern, $replacement, $content_file);
  }
  public function compile_components(string $content_file): string {
    $expressions = $this->expressions;

    $pattern  = [
      '/(@component) (\w+)(\s*[\(\[])('. $expressions .')([\)\]];?)/',
    ];
    $replacement = [
      '<?php Component::render("$2", [$4]); ?>',
    ];

    return preg_replace($pattern, $replacement, $content_file);
  }
}