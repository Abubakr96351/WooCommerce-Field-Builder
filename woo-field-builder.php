<?php
/**
 * Plugin Name: WooCommerce Field Builder
 * Plugin URI: https://example.com/woo-field-builder
 * Description: Add custom fields to WooCommerce products.
 * Version: 0.1.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 7.0
 * Author: Your Company
 * Text Domain: woo-field-builder
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WFB_VERSION', '0.1.0');
define('WFB_PLUGIN_FILE', __FILE__);
define('WFB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WFB_PLUGIN_URL', plugin_dir_url(__FILE__));

$wfb_autoloader = WFB_PLUGIN_DIR . 'vendor/autoload.php';

if (file_exists($wfb_autoloader)) {
    require_once $wfb_autoloader;
} else {
    require_once WFB_PLUGIN_DIR . 'src/autoload.php';
}

add_action('plugins_loaded', function () {

    load_plugin_textdomain('woo-field-builder', false, dirname(plugin_basename(WFB_PLUGIN_FILE)) . '/languages');

    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__('WooCommerce Field Builder requires WooCommerce to be installed and active.', 'woo-field-builder');
            echo '</p></div>';
        });
        return;
    }

    \WooFieldBuilder\Plugin::get_instance()->boot();
});
