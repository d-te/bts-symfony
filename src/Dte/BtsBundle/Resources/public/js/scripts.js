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
                    $('#dte_btsbundle_issue_parent').attr('disabled', 'disabled');
                } else {
                    $('#dte_btsbundle_issue_parent').removeAttr('disabled');
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