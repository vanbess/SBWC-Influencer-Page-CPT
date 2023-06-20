<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manage columns
 * 
 * @param array $columns - the columns
 * @return array $columns - the modified columns
 * 
 */
add_filter('manage_influencer_page_posts_columns', function ($columns) {

    // remove date column
    unset($columns['date']);

    // add coupon code column
    $columns['sbwc_influencer_coupon_code'] = __('Coupon Code', 'sbwc-influencer-page');

    // add coupon discount amount column
    $columns['sbwc_influencer_coupon_discount_amount'] = __('Discount Amount', 'sbwc-influencer-page');

    // add coupon discount type column
    $columns['sbwc_influencer_coupon_discount_type'] = __('Discount Type', 'sbwc-influencer-page');

    // add coupon usage limit column
    $columns['sbwc_influencer_coupon_usage_limit'] = __('Usage Limit', 'sbwc-influencer-page');

    // add coupon expiry date column
    $columns['sbwc_influencer_coupon_expiry'] = __('Expiry Date', 'sbwc-influencer-page');

    // add date column
    $columns['date'] = __('Published On', 'sbwc-influencer-page');

    // return columns
    return $columns;
}, 10, 1);

/**
 * Manage column content
 * 
 * @param string $column - the column
 * @param int $post_id - the post id
 * 
 * @return void
 */
add_action('manage_influencer_page_posts_custom_column', function ($column, $post_id) {

    // log meta
    error_log(print_r(get_post_meta($post_id), true));

    // if coupon code column
    if ($column === 'sbwc_influencer_coupon_code') {

        error_log('coupon code column');

        // get coupon code
        $coupon_code = get_post_meta($post_id, 'sbwc_influencer_coupon_code', true);

        // if coupon code, echo
        if ($coupon_code) {
            echo $coupon_code;
        }
    }

    // if coupon discount amount column
    if ($column === 'sbwc_influencer_coupon_discount_amount') {

        // get coupon discount amount
        $coupon_discount_amount = get_post_meta($post_id, 'sbwc_influencer_coupon_discount_amount', true);

        // if coupon discount amount, echo
        if ($coupon_discount_amount) {
            echo $coupon_discount_amount;
        }
    }

    // if coupon discount type column
    if ($column === 'sbwc_influencer_coupon_discount_type') {

        // get coupon discount type
        $coupon_discount_type = get_post_meta($post_id, 'sbwc_influencer_coupon_discount_type', true);

        // if coupon discount type, echo
        if ($coupon_discount_type) {
            echo $coupon_discount_type;
        }
    }

    // if coupon usage limit column
    if ($column === 'sbwc_influencer_coupon_usage_limit') {

        // get coupon usage limit
        $coupon_usage_limit = get_post_meta($post_id, 'sbwc_influencer_coupon_usage_limit', true);

        // if coupon usage limit, echo
        if ($coupon_usage_limit) {


            // check if usage limit is set to minus one, echo unlimited symbol
            if ($coupon_usage_limit == '-1') {
                    
                    // echo unlimited symbol
                    echo '&#8734;';
            } else {

                // echo usage limit
                echo $coupon_usage_limit;
            }

            // if usage limit is empty string, echo unlimited symbol

        }elseif($coupon_usage_limit === ''){
            echo '-';
        }
    }

    // if coupon expiry date column
    if ($column === 'sbwc_influencer_coupon_expiry') {

        // get coupon expiry date
        $coupon_expiry_date = get_post_meta($post_id, 'sbwc_influencer_coupon_expiry', true);

        // if coupon expiry date, echo
        if ($coupon_expiry_date) {
            echo $coupon_expiry_date;
        }
    }
}, 10, 2);

/**
 * Allow column sorting
 * 
 * @param array $columns - the columns
 * @return array $columns - the modified columns
 * 
 */
add_filter('manage_edit-influencer_page_sortable_columns', function ($columns) {

    // add coupon code column
    $columns['sbwc_influencer_coupon_code'] = 'sbwc_influencer_coupon_code';

    // add coupon discount amount column
    $columns['sbwc_influencer_coupon_discount_amount'] = 'sbwc_influencer_coupon_discount_amount';

    // add coupon discount type column
    $columns['sbwc_influencer_coupon_discount_type'] = 'sbwc_influencer_coupon_discount_type';

    // add coupon usage limit column
    $columns['sbwc_influencer_coupon_usage_limit'] = 'sbwc_influencer_coupon_usage_limit';

    // add coupon expiry date column
    $columns['sbwc_influencer_coupon_expiry'] = 'sbwc_influencer_coupon_expiry';

    // return columns
    return $columns;

}, 10, 1);

/**
 * Sort columns
 * 
 * @param object $query - the query
 * @return void
 * 
 */
add_action('pre_get_posts', function ($query) {

    // if coupon code column
    if ($query->get('orderby') === 'sbwc_influencer_coupon_code') {

        // order by coupon code meta value
        $query->set('meta_key', 'sbwc_influencer_coupon_code');
        $query->set('orderby', 'meta_value');
    }

    // if coupon discount amount column
    if ($query->get('orderby') === 'sbwc_influencer_coupon_discount_amount') {

        // order by coupon discount amount meta value
        $query->set('meta_key', 'sbwc_influencer_coupon_discount_amount');
        $query->set('orderby', 'meta_value');
    }

    // if coupon discount type column
    if ($query->get('orderby') === 'sbwc_influencer_coupon_discount_type') {

        // order by coupon discount type meta value
        $query->set('meta_key', 'sbwc_influencer_coupon_discount_type');
        $query->set('orderby', 'meta_value');
    }

    // if coupon usage limit column
    if ($query->get('orderby') === 'sbwc_influencer_coupon_usage_limit') {

        // order by coupon usage limit meta value
        $query->set('meta_key', 'sbwc_influencer_coupon_usage_limit');
        $query->set('orderby', 'meta_value');
    }

    // if coupon expiry date column
    if ($query->get('orderby') === 'sbwc_influencer_coupon_expiry') {

        // order by coupon expiry date meta value
        $query->set('meta_key', 'sbwc_influencer_coupon_expiry');
        $query->set('orderby', 'meta_value');
    }

}, 10, 1);
?>