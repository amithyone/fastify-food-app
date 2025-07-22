# 📱 Twilio WhatsApp Setup Guide

## 🎯 **Complete Integration Guide**

This guide will help you set up real WhatsApp messaging using Twilio's WhatsApp API.

## 📋 **Prerequisites**

### **1. Twilio Account**
- ✅ **Sign up** at [twilio.com](https://twilio.com)
- ✅ **Verify your account** (email + phone)
- ✅ **Add payment method** (required for WhatsApp)

### **2. WhatsApp Business API**
- ✅ **Apply for WhatsApp Business API** through Twilio
- ✅ **Wait for approval** (usually 24-48 hours)
- ✅ **Get your WhatsApp number**

## 🔧 **Step-by-Step Setup**

### **Step 1: Get Twilio Credentials**

1. **Go to** [Twilio Console](https://console.twilio.com/)
2. **Find your credentials**:
   - **Account SID** (starts with `AC...`)
   - **Auth Token** (click "show" to reveal)

### **Step 2: Set Up WhatsApp Sandbox**

1. **Go to** [WhatsApp Sandbox](https://console.twilio.com/us1/develop/sms/manage/whatsapp-sandbox)
2. **Join the sandbox** by sending the code to `+14155238886`
3. **Note your WhatsApp number**: `whatsapp:+14155238886`

### **Step 3: Configure Environment Variables**

Add these to your `.env` file:

```env
# Twilio Configuration
TWILIO_ACCOUNT_SID=ACyour_account_sid_here
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886

# Enable Real WhatsApp
ENABLE_REAL_WHATSAPP=true
```

### **Step 4: Install Twilio SDK**

```bash
composer require twilio/sdk
```

## 🧪 **Testing the Integration**

### **Test with Sandbox**

1. **Join the sandbox**:
   - Send `join <your-sandbox-code>` to `+14155238886`
   - You'll receive a confirmation

2. **Test the API**:
   ```bash
   curl -X POST http://localhost:8000/api/phone/send-code \
     -H "Content-Type: application/json" \
     -H "X-CSRF-TOKEN: your_csrf_token" \
     -d '{"phone_number": "your_phone_number", "is_login": false}'
   ```

3. **Check response**:
   ```json
   {
     "success": true,
     "phone_number": "+234your_number",
     "expires_in": 9,
     "debug_code": "123456",
     "message": "Verification code sent to your WhatsApp! 📱",
     "notification_type": "whatsapp",
     "whatsapp_status": "queued",
     "development_mode": false
   }
   ```

## 🚀 **Production Setup**

### **1. Upgrade to WhatsApp Business API**

1. **Apply for production**:
   - Go to [WhatsApp Business API](https://www.twilio.com/whatsapp)
   - Fill out the application form
   - Wait for approval (1-2 weeks)

2. **Get your business number**:
   - You'll receive a dedicated WhatsApp number
   - Update `TWILIO_WHATSAPP_FROM` in your `.env`

### **2. Update Configuration**

```env
# Production WhatsApp
TWILIO_WHATSAPP_FROM=whatsapp:+1234567890
ENABLE_REAL_WHATSAPP=true
```

## 📱 **Phone Number Formatting**

The system automatically formats phone numbers:

### **Input Formats Supported**:
- ✅ `08012345678` (Nigerian format)
- ✅ `+2348012345678` (International)
- ✅ `2348012345678` (Without +)

### **WhatsApp Format**:
- ✅ `whatsapp:+2348012345678`

## 🔍 **Debugging**

### **Check Logs**
```bash
tail -f storage/logs/laravel.log
```

### **Common Issues**:

#### **1. "Twilio credentials not configured"**
- ✅ Check `.env` file
- ✅ Verify `TWILIO_ACCOUNT_SID` and `TWILIO_AUTH_TOKEN`
- ✅ Restart your server

#### **2. "Message not delivered"**
- ✅ Join the WhatsApp sandbox first
- ✅ Check if number is in correct format
- ✅ Verify Twilio account is active

#### **3. "Authentication failed"**
- ✅ Check Account SID and Auth Token
- ✅ Ensure no extra spaces in `.env`
- ✅ Verify Twilio account status

## 💰 **Pricing**

### **Sandbox (Free)**
- ✅ **1000 messages/month** free
- ✅ **Perfect for testing**
- ✅ **Limited to sandbox numbers**

### **Production**
- ✅ **$0.005 per message** (first 1000)
- ✅ **$0.004 per message** (1001-10000)
- ✅ **Volume discounts available**

## 🎯 **Best Practices**

### **1. Error Handling**
- ✅ **Always check response status**
- ✅ **Log errors for debugging**
- ✅ **Provide fallback options**

### **2. Rate Limiting**
- ✅ **Respect WhatsApp rate limits**
- ✅ **Implement retry logic**
- ✅ **Monitor message delivery**

### **3. User Experience**
- ✅ **Clear error messages**
- ✅ **Alternative verification methods**
- ✅ **Helpful instructions**

## 🔄 **Development vs Production**

### **Development Mode**
```env
ENABLE_REAL_WHATSAPP=false
```
- ✅ **No Twilio required**
- ✅ **Instant code display**
- ✅ **Perfect for testing**

### **Production Mode**
```env
ENABLE_REAL_WHATSAPP=true
```
- ✅ **Real WhatsApp messages**
- ✅ **Professional delivery**
- ✅ **Production ready**

## 📞 **Support**

### **Twilio Support**
- 📧 **Email**: help@twilio.com
- 📞 **Phone**: +1 (877) 487-9266
- 💬 **Chat**: Available in console

### **WhatsApp Business API**
- 📧 **Email**: business-support@whatsapp.com
- 🌐 **Website**: [WhatsApp Business](https://business.whatsapp.com/)

## 🎉 **Success Checklist**

- ✅ **Twilio account created**
- ✅ **WhatsApp sandbox joined**
- ✅ **Environment variables set**
- ✅ **SDK installed**
- ✅ **Test message sent**
- ✅ **Production API approved**
- ✅ **Business number configured**
- ✅ **Error handling implemented**

Your WhatsApp integration is now ready! 🚀 