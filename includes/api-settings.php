<?php
// Register settings
function aicg_register_settings() {
    register_setting('aicg_settings', 'aicg_openrouter_api_key');
    register_setting('aicg_settings', 'aicg_ai_model');
    register_setting('aicg_settings', 'aicg_pexels_api_key');
}
add_action('admin_init', 'aicg_register_settings');

// Add submenu for API settings
function aicg_add_api_settings_page() {
    add_submenu_page(
        'ai-content-generator',
        'API Settings',
        'API Settings',
        'manage_options',
        'ai-content-generator-settings',
        'aicg_render_api_settings_page'
    );
}
add_action('admin_menu', 'aicg_add_api_settings_page', 11);

// Render API settings page
function aicg_render_api_settings_page() {
    // Save settings
    if (isset($_POST['aicg_save_settings']) && wp_verify_nonce($_POST['aicg_settings_nonce'], 'aicg_save_settings_action')) {
        update_option('aicg_openrouter_api_key', sanitize_text_field($_POST['aicg_openrouter_api_key']));
        update_option('aicg_ai_model', sanitize_text_field($_POST['aicg_ai_model']));
        update_option('aicg_pexels_api_key', sanitize_text_field($_POST['aicg_pexels_api_key']));
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    $api_key = get_option('aicg_openrouter_api_key', '');
    $selected_model = get_option('aicg_ai_model', 'meta-llama/llama-3.2-3b-instruct:free');
    $pexels_api_key = get_option('aicg_pexels_api_key', '');
    ?>
    <div class="wrap aicg-wrap">
        <h1><span class="dashicons dashicons-admin-settings"></span> API Settings</h1>
        <p class="aicg-description">Configure your OpenRouter API credentials</p>

        <div class="aicg-container" style="grid-template-columns: 1fr;">
            <div class="aicg-form-card">
                <form method="post" action="">
                    <?php wp_nonce_field('aicg_save_settings_action', 'aicg_settings_nonce'); ?>

                    <div class="aicg-form-group">
                        <label for="aicg_openrouter_api_key">
                            <span class="dashicons dashicons-admin-network"></span>
                            OpenRouter API Key
                        </label>
                        <input type="text" 
                               id="aicg_openrouter_api_key" 
                               name="aicg_openrouter_api_key" 
                               value="<?php echo esc_attr($api_key); ?>" 
                               placeholder="sk-or-v1-xxxxxxxxxxxxxxxx"
                               required
                               style="font-family: monospace;">
                        <span class="aicg-hint">
                            Get your API key from <a href="https://openrouter.ai/keys" target="_blank">OpenRouter Dashboard</a>
                        </span>
                    </div>

                    <div class="aicg-form-group">
                        <label for="aicg_ai_model">
                            <span class="dashicons dashicons-editor-code"></span>
                            AI Model
                        </label>
                        <select id="aicg_ai_model" name="aicg_ai_model" required>
                            <optgroup label="Free Models">
                                <option value="meta-llama/llama-3.2-3b-instruct:free" <?php selected($selected_model, 'meta-llama/llama-3.2-3b-instruct:free'); ?>>
                                    Llama 3.2 3B (Recommended)
                                </option>
                                <option value="google/gemma-2-9b-it:free" <?php selected($selected_model, 'google/gemma-2-9b-it:free'); ?>>
                                    Google Gemma 2 9B
                                </option>
                                <option value="mistralai/mistral-7b-instruct:free" <?php selected($selected_model, 'mistralai/mistral-7b-instruct:free'); ?>>
                                    Mistral 7B Instruct
                                </option>
                                <option value="openchat/openchat-7b:free" <?php selected($selected_model, 'openchat/openchat-7b:free'); ?>>
                                    OpenChat 7B
                                </option>
                                <option value="nousresearch/hermes-3-llama-3.1-405b:free" <?php selected($selected_model, 'nousresearch/hermes-3-llama-3.1-405b:free'); ?>>
                                    Hermes 3 Llama 405B
                                </option>
                            </optgroup>
                            <optgroup label="Paid Models (Better Quality)">
                                <option value="anthropic/claude-3.5-sonnet" <?php selected($selected_model, 'anthropic/claude-3.5-sonnet'); ?>>
                                    Claude 3.5 Sonnet
                                </option>
                                <option value="openai/gpt-4-turbo" <?php selected($selected_model, 'openai/gpt-4-turbo'); ?>>
                                    GPT-4 Turbo
                                </option>
                                <option value="openai/gpt-3.5-turbo" <?php selected($selected_model, 'openai/gpt-3.5-turbo'); ?>>
                                    GPT-3.5 Turbo
                                </option>
                            </optgroup>
                        </select>
                        <span class="aicg-hint">
                            Choose a model. Free models have rate limits but work well for testing.
                        </span>
                    </div>

                    <hr style="margin: 30px 0; border: none; border-top: 1px solid #e8e9ea;">

                    <h3 style="margin-bottom: 20px; color: #1d2327;">
                        <span class="dashicons dashicons-format-image"></span>
                        Image Generation Settings (Optional)
                    </h3>

                    <div class="aicg-form-group">
                        <label for="aicg_pexels_api_key">
                            <span class="dashicons dashicons-camera"></span>
                            Pexels API Key (Optional)
                        </label>
                        <input type="text" 
                               id="aicg_pexels_api_key" 
                               name="aicg_pexels_api_key" 
                               value="<?php echo esc_attr($pexels_api_key); ?>" 
                               placeholder="Optional - For premium quality images"
                               style="font-family: monospace;">
                        <span class="aicg-hint">
                            Optional: Get free API key from <a href="https://www.pexels.com/api/" target="_blank">Pexels API</a> for highest quality images.
                        </span>
                    </div>

                    <div class="aicg-alert aicg-alert-success">
                        <span class="dashicons dashicons-images-alt2"></span>
                        <div>
                            <strong>Image Sources (in order of preference):</strong>
                            <ol style="margin: 10px 0 0 20px; line-height: 1.8;">
                                <li><strong>Pexels</strong> (if API key provided) - Premium curated photos</li>
                                <li><strong>Pixabay</strong> (default) - Free keyword-based images</li>
                                <li><strong>Unsplash</strong> (backup) - Free stock photography</li>
                                <li><strong>Placeholder</strong> (last resort) - Generic placeholder if all fail</li>
                            </ol>
                            <p style="margin-top: 10px;"><strong>Note:</strong> Without a Pexels API key, the plugin will use Pixabay (free, keyword-based images).</p>
                        </div>
                    </div>

                    <div class="aicg-alert aicg-alert-info">
                        <span class="dashicons dashicons-info"></span>
                        <div>
                            <strong>How to get your API Key:</strong>
                            <ol style="margin: 10px 0 0 20px;">
                                <li>Visit <a href="https://openrouter.ai" target="_blank">OpenRouter.ai</a></li>
                                <li>Sign up or log in to your account</li>
                                <li>Go to the <a href="https://openrouter.ai/keys" target="_blank">API Keys</a> section</li>
                                <li>Create a new API key</li>
                                <li>Copy and paste it here</li>
                            </ol>
                            <p style="margin-top: 10px;"><strong>Note:</strong> Free tier includes $1 credit to test the API. For production use, consider adding credits to your account.</p>
                        </div>
                    </div>

                    <div class="aicg-form-actions">
                        <button type="submit" name="aicg_save_settings" class="button button-primary aicg-submit-btn">
                            <span class="dashicons dashicons-saved"></span>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .aicg-alert {
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .aicg-alert-info {
            background: #e5f5fa;
            border-left: 4px solid #00a0d2;
        }
        .aicg-alert .dashicons {
            color: #00a0d2;
            font-size: 20px;
            width: 20px;
            height: 20px;
            margin-top: 2px;
        }
        .aicg-alert strong {
            color: #1d2327;
        }
        .aicg-alert a {
            color: #00a0d2;
            text-decoration: none;
        }
        .aicg-alert a:hover {
            text-decoration: underline;
        }
    </style>
    <?php
}