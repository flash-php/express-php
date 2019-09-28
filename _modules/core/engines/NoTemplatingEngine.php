<?php


class NoTemplatingEngine implements TemplateEngineStrategy {
  public function compile_render($view_name, $view_data) {
    $view_path = trim(PATH_VIEWS, '/') . '/';
    $view_name = trim($view_name, '/');

    $full_view_path = $view_path.$view_name.'.php';

    $data = $view_data;
    include($full_view_path);
  }
}