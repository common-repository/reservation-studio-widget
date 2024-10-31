<?php
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

include_once 'config.php';

foreach ($rules as $key => $value) {
    delete_option('rs_widget_' . $key);
}


