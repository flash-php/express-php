<?php


/**
 * RouterResponseObject Class
 * @author Ingo Andelhofs
 *
 * A class that handles all the Response functionalities.
 *
 * @uses Router class (template_engine functions)
 */
class Response {
    // Writing to the screen
    public function send($data='') {
        echo $data;
    }

    public function send_r($data='') {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    public function json($data=[]) {
        echo '<pre>';
        echo json_encode($data);
        echo '</pre>';
    }

    public function view($path='home/index', $data=[]) {
        $full_path = "./views/$path.php";

        Router::compile_render_template($path, $data);
    }

    public function render($path='home/index', $data=[]) {
        $this->view($path, $data);
    }

    public function js_log($data='') {
        echo "<script>";
        echo "console.log('$data');";
        echo "</script>";
    }


    // Ending the program
    public function end($data='') {
        die($data);
    }


    // Redirecting
    public function redirect($to='/home/index') {
        header("Location: $to");
        $this->end("Redirecting to: $to...");
    }

    public function redirect_back() {
        $this->redirect($_SERVER['HTTP_REFERER']); // $this->redirect('javascript://history.go(-1)');
    }


    // File handeling
    public function download() {
        // Code here...
    }
};