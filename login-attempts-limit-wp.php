<?php
/**
 * Plugin Name: LOGIN AND REGISTRATION ATTEMPTS LIMIT
 * Plugin URI: https://bitbucket.org/krugerman/wp-plugins
 * Description: Protects your blog from password guessing and from auto registrations.
 * Text Domain: login-attempts-limit-wp
 * Domain Path: /languages
 * Version: 2.1
 * Author: German Krutov
 * Author URI: https://profiles.wordpress.org/krut1/
 * License: GPLv3 or later
 * Tested up to: 5.4
 */

define('KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_PATH', plugin_dir_path(__FILE__));
define('KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_URL', plugins_url('/', __FILE__));
define('KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_FILE', plugin_basename(__FILE__));
define('KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_DIR_NAME', basename(__DIR__));

define('KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_VERSION', '2.1');

// Load main Class
require KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_PATH . 'includes/Plugin.php';

// Activation hook
register_activation_hook(__FILE__, [\Krut1LoginAttemptsLimitWp\DdStructure::class, 'createTables']);
// Deactivation hook
register_deactivation_hook(__FILE__, [\Krut1LoginAttemptsLimitWp\DdStructure::class, 'dropTables']);
