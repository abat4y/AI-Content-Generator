<?php 
function aicg_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>AI Content Generator</h1>
        <form id="aicg-form" method="post" action="">
            <?php wp_nonce_field('aicg_form_action', 'aicg_nonce'); // Add a nonce for security ?>

            <label for="keyword">Keyword:</label>
            <input type="text" id="keyword" name="keyword" required>

            <label for="post-type">Post Type:</label>
            <select id="post-type" name="post_type">
                <option value="post">Post</option>
                <option value="event">Event</option>
            </select>

            <button type="submit" class="button button-primary">Generate</button>
        </form>
    </div>
    <?php
}