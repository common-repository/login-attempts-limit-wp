<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class Autoloader
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class Autoloader
{
    /**
     * Run autoloader.
     */
    public static function run(): void
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Autoload.
     *
     * For a given class, check if it exist and load it.
     *
     * @param string $class Class name.
     */
    private static function autoload($class): void
    {
        $realClassName = str_replace(__NAMESPACE__ . '\\', '', $class);

        $filePath = KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_PATH . 'includes/' . $realClassName . '.php';

        if (file_exists($filePath) && is_readable($filePath)) {
            require_once $filePath;
        }
    }
}
