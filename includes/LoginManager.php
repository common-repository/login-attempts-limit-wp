<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class LoginManager
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class LoginManager extends TableManager
{
    public $tableName = 'login_attempts_limit_wp_failed_login_attempts';

    /**
     * Update row in DB if login failed
     *
     * @param $username
     * @throws \Exception
     */
    public function loginFailed($username): void
    {
        global $wpdb;

        // If ip is in a white list, we don't save attempts
        if (\in_array(Ip::instance()->getIp(), Options::getInstance()->get('white_ips') ?? [], true)) {
            return;
        }

        $loginFailedRow = $this->getRowByIp();

        if ($loginFailedRow === null) {
            $wpdb->insert($this->getTableName(), ['ip' => ip2long(Ip::instance()->getIp()), 'attempts' => 1, 'total_attempts' => 1]);
        } else {
            $this->increaseAttempts($loginFailedRow);
        }
    }

    /**
     * @param \WP_User|\WP_Error|null $user
     * @param string                  $username
     * @param string                  $password
     * @return \WP_User|\WP_Error|null
     * @throws \Exception
     */
    public function preventAuthenticate($user, $username, $password)
    {
        global $wpdb;

        $loginFailedRow = $this->getRowByIp();

        // If we have already a record about this ip
        if ($loginFailedRow !== null) {
            // If attempts limit was reached
            if ($loginFailedRow['attempts'] >= Options::getInstance()->get('login_attempts_max')) {
                $allowAfter = (new \DateTime($loginFailedRow['last_time']))->add(new \DateInterval(Options::getInstance()->get('login_attempts_timeout')));

                // If the required break time has not yet passed
                if ($allowAfter > new \DateTime()) {
                    $this->increaseAttempts($loginFailedRow, true);
                    $user = new \WP_Error(403, sprintf(__('You have reached failed login attempts. Please try again after <strong>%s</strong> minutes.', 'login-attempts-limit-wp'), $this->getMinutes((new\ DateTime()), $allowAfter)));
                } else {
                    // Clear restrictions for this user because the time has passed
                    $this->resetAttempts($loginFailedRow['id']);
                }
            }
        }

        return $user;
    }
}
