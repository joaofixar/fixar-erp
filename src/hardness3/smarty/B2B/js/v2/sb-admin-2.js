(function($) {
  "use strict"; // Start of use strict


  $('#dataEmissao').datepicker({
      format: 'dd/mm/yyyy',
      language: "pt-BR",
      autoclose: true,
      defaultViewDate: true,
      //startDate: '0'
  });

  // Toggle the side navigation
  $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
    if ($(".sidebar").hasClass("toggled")) {
      $('.sidebar .collapse').collapse('hide');
    };
  });

  // Close any open menu accordions when window is resized below 768px
  $(window).resize(function() {
    if ($(window).width() < 765) {
      $('.sidebar .collapse').collapse('hide');
    };
  });

  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
    if ($(window).width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  $(function () {
      var $doc = $('html, body');
      $('.scroll-to-top').click(function () {
          $doc.animate({
              scrollTop: $($(this).attr('href')).position().top
          }, 700);
          return false;
      });
  });

  // Scroll to top button appear
  // $(document).on('scroll', function() {
  //   var scrollDistance = $(this).scrollTop();
  //   if (scrollDistance > 100) {
  //     $('.scroll-to-top').fadeIn();
  //   } else {
  //     $('.scroll-to-top').fadeOut();
  //   }
  // });

  // // Smooth scrolling using jQuery easing
  // $(document).on('click', 'a.scroll-to-top', function(e) {
  //   var $anchor = $(this);
  //   $('html, body').stop().animate({
  //     scrollTop: ($($anchor.attr('href')).offset().top)
  //   }, 1000, 'easeInOutExpo');
  //   e.preventDefault();
  // });

})(jQuery); // End of use strict