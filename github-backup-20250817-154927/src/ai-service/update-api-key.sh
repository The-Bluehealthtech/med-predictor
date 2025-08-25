#!/bin/bash

echo "🔑 Med-Gemini API Key Configuration"
echo "=================================="
echo ""

# Check if API key is provided as argument
if [ -n "$1" ]; then
    API_KEY="$1"
    echo "✅ API key provided as argument"
else
    echo "📝 Please enter your Med-Gemini API key (starts with AIza...):"
    read -r API_KEY
fi

# Validate API key format
if [[ $API_KEY =~ ^AIza[0-9A-Za-z_-]{35}$ ]]; then
    echo "✅ API key format looks valid"
else
    echo "⚠️  Warning: API key format doesn't match expected pattern"
    echo "   Expected format: AIza... (39 characters total)"
    echo "   Continue anyway? (y/n):"
    read -r continue_anyway
    if [[ $continue_anyway != "y" && $continue_anyway != "Y" ]]; then
        echo "❌ Configuration cancelled"
        exit 1
    fi
fi

# Backup current .env file
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo "📦 Created backup of current .env file"

# Update the .env file
sed -i.bak "s/GEMINI_API_KEY=.*/GEMINI_API_KEY=$API_KEY/" .env
rm .env.bak

echo "✅ Updated .env file with new API key"
echo ""

# Test the configuration
echo "🧪 Testing configuration..."
if curl -s http://localhost:3001/health > /dev/null; then
    echo "✅ AI service is running"
    
    # Test API call
    response=$(curl -s -X POST http://localhost:3001/api/v1/med-gemini/analyze \
        -H "Content-Type: application/json" \
        -d '{"analysis_type": "ecg_image", "file_content": "test", "file_type": "jpg", "prompt": "Test"}' | jq -r '.analysis.mockMode // "unknown"')
    
    if [ "$response" = "false" ]; then
        echo "✅ Real Med-Gemini API is now active!"
        echo "🎉 Configuration successful!"
    elif [ "$response" = "true" ]; then
        echo "⚠️  Still in mock mode - API key may be invalid"
        echo "   Please check your API key and restart the service"
    else
        echo "❓ Could not determine API mode"
    fi
else
    echo "⚠️  AI service is not running"
    echo "   Start it with: cd src/ai-service && node server.js"
fi

echo ""
echo "📋 Next steps:"
echo "1. Restart the AI service: cd src/ai-service && node server.js"
echo "2. Test with a real medical image file"
echo "3. Check the logs for any API errors"
echo ""
echo "🔗 Get your API key from: https://aistudio.google.com/app/apikey" 