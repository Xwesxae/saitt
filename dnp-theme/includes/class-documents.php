<?php
/**
 * Система управления документами с ограничением доступа
 */

class DNP_Documents {
    
    public static function init() {
        add_action('pre_get_posts', [__CLASS__, 'filter_documents_by_village']);
        add_action('template_redirect', [__CLASS__, 'check_document_access']);
        add_filter('the_content', [__CLASS__, 'add_document_meta'], 10, 2);
        add_shortcode('documents_list', [__CLASS__, 'documents_list_shortcode']);
        add_action('wp_ajax_download_document', [__CLASS__, 'ajax_download_document']);
        add_action('wp_ajax_nopriv_download_document', [__CLASS__, 'ajax_download_document']);
    }
    
    // Фильтрация документов по поселку
    public static function filter_documents_by_village($query) {
        if (!is_admin() && $query->is_main_query() && 
            (is_post_type_archive('document') || is_tax('document_type'))) {
            
            $current_village = get_current_village();
            $user_id = get_current_user_id();
            
            // Жители видят только документы своего поселка
            if (!current_user_can('administrator') && !current_user_can('predsedatel')) {
                $meta_query = [
                    'relation' => 'OR',
                    [
                        'key' => 'village_association',
                        'value' => $current_village
                    ],
                    [
                        'key' => 'village_association',
                        'value' => 'both'
                    ]
                ];
                
                $query->set('meta_query', $meta_query);
            }
        }
    }
    
    // Проверка доступа к документу
    public static function check_document_access() {
        if (!is_singular('document')) return;
        
        $post_id = get_the_ID();
        $post_village = get_post_meta($post_id, 'village_association', true);
        $current_village = get_current_village();
        
        // Админы и председатели видят всё
        if (current_user_can('administrator') || current_user_can('predsedatel')) {
            return;
        }
        
        // Проверяем доступ
        if ($post_village && $post_village !== 'both' && $post_village !== $current_village) {
            wp_die(
                '<div style="text-align:center; padding:100px 20px;">
                    <h1 style="color:#dc3545;">🚫 Доступ запрещен</h1>
                    <p style="font-size:18px; margin:20px 0;">Этот документ доступен только жителям поселка "' . 
                    ($post_village == 'zapovednoe' ? 'Заповедное' : 'Колосок') . '"</p>
                    <a href="' . get_post_type_archive_link('document') . '" 
                       style="background:#2E7D32; color:white; padding:12px 30px; text-decoration:none; border-radius:5px;">
                        Вернуться к документам
                    </a>
                </div>',
                'Доступ запрещен',
                403
            );
        }
    }
    
    // Добавление мета-информации к документу
    public static function add_document_meta($content) {
        if (!is_singular('document')) return $content;
        
        $post_id = get_the_ID();
        $document_file = get_post_meta($post_id, 'document_file', true);
        $document_date = get_post_meta($post_id, 'document_date', true);
        $document_number = get_post_meta($post_id, 'document_number', true);
        
        $meta_html = '<div class="document-meta" style="background:#f8f9fa; padding:20px; border-radius:10px; margin:20px 0;">';
        $meta_html .= '<h3>Информация о документе:</h3>';
        $meta_html .= '<ul style="list-style:none; padding:0;">';
        
        if ($document_date) {
            $meta_html .= '<li><strong>Дата:</strong> ' . date('d.m.Y', strtotime($document_date)) . '</li>';
        }
        
        if ($document_number) {
            $meta_html .= '<li><strong>Номер:</strong> ' . esc_html($document_number) . '</li>';
        }
        
        $terms = get_the_terms($post_id, 'document_type');
        if ($terms && !is_wp_error($terms)) {
            $meta_html .= '<li><strong>Тип:</strong> ' . esc_html($terms[0]->name) . '</li>';
        }
        
        if ($document_file) {
            $file_url = wp_get_attachment_url($document_file);
            $file_name = get_the_title($document_file);
            $file_size = size_format(filesize(get_attached_file($document_file)));
            
            $meta_html .= '<li><strong>Файл:</strong> ' . esc_html($file_name) . ' (' . $file_size . ')</li>';
            $meta_html .= '<li><a href="' . esc_url($file_url) . '" class="btn" target="_blank" download>📥 Скачать документ</a></li>';
        }
        
        $meta_html .= '</ul></div>';
        
        return $meta_html . $content;
    }
    
    // Шорткод списка документов
    public static function documents_list_shortcode($atts) {
        $atts = shortcode_atts([
            'type' => 'all',
            'limit' => -1,
            'show_filter' => true
        ], $atts);
        
        $current_village = get_current_village();
        $is_resident = is_user_logged_in() && (current_user_can('resident') || current_user_can('predsedatel'));
        
        // Гости не видят документы
        if (!is_user_logged_in() && !current_user_can('administrator')) {
            return '<div class="document-access-warning">
                        <h3>🔒 Доступ ограничен</h3>
                        <p>Просмотр документов доступен только зарегистрированным жителям поселка.</p>
                        <a href="' . wp_login_url(get_permalink()) . '" class="btn">Войти в систему</a>
                    </div>';
        }
        
        // Жители видят только свой поселок
        $meta_query = [];
        if ($is_resident && !current_user_can('administrator')) {
            $meta_query = [
                'relation' => 'OR',
                [
                    'key' => 'village_association',
                    'value' => $current_village
                ],
                [
                    'key' => 'village_association',
                    'value' => 'both'
                ]
            ];
        }
        
        $args = [
            'post_type' => 'document',
            'posts_per_page' => $atts['limit'],
            'meta_query' => $meta_query
        ];
        
        if ($atts['type'] != 'all') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'document_type',
                    'field' => 'slug',
                    'terms' => $atts['type']
                ]
            ];
        }
        
        $documents = new WP_Query($args);
        
        ob_start();
        
        if ($atts['show_filter'] && $documents->have_posts()) {
            echo self::render_document_filters();
        }
        
        if ($documents->have_posts()) : ?>
            <div class="documents-grid">
                <?php while ($documents->have_posts()) : $documents->the_post(); ?>
                    <div class="document-card">
                        <div class="document-icon">📄</div>
                        <div class="document-content">
                            <h3 class="document-title"><?php the_title(); ?></h3>
                            
                            <?php 
                            $document_date = get_post_meta(get_the_ID(), 'document_date', true);
                            $document_number = get_post_meta(get_the_ID(), 'document_number', true);
                            ?>
                            
                            <?php if ($document_date): ?>
                                <div class="document-date">Дата: <?php echo date('d.m.Y', strtotime($document_date)); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($document_number): ?>
                                <div class="document-number">№ <?php echo esc_html($document_number); ?></div>
                            <?php endif; ?>
                            
                            <div class="document-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <div class="document-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn-sm">Подробнее</a>
                                
                                <?php 
                                $document_file = get_post_meta(get_the_ID(), 'document_file', true);
                                if ($document_file):
                                    $file_url = wp_get_attachment_url($document_file);
                                ?>
                                    <a href="<?php echo esc_url($file_url); ?>" class="btn btn-sm btn-outline" download>
                                        📥 Скачать
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="no-documents">
                <p>Документы не найдены.</p>
            </div>
        <?php endif;
        
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    // Фильтры документов
    private static function render_document_filters() {
        $current_type = isset($_GET['doc_type']) ? $_GET['doc_type'] : 'all';
        $types = get_terms(['taxonomy' => 'document_type', 'hide_empty' => true]);
        
        ob_start();
        ?>
        <div class="document-filters">
            <form method="get" class="filter-form">
                <div class="filter-group">
                    <label for="doc_type">Тип документа:</label>
                    <select name="doc_type" id="doc_type">
                        <option value="all" <?php selected($current_type, 'all'); ?>>Все типы</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo esc_attr($type->slug); ?>" 
                                <?php selected($current_type, $type->slug); ?>>
                                <?php echo esc_html($type->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="doc_year">Год:</label>
                    <select name="doc_year" id="doc_year">
                        <option value="all">Все годы</option>
                        <?php 
                        $current_year = date('Y');
                        for ($year = $current_year; $year >= 2015; $year--): ?>
                            <option value="<?php echo $year; ?>" 
                                <?php selected(isset($_GET['doc_year']) ? $_GET['doc_year'] : '', $year); ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn">Применить фильтры</button>
                <?php if ($current_type != 'all' || isset($_GET['doc_year'])): ?>
                    <a href="?" class="btn btn-outline">Сбросить</a>
                <?php endif; ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // AJAX скачивание документа
    public static function ajax_download_document() {
        check_ajax_referer('dnp_ajax_nonce', 'nonce');
        
        $document_id = intval($_POST['document_id']);
        $user_id = get_current_user_id();
        
        if (!$user_id) {
            wp_send_json_error(['message' => 'Требуется авторизация']);
        }
        
        // Проверка доступа
        $post_village = get_post_meta($document_id, 'village_association', true);
        $user_village = get_user_meta($user_id, 'user_village', true);
        
        if (!$post_village || $post_village == 'both' || $post_village == $user_village || 
            current_user_can('administrator') || current_user_can('predsedatel')) {
            
            $document_file = get_post_meta($document_id, 'document_file', true);
            
            if ($document_file) {
                $file_url = wp_get_attachment_url($document_file);
                
                // Логируем скачивание
                self::log_document_download($document_id, $user_id);
                
                wp_send_json_success(['url' => $file_url]);
            }
        }
        
        wp_send_json_error(['message' => 'Доступ запрещен']);
    }
    
    // Логирование скачиваний
    private static function log_document_download($document_id, $user_id) {
        $downloads = get_post_meta($document_id, 'document_downloads', true);
        if (!$downloads) $downloads = [];
        
        $downloads[] = [
            'user_id' => $user_id,
            'time' => current_time('mysql'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ];
        
        update_post_meta($document_id, 'document_downloads', $downloads);
    }
}