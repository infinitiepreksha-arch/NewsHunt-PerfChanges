let cropper;

/* Image cropper function */
function initializeImageCropper(inputSelector, previewSelector, cropperContainerSelector, cropperImageSelector, hiddenInputName) {
    let cropper;

    $(inputSelector).on('change', function (e) {
        let file = e.target.files[0];

        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $(cropperImageSelector).attr('src', event.target.result);
                $(cropperContainerSelector).removeClass('d-none');
                $(previewSelector).hide();

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(document.getElementById(cropperImageSelector.slice(1)), {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 0.8,
                    responsive: true,
                });
            };
            reader.readAsDataURL(file);
        }
    });

    $(cropperContainerSelector).on('click', '#crop-image', function () {
        const canvas = cropper.getCroppedCanvas();
        const croppedImageData = canvas.toDataURL('image/png');
        $(previewSelector).attr('src', croppedImageData);
        $(inputSelector).val('');
        $(cropperContainerSelector).hide();

        $('<input>').attr({
            type: 'hidden',
            name: hiddenInputName,
            value: croppedImageData
        }).appendTo($(inputSelector).closest('form'));
    });
}

$(document).ready(function () {

    var topicTable = $('#list-topic').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, 'desc']],
        ajax: {
            url: $('#list-topic').data('url'),
            data: function (d) {
                d.status = $('#topics_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            {
                data: 'logo',
                render: data => data ? `<img src='${data}' class='img-channels'/>` : ''
            },
            { data: 'name', name: 'name' },
            { data: 'slug', name: 'slug' },
            {
                data: 'status', name: 'status',
                render: (data, type, row) => `
                    <div class="form-check form-switch">
                        <input class="form-check-input switch-input topic-switch-input" type="checkbox" data-id="${row.id}" ${data === 'active' ? 'checked' : ''}>
                    </div>`
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        language: current_locale === 'en' ? englishLanguage : hindiLanguage
    });


    $('#list-topic').on('click', '.edit_btn', function () {
        const row = topicTable.row($(this).closest('tr')).data();
        if (row) {
            $('#topic-id').val(row.id);
            $('#edit-topic-name').val(row.name);
            $('#edit_news_language_id').val(row.news_language_id);
            $('#edit-topic-status').val(row.status);
            $('#edit-topic-logo-privew').attr('src', row.logo || asset('assets/images/no_image_available.png'));
            $('#edit-Topic-Form').attr('action', route('topic.update', '') + '/' + row.id);
            $('#editTopicModal').modal('show');
        }
    });


    //  initializeImageCropper('#topic-logo-input','#topic-logo-privew','#cropper-container','#cropper-image','cropped_logo');
    $('#addTopicModal').on('show.bs.modal', function () {
        $("#addTopicForm")[0].reset();
        $('#topic-logo-privew').attr('src', window.baseurl + 'assets/images/no_image_available.png');
        $("#name-error-message").text("");
        $("#status-error-message").text("");
        $("#logo-error-message").text("");
    });

    $("#addTopicForm").on("submit", function (e) {
        e.preventDefault();

        $("#name-error-message").text("");
        $("#status-error-message").text("");
        $("#logo-error-message").text("");

        var formData = new FormData(this);
        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("#addTopicModal").modal("hide");
                $("#addTopicForm")[0].reset();
                $('#topic-logo-privew').attr('src', window.baseurl + 'assets/images/no_image_available.png');
                showSuccessToast(response.message);
                topicTable.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;

                    $("#name-error-message").text("");
                    $("#status-error-message").text("");
                    $("#logo-error-message").text("");
                    if (errors.name) {
                        $("#name-error-message").text(errors.name[0]);
                    }
                    if (errors.status) {
                        $("#status-error-message").text(errors.status[0]);
                    }
                    if (errors.logo) {
                        $("#logo-error-message").text(errors.logo[0]);
                    }
                }
            }
        });
    });

    /* Update topic data */
    // initializeImageCropper('#edit-topic-logo-input', '#edit-topic-logo-privew', '#edit-cropper-container', '#edit-cropper-image', 'cropped_logo');

    $("#edit-Topic-Form").on("submit", function (e) {
        e.preventDefault();

        $("#edit-name-error-message").text("");
        $("#edit-status-error-message").text("");
        $("#logo-error-message").text("");

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("#editTopicModal").modal("hide");
                $("#edit-Topic-Form")[0].reset();
                showSuccessToast(response.message);
                topicTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;

                    if (errors.name) {
                        $("#edit-name-error-message").text("Please provide a topic name.");
                    }
                    if (errors.status) {
                        $("#edit-status-error-message").text("Please select a status.");
                    }
                    if (errors.logo) {
                        $("#logo-error-message").text("Please upload a valid logo.");
                    }
                }
            },
        });
    });

    /* Change topic status */
    $('#topics_status').on('change', () => topicTable.ajax.reload());

    $(document).on('change', '.topic-switch-input', function () {
        const id = $(this).data('id');
        const status = $(this).prop('checked') ? 'active' : 'inactive';
        const url = $('#topic_status_url').val();
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                id: id,
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: response => {
                showSuccessToast(response.message);
                topicTable.ajax.reload(null, false);
            }
        });
    });
});