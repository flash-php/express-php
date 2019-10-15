<?php

namespace FlashPHP\core\engines;
use FlashPHP\interfaces\TemplatingEngineStrategy;

/**
 * Class NTemplatingEngine <br>
 * A super simple engine that renders a view with data and uses variable prefixing.
 *
 * @author Ingo Andelhofs
 */
class NTemplatingEngine extends BaseTemplatingEngine implements TemplatingEngineStrategy {
  public function compile_render(string $file, array $data) {
    try {
      $content_file = $this->get_file_content($file, PATH_VIEWS, 'View file does not exist');
      $this->render_file($content_file, $data);
    }
    catch (FlashTemplatingEngineException $e) {
      (new Response())->error("@NTemplateEngine".$e);
    }
  }
}