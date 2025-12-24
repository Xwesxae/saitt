<?php get_header(); ?>

<!-- Герой -->
<section class="hero-section" id="home">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title fade-in">Добро пожаловать в ДНП "<?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?>"</h1>
            <p class="hero-subtitle fade-in">
                <?php 
                echo (get_current_village() == 'zapovednoe') 
                    ? 'Экологически чистый поселок в сосновом бору' 
                    : 'Современный поселок с развитой инфраструктурой';
                ?>
            </p>
            <div class="hero-buttons">
                <a href="#about" class="btn fade-in">О поселке</a>
                <a href="#plots" class="btn btn-outline fade-in">Участки</a>
            </div>
        </div>
    </div>
</section>

<!-- О поселке -->
<section class="about-section" id="about">
    <div class="container">
        <h2 class="section-title fade-in">О поселке "<?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?>"</h2>
        
        <div class="about-content">
            <div class="about-text fade-in">
                <?php echo dnp_get_village_content('about'); ?>
            </div>
            
            <div class="about-stats">
                <div class="stat-item fade-in">
                    <div class="stat-number"><?php echo (get_current_village() == 'zapovednoe') ? '15' : '12'; ?></div>
                    <div class="stat-label">гектаров</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-number"><?php echo (get_current_village() == 'zapovednoe') ? '45' : '38'; ?></div>
                    <div class="stat-label">участков</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-number"><?php echo (get_current_village() == 'zapovednoe') ? '2015' : '2018'; ?></div>
                    <div class="stat-label">год основания</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Преимущества -->
<section class="advantages-section">
    <div class="container">
        <h2 class="section-title fade-in">Наши преимущества</h2>
        
        <div class="advantages-grid">
            <div class="advantage-card fade-in">
                <div class="advantage-icon">🛡️</div>
                <h3 class="advantage-title">Охраняемая территория</h3>
                <p>Круглосуточная охрана и видеонаблюдение</p>
            </div>
            
            <div class="advantage-card fade-in">
                <div class="advantage-icon">🛣️</div>
                <h3 class="advantage-title">Хорошие дороги</h3>
                <p>Асфальтированные подъездные пути</p>
            </div>
            
            <div class="advantage-card fade-in">
                <div class="advantage-icon">⚡</div>
                <h3 class="advantage-title">Коммуникации</h3>
                <p>Электричество, водоснабжение, газ</p>
            </div>
            
            <div class="advantage-card fade-in">
                <div class="advantage-icon">🏞️</div>
                <h3 class="advantage-title">Природа</h3>
                <p>Чистый воздух и красивые пейзажи</p>
            </div>
            
            <div class="advantage-card fade-in">
                <div class="advantage-icon">🏘️</div>
                <h3 class="advantage-title">Инфраструктура</h3>
                <p>Детские площадки, зоны отдыха</p>
            </div>
            
            <div class="advantage-card fade-in">
                <div class="advantage-icon">📄</div>
                <h3 class="advantage-title">Документы</h3>
                <p>Полный пакет документов</p>
            </div>
        </div>
    </div>
</section>

<!-- Участки -->
<section class="plots-section" id="plots">
    <div class="container">
        <h2 class="section-title fade-in">Свободные участки</h2>
        
        <div class="plots-info">
            <p class="plots-text fade-in" style="font-size: 18px; margin-bottom: 30px; text-align: center;">
                <?php echo dnp_get_village_content('plots'); ?>
            </p>
            
            <div class="plots-grid">
                <div class="plot-card fade-in">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/plot-1.jpg" 
                         alt="Участок" 
                         class="plot-image">
                    <div class="plot-content">
                        <h3 class="plot-title">
                            <?php echo (get_current_village() == 'zapovednoe') ? 'Участок №15' : 'Участок №7'; ?>
                        </h3>
                        <p class="plot-meta">
                            Площадь: <?php echo (get_current_village() == 'zapovednoe') ? '8 соток' : '9 соток'; ?>
                        </p>
                        <p class="plot-price">
                            <?php echo (get_current_village() == 'zapovednoe') ? '1 500 000 ₽' : '1 650 000 ₽'; ?>
                        </p>
                        <a href="#contacts" class="btn">Забронировать</a>
                    </div>
                </div>
                
                <div class="plot-card fade-in">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/plot-2.jpg" 
                         alt="Участок" 
                         class="plot-image">
                    <div class="plot-content">
                        <h3 class="plot-title">
                            <?php echo (get_current_village() == 'zapovednoe') ? 'Участок №22' : 'Участок №12'; ?>
                        </h3>
                        <p class="plot-meta">
                            Площадь: <?php echo (get_current_village() == 'zapovednoe') ? '10 соток' : '7 соток'; ?>
                        </p>
                        <p class="plot-price">
                            <?php echo (get_current_village() == 'zapovednoe') ? '1 800 000 ₽' : '1 400 000 ₽'; ?>
                        </p>
                        <a href="#contacts" class="btn">Забронировать</a>
                    </div>
                </div>
                
                <div class="plot-card fade-in">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/plot-1.jpg" 
                         alt="Участок" 
                         class="plot-image">
                    <div class="plot-content">
                        <h3 class="plot-title">
                            <?php echo (get_current_village() == 'zapovednoe') ? 'Участок №30' : 'Участок №25'; ?>
                        </h3>
                        <p class="plot-meta">
                            Площадь: <?php echo (get_current_village() == 'zapovednoe') ? '6 соток' : '11 соток'; ?>
                        </p>
                        <p class="plot-price">
                            <?php echo (get_current_village() == 'zapovednoe') ? '1 200 000 ₽' : '1 900 000 ₽'; ?>
                        </p>
                        <a href="#contacts" class="btn">Забронировать</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Новости -->
<section class="news-section" id="news">
    <div class="container">
        <h2 class="section-title fade-in">Новости поселка</h2>
        
        <div class="news-content">
            <div class="news-item fade-in" style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
                <div class="news-icon" style="font-size: 40px; margin-bottom: 20px;">📢</div>
                <div class="news-text" style="font-size: 16px;">
                    <?php echo dnp_get_village_content('news'); ?>
                </div>
            </div>
            
            <div class="access-info fade-in" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 10px;">
                <div class="access-icon">🔒</div>
                <div class="access-text">
                    <strong>Доступ ограничен:</strong> Эта информация доступна только жителям поселка 
                    "<?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?>"
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Инфраструктура -->
<section class="infrastructure-section" id="infrastructure">
    <div class="container">
        <h2 class="section-title fade-in">Инфраструктура поселка</h2>
        
        <div class="infrastructure-content">
            <div class="infrastructure-text fade-in">
                <?php echo dnp_get_village_content('infrastructure'); ?>
            </div>
            
            <div class="infrastructure-images fade-in" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/infrastructure-1.jpg" 
                     alt="Инфраструктура" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/nature-1.jpg" 
                     alt="Природа" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px;">
            </div>
        </div>
    </div>
</section>

<!-- Контакты -->
<section class="contacts-section" id="contacts">
    <div class="container">
        <h2 class="section-title fade-in">Контакты поселка</h2>
        
        <div class="contacts-content">
            <div class="contacts-info fade-in">
                <div class="contacts-text" style="font-size: 16px; line-height: 1.8; margin-bottom: 25px;">
                    <?php echo dnp_get_village_content('contacts'); ?>
                </div>
                
                <div class="access-warning">
                    <div class="warning-icon">⚠️</div>
                    <div class="warning-text">
                        <strong>Только для жителей:</strong> Контактная информация доступна исключительно 
                        жителям поселка "<?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?>"
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container fade-in">
                <h3>Написать правлению</h3>
                <form class="contact-form">
                    <div class="form-group">
                        <input type="text" placeholder="Ваше имя" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Номер участка" required>
                    </div>
                    <div class="form-group">
                        <textarea placeholder="Текст обращения" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn" style="width: 100%;">Отправить</button>
                </form>
                <div class="form-success" style="display:none; margin-top: 20px; padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; border: 1px solid #c3e6cb;">
                    ✅ Сообщение отправлено! Мы свяжемся с вами в ближайшее время.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Галерея -->
<section class="gallery-section" id="gallery">
    <div class="container">
        <h2 class="section-title fade-in">Галерея поселка</h2>
        
        <div class="gallery-grid">
            <a href="<?php echo get_template_directory_uri(); ?>/assets/images/plot-1.jpg" class="gallery-item fade-in">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/plot-1.jpg" alt="Участок">
                <div class="gallery-overlay">
                    <span class="zoom-icon">🔍</span>
                </div>
            </a>
            
            <a href="<?php echo get_template_directory_uri(); ?>/assets/images/plot-2.jpg" class="gallery-item fade-in">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/plot-2.jpg" alt="Участок">
                <div class="gallery-overlay">
                    <span class="zoom-icon">🔍</span>
                </div>
            </a>
            
            <a href="<?php echo get_template_directory_uri(); ?>/assets/images/infrastructure-1.jpg" class="gallery-item fade-in">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/infrastructure-1.jpg" alt="Инфраструктура">
                <div class="gallery-overlay">
                    <span class="zoom-icon">🔍</span>
                </div>
            </a>
        </div>
        
        <div class="text-center" style="margin-top: 40px;">
            <a href="#gallery" class="btn btn-outline" style="color: #333; border-color: #333;">Показать все фото</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>