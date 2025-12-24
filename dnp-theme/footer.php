</main><!-- #main -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">ДНП "<?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?>"</h3>
                <p>Ваш дачный поселок с современной инфраструктурой</p>
            </div>

            <div class="footer-section">
                <h3 class="footer-title">Быстрые ссылки</h3>
                <ul class="footer-links">
                    <li><a href="#home">Главная</a></li>
                    <li><a href="#about">О поселке</a></li>
                    <li><a href="#infrastructure">Инфраструктура</a></li>
                    <li><a href="#plots">Участки</a></li>
                    <li><a href="#contacts">Контакты</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="footer-title">Контакты</h3>
                <p>Телефон: <?php echo (get_current_village() == 'zapovednoe') ? '+7 (999) 123-45-67' : '+7 (999) 987-65-43'; ?></p>
                <p>Email: <?php echo (get_current_village() == 'zapovednoe') ? 'zapovednoe@dnp.ru' : 'kolosok@dnp.ru'; ?></p>
                <p>Адрес: Московская область</p>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Ваш поселок</h3>
                <div class="current-village-info">
                    <p>Вы просматриваете: <strong><?php echo (get_current_village() == 'zapovednoe') ? 'Заповедное' : 'Колосок'; ?></strong></p>
                    <p><a href="?logout_village=1" class="change-village-link">Сменить поселок</a></p>
                    <?php if (current_user_can('administrator')): ?>
                        <p><a href="/wp-admin" class="admin-link">Перейти в админку</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> ДНП "Заповедное" и "Колосок". Все права защищены.</p>
            <p class="footer-note">
                <?php 
                if (current_user_can('administrator')) {
                    echo '👑 Вы вошли как администратор';
                } elseif (is_user_logged_in()) {
                    echo '👤 Вы вошли как житель поселка';
                } else {
                    echo '👋 Вы гость на сайте';
                }
                ?>
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

<script>
// Глобальные переменные для JavaScript
var dnp_vars = {
    current_village: '<?php echo get_current_village(); ?>',
    is_admin: '<?php echo current_user_can("administrator") ? "1" : "0"; ?>',
    ajax_url: '<?php echo admin_url("admin-ajax.php"); ?>'
};
</script>

</body>
</html>