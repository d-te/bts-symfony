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
                    addComment();

                    clearForm();
                    disableForm();
                }

                return false;
            });

            function clearForm()
            {
                $('#dte_btsbundle_comment_body').val('');
            }

            function disableForm()
            {
                $('#dte_btsbundle_comment_button').attr('disabled', 'disabled');
                $('#dte_btsbundle_comment_body').attr('disabled', 'disabled');
            }

            function enableForm()
            {
                $('#dte_btsbundle_comment_button').removeAttr('disabled');
                $('#dte_btsbundle_comment_body').removeAttr('disabled');
            }

            function refresh()
            {
                $.get(url, function(data) {
                    $('#comments').html(data);
                });
            }

            function addComment()
            {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(){
                        refresh();
                        enableForm();
                    },
                });
            }

        }
    });
})(jQuery);