<?php
defined('ABSPATH') OR exit;

function sb_post_widget_check_core() {
    $activated_plugins = get_option('active_plugins');

    $sb_core_installed = in_array('sb-core/sb-core.php', $activated_plugins);

    if(!$sb_core_installed) {
        $sb_plugins = array(SB_POST_WIDGET_BASENAME);
        $activated_plugins = get_option('active_plugins');
        $activated_plugins = array_diff($activated_plugins, $sb_plugins);
        update_option('active_plugins', $activated_plugins);
    }
    return $sb_core_installed;
}

sb_post_widget_check_core();

function sb_post_widget_activation() {
    if(!sb_post_widget_check_core()) {
        wp_die(sprintf(__('You must install plugin %1$s first! Click here to %2$s.', 'sb-post-widget'), '<a href="https://wordpress.org/plugins/sb-core/">SB Core</a>', sprintf('<a href="%1$s">%2$s</a>', admin_url('plugins.php'), __('go back', 'sb-post-widget'))));
    }
    do_action('sb_post_widget_activation');
}
register_activation_hook(SB_POST_WIDGET_FILE, 'sb_post_widget_activation');

function sb_post_widget_settings_link($links) {
    if(sb_post_widget_check_core()) {
        $settings_link = sprintf('<a href="admin.php?page=sb_post_widget">%s</a>', __('Settings', 'sb-post-widget'));
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links_' . SB_POST_WIDGET_BASENAME, 'sb_post_widget_settings_link');

function sb_post_widget_textdomain() {
    load_plugin_textdomain( 'sb-post-widget', false, SB_POST_WIDGET_DIRNAME . '/languages/' );
}
add_action('plugins_loaded', 'sb_post_widget_textdomain');

function sb_post_widget_init() {
    register_widget('SB_Post_Widget');
}
add_action('widgets_init', 'sb_post_widget_init');

function sb_post_widget_style_and_script() {
    wp_register_style('sb-post-widget-style', SB_POST_WIDGET_URL . '/css/sb-post-widget-style.css');
    wp_enqueue_style('sb-post-widget-style');
}
add_action('wp_enqueue_scripts', 'sb_post_widget_style_and_script');

function sb_post_widget_admin_style_and_script() {
    $screen = get_current_screen();
    if ( 'widgets' == $screen->base ) {
        wp_register_script('sb-post-widget', SB_POST_WIDGET_URL . '/js/sb-post-widget-script.js', array('jquery'), false, true);
        wp_enqueue_script('sb-post-widget');
    }
}
add_action('admin_enqueue_scripts', 'sb_post_widget_admin_style_and_script');

require SB_POST_WIDGET_INC_PATH . '/sb-plugin-load.php';