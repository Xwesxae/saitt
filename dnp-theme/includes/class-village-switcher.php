<?php
/**
 * Система переключения между поселками
 */

class DNP_Village_Switcher {
    
    public static function init() {
        // AJAX обработчики
        add_action('wp_ajax_switch_village', [__CLASS__, 'ajax_switch_village']);
        add_action('wp_ajax_nopriv_switch_village', [__CLASS__, 'ajax_switch_village']);
        add_action('wp_ajax_admin_switch_village', [__CLASS__, 'ajax_admin_switch_village']);
        add_action('wp_ajax_set_guest_village', [__CLASS__, 'ajax_set_guest_village']);
        add_action('wp_ajax_get_village_content', [__CLASS__, 'ajax_get_village_content']);
        
        // Шорткоды
        add_shortcode('village_switcher_panel', [__CLASS__, 'switcher_panel_shortcode']);
        
        // Хуки
        add_action('wp_footer', [__CLASS__, 'add_village_loader']);
        add_filter('body_class', [__CLASS__, 'add_village_body_class']);
    }
    
    // AJAX переключение поселка
    public static function ajax_switch_village() {
        check_ajax_referer('dnp_ajax_nonce', 'nonce');
        
        $village = sanitize_text_field($_POST['village']);
        $current_url = esc_url_raw($_POST['current_url']);
        
        if (!in_array($village, ['zapovednoe', 'kolosok'])) {
            wp_send_json_error(['message' => 'Неверный поселок']);
        }
        
        // Для зарегистрированных пользователей
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'user_village', $village);
            
            wp_send_json_success([
                'message' => 'Поселок изменен',
                'redirect_url' => $current_url
            ]);
        }
        // Для гостей
        else {
            $_SESSION['guest_village'] = $village;
            setcookie('guest_village', $village, time() + (86400 * 30), "/");
            
            wp_send_json_success([
                'message' => 'Поселок выбран',
                'redirect_url' => $current_url
            ]);
        }
    }
    
    // Быстрое переключение для админов
    public static function ajax_admin_switch_village() {
        check_ajax_referer('dnp_ajax_nonce', 'nonce');
        
        if (!current_user_can('administrator') && !current_user_can('predsedatel')) {
            wp_send_json_error(['message' => 'Нет доступа']);
        }
        
        $village = sanitize_text_field($_POST['village']);
        
        if (in_array($village, ['zapovednoe', 'kolosok'])) {
            $_SESSION['admin_current_village'] = $village;
            wp_send_json_success(['message' => 'Поселок переключен']);
        }
        
        wp_send_json_error(['message' => 'Ошибка переключения']);
    }
    
    // Установка поселка для гостей
    public static function ajax_set_guest_village() {
        check_ajax_referer('dnp_ajax_nonce', 'nonce');
        
        $village = sanitize_text_field($_POST['village']);
        
        if (in_array($village, ['zapovednoe', 'kolosok'])) {
            $_SESSION['guest_village'] = $village;
            setcookie('guest_village', $village, time() + (86400 * 30), "/");
            
            wp_send_json_success(['message' => 'Поселок выбран']);
        }
        
        wp_send_json_error(['message' => 'Ошибка выбора']);
    }
    
    // Получение контента для поселка
    public static function ajax_get_village_content() {
        check_ajax_referer('dnp_ajax_nonce', 'nonce');
        
        $village = sanitize_text_field($_POST['village']);
        $page_id = intval($_POST['page_id']);
        
        // Сохраняем выбор поселка
        if (is_user_logged_in()) {
            update_user_meta(get_current_user_id(), 'user_village', $village);
        } else {
            $_SESSION['guest_village'] = $village;
        }
        
        // Получаем контент для выбранного поселка
        $content = '';
        $title = '';
        
        if ($page_id) {
            $page = get_post($page_id);
            if ($page) {
                $content = apply_filters('the_content', $page->post_content);
                $title = $page->post_title;
            }
        }
        
        // Формируем новый URL
        $url = add_query_arg(['village' => $village], get_permalink($page_id));
        
        wp_send_json_success([
            'content' => $content,
            'title' => $title,
            'url' => $url
        ]);
    }
    
    // Шорткод панели переключения
    public static function switcher_panel_shortcode($atts) {
        $atts = shortcode_atts([
            'style' => 'default',
            'position' => 'header'
        ], $atts);
        
        $current_village = get_current_village();
        $is_admin = current_user_can('administrator') || current_user_can('predsedatel');
        
        ob_start();
        ?>
        <div class="village-switcher-panel style-<?php echo $atts['style']; ?> position-<?php echo $atts['position']; ?>">
            <div class="current-village-info">
                <span class="village-icon"><?php echo $current_village == 'zapovednoe' ? '🌲' : '🌾'; ?></span>
                <span class="village-name"><?php echo $current_village == 'zapovednoe' ? 'Заповедное' : 'Колосок'; ?></span>
                <?php if ($is_admin): ?>
                    <span class="admin-badge">Админ</span>
                <?php elseif (is_user_logged_in()): ?>
                    <span class="resident-badge">Житель</span>
                <?php else: ?>
                    <span class="guest-badge">Гость</span>
                <?php endif; ?>
            </div>
            
            <div class="switcher-controls">
                <?php if ($is_admin): ?>
                    <button class="switch-btn <?php echo $current_village == 'zapovednoe' ? 'active' : ''; ?>" 
                            data-village="zapovednoe">
                        Заповедное
                    </button>
                    <button class="switch-btn <?php echo $current_village == 'kolosok' ? 'active' : ''; ?>" 
                            data-village="kolosok">
                        Колосок
                    </button>
                <?php else: ?>
                    <div class="village-select">
                        <form class="guest-village-form">
                            <select name="village">
                                <option value="zapovednoe" <?php selected($current_village, 'zapovednoe'); ?>>Заповедное</option>
                                <option value="kolosok" <?php selected($current_village, 'kolosok'); ?>>Колосок</option>
                            </select>
                            <button type="submit">Выбрать</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Лоадер при переключении
    public static function add_village_loader() {
        ?>
        <div id="village-loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.9); z-index:9999;">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center;">
                <div class="loader" style="width:50px; height:50px; border:5px solid #f3f3f3; border-top:5px solid #2E7D32; border-radius:50%; animation:spin 1s linear infinite; margin:0 auto 20px;"></div>
                <p style="font-size:18px; color:#333;">Загрузка контента поселка...</p>
                <style>
                    @keyframes spin {
                        0% { transform: translate(-50%, -50%) rotate(0deg); }
                        100% { transform: translate(-50%, -50%) rotate(360deg); }
                    }
                </style>
            </div>
        </div>
        <?php
    }
    
    // Добавление класса поселка к body
    public static function add_village_body_class($classes) {
        $village = get_current_village();
        $classes[] = 'village-' . $village;
        
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            foreach ($user->roles as $role) {
                $classes[] = 'role-' . $role;
            }
        } else {
            $classes[] = 'role-guest';
        }
        
        return $classes;
    }
}