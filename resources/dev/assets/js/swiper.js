// свайпери

if (el('.title-swiper').length>0) {
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


if (el('.product-swiper').length>0) {
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

if (el('.best-product-swiper').length>0) {
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

if (el('.new-product-swiper').length>0) {
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

if (el('.reviews-swiper').length>0) {
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

if (el('.bank-swiper').length>0) {
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
    if (el('.gallery-thumbs').length>0) {
        var swiper7 = new Swiper(".gallery-thumbs", {
            spaceBetween: 8,
            slidesPerView: 8,


            direction: 'vertical',
            navigation: {
                nextEl: ".gallery-next",
                prevEl: ".gallery-prev",
            },
        });
    }

    if (el('.gallery-swiper').length>0) {
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
    }

})
ready(function() {
    let initSliderTimeout;
    if (el('.table-swiper').length > 0 || el('.comparison-prod-swiper').length > 0 || el('.comparison-fixed-swiper').length > 0) {
        console.log('addEventListener');

        // Проверяем, изменился ли нужный элемент, и предотвращаем многократные вызовы
        Livewire.hook('morph.updating', ({el, component, toEl, skip, childrenOnly}) => {
            // Проверка, что элемент содержит слайдеры, и слайдер еще не инициализирован
            if ((el.querySelector('.table-swiper') || el.querySelector('.comparison-prod-swiper') || el.querySelector('.comparison-fixed-swiper'))) {
                console.log('Livewire hook: morph.updating');
                clearTimeout(initSliderTimeout);
                // Инициализация слайдера
                initSliderTimeout = setTimeout(function () {
                    initCompareSlider();
                }, 500);
            }
        });
    }
});
var swiper9, swiper15, swiper16;

function initCompareSlider(){
    console.log('initCompareSlider');
    swiper9 = false; swiper15 = false; swiper16 = false;
    if (el('.table-swiper').length>0) {
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
    }
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
    if (el('.comparison-fixed-swiper').length>0) {
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
                slideChange: function (me) {
                    sl = (me.realIndex + 1)
                    if (el('.fixed-comparison-row').hasClass('fixed')) {
                        el('.comparison-prod-swiper .custom-pagiation span:nth-child(' + sl + ')').trigger('click')
                    }
                }
            },

        });
    }
}


if (el('.viewed-swiper').length>0) {
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
}

if (el('.cart-swiper').length>0) {
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
}

ready(function() {

    if (el('.blog-swiper').length>0){
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
    }

    if (el('.gallery-thumbs-1').length>0) {
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
    }

    if (el('.gallery-swiper-1').length>0) {
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
    }

})