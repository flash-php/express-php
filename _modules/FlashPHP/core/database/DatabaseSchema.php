<?php

namespace FlashPHP\core\database;

/**
 * DataBaseSchema Class
 * @author Ingo Andelhofs
 *
 * A simple class that makes it possible to create simple database schema's.
 * You can export them to your Database class.
 */
class DatabaseSchema {
    private static $database_schemas;

    public function __construct($table_name, $schema) {
        self::$database_schemas[$table_name] = $schema;
    }

    public static function export() {
        // Return: ['table_name' => ['col1' => PDO::PARAM_INT, ...]]
        return self::$database_schemas;
    }
};