<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Query currently active theme template files, adds support for influencer post type and inserts files into theme directory
 */
add_action('wp_admin', function () {

    // check if current theme is child theme; if true, get parent theme page templates, else just get parent theme page templates
    if (is_child_theme()) :
        $parent_theme_page_templates = wp_get_theme(get_template())->get_page_templates();
    else :
        $parent_theme_page_templates = wp_get_theme()->get_page_templates();
    endif;

    // write templates to plugin template names to text file
    $plugin_template_names = fopen(plugin_dir_path(__FILE__) . 'templates/template-names.txt', 'w') or error_log('Unable to open file!');

    // write template names to the text file
    fwrite($plugin_template_names, print_r($parent_theme_page_templates, true));

    // close the file
    fclose($plugin_template_names);

    // loop through each template, add support for influencer post type and insert into theme directory
    foreach ($parent_theme_page_templates as $location => $name) :

        // Parse PHP page template file contents to array, minus new and empty lines.
        $file_arr = file(get_parent_theme_file_path($location), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Search for comment block and add post type support tag to it.
        $key = array_search('/*', $file_arr, true);
        if ($key) :
            $file_arr[$key] = '/* Template Post Type: influencer_page';
        endif;

        // Modify page template name to avoid conflicts with original templates when writing to theme directory.
        $template_name = str_replace('page-', 'sbip-', $location);

        // Write modified file with proper line breaks to plugin templates directory for future reference.
        $fp_pi  = fopen(plugin_dir_path(__FILE__) . 'templates/' . $template_name, 'w');
        $fp_thm = fopen(get_stylesheet_directory() . '/' . $template_name, 'w');
        fwrite($fp_pi, implode(PHP_EOL, $file_arr));
        fwrite($fp_thm, implode(PHP_EOL, $file_arr));
        fclose($fp_pi);
        fclose($fp_thm);

    endforeach;
});
