<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class DdStructure
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class DdStructure
{
    /**
     * Create table in DB
     */
    public static function createTables(): void
    {
        $loginTableName = LoginManager::getInstance()->getTableName();
        $blogRegistrationTableName = RegistrationBlogManager::getInstance()->getTableName();
        $userRegistrationTableName = RegistrationUserManager::getInstance()->getTableName();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS `{$loginTableName}` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` INT UNSIGNED NOT NULL,
                `attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `total_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `blocked_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,                
                `last_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE (ip)
                );";

        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `{$blogRegistrationTableName}` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` INT UNSIGNED NOT NULL,
                `attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `total_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `blocked_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `last_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE (ip)
                );";

        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `{$userRegistrationTableName}` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` INT UNSIGNED NOT NULL,
                `attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `total_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `blocked_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                `last_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE (ip)
                );";

        dbDelta($sql);
    }

    /**
     * Drop table from DB
     */
    public static function dropTables(): void
    {
        global $wpdb;

        $loginTableName = LoginManager::getInstance()->getTableName();
        $registrationBlogTableName = RegistrationBlogManager::getInstance()->getTableName();
        $registrationUserTableName = RegistrationUserManager::getInstance()->getTableName();

        $wpdb->query("DROP TABLE IF EXISTS `{$loginTableName}`;");
        $wpdb->query("DROP TABLE IF EXISTS `{$registrationBlogTableName}`;");
        $wpdb->query("DROP TABLE IF EXISTS `{$registrationUserTableName}`;");
    }

    /**
     * Check version and do upgrade
     */
    public static function doUpgrade(): void
    {
        if (Options::getInstance()->get('current_version') === KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_VERSION) {
            return;
        }

        self::dropTables();
        self::createTables();

        Options::getInstance()->set('current_version', KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_VERSION);
    }
}
