<?php
/**
 * DNP Theme Functions
 */

// ========== СИСТЕМА СЕССИЙ ==========
if (!session_id()) {
    session_start();
}

// ========== ПОЛУЧЕНИЕ ТЕКУЩЕГО ПОСЕЛКА ==========
function get_current_village() {
    // Для админов - из GET или сессии
    if (current_user_can('administrator')) {
        if (isset($_GET['village']) && in_array($_GET['village'], ['zapovednoe', 'kolosok'])) {
            $_SESSION['admin_village'] = $_GET['village'];
            return $_GET['village'];
        }
        
        if (isset($_SESSION['admin_village'])) {
            return $_SESSION['admin_village'];
        }
        
        return 'zapovednoe';
    }
    
    // Для обычных пользователей
    if (isset($_SESSION['user_village'])) {
        return $_SESSION['user_village'];
    }
    
    if (isset($_COOKIE['user_village'])) {
        $_SESSION['user_village'] = $_COOKIE['user_village'];
        return $_COOKIE['user_village'];
    }
    
    return 'zapovednoe';
}

// ========== ПРОВЕРКА ВЫБОРА ПОСЕЛКА ==========
function dnp_check_village_selection() {
    // Администраторы могут всё
    if (current_user_can('administrator')) {
        return true;
    }
    
    // Если уже выбрал в сессии
    if (isset($_SESSION['user_village'])) {
        return true;
    }
    
    // Если выбрал через GET параметр
    if (isset($_GET['village']) && in_array($_GET['village'], ['zapovednoe', 'kolosok'])) {
        $_SESSION['user_village'] = $_GET['village'];
        setcookie('user_village', $_GET['village'], time() + (86400 * 30), "/");
        return true;
    }
    
    // Проверяем куки
    if (isset($_COOKIE['user_village'])) {
        $_SESSION['user_village'] = $_COOKIE['user_village'];
        return true;
    }
    
    // Показываем форму выбора (только на фронтенде)
    if (!is_admin() && !wp_doing_ajax()) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Выберите поселок</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: Arial, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .selection-box {
                    background: white;
                    padding: 50px;
                    border-radius: 20px;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    text-align: center;
                    max-width: 600px;
                    width: 100%;
                }
                h1 { margin-bottom: 30px; color: #333; font-size: 32px; }
                .village-options {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px;
                    margin-bottom: 40px;
                }
                .village-btn {
                    padding: 40px 20px;
                    border: 3px solid #e0e0e0;
                    border-radius: 15px;
                    background: white;
                    cursor: pointer;
                    transition: all 0.3s;
                    text-decoration: none;
                    color: inherit;
                    display: block;
                }
                .village-btn:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
                }
                .village-btn.zapovednoe:hover {
                    border-color: #2E7D32;
                    background: #E8F5E9;
                }
                .village-btn.kolosok:hover {
                    border-color: #F57C00;
                    background: #FFF3E0;
                }
                .village-icon {
                    font-size: 60px;
                    margin-bottom: 20px;
                }
                .village-name {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
                .village-desc {
                    color: #666;
                    font-size: 14px;
                    line-height: 1.5;
                }
                .warning-note {
                    margin-top: 30px;
                    padding: 15px;
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 10px;
                    color: #856404;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="selection-box">
                <h1>Вход в информационную систему ДНП</h1>
                <p style="margin-bottom: 30px; color: #666;">Пожалуйста, выберите ваш поселок для доступа к информации</p>
                
                <div class="village-options">
                    <a href="?village=zapovednoe" class="village-btn zapovednoe">
                        <div class="village-icon">🌲</div>
                        <div class="village-name">Заповедное</div>
                        <div class="village-desc">Только для жителей<br>поселка "Заповедное"</div>
                    </a>
                    
                    <a href="?village=kolosok" class="village-btn kolosok">
                        <div class="village-icon">🌾</div>
                        <div class="village-name">Колосок</div>
                        <div class="village-desc">Только для жителей<br>поселка "Колосок"</div>
                    </a>
                </div>
                
                <div class="warning-note">
                    <strong>⚠️ Внимание!</strong>
                    <p>Каждый поселок имеет отдельную информационную систему.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    return false;
}
add_action('template_redirect', 'dnp_check_village_selection', 1);

// ========== ОСНОВНЫЕ НАСТРОЙКИ ТЕМЫ ==========
function dnp_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(['primary' => 'Главное меню']);
}
add_action('after_setup_theme', 'dnp_setup');

// ========== СТИЛИ И СКРИПТЫ ==========
function dnp_styles() {
    wp_enqueue_style('dnp-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'dnp_styles');

function dnp_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('dnp-main', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], '1.0', true);
}
add_action('wp_enqueue_scripts', 'dnp_scripts');

// ========== ВЫХОД ИЗ ПОСЕЛКА ==========
function dnp_logout_village() {
    if (isset($_GET['logout_village'])) {
        if (current_user_can('administrator')) {
            unset($_SESSION['admin_village']);
        } else {
            unset($_SESSION['user_village']);
        }
        setcookie('user_village', '', time() - 3600, "/");
        wp_redirect(home_url());
        exit;
    }
}
add_action('init', 'dnp_logout_village');

// ========== ФУНКЦИИ ПОМОЩНИКИ ==========
function dnp_get_village_content($section) {
    $current_village = get_current_village();
    
    $content = array(
        'zapovednoe' => array(
            'about' => 'Поселок "Заповедное" расположен в экологически чистом районе Подмосковья. Площадь поселка: 15 гектаров. Основан в 2015 году.',
            'infrastructure' => 'В поселке: охраняемая территория, асфальтированные дороги, центральное водоснабжение, электричество 15 кВт, детская площадка, зона BBQ.',
            'news' => '15.01.2024 - Общее собрание жителей 20 января в 18:00<br>10.01.2024 - Завершено строительство новой детской площадки',
            'plots' => 'Свободные участки: №15 (8 соток), №22 (10 соток), №30 (6 соток). Все участки с подключенными коммуникациями.',
            'contacts' => 'Председатель: Иванов И.И.<br>Телефон: +7 (999) 123-45-67<br>Email: zapovednoe@dnp.ru'
        ),
        'kolosok' => array(
            'about' => 'Поселок "Колосок" - современный дачный поселок с развитой инфраструктурой. Площадь: 12 гектаров. Основан в 2018 году.',
            'infrastructure' => 'Инфраструктура: видеонаблюдение, газоснабжение, скважина с очисткой воды, спортивная площадка, магазин, парковка для гостей.',
            'news' => '20.01.2024 - Планируется подключение оптоволокна<br>05.01.2024 - Установлены новые системы видеонаблюдения',
            'plots' => 'Доступные участки: №7 (9 соток), №12 (7 соток), №25 (11 соток). Участки с подведенным газом и электричеством.',
            'contacts' => 'Председатель: Петров П.П.<br>Телефон: +7 (999) 987-65-43<br>Email: kolosok@dnp.ru'
        )
    );
    
    return isset($content[$current_village][$section]) ? $content[$current_village][$section] : '';
}

// ========== ДОБАВЛЕНИЕ КЛАССА ПОСЕЛКА К BODY ==========
function dnp_add_village_body_class($classes) {
    $village = get_current_village();
    $classes[] = 'village-' . $village;
    return $classes;
}
add_filter('body_class', 'dnp_add_village_body_class');
?>