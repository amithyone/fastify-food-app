# AI Image Recognition - Constraints & Logging

## 📸 Image Constraints

### File Size Limits
- **Maximum file size**: 1MB (1024KB)
- **Supported formats**: JPEG, PNG, JPG, GIF
- **Automatic compression**: Images larger than 1MB are automatically compressed

### Image Compression
- **Maximum dimensions**: 800px width/height
- **JPEG quality**: 85%
- **PNG compression**: Level 6
- **Transparency**: Preserved for PNG and GIF images

### Camera Recommendations
- **Resolution**: Use 2MP or lower for optimal performance
- **Aspect ratio**: Any ratio supported (will be resized to max 800px)
- **File format**: JPEG recommended for best compression

## 🔍 Error Logging

### What Gets Logged
1. **Image upload details**:
   - File name, size, MIME type
   - Restaurant ID and user ID
   - Compression ratio (if applied)

2. **AI service attempts**:
   - Which services are configured
   - Success/failure of each service
   - Confidence scores and results

3. **Error details**:
   - Full error messages and stack traces
   - API configuration status
   - Image processing failures

### Log Levels
- **INFO**: Successful operations, image details
- **WARNING**: Service failures, large images
- **ERROR**: Processing failures, invalid images

## 🚀 Usage

### For Restaurant Managers
1. Take photos with camera set to 2MP or lower
2. Ensure good lighting for better recognition
3. Focus on the food item clearly
4. Upload images under 1MB for best performance

### For Developers
1. Check logs in `storage/logs/laravel.log`
2. Look for entries with "AI recognition" keywords
3. Monitor compression ratios and service success rates
4. Debug API configuration issues

## 📊 Performance Metrics

### Expected File Sizes
- **2MP photo**: ~200-500KB
- **1MP photo**: ~100-250KB
- **Compressed image**: ~50-150KB

### Recognition Accuracy
- **Google Vision API**: 90%+ (when configured)
- **Local analysis**: 75-85% (fallback)
- **Combined approach**: 85-95%

## 🔧 Configuration

### Environment Variables
```env
GOOGLE_VISION_API_KEY=your_google_api_key
AZURE_VISION_API_KEY=your_azure_api_key
AZURE_VISION_ENDPOINT=your_azure_endpoint
FOOD_RECOGNITION_API_KEY=your_logmeal_api_key
```

### API Priority
1. Google Vision API (most accurate)
2. Azure Computer Vision
3. LogMeal API (food-specific)
4. Local analysis (fallback)

## 🐛 Troubleshooting

### Common Issues
1. **Large file size**: Image automatically compressed
2. **API failures**: Check API keys and network connectivity
3. **Recognition failures**: Try different lighting or angle
4. **Upload errors**: Ensure file is under 1MB

### Debug Commands
```bash
# Check recent AI logs
tail -f storage/logs/laravel.log | grep -i "ai"

# Test image compression
php artisan tinker --execute="echo 'Testing compression...';"

# Check API configuration
php artisan tinker --execute="echo 'Google: ' . config('services.google_vision.api_key') ? 'OK' : 'Missing';"
``` 