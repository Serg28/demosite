"use strict";

var ImageStorage = {

    entity       : 'images',

    current_page : 1,
    last_page    : 1,

    is_loading   : false,
    is_selecting : false,



    init: function()
    {
        ImageStorage.initEvents()

    },

    initEvents: function()
    {
        ImageStorage.initSelectBoxes();

        ImageStorage.initScrollEndlessEvent();

        ImageStorage.initDatePickers();

        ImageStorage.initSelectable();

        ImageStorage.initEditClickEvent();

    },

    initSelectBoxes: function()
    {
        $('select.image-storage-select').select2("destroy").select2();
        $('.select2-hidden-accessible').hide();
    },

    initScrollEndlessEvent: function()
    {
        $(document).scrollTop(0);

        var pagination = $("#dt_basic_paginate ul.pagination");

        if(pagination.length){

            ImageStorage.current_page = parseInt(pagination.find("li.active span").text());
            ImageStorage.last_page    = parseInt(pagination.find("li:eq(-2) a").text());

            $(document).scroll(function() {
                if ($(document).scrollTop() + $(window).height() == $(document).height()) {
                    if ($('.image-storage-container.images-container').length) {
                        ImageStorage.loadMore();
                    }
                }
            });
        }
    },

    initEditClickEvent: function()
    {
        $('.superbox-list').unbind("click");
        $('.superbox-list').click(function(e) {
            if (e.ctrlKey || e.metaKey) {
                $(this).toggleClass('selected').toggleClass('ui-selected');
                ImageStorage.checkSelected();
            }else{
                ImageStorage.getEditForm($(this));
            }
        })
    },

    initPopupClicks: function()
    {
        $('.superbox-close').click(function() {
            ImageStorage.closeSuperBoxPopup();
        });
        jQuery('.j-images-storage').on('click', function() {
            jQuery('.select22').select2("close");
            jQuery('.select2-hidden-accessible').hide();
        });
    },

    initDatePickers: function()
    {
        $('.datepicker').datepicker({
            changeMonth: true,
            prevText: '<i class="fa fa-chevron-left"></i>',
            nextText: '<i class="fa fa-chevron-right"></i>',
            dateFormat: "dd-mm-yy",
            //showButtonPanel: true,
            regional: ["ru"],
            onClose: function (selectedDate) {}
        });
    },

    closePopup: function()
    {
        $('.image-storage-process-popup').hide();
    },

    initSortable: function()
    {
        var $sortable = $('.image-storage-sortable');
        $sortable.sortable(
            {
                items: "> li",
                context: this,
                update: function() {
                    ImageStorage.onChangeGalleryOrder();
                },
                create: function(event, ui) {
                    $('.image-storage-sortable-item').click(function(e) {
                        if (e.ctrlKey || e.metaKey) {
                            ImageStorage.setGalleryPreview(this);
                        }
                    });
                },
            }
        );
        $sortable.disableSelection();
    },

    initSelectable: function()
    {
        var $selectable = $('.image-storage-selectable');

        if ($selectable.hasClass('ui-selectable')) {
            $selectable.selectable("refresh");
            return;
        }

        $selectable.selectable({
            distance: 5,
            filter: '.superbox-list',
            cancel: '.image-storage-popup',
            selected: function (event, ui) {
                $(ui.selected).addClass('selected');
                ImageStorage.checkSelected();
            },
            unselected: function (event, ui) {
                $(ui.unselected).removeClass('selected');
                ImageStorage.checkSelected();
            },
            start: function( event, ui ) {
                ImageStorage.is_selecting = true;
            },
            stop: function( event, ui ) {
                ImageStorage.is_selecting = false;
            },
        });

    },

    initReplaceSrcImageForm: function()
    {
        $('#image-storage-images-sizes-tabs li').click(function(){
            var element = $(this).find("a").attr('href');
            var img = ($("#image-storage-tabs-content").find(element).find("img"));

            if(typeof img.attr('real-src') !== typeof undefined && img.attr('real-src') !== false){
                img.attr('src',img.attr("real-src"));
                img.removeAttr("real-src")
            }
        });
    },

    initCopyToClipboard: function()
    {
        $('.image-storage-btn-clipboard-copy').click(function(){
            var copyText = $(this).prev(".image-storage-btn-download").attr("href"),
                inputStub   = "<input class='copy-input-stub' type='text' value='"+copyText+"'>";

            $(inputStub).insertAfter($(this)).select();
            document.execCommand('copy');
            $(".copy-input-stub").remove();
            TableBuilder.showSuccessNotification('Скопировано в буфер');

        });
    },

    doResetFilters: function()
    {
        TableBuilder.showPreloader();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity,
            data: {
                forget_filters: true
            },
            success : function(response) {
                $('#content_admin').html(response);
                ImageStorage.init();
                TableBuilder.hidePreloader()
            }
        });

    },

    doResetUploadPreloader: function(button)
    {
        $(button).hide().parent().parent().hide();
        $('.image-storage-progress-fail,.image-storage-progress-success').css('width', '0%');
        $('.image-storage-upload-success, .image-storage-upload-fail, .image-storage-upload-upload, .image-storage-upload-total').text("0");
    },

    //common in pages with grid view
    loadMore: function()
    {
        if (ImageStorage.is_loading || ImageStorage.is_selecting) {
            return;
        }

        if(ImageStorage.current_page >= ImageStorage.last_page ){
            return;
        }

        TableBuilder.showPreloader();
        ImageStorage.is_loading = true;

        var next_page = ImageStorage.current_page + 1;
        var data = $('#image-storage-search-form').serializeArray();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/load_more?page="+next_page,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('.superbox').append(response.html);
                    $('.dt-toolbar-footer').replaceWith(response.pagination);
                    ImageStorage.initSelectable();
                    ImageStorage.initEditClickEvent();
                    ImageStorage.is_loading = false;
                    ImageStorage.current_page = next_page;
                    TableBuilder.hidePreloader();

                    history.pushState(null, null, '?page=' + ImageStorage.current_page);
                } else {
                    TableBuilder.hidePreloader();
                    TableBuilder.showErrorNotification('Неудалось загрузить изображения...');
                }
            }
        });


    },

    doSearch: function()
    {
        var data = $('#image-storage-search-form').serializeArray();
        TableBuilder.showPreloader();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity,
            data: data,
            success: function(response) {
                $('#content_admin').html(response);
                ImageStorage.init();
                TableBuilder.hidePreloader();
            }
        });
    },

    openSuperBoxPopup: function(context,currentBlock,html)
    {
        $('.superbox-show').remove();

        $(html).insertAfter(context).css('display', 'block');

        $('html, body').animate({
            scrollTop:$('.superbox-show').position().top - currentBlock.width()
        }, 'medium');

    },

    closeSuperBoxPopup: function()
    {
        $('.superbox-list').removeClass('active');
        $('#image-storage-tabs-content').animate({opacity: 0}, 200, function() {
            $('.superbox-show').slideUp(400, function() {
                $('.superbox-show').remove();
            });
        });
    },

    closeModalForm: function()
    {
        var modalForm = $("#modal_form");
        if(modalForm.length){
            modalForm.modal('hide');
        }
    },

    getSelected: function()
    {
        var selected = $('.superbox-list.selected .superbox-img'),
            selectedArray = [];

        selected.each(function() {
            selectedArray.push($(this).data('id'));
        });

        return selectedArray;

    },

    checkSelected: function()
    {
        var selectedArray = ImageStorage.getSelected();

        if (selectedArray.length) {
            $('.image-storage-operations').show();
        } else {
            $('.image-storage-operations').hide();
            $('form[name=image-storage-operations-form]')[0].reset();
        }
    },

    doDelete: function(id)
    {
        jQuery.SmartMessageBox({
            title : "Удалить запись?",
            content : "Эту операцию нельзя будет отменить.",
            buttons : '[Нет][Да]'
        }, function(ButtonPressed) {
            if (ButtonPressed === "Да") {
                jQuery.ajax({
                    type: "POST",
                    url: "/admin/image_storage/"+ImageStorage.entity+"/delete",
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            TableBuilder.showSuccessNotification('Запись удалена');
                            ImageStorage.updateGridView(id);
                            ImageStorage.closeSuperBoxPopup();
                        } else {
                            TableBuilder.showErrorNotification('Что-то пошло не так');
                        }
                    }
                });
            }
        });
    },

    doSaveInfo: function(id)
    {
        var data = $('#imgInfoBox-form').serializeArray();
        data.push({ name: 'id', value: id });

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/save_info",
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Сохранено');
                    ImageStorage.updateGridView(id,response.html);
                    ImageStorage.closeSuperBoxPopup();
                } else {
                    if (response.message){
                        TableBuilder.showErrorNotification(response.message);
                    }else{
                        TableBuilder.showErrorNotification("Что-то пошло не так");
                    }
                }
            }
        });
    },

    getEditForm: function(context)
    {
        var $this = $(context);
        var currentBlock = $this.find('.superbox-img');
        var currentBlockId = currentBlock.data('id');

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/get_form",
            dataType: 'json',
            data: {
                id: currentBlockId,
            },
            success: function(response) {
                if (response.status) {

                    $('.superbox-list').removeClass('active');
                    $this.addClass('active');

                    ImageStorage.openSuperBoxPopup(context,currentBlock, response.html);
                    ImageStorage.initSelectBoxes();
                    ImageStorage.initPopupClicks();
                    ImageStorage.initReplaceSrcImageForm();
                    ImageStorage.initCopyToClipboard();

                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });
    },

    updateGridView: function(id, html){

        if(id){
            $('.superbox').find(".superbox-img[data-id='" + id + "']").parent().replaceWith(html);
        }else{
            $('.superbox').prepend(html);
        }

        ImageStorage.initSelectable();
        ImageStorage.initEditClickEvent();
    },

    deleteMultipleGridView: function(){

        jQuery.SmartMessageBox({
            title : "Удалить записи?",
            content : "Эту операцию нельзя будет отменить.",
            buttons : '[Нет][Да]'
        }, function(ButtonPressed) {
            if (ButtonPressed === "Да") {
                var idArray = ImageStorage.getSelected();

                jQuery.ajax({
                    type: "POST",
                    url: "/admin/image_storage/"+ImageStorage.entity+"/delete_multiple",
                    data: {
                        idArray:     idArray,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            TableBuilder.showSuccessNotification('Записи удалены');
                            $.each(idArray, function(index, item) {
                                ImageStorage.updateGridView(item);
                            });
                            ImageStorage.checkSelected();
                        } else {
                            TableBuilder.showErrorNotification('Что-то пошло не так');
                        }
                    }
                });
            }
        });
    },

    changeMultipleActivity: function(activity){

        var idArray = ImageStorage.getSelected();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/change_multiple_activity",
            data: {
                idArray:     idArray,
                activity:    activity,
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Записи изменены');
                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });

    },

    doUpdateNewSize: function(){

        TableBuilder.showPreloader();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/update_new_size",
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Записи изменены');
                    TableBuilder.hidePreloader();
                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                    TableBuilder.hidePreloader();
                }
            }
        });
    },
    //end common in pages with grid view

    //common in pages with table view
    doSearchInTable: function()
    {
        var data = $('#image-storage-search-form').serializeArray();
        TableBuilder.showPreloader();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity,
            data: data,
            success : function(response) {
                $('#content_admin').html(response);
                ImageStorage.init();
                TableBuilder.hidePreloader()
            }
        });
    },

    getEditFormInTable: function(id)
    {
        TableBuilder.showPreloader();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/get_form",
            dataType: 'json',
            data: { id: id },
            success: function(response) {
                if (response.status) {
                    $("#modal_form").modal('show');
                    $("#modal_form .modal-content").html(response.html);
                    TableBuilder.hidePreloader();
                    ImageStorage.initSortable();
                    ImageStorage.initSelectBoxes();
                } else {
                    TableBuilder.hidePreloader();
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });
    },

    doDeleteInTable: function(id)
    {
        jQuery.SmartMessageBox({
            title : "Удалить запись?",
            content : "Эту операцию нельзя будет отменить.",
            buttons : '[Нет][Да]'
        }, function(ButtonPressed) {
            if (ButtonPressed === "Да") {
                jQuery.ajax({
                    type: "POST",
                    url: "/admin/image_storage/"+ImageStorage.entity+"/delete",
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            TableBuilder.showSuccessNotification('Запись удалена');
                            ImageStorage.updateTableView(id);
                            ImageStorage.closeModalForm();
                        } else {
                            TableBuilder.showErrorNotification('Что-то пошло не так');
                        }
                    }
                });
            }
        });
    },

    doSaveInfoInTable: function(id)
    {
        var data = $('#imgInfoBox-form-table').serializeArray();
        data.push({ name: 'id', value: id });

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/save_info",
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Сохранено');
                    ImageStorage.updateTableView(id, response.html);
                    ImageStorage.closeModalForm();
                } else {
                    if (response.message){
                        TableBuilder.showErrorNotification(response.message);
                    }else{
                        TableBuilder.showErrorNotification("Что-то пошло не так");
                    }
                }
            }
        });
    },

    updateTableView: function(id, html){

        var tableBody = $('#sort_t').find('tbody');

        if(id){
            tableBody.find('.tr_'+id).replaceWith(html);
        }else{
            tableBody.prepend(html);
        }
    },
    //end common in pages with table view

    //files
    uploadFiles: function(context)
    {
        var fileTotal = context.files.length;
        var fileCount        = 0;
        var fileFailCount    = 0;
        var fileSuccessCount = 0;
        var percentageMod     = 100 / fileTotal;
        var failPercentage    = 0;
        var successPercentage = 0;

        var uploadedId = [];

        var $fog = $('.image-storage-process-popup').show();
        $fog.find('.image-storage-upload-total').text(fileTotal);

        $fog.find('.image-storage-progress-success').css('width', '1%');

        for (var x = 0; x < fileTotal; x++) {
            var data = new FormData();
            data.append("file", context.files[x]);

            jQuery.ajax({
                data: data,
                type: "POST",
                url: "/admin/image_storage/"+ImageStorage.entity+"/upload",
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    fileCount = fileCount + 1;

                    if (response.status) {
                        $('.superbox').prepend(response.html);
                        ImageStorage.initSelectable();
                        ImageStorage.initEditClickEvent();
                        fileSuccessCount = fileSuccessCount + 1;
                        successPercentage = successPercentage + percentageMod;

                        uploadedId.push(response.id);

                        $fog.find('.image-storage-upload-upload').text(fileCount);
                        $fog.find('.image-storage-upload-success').text(fileSuccessCount);
                        $fog.find('.image-storage-progress-success').css('width', successPercentage +'%');
                        $fog.hide();

                    } else {
                        fileFailCount = fileFailCount + 1;
                        failPercentage = successPercentage + failPercentage;
                        var failWidth  = successPercentage + failPercentage;
                        $fog.find('.image-storage-progress-fail').css('width', failWidth +'%');
                        $fog.find('.image-storage-upload-fail').text(fileFailCount);

                        if (response.message){
                            TableBuilder.showErrorNotification(response.message);
                        }else{
                            TableBuilder.showErrorNotification("Ошибка при загрузке файла");
                        }
                    }

                    if (fileCount == fileTotal) {
                        $fog.find('.image-storage-upload-finish-btn').show();
                        if (uploadedId.length && ImageStorage.entity == 'images') {
                            ImageStorage.optimizeImage(uploadedId);
                        }
                    }
                }
            });
        }
        $("#upload-image-storage-input").val("");
    },
    replaceSingleFile: function(context, size, idImage)
    {
        var data = new FormData();
        data.append("file", context.files[0]);
        data.append('size', size);
        data.append('id', idImage);

        jQuery.ajax({
            data: data,
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/replace_single",
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status) {
                    $(context).parents(".tab-pane.active").find('.image-storage-images-sizes-content').html(response.html);
                    $(context).parents(".tab-pane.active").find('.image-storage-btn-download').prop('href', response.src);

                    TableBuilder.showSuccessNotification('Файл успешно заменен');
                    if (ImageStorage.entity === 'images') {
                        ImageStorage.optimizeImage(idImage, size);
                    }

                } else {
                    if (response.message){
                        TableBuilder.showErrorNotification(response.message);
                    }else{
                        TableBuilder.showErrorNotification("Ошибка при загрузке файла");
                    }
                }
            }
        });
    },
    //end files

    //images
    optimizeImage: function (id, size)
    {
        setTimeout(function(){
            jQuery.ajax({
                type: "POST",
                url: "/admin/image_storage/images/optimize_image",
                data: {
                    id:    id,
                    size:  size,
                },
                dataType: 'json',
                /*success: function (response) {
                    if (response.status) {
                        TableBuilder.showSuccessNotification('Изображение успешно оптимизированно');
                    } else {
                        TableBuilder.showErrorNotification('Что-то пошло не так при оптимизации изображения');
                    }
                }*/
            });
        }, 1000)

    },
    //end images

    //videos
    uploadVideoPreview: function(context, id)
    {
        var data = new FormData();
        data.append("file", context.files[0]);
        data.append('id', id);

        jQuery.ajax({
            data: data,
            type: "POST",
            url: "/admin/image_storage/videos/upload_video_preview",
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Превью установлено');
                    ImageStorage.changePreviewSrc(context,response.src);
                    ImageStorage.optimizeImage(response.id);

                } else {
                    if (response.message){
                        TableBuilder.showErrorNotification(response.message);
                    }else{
                        TableBuilder.showErrorNotification("Ошибка при загрузке изображения");
                    }
                }
            }
        });
    },
    removeUploadedPreview: function(context, id)
    {
        jQuery.SmartMessageBox({
            title : "Удалить превью?",
            content : "Эту операцию нельзя будет отменить.",
            buttons : '[Нет][Да]'
        }, function(ButtonPressed) {
            if (ButtonPressed === "Да") {
                jQuery.ajax({
                    type: "POST",
                    url: "/admin/image_storage/videos/remove_video_preview",
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            TableBuilder.showSuccessNotification('Превью удалено');
                            ImageStorage.changePreviewSrc(context,response.src);
                        } else {
                            TableBuilder.showErrorNotification('Что-то пошло не так');
                        }
                    }
                });
            }
        });
    },

    changePreviewSrc: function (context,src){
        $(context).parents(".tab-pane.active").find('.superbox-current-img').prop('src', src);
        $(context).parents(".tab-pane.active").find('.image-storage-btn-download').prop('href', src);
        $(".superbox-list-video.active").find('.image-storage-img').prop('src', src);
    },
    //end videos

    //galleries
    onChangeGalleryOrder: function()
    {
        var idArray = $('.image-storage-sortable').sortable('toArray');
        var idGallery = $('.modal-body.row').data('gallery_id');

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/change_order",
            data: { idArray: idArray, idGallery: idGallery  },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Порядок следования изменен');
                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });
    },

    deleteGalleryRelation: function(id, idGallery)
    {
        jQuery.SmartMessageBox({
            title : "Удалить связь?",
            content : "Эту операцию нельзя будет отменить.",
            buttons : '[Нет][Да]'
        }, function(ButtonPressed) {
            if (ButtonPressed === "Да") {
                jQuery.ajax({
                    type: "POST",
                    url: "/admin/image_storage/"+ImageStorage.entity+"/delete_relation",
                    data: { id: id, idGallery:idGallery  },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $('li#'+id).remove();
                            $(".image-storage-sortable").sortable("refresh");
                            TableBuilder.showSuccessNotification('Связь успешно удалена из галереи');
                        } else {
                            TableBuilder.showErrorNotification('Что-то пошло не так');
                        }
                    }
                });
            }
        });
    },

    setGalleryPreview: function(preview)
    {
        var idPreview = $(preview).attr('id');
        var idGallery = $('.modal-body.row').data('gallery_id');

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+ImageStorage.entity+"/set_gallery_preview",
            data: { idPreview: idPreview, idGallery:idGallery  },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $(".image-storage-sortable-item").removeClass('preview');
                    $(preview).addClass('preview');
                    TableBuilder.showSuccessNotification('Превью галереи успешно установлено');
                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });
    },

    createGalleryWith: function (gallery_type)
    {
        var galleryName = $('form[name="image-storage-operations-form"] input[name="gallery_name"]').val().trim();

        if (!galleryName) {
            TableBuilder.showErrorNotification('Введите название галереи');
            return false;
        }

        var idArray = ImageStorage.getSelected();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+gallery_type+"/create_gallery_with",
            data: {
                idArray:     idArray,
                galleryName: galleryName
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Галерея успешно создана');
                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });

    },
    saveGalleriesRelations: function (gallery_type)
    {
        var  idGalleries = $('form[name="image-storage-operations-form"] select[name="relations[image-storage-'+gallery_type+'][]"]').val();

        if (!idGalleries) {
            TableBuilder.showErrorNotification('Выберите галереи для добавления');
            return false;
        }

        var idArray = ImageStorage.getSelected();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/"+gallery_type+"/add_array_to_galleries",
            data: {
                idArray:     idArray,
                idGalleries:  idGalleries
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Записи успешно добавлены к галереям');
                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });
    },
    //end galleries

    //tags
    doRelateToTags: function()
    {
        var  idTags = $('form[name="image-storage-operations-form"] select[name="relations[image-storage-tags][]"]').val();

        if (!idTags) {
            TableBuilder.showErrorNotification('Выберите теги для добавления');
            return false;
        }

        var idArray = ImageStorage.getSelected();

        jQuery.ajax({
            type: "POST",
            url: "/admin/image_storage/tags/relate_to_tags/"+ImageStorage.entity,
            data: {
                idArray: idArray,
                idTags:  idTags
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    TableBuilder.showSuccessNotification('Записи добавлены к тегу');
                } else {
                    TableBuilder.showErrorNotification('Что-то пошло не так');
                }
            }
        });
    },
    //end tags

};
$(document).ready(function(){
    ImageStorage.init();
});
