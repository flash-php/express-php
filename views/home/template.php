@extends Template1

@section scripts
<!--<script src="script.js"></script>-->
<!--<script src="app.js"></script>-->

@section main
<section class="app" style="border: 1px solid red;">
    <h1><?= $name; ?></h1>
  <h2>Main section here...</h2>
  <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptatum vero repudiandae rem cupiditate illum? Nesciunt rerum hic minus repellendus, ipsum excepturi ab, sit quae incidunt quas numquam at ullam fugit?</p>
</section>


<!-- @component('Example1', ['title' => 'This is the title.', 'content' => 'This is the content...']) -->
<!-- <?php // Component::render('Example1', ['title' => 'This is the title.', 'content' => 'This is the content...']); ?> -->