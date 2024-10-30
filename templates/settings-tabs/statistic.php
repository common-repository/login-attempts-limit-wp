<?php

use Krut1LoginAttemptsLimitWp\LoginManager;
use Krut1LoginAttemptsLimitWp\RegistrationBlogManager;
use Krut1LoginAttemptsLimitWp\RegistrationUserManager;

\defined('ABSPATH') || die('Keep Silent');

if (isset($_GET['clear_data']) && $_GET['clear_data'] === 'yes') {
    RegistrationBlogManager::getInstance()->clearTable();
    RegistrationUserManager::getInstance()->clearTable();
    LoginManager::getInstance()->clearTable();
    wp_redirect(admin_url('admin.php?page=login_attempts_limit_wp_settings&tab=statistic'));
}

$registrationBlogRows = RegistrationBlogManager::getInstance()->getMostPopularRows();
$registrationUserRows = RegistrationUserManager::getInstance()->getMostPopularRows();
$loginRows = LoginManager::getInstance()->getMostPopularRows();

?>
    <p><?= __('You can see on this page, how much times the plugin <b>LOGIN AND REGISTRATION ATTEMPTS LIMIT</b> protected your blog. The 10 most popular attackers are showing.', 'login-attempts-limit-wp') ?></p>

<?php if (count($loginRows) > 0): ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th><?= __('Login attempts', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Count', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Allowed', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Blocked', 'login-attempts-limit-wp') ?></th>
            <th><?= __('Last successful attempt', 'login-attempts-limit-wp') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($loginRows as $loginRow): ?>
            <tr>
                <td><?= long2ip($loginRow['ip']) ?></td>
                <td class="center"><?= $loginRow['total_attempts'] ?></td>
                <td class="center allowed-attempts"><?= $loginRow['total_attempts'] - $loginRow['blocked_attempts'] ?></td>
                <td class="center blocked-attempts"><?= $loginRow['blocked_attempts'] ?></td>
                <td><?= (new \DateTime($loginRow['last_time']))->format('d.m.Y H:i') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php if (count($registrationUserRows) > 0): ?>
    <br>
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th><?= __('Registration attempts', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Count', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Allowed', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Blocked', 'login-attempts-limit-wp') ?></th>
            <th><?= __('Last successful attempt', 'login-attempts-limit-wp') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registrationUserRows as $registrationUserRow): ?>
            <tr>
                <td><?= long2ip($registrationUserRow['ip']) ?></td>
                <td class="center"><?= $registrationUserRow['total_attempts'] ?></td>
                <td class="center allowed-attempts"><?= $registrationUserRow['total_attempts'] - $registrationUserRow['blocked_attempts'] ?></td>
                <td class="center blocked-attempts"><?= $registrationUserRow['blocked_attempts'] ?></td>
                <td><?= (new \DateTime($registrationUserRow['last_time']))->format('d.m.Y H:i') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php if (count($registrationBlogRows) > 0): ?>
    <br>
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th><?= __('Create new blog attempts', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Count', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Allowed', 'login-attempts-limit-wp') ?></th>
            <th class="center"><?= __('Blocked', 'login-attempts-limit-wp') ?></th>
            <th><?= __('Last successful attempt', 'login-attempts-limit-wp') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registrationBlogRows as $registrationBlogRow): ?>
            <tr>
                <td><?= long2ip($registrationBlogRow['ip']) ?></td>
                <td class="center"><?= $registrationBlogRow['total_attempts'] ?></td>
                <td class="center allowed-attempts"><?= $registrationBlogRow['total_attempts'] - $registrationBlogRow['blocked_attempts'] ?></td>
                <td class="center blocked-attempts"><?= $registrationBlogRow['blocked_attempts'] ?></td>
                <td><?= (new \DateTime($registrationBlogRow['last_time']))->format('d.m.Y H:i') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php
    if (count($registrationBlogRows) + count($registrationUserRows) + count($loginRows) === 0) {
        echo '<p>' . __('We don\'t have any statistic for you. Come back later.', 'login-attempts-limit-wp') . '</p>';
    } else {
        echo '<p><a href="?page=login_attempts_limit_wp_settings&tab=statistic&clear_data=yes" title="' . __('Clear all tables', 'login-attempts-limit-wp') . '">' . __('Reset all data', 'login-attempts-limit-wp') . '</a></p>';
    }
?>
