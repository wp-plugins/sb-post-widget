<?php
/*
Plugin Name: SB Post Widget
Plugin URI: http://hocwp.net/
Description: SB Post Widget is a plugin that allows to show custom post on sidebar.
Author: SB Team
Version: 1.0.2
Author URI: http://hocwp.net/
Text Domain: sb-post-widget
Domain Path: /languages/
*/

define('SB_POST_WIDGET_FILE', __FILE__);

define('SB_POST_WIDGET_PATH', untrailingslashit(plugin_dir_path(SB_POST_WIDGET_FILE)));

define('SB_POST_WIDGET_URL', plugins_url('', SB_POST_WIDGET_FILE));

define('SB_POST_WIDGET_INC_PATH', SB_POST_WIDGET_PATH . '/inc');

define('SB_POST_WIDGET_BASENAME', plugin_basename(SB_POST_WIDGET_FILE));

define('SB_POST_WIDGET_DIRNAME', dirname(SB_POST_WIDGET_BASENAME));

require SB_POST_WIDGET_INC_PATH . '/sb-plugin-functions.php';