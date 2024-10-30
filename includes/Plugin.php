<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class Plugin
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class Plugin
{
    /**
     * @var Plugin
     */
    public static $instance;

    /**
     * @var bool Is plugin active for network
     */
    private static $networkActive;

    /**
     * @return Plugin
     */
    public static function getInstance(): Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Plugin constructor
     */
    private function __construct()
    {
        $this->registerAutoloader();

        // Load translations
        TextDomain::registerTranslations();

        // Add menu
        Menu::addMenu();

        // On init
        \add_action('admin_init', [$this, 'adminInit']);

        // 1. Save login failed counts
        \add_action('wp_login_failed', [LoginManager::getInstance(), 'loginFailed']);

        // Disable authentication if limit was reached
        \add_filter('authenticate', [LoginManager::getInstance(), 'preventAuthenticate'], 30, 3);

        // 2. Save user registration
        \add_action('after_signup_user', [RegistrationUserManager::getInstance(), 'registerUserSuccessfulMultisite'], 10, 4);
        \add_action('user_register', [RegistrationUserManager::getInstance(), 'registerUserSuccessful']);

        // Disable registration if limit was reached
        \add_filter('wpmu_validate_user_signup', [RegistrationUserManager::getInstance(), 'preventUserRegistrationMultisite']);
        \add_filter('pre_user_login', [RegistrationUserManager::getInstance(), 'preventUserRegistration']);

        // 3. Save blog registration
        \add_action('after_signup_site', [RegistrationBlogManager::getInstance(), 'registerBlogSuccessful'], 10, 7);

        // Disable registration if limit was reached
        \add_action('wpmu_validate_blog_signup', [RegistrationBlogManager::getInstance(), 'preventBlogRegistration']);

        // Rating notices
        \add_action('admin_notices', [$this, 'adminNotices']);
        \add_action('network_admin_notices', [$this, 'adminNotices']);
    }

    /**
     * On admin init
     */
    public function adminInit(): void
    {
        // Do upgrade
        DdStructure::doUpgrade();

        // Add css
        Resources::addResources();
    }

    /**
     * Register autoloader
     */
    private function registerAutoloader(): void
    {
        require_once KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_PATH . 'includes/Autoloader.php';

        Autoloader::run();
    }

    /**
     * If plugin network activated
     *
     * @return bool
     */
    public static function isNetworkActive(): bool
    {
        if (self::$networkActive !== null) {
            return self::$networkActive;
        }

        // Makes sure the plugin is defined before trying to use it
        if (!function_exists('is_plugin_active_for_network')) {
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        }

        self::$networkActive = \is_multisite() && \is_plugin_active_for_network('login-attempts-limit-wp/login-attempts-limit-wp.php');

        return self::isNetworkActive();
    }

    /**
     * Show admin notice (rate me)
     */
    public function adminNotices(): void
    {
        // Hide notice for 1/4 year
        if (isset($_GET['login_attempts_limit_wp_hide_rate_notice']) && (int)$_GET['login_attempts_limit_wp_hide_rate_notice'] === 1) {
            Options::getInstance()->set('next_show_rate_notice_time', time() + 7776000);
        }

        $currentTime = time();
        // Test mail was successful + notice is not disabled + the plugin is working more than 10 days
        if ($this->hasStatistic() && Options::getInstance()->get('next_show_rate_notice_time', 0) < $currentTime) {
            echo '<div class="notice notice-success is-dismissible"><p><img style="float:left;margin:-6px 10px 0 -6px;border-radius:50%" class="theme-author-img" src="/wp-content/plugins/login-attempts-limit-wp/img/avatar-author.png" alt="' . __( 'Plugin author', 'login-attempts-limit-wp' ) . '" width="32"> <strong style="color:#4d820c">' . __( 'Hey you! I\'m German, the plugin author of LOGIN ATTEMPTS LIMIT WP.', 'login-attempts-limit-wp' ) . '</strong> ' . __( 'Do you like this plugin? Please show your appreciation and rate the plugin. Help me to develop a powerful plugin that will benefit you for a long time.', 'login-attempts-limit-wp' ) . ' <span style="font-size:17px;margin:0 -2px;color:rgba(208,174,71,0.57)" class="dashicons dashicons-star-filled"></span> <span style="font-size:17px;margin:0 -2px;color:rgba(208,174,71,0.57)" class="dashicons dashicons-star-filled"></span> <span style="font-size:17px;margin:0 -2px;color:rgba(208,174,71,0.57)" class="dashicons dashicons-star-filled"></span> <span style="font-size:17px;margin:0 -2px;color:rgba(208,174,71,0.57)" class="dashicons dashicons-star-filled"></span> <span style="font-size:17px;margin:0 -2px;color:rgba(208,174,71,0.57)" class="dashicons dashicons-star-filled"></span> &nbsp;&nbsp;&nbsp;<a href="https://wordpress.org/support/plugin/login-attempts-limit-wp/reviews/#new-post" target="_blank">' . __( 'Rate now!', 'login-attempts-limit-wp' ) . '</a> &nbsp;&nbsp;&nbsp;<a style="color: lightgrey; text-decoration: none;" href="index.php?login_attempts_limit_wp_hide_rate_notice=1">' . __( 'I have already rated.', 'login-attempts-limit-wp' ) . '</a></p></div>';
        }
    }

    /**
     * If we have already a stat for owner
     *
     * @return bool
     */
    public function hasStatistic(): bool
    {
        if (count(RegistrationBlogManager::getInstance()->getMostPopularRows()) > 0) {
            return true;
        }

        if (count(RegistrationUserManager::getInstance()->getMostPopularRows()) > 0) {
            return true;
        }

        if (count(LoginManager::getInstance()->getMostPopularRows()) > 0) {
            return true;
        }

        return false;
    }
}

Plugin::getInstance();
