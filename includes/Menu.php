<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class Menu
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class Menu
{
    /**
     * Add menu elements for plugin
     */
    public static function addMenu(): void
    {
        $menuPrefix = Plugin::isNetworkActive() ? 'network_' : '';
        $actionsLinksPrefix = Plugin::isNetworkActive() ? 'network_admin_' : '';

        // Add network menu page
        \add_action($menuPrefix . 'admin_menu', [__CLASS__, 'createMenu']);

        // Add settings link on plugin page
        \add_filter($actionsLinksPrefix . 'plugin_action_links_' . KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_FILE, [__CLASS__, 'addSettingsPage']);
    }

    /**
     * Add menu to admin panel
     */
    public static function createMenu(): void
    {
        $capability = Plugin::isNetworkActive() ? 'manage_network' : 'manage_options';

        add_menu_page(
            __('Login attempts', 'login-attempts-limit-wp'),
            __('Login attempts', 'login-attempts-limit-wp'),
            $capability,
            'login_attempts_limit_wp_settings',
            [Page::class, 'settingsPage'],
            'dashicons-shield'
        );
    }

    /**
     * @param $links
     * @return mixed
     */
    public static function addSettingsPage($links)
    {
        $settings_link = '<a href="admin.php?page=login_attempts_limit_wp_settings">' . __('Settings', 'login-attempts-limit-wp') . '</a>';
        array_unshift($links, $settings_link);

        return $links;
    }
}
