<?php
/**
 * Тип запису «Курс».
 *
 * Слаг `motocourses` і адреси курсів успадковані зі старого сайту —
 * вони затверджені замовником і на них уся історія посилань:
 *   /motocourses/                   каталог
 *   /motocourses/bazovyj/           STREET
 *   /motocourses/intensiv/          CONTROL
 *   /motocourses/polnyj-kurs/       ADVANCED
 *   /motocourses/gorod/             STARTER
 *   /motocourses/probnyj/           пробний урок
 * Міняти слаг не можна без відповідного 301 — див. deploy/redirects/.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FR_COURSE = 'fr_course';

function fr_register_course_cpt(): void {
	register_post_type( FR_COURSE, [
		'labels' => [
			'name'                  => 'Курси',
			'singular_name'         => 'Курс',
			'add_new'               => 'Додати курс',
			'add_new_item'          => 'Новий курс',
			'edit_item'             => 'Редагувати курс',
			'new_item'              => 'Новий курс',
			'view_item'             => 'Переглянути курс',
			'view_items'            => 'Переглянути курси',
			'search_items'          => 'Шукати курси',
			'not_found'             => 'Курсів немає',
			'not_found_in_trash'    => 'У кошику курсів немає',
			'all_items'             => 'Усі курси',
			'archives'              => 'Каталог курсів',
			'menu_name'             => 'Курси',
			'item_updated'          => 'Курс оновлено',
			'item_published'        => 'Курс опубліковано',
		],
		'public'              => true,
		'has_archive'         => 'motocourses',
		'rewrite'             => [ 'slug' => 'motocourses', 'with_front' => false ],
		'menu_icon'           => 'dashicons-awards',
		'menu_position'       => 21,
		'show_in_rest'        => true,
		'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions' ],
		'hierarchical'        => false,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
	] );
}
add_action( 'init', 'fr_register_course_cpt' );

/**
 * Каталог курсів сортується полем «Порядок», а не датою: лінійка курсів
 * має йти від простішого до повнішого, і це не залежить від того,
 * коли сторінку створили.
 */
add_action( 'pre_get_posts', static function ( WP_Query $q ): void {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_post_type_archive( FR_COURSE ) ) {
		$q->set( 'orderby', [ 'menu_order' => 'ASC', 'title' => 'ASC' ] );
		$q->set( 'posts_per_page', -1 );
	}
} );

/**
 * Усі курси в потрібному порядку. Одна функція на весь сайт —
 * щоб каталог, калькулятор і блоки на головній не розʼїхалися.
 *
 * @return WP_Post[]
 */
function fr_courses( bool $only_calculator = false ): array {
	$args = [
		'post_type'      => FR_COURSE,
		'posts_per_page' => -1,
		'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
		'post_status'    => 'publish',
	];
	if ( $only_calculator ) {
		$args['meta_query'] = [ [ 'key' => 'in_calculator', 'value' => '1' ] ];
	}
	return get_posts( $args );
}
