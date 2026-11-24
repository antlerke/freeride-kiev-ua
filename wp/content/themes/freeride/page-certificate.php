<?php
/**
 * Template Name: Подарунковий сертифікат
 *
 * Ціни варіантів беруться з курсів, а не вписуються тут: сторінка
 * сертифіката інакше першою розійшлася б із прайсом.
 */

declare( strict_types = 1 );
get_header();

while ( have_posts() ) :
	the_post();
	$pid = get_the_ID();
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО: мотоцикліст і міський краєвид]'; ?>
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

	<?php $gifts = (array) ( get_field( 'gifts', $pid ) ?: [] ); ?>
	<section class="wrap" style="padding: 0 24px 44px">
		<div class="certrow" data-rev="up">
			<?php
			get_template_part( 'parts/certificate-card', null, [
				'caption' => (string) ( get_field( 'card_caption', $pid ) ?: 'Подарунковий сертифікат' ),
				'note'    => (string) get_field( 'card_note', $pid ),
				'value'   => (string) get_field( 'card_value', $pid ),
				'term'    => (string) get_field( 'card_term', $pid ),
			] );
			?>
			<div class="certrow-txt">
				<h2>Що можна подарувати</h2>
				<?php if ( $gifts ) : ?>
					<div class="gifts">
						<?php foreach ( $gifts as $g ) :
							$cid   = (int) ( $g['course'] ?? 0 );
							$price = $cid ? (int) get_field( 'price', $cid ) : 0;
							$href  = $cid ? get_permalink( $cid ) : '';
							?>
							<div class="gift">
								<span class="gift-ic"><?php echo fr_icon( (string) ( $g['icon'] ?: 'i-cert' ), 24 ); ?></span>
								<div class="gift-txt">
									<b><?php echo esc_html( (string) $g['title'] ); ?></b>
									<?php if ( ! empty( $g['text'] ) ) : ?><span><?php echo esc_html( $g['text'] ); ?></span><?php endif; ?>
								</div>
								<div class="gift-price">
									<?php if ( $price ) : ?>
										<b><?php echo esc_html( number_format_i18n( $price ) ); ?> грн</b>
									<?php elseif ( ! empty( $g['price_note'] ) ) : ?>
										<b><?php echo esc_html( $g['price_note'] ); ?></b>
									<?php endif; ?>
									<?php if ( $href ) : ?>
										<a href="<?php echo esc_url( (string) $href ); ?>">про курс →</a>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php $calc = get_page_by_path( 'kalkulyator' ); ?>
				<?php if ( $calc ) : ?>
					<p class="certrow-hint"><?php echo fr_icon( 'i-calc', 20 ); ?><span>Можна
						подарувати й власну програму — зберіть її в
						<a href="<?php echo esc_url( (string) get_permalink( $calc ) ); ?>">калькуляторі</a>
						і назвіть суму при замовленні.</span></p>
				<?php endif; ?>
				<?php if ( $env = get_field( 'envelope_note', $pid ) ) : ?>
					<p class="certrow-env"><?php echo fr_icon( 'i-check', 19, 'perk-ic' ); ?><span><?php echo esc_html( $env ); ?></span></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php $steps = (array) ( get_field( 'steps', $pid ) ?: [] ); ?>
	<?php if ( $steps ) : ?>
		<section class="wrap" style="padding: 0 24px 44px">
			<h2 class="eyebrow-h"><span class="slash sm"><i></i><i></i></span>Як це працює</h2>
			<div class="howto" data-rev-group="up">
				<?php foreach ( $steps as $i => $s ) : ?>
					<div class="howto-step">
						<span class="howto-n"><?php echo esc_html( (string) ( $i + 1 ) ); ?></span>
						<b><?php echo esc_html( (string) $s['title'] ); ?></b>
						<?php if ( ! empty( $s['text'] ) ) : ?><span><?php echo esc_html( $s['text'] ); ?></span><?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $ship = (array) ( get_field( 'delivery', $pid ) ?: [] ); ?>
	<section class="wrap" style="padding: 0 24px 56px">
		<div class="duo-form" data-rev-group="up">
			<?php
			get_template_part( 'parts/lead-form', null, [
				'title'  => 'Замовити сертифікат',
				'intro'  => (string) get_field( 'form_intro', $pid ),
				'button' => 'Замовити',
				'topic'  => 'Подарунковий сертифікат',
			] );
			?>
			<?php if ( $ship ) : ?>
				<div class="ship">
					<h2 class="eyebrow-h"><span class="slash sm"><i></i><i></i></span>Як отримати</h2>
					<?php foreach ( $ship as $d ) : ?>
						<div class="ship-row">
							<span class="ship-ic"><?php echo fr_icon( (string) ( $d['icon'] ?: 'i-check' ), 22 ); ?></span>
							<div><b><?php echo esc_html( (string) $d['title'] ); ?></b>
								<?php if ( ! empty( $d['text'] ) ) : ?><span><?php echo esc_html( $d['text'] ); ?></span><?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( get_the_content() ) : ?>
		<section class="wrap" style="padding: 0 24px 56px"><div class="entry"><?php the_content(); ?></div></section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
