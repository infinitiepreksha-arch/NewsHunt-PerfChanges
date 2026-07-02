/*
* Common JS is used to write code which is generally used for all the UI components
* Specific component related code won't be written here
*/

"use strict";
$(document).ready(function () {
    $('#table_list').on('all.bs.table', function () {
        $('#toolbar').parent().addClass('col-12  col-md-7 col-lg-7 p-0');
    })
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    if ($('.permission-tree').length > 0) {
        $(function () {
            $('.permission-tree').on('changed.jstree', function (e, data) {
                // let i, j = [];
                let html = "";
                for (let i = 0, j = data.selected.length; i < j; i++) {
                    let permissionName = data.instance.get_node(data.selected[i]).data.name;
                    if (permissionName) {
                        html += "<input type='hidden' name='permission[]' value='" + permissionName + "'/>"
                    }
                }
                $('#permission-list').html(html);
            }).jstree({
                "plugins": ["checkbox"],
            });
        });
    }
})
//Setup CSRF Token default in AJAX Request
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/* Create Form Data for Store. */
$('#create-form,.create-form,.create-form-without-reset').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');

    let data = new FormData(this);

    let filteredData = {};
data.forEach((value, key) => {
    filteredData[key] = value;
});
console.log(filteredData.maintenance_mode);

    let preSubmitFunction = $(this).data('pre-submit-function');
    if (preSubmitFunction) {
        
        if (eval(preSubmitFunction + "()") == false) {
            return false;
        }
    }
    let customSuccessFunction = $(this).data('success-function');

    // noinspection JSUnusedLocalSymbols

    function successCallback(response) {
        if (!$(formElement).hasClass('create-form-without-reset')) {
            formElement[0].reset();
            $(".select2").val("").trigger('change');
            $('.filepond').filepond('removeFile')
        }
        $('#table_list').bootstrapTable('refresh');
        if (customSuccessFunction) {
            eval(customSuccessFunction + "(response)");
        }
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

/* Edite Form Data. */
$('#edit-form,.edit-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let data = new FormData(this);
    $(formElement).parents('modal').modal('hide');
    let url = $(this).attr('action');
    url = url.replace('/edit', '');
    let preSubmitFunction = $(this).data('pre-submit-function');
    if (preSubmitFunction) {

        eval(preSubmitFunction + "()");
    }
    let customSuccessFunction = $(this).data('success-function');

    function successCallback(response) {
        $('#table_list').bootstrapTable('refresh');
        setTimeout(function () {
            $('#editModal').modal('hide');
            $(formElement).parents('.modal').modal('hide');
        }, 1000)
        if (customSuccessFunction) {
            eval(customSuccessFunction + "(response)");
        }
    }

    formAjaxRequest('PATCH', url, data, formElement, submitButtonElement, successCallback);
})

$(document).on('click', '.delete-form', function (e) {
    e.preventDefault();
    showDeletePopupModal($(this).attr('href'), {
        successCallBack: function () {
            $('#table_list').bootstrapTable('refresh');
        }, errorCallBack: function (response) {
            // showErrorToast(response.message);
        }
    })
})

$(document).on('click', '.restore-data', function (e) {
    e.preventDefault();
    showRestorePopupModal($(this).attr('href'), {
        successCallBack: function () {
            $('#table_list').bootstrapTable('refresh');
        }
    })
})

$(document).on('click', '.trash-data', function (e) {
    e.preventDefault();
    showPermanentlyDeletePopupModal($(this).attr('href'), {
        successCallBack: function () {
            $('#table_list').bootstrapTable('refresh');
        }
    })
})

$(document).on('click', '.set-form-url', function (e) {
    e.preventDefault();
    $('#edit-form,.edit-form').attr('action', $(this).attr('href'));
})

$(document).on('click', '.delete-form-reload', function (e) {
    e.preventDefault();
    showDeletePopupModal($(this).attr('href'), {
        successCallBack: function () {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    })
})

// Change event for Status toggle change in Bootstrap-table
$(document).on('change', '.update-status', function () {
    let tableElement = $(this).parents('table');
    let url = $(tableElement).data('custom-status-change-url') || window.baseurl + "common/change-status";
    ajaxRequest('PUT', url, {
        id: $(this).attr('id'),
        table: $(tableElement).data('table'),
        column: $(tableElement).data('status-column') || "",
        status: $(this).is(':checked') ? 1 : 0
    }, null, function (response) {
        showSuccessToast(response.message);
    }, function (error) {
        showErrorToast(error.message);
    })
})


//Fire Ajax request when the the Bootstrap-table rows are rearranged
$('#table_list').on('reorder-row.bs.table', function (element, rows) {
    let url = $(element.currentTarget).data('custom-reorder-row-url') || window.baseurl + "common/change-row-order";
    ajaxRequest('PUT', url, {
        table: $(element.currentTarget).data('table'),
        column: $(element.currentTarget).data('reorder-column') || "",
        data: rows
    }, null, function (success) {
        $('#table_list').bootstrapTable('refresh');
        showSuccessToast(success.message);
    }, function (error) {
        showErrorToast(error.message);
    })
})