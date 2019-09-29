<?php

/**
 * Interface TemplateEngineStrategy
 *
 * @author Ingo Andelhofs
 */
interface TemplateEngineStrategy {
  public function compile_render(string $file, array $data);
};