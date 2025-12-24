<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ДНП <?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- Плавающий переключатель поселков -->
<div id="floating-village-switcher">
    <div class="switcher-header">
        <span class="current-icon">
            <?php echo (get_current_village() == 'zapovednoe') ? '🌲' : '🌾'; ?>
        </span>
        <span class="current-name">
            <?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?>
        </span>
    </div>
    
    <div class="switcher-buttons">
        <?php if (current_user_can('administrator')): ?>
            <a href="?village=zapovednoe" class="switch-btn <?php echo (get_current_village() == 'zapovednoe') ? 'active' : ''; ?>">
                Заповедное
            </a>
            <a href="?village=kolosok" class="switch-btn <?php echo (get_current_village() == 'kolosok') ? 'active' : ''; ?>">
                Колосок
            </a>
            <a href="/wp-admin" class="admin-btn">
                Админка
            </a>
            <a href="?logout_village=1" class="logout-btn">
                Выйти
            </a>
        <?php else: ?>
            <a href="?village=zapovednoe" class="switch-btn <?php echo (get_current_village() == 'zapovednoe') ? 'active' : ''; ?>">
                Заповедное
            </a>
            <a href="?village=kolosok" class="switch-btn <?php echo (get_current_village() == 'kolosok') ? 'active' : ''; ?>">
                Колосок
            </a>
            <a href="?logout_village=1" class="logout-btn">
                Сменить
            </a>
        <?php endif; ?>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="header-content">
            <!-- Логотип -->
            <div class="logo-section">
                <a href="<?php echo home_url(); ?>" class="logo">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.jpg" 
                         alt="Логотип ДНП" 
                         class="logo-img">
                </a>
                <div class="village-header">
                    <div class="village-name">
                        ДНП "<?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?>"
                    </div>
                    <div class="village-status">
                        <?php if (current_user_can('administrator')): ?>
                            <span class="admin-badge">👑 Администратор</span>
                        <?php elseif (is_user_logged_in()): ?>
                            <span class="resident-badge">👤 Житель поселка</span>
                        <?php else: ?>
                            <span class="guest-badge">👤 Гость</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Меню -->
            <nav class="main-nav">
                <ul>
                    <li><a href="#home" class="active">Главная</a></li>
                    <li><a href="#about">О поселке</a></li>
                    <li><a href="#infrastructure">Инфраструктура</a></li>
                    <li><a href="#plots">Участки</a></li>
                    <li><a href="#news">Новости</a></li>
                    <li><a href="#contacts">Контакты</a></li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<main id="main">