<?php
/**
 * Template Name: Галерея
 *
 * Фільтр показується лише тоді, коли є що фільтрувати: розділи
 * збираються з самих матеріалів, і якщо розділ один — смуги кнопок
 * немає. Кнопка, яка нічого не змінює, гірша за її відсутність.
 */

declare( strict_types = 1 );
get_header();

$c      = fr_contacts();
$labels = fr_channel_labels();

while ( have_posts() ) :
	the_post();
	$id       = get_the_ID();
	$featured = (array) ( get_field( 'featured', $id ) ?: [] );
	$albums   = (array) ( get_field( 'albums', $id ) ?: [] );

	// Розділи — з реальних матеріалів, у порядку появи
	$tags = [];
	foreach ( array_merge( $featured, $albums ) as $it ) {
		$t = trim( (string) ( $it['tag'] ?? '' ) );
		if ( '' !== $t && ! in_array( $t, $tags, true ) ) {
			$tags[] = $t;
		}
	}
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО: мотоцикліст на тлі Києва]'; ?>
		</div>
		<div class="hero-veil"></div>
		<div class="wrap hero-in">
			<?php fr_breadcrumbs(); ?>
			<?php if ( $e = get_field( 'eyebrow', $id ) ) : ?>
				<p class="gl-eyebrow"><?php echo esc_html( $e ); ?></p>
			<?php endif; ?>
			<h1><span class="slash"><i></i><i></i></span><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<?php if ( $v = get_field( 'hero_video', $id ) ) : ?>
				<a class="gl-play" href="<?php echo esc_url( $v ); ?>" target="_blank" rel="noopener">
					<span class="gl-play-btn"><?php echo fr_icon( 'i-play', 22 ); ?></span>
					<span>Дивитися<br>відео про FreeRide</span>
				</a>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( count( $tags ) > 1 ) : ?>
		<section class="wrap" style="padding: 0 24px 22px">
			<div class="gl-pills" role="group" aria-label="Розділи медіа">
				<button class="pill-btn is-on" type="button" data-tag="">
					<?php echo fr_icon( 'i-layers', 18 ); ?>Усі медіа</button>
				<?php foreach ( $tags as $t ) : ?>
					<button class="pill-btn" type="button" data-tag="<?php echo esc_attr( $t ); ?>">
						<?php echo esc_html( $t ); ?></button>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $featured ) : ?>
		<section class="wrap" style="padding: 0 24px 40px">
			<div class="gl-top" data-rev-group="up">
				<div class="gl-feat">
					<?php foreach ( $featured as $i => $f ) :
						$img = (int) ( $f['image'] ?? 0 );
						$video = 'video' === ( $f['kind'] ?? 'photo' );
						$tag   = trim( (string) ( $f['tag'] ?? '' ) );
						$url   = (string) ( $f['url'] ?? '' );
						$el    = $url ? 'a' : 'div';
						?>
						<<?php echo $el; ?> class="gl-item<?php echo 0 === $i ? ' is-lead' : ''; ?>"
							data-tag="<?php echo esc_attr( $tag ); ?>"
							<?php if ( $url ) : ?>href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"<?php endif; ?>>
							<?php echo $img
								? wp_get_attachment_image( $img, 0 === $i ? 'fr-hero' : 'fr-card', false, [ 'alt' => esc_attr( (string) ( $f['title'] ?? '' ) ), 'loading' => 'lazy' ] )
								: '<span class="gl-ph">[ФОТО не додано]</span>'; ?>
							<span class="gl-kind"><?php echo fr_icon( $video ? 'i-play' : 'i-ext', 15 ); ?><?php echo $video ? 'Відео' : 'Фото'; ?></span>
							<?php if ( $video ) : ?>
								<span class="gl-bigplay" aria-hidden="true"><?php echo fr_icon( 'i-play', 26 ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $f['title'] ) ) : ?>
								<span class="gl-cap"><?php echo esc_html( $f['title'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $f['length'] ) ) : ?>
								<span class="gl-len"><?php echo esc_html( $f['length'] ); ?></span>
							<?php endif; ?>
						</<?php echo $el; ?>>
					<?php endforeach; ?>
				</div>

				<?php if ( $c['socials'] ) : ?>
					<aside class="gl-soc">
						<h2><span class="slash sm"><i></i><i></i></span>Слідкуйте за нами</h2>
						<?php foreach ( $c['socials'] as $s ) :
							$kind = (string) ( $s['kind'] ?? '' );
							if ( empty( $s['url'] ) || ! isset( $labels[ $kind ] ) ) { continue; } ?>
							<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener">
								<span class="gl-soc-ic gl-soc-<?php echo esc_attr( $kind ); ?>"><?php echo fr_icon( $labels[ $kind ][0], 20 ); ?></span>
								<b><?php echo esc_html( $labels[ $kind ][1] ); ?></b>
								<?php echo fr_icon( 'i-arrow', 18 ); ?>
							</a>
						<?php endforeach; ?>
					</aside>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $albums ) : ?>
		<section class="wrap" style="padding: 0 24px 40px">
			<h2 class="eyebrow-h"><span class="slash sm"><i></i><i></i></span>Галерея</h2>
			<div class="gl-albums" data-rev-group="up">
				<?php foreach ( $albums as $a ) :
					$img = (int) ( $a['image'] ?? 0 );
					$url = (string) ( $a['url'] ?? '' );
					$el  = $url ? 'a' : 'div'; ?>
					<<?php echo $el; ?> class="gl-album" data-tag="<?php echo esc_attr( trim( (string) ( $a['tag'] ?? '' ) ) ); ?>"
						<?php if ( $url ) : ?>href="<?php echo esc_url( $url ); ?>"<?php endif; ?>>
						<span class="gl-album-img">
							<?php echo $img
								? wp_get_attachment_image( $img, 'fr-card', false, [ 'alt' => '', 'loading' => 'lazy' ] )
								: '<span class="gl-ph" style="aspect-ratio:4/3">[ФОТО]</span>'; ?>
							<?php if ( ! empty( $a['count'] ) ) : ?>
								<span class="gl-kind"><?php echo fr_icon( 'i-ext', 15 ); ?><?php echo esc_html( (string) (int) $a['count'] ); ?></span>
							<?php endif; ?>
						</span>
						<span class="gl-album-foot">
							<b><?php echo esc_html( (string) $a['title'] ); ?></b>
							<?php echo fr_icon( 'i-arrow', 18 ); ?>
						</span>
					</<?php echo $el; ?>>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $ct = get_field( 'cta_title', $id ) ) : ?>
		<section class="wrap" style="padding: 0 24px 56px">
			<div class="gl-cta">
				<?php $ci = (int) get_field( 'cta_image', $id ); ?>
				<div class="gl-cta-media<?php echo $ci ? '' : ' ph'; ?>">
					<?php echo $ci ? wp_get_attachment_image( $ci, 'fr-hero', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО: мотоцикліст на серпантині]'; ?>
				</div>
				<div class="gl-cta-veil"></div>
				<div class="gl-cta-in">
					<h2><span class="slash sm"><i></i><i></i></span><?php echo esc_html( $ct ); ?></h2>
					<?php if ( $c['socials'] ) : ?>
						<div class="gl-cta-soc">
							<?php foreach ( $c['socials'] as $s ) :
								$kind = (string) ( $s['kind'] ?? '' );
								if ( empty( $s['url'] ) || ! isset( $labels[ $kind ] ) ) { continue; } ?>
								<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener"
								   aria-label="<?php echo esc_attr( $labels[ $kind ][1] ); ?>"><?php echo fr_icon( $labels[ $kind ][0], 22 ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( $cu = get_field( 'cta_url', $id ) ) : ?>
						<a class="btn btn-go btn-xl" href="<?php echo esc_url( $cu ); ?>" target="_blank" rel="noopener">
							<span><b><?php echo esc_html( (string) ( get_field( 'cta_button', $id ) ?: 'Підписатися' ) ); ?></b></span>
							<?php echo fr_icon( 'i-arrow', 20 ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
