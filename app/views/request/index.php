<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>

  <h1>Safe Image Urls</h1><hr>
  <img src="/files/image/profile-picture.jpeg" alt="" width="250px">
  <br>

  <h1>PUT Form</h1><hr>
  <form action="/home/index" method="PUT">
    <input type="text" name="test_key" placeholder="put value">
    <input type="submit" value="Go">
  </form>
  <br>


  <h1>FILE Form</h1><hr>
  <form action="/files/upload" method="POST" enctype="multipart/form-data">
      <input type="file" name="profile_image" placeholder="file upload">
      <input type="submit" value="Go">
  </form>
  <br>

  <script src="/public/js/RESThelper.js"></script>  
</body>
</html>