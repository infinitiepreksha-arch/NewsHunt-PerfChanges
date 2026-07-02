// <><><><><><><> START JS FOR COPY URL ON POST DETAILS PAGE <><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
    var buttons = [document.getElementById('copyButton'), document.getElementById('copyButton_1')];

    buttons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            var currentUrl = window.location.href;

            var textarea = document.createElement('textarea');
            textarea.value = currentUrl;
            document.body.appendChild(textarea);

            textarea.select();
            document.execCommand('copy');

            document.body.removeChild(textarea);
            const message = button.dataset.message || 'Post link copied to clipboard successfully!';
            iziToast.success({
                title: message,
                position: 'topCenter',
            });
        });
    });
});
// <><><><><><><> END JS OF COPY URL ON POST DETAILS PAGE <><><><><><><>

// <><><><><><><> START JS FOR BOOKMARK POSTS ON POST DETAILS PAGE <><><><><><><>
$(document).ready(function () {
    let id = $('#post_id').val();
    let bookmark_button = $('#bookmark-post');

    bookmark_button.click(function (event) {
        event.preventDefault();

        $.ajax({
            url: '/posts/favorite',
            method: 'POST',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                const favorit_count = $('#favorite_counts')
                if (response.status == 1) {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                    favorit_count.text(response.count);
                    $('#bookmark-post i').removeClass('bi-bookmarks').addClass('bi-bookmarks-fill');
                } else {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                    favorit_count.text(response.count);
                    $('#bookmark-post i').removeClass('bi-bookmarks-fill').addClass('bi-bookmarks');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error occurred: ' + error);
            }
        });
    });
});
// <><><><><><><> END JS OF BOOKMARK POSTS ON POST DETAILS PAGE <><><><><><><>

// <><><><><><><> START JS FOR COMMENT AND REPORTED COMMENT <><><><><><><>
document.addEventListener("DOMContentLoaded", function () {
    const postId = document.getElementById('post_id').value;
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');

    // Store report types globally
    let reportTypes = [];

    // Fetch report types on page load
    fetchReportTypes();

    let lastErrorMessage = '';
    let lastErrorToastTime = 0;
    // Function to show error message using iziToast
    function showErrorMessage(messageOrElement) {
        let message = 'An error occurred.';
        if (typeof messageOrElement === 'string') {
            message = messageOrElement;
        } else if (messageOrElement && messageOrElement.dataset && messageOrElement.dataset.message) {
            message = messageOrElement.dataset.message;
        }

        const now = Date.now();
        if (message === lastErrorMessage && now - lastErrorToastTime < 1000) {
            return;
        }
        lastErrorMessage = message;
        lastErrorToastTime = now;

        iziToast.error({
            title: message,
            position: 'topCenter',
            timeout: 3000,
        });
    }

    // Handle both inputs at once
    document.querySelectorAll('#name, #email').forEach(function (input) {
        input.addEventListener('focus', function (e) {
            showErrorMessage(e.target);
            e.target.blur();
        });
    });

    // Prevent interaction with name and email fields
    if (nameInput && emailInput) {
        const originalName = nameInput.value;
        const originalEmail = emailInput.value;

        nameInput.addEventListener('click', (e) => {
            e.preventDefault();
            showErrorMessage(e.target);
        });
        emailInput.addEventListener('click', (e) => {
            e.preventDefault();
            showErrorMessage(e.target);
        });

        nameInput.addEventListener('input', (e) => {
            e.target.value = originalName;
            showErrorMessage(e.target);
        });
        emailInput.addEventListener('input', (e) => {
            e.target.value = originalEmail;
            showErrorMessage(e.target);
        });

        nameInput.addEventListener('keydown', (e) => {
            e.preventDefault();
            showErrorMessage(e.target);
        });
        emailInput.addEventListener('keydown', (e) => {
            e.preventDefault();
            showErrorMessage(e.target);
        });
    }

    // Fetch report types from backend dynamically
    function fetchReportTypes() {
        const apiUrl = document.getElementById('reportReasonDataUrl').value;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                // Check your backend structure — here it returns { error, message, data: [...] }
                if (!data.error && data.data && Array.isArray(data.data)) {
                    reportTypes = data.data;
                } else {
                    console.error('Invalid response format:', data);
                    reportTypes = [];
                }
            })
            .catch(error => {
                console.error('Error fetching report types:', error);
                reportTypes = [];
            });
    }

    // Generate report type options HTML
    function generateReportTypeOptions() {
        // if (!reportTypes.length) {
        //     return `<option value="">No report reasons available</option>`;
        // }

        return reportTypes.map(type =>
            `<option value="${type.id}">${type.title}</option>`
        ).join('');
    }


    // Fetch comments
    function fetchComments() {
        fetch(`/posts/${postId}/comments`)
            .then(response => response.json())
            .then(data => {
                renderComments(data.comments);
                updateCommentCount(data.count);
            })
            .catch(error => console.error('Error fetching comments:', error));
    }

    // Render comments
    function renderComments(comments) {
        const commentsList = document.getElementById('comments-list');
        commentsList.innerHTML = '';
        comments.forEach(comment => {
            commentsList.appendChild(createCommentElement(comment));
        });
    }

    // Update the comment count in the HTML
    function updateCommentCount(count) {
        const numberElement = document.getElementById('count-num');
        if (numberElement) {
            numberElement.innerText = count;
        }
    }

    function createCommentElement(comment) {
        const li = document.createElement('li');
        var button = document.getElementById("user_id").value;
        $(li).addClass('comment-item py-0 px-0 dark:bg-black custom-margin-0 m-1 sm:m-2 ');
        const userName = comment.user ? comment.user.name : 'Anonymous';
        const userId = comment.user_id ?? '0';
        const commentId = comment.id ?? '0';
        const defaultProfileUrl = `${window.location.origin}/public/front_end/classic/images/default/profile-avatar.jpg`;
        const userProfile = comment.user && comment.user.profile ? comment.user.profile : defaultProfileUrl;
        const isRemoved = comment.is_removed || false;
        console.log(`Comment ID: ${commentId}, isRemoved:`, isRemoved);

        // Get all translations at once
        const translations = document.getElementById('translation-data').dataset;

        const commentText = isRemoved ? `<span class="text-muted italic">Your comment has been removed by the admin.</span>` : comment.comment;

        li.innerHTML = `
        <div class="avatar mt-4 ">
            <img src="${userProfile}" alt="">
        </div>
        <div class="comment-info">
            <span class="c_name mt-4">${userName}</span>
            <span class="c_date id-color">${comment.created_at}</span>
            ${!isRemoved && userId != button ? `
            <a onclick="setReportId(${commentId})" id='report_user_comment' data-bs-toggle='tooltip' data-comment-id='${commentId}' title='Report'>
                <i class="bi bi-flag-fill text-primary"></i>
            </a>
            <a onclick="setBlockId(${commentId})" id='block_user_comment' data-bs-toggle='tooltip' data-comment-id='${commentId}' title='Block'>
                <i class="bi bi-slash-circle-fill text-danger "></i>
            </a>
            ` : ''}
            ${!isRemoved && userId == button ? `
                <a class="custim-left-margin-10" onclick="setEditId(${commentId})"><i class='bi bi-pencil-fill'></i></a>
                <a href='javascript:void(0);' class='m-1 text-danger' id='delete_user_comment' data-bs-toggle='tooltip' data-comment-id='${commentId}' title='Delete'><i class="bi bi-trash3-fill text-primary"></i></a>
            ` : ""}
            ${!isRemoved && !comment.parent_id ? `<span class="c_reply"><a onclick="setParentId(${commentId})">Reply</a></span>` : ""}
            <div class="clearfix"></div>
        </div>
        
        <div class="comment mb-4">${commentText}</div>

        <div class="card d-none col-12 dark:bg-black" id="comment_report_box_${commentId}">
            <div class="card-header d-flex justify-between">
                <span>${translations.reportComment}</span>
                <a class="text-none" onclick="closeModelReport(${commentId})" id="close_report_${commentId}">
                    <i class="unicon-close"></i>
                </a>
            </div>
            <div class="card-body">
                <form id="report-form-${commentId}" class="mb-0" onsubmit="submitCommentReport(event, ${commentId})">
                    <input type="hidden" name="comment_id" id="comment_id_${commentId}" value="${commentId}">
                    <input type="hidden" name="user_id" value="${button}">
                    <input type="hidden" name="type" value="report">
                    
                    <!-- Reason Dropdown -->
                    <div class="mb-3">
                        <label for="report_reason_${commentId}" class="form-label">${translations.selectReason} <span class="text-danger">*</span></label>
                        <select class="form-control bg-white dark:bg-gray-800 text-black dark:text-white" name="report_type_id" id="report_reason_${commentId}" 
                                onchange="toggleOtherReason(${commentId})">
                            <option class="bg-white dark:bg-gray-800 text-black dark:text-white" value="">-- ${translations.selectReason} --</option>
                            ${generateReportTypeOptions()}
                            <option value="other">${translations.other}</option>
                        </select>
                    </div>

                    <!-- Other Reason Input (Hidden by default) -->
                    <div class="mb-3 d-none" id="other_reason_container_${commentId}">
                        <label for="other_reason_${commentId}" class="form-label">${translations.customReasonPlaceholder} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="other_type" 
                            id="other_reason_${commentId}" placeholder="${translations.customReasonPlaceholder}">
                    </div>

                    <!-- Additional Comments -->
                    <div class="mb-3">
                        <label for="comment_report_${commentId}" class="form-label">${translations.additionalDetails}</label>
                        <textarea class="form-control col-12" name="report" 
                                id="comment_report_${commentId}" rows="3" 
                                placeholder="${translations.additionalInfoPlaceholder}"></textarea>
                    </div>

                    ${button !== "0" ?
                `<button class="btn btn-primary btn-xs mt-2 mb-0" type="submit">Send Report</button>` :
                `<a class="btn btn-primary btn-xs mt-1" href="#uc-account-modal" data-uc-toggle>${translations.sendReport}"</a>`
            }
                </form>
            </div>
        </div>

        <!-- Block Comment Box -->
        <div class="card d-none col-12 dark:bg-black" id="comment_block_box_${commentId}">
            <div class="card-header d-flex justify-between">
                <span>${translations.blockComment}</span>
                <a class="text-none" onclick="closeModelBlock(${commentId})" id="close_block_${commentId}">
                    <i class="unicon-close"></i>
                </a>
            </div>
            <div class="card-body">
                <form id="block-form-${commentId}" class="mb-0" onsubmit="submitCommentBlock(event, ${commentId})">
                    <input type="hidden" name="comment_id" value="${commentId}">
                    <input type="hidden" name="user_id" value="${button}">
                    <input type="hidden" name="type" value="block">

                    <div class="mb-3">
                        <label for="block_reason_${commentId}" class="form-label">${translations.blockReason} <span class="text-danger">*</span></label>
                        <textarea class="form-control col-12" name="block_reason" id="block_reason_${commentId}" rows="3" placeholder="${translations.blockReasonPlaceholder}"></textarea>
                    </div>

                    ${button !== "0" ?
                `<button class="btn btn-primary btn-xs mt-2 mb-0" type="submit">${translations.sendBlock}</button>` :
                `<a class="btn btn-danger btn-xs mt-1" href="#uc-account-modal" data-uc-toggle>${translations.sendBlock}</a>`
            }
                </form>
            </div>
        </div>

        <!-- Reply Comment Modal -->
        <div class="card d-none col-12 dark:bg-black" id="comment_repay_box_${commentId}">
            <div class="card-header d-flex justify-between">
                <span>Leave Comment</span>
                <a class="text-none" onclick="closeModel(${commentId})" id="close_replay_${commentId}"><i class="unicon-close"></i></a>
            </div>
            <div class="card-body">
                <form id="replay-form" class="mb-0" onsubmit="submitCommentReplay(event,${commentId})">
                    <input type="hidden" name="parent_id" id="parent-id_${commentId}" value="${commentId}">
                    <input
                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                        type="text" name="name" placeholder="First name" value="${nameInput ? nameInput.value : ''}" >
                    <input
                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                        type="email" name="email" placeholder="Your email" value="${emailInput ? emailInput.value : ''}" >
                    <textarea class="form-control col-12 mr-1" name="comment" id="comment_replay_${commentId}" rows="3" ></textarea>
                    ${button !== "0" ?
                `<button class="btn btn-primary btn-xs mt-2 mb-0" type="submit">Send</button>` :
                `<a class="btn btn-primary btn-xs mt-1" href="#uc-account-modal" data-uc-toggle>Send</a>`
            }
                </form>
            </div>
        </div>

        <!-- Edit Comment Modal -->
        <div class="card d-none col-12 dark:bg-black" id="comment_edit_box_${commentId}">
            <div class="card-header d-flex justify-between">
                <span>Edit Comment</span>
                <a class="text-none" onclick="closeModelEdit(${commentId})" id="close_edit_${commentId}"><i class="unicon-close"></i></a>
            </div>
            <div class="card-body">
                <form id="edit-comment-form" class="mb-0" onsubmit="submitCommentEdit(event, ${commentId})">
                    <input type="hidden" name="comment_id" id="comment_id" value="${commentId}">
                   
                    <textarea class="form-control col-12 mr-1" name="comment" id="comment_update_${commentId}" rows="3">${comment.comment}</textarea>
                    ${button !== "0" ?
                `<button class="btn btn-primary btn-xs mt-2 mb-0" type="submit">Send</button>` :
                `<a class="btn btn-primary btn-xs mt-1" href="#uc-account-modal" data-uc-toggle>Send</a>`
            }
                </form>
            </div>
        </div>
    `;

        // Handle replies
        if (comment.replies && comment.replies.length) {
            const repliesList = document.createElement('ol');
            comment.replies.forEach(reply => {
                repliesList.appendChild(createCommentElement(reply));
            });
            li.appendChild(repliesList);
        }

        return li;
    }

    // Toggle Other Reason input field
    window.toggleOtherReason = function (commentId) {
        const selectElement = document.getElementById(`report_reason_${commentId}`);
        const container = document.getElementById(`other_reason_container_${commentId}`);
        const otherInput = document.getElementById(`other_reason_${commentId}`);

        if (selectElement.value === 'other') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
            otherInput.value = '';
        }
    };

    window.setParentId = function (parentId) {
        document.getElementById('comment_repay_box_' + parentId).classList.remove('d-none');
    };

    window.setEditId = function (parentId) {
        document.getElementById('comment_edit_box_' + parentId).classList.remove('d-none');
    };

    window.setReportId = function (parentId) {
        const reportUrl = document.getElementById('reportDataUrl').value;
        const checkUrl = reportUrl.replace('comments/web/report', 'commets/check-report');

        fetch(`${checkUrl}?comment_id=${parentId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.already_reported) {
                    iziToast.error({
                        title: data.message || 'You have already reported this comment.',
                        position: 'topCenter',
                    });
                } else {
                    document.getElementById('comment_report_box_' + parentId).classList.remove('d-none');
                    document.getElementById('comment_block_box_' + parentId).classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Error checking report status:', error);
                // Fallback: show the form anyway if the check fails
                document.getElementById('comment_report_box_' + parentId).classList.remove('d-none');
            });
    };

    window.setBlockId = function (commentId) {
        document.getElementById('comment_block_box_' + commentId).classList.remove('d-none');
        document.getElementById('comment_report_box_' + commentId).classList.add('d-none');
    };

    window.closeModel = function (commentId) {
        const replyBox = document.getElementById(`comment_repay_box_${commentId}`);
        if (replyBox) {
            replyBox.classList.add('d-none');
        }
    }

    window.closeModelEdit = function (commentId) {
        const replyBox = document.getElementById(`comment_edit_box_${commentId}`);
        if (replyBox) {
            replyBox.classList.add('d-none');
        }
    }

    window.closeModelReport = function (commentId) {
        const reportBox = document.getElementById(`comment_report_box_${commentId}`);
        if (reportBox) {
            reportBox.classList.add('d-none');

            // Reset form
            const form = document.getElementById(`report-form-${commentId}`);
            if (form) {
                form.reset();
            }

            // Hide other reason container
            const otherContainer = document.getElementById(`other_reason_container_${commentId}`);
            if (otherContainer) {
                otherContainer.classList.add('d-none');
            }
        }
    }

    window.closeModelBlock = function (commentId) {
        const blockBox = document.getElementById(`comment_block_box_${commentId}`);
        if (blockBox) {
            blockBox.classList.add('d-none');
            const form = document.getElementById(`block-form-${commentId}`);
            if (form) form.reset();
        }
    }

    // Submit a comment
    window.submitComment = function (event) {
        event.preventDefault();
        const commentText = document.getElementById('comment').value;
        const name = nameInput ? nameInput.value : null;
        const email = emailInput ? emailInput.value : null;
        const parentId = null; // For top-level comments
        const sendDataUrl = document.getElementById('sendDataUrl').value;
        const data = {
            comment: commentText,
            name: name,
            email: email,
            parent_id: parentId,
            post_id: postId
        };
        fetch(sendDataUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchComments(); // Render the new comment
                    document.getElementById('comment').value = '';
                    iziToast.success({
                        title: data.message,
                        position: 'topCenter',
                    });
                } else {
                    const errorMsg = data.errors ? Object.values(data.errors).join(', ') : data.message || 'An error occurred.';
                    showErrorMessage(errorMsg);
                }
            })
            .catch(error => {
                showErrorMessage('An error occurred while submitting the comment.');
            });
    };

    // Submit a comment replay
    window.submitCommentReplay = function (event, replay_id) {
        event.preventDefault();
        var parentId = document.getElementById('parent-id_' + replay_id).value;
        const commentText = document.getElementById('comment_replay_' + replay_id).value;
        const name = document.querySelector(`#comment_repay_box_${replay_id} input[name="name"]`).value;
        const email = document.querySelector(`#comment_repay_box_${replay_id} input[name="email"]`).value;
        const sendDataUrl = document.getElementById('sendDataUrl').value;
        const data = {
            comment: commentText,
            name: name,
            email: email,
            parent_id: parentId ? parentId : null,
            post_id: postId
        };
        fetch(sendDataUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const replyBox = document.getElementById(`comment_repay_box_${parentId}`);
                    replyBox.classList.add('d-none');
                    fetchComments();
                } else {
                    iziToast.error({
                        title: data.errors ? Object.values(data.errors).join(', ') : (data.message || 'Failed to submit reply'),
                        position: 'topCenter',
                    });
                }
            })
            .catch(error => {
                iziToast.error({
                    title: 'Error',
                    message: 'An error occurred while submitting the reply',
                    position: 'topCenter',
                });
            });
    };

    // Submit a comment edit
    window.submitCommentEdit = function (event, comment_edit_id) {
        event.preventDefault();
        const commentText = document.getElementById('comment_update_' + comment_edit_id).value;
        const name = nameInput ? nameInput.value : null;
        const email = emailInput ? emailInput.value : null;
        const sendDataUrl = document.getElementById('updateDataUrl').value;
        const data = {
            comment: commentText,
            name: name,
            email: email,
            id: comment_edit_id
        };
        fetch(sendDataUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const replyBox = document.getElementById(`comment_edit_box_${comment_edit_id}`);
                    replyBox.classList.add('d-none');
                    fetchComments();
                    iziToast.success({
                        title: data.message,
                        position: 'topCenter',
                    });
                } else {
                    const errorMsg = data.errors ? Object.values(data.errors).join(', ') : data.message || 'An error occurred.';
                    showErrorMessage(errorMsg);
                }
            })
            .catch(error => {
                showErrorMessage('An error occurred while updating the comment.');
            });
    };

    /* Report Comment - UPDATED */

    window.submitCommentReport = function (event, commentId) {
        event.preventDefault();

        const form = document.getElementById(`report-form-${commentId}`);
        const formData = new FormData(form);
        const sendDataUrl = document.getElementById('reportDataUrl').value;

        const selectElement = document.getElementById(`report_reason_${commentId}`);
        const isOther = selectElement.value === 'other';

        // Prepare data
        const data = {
            type: formData.get('type') || 'report',
            comment_id: formData.get('comment_id'),
            user_id: formData.get('user_id'),
            report_type_id: isOther ? null : formData.get('report_type_id'),
            report: formData.get('report') || '',
            is_other: isOther ? 1 : 0,
            other_type: isOther ? formData.get('other_type') : null,
        };

        fetch(sendDataUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(async response => {
                const resData = await response.json();

                if (response.status === 422) {
                    // Validation errors
                    for (const [key, messages] of Object.entries(resData.errors)) {
                        messages.forEach(msg => {
                            iziToast.error({
                                title: msg,
                                position: 'topCenter',
                            });
                        });
                    }
                } else if (!resData.success) {
                    // Other errors (like duplicate report or unauthorized)
                    iziToast.error({
                        title: resData.message || 'Failed to submit report',
                        position: 'topCenter',
                    });
                } else {
                    // Success
                    closeModelReport(commentId);
                    iziToast.success({
                        title: resData.message || 'Report submitted successfully!',
                        position: 'topCenter',
                    });

                    // Optional: reset form
                    form.reset();
                    document.getElementById(`other_reason_container_${commentId}`).classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Error submitting report:', error);
                iziToast.error({
                    title: 'An error occurred while submitting the report',
                    position: 'topCenter',
                });
            });
    };

    window.submitCommentBlock = function (event, commentId) {
        event.preventDefault();
        const form = document.getElementById(`block-form-${commentId}`);
        const formData = new FormData(form);
        const sendDataUrl = document.getElementById('reportDataUrl').value;

        const data = {
            type: 'block',
            comment_id: formData.get('comment_id'),
            user_id: formData.get('user_id'),
            block_reason: formData.get('block_reason'),
        };

        fetch(sendDataUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(async response => {
                const resData = await response.json();
                if (response.status === 422) {
                    for (const [key, messages] of Object.entries(resData.errors)) {
                        messages.forEach(msg => iziToast.error({ title: msg, position: 'topCenter' }));
                    }
                } else if (!resData.success) {
                    iziToast.error({ title: resData.message || 'Failed to block comment', position: 'topCenter' });
                } else {
                    closeModelBlock(commentId);
                    iziToast.success({ title: resData.message || 'Comment blocked successfully!', position: 'topCenter' });
                    fetchComments(); // Refresh list to reflect changes if any (though usually block just hides it for that user)
                }
            })
            .catch(error => {
                console.error('Error blocking comment:', error);
                iziToast.error({ title: 'An error occurred while blocking the comment', position: 'topCenter' });
            });
    };



    // Close report modal
    window.closeModelReport = function (commentId) {
        const box = document.getElementById(`comment_report_box_${commentId}`);
        if (box) box.classList.add('d-none');
    };

    fetchComments();

    function deleteComment(commentId) {
        $.ajax({
            url: '/comments/delete/' + commentId,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                if (response.message) {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                    fetchComments();
                } else {
                    iziToast.success({
                        title: "Fail to Delete",
                        position: 'topCenter',
                    });
                }
            },
            error: function () {
                iziToast.success({
                    title: "An error occurred while deleting",
                    position: 'topCenter',
                });
            }
        });
    }

    $(document).on('click', '#delete_user_comment', function (e) {
        e.preventDefault();
        const commentId = $(this).data('comment-id');

        const deleteTitle = document.getElementById('swal-delete-title').innerText;
        const deleteText = document.getElementById('swal-delete-text').innerText;
        const deleteBtn = document.getElementById('swal-delete-button').innerText;
        const cancelBtn = document.getElementById('swal-cancel-button').innerText;
        Swal.fire({
            title: deleteTitle,
            text: deleteText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: deleteBtn,
            cancelButtonText: cancelBtn,
            customClass: {
                popup: 'dark:bg-black dark:text-white'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                deleteComment(commentId)
            }
        });
    });
});
// <><><><><><><> END JS OF COMMENT AND REPORTED COMMENT <><><><><><><>
