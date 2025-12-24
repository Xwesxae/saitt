<?php
// Временный файл для отладки
session_start();
?>
<h1>Отладка переключателя</h1>
<pre>
Сессия: <?php print_r($_SESSION); ?>

GET параметры: <?php print_r($_GET); ?>

Функция get_current_village(): <?php echo get_current_village(); ?>

Текущий пользователь: 
<?php 
$user = wp_get_current_user();
print_r($user->roles);
?>

Ссылки:
<a href="?village=zapovednoe&admin_switch=1">Переключиться на Заповедное</a>
<a href="?village=kolosok&admin_switch=1">Переключиться на Колосок</a>
<a href="?logout_village=1">Выйти</a>
</pre>