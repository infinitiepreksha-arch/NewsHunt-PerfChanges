/* Update Profile  */
$(document).ready(function () {
  $("#changeProfileForm").on("submit", function (event) {
    event.preventDefault();

    var form = $(this);
    var formData = new FormData(form[0]);
    $.ajax({
      url: form.attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },

      success: function (response) {
        console.log('Response received:', response); // Debug log
        if (response.status === 'success') {
          showSuccessToast(response.message);
          // Increase timeout to 3 seconds
          setTimeout(function () {
            console.log('Redirecting to:', response.redirect); // Debug log
            window.location.href = response.redirect;
          }, 1000);
        } else {
          showErrorToast(response.message);
        }
      },
      error: function (xhr, status, error) { },
    });
  });
});


// /* Change Password */
$(document).ready(function () {
  $("#changePasswordForm").on("submit", function (event) {
    event.preventDefault();

    var form = $(this);
    var formData = new FormData(form[0]);

    $.ajax({
      url: form.attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        console.log('Response received:', response); // Debug log
        if (response.status === 'success') {
          showSuccessToast(response.message);
          // Increase timeout to 3 seconds
          setTimeout(function () {
            console.log('Redirecting to:', response.redirect); // Debug log
            window.location.href = response.redirect;
          }, 3000);
        } else {
          showErrorToast(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.log('AJAX Error:', xhr, status, error); // Debug log
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          var errors = xhr.responseJSON.errors;
          var errorMessages = Object.values(errors).flat().join("<br>");
          $("#validationErrors").html(errorMessages).removeClass("d-none");
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          $("#validationErrors").html(xhr.responseJSON.message).removeClass("d-none");
        } else {
          $("#validationErrors")
            .html("An error occurred. Please try again.")
            .removeClass("d-none");
        }
      },
    });
  });
});

function showSuccessToast(message) {
  Toastify({
    text: message,
    duration: 4000,
    close: true,
    style: { background: "#28a745" },
  }).showToast();
}

function showErrorToast(message) {
  Toastify({
    text: message,
    duration: 4000,
    close: true,
    style: { background: "#dc3545" }, // red color
  }).showToast();
}

/* Add Channel */
$(document).ready(function () {
  $("#addChannelForm").on("submit", function (e) {
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
        $("#addChannelModal").modal("hide");
        $("#addChannelForm")[0].reset();

        if (response.status === 'success') {
          showSuccessToast(response.message); // e.g., "Channel created successfully."
        } else if (response.status === 'error') {
          showErrorToast(response.message); // e.g., "Something went wrong."
        }

        setTimeout(function () {
          location.reload();
        }, 2000);
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          var errors = xhr.responseJSON.errors;
          if (errors.name) {
            $("#name-error-message").text(errors.name[0]); // Laravel will return "The name has already been taken."
          }
          if (errors.status) {
            $("#status-error-message").text(errors.status[0]);
          }
          if (errors.logo) {
            $("#logo-error-message").text(errors.logo[0]);
          }
        }
      },
    });
  });
});

/* Function for image cropper */
function postInitializeImageCropper(inputSelector, previewSelector, cropperContainerSelector, cropperImageSelector, hiddenInputName) {
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
          aspectRatio: 1.7,
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
  $("#addRssFeedForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
      url: $("#rssfeedstore").val(),
      method: $(this).attr("method"),
      data: new FormData(this),
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#addRssFeedModal").modal("hide");
          $("#addRssFeedForm")[0].reset();
        }
        showSuccessToast(response.message);

        setInterval(function () {
          location.reload();
        }, 2000);
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          $.each(errors, function (key, value) {
            let element = $("[name=" + key + "]");
            element.closest(".form-group").find(".parsley-required").remove();
            element.after(
              '<span class="parsley-required">' + value[0] + "</span>"
            );
          });
        }
      },
    });
  });
});


function clearErrorMessages() {
  $(".text-danger strong").text("");
}
function addDisplayErrors(errors) {
  clearErrorMessages();

  const errorMap = {
    title: "#title-error-message",
    description: "#description-error-message",
    channel_id: "#channel-error-message",
    news_language_id: "#news_language-error-message",
    topic_id: "#topic-error-message",
    status: "#status-error-message",
    image: "#image-error-message",
    audio: "#audio-error-message",
    video_url: "#video_url-error-message",
    thumb_image: "#thumb_image-error-message",
  };

  Object.keys(errors).forEach((field) => {
    let errorElementId = errorMap[field];
    if (!$(errorElementId).length) {
      errorElementId = "#" + field.replace(".", "_") + "-error";
    }

    if ($(errorElementId).length) {
      $(errorElementId).text(errors[field][0]);
      $(`[name="${field}"]`).addClass("is-invalid");
    }
  });
}

function editDisplayErrors(errors) {
  clearErrorMessages();

  const errorMap = {
    title: "#edit-title-error-message",
    description: "#edit-description-error-message",
    channel_id: "#edit-channel-error-message",
    news_language_id: "#news_language-error-message",
    topic_id: "#edit-topic-error-message",
    status: "#edit-status-error-message",
    image: "#edit-image-error-message",
    video_url: "#video_url-error-message",
    thumb_image: "#thumb_image-error-message",
  };

  Object.keys(errors).forEach((field) => {
    let errorElementId = errorMap[field];
    if (!$(errorElementId).length) {
      errorElementId = "#" + field.replace(".", "_") + "-error";
    }

    if ($(errorElementId).length) {
      $(errorElementId).text(errors[field][0]);
      $(`[name="${field}"]`).addClass("is-invalid");
    }
  });
}

// $(document).ready(function () {

//   postInitializeImageCropper('#audio-image-input', '#audio-image-preview', '#cropper-container', '#cropper-image', 'cropped_logo');
//   // Handle audio file preview
//   $('#audio-file-input').on('change', function (e) {
//     const file = e.target.files[0];
//     if (file) {
//       const audioURL = URL.createObjectURL(file);
//       $('#audio-preview').attr('src', audioURL);
//     }
//   });
//   // Handle form submission
//   $("#addAudioForm").on("submit", function (event) {
//     event.preventDefault();
//     clearErrorMessages();
//     var formData = new FormData(this);

//     if (cropper) {
//       const canvas = cropper.getCroppedCanvas();
//       canvas.toBlob(function (blob) {
//         const postType = $("#select_type_posts").val();
//         if (postType == "audio") {
//           const file = new File([blob], "cropped-thumb.png", {
//             type: "image/png",
//           });
//           formData.set("image", file);
//         }
//         submitForm(formData);
//       });
//     } else {
//       submitForm(formData);
//     }
//   });

// Function to handle form submission via AJAX
// function submitForm(formData) {
//   const url = $('#addAudioForm').attr("action");
//   const method = $('#addAudioForm').attr("method");
//   $('#audio_submite_button').attr("disabled", true);
//   $('#audio_back_button').attr("disabled", true);

//   $.ajax({
//     url: url,
//     method: method,
//     data: formData,
//     processData: false,
//     contentType: false,
//     headers: {
//       "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//     },
//     dataType: "json",
//     success: function (response) {
//       if (response.status === "success") {
//         showSuccessToast(response.message);
//         setTimeout(() => {
//           window.location.href = response.redirect;
//         }, 1500);
//       }
//     },

//     error: function (xhr) {
//       if (xhr.status === 422) {
//         addDisplayErrors(xhr.responseJSON.errors);
//         $('#audio_submite_button').attr("disabled", false);
//         $('#audio_back_button').attr("disabled", false);
//       } else {
//         showErrorToast("An error occurred while processing your request.");
//       }
//     },
//   });
// }
// });

$(document).ready(function () {
  postInitializeImageCropper('#audio-image-input', '#audio-image-preview', '#cropper-container', '#cropper-image', 'cropped_logo');

  $("#editAudioPostForm").on("submit", function (event) {
    event.preventDefault();
    clearErrorMessages();

    $('#audio_update_submite_button').attr("disabled", true);
    $('#audio_back_button').attr("disabled", true);

    var postType = $('#select_type_posts').val(); // 'audio'
    var formData = new FormData(this);
    formData.append("_method", "PUT");

    if (postType === 'audio' && typeof cropper !== 'undefined') {
      var imageUrl = $('#cropper-image').attr('src');

      if (imageUrl != "") {
        const canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function (blob) {
          const file = new File([blob], "cropped-image.png", { type: "image/png" });
          formData.set("image", file);
          updateAudioPost(formData);
        });
      } else {
        // No new image: don't set anything, let controller keep old image
        updateAudioPost(formData);
      }
    } else {
      // Fallback: submit without cropper handling (e.g., if no new image or misconfig)
      updateAudioPost(formData);
    }
  });

  function updateAudioPost(formData) {
    const url = $('#editAudioPostForm').attr("action");

    $.ajax({
      url: url,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {  // Fixed: check boolean 'success' key
          showSuccessToast(response.message);
          setTimeout(() => {
            window.location.href = response.redirect;  // Assumes controller adds this
          }, 1500);
        }
      },
      error: function (xhr) {
        $('#audio_update_submite_button').attr("disabled", false);
        $('#audio_back_button').attr("disabled", false);
        if (xhr.status === 422) {
          addDisplayErrors(xhr.responseJSON.errors);
        } else {
          showErrorToast("An error occurred while processing your request.");
        }
      },
    });
  }
});

$(document).ready(function () {
  // Audio Posts Ajax
  let audioPostsData = [];
  let selectedAudioPosts = new Set();

  function fetchAudioPosts(page = 1) {
    const $audiosContainer = $("#audios-container");
    const $paginationContainer = $("#audio-pagination-container");
    const $totalPosts = $("#total-audio-posts");

    const searchInput = $("#search-input").val();
    const filter = $("#select-filter").val();
    const topic = $("#select-topic").val();
    const channel = $("#select-channel").val();
    const dataUrl = $audiosContainer.data("url");

    $.ajax({
      url: dataUrl,
      type: "GET",
      data: { page, filter, topic, channel, search: searchInput },
      success: function (response) {
        const { data = [], total, last_page, current_page } = response;
        audioPostsData = data;

        // Generate post elements with checkboxes
        const postElements = data
          .map(
            (post) => `
                    <div class="col-sm-4 col-lg-3" data-id="${post.id}">
                        <div class="card card-sm pull-effect posts_card">
                            <!-- Checkbox for selection -->
                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                <input type="checkbox" class="form-check-input audio-checkbox" 
                                       data-post-id="${post.id}" 
                                       ${selectedAudioPosts.has(post.id) ? 'checked' : ''}>
                            </div>
                            <div class="image-container" style="height: 230px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-play-circle text-white card-play-button" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445"/>
                                </svg>
                                <img src="${post.image}" class="card-img-top-custom card-img-top h-100" alt="Audio Post" onerror="this.onerror=null; this.src='/assets/images/no_image_available.png';">
                                ${post.audio ? `
                                  <div class="position-absolute bottom-0 end-0 me-1 mb-1">
                                  </div>
                                ` : ''}
                            </div>
                            <div class="card-body">
                                <h5 class="card-title custom-title text-truncate">${post.title}</h5>
                                <div class="d-flex align-items-center mt-2">
                                    <img src="/storage/images/${post.channel_logo}" class="channel-post-icone" alt="Channel Logo" onerror="this.src='/assets/images/no_image_available.png';">
                                    <div>
                                        <div>${post.channel_name}</div>
                                        <div class="text-secondary">${post.pubdate || post.publish_date}</div>
                                    </div>
                                    <div class="ms-auto">
                                        <b class="text-secondary">
                                            <i class="fa fa-eye" aria-hidden="true"></i> ${post.view_count}
                                        </b>
                                        <b class="ms-3 text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-thumb-up">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M13 3a3 3 0 0 1 2.995 2.824l.005 .176v4h2a3 3 0 0 1 2.98 2.65l.015 .174l.005 .176l-.02 .196l-1.006 5.032c-.381 1.626 -1.502 2.796 -2.81 2.78l-.164 -.008h-8a1 1 0 0 1 -.993 -.883l-.007 -.117l.001 -9.536a1 1 0 0 1 .5 -.865a2.998 2.998 0 0 0 1.492 -2.397l.007 -.202v-1a3 3 0 0 1 3 -3z" />
                                                <path d="M5 10a1 1 0 0 1 .993 .883l.007 .117v9a1 1 0 0 1 -.883 .993l-.117 .007h-1a2 2 0 0 1 -1.995 -1.85l-.005 -.15v-7a2 2 0 0 1 1.85 -1.995l.15 -.005h1z" />
                                            </svg> ${post.reactions_count ?? 0}
                                        </b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `
          )
          .join("");
        $audiosContainer.html(postElements);

        // Generate pagination
        function createPageItem(page, label = page, active = false, disabled = false) {
          return `
                        <li class="page-item ${active ? "active" : ""} ${disabled ? "disabled" : ""}">
                            <a class="page-link" href="javascript:void(0)" data-page="${page}">${label}</a>
                        </li>
                    `;
        }

        let paginationHtml = createPageItem(
          current_page - 1,
          trans("PREVIOUS"),
          false,
          current_page === 1
        );


        if (last_page <= 5) {
          paginationHtml += Array.from({ length: last_page }, (_, i) =>
            createPageItem(i + 1, i + 1, current_page === i + 1)
          ).join("");
        } else {
          paginationHtml +=
            current_page <= 3
              ? Array.from({ length: 3 }, (_, i) =>
                createPageItem(i + 1, i + 1, current_page === i + 1)
              ).join("") +
              '<li class="page-item disabled"><span class="page-link">...</span></li>' +
              createPageItem(last_page)
              : current_page >= last_page - 2
                ? createPageItem(1) +
                '<li class="page-item disabled"><span class="page-link">...</span></li>' +
                Array.from({ length: 3 }, (_, i) =>
                  createPageItem(
                    last_page - 2 + i,
                    last_page - 2 + i,
                    current_page === last_page - 2 + i
                  )
                ).join("")
                : createPageItem(1) +
                '<li class="page-item disabled"><span class="page-link">...</span></li>' +
                Array.from({ length: 3 }, (_, i) =>
                  createPageItem(
                    current_page - 1 + i,
                    current_page - 1 + i,
                    current_page === current_page - 1 + i
                  )
                ).join("") +
                '<li class="page-item disabled"><span class="page-link">...</span></li>' +
                createPageItem(last_page);
        }

        paginationHtml += createPageItem(
          current_page + 1,
          trans("NEXT"),
          false,
          current_page === last_page
        );
        $paginationContainer.html(paginationHtml);

        const perPage = data.length;
        const start = (current_page - 1) * perPage + 1;
        const end = Math.min(start + perPage - 1, total);
        $totalPosts.html(`${start}-${end} ${trans("OF")} ${total} ${trans("Audio Posts")}`);

        updateBulkDeleteUI();
      },
      error: function (error) {
        console.error("Error fetching audio posts:", error);
      },
    });
  }

  // Update bulk delete UI visibility and count
  function updateBulkDeleteUI() {
    const count = selectedAudioPosts.size;

    if (count > 0) {
      $("#select-all-audio-posts").removeClass("d-none");
      $("#bulk-audio-delete-btn").removeClass("d-none");
      $("#selected-count-badge").text(count);

      // Update select all checkbox state
      const allChecked = $(".audio-checkbox").length === $(".audio-checkbox:checked").length;
      $("#select-all-audio-checkbox").prop("checked", allChecked);
    } else {
      $("#select-all-audio-posts").addClass("d-none");
      $("#bulk-audio-delete-btn").addClass("d-none");
      selectedAudioPosts.clear();
    }
  }

  // Handle individual checkbox change
  $(document).on("change", ".audio-checkbox", function (e) {
    e.stopPropagation();
    const postId = $(this).data("post-id");

    if ($(this).is(":checked")) {
      selectedAudioPosts.add(postId);
    } else {
      selectedAudioPosts.delete(postId);
    }

    updateBulkDeleteUI();
  });

  // Handle select all checkbox
  $(document).on("change", "#select-all-audio-checkbox", function () {
    const isChecked = $(this).is(":checked");

    $(".audio-checkbox").prop("checked", isChecked);

    if (isChecked) {
      $(".audio-checkbox").each(function () {
        selectedAudioPosts.add($(this).data("post-id"));
      });
    } else {
      selectedAudioPosts.clear();
    }

    updateBulkDeleteUI();
  });

  // Handle bulk delete action
  $(document).on("click", "#bulk-audio-delete-action", function () {
    if (selectedAudioPosts.size === 0) {
      Swal.fire({
        icon: 'warning',
        title: trans("NO_POSTS_SELECTED") || "No Posts Selected",
        text: trans("PLEASE_SELECT_POSTS") || "Please select posts to delete",
        confirmButtonText: 'OK'
      });
      return;
    }

    const confirmMessage =
      `Are you sure you want to delete ${selectedAudioPosts.size} posts?`;

    Swal.fire({
      title: 'Are you sure?',
      text: confirmMessage,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      allowEnterKey: false,
      allowEscapeKey: false,
      allowOutsideClick: false
    }).then((result) => {
      if (result.isConfirmed) {
        const postIds = Array.from(selectedAudioPosts);
        const currentURL = window.location.href;
        const deleteUrl = currentURL.replace(/\/$/, "") + "/bulk-delete";

        $.ajax({
          url: deleteUrl,
          type: "POST",
          data: {
            post_ids: postIds,
            _token: $('meta[name="csrf-token"]').attr("content")
          },
          success: function (response) {
            selectedAudioPosts.clear();
            fetchAudioPosts();

            const successMessage = response.message ||
              "Posts deleted successfully";

            Swal.fire({
              icon: "success",
              title: "Deleted!",
              text: successMessage,
              showConfirmButton: true,
              confirmButtonText: "Ok",
              allowEnterKey: false,
              allowEscapeKey: false,
              allowOutsideClick: false

            });
          },
          error: function (xhr) {
            console.error("Error deleting posts:", xhr);
            const errorMessage = xhr.responseJSON?.message ||
              trans("ERROR_DELETING_POSTS") ||
              "Error deleting posts. Please try again.";

            Swal.fire({
              icon: 'error',
              title: trans("ERROR") || 'Error!',
              text: errorMessage,
              confirmButtonText: 'OK'
            });
          }
        });
      }
    });
  });

  function removeHtmlTags(description) {
    if (!description) return '';
    return description
      .replace(/<\/?[^>]+(>|$)/g, "")
      .replace(/&nbsp;/g, " ")
      .replace(/&#39;/g, "'")
      .replace(/&quot;/g, '"')
      .replace(/&amp;/g, '&')
      .replace(/&lt;/g, '<')
      .replace(/&gt;/g, '>');
  }

  function showPostModal(post, delete_url) {
    // Update image
    $("#post-image")
      .attr("src", post.image ? post.image : "/assets/images/no_image_available.png")
      .on("error", function () {
        $(this).off("error").attr("src", "/assets/images/no_image_available.png");
      });

    // Update audio player if available
    if (post.audio) {
      $("#audio-player-container").removeClass("d-none");
      const audioElement = $("#audio-player")[0];
      $("#audio-player source").attr("src", post.audio);
      audioElement.load();
    } else {
      $("#audio-player-container").addClass("d-none");
    }

    $("#post-title").text(removeHtmlTags(post.title));
    $("#channel-logo")
      .attr("src", `/storage/images/${post.channel_logo}`)
      .on("error", function () {
        $(this).off("error").attr("src", "/assets/images/no_image_available.png");
      });
    $("#channel-name").text(post.channel_name);
    $("#post-date").text(post.pubdate || post.publish_date);
    $("#view-count").html(`<i class="bi bi-eye-fill"></i> ${post.view_count || 0}`);
    $("#view-comments").html(`<i class="bi bi-chat-left-text-fill"></i> ${post.comment || 0}`);
    $("#comments_url").attr("href", `/admin/comments?post=${post.slug}`);
    $("#favorite-count").html(`<i class="bi bi-heart-fill"></i> ${post.favorite || 0}`);
    const description = removeHtmlTags(post.description);
    $("#post-description-text").text(description).addClass("line-clamp-3");
    if (description && description.length > 150) {
      $("#read-more-btn").show().text("Read more");
    } else {
      $("#read-more-btn").hide();
    }
    $("#edit-audio-btn").attr("href", `/admin/audios/${post.id}/edit`);
    $("#notification-audio-btn").attr("data-notification-url", `/admin/audios/${post.id}/sendNotification`);

    $("#post_delete_url").attr("href", delete_url);
    $("#post-description").modal("show");
    $("#reaction-count").html(`
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-thumb-up me-1">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M13 3a3 3 0 0 1 2.995 2.824l.005 .176v4h2a3 3 0 0 1 2.98 2.65l.015 .174l.005 .176l-.02 .196l-1.006 5.032c-.381 1.626 -1.502 2.796 -2.81 2.78l-.164 -.008h-8a1 1 0 0 1 -.993 -.883l-.007 -.117l.001 -9.536a1 1 0 0 1 .5 -.865a2.998 2.998 0 0 0 1.492 -2.397l.007 -.202v-1a3 3 0 0 1 3 -3z" />
        <path d="M5 10a1 1 0 0 1 .993 .883l.007 .117v9a1 1 0 0 1 -.883 .993l-.117 .007h-1a2 2 0 0 1 -1.995 -1.85l-.005 -.15v-7a2 2 0 0 1 1.85 -1.995l.15 -.005h1z" />
      </svg>
      ${post.reactions_count ?? 0}
    `);
  }

  // Event Delegation - click on card (excluding checkbox)
  $("#audios-container").on("click", ".col-sm-4", function (e) {
    // Don't open modal if clicking on checkbox or its label
    if ($(e.target).hasClass("audio-checkbox") || $(e.target).closest(".form-check-input").length) {
      return;
    }

    const postId = $(this).data("id");
    const post = audioPostsData.find((p) => p.id === postId);
    const currentURL = $(location).attr("href");
    const delete_url = currentURL + "/" + post.id;

    if (post) showPostModal(post, delete_url);
  });

  $('#post-description').on('hide.bs.modal', function () {
    var audio = document.getElementById('audio-player');
    if (audio) {
      audio.pause();
      audio.currentTime = 0;
    }
  });

  $(document).on("click", ".page-link", function () {
    const page = $(this).data("page");
    if (page) fetchAudioPosts(page);
  });

  function onFilterChange() {
    fetchAudioPosts();
  }

  $("#select-filter, #select-topic, #select-channel").on("change", onFilterChange);

  // Search with debounce
  let searchTimeout;
  $("#search-input").on("keyup", function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(onFilterChange, 500);
  });

  // Initial fetch
  fetchAudioPosts();
});

/* Unified Post Form Handler - Works for both Create and Edit */
$(document).ready(function () {
  const PostFormHandler = {
    // State variables
    cropper: null,
    baseAssetUrl: window.location.origin,
    isEditMode: false,
    formSelector: null,
    extraImageCounter: 0,
    mainFilePond: null, // Single multi-file pond instance
    deletedExistingIds: [],
    // Initialize the handler
    init: function (formSelector) {
      this.formSelector = formSelector;
      this.isEditMode = formSelector === '#editPostForm';
      const $form = $(this.formSelector);

      // Initialize cropper
      this.initializeCropper();

      // Initialize Single FilePond for Extra Images
      this.initFilePond();

      // Bind all events
      this.bindEvents();

      // Load old data if in edit mode
      if (this.isEditMode) {
        this.loadOldData();
      }

      // Update badge
      this.updateTotalImageCount();
    },
    // Initialize image cropper
    initializeCropper: function () {
      postInitializeImageCropper(
        '#post-image-input',
        '#post-image-preview',
        '#cropper-container',
        '#cropper-image',
        'cropped_logo'
      );
    },
    // Initialize FilePond
    initFilePond: function () {
      const self = this;
      const inputElement = document.querySelector('#extra-images-dropzone');
      if (!inputElement) return;



      this.mainFilePond = FilePond.create(inputElement, {
        acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif'],
        maxFileSize: '5MB',
        allowImagePreview: false, // We'll handle our own previews
        onupdatefiles: function (files) {
          self.updateTotalImageCount();
          self.renderNewImagePreviews(files);
        }
      });
    },
    // Render custom previews for new files
    renderNewImagePreviews: function (files) {
      const $container = $('#existingImagesContainer');
      // Track currently shown new previews to avoid unnecessary re-renders
      const currentNewPreviewIds = [];
      $container.find('.new-image-preview-item').each(function () {
        currentNewPreviewIds.push($(this).data('file-id'));
      });

      // Filter out files that are already previewed
      const filesToPreview = files.filter(f => !currentNewPreviewIds.includes(f.id));

      // Identify files that should be removed (files in UI but not in 'files' list)
      const activeFileIds = files.map(f => f.id);
      $container.find('.new-image-preview-item').each(function () {
        const fid = $(this).data('file-id');
        if (!activeFileIds.includes(fid)) {
          $(this).remove();
        }
      });

      // For each new file, create a preview
      filesToPreview.forEach((fileItem) => {
        const fileId = fileItem.id;
        const reader = new FileReader();
        reader.onload = (e) => {
          // Double check if file is still in the list before appending
          if (!this.mainFilePond.getFiles().find(f => f.id === fileId)) return;
          // Re-check if already appended (race condition)
          if ($container.find(`.new-image-preview-item[data-file-id="${fileId}"]`).length) return;

          const html = `
            <div class="col-6 col-md-3 new-image-preview-item mb-2" data-file-id="${fileId}">
              <div class="position-relative border rounded p-1">
                <img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm remove-new-image position-absolute top-0 end-0 m-1">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          `;
          $container.append(html);
        };
        reader.readAsDataURL(fileItem.file);
      });
    },
    // Bind all event handlers
    bindEvents: function () {
      const self = this;
      // Toggle Extra Images Section
      $('#enableExtraImages').off('change').on('change', function () {
        self.toggleExtraImages($(this).is(':checked'));
      });
      // Remove Existing Extra Image (delegated)
      $(document).off('click', '.remove-existing-image').on('click', '.remove-existing-image', function (e) {
        e.preventDefault();
        const imageId = $(this).data('image-id');
        self.markExistingForDeletion(imageId, $(this).closest('.existing-image-item'));
      });
      // Remove New Extra Image Preview (delegated)
      $(document).off('click', '.remove-new-image').on('click', '.remove-new-image', function (e) {
        e.preventDefault();
        const fileId = $(this).closest('.new-image-preview-item').data('file-id');
        if (self.mainFilePond) {
          self.mainFilePond.removeFile(fileId);
        }
      });
      // Form Submit
      $(this.formSelector).off('submit').on('submit', function (event) {
        event.preventDefault();
        self.handleSubmit();
      });
    },
    // Toggle Extra Images Section
    toggleExtraImages: function (isChecked) {
      if (isChecked) {
        $('#extraImagesSection').removeClass('d-none');
        $('#imagesCollapse').collapse('show');
      } else {
        $('#imagesCollapse').collapse('hide');
        setTimeout(() => {
          $('#extraImagesSection').addClass('d-none');
          // If turning off, we should clear and mark all existing for deletion if in edit mode
          if (this.isEditMode) {
            const $form = $(this.formSelector);
            const oldIdsStr = $form.attr('data-original-extra-ids');
            if (oldIdsStr) {
              this.deletedExistingIds = JSON.parse(oldIdsStr);
            }
          }
          if (this.mainFilePond) {
            this.mainFilePond.removeFiles();
          }
          $('#existingImagesContainer').empty();
          this.updateTotalImageCount();
        }, 400);
      }
    },
    // Mark existing image for deletion
    markExistingForDeletion: function (imageId, $element) {
      if (!this.deletedExistingIds.includes(imageId)) {
        this.deletedExistingIds.push(imageId);
      }
      $element.fadeOut(400, () => {
        $element.remove();
        this.updateTotalImageCount();
      });
    },
    // Update total count shown in badge
    updateTotalImageCount: function () {
      const existingCount = $('.existing-image-item').length;
      const newCount = this.mainFilePond ? this.mainFilePond.getFiles().length : 0;
      $('#extraImagesCount').text(existingCount + newCount);
    },
    // Handle form submission
    handleSubmit: function () {
      this.clearErrorMessages();

      const $form = $(this.formSelector);

      // Validation for Extra Images
      const isExtraImagesEnabled = $('#enableExtraImages').is(':checked');
      const existingCount = $('.existing-image-item').length;
      const newFilesCount = this.mainFilePond ? this.mainFilePond.getFiles().length : 0;
      const totalCount = existingCount + newFilesCount;

      if (isExtraImagesEnabled) {
        if (totalCount === 0) {
          showErrorToast("Please upload at least one extra image.");
          $('#imagesCollapse').collapse('show');
          return;
        }
      }

      const formData = new FormData();

      // Append non-file form fields
      $.each($form.serializeArray(), function (i, field) {
        // Skip extra_images[] as we handle it via FilePond
        if (field.name !== 'extra_images[]') {
          formData.append(field.name, field.value);
        }
      });

      // Add deleted existing image IDs
      if (this.deletedExistingIds.length > 0) {
        formData.append('deleted_extra_image_ids', JSON.stringify(this.deletedExistingIds));
      }

      // Add flags
      formData.append('has_extra_images', isExtraImagesEnabled ? '1' : '0');

      if (this.isEditMode) {
        formData.append('_method', 'PUT');
      }

      // Append new files from FilePond
      if (this.mainFilePond) {
        this.mainFilePond.getFiles().forEach(fileItem => {
          if (fileItem.file instanceof File) {
            formData.append('extra_images[]', fileItem.file);
          }
        });
      }

      // Handle cropped main image for create mode
      if (!this.isEditMode && typeof cropper !== 'undefined' && cropper) {
        const canvas = cropper.getCroppedCanvas();
        if (canvas) {
          canvas.toBlob((blob) => {
            const postType = $("#select_type_posts").val();
            if (postType === "post") {
              const file = new File([blob], "cropped-thumb.png", { type: "image/png" });
              formData.set("image", file);
            }
            this.submitForm(formData);
          });
          return;
        }
      }

      // Handle main image for edit mode
      if (this.isEditMode) {
        const postType = $('#select_type_posts').val();
        const fileInput = document.getElementById('post-image-input');
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
        if (postType === 'post' && hasFile) {
          const imageFile = fileInput.files[0];
          formData.set("image", imageFile);
        } else {
          formData.delete("image");
        }
      }

      this.submitForm(formData);
    },
    // Submit form via AJAX
    submitForm: function (formData) {
      const url = $(this.formSelector).attr("action");
      const method = this.isEditMode ? "POST" : $(this.formSelector).attr("method");
      $('#submite_button').attr("disabled", true);
      $('#back_button').attr("disabled", true);
      const originalButtonText = $('#submite_button').html();
      $('#submite_button').html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

      $.ajax({
        url: url,
        method: method,
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: (response) => {
          if (response.status === 'success' || response.success) {
            showSuccessToast(response.message);
            setTimeout(() => {
              window.location.href = response.redirect || (this.baseAssetUrl + "/admin/posts/");
            }, 1000);
          }
        },
        error: (xhr) => {
          $('#submite_button').html(originalButtonText);
          $('#submite_button').attr("disabled", false);
          $('#back_button').attr("disabled", false);
          if (xhr.status === 422) {
            this.displayErrors(xhr.responseJSON.errors);
            this.scrollToFirstError();
          } else {
            console.error('Error:', xhr.responseText);
            showErrorToast("An error occurred while processing your request.");
          }
        },
      });
    },
    // Clear error messages
    clearErrorMessages: function () {
      $('[id$="-error"]').text('');
      $('[id$="-error-message"]').text('');
    },
    // Display errors
    displayErrors: function (errors) {
      $.each(errors, function (field, messages) {
        // Handle array field errors (e.g., extra_images.0)
        let field_id = field.replace(/\./g, "_");
        const $targetError = $(`#${field_id}-error`).length ? $(`#${field_id}-error`) : $(`#${field_id}-error-message`);

        if ($targetError.length) {
          $targetError.text(messages[0]);
        } else {
          // Fallback to specific fields if IDs don't match
          if (field === 'title') $('#title-error').text(messages[0]);
          if (field === 'description') $('#description-error').text(messages[0]);
          if (field === 'news_language_id') $('#news_language_id-error').text(messages[0]);
          if (field === 'channel_id') $('#channel_id-error').text(messages[0]);
          if (field === 'image') $('#image-error').text(messages[0]);
          if (field.startsWith('extra_images')) $('#extra_images-error').text(messages[0]);
        }
      });
    },
    // Scroll to first error
    scrollToFirstError: function () {
      const firstError = $('[id$="-error"]:not(:empty), [id$="-error-message"]:not(:empty)').first();
      if (firstError.length) {
        $('html, body').animate({
          scrollTop: firstError.closest('.form-group, .col-md-6, .col-12, .mb-4').offset().top - 100
        }, 500);
      }
    },
    // Load old data for edit mode
    loadOldData: function () {
      const $form = $(this.formSelector);
      const hasExtraImages = $form.data('has-extra-images') === true || $form.data('has-extra-images') === 1;
      this.deletedExistingIds = [];

      if (hasExtraImages) {
        const oldExtraImages = $form.data('old-extra-images');
        if (oldExtraImages && oldExtraImages.length > 0) {
          $('#enableExtraImages').prop('checked', true).trigger('change');
          const $container = $('#existingImagesContainer');
          $container.empty();

          oldExtraImages.forEach((image) => {
            const html = `
              <div class="col-6 col-md-3 existing-image-item mb-2">
                <div class="position-relative border rounded p-1">
                  <img src="${image.image}" class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover;">
                  <button type="button" class="btn btn-danger btn-sm remove-existing-image position-absolute top-0 end-0 m-1" data-image-id="${image.id}">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            `;
            $container.append(html);
          });

          // Set original ids for deletion tracking
          const originalIds = oldExtraImages.map(img => img.id);
          $form.attr('data-original-extra-ids', JSON.stringify(originalIds));
          this.updateTotalImageCount();
        }
      }
    },
  };
  // Initialize based on which form exists on the page
  if ($('#addPostForm').length) {
    PostFormHandler.init('#addPostForm');
  } else if ($('#editPostForm').length) {
    PostFormHandler.init('#editPostForm');
  }
});

$(document).ready(function () {
  $('#fetch_rssfeed').on('click', function (e) {
    e.preventDefault();
    var url = window.location.origin + '/admin/run-queue'
    var $btn = $(this);
    $btn.attr('disabled', true).text('Syncing...');

    $.ajax({
      url: url,
      type: 'GET',
      success: function (response) {
        $btn.attr('disabled', false).text('Sync Feeds');
        showSuccessToast(response.message);
      },
      error: function (xhr, status, error) {
        $btn.attr('disabled', false).text('Sync Feeds');
        showErrorToast('An error occurred. Please try again.');
      }
    });
  });
});


$(document).ready(function () {
  function handlePostTypeChange() {
    const postType = $("#select_type_posts").val();
    if (postType == "video") {
      $('#posts_image_upload').addClass('d-none');
      $('#video_file').removeClass('d-none');
      $('#video_thumbnail').removeClass('d-none');
      $('#extra_images_option_container').addClass('d-none');
    } else {
      $('#posts_image_upload').removeClass('d-none');
      $('#video_file').addClass('d-none');
      $('#video_thumbnail').addClass('d-none');

      if (postType == "audio" || postType == "youtube") {
        $('#extra_images_option_container').addClass('d-none');
      } else {
        $('#extra_images_option_container').removeClass('d-none');
      }
    }
  }

  // Run on page load
  handlePostTypeChange();

  // Run on input change
  $("#select_type_posts").on("input change", function () {
    handlePostTypeChange();
  });
});

function readChapterVideo(input) {
  $('.video-thumb').show();
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('.video-thumb').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
