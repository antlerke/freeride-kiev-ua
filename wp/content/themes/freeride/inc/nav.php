<?php
/**
 * Меню.
 *
 * Дизайн меню — це просто рядок посилань, без списків і вкладеності.
 * Стандартний вивід WordPress обгортає кожен пункт у <li> з десятком
 * класів; тут ми віддаємо рівно ті <a>, під які написаний CSS.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FR_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$current = in_array( 'current-menu-item', (array) $item->classes, true )
			|| in_array( 'current-menu-ancestor', (array) $item->classes, true )
			|| in_array( 'current_page_parent', (array) $item->classes, true );

		// Стрілочка — у пунктів, у яких у меню є вкладені
		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );

		$output .= sprintf(
			'<a href="%s"%s>%s%s</a>',
			esc_url( $item->url ),
			$current ? ' aria-current="page"' : '',
			esc_html( $item->title ),
			$has_children ? '<span class="caret">▾</span>' : ''
		);
	}
}

/** Той самий вивід, але без стрілочок — для підвалу. */
class FR_Plain_Walker extends FR_Nav_Walker {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$output .= sprintf( '<a href="%s">%s</a>', esc_url( $item->url ), esc_html( $item->title ) );
	}
}

/**
 * Перемикач мов. Українська в корені, англійська під /en/ —
 * решту робить Polylang. Без Polylang перемикач просто не зʼявляється.
 */
function fr_lang_switcher(): void {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}
	$langs = pll_the_languages( [ 'raw' => 1, 'hide_if_no_translation' => 0 ] );
	if ( count( (array) $langs ) < 2 ) {
		return;
	}
	echo '<div class="lang">';
	foreach ( (array) $langs as $l ) {
		printf(
			'<a href="%s"%s>%s</a>',
			esc_url( $l['url'] ),
			! empty( $l['current_lang'] ) ? ' aria-current="true"' : '',
			esc_html( strtoupper( $l['slug'] === 'uk' ? 'ua' : $l['slug'] ) )
		);
	}
	echo '</div>';
}
