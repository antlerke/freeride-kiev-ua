<?php
/**
 * Template Name: Підбір мотоцикла
 *
 * Вісім кроків показані маршрутом, а не списком: так видно, що це
 * послідовна робота з початком і кінцем, а не перелік обіцянок.
 */

declare( strict_types = 1 );
get_header();

while ( have_posts() ) :
	the_post();
	$id = get_the_ID();
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО: огляд мотоцикла, руки й ключ]'; ?>
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

	<?php $gets = (array) ( get_field( 'deliverables', $id ) ?: [] ); ?>
	<?php if ( $gets ) : ?>
		<section class="wrap" style="padding: 0 24px 40px">
			<div class="gets" data-rev-group="up">
				<?php foreach ( $gets as $g ) : ?>
					<div class="get">
						<span class="get-ic"><?php echo fr_icon( (string) ( $g['icon'] ?: 'i-check' ), 26 ); ?></span>
						<b><?php echo esc_html( (string) $g['title'] ); ?></b>
						<?php if ( ! empty( $g['text'] ) ) : ?><span><?php echo esc_html( $g['text'] ); ?></span><?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $steps = (array) ( get_field( 'steps', $id ) ?: [] ); ?>
	<?php if ( $steps ) : ?>
		<section class="wrap" style="padding: 0 24px 44px">
			<div class="route-head" data-rev="up">
				<h2 class="eyebrow-h"><span class="slash sm"><i></i><i></i></span>Як ми працюємо</h2>
				<?php if ( $intro = get_field( 'intro', $id ) ) : ?>
					<p><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
			</div>
			<?php
			// Лінія маршруту йде вниз по стовпцю, тому й нумерація має йти
			// вниз, а не впоперек: перша половина кроків — лівий стовпець,
			// друга — правий. Кількість рядків знає тільки PHP, бо кроків
			// може бути скільки завгодно.
			$rows = (int) ceil( count( $steps ) / 2 );
			?>
			<ol class="route" data-rev-group="up" style="--rows: <?php echo esc_attr( (string) $rows ); ?>">
				<?php foreach ( $steps as $i => $s ) : ?>
					<?php // кінець стовпця: лінія далі не тягнеться в порожнечу
					$col_end = ( $i + 1 === $rows ) || ( $i + 1 === count( $steps ) ); ?>
					<li class="route-step<?php echo $col_end ? ' is-col-end' : ''; ?>">
						<span class="route-dot" aria-hidden="true"></span>
						<div>
							<b><?php echo esc_html( (string) $s['title'] ); ?></b>
							<?php if ( ! empty( $s['text'] ) ) : ?><span><?php echo esc_html( $s['text'] ); ?></span><?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endif; ?>

	<?php
	$p1 = get_field( 'price_first', $id );
	$p2 = get_field( 'price_second', $id );
	if ( $p1 || $p2 ) :
		?>
		<section class="wrap" style="padding: 0 24px 44px">
			<div class="pricebox" data-rev="up">
				<div class="pricebox-head">
					<h2 class="eyebrow-h"><span class="slash sm"><i></i><i></i></span>Скільки це коштує</h2>
					<?php if ( $pv = get_field( 'proof_value', $id ) ) : ?>
						<div class="proof"><b><?php echo esc_html( $pv ); ?></b><span><?php echo esc_html( (string) get_field( 'proof_text', $id ) ); ?></span></div>
					<?php endif; ?>
				</div>
				<div class="prices">
					<?php if ( $p1 ) : ?>
						<div class="price-part">
							<span class="price-n">1</span>
							<b><?php echo esc_html( (string) $p1 ); ?></b>
							<span><?php echo esc_html( (string) get_field( 'price_first_note', $id ) ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( $p2 ) : ?>
						<div class="price-part">
							<span class="price-n">2</span>
							<b><?php echo esc_html( (string) $p2 ); ?></b>
							<span><?php echo esc_html( (string) get_field( 'price_second_note', $id ) ); ?></span>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $pl = get_field( 'price_logic', $id ) ) : ?>
					<p class="price-logic"><?php echo fr_icon( 'i-piggy', 26 ); ?><?php echo esc_html( $pl ); ?></p>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="wrap" style="padding: 0 24px 56px">
		<div class="duo-form" data-rev-group="up">
			<?php
			get_template_part( 'parts/lead-form', null, [
				'title'  => 'Шукаєте мотоцикл?',
				'intro'  => (string) get_field( 'form_intro', $id ),
				'button' => 'Надіслати запит',
				'topic'  => 'Підбір мотоцикла',
			] );
			?>
			<?php $pi = (int) get_field( 'proof_image', $id ); ?>
			<div class="pickphoto<?php echo $pi ? '' : ' ph'; ?>">
				<?php echo $pi ? wp_get_attachment_image( $pi, 'fr-hero', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО: огляд мотоцикла перед купівлею]'; ?>
			</div>
		</div>
	</section>

	<?php if ( get_the_content() ) : ?>
		<section class="wrap" style="padding: 0 24px 56px"><div class="entry"><?php the_content(); ?></div></section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
