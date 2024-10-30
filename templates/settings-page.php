<?php

\defined('ABSPATH') || die('Keep Silent');

$tabs = [
    'options' => __('Settings', 'login-attempts-limit-wp'),
    'statistic' => __('Statistic', 'login-attempts-limit-wp')
];

// Default current tab
$currentTab = 'options';

// Validate get params with tab name
if (isset($_GET['tab'], $tabs[$_GET['tab']])) {
    $currentTab = sanitize_text_field($_GET['tab']);
}

?>
<div class="wrap login-attempts-limit-wp">
    <h2><?= get_admin_page_title() ?></h2>
    <h2 class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab => $tabName) : ?>
            <a class="nav-tab<?= $currentTab === $tab ? ' nav-tab-active' : '' ?>" href="?page=login_attempts_limit_wp_settings&tab=<?= $tab ?>"><?= $tabName ?></a>
        <?php endforeach; ?>
    </h2>

    <?php
    // Include tabs template
    include KRUT1_LOGIN_ATTEMPTS_LIMIT_WP_PATH . "templates/settings-tabs/{$currentTab}.php";
    ?>
</div>