jQuery(document).ready(function () {
var hamburger = document.querySelector(".hamburger");
hamburger.addEventListener("click", function() {
  hamburger.classList.toggle("is-active");
  jQuery(".navbar-collapse").toggleClass( "show" );
});


});
