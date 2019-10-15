<?php

namespace FlashPHP\interfaces;
use Closure;

interface IDatabase {
  function __construct(); // Private
  public static function get_instance();

  public static function new_query(string $name, Closure $callback);

  public function query(string $statement, array $parameters=[]);

  public function create(string $table, array $insert_data, array $options=[]) : int;
  public function read(string $table, array $where, array $options=[]);
  public function update(string $table, array $where, array $update_data, array $options=[]);
  public function delete(string $table, array $where, array $options=[]);

  public function duplicate(string $table, array $where, array $options=[]) : int;
  public function exist(string $table, array $where);

  // SQL HELPERS
  function generate_where(array $where, array $options) : string; // Private
  function generate_options(array $options) : string; // Private
}

// Throws:
// FlashPHPException,
// DatabaseException,
// DatabaseQueryException, / DatabaseConfigurationException
// DatabaseInitializeException,
// DatabaseGenerateException

// Helpers/
// Singleton, DatabaseConnection, CallMagicHelper, Query

// Inheritance:
// SQLDatabase -> (MySQLDatabase, PgSQLDatabase, ...)
// NoSQLDatabase -> (MongoDatabase)


// Future:
// Database Pooling, ...