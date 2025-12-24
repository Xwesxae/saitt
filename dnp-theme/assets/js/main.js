jQuery(document).ready(function($) {
    console.log('DNP Theme loaded');
    
    // Анимация шапки при скролле
    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
    });
    
    // Плавный скролл к якорям
    $('a[href^="#"]').on('click', function(e) {
        if ($(this).attr('href') !== '#') {
            e.preventDefault();
            const target = $(this).attr('href');
            if ($(target).length) {
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 80
                }, 600);
                
                // Подсветка активного пункта меню
                $('.main-nav a').removeClass('active');
                $(this).addClass('active');
            }
        }
    });
    
    // Активное меню при скролле
    $(window).scroll(function() {
        const scrollPos = $(document).scrollTop();
        $('.main-nav a').each(function() {
            const currLink = $(this);
            const refElement = $(currLink.attr("href"));
            if (refElement.length) {
                if (refElement.offset().top <= scrollPos + 100 && 
                    refElement.offset().top + refElement.height() > scrollPos) {
                    $('.main-nav a').removeClass("active");
                    currLink.addClass("active");
                }
            }
        });
    });
    
    // Обработка формы контактов
    $('.contact-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.text();
        
        // Показываем загрузку
        submitBtn.text('Отправка...').prop('disabled', true);
        
        // Имитация отправки
        setTimeout(function() {
            form.find('.form-success').fadeIn();
            form.trigger('reset');
            submitBtn.text(originalText).prop('disabled', false);
            
            // Скрываем успешное сообщение через 5 секунд
            setTimeout(function() {
                form.find('.form-success').fadeOut();
            }, 5000);
        }, 1500);
    });
    
    // Галерея - увеличение по клику
    $('.gallery-item').on('click', function(e) {
        e.preventDefault();
        const imgSrc = $(this).attr('href');
        const imgAlt = $(this).find('img').attr('alt') || 'Изображение';
        
        // Создаем модальное окно
        const modal = $('<div class="gallery-modal"></div>');
        const modalContent = $('<div class="modal-content"></div>');
        const modalImg = $('<img src="' + imgSrc + '" alt="' + imgAlt + '">');
        const closeBtn = $('<button class="modal-close">&times;</button>');
        
        modalContent.append(modalImg, closeBtn);
        modal.append(modalContent);
        $('body').append(modal);
        
        // Анимация появления
        setTimeout(function() {
            modal.addClass('active');
        }, 10);
        
        // Закрытие
        function closeModal() {
            modal.removeClass('active');
            setTimeout(function() {
                modal.remove();
            }, 300);
        }
        
        modal.on('click', closeModal);
        closeBtn.on('click', closeModal);
        
        // Предотвращаем закрытие при клике на картинку
        modalImg.on('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Анимация элементов при скролле
    function animateOnScroll() {
        $('.fade-in').each(function() {
            const elementTop = $(this).offset().top;
            const elementBottom = elementTop + $(this).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animated');
            }
        });
    }
    
    // Запуск анимации
    animateOnScroll();
    $(window).scroll(animateOnScroll);
    
    // Инициализация при загрузке
    setTimeout(animateOnScroll, 100);
});