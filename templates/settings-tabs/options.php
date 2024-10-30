<?php

\defined('ABSPATH') || die('Keep Silent');

use Krut1LoginAttemptsLimitWp\Options;

?>
<?php
// Handle request
if (isset($_POST['login_attempts_limit_wp_options'])) {
    Options::getInstance()->set('login_attempts_max', (int)$_POST['login_attempts_limit_wp_options']['login_attempts_max']);
    Options::getInstance()->set('login_attempts_timeout', \sanitize_text_field($_POST['login_attempts_limit_wp_options']['login_attempts_timeout']));
    Options::getInstance()->set('blog_registration_attempts_max', (int)$_POST['login_attempts_limit_wp_options']['blog_registration_attempts_max']);
    Options::getInstance()->set('blog_registration_attempts_timeout', \sanitize_text_field($_POST['login_attempts_limit_wp_options']['blog_registration_attempts_timeout']));
    Options::getInstance()->set('user_registration_attempts_max', (int)$_POST['login_attempts_limit_wp_options']['user_registration_attempts_max']);
    Options::getInstance()->set('user_registration_attempts_timeout', \sanitize_text_field($_POST['login_attempts_limit_wp_options']['user_registration_attempts_timeout']));
    Options::getInstance()->set('white_ips', array_filter(array_map('trim', explode(PHP_EOL, \sanitize_textarea_field($_POST['login_attempts_limit_wp_options']['white_ips'])))));

    // Show message
    echo '<div class="notice notice-success"><p>' . __('Settings were saved.', 'login-attempts-limit-wp') . '</p></div>';
}

$timePeriodsSelect = [
    'PT5M' => __('5 minutes', 'login-attempts-limit-wp'),
    'PT15M' => __('15 minutes', 'login-attempts-limit-wp'),
    'PT30M' => __('30 minutes', 'login-attempts-limit-wp'),
    'PT1H' => __('1 hour', 'login-attempts-limit-wp'),
    'PT12H' => __('12 hours', 'login-attempts-limit-wp'),
    'P1D' => __('1 day', 'login-attempts-limit-wp')
];
?>
<form action="" method="POST">
    <table class="form-table">
        <tr>
            <th colspan="2"><?= __('User login', 'login-attempts-limit-wp'); ?></th>
        </tr>
        <tr>
            <td><label for="login_attempts_limit_wp_option_login_attempts_max"><?= __('Max login attempts', 'login-attempts-limit-wp'); ?></label></td>
            <td><input type="number" id="login_attempts_limit_wp_option_login_attempts_max" name="login_attempts_limit_wp_options[login_attempts_max]" value="<?= Options::getInstance()->get('login_attempts_max') ?? '' ?>"></td>
            <td rowspan="2"><p><?= sprintf(__('If a user for a certain amount of time (%1$s) tries to log in more than %2$d times, then he must take a break of %1$s.', 'login-attempts-limit-wp'), $timePeriodsSelect[Options::getInstance()->get('login_attempts_timeout')], Options::getInstance()->get('login_attempts_max')) ?></p></td>
        </tr>
        <tr>
            <td><label for="login_attempts_limit_wp_option_login_attempts_timeout"><?= __('Login attempts timeout', 'login-attempts-limit-wp'); ?></label></td>
            <td>
                <select id="login_attempts_limit_wp_option_login_attempts_timeout" name="login_attempts_limit_wp_options[login_attempts_timeout]">
                    <?php foreach ($timePeriodsSelect as $period => $name): ?>
                        <option value="<?= $period ?>"<?= Options::getInstance()->get('login_attempts_timeout') === $period ? ' selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th colspan="2"><?= __('User registration', 'login-attempts-limit-wp'); ?></th>
        </tr>
        <tr>
            <td><label for="login_attempts_limit_wp_option_user_registration_attempts_max"><?= __('Max registration attempts', 'login-attempts-limit-wp'); ?></label></td>
            <td><input type="number" id="login_attempts_limit_wp_option_user_registration_attempts_max" name="login_attempts_limit_wp_options[user_registration_attempts_max]" value="<?= Options::getInstance()->get('user_registration_attempts_max') ?? '' ?>"></td>
            <td rowspan="2"><p><?= sprintf(__('If a user for a certain amount of time (%1$s) tries to register more than %2$d accounts, then he must take a break of %1$s.', 'login-attempts-limit-wp'), $timePeriodsSelect[Options::getInstance()->get('user_registration_attempts_timeout')], Options::getInstance()->get('blog_registration_attempts_max')) ?></p></td>
        </tr>
        <tr>
            <td><label for="login_attempts_limit_wp_option_user_registration_attempts_timeout"><?= __('Registration attempts timeout', 'login-attempts-limit-wp'); ?></label></td>
            <td>
                <select id="login_attempts_limit_wp_option_user_registration_attempts_timeout" name="login_attempts_limit_wp_options[user_registration_attempts_timeout]">
                    <?php foreach ($timePeriodsSelect as $period => $name): ?>
                        <option value="<?= $period ?>"<?= Options::getInstance()->get('user_registration_attempts_timeout') === $period ? ' selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php if (is_main_site()): ?>
            <tr>
                <th colspan="2"><?= __('Blog registration', 'login-attempts-limit-wp'); ?></th>
            </tr>
            <tr>
                <td><label for="login_attempts_limit_wp_option_blog_registration_attempts_max"><?= __('Max registration attempts', 'login-attempts-limit-wp'); ?></label></td>
                <td><input type="number" id="login_attempts_limit_wp_option_blog_registration_attempts_max" name="login_attempts_limit_wp_options[blog_registration_attempts_max]" value="<?= Options::getInstance()->get('blog_registration_attempts_max') ?? '' ?>"></td>
                <td rowspan="2">
                    <p><?= sprintf(__('If a user for a certain amount of time (%1$s) tries to register more than %2$d blogs, then he must take a break of %1$s.', 'login-attempts-limit-wp'), $timePeriodsSelect[Options::getInstance()->get('blog_registration_attempts_timeout')], Options::getInstance()->get('blog_registration_attempts_max')) ?></p>
                    <p><small>* <?= __('This option works only in multisite installations. And it can be configured only in main blog.', 'login-attempts-limit-wp') ?></small></p>
                </td>
            </tr>
            <tr>
                <td><label for="login_attempts_limit_wp_option_blog_registration_attempts_timeout"><?= __('Registration attempts timeout', 'login-attempts-limit-wp'); ?></label></td>
                <td>
                    <select id="login_attempts_limit_wp_option_blog_registration_attempts_timeout" name="login_attempts_limit_wp_options[blog_registration_attempts_timeout]">
                        <?php foreach ($timePeriodsSelect as $period => $name): ?>
                            <option value="<?= $period ?>"<?= Options::getInstance()->get('blog_registration_attempts_timeout') === $period ? ' selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th colspan="2"><?= __('White list', 'login-attempts-limit-wp'); ?></th>
        </tr>
        <tr>
            <td><label for="login_attempts_limit_wp_option_white_ips"><?= __('White list of IPs', 'login-attempts-limit-wp'); ?></label></td>
            <td><textarea id="login_attempts_limit_wp_option_white_ips" name="login_attempts_limit_wp_options[white_ips]"><?= implode(PHP_EOL, Options::getInstance()->get('white_ips') ?? []) ?></textarea></td>
            <td><p><?= __('Restrictions will not be applied for users with this IPs. Each IP on the new line.', 'login-attempts-limit-wp') ?></p></td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>
