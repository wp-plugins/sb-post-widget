<?php
function sb_post_widget_init() {
    register_widget('SB_Post_Widget');
}
add_action('widgets_init', 'sb_post_widget_init');

function sb_post_widget_style_and_script() {
    if(sb_post_widget_testing()) {
        wp_register_style('sb-post-widget-style', SB_POST_WIDGET_URL . '/css/sb-post-widget-style.css');
    } else {
        wp_register_style('sb-post-widget-style', SB_POST_WIDGET_URL . '/css/sb-post-widget-style.min.css');
    }
    wp_enqueue_style('sb-post-widget-style');
}
add_action('wp_enqueue_scripts', 'sb_post_widget_style_and_script');

function sb_post_widget_admin_style_and_script() {
    $screen = get_current_screen();
    if('widgets' == $screen->base) {
        if(sb_post_widget_testing()) {
            wp_register_script('sb-post-widget', SB_POST_WIDGET_URL . '/js/sb-post-widget-admin-script.js', array('jquery'), false, true);
        } else {
            wp_register_script('sb-post-widget', SB_POST_WIDGET_URL . '/js/sb-post-widget-admin-script.min.js', array('jquery'), false, true);
        }
        wp_enqueue_script('sb-post-widget');
    }
}
add_action('admin_enqueue_scripts', 'sb_post_widget_admin_style_and_script');