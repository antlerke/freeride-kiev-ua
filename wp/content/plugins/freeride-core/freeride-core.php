<?php
/**
 * Plugin Name:       FreeRide Core
 * Description:       Курси, ціни й контакти сайту мотошколи. Усе, що є вмістом, а не оформленням, живе тут, а не в темі — щоб пережити будь-яку зміну теми.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Text Domain:       freeride
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FR_VERSION', '1.0.0' );
define( 'FR_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Чому плагін, а не тема.
 *
 * Тип запису «Курс», поля й налаштування — це вміст. Якщо оголосити їх
 * у темі, то зміна теми забирає з сайту всі курси: записи лишаються
 * в базі, але WordPress більше не знає такого типу й не показує їх.
 * У плагіні вони переживають і зміну теми, і повне переписування
 * оформлення. Тема відповідає лише за те, як це виглядає.
 */
require_once FR_DIR . 'inc/cpt.php';
require_once FR_DIR . 'inc/fields.php';
require_once FR_DIR . 'inc/fields-pages.php';
require_once FR_DIR . 'inc/leads.php';
require_once FR_DIR . 'inc/pricing.php';
require_once FR_DIR . 'inc/seo.php';
require_once FR_DIR . 'inc/admin.php';

register_activation_hook( __FILE__, static function (): void {
	fr_register_course_cpt();
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
