<?php

interface TemplateEngineStrategy {
  public function compile_render($view_name, $view_data);
};