<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hook into wp_footer to insert coupon code into container class ip-coupon-code
 * 
 * @return void
 */
add_action('wp_footer', function () {

    // bail if not on influencer page
    if (get_post_type() !== 'influencer_page') {
        return;
    }

    // get coupon code from post meta
    $coupon_code = get_post_meta(get_the_ID(), 'sbwc_influencer_coupon_code', true);

    // if coupon code not present, bail
    if (!$coupon_code) {
        return;
    }

    // JS script to insert coupon code into container class ip-coupon-code
?>
    <script>
        (function() {
            var couponCode = '<?php echo $coupon_code; ?>';
            var couponCodeContainer = document.querySelector('.ip-coupon-code');
            couponCodeContainer.innerHTML = couponCode;
        })();
    </script>
<?php

});
?>