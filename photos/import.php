<?php
/**
 * Заливає кадри в медіатеку й розставляє за wp-map.json.
 * Запускається через `wp eval-file`, тому без declare(strict_types) — як і seed.php.
 * Ідемпотентний: вкладення шукається за метою _fr_shot, повторно не створюється.
 *
 * Очікує файли в /var/www/html/wp-content/uploads/_in/<shot>.jpg
 * і сам wp-map.json поруч із ними.
 */

$in  = WP_CONTENT_DIR . '/uploads/_in';
$map = $in . '/wp-map.json';

if ( ! file_exists( $map ) ) {
	WP_CLI::error( "Немає $map" );
}
$cfg = json_decode( (string) file_get_contents( $map ), true );
if ( ! is_array( $cfg ) ) {
	WP_CLI::error( 'wp-map.json не читається' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

/** Знаходить або створює вкладення для кадру. Повертає ID або 0. */
function fr_photo_id( $shot ) {
	static $cache = [];
	if ( isset( $cache[ $shot ] ) ) {
		return $cache[ $shot ];
	}
	$found = get_posts( [
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'numberposts' => 1,
		'fields'      => 'ids',
		'meta_key'    => '_fr_shot',
		'meta_value'  => $shot,
	] );
	if ( $found ) {
		return $cache[ $shot ] = (int) $found[0];
	}

	$src = WP_CONTENT_DIR . '/uploads/_in/' . $shot . '.jpg';
	if ( ! file_exists( $src ) ) {
		return $cache[ $shot ] = 0;
	}

	$up = wp_upload_dir();
	$name = wp_unique_filename( $up['path'], $shot . '.jpg' );
	$dest = $up['path'] . '/' . $name;
	if ( ! copy( $src, $dest ) ) {
		return $cache[ $shot ] = 0;
	}

	$id = wp_insert_attachment( [
		'post_mime_type' => 'image/jpeg',
		'post_title'     => $shot,
		'post_name'      => $shot,
		'post_status'    => 'inherit',
	], $dest );
	if ( is_wp_error( $id ) || ! $id ) {
		return $cache[ $shot ] = 0;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
	update_post_meta( $id, '_fr_shot', $shot );
	return $cache[ $shot ] = (int) $id;
}

/** Ставить одне поле. $field === 'thumbnail' → головне зображення запису. */
function fr_photo_set( $post_id, $field, $shot ) {
	if ( ! $post_id ) {
		WP_CLI::log( "  пропуск $field ($shot): запису немає" );
		return;
	}
	$aid = fr_photo_id( $shot );
	if ( ! $aid ) {
		WP_CLI::log( "  пропуск $field ($shot): файлу немає" );
		return;
	}
	if ( 'thumbnail' === $field ) {
		set_post_thumbnail( $post_id, $aid );
	} elseif ( function_exists( 'update_field' ) ) {
		update_field( $field, $aid, $post_id );
	} else {
		update_post_meta( $post_id, $field, $aid );
	}
	WP_CLI::log( "  #$post_id · $field ← $shot (#$aid)" );
}

function fr_by_slug( $type, $slug ) {
	$p = get_posts( [
		'post_type'   => $type,
		'name'        => $slug,
		'numberposts' => 1,
		'fields'      => 'ids',
		'post_status' => 'any',
	] );
	return $p ? (int) $p[0] : 0;
}

WP_CLI::log( 'Головна:' );
$front = (int) get_option( 'page_on_front' );
foreach ( (array) ( $cfg['front_page'] ?? [] ) as $field => $shot ) {
	fr_photo_set( $front, $field, $shot );
}

WP_CLI::log( 'Сторінки:' );
foreach ( (array) ( $cfg['pages'] ?? [] ) as $slug => $fields ) {
	$pid = fr_by_slug( 'page', $slug );
	foreach ( (array) $fields as $field => $shot ) {
		fr_photo_set( $pid, $field, $shot );
	}
}

WP_CLI::log( 'Курси:' );
foreach ( (array) ( $cfg['courses'] ?? [] ) as $slug => $fields ) {
	$cid = fr_by_slug( 'fr_course', $slug );
	foreach ( (array) $fields as $field => $shot ) {
		fr_photo_set( $cid, $field, $shot );
	}
}

WP_CLI::log( 'Галерея:' );
$gal = array_values( array_filter( array_map( 'fr_photo_id', (array) ( $cfg['gallery'] ?? [] ) ) ) );
$gpid = fr_by_slug( 'page', 'galereya' );
if ( $gpid && $gal ) {
	if ( function_exists( 'update_field' ) ) {
		update_field( 'featured', array_map( static function ( $id ) {
			return [ 'image' => $id, 'kind' => 'photo', 'tag' => '', 'title' => '', 'url' => '', 'length' => '' ];
		}, $gal ), $gpid );
	}
	WP_CLI::log( '  ' . count( $gal ) . ' зображень у поле featured сторінки галереї' );
} else {
	WP_CLI::log( '  пропуск: немає сторінки galereya або файлів' );
}

WP_CLI::success( 'Фото розставлено.' );
