/*issue form*/
(function ($) {
    $(document).ready(function() {

        if ($('form[name="dte_btsbundle_issue"]').length > 0) {
            $('#dte_btsbundle_issue_project').on('change', function(e) {
                var projectId = $('#dte_btsbundle_issue_project').find(':selected').val();

                loadProjectMembers(projectId);
                loadProjectStories(projectId);
            });

            $('#dte_btsbundle_issue_type').on('change', function(e) {
                var type = $('#dte_btsbundle_issue_type').find(':selected').val();

                if (type !== '4') {
                    $('#dte_btsbundle_issue_parent').find(':selected').removeAttr('selected');
                    $('#dte_btsbundle_issue_parent').attr('readonly', 'readonly');
                } else {
                    $('#dte_btsbundle_issue_parent').removeAttr('readonly');
                }
            });

            function loadProjectMembers(projectId)
            {
                $.get('/project/' + projectId + '/members', function(data) {
                    populateDropdown($('#dte_btsbundle_issue_assignee'), data);
                });
            }

            function loadProjectStories(projectId)
            {
                $.get('/project/' + projectId + '/stories', function(data) {
                    populateDropdown($('#dte_btsbundle_issue_parent'), data);
                });
            }

            function populateDropdown(element, data)
            {
                element.find('option:not(:first)').remove();
                $.each(data, function(key, value) {
                     element
                         .append($('<option></option>')
                         .attr('value', value.id)
                         .text(value.label));
                });
            }
        }
    });
})(jQuery);

/*comments*/
(function ($) {
    $(document).ready(function() {
        if ($('form[name="dte_btsbundle_comment"]').length > 0) {
            var form    = $('form[name="dte_btsbundle_comment"]');
            var issueId = parseInt($('#comments').attr('data-issue-id'));
            var url     = '/issue/' + issueId + '/comment/';

            refresh();

            form.submit(function(e) {
                e.preventDefault();

                var body = $('#dte_btsbundle_comment_body').val();
                if ('' !== body) {
                    addComment(form.serialize());

                    clearAddForm();
                    disableAddForm();
                }

                return false;
            });

            function registerFormsEvents() {
                $('.edit_comment_button').click(function(e){
                    e.preventDefault();

                    var commentId = $(this).attr('data-comment-id');

                    showEditForm(commentId);

                    return false;
                });

                $('.comment-form').submit(function(e) {
                    e.preventDefault();

                    var commentId = $(this).attr('data-comment-id');

                    var body = $(this).find('textarea[name="dte_btsbundle_comment[body]"]').val();
                    if ('' !== body) {
                        updateComment(commentId, this.serialize());
                    }

                    return false;
                });
            }

            function showEditForm(commentId)
            {
                $('#comment-body-textblock-' + commentId).addClass('hidden');
                $('#comment-body-formblock-' + commentId).removeClass('hidden');
            }

            function clearAddForm()
            {
                $('#dte_btsbundle_comment_body').val('');
            }

            function disableAddForm()
            {
                $('#dte_btsbundle_comment_button').attr('disabled', 'disabled');
                $('#dte_btsbundle_comment_body').attr('disabled', 'disabled');
            }

            function enableAddForm()
            {
                $('#dte_btsbundle_comment_button').removeAttr('disabled');
                $('#dte_btsbundle_comment_body').removeAttr('disabled');
            }

            function refresh()
            {
                $.get(url, function(data) {
                    $('#comments').html(data);

                    registerFormsEvents();
                });
            }

            function addComment(data)
            {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function() {
                        refresh();
                        enableAddForm();
                    },
                });
            }

            function updateComment(data)
            {
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: data,
                    success: function() {
                        refresh();
                    },
                });
            }

        }
    });
})(jQuery);