 // Create global variables that can be used elsewhere

 // set variables
 var xs;
 var sm;
 var md;
 var lg;
 var xl;
 var breakpoint;

 // Checks if the span is set to display lock via CSS
 function checkIfBlock (target) {
     var target = jQuery(target).css('display') == 'block';
     return target;
 }

 // function to check the sizes
 function checkSize (){
   // Set some variables to use with the if checks below

 xs = checkIfBlock('.breakpoint-check .xs');
 sm = checkIfBlock('.breakpoint-check .sm');
 md = checkIfBlock('.breakpoint-check .md');
 lg = checkIfBlock('.breakpoint-check .lg');
 xl = checkIfBlock('.breakpoint-check .xl');


 // add the breakpoint to the console
 console.clear();
 if( xs == true) {
 	breakpoint = "xs - <576px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "xs" );
 }

 if( sm == true) {
 		breakpoint = "sm - ≥576px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "sm" );


        var tlsm2 = new TimelineLite();
       tlsm2.to(".img2", 1, {x:0, y:0});

       var tlsm3 = new TimelineLite();
       tlsm3.to(".img3", 1, {x:0, y:0});

       var tlsm4 = new TimelineLite();
       tlsm4.to(".img4", 1, {x:0, y:0});


 }

 if( md == true) {
 		breakpoint = "md - ≥768px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "md" );
  var tlmd2 = new TimelineLite();
 tlmd2.to(".img2", 1, {x:0, y:0});

 var tlmd3 = new TimelineLite();
 tlmd3.to(".img3", 1, {x:0, y:0});

 var tlmd4 = new TimelineLite();
 tlmd4.to(".img4", 1, {x:0, y:0});
 }

 if( lg == true) {
 		breakpoint = "lg - ≥992px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "lg" );

  var tllg2 = new TimelineLite();
 tllg2.to(".img2", 1, {x:-70, y:63});

 var tllg3 = new TimelineLite();
 tllg3.to(".img3", 1, {x:-70, y:10});

 var tllg4 = new TimelineLite();
 tllg4.to(".img4", 1, {x:-90, y:-15});
 }

 if( xl == true) {
 		breakpoint = "xl - ≥1200px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "xl" );

  var tlxl2 = new TimelineLite();
 tlxl2.to(".img2", 1, {x:-70, y:63});

 var tlxl3 = new TimelineLite();
 tlxl3.to(".img3", 1, {x:-70, y:10});

 var tlxl4 = new TimelineLite();
 tlxl4.to(".img4", 1, {x:-90, y:-15});

  var tlxl5 = new TimelineLite();
  tlxl5.to(".img5", 1, {x:-37, y:-95});
 }

 }
 // end check size

jQuery(document).ready(function(){
  	// Add some invisible elements with Bootstrap CSS visibile utility classes
 	jQuery( "body" ).append( "<div style='display:none;' class='breakpoint-check'><span class='xs d-block d-sm-inline'></span><span class='sm d-sm-block d-md-inline'></span><span class='md d-md-block d-lg-inline'></span><span class='lg d-lg-block d-xl-inline'></span><span class='xl d-xl-block'></span></div>" );
 	checkSize();
 });


 // Reload demo on  window resize
 jQuery( window ).resize( function(){
 	checkSize();
 });
