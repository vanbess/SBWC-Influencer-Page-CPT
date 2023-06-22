<?php

// if access directly then exit
if (!defined('ABSPATH')) {
    exit;
}

// schedule coupon renewal check for every 5 minutes using Action Scheduler
add_action('as_schedule_ip_coupon_renewal', function () {

    // log separator using error_log
    error_log('----------------------------------------');

    // $wpdb
    global $wpdb;

    // log coupon renewal check time using error_log in readable format
    error_log('Coupon renewal check time: ' . date('Y-m-d H:i:s', current_time('timestamp')));

    // log querying all influencer pages using error_log
    error_log('Querying all influencer pages');

    // query all influencer pages which are published, returning an array containing the value of sbwc_influencer_coupon_id meta key and the post id
    $influencer_pages = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'sbwc_influencer_coupon_id' AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = 'influencer_page' AND post_status = 'publish')",
            date('Y-m-d H:i:s', current_time('timestamp'))
        )
    );

    // log number of influencer pages with coupons using error_log
    error_log('Number of influencer pages with coupons: ' . count($influencer_pages));

    // log loop start time using error_log
    error_log('Update loop start time: ' . date('Y-m-d H:i:s', current_time('timestamp')));

    // search for returned coupon id under WooCommerce coupons and see if is expired
    foreach ($influencer_pages as $influencer_page) {

        // log retrieving coupon object for coupon id using error_log
        error_log('Retrieving coupon object for coupon id: ' . $influencer_page->meta_value);

        // create new WC_Coupon object
        $coupon = new WC_Coupon($influencer_page->meta_value);

        // if coupon object successfully created then log coupon expiry date using error_log, else log error and continue to next coupon
        if ($coupon) {

            // log coupon expiry date using error_log
            error_log('Coupon expiry date: ' . $coupon->get_date_expires());

            // if coupon object not successfully created then log error and continue to next coupon
        } else {

            // log error
            error_log('Error retrieving coupon object for coupon id: ' . $influencer_page->meta_value);

            // continue to next coupon
            continue;
        }

        // if coupon is expired then delete the coupon id and coupon name from the influencer page
        if (strtotime($coupon->get_date_expires()) < time()) {

            // log coupon expiry using error_log
            error_log('Coupon expired: ' . $coupon->get_code() . ' at ' . date('Y-m-d H:i:s', current_time('timestamp')) . ' for influencer page id ' . $influencer_page->post_id);

            // get coupon name from coupon id
            $coupon_name = $coupon->get_code();

            // log coupon name using error_log
            error_log('Coupon name: ' . $coupon_name);

            // remove any previously added timestamp from coupon name
            $coupon_name = explode('-', $coupon_name)[0];

            // log coupon name using error_log
            error_log('Stripped coupon name: ' . $coupon_name);

            // generate new coupon name using existing coupon name, appending 6 random characters
            $new_coupon_name = $coupon_name . '-' . wp_generate_password(6, false, false);

            // log new coupon name using error_log
            error_log('New coupon name: ' . $new_coupon_name);

            // retrieve coupon discount from influencer page
            $coupon_discount = get_post_meta($influencer_page->post_id, 'sbwc_influencer_coupon_discount_amount', true);

            // log coupon discount using error_log
            error_log('Coupon discount: ' . $coupon_discount);

            // retrieve coupon discount type from influencer page
            $coupon_discount_type = get_post_meta($influencer_page->post_id, 'sbwc_influencer_coupon_discount_type', true);

            // log coupon discount type using error_log
            error_log('Coupon discount type: ' . $coupon_discount_type);

            // retrieve coupon usage limit from influencer page
            $coupon_usage_limit = get_post_meta($influencer_page->post_id, 'sbwc_influencer_coupon_usage_limit', true);

            // log coupon usage limit using error_log
            error_log('Coupon usage limit: ' . $coupon_usage_limit);

            // retrieve coupon product ids from influencer page
            $coupon_product_ids = get_post_meta($influencer_page->post_id, 'sbwc_influencer_page_products', true);

            // log coupon product ids using error_log
            error_log('Coupon product ids: ' . implode(',', $coupon_product_ids));

            // log creating new coupon using error_log
            error_log('Creating new coupon');

            // create new coupon
            $new_coupon = array(
                'post_title'   => $new_coupon_name,
                'post_content' => '',
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_type'    => 'shop_coupon'
            );

            // insert the post into the database
            $new_coupon_id = wp_insert_post($new_coupon);

            // if successfully created new coupon then update the influencer page with the new coupon id and name only, else log error
            if (!is_wp_error($new_coupon_id)) {

                // get coupon object for new coupon id
                $new_coupon = new WC_Coupon($new_coupon_id);

                // log new coupon id using error_log
                error_log('New coupon id: ' . $new_coupon_id);

                // log updating influencer page with new coupon id and name using error_log
                error_log('Updating influencer page with new coupon id and name');

                // update influencer page with new coupon id and name
                $coupon_id_updated   = update_post_meta($influencer_page->post_id, 'sbwc_influencer_coupon_id', $new_coupon_id);
                $coupon_name_updated = update_post_meta($influencer_page->post_id, 'sbwc_influencer_coupon_code', $new_coupon_name);

                // if update failed then log error
                if (!$coupon_id_updated || !$coupon_name_updated) {

                    // log error along with influencer page id
                    error_log('Error updating influencer page with new coupon id and name for influencer page id ' . $influencer_page->post_id);

                    // continue to next coupon
                    continue;
                }

                // set new coupon description to influencer page title
                $new_coupon->set_description(get_the_title($influencer_page->post_id));

                // log updating new coupon with coupon discount, discount type, usage limit and product ids using error_log
                error_log('Updating new coupon with coupon discount, discount type, usage limit and product ids');

                // update new coupon with coupon discount, discount type, usage limit and product ids
                $new_coupon->set_amount($coupon_discount);
                $new_coupon->set_discount_type($coupon_discount_type);
                $new_coupon->set_usage_limit($coupon_usage_limit == '-1'? '' : $coupon_usage_limit);
                $new_coupon->set_product_ids($coupon_product_ids);

                // log updating new coupon expiry date using error_log
                error_log('Updating new coupon expiry date to 48 hours from now');

                // set coupon expiry date to 2 days from now
                $coupon_expiry_date = date('Y-m-d H:i:s', strtotime('+2 days', current_time('timestamp')));
                $new_coupon->set_date_expires($coupon_expiry_date);

                // log coupon renewal with old name, new name, time, expiry date and associated influencer page id using error_log
                error_log('Coupon renewal: ' . $coupon_name . ' to ' . $new_coupon_name . ' at ' . date('Y-m-d H:i:s', current_time('timestamp')) . ' for influencer page id ' . $influencer_page->post_id .' with expiry date ' . $coupon_expiry_date. ' and usage limit ' . $coupon_usage_limit . ' and product ids ' . implode(',', $coupon_product_ids). ' and discount ' . $coupon_discount . ' and discount type ' . $coupon_discount_type. ' and new coupon id ' . $new_coupon_id);

                // save new coupon
                $new_coupon->save();

                // get WP_Error object from $new_coupon_id and log error
            } else {

                // log error
                error_log('Coupon renewal error: ' . $new_coupon_id->get_error_message());
            }
        }else{

            // log coupon not expired using error_log
            error_log('Coupon not expired: ' . $coupon->get_code() . ' at ' . date('Y-m-d H:i:s', current_time('timestamp')) . ' for influencer page id ' . $influencer_page->post_id);

            // log moving to next coupon using error_log
            error_log('Moving to next coupon');
        }
    }

    // log update loop end time using error_log
    error_log('Update loop end time: ' . date('Y-m-d H:i:s', current_time('timestamp')));

    // log script end using error_log
    error_log('Script end');

    // log separator using error_log
    error_log('----------------------------------------');
});

// schedule coupon renewal check using Action Scheduler for every 5 minutes
add_action('init', function () {
    
    // schedule recurring ACTION SCHEDULER event if not already scheduled
    if (!as_next_scheduled_action('as_schedule_ip_coupon_renewal')) {
        as_schedule_recurring_action(strtotime('now'), 300, 'as_schedule_ip_coupon_renewal', array(), 'sbwc_influencer_page_coupon_renewal');
    }

});
