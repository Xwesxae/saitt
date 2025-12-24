<?php
/**
 * Обработчик AJAX запросов для ДНП
 */

class DNP_Ajax_Handler {
    
    public static function init() {
        // Регистрация AJAX действий
        add_action('wp_ajax_switch_village', [__CLASS__, 'ajax_switch_village']);
        add_action('wp_ajax_nopriv_switch_village', [__CLASS__, 'ajax_switch_village']);
        add_action('wp_ajax_admin_switch_village', [__CLASS__, 'ajax_admin_switch_village']);
        add_action('wp_ajax_set_guest_village', [__CLASS__, 'ajax_set_guest_village']);
        add_action('wp_ajax_get_village_content', [__CLASS__, 'ajax_get_village_content']);
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
            if (!session_id()) {
                session_start();
            }
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
            if (!session_id()) {
                session_start();
            }
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
            if (!session_id()) {
                session_start();
            }
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
            if (!session_id()) {
                session_start();
            }
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
}