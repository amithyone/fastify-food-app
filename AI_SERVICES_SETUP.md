# AI Image Recognition Services Setup Guide

This guide explains how to set up multiple AI services for better food image recognition accuracy.

## 🎯 **Why Multiple Services?**

The system now supports multiple AI services to improve recognition accuracy:

1. **Google Vision API** - Most accurate for general image recognition
2. **Azure Computer Vision** - Good for detailed object detection
3. **LogMeal API** - Specialized in food recognition
4. **Local Analysis** - Fallback when external services are unavailable

## 🔧 **Setup Instructions**

### **1. Google Vision API (Recommended)**

**Step 1: Create Google Cloud Project**
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable the Vision API

**Step 2: Create API Key**
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "API Key"
3. Copy the API key

**Step 3: Add to Environment**
```bash
# Add to your .env file
GOOGLE_VISION_API_KEY=your_google_vision_api_key_here
```

### **2. Azure Computer Vision**

**Step 1: Create Azure Account**
1. Go to [Azure Portal](https://portal.azure.com/)
2. Create a Computer Vision resource
3. Get the API key and endpoint

**Step 2: Add to Environment**
```bash
# Add to your .env file
AZURE_VISION_API_KEY=your_azure_vision_api_key_here
AZURE_VISION_ENDPOINT=https://your-resource-name.cognitiveservices.azure.com/
```

### **3. LogMeal API (Food-Specific)**

**Step 1: Sign Up**
1. Go to [LogMeal API](https://logmeal.es/api)
2. Create an account and get API key

**Step 2: Add to Environment**
```bash
# Add to your .env file
FOOD_RECOGNITION_API_KEY=your_logmeal_api_key_here
```

## 🚀 **How It Works**

### **Service Priority Order:**
1. **Google Vision API** (90% confidence) - Best for general recognition
2. **Azure Computer Vision** (85% confidence) - Good for detailed analysis
3. **LogMeal API** (80% confidence) - Specialized in food
4. **Local Analysis** (75% confidence) - Fallback option

### **Recognition Process:**
1. Upload image to AI modal
2. System tries each service in order
3. Combines results and selects best match
4. Returns detailed food information

### **Features:**
- ✅ **Multi-service support** - Tries multiple AI services
- ✅ **Fallback system** - Always returns a result
- ✅ **Nigerian food support** - Recognizes local dishes
- ✅ **Detailed information** - Ingredients, allergens, etc.
- ✅ **Learning system** - Improves with user corrections

## 📊 **Accuracy Comparison**

| Service | General Food | Nigerian Food | Speed | Cost |
|---------|-------------|---------------|-------|------|
| Google Vision | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | Fast | Low |
| Azure Vision | ⭐⭐⭐⭐ | ⭐⭐⭐ | Fast | Low |
| LogMeal | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | Medium | Medium |
| Local Analysis | ⭐⭐ | ⭐⭐ | Fast | Free |

## 🔍 **Nigerian Food Recognition**

The system is optimized for Nigerian cuisine:

### **Supported Dishes:**
- Jollof Rice
- Egusi Soup
- Suya
- Amala & Ewedu
- Pepper Soup
- Banga Soup
- Moi Moi
- Zobo Drink
- Kunu Drink

### **Recognition Keywords:**
- `jollof`, `egusi`, `suya`, `amala`, `pepper soup`
- `banga`, `moi moi`, `zobo`, `kunu`, `pounded yam`

## 💰 **Cost Estimation**

### **Google Vision API:**
- First 1,000 requests/month: Free
- Additional requests: $1.50 per 1,000

### **Azure Computer Vision:**
- First 5,000 transactions/month: Free
- Additional transactions: $1.00 per 1,000

### **LogMeal API:**
- Free tier: 100 requests/month
- Paid plans: $10-50/month

## 🛠️ **Testing**

### **Test with Sample Images:**
1. Upload a food image
2. Check console for service usage
3. Verify recognition accuracy
4. Test with Nigerian dishes

### **Monitor Logs:**
```bash
tail -f storage/logs/laravel.log
```

Look for:
- `AI food recognition successful`
- `services_used` count
- `best_result` details

## 🔧 **Troubleshooting**

### **Common Issues:**

**1. API Key Errors**
```bash
# Check if API keys are set
echo $GOOGLE_VISION_API_KEY
echo $AZURE_VISION_API_KEY
```

**2. Service Unavailable**
- System will fallback to local analysis
- Check internet connection
- Verify API quotas

**3. Poor Recognition**
- Try different image angles
- Ensure good lighting
- Use clear, focused images

## 📈 **Performance Tips**

1. **Image Quality:**
   - Use high-resolution images
   - Ensure good lighting
   - Focus on the food item

2. **Service Selection:**
   - Google Vision for general recognition
   - LogMeal for food-specific dishes
   - Azure for detailed analysis

3. **Caching:**
   - Results are cached for similar images
   - Learning system improves over time

## 🎯 **Next Steps**

1. **Set up Google Vision API** (recommended)
2. **Test with sample images**
3. **Monitor recognition accuracy**
4. **Add more Nigerian dishes** to recognition database
5. **Implement user feedback system**

## 📞 **Support**

For issues with AI recognition:
1. Check API key configuration
2. Verify service quotas
3. Test with different images
4. Review error logs

The multi-service approach ensures reliable food recognition with high accuracy! 🎉 