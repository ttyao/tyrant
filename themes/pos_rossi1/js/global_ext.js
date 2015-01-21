
// google analytics
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-58238512-1', 'auto');
ga('send', 'pageview');

var _last_scroll_height = 0;
$(window).scroll(function() {
  var height = $(window).scrollTop();
  if (height > $('.header_content').height() + $('.banner').height()) {
    $('.megamenu').addClass('megamenuSticky');
    if (height > _last_scroll_height) {
      $('.megamenu').addClass('megamenuHide');
    } else {
      $('.megamenu').removeClass('megamenuHide');
    }
  } else {
    $('.megamenu').removeClass('megamenuSticky');
  }
  _last_scroll_height = height;
});

// hack hack: move ajax cart into top menu bar
$(document).ready(function(){
  $('.shopping-cart-outer').appendTo('.topCart');
});
