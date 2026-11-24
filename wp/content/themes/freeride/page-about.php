<?php
/**
 * Template Name: Про нас
 *
 * Інструктори беруться з розділу «Інструктори», а не переписуються
 * тут руками: той самий список показується на сторінці «Команда».
 */

declare( strict_types = 1 );
get_header();

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

	<?php if ( $fname = get_field( 'founder_name', $id ) ) : ?>
		<section class="wrap" style="padding: 0 24px 48px">
			<div class="founder" data-rev="up">
				<?php $fp = (int) get_field( 'founder_photo', $id ); ?>
				<div class="founder-photo<?php echo $fp ? '' : ' ph'; ?>">
					<?php echo $fp ? wp_get_attachment_image( $fp, 'fr-card', false, [ 'alt' => esc_attr( (string) $fname ), 'loading' => 'lazy' ] ) : '[ФОТО: вертикальний портрет засновника]'; ?>
				</div>
				<div class="founder-body">
					<p class="kicker-sm">Засновник FreeRide</p>
					<h2><?php echo esc_html( (string) $fname ); ?></h2>
					<?php if ( $fr = get_field( 'founder_role', $id ) ) : ?>
						<p class="founder-role"><?php echo esc_html( $fr ); ?></p>
					<?php endif; ?>
					<?php if ( $fq = get_field( 'founder_quote', $id ) ) : ?>
						<blockquote><?php echo esc_html( $fq ); ?></blockquote>
					<?php endif; ?>
					<?php if ( $fs = get_field( 'founder_sign', $id ) ) : ?>
						<div class="founder-sign"><span><?php echo esc_html( $fs ); ?></span></div>
					<?php endif; ?>
				</div>
				<?php $facts = (array) ( get_field( 'founder_facts', $id ) ?: [] ); ?>
				<?php if ( $facts ) : ?>
					<div class="founder-facts">
						<?php foreach ( $facts as $f ) : ?>
							<div class="fact"><?php echo fr_icon( (string) ( $f['icon'] ?: 'i-check' ), 26 ); ?>
								<div><b><?php echo esc_html( (string) $f['value'] ); ?></b><span><?php echo esc_html( (string) ( $f['label'] ?? '' ) ); ?></span></div></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$mtxt  = get_field( 'mission_text', $id );
	$prin  = (array) ( get_field( 'principles', $id ) ?: [] );
	if ( $mtxt || $prin ) :
		$mi = (int) get_field( 'mission_image', $id );
		?>
		<section class="mission">
			<div class="mission-media<?php echo $mi ? '' : ' ph'; ?>">
				<?php echo $mi ? wp_get_attachment_image( $mi, 'fr-hero', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО: гірська дорога]'; ?>
			</div>
			<div class="mission-veil"></div>
			<div class="wrap mission-in">
				<div class="mission-txt" data-rev="left">
					<h2><span class="slash"><i></i><i></i></span>Наша місія</h2>
					<?php if ( $mtxt ) : ?><p><?php echo esc_html( $mtxt ); ?></p><?php endif; ?>
				</div>
				<?php if ( $prin ) : ?>
					<div data-rev="right">
						<h3 style="margin:0"><span class="slash"><i></i><i></i></span>Наші принципи</h3>
						<div class="principles">
							<?php foreach ( $prin as $p ) : ?>
								<div class="principle"><?php echo fr_icon( (string) ( $p['icon'] ?: 'i-check' ), 34 ); ?>
									<b><?php echo wp_kses( nl2br( esc_html( (string) $p['title'] ) ), [ 'br' => [] ] ); ?></b></div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $mates = fr_mates( 4 ); ?>
	<?php if ( $mates ) : ?>
		<section class="wrap" style="padding: 48px 24px">
			<div class="head-row" data-rev="up">
				<h2><span class="slash"><i></i><i></i></span>Команда інструкторів</h2>
				<?php
				$team = get_page_by_path( 'komanda' );
				if ( $team ) :
					?>
					<a href="<?php echo esc_url( (string) get_permalink( $team ) ); ?>" style="font-weight:700; font-size:15px">Уся команда →</a>
				<?php endif; ?>
			</div>
			<div class="team" data-rev-group="up">
				<?php foreach ( $mates as $m ) { get_template_part( 'parts/mate-card', null, [ 'mate' => $m ] ); } ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $stats = (array) ( get_field( 'stats', $id ) ?: [] ); ?>
	<?php if ( $stats ) : ?>
		<section class="wrap" style="padding: 0 24px 48px">
			<div class="stats" data-rev-group="up">
				<?php foreach ( $stats as $s ) : ?>
					<div class="stat"><?php echo fr_icon( (string) ( $s['icon'] ?: 'i-check' ), 26 ); ?>
						<div><b><?php echo esc_html( (string) $s['value'] ); ?></b><span><?php echo esc_html( (string) ( $s['label'] ?? '' ) ); ?></span></div></div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$steps = (array) ( get_field( 'steps', $id ) ?: [] );
	$chips = (array) ( get_field( 'gear_chips', $id ) ?: [] );
	if ( $steps || $chips ) :
		?>
		<section class="wrap" style="padding: 0 24px 48px">
			<div class="duo">
				<?php if ( $steps ) : ?>
					<div class="duo-col" data-rev="left">
						<h2><span class="slash"><i></i><i></i></span>Як ми навчаємо</h2>
						<div class="duo-split">
							<div class="steps">
								<?php foreach ( $steps as $s ) : ?>
									<div class="step"><?php echo fr_icon( (string) ( $s['icon'] ?: 'i-check' ), 24 ); ?>
										<div><b><?php echo esc_html( (string) $s['title'] ); ?></b><span><?php echo esc_html( (string) ( $s['text'] ?? '' ) ); ?></span></div></div>
								<?php endforeach; ?>
							</div>
							<?php $si = (int) get_field( 'steps_image', $id ); ?>
							<div class="duo-photo<?php echo $si ? '' : ' ph'; ?>">
								<?php echo $si ? wp_get_attachment_image( $si, 'fr-card', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО: мотоцикл у русі]'; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $chips ) : ?>
					<div class="duo-col" data-rev="right">
						<h2><span class="slash"><i></i><i></i></span>Наша техніка та екіпірування</h2>
						<?php $gi = (int) get_field( 'gear_image', $id ); ?>
						<div class="duo-wide<?php echo $gi ? '' : ' ph'; ?>">
							<?php echo $gi ? wp_get_attachment_image( $gi, 'fr-hero', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО: мотоцикли школи в ряд]'; ?>
						</div>
						<div class="chips">
							<?php foreach ( $chips as $ch ) : ?>
								<span class="chip"><?php echo fr_icon( (string) ( $ch['icon'] ?: 'i-check' ), 20 ); ?><?php echo esc_html( (string) $ch['text'] ); ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( get_the_content() ) : ?>
		<section class="wrap" style="padding: 0 24px 48px"><div class="entry"><?php the_content(); ?></div></section>
	<?php endif; ?>

	<section class="final" id="final">
		<?php $fi = (int) get_field( 'final_image', $id ); ?>
		<div class="final-media<?php echo $fi ? '' : ' ph'; ?>">
			<?php echo $fi ? wp_get_attachment_image( $fi, 'fr-hero', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО: троє мотоциклістів на оглядовому майданчику]'; ?>
		</div>
		<div class="final-veil"></div>
		<div class="wrap final-in">
			<h2>Приєднуйтесь до спільноти FreeRide!</h2>
			<?php if ( $ft = get_field( 'final_text', $id ) ) : ?>
				<p><?php echo esc_html( $ft ); ?></p>
			<?php endif; ?>
			<?php get_template_part( 'parts/cta-trial' ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
