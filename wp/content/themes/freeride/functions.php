<?php
/**
 * Тема FreeRide.
 *
 * Правило теми: вона нічого не знає про ціни й курси — тільки про те,
 * як їх показати. Усі дані приходять із плагіна FreeRide Core.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FR_THEME_DIR', get_stylesheet_directory() );
define( 'FR_THEME_URI', get_stylesheet_directory_uri() );

add_action( 'after_setup_theme', static function (): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'responsive-embeds' );
	register_nav_menus( [
		'primary' => 'Головне меню',
		'footer'  => 'Меню в підвалі',
	] );
	// Розміри під наші картки, щоб не віддавати 3000px там, де треба 600
	add_image_size( 'fr-card', 760, 520, true );
	add_image_size( 'fr-hero', 1920, 1000, true );
} );

/**
 * Версія файлу — його час зміни. На бойовому сервері це знімає вічну
 * проблему «я правив CSS, а в людей старий»: адреса міняється сама.
 */
function fr_asset( string $rel ): array {
	$path = FR_THEME_DIR . '/assets/' . $rel;
	return [ FR_THEME_URI . '/assets/' . $rel, file_exists( $path ) ? (string) filemtime( $path ) : FR_VERSION ];
}

function fr_style( string $handle, string $file, array $deps = [] ): void {
	[ $uri, $ver ] = fr_asset( $file );
	wp_enqueue_style( $handle, $uri, $deps, $ver );
}

function fr_script( string $handle, string $file ): void {
	[ $uri, $ver ] = fr_asset( $file );
	wp_enqueue_script( $handle, $uri, [], $ver, [ 'strategy' => 'defer' ] );
}

add_action( 'wp_enqueue_scripts', static function (): void {
	wp_enqueue_style(
		'fr-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Montserrat:ital,wght@0,600;0,700;0,800;1,700;1,800&display=swap',
		[], null
	);
	fr_style( 'fr-base', 'base.css', [ 'fr-fonts' ] );
	fr_style( 'fr-home', 'home.css', [ 'fr-base' ] );   // шапка, бургер, підвал — скрізь

	fr_script( 'fr-nav', 'nav.js' );
	fr_script( 'fr-contact', 'contact.js' );
	fr_script( 'fr-reveal', 'reveal.js' );

	// Решта — тільки там, де потрібна
	if ( is_post_type_archive( FR_COURSE ) || is_singular( FR_COURSE ) ) {
		fr_style( 'fr-courses', 'courses.css', [ 'fr-base' ] );
	}
	if ( is_singular( FR_COURSE ) ) {
		fr_style( 'fr-course', 'course.css', [ 'fr-courses' ] );
		fr_style( 'fr-pages', 'pages.css', [ 'fr-base' ] );   // картки відгуків
	}
	if ( is_page_template( 'page-calculator.php' ) ) {
		fr_style( 'fr-calc', 'calc.css', [ 'fr-base' ] );
		fr_script( 'fr-calc', 'calc.js' );
		// Калькулятор читає прайс із адмінки тим самим кодом, яким раніше
		// читав файл: схема відповіді збігається один в один.
		wp_add_inline_script(
			'fr-calc',
			'window.FR_PRICING_URL=' . wp_json_encode( rest_url( 'freeride/v1/pricing' ) ) . ';',
			'before'
		);
	}
	if ( is_page_template( [ 'page-about.php', 'page-reviews.php', 'page-team.php' ] ) ) {
		fr_style( 'fr-pages', 'pages.css', [ 'fr-base' ] );
	}
	if ( is_page_template( [ 'page-certificate.php', 'page-bikepick.php', 'page-legal.php' ] ) ) {
		fr_style( 'fr-services', 'services.css', [ 'fr-base' ] );
	}
	if ( is_page_template( [ 'page-certificate.php', 'page-bikepick.php' ] ) ) {
		fr_style( 'fr-contacts', 'contacts.css', [ 'fr-base' ] );   // форма заявки
	}
	if ( is_page_template( 'page-gallery.php' ) ) {
		fr_style( 'fr-gallery', 'gallery.css', [ 'fr-base' ] );
		fr_script( 'fr-gallery', 'gallery.js' );
	}
	if ( is_page_template( 'page-contacts.php' ) ) {
		fr_style( 'fr-contacts', 'contacts.css', [ 'fr-base' ] );
	}
	if ( is_page_template( 'page-reviews.php' ) ) {
		fr_script( 'fr-reviews', 'reviews.js' );
	}
}, 10 );

/** Клас js на <html> — щоб сторінка без скриптів не ховала вміст. */
add_action( 'wp_head', static function (): void {
	echo "<script>document.documentElement.classList.add('js')</script>\n";
}, 1 );

/** Прибираємо те, чого на цьому сайті немає. */
add_action( 'init', static function (): void {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	// Емодзі-скрипт — 10 КБ на кожній сторінці заради нічого
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
} );

require_once FR_THEME_DIR . '/inc/nav.php';
require_once FR_THEME_DIR . '/inc/sprite.php';
require_once FR_THEME_DIR . '/inc/template-tags.php';
