<?php global $files;

$files->get('/image/:path', function(Request $req, Response $res) {
  $res->download('./private/images/' . $req->params->path);
});



$files->post('/upload', function(Request $req, Response $res) {
//  $req->files->profile_image->store('private/images/');
//  $req->files->profile_image->storeAs('private/images/', 'profile-picture');

  echo $req->files->profile_image->extension;
});