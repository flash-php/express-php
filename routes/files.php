<?php global $files;

$files->get('/image/:path', function(Request $req, Response $res) {
  FileHandler::returnImage("./public/images/{$req->params->path}");
});



$files->post('/upload', function(Request $req, Response $res) {
    FileHandler::saveSingleImage('test', 'public/images/uploads/');

//    $res->storeImage('test', '');

});