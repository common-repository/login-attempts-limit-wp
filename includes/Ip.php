<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class Ip
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class Ip
{
    /**
     * @var Ip
     */
    private static $instance;

    /**
     * Ip of current user
     *
     * @var string
     */
    private $ip;

    /**
     * @return Ip
     */
    public static function instance(): Ip
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get Ip of user
     *
     * @return string
     */
    public function getIp(): string
    {
        if ($this->ip !== null) {
            return $this->ip;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                $this->ip = $ips[0];

                return $this->ip;
            }

            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

            return $this->ip;
        }

        $this->ip = $_SERVER['REMOTE_ADDR'];

        return $this->ip;
    }
}
