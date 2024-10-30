<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class ResourceManager
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class Resources
{
    /**
     * Register files for plugin
     */
    public static function addResources(): void
    {
        // Settings page
        if (isset($_GET['page']) && $_GET['page'] === 'login_attempts_limit_wp_settings') {
            wp_enqueue_style('login_attempts_limit_wp_admin_css', KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_URL . 'css/admin.css', [], KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_VERSION);
        }
    }
}
