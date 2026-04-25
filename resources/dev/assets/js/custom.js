let mobile_media = window.matchMedia('(max-width: 1080px)');
let pc_media = window.matchMedia('(min-width: 1180px)');
let pc = window.matchMedia('(max-width: 1180px)');
let mob_media = window.matchMedia('(max-width: 850px)');
let tablet_media = window.matchMedia('(max-width: 992px)');
// авто перехід на наступний інпут

event(".input-num-row input","input",function(selector){
if(selector.val().length == selector.attr('maxlength')){
      selector.next('input').trigger('focus');
    }
})

event('.baner .closer', 'click', function(selector){
    el('.baner').remove();
})

event('.custom-select .select-row','click',function(selector){
  let selectedText = selector.text();
  let inputWidth = selector.position().width
  selector.parent().parent().findIn('.select-row').removeClass('current');
  selector.toggleClass('current');
  selector.parents('.visible').findIn('input').val(selectedText);
  //selector.parents('.visible').findIn('input').css('width', inputWidth+10 +'px');
})

 
// таби 

event('.product-card-color','click',function(selector){
  el('.colors .product-card-color').removeClass('current');
  selector.toggleClass('current');
  data_color = selector.attr('data-color');
  selector.parent().parent().findIn('.image').fadeOut(0);
  selector.parent().parent().findIn('.image-'+data_color).fadeIn(100);
})
/*event('.product-tabs .tab','click',function(selector){
  el('.product-tabs .tab').removeClass('current');
  selector.toggleClass('current');
  data_swiper = selector.attr('data-swiper');
  el('.product-screen .screens .screen').fadeOut(0);
  el('.product-screen .screens .screen-'+data_swiper).fadeIn(100);
  return false
})*/
/*event('.product-title-screen .product-card-color','click',function(selector){
  el('.product-title-screen .colors .product-card-color').removeClass('current');
  selector.toggleClass('current');
  data_color = selector.attr('data-color');
  selector.parents('.product-title-screen__wrap').findIn('.screen').fadeOut(0);
  selector.parents('.product-title-screen__wrap').findIn('.screen-'+data_color).fadeIn(100);
})*/
event('.blog-tabs .blog-tab','click',function(selector){
  el('.blog-tabs .blog-tab').removeClass('current');
  selector.toggleClass('current');
  data_blog = selector.attr('data-blog-cat');
  el('.blog .screen').fadeOut(0);
  el('.blog .screen-'+data_blog).fadeIn(100);
})

event('.form-section .tab','click',function(selector){
  el('.form-section .tab').removeClass('current');
  selector.toggleClass('current');
  data_screen = selector.attr('data-screen');
  el('.form-section .screen').fadeOut(0);
  el('.form-section .screen-'+data_screen).fadeIn(200);
})

event('.comparison .tab','click',function(selector){
  el('.comparison .tab').removeClass('current');
  selector.toggleClass('current');
  data_screen = selector.attr('data-comparison-screen');
  el('.comparison .screen').fadeOut(0);
  el('.comparison .screen-'+data_screen).fadeIn(200);
  el('.comparison .screen').removeClass('active');
  el('.comparison .screen-'+data_screen).toggleClass('active');
})






// ховери

if(pc_media.matches){
event('.product-card','mouseover',function(selector,event){
  let height = selector.findIn('.hidden-block').position().height
  selector.findIn('.before').css('height', `calc(100% + ${height + 1}px)`);
  selector.css('z-index', '2');
  if(el('.product-screen').length>0){
    el('.product-screen').css('z-index', '1');
    selector.parents('.product-screen').css('z-index', '5');
    el('.show-all-prod').css('z-index', '1');
  }
  
   })
    event('.product-card','mouseout',function(selector,event){
      if(el('.product-screen').length>0){
        el('.product-screen').css('z-index', '1');
      el('.show-all-prod').css('z-index', '5');
      }
      selector.findIn('.before').css('height', '100%');
      selector.css('z-index', '1');
})
}

if(el('#hover-container').length>0){
  document.getElementById('hover-container').addEventListener('mouseleave',function(event){
  if (![event.target].parents('.hover-wrap')[0]){
    el('.title-menu').css('display', 'none');
    el('.title-side-bar').removeClass('not-rounded')
  }
},true);
}

event('.title .title-side-bar li','mouseover',function(selector,event){
  menu = selector.attr('data-menu');
    el('.title-menu').css('display', 'none');
    el('.title-menu-'+menu).css('display', 'block');  
    if (selector.hasClass('menu-item-has-children')){
      el('.title-side-bar').addClass('not-rounded');
    }else{
      el('.title-side-bar').removeClass('not-rounded')
    }
   })




// кліки
/*event('.get-edit-order', 'click', function(selector) {
  el('.right-side-bar .prod-row').toggleClass('active');
})*/
event('.like','click',function(selector){
  selector.toggleClass('active');
})
event('.compare','click',function(selector){
  selector.toggleClass('active');
})
/*event('.read-more-btn', 'click', function(selector){
  selector.parent().toggleClass('active');
  selector.parent().parent().findIn('.description').toggleClass('hidden');
})*/
event('.more-char-btn', 'click', function(selector){
  selector.parent().toggleClass('active');
  selector.parent().parent().findIn('.prod-characteristics__rows').toggleClass('hidden');
})
event('.show-all-options', 'click', function(selector){
  selector.toggleClass('active');
  selector.parent().findIn('.labels-wrapper .hidden-row-wrapper').slideToggle(200);
})
//event('.custom-select .visible', 'click', function(selector){
//  selector.parent().toggleClass('active');
//})

event('.cat-cell .visible-row', 'click', function(selector){
  selector.parent().toggleClass('active');
})
/*event('.cat-cell .show-all-label', 'click', function(selector){
  selector.parent().findIn('.hidden-row').toggleClass('full');
  selector.toggleClass('active');
})*/

event('.get-more', 'click', function(selector){
  el('.change-popup').fadeIn(200);
  
})
event('.get-chalenge-adress', 'click', function(selector){
  el('.chalenge-adress-popup').fadeIn(200);
  
})
event('.get-chalenge-new-post', 'click', function(selector){
  el('.new-post-popup').fadeIn(200);
  
})
event('.get-cart', 'click', function(selector){
  el('.cart-popup').fadeIn(200);
  //el('html').toggleClass('hidden');
  return false
})
event('.get-review', 'click', function(selector){
  el('.review-popup').fadeIn(200);
  //el('html').toggleClass('hidden');
})
event('.get-edit', 'click', function(selector){
  el('.edit-acc-popup').fadeIn(200);
  //el('html').toggleClass('hidden');
})
event('.add-adress', 'click', function(selector){
  el('.add-adress-popup').fadeIn(200);
  //el('html').toggleClass('hidden');
})
event('.get-edit-adress', 'click', function(selector){
  el('.edit-adress-popup').fadeIn(200);
  //el('html').toggleClass('hidden');
})
event('.popup-close, .popup .closer', 'click', function(selector){
  el('.main-popup-wrap').fadeOut(200);
  //el('html').removeClass('hidden');
})
event('.acc-section .top', 'click', function(selector){
  selector.parent().toggleClass('active');
})


event('.faq-row .icon', 'click', function(selector){
  selector.parent().parent().toggleClass('active');
})


event('.history-row .icon', 'click', function(selector){
  selector.parent().parent().toggleClass('active');
})


event('.account-main .radio input', 'click', function(selector){
  el('.account-main .radio-row').removeClass('active');
  selector.parent().parent().addClass('active');
})

event('.how-it-work .visible .icon', 'click', function(selector){
  el('.how-it-work .hidden ').slideToggle(300);
  selector.toggleClass('active');
})

event('.account-reviews-row .icon', 'click', function(selector){
  selector.parent().parent().findIn('.hidden').slideToggle(300);
  selector.toggleClass('active');
})
event('.login-popup .get-login', 'click', function(selector){
  el('.login-popup .sc-1').fadeOut(200);
  el('.login-popup .sc-3').fadeIn(200);
})
event('.login-popup .back-login', 'click', function(selector){
  el('.login-popup .sc-3').fadeOut(200);
  el('.login-popup .sc-1').fadeIn(200);
})
event('.get-login-popup', 'click', function(selector){
  el('.login-popup').fadeIn(200);
  //el('html').toggleClass('hidden')
  el('.mobile-menu').fadeOut(200);
})
event('.get-catalog', 'click', function(selector){
  el('.catalog-popup').fadeIn(200);
  //el('html').toggleClass('hidden')
})
event('.right-side-bar .promo-wrap .visible-row', 'click', function(selector){
  selector.toggleClass('active');
  el('.right-side-bar .promo-wrap .hidden-row').slideToggle(200);
})

event('.form-section .get-sc-2', 'click', function(selector){
  el('.form-section .sc-1').fadeOut(200);
  el('.form-section .sc-2').fadeIn(200);
})
event('.form-section .back-to-sc-1', 'click', function(selector){
  el('.form-section .sc-2').fadeOut(200);
  el('.form-section .sc-1').fadeIn(200);
})

event('.form-section-2 .radio input', 'click', function(selector){
  el('.form-section-2 .radio-row').removeClass('current');
  selector.parent().parent().parent().addClass('current');
  var val = selector.val()
  //el('.droppdown-wrap').fadeOut(100);
  //el('.'+val).fadeIn(200);
})
event('.form-section-3 .radio input', 'click', function(selector){
  el('.form-section-3 .radio-row').removeClass('current');
  selector.parent().parent().parent().addClass('current');
  var val = selector.val()
  //el('.droppdown-wrapper').fadeOut(100);
  //el('.'+val).fadeIn(200);
})
/*event('.get-comment-area', 'click', function(selector){
  el('.comment-area').slideToggle(200);
})*/
event('.bank-droppdown .row .visible', 'click', function(selector){
  el('.bank-droppdown .row').removeClass('current');
  selector.parent().addClass('current');
})
event('.cart-popup .promo-wrap .visible-row', 'click', function(selector){
  selector.toggleClass('active');
  el('.cart-popup .promo-wrap .hidden-row').slideToggle(200);
})
 
event('.cart-popup .add-service', 'click', function(selector){
  selector.toggleClass('active');
  selector.parent().findIn('.labels-wrapper').slideToggle(200);
})
event('.cat-row .visible', 'click', function(selector){
  selector.toggleClass('active');
  selector.parent().findIn('.hidden').slideToggle(200);
})
event('.get-mobile-catalog', 'click', function(selector){
  selector.parent().findIn('.hidden-row').slideToggle(200);
})

event('.footer .menu-column-heading', 'click', function(selector){
  selector.parent().findIn('.hidden').slideToggle(200);
  selector.toggleClass('active');
  selector.parent().toggleClass('active');
})

event('.hidden-button-block .btn', 'click', function(selector){
  selector.parent().findIn('.btn').removeClass('current');
  selector.toggleClass('current');
  screen = selector.attr('data-screen');
  selector.parents('.bottom-row').findIn('.screen').fadeOut(200);
  selector.parents('.bottom-row').findIn('.screen-'+screen).fadeIn(200);
})
event('.gam', 'click', function(selector){
  el('.mobile-menu').fadeIn(200);
  //el('html').toggleClass('hidden');
  el('.mobile-menu .header-mobile-menu').fadeIn(100);
  el('.mobile-menu .header-mobile-sub-menu').fadeOut(0);
})
event('.mobile-menu .menu-item-has-children .side-bar-link', 'click', function(selector){
  menu = selector.parent().attr('data-menu');
  el('.mobile-menu .header-mobile-menu').fadeOut(200);
  el('.mobile-menu .header-mobile-sub-menu-'+menu).fadeIn(200);
})
event('.mobile-menu .back', 'click', function(selector){
  el('.mobile-menu .header-mobile-menu').fadeIn(200);
  el('.mobile-menu .header-mobile-sub-menu-').fadeOut(200);
})
// event('.mobile-menu .lang-menu .menu-item-has-children', 'click', function(selector){
//   // selector.parent().findIn('.sub-menu').fadeToggle(200);
//   selector.toggleClass('active');
// })

event('.catalog-hidden-button .btn', 'click', function(selector){
  selector.toggleClass('active');
  el('.catalog-level-1').toggleClass('hidden');
})
// event('.hidden-filter-buttons .get-sort', 'click', function(selector) {
//   selector.parent().findIn('.sub-menu').fadeToggle(200);
// })
event('.get-filter', 'click', function(selector) {
  //el('html').toggleClass('hidden');
  el('.filter-popup').fadeIn(200);
})
event('.product-title-screen__wrap .right .columns .column .labels .label', 'click', function(selector) {
  el('.product-title-screen__wrap .right .columns .column .labels .label').removeClass('current');
  selector.toggleClass('current');
})
event('.scroll-to', 'click', function(selector){
  let parameter1 = el(selector.attr('href'))
  let parameter2 =  500
  let parameter3 = 300

  scrollTo(parameter1,parameter2,parameter3)
})
event('.scroll-to-seo', 'click', function(selector){
  let parameter1 = el(selector.attr('href'))
  let parameter2 =  500
  let parameter3 = 100

  scrollTo(parameter1,parameter2,parameter3)
})
event('.sticky-row .tabs .tab', 'click', function(selector){
  el('.sticky-row .tabs .tab').removeClass('current');
  selector.toggleClass('current')
})

event('.input-placeholder-clear', 'click', function(selector) {
  selector.parent().findIn('input')[0].value = '';
})
event('.gallery-swiper .swiper-slide', 'click', function(selector) {
  //el('html').addClass('hidden');
  el('.product-gallery-popup').fadeIn(200);
})
// event('.get-edit-order', 'click', function(selector) {
//   el('.right-side-bar .prod-row').toggleClass('active');
// })
/*event('.other-cust', 'click', function(selector){
  el('.droppdown-other').slideToggle(200);
})*/





//scroll 

 window.addEventListener('scroll', function() {
      // Отримання висоти прокрутки сторінки
      var scrollHeight = window.scrollY || window.pageYOffset;

      // Перевірка, чи прокручено більше 200 пікселів
      if (scrollHeight > 200) {
        // Додавання класу, якщо прокручено більше 200 пікселів
        el('.sticky-row .hidden-row').addClass('visible');
      } else {
        // Видалення класу, якщо прокручено менше 200 пікселів
        el('.sticky-row .hidden-row').removeClass('visible');
      }
    });
  window.addEventListener('scroll', function() {
      // Отримання висоти прокрутки сторінки
      var scrollHeight = window.scrollY || window.pageYOffset;

      // Перевірка, чи прокручено більше 200 пікселів
      if (scrollHeight > 60) {
        // Додавання класу, якщо прокручено більше 200 пікселів
        el('.sticky-row').addClass('fixed');
      } else {
        // Видалення класу, якщо прокручено менше 200 пікселів
        el('.sticky-row').removeClass('fixed');
      }
    });

   window.addEventListener('scroll', function() {
      // Отримання висоти прокрутки сторінки
      var scrollHeight = window.scrollY || window.pageYOffset;

      // Перевірка, чи прокручено більше 200 пікселів
      if (scrollHeight > 620) {
        // Додавання класу, якщо прокручено більше 200 пікселів
        el('.fixed-comparison-row').addClass('fixed');
      } else {
        // Видалення класу, якщо прокручено менше 200 пікселів
        el('.fixed-comparison-row').removeClass('fixed');
      }
    });

// додавання та видалення товарів у корзині
ready(function(){
  event('.input-group .minus-item', 'click' , function(selector){
    numb = parseInt(selector.parent().findIn('input').val());
    if(numb > 1){
      numb=numb-1;
    }
    selector.parent().findIn('input').val(numb);
    if(numb <= 1){
      selector.attr('disabled', true);
    }
    if(numb < 999){
      selector.parent().findIn('.plus-item').removeAttr('disabled')
    }
  })
})

ready(function(){
  event('.input-group .plus-item', 'click' , function(selector){
    numb = parseInt(selector.parent().findIn('input').val());
    numb = numb+1;
    if(numb > 1){
      selector.parent().findIn('.minus-item').removeAttr('disabled');
    }
    selector.parent().findIn('input').val(numb);
    if(numb > 998){
      selector.attr('disabled', true);
    }
  })
})




// рейтинг зірки
/*event('.set_rating_wrap span', 'click', function(selector){
  rating= index(selector);
  selector.parent().findIn('.active').removeClass('active')
  selector.toggleClass('active')
  selector.parent().parent().findIn('input[name="rating"]').val(rating)
  if (selector.parent().findIn('.active').length>0){
    selector.parent().addClass('active')
  }else{
    selector.parent().removeClass('active')
  }
})*/




// свайпери
if(typeof document.getElementsByClassName("title-swiper") !== 'undefined'){
 var swiper1 = new Swiper(".title-swiper", {
      slidesPerView: 1,
      spaceBetween: 20,
       loop: true,
      autoplay: {
      delay: 2500,
        disableOnInteraction: false,
      },
      speed: 500,
      navigation: {
        nextEl: ".title-swiper-btn-next",
        prevEl: ".title-swiper-btn-prev",
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
    });
}

if(typeof document.getElementsByClassName("product-swiper") !== 'undefined'){
 var swiper2 = new Swiper(".product-swiper ", {
      slidesPerView: 2,
      spaceBetween: 12,
      loop: true,
      autoplay: {
      delay: 2500,
        disableOnInteraction: false,
      },
      speed: 1000,
      navigation: {
        nextEl: ".product-swiper-btn-next",
        prevEl: ".product-swiper-btn-prev",
      },
      pagination: {
        el: ".product-swiper-pagination",
  
      },
       breakpoints: {
        768: {
          slidesPerView: 3,
        },
         1080: {
          slidesPerView: 4,
          spaceBetween: 16,
        },
      }

    });
}
if(typeof document.getElementsByClassName("best-product-swiper") !== 'undefined') {
    var swiper3 = new Swiper(".best-product-swiper ", {
        slidesPerView: 2,
        spaceBetween: 12,
        loop: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        speed: 1000,
        navigation: {
            nextEl: ".best-product-swiper-btn-next",
            prevEl: ".best-product-swiper-btn-prev",
        },
        pagination: {
            el: ".swiper-pagination",

        },
        breakpoints: {
            768: {
                slidesPerView: 3,
            },
            1080: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
        }

    });
}
if(typeof document.getElementsByClassName("new-product-swiper") !== 'undefined') {
    var swiper4 = new Swiper(".new-product-swiper ", {
        slidesPerView: 2,
        spaceBetween: 12,
        loop: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        speed: 1000,
        navigation: {
            nextEl: ".new-product-swiper-btn-next",
            prevEl: ".new-product-swiper-btn-prev",
        },
        pagination: {
            el: ".swiper-pagination",

        },
        breakpoints: {
            768: {
                slidesPerView: 3,
            },
            1080: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
        }

    });
}

function initReviewsSlider() {
    if(typeof document.getElementsByClassName("reviews-swiper") !== 'undefined') {
        var swiper5 = new Swiper(".reviews-swiper ", {
            slidesPerView: 1,
            spaceBetween: 12,
            loop: true,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false,
            },
            speed: 1000,
            // autoHeight: true,
            navigation: {
                nextEl: ".reviews-product-swiper-btn-next",
                prevEl: ".reviews-product-swiper-btn-prev",
            },
            pagination: {
                el: ".swiper-pagination",

            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1080: {
                    slidesPerView: 3,
                    spaceBetween: 16,
                },
            }

        });
    }
}
initReviewsSlider();

if(typeof document.getElementsByClassName("bank-swiper") !== 'undefined') {
    var swiper6 = new Swiper(".bank-swiper ", {
        slidesPerView: 2.5,
        spaceBetween: 8,
        // autoHeight: true,
        navigation: {
            nextEl: ".bank-swiper-button-next",
            prevEl: ".bank-swiper-button-prev",
        },
        breakpoints: {
            1080: {
                slidesPerView: 3.5,
            },
        }

    });
}

ready(function() {
  var swiper7 = new Swiper(".gallery-thumbs", {
      spaceBetween: 8,
      slidesPerView: 8,
      
   
      direction: 'vertical',
       navigation: {
        nextEl: ".gallery-next",
        prevEl: ".gallery-prev",
      },
    });
    var swiper8 = new Swiper(".gallery-swiper", {
      spaceBetween: 10,
      slidesPerView: 1,
      keyboard: {
        enabled: true,
      },
      thumbs: {
        swiper: swiper7,
      },
      pagination: {
        el: ".swiper-pagination",
      },
      navigation: {
        nextEl: ".gallery-swiper-btn-next",
        prevEl: ".gallery-swiper-btn-prev",
      },
    });

})



 ready(function() {
  var swiper9, swiper15, swiper16;
  swiper16 = new Swiper(".table-swiper", {
      slidesPerView: 2,
      // spaceBetween: 12,
      allowTouchMove: false,
      navigation: {
        nextEl: ".table-next",
        prevEl: ".table-prev",
      },
      pagination: {
        el: ".swiper-pagination",
  
      },
       breakpoints: {
        768: {
          slidesPerView: 3,
        },
         1080: {
          slidesPerView: 4,
          // spaceBetween: 16,
          allowTouchMove: false,
        },
      }

    });

  el('.comparison-prod-swiper').each(function(selector){
    let ind=selector.attr('data-slider')
   swiper9 = new Swiper(".comparison-prod-swiper[data-slider='"+ind+"']", {
      slidesPerView: 2,
      spaceBetween: 0,
      // allowTouchMove: false,
      navigation: {
        nextEl: ".comparison-prod-swiper[data-slider='"+ind+"'] ~ .comparison-swiper-btn-next",
        prevEl: ".comparison-prod-swiper[data-slider='"+ind+"'] ~ .comparison-swiper-btn-prev",
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
     
       breakpoints: {
        768: {
          slidesPerView: 3,
        },
         1180: {
          slidesPerView: 4,
          spaceBetween: 0,
          allowTouchMove: false,
        },
      },
      on: {
        slideChange: function(me){
          sl = (me.realIndex+1)
          if (!el('.screen.active .fixed-comparison-row').hasClass('fixed')){
            el('.screen.active .comparison-fixed-swiper .custom-pagiation span:nth-child('+sl+')').trigger('click')
          }
        }
      },

    });
 })

if (el('.comparison-prod-swiper').length>0){
   setInterval(function(){
    if (el('.screen.active .fixed-comparison-row').hasClass('fixed')){
      getstyle=el('.screen.active .comparison-fixed-swiper .swiper-wrapper').attr('style');
      el('.screen.active .table-swiper .swiper-wrapper').attr('style',getstyle)
      // el('.comparison-prod-swiper .swiper-wrapper').attr('style',getstyle)
    }else{
      getstyle=el('.screen.active .comparison-prod-swiper .swiper-wrapper').attr('style');
      el('.screen.active .table-swiper .swiper-wrapper').attr('style',getstyle)
      // el('.comparison-fixed-swiper .swiper-wrapper').attr('style',getstyle)
    }
   },1)
 }

   swiper15 = new Swiper(".comparison-fixed-swiper", {
      slidesPerView: 2,
      navigation: {
        nextEl: ".comparison-fixed-swiper-btn-next",
        prevEl: ".comparison-fixed-swiper-btn-prev",
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
       breakpoints: {
        768: {
          slidesPerView: 3,
        },
         1180: {
          slidesPerView: 4,
          allowTouchMove: false,
        },
      },
      on: {
        slideChange: function(me){
          sl = (me.realIndex+1)
          if (el('.fixed-comparison-row').hasClass('fixed')){
            el('.comparison-prod-swiper .custom-pagiation span:nth-child('+sl+')').trigger('click')
          }
        }
      },

    });

  
 })


 var swiper10 = new Swiper(".viewed-swiper ", {
      slidesPerView: 2,
      spaceBetween: 12,
      navigation: {
        nextEl: ".viewed-swiper-btn-next",
        prevEl: ".viewed-swiper-btn-prev",
      },
      pagination: {
        el: ".swiper-pagination",
  
      },
       breakpoints: {
        1080: {
          slidesPerView: 3,
          spaceBetween: 16,
        },
      }

    });
  var swiper11 = new Swiper(".cart-swiper ", {
      slidesPerView: 2,
      spaceBetween: 12,
      loop: true,
          autoplay: {
          delay: 1500,
            disableOnInteraction: false,
          },
          speed: 1000,
      navigation: {
        nextEl: ".cart-swiper-btn-next",
        prevEl: ".cart-swiper-btn-prev",
      },
      pagination: {
        el: ".swiper-pagination",
  
      },
       breakpoints: {
        768: {
          slidesPerView: 3,
        },
         1080: {
          slidesPerView: 5,
          spaceBetween: 12,
        },
      }

    });

   var swiper12 = new Swiper(".blog-swiper ", {
      slidesPerView: 1,
      spaceBetween: 16,
      pagination: {
        el: ".blog-pagination",
  
      },
       breakpoints: {
        768: {
          slidesPerView: 2,
        },
        992: {
          slidesPerView: 3,
        },
        1080: {
          slidesPerView: 4,
        },
      }

    });
     
ready(function() {
  var swiper13 = new Swiper(".gallery-thumbs-1", {
      spaceBetween: 8,
      slidesPerView: 8,
      
   
      direction: 'vertical',
       navigation: {
        nextEl: ".gallery-next",
        prevEl: ".gallery-prev",
      },
      breakpoints: {
        768: {
          slidesPerView: 8,
        },
         1080: {
          slidesPerView: 6,
          
        },
      }
    });
    var swiper14 = new Swiper(".gallery-swiper-1", {
      spaceBetween: 10,
      slidesPerView: 1,
      keyboard: {
        enabled: true,
      },
      thumbs: {
        swiper: swiper13,
      },
      pagination: {
        el: ".swiper-pagination",
      },
      navigation: {
        nextEl: ".gallery-swiper-btn-next",
        prevEl: ".gallery-swiper-btn-prev",
      },
    });

})

// function setTelMask(selector,set=false) {
//   IMask(
//     selector[0],
//     {
//       mask: '+{38} (000) 000-00-00',
//       lazy: set
//     }
//   )
// }
// // маска телефону
// event('.tel-input','click',function(selector){
//   setTelMask(selector,false);
// })

// event('.tel-input','blur',function(selector){
//   setTelMask(selector,true);
// })

// el('.tel-input').each(function(selector){
//   IMask(
//     selector[0],
//     {
//       mask: '+{38} (000) 000-00-00',
//       lazy: false
//     }
//   )
// })

/*
var PhoneInputs = document.querySelectorAll('.tel-input');
var phoneBeginning = '+38 (';
var phoneMask = {
    mask: '+{38} (000) 000-00-00',
};



var phoneValidationSetup = function (phoneInputs, inputMask) {
    phoneInputs.forEach(function (phoneInput) {
        var cellularPhone = IMask(phoneInput, inputMask);
        phoneInput.addEventListener('focus', function () {
            if (cellularPhone.value === '') {
                cellularPhone.value = phoneBeginning;
            }
        });
        phoneInput.addEventListener('blur', function () {
          console.log(cellularPhone.value.length)
            if (cellularPhone.value.length < 19) {
                // cellularPhone.value = phoneBeginning;
                // cellularPhone.phoneMask.reset()
                cellularPhone.value=''
            }
        });
    });
};

phoneValidationSetup(PhoneInputs, phoneMask);
*/

// var inpTel = document.querySelectorAll('.tel-input');
// var mask;
// for(var i = 0; i < inpTel.length; i++) {
//   inpTel[i].addEventListener('focus', function(){
//     mask = IMask(this, {
//         mask: '+{7} (000) 000 00 00',
//         overwrite: true,
//         lazy: false,
//         autofix: true
//     });
//   })
//   inpTel[i].addEventListener('blur', function(){
//     if(this.value.match('_')){
//       mask.masked.reset()
//       console.log('shchos');
//       this.value=''
//     }
//   })
// };
//range

// const rangeInput = document.querySelectorAll(".range-input input"),
// priceInput = document.querySelectorAll(".price-input input"),
// range = document.querySelector(".slider .progress");
// let priceGap = 1000;

// priceInput.forEach((input) => {
//   input.addEventListener("input", (e) => {
//     let minPrice = parseInt(priceInput[0].value),
//       maxPrice = parseInt(priceInput[1].value);

//     if (maxPrice - minPrice >= priceGap && maxPrice <= rangeInput[1].max) {
//       if (e.target.className === "input-min") {
//         rangeInput[0].value = minPrice;
//         range.style.left = (minPrice / rangeInput[0].max) * 100 + "%";
//       } else {
//         rangeInput[1].value = maxPrice;
//         range.style.right = 100 - (maxPrice / rangeInput[1].max) * 100 + "%";
//       }
//     }
//   });
// });

// rangeInput.forEach((input) => {
//   input.addEventListener("input", (e) => {
//     let minVal = parseInt(rangeInput[0].value),
//       maxVal = parseInt(rangeInput[1].value);

//     if (maxVal - minVal < priceGap) {
//       if (e.target.className === "range-min") {
//         rangeInput[0].value = maxVal - priceGap;
//       } else {
//         rangeInput[1].value = minVal + priceGap;
//       }
//     } else {
//       priceInput[0].value = minVal;
//       priceInput[1].value = maxVal;
//       range.style.left = (minVal / rangeInput[0].max) * 100 + "%";
//       range.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
//     }
//   });
// });














if(el('.account-page__side-bar').length>0){
  if (mobile_media.matches) {
    el('.account-page__side-bar a.current')[0].scrollIntoView({
      behavior: 'auto',
      block: 'center',
      inline: "center"
    });
  }
}
if(el('.info-page-side-bar').length>0){
  if (tablet_media.matches) {
    el('.info-page-side-bar a.current')[0].scrollIntoView({
      behavior: 'auto',
      block: 'center',
      inline: "center"
    });
  }
}


// event('.catalog-popup .title-side-bar','scroll', function() {
//   let pos=el('.catalog-popup .title-side-bar .sale').position();
//   console.log(pos)
// if (IsInViewport(el('.catalog-popup .title-side-bar .sale'))){
//   console.log('here')
//   el('.catalog-popup .title-side-bar .before').css('opacity', '0');
// }else{
//   console.log('not-here')
//   el('.catalog-popup .title-side-bar .before').css('opacity', '1');
// }
// })

setInterval(function(){
  obj=el('.catalog-popup .title-side-bar .simplebar-content-wrapper')[0];
  if( obj.scrollTop === (obj.scrollHeight - obj.offsetHeight)){
    // console.log('bottom')
    el('.catalog-popup .title-side-bar .before').css('opacity', '0');
  }else{
    // console.log('top')
    el('.catalog-popup .title-side-bar .before').css('opacity', '1');
  }
},100)


if(mob_media.matches){
   window.addEventListener('scroll', function() {
      // Отримання висоти прокрутки сторінки
      var scrollHeight = window.scrollY || window.pageYOffset;

      // Перевірка, чи прокручено більше 200 пікселів
      if (scrollHeight > 50) {
        // Додавання класу, якщо прокручено більше 200 пікселів
        el('.header').addClass('scrolled');
        el('body').css('padding-top', '56px');
        el('.sticky-row.fixed').css('top', '56px');
        el('.hidden-filter-buttons').css('top', '56px');
        el('.info-page-side-bar').css('top', '56px');
        el('.account-page__side-bar').css('top', '56px');
        el('.comparison .fixed-comparison-row').css('top', '56px');
      } else {
        // Видалення класу, якщо прокручено менше 200 пікселів
        el('.header').removeClass('scrolled');
        el('body').css('padding-top', '116px');
        el('.sticky-row.fixed').css('top', '116px');
        el('.hidden-filter-buttons').css('top', '116px');
        el('.info-page-side-bar').css('top', '116px');
        el('.account-page__side-bar').css('top', '116px');
        el('.comparison .fixed-comparison-row').css('top', '116px');
      }
    });
}
if(tablet_media.matches){
   window.addEventListener('scroll', function() {
      // Отримання висоти прокрутки сторінки
      var scrollHeight = window.scrollY || window.pageYOffset;

      // Перевірка, чи прокручено більше 200 пікселів
      if (scrollHeight > 100) {
        // Додавання класу, якщо прокручено більше 200 пікселів
        el('.mobile-fixed-row-product').addClass('active');
      } else {
        // Видалення класу, якщо прокручено менше 200 пікселів
        el('.mobile-fixed-row-product').removeClass('active');
      }
    });
}
if(el('#get-catalog-button').length>0){
document.getElementById('get-catalog-button').addEventListener('mouseover',function(event){
  // console.log([event.target].parents('.get-catalog-button-wrapper'))
    el('.catalog-popup').fadeIn(200);
   
});
}

if(el('#popup-wrap').length>0){
document.getElementById('popup-wrap').addEventListener('mouseover',function(event){
  if([event.target].hasClass('popup-wrap')){
    el('.catalog-popup').fadeOut(200);

  }
});
}

if(el('#get-catalog-button').length>0){
document.getElementById('get-catalog-button').addEventListener('mouseleave',function(event){
  if (![event.target].parents('.get-catalog-button-wrapper')[0]){
    el('.catalog-popup').fadeOut(200);
  }
},true);
}

event('.catalog-popup .title-side-bar li','mouseover',function(selector,event){
   menu = selector.attr('data-menu');
    el('.catalog-popup .title-menu').css('display', 'none');
    el('.catalog-popup .title-menu-'+menu).css('display', 'block');  
   
})

 window.addEventListener('scroll', function() {
      // Отримання висоти прокрутки сторінки
      var scrollHeight = window.scrollY || window.pageYOffset;

      // Перевірка, чи прокручено більше 200 пікселів
      if (scrollHeight > 60) {
        // Додавання класу, якщо прокручено більше 200 пікселів
        el('.header').addClass('fixed');
        el('.product-title-screen .sticky-row').addClass('top');
        el('.header .header-bottom-row .search .clicable-zone').addClass('top');
        el('.catalog-popup').addClass('top');
        el('.catalog-popup .popup-wrap').addClass('top');
        el('.product-reviews-screen__wrap .right').addClass('top');
        el('.product-title-screen__wrap .left').addClass('top');
        el('.catalog-side-bar.sticky').addClass('top');
        el('.comparison .fixed-comparison-row.fixed').addClass('top');

      } else {
        // Видалення класу, якщо прокручено менше 200 пікселів
        el('.header').removeClass('fixed');
        el('.product-title-screen .sticky-row').removeClass('top');
        el('.header .header-bottom-row .search .clicable-zone').removeClass('top');
        el('.catalog-popup').removeClass('top');
        el('.catalog-popup .popup-wrap').removeClass('top');
        el('.product-reviews-screen__wrap .right').removeClass('top');
        el('.product-title-screen__wrap .left').removeClass('top');
        el('.catalog-side-bar.sticky').removeClass('top');
        el('.comparison .fixed-comparison-row.fixed').removeClass('top');
      }
    });
