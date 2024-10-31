<?php

// On plugin activation
register_activation_hook(__FILE__, 'pluginVersionUpdate');

/**
 * Updates the version of the plugin in the WordPress options table.
 *
 * If the get_plugin_data() function is not available, it includes the necessary file.
 * Retrieves the version of the plugin using the get_plugin_data() function.
 * Checks if the 'rs_widget_version' option exists in the options table.
 * If the option does not exist, adds it with the current version of the plugin.
 * If the option exists, updates it with the current version of the plugin.
 *
 * @return void
 */
function pluginVersionUpdate()
{
    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $pluginData = get_plugin_data(__DIR__ . '/widget.php');
    $version = $pluginData['Version'];

    $hasOption = get_option('rs_widget_version');
    if ($hasOption === false) {
        add_option('rs_widget_version', $version);
    } else {
        update_option('rs_widget_version', $version);
    }
}

function plugin_upgrader_process_complete()
{
    $currentVersion = get_option('rs_widget_version');

    if (version_compare('2.0', $currentVersion, '>=')) {
        plugin_update_to_v3();
    }
}

function plugin_update_to_v3()
{
    //var_dump('plugin_update_to_v2');
}

add_action('upgrader_process_complete', 'plugin_upgrader_process_complete');