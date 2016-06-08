(function($) {
  // Site title
  wp.customize('blogname', function(value) {
    value.bind(function(to) {
      $('.brand').text(to);
    });
  });
})(jQuery);

(function($) {
    console.log($);
    // Responsive video embeds
    console.log("wrap test");
    $( '.single-news .entry-content iframe' ).hide();
})( jQuery );

// jQuery(document).ready(function( $ ) {
//
// 	// $ Works! You can test it with next line if you like
// 	console.log($);
//
// });

//
// // append Decline post status
// jQuery(document).ready(function($){
//     $('select#post_status').append('<option selected=\"selected\" value=\"decline\">Decline</option>');
// });
//
// // remove .hide-on-mobile based on screensize
// jQuery(document).ready(function($) {
//     if($(window).width() <= 1024) {
//         $('.hide-on-mobile').remove();
//     }
// });
