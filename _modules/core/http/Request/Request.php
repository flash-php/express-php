<?php


/**
 * RouterRequestObject Class
 * @author Ingo Andelhofs
 *
 * A class that handles all the Request functionalities.
 *
 * @uses Database class
 * @uses GetNullObj / AdvanvcedNullObject
 */
class Request {
    public $body;
    public $params;

    public $db;

    public $session;
    public $cookie;
    public $files;

    public function __construct($param_array) {
        $this->body = new RequestBodyHandler();
        $this->params = new AdvancedNullObject($param_array);

        $this->db = new Database();

        $this->session = new AdvancedNullObject($_SESSION);

        $this->files = new RequestFileHandler();
    }
};