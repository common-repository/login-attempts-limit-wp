<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class RegistrationUserManager
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class RegistrationUserManager extends TableManager
{
    public $tableName = 'login_attempts_limit_wp_user_registration_attempts';

    /**
     * Link to registerUserSuccessful
     *
     * @param       $user_login
     * @param       $user_email
     * @param       $key
     * @param array $meta
     * @throws \Exception
     */
    public function registerUserSuccessfulMultisite($user_login, $user_email, $key, $meta = array()): void
    {
        $this->registerUserSuccessful(0);
    }

    /**
     * Update row in DB if register of new user successful
     *
     * @param int $userId
     * @throws \Exception
     */
    public function registerUserSuccessful(int $userId): void
    {
        global $wpdb;

        // If ip is in a white list, we don't save attempts
        if (\in_array(Ip::instance()->getIp(), Options::getInstance()->get('white_ips') ?? [], true)) {
            return;
        }

        $registrationAttemptsRow = $this->getRowByIp();

        if ($registrationAttemptsRow === null) {
            $wpdb->insert($this->getTableName(), ['ip' => ip2long(Ip::instance()->getIp()), 'attempts' => 1, 'total_attempts' => 1]);
        } else {
            $this->increaseAttempts($registrationAttemptsRow);
        }
    }

    /**
     * Prevent registration if limit is reached
     *
     * @param array $result
     * @return array
     * @throws \Exception
     */
    public function preventUserRegistrationMultisite(array $result): array {
        $error = $this->getErrorUserRegistration();

        if ($error !== false) {
            $result['errors']->add('user_name', $error);
        }

        return $result;
    }

    /**
     * Prevent registration if limit is reached
     *
     * @param string $userLogin
     * @return string
     * @throws \Exception
     */
    public function preventUserRegistration(string $userLogin): string {
        $error = $this->getErrorUserRegistration();

        if ($error !== false) {
            \wp_die(new \WP_Error(403, $error));
        }

        return $userLogin;
    }

    /**
     * Get error if limit is reached
     *
     * @return bool|string
     * @throws \Exception
     */
    public function getErrorUserRegistration()
    {
        // Allow it always for admins
        if (\current_user_can('manage_options')) {
            return false;
        }

        $registrationAttemptsRow = $this->getRowByIp();

        // If we have already a record about this ip
        if ($registrationAttemptsRow !== null) {
            // If attempts limit was reached
            if ($registrationAttemptsRow['attempts'] >= Options::getInstance()->get('user_registration_attempts_max')) {
                $allowAfter = (new \DateTime($registrationAttemptsRow['last_time']))->add(new \DateInterval(Options::getInstance()->get('user_registration_attempts_timeout')));

                // If the required break time has not yet passed
                if ($allowAfter > new \DateTime()) {
                    $this->increaseAttempts($registrationAttemptsRow, true);
                    return sprintf(__('You have reached registration attempts. Please try again after <strong>%s</strong> minutes.', 'login-attempts-limit-wp'), $this->getMinutes((new\ DateTime()), $allowAfter));
                }

                // Clear restrictions for this user because the time has passed
                $this->resetAttempts($registrationAttemptsRow['id']);
            }
        }

        return false;
    }
}
