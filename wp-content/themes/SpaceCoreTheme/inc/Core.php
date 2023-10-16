<?php

/**
 * Основной класс темы, основанный на анти-паттерне Singleton
 */
class SpaceCoreTheme
{

    /**
     * Текущий инстанс
     * @var mixed|null
     */
    protected static mixed $instance = null;
    /**
     * Массив настроек сайта
     * @var mixed
     */
    public static mixed $settings;
    /**
     * Объект текущей страницы
     * @var object|null
     */
    public static object|null $currentDoc;


    /**
     * Получаем настройки темы
     */
    private function __construct()
    {
        self::$settings = get_field("system", "options");
        self::$currentDoc = get_queried_object();
    }

    /**
     * Возвращает класс темы, если он объявлен
     * @return object
     */
    public static function getInstance():object
    {
        if (is_null(self::$instance)) {
            self::$instance = new SpaceCoreTheme();
        }
        return self::$instance;
    }


    /**
     * Инициализация темы
     * @return object|null
     */
    public function init():object|null
    {
        add_action('wp_enqueue_scripts', [self::getInstance(), 'removeWpDefaultScriptsAndStyles']);
        self::disableGutenBerg();
        self::addThemeSupport();
        self::registerAllMenus();
        add_action('wp_enqueue_scripts', [self::getInstance(), 'enqueueAllScriptsAndStyles']);
        add_action('wp_head', [self::getInstance(), 'addRootStyles']);
        self::addShortCodeYoastSEO();
        add_filter('mime_types', [self::getInstance(), 'enableWebp']);
        return self::getInstance();
    }

    /**
     * Включение поддержки webp
     * @return array
     */
    public static function enableWebp():array
    {
        $existing_mimes['webp'] = 'image/webp';
        return $existing_mimes;
    }

    /**
     * Включение шорткодов для Yoast SEO
     * @return void
     */
    public static function addShortCodeYoastSEO():void
    {
        add_filter('wpseo_title', [self::getInstance(), 'returnShortcode']);
        add_filter('wpseo_opengraph_title', [self::getInstance(), 'returnShortcode']);
        add_filter('wpseo_twitter_title', [self::getInstance(), 'returnShortcode']);
        add_filter('wpseo_metadesc', [self::getInstance(), 'returnShortcode'], 100, 1);
        add_filter('wpseo_twitter_description', [self::getInstance(), 'returnShortcode'], 100, 1);
        add_filter('wpseo_opengraph_desc', [self::getInstance(), 'support_wpseo_opengrap_shortcodes'], 10);
    }

    /**
     * Возвращает строку с обработанными шорткодами
     * @param $content
     * @return string
     */
    public static function returnShortcode($content):string
    {
        return do_shortcode($content);
    }

    /**
     * Включение шорткодов для мета-описания Yoast SEO
     * @param $description
     * @return string
     */
    public static function support_wpseo_opengrap_shortcodes($description):string
    {
        unset($description);
        $opengrap_description = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
        return do_shortcode($opengrap_description);
    }

    /**
     * Отображение root css со всеми цветами 
     * @return void
     */
    public static function addRootStyles():void
    {
        ?>
            <style id="root-styles">
                :root {
                    <?
                        foreach (self::$settings['colors'] as $color) {
                            $value = get_theme_mod($color['key'], $color['value']);
                            echo "--" . $color['key'] . ": $value;\n";
                        }
                    ?>
                }
            </style>
        <?php
    }

    /**
     * Подключение стилей и скриптов
     *
     * @return void
     */
    public static function enqueueAllScriptsAndStyles():void
    {
        foreach (self::$settings['stylesAndScripts'] as $item) {

            if ($item['type'] == 'url') {

                $item['url'] = str_replace("{get_template_directory_uri}", get_template_directory_uri(), $item['url']);

                $deps = [];
                if (isset($item['deps']) && $item['deps'] != null) {
                    $deps = str_replace([' ', '"'], "", $item['deps']);
                    $deps = explode(",", $deps);
                }

                if ($item['file'] == 'script') {
                    if ($item['in_footer'] == 'header') {
                        $in_footer = false;
                    } else {
                        $in_footer = true;
                    }
                    wp_enqueue_script($item['slug'], $item['url'], $deps, $item['ver'], $in_footer);
                } else {
                    wp_enqueue_style($item['slug'], $item['url'], $deps, $item['ver'], $item['media']);
                }
            } elseif ($item['type'] == 'inline') {

                if ($item['in_footer'] == 'header' || $item['in_footer'] == null) {
                    add_action('wp_head', function () use ($item) {
                        echo $item['code'];
                    });
                } else {
                    add_action('wp_footer', function () use ($item) {
                        echo $item['code'];
                    });
                }
            }
        }
    }

    /**
     * Регистрация всех меню
     * @return void
     */
    private static function registerAllMenus():void
    {
        foreach (self::$settings['menus'] as $menu){
            register_nav_menu($menu['slug'], $menu['title']);
        }
    }

    /**
     * Включить поддержку определенных компонентов в теме
     * @return void
     */
    private static function addThemeSupport():void
    {
        foreach (self::$settings['theme_support'] as $supItem){
            if ($supItem['enable']) {
                if ($supItem['params_enable'] && $supItem['args'] != null) {
                    $argsArray = json_decode($supItem['args'], true);
                    if (is_array($argsArray)) {
                        add_theme_support($supItem['function'], $argsArray);
                    }
                } else {
                    add_theme_support($supItem['function']);
                }
            }
        }
    }

    /**
     * Отключение редактора гутенберг
     * @return void
     */
    private static function disableGutenBerg():void
    {
        if ('disable_gutenberg') {
            remove_theme_support('core-block-patterns');
            add_filter('use_block_editor_for_post_type', '__return_false', 100);
            remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
            add_action('admin_init', function () {
                remove_action('admin_notices', ['WP_Privacy_Policy_Content', 'notice']);
                add_action('edit_form_after_title', ['WP_Privacy_Policy_Content', 'notice']);
            });
        }
    }

    /**
     * Отключение встроенных стилей и скриптов
     * @return void
     */
    public static function removeWpDefaultScriptsAndStyles():void
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-block-style');
        wp_deregister_script('jquery');
        wp_deregister_script('wp-embed');
    }


}