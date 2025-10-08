<?php 
function aicg_render_settings_page() {
    // Get all registered post types
    $post_types = get_post_types(array('public' => true), 'objects');
    ?>
    <div class="wrap aicg-wrap">
        <h1><span class="dashicons dashicons-admin-generic"></span> AI Content Generator</h1>
        <p class="aicg-description">Generate high-quality content using artificial intelligence</p>
        
        <div class="aicg-container">
            <div class="aicg-form-card">
                <form id="aicg-form">
                    <div class="aicg-form-group">
                        <label for="keyword">
                            <span class="dashicons dashicons-search"></span>
                            Keyword / Topic
                        </label>
                        <input type="text" id="keyword" name="keyword" placeholder="Enter your topic or keyword" required>
                        <span class="aicg-hint">Enter the main topic for your content</span>
                    </div>

                    <div class="aicg-form-group">
                        <label for="post-type">
                            <span class="dashicons dashicons-admin-post"></span>
                            Post Type
                        </label>
                        <select id="post-type" name="post_type" required>
                            <?php foreach ($post_types as $post_type): ?>
                                <option value="<?php echo esc_attr($post_type->name); ?>">
                                    <?php echo esc_html($post_type->labels->singular_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="aicg-hint">Select the type of content to create</span>
                    </div>

                    <div class="aicg-form-group">
                        <label for="num-posts">
                            <span class="dashicons dashicons-admin-page"></span>
                            Number of Posts
                        </label>
                        <input type="number" id="num-posts" name="num_posts" min="1" max="10" value="1" required>
                        <span class="aicg-hint">How many posts to create (1-10)</span>
                    </div>

                    <div class="aicg-form-group aicg-checkbox-group">
                        <label for="generate-image">
                            <input type="checkbox" id="generate-image" name="generate_image" checked>
                            <span class="dashicons dashicons-format-image"></span>
                            Generate Featured Image
                            <span class="aicg-hint-inline">AI will create a featured image for each post</span>
                        </label>
                    </div>

                    <div class="aicg-form-actions">
                        <button type="submit" class="button button-primary aicg-submit-btn">
                            <span class="dashicons dashicons-update"></span>
                            Generate Content
                        </button>
                    </div>
                </form>

                <!-- Loader -->
                <div id="aicg-loader" class="aicg-loader" style="display: none;">
                    <div class="aicg-spinner"></div>
                    <p>Generating your content with AI...</p>
                    <p class="aicg-loader-subtext">This may take a few moments</p>
                </div>

                <!-- Response Messages -->
                <div id="aicg-response" class="aicg-response"></div>
            </div>

            <!-- Info Card -->
            <div class="aicg-info-card">
                <h3><span class="dashicons dashicons-info"></span> How It Works</h3>
                <ul>
                    <li><span class="dashicons dashicons-yes"></span> Enter a keyword or topic</li>
                    <li><span class="dashicons dashicons-yes"></span> Select your desired post type</li>
                    <li><span class="dashicons dashicons-yes"></span> Choose number of posts</li>
                    <li><span class="dashicons dashicons-yes"></span> Optionally generate featured images</li>
                    <li><span class="dashicons dashicons-yes"></span> Click generate and wait</li>
                </ul>

                <div class="aicg-tips">
                    <h4><span class="dashicons dashicons-lightbulb"></span> Tips</h4>
                    <p>• Be specific with your keywords for better results</p>
                    <p>• Creating multiple posts may take longer</p>
                    <p>• Review and edit generated content before publishing</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}