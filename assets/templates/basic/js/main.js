'use strict';
(function ($) {
  // ==========================================
  //      Start Document Ready function
  // ==========================================
  $(document).ready(function () {
    // ============== Header Hide Click On Body Js Start ========
    $('.header-button').on('click', function () {
      $('.body-overlay').toggleClass('show');
    });
    $('.body-overlay').on('click', function () {
      $('.header-button').trigger('click');
      $(this).removeClass('show');
    });
    // =============== Header Hide Click On Body Js End =========
    // // ========================= Header Sticky Js Start ==============
    $(window).on('scroll', function () {
      if ($(window).scrollTop() >= 300) {
        $('.header').addClass('fixed-header');
      } else {
        $('.header').removeClass('fixed-header');
      }
    });
    // // ========================= Header Sticky Js End===================


  // Sidebar Dropdown Menu End

    // Sidebar Icon & Overlay js 
    $(".dashboard-body__bar-icon").on("click", function () {
      $(".sidebar-menu").addClass('show-sidebar');
      $(".sidebar-overlay").addClass('show');
    });
    $(".sidebar-menu__close, .sidebar-overlay").on("click", function () {
      $(".sidebar-menu").removeClass('show-sidebar');
      $(".sidebar-overlay").removeClass('show');
    });
    // Sidebar Icon & Overlay js 
    // ===================== Sidebar Menu Js End =================



    // //============================ Scroll To Top Icon Js Start =========
    var btn = $('.scroll-top');

    $(window).scroll(function () {
      if ($(window).scrollTop() > 300) {
        btn.addClass('show');
      } else {
        btn.removeClass('show');
      }
    });

    btn.on('click', function (e) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: 0 }, '300');
    });

    // ========================== Header Hide Scroll Bar Js Start =====================
    $('.navbar-toggler.header-button').on('click', function () {
      $('body').toggleClass('scroll-hide-sm');
    });
    $('.body-overlay').on('click', function () {
      $('body').removeClass('scroll-hide-sm');
    });
    // ========================== Header Hide Scroll Bar Js End =====================

    // ========================== Small Device Header Menu On Click Dropdown menu collapse Stop Js Start =====================
    $('.dropdown-item').on('click', function () {
      $(this).closest('.dropdown-menu').addClass('d-block');
    });
    // ========================== Small Device Header Menu On Click Dropdown menu collapse Stop Js End =====================

    // ========================== Add Attribute For Bg Image Js Start =====================
    $('.bg-img').css('background', function () {
      var bg = 'url(' + $(this).data('background-image') + ')';
      return bg;
    });
    // ========================== Add Attribute For Bg Image Js End =====================


    // ================== Password Show Hide Js Start ==========
    $('.toggle-password').on('click', function () {
      $(this).toggleClass('fa fa-eye');
      var input = $(this).siblings('input');
      if (input.attr('type') == 'password') {
        input.attr('type', 'text');
      } else {
        input.attr('type', 'password');
      }
    });
    // =============== Password Show Hide Js End =================


    $('.moreless-button').click(function () {
      $('.moretext').slideToggle();
      if ($('.moreless-button').text() == "Less....") {
        $(this).text("More....")
      } else {
        $(this).text("Less....")
      }
    });

    $('.review-prompt-title').on('click', function () {
      $(this).toggleClass('arrow-icon');
      $('.review-prompt__content').slideToggle('slow');
    });

    // ================== Sidebar Menu Js Start ===============
    // Sidebar Dropdown Menu Start
    $('.has-dropdown > a').click(function () {
      $('.sidebar-submenu').slideUp(200);
      if ($(this).parent().hasClass('active')) {
        $('.has-dropdown').removeClass('active');
        $(this).parent().removeClass('active');
      } else {
        $('.has-dropdown').removeClass('active');
        $(this).next('.sidebar-submenu').slideDown(200);
        $(this).parent().addClass('active');
      }
    });
    // ===================== Sidebar Menu Js End =================

    // ==================== Dashboard User prompt Dropdown Start ==================
    $('.user-info__button').on('click', function (e) {
      e.stopPropagation();
      $(this).parent().find('.user-info-dropdown').toggleClass('show');
    });
    $('.user-info__button').attr('tabindex', -1).focus();

    $('.user-info__button').on('focusout', function () {
      $('.user-info-dropdown').removeClass('show');
    });
    // ==================== Dashboard User prompt Dropdown End ==================


    // CURSOR

    function mim_tm_cursor() {

      var myCursor = jQuery('.mouse-cursor');

      if (myCursor.length) {
        if ($("body")) {

          const e = document.querySelector(".cursor-inner"),
            t = document.querySelector(".cursor-outer");
          let n, i = 0,
            o = !1;
          window.onmousemove = function (s) {
            o || (t.style.transform = "translate(" + s.clientX + "px, " + s.clientY + "px)"), e.style.transform = "translate(" + s.clientX + "px, " + s.clientY + "px)", n = s.clientY, i = s.clientX
          }, $("body").on("mouseenter", "a, .cursor-pointer", function () {
            e.classList.add("cursor-hover"), t.classList.add("cursor-hover")
          }), $("body").on("mouseleave", "a, .cursor-pointer", function () {
            $(this).is("a") && $(this).closest(".cursor-pointer").length || (e.classList.remove("cursor-hover"), t.classList.remove("cursor-hover"))
          }), e.style.visibility = "visible", t.style.visibility = "visible"
        }
      }
    };
    mim_tm_cursor()

    $(window).scroll(function () {
      var scroll = $(window).scrollTop();
      if (scroll >= 500) {
        $(".back-to-top-icon").addClass("show");
      } else {
        $(".back-to-top-icon").removeClass("show");
      }
    });

  });
  // ==========================================
  //      End Document Ready function
  // ==========================================

  // ========================= Preloader Js Start =====================
  $(window).on('load', function () {
    $('.preloader').fadeOut();
  });
  // ========================= Preloader Js End=====================

  $('.header-search-btn').on('click', function () {
    $('.header-search').toggleClass('show');
    if ($('.header-search').hasClass('show')) {
      $(this).html('<i class="la la-times"></i>');
    } else {
      $(this).html('<i class="la la-search"></i>');
    }
  });

  // ========================= filter Js start=====================

  $(".filter-task").on("click", (function () {
    $(".sidebar-overlay").addClass("active show");
    $(".filter-sidebar").addClass("active")
  }));

  $(".side-sidebar-close-btn, .sidebar-overlay").on("click", (function () {
    $(".sidebar-overlay").removeClass("active show");
    $(".filter-sidebar").removeClass("active")
  }));

  // ========================= filter Js end=====================

  // ========================= header bottom menu Js start=====================

  function initializeTabs() {
    const $allLinks = $('.tabs-container a');
    const $rightArrow = $('.tabs-container .right-arrow .next');
    const $leftArrow = $('.tabs-container .left-arrow .pre');
    const $tabList = $('.tabs-container ul');

    const removeAllLinkActive = () => {
      $allLinks.removeClass('active');
    }

    $allLinks.on('click', function () {
      removeAllLinkActive();
      $(this).addClass('active');
    });

    $tabList.on('scroll', handleScroll);

    function handleScroll() {
      const MIN_SCROLL = 20;
      const scrollLeft = $tabList.scrollLeft();
      const max = $tabList[0].scrollWidth - $tabList.outerWidth() - MIN_SCROLL;

      if (scrollLeft >= MIN_SCROLL) {
        $leftArrow.parent().addClass('active');
      } else {
        $leftArrow.parent().removeClass('active');
      }

      if (scrollLeft >= max) {
        $rightArrow.parent().removeClass('active');
      } else {
        $rightArrow.parent().addClass('active');
      }
    }

    $leftArrow.on('click', function () {
      $tabList.scrollLeft($tabList.scrollLeft() - 200);
    });

    $rightArrow.on('click', function () {
      $tabList.scrollLeft($tabList.scrollLeft() + 200);
      handleScroll();
    });
  }

  // Call the function to initialize the tabs
  initializeTabs();


  // ========================= header bottom menu Js end=====================


})(jQuery);
