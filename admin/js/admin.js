(function ($) {
    "use strict";
    jQuery(document).ready(function () {

        //Import form step one button ajax
        $(document).on('click', '#ic_importer_step1_btn', function (e) {
            e.preventDefault();

            var button = $(this);
            const form = $('.ic_importer_form');
            const ic_file_data = $('#ic-importer-file').prop('files')[0];
            const postType = form.find('select[name=ic-importer-post-type]').val();
            const ic_form_data = new FormData();
            ic_form_data.append('file', ic_file_data);
            ic_form_data.append('postType', postType);
            ic_form_data.append('action', 'ic_importer_file_upload');
            ic_form_data.append('ic_importer_nonce', form.find('input[name=ic_importer_nonce]').val());

            $.ajax({
                url: ic_importer_ajax_object.ajaxurl,
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: ic_form_data,
                type: 'POST',
                beforeSend: function () {
                    button.addClass('ic-ajax-loading');
                    form.css({'opacity': '0.7', 'pointer-events': 'none'});
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    $('.ic_importer_form .alert-danger').hide();
                    $('.ic_importer_form .alert-success').hide();

                    if (obj.status === 'success') {
                        $('.ic-excel-column-table tbody').html(obj.excelColumns);
                        $('.ic-post-fields').html(obj.post_fields);
                        ic_importer_drag_and_drop();
                        $('.ic-importer-step-one').hide();
                        $('.ic-importer-step-two').show();
                    } else {
                        $('.ic_importer_form .alert-danger').show().html('<p class="m-0">' + obj.message + '</p>');

                        setTimeout(function () {
                            $('.ic_importer_form .alert-danger').hide();
                        }, 5000);
                    }
                    button.removeClass('ic-ajax-loading');
                    form.css({'opacity': '1', 'pointer-events': 'auto'});
                },
                error: function (errorThrown, status, error) {
                    alert(errorThrown.statusText);
                    location.reload();
                }
            });
        });

        //Import form submit ajax
        $(document).on('submit', '.ic_importer_form', function (e) {
            e.preventDefault();
            const thisElm = $(this);

            var submitButton = $(this).find('button[type=submit]');
            const ic_file_data = $('#ic-importer-file').prop('files')[0];
            const postType = thisElm.find('select[name=ic-importer-post-type]').val();
            const formData = thisElm.serializeArray();
            const ic_form_data = new FormData();
            ic_form_data.append('file', ic_file_data);
            ic_form_data.append('action', 'ic_importer_post_import');

            //Form data
            $.each(formData, function (key, input) {
                ic_form_data.append(input.name, input.value);
            });
            $.ajax({
                url: ic_importer_ajax_object.ajaxurl,
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: ic_form_data,
                type: 'POST',
                beforeSend: function () {
                    submitButton.addClass('ic-ajax-loading');
                    thisElm.css({'opacity': '0.7', 'pointer-events': 'none'});
                },
                success: function (response) {
                    var obj = JSON.parse(response);

                    if (obj.status === 'success') {
                        $('.ic_importer_form .alert-danger').hide();
                        $('.ic_importer_form .alert-success').show().html('<p class="m-0">' + obj.message + '</p>');
                    } else {
                        $('.ic_importer_form .alert-success').hide();
                        $('.ic_importer_form .alert-danger').show().html('<p class="m-0">' + obj.message + '</p>');

                        setTimeout(function () {
                            $('.ic_importer_form .alert-danger').hide();
                        }, 3000);
                    }
                    submitButton.removeClass('ic-ajax-loading');
                    thisElm.css({'opacity': '1', 'pointer-events': 'auto'});
                },
                error: function (errorThrown, status, error) {
                    alert(errorThrown.statusText);
                    location.reload();
                }
            });
        });

        //Import form previous button
        $(document).on('click', '.ic-importer-prev-btn', function (e) {
            e.preventDefault();
            $('.ic_importer_form .alert-danger').hide();
            $('.ic_importer_form .alert-success').hide();
            $(this).closest('.ic-importer-step').hide();
            $(this).closest('.ic-importer-step').prev().show();
        })

        function ic_importer_drag_and_drop() {

            $('.ic-column').draggable({
                helper: 'clone',
                cancel: false,
                cursor: "move",
                cursorAt: {top: 10, left: -5},
            });


            $('.ic-post-field').droppable({
                activeClass: 'active',
                hoverClass: 'hover',
                accept: ":not(.ui-sortable-helper)",
                drop: function (event, ui) {
                    const item = $('.ui-draggable-dragging');
                    $(this).addClass("ui-state-highlight").val(item.val());
                    $(this).val(item.data('label'));
                    $(this).attr('data-value', item.attr('value'));
                }
            });
        }
    });
})(jQuery);