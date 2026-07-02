$(document).ready(function () {
    const searchInput = $('#globle_search');
    const suggestionsList = $('#suggestions');
    const autocompleteUrl = $('#uc-search-modal').data('autocomplete-url');

    searchInput.on('keyup', function () {
        const searchQuery = $(this).val().trim();

        if (searchQuery !== '') {
            $.ajax({
                type: 'GET',
                url: autocompleteUrl,
                data: { search: searchQuery },
                dataType: 'json'
            })
                .done(function (data) {
                    suggestionsList.empty();
                    if (data.length > 0) {
                        $.each(data, function (index, suggestion) {
                            const suggestionItem = `
                            <li class="suggestion-item" onclick="selectSuggestion('${suggestion.title}')">
                                <i class="unicon-search icon-1"></i>
                                <span>${suggestion.title}</span>
                            </li>`;
                            suggestionsList.append(suggestionItem);
                        });
                        suggestionsList.show();
                    } else {
                        suggestionsList.hide();
                    }
                })
                .fail(function (xhr, status, error) {
                    console.error(error);
                });
        } else {
            suggestionsList.empty();
            suggestionsList.hide();
        }
    });
});

function selectSuggestion(suggestion) {
    $('#globle_search').val(suggestion);
    $('#suggestions').hide();
    document.getElementById("search-form-data").submit();
}


$(document).ready(function () {

    function followOnlyChannel(channelId, button) {
        $.ajax({
            url: '/follow/' + channelId,
            method: 'GET',
            success: function (response) {
                if (response.status == 1) {
                    button.text('Unfollow');
                    button.removeClass('btn-outline-primary').addClass('btn-primary');
                } else {
                    button.text('follow');
                    button.removeClass('btn-primary').addClass('btn-outline-primary');
                }
                if (!response.error) {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                }
            },
            error: function (xhr) { }
        });
    }


    $('.channel-follow').on('click', function (event) {
        event.preventDefault();
        const channelId = $(this).data('channel-id');
        followOnlyChannel(channelId, $(this));
    });
});

$(window).on('load', function () {
    if (typeof UniCore !== 'undefined' && UniCore.modal) {

        if ($("#channels-follow-model").length) {
            UniCore.modal("#channels-follow-model").show();
        }
    }
    localStorage.setItem("newsletterModalShown", "true");
    // localStorage.setItem("visitCount", "0");

    $.ajax({
        url: '/first-login',
        type: 'get',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
        },
        error: function (error) {
        }   
    });
});
