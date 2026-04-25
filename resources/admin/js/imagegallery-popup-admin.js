function reloadImagePopup(name, type, thisFileElement, baseName, pageId) {

    TableBuilder.thisPictureElementInGroup = thisFileElement;

    var section = thisFileElement.parents('.pictures_input_field');

    section.find("#files_uploaded_table_" + baseName).show();
    var model = thisFileElement.parents('.pictures_input_field').find('.select_with_uploaded').attr('data-name-model')

    var data = {
        query_type: "select_with_uploaded_images",
        ident : name,
        baseName : baseName,
        page_id : pageId,
        path_model: model
    };
    section.find('#files_uploaded_table_' + baseName + ' tbody').html('<tr><td colspan="5" style="text-align: center">Загрузка...</td></tr>');
    $.post(
        '/admin/photo/select_photos',
        data,
        function (response) {
            section.find('#files_uploaded_table_' + baseName + ' tbody').html(response.data);
            section.find('#files_uploaded_table_' + baseName + ' tbody').attr('data-type', type);
        },
        'json'
    );
}


function deleteImage(id,name, type, thisFileElement, baseName, pageId) {
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
                        TableBuilder.showSuccessNotification('Изображение удалено');
                        ImageStorage.updateGridView(id);
                        ImageStorage.closeSuperBoxPopup();
                        reloadImagePopup(name, type, thisFileElement, baseName, pageId)
                    } else {
                        TableBuilder.showErrorNotification('Что-то пошло не так');
                    }
                }
            });
        }
    });
}


