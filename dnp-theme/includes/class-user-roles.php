<?php
/**
 * Система пользовательских ролей для ДНП
 */

class DNP_User_Roles {
    
    public static function init() {
        add_action('init', [__CLASS__, 'register_user_roles']);
        add_action('user_register', [__CLASS__, 'assign_village_to_user'], 10, 1);
        add_action('show_user_profile', [__CLASS__, 'add_village_field']);
        add_action('edit_user_profile', [__CLASS__, 'add_village_field']);
        add_action('personal_options_update', [__CLASS__, 'save_village_field']);
        add_action('edit_user_profile_update', [__CLASS__, 'save_village_field']);
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_filter('manage_users_columns', [__CLASS__, 'add_village_column']);
        add_filter('manage_users_custom_column', [__CLASS__, 'show_village_column'], 10, 3);
    }
    
    // Регистрация пользовательских ролей
    public static function register_user_roles() {
        // Роль: Председатель
        add_role('predsedatel', 'Председатель', [
            'read' => true,
            'edit_posts' => true,
            'edit_pages' => true,
            'edit_others_posts' => true,
            'create_posts' => true,
            'manage_categories' => true,
            'publish_posts' => true,
            'upload_files' => true,
            'edit_theme_options' => false,
            'manage_options' => false,
            'moderate_comments' => true,
            'manage_village' => true,
            'edit_village_content' => true,
            'manage_documents' => true,
            'manage_plots' => true
        ]);
        
        // Роль: Житель поселка
        add_role('resident', 'Житель поселка', [
            'read' => true,
            'edit_posts' => false,
            'upload_files' => false,
            'read_documents' => true,
            'view_village_content' => true,
            'submit_requests' => true
        ]);
    }
    
    // Назначение поселка новому пользователю
    public static function assign_village_to_user($user_id) {
        // По умолчанию назначаем "Заповедное"
        // Админ может изменить позже
        update_user_meta($user_id, 'user_village', 'zapovednoe');
    }
    
    // Поле выбора поселка в профиле пользователя
    public static function add_village_field($user) {
        if (!current_user_can('administrator')) return;
        
        $user_village = get_user_meta($user->ID, 'user_village', true);
        ?>
        <h3>Информация о поселке</h3>
        <table class="form-table">
            <tr>
                <th><label for="user_village">Поселок пользователя</label></th>
                <td>
                    <select name="user_village" id="user_village">
                        <option value="">-- Выберите поселок --</option>
                        <option value="zapovednoe" <?php selected($user_village, 'zapovednoe'); ?>>Заповедное</option>
                        <option value="kolosok" <?php selected($user_village, 'kolosok'); ?>>Колосок</option>
                    </select>
                    <p class="description">Укажите, к какому поселку относится пользователь</p>
                </td>
            </tr>
            <tr>
                <th><label>Роль</label></th>
                <td>
                    <input type="text" readonly value="<?php echo implode(', ', $user->roles); ?>" class="regular-text">
                    <p class="description">Текущая роль пользователя</p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    // Сохранение поля поселка
    public static function save_village_field($user_id) {
        if (!current_user_can('administrator')) return false;
        
        if (isset($_POST['user_village'])) {
            update_user_meta($user_id, 'user_village', sanitize_text_field($_POST['user_village']));
        }
        
        return true;
    }
    
    // Добавление колонки в список пользователей
    public static function add_village_column($columns) {
        $columns['user_village'] = 'Поселок';
        $columns['user_role'] = 'Роль';
        return $columns;
    }
    
    // Отображение колонки
    public static function show_village_column($value, $column_name, $user_id) {
        if ($column_name == 'user_village') {
            $village = get_user_meta($user_id, 'user_village', true);
            if ($village) {
                return $village == 'zapovednoe' ? 'Заповедное' : 'Колосок';
            }
            return 'Не указан';
        }
        
        if ($column_name == 'user_role') {
            $user = get_userdata($user_id);
            return implode(', ', $user->roles);
        }
        
        return $value;
    }
    
    // Добавление меню для управления пользователями
    public static function add_admin_menu() {
        add_users_page(
            'Управление жителями',
            'Жители поселков',
            'manage_options',
            'village-residents',
            [__CLASS__, 'render_residents_page']
        );
    }
    
    // Страница управления жителями
    public static function render_residents_page() {
        ?>
        <div class="wrap">
            <h1>Управление жителями поселков</h1>
            
            <div class="village-stats" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
                <div class="stat-card" style="background: #E8F5E9; padding: 20px; border-radius: 10px;">
                    <h3>Поселок "Заповедное"</h3>
                    <p style="font-size: 24px; font-weight: bold; color: #2E7D32;">
                        <?php echo self::count_residents_by_village('zapovednoe'); ?> жителей
                    </p>
                </div>
                <div class="stat-card" style="background: #FFF3E0; padding: 20px; border-radius: 10px;">
                    <h3>Поселок "Колосок"</h3>
                    <p style="font-size: 24px; font-weight: bold; color: #F57C00;">
                        <?php echo self::count_residents_by_village('kolosok'); ?> жителей
                    </p>
                </div>
            </div>
            
            <h2>Добавить нового жителя</h2>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="dnp_add_resident">
                <?php wp_nonce_field('dnp_add_resident', 'dnp_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th><label>Имя пользователя</label></th>
                        <td><input type="text" name="username" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label>Email</label></th>
                        <td><input type="email" name="email" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label>Пароль</label></th>
                        <td><input type="password" name="password" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label>Поселок</label></th>
                        <td>
                            <select name="village" required>
                                <option value="zapovednoe">Заповедное</option>
                                <option value="kolosok">Колосок</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Участок</label></th>
                        <td><input type="text" name="plot_number" class="regular-text" placeholder="Например: 15"></td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" class="button button-primary" value="Добавить жителя">
                </p>
            </form>
        </div>
        <?php
    }
    
    // Подсчет жителей по поселкам
    private static function count_residents_by_village($village) {
        $args = [
            'meta_key' => 'user_village',
            'meta_value' => $village,
            'count_total' => true
        ];
        
        $users = new WP_User_Query($args);
        return $users->get_total();
    }
}