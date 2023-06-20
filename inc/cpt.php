<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hook to init to register post type
 * 
 * @return void
 * 
 */
add_action('init', function () {

    /**
     * Post Type: Influencer Pages.
     */
    $labels = array(
        'name'                     => __('Influencer Pages', 'sbwc-influencer-page'),
        'singular_name'            => __('Influencer Page', 'sbwc-influencer-page'),
        'menu_name'                => __('Influencer Pages', 'sbwc-influencer-page'),
        'all_items'                => __('All Influencer Pages', 'sbwc-influencer-page'),
        'add_new'                  => __('Add new', 'sbwc-influencer-page'),
        'add_new_item'             => __('Add new Influencer Page', 'sbwc-influencer-page'),
        'edit_item'                => __('Edit Influencer Page', 'sbwc-influencer-page'),
        'new_item'                 => __('New Influencer Page', 'sbwc-influencer-page'),
        'view_item'                => __('View Influencer Page', 'sbwc-influencer-page'),
        'view_items'               => __('View Influencer Pages', 'sbwc-influencer-page'),
        'search_items'             => __('Search Influencer Pages', 'sbwc-influencer-page'),
        'not_found'                => __('No Influencer Pages found', 'sbwc-influencer-page'),
        'not_found_in_trash'       => __('No Influencer Pages found in trash', 'sbwc-influencer-page'),
        'parent'                   => __('Parent Influencer Page:', 'sbwc-influencer-page'),
        'featured_image'           => __('Featured image for this Influencer Page', 'sbwc-influencer-page'),
        'set_featured_image'       => __('Set featured image for this Influencer Page', 'sbwc-influencer-page'),
        'remove_featured_image'    => __('Remove featured image for this Influencer Page', 'sbwc-influencer-page'),
        'use_featured_image'       => __('Use as featured image for this Influencer Page', 'sbwc-influencer-page'),
        'archives'                 => __('Influencer Page archives', 'sbwc-influencer-page'),
        'insert_into_item'         => __('Insert into Influencer Page', 'sbwc-influencer-page'),
        'uploaded_to_this_item'    => __('Upload to this Influencer Page', 'sbwc-influencer-page'),
        'filter_items_list'        => __('Filter Influencer Pages list', 'sbwc-influencer-page'),
        'items_list_navigation'    => __('Influencer Pages list navigation', 'sbwc-influencer-page'),
        'items_list'               => __('Influencer Pages list', 'sbwc-influencer-page'),
        'attributes'               => __('Influencer Pages attributes', 'sbwc-influencer-page'),
        'name_admin_bar'           => __('Influencer Page', 'sbwc-influencer-page'),
        'item_published'           => __('Influencer Page published', 'sbwc-influencer-page'),
        'item_published_privately' => __('Influencer Page published privately.', 'sbwc-influencer-page'),
        'item_reverted_to_draft'   => __('Influencer Page reverted to draft.', 'sbwc-influencer-page'),
        'item_scheduled'           => __('Influencer Page scheduled', 'sbwc-influencer-page'),
        'item_updated'             => __('Influencer Page updated.', 'sbwc-influencer-page'),
        'parent_item_colon'        => __('Parent Influencer Page:', 'sbwc-influencer-page'),
    );

    $args = array(
        'label'                 => __('Influencer Pages', 'sbwc-influencer-page'),
        'labels'                => $labels,
        'description'           => 'CPT for Influencer Page pages',
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_rest'          => true,
        'rest_base'             => 'influencer-page',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'has_archive'           => false,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'delete_with_user'      => false,
        'exclude_from_search'   => false,
        'capability_type'       => 'page',
        'map_meta_cap'          => true,
        'hierarchical'          => true,
        'rewrite'               => array(
            'slug'       => 'influencer-page',
            'with_front' => true,
        ),
        'query_var'             => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-businessman',
        'supports'              => array('title', 'editor', 'thumbnail', 'page-attributes'),
    );

    register_post_type('influencer_page', $args);

    // check if we need to flush rewrite rules to get elementor to work and to avoid 404 errors for this post type
    if(get_option('sbwc_influencer_page_flush_rewrite_rules')):

        // flush rewrite rules
        flush_rewrite_rules();

        // delete the option
        delete_option('sbwc_influencer_page_flush_rewrite_rules');
    
    endif;
    
});

/**
 * Add PolyLang Support
 */
add_filter('pll_get_post_types', function ($post_types) {
    $post_types[] = 'influencer_page';
    return $post_types;
});
