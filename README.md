# Hive's Code Snippets Manager

Hive's Code Snippets Manager is a custom WordPress plugin that allows you to manage and toggle PHP and CSS code snippets directly from the WordPress admin panel. The plugin includes features like dynamic scrollbar customization, URL-specific snippet application, and more.

## Features

- **PHP Code Snippets**:
  - Add and manage custom PHP snippets.
  - Execute snippets globally or restrict them to specific URLs.
  - Example: Add an "Enquire Now" button to out-of-stock WooCommerce products.

- **CSS Code Snippets**:
  - Add and manage custom CSS snippets.
  - Apply snippets globally or restrict them to specific URLs.
  - Example: Customize the appearance of scrollbars with user-defined colors.

- **Dynamic Scrollbar Styling**:
  - Customize the scrollbar thumb color directly from the admin interface.

- **User-Friendly Admin Interface**:
  - Enable or disable snippets with one click.
  - Add URL-specific options for snippets.
  - Color picker for customizing CSS properties like scrollbars.

## Installation

### Via WordPress Admin
1. Download the plugin ZIP file.
2. Log in to your WordPress admin dashboard.
3. Go to **Plugins > Add New**.
4. Click **Upload Plugin** and select the ZIP file.
5. Click **Install Now** and then **Activate**.

### Via FTP
1. Download and unzip the plugin files.
2. Upload the folder `hives-code-snippets-manager` to the `/wp-content/plugins/` directory.
3. Log in to your WordPress admin dashboard and activate the plugin.

## Usage

1. Go to **Settings > Snippets Manager** in your WordPress dashboard.
2. Manage PHP and CSS snippets:
   - Enable or disable individual snippets.
   - Add URL-specific options for targeted application.
3. For CSS snippets, use the color picker to customize scrollbar thumb colors dynamically.

## Example Snippets

### PHP Snippet: WooCommerce Out-of-Stock Enquiry Button
Adds a button to out-of-stock WooCommerce products with a customizable URL.

```php
add_filter('woocommerce_get_availability_text', function ($availability, $product) {
    if ($availability === 'Out of stock') {
        $availability = '<a href="your-url" class="oos-enquiry-btn"> Sold Out, Enquire Now </a>';
    }
    return $availability;
}, 10, 2);
