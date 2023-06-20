<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save meta box data
 * 
 * @param int $post_id - the post id
 * @param WP_Post $post - the post object
 * @param bool $update - whether this is an existing post being updated or not
 * 
 * @return void
 */
add_action('save_post_influencer_page', function ($post_id, $post, $update) {

    // if not influencer page, bail
    if (get_post_type($post_id) !== 'influencer_page') {
        return;
    }

    // if nonce is not set, bail
    if (!isset($_POST['sbwc_influencer_page_meta_box_nonce'])) {
        return;
    }

    // if nonce is not verified, bail
    if (!wp_verify_nonce($_POST['sbwc_influencer_page_meta_box_nonce'], 'sbwc_influencer_page_meta_box')) {
        return;
    }

    // if autosave, bail
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // save product ids
    if (isset($_POST['sbwc_influencer_page_products'])) {
        update_post_meta($post_id, 'sbwc_influencer_page_products', $_POST['sbwc_influencer_page_products']);
    }

    // save coupon code
    if (isset($_POST['sbwc_influencer_coupon_code'])) {
        update_post_meta($post_id, 'sbwc_influencer_coupon_code', $_POST['sbwc_influencer_coupon_code']);
    }

    // save coupon discount amount
    if (isset($_POST['sbwc_influencer_coupon_discount_amount'])) {
        update_post_meta($post_id, 'sbwc_influencer_coupon_discount_amount', $_POST['sbwc_influencer_coupon_discount_amount']);
    }

    // save coupon discount type
    if (isset($_POST['sbwc_influencer_coupon_discount_type'])) {
        update_post_meta($post_id, 'sbwc_influencer_coupon_discount_type', $_POST['sbwc_influencer_coupon_discount_type']);
    }

    // save coupon usage limit
    if (isset($_POST['sbwc_influencer_coupon_usage_limit'])) {
        update_post_meta($post_id, 'sbwc_influencer_coupon_usage_limit', $_POST['sbwc_influencer_coupon_usage_limit']);
    }

    // get product ids
    $product_ids = get_post_meta($post_id, 'sbwc_influencer_page_products', true);

    // check if page has WooCommerce coupon associated with it
    $coupon_code = get_post_meta($post_id, 'sbwc_influencer_coupon_code', true);

    // get coupon discount amount from post meta
    $coupon_discount_amount = get_post_meta($post_id, 'sbwc_influencer_coupon_discount_amount', true);

    // get discount type from post meta
    $coupon_discount_type = get_post_meta($post_id, 'sbwc_influencer_coupon_discount_type', true);

    // get coupon usage limit from post meta
    $coupon_usage_limit = get_post_meta($post_id, 'sbwc_influencer_coupon_usage_limit', true);

    // if coupon usage limit == 0, set to empty string to make coupon unlimited
    if ($coupon_usage_limit =='-1') {
        $coupon_usage_limit = '';
    }

    // get coupon id from post meta
    $coupon_id = get_post_meta($post_id, 'sbwc_influencer_coupon_id', true);

    // get WC coupon object
    $coupon = new WC_Coupon($coupon_id);

    // check expiry date
    $coupon_expiry_date = $coupon->get_date_expires();

    // if coupon not yet expired, bail
    if ($coupon_expiry_date && strtotime($coupon_expiry_date) > time()) {
        return;
    }

    // generate WooCommerce coupon
    $coupon = array(
        'post_title'   => $coupon_code,
        'post_content' => '',
        'post_status'  => 'publish',
        'post_author'  => 1,
        'post_type'    => 'shop_coupon'
    );

    // insert the post into the database
    $coupon_id = wp_insert_post($coupon);

    // if error, log and bail
    if (is_wp_error($coupon_id)) {
        error_log('Error creating coupon for Influencer Page: ' . $post_id);
        return;
    }

    // if not error, get coupon and set options
    $coupon = new WC_Coupon($coupon_id);

    // set coupon code
    $coupon->set_code($coupon_code);

    // if product ids set, set product ids
    if ($product_ids) {
        $coupon->set_product_ids($product_ids);
    }

    // set coupon type
    $coupon->set_discount_type($coupon_discount_type);

    // set coupon amount
    $coupon->set_amount($coupon_discount_amount);

    // set coupon usage limit
    $coupon->set_usage_limit($coupon_usage_limit);

    // set description to influencer page title
    $coupon->set_description(get_the_title($post_id));

    // set usage per user limit
    $coupon->set_usage_limit_per_user(1);

    // set expiry date
    $coupon->set_date_expires(date('Y-m-d H:i:s', strtotime('+48 hours')));

    // save coupon
    $coupon->save();

    // update post meta with coupon id
    update_post_meta($post_id, 'sbwc_influencer_coupon_id', $coupon_id);
}, 10, 3);
