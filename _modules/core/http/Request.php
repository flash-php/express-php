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

    public function __construct($param_array) {
        $this->body = new AdvancedNullObject($this->get_body_array());
        $this->params = new AdvancedNullObject($param_array);

        $this->db = new Database();

        $this->session = new AdvancedNullObject($_SESSION);

    }

    private function &get_body_array() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                return $_GET;
                break;
            case 'POST':
                unset($_POST['REQUEST_METHOD']);
                return $_POST;
                break;
            case 'PUT':
                parse_str(file_get_contents('php://input'), $_PUT);
                return $_PUT;
                break;
            default:
                return [];
                break;
        }
    }
};