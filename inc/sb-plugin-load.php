<?php
require SB_POST_WIDGET_INC_PATH . '/sb-plugin-install.php';

if(!sb_post_widget_check_core()) {
    return;
}

require SB_POST_WIDGET_INC_PATH . '/sb-plugin-functions.php';

require SB_POST_WIDGET_INC_PATH . '/class-sb-post-widget.php';

require SB_POST_WIDGET_INC_PATH . '/sb-plugin-admin.php';

require SB_POST_WIDGET_INC_PATH . '/sb-plugin-hook.php';