<?php 
function aicg_create_post($title, $content, $post_type, $image_url = '') {
    // Create the post first
    $post_data = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_type'     => $post_type,
        'post_author'   => get_current_user_id()
    );

    $post_id = wp_insert_post($post_data);
    
    if (!$post_id || is_wp_error($post_id)) {
        error_log('Failed to create post');
        return false;
    }

    error_log('Post created successfully. ID: ' . $post_id);

    // Upload image and attach to post if URL provided
    if (!empty($image_url)) {
        error_log('Attempting to add featured image for post ' . $post_id);
        $image_id = aicg_upload_image_from_url($image_url, $title, $post_id);
        
        // Set featured image if upload was successful
        if (!empty($image_id) && !is_wp_error($image_id)) {
            $thumbnail_set = set_post_thumbnail($post_id, $image_id);
            error_log('Set post thumbnail result: ' . ($thumbnail_set ? 'Success' : 'Failed'));
        } else {
            if (is_wp_error($image_id)) {
                error_log('Image upload error: ' . $image_id->get_error_message());
            } else {
                error_log('Image upload returned empty/invalid ID');
            }
        }
    }

    return $post_id;
}

function aicg_upload_image_from_url($image_url, $title = '', $post_id = 0) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    error_log('Starting image download from: ' . $image_url);

    // Add random parameter to force unique download (important for cached URLs)
    if (strpos($image_url, '?') === false) {
        $image_url .= '?cache=' . time();
    } else {
        $image_url .= '&cache=' . time();
    }

    // Download image to temp location with longer timeout
    $temp_file = download_url($image_url, 45);

    if (is_wp_error($temp_file)) {
        error_log('Failed to download image: ' . $temp_file->get_error_message());
        return $temp_file;
    }

    error_log('Image downloaded successfully to: ' . $temp_file);

    // Check if file exists and has content
    if (!file_exists($temp_file) || filesize($temp_file) == 0) {
        error_log('Downloaded file is empty or does not exist');
        @unlink($temp_file);
        return new WP_Error('download_error', 'Downloaded file is empty');
    }

    // Get file extension from the temp file
    $file_type = wp_check_filetype($temp_file);
    $extension = !empty($file_type['ext']) ? $file_type['ext'] : 'jpg';

    error_log('File type detected: ' . $extension);

    // Prepare file array for sideload
    $file_array = array(
        'name'     => 'ai-image-' . sanitize_title($title) . '-' . time() . '.' . $extension,
        'tmp_name' => $temp_file
    );

    error_log('Uploading file: ' . $file_array['name']);

    // Sideload the image (this will move it to the uploads folder)
    $attachment_id = media_handle_sideload($file_array, $post_id, $title);

    // Check for errors
    if (is_wp_error($attachment_id)) {
        @unlink($temp_file); // Clean up
        error_log('Failed to sideload image: ' . $attachment_id->get_error_message());
        return $attachment_id;
    }

    error_log('Image uploaded successfully! Attachment ID: ' . $attachment_id);

    return $attachment_id;
}