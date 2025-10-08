jQuery(document).ready(function($) {
    $('#aicg-form').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        var formData = {
            action: 'aicg_generate_posts',
            nonce: aicg_ajax.nonce,
            keyword: $('#keyword').val(),
            post_type: $('#post-type').val(),
            num_posts: $('#num-posts').val(),
            generate_image: $('#generate-image').is(':checked') ? 1 : 0
        };

        // Show loader
        $('#aicg-loader').fadeIn(300);
        $('#aicg-response').html('').hide();
        $('.aicg-submit-btn').prop('disabled', true).addClass('disabled');

        // Send AJAX request
        $.ajax({
            url: aicg_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                // Hide loader
                $('#aicg-loader').fadeOut(300, function() {
                    // Show response
                    if (response.success) {
                        var message = '<div class="aicg-success">';
                        message += '<span class="dashicons dashicons-yes-alt"></span>';
                        message += '<h3>' + response.data.message + '</h3>';
                        
                        if (response.data.posts && response.data.posts.length > 0) {
                            message += '<div class="aicg-posts-list">';
                            message += '<h4>Created Posts:</h4>';
                            message += '<ul>';
                            response.data.posts.forEach(function(post) {
                                message += '<li>';
                                message += '<span class="dashicons dashicons-admin-post"></span>';
                                message += '<strong>' + post.title + '</strong>';
                                message += ' <a href="' + post.edit_link + '" target="_blank" class="button button-small">Edit Post</a>';
                                message += '</li>';
                            });
                            message += '</ul>';
                            message += '</div>';
                        }
                        
                        if (response.data.errors && response.data.errors.length > 0) {
                            message += '<div class="aicg-warnings">';
                            message += '<h4>Warnings:</h4>';
                            message += '<ul>';
                            response.data.errors.forEach(function(error) {
                                message += '<li>' + error + '</li>';
                            });
                            message += '</ul>';
                            message += '</div>';
                        }
                        
                        message += '</div>';
                        $('#aicg-response').html(message).fadeIn(300);
                        
                        // Reset form
                        $('#aicg-form')[0].reset();
                    } else {
                        var errorMessage = '<div class="aicg-error">';
                        errorMessage += '<span class="dashicons dashicons-dismiss"></span>';
                        errorMessage += '<h3>Error</h3>';
                        errorMessage += '<p>' + (response.data.message || 'An error occurred') + '</p>';
                        
                        if (response.data.errors && response.data.errors.length > 0) {
                            errorMessage += '<ul>';
                            response.data.errors.forEach(function(error) {
                                errorMessage += '<li>' + error + '</li>';
                            });
                            errorMessage += '</ul>';
                        }
                        
                        errorMessage += '</div>';
                        $('#aicg-response').html(errorMessage).fadeIn(300);
                    }
                    
                    // Re-enable submit button
                    $('.aicg-submit-btn').prop('disabled', false).removeClass('disabled');
                });
            },
            error: function(xhr, status, error) {
                // Hide loader
                $('#aicg-loader').fadeOut(300, function() {
                    // Show error
                    var errorMessage = '<div class="aicg-error">';
                    errorMessage += '<span class="dashicons dashicons-dismiss"></span>';
                    errorMessage += '<h3>Connection Error</h3>';
                    errorMessage += '<p>Failed to connect to the server. Please try again.</p>';
                    errorMessage += '<p class="aicg-error-details">' + error + '</p>';
                    errorMessage += '</div>';
                    
                    $('#aicg-response').html(errorMessage).fadeIn(300);
                    
                    // Re-enable submit button
                    $('.aicg-submit-btn').prop('disabled', false).removeClass('disabled');
                });
            }
        });
    });

    // Number input validation
    $('#num-posts').on('input', function() {
        var val = parseInt($(this).val());
        if (val < 1) $(this).val(1);
        if (val > 10) $(this).val(10);
    });
});