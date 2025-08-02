# Video Functionality for Fastify

This directory contains video assets and documentation for the kitchen live stream functionality.

## Features

- **YouTube Shorts Integration**: Embed YouTube videos as kitchen live streams
- **Local Video Support**: Fallback to local video files
- **Video Controls**: Play/pause and switch between video sources
- **Responsive Design**: Works on mobile and desktop
- **Dark Mode Support**: Videos adapt to light/dark themes

## File Structure

```
public/videos/
├── README.md                    # This file
├── kitchen-status.html          # Video placeholder page
├── kitchen-status.mp4           # Local video file (add your own)
└── kitchen-status.webm          # WebM format (optional)
```

## Setup Instructions

### 1. YouTube Shorts (Recommended)

The app comes with sample YouTube Shorts URLs. To use your own:

1. Find a YouTube Shorts video you want to use
2. Get the video ID from the URL
3. Update the URLs in the JavaScript files:
   - `resources/views/orders/show.blade.php`
   - `resources/views/menu/index.blade.php`

Replace the `youtubeShorts` array with your video URLs:

```javascript
const youtubeShorts = [
    'https://www.youtube.com/embed/YOUR_VIDEO_ID?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1',
    // Add more videos as needed
];
```

### 2. Local Video Files

To use local video files:

1. **Download a sample video**:
   - Visit `/videos/kitchen-status.html` in your browser
   - Click the download links for sample MP4 files
   - Or use your own video file

2. **Add the video file**:
   - Rename your video to `kitchen-status.mp4`
   - Place it in the `public/videos/` directory
   - The video will automatically load when users switch to local video mode

3. **Optional WebM format**:
   - For better browser compatibility, also add a WebM version
   - Name it `kitchen-status.webm`

### 3. Video Requirements

- **Format**: MP4 (H.264) or WebM
- **Resolution**: 720p or 1080p recommended
- **Duration**: 30 seconds to 5 minutes for kitchen status
- **Size**: Keep under 10MB for fast loading
- **Content**: Kitchen activities, food preparation, cooking processes

## Usage

### For Users

1. **On Menu Page**: 
   - Video appears below stories section
   - Click play/pause button to control video
   - Click switch button to change video source

2. **On Order Status Page**:
   - Video appears in "Live Kitchen Status" section
   - Same controls as menu page
   - Shows kitchen activity while order is being prepared

### For Developers

The video functionality is implemented in:

- **Order Status**: `resources/views/orders/show.blade.php`
- **Menu Page**: `resources/views/menu/index.blade.php`

Key functions:
- `initializeVideoControls()` - Order page video controls
- `initializeMenuVideoControls()` - Menu page video controls

## Customization

### Change Video Sources

Update the `youtubeShorts` array in both JavaScript files:

```javascript
const youtubeShorts = [
    'https://www.youtube.com/embed/VIDEO_ID_1?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1',
    'https://www.youtube.com/embed/VIDEO_ID_2?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1',
    // Add more videos
];
```

### Modify Video Placeholder

The placeholder appears when videos fail to load. Customize it in the HTML:

```html
<div id="videoPlaceholder" class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-orange-100 to-yellow-100">
    <!-- Customize this content -->
</div>
```

### Add More Video Controls

Extend the video controls by adding more buttons:

```html
<div class="absolute top-2 right-2 flex gap-2">
    <button id="toggleVideo" class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-full">
        <i class="fas fa-play"></i>
    </button>
    <button id="switchVideo" class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-full">
        <i class="fas fa-sync-alt"></i>
    </button>
    <!-- Add more buttons here -->
</div>
```

## Troubleshooting

### Video Not Loading

1. **Check file permissions**: Ensure video files are readable
2. **Verify file paths**: Confirm files are in `public/videos/` directory
3. **Check file format**: Use MP4 (H.264) or WebM format
4. **File size**: Keep videos under 10MB for fast loading

### YouTube Videos Not Working

1. **Check video ID**: Ensure YouTube video ID is correct
2. **Video privacy**: Make sure YouTube video is public or unlisted
3. **Embedding enabled**: YouTube video must allow embedding
4. **Network issues**: Check internet connection

### Controls Not Working

1. **JavaScript errors**: Check browser console for errors
2. **Element IDs**: Ensure all video element IDs match JavaScript
3. **Font Awesome**: Make sure Font Awesome icons are loaded

## Best Practices

1. **Use short videos**: 30 seconds to 2 minutes for kitchen status
2. **Optimize file size**: Compress videos for faster loading
3. **Provide fallbacks**: Always have placeholder content
4. **Test on mobile**: Ensure videos work on mobile devices
5. **Update regularly**: Change videos periodically to keep content fresh

## Sample Videos

For testing, you can use these sample video URLs:

- **Sample MP4 (1MB)**: https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4
- **Sample MP4 (2MB)**: https://www.learningcontainer.com/wp-content/uploads/2020/05/sample-mp4-file.mp4

Download these files and rename them to `kitchen-status.mp4` for local testing. 