#!/bin/bash
# Test storage configuration di Railway setelah deployment

echo "🧪 TESTING STORAGE CONFIGURATION"
echo "=================================="

URL="https://sucofindo-absen-production.up.railway.app"

echo "📡 Testing storage configuration..."
echo "URL: $URL/test-storage"
echo ""

# Test storage configuration
curl -s "$URL/test-storage" | jq '.' || {
    echo "❌ Storage test failed atau jq tidak tersedia"
    echo "Trying without jq..."
    curl -s "$URL/test-storage"
}

echo ""
echo "=================================="
echo "📋 MANUAL VERIFICATION STEPS:"
echo ""
echo "1. Open: $URL/test-storage"
echo "2. Check storage_link_exists: should be true"
echo "3. Check storage_writable: should be true"
echo "4. Check file_created: should be true"
echo "5. Check file_web_accessible: should be true"
echo ""
echo "🌐 Admin Panel: $URL/admin"
echo "📊 Analytics: $URL/admin/kepala-bidang/attendance-analytics"
echo ""
echo "✅ If all tests pass, storage is configured correctly!"
