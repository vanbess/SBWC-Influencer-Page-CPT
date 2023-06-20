<?php

/**
 * Plugin Name:       SBWC Influencer Page Custom Post Type
 * Plugin URI:        N/A 
 * Description:       Custom Post Type for Influencer Page and functionality to auto generate WooCommerce coupon code per page which expires in 48 hours and is then regenerated.
 * Version:           1.0.0
 * Author:            WC Bessinger
 * Author URI:        N/A
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sbwc-influencer-page
 */

// if absolute path is not defined, bail
if (!defined('ABSPATH')) {
    exit;
}

// check if WooCommerce is active and bail with admin notice if not
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>SBWC Influencer Page Custom Post Type requires WooCommerce to be installed and active.</p></div>';
    });
    return;
}

// include custom post type
require_once plugin_dir_path(__FILE__) . 'inc/cpt.php';

// custom post type save action which generates coupon code
// require_once plugin_dir_path(__FILE__) . 'inc/cpt_save_gen_coupon.php';

// add coupon code to matching dom elements
require_once plugin_dir_path(__FILE__) . 'inc/add_coupon_code_to_dom.php';

// cpt metaboxes
require_once plugin_dir_path(__FILE__) . 'inc/cpt_metaboxes.php';

// save cpt metaboxes
require_once plugin_dir_path(__FILE__) . 'inc/cpt_save_metaboxes_gen_coupon.php';

// manage cpt columns
require_once plugin_dir_path(__FILE__) . 'inc/cpt_manage_columns.php';

// query theme templates
require_once plugin_dir_path(__FILE__) . 'inc/query_theme_templates.php';