This plugin adds product metadata to control which products are available for Point of Sale. All products are marked as POS-available by default.

- Global setting: "Sell all products in POS" (enabled by default)
- Automatic product metadata: `_wc_pos_allowed` set to "yes" for all products
- Read-only field in product edit screen showing POS availability status

## Settings

Navigate to **WooCommerce > Settings > Point of Sale** to configure:

- Enable/disable all products for POS by default
- View plugin activation status

## Product Metadata

Each product gets a meta key:
- **Key**: `_wc_pos_allowed`
- **Default Value**: `yes`
- **Location**: Product Data > Advanced tab (read-only)

## Installation

1. Upload the plugin to your WordPress plugins directory
2. Activate the plugin
3. Be sure to have enabled Point of Sale features: For this navigate to WooCommerce > Settings > Advanced > Features, and enable:
```
Point of Sale
Point of Sale Enable Point of Sale functionality in the WooCommerce mobile apps.
```
4. Navigate to WooCommerce > Settings > Point of Sale to configure
