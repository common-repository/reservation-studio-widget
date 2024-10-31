<?php
/*
Plugin Name: Reservation.Studio widget
Plugin URI: https://help.reservation.studio
Description: Reservation.Studio WordPress booking widget.
Version: 2.0.2
Author: Reservation.Studio
Author URI: https://reservation.studio
License: GPLv2
*/
include 'updater.php';

if (!function_exists('add_action')) {
    exit;
}

class RSPlugin
{
    public function __construct()
    {
        add_action('wp_footer', [$this, 'addScript']);
    }

    public static function initSettings()
    {
        add_menu_page(
            __('RS Widget Settings', 'textdomain'),
            'RS Widget',
            'manage_options',
            'reservation-studio-widget/admin.php',
            '',
            plugins_url('reservation-studio-widget/assets/images/icon.svg'),
            100
        );
    }

    public function addScript()
    {
        $enabled = get_option('rs_widget_enabled');
        if (!$enabled) {
            return;
        }

        echo '
        <script> 
            !function(b,c,d){var a,e=b.getElementsByTagName(c)[0];b.getElementById(d)||((a=b.createElement(c)).id=d,a.src="https://js-widget.reservation.studio/v2/widget.min.js",e.parentNode.insertBefore(a,e),a.onload=function(){window.RSWidget(' . $this->getJsWidgetConfig() . ')})}(document,"script","rs-booking-widget-js")
        </script>';
    }

    private function getJsWidgetConfig()
    {
        $config = [
            'modal' => []
        ];
        $config['type'] = get_option('rs_widget_page_type');
        $config['slug'] = get_option('rs_widget_slug');

        $language = get_option('rs_widget_language');
        if ($language) {
            $config['language'] = $language;
        }

        $stickyButtonEnabled = get_option('rs_widget_sticky_button_enabled');
        if ($stickyButtonEnabled) {
            $config['sticky_button'] = [];

            $stickyButtonText = get_option('rs_widget_sticky_button_text');
            if ($stickyButtonText) {
                $config['sticky_button']['text'] = $stickyButtonText;
            }

            $stickyButtonTextColor = get_option('rs_widget_sticky_button_text_color');
            if ($stickyButtonTextColor) {
                $config['sticky_button']['text_color'] = $stickyButtonTextColor;
            }

            $stickyButtonBackgroundColor = get_option('rs_widget_sticky_button_Background_color');
            if ($stickyButtonBackgroundColor) {
                $config['sticky_button']['background_color'] = $stickyButtonBackgroundColor;
            }

            $stickyButtonPosition = get_option('rs_widget_sticky_button_position');
            if ($stickyButtonPosition) {
                $config['sticky_button']['position'] = $stickyButtonPosition;
            }

            $stickyTooltipText = get_option('rs_widget_sticky_tooltip_text');
            if ($stickyTooltipText) {
                $config['sticky_button']['tooltip_text'] = $stickyTooltipText;
            }

            $stickyTooltipShowDelay = get_option('rs_widget_sticky_tooltip_show_delay');
            if ($stickyTooltipShowDelay) {
                $config['sticky_button']['tooltip_show_delay'] = $stickyTooltipShowDelay;
            }

            $stickyTooltipExpireTime = get_option('rs_widget_sticky_tooltip_expire_time');
            if ($stickyTooltipExpireTime) {
                $config['sticky_button']['tooltip_expire_time'] = $stickyTooltipExpireTime;
            }
        }

        $buttonSelector = get_option('rs_widget_buttons_selector');
        if (!empty($buttonSelector)) {
            $config['buttons_selector'] = $buttonSelector;
        }

        $modalMaxWidth = get_option('rs_widget_modal_max_width');
        if (!empty($modalMaxWidth)) {
            $config['modal']['max_width'] = (string) $modalMaxWidth;
        }

        $modalMaxHeight = get_option('rs_widget_modal_max_height');
        if (!empty($modalMaxWidth)) {
            $config['modal']['max_height'] = (string) $modalMaxHeight;
        }

        return json_encode($config);
    }

}

$RSPlugin = new RSPlugin();

if (is_admin()) {
    add_action('admin_menu', ['RSPlugin', 'initSettings']);
}