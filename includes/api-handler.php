<?php 
function aicg_generate_content($keyword, $post_type) {
    // Get API Key from settings
    $api_key = get_option('aicg_openrouter_api_key', '');
    
    // Check if API key is set
    if (empty($api_key)) {
        return new WP_Error('api_error', 'OpenRouter API key is not configured. Please go to AI Generator > API Settings to add your API key.');
    }
    
    // Get selected model from settings
    $model = get_option('aicg_ai_model', 'meta-llama/llama-3.2-3b-instruct:free');
    
    // Prepare data to send to API
    $data = array(
        'model' => $model,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => "Write a detailed, well-structured article about '$keyword' for a WordPress $post_type. Include an engaging introduction, detailed body content with multiple paragraphs, and a conclusion. Make it informative and reader-friendly."
            )
        ),
        'max_tokens' => 1000,
        'temperature' => 0.7
    );

    // Prepare request
    $args = array(
        'body' => json_encode($data),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
            'HTTP-Referer' => home_url(),
            'X-Title' => get_bloginfo('name')
        ),
        'timeout' => 60
    );

    // Send request
    $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $args);

    // Check for HTTP errors
    if (is_wp_error($response)) {
        error_log('API Connection Error: ' . $response->get_error_message());
        return new WP_Error('api_error', 'Failed to connect to API: ' . $response->get_error_message());
    }

    // Get response code
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    error_log('API Response Code: ' . $response_code);
    error_log('API Response Body: ' . $response_body);

    // Check for non-200 response codes
    if ($response_code !== 200) {
        $error_message = 'API returned error code: ' . $response_code;
        
        $body = json_decode($response_body, true);
        if (isset($body['error']['message'])) {
            $error_message .= ' - ' . $body['error']['message'];
        }
        
        // Add helpful message for 401 errors
        if ($response_code === 401) {
            $error_message .= '. Please check your API key in AI Generator > API Settings.';
        }
        
        error_log('API Error Response: ' . $error_message);
        return new WP_Error('api_error', $error_message);
    }

    // Decode JSON response
    $body = json_decode($response_body, true);
    
    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Decode Error: ' . json_last_error_msg());
        return new WP_Error('api_error', 'Failed to decode API response');
    }

    // Check if response has the expected structure
    if (isset($body['choices']) && is_array($body['choices']) && !empty($body['choices'])) {
        if (isset($body['choices'][0]['message']['content'])) {
            return $body['choices'][0]['message']['content'];
        }
    }
    
    // If we got here, the response structure is unexpected
    error_log('Unexpected API Response Structure: ' . print_r($body, true));
    
    // Check for specific error messages from OpenRouter
    if (isset($body['error'])) {
        $error_msg = isset($body['error']['message']) ? $body['error']['message'] : 'Unknown API error';
        return new WP_Error('api_error', 'API Error: ' . $error_msg);
    }
    
    return new WP_Error('api_error', 'API response does not contain valid content. Please check your API key and model availability.');
}

function aicg_generate_image($keyword) {
    error_log('Generating image for keyword: ' . $keyword);
    
    // Try multiple image sources in order of preference
    
    // 1. First try Pexels API if key is available (best quality, keyword-based)
    $pexels_api_key = get_option('aicg_pexels_api_key', '');
    
    if (!empty($pexels_api_key)) {
        $search_query = urlencode($keyword);
        $api_url = "https://api.pexels.com/v1/search?query={$search_query}&per_page=1&orientation=landscape";
        
        $response = wp_remote_get($api_url, array(
            'headers' => array(
                'Authorization' => $pexels_api_key
            ),
            'timeout' => 30
        ));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            if (isset($body['photos'][0]['src']['large'])) {
                error_log('Using Pexels image: ' . $body['photos'][0]['src']['large']);
                return $body['photos'][0]['src']['large'];
            }
        }
    }
    
    // 2. Try Pixabay API (free, no key required for basic usage)
    $search_query = urlencode($keyword);
    $pixabay_url = "https://pixabay.com/api/?key=46835086-1e5f33e0bddf4d238db4f9a18&q={$search_query}&image_type=photo&per_page=3&safesearch=true";
    
    $response = wp_remote_get($pixabay_url, array('timeout' => 30));
    
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['hits']) && !empty($body['hits'])) {
            $image_url = $body['hits'][0]['largeImageURL'];
            error_log('Using Pixabay image: ' . $image_url);
            return $image_url;
        }
    }
    
    // 3. Fallback to Unsplash API (keyword-based, free)
    $search_query = urlencode($keyword);
    $unsplash_url = "https://api.unsplash.com/photos/random?query={$search_query}&client_id=fXH5TScF9Y2xF5pHPuGSgPHAVh96vE2wJKzQQxJMSXU&orientation=landscape";
    
    $response = wp_remote_get($unsplash_url, array('timeout' => 30));
    
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['urls']['regular'])) {
            error_log('Using Unsplash image: ' . $body['urls']['regular']);
            return $body['urls']['regular'];
        }
    }
    
    // 4. Final fallback - use a generic placeholder
    error_log('All image sources failed, using placeholder');
    return 'https://via.placeholder.com/1200x630/cccccc/666666?text=' . urlencode($keyword);
}