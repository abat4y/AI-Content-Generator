<?php 
function aicg_generate_content($keyword, $post_type) {
    // API Key for OpenRouter
    $api_key = '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';
    
    // Prepare data to send to API
    $data = array(
        'model' => 'openai/gpt-4o', // Using a more reliable free model
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
    // Using Unsplash API as a free alternative for images
    // You can replace this with your preferred image generation service
    
    // Unsplash API (free, no API key required for basic usage)
    $search_query = urlencode($keyword);
    $unsplash_url = "https://source.unsplash.com/1200x630/?{$search_query}";
    
    return $unsplash_url;
    
    // Alternative: If you want to use Stability AI or another service
    // Uncomment and configure the code below:
    
    /*
    $api_key = 'YOUR_STABILITY_AI_API_KEY';
    
    $data = array(
        'text_prompts' => array(
            array('text' => "A high-quality, professional image representing: $keyword")
        ),
        'steps' => 30,
        'width' => 1024,
        'height' => 1024,
        'seed' => rand(1, 999999),
        'cfg_scale' => 7,
    );

    $args = array(
        'body' => json_encode($data),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ),
        'timeout' => 60
    );

    $response = wp_remote_post('https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image', $args);

    if (is_wp_error($response)) {
        error_log('Image API Error: ' . $response->get_error_message());
        return new WP_Error('api_error', 'Failed to generate image.');
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    error_log('Image API Response: ' . print_r($body, true));

    if (!isset($body['artifacts']) || !is_array($body['artifacts']) || empty($body['artifacts'])) {
        error_log('Error: Image API response does not contain valid artifacts.');
        return new WP_Error('api_error', 'Image API response does not contain valid artifacts.');
    }

    // Save base64 image to WordPress media library
    $image_data = base64_decode($body['artifacts'][0]['base64']);
    $upload_dir = wp_upload_dir();
    $filename = 'ai-generated-' . time() . '.png';
    $filepath = $upload_dir['path'] . '/' . $filename;
    
    file_put_contents($filepath, $image_data);
    
    return $upload_dir['url'] . '/' . $filename;
    */
}