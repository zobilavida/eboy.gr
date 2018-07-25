jQuery(document).ready(function($) {

  var demourl = 'https://eboy.gr/app/themes/Avada-Child-Theme/img/stamp_placeholder.png';
    $('.selected-stamp').attr('src', demourl);
  $(document).on('facetwp-loaded', function() {
    $('.slick-product').slick({
      dots: true,
      infinite: true,
      speed: 300,
      slidesToShow: 1,
      centerMode: true,
      variableWidth: false
    });

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
