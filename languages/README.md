# Translation Files

This folder contains translation files for the Yaxii WooCommerce Shipping Importer plugin.

## Available Languages

- **English** (default) - Built into the plugin
- **Arabic** (ar) - `yaxii-wc-importer-ar.po` / `.mo`
- **French** (fr_FR) - `yaxii-wc-importer-fr_FR.po` / `.mo`

## Compiling Translations

### Option 1: Using Poedit (Recommended for Windows)

1. Download and install [Poedit](https://poedit.net/)
2. Open each `.po` file in Poedit
3. Click **File → Save** or press `Ctrl+S`
4. Poedit will automatically generate the `.mo` file

### Option 2: Using Command Line (Linux/Mac)

Run the included script:

```bash
bash compile-translations.sh
```

Or manually:

```bash
msgfmt -o languages/yaxii-wc-importer-ar.mo languages/yaxii-wc-importer-ar.po
msgfmt -o languages/yaxii-wc-importer-fr_FR.mo languages/yaxii-wc-importer-fr_FR.po
```

### Option 3: Using WP-CLI

```bash
wp i18n make-mo languages/
```

## File Structure

```
languages/
├── yaxii-wc-importer.pot        # Translation template
├── yaxii-wc-importer-ar.po      # Arabic translations (editable)
├── yaxii-wc-importer-ar.mo      # Arabic compiled (binary)
├── yaxii-wc-importer-fr_FR.po   # French translations (editable)
├── yaxii-wc-importer-fr_FR.mo   # French compiled (binary)
└── README.md                     # This file
```

## Adding New Translations

1. Copy `yaxii-wc-importer.pot` to `yaxii-wc-importer-{locale}.po`
2. Translate the strings using Poedit or another translation tool
3. Compile to `.mo` file
4. Upload both `.po` and `.mo` files to the `languages/` folder

## Language Codes

Common WordPress locale codes:

- Arabic: `ar`
- French (France): `fr_FR`
- Spanish: `es_ES`
- German: `de_DE`
- Italian: `it_IT`

## Notes

- The `.po` files are human-readable and editable
- The `.mo` files are binary and used by WordPress
- Always compile `.po` to `.mo` after making changes
- The plugin will automatically load the correct translation based on WordPress language settings
