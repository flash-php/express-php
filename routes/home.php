<?php global $home;

$home->get('/index', function() {
  echo '/home/index';
});




// TODO
$home->get('/reqres/{id}/{test}', function($req, $res) {
  $uname = $req['body']['username'];
  
  $id = $req['params']['id'];
  $id = $req->params->id;

  $test = $req['params']['test'];

  $res->render('views/viewname');
  $res->send('Just some text');
});


$home->post('/middle/model', [
  'middleware' => ['isUserLoggedIn'], 
  'models' => ['Theme', 'Class']
], function($req, $res) {

  $req->middleware('isUserLoggedIn');

  $req->model->Theme->getThemeById( $req->params->id );
  $req->themeModel;

});


// $home->all([
//   'middleware' => ['isUserLoggedIn'],
//   'models' => ['User']
// ]);


// $home->helper(function() {

// });