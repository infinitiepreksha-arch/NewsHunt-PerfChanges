// Comment Management
$(document).ready(function() {
    var contactTable = $('#comments-table').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, 'desc']],
        ajax: {
            url: $('#comments-table').data('url'),
            data: function(d) {
                d.post_id = $('#post_id').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'user_name' },
            { data: 'comment', name: 'comment' },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data, type, row) {
                if (data) {
                        const date = new Date(data);
                        return date.toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit',hour12: false 
                        });
                    }
                    return '';
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        language: current_locale === 'en' ? englishLanguage : hindiLanguage
    });


    $(document).on('click', '#delete_user_comment', function(e) {
        e.preventDefault();
    
        var commentId = $(this).data('comment-id');
        
        if (confirm('Are you sure you want to delete this comment?')) {
            $.ajax({
                url: '/admin/user-comments/' + commentId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    contactTable.ajax.reload();
                    if (response.message) {
                        $(`.comment[data-comment-id="${commentId}"]`).remove();
                        showSuccessToast('Comment deleted successfully!');
                    } else {
                        alert('Failed to delete the comment. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the comment. Please try again.');
                }
            });
        }
    });
});
