<?php 
function aicg_create_post($title, $content, $post_type, $image_url = '') {
    // تنزيل الصورة وربطها بالمنشور
    $image_id = '';
    if (!empty($image_url)) {
        $image_id = aicg_upload_image_from_url($image_url);
    }

    // إنشاء المنشور
    $post_data = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_type'     => $post_type,
        'meta_input'    => array('_thumbnail_id' => $image_id)
    );

    $post_id = wp_insert_post($post_data);

    return $post_id;
}

function aicg_upload_image_from_url($image_url) {
    $file_array = array();
    $file_array['name'] = basename($image_url);
    $file_array['tmp_name'] = download_url($image_url);

    if (is_wp_error($file_array['tmp_name'])) {
        return '';
    }

    $attachment_id = media_handle_sideload($file_array, 0);
    if (is_wp_error($attachment_id)) {
        return '';
    }

    return $attachment_id;
}