<?php
function sb_post_widget_menu() {
    SB_Admin_Custom::add_submenu_page('SB Post Widget', 'sb_post_widget', array('SB_Admin_Custom', 'setting_page_callback'));
}
add_action('sb_admin_menu', 'sb_post_widget_menu');

function sb_post_widget_tab($tabs) {
    $tabs['sb_post_widget'] = array('title' => 'SB Post Widget', 'section_id' => 'sb_post_widget_section', 'type' => 'plugin');
    return $tabs;
}
add_filter('sb_admin_tabs', 'sb_post_widget_tab');

function sb_post_widget_setting_field() {
    SB_Admin_Custom::add_section('sb_post_widget_section', __('SB Post Widget options page', 'sb-post-widget'), 'sb_post_widget');
    SB_Admin_Custom::add_setting_field('sb_post_widget_no_thumbnail', __('No thumbnail image', 'sb-post-widget'), 'sb_post_widget_section', 'sb_post_widget_no_thumbnail_callback', 'sb_post_widget');
}
add_action('sb_admin_init', 'sb_post_widget_setting_field');

function sb_post_widget_no_thumbnail_callback() {
    $id = 'sb_post_widget_no_thumbnail';
    $name = 'sb_options[post_widget][no_thumbnail]';
    $options = SB_Option::get();
    $value = isset($options['post_widget']['no_thumbnail']) ? $options['post_widget']['no_thumbnail'] : '';
    $description = __('You can enter url or upload new image file.', 'sb-post-widget');
    SB_Field::media_image($id, $name, $value, $description);
}