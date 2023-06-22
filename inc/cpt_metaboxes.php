<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add meta box to influencer page
 * 
 * @return void
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'sbwc_influencer_page_meta_box',
        __('Influencer Page Settings', 'sbwc-influencer-page'),
        'sbwc_influencer_page_meta_box_callback',
        'influencer_page',
        'normal',
        'high'
    );
});

/**
 * Meta box callback function
 * 
 * @param WP_Post $post - the post object
 * 
 * @return void
 */
function sbwc_influencer_page_meta_box_callback($post)
{

    // get coupon code from post meta
    $coupon_code = get_post_meta($post->ID, 'sbwc_influencer_coupon_code', true);

    // get coupon discount amount from post meta
    $coupon_discount_amount = get_post_meta($post->ID, 'sbwc_influencer_coupon_discount_amount', true);

    // get discount type from post meta
    $coupon_discount_type = get_post_meta($post->ID, 'sbwc_influencer_coupon_discount_type', true);

    // get coupon usage limit from post meta
    $coupon_usage_limit = get_post_meta($post->ID, 'sbwc_influencer_coupon_usage_limit', true);

    // get coupon id from post meta
    $coupon_id = get_post_meta($post->ID, 'sbwc_influencer_coupon_id', true);

     // get product ids from post meta
    $product_ids = get_post_meta($post->ID, 'sbwc_influencer_page_products', true);

    // nonce field
    wp_nonce_field('sbwc_influencer_page_meta_box', 'sbwc_influencer_page_meta_box_nonce');

    // important instructions to use CSS class on each Elementor element that the user wants to add the coupon code to
?>
    <div style="background-color: #ed143d30; padding: 10px; border-radius: 3px;">
        <b><i><u><?php _e('IMPORTANT:', 'sbwc-influencer-page'); ?></u></i></b>

        <ul style="list-style:decimal; padding-left: 20px;">
            <li>
                <?php _e('To display the coupon code on the page, add the CSS class <b>ip-coupon-code</b> to each Elementor element you want to display the coupon code in. The coupon code will be inserted inside this element or elements.', 'sbwc-influencer-page'); ?>
            </li>
            <li>
                <?php _e('If Elementor\'s editor fails to load or seems to be hanging, or visiting this page on the front-end produces a 404 error, please navigate to Settings -> Permalinks and click on the Save Changes button at the bottom of the page to flush permalinks.', 'sbwc-influencer-page'); ?>
            </li>
        </ul>
    </div>

    <?php
    // coupon id
    ?>
    <p>
        <label for="sbwc_influencer_coupon_id"><b><i><?php _e('Coupon ID:', 'sbwc-influencer-page'); ?></i></b></label>
    </p>
    <p>
        <input type="text" class="form-field regular-text" name="sbwc_influencer_coupon_id" id="sbwc_influencer_coupon_id" value="<?php echo $coupon_id; ?>" readonly />
        <icon>?</icon> <span class="ip-help" style="display: none;"><?php _e('The WooCommerce coupon ID generated for this particular page will appear here once you publish this page.', 'sbwc-influencer-page'); ?></span>
    </p>

    <?php
    // get current influencer page pll language
    $influencer_page_language = pll_get_post_language($post->ID);

    // retrieve all products with current influencer page language
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'lang'           => $influencer_page_language,
        'fields'         => 'ids'
    );

    $product_ids = get_posts($args);

    // loop through product ids and get product title, id and polylang language if set
    $product_titles = array();

    foreach ($product_ids as $product_id) {
        $product_title = get_the_title($product_id);
        $product_titles[$product_id] = $product_title . ' (' . strtoupper(pll_get_post_language($product_id)) . ' - ID ' . $product_id . ')';
    }

    // select2 dropdown using select2.js and product titles, allows multiple selection
    ?>
    <p>
        <label for="sbwc_influencer_page_products"><b><i><?php _e('Select products:', 'sbwc-influencer-page'); ?></i></b></label>
    </p>
    <p>
        <select name="sbwc_influencer_page_products[]" id="sbwc_influencer_page_products" class="form-field regular-text" multiple="multiple">
            <?php
            foreach ($product_titles as $product_id => $product_title) {
                echo '<option value="' . $product_id . '" ' . selected(in_array($product_id, get_post_meta($post->ID, 'sbwc_influencer_page_products', true)), true, false) . '>' . $product_title . '</option>';
            }
            ?>
        </select>
        <icon>?</icon> <span class="ip-help" style="display: none;"><?php _e('Select the products you are going to use on the page, i.e. the products to which the coupon which is generated will be limited to.', 'sbwc-influencer-page'); ?></span>
    </p>

    <!-- select2 js -->
    <script>
        jQuery(document).ready(function($) {
            $('#sbwc_influencer_page_products').select2({
                placeholder: '<?php _e('Select products', 'sbwc-influencer-page'); ?>',
            });
        });
    </script>

    <style>
        .select2-container .select2-search--inline .select2-search__field {
            box-sizing: border-box;
            border: none;
            font-size: 100%;
            margin-top: 0px;
            padding: 0;
            position: relative;
            bottom: 14px;
            left: 5px;
        }
    </style>

    <?php
    // coupon code
    ?>
    <p>
        <label for="sbwc_influencer_coupon_code"><b><i><?php _e('Coupon Code:', 'sbwc-influencer-page'); ?></i></b></label>
    </p>
    <p>
        <input type="text" class="form-field regular-text" name="sbwc_influencer_coupon_code" id="sbwc_influencer_coupon_code" value="<?php echo $coupon_code; ?>" />
        <icon>?</icon> <span class="ip-help" style="display: none;"> <?php _e('The coupon code you want to use/display on this page.', 'sbwc-influencer-page'); ?></span>
    </p>
    <?php
    // coupon discount amount
    ?>
    <p>
        <label for="sbwc_influencer_coupon_discount_amount"><b><i><?php _e('Discount Amount', 'sbwc-influencer-page'); ?></i></b></label>
    </p>
    <p>
        <input type="number" class="form-field regular-text" name="sbwc_influencer_coupon_discount_amount" id="sbwc_influencer_coupon_discount_amount" value="<?php echo $coupon_discount_amount; ?>" />
        <icon>?</icon> <span class="ip-help" style="display: none;"><?php _e('Discount amount/percentage which is applied when this coupon is used.', 'sbwc-influencer-page'); ?></span>
    </p>
    <?php
    // coupon discount type
    ?>
    <p>
        <label for="sbwc_influencer_coupon_discount_type"><b><i><?php _e('Discount Type', 'sbwc-influencer-page'); ?></i></b></label>
    </p>
    <p>
        <select name="sbwc_influencer_coupon_discount_type" id="sbwc_influencer_coupon_discount_type" class="form-field regular-text">
            <option value="fixed_cart" <?php selected($coupon_discount_type, 'fixed_cart'); ?>>Fixed Cart</option>
            <option value="percent" <?php selected($coupon_discount_type, 'percent'); ?>>Percent</option>
        </select>
        <icon>?</icon><span class="ip-help" style="display: none;"> <?php _e('The coupon type, i.e. fixed amount discount or percentage discount.', 'sbwc-influencer-page'); ?></span>
    </p>
    <?php

    // coupon usage limit
    ?>
    <p>
        <label for="sbwc_influencer_coupon_usage_limit"><b><i><?php _e('Usage Limit', 'sbwc-influencer-page'); ?></i></b></label>
    </p>
    <p>
        <input type="text" class="form-field regular-text" name="sbwc_influencer_coupon_usage_limit" id="sbwc_influencer_coupon_usage_limit" value="<?php echo $coupon_usage_limit; ?>" />
        <icon>?</icon><span class="ip-help" style="display: none;"> <?php _e('The number of times this coupon can be used by all customers before being invalid. Set to -1 for unlimited usage.', 'sbwc-influencer-page'); ?></span>
    </p>

    <style>
        icon {
            background: #ccc;
            color: #666;
            width: 20px;
            display: inline-block;
            text-align: center;
            border-radius: 50%;
            position: relative;
            top: 2px;
            cursor: pointer;
        }

        span.ip-help {
            font-style: italic;
            position: relative;
            top: 1px;
            left: 5px;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('icon').hover(function() {
                $(this).next().fadeToggle();
            });
        });
    </script>
<?php

}
?>