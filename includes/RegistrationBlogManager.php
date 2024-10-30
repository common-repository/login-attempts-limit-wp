<?php

namespace Krut1LoginAttemptsLimitWp;

/**
 * Class RegistrationBlogManager
 *
 * @package Krut1LoginAttemptsLimitWp
 */
class RegistrationBlogManager extends TableManager
{
    public $tableName = 'login_attempts_limit_wp_blog_registration_attempts';

    /**
     * Update row in DB if register of new blog successful
     *
     * @param string $domain     The requested domain.
     * @param string $path       The requested path.
     * @param string $title      The requested site title.
     * @param string $user       The user's requested login name.
     * @param string $user_email The user's email address.
     * @param string $key        The user's activation key.
     * @param array  $meta       Signup meta data. By default, contains the requested privacy setting and lang_id.
     * @throws \Exception
     */
    public function registerBlogSuccessful($domain, $path, $title, $user, $user_email, $key, $meta): void
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
    public function preventBlogRegistration(array $result): array
    {
        global $wpdb;

        $registrationAttemptsRow = $this->getRowByIp();

        // If we have already a record about this ip
        if ($registrationAttemptsRow !== null) {
            // If attempts limit was reached
            if ($registrationAttemptsRow['attempts'] >= Options::getInstance()->get('blog_registration_attempts_max')) {
                $allowAfter = (new \DateTime($registrationAttemptsRow['last_time']))->add(new \DateInterval(Options::getInstance()->get('blog_registration_attempts_timeout')));

                // If the required break time has not yet passed
                if ($allowAfter > new \DateTime()) {
                    $this->increaseAttempts($registrationAttemptsRow, true);
                    $result['errors']->add('blogname', sprintf(__('You have reached registration attempts. Please try again after <strong>%s</strong> minutes.', 'login-attempts-limit-wp'), $this->getMinutes((new\ DateTime()), $allowAfter)));
                } else {
                    // Clear restrictions for this user because the time has passed
                    $this->resetAttempts($registrationAttemptsRow['id']);
                }
            }
        }

        return $result;
    }
}
