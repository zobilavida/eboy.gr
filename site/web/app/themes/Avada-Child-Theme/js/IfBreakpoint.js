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
  jQuery(".left").css({"height": "30%"});

 }

 if( sm == true) {
 		breakpoint = "sm - ≥576px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "sm" );
  jQuery(".left").css({"height": "30%"});

}

 if( md == true) {
 		breakpoint = "md - ≥768px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "md" );
    jQuery(".left").css({"height": "100%"});
}

 if( lg == true) {
 		breakpoint = "lg - ≥992px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "lg" );
  jQuery(".left").css({"height": "100%"});
}

 if( xl == true) {
 		breakpoint = "xl - ≥1200px";
 	console.log(breakpoint);
 	jQuery("body").removeClass('xs sm md lg xl').addClass( "xl" );
  jQuery(".left").css({"height": "100%"});
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
