<?php 
function aicg_create_post($title, $content, $post_type, $image_url = '') {
    // Upload image and attach to post if URL provided
    $image_id = '';
    if (!empty($image_url)) {
        $image_id = aicg_upload_image_from_url($image_url, $title);
    }

    // Create the post
    $post_data = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_type'     => $post_type,
        'post_author'   => get_current_user_id()
    );

    $post_id = wp_insert_post($post_data);

    // Set featured image if available
    if ($post_id && !empty($image_id) && !is_wp_error($image_id)) {
        set_post_thumbnail($post_id, $image_id);
    }

    return $post_id;
}

function aicg_upload_image_from_url($image_url, $title = '') {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Download image to temp location
    $temp_file = download_url($image_url);

    if (is_wp_error($temp_file)) {
        error_log('Failed to download image: ' . $temp_file->get_error_message());
        return '';
    }

    // Prepare file array
    $file_array = array(
        'name'     => 'ai-generated-' . sanitize_title($title) . '-' . time() . '.jpg',
        'tmp_name' => $temp_file
    );

    // Upload to media library
    $attachment_id = media_handle_sideload($file_array, 0, $title);

    // Clean up temp file
    if (is_wp_error($attachment_id)) {
        @unlink($temp_file);
        error_log('Failed to upload image to media library: ' . $attachment_id->get_error_message());
        return '';
    }

    return $attachment_id;
}