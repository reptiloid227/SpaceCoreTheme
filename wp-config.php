<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки базы данных
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'spacecoretheme' );

/** Имя пользователя базы данных */
define( 'DB_USER', 'alexreptiloid227' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', '[R)THE6i_9znSwoQ' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'WK|LXvnhtlT.Qy^Z4EATLQ.5I6~pXm{uxa^}b}B:6 @rUg}RG,aG<62a7QU`[mY ' );
define( 'SECURE_AUTH_KEY',  '9<{b;r<;N!3k99&oC0irPL6IM7[o2xm+;Qj6% SB48gkN9u`G}n4]k{J.c#Jl?.A' );
define( 'LOGGED_IN_KEY',    '#bj9bO;e|?tWQ,lF.qAxC7@]xrlHfC^xF#w!C}=MzcQeu~r{nX^4k`g6IbwjR*Ia' );
define( 'NONCE_KEY',        '`M auSE4ceD;=5YYnDB;j_t;Y+5[1lt#u:zG-SnYe3Chi>%LMU44B0$#bg255=3b' );
define( 'AUTH_SALT',        'v1C|l*<9tTx!6QE+G$0EfwT*3X04Q)%u(]7i)aq5qke==dKiF: ?oL~5Lyr<>e[#' );
define( 'SECURE_AUTH_SALT', 'H.tq_Usx)p?j^%}O@_**3?)Z-FU$4H}8ixg1;<z.L(5q}M3hyeVzz)|H8mhfUvj^' );
define( 'LOGGED_IN_SALT',   '{)(#HJk*Vh@!`{dgZZvpKklrb{R21|uvD|a}21X40/K&Duu2[Gn|*oW#Sip{j6}!' );
define( 'NONCE_SALT',       '[@pM0xkIOho.(j82.&0}1sf3u,TB)n7vL_(e=zU4K</^TR?0&JIcFrx>Cf7]fHX9' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_sct_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
