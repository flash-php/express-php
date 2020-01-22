<?php

class Config {
  // ROOT PATH
  public static $DIR = __DIR__;
  public static $HOME = __DIR__;
  public static $HOME_PATH = __DIR__;

  // DATABASE
  public static $DB_ACTIVE = false;
  public static $DB_DRIVER = 'mysql';
  public static $DB_HOST = 'localhost';
  public static $DB_PORT = '3306';
  public static $DB_NAME = 'mysql-db';
  public static $DB_USERNAME = 'root';
  public static $DB_PASSWORD = 'root';

  // FILES
  public static $FILE_SIZE_MB = 2;
  public static $FILE_NAME = 'filename';
  public static $IMAGE_TYPES = [
    'image/gif',
    'image/jpeg',
    'image/png',
    'image/pjpeg',
//    'image/svg', ??? bitmap ???
  ];

  // PATHS
  public static $PATH_FILE_UPLOAD = 'private/upload/';
  public static $PATH_COMPONENTS = 'app/';
  public static $PATH_MIDDLEWARE = 'app/';
  public static $PATH_MODELS = 'app/';
  public static $PATH_ROUTES = 'app/';
  public static $PATH_TEMPLATES = 'app/';
  public static $PATH_VIEWS = 'app/';

  // ROUTER
  public static $ROUTER_DEFAULT_ROUTE = 'home';
  public static $ROUTER_DEFAULT_METHOD = 'index';

  // TEMPLATE ENGINE
  public static $TEMPLATE_ENGINE = null;

  // REGEX
  public static $REGEX_ALL_LETTERS = '-\'a-zA-ZÀ-ÖØ-öø-ÿ';
  public static $REGEX_BASIC_LETTERS = 'a-zA-Z';
  public static $REGEX_EMAIL = '';
  public static $REGEX_PASSWORD = '';
}