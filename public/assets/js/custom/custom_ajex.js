var current_locale = $("#current_locale").val();
var englishLanguage = {
  processing: "Processing...",
  search: "Search:",
  lengthMenu: "Show _MENU_ entries",
  info: "Showing _START_ to _END_ of _TOTAL_ entries",
  infoEmpty: "No entries to show",
  infoFiltered: "(filtered from _MAX_ total entries)",
  loadingRecords: "Loading...",
  zeroRecords: "No matching records found",
  emptyTable: "No data available in table",
  paginate: {
    first: "First",
    last: "Last",
    next: "Next",
    previous: "Previous",
  },
};

var hindiLanguage = {
  processing: "प्रोसेसिंग...",
  search: "खोजें:",
  lengthMenu: "दिखाएँ _MENU_ प्रविष्टियाँ",
  info: "_TOTAL_ प्रविष्टियों में से _START_ से _END_ दिखा रहे हैं",
  infoEmpty: "दिखाने के लिए कोई प्रविष्टि नहीं",
  infoFiltered: "(कुल _MAX_ प्रविष्टियों से छान लिया गया)",
  loadingRecords: "लोड हो रहा है...",
  zeroRecords: "कोई मिलान रिकॉर्ड नहीं मिला",
  emptyTable: "तालिका में कोई डेटा उपलब्ध नहीं है",
  paginate: {
    first: "पहला",
    last: "अंतिम",
    next: "अगला",
    previous: "पिछला",
  },
};

// Image Preview Setup
(function () {
  function setupImagePreview(inputSelector, previewSelector) {
    $(inputSelector).on("change", function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => $(previewSelector).attr("src", e.target.result);
        reader.readAsDataURL(file);
      }
    });
  }

  setupImagePreview("#change-profile", "#change-profile-privew");
  setupImagePreview("#add-user-profile-img", "#add-user-profile-privew");
  setupImagePreview("#user-profile-img", "#user-profile-privew");
  setupImagePreview("#channel-logo-input", "#channel-logo-privew");
  setupImagePreview("#edit-channel-logo", "#edit-channel-privew");
  setupImagePreview("#Notification_img", "#Notification_preview");
  setupImagePreview("#theme-logo-input", "#theme-logo-preview");
  setupImagePreview("#edit-theme-logo-input", "#edit-theme-logo-preview");
  setupImagePreview("#post-image-input", "#post-image-privew");
  setupImagePreview("#edit-post-image-input", "#edit-post-image-privew");
  setupImagePreview("#topic-logo-input", "#topic-logo-privew");
  setupImagePreview("#edit-topic-logo-input", "#edit-topic-logo-privew");
})();

// <><><><><><> START JS FOR ADMIN PANEL CHANNEL TABLE <><><><><><>
$(document).ready(function () {
  const channelTable = $("#list-channel").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#list-channel").data("url"),
      data: function (d) {
        d.channel_status = $("#channel_status").val();
      },
    },
    columns: [
      { data: "id", name: "id" },
      {
        data: "poster_image",
        render: (data) =>
          data ? `<img src='${data}' class='img-channels'/>` : "",
      },
      {
        data: "name",
        name: "name",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data}</div>`;
        }
      },
      {
        data: "slug",
        name: "slug",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data}</div>`;
        }
      },
      {
        data: "description",
        name: "description",
        className: "description-column",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data}</div>`;
        },
      },

      {
        data: "status",
        name: "status",
        render: (data, type, row) => `
                    <div class="form-check form-switch">
                        <input class="form-check-input switch-input channel-switch-input" type="checkbox" data-id="${row.id
          }" ${data === "active" ? "checked" : ""}>
                    </div>`,
      },
      { data: "follow_count", name: "follow_count" },
      { data: "action", name: "action" },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });

  $("#list-channel").on("click", ".edit_btn", function () {
    const row = channelTable.row($(this).closest("tr")).data();
    if (row) {
      $("#channel-id").val(row.id);
      $("#edit-channel-name").val(row.name);
      $("#edit-channel-description").val(row.description);
      $("#edit-channel-status").val(row.status);
      $("#edit_news_language_id").val(row.news_language_id);
      $("#edit-channel-privew").attr(
        "src",
        row.poster_image || asset("assets/images/no_image_available.png")
      );
      // Get the form's original action and replace the 0 with actual ID
      let updateUrl = $("#editChannelForm").attr("action").replace('/0', '/' + row.id);
      $("#editChannelForm").attr("action", updateUrl);

      $("#editChannelModal").modal("show");
    }
  });

  $("#channel_status").on("change", () => channelTable.ajax.reload());

  $(document).on("change", ".channel-switch-input", function () {
    const id = $(this).data("id");
    const status = $(this).prop("checked") ? "active" : "inactive";
    const url = $("#channel_status_url").val();
    $.ajax({
      type: "POST",
      url: url,
      data: {
        id: id,
        status: status,
        _token: $('meta[name="csrf-token"]').attr("content"),
      },
      success: (response) => {
        showSuccessToast(response.message);

        // Update the row data in DataTable
        const rowIndex = channelTable.row($(this).closest('tr')).index();
        const rowData = channelTable.row(rowIndex).data();
        rowData.status = status;
        channelTable.row(rowIndex).data(rowData);

        // If edit modal is open and it's for the same channel, update the modal status
        if ($("#editChannelModal").hasClass("show") && $("#channel-id").val() == id) {
          $("#edit-channel-status").val(status);
        }
      },
      error: (xhr) => console.error("Error:", xhr.responseText),
    });
  });

});
// <><><><><><> END JS OF ADMIN PANEL CHANNEL TABLE <><><><><><>

// <><><><><><> START JS FOR EDIT CHANNEL <><><><><><>
$("#editChannelForm").on("submit", function (e) {
  e.preventDefault();
  $("#edit-name-error-message").text("");

  var formData = new FormData(this);

  $.ajax({
    url: $(this).attr("action"),
    method: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      $("#editChannelModal").modal("hide");

      if (response.status === "success") {
        showSuccessToast(response.message);
      } else {
        showErrorToast(response.message);
      }

      setTimeout(function () {
        location.reload();
      }, 2000);
    },
    error: function (xhr) {
      if (xhr.status === 422) {
        var errors = xhr.responseJSON.errors;
        if (errors.name) {
          $("#edit-name-error-message").text(errors.name[0]);
        }
      }
    },
  });
});
// <><><><><><> END JS OF EDIT CHANNEL <><><><><><>

$(document).ready(function () {
  // Posts Ajax
  let postsData = [];
  let selectedVideoPosts = new Set();

  function fetchPosts(page = 1) {
    const $videoContainer = $("#video-container");
    const $paginationContainer = $("#video-pagination-container");
    const $totalPosts = $("#total-video-posts");

    const searchInput = $("#search-input").val();
    const filter = $("#select-filter").val();
    const topic = $("#select-topic").val();
    const channel = $("#select-channel").val();
    const dataUrl = $videoContainer.data("url");

    $.ajax({
      url: dataUrl,
      type: "GET",
      data: { page, filter, topic, channel, search: searchInput },
      success: function (response) {
        const { data = [], total, last_page, current_page } = response;
        postsData = data;

        // Generate post elements with checkboxes
        const postElements = data
          .map(
            (post) => `
                    <div class="col-sm-4 col-lg-3" data-id="${post.id}">
                        <div class="card card-sm pull-effect posts_card">
                            <!-- Checkbox for selection -->
                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                <input type="checkbox" class="form-check-input video-checkbox" 
                                       data-post-id="${post.id}" 
                                       ${selectedVideoPosts.has(post.id) ? 'checked' : ''}>
                            </div>
                            <div class="image-container" style="height: 230px;">
                            ${post.type === "video" || post.type === "youtube"
                ? `<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-play-circle text-white card-play-button" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
      <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445"/>
    </svg>`
                : ""}
                                <img src="${post.type == "post"
                ? post.image
                : post.video_thumb
              }" class="card-img-top-custom card-img-top h-100" alt="Post Image" onerror="this.onerror=null; this.src='/assets/images/no_image_available.png';">
                                ${post.type == "video"
                ? '<div class="play-button"></div>'
                : ""
              }
                            </div>
                            <div class="card-body">
                                <h5 class="card-title custom-title">${post.title
              }</h5>
                                <div class="d-flex align-items-center mt-2">
                                    <img src="/storage/images/${post.channel_logo
              }" class="channel-post-icone" alt="Channel Logo">
                                    <div>
                                        <div>${post.channel_name}</div>
                                        <div class="text-secondary">${post.publish_date
              }</div>
                                    </div>
                                    <div class="ms-auto">
                                        <b class="text-secondary">
                                            <i class="fa fa-eye" aria-hidden="true"></i> ${post.view_count
              }
                                        </b>
                                        <b class="ms-3 text-secondary">
                                            <i class="fa fa-heart" aria-hidden="true"></i> ${post.favorite
              }
                                        </b>
                                        <b id="reaction-count" class="ms-3 text-secondary">
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
        $videoContainer.html(postElements);

        // Generate pagination
        function createPageItem(
          page,
          label = page,
          active = false,
          disabled = false
        ) {
          return `
                        <li class="page-item ${active ? "active" : ""} ${disabled ? "disabled" : ""
            }">
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
        $totalPosts.html(
          `1-${data.length} ${trans("OF")} ${total} ${trans("POSTS")}`
        );

        updateBulkDeleteUI();
      },
      error: function (error) {
        console.error("Error fetching posts:", error);
      },
    });
  }

  // Update bulk delete UI visibility and count
  function updateBulkDeleteUI() {
    const count = selectedVideoPosts.size;

    if (count > 0) {
      $("#select-all-video-posts").removeClass("d-none");
      $("#bulk-video-delete-btn").removeClass("d-none");
      $("#selected-count-badge").text(count);

      // Update select all checkbox state
      const allChecked = $(".video-checkbox").length === $(".video-checkbox:checked").length;
      $("#select-all-video-checkbox").prop("checked", allChecked);
    } else {
      $("#select-all-video-posts").addClass("d-none");
      $("#bulk-video-delete-btn").addClass("d-none");
      selectedVideoPosts.clear();
    }
  }

  // Handle individual checkbox change
  $(document).on("change", ".video-checkbox", function (e) {
    e.stopPropagation();
    const postId = $(this).data("post-id");

    if ($(this).is(":checked")) {
      selectedVideoPosts.add(postId);
    } else {
      selectedVideoPosts.delete(postId);
    }

    updateBulkDeleteUI();
  });

  // Handle select all checkbox
  $(document).on("change", "#select-all-video-checkbox", function () {
    const isChecked = $(this).is(":checked");

    $(".video-checkbox").prop("checked", isChecked);

    if (isChecked) {
      $(".video-checkbox").each(function () {
        selectedVideoPosts.add($(this).data("post-id"));
      });
    } else {
      selectedVideoPosts.clear();
    }

    updateBulkDeleteUI();
  });

  // Handle bulk delete action
  $(document).on("click", "#bulk-video-delete-action", function () {
    if (selectedVideoPosts.size === 0) {
      Swal.fire({
        icon: 'warning',
        title: trans("NO_POSTS_SELECTED") || "No Posts Selected",
        text: trans("PLEASE_SELECT_POSTS") || "Please select posts to delete",
        confirmButtonText: 'OK'
      });
      return;
    }

    const confirmMessage =
      `Are you sure you want to delete ${selectedVideoPosts.size} posts?`;

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
        const postIds = Array.from(selectedVideoPosts);
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
            selectedVideoPosts.clear();
            fetchPosts();

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
    return description
      .replace(/<\/?[^>]+(>|$)/g, "")
      .replace(/&nbsp;/g, " ")
      .replace(/&#39;/g, "'")
      .replace(/&quot;/g, '"');
  }

  function showPostModal(post, delete_url) {
    if (post.type === "post") {
      $("#video-preview").addClass("d-none");
      $("#video_frame").addClass("d-none");

      $("#post-image")
        .removeClass("d-none")
        .attr("src", post.image ? post.image : "/assets/images/no_image_available.png")
        .on("error", function () {
          $(this).off("error").attr("src", "/assets/images/no_image_available.png");
        });

    } else if (post.type === "youtube") {
      $("#video-preview").addClass("d-none");
      $("#post-image").addClass("d-none");

      $("#video_frame")
        .removeClass("d-none")
        .attr("src", post.video)
        .on("error", function () {
          $(this).off("error").attr("src", "/assets/images/no_image_available.png");
        });

    } else if (post.type === "video") {
      $("#video_frame").addClass("d-none");
      $("#post-image").addClass("d-none");

      $("#video-preview")
        .removeClass("d-none")
        .find("source")
        .attr("src", post.video);

      $("#video-preview")[0].load();
    }

    $("#post-title").text(removeHtmlTags(post.title));
    $("#channel-logo")
      .attr("src", `/storage/images/${post.channel_logo}`)
      .on("error", function () {
        $(this)
          .off("error")
          .attr("src", "/assets/images/no_image_available.png");
      });
    $("#channel-name").text(post.channel_name);
    $("#post-date").text(post.pubdate);
    $("#view-count").html(`<i class="bi bi-eye-fill"></i> ${post.view_count}`);
    $("#view-comments").html(
      `<i class="bi bi-chat-left-text-fill"></i> ${post.comment}`
    );
    $("#comments_url").attr("href", `/admin/comments?post=${post.slug}`);
    $("#favorite-count").html(
      `<i class="bi bi-heart-fill"></i> ${post.favorite}`
    );
    const description = removeHtmlTags(post.description);
    $("#post-description-text").text(description).addClass("line-clamp-3");
    if (description.length > 150) {
      $("#read-more-btn").show().text("Read more");
    } else {
      $("#read-more-btn").hide();
    }
    $("#edit-post-btn").attr("href", `/admin/posts/${post.id}/edit`);
    $("#notification-post-btn").attr("data-notification-url", `/admin/posts/${post.id}/sendNotification`);
    if (post.type === 'video') {
      $("#edit-video-custom-btn")
        .removeClass('d-none')
        .attr("href", `/admin/videos/${post.id}/custom`);
      $("#notification-video-custom-btn").attr("data-notification-url", `/admin/videos/${post.id}/sendNotification`);
      $("#edit-video-youtube-btn").addClass('d-none');
    } else if (post.type === 'youtube') {
      $("#edit-video-youtube-btn")
        .removeClass('d-none')
        .attr("href", `/admin/videos/${post.id}/youtube`);
      $("#notification-video-custom-btn").attr("data-notification-url", `/admin/videos/${post.id}/sendNotification`);
      $("#edit-video-custom-btn").addClass('d-none');
    } else {
      $("#edit-video-custom-btn").addClass('d-none');
      $("#edit-video-youtube-btn").addClass('d-none');
    }

    $("#post_delete_url").attr("href", delete_url);
    $("#post-description").modal("show");
    $("#reaction-count").html(`
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-thumb-up">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M13 3a3 3 0 0 1 2.995 2.824l.005 .176v4h2a3 3 0 0 1 2.98 2.65l.015 .174l.005 .176l-.02 .196l-1.006 5.032c-.381 1.626 -1.502 2.796 -2.81 2.78l-.164 -.008h-8a1 1 0 0 1 -.993 -.883l-.007 -.117l.001 -9.536a1 1 0 0 1 .5 -.865a2.998 2.998 0 0 0 1.492 -2.397l.007 -.202v-1a3 3 0 0 1 3 -3z" />
                <path d="M5 10a1 1 0 0 1 .993 .883l.007 .117v9a1 1 0 0 1 -.883 .993l-.117 .007h-1a2 2 0 0 1 -1.995 -1.85l-.005 -.15v-7a2 2 0 0 1 1.85 -1.995l.15 -.005h1z" />
            </svg>
        `);
  }

  // Event Delegation - click on card (excluding checkbox)
  $("#video-container").on("click", ".col-sm-4", function (e) {
    // Don't open modal if clicking on checkbox or its label
    if ($(e.target).hasClass("video-checkbox") || $(e.target).closest(".form-check-input").length) {
      return;
    }

    const post = postsData.find((p) => p.id === $(this).data("id"));
    const currentURL = $(location).attr("href");
    const delete_url = currentURL + "/" + post.id;

    if (post) showPostModal(post, delete_url);
  });

  $('#post-description').on('hidden.bs.modal', function () {
    var video = document.getElementById('video-preview');
    if (!$(video).hasClass('d-none')) {
      video.pause();
      video.currentTime = 0;
      video.src = ""; // 👈 This is important: remove src to force stop
    }
  });

  $(document).on("click", ".page-link", function () {
    const page = $(this).data("page");
    if (page) fetchPosts(page);
  });

  function onFilterChange() {
    fetchPosts();
  }

  $("#select-filter, #select-topic, #select-channel, #search-input").on(
    "change keyup",
    onFilterChange
  );

  // Initial fetch
  const urlParams = new URLSearchParams(window.location.search);
  let filterParam = urlParams.get('filter');

  if (!filterParam) {
    filterParam = $("#select-filter").data('default');
  }

  if (filterParam && $("#select-filter").length) {
    $("#select-filter").val(filterParam);
    if (urlParams.has('filter')) {
      const newUrl = window.location.pathname;
      window.history.replaceState({}, document.title, newUrl);
    }
  }
  fetchPosts();
});

// <><><><><><> START JS FOR CONTACT US TABLE <><><><><><>
$(document).ready(function () {
  const contactTable = $("#contact_us_table").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#contact_us_table").data("url"),
      data: function (d) {
        d.status = $("#topics_status").val();
      },
    },
    columns: [
      { data: "id", name: "id" },
      { data: "name", name: "name" },
      { data: "phone", name: "phone" },
      { data: "email", name: "email" },
      {
        data: "message",
        name: "message",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data}</div>`;
        }
      },
      { data: "action", orderable: false, searchable: false },
    ],

    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });

  $("#contact_us_table").on("click", ".edit_btn", function () {
    const row = contactTable.row($(this).closest("tr")).data();
    if (row) {
      $("#contact-name").text(row.name);
      $("#contact-email").text(row.email);
      $("#contact-mobile").text(row.phone);
      $("#contact-message").text(row.message);
      $("#contact-us-modal").modal("show");
    }
  });


  $('#contact_us_table').on('click', '.delete_btn', function () {
    const contactId = $(this).data('id');

    Swal.fire({
      title: 'Are you sure?',
      text: 'This contact will be permanently deleted!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      customClass: {
        popup: 'dark:bg-black dark:text-white'
      }
    }).then((result) => {
      if (!result.isConfirmed) return;

      $.ajax({
        url: `contact-us/${contactId}`,   // ← your DELETE route
        type: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: (resp) => {

          contactTable.ajax.reload(null, false); // keep on same page
        },
        error: () => {
          Swal.fire({
            icon: 'error',
            title: 'Oops…',
            text: 'Could not delete this contact.',
          });
        }
      });
    });
  });
});
// <><><><><><> END JS OF CONTACT US TABLE <><><><><><>

// <><><><><><> START JS FOR COMMENT TABLE <><><><><><>
$(document).ready(function () {
  const dataUrl = $("#posts-container").data("url");
  const commentsTable = $("#user_comments_table").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: dataUrl,
      dataSrc: function (json) {
        return json.data;
      },
    },
    columns: [
      { data: "id", name: "id" },
      { data: "name", name: "name" },
      { data: "comment", name: "comment" },
      { data: "title", name: "title" },
      { data: "action", name: "action", orderable: false, searchable: false },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });

  $("#user_comments_table").on("click", ".edit_btn", function () {
    const row = contactTable.row($(this).closest("tr")).data();
    if (row) {
      $("#post_title").text(row.title);
      $("#Comments_model").modal("show");
    }
  });
});
// <><><><><><> END JS OF COMMENT TABLE <><><><><><>

// <><><><><><> START JS FOR REPORTED COMMENT TABLE <><><><><><>
$(document).ready(function () {
  const reportedComments = $("#report_comments_table").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#report_comments_table").data("url"),
      dataSrc: function (json) {
        return json.data;
      },
    },
    columns: [
      { data: "id", name: "id" },
      { data: "name", name: "name" },
      {
        data: "reason_type",
        name: "reason_type",
        render: function (data) {
          return `<div class="tabler_report_text_wrap_css">${data ?? ""}</div>`;
        }
      },
      {
        data: "report",
        name: "report",
        render: function (data) {
          return `<div class="tabler_report_text_wrap_css">${data ?? ""}</div>`;
        }
      },
      {
        data: "comment",
        name: "comment",
        render: function (data) {
          return `<div class="tabler_report_text_wrap_css">${data}</div>`;
        }
      },
      {
        data: "created_at",
        name: "created_at",
        render: function (data, type, row) {
          // Format date using Intl.DateTimeFormat
          let date = new Date(data);
          let formattedDate = new Intl.DateTimeFormat("en-IN", {
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false,
          }).format(date);
          return formattedDate;
        },
      },
      { data: "action", name: "action", orderable: false, searchable: false },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });

  /* Delete Reported Comment */
  $(document).on("click", "#delete_report_comment", function (e) {
    e.preventDefault();
    var commentId = $(this).data("comment-id");

    Swal.fire({
      title: "Are you sure?",
      text: "You won’t be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
      allowEnterKey: false,
      allowEscapeKey: false,
      allowOutsideClick: false,
    }).then((result) => {
      if (result.isConfirmed) {
        deleteComment(commentId);
      }
    });

    function deleteComment(commentId) {
      $.ajax({
        url: "report-comments/" + commentId,
        type: "DELETE",
        data: {
          _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
          if (response.error == false) {
            reportedComments.ajax.reload(null, false);
            Swal.fire({
              title: "Deleted!",
              text: response.message,
              icon: "success",
              allowEnterKey: false,
              allowEscapeKey: false,
              allowOutsideClick: false
            });
          } else {
            Swal.fire("Error!", response.message, "error");
          }
        },
        error: function (xhr) {
          Swal.fire("Error!", "An error occurred while deleting the comment. Please try again.", "error");
          console.log(xhr.responseText);
        },
      });
    }
  });

  /* Ignore Reported Comment */
  $(document).on("click", "#ignore_report_comment", function (e) {
    e.preventDefault();
    var commentId = $(this).data("comment-id");

    Swal.fire({
      title: "Are you sure?",
      text: "This will mark the report as ignored!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, ignore it!",
      cancelButtonText: "Cancel",
      allowEnterKey: false,
      allowEscapeKey: false,
      allowOutsideClick: false,
    }).then((result) => {
      if (result.isConfirmed) {
        ignoreComment(commentId);
      }
    });

    function ignoreComment(commentId) {
      $.ajax({
        url: "report-comments/" + commentId + "/ignore", // your ignore route
        type: "POST",
        data: {
          _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
          if (response.error == false) {
            reportedComments.ajax.reload(null, false);
            Swal.fire({
              title: "Ignored!",
              text: response.message,
              icon: "success",
              allowEnterKey: false,
              allowEscapeKey: false,
              allowOutsideClick: false
            });
          } else {
            Swal.fire({
              title: "Notice!",
              text: response.message,
              icon: "info",
              allowEnterKey: false,
              allowEscapeKey: false,
              allowOutsideClick: false
            });
          }
        },
        error: function (xhr) {
          Swal.fire("Error!", "An error occurred while ignoring the report. Please try again.", "error");
          console.log(xhr.responseText);
        },
      });
    }
  });


  /* Remove Reported Comment */
  $(document).on("click", "#remove_report_comment", function (e) {
    e.preventDefault();
    var commentId = $(this).data("comment-id");

    Swal.fire({
      title: "Are you sure?",
      text: "This will remove the comment completely!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#6c757d",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, remove it!",
      cancelButtonText: "Cancel",
      allowEnterKey: false,
      allowEscapeKey: false,
      allowOutsideClick: false,
    }).then((result) => {
      if (result.isConfirmed) {
        removeComment(commentId);
      }
    });

    function removeComment(commentId) {
      $.ajax({
        url: "report-comments/" + commentId + "/remove", // your remove route
        type: "POST",
        data: {
          _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
          if (response.error == false) {
            reportedComments.ajax.reload(null, false);
            Swal.fire({
              title: "Removed!",
              text: response.message,
              icon: "success",
              allowEnterKey: false,
              allowEscapeKey: false,
              allowOutsideClick: false
            });
          } else {
            Swal.fire({
              title: "Notice!",
              text: response.message,
              icon: "info",
              allowEnterKey: false,
              allowEscapeKey: false,
              allowOutsideClick: false
            });
          }
        },
        error: function (xhr) {
          Swal.fire("Error!", "An error occurred while removing the comment. Please try again.", "error");
          console.log(xhr.responseText);
        },
      });
    }
  });

});
// <><><><><><> END JS OF REPORTED COMMENT TABLE <><><><><><>

// <><><><><><> START JS FOR SUBSCRIBER TABLE <><><><><><>
$(document).ready(function () {
  const subscriberTable = $("#subscribers-table").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#subscribers-table").data("url"),
      data: function (d) {
        d.status = $("#topics_status").val();
      },
    },
    columns: [
      { data: "id", name: "id" },
      { data: "email", name: "email" },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });
});
// <><><><><><> END JS OF SUBSCRIBER TABLE <><><><><><>

// <><><><><><> START JS FOR WEBTHEME TABLE <><><><><><>
$(document).ready(function () {
  const themeTable = $("#theme_table").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#theme_table").data("url"),
      data: function (d) { },
    },
    columns: [
      { data: "id", name: "id" },
      {
        data: "image",
        render: (data) =>
          data
            ? `<img src='${data}' class='img-channels' alt='Channel Image' />`
            : "",
      },
      { data: "name", name: "name" },
      { data: "slug", name: "slug" },

      {
        data: "is_default",
        name: "is_default",
        render: (data, type, row) => `
                    <div class="form-check form-switch">
                        <input class="form-check-input theme-switch-input" type="checkbox" data-id="${row.id
          }" ${data === 1 ? "checked" : ""
          } role="switch" aria-checked="${data === 1
          }" aria-label="Status switch for ${row.name}">
                    </div>`,
      },
      { data: "action", orderable: false, searchable: false },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });
  $("#theme_table").on("click", ".edit_btn", function () {
    const row = themeTable.row($(this).closest("tr")).data();
    if (row) {
      $("#theme-id").val(row.id);
      $("#edit-theme-name").val(row.name);
      $("#edit-theme-status").val(row.status);
      $("#edit-theme-logo-preview").attr(
        "src",
        row.image || asset("assets/images/no_image_available.png")
      );

      // Use the data attribute to get the update URL
      const updateUrl = $("#editWebThemeForm").data("update-url");
      $("#editWebThemeForm").attr("action", updateUrl + "/" + row.id);

      $("#editWebTheme").modal("show");
    }
  });

  $(document).on("change", ".theme-switch-input", function () {
    const $checkbox = $(this);
    const id = $checkbox.data("id");
    const status = $checkbox.prop("checked") ? "1" : "0";
    const url = $("#theme_status_url").val();

    // Show Swal confirmation
    Swal.fire({
      title: "Are you sure?",
      text: `Do you want to ${status === "1" ? "enable" : "disable"} this theme as default?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, proceed!",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        // Proceed with AJAX request if confirmed
        $.ajax({
          type: "POST",
          order: [[0, "desc"]],
          url: url,
          data: {
            id: id,
            status: status,
            _token: $('meta[name="csrf-token"]').attr("content"),
          },
          success: (response) => {
            showSuccessToast(response.message);
            themeTable.ajax.reload(null, false);
          },
          error: (xhr) => {
            // Revert checkbox state on error
            $checkbox.prop("checked", status === "1" ? false : true);
            Swal.fire({
              title: "Error!",
              text: "An error occurred while updating the theme status.",
              icon: "error",
              confirmButtonText: "OK",
            });
          },
        });
      } else {
        // Revert checkbox state if user cancels
        $checkbox.prop("checked", status === "1" ? false : true);
      }
    });
  });
});
/* <><><><><><><><><> END JS OF WEBTHEME TABLE <><><><><><><><><> */

// <><><><><><> START E-NEWS DELETE JS <><><><><><>
document.addEventListener('DOMContentLoaded', function () {
  const deleteButtons = document.querySelectorAll('.delete-enews-btn');

  deleteButtons.forEach(button => {
    button.addEventListener('click', function () {
      const id = this.dataset.id;

      Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById(`delete-form-${id}`).submit();
        }
      });
    });
  });
});
// <><><><><><> END E-NEWS DELETE JS <><><><><><>

$(document).ready(function () {
  // Function to initialize DataTable with common settings
  function initializeDataTable(
    selector,
    ajaxUrl,
    columns,
    additionalSettings = {}
  ) {
    return $(selector).DataTable({
      processing: true,
      serverSide: true,
      order: [[0, "desc"]],
      ajax: ajaxUrl,
      columns: columns,
      language: current_locale === "en" ? englishLanguage : hindiLanguage,
      ...additionalSettings,
    });
  }

  // Initialize Permission DataTable
  var permissionTable = initializeDataTable(
    "#permissionAjex",
    $("#permissionAjex").data("url"),
    [
      { data: "id", name: "id" },
      { data: "name", name: "name" },
      { data: "guard_name", name: "guard_name" },
      { data: "action", orderable: false, searchable: false },
    ]
  );

  // Edit Button Event for Permission DataTable
  $("#permissionAjex").on("click", ".edit_btn", function () {
    var row = permissionTable.row($(this).closest("tr")).data();
    if (row) {
      $("#permission_id").val(row.id);
      $("#edit-permission-name").val(row.name);
      $("#edit-permission-guard-name").val(row.guard_name);
      $("#edit-Permission-Form").attr(
        "action",
        route("permission.update", "") + "/" + row.id
      );
      $("#editPermissionModal").modal("show");
    }
  });

  // Initialize Roles DataTable
  initializeDataTable("#roal-list", $("#roal-list").data("url"), [
    { data: "id" },
    { data: "no" },
    { data: "name" },
    { data: "action", orderable: false, searchable: false },
  ]);

  $(document).on('click', '#delete-role', function (e) {
    e.preventDefault();

    var url = $(this).attr('href'); // The delete route URL

    Swal.fire({
      title: "Are you sure?",
      text: "This role will be permanently deleted!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            Swal.fire({
              icon: "success",
              title: "Deleted!",
              text: response.message || "Role deleted successfully",
              confirmButtonText: "OK",
              allowOutsideClick: false,
              allowEscapeKey: false,
              allowEnterKey: true
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              }
            });
          },
          error: function (xhr) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: xhr.responseJSON?.message || "Something went wrong!",
              confirmButtonText: "OK",
              allowOutsideClick: false,
              allowEscapeKey: false
            });
          }
        });
      }
    });
  });


  /* Initialize Rss Feeds DataTable */
  var rssFeedTable = initializeDataTable(
    "#rss-feed-list",
    $("#rss-feed-list").data("url"),
    [
      { data: "id", name: "id" },
      { data: "channel_name", name: "channel_name" },
      { data: "topic_name", name: "topic_name" },
      {
        data: "feed_url",
        name: "feed_url",
        render: function (data) {
          if (!data) return "";
          let url = encodeURI(data);
          return `<a href="${url}" target="_blank" rel="noopener noreferrer">${data}</a>`;
        },
      },
      { data: "data_format", name: "data_format" },
      { data: "sync_interval", name: "sync_interval" },
      {
        data: "status",
        name: "status",
        render: function (data, type, row) {
          return `<div class="form-check form-switch">
                                <input class="form-check-input switch-input rssfeed-switch-input" type="checkbox" data-id="${row.id
            }" ${data === "active" ? "checked" : ""}>
                            </div>`;
        },
      },
      {
        data: "sync",
        name: "sync",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return `<button class="btn btn-link text-decoration-none sync-single-feed-btn" data-id="${row.id}" data-status="normal">
                    <i class="fa fa-sync" id="sync_icon_${row.id}"></i>
                 </button>`;
        },
      },
      { data: "action", orderable: false, searchable: false },
    ]
  );
  // Filter event handlers
  $("#feed_status, #feed_channel, #feed_topic").on("change", function () {
    var status = $("#feed_status").val();
    var channelId = $("#feed_channel").val();
    var topicId = $("#feed_topic").val();

    var url = $("#rss-feed-list").data("url");
    var params = [];

    if (status && status !== "*") {
      params.push("feedStatus=" + status);
    }

    if (channelId && channelId !== "*") {
      params.push("channelId=" + channelId);
    }

    if (topicId && topicId !== "*") {
      params.push("topicId=" + topicId);
    }

    var newUrl = params.length > 0 ? url + "?" + params.join("&") : url;

    rssFeedTable.ajax.url(newUrl).load();
  });

  // Edit Button Event for Rss Feeds DataTable
  $("#rss-feed-list").on("click", ".edit_btn", function () {
    var row = rssFeedTable.row($(this).closest("tr")).data();
    if (row) {
      $("#rss-feed-id").val(row.id);
      $("#edit_feed_url").val(row.feed_url);
      $("#edit_channel_name").val(row.channel_id);
      $("#edit_channel_description").val(row.description);
      $("#edit_topic_name").val(row.topic_id);
      $("#edit_sync_interval").val(row.sync_interval);
      $("#edit_news_language_id").val(row.news_language_id);
      $("#edit_data_formate").val(row.data_format);
      $("#edit_description_type").val(row.description_type);
      $("#edit_status").val(row.status);
      $("#editRssFeedForm").attr(
        "action",
        route("rss-feeds.update", "") + "/" + row.id
      );
      $("#editRssFeedModal").modal("show");
    }
  });

  // Handle feed status change
  $("#feed_status").on("change", function () {
    rssFeedTable.ajax.reload();
  });

  // Handle RSS feed switch change
  $(document).on("change", ".rssfeed-switch-input", function () {
    var id = $(this).data("id");
    var status = $(this).prop("checked") ? "active" : "inactive";
    var url = $("#channel_status_url").val();
    $.ajax({
      type: "POST",
      url: url,
      data: {
        id: id,
        status: status,
        _token: $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        showSuccessToast(response.message);
      },
      error: function (xhr) {
        console.error("Error:", xhr.responseText);
      },
    });
  });

  /* Fetch single feed */
  $(document).on("click", ".sync-single-feed-btn", function () {
    const button = $(this);
    const id = button.data("id");
    const url = $("#rssfeedFetchSingle").val();
    const icon = $("#sync_icon_" + id);
    const processText = trans("Processing");

    button.prop("disabled", true);
    icon.removeClass("fa-sync");
    icon.text(processText);

    $.ajax({
      type: "POST",
      url: url,
      data: {
        id: id,
        _token: $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        button.prop("disabled", false);
        icon.text("");
        icon.addClass("fa-sync");

        if (response.error) {
          iziToast.error({
            title: response.message,
            position: 'topRight',
            timeout: 5000,
          });
          return;
        }

        // Show detailed iziToast messages for each result
        if (response.details && response.details.length > 0) {
          response.details.forEach(function (detail) {
            if (response.stats && response.stats.saved > 0 && detail.includes('saved')) {
              iziToast.success({
                title: detail,
                position: 'topRight',
                timeout: 6000,
              });
            } else if (response.stats && response.stats.skipped > 0 && (detail.includes('skipped') || detail.includes('not available'))) {
              iziToast.warning({
                title: detail,
                position: 'topRight',
                timeout: 8000,
              });
            } else if (response.stats && response.stats.already_exists > 0 && detail.includes('already exist')) {
              iziToast.info({
                title: detail,
                position: 'topRight',
                timeout: 5000,
              });
            } else {
              iziToast.success({
                title: detail,
                position: 'topRight',
                timeout: 5000,
              });
            }
          });
        }
      },
      error: function (xhr) {
        console.error("Error:", xhr.responseText);
        button.prop("disabled", false);
        icon.text("");
        icon.addClass("fa-sync");
        iziToast.error({
          title: 'Error',
          message: 'An error occurred while syncing the feed.',
          position: 'topRight',
          timeout: 5000,
        });
      },
    });
  });

  // Initialize Language List DataTable
  var languageListTable = initializeDataTable(
    "#language_list",
    $("#language_list").data("url"),
    [
      { data: "id" },
      { data: "name" },
      { data: "name_in_english" },
      { data: "code" },
      { data: "rtl", render: (data) => (data == "1" ? "Yes" : "No") },
      {
        data: "image",
        render: (data) =>
          data ? `<img src='${data}' class='img-channels'/>` : "",
      },
      { data: "action" },
    ]
  );

  // Edit Button Event for Language List DataTable
  $("#language_list").on("click", ".edit_btn", function () {
    var row = languageListTable.row($(this).closest("tr")).data();
    if (row) {
      $(".filepond").filepond("removeFile");
      $("#edit_name").val(row.name);
      $("#edit_name_in_english").val(row.name_in_english);
      $("#edit_code").val(row.code);
      $("#edit_rtl_switch").prop("checked", row.rtl === "Yes");
      $("#edit_rtl").val(row.rtl === "Yes" ? 1 : 0);
      $("#editModal").modal("show");
    }
  });

  // Reload page on button click
  $("#edit_page_reload").on("click", function () {
    setTimeout(function () {
      location.reload();
    }, 1000);
  });

  // Initialize Admin Users DataTable
  initializeDataTable("#admin_user_list", $("#admin_user_list").data("url"), [
    { data: "id" },
    { data: "name" },
    { data: "role_name" },
    { data: "email" },
    { data: "status" },
    { data: "action" },
  ]);

  initializeDataTable("#Counitry-list", $("#Counitry-list").data("url"), [
    { data: "id" },
    { data: "name" },
    { data: "emoji" },
    { data: "action", orderable: false, searchable: false },
  ]);
});

$(document).ready(function () {
  // Posts Ajax
  let postsData = [];
  let selectedPosts = new Set();

  function fetchPosts(page = 1) {
    const $postsContainer = $("#posts-container");
    const $paginationContainer = $("#pagination-container");
    const $totalPosts = $("#total-posts");

    const searchInput = $("#search-input").val();
    const filter = $("#select-filter").val();
    const topic = $("#select-topic").val();
    const channel = $("#select-channel").val();
    const dataUrl = $postsContainer.data("url");

    $.ajax({
      url: dataUrl,
      type: "GET",
      data: { page, filter, topic, channel, search: searchInput },
      success: function (response) {
        const { data = [], total, last_page, current_page } = response;
        postsData = data;

        // Generate post elements with checkboxes
        const postElements = data
          .map(
            (post) => `
                    <div class="col-sm-4 col-lg-3" data-id="${post.id}">
                        <div class="card card-sm pull-effect posts_card">
                            <!-- Checkbox for selection -->
                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                <input type="checkbox" class="form-check-input post-checkbox" 
                                       data-post-id="${post.id}" 
                                       ${selectedPosts.has(post.id) ? 'checked' : ''}>
                            </div>
                            <div class="image-container" style="height: 230px;">
                            ${post.type === "video" || post.type === "youtube"
                ? `<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-play-circle text-white card-play-button" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
      <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445"/>
    </svg>`
                : ""}
                                <img src="${post.type == "post"
                ? post.image
                : post.video_thumb
              }" class="card-img-top-custom card-img-top h-100" alt="Post Image" onerror="this.onerror=null; this.src='/assets/images/no_image_available.png';">
                                ${post.type == "video"
                ? '<div class="play-button"></div>'
                : ""
              }
                            </div>
                            <div class="card-body">
                                <h5 class="card-title custom-title">${post.title}</h5>
                                <div class="d-flex align-items-center mt-2">
                                    <img src="/storage/images/${post.channel_logo}" class="channel-post-icone" alt="Channel Logo">
                                    <div>
                                        <div>${post.channel_name}</div>
                                        <div class="text-secondary">${post.publish_date}</div>
                                    </div>
                                    <div class="ms-auto">
                                        <b class="text-secondary">
                                            <i class="fa fa-eye" aria-hidden="true"></i> ${post.view_count}
                                        </b>
                                        <b class="ms-3 text-secondary">
                                            <i class="fa fa-heart" aria-hidden="true"></i> ${post.favorite}
                                        </b>
                                        <b id="reaction-count" class="ms-3 text-secondary">
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
        $postsContainer.html(postElements);

        // Generate pagination
        function createPageItem(page, label = page, active = false, disabled = false) {
          return `
                        <li class="page-item ${active ? "active" : ""} ${disabled ? "disabled" : ""}">
                            <a class="page-link" href="javascript:void(0)" data-page="${page}">${label}</a>
                        </li>
                    `;
        }

        let paginationHtml = createPageItem(current_page - 1, trans("PREVIOUS"), false, current_page === 1);

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
                  createPageItem(last_page - 2 + i, last_page - 2 + i, current_page === last_page - 2 + i)
                ).join("")
                : createPageItem(1) +
                '<li class="page-item disabled"><span class="page-link">...</span></li>' +
                Array.from({ length: 3 }, (_, i) =>
                  createPageItem(current_page - 1 + i, current_page - 1 + i, current_page === current_page - 1 + i)
                ).join("") +
                '<li class="page-item disabled"><span class="page-link">...</span></li>' +
                createPageItem(last_page);
        }

        paginationHtml += createPageItem(current_page + 1, trans("NEXT"), false, current_page === last_page);
        $paginationContainer.html(paginationHtml);
        $totalPosts.html(`1-${data.length} ${trans("OF")} ${total} ${trans("POSTS")}`);

        updateBulkDeleteUI();
      },
      error: function (error) {
        console.error("Error fetching posts:", error);
      },
    });
  }

  // Update bulk delete UI visibility and count
  function updateBulkDeleteUI() {
    const count = selectedPosts.size;

    if (count > 0) {
      $("#select-all-posts").removeClass("d-none");
      $("#bulk-delete-btn").removeClass("d-none");
      $("#selected-count-badge").text(count);

      // Update select all checkbox state
      const allChecked = $(".post-checkbox").length === $(".post-checkbox:checked").length;
      $("#select-all-checkbox").prop("checked", allChecked);
    } else {
      $("#select-all-posts").addClass("d-none");
      $("#bulk-delete-btn").addClass("d-none");
      selectedPosts.clear();
    }
  }

  // Handle individual checkbox change
  $(document).on("change", ".post-checkbox", function (e) {
    e.stopPropagation();
    const postId = $(this).data("post-id");

    if ($(this).is(":checked")) {
      selectedPosts.add(postId);
    } else {
      selectedPosts.delete(postId);
    }

    updateBulkDeleteUI();
  });

  // Handle select all checkbox
  $(document).on("change", "#select-all-checkbox", function () {
    const isChecked = $(this).is(":checked");

    $(".post-checkbox").prop("checked", isChecked);

    if (isChecked) {
      $(".post-checkbox").each(function () {
        selectedPosts.add($(this).data("post-id"));
      });
    } else {
      selectedPosts.clear();
    }

    updateBulkDeleteUI();
  });

  // Handle bulk delete action
  $(document).on("click", "#bulk-delete-action", function () {
    if (selectedPosts.size === 0) {
      Swal.fire({
        icon: 'warning',
        title: trans("NO_POSTS_SELECTED") || "No Posts Selected",
        text: trans("PLEASE_SELECT_POSTS") || "Please select posts to delete",
        confirmButtonText: 'OK'
      });
      return;
    }

    const confirmMessage =
      `Are you sure you want to delete ${selectedPosts.size} posts?`;

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
        const postIds = Array.from(selectedPosts);
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
            selectedPosts.clear();
            fetchPosts();

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
    return description
      .replace(/<\/?[^>]+(>|$)/g, "")
      .replace(/&nbsp;/g, " ")
      .replace(/&#39;/g, "'")
      .replace(/&quot;/g, '"');
  }

  function showPostModal(post, delete_url) {
    if (post.type === "post") {
      $("#video-preview").addClass("d-none");
      $("#video_frame").addClass("d-none");
      $("#post-image")
        .removeClass("d-none")
        .attr("src", post.image ? post.image : "/assets/images/no_image_available.png")
        .on("error", function () {
          $(this).off("error").attr("src", "/assets/images/no_image_available.png");
        });
    } else if (post.type === "youtube") {
      $("#video-preview").addClass("d-none");
      $("#post-image").addClass("d-none");
      $("#video_frame")
        .removeClass("d-none")
        .attr("src", post.video)
        .on("error", function () {
          $(this).off("error").attr("src", "/assets/images/no_image_available.png");
        });
    } else if (post.type === "video") {
      $("#video_frame").addClass("d-none");
      $("#post-image").addClass("d-none");
      $("#video-preview")
        .removeClass("d-none")
        .find("source")
        .attr("src", post.video);
      $("#video-preview")[0].load();
    }

    $("#post-title").text(removeHtmlTags(post.title));
    $("#channel-logo")
      .attr("src", `/storage/images/${post.channel_logo}`)
      .on("error", function () {
        $(this).off("error").attr("src", "/assets/images/no_image_available.png");
      });
    $("#channel-name").text(post.channel_name);
    $("#post-date").text(post.pubdate);
    $("#view-count").html(`<i class="bi bi-eye-fill"></i> ${post.view_count}`);
    $("#view-comments").html(`<i class="bi bi-chat-left-text-fill"></i> ${post.comment}`);
    $("#comments_url").attr("href", `/admin/comments?post=${post.slug}`);
    $("#favorite-count").html(`<i class="bi bi-heart-fill"></i> ${post.favorite}`);
    const description = removeHtmlTags(post.description);
    $("#post-description-text").text(description).addClass("line-clamp-3");
    if (description.length > 150) {
      $("#read-more-btn").show().text("Read more");
    } else {
      $("#read-more-btn").hide();
    }
    $("#edit-post-btn").attr("href", `/admin/posts/${post.id}/edit`);
    $("#notification-post-btn").attr("data-notification-url", `/admin/posts/${post.id}/sendNotification`);

    if (post.type === 'video') {
      $("#edit-video-custom-btn").removeClass('d-none').attr("href", `/admin/videos/${post.id}/custom`);
      $("#notification-video-custom-btn").attr("data-notification-url", `/admin/videos/${post.id}/sendNotification`);
      $("#edit-video-youtube-btn").addClass('d-none');
    } else if (post.type === 'youtube') {
      $("#edit-video-youtube-btn").removeClass('d-none').attr("href", `/admin/videos/${post.id}/youtube`);
      $("#notification-video-custom-btn").attr("data-notification-url", `/admin/videos/${post.id}/sendNotification`);
      $("#edit-video-custom-btn").addClass('d-none');
    } else {
      $("#edit-video-custom-btn").addClass('d-none');
      $("#edit-video-youtube-btn").addClass('d-none');
    }

    $("#post_delete_url").attr("href", delete_url);
    $("#post-description").modal("show");
    $("#reaction-count").html(`
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-thumb-up">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M13 3a3 3 0 0 1 2.995 2.824l.005 .176v4h2a3 3 0 0 1 2.98 2.65l.015 .174l.005 .176l-.02 .196l-1.006 5.032c-.381 1.626 -1.502 2.796 -2.81 2.78l-.164 -.008h-8a1 1 0 0 1 -.993 -.883l-.007 -.117l.001 -9.536a1 1 0 0 1 .5 -.865a2.998 2.998 0 0 0 1.492 -2.397l.007 -.202v-1a3 3 0 0 1 3 -3z" />
        <path d="M5 10a1 1 0 0 1 .993 .883l.007 .117v9a1 1 0 0 1 -.883 .993l-.117 .007h-1a2 2 0 0 1 -1.995 -1.85l-.005 -.15v-7a2 2 0 0 1 1.85 -1.995l.15 -.005h1z" />
      </svg>
    `);
  }

  // Event Delegation - click on card (excluding checkbox)
  $("#posts-container").on("click", ".col-sm-4", function (e) {
    // Don't open modal if clicking on checkbox or its label
    if ($(e.target).hasClass("post-checkbox") || $(e.target).closest(".form-check-input").length) {
      return;
    }

    const post = postsData.find((p) => p.id === $(this).data("id"));
    const currentURL = $(location).attr("href");
    const delete_url = currentURL + "/" + post.id;

    if (post) showPostModal(post, delete_url);
  });

  $('#post-description').on('hidden.bs.modal', function () {
    var video = document.getElementById('video-preview');
    if (!$(video).hasClass('d-none')) {
      video.pause();
      video.currentTime = 0;
      video.src = "";
    }
  });

  $(document).on("click", ".page-link", function () {
    const page = $(this).data("page");
    if (page) fetchPosts(page);
  });

  function onFilterChange() {
    fetchPosts();
  }

  $("#select-filter, #select-topic, #select-channel, #search-input").on("change keyup", onFilterChange);

  // Initial fetch
  fetchPosts();
});
$(document).on("click", "#notification-post-btn, #notification-video-custom-btn,#notification-audio-btn", function (e) {
  e.preventDefault();

  const button = $(this);
  const notificationUrl = button.attr("data-notification-url");

  if (!notificationUrl) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Notification URL is missing! Please close and reopen the modal.",
    });
    return;
  }

  // Show SweetAlert confirmation
  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to send a notification for this post?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, send it!",
    cancelButtonText: "Cancel",
    allowOutsideClick: false,
    allowEnterKey: false,
    allowEscapeKey: false,
  }).then((result) => {
    if (result.isConfirmed) {
      // Disable button and show loading state
      button.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

      $.ajax({
        url: notificationUrl, // ✅ Use the stored URL
        type: "POST",
        data: {
          _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
          Swal.fire({
            icon: "success",
            title: "Notification Sent!",
            text: response.message || "The notification was successfully sent!",
            timer: 2000,
            showConfirmButton: true,
            confirmButtonText: "Ok",
            allowOutsideClick: false,
            allowEnterKey: false,
            allowEscapeKey: false,
          });

        },
        error: function (xhr) {
          let errorMessage = "Something went wrong while sending the notification.";

          if (xhr.status === 404) {
            errorMessage = "Route not found! Please check your routes.";
          } else if (xhr.status === 419) {
            errorMessage = "Session expired. Please refresh the page.";
          } else if (xhr.responseJSON?.message) {
            errorMessage = xhr.responseJSON.message;
          }

          Swal.fire({
            icon: "error",
            title: "Failed!",
            text: errorMessage,
          });
        },
        complete: function () {
          button.prop("disabled", false).html('Send Notification');
        },
      });
    }
  });
});
/* <><><><><><><><><> START JS FOR POST AND VIDEO COUNT <><><><><><><><><> */
$(document).ready(function () {
  // Dashboard Chart Initialization and Update
  const chartElement = document.getElementById("combinedCharts");
  if (chartElement) {
    const ctx = chartElement.getContext("2d");

    // Gradient colors
    const postGradient = ctx.createLinearGradient(0, 0, 0, 400);
    postGradient.addColorStop(0, "rgba(54,162,235,0.4)");
    postGradient.addColorStop(1, "rgba(54,162,235,0)");

    const videoGradient = ctx.createLinearGradient(0, 0, 0, 400);
    videoGradient.addColorStop(0, "rgba(255,99,132,0.4)");
    videoGradient.addColorStop(1, "rgba(255,99,132,0)");

    const combinedChart = new Chart(ctx, {
      type: "line",
      data: {
        labels: [],
        datasets: [
          {
            label: "Posts",
            data: [],
            borderColor: "#36A2EB",
            backgroundColor: postGradient,
            fill: true,
            tension: 0.45,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: "#36A2EB",
          },
          {
            label: "Videos",
            data: [],
            borderColor: "#FF6384",
            backgroundColor: videoGradient,
            fill: true,
            tension: 0.45,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: "#FF6384",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: "index",
          intersect: false,
        },
        plugins: {
          legend: {
            position: "top",
            labels: {
              usePointStyle: true,
              padding: 20,
              font: {
                size: 13,
                weight: "600",
              },
            },
          },
          tooltip: {
            backgroundColor: "#1e293b",
            titleColor: "#fff",
            bodyColor: "#fff",
            padding: 12,
            cornerRadius: 6,
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "#6b7280",
            },
          },
          y: {
            beginAtZero: true,
            grid: {
              color: "rgba(0,0,0,0.05)",
            },
            ticks: {
              color: "#6b7280",
              stepSize: 1,
            },
          },
        },
      },
    });

    function updateChart(startDate, endDate) {
      // Format dates as YYYY-MM-DD for the API
      const start = startDate.format("YYYY-MM-DD");
      const end = endDate.format("YYYY-MM-DD");
      fetch(`chart/data?start=${start}&end=${end}`)
        .then((response) => response.json())
        .then((data) => {
          combinedChart.data.labels = data.labels || [];
          combinedChart.data.datasets[0].data = data.posts || [];
          combinedChart.data.datasets[1].data = data.videos || [];
          combinedChart.update();
        })
        .catch((error) => console.error("Error fetching chart data:", error));
    }

    // Initialize daterangepicker
    var start = moment().subtract(29, "days");
    var end = moment();

    function cb(start, end) {
      const rangeText = start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY");
      $("#reportranges span").html(rangeText);
      updateChart(start, end);
    }

    $("#reportranges").daterangepicker(
      {
        startDate: start,
        endDate: end,
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
      },
      cb
    );

    // Initial chart update
    cb(start, end);

    // System success function
    window.SystemSuccessFunction = () => window.location.reload();
  }
});
/* <><><><><><><><><> END JS OF POST AND VIDEO COUNT <><><><><><><><><> */

/* <><><><><><><><><> START JS FOR MOST LIKE POST AND VIDEO CHART <><><><><><><><><> */
$(document).ready(function () {
  // Dashboard Chart Initialization and Update
  const chartElement = document.getElementById("combinedCharts_liked");
  if (!chartElement) {

    return;
  }

  const ctx = chartElement.getContext("2d");
  const combinedChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        {
          label: "Most Liked Posts",
          data: [],
          borderColor: "rgba(153, 102, 255, 1)",
          backgroundColor: "rgba(223, 106, 198, 0.64)",
          fill: false,
        },
        {
          label: "Most Liked Videos",
          data: [],
          borderColor: "rgba(30, 201, 53, 1)",
          backgroundColor: "rgba(24, 174, 212, 0.47)",
          fill: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: "top" },
        tooltip: { mode: "index", intersect: false },
      },
      scales: {
        x: { display: true, title: { display: true, text: "Date" } },
        y: {
          display: true,
          title: { display: true, text: "Max Reactions" },
          suggestedMin: 0,
        },
      },
    },
  });

  function updateChart(startDate, endDate) {
    const start = startDate.format("YYYY-MM-DD");
    const end = endDate.format("YYYY-MM-DD");
    const fetchUrl = `chart/data?start=${start}&end=${end}`;
    console.log(`Fetching data from: ${fetchUrl}`);

    fetch(fetchUrl)
      .then((response) => {
        console.log("Response status:", response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log("Received data:", data);
        if (data.error) {
          console.error("Server error:", data.message);
          combinedChart.data.labels = [];
          combinedChart.data.datasets[0].data = [];
          combinedChart.data.datasets[1].data = [];
        } else {
          combinedChart.data.labels = data.labels || [];
          combinedChart.data.datasets[0].data = data.mostLikedPosts || [];
          combinedChart.data.datasets[1].data = data.mostLikedVideos || [];
          console.log("Labels:", data.labels);
          console.log("Most Liked Posts:", data.mostLikedPosts);
          console.log("Most Liked Videos:", data.mostLikedVideos);
        }
        combinedChart.update();
      })
      .catch((error) => {
        console.error("Error fetching chart data:", error);
        combinedChart.data.labels = [];
        combinedChart.data.datasets[0].data = [];
        combinedChart.data.datasets[1].data = [];
        combinedChart.update();
      });
  }

  // Initialize daterangepicker
  const reportRange = $("#reportrange_liked");
  if (!reportRange.length) {
    console.error("Daterangepicker element 'reportrange_liked' not found");
    return;
  }

  var start = moment().subtract(29, "days");
  var end = moment();



  function cb(start, end) {
    const rangeText = start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY");
    $("#reportrange_liked span").html(rangeText);
    updateChart(start, end);
  }


  reportRange.daterangepicker(
    {
      startDate: start,
      endDate: end,
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
      },
    },
    cb
  );

  // Initial chart update
  cb(start, end);

  // System success function
  window.SystemSuccessFunction = () => window.location.reload();
});
/* <><><><><><><><><> END JS OF MOST LIKE POST AND VIDEO CHART <><><><><><><><><> */

/* <><><><><><><><><> START JS FOR MOST FOLLOWED CHANNELS CHART <><><><><><><><><> */
$(document).ready(function () {
  // Dashboard Chart Initialization and Update
  const chartElement = document.getElementById("mostfollowed_channels");
  if (!chartElement) {
    return;
  }

  const ctx = chartElement.getContext("2d");

  const combinedChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: [],
      datasets: [
        {
          label: "Followers",
          data: [],
          backgroundColor: [
            "#a46cee", // Indigo
            "#ec5fa6", // Pink
            "#fda43f", // Amber
            "#10B981", // Green
            "#3B82F6"  // Blue
          ],
          borderWidth: 3,
          hoverOffset: 12
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "65%", // donut thickness

      plugins: {
        legend: {
          position: "bottom",
          labels: {
            color: "#374151",
            font: {
              size: 13,
              weight: "600"
            },
            padding: 20,
            usePointStyle: true,
            pointStyle: "circle"
          }
        },

        tooltip: {
          enabled: true,
          backgroundColor: "#111827",
          titleColor: "#fff",
          bodyColor: "#fff",
          padding: 12,
          cornerRadius: 8,
          callbacks: {
            label: function (context) {
              const index = context.dataIndex;
              const channel = combinedChart.channels[index] || {};
              return `${channel.name || 'Unknown'} : ${context.raw} followers`;
            }
          }
        }
      }
    }
  });

  // Store channel data for tooltip access
  combinedChart.channels = [];

  function updateChart(startDate, endDate) {
    const start = startDate.format("YYYY-MM-DD");
    const end = endDate.format("YYYY-MM-DD");
    const fetchUrl = `chart/data?start=${start}&end=${end}`;
    console.log(`Fetching data from: ${fetchUrl}`);

    fetch(fetchUrl)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log("Received data:", data);
        if (data.error) {
          console.error("Server error:", data.message);
          combinedChart.data.labels = [];
          combinedChart.data.datasets[0].data = [];
          combinedChart.channels = [];
        } else {
          // Update chart with top 3 channels
          combinedChart.data.labels = data.topChannels.map(channel => channel.name);
          combinedChart.data.datasets[0].data = data.topChannels.map(channel => channel.follow_count);
          combinedChart.channels = data.topChannels;
        }
        combinedChart.update();
      })
      .catch((error) => {
        console.error("Error fetching chart data:", error);
        combinedChart.data.labels = [];
        combinedChart.data.datasets[0].data = [];
        combinedChart.channels = [];
        combinedChart.update();
      });
  }



  // Initialize daterangepicker
  const reportRange = $("#reportrange_mostfollowed_channels");
  if (!reportRange.length) {
    console.error("Daterangepicker element 'reportrange_mostfollowed_channels' not found");
    return;
  }

  var start = moment().subtract(29, "days");
  var end = moment();

  function cb(start, end) {
    const rangeText = start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY");
    $("#reportrange_mostfollowed_channels span").html(rangeText);
    updateChart(start, end);
  }

  reportRange.daterangepicker(
    {
      startDate: start,
      endDate: end,
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
      },
    },
    cb
  );

  // Initial chart update
  cb(start, end);

  // System success function
  window.SystemSuccessFunction = () => window.location.reload();
});
/* <><><><><><><><><> END JS OF MOST FOLLOWED CHANNELS CHART <><><><><><><><><> */

/* <><><><><><><><><> START JS FOR SUBSCRIPTION CHART <><><><><><><><><> */
$(document).ready(function () {
  // Dashboard Chart Initialization and Update
  const chartElement = document.getElementById("subscription_chart");
  if (chartElement) {
    const ctx = chartElement.getContext("2d");

    // Create gradient background
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, "#125058");
    gradient.addColorStop(1, "#2ea6b6");

    const combinedChart = new Chart(ctx, {
      type: "line",
      data: {
        labels: [],
        datasets: [
          {
            label: "Subscriptions",
            data: [],
            borderColor: "#125058",
            backgroundColor: gradient,
            fill: true,
            tension: 0.45,
            borderWidth: 3,

            pointBackgroundColor: "#125058",
            pointBorderColor: "#fff",
            pointRadius: 4,
            pointHoverRadius: 7,
            pointHoverBackgroundColor: "#125058",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
          legend: {
            position: "top",
            labels: {
              color: "#374151",
              font: {
                size: 13,
                weight: "600",
              },
              usePointStyle: true,
            },
          },

          tooltip: {
            backgroundColor: "#111827",
            titleColor: "#fff",
            bodyColor: "#fff",
            padding: 12,
            cornerRadius: 8,
            mode: "index",
            intersect: false,
          },
        },

        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "#374151",
            },
            title: {
              display: true,
              text: "Date",
              color: "#374151",
              font: { weight: "600" },
            },
          },

          y: {
            grid: {
              color: "rgba(0,0,0,0.05)",
            },
            ticks: {
              color: "#374151",
            },
            title: {
              display: true,
              text: "Subscriptions",
              color: "#374151",
              font: { weight: "600" },
            },
            suggestedMin: 0,
          },
        },
      },
    });

    function updateChart(startDate, endDate) {
      // Format dates as YYYY-MM-DD for the API
      const start = startDate.format("YYYY-MM-DD");
      const end = endDate.format("YYYY-MM-DD");
      fetch(`chart/data?start=${start}&end=${end}`)
        .then((response) => response.json())
        .then((data) => {
          combinedChart.data.labels = data.labels || [];
          combinedChart.data.datasets[0].data = data.subscriptions || [];
          combinedChart.update();
        })
        .catch((error) => console.error("Error fetching chart data:", error));
    }

    // Initialize daterangepicker
    var start = moment().subtract(29, "days");
    var end = moment();

    function cb(start, end) {
      const rangeText = start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY");
      $("#subscription_picker span").html(rangeText);
      updateChart(start, end);
    }

    $("#subscription_picker").daterangepicker(
      {
        startDate: start,
        endDate: end,
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
      },
      cb
    );

    // Initial chart update
    cb(start, end);

    // System success function
    window.SystemSuccessFunction = () => window.location.reload();
  }
});
/* <><><><><><><><><> END JS OF MOST FOLLOWED CHANNELS CHART <><><><><><><><><> */

/* <><><><><><><><><> START JS FOR TRANSACTION CHART <><><><><><><><><> */
$(document).ready(function () {
  // Transactions Chart Initialization and Update
  const chartElement = document.getElementById("transactions_chart");
  if (!chartElement) {
    return;
  }

  const ctx = chartElement.getContext("2d");

  const combinedChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        {
          label: "Transactions",
          data: [],
          backgroundColor: "#ff5c848a", // modern green
          borderColor: "#ff5c848a",
          borderWidth: 1.5,
          barThickness: 28,
          hoverBackgroundColor: "#ff5c848a"
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,

      plugins: {
        legend: {
          position: "top",
          labels: {
            color: "#374151",
            font: {
              size: 13,
              weight: "600",
            },
            usePointStyle: true,
          },
        },

        tooltip: {
          backgroundColor: "#111827",
          titleColor: "#fff",
          bodyColor: "#fff",
          padding: 10,
          cornerRadius: 8,
          mode: "index",
          intersect: false,
        },
      },

      scales: {
        x: {
          grid: {
            display: false,
          },
          ticks: {
            color: "#6B7280",
          },
          title: {
            display: true,
            text: "Date",
            color: "#374151",
            font: { weight: "600" },
          },
        },

        y: {
          grid: {
            color: "rgba(0,0,0,0.05)",
          },
          ticks: {
            color: "#6B7280",
          },
          title: {
            display: true,
            text: "Transaction Count",
            color: "#374151",
            font: { weight: "600" },
          },
          suggestedMin: 0,
        },
      },
    },
  });

  function updateChart(startDate, endDate) {
    const start = startDate.format("YYYY-MM-DD");
    const end = endDate.format("YYYY-MM-DD");
    const fetchUrl = `chart/data?start=${start}&end=${end}`;
    console.log(`Fetching data from: ${fetchUrl}`);

    fetch(fetchUrl)
      .then((response) => {
        console.log("Response status:", response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log("Received data:", data);
        if (data.error) {
          console.error("Server error:", data.message);
          combinedChart.data.labels = [];
          combinedChart.data.datasets[0].data = [];
        } else {
          combinedChart.data.labels = data.labels || [];
          combinedChart.data.datasets[0].data = data.transactions || [];
          console.log("Labels:", data.labels);
          console.log("Transactions:", data.transactions);
        }
        combinedChart.update();
      })
      .catch((error) => {
        console.error("Error fetching chart data:", error);
        combinedChart.data.labels = [];
        combinedChart.data.datasets[0].data = [];
        combinedChart.update();
      });
  }

  // Initialize daterangepicker
  const reportRange = $("#transaction_picker");
  if (!reportRange.length) {
    console.error("Daterangepicker element 'transaction_picker' not found");
    return;
  }

  var start = moment().subtract(29, "days");
  var end = moment();

  function cb(start, end) {
    const rangeText = start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY");
    $("#transaction_picker span").html(rangeText);
    updateChart(start, end);
  }

  try {
    reportRange.daterangepicker(
      {
        startDate: start,
        endDate: end,
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
      },
      cb
    );
    console.log("Daterangepicker initialized successfully");
  } catch (error) {
    console.error("Error initializing daterangepicker:", error);
  }

  // Initial chart update
  cb(start, end);

  // System success function
  window.SystemSuccessFunction = () => window.location.reload();
});
/* <><><><><><><><><> END JS OF TRANSACTION CHART <><><><><><><><><> */

/* <><><><><><><><><> START JS FOR TRANSACTION CHART <><><><><><><><><> */
$(document).ready(function () {
  // Active Users Chart Initialization and Update
  const chartElement = document.getElementById("active_users_chart");
  if (!chartElement) {
    return;
  }

  const ctx = chartElement.getContext("2d");
  const activeUsersChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        {
          label: "Daily Active Users",
          data: [],
          borderColor: "#22C55E",
          backgroundColor: "rgba(34,197,94,0.35)",
        },
        {
          label: "Hourly Active Users",
          data: [],
          borderColor: "#e94fbb",
          backgroundColor: "#e94fbb7e",
        }
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: "top" },
        tooltip: { mode: "index", intersect: false },
      },
      scales: {
        x: { display: true, title: { display: true, text: "Date / Hour" } },
        y: {
          display: true,
          title: { display: true, text: "Active Users Count" },
          suggestedMin: 0,
        },
      },
    },
  });

  function updateChart(startDate, endDate, timeFilter = '') {
    const start = startDate.format("YYYY-MM-DD");
    const end = endDate.format("YYYY-MM-DD");
    const fetchUrl = `chart/data?start=${start}&end=${end}${timeFilter ? `&time_filter=${timeFilter}` : ''}`;
    console.log(`Fetching data from: ${fetchUrl}`);

    fetch(fetchUrl)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        if (data.error) {
          console.error("Server error:", data.message);
          activeUsersChart.data.labels = [];
          activeUsersChart.data.datasets[0].data = [];
          activeUsersChart.data.datasets[1].data = [];
        } else {
          // Update daily active users
          activeUsersChart.data.labels = data.labels || [];
          activeUsersChart.data.datasets[0].data = data.active_users || [];

          // Update hourly active users (use hours 0-23 as labels)
          const hourlyLabels = Array.from({ length: 24 }, (_, i) => i);
          activeUsersChart.data.datasets[1].data = data.active_users_hourly || [];
          activeUsersChart.data.labels = timeFilter ? hourlyLabels : data.labels; // Use hours only if time filter is applied
        }
        activeUsersChart.update();
      })
      .catch((error) => {
        console.error("Error fetching chart data:", error);
        activeUsersChart.data.labels = [];
        activeUsersChart.data.datasets[0].data = [];
        activeUsersChart.data.datasets[1].data = [];
        activeUsersChart.update();
      });
  }

  // Initialize daterangepicker for active users
  const reportRange = $("#active_users_picker");
  if (!reportRange.length) {
    console.error("Daterangepicker element 'active_users_picker' not found");
    return;
  }

  var start = moment().subtract(29, "days");
  var end = moment();

  function cb(start, end) {
    const rangeText = start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY");
    $("#active_users_picker span").html(rangeText);
    updateChart(start, end, $("#active_users_time_filter").val());
  }

  try {
    reportRange.daterangepicker(
      {
        startDate: start,
        endDate: end,
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
      },
      cb
    );
    console.log("Daterangepicker initialized successfully");
  } catch (error) {
    console.error("Error initializing daterangepicker:", error);
  }

  // Add event listener for time filter change
  $("#active_users_time_filter").on("change", function () {
    const timeFilter = $(this).val();
    updateChart(moment().subtract(29, "days"), moment(), timeFilter);
  });

  // Initial chart update
  cb(start, end);

  // System success function
  window.SystemSuccessFunction = () => window.location.reload();
});
/* <><><><><><><><><> END JS OF TRANSACTION CHART <><><><><><><><><> */

$(document).ready(function () {

  // -----------------------------
  // Google Analytics / AdSense Chart
  // -----------------------------
  const gaCtx = document.getElementById("ga_adsense_chart");

  let gaChart = new Chart(gaCtx.getContext("2d"), {
    type: "line",
    data: {
      labels: [],
      datasets: [
        {
          label: "Impressions",
          data: [],
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.4,
          fill: true
        },
        {
          label: "Clicks",
          data: [],
          borderColor: 'rgb(54, 162, 235)',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          tension: 0.4,
          fill: true
        },
        {
          label: "Earnings (₹)",
          data: [],
          borderColor: 'rgb(255, 205, 86)',
          backgroundColor: 'rgba(255, 205, 86, 0.2)',
          tension: 0.4,
          fill: true,
          yAxisID: 'y1'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          position: "top",
          labels: {
            usePointStyle: true,
            padding: 15
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          titleFont: {
            size: 14
          },
          bodyFont: {
            size: 13
          },
          callbacks: {
            label: function (context) {
              let label = context.dataset.label || '';
              if (label) {
                label += ': ';
              }
              if (context.dataset.label === 'Earnings (₹)') {
                label += '₹' + context.parsed.y.toFixed(2);
              } else {
                label += context.parsed.y.toLocaleString();
              }
              return label;
            }
          }
        }
      },
      scales: {
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          beginAtZero: true,
          title: {
            display: true,
            text: 'Impressions & Clicks'
          },
          ticks: {
            callback: function (value) {
              return value.toLocaleString();
            }
          }
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          beginAtZero: true,
          title: {
            display: true,
            text: 'Earnings (₹)'
          },
          grid: {
            drawOnChartArea: false
          },
          ticks: {
            callback: function (value) {
              return '₹' + value.toFixed(2);
            }
          }
        }
      }
    }
  });

  // -----------------------------
  // Fetch Analytics Data
  // -----------------------------
  function updateGAChart(startDate, endDate) {
    // Show loading state
    $("#ga_impressions").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#ga_clicks").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#ga_earnings").html('<i class="fas fa-spinner fa-spin"></i>');
    $("#ga_ctr").html('<i class="fas fa-spinner fa-spin"></i>');

    fetch(`chart/data?start=${startDate}&end=${endDate}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(res => {
        if (!res.adsense) {
          throw new Error('No AdSense data received');
        }

        const adsense = res.adsense;

        // Update connection status
        if (adsense.is_demo) {
          $("#adsense_status").removeClass('d-none alert-success').addClass('alert-warning');
          $("#adsense_status").html(
            '<i class="fas fa-exclamation-triangle"></i> ' +
            'Google AdSense not connected. Showing demo data. ' +
            '<a href="/admin/adsense/callback" class="alert-link">Connect Now</a>'
          );
        } else {
          $("#adsense_status").removeClass('d-none alert-warning').addClass('alert-success');
          $("#adsense_status").html(
            '<i class="fas fa-check-circle"></i> Google AdSense connected successfully!'
          );
        }

        // Update chart
        gaChart.data.labels = adsense.labels;
        gaChart.data.datasets[0].data = adsense.impressions;
        gaChart.data.datasets[1].data = adsense.clicks;
        gaChart.data.datasets[2].data = adsense.earnings;
        gaChart.update();

        // Calculate totals
        const totalImpressions = adsense.impressions.reduce((a, b) => a + b, 0);
        const totalClicks = adsense.clicks.reduce((a, b) => a + b, 0);
        const totalEarnings = adsense.earnings.reduce((a, b) => a + b, 0);
        const avgCtr = totalImpressions > 0
          ? ((totalClicks / totalImpressions) * 100)
          : 0;

        // Update stats with animation
        animateValue('ga_impressions', 0, totalImpressions, 1000);
        animateValue('ga_clicks', 0, totalClicks, 1000);
        animateValue('ga_earnings', 0, totalEarnings, 1000, true);
        animateValue('ga_ctr', 0, avgCtr, 1000, false, true);

        // Calculate and display trends
        displayTrends(adsense);
      })
      .catch(err => {
        console.error("Analytics Error:", err);
        $("#adsense_status").removeClass('d-none alert-success').addClass('alert-danger');
        $("#adsense_status").html(
          '<i class="fas fa-times-circle"></i> Error loading AdSense data. Please try again.'
        );

        // Reset stats
        $("#ga_impressions").text('0');
        $("#ga_clicks").text('0');
        $("#ga_earnings").text('0.00');
        $("#ga_ctr").text('0%');
      });
  }

  // -----------------------------
  // Animate Number Counter
  // -----------------------------
  function animateValue(id, start, end, duration, isCurrency = false, isPercentage = false) {
    const element = document.getElementById(id);
    const range = end - start;
    const increment = range / (duration / 16); // 60 FPS
    let current = start;

    const timer = setInterval(() => {
      current += increment;
      if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
        current = end;
        clearInterval(timer);
      }

      let displayValue;
      if (isCurrency) {
        displayValue = current.toFixed(2);
      } else if (isPercentage) {
        displayValue = current.toFixed(2) + '%';
      } else {
        displayValue = Math.floor(current).toLocaleString();
      }

      element.textContent = displayValue;
    }, 16);
  }

  // -----------------------------
  // Display Trends
  // -----------------------------
  function displayTrends(adsense) {
    const midPoint = Math.floor(adsense.impressions.length / 2);

    // First half vs second half comparison
    const firstHalf = {
      impressions: adsense.impressions.slice(0, midPoint).reduce((a, b) => a + b, 0),
      clicks: adsense.clicks.slice(0, midPoint).reduce((a, b) => a + b, 0),
      earnings: adsense.earnings.slice(0, midPoint).reduce((a, b) => a + b, 0)
    };

    const secondHalf = {
      impressions: adsense.impressions.slice(midPoint).reduce((a, b) => a + b, 0),
      clicks: adsense.clicks.slice(midPoint).reduce((a, b) => a + b, 0),
      earnings: adsense.earnings.slice(midPoint).reduce((a, b) => a + b, 0)
    };

    // Calculate percentage changes
    const impTrend = calculateTrend(firstHalf.impressions, secondHalf.impressions);
    const clickTrend = calculateTrend(firstHalf.clicks, secondHalf.clicks);
    const earnTrend = calculateTrend(firstHalf.earnings, secondHalf.earnings);

    // Update trend indicators
    updateTrendIndicator('imp_trend', impTrend);
    updateTrendIndicator('click_trend', clickTrend);
    updateTrendIndicator('earn_trend', earnTrend);
  }

  function calculateTrend(oldVal, newVal) {
    if (oldVal === 0) return newVal > 0 ? 100 : 0;
    return ((newVal - oldVal) / oldVal) * 100;
  }

  function updateTrendIndicator(elementId, percentage) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const absPercentage = Math.abs(percentage).toFixed(1);
    const icon = percentage >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
    const colorClass = percentage >= 0 ? 'text-success' : 'text-danger';

    element.innerHTML = `<small class="${colorClass}">
      <i class="fas ${icon}"></i> ${absPercentage}%
    </small>`;
  }

  // -----------------------------
  // Date Range Picker
  // -----------------------------
  let start = moment().subtract(29, 'days');
  let end = moment();

  function cb(start, end) {
    $("#ga_picker span").html(
      start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY')
    );

    updateGAChart(
      start.format('YYYY-MM-DD'),
      end.format('YYYY-MM-DD')
    );
  }

  $("#ga_picker").daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
      "Today": [moment(), moment()],
      "Yesterday": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      "Last 7 Days": [moment().subtract(6, 'days'), moment()],
      "Last 30 Days": [moment().subtract(29, 'days'), moment()],
      "This Month": [moment().startOf('month'), moment().endOf('month')],
      "Last Month": [
        moment().subtract(1, 'month').startOf('month'),
        moment().subtract(1, 'month').endOf('month')
      ]
    },
    locale: {
      format: 'MMMM D, YYYY'
    }
  }, cb);

  // Initial Load
  cb(start, end);

  // Auto-refresh every 5 minutes (optional)
  setInterval(() => {
    updateGAChart(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
  }, 300000);

});

$(document).ready(function () {
  // Initialize DataTables for error logs
  $("#table-log").DataTable({
    order: [$("#table-log").data("orderingIndex"), "desc"],
    stateSave: true,
    stateSaveCallback: function (settings, data) {
      window.localStorage.setItem("datatable", JSON.stringify(data));
    },
    stateLoadCallback: function (settings) {
      var data = JSON.parse(window.localStorage.getItem("datatable"));
      if (data) data.start = 0;
      return data;
    },
  });

  // Bind click events for log actions
  $("#delete-log, #clean-log, #delete-all-log").click(function () {
    return confirm("Are you sure?");
  });

  // Initialize theme switcher for dark mode
  const darkSwitch = $("#darkSwitch");
  if (darkSwitch.length) {
    initTheme();
    darkSwitch.on("change", resetTheme);
  }

  // Search functionality for country list
  $("#countrySearch").on("input", function () {
    const searchTerm = $(this).val().toLowerCase();
    $("#countryList .country-item").each(function () {
      const countryName = $(this).data("name").toLowerCase();
      $(this).toggle(countryName.includes(searchTerm));
    });
  });


  // <><><><><><><><><><><> START CREATE JS FOR NOTIFICATION <><><><><><><><><><><>  
  $(document).ready(function () {
    // Store selected user IDs across all pages
    var selectedUserIds = new Set();

    // Initialize DataTable for user list
    var userTable = initializeDataTable(
      "#user_list_data",
      [
        {
          data: null,
          render: function (data, type, row) {
            const isChecked = selectedUserIds.has(row.id.toString()) ? 'checked' : '';
            const isDisabled = $('#send_to').val() === 'all' ? 'disabled' : '';
            return `<input type="checkbox" class="select-checkbox" value="${row.id}" ${isChecked} ${isDisabled}>`;
          },
          orderable: false,
          searchable: false,
        },
        { data: "id" },
        { data: "name" },
        { data: "mobile" },
      ],
      "#select_all"
    );

    // Initialize DataTable for notification table
    initializeDataTable(
      "#notificationTable",
      [
        { data: "id" },
        {
          data: "title",
          name: "title",
          render: function (data) {
            return `<div class="tabler_text_wrap_css">${data}</div>`;
          }
        },
        { data: "send_to" },
        { data: "action", orderable: false, searchable: false },
      ],
      "#select_all_notification",
      "#delete_multiple"
    );

    // Handle dropdown change for send_to
    $('#send_to').on('change', function () {
      const sendTo = $(this).val();
      handleSendToChange(sendTo);
    });

    function handleSendToChange(sendTo) {
      if (sendTo === 'all') {
        // Clear individual selections and set to 'all'
        selectedUserIds.clear();
        $('#select_all').prop('checked', true).prop('disabled', true);
        $('#user_id').val('all');
        // Redraw table to show all checkboxes as checked and disabled
        userTable.draw(false);
      } else if (sendTo === 'selected') {
        // Enable checkboxes
        $('#select_all').prop('disabled', false);
        updateUserIds();
        updateSelectAllState();
        // Redraw table to show current selections
        userTable.draw(false);
      }
    }

    // Handle individual checkbox changes
    $(document).on('change', '#user_list_data .select-checkbox', function () {
      const userId = $(this).val();
      const isChecked = $(this).prop('checked');

      if ($('#send_to').val() === 'selected') {
        if (isChecked) {
          selectedUserIds.add(userId);
        } else {
          selectedUserIds.delete(userId);
        }
        updateUserIds();
        updateSelectAllState();
      }
    });

    // Handle select all checkbox
    $(document).on('change', '#select_all', function () {
      const isChecked = $(this).prop('checked');
      const sendTo = $('#send_to').val();

      if (sendTo === 'selected') {
        if (isChecked) {
          // Add all visible users to selection
          $('#user_list_data .select-checkbox').each(function () {
            const userId = $(this).val();
            selectedUserIds.add(userId);
            $(this).prop('checked', true);
          });
        } else {
          // Remove all visible users from selection
          $('#user_list_data .select-checkbox').each(function () {
            const userId = $(this).val();
            selectedUserIds.delete(userId);
            $(this).prop('checked', false);
          });
        }
        updateUserIds();
      }
    });

    // Function to update select all checkbox state based on visible rows
    function updateSelectAllState() {
      if ($('#send_to').val() !== 'selected') return;

      const visibleCheckboxes = $('#user_list_data .select-checkbox');
      const totalVisible = visibleCheckboxes.length;
      let checkedVisible = 0;

      visibleCheckboxes.each(function () {
        if (selectedUserIds.has($(this).val())) {
          checkedVisible++;
        }
      });

      if (checkedVisible === 0) {
        $('#select_all').prop('indeterminate', false).prop('checked', false);
      } else if (checkedVisible === totalVisible) {
        $('#select_all').prop('indeterminate', false).prop('checked', true);
      } else {
        $('#select_all').prop('indeterminate', true);
      }
    }

    // Function to update user_ids textarea
    function updateUserIds() {
      const sendTo = $('#send_to').val();

      if (sendTo === 'all') {
        $('#user_id').val('all');
      } else if (sendTo === 'selected') {
        const userIdsArray = Array.from(selectedUserIds);
        $('#user_id').val(userIdsArray.length > 0 ? JSON.stringify(userIdsArray) : '');
      }
      console.log('Updated user_id:', $('#user_id').val());
      console.log('Selected user IDs:', Array.from(selectedUserIds));
    }

    // Initialize on page load
    setTimeout(function () {
      $('#send_to').trigger('change');
    }, 1000);
  });

  function initializeDataTable(selector, columns, selectAllSelector, deleteMultipleSelector) {
    var table = $(selector).DataTable({
      processing: false, // Disable the processing indicator
      serverSide: true,
      order: [[1, "desc"]],
      ajax: {
        url: $(selector).data("url"),
      },
      columns: columns,
      language: current_locale === "en" ? englishLanguage : hindiLanguage,
      drawCallback: function () {
        // Update checkbox states after page change
        if (selector === "#user_list_data") {
          const sendTo = $('#send_to').val();

          if (sendTo === 'all') {
            // All users selected - check and disable all checkboxes
            $(selector + ' .select-checkbox').prop('checked', true).prop('disabled', true);
          } else if (sendTo === 'selected') {
            // Update checkboxes based on stored selections
            $(selector + ' .select-checkbox').each(function () {
              const userId = $(this).val();
              const isSelected = selectedUserIds.has(userId);
              $(this).prop('checked', isSelected).prop('disabled', false);
            });

            // Update select all state for current page
            updateSelectAllState();
          }
        }
      }
    });

    if (deleteMultipleSelector) {
      $(deleteMultipleSelector).on("click", function (e) {
        e.preventDefault();
        var selected = $(`${selector} .row-select:checked`)
          .map(function () {
            return $(this).val();
          })
          .get();

        if (selected.length === 0) {
          showErrorToast("Please select notifications first.");
          return;
        }

        $.ajax({
          url: $(this).attr("href"),
          type: "POST",
          data: { id: selected.join(",") },
          success: function (response) {
            $(selector).DataTable().ajax.reload();
            showSuccessToast(response.message);
          },
          error: function () {
            showErrorToast("An error occurred while deleting notifications.");
          },
        });
      });
    }

    return table;
  }
});
// <><><><><><><><><><><> END JS OF NOTIFICATION <><><><><><><><><><><>

// <><><><><><>  START JS FOR STORY DELETE  JS <><><><><><>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".delete-btn").forEach((button) => {
    button.addEventListener("click", function () {
      let storyId = this.getAttribute("data-id");
      Swal.fire({
        title: "Are you sure?",
        text: trans("You wont be able to revert this"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Delete",
        customClass: {
          popup: "dark:bg-black dark:text-white",
        },
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById("delete-form-" + storyId).submit();
        }
      });
    });
  });
});
// <><><><><><> END JS OF  STORY DELETE JS <><><><><><>

// <><><><><><>  START JS FOR LANGAUEGS JS <><><><><><>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".news-language-delete-form").forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      let news_language_id = this.getAttribute("data-id");

      // Show confirmation for deletion
      Swal.fire({
        title: "Are you sure?",
        text: "Are you sure you want to delete this news language?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        confirmButtonText: "Delete",
        customClass: { popup: "dark:bg-black dark:text-white" },
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/admin/news_languages/${news_language_id}`, {
            method: "DELETE",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              "Accept": "application/json",
            },
          })
            .then(res => res.json())
            .then(data => {
              if (data.status === 'error') {
                Swal.fire({
                  title: "Deleted!",
                  text: data.message,
                  icon: "info", // <-- use info icon instead of success
                  confirmButtonColor: "#3085d6",
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  allowEnterKey: false,
                  customClass: { popup: "dark:bg-black dark:text-white" },
                }).then(() => location.reload());
              }
              else {
                Swal.fire({
                  title: "Success!",
                  text: data.message,
                  icon: "success", // <-- use info icon instead of success
                  confirmButtonColor: "#3085d6",
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  allowEnterKey: false,
                  customClass: { popup: "dark:bg-black dark:text-white" },
                }).then(() => location.reload());
              }
            });
        }
      });
    });
  });
});

// Add Custom Ajax for Create News Language Form
$("#addNewsLanguageForm").on("submit", function (e) {
  e.preventDefault();
  let form = $(this);
  let formData = new FormData(this);

  $.ajax({
    url: form.attr("action"),
    type: form.attr("method"),
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      $("#addNewsLanguageModal").modal("hide");
      $("#addNewsLanguageForm")[0].reset();

      if (response.status === 'success') {
        showSuccessToast(response.message);
      } else if (response.status === 'error') {
        showErrorToast(response.message);
      }

      setTimeout(function () {
        location.reload();
      }, 2000);
    },
    error: function (response) {
      form.find(".parsley-required").remove(); // Remove old errors
      let errors = response.responseJSON.errors;
      for (let field in errors) {
        let errorMessage = errors[field][0];
        let input = form.find(`[name="${field}"]`);
        input.after(
          `<span class="parsley-required"><strong>${errorMessage}</strong></span>`
        );
      }
    },
  });
});

$(".edit_btn").on("click", function (e) {
  e.preventDefault();

  // Get data from button attributes
  let id = $(this).data('id');
  let name = $(this).data('name');
  let code = $(this).data('code');

  // Get current status from the toggle switch (real-time status)
  let currentStatus = $(this).closest('tr').find('.news-language-status-toggle').is(':checked') ? 'active' : 'inactive';

  // Fallback to data attribute if toggle doesn't exist or is disabled
  if ($(this).closest('tr').find('.news-language-status-toggle').length === 0) {
    currentStatus = $(this).data('status');
  }

  // Open the specific modal for this language
  $("#editNewsLanguageModal_" + id).modal("show");

  // Set form values
  $("#news_language_name_" + id).val(name);
  $("#news_language_code_" + id).val(code);

  // Set the status dropdown value with current status
  $("#editNewsLanguageModal_" + id).find('select[name="status"]').val(currentStatus);

  // Make sure the form has the correct action URL
  $("#editNewsLanguageForm_" + id).attr("action", "/admin/news_languages/" + id);
});

// Create News Languages : This js use for  after add image show in preview
document.addEventListener("DOMContentLoaded", function () {
  const imageInput = document.getElementById("news_languages_image");
  const imagePreview = document.getElementById("news_languages_image_preview");

  if (imageInput && imagePreview) {
    imageInput.addEventListener("change", function (event) {
      let reader = new FileReader();
      reader.onload = function (e) {
        imagePreview.src = e.target.result;
      };
      if (event.target.files.length > 0) {
        reader.readAsDataURL(event.target.files[0]);
      }
    });
  }
});

// Edit News Languages : This js use for  after add image show in preview
document.querySelectorAll('input[type="file"][id^="news_languages_image"]')
  .forEach((input) => {
    input.addEventListener("change", function (event) {
      let reader = new FileReader();
      let imgPreviewId =
        "news_languages_image_preview_" +
        this.closest(".modal").querySelector('input[name="id"]').value;
      reader.onload = function (e) {
        document
          .getElementById(imgPreviewId)
          .setAttribute("src", e.target.result);
      };
      if (event.target.files.length > 0) {
        reader.readAsDataURL(event.target.files[0]);
      }
    });
  });
// <><><><><><> END NEWS LANGAUEGS JS <><><><><><>

// <><><><><><> START NEWS LANGUAGE STATUS TOGGLE JS <><><><><><>

document.querySelectorAll(".news-language-status-toggle").forEach((toggle) => {
  toggle.addEventListener("change", function () {
    const newsLanguageId = this.getAttribute("data-id");
    const status = this.checked ? 'active' : 'inactive';

    // Show loading state
    const loadingSpinner = document.getElementById(`status-spinner-${newsLanguageId}`);
    if (loadingSpinner) loadingSpinner.classList.remove("d-none");

    // Send AJAX request to update status
    $.ajax({
      url: `/admin/news_languages/${newsLanguageId}/update-status`,
      type: "POST",
      success: (response) => {
        if (response.message) {
          showSuccessToast(response.message); // your toast function
        }
      },
      error: (xhr) => {
        console.error("Error:", xhr.responseText);
        // Revert the switch on error
        $toggle.prop("checked", !$toggle.prop("checked"));
        let errorMsg = "Failed to update status";
        if (xhr.responseJSON && xhr.responseJSON.error) {
          errorMsg = xhr.responseJSON.error;
        }
        showErrorToast(errorMsg); // optional toast for error
      },
      data: {
        status: status,
        _token: $('meta[name="csrf-token"]').attr('content')
      }

    });
  });
});
// <><><><><><> END NEWS LANGUAGE STATUS TOGGLE JS <><><><><><>

// <><><><><><> END NEWS LANGUAGE STATUS TOGGLE JS <><><><><><>

// <><><><><><> START EMAIL TEMPLATE DETAILS JS <><><><><><>
$(document).ready(function () {
  const emailTemplateTable = $("#EmailTemplate_list").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#EmailTemplate_list").data("url"),
      data: function (d) {
        d.template_status = $("#template_status").val();
      },
    },
    columns: [
      { data: "id", name: "id" },
      {
        data: "title",
        name: "title",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data}</div>`;
        }
      },
      {
        data: "slug",
        name: "slug",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data}</div>`;
        }
      },
      {
        data: "post_count",
        name: "post_count",
        className: "text-center"
      },
      {
        data: "layout_width",
        name: "layout_width",
        className: "text-center",
        render: function (data) {
          return data + " px";
        }
      },
      {
        data: "status",
        name: "status",
        render: (data, type, row) => `
                    <div class="form-check form-switch">
                        <input class="form-check-input switch-input template-switch-input-field" 
                               type="checkbox" 
                               data-id="${row.id}" 
                               ${data === "active" ? "checked" : ""}>
                    </div>`,
      },
      {
        data: "created_at",
        name: "created_at",
        render: function (data) {
          return new Date(data).toLocaleDateString();
        }
      },
      { data: "action", name: "action" },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });

  // Status filter change handler
  $("#template_status").on("change", () => emailTemplateTable.ajax.reload());

  // Status switch handler
  $(document).on("change", ".template-switch-input-field", function () {
    const id = $(this).data("id");
    const status = $(this).prop("checked") ? "active" : "inactive";
    const url = $("#template_status_url").val();

    $.ajax({
      type: "POST",
      url: url,
      data: {
        id: id,
        status: status,
        _token: $('meta[name="csrf-token"]').attr("content"),
      },
      success: (response) => {
        showSuccessToast(response.message);
      },
      error: (xhr) => {
        console.error("Error:", xhr.responseText);
      },
    });
  });


  function htmlDecode(html) {
    const txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
  }

  // Preview button click handler
  $("#EmailTemplate_list").on("click", ".preview_btn", function () {
    const row = emailTemplateTable.row($(this).closest("tr")).data();
    if (row) {
      // Create a new window/tab to show the email template preview
      const previewWindow = window.open('', '_blank', 'width=800,height=600');
      previewWindow.document.write(`
                <html>
                    <head>
                        <title>Email Template Preview - ${row.title}</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                margin: 20px; 
                                background-color: #f5f5f5; 
                            }
                            .preview-container {
                                max-width: ${row.layout_width}px;
                                margin: 0 auto;
                                background: white;
                                padding: 20px;
                                border-radius: 8px;
                                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            }
                            .preview-header {
                                border-bottom: 2px solid #eee;
                                padding-bottom: 10px;
                                margin-bottom: 20px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="preview-container">
                            <div class="preview-header">
                                <h2>${row.title}</h2>
                                <p><strong>Post Count:</strong> ${row.post_count} | <strong>Layout Width:</strong> ${row.layout_width}px</p>
                            </div>
                            <div class="preview-content">
                                ${row.html_content ? htmlDecode(row.html_content) : '<p class="text-muted">No content available</p>'}

                            </div>
                        </div>
                    </body>
                </html>
            `);
      previewWindow.document.close();
    }
  });
});
// <><><><><><> END EMAIL TEMPLATE DETAILS JS <><><><><><>

// <><><><><><> START CUSTOM ADS DETAILS JS <><><><><><>
$(document).ready(function () {
  const customAdsTable = $("#Custom_ads_list").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    responsive: false,
    columnDefs: [
      {
        targets: [5, 6],
        render: function (data, type, row) {
          if (type === 'display') {
            return data;
          }
          return data ? data.replace(/<[^>]*>/g, '').replace(/Payment:\s*/, '') : '';
        }
      }
    ],
    ajax: {
      url: $("#Custom_ads_list").data("url"),
      type: "GET",
      error: function (xhr, error, code) {

        // Show user-friendly error message
        $('#Custom_ads_list_processing').hide();
        $('#Custom_ads_list tbody').html(`
                    <tr>
                        <td colspan="10" class="text-center text-danger">
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Error Loading Data!</h4>
                                <p>Status: ${xhr.status} - ${xhr.statusText}</p>
                                <p class="mb-0">Please check the console for more details.</p>
                                <button class="btn btn-outline-danger mt-2" onclick="location.reload()">
                                    <i class="fas fa-refresh"></i> Reload Page
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
      },
      beforeSend: function (xhr) {
        // Add CSRF token if needed
        const token = $('meta[name="csrf-token"]').attr('content');
        if (token) {
          xhr.setRequestHeader('X-CSRF-TOKEN', token);
        }
      }
    },
    columns: [
      {
        data: "id",
        name: "id",
        render: function (data) {
          return `<span class="fw-bold">${data || ''}</span>`;
        }
      },

      {
        data: "user",
        name: "contact_name",
        render: function (data, type, row) {
          return `<div class="text-wrap">${data || row.contact_name || 'N/A'}</div>`;
        }
      },
      {
        data: "title",
        name: "title",
        render: function (data) {
          return `<div class="text-wrap fw-semibold">${data || 'No Title'}</div>`;
        }
      },
      {
        data: "ad_type",
        name: "ad_type",
        render: function (data) {
          return data ? `<span class="badge">${data}</span>` : '<span class="text-white">-</span>';
        }
      },
      {
        data: "vertical_image",
        name: "vertical_image",
        render: function (data) {
          if (data && data !== '') {
            return `<img src="${data}" alt="Ad vertical_image" style="width: 50px; height: 50px; object-fit: cover;" class="rounded shadow-sm">`;
          }
          return '<span class="text-white">No vertical_image</span>';
        }
      },
      {
        data: "horizontal_image",
        name: "horizontal_image",
        render: function (data) {
          if (data && data !== '') {
            return `<img src="${data}" alt="Ad horizontal_image" style="width: 50px; height: 50px; object-fit: cover;" class="rounded shadow-sm">`;
          }
          return '<span class="text-white">No horizontal_image</span>';
        }
      },
      {
        data: "ad_publish_status",
        name: "ad_publish_status",
        render: function (data) {
          if (!data) return '<span class="badge bg-secondary text-white">-</span>';

          const statusLower = data.toLowerCase();
          let badgeClass = 'bg-secondary text-white';

          switch (statusLower) {
            case 'approved':
              badgeClass = 'bg-success text-white';
              break;
            case 'pending':
              badgeClass = 'bg-warning text-dark';
              break;
            case 'rejected':
              badgeClass = 'bg-danger text-white';
              break;
          }

          return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
        }
      },
      {
        data: "payment_status",
        name: "payment_status",
        render: function (data) {
          if (!data) return '<span class="badge bg-secondary text-white">-</span>';

          const statusLower = data.toLowerCase();
          let badgeClass = 'bg-secondary text-white';

          switch (statusLower) {
            case 'success':
              badgeClass = 'bg-success text-white';
              break;
            case 'pending':
              badgeClass = 'bg-warning text-dark';
              break;
            case 'failed':
              badgeClass = 'bg-danger text-white';
              break;
          }

          return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
        }
      },
      {
        data: "pricing",
        name: "total_price",
        render: function (data, type, row) {
          // Handle both direct pricing data and row pricing
          if (data) return data;

          const totalPrice = row.total_price || 0;
          const dailyPrice = row.daily_price || 0;
          const totalDays = row.total_days || 0;

          if (totalPrice > 0) {
            return `<strong>${parseFloat(totalPrice).toFixed(2)}</strong><br><small>${parseFloat(dailyPrice).toFixed(2)}/day × ${totalDays} days</small>`;
          }
          return '<span class="text-muted">$0.00</span>';
        }
      },
      {
        data: "created_at",
        name: "created_at",
        render: function (data) {
          return data ? new Date(data).toLocaleDateString() : '-';
        }
      },
      {
        data: null,
        name: "action",
        render: function (data, type, row) {
          // Get permissions from div
          var permissionsDiv = $('#custom-ads-permissions');
          var canView = permissionsDiv.data('view-details') == 1;
          var canChange = permissionsDiv.data('change-status') == 1;

          let html = `<div class="d-flex gap-1">`;

          // View Details button
          if (canView) {
            html += `
        <button class="btn bg btn-sm border-none preview-btn d-inline-flex align-items-center justify-content-center previewModalcss me-1" 
                data-id="${row.id}" 
                data-bs-toggle="modal" 
                data-bs-target="#previewModal"
                title="View Details">
            <i class="fas fa-eye"></i>
        </button>`;
          } else {
            html += `<span class='badge bg-primary text-white m-1'>No permission for View.</span>`;
          }

          // Change Status dropdown
          if (canChange) {
            html += `
        <div class="dropdown ms-1">
            <button class="btn btn-sm dropdown-toggle d-inline-flex align-items-center justify-content-center previewModalcss" 
                    type="button" 
                    data-bs-toggle="dropdown"
                    title="Change Status">
                <i class="fas fa-cog"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <button class="dropdown-item status-action ${(row.ad_publish_status || '').toLowerCase() === 'pending' ? 'text-warning' : ''}" data-id="${row.id}" data-status="pending">
                        <i class="fas fa-clock text-warning m-2"></i> Pending
                    </button>
                </li>
                <li>
                    <button class="dropdown-item status-action ${(row.ad_publish_status || '').toLowerCase() === 'approved' ? 'text-success' : ''}" data-id="${row.id}" data-status="approved">
                        <i class="fas fa-check text-success m-2"></i> Approved
                    </button>
                </li>
                <li>
                    <button class="dropdown-item status-action ${(row.ad_publish_status || '').toLowerCase() === 'rejected' ? 'text-danger' : ''}" data-id="${row.id}" data-status="rejected">
                        <i class="fas fa-times text-danger m-2"></i> Rejected
                    </button>
                </li>
            </ul>
        </div>`;
          } else {
            html += `<span class='badge bg-primary text-white m-1'>No permission for change status.</span>`;
          }

          html += `</div>`;
          return html;
        }
      }

    ],
    drawCallback: function (settings) {
      // Initialize tooltips after table draw
      $('[title]').tooltip();
    },
  });

  // Helper function to get status badge (keeping for modal use)
  function getStatusBadge(status) {
    if (!status) return '';

    const statusLower = status.toLowerCase();
    let badgeClass = 'bg-secondary text-white';

    switch (statusLower) {
      case 'approved':
        badgeClass = 'bg-success text-white';
        break;
      case 'pending':
        badgeClass = 'bg-secondary text-white';
        break;
      case 'rejected':
        badgeClass = 'bg-danger text-white';
        break;
    }

    return `<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
  }

  // Preview button click handler
  $("#Custom_ads_list").on("click", ".preview-btn", function () {
    const row = customAdsTable.row($(this).closest("tr")).data();
    if (row) {
      updateModalData(row);
    }
  });

  // Status change handler from table dropdown
  $("#Custom_ads_list").on("click", ".status-action", function (e) {
    e.preventDefault();
    const adId = $(this).data('id');
    const newStatus = $(this).data('status');
    const row = customAdsTable.row($(this).closest("tr")).data();

    if (!adId || !row) {
      showToast('Error: Ad ID or row data not found', 'error');
      return;
    }

    // Check if the ad is already approved and payment is success - cannot change status
    if (row.ad_publish_status.toLowerCase() === 'approved' && row.payment_status.toLowerCase() === 'success') {
      Swal.fire({
        title: 'Cannot Change Status',
        text: 'This ad is already approved and payment is successful. The publish status cannot be changed.',
        icon: 'info',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Check if the ad is already rejected - cannot change status
    if (row.ad_publish_status.toLowerCase() === 'rejected') {
      Swal.fire({
        title: 'Cannot Change Status',
        text: 'This ad has been rejected and the status cannot be changed.',
        icon: 'info',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
      });
      return;
    }

    if (row.ad_publish_status.toLowerCase() === 'approved') {
      Swal.fire({
        title: 'Cannot Change Status',
        text: 'This ad has been approved and the status cannot be changed.',
        icon: 'info',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Check approval limit only when trying to approve (and it's not already approved)
    if (newStatus === 'approved' && row.ad_publish_status.toLowerCase() !== 'approved' && window.currentApproved >= window.approvalLimit) {
      Swal.fire({
        title: 'Approval Limit Reached',
        text: 'Your approval limit is over. You cannot approve more ads.',
        icon: 'info',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
      });
      return;
    }

    updateAdStatus(adId, newStatus, row);
  });

  // Function to update modal data
  function updateModalData(row) {
    try {
      // Basic Information
      $('#modal-id').text(row.id || '-');
      $('#modal-user_id').text(row.user_id || '-');
      $('#modal-title').text(row.title || 'No Title');
      $('#modal-description').text(row.description || 'No Description');
      $('#modal-slug').text(row.slug || '-');
      $('#modal-ad_type').text(row.ad_type || '-');
      $('#modal-url').text(row.url || '-');

      // Status badges
      const currentStatus = row.ad_publish_status || 'pending';
      const paymentStatus = row.payment_status || 'pending';

      $('#modal-ad_publish_status')
        .text(currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1))
        .removeClass()
        .addClass(`badge ${getStatusBadgeClass(currentStatus)}`);

      $('#modal-payment_status')
        .text(paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1))
        .removeClass()
        .addClass(`badge ${getPaymentStatusBadgeClass(paymentStatus)}`);

      // Update status buttons with current ad ID and check if status can be changed
      $('.status-btn').each(function () {
        $(this).data('id', row.id);

        // Disable status buttons if ad is approved with successful payment or rejected
        const isApprovedWithSuccessPayment = row.ad_publish_status.toLowerCase() === 'approved' && row.payment_status.toLowerCase() === 'success';
        const isRejected = row.ad_publish_status.toLowerCase() === 'rejected';

        if (isApprovedWithSuccessPayment || isRejected) {
          $(this).prop('disabled', true).addClass('disabled');
        } else {
          $(this).prop('disabled', false).removeClass('disabled');
        }
      });

      // Image handling
      if (row.vertical_image && row.vertical_image !== '') {
        $('#modal_vertical_image').html(`<img src="${row.vertical_image}" class="rounded align-center" style="height: 100%;" alt="Ad Image">`);
      } else {
        $('#modal_vertical_image').html(`
                    <div class="text-center">
                        <i class="fas fa-image fa-3x text-muted mb-2"></i>
                        <p class="text-muted">No image available</p>
                    </div>
                `);
      }
      if (row.horizontal_image && row.horizontal_image !== '') {
        $('#modal_horizontal_image').html(`<img src="${row.horizontal_image}" class="rounded align-center" style="height: 100%;" alt="Ad Image">`);
      } else {
        $('#modal_horizontal_image').html(`
                    <div class="text-center">
                        <i class="fas fa-image fa-3x text-muted mb-2"></i>
                        <p class="text-muted">No image available</p>
                    </div>
                `);
      }

      // Pricing Information
      $('#modal-total_price').text(row.total_price ? + parseFloat(row.total_price).toFixed(2) : '$0.00');
      $('#modal-daily_price').text(row.daily_price ? + parseFloat(row.daily_price).toFixed(2) : '$0.00');
      $('#modal-total_days').text(row.total_days || '0');

      // Handle price_summary
      if ($('#modal-price_summary').length) {
        if (row.price_summary) {
          if (Array.isArray(row.price_summary)) {
            const summaryText = row.price_summary.map(item => {
              if (typeof item === 'object' && item !== null) {
                return `${item.placement || 'Placement'} (${item.type || 'Type'}: ${item.display_name || 'Name'}) - ${item.daily_price || '0.00'}`;
              }
              return item.toString();
            }).join('<br>');
            $('#modal-price_summary').html(summaryText);
          } else {
            $('#modal-price_summary').text(row.price_summary.toString());
          }
        } else {
          $('#modal-price_summary').text('-');
        }
      }

      // Contact Information
      $('#modal-contact_name').text(row.contact_name || '-');
      $('#modal-contact_email').text(row.contact_email || '-');
      $('#modal-contact_phone').text(row.contact_phone || '-');

      // Placement Information
      if ($('#modal-web_ads_placement').length) {
        const webPlacement = Array.isArray(row.web_ads_placement) ?
          row.web_ads_placement.join(', ') :
          (row.web_ads_placement || '-');
        $('#modal-web_ads_placement').text(webPlacement);
      }

      if ($('#modal-app_ads_placement').length) {
        const appPlacement = Array.isArray(row.app_ads_placement) ?
          row.app_ads_placement.join(', ') :
          (row.app_ads_placement || '-');
        $('#modal-app_ads_placement').text(appPlacement);
      }


      // Payment Information
      if ($('#modal-payment_gateway').length) {
        $('#modal-payment_gateway').text(row.payment_gateway || '-');
      }
      if ($('#modal-transaction_id').length) {
        $('#modal-transaction_id').text(row.transaction_id || '-');
      }

      // Date Information
      $('#modal-start_date').text(row.start_date ? formatDate(row.start_date) : '-');
      $('#modal-end_date').text(row.end_date ? formatDate(row.end_date) : '-');
      $('#modal-created_at').text(row.created_at ? formatDate(row.created_at) : '-');
      if ($('#modal-updated_at').length) {
        $('#modal-updated_at').text(row.updated_at ? formatDate(row.updated_at) : '-');
      }

    } catch (error) {
      console.error('Error updating modal data:', error);
      showToast('Error loading ad details', 'error');
    }
  }

  // Function to format dates
  function formatDate(dateString) {
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
      return dateString;
    }
  }

  // Function to get status badge class (updated to match backend)
  function getStatusBadgeClass(status) {
    switch ((status || '').toLowerCase()) {
      case 'approved': return 'bg-success text-white';
      case 'pending': return 'bg-secondary text-white';
      case 'rejected': return 'bg-danger text-white';
      default: return 'bg-secondary text-white';
    }
  }

  // Function to get payment status badge class (updated to match backend)
  function getPaymentStatusBadgeClass(status) {
    switch ((status || '').toLowerCase()) {
      case 'success': return 'bg-success text-white';
      case 'pending': return 'bg-secondary text-white';
      case 'failed': return 'bg-danger';
      default: return 'bg-secondary text-white';
    }
  }

  // Function to update ad status
  function updateAdStatus(adId, status, row) {
    // Helper function to send AJAX request
    function sendStatusUpdate() {
      $.ajax({
        url: `/admin/custom-ads-requests/${adId}/update-status`,
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          status: status
        },
        success: function (response) {
          console.log('Status update response:', response);

          if (response.success) {
            // Update local currentApproved count
            const oldStatus = row.ad_publish_status.toLowerCase();
            const newStat = status.toLowerCase();
            if (newStat === 'approved' && oldStatus !== 'approved') {
              window.currentApproved++;
            } else if (newStat !== 'approved' && oldStatus === 'approved') {
              window.currentApproved--;
            }

            // Update modal if open
            if ($('#previewModal').hasClass('show')) {
              $('#modal-ad_publish_status')
                .text(status.charAt(0).toUpperCase() + status.slice(1))
                .removeClass()
                .addClass(`badge ${getStatusBadgeClass(status)}`);
            }

            // Reload DataTable
            customAdsTable.ajax.reload(null, false);

            showToast(`Status updated to ${status.charAt(0).toUpperCase() + status.slice(1)} successfully`, 'success');
          } else {
            showToast(response.message || 'Failed to update status', 'error');
          }
        },
        error: function (xhr) {
          console.error('Status update error:', xhr);

          let errorMessage = 'Failed to update status';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
          } else if (xhr.status === 404) {
            errorMessage = 'Ad request not found';
          } else if (xhr.status === 403) {
            errorMessage = 'You do not have permission to perform this action';
          } else if (xhr.status === 500) {
            errorMessage = 'Server error occurred. Please try again later.';
          }
          showToast(errorMessage, 'error');
        },
        complete: function () {
          $('.status-btn, .status-action').prop('disabled', false);
        }
      });
    }

    if (status === 'rejected') {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to reject this ad. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, reject it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          sendStatusUpdate();
        }
      });
    } else if (status === 'approved') {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to approve this ad.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          sendStatusUpdate();
        }
      });
    } else if (status === 'pending') {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to set this ad to pending.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, set to pending!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          sendStatusUpdate();
        }
      });
    } else {
      // For other statuses (though only these three exist)
      sendStatusUpdate();
    }
  }

  // Function to show toast messages
  function showToast(message, type = 'info') {
    // Remove existing toast
    $('#statusToast').remove();

    // Create new toast
    const toastHtml = `
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div id="statusToast" class="toast ${getToastClass(type)}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;

    $('body').append(toastHtml);

    // Show toast
    const toast = new bootstrap.Toast($('#statusToast')[0], {
      autohide: type !== 'info',
      delay: type === 'success' ? 3000 : 5000
    });
    toast.show();

    // Auto-remove toast element after it's hidden
    $('#statusToast').on('hidden.bs.toast', function () {
      $(this).parent().remove();
    });
  }

  function getToastClass(type) {
    switch (type) {
      case 'success': return 'text-bg-success';
      case 'error': return 'text-bg-danger';
      case 'warning': return 'text-bg-warning';
      default: return 'text-bg-info';
    }
  }

  function getToastIcon(type) {
    switch (type) {
      case 'success': return 'fa-check-circle';
      case 'error': return 'fa-exclamation-circle';
      case 'warning': return 'fa-exclamation-triangle';
      default: return 'fa-info-circle';
    }
  }

  function getToastTitle(type) {
    switch (type) {
      case 'success': return 'Success';
      case 'error': return 'Error';
      case 'warning': return 'Warning';
      default: return 'Information';
    }
  }

  // Make functions globally available
  window.updateModalData = updateModalData;
  window.updateAdStatus = updateAdStatus;
  window.showToast = showToast;
});
// <><><><><><> END CUSTOM ADS DETAILS JS <><><><><><>

document.addEventListener('DOMContentLoaded', function () {
  // Initialize TinyMCE
  tinymce.init({
    selector: '#tinymce_editor',
    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    setup: function (editor) {
      editor.on('init', function () {
        console.log('TinyMCE initialized');
        updatePreview(); // Update preview once TinyMCE is ready
      });
      editor.on('input change keyup paste', function () {
        debounceUpdatePreview();
      });
    }
  });

  let logoCropper = null;
  let imageCropper = null;
  let croppedLogoBlob = null;
  let croppedImageBlob = null;

  // Debounce function to improve performance
  let debounceTimeout;
  function debounceUpdatePreview() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(updatePreview, 300); // Delay of 300ms
  }

  // Real-time preview updates
  function updatePreview() {
    // Get form field values
    const title = document.getElementById('title')?.value || 'No title';
    const type = document.getElementById('type')?.value || 'sponsor';
    const subject = document.getElementById('subject')?.value || 'No subject';
    const layoutWidth = document.getElementById('layout_width')?.value || 600;
    const status = document.getElementById('status')?.value || 'active';
    const closing = document.getElementById('closing')?.value || '';
    const signature = document.getElementById('signature')?.value || '';
    const footerText = document.getElementById('footer_text')?.value || '';


    // Update preview elements
    const previewTitle = document.getElementById('preview-title');
    const previewSubject = document.getElementById('preview-subject');
    const previewWidth = document.getElementById('preview-width');
    const previewContent = document.getElementById('preview-content');
    const emailPreview = document.getElementById('email-preview');

    if (previewTitle) previewTitle.textContent = title;
    if (previewSubject) previewSubject.textContent = subject;
    if (previewWidth) previewWidth.textContent = `${layoutWidth}px`;

    // Update TinyMCE content
    const editor = tinymce.get('tinymce_editor');
    if (previewContent) {
      if (editor && editor.getContent) {
        const content = editor.getContent();
        previewContent.innerHTML = content || '<p class="text-muted" style="margin: 0; color: #5f6368;">Content will appear here...</p>';
      } else {
        const textarea = document.getElementById('tinymce_editor');
        previewContent.innerHTML = textarea?.value || '<p class="text-muted" style="margin: 0; color: #5f6368;">Content will appear here...</p>';
      }

      // Add closing, signature, and footer to preview
      const previewClosing = document.createElement('div');
      previewClosing.className = 'mt-3';
      previewClosing.innerHTML = closing ? `<p>${closing}</p>` : '';

      const previewSignature = document.createElement('div');
      previewSignature.className = 'mt-2';
      previewSignature.innerHTML = signature ? `<p>${signature.replace(/\n/g, '<br>')}</p>` : '';

      const previewFooter = document.createElement('div');
      previewFooter.className = 'mt-2 text-muted';
      previewFooter.style.fontSize = '12px';
      previewFooter.innerHTML = footerText ? `<p>${footerText.replace(/\n/g, '<br>')}</p>` : '';

      // Clear existing closing, signature, and footer
      const existingClosing = previewContent.querySelector('.closing');
      const existingSignature = previewContent.querySelector('.signature');
      const existingFooter = previewContent.querySelector('.footer');
      if (existingClosing) existingClosing.remove();
      if (existingSignature) existingSignature.remove();
      if (existingFooter) existingFooter.remove();

      // Append new closing, signature, and footer
      previewClosing.classList.add('closing');
      previewSignature.classList.add('signature');
      previewFooter.classList.add('footer');
      previewContent.appendChild(previewClosing);
      previewContent.appendChild(previewSignature);
      previewContent.appendChild(previewFooter);

      // Update email preview container width for mobile/desktop
      const isMobile = emailPreview.classList.contains('mobile-preview');
      emailPreview.style.maxWidth = isMobile ? '360px' : `${layoutWidth}px`;

      // Inline CSS for Gmail compatibility
      const elements = previewContent.querySelectorAll('*');
      elements.forEach(el => {
        if (el.style.position === 'absolute' || el.style.position === 'fixed') {
          el.style.position = 'static';
        }
      });
    }
  }

  // Toggle mobile/desktop preview
  const togglePreviewButton = document.getElementById('toggle-preview-mode');
  const toggleRefreshButton = document.getElementById('toggle-refresh-mode');
  if (togglePreviewButton) {
    togglePreviewButton.addEventListener('click', function () {
      const emailPreview = document.getElementById('email-preview');
      const isMobile = emailPreview.classList.toggle('mobile-preview');
      togglePreviewButton.textContent = isMobile ? 'Switch to Desktop View' : 'Switch to Mobile View';
      updatePreview();
    });
  }
  if (toggleRefreshButton) {
    toggleRefreshButton.addEventListener('click', function () {
      updatePreview();
    });
  }

  // Bind input events for all fields
  const fields = ['title', 'type', 'subject', 'layout_width', 'status', 'closing', 'signature', 'footer_text'];
  fields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', debounceUpdatePreview);
      field.addEventListener('change', debounceUpdatePreview);
      field.addEventListener('keyup', debounceUpdatePreview);
    }
  });

  // Logo Cropper
  const logoInput = document.getElementById('logo');
  const logoPreview = document.getElementById('preview-logo-img');
  const logoCropperContainer = document.getElementById('logo-cropper');
  const logoPreviewContainer = document.getElementById('preview-logo');
  if (logoInput) {
    logoInput.addEventListener('change', function (e) {
      const files = e.target.files;
      if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (event) {
          const logoPreviewImg = document.getElementById('logo-preview');
          logoPreviewImg.src = event.target.result;
          logoCropperContainer.style.display = 'block';
          if (logoCropper) logoCropper.destroy();
          logoCropper = new Cropper(logoPreviewImg, {
            aspectRatio: 3 / 1,
            viewMode: 1,
          });
        };
        reader.readAsDataURL(files[0]);
      }
    });
  }
  document.getElementById('crop-logo')?.addEventListener('click', function () {
    if (logoCropper) {
      const canvas = logoCropper.getCroppedCanvas();
      canvas.toBlob(function (blob) {
        croppedLogoBlob = blob;
        const url = URL.createObjectURL(blob);
        logoPreview.src = url;
        logoPreviewContainer.style.display = 'block';
        logoCropperContainer.style.display = 'none';
        logoCropper.destroy();
        logoCropper = null;
        debounceUpdatePreview();
      });
    }
  });
  document.getElementById('cancel-logo')?.addEventListener('click', function () {
    logoCropperContainer.style.display = 'none';
    if (logoCropper) {
      logoCropper.destroy();
      logoCropper = null;
    }
    logoInput.value = '';
    logoPreviewContainer.style.display = 'none';
    debounceUpdatePreview();
  });

  // Extra Image Cropper
  const imageInput = document.getElementById('image');
  const imagePreview = document.getElementById('preview-extra-img');
  const imageCropperContainer = document.getElementById('image-cropper');
  const imagePreviewContainer = document.getElementById('preview-extra-image');
  if (imageInput) {
    imageInput.addEventListener('change', function (e) {
      const files = e.target.files;
      if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (event) {
          const imagePreviewImg = document.getElementById('image-preview');
          imagePreviewImg.src = event.target.result;
          imageCropperContainer.style.display = 'block';
          if (imageCropper) imageCropper.destroy();
          imageCropper = new Cropper(imagePreviewImg, {
            aspectRatio: 1,
            viewMode: 1,
          });
        };
        reader.readAsDataURL(files[0]);
      }
    });
  }
  document.getElementById('crop-image')?.addEventListener('click', function () {
    if (imageCropper) {
      const canvas = imageCropper.getCroppedCanvas();
      canvas.toBlob(function (blob) {
        croppedImageBlob = blob;
        const url = URL.createObjectURL(blob);
        imagePreview.src = url;
        imagePreviewContainer.style.display = 'block';
        imageCropperContainer.style.display = 'none';
        imageCropper.destroy();
        imageCropper = null;
        debounceUpdatePreview();
      });
    }
  });
  document.getElementById('cancel-image')?.addEventListener('click', function () {
    imageCropperContainer.style.display = 'none';
    if (imageCropper) {
      imageCropper.destroy();
      imageCropper = null;
    }
    imageInput.value = '';
    imagePreviewContainer.style.display = 'none';
    debounceUpdatePreview();
  });

  // Initial preview update
  setTimeout(debounceUpdatePreview, 500);
});

// <><><><><><> START REPORT REASON TYPES JS <><><><><><>
$(document).ready(function () {
  // ===== DataTable =====
  const reportReasonTypeTable = $("#reportReasonTypeTable").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#reportReasonTypeTable").data("url"),
    },
    columns: [
      { data: "id", name: "id" },
      { data: "title", name: "title" },
      { data: "created_at", name: "created_at" },
      { data: "updated_at", name: "updated_at" },
      { data: "action", name: "action", orderable: false, searchable: false },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });

  // ===== Delete Button =====
  $("#reportReasonTypeTable").on("click", ".delete_report_btn", function () {
    const id = $(this).data("id");
    Swal.fire({
      title: "Are you sure?",
      text: "You have delete this report reason type.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Delete",
      allowOutsideClick: false,
      allowEnterKey: false,
      allowEscapeKey: false,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/admin/report-comments/reason-type/" + id, // ✅ Updated
          type: "DELETE",
          headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
          success: function (response) {
            Swal.fire("Deleted!", response.message, "success"); // ✅ use response.message
            reportReasonTypeTable.ajax.reload(null, false);
          },
          error: function (xhr) {
            let msg = "Something went wrong.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
              msg = xhr.responseJSON.message;
            }
            Swal.fire("Error!", msg, "error");
          },
        });
      }
    });
  });

  // ===== Add / Remove Dynamic Rows =====
  $("#report-types-container").on("click", ".add-report-type", function () {
    let firstRow = $(".report-type-row:first").clone(true);
    let newIndex = $(".report-type-row").length;
    firstRow.find("input").val("").attr("id", "title-" + newIndex);
    firstRow.find("label").attr("for", "title-" + newIndex);
    // Update button for remove
    let newButton = firstRow.find("button");
    newButton.removeClass("add-report-type btn-success").addClass("remove-report-type btn-danger");
    newButton.html('<i class="fas fa-times"></i>');
    $("#report-types-container").append(firstRow);
  });

  $("#report-types-container").on("click", ".remove-report-type", function () {
    if ($(".report-type-row").length > 1) {
      $(this).closest(".report-type-row").remove();
    }
  });

  // ===== Form Submit =====
  $("#addReportTypeForm").on("submit", function (e) {
    e.preventDefault();
    let form = $(this);
    let formData = new FormData(this);
    $("[id$='-error']").text("");

    $.ajax({
      url: form.attr("action"),
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
      success: function (response) {
        if (response.status === true) {
          showSuccessToast(response.message);
          setTimeout(() => { window.location.href = response.redirect; }, 2000);
        } else {
          showErrorToast(response.message || "Something went wrong.");
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          $.each(errors, function (key, value) {
            let fieldId = key.replace(/\./g, '-');
            $("#" + fieldId + "-error").text(value[0]);
          });
        } else {
          showErrorToast("Unexpected error occurred.");
        }
      }
    });
  });
});
// <><><><><><> END REPORT REASON TYPES JS <><><><><><>

// <><><><><><> START EMAIL TEMPLATE DETAILS JS <><><><><><>
$(document).ready(function () {
  const emailTemplateTable = $("#EmailTemplateSponsor_list").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    scrollX: true, // Enable horizontal scrolling for many columns
    ajax: {
      url: $("#EmailTemplateSponsor_list").data("url"),
      data: function (d) {
        d.template_status = $("#sponsortemplate_status").val();
      },
    },
    columns: [
      { data: "id", name: "id" },
      {
        data: "title",
        name: "title",
        width: "150px",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data || ''}</div>`;
        }
      },
      {
        data: "slug",
        name: "slug",
        width: "120px",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data || ''}</div>`;
        }
      },
      {
        data: "subject",
        name: "subject",
        width: "150px",
        render: function (data) {
          return `<div class="tabler_text_wrap_css" title="${data || ''}">${data ? (data.length > 30 ? data.substring(0, 30) + '...' : data) : ''}</div>`;
        }
      },
      {
        data: "type",
        name: "type",
        width: "100px",
        className: "text-center",
        render: function (data) {
          return data ? `<span class="badge bg-primary text-white">${data}</span>` : '';
        }
      },
      {
        data: "layout_width",
        name: "layout_width",
        width: "100px",
        className: "text-center",
        render: function (data) {
          return data ? data + " px" : '';
        }
      },
      {
        data: "logo",
        name: "logo",
        width: "80px",
        className: "text-center",
        render: function (data) {
          if (data && data.trim() !== '') {
            return `
        <img src="/storage/${data}" 
             alt="Logo" 
             class="rounded mx-auto d-block max-h-10" 
             onerror="this.style.display='none'">
      `;
          }
          return `
            <img src="/public/front_end/classic/images/default/post-placeholder.jpg" 
                 alt="No Image" 
                 class="rounded mx-auto d-block max-h-10">
        `;
        }
      },
      {
        data: "image",
        name: "image",
        width: "80px",
        className: "text-center",
        render: function (data) {
          if (data && data.trim() !== '') {
            return `
                <img src="/storage/${data}" 
                     alt="Image" 
                     class="rounded mx-auto d-block max-h-10" 
                     onerror="this.style.display='none'">
            `;
          }
          return `
            <img src="/public/front_end/classic/images/default/post-placeholder.jpg" 
                 alt="No Image" 
                 class="rounded mx-auto d-block max-h-10">
        `;
        }
      },

      {
        data: "closing",
        name: "closing",
        width: "120px",
        render: function (data) {
          return `<div class="tabler_text_wrap_css" title="${data || ''}">${data ? (data.length > 20 ? data.substring(0, 20) + '...' : data) : ''}</div>`;
        }
      },
      {
        data: "signature",
        name: "signature",
        width: "120px",
        render: function (data) {
          if (data && data.trim() !== '') {
            // Remove HTML tags for display in table
            const textOnly = data.replace(/<[^>]*>/g, '');
            return `<div class="tabler_text_wrap_css" title="${textOnly}">${textOnly.length > 20 ? textOnly.substring(0, 20) + '...' : textOnly}</div>`;
          }
          return '<span class="text-muted">No Signature</span>';
        }
      },
      {
        data: "footer_text",
        name: "footer_text",
        width: "120px",
        render: function (data) {
          if (data && data.trim() !== '') {
            // Remove HTML tags for display in table
            const textOnly = data.replace(/<[^>]*>/g, '');
            return `<div class="tabler_text_wrap_css" title="${textOnly}">${textOnly.length > 20 ? textOnly.substring(0, 20) + '...' : textOnly}</div>`;
          }
          return '<span class="text-muted">No Footer</span>';
        }
      },
      {
        data: "status",
        name: "status",
        width: "80px",
        className: "text-center",
        render: (data, type, row) => {
          const permissions = $("#sponsor-email-template-permissions").data();
          const canUpdateStatus = permissions.updateStatus === 1 || permissions.updateStatus === "1";

          if (canUpdateStatus) {
            return `
        <div class="form-check form-switch">
          <input class="form-check-input switch-input template-switch-input" 
                 type="checkbox" 
                 data-id="${row.id}" 
                 ${data === "active" ? "checked" : ""}>
        </div>`;
          } else {
            return `<span class="badge bg-primary text-white m-1">No permission For change status.</span>`;
          }
        }
      },
      {
        data: "created_at",
        name: "created_at",
        width: "100px",
        render: function (data) {
          return new Date(data).toLocaleDateString();
        }
      },
      {
        data: "action",
        name: "action",
        width: "100px",
        orderable: false,
        searchable: false
      },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
    columnDefs: [
      {
        targets: '_all',
        className: 'align-middle'
      }
    ]
  });

  // Status filter change handler
  $("#sponsortemplate_status").on("change", () => emailTemplateTable.ajax.reload());

  // Status switch handler
  $(document).on("change", ".template-switch-input", function () {
    const id = $(this).data("id");
    const status = $(this).prop("checked") ? "active" : "inactive";
    const url = $("#sponsortemplate_status_url").val();
    $.ajax({
      type: "POST",
      url: url,
      data: {
        id: id,
        status: status,
        _token: $('meta[name="csrf-token"]').attr("content"),
      },
      success: (response) => {
        showSuccessToast(response.message);
      },
      error: (xhr) => {
        console.error("Error:", xhr.responseText);
        // Revert the switch on error
        $(this).prop("checked", !$(this).prop("checked"));
      },
    });
  });
  function decodeHtml(html) {
    const txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
  }

  // Preview button click handler
  $("#EmailTemplateSponsor_list").on("click", ".preview_btn", function () {
    const row = emailTemplateTable.row($(this).closest("tr")).data();
    if (row) {
      const previewWindow = window.open('', '_blank', 'width=900,height=700');
      previewWindow.document.write(`
      <html>
        <head>
          <title>Email Template Preview - ${row.title || 'Untitled'}</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
          <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 p-4">
          <div class="container mx-auto">
            <div class="bg-white rounded p-6 max-w-${row.layout_width || 600}px mx-auto">
              <div class="border-b pb-4 mb-4">
                <h2 class="text-xl font-bold">${row.title || 'Untitled Template'}</h2>
                <div class="row text-sm text-gray-600 mt-3">
                  <div class="col-md-4"><strong>Subject:</strong> ${row.subject || 'No subject'}</div>
                  <div class="col-md-4"><strong>Type:</strong> ${row.type || 'Not specified'}</div>
                  <div class="col-md-4"><strong>Post Count:</strong> ${row.post_count || 0}</div>
                  <div class="col-md-4"><strong>Layout Width:</strong> ${row.layout_width || 600}px</div>
                  <div class="col-md-4"><strong>Status:</strong> ${row.status || 'inactive'}</div>
                  <div class="col-md-4"><strong>Closing:</strong> ${row.closing || 'Not specified'}</div>
                </div>
              </div>

              ${row.logo ? `
                <div class="text-center mb-4">
                  <img src="/storage/${row.logo}" alt="Logo" class="mx-auto h-20 object-contain" onerror="this.style.display='none'">
                </div>` : ''}

              ${row.image ? `
                <div class="text-center mb-4">
                  <img src="/storage/${row.image}" alt="Image" class="mx-auto max-h-60 object-contain" onerror="this.style.display='none'">
                </div>` : ''}

              <div class="prose max-w-none mb-4">
                ${row.html_content ? decodeHtml(row.html_content) : '<p class="text-muted">No content available</p>'}
              </div>

              ${row.signature ? `
                <div class="border-t pt-3 mt-4">
                  <strong>Signature:</strong><br>${row.signature}
                </div>` : ''}

              ${row.footer_text ? `
                <div class="border-t pt-3 mt-4">
                  <strong>Footer:</strong><br>${row.footer_text}
                </div>` : ''}

            </div>
          </div>
        </body>
      </html>
    `);
      previewWindow.document.close();
    }
  });

});
// <><><><><><> END EMAIL TEMPLATE DETAILS JS <><><><><><><>

// <><><><><><> START CREATE E-NEWSPAPER JS <><><><><><>
$(document).ready(function () {
  $("#createENewspaperForm").on("submit", function (e) {
    e.preventDefault();

    let form = $(this);
    let formData = new FormData(this);

    // clear old errors
    $("strong[id$='-error']").text("");

    $.ajax({
      url: $(this).attr("action"),
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        if (response.status === true) {
          showSuccessToast(response.message);

          setTimeout(function () {
            window.location.href = response.redirect;
          }, 2000);
        } else {
          showErrorToast(response.message || "Something went wrong.");
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          var errors = xhr.responseJSON.errors;
          $.each(errors, function (key, value) {
            $("#" + key + "-error").text(value[0]);
          });
        } else {
          showErrorToast("Unexpected error occurred."); // ✅ toast on other errors
        }
      },
    });
  });
});
// <><><><><><> END CREATE E-NEWSPAPER JS <><><><><><>

// <><><><><><> START VIDEO POST JS <><><><><><>
// $(document).ready(function () {

//   postInitializeImageCropper('#video-thumb-input', '#video-thumb-preview', '#thumb-cropper-container', '#video-thumb-cropped', 'cropped_thumb');
//   postInitializeImageCropper('#post-image-input', '#post-image-preview', '#cropper-container', '#cropper-image');
//   // Handle form submission
//   $("#videoPostForm").on("submit", function (event) {
//     event.preventDefault();
//     clearErrorMessages();
//     var formData = new FormData(this);

//     if (cropper) {
//       const canvas = cropper.getCroppedCanvas();
//       canvas.toBlob(function (blob) {
//         const postType = $("#select_type_posts").val();
//         if (postType == "video") {
//           const file = new File([blob], "cropped-image.png", {
//             type: "image/png",
//           });
//           formData.set("thumb_image", file);
//         } else {
//           var imageUrl = $('#cropper-image').attr('src');

//           if (imageUrl != "") {
//             const canvas = cropper.getCroppedCanvas();
//             canvas.toBlob(function (blob) {
//               const file = new File([blob], "cropped-image.png", { type: "image/png" });
//               formData.set("image", file);
//               updateVideoPosts(formData);
//             });
//           } else {
//             formData.set("image", "");
//             updateVideoPosts(formData);
//           }
//         }
//         submitForm(formData);
//       });
//     } else {
//       submitForm(formData);
//     }
//   });

//   // Function to handle form submission via AJAX
//   function submitForm(formData) {
//     const url = $('#videoPostForm').attr("action");
//     const method = $('#videoPostForm').attr("method");
//     $('#video_submite_button').attr("disabled", false);
//     $('#video_back_button').attr("disabled", true);

//     $.ajax({
//       url: url,
//       method: method,
//       data: formData,
//       processData: false,
//       contentType: false,
//       headers: {
//         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//       },
//       dataType: "json",
//       success: function (response) {
//         if (response.status === "success") {
//           showSuccessToast(response.message);
//           setTimeout(() => {
//             window.location.href = response.redirect;
//           }, 1500);
//         }
//       },

//       error: function (xhr) {
//         if (xhr.status === 422) {
//           addDisplayErrors(xhr.responseJSON.errors);
//           $('#video_submite_button').attr("disabled", false);
//           $('#video_back_button').attr("disabled", false);
//         } else {
//           showErrorToast("An error occurred while processing your request.");
//         }
//       },
//     });
//   }
// });
// <><><><><><> END VIDEO POST JS  <><><><><><>

// <><><><><><> START VIDEO POST EDIT JS <><><><><><>
$(document).ready(function () {

  postInitializeImageCropper('#video-thumb-input', '#video-thumb-preview', '#thumb-cropper-container', '#video-thumb-cropped', 'cropped_thumb');
  postInitializeImageCropper('#post-image-input', '#post-image-preview', '#cropper-container', '#cropper-image');

  $("#editVideoPostForm").on("submit", function (event) {
    event.preventDefault();
    clearErrorMessages();
    $("strong[id$='-error']").text("");

    $('#video_update_submite_button').attr("disabled", true);
    $('#video_back_button').attr("disabled", true);

    var postType = $('#select_type_posts').val(); // 'youtube' or 'image'
    var formData = new FormData(this);
    formData.append("_method", "PUT");

    if (postType === 'video' && typeof cropper !== 'undefined') {
      var imageUrl = $('#cropper-image').attr('src');
      var imageThumbUrl = $('#video-thumb-cropped').attr('src');

      if (imageUrl != "") {
        const canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function (blob) {
          const file = new File([blob], "cropped-image.png", { type: "image/png" });
          formData.set("image", file);
          updateVideoPosts(formData);
        });
      } else if (imageThumbUrl) {
        const canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function (blob) {
          const file = new File([blob], "cropped-image.png", { type: "image/png" });
          formData.set("thumb_image", file);
          updateVideoPosts(formData);
        });
      } else {
        formData.set("image", "");
        updateVideoPosts(formData);
      }
    } else {
      var imageUrl = $('#cropper-image').attr('src');

      if (imageUrl != "") {
        const canvas = cropper.getCroppedCanvas();
        canvas.toBlob(function (blob) {
          const file = new File([blob], "cropped-image.png", { type: "image/png" });
          formData.set("image", file);
          updateVideoPosts(formData);
        });
      } else {
        formData.set("image", "");
        updateVideoPosts(formData);
      }
    }
  });

  function updateVideoPosts(formData) {
    const url = $('#editVideoPostForm').attr("action");


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
        if (response.status === "success") {
          showSuccessToast(response.message);
          setTimeout(() => {
            window.location.href = response.redirect;
          }, 1500);
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          $('#video_update_submite_button').attr("disabled", false);
          $('#video_back_button').attr("disabled", false);
          addDisplayErrors(xhr.responseJSON.errors);

          let errors = xhr.responseJSON.errors;
          $.each(errors, function (key, value) {
            let errorId1 = "#" + key + "-error";
            let errorId2 = "#" + key.replaceAll("_", "-") + "-error";
            if ($(errorId1).length) {
              $(errorId1).text(value[0]);
            } else if ($(errorId2).length) {
              $(errorId2).text(value[0]);
            }
          });
        } else {
          showErrorToast("An error occurred while processing your request.");
        }
      },
    });
  }
});
// <><><><><><> END VIDEO POST JS  <><><><><><>

// <><><><><><> START JS FOR VALIDATION ON FORMS <><><><><><>
$(document).ready(function () {
  $("#createCompanySetupForm,#addAudioForm,#videoPostForm,#email_template_form,#user-add-form,#user-edit-form,#addRoleForm,#editRoleForm,#addAdminUserForm,#subscription-setting-modal,#notification-setting-modal").on("submit", function (e) {
    e.preventDefault();

    let form = $(this);
    let formData = new FormData(this);

    // clear old errors
    $("strong[id$='-error']").text("");

    $.ajax({
      url: form.attr("action"),
      // method: "POST",
      method: form.attr("method") || "POST",
      data: formData,
      contentType: false,
      processData: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        if (response.success === true || response.status === true || response.status === 'success') {
          showSuccessToast(response.message);

          setTimeout(function () {
            if (response.redirect) {
              window.location.href = response.redirect;
            }
          }, 2000);
        } else {
          showErrorToast(response.message || "Something went wrong.");
        }
      },

      error: function (xhr) {
        if (xhr.status === 422) {

          let errors = xhr.responseJSON.errors;

          $.each(errors, function (key, value) {

            // support both id formats
            let errorId1 = "#" + key + "-error"; // news_language_id-error
            let errorId2 = "#" + key.replaceAll("_", "-") + "-error"; // news-language-id-error

            if ($(errorId1).length) {
              $(errorId1).text(value[0]);
            }
            else if ($(errorId2).length) {
              $(errorId2).text(value[0]);
            }

          });

        } else {
          showErrorToast("Unexpected error occurred.");
        }
      },
    });
  });
});

// ✅ Toasts
function showSuccessToast(message) {
  Toastify({
    text: message,
    duration: 4000,
    close: true,
    style: { background: "#28a745" }, // green
  }).showToast();
}

function showErrorToast(message) {
  Toastify({
    text: message,
    duration: 4000,
    close: true,
    style: { background: "#dc3545" }, // red
  }).showToast();
}

// <><><><><><> END JS OF VALIDATION ON FORMS <><><><><><>

// <><><><><><> START JS FOR ONLY ADD NUMBER NOT TEXT <><><><><
document.addEventListener('DOMContentLoaded', function () {
  const numberInputs = document.querySelectorAll('.only-numbers');

  numberInputs.forEach(function (input) {

    // Remove non-numeric characters on input
    input.addEventListener('input', function () {
      this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Prevent typing non-numeric keys
    input.addEventListener('keypress', function (e) {
      if (e.charCode < 48 || e.charCode > 57) {
        e.preventDefault();
      }
    });

    // Prevent pasting non-numeric content
    input.addEventListener('paste', function (e) {
      let pasteData = (e.clipboardData || window.clipboardData).getData('text');
      if (!/^\d+$/.test(pasteData)) {
        e.preventDefault();
      }
    });

  });
});
// <><><><><><> END JS OF ONLY ADD NUMBER NOT TEXT  <><><><><><>

// <><><><><><> START JS FOR STRIPE PAYMENT GATEWAY SETTING FORM VALIDATION <><><><><><>
$(document).ready(function () {
  $(".stripe_create_form").on("submit", function (e) {
    e.preventDefault();

    let form = $(this);
    let formData = new FormData(this);

    // clear old errors
    $("strong[id$='-error']").text("");

    $.ajax({
      url: form.attr("action"),
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        if (response.status === true) {
          showSuccessToast(response.message);

          setTimeout(function () {
            window.location.href = response.redirect; // ✅ redirect after success
          }, 2000);
        } else {
          showErrorToast(response.message || "Something went wrong.");
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;

          $.each(errors, function (key, value) {
            // Normalize keys
            let fieldId = "stripe_" + key.replaceAll("_", "_");

            // Show error in corresponding <span>
            $("#" + fieldId + "-error").text(value[0]);
          });
        } else {
          showErrorToast("Unexpected error occurred.");
        }
      }

    });
  });
});
// <><><><><><> END JS OF CREATE STRIPE PAYMENT GATEWAY SETTING FORM VALIDATION <><><><><><>

// <><><><><><> START JS FOR CREATE RAZORPAY PAYMENT GATEWAY SETTING FORM VALIDATION <><><><><><>
$(document).ready(function () {
  $(".razorpay_create_form").on("submit", function (e) {
    e.preventDefault();

    let form = $(this);
    let formData = new FormData(this);

    // clear old errors
    $("strong[id$='-error']").text("");

    $.ajax({
      url: form.attr("action"),
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        if (response.status === true) {
          showSuccessToast(response.message);

          setTimeout(function () {
            window.location.href = response.redirect; // ✅ redirect after success
          }, 2000);
        } else {
          showErrorToast(response.message || "Something went wrong.");
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;

          $.each(errors, function (key, value) {
            // Normalize keys
            let fieldId = "razorpay_" + key.replaceAll("_", "_");

            // Show error in corresponding <span>
            $("#" + fieldId + "-error").text(value[0]);
          });
        } else {
          showErrorToast("Unexpected error occurred.");
        }
      }

    });
  });
});
// <><><><><><> END JS OF CREATE RAZORPAY PAYMENT GATEWAY SETTING FORM VALIDATION <><><><><><>

// <><><><><><> START JS FOR  APPLE PAYMENT GATEWAY SETTING FORM VALIDATION <><><><><><>
$(document).ready(function () {
  $(".apple_pay_create_form").on("submit", function (e) {
    e.preventDefault();

    let form = $(this);
    let formData = new FormData(this);

    // clear old errors
    $("strong[id$='-error']").text("");

    $.ajax({
      url: form.attr("action"),
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        if (response.status === true) {
          showSuccessToast(response.message);

          setTimeout(function () {
            window.location.href = response.redirect; // ✅ redirect after success
          }, 2000);
        } else {
          showErrorToast(response.message || "Something went wrong.");
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;

          $.each(errors, function (key, value) {
            // Normalize keys
            let fieldId = "apple_pay_" + key.replaceAll("_", "_");

            // Show error in corresponding <span>
            $("#" + fieldId + "-error").text(value[0]);
          });
        } else {
          showErrorToast("Unexpected error occurred.");
        }
      }

    });
  });
});

// ✅ Toasts
function showSuccessToast(message) {
  Toastify({
    text: message,
    duration: 4000,
    close: true,
    style: { background: "#28a745" }, // green
  }).showToast();
}

function showErrorToast(message) {
  Toastify({
    text: message,
    duration: 4000,
    close: true,
    style: { background: "#dc3545" }, // red
  }).showToast();
}
// <><><><><><> END JS OF APPLE PAYMENT GATEWAY SETTING FORM VALIDATION <><><><><><>

// <><><><><><> START EMAIL TEMPLATE SPONSOR CREATE/EDIT JS <><><><><><>
$(document).ready(function () {
  $("#email_sponsor_template_form").on("submit", function (e) {
    e.preventDefault();

    // Clear previous errors
    $('#title-error-message').text('');
    $('#subject-error-message').text('');
    $('#logo-error-message').text('');
    $('#image-error-message').text('');
    $('#layout_width-error-message').text('');
    $('#status-error-message').text('');
    $('#html_content-error-message').text('');
    $('#closing-error-message').text('');
    $('#signature-error-message').text('');
    $('#footer_text-error-message').text('');

    var formData = new FormData(this);

    $.ajax({
      url: $(this).attr("action"),
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        $("#sponsorAddata").modal("hide");
        $("#email_sponsor_template_form")[0].reset();

        if (response.status === 'success') {
          showSuccessToast(response.message);

          // Use redirect URL from response instead of location.reload
          if (response.redirect) {
            setTimeout(function () {
              window.location.href = response.redirect;
            }, 2000); // 2-second delay
          }
        } else if (response.status === 'error') {
          showErrorToast(response.message);
        }
      },

      error: function (xhr) {
        if (xhr.status === 422) {
          var errors = xhr.responseJSON.errors;
          if (errors.title) { $("#title-error-message").text(errors.title[0]); }
          if (errors.subject) { $("#subject-error-message").text(errors.subject[0]); }
          if (errors.logo) { $("#logo-error-message").text(errors.logo[0]); }
          if (errors.image) { $("#image-error-message").text(errors.image[0]); }
          if (errors.layout_width) { $("#layout_width-error-message").text(errors.layout_width[0]); }
          if (errors.html_content) { $("#html_content-error-message").text(errors.html_content[0]); }
          if (errors.status) { $("#status-error-message").text(errors.status[0]); }
          if (errors.closing) { $("#closing-error-message").text(errors.closing[0]); }
          if (errors.signature) { $("#signature-error-message").text(errors.signature[0]); }
          if (errors.footer_text) { $("#footer_text-error-message").text(errors.footer_text[0]); }
        }
      },
    });
  });
});
// <><><><><><> END EMAIL TEMPLATE SPONSOR CREATE/EDIT JS <><><><><><>

// <><><><><><> START ADMIN USER DELETE JS <><><><><><>
$(document).on("click", ".admin-user-delete-form", function (e) {
  e.preventDefault();
  const url = $(this).attr("href");
  const token = $('meta[name="csrf-token"]').attr("content");

  Swal.fire({
    title: "Are you sure?",
    text: "This action will permanently delete the user.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#07467eff",
    confirmButtonText: "Yes, delete it!"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: url,
        type: "POST",
        data: {
          _method: "DELETE",
          _token: token,
        },
        success: function (response) {
          Swal.fire({
            title: "Deleted!",
            text: response.message || "User has been deleted.",
            icon: "success",
            confirmButtonText: "OK",
            allowOutsideClick: false,   // Disable outside click
            allowEscapeKey: false,      // Disable ESC
            allowEnterKey: false,       // Disable Enter key
            didOpen: () => {
              // disable all background clicks
              $(".swal2-container").css("pointer-events", "auto"); // keep swal clickable
              $("body").css("pointer-events", "none");             // disable body
              $(".swal2-container").css("pointer-events", "auto"); // re-enable swal
            }
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload(); // reload page only when user clicks OK
            }
          });
        },
        error: function (xhr) {
          Swal.fire({
            title: "Error!",
            text: xhr.responseJSON?.message || "Something went wrong.",
            icon: "error",
            confirmButtonText: "OK",
            allowOutsideClick: false,
            allowEscapeKey: false,
          });
        }
      });
    }
  });
});
// <><><><><><> END ADMIN USER DELETE JS  <><><><><><>

// <><><><><><> START JS FOR CREATE LANGUAGE JS <><><><><><>
const languageForm = document.getElementById("language-create-form");
if (languageForm) {
  languageForm.addEventListener("submit", function (e) {
    e.preventDefault();
    let form = this;
    let url = form.getAttribute("action");

    if (!url) return;

    let formData = new FormData(form);

    // Clear previous errors
    document.querySelectorAll('.error-text').forEach(el => el.textContent = "");

    const csrfToken = document.querySelector('input[name="_token"]');

    fetch(url, {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": csrfToken ? csrfToken.value : "",
        "Accept": "application/json"
      },
      body: formData
    })
      .then(response => {
        if (!response.ok) {
          return response.json().catch(() => ({}));
        }
        return response.json();
      })
      .then(data => {
        if (!data) return;

        if (data.errors) {
          for (let field in data.errors) {
            let errorSpan = document.querySelector(`.${field}_error`);
            if (errorSpan && data.errors[field] && data.errors[field][0]) {
              errorSpan.textContent = data.errors[field][0];
            }
          }
        } else if (data.success) {
          const modal = document.getElementById('languageAddModal');
          if (modal && typeof $ !== 'undefined' && $.fn.modal) {
            $('#languageAddModal').modal('hide');
          }

          if (typeof showSuccessToast === 'function') {
            showSuccessToast(data.message || 'Language created successfully');
          }

          form.reset();
          window.location.reload();
        }
      })
      .catch(err => {
        // Silent catch - no console errors
        // Optionally handle error in UI instead
      });
  });
}
// <><><><><><> END JS OF CREATE LANGUAGE JS <><><><><><>

// <><><><><><> START JS FOR DELETE LANGUAGE FILES <><><><><><>
document.querySelectorAll('.delete-file').forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.preventDefault();

    let url = this.dataset.url;
    let listItem = this.closest('.list-group-item');
    let parentList = listItem.closest('.list-group');
    let section = parentList.closest('.mb-3'); // parent section
    let uploadedSection = document.querySelector('#uploadedFilesSection');

    Swal.fire({
      title: 'Are you sure?',
      text: "This file will be permanently deleted!",
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
        this.disabled = true;

        fetch(url, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          }
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: data.message || 'File deleted successfully.',
                allowEnterKey: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
                showConfirmButton: true,
                confirmButtonText: "Ok",
              });

              listItem.style.transition = 'opacity 0.3s ease';
              listItem.style.opacity = 0;
              setTimeout(() => {
                listItem.remove();

                // if section empty, remove heading + section
                if (parentList.querySelectorAll('.list-group-item').length === 0) {
                  section.remove();
                }

                // if no section remains, remove main block
                if (!uploadedSection.querySelector('.list-group-item')) {
                  uploadedSection.remove();
                }
              }, 300);
            } else {
              Swal.fire('Error!', data.message || 'Something went wrong.', 'error');
            }
          })
          .catch(() => Swal.fire('Error!', 'Unable to delete file.', 'error'))
          .finally(() => { this.disabled = false; });
      }
    });
  });
});
// <><><><><><> END JS OF DELETE LANGUAGE FILES <><><><><><>

// <><><><><><> START CREDIT PACK DETAILS JS <><><><><><>
$(document).ready(function () {
  const creditPackTable = $("#creditPackTable").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#creditPackTable").data("url"),
    },
    columns: [
      { data: "id", name: "id" },
      { data: "name", name: "name" },
      { data: "product_id", name: "product_id" },
      { data: "credits", name: "credits" },
      { data: "price", name: "price" },
      { data: "savings_percent", name: "savings_percent" },
      { data: "is_popular", name: "is_popular", className: "text-center" },
      { data: "is_best_value", name: "is_best_value", className: "text-center" },
      { data: "action", name: "action", orderable: false, searchable: false },
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });
  // Delete Button with SweetAlert
  $("#creditPackTable").on("click", ".delete_btn", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "Are you sure?",
      text: "This action cannot be undone!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "credit-packs/" + id,
          type: "DELETE",
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
          success: function (response) {
            Swal.fire("Deleted!", response.success, "success");
            creditPackTable.ajax.reload(null, false); // reload without resetting page
          },
          error: function (xhr) {
            let msg = "Something went wrong.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
              msg = xhr.responseJSON.message;
            }
            Swal.fire("Error!", msg, "error");
          },
        });
      }
    });
  });
});
document.querySelectorAll('#creditPackForm input[type="number"]').forEach(input => {
  input.addEventListener('input', function () {
    if (this.value < 0) {
      this.value = 0; // reset to 0 if user types -1 or any negative
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("creditPackForm");

  if (form) {
    document.getElementById("creditPackForm").addEventListener("submit", function (e) {
      e.preventDefault();
      let form = this;
      let url = form.getAttribute("action");
      let formData = new FormData(form);
      document.querySelectorAll('.error-text').forEach(el => el.textContent = "");

      fetch(url, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
          "Accept": "application/json"
        },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.errors) {
            for (let field in data.errors) {
              let errorSpan = document.querySelector(`.${field}_error`);
              if (errorSpan) {
                errorSpan.textContent = data.errors[field][0];
              }
            }
          } else if (data.success) {
            $('#createCreditPackModal').modal('hide');
            showSuccessToast(data.message || 'Credit Pack created successfully');
            form.reset();

            window.location.reload();
          }
        })
        .catch(err => {
          console.error("Error:", err);
        });
    });
  }
});
// <><><><><><> END CREDIT PACK DETAILS JS <><><><><><>

$(document).on("click", "#read-more-btn", function () {
  const $text = $("#post-description-text");
  if ($text.hasClass("line-clamp-3")) {
    $text.removeClass("line-clamp-3");
    $(this).text("Read less");
  } else {
    $text.addClass("line-clamp-3");
    $(this).text("Read more");
  }
});

$(document).ready(function () {
  // Topic Ordering Logic
  if ($("#sortableTopics").length) {
    // Initialize sortable
    $("#sortableTopics").sortable();

    function fetchAndRenderTopics() {
      const selectedLang = $("#languageFilter").val();
      const url = "get-topics-order-by-language";

      $("#sortableTopics").empty().append('<li class="list-group-item text-center">Loading...</li>');

      $.ajax({
        type: "GET",
        url: url,
        data: { news_language_id: selectedLang },
        success: (response) => {
          $("#sortableTopics").empty();
          if (response.success && response.data.length > 0) {
            response.data.forEach((topic) => {
              let statusBadge = topic.status !== 'active' ? `<span class="badge bg-secondary ms-2">${topic.status.charAt(0).toUpperCase() + topic.status.slice(1)}</span>` : '';
              let logoImg = topic.logo ? `<img src="/storage/images/${topic.logo}" alt="${topic.name}" class="me-3" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">` : '';

              const item = `
                <li class="list-group-item topic-item" data-id="${topic.id}" style="cursor: move;">
                  <div class="d-flex align-items-center">
                    <i class="fa fa-bars me-3 text-muted"></i>
                    ${logoImg}
                    <span>${topic.name}</span>
                    ${statusBadge}
                  </div>
                </li>
              `;
              $("#sortableTopics").append(item);
            });
            $("#noTopicsMessage").hide();
          } else {
            if ($("#noTopicsMessage").length === 0) {
              $("#sortableTopics").after('<div id="noTopicsMessage" class="alert alert-info mt-2">No topics found for this language.</div>');
            } else {
              $("#noTopicsMessage").show();
            }
          }
        },
        error: (xhr) => {
          console.error("Error fetching topics:", xhr.responseText);
          $("#sortableTopics").empty().append('<li class="list-group-item text-danger text-center">Error loading topics</li>');
        }
      });
    }

    // Language Filter Change
    $("#languageFilter").on("change", fetchAndRenderTopics);

    // Initial Filter
    fetchAndRenderTopics();

    // Save Order Action
    $("#saveOrder").on("click", function () {
      const order = [];

      $("#sortableTopics .topic-item").each(function (index) {
        order.push({
          id: $(this).data("id"),
          position: index + 1
        });
      });

      if (order.length === 0) {
        showErrorToast("No topics to reorder.");
        return;
      }

      const url = "topics/update-order"; // Relative to /admin
      $.ajax({
        type: "POST",
        url: url,
        data: {
          order: order,
          _token: $('meta[name="csrf-token"]').attr("content")
        },
        success: (response) => {
          if (response.success) {
            showSuccessToast(response.message);
          } else {
            showErrorToast(response.message);
          }
        },
        error: (xhr) => {
          console.error("Error updating order:", xhr.responseText);
          showErrorToast("Something went wrong while saving the order.");
        }
      });
    });
  }
});


// <><><><><><> START JS FOR BLOCKED COMMENTS TABLE <><><><><><>
$(document).ready(function () {
  const blockedCommentsTable = $("#blocked_comments_table").DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    ajax: {
      url: $("#blocked_comments_table").data("url"),
      type: "GET"
    },
    columns: [
      { data: "id", name: "id" },
      { data: "blocker_name", name: "blocker_name" },
      { data: "owner_name", name: "owner_name" },
      {
        data: "comment",
        name: "comment",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data}</div>`;
        }
      },
      {
        data: "block_reason",
        name: "block_reason",
        render: function (data) {
          return `<div class="tabler_text_wrap_css">${data ?? ""}</div>`;
        }
      },
      {
        data: "created_at",
        name: "created_at",
        render: function (data) {
          let date = new Date(data);
          let formattedDate = new Intl.DateTimeFormat("en-IN", {
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false,
          }).format(date);
          return formattedDate;
        },
      }
    ],
    language: current_locale === "en" ? englishLanguage : hindiLanguage,
  });
});
// <><><><><><> END JS OF BLOCKED COMMENTS TABLE <><><><><><>
