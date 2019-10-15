<?php

namespace FlashPHP\interfaces;

/**
 * Interface TemplatingEngineStrategy
 *
 * @author Ingo Andelhofs
 */
interface TemplatingEngineStrategy {
  /**
   * @param string $file The name of the view that u want to load.
   * @param array $data The data u want the view to be rendered with.
   * @return void
   */
  public function compile_render(string $file, array $data);
};