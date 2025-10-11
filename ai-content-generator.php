<?php
/*
Plugin Name: AI Content Generator
Description: Create articles and events using artificial intelligence.
Version: 2.0
Author: Sameh Helal
Author URI: https://sameh-helal.abatchy.site/
*/

// Load helper files
require_once plugin_dir_path(__FILE__) . 'includes/api-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-creator.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-settings.php';

// Create settings page
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

// AJAX handler for form submission
function aicg_handle_ajax_submission() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aicg_ajax_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
        return;
    }

    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Insufficient permissions.'));
        return;
    }

    // Get form data
    $keyword = sanitize_text_field($_POST['keyword']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $num_posts = intval($_POST['num_posts']);
    $generate_image = isset($_POST['generate_image']) ? true : false;

    // Validate input
    if (empty($keyword)) {
        wp_send_json_error(array('message' => 'Keyword is required.'));
        return;
    }

    if ($num_posts < 1 || $num_posts > 10) {
        wp_send_json_error(array('message' => 'Number of posts must be between 1 and 10.'));
        return;
    }

    $created_posts = array();
    $errors = array();

    // Generate multiple posts
    for ($i = 0; $i < $num_posts; $i++) {
        // Add variation to keyword for multiple posts
        $varied_keyword = $num_posts > 1 ? "$keyword (variation " . ($i + 1) . ")" : $keyword;
        
        // Generate content
        $content = aicg_generate_content($varied_keyword, $post_type);

        if (is_wp_error($content)) {
            $errors[] = "Post " . ($i + 1) . ": " . $content->get_error_message();
            continue;
        }

        // Generate image if requested
        $image_url = '';
        if ($generate_image) {
            $image_url = aicg_generate_image($keyword);
            if (is_wp_error($image_url)) {
                $image_url = ''; // Continue without image if generation fails
            }
        }

        // Create post
        $post_id = aicg_create_post($varied_keyword, $content, $post_type, $image_url);

        if ($post_id) {
            $created_posts[] = array(
                'id' => $post_id,
                'title' => $varied_keyword,
                'edit_link' => get_edit_post_link($post_id, 'raw')
            );
        } else {
            $errors[] = "Failed to create post " . ($i + 1);
        }
    }

    // Send response
    if (!empty($created_posts)) {
        wp_send_json_success(array(
            'message' => count($created_posts) . ' post(s) created successfully!',
            'posts' => $created_posts,
            'errors' => $errors
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'Failed to create posts.',
            'errors' => $errors
        ));
    }
}
add_action('wp_ajax_aicg_generate_posts', 'aicg_handle_ajax_submission');

// Load CSS and JS
function aicg_enqueue_scripts($hook) {
    if ($hook != 'toplevel_page_ai-content-generator') {
        return;
    }
    wp_enqueue_style('aicg-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '2.0');
    wp_enqueue_script('aicg-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '2.0', true);
    
    // Pass AJAX URL and nonce to JavaScript
    wp_localize_script('aicg-script', 'aicg_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('aicg_ajax_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'aicg_enqueue_scripts');