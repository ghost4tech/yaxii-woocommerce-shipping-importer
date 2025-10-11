#!/bin/bash
# Compile translation files for Yaxii WooCommerce Shipping Importer

echo "Compiling translations..."

# Check if msgfmt is available
if ! command -v msgfmt &> /dev/null
then
    echo "Error: msgfmt command not found. Please install gettext."
    echo "Ubuntu/Debian: sudo apt-get install gettext"
    echo "macOS: brew install gettext"
    exit 1
fi

# Compile Arabic
echo "Compiling Arabic (ar)..."
msgfmt -o languages/yaxii-wc-importer-ar.mo languages/yaxii-wc-importer-ar.po

# Compile French
echo "Compiling French (fr_FR)..."
msgfmt -o languages/yaxii-wc-importer-fr_FR.mo languages/yaxii-wc-importer-fr_FR.po

echo "✅ All translations compiled successfully!"
echo ""
echo "Generated files:"
echo "- languages/yaxii-wc-importer-ar.mo"
echo "- languages/yaxii-wc-importer-fr_FR.mo"

