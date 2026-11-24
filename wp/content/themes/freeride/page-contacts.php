<?php
/**
 * Template Name: Контакти
 *
 * Сторінка, заради якої існують усі інші: тут людина пише або дзвонить.
 * Тому телефон і месенджери стоять вище за форму — більшість не хоче
 * заповнювати поля, якщо можна натиснути одну кнопку.
 */

declare( strict_types = 1 );
get_header();

$c      = fr_contacts();
$labels = fr_channel_labels();

while ( have_posts() ) :
	the_post();
	$id = get_the_ID();
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО: мотоцикліст на тлі Києва]'; ?>
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

	<!-- Швидкі контакти: один клік, без форми -->
	<section class="wrap" style="padding: 0 24px 40px">
		<div class="qcon" data-rev-group="up">
			<?php if ( $c['phone'] ) : ?>
				<a class="qcon-card" href="tel:<?php echo esc_attr( $c['phone_raw'] ?: $c['phone'] ); ?>">
					<span class="qcon-ic"><?php echo fr_icon( 'i-phone', 21 ); ?></span>
					<span class="qcon-txt"><s>Телефон</s><b><?php echo esc_html( $c['phone'] ); ?></b></span>
					<?php echo fr_icon( 'i-chev', 17, 'qcon-go' ); ?>
				</a>
			<?php endif; ?>

			<?php
			// WhatsApp і Telegram — одна картка на двох: у макеті вони поруч,
			// і людині байдуже, у якому саме месенджері їй відповідять.
			$chat = array_values( array_filter(
				$c['messengers'],
				static fn( $m ) => in_array( $m['kind'] ?? '', [ 'wa', 'tg' ], true ) && ! empty( $m['url'] )
			) );
			if ( $chat ) :
				?>
				<a class="qcon-card" href="<?php echo esc_url( $chat[0]['url'] ); ?>" target="_blank" rel="noopener">
					<span class="qcon-ic qcon-ic-2">
						<?php foreach ( $chat as $m ) { echo fr_icon( $labels[ $m['kind'] ][0], 21 ); } ?>
					</span>
					<span class="qcon-txt">
						<s><?php echo esc_html( implode( ' / ', array_map( static fn( $m ) => $labels[ $m['kind'] ][1], $chat ) ) ); ?></s>
						<b>Написати зараз</b>
					</span>
					<?php echo fr_icon( 'i-chev', 17, 'qcon-go' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $c['email'] ) : ?>
				<a class="qcon-card" href="mailto:<?php echo esc_attr( $c['email'] ); ?>">
					<span class="qcon-ic"><?php echo fr_icon( 'i-mail', 21 ); ?></span>
					<span class="qcon-txt"><s>E-mail</s><b><?php echo esc_html( $c['email'] ); ?></b></span>
					<?php echo fr_icon( 'i-chev', 17, 'qcon-go' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $c['hours'] ) : ?>
				<div class="qcon-card is-static">
					<span class="qcon-ic"><?php echo fr_icon( 'i-clock', 21 ); ?></span>
					<span class="qcon-txt"><s>Графік роботи</s><b><?php echo esc_html( $c['hours'] ); ?></b></span>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- Локація -->
	<?php
	$map   = (int) get_field( 'loc_map', $id );
	$photo = (int) get_field( 'loc_photo', $id );
	if ( $map || $photo || $c['address'] ) :
		?>
		<section class="wrap" style="padding: 0 24px 40px">
			<div class="loc" data-rev-group="up">
				<div class="loc-map">
					<?php if ( $map ) : ?>
						<?php echo wp_get_attachment_image( $map, 'large', false, [ 'alt' => 'Карта проїзду', 'loading' => 'lazy' ] ); ?>
					<?php else : ?>
						<div class="ph" style="aspect-ratio: 16/11">[КАРТА: статичний знімок]</div>
					<?php endif; ?>
					<?php if ( $c['map_url'] ) : ?>
						<a class="loc-ext" href="<?php echo esc_url( $c['map_url'] ); ?>" target="_blank" rel="noopener"
						   aria-label="Відкрити карту в новій вкладці"><?php echo fr_icon( 'i-ext', 18 ); ?></a>
					<?php endif; ?>
				</div>

				<div class="loc-txt">
					<?php if ( $e = get_field( 'loc_eyebrow', $id ) ) : ?>
						<p class="eyebrow"><i></i><?php echo esc_html( $e ); ?></p>
					<?php endif; ?>
					<h2><?php echo esc_html( (string) ( get_field( 'loc_title', $id ) ?: 'Наш майданчик' ) ); ?></h2>
					<?php if ( $m = get_field( 'loc_metro', $id ) ) : ?>
						<p class="loc-row"><?php echo fr_icon( 'i-pin', 21 ); ?><b><?php echo esc_html( $m ); ?></b></p>
					<?php endif; ?>
					<?php if ( $c['address'] ) : ?>
						<p class="loc-row"><?php echo fr_icon( 'i-moto', 21 ); ?><span>
							<b><?php echo esc_html( $c['address'] ); ?></b>
							<?php if ( $n = get_field( 'loc_note', $id ) ) : ?>
								<s><?php echo esc_html( $n ); ?></s>
							<?php endif; ?>
						</span></p>
					<?php endif; ?>
				</div>

				<div class="loc-photo<?php echo $photo ? '' : ' ph'; ?>">
					<?php echo $photo
						? wp_get_attachment_image( $photo, 'fr-card', false, [ 'alt' => '', 'loading' => 'lazy' ] )
						: '[ФОТО: майданчик із конусами]'; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- Форма і онлайн-запис -->
	<section class="wrap" style="padding: 0 24px 40px">
		<div class="duo-form" data-rev-group="up">

			<?php
			get_template_part( 'parts/lead-form', null, [
				'title'   => (string) ( get_field( 'form_title', $id ) ?: 'Звʼяжіться з нами' ),
				'button'  => (string) ( get_field( 'form_button', $id ) ?: 'Надіслати запит' ),
				'success' => (string) ( get_field( 'form_success', $id ) ?: 'Дякуємо! Передзвонимо найближчим часом.' ),
				'topics'  => (array) ( get_field( 'form_topics', $id ) ?: [] ),
			] );
			?>

			<?php if ( $bu = get_field( 'book_url', $id ) ) : ?>
				<div class="bookcard">
					<div class="bookcard-txt">
						<?php echo fr_icon( 'i-cal', 28, 'bookcard-ic' ); ?>
						<h2><?php echo esc_html( (string) get_field( 'book_title', $id ) ); ?></h2>
						<?php if ( $bt = get_field( 'book_text', $id ) ) : ?>
							<p><?php echo esc_html( $bt ); ?></p>
						<?php endif; ?>
						<a class="btn btn-go btn-xl" href="<?php echo esc_url( $bu ); ?>">
							<span><b><?php echo esc_html( (string) ( get_field( 'book_button', $id ) ?: 'Записатися' ) ); ?></b></span>
							<?php echo fr_icon( 'i-arrow', 20 ); ?></a>
					</div>
					<div class="bookcard-img<?php echo get_field( 'book_image', $id ) ? '' : ' ph'; ?>">
						<?php
						$bi = (int) get_field( 'book_image', $id );
						echo $bi ? wp_get_attachment_image( $bi, 'fr-card', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ЗОБРАЖЕННЯ: телефон із вікном запису]';
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php $bene = (array) ( get_field( 'benefits', $id ) ?: [] ); ?>
	<?php if ( $bene ) : ?>
		<section class="wrap" style="padding: 0 24px 56px">
			<div class="wins" data-rev-group="up">
				<?php foreach ( $bene as $b ) : ?>
					<div>
						<?php echo fr_icon( (string) ( $b['icon'] ?: 'i-check' ), 28 ); ?>
						<b><?php echo esc_html( (string) $b['title'] ); ?>
							<?php if ( ! empty( $b['text'] ) ) : ?><s><?php echo esc_html( $b['text'] ); ?></s><?php endif; ?>
						</b>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( get_the_content() ) : ?>
		<section class="wrap" style="padding: 0 24px 56px">
			<div class="entry"><?php the_content(); ?></div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
