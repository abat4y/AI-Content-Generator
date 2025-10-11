# AI Content Generator for WordPress

Generate high-quality articles and posts using artificial intelligence, complete with featured images.

## Features

✅ **AI-Powered Content Generation** - Create detailed, well-structured articles  
✅ **Automatic Featured Images** - Download and attach images to posts automatically  
✅ **Multiple Post Types** - Works with all WordPress post types  
✅ **Bulk Creation** - Generate up to 10 posts at once  
✅ **AJAX Interface** - Smooth, no-reload experience with loading animations  
✅ **API Settings Page** - Securely manage your API keys  
✅ **Modern UI** - Clean, professional WordPress-style interface  

## Installation

1. Upload the `ai-content-generator` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **AI Generator > API Settings** and add your OpenRouter API key
4. Start generating content!

## Configuration

### Step 1: Get OpenRouter API Key

1. Visit [OpenRouter.ai](https://openrouter.ai)
2. Sign up or log in
3. Go to [API Keys](https://openrouter.ai/keys)
4. Create a new API key
5. Copy the key (starts with `sk-or-v1-...`)

### Step 2: Configure Plugin

1. In WordPress admin, go to **AI Generator > API Settings**
2. Paste your OpenRouter API key
3. Select an AI model (default: Llama 3.2 3B - Free)
4. (Optional) Add Pexels API key for better quality images
5. Click **Save Settings**

### Step 3: Optional - Pexels API (Better Images)

1. Visit [Pexels API](https://www.pexels.com/api/)
2. Sign up for free
3. Get your API key
4. Add it in **AI Generator > API Settings**

## Usage

1. Go to **AI Generator** in WordPress admin
2. Enter a keyword or topic
3. Select post type (Post, Page, or custom post types)
4. Choose number of posts (1-10)
5. Check/uncheck "Generate Featured Image"
6. Click **Generate Content**
7. Wait for the AI to create your content
8. Edit the generated posts as needed

## File Structure

```
ai-content-generator/
├── ai-content-generator.php    # Main plugin file
├── README.md                    # This file
├── assets/
│   ├── css/
│   │   └── style.css           # Styling
│   └── js/
│       └── script.js           # AJAX functionality
└── includes/
    ├── api-handler.php         # API communication
    ├── api-settings.php        # Settings page
    ├── post-creator.php        # Post creation & image handling
    └── settings-page.php       # Main form page
```

## How It Works

### Content Generation
- Uses OpenRouter API to access various AI models
- Supports both free and paid models
- Generates detailed, structured articles

### Image Handling
- **With Pexels API**: Gets premium curated stock photos (best quality)
- **Default (Pixabay)**: Free, keyword-based images automatically
- **Backup (Unsplash)**: Additional free stock photography source
- **Final Fallback**: Placeholder image if all sources fail
- Downloads images to WordPress media library
- Automatically sets as featured image
- All images are keyword-related and relevant to content

## Troubleshooting

### Featured Image Not Appearing

Check your `wp-content/debug.log` for error messages. Enable WordPress debugging by adding to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Common solutions:
- Ensure your server can download external images
- Check file permissions on `/wp-content/uploads/`
- Verify the image URL is accessible
- Try adding a Pexels API key for more reliable images

### API Errors

**401 - User not found**: Your OpenRouter API key is invalid
- Get a new key from [OpenRouter.ai](https://openrouter.ai/keys)
- Update it in **AI Generator > API Settings**

**Rate Limited**: You've exceeded the free tier limits
- Wait for the rate limit to reset
- Add credits to your OpenRouter account
- Use a paid model for higher limits

### Content Not Generated

- Check your API key is correctly entered
- Verify the selected model is available
- Check your WordPress error log
- Ensure your server can make external HTTP requests

## Free Tier Limits

**OpenRouter Free Tier**:
- $1 free credit to test
- Free models available (with rate limits)
- Suitable for testing and low-volume use

**Pexels API**:
- Completely free
- 200 requests per hour
- Unlimited requests per month

## Recommended Models

### Free Models (No Cost)
- **Llama 3.2 3B** (Recommended) - Fast and balanced
- **Google Gemma 2 9B** - Higher quality
- **Mistral 7B** - Good for technical content

### Paid Models (Better Quality)
- **GPT-4 Turbo** - Best quality, higher cost
- **Claude 3.5 Sonnet** - Excellent for long-form content
- **GPT-3.5 Turbo** - Good balance of quality and cost

## Support

For issues and feature requests, please check:
- WordPress error log (`wp-content/debug.log`)
- OpenRouter API status
- Server configuration (PHP version, file permissions)

## Version

**Current Version**: 2.0

### Changelog

**Version 2.0**
- Added AJAX functionality
- Added loading animations
- Added API settings page
- Added support for all post types
- Added bulk post creation
- Improved featured image handling
- Added Pexels integration
- Enhanced error handling
- Modern UI redesign

**Version 1.0**
- Initial release

## Credits

**Author**: Sameh Helal  
**Website**: https://sameh-helal.abatchy.site/

**AI Services**:
- [OpenRouter](https://openrouter.ai) - AI content generation
- [Pexels](https://www.pexels.com) - Stock photos
- [Lorem Picsum](https://picsum.photos) - Fallback images

## License

This plugin is provided as-is for WordPress users. Free to use and modify for your projects.