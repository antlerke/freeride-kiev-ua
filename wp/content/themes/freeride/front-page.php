<?php
/**
 * Головна.
 *
 * Чотири рівні внизу — не окремий список, а ті самі курси з розділу
 * «Курси». Додали курс або змінили ціну — головна оновилася сама.
 */

declare( strict_types = 1 );
get_header();

$id = (int) get_option( 'page_on_front' );
?>
<section class="hero">
	<div class="hero-media<?php echo has_post_thumbnail( $id ) ? '' : ' ph'; ?>">
		<?php echo has_post_thumbnail( $id )
			? get_the_post_thumbnail( $id, 'fr-hero', [ 'alt' => '' ] )
			: '[ФОТО: мотоцикліст на трасі, вечірнє світло]'; ?>
	</div>
	<div class="hero-veil"></div>
	<div class="wrap hero-in">
		<h1><span class="slash"><i></i><i></i></span>Мотошкола<br>Free<span class="ride">Ride</span></h1>
		<?php if ( $sub = get_field( 'hero_sub', $id ) ) : ?>
			<p class="hero-sub"><?php echo esc_html( $sub ); ?></p>
		<?php endif; ?>
		<?php
		if ( $motto = get_field( 'hero_motto', $id ) ) :
			// Останнє слово підсвічується — так у макеті, і так воно
			// лишається підсвіченим, хоч би яке гасло поставили
			$words = preg_split( '/\s+/u', trim( (string) $motto ) );
			$last  = array_pop( $words );
			?>
			<p class="hero-motto"><?php
				echo esc_html( implode( ' ', $words ) );
				echo $words ? ' ' : '';
				?><span><?php echo esc_html( (string) $last ); ?></span></p>
		<?php endif; ?>

		<div class="hero-cta">
			<?php get_template_part( 'parts/cta-trial' ); ?>
			<?php if ( $v = get_field( 'video_url', $id ) ) : ?>
				<a class="btn btn-ghost btn-xl" href="<?php echo esc_url( $v ); ?>" target="_blank" rel="noopener">
					<svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true"><path d="M3 1.6 15.5 9 3 16.4V1.6Z" fill="currentColor"/></svg>
					<span><b><?php echo esc_html( (string) ( get_field( 'video_label', $id ) ?: 'Дивитись відео' ) ); ?></b><s class="muted">про навчання</s></span></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php $perks = (array) ( get_field( 'perks', $id ) ?: [] ); ?>
<?php if ( $perks ) : ?>
	<section class="wrap" style="padding: 0 24px">
		<div class="perks" data-rev-group="up">
			<?php foreach ( $perks as $p ) : ?>
				<div class="perk">
					<?php echo fr_icon( (string) ( $p['icon'] ?: 'i-check' ), 30, 'perk-ic' ); ?>
					<div><b><?php echo esc_html( (string) $p['title'] ); ?></b><span><?php echo esc_html( (string) ( $p['text'] ?? '' ) ); ?></span></div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

<?php $courses = fr_courses( true ); ?>
<?php if ( $courses ) : ?>
	<section class="wrap" id="levels" style="padding: 44px 24px 48px">
		<div data-rev="up" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: baseline; margin-bottom: 26px">
			<h2 style="margin: 0; flex-grow: 1"><span class="slash"><i></i><i></i></span>Оберіть свій рівень навчання</h2>
			<a href="<?php echo esc_url( (string) get_post_type_archive_link( FR_COURSE ) ); ?>" style="font-weight: 700; font-size: 15px">Усі курси та ціни →</a>
		</div>
		<div class="levels" data-rev-group="up">
			<?php foreach ( $courses as $c ) : $h = fr_course_hours( $c->ID ); ?>
				<a class="lvl" href="<?php echo esc_url( (string) get_permalink( $c ) ); ?>">
					<div class="lvl-head"><i></i><span>
						<b><?php echo esc_html( (string) get_field( 'code', $c->ID ) ); ?></b>
						<em><?php echo esc_html( get_the_title( $c ) ); ?></em>
					</span></div>
					<div class="lvl-media<?php echo has_post_thumbnail( $c->ID ) ? '' : ' ph'; ?>">
						<?php echo has_post_thumbnail( $c->ID )
							? get_the_post_thumbnail( $c->ID, 'fr-card', [ 'alt' => '', 'loading' => 'lazy' ] )
							: '[ФОТО не додано]'; ?>
					</div>
					<div class="lvl-foot">
						<span class="lvl-hrs"><?php echo fr_icon( 'i-clock', 19 ); ?><?php echo esc_html( (string) $h ); ?> <?php echo esc_html( _n( 'година', 'годин', $h, 'freeride' ) ); ?></span>
						<span class="lvl-go"><?php echo fr_icon( 'i-arrow', 17 ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( $rt = get_field( 'rider_text', $id ) ) : ?>
	<section class="wrap" id="rider" style="padding: 0 24px 48px">
		<div class="rider" data-rev="up">
			<div>
				<h2><span class="slash"><i></i><i></i></span>Rider&nbsp;Card<sup style="font-size:.5em; vertical-align:super">®</sup></h2>
				<?php if ( $rl = get_field( 'rider_lede', $id ) ) : ?>
					<p class="rider-lede"><?php echo esc_html( $rl ); ?></p>
				<?php endif; ?>
				<?php $pts = (array) ( get_field( 'rider_points', $id ) ?: [] ); ?>
				<?php if ( $pts ) : ?>
					<ul>
						<?php foreach ( $pts as $pt ) : ?>
							<li><?php echo fr_icon( 'i-check', 21, 'perk-ic' ); ?><?php echo esc_html( (string) $pt['text'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'parts/rider-phone' ); ?>
			<div class="rider-side">
				<p><?php echo wp_kses( (string) $rt, [ 'br' => [] ] ); ?></p>
				<?php if ( $ru = get_field( 'rider_url', $id ) ) : ?>
					<a class="btn btn-ghost" href="<?php echo esc_url( $ru ); ?>">Детальніше про Rider Card
						<?php echo fr_icon( 'i-arrow', 19 ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $qt = get_field( 'quote_text', $id ) ) : $rating = (int) ( get_field( 'quote_rating', $id ) ?: 5 ); ?>
	<section class="wrap" id="review" style="padding: 0 24px 40px">
		<div class="quote" data-rev="up">
			<?php $qa = (int) get_field( 'quote_photo', $id ); ?>
			<div class="quote-ava<?php echo $qa ? '' : ' ph'; ?>"<?php echo $qa ? '' : ' style="padding:0; font-size:10px"'; ?>>
				<?php echo $qa ? wp_get_attachment_image( $qa, 'thumbnail', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО]'; ?>
			</div>
			<div class="quote-text">
				<span class="quote-mark" aria-hidden="true">&ldquo;</span>
				<blockquote><?php echo esc_html( $qt ); ?></blockquote>
			</div>
			<div class="quote-who">
				<div style="font-size:15.5px"><?php echo esc_html( (string) get_field( 'quote_author', $id ) ); ?></div>
				<div class="stars" aria-label="Оцінка <?php echo esc_attr( (string) $rating ); ?> з 5">
					<?php for ( $i = 0; $i < $rating; $i++ ) { echo fr_icon( 'i-star', 20, 'star-on' ); } ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="final" id="final">
	<?php $fi = (int) get_field( 'final_image', $id ); ?>
	<div class="final-media<?php echo $fi ? '' : ' ph'; ?>">
		<?php echo $fi ? wp_get_attachment_image( $fi, 'fr-hero', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО: серпантин на заході сонця]'; ?>
	</div>
	<div class="final-veil"></div>
	<div class="wrap final-in">
		<h2>Готовий(а) розпочати?</h2>
		<?php if ( $ft = get_field( 'final_text', $id ) ) : ?>
			<p><?php echo esc_html( $ft ); ?></p>
		<?php endif; ?>
		<?php get_template_part( 'parts/cta-trial' ); ?>
	</div>
</section>

<?php get_footer();
