<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class TextDomain
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class TextDomain
{
    public static $domainName = 'login-attempts-limit-wp';

    public static function registerTranslations(): void
    {
        \load_plugin_textdomain(self::$domainName, false, KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_DIR_NAME . '/languages/');
    }
}
