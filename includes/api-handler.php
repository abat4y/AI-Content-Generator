<?php 
function aicg_generate_content($keyword, $post_type) {
    // API Key الخاصة بـ OpenRouter
    $api_key = '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';
    
    // البيانات التي سيتم إرسالها إلى API
    $data = array(
        'model' => 'qwen/qwen2.5-vl-32b-instruct:free', // اختيار النموذج (يمكنك تغييره حسب الحاجة)
        'messages' => array(
            array(
                'role' => 'user',
                'content' => "Write a detailed article about $keyword for a WordPress $post_type."
            )
        ),
        'max_tokens' => 500,
        'temperature' => 0.7
    );

    // إعداد الطلب
    $args = array(
        'body' => json_encode($data), // تحويل البيانات إلى JSON
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
            'HTTP-Referer' => home_url(), // Optional. Your site URL for rankings on OpenRouter.
            'X-Title' => get_bloginfo('name') // Optional. Your site title for rankings on OpenRouter.
        ),
        'timeout' => 30 // Increase timeout to 30 seconds
    );

    // إرسال الطلب
    $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $args);

    // التحقق من الاستجابة
    if (is_wp_error($response)) {
        error_log('API Error: ' . $response->get_error_message());
        return new WP_Error('api_error', 'Failed to generate content.');
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    error_log('API Response: ' . print_r($body, true)); // Log the API response

    // التحقق من وجود المفتاح "choices" واستخراج النص المولد
    if (isset($body['choices']) && is_array($body['choices']) && !empty($body['choices'])) {
        return $body['choices'][0]['message']['content']; // استخراج النص المولد
    } else {
        error_log('Error: API response does not contain valid choices.');
        return new WP_Error('api_error', 'API response does not contain valid choices.');
    }
}
// function aicg_generate_image($keyword) {
//     // API Key الخاصة بـ Stability AI أو أي خدمة أخرى
//     $api_key = 'sk-vUH1ylcFpEQ8oClRVdkJ8UUJ2fiqPuUKTdQ8u7GWPrtkG1TD';
    
//     // البيانات التي سيتم إرسالها إلى API
//     $data = array(
//         'text_prompts' => array(
//             array('text' => "A creative image representing $keyword")
//         ),
//         'steps' => 30,
//         'width' => 1024,
//         'height' => 1024,
//         'seed' => rand(1, 999999),
//         'cfg_scale' => 7,
//     );

//     // إعداد الطلب
//     $args = array(
//         'body' => json_encode($data),
//         'headers' => array(
//             'Content-Type' => 'application/json',
//             'Authorization' => 'Bearer ' . $api_key
//         )
//     );

//     // إرسال الطلب
//     $response = wp_remote_post('https://api.stability.ai/v1/generation/stable-diffusion-v1-5/text-to-image', $args);

//     // التحقق من الاستجابة
//     if (is_wp_error($response)) {
//         error_log('Image API Error: ' . $response->get_error_message());
//         return new WP_Error('api_error', 'Failed to generate image.');
//     }

//     $body = json_decode(wp_remote_retrieve_body($response), true);
//     error_log('Image API Response: ' . print_r($body, true)); // Log the API response

//     // التحقق من وجود المفتاح "artifacts"
//     if (!isset($body['artifacts']) || !is_array($body['artifacts']) || empty($body['artifacts'])) {
//         error_log('Error: Image API response does not contain valid artifacts.');
//         return new WP_Error('api_error', 'Image API response does not contain valid artifacts.');
//     }

//     // استخراج الصورة كملف Base64
//     return $body['artifacts'][0]['base64'];
// }