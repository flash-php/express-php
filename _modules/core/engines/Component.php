<?php

/**
 * Component Class
 * @author Ingo Andelhofs
 *
 * This simple class handles components with simple variables.
 *
 * @uses STE class
 */
class Component {
    public static $components_path = './components/';
    public static $view_path = './views/';

    public static function render($component_name, $dynamic_data) {
        STE::render_data("./components/$component_name.php", $dynamic_data);
    }
};