<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class Page
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class Page
{
    /**
     * Show setting page
     */
    public static function settingsPage(): void
    {
        include KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_PATH . 'templates/settings-page.php';
    }
}
