"use strict";

var Like =
{
    doToggle : function(self, idPage)
    {
        if ( $(self).hasClass('active') ) {
            Like.doRemove(self, idPage);
        } else {
            Like.doAdd(self, idPage);
        }
    },

    doAdd : function(self, idPage)
    {
        $.post( window.lang + "like/add/" + idPage,
            function(data){}, "json")
            .done(function(data) {
                toastr.success(data.message);
                $('#like_count, .js_like_count').html(data.count);
                $('.js_like_total').html(data.total);
                if (data.count) {
                    $('#like_count, .js_like_count').show();
                    $('.like_widget').show();
                }
                $(self).addClass('active');
                $('.favorite[data-product='+idPage+']').addClass('active');

                if(data.product) {
                    //GA4
                    data.product.item_list_name = $(self).closest('.ga4_item_list_name').data('item_list_name');
                    dataLayer.push({ecommerce: null});
                    dataLayer.push({
                        event: "add_to_wishlist",
                        ecommerce: {
                            items: [
                                {
                                    item_name: data.product.item_name,
                                    item_id: data.product.item_id,
                                    price: data.product.price,
                                    item_brand: data.product.item_brand,
                                    item_category: data.product.item_category,
                                    item_category2: data.product.item_category2,
                                    item_list_name: data.product.item_list_name
                                }]
                        }
                    });
                }
                //

              })
            .fail(function(data) {
                var errorResult = jQuery.parseJSON(data.responseText);
                toastr.error(errorResult.message);
            });
    },

    doRemove : function(self, idPage)
    {
        $.post( window.lang + "like/delete/" + idPage,
            function(data){}, "json")
            .done(function(data) {
                toastr.success(data.message);
                $('#like_count, .js_like_count').html(data.count);
                $('.js_like_total').html(data.total);
                if (!data.count) {
                    $('#like_count, .js_like_count').hide();
                    $('.like_widget').hide();
                }
                $('#item-' + idPage).remove();
                $(self).removeClass('active');
                $('.favorite[data-product='+idPage+']').removeClass('active');
                if ($(self).hasClass('favorite_profile')) {
                    location.href = location.href;
                }
            })
            .fail(function(data) {
                var errorResult = jQuery.parseJSON(data.responseText);
                toastr.error(errorResult.message);
            });
    },
    info : function() {
        $.post( window.lang + "like/info",
            function(data){}, "json")
            .done(function(data) {
                $('#like_count, .js_like_count').html(data.count);
                $('.js_like_total').html(data.total);
                if (!data.count) {
                    $('#like_count, .js_like_count').hide();
                    $('.like_widget').hide();
                }
            })
            .fail(function(data) {
                var errorResult = jQuery.parseJSON(data.responseText);
                toastr.error(errorResult.message);
            });
    }
};
