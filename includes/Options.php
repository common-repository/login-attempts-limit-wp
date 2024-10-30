<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class Options
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class Options
{
    /**
     * @var Options
     */
    private static $instance;

    private $defaultOptions = [
        'login_attempts_max' => 3,
        'login_attempts_timeout' => 'PT5M',
        'blog_registration_attempts_max' => 1,
        'blog_registration_attempts_timeout' => 'PT30M',
        'user_registration_attempts_max' => 1,
        'user_registration_attempts_timeout' => 'PT15M',
        'next_show_rate_notice_time' => 0,
    ];

    /**
     * Options
     *
     * @var array
     */
    private $options;

    private $blogOptionsName = 'login_attempts_limit_wp_options';

    /**
     * @return Options
     */
    public static function getInstance(): Options
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->loadOptions();
    }

    /**
     * Load options into class from db
     */
    private function loadOptions(): void
    {
        $this->options = Plugin::isNetworkActive() ? \get_site_option($this->blogOptionsName, []) : \get_option($this->blogOptionsName, []);
    }

    /**
     * Get option by name
     *
     * @param string $name
     * @param null   $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this->options[$name] ?? $this->defaultOptions[$name] ?? $default;
    }

    /**
     * Set option
     *
     * @param string $name
     * @param        $value
     * @return bool
     */
    public function set(string $name, $value): bool
    {
        $this->options[$name] = $value;

        if (Plugin::isNetworkActive()) {
            return \update_site_option($this->blogOptionsName, $this->options);
        }

        return \update_option($this->blogOptionsName, $this->options);
    }
}
