<section id="about">
<div class="container">

<div class="row about">
  <div class="col-6">
    <h5>
    <?
    $post_about = get_page_by_path('about');
  $title = $post_about->post_title;
  echo $title
  ?>
</h5>
<span class="ido">
<?
$post_about = get_page_by_path('about');
$content = $post_about->post_content;
echo $content
?>
</span>
</div>
<div class="col-1">
</div>
<div class="col-5">
  <h5>
skills
</h5>
<div class="meter">
<span class="bg-black" style="width: 91%;"></span>
<div>
  <span class="float-left"><h6>Adobe Creative Suite</h6></span>
  <span class="float-right"><h6>91%</h6></span>
</div>
</div>

<div class="meter">
<span class="bg-black" style="width: 85%;"></span>
<div>
  <span class="float-left"><h6>HTML / CSS / JavaScript</h6></span>
  <span class="float-right"><h6>85%</h6></span>
</div>
</div>

<div class="meter">
<span class="bg-black" style="width: 75%;"></span>
<div>
  <span class="float-left"><h6>Linux / Nginx / MySQL / PHP</h6></span>
  <span class="float-right"><h6>75%</h6></span>
</div>
</div>
<div class="meter">
<span class="bg-black" style="width: 85%;"></span>
<div>
  <span class="float-left"><h6>Wordpress</h6></span>
  <span class="float-right"><h6>85%</h6></span>
</div>
</div>
</div>

  <div class="col-12 what-i-do">
  <h5>
What i do
</h5>


</div>
<div class="col-3 text-center">
<img class="mx-auto d-block" src="<?= get_template_directory_uri(); ?>/dist/images/ico_design.svg">
<span class="service_head"><h6>Design & DTP</h6></span>
<span class="service_text">Branding and corporate indentity using <span class="bold">hand</span> and <span class="bold">illustrator</span>. Design, layout and typography of printent matterial using <span class="bold">InDesign</span></span>
</div>
<div class="col-3 text-center">
  <img class="mx-auto d-block" src="<?= get_template_directory_uri(); ?>/dist/images/ico_ui.svg">
<span class="service_head"><h6>UI / UX</h6></span>
  <span class="service_text">Responsive layouts using <span class="bold">SASS</span> and <span class="bold">Gulp</span> compiler, based on <span class="bold">Bootstrap</span> or <span class="bold">Foundation</span></span>
</div>
<div class="col-3 text-center">
  <img class="mx-auto d-block" src="<?= get_template_directory_uri(); ?>/dist/images/ico_code.svg">
<span class="service_head"><h6>Programming</h6></span>
  <span class="service_text"><span class="bold">Wordpress custom themes</span>, with advanced <span class="bold">Ajax</span> techniques using <span class="bold">PHP7</span> and <span class="bold">Jquery</span></span>
</div>
<div class="col-3 text-center">
  <img class="mx-auto d-block" src="<?= get_template_directory_uri(); ?>/dist/images/ico_server.svg">
<span class="service_head"><h6>System Administration</h6></span>
  <span class="service_text"><span class="bold">Nginx</span> setup on Ubuntu. Automatic cloud deployments, using <span class="bold">GIT</span> and <span class="bold">command line</span></span>
</div>

 </section>
