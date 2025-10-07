<?php
/*
Plugin Name: AI Content Generator
Description: Create articles and events using artificial intelligence.
Version: 1.0
Author: Sameh Helal
Author URI: https://sameh-helal.abatchy.site/
*/

// تحميل الملفات المساعدة
require_once plugin_dir_path(__FILE__) . 'includes/api-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-creator.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-page.php';

// إنشاء صفحة الإعدادات
function aicg_add_settings_page() {
    add_menu_page(
        'AI Content Generator', 
        'AI Generator', 
        'manage_options', 
        'ai-content-generator', 
        'aicg_render_settings_page',
        'dashicons-admin-generic',
        6
    );
}
add_action('admin_menu', 'aicg_add_settings_page');
add_action('admin_init', 'aicg_handle_form_submission');

function aicg_handle_form_submission() {
    // Check if the form was submitted
    if (isset($_POST['aicg_nonce']) && wp_verify_nonce($_POST['aicg_nonce'], 'aicg_form_action')) {
        // Get form data
        $keyword = sanitize_text_field($_POST['keyword']);
        $post_type = sanitize_text_field($_POST['post_type']);

        // Debugging: Log the received data
        error_log('Form Submitted - Keyword: ' . $keyword . ', Post Type: ' . $post_type);

        // Validate input
        if (empty($keyword)) {
            error_log('Error: Keyword is required.');
            return;
        }

        // Generate content and create the post
        $content = aicg_generate_content($keyword, $post_type);
        //$image_url = aicg_generate_image($keyword);

        if (!is_wp_error($content)) {
            $post_id = aicg_create_post($keyword, $content, $post_type);

            if ($post_id) {
                error_log('Post Created Successfully - ID: ' . $post_id);
            } else {
                error_log('Error: Failed to create post.');
            }
        } else {
            error_log('Error: Failed to generate content.');
        }
    }
}
// تحميل CSS وJS
function aicg_enqueue_scripts($hook) {
    if ($hook != 'toplevel_page_ai-content-generator') {
        return;
    }
    wp_enqueue_style('aicg-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('aicg-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'aicg_enqueue_scripts');
