jQuery(document).ready(function($) {
    
    // Переключение поселка
    $('.village-switch-btn, .village-btn').on('click', function(e) {
        e.preventDefault();
        
        const village = $(this).data('village');
        const currentVillage = dnp_ajax.current_village;
        
        // Если уже выбран этот поселок
        if (village === currentVillage) return;
        
        // Показать лоадер
        $('main').fadeOut(300);
        $('#village-loader').fadeIn();
        
        // Отправить AJAX запрос
        $.ajax({
            url: dnp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'switch_village',
                village: village,
                nonce: dnp_ajax.nonce,
                current_url: window.location.href
            },
            success: function(response) {
                if (response.success) {
                    // Обновить страницу с новым поселком
                    window.location.href = response.data.redirect_url;
                } else {
                    alert('Ошибка переключения: ' + response.data.message);
                    $('main').fadeIn(300);
                    $('#village-loader').fadeOut();
                }
            },
            error: function() {
                alert('Ошибка сервера. Попробуйте обновить страницу.');
                $('main').fadeIn(300);
                $('#village-loader').fadeOut();
            }
        });
    });
    
    // Быстрое переключение для админов
    $(document).on('click', '.admin-village-switch', function(e) {
        e.preventDefault();
        const village = $(this).data('village');
        
        $.ajax({
            url: dnp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'admin_switch_village',
                village: village,
                nonce: dnp_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
    
    // Запоминание выбора для гостей
    $(document).on('submit', '.guest-village-form', function(e) {
        e.preventDefault();
        const village = $(this).find('select[name="village"]').val();
        
        $.ajax({
            url: dnp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'set_guest_village',
                village: village,
                nonce: dnp_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
    
    // Динамическое обновление контента без перезагрузки
    function loadVillageContent(village) {
        // Сохраняем текущую позицию скролла
        const scrollPos = $(window).scrollTop();
        
        // Загружаем контент для нового поселка
        $.ajax({
            url: dnp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_village_content',
                village: village,
                nonce: dnp_ajax.nonce,
                page_id: $('body').data('page-id')
            },
            success: function(response) {
                if (response.success) {
                    // Обновляем основной контент
                    $('main').html(response.data.content);
                    
                    // Обновляем заголовок
                    document.title = response.data.title;
                    
                    // Обновляем URL без перезагрузки
                    history.pushState({village: village}, '', response.data.url);
                    
                    // Восстанавливаем позицию скролла
                    $(window).scrollTop(scrollPos);
                    
                    // Инициализируем скрипты для нового контента
                    initPageScripts();
                }
            }
        });
    }
    
    function initPageScripts() {
        // Инициализация анимаций
        $('.fade-in').addClass('animated');
        
        // Инициализация галереи
        $('.gallery-item').magnificPopup({
            type: 'image',
            gallery: { enabled: true }
        });
    }
});