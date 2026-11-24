<?php
/**
 * Адмінка: щоб у списку курсів було видно те саме, що на сайті.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'manage_' . FR_COURSE . '_posts_columns', static function ( array $cols ): array {
	$new = [];
	foreach ( $cols as $k => $v ) {
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['fr_code']  = 'Код';
			$new['fr_hours'] = 'Годин';
			$new['fr_price'] = 'Ціна';
			$new['fr_calc']  = 'У калькуляторі';
			$new['fr_order'] = 'Порядок';
		}
	}
	return $new;
} );

add_action( 'manage_' . FR_COURSE . '_posts_custom_column', static function ( string $col, int $id ): void {
	switch ( $col ) {
		case 'fr_code':
			echo esc_html( (string) get_field( 'code', $id ) );
			break;
		case 'fr_hours':
			$h = fr_course_hours( $id );
			echo $h ? esc_html( $h . ' год' ) : '<span style="color:#b32d2e">склад не заповнено</span>';
			break;
		case 'fr_price':
			$p = (int) get_field( 'price', $id );
			echo $p ? esc_html( number_format_i18n( $p ) . ' грн' ) : '<span style="color:#b32d2e">—</span>';
			break;
		case 'fr_calc':
			echo get_field( 'in_calculator', $id ) ? '✓' : '—';
			break;
		case 'fr_order':
			echo (int) get_post_field( 'menu_order', $id );
			break;
	}
}, 10, 2 );

add_filter( 'manage_edit-' . FR_COURSE . '_sortable_columns', static function ( array $c ): array {
	$c['fr_price'] = 'fr_price';
	$c['fr_order'] = 'menu_order';
	return $c;
} );

add_action( 'pre_get_posts', static function ( WP_Query $q ): void {
	if ( ! is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( 'fr_price' === $q->get( 'orderby' ) ) {
		$q->set( 'meta_key', 'price' );
		$q->set( 'orderby', 'meta_value_num' );
	}
} );

/** За замовчуванням список курсів іде в порядку показу на сайті. */
add_action( 'pre_get_posts', static function ( WP_Query $q ): void {
	if ( is_admin() && $q->is_main_query() && FR_COURSE === $q->get( 'post_type' ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', 'menu_order' );
		$q->set( 'order', 'ASC' );
	}
} );
