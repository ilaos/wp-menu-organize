#!/bin/bash

# WMO Plugin Build Script
# This script minifies CSS and JavaScript files for better performance

echo "🔧 WMO Plugin Build Script"
echo "=========================="

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js to use this build script."
    echo "   You can download it from: https://nodejs.org/"
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm to use this build script."
    exit 1
fi

# Install required packages if not already installed
echo "📦 Installing required packages..."
npm install -g cssnano-cli terser

# Set paths
CSS_DIR="assets/css"
JS_DIR="assets/js"

echo "🎨 Minifying CSS files..."

# Minify admin.css
if [ -f "$CSS_DIR/admin.css" ]; then
    cssnano "$CSS_DIR/admin.css" "$CSS_DIR/admin.min.css"
    echo "✅ admin.css → admin.min.css"
else
    echo "⚠️  admin.css not found"
fi

echo "📜 Minifying JavaScript files..."

# Minify admin.js
if [ -f "$JS_DIR/admin.js" ]; then
    terser "$JS_DIR/admin.js" -c -m -o "$JS_DIR/admin.min.js"
    echo "✅ admin.js → admin.min.js"
else
    echo "⚠️  admin.js not found"
fi

# Minify color-picker.js
if [ -f "$JS_DIR/color-picker.js" ]; then
    terser "$JS_DIR/color-picker.js" -c -m -o "$JS_DIR/color-picker.min.js"
    echo "✅ color-picker.js → color-picker.min.js"
else
    echo "⚠️  color-picker.js not found"
fi

echo ""
echo "🎉 Build complete!"
echo ""
echo "📊 File sizes:"
if [ -f "$CSS_DIR/admin.css" ]; then
    ORIGINAL_CSS=$(wc -c < "$CSS_DIR/admin.css")
    MINIFIED_CSS=$(wc -c < "$CSS_DIR/admin.min.css")
    CSS_SAVINGS=$((ORIGINAL_CSS - MINIFIED_CSS))
    CSS_PERCENT=$((CSS_SAVINGS * 100 / ORIGINAL_CSS))
    echo "   admin.css: ${ORIGINAL_CSS} bytes → ${MINIFIED_CSS} bytes (${CSS_SAVINGS} bytes saved, ${CSS_PERCENT}% reduction)"
fi

if [ -f "$JS_DIR/admin.js" ]; then
    ORIGINAL_JS=$(wc -c < "$JS_DIR/admin.js")
    MINIFIED_JS=$(wc -c < "$JS_DIR/admin.min.js")
    JS_SAVINGS=$((ORIGINAL_JS - MINIFIED_JS))
    JS_PERCENT=$((JS_SAVINGS * 100 / ORIGINAL_JS))
    echo "   admin.js: ${ORIGINAL_JS} bytes → ${MINIFIED_JS} bytes (${JS_SAVINGS} bytes saved, ${JS_PERCENT}% reduction)"
fi

if [ -f "$JS_DIR/color-picker.js" ]; then
    ORIGINAL_CP=$(wc -c < "$JS_DIR/color-picker.js")
    MINIFIED_CP=$(wc -c < "$JS_DIR/color-picker.min.js")
    CP_SAVINGS=$((ORIGINAL_CP - MINIFIED_CP))
    CP_PERCENT=$((CP_SAVINGS * 100 / ORIGINAL_CP))
    echo "   color-picker.js: ${ORIGINAL_CP} bytes → ${MINIFIED_CP} bytes (${CP_SAVINGS} bytes saved, ${CP_PERCENT}% reduction)"
fi

echo ""
echo "💡 The plugin will automatically use minified files when SCRIPT_DEBUG is not enabled."
echo "   To use unminified files for development, add this to wp-config.php:"
echo "   define('SCRIPT_DEBUG', true);"
