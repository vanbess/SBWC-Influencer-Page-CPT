<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hook into save post action for custom post type and generate 48 hour WooCommerce coupon code by default
 * 
 * @param int $post_id - the post id
 * @param WP_Post $post - the post object
 * @param bool $update - whether this is an existing post being updated or not
 */
add_action('save_post_influencer_page', function ($post_id, $post, $update) {

    // if it's not the correct post type, bail
    if (get_post_type($post_id) !== 'influencer_page') {
        return;
    }

    // check if page has WooCommerce coupon associated with it
    $coupon_code = get_post_meta($post_id, 'sbwc_influencer_coupon_code', true);

    // if coupon code is present, bail
    if ($coupon_code) {
        return;
    }

    // get coupon discount amount from post meta
    $coupon_discount_amount = get_post_meta($post_id, 'sbwc_influencer_coupon_discount_amount', true);

    // if coupon discount amount is not present, bail
    if (!$coupon_discount_amount) {
        return;
    }

    // get discount type from post meta
    $coupon_discount_type = get_post_meta($post_id, 'sbwc_influencer_coupon_discount_type', true);

    // if discount type is not present, bail
    if (!$coupon_discount_type) {
        return;
    }

    // get coupon usage limit from post meta
    $coupon_usage_limit = get_post_meta($post_id, 'sbwc_influencer_coupon_usage_limit', true);

    // if coupon usage limit == 0, set to empty string to make coupon unlimited
    if ($coupon_usage_limit == 0) {
        $coupon_usage_limit = '';
    }

    // get coupon id from post meta
    $coupon_id = get_post_meta($post_id, 'sbwc_influencer_coupon_id', true);

    // get coupon expiry date from coupon id
    $coupon_expiry_date = get_post_meta($coupon_id, 'sbwc_influencer_coupon_expiry', true);

    // if expiry date set and coupon not yet expired, bail
    if ($coupon_expiry_date && strtotime($coupon_expiry_date) > time()) {
        return;
    }

    // if coupon is present but does not have expiry date, set expiry date to 48 hours from now and bail
    if ($coupon_id && !$coupon_expiry_date) {
        $coupon_expiry_date = date('Y-m-d H:i:s', strtotime('+48 hours'));
        update_post_meta($coupon_id, 'sbwc_influencer_coupon_expiry', $coupon_expiry_date);
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

    // set coupon type
    $coupon->set_discount_type($coupon_discount_type);

    // set coupon amount
    $coupon->set_amount($coupon_discount_amount);

    // set coupon usage limit
    $coupon->set_usage_limit($coupon_usage_limit);

    // set usage per user limit
    $coupon->set_usage_limit_per_user(1);

    // if coupon expiry date provided, set expiry date, else set to 48 hours from now
    if ($coupon_expiry_date) {
        $coupon->set_date_expires($coupon_expiry_date);
    } else {
        $coupon->set_date_expires(date('Y-m-d H:i:s', strtotime('+48 hours')));
    }

    // save coupon
    $coupon->save();

    // update post meta with coupon id
    update_post_meta($post_id, 'sbwc_influencer_coupon_id', $coupon_id);

}, 10, 3);
