<?php
/**
 * Template Name: Відгуки
 *
 * Відгуки — окремий тип запису, а не текст у сторінці: їх додають
 * і адміністратор, і згодом самі учні після заняття. Один відгук може
 * стосуватися курсу, інструктора або обох — і тоді потрапляє в обидва
 * списки, не дублюючись.
 *
 * Середня оцінка й кількість рахуються з самих відгуків: вписана руками
 * цифра розійшлася б із тим, що видно нижче на цій же сторінці.
 */

declare( strict_types = 1 );
get_header();

while ( have_posts() ) :
	the_post();
	$pid    = get_the_ID();
	$score = fr_review_score();
	$vids  = fr_reviews( [ 'kind' => 'video' ] );
	$texts = fr_reviews( [ 'kind' => 'text' ] );

	// Курси, про які справді є відгуки — фільтр не має пропонувати порожнечу
	$course_codes = [];
	foreach ( $texts as $t ) {
		$c = (int) get_field( 'course', $t->ID );
		if ( $c ) {
			$course_codes[ (string) get_field( 'code', $c ) ] = true;
		}
	}
	$course_codes = array_keys( $course_codes );
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО: мотоцикліст зі спини на тлі Києва]'; ?>
		</div>
		<div class="hero-veil"></div>
		<div class="wrap hero-in">
			<?php fr_breadcrumbs(); ?>
			<h1><span class="slash"><i></i><i></i></span><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="wrap" style="padding: 0 24px 26px">
		<div class="stats score" data-rev-group="up">
			<?php if ( $score['count'] ) : ?>
				<div class="stat">
					<div class="score-big"><b><?php echo esc_html( number_format_i18n( $score['avg'], 1 ) ); ?></b><?php echo fr_icon( 'i-star', 22, 'star-on' ); ?></div>
					<div><span><?php echo esc_html( sprintf( '%d %s на сайті', $score['count'], _n( 'відгук', 'відгуків', $score['count'], 'freeride' ) ) ); ?></span></div>
				</div>
			<?php endif; ?>
			<?php if ( $g = get_field( 'google_rating', $pid ) ) : ?>
				<div class="stat"><?php echo fr_icon( 'i-google', 24 ); ?>
					<div><b style="font-size:19px"><?php echo esc_html( $g ); ?></b><span><?php echo esc_html( (string) get_field( 'google_count', $pid ) ); ?></span></div></div>
			<?php endif; ?>
			<?php foreach ( (array) ( get_field( 'stats', $pid ) ?: [] ) as $s ) : ?>
				<div class="stat"><?php echo fr_icon( (string) ( $s['icon'] ?: 'i-check' ), 26 ); ?>
					<div><b><?php echo esc_html( (string) $s['value'] ); ?></b><span><?php echo esc_html( (string) ( $s['label'] ?? '' ) ); ?></span></div></div>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( $vids && $texts ) : ?>
		<section class="wrap" style="padding: 0 24px 26px">
			<div class="filters" id="filters" data-rev="up">
				<button class="pill-btn" type="button" data-show="all" aria-pressed="true">Усі відгуки (<?php echo esc_html( (string) ( count( $vids ) + count( $texts ) ) ); ?>)</button>
				<button class="pill-btn" type="button" data-show="video" aria-pressed="false">
					<?php echo fr_icon( 'i-play', 17 ); ?>Відео (<?php echo esc_html( (string) count( $vids ) ); ?>)</button>
				<button class="pill-btn" type="button" data-show="text" aria-pressed="false">
					<?php echo fr_icon( 'i-quote', 17 ); ?>Текстові (<?php echo esc_html( (string) count( $texts ) ); ?>)</button>
				<?php if ( $gu = get_field( 'google_url', $pid ) ) : ?>
					<a class="pill-btn" href="<?php echo esc_url( $gu ); ?>" target="_blank" rel="noopener">
						<?php echo fr_icon( 'i-google', 17 ); ?>На Google<?php echo fr_icon( 'i-ext', 15 ); ?></a>
				<?php endif; ?>
				<span class="spacer"></span>
				<?php if ( count( $course_codes ) > 1 ) : ?>
					<label class="pill-btn" style="gap:10px">За курсом
						<select id="by-course" style="font:inherit; font-weight:700; border:0; background:transparent; color:inherit">
							<option value="all">усі</option>
							<?php foreach ( $course_codes as $code ) : ?>
								<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $code ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $vids ) : ?>
		<section class="wrap" id="sec-video" style="padding: 0 24px 48px">
			<div class="head-row" data-rev="up">
				<h2><span class="slash"><i></i><i></i></span>Відеовідгуки наших учнів</h2>
				<?php if ( count( $vids ) > 2 ) : ?>
					<span class="vid-nav">
						<button type="button" data-scroll="-1" aria-label="Попередні відео"><?php echo fr_icon( 'i-arrow', 18 ); ?></button>
						<button type="button" data-scroll="1" aria-label="Наступні відео"><?php echo fr_icon( 'i-arrow', 18 ); ?></button>
					</span>
				<?php endif; ?>
			</div>
			<div class="vids" id="vids" role="region" aria-label="Відеовідгуки" tabindex="0">
				<?php foreach ( $vids as $v ) :
					$c   = (int) get_field( 'course', $v->ID );
					$url = (string) get_field( 'video_url', $v->ID );
					$tag = $url ? 'a' : 'article';
					?>
					<<?php echo $tag; ?> class="vid"<?php if ( $url ) : ?> href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"<?php endif; ?>>
						<span class="vid-shot<?php echo has_post_thumbnail( $v->ID ) ? '' : ' ph'; ?>">
							<?php echo has_post_thumbnail( $v->ID )
								? get_the_post_thumbnail( $v->ID, 'fr-card', [ 'alt' => '', 'loading' => 'lazy' ] )
								: '[ФОТО: кадр з відео]'; ?>
						</span>
						<?php if ( $c ) : ?><span class="vid-tag"><?php echo esc_html( (string) get_field( 'code', $c ) ); ?></span><?php endif; ?>
						<span class="vid-play"><?php echo fr_icon( 'i-play', 22 ); ?></span>
						<b><?php echo esc_html( get_the_title( $v->ID ) ); ?></b>
						<?php if ( $vl = get_field( 'video_length', $v->ID ) ) : ?>
							<span class="vid-time"><?php echo esc_html( $vl ); ?></span>
						<?php endif; ?>
					</<?php echo $tag; ?>>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $texts ) : ?>
		<section class="wrap" id="sec-text" style="padding: 0 24px 48px">
			<div class="head-row" data-rev="up">
				<h2><span class="slash"><i></i><i></i></span>Що говорять наші учні</h2>
			</div>
			<div class="revs" id="revs" data-rev-group="up">
				<?php foreach ( $texts as $t ) { get_template_part( 'parts/review-card', null, [ 'review' => $t ] ); } ?>
			</div>
			<p class="muted" id="revs-empty" hidden style="text-align:center; margin:18px 0 0">Про цей курс відгуків поки немає.</p>
		</section>
	<?php endif; ?>

	<?php
	$press = (array) ( get_field( 'press', $pid ) ?: [] );
	$gu    = get_field( 'google_url', $pid );
	if ( $press || $gu ) :
		?>
		<section class="wrap" style="padding: 0 24px 48px">
			<div class="press" data-rev="up">
				<?php if ( $press ) : ?>
					<div>
						<h3><span class="slash"><i></i><i></i></span>Про нас пишуть</h3>
						<div class="press-logos">
							<?php foreach ( $press as $p ) :
								$logo = (int) ( $p['logo'] ?? 0 );
								$url  = (string) ( $p['url'] ?? '' );
								$tag  = $url ? 'a' : 'span';
								?>
								<<?php echo $tag; ?> class="<?php echo $logo ? '' : 'ph'; ?>"<?php if ( $url ) : ?> href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"<?php endif; ?>>
									<?php echo $logo
										? wp_get_attachment_image( $logo, 'medium', false, [ 'alt' => esc_attr( (string) $p['name'] ), 'loading' => 'lazy' ] )
										: '[ЛОГО: ' . esc_html( (string) $p['name'] ) . ']'; ?>
								</<?php echo $tag; ?>>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( $gu ) : ?>
					<div>
						<h3><span class="slash"><i></i><i></i></span>Рейтинг на Google</h3>
						<div class="google-row">
							<?php echo fr_icon( 'i-google', 26 ); ?>
							<span class="score-big"><b style="font-family:'Montserrat',sans-serif; font-weight:800; font-style:italic"><?php echo esc_html( (string) ( get_field( 'google_rating', $pid ) ?: '—' ) ); ?></b>
								<?php echo fr_icon( 'i-star', 20, 'perk-ic' ); ?></span>
							<span class="muted" style="font-size:14.5px"><?php echo esc_html( (string) get_field( 'google_count', $pid ) ); ?></span>
							<a class="pill-btn" href="<?php echo esc_url( $gu ); ?>" target="_blank" rel="noopener">Переглянути на карті<?php echo fr_icon( 'i-ext', 15 ); ?></a>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! $vids && ! $texts ) : ?>
		<section class="wrap" style="padding: 0 24px 48px">
			<p>Відгуків ще немає. Додайте їх у розділі «Відгуки» в адмінці.</p>
		</section>
	<?php endif; ?>

	<section class="final" id="final">
		<div class="final-media ph">[ФОТО: мотоцикліст на серпантині, захід сонця]</div>
		<div class="final-veil"></div>
		<div class="wrap final-in">
			<h2>Хочете так само?</h2>
			<p>Почніть із пробного уроку — і напишіть свій відгук за кілька тижнів.</p>
			<?php get_template_part( 'parts/cta-trial' ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
