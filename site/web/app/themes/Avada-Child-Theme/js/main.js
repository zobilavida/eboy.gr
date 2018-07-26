jQuery(document).ready(function($) {
    $(".right-category").html("loading1...");
var def_url = 'https://eboy.gr/tshirtakias/mens/';
$(".right-category").load(def_url + " .related-products-preview-wrapper");
  $(document).on('facetwp-loaded', function() {


  $(".product-preview.mens").click(function(){
    var post_url = $(this).attr('data-href');
    $(".right-category").html("loading...");
      $(".right-category").load(post_url + " .related-products-preview-wrapper");
  });
  $(".product-preview.womens").click(function(){
    var post_url = $(this).attr('data-href');
    $(".right-category").html("loading...");
      $(".right-category").load(post_url + " .related-products-preview-wrapper");
  });
  $(".product-preview.hoodies").click(function(){
    var post_url = $(this).attr('data-href');
    $(".right-category").html("loading...");
      $(".right-category").load(post_url + " .related-products-preview-wrapper");
  });
  $(".product-preview.kids").click(function(){
    var post_url = $(this).attr('data-href');
    $(".right-category").html("loading...");
      $(".right-category").load(post_url + " .related-products-preview-wrapper");
  });
  $(".product-preview.babies").click(function(){
    var post_url = $(this).attr('data-href');
    $(".right-category").html("loading...");
      $(".right-category").load(post_url + " .related-products-preview-wrapper");
  });
  $( ".stamp" ).click(function() {
    var text = $( this ).attr('stamp-name');
    var url = $( this ).attr('stamp-url');

    $( "input[name='stamp_url']" ).attr('value', url);
    $( "input[name='_custom_option']" ).val( text );
    $('.selected-stamp').attr('src', url);
  });


});
  });
