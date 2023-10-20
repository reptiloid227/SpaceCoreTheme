<?php

/**
 * Основной класс темы, основанный на анти-паттерне Singleton
 */
class SpaceCoreTheme {

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
	 * Текущий инстанс
	 * @var mixed|null
	 */
	protected static mixed $instance = null;

	/**
	 * Получаем настройки темы
	 */
	private function __construct() {
		self::$settings   = get_field( "system", "options" );
		self::$currentDoc = get_queried_object();
	}

	/**
	 * Включение поддержки webp
	 * @return array
	 */
	public static function enableWebp(): array {
		$existing_mimes['webp'] = 'image/webp';

		return $existing_mimes;
	}

	/**
	 * Возвращает строку с обработанными шорткодами
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public static function returnShortcode( $content ): string {
		return do_shortcode( $content );
	}

	/**
	 * Включение шорткодов для мета-описания Yoast SEO
	 *
	 * @param $description
	 *
	 * @return string
	 */
	public static function support_wpseo_opengrap_shortcodes( $description ): string {
		unset( $description );
		$opengrap_description = get_post_meta( get_the_ID(), '_yoast_wpseo_metadesc', true );

		return do_shortcode( $opengrap_description );
	}

	/**
	 * Отображение root css со всеми цветами
	 * @return void
	 */
	public static function addRootStyles(): void {
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
	public static function enqueueAllScriptsAndStyles(): void {
		foreach ( self::$settings['stylesAndScripts'] as $item ) {

			if ( $item['type'] == 'url' ) {

				$item['url'] = str_replace( "{get_template_directory_uri}", get_template_directory_uri(), $item['url'] );

				$deps = [];
				if ( isset( $item['deps'] ) && $item['deps'] != null ) {
					$deps = str_replace( [ ' ', '"' ], "", $item['deps'] );
					$deps = explode( ",", $deps );
				}

				if ( $item['file'] == 'script' ) {
					if ( $item['in_footer'] == 'header' ) {
						$in_footer = false;
					} else {
						$in_footer = true;
					}
					wp_enqueue_script( $item['slug'], $item['url'], $deps, $item['ver'], $in_footer );
				} else {
					wp_enqueue_style( $item['slug'], $item['url'], $deps, $item['ver'], $item['media'] );
				}
			} elseif ( $item['type'] == 'inline' ) {

				if ( $item['in_footer'] == 'header' || $item['in_footer'] == null ) {
					add_action( 'wp_head', function () use ( $item ) {
						echo $item['code'];
					} );
				} else {
					add_action( 'wp_footer', function () use ( $item ) {
						echo $item['code'];
					} );
				}
			}
		}
	}

	/**
	 * Отключение встроенных стилей и скриптов
	 * @return void
	 */
	public static function removeWpDefaultScriptsAndStyles(): void {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-block-style' );
		wp_deregister_script( 'jquery' );
		wp_deregister_script( 'wp-embed' );
	}

	public static function customizerPreviewScripts(): void {
		wp_enqueue_script( 'customizer-colors', get_template_directory_uri() . '/assets/js/inc/customizer-colors.js', [
				'customize-preview',
				'jquery'
			], '1.0', true );
		wp_localize_script( 'customizer-colors', 'customizerData', [ 'colors' => self::$settings['colors'] ] );
	}

	public static function getMenu($slugMenu):array {
		require_once('structureMenu.php');
		$oMainMenu = new StructureMenu($slugMenu);
		return $oMainMenu->getItemsStructureMenu();
    }


	/**
	 * Передача параметров в AJAX
	 * @return void
	 */
	public static function addAJAX():void
	{
		wp_enqueue_script('ajax-form', get_template_directory_uri() . '/js/inc/ajax-form.js', ['jquery'], '1.0', true);

		wp_localize_script('ajax-form', 'ajax_form_object', [
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax-form-nonce'),
		]);
	}



	/**
	 * Генерируем секцию и настройки секции
	 * @return void
	 */
	public static function addCustomizerSettings(): void {
		global $wp_customize;
		$colors = self::$settings['colors'];
		$wp_customize->add_section( 'site_colors', array(
				'title'    => 'Цвета сайта',
				'priority' => 30,
			) );

		if ( ! empty( $colors ) ) {
			foreach ( $colors as $color ) {
				$wp_customize->add_setting( $color['key'], array(
						'default'           => $color['value'],
						'transport'         => 'postMessage',
						'sanitize_callback' => 'sanitize_hex_color',
					) );
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $color['key'], [
					'label'    => $color['title'],
					'section'  => 'site_colors',
					'settings' => $color['key'],
				] ) );
			}
		}
	}

	/**
	 * Инициализация темы
	 * @return object|null
	 */
	public function init(): object|null {
		add_action( 'wp_enqueue_scripts', [ self::getInstance(), 'removeWpDefaultScriptsAndStyles' ] );
		self::addThemeSupport();
		self::registerAllMenus();
		add_action( 'wp_enqueue_scripts', [ self::getInstance(), 'enqueueAllScriptsAndStyles' ] );
		add_action( 'wp_head', [ self::getInstance(), 'addRootStyles' ] );
		self::addShortCodeYoastSEO();
		add_filter( 'mime_types', [ self::getInstance(), 'enableWebp' ] );
		add_action( 'customize_register', [ self::getInstance(), 'addCustomizerSettings' ] );
		add_action( 'customize_preview_init', [ self::getInstance(), 'customizerPreviewScripts' ] );
		add_action('wp_enqueue_scripts', [self::getInstance(), 'addAJAX']);


		return self::getInstance();
	}

	/**
	 * Возвращает объект темы, если он инициализирован
	 * @return object
	 */
	public static function getInstance(): object {
		if ( is_null( self::$instance ) ) {
			self::$instance = new SpaceCoreTheme();
		}

		return self::$instance;
	}

	/**
	 * Включить поддержку определенных компонентов в теме
	 * @return void
	 */
	private static function addThemeSupport(): void {
		foreach ( self::$settings['theme_support'] as $supItem ) {
			if ( $supItem['enable'] ) {
				if ( $supItem['params_enable'] && $supItem['args'] != null ) {
					$argsArray = json_decode( $supItem['args'], true );
					if ( is_array( $argsArray ) ) {
						add_theme_support( $supItem['function'], $argsArray );
					}
				} else {
					add_theme_support( $supItem['function'] );
				}
			}
		}
	}

	/**
	 * Регистрация всех меню
	 * @return void
	 */
	private static function registerAllMenus(): void {
		foreach ( self::$settings['menus'] as $menu ) {
			register_nav_menu( $menu['slug'], $menu['title'] );
		}
	}

	/**
	 * Включение шорткодов для Yoast SEO
	 * @return void
	 */
	public static function addShortCodeYoastSEO(): void {
		add_filter( 'wpseo_title', [ self::getInstance(), 'returnShortcode' ] );
		add_filter( 'wpseo_opengraph_title', [ self::getInstance(), 'returnShortcode' ] );
		add_filter( 'wpseo_twitter_title', [ self::getInstance(), 'returnShortcode' ] );
		add_filter( 'wpseo_metadesc', [ self::getInstance(), 'returnShortcode' ], 100 );
		add_filter( 'wpseo_twitter_description', [ self::getInstance(), 'returnShortcode' ], 100 );
		add_filter( 'wpseo_opengraph_desc', [ self::getInstance(), 'support_wpseo_opengrap_shortcodes' ] );
	}

}