<?php
/**
 * Сторінка курсу.
 *
 * Два блоки тут не заповнюються руками, а рахуються: «із чого складаються
 * години» — зі складу самого курсу, таблиця порівняння — з усіх курсів
 * одразу. Через це вони фізично не можуть розійтися з прайсом, а редактор
 * не мусить памʼятати, що після зміни однієї ціни треба правити ще
 * чотири сторінки.
 */

declare( strict_types = 1 );
get_header();

$types = (array) ( get_field( 'lesson_types', 'option' ) ?: [] );
$byid  = [];
foreach ( $types as $t ) {
	if ( ! empty( $t['id'] ) ) {
		$byid[ $t['id'] ] = $t;
	}
}
$doc_label = static fn( string $d ): string => 'A1' === $d ? 'А1' : ( 'A' === $d ? 'А' : $d );

while ( have_posts() ) :
	the_post();
	$cid     = get_the_ID();
	$code   = (string) get_field( 'code', $cid );
	$price  = (int) get_field( 'price', $cid );
	$hours  = fr_course_hours( $cid );
	$docs   = (array) ( get_field( 'docs', $cid ) ?: [] );
	$doctxt = implode( ' та ', array_map( $doc_label, $docs ) );
	?>
	<section class="chero">
		<div class="chero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО курсу: горизонтальний кадр під усю ширину]'; ?>
		</div>
		<div class="chero-veil"></div>
		<div class="wrap chero-in">
			<?php if ( $k = get_field( 'kicker', $cid ) ) : ?>
				<p class="chero-kicker"><span class="slash"><i></i><i></i></span><?php echo esc_html( $k ); ?></p>
			<?php endif; ?>
			<h1><?php echo esc_html( $code ?: get_the_title() ); ?></h1>
			<?php if ( $sub = get_field( 'summary', $cid ) ) : ?>
				<p class="chero-sub"><?php echo esc_html( $sub ); ?></p>
			<?php endif; ?>

			<div class="facts">
				<?php if ( $hours ) : ?>
					<div class="fact"><?php echo fr_icon( 'i-clock', 26 ); ?>
						<div><b><?php echo esc_html( (string) $hours ); ?> <?php echo esc_html( _n( 'година', 'годин', $hours, 'freeride' ) ); ?></b><span>практики</span></div></div>
				<?php endif; ?>
				<?php foreach ( (array) ( get_field( 'facts', $cid ) ?: [] ) as $f ) : ?>
					<div class="fact"><?php echo fr_icon( (string) ( $f['icon'] ?: 'i-check' ), 26 ); ?>
						<div><b><?php echo esc_html( (string) $f['title'] ); ?></b><span><?php echo esc_html( (string) ( $f['text'] ?? '' ) ); ?></span></div></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="wrap" style="padding: 40px 24px 48px">
		<div class="top3">
			<?php $vp = (int) get_field( 'video_poster', $cid ); $vu = (string) get_field( 'video_url', $cid ); ?>
			<?php if ( $vu || $vp ) : $tag = $vu ? 'a' : 'div'; ?>
				<<?php echo $tag; ?> class="vid" data-rev="left"<?php if ( $vu ) : ?> href="<?php echo esc_url( $vu ); ?>" target="_blank" rel="noopener"<?php endif; ?>>
					<?php echo $vp
						? wp_get_attachment_image( $vp, 'fr-card', false, [ 'alt' => '', 'loading' => 'lazy' ] )
						: '<div class="ph">[ВІДЕО: огляд курсу ' . esc_html( $code ) . ']</div>'; ?>
					<span class="vid-play"><?php echo fr_icon( 'i-play', 22 ); ?></span>
					<?php if ( $vl = get_field( 'video_length', $cid ) ) : ?>
						<span class="vid-len"><?php echo esc_html( $vl ); ?></span>
					<?php endif; ?>
				</<?php echo $tag; ?>>
			<?php endif; ?>

			<?php $inc = (array) ( get_field( 'includes', $cid ) ?: [] ); ?>
			<?php if ( $inc ) : ?>
				<div class="plan" data-rev="up">
					<h2 style="margin-bottom: 16px"><span class="slash"><i></i><i></i></span>Програма курсу</h2>
					<ul>
						<?php foreach ( $inc as $row ) : ?>
							<?php if ( empty( $row['text'] ) ) { continue; } ?>
							<li><?php echo fr_icon( 'i-check', 20 ); ?><?php echo esc_html( $row['text'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<aside class="pricebox" data-rev="right">
				<div class="sum"><?php echo esc_html( number_format_i18n( $price ) ); ?> грн</div>
				<p class="sum-note">
					<?php echo esc_html( (string) $hours ); ?> <?php echo esc_html( _n( 'година', 'годин', $hours, 'freeride' ) ); ?> практики<?php
					echo $doctxt ? ' · документи ' . esc_html( $doctxt ) . ' включені' : ''; ?>
				</p>
				<a class="btn btn-go" href="#final"><?php echo fr_icon( 'i-cal', 19 ); ?>Записатися на курс</a>
				<?php $calc = get_page_by_path( 'kalkulyator' ); ?>
				<?php if ( $calc ) : ?>
					<a class="btn btn-ghost" href="<?php echo esc_url( (string) get_permalink( $calc ) ); ?>"><?php echo fr_icon( 'i-calc', 19 ); ?>Розрахувати свій курс</a>
				<?php endif; ?>
				<?php $banks = (array) ( get_field( 'pay_banks', 'option' ) ?: [] ); ?>
				<?php if ( $banks ) : ?>
					<div class="pay">
						<p class="pay-t"><?php echo esc_html( (string) ( get_field( 'pay_title', 'option' ) ?: 'Оплата частинами' ) ); ?></p>
						<div class="pay-row">
							<?php foreach ( $banks as $b ) : ?><span><?php echo esc_html( (string) $b['name'] ); ?></span><?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</aside>
		</div>
	</section>

	<?php $why = (array) ( get_field( 'why', $cid ) ?: [] ); ?>
	<?php if ( $why ) : ?>
		<section class="wrap" style="padding: 0 24px 48px">
			<h2 data-rev="up" style="margin-bottom: 20px"><span class="slash"><i></i><i></i></span>Чому <?php echo esc_html( $code ); ?></h2>
			<div class="why" data-rev-group="up">
				<?php foreach ( $why as $w ) : $wi = (int) ( $w['image'] ?? 0 ); ?>
					<div class="why-card">
						<div class="why-media<?php echo $wi ? '' : ' ph'; ?>">
							<?php echo $wi ? wp_get_attachment_image( $wi, 'fr-card', false, [ 'alt' => '', 'loading' => 'lazy' ] ) : '[ФОТО]'; ?>
						</div>
						<div class="why-body"><?php echo fr_icon( (string) ( $w['icon'] ?: 'i-target' ), 26 ); ?>
							<b><?php echo esc_html( (string) $w['title'] ); ?></b>
							<span><?php echo esc_html( (string) ( $w['text'] ?? '' ) ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	// Склад годин — той самий mix, із якого рахуються години й калькулятор
	$mix = [];
	foreach ( (array) get_field( 'mix', $cid ) as $row ) {
		if ( empty( $row['type'] ) ) { continue; }
		$mix[ $row['type'] ] = ( $mix[ $row['type'] ] ?? 0 ) + (int) ( $row['hours'] ?? 0 );
	}
	if ( count( $mix ) > 1 ) :
		?>
		<section class="wrap" style="padding: 0 24px 48px">
			<h2 data-rev="up" style="margin-bottom: 20px"><span class="slash"><i></i><i></i></span>Із чого складаються <?php echo esc_html( (string) $hours ); ?> <?php echo esc_html( _n( 'година', 'годин', $hours, 'freeride' ) ); ?></h2>
			<div class="mix" data-rev="up">
				<?php $n = 0; foreach ( $mix as $type => $h ) : $n++; $t = $byid[ $type ] ?? []; ?>
					<?php if ( $n > 1 ) : ?><span class="mix-op" aria-hidden="true">+</span><?php endif; ?>
					<div class="mix-part">
						<span class="pill pill-go"><?php echo esc_html( (string) $h ); ?> год</span>
						<?php echo fr_icon( (string) ( $t['icon'] ?? 'i-moto' ), 28 ); ?>
						<b><?php echo esc_html( (string) ( $t['label'] ?? $type ) ); ?></b>
						<span><?php echo esc_html( (string) ( $t['desc'] ?? '' ) ); ?></span>
					</div>
				<?php endforeach; ?>
				<?php if ( $mr = get_field( 'mix_result', $cid ) ) : ?>
					<span class="mix-op" aria-hidden="true">=</span>
					<div class="mix-res">
						<b>Результат<?php echo $doctxt ? ': категорії ' . esc_html( $doctxt ) : ''; ?></b>
						<span><?php echo esc_html( $mr ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	// Таблиця порівняння будується з усіх курсів: вести її руками на пʼятьох
	// сторінках означало б розійтися після першої ж зміни ціни.
	$all = fr_courses( true );
	if ( count( $all ) > 1 ) :
		$mixes = [];
		foreach ( $all as $c ) {
			$m = [];
			foreach ( (array) get_field( 'mix', $c->ID ) as $r ) {
				if ( ! empty( $r['type'] ) ) {
					$m[ $r['type'] ] = ( $m[ $r['type'] ] ?? 0 ) + (int) ( $r['hours'] ?? 0 );
				}
			}
			$mixes[ $c->ID ] = $m;
		}
		?>
		<section class="wrap" style="padding: 0 24px 48px">
			<h2 data-rev="up" style="margin-bottom: 20px"><span class="slash"><i></i><i></i></span><?php echo esc_html( $code ); ?> у порівнянні з іншими програмами</h2>
			<div class="cmp-wrap" tabindex="0" role="region" aria-label="Таблиця порівняння програм, прокручується вбік">
				<table class="cmp">
					<thead>
						<tr>
							<th scope="col">Що входить</th>
							<?php foreach ( $all as $c ) : $ch = fr_course_hours( $c->ID ); ?>
								<th scope="col"><b><?php echo esc_html( (string) get_field( 'code', $c->ID ) ); ?></b><span><?php echo esc_html( (string) $ch ); ?> год</span></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $byid as $tid => $t ) :
							$used = array_filter( $mixes, static fn( $m ) => ! empty( $m[ $tid ] ) );
							if ( ! $used ) { continue; }
							?>
							<tr>
								<th scope="row"><?php echo esc_html( (string) ( $t['label'] ?? $tid ) ); ?></th>
								<?php foreach ( $all as $c ) : $h = $mixes[ $c->ID ][ $tid ] ?? 0; ?>
									<td class="<?php echo $c->ID === $cid ? 'yes' : ( $h ? '' : 'no' ); ?>"><?php echo $h ? esc_html( $h . ' год' ) : '—'; ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
						<tr>
							<th scope="row">Категорії</th>
							<?php foreach ( $all as $c ) :
								$d   = (array) ( get_field( 'docs', $c->ID ) ?: [] );
								$txt = implode( ' та ', array_map( $doc_label, $d ) ); ?>
								<td class="<?php echo $c->ID === $cid ? 'yes' : ( $txt ? '' : 'no' ); ?>"><?php echo esc_html( $txt ?: '—' ); ?></td>
							<?php endforeach; ?>
						</tr>
						<tr>
							<th scope="row">Вартість</th>
							<?php foreach ( $all as $c ) : ?>
								<td class="sum"><?php echo esc_html( number_format_i18n( (int) get_field( 'price', $c->ID ) ) ); ?> грн</td>
							<?php endforeach; ?>
						</tr>
					</tbody>
				</table>
			</div>
		</section>
	<?php endif; ?>

	<?php
	// Відгуки саме про цей курс — соціальний доказ там, де ухвалюють рішення
	$crevs = fr_reviews( [ 'course' => $cid, 'kind' => 'text', 'limit' => 2 ] );
	if ( $crevs ) :
		?>
		<section class="wrap" style="padding: 0 24px 48px">
			<h2 data-rev="up" style="margin-bottom: 20px"><span class="slash"><i></i><i></i></span>Відгуки про <?php echo esc_html( $code ); ?></h2>
			<div class="revs" data-rev-group="up">
				<?php foreach ( $crevs as $r ) { get_template_part( 'parts/review-card', null, [ 'review' => $r ] ); } ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $res = (array) ( get_field( 'results', $cid ) ?: [] ); ?>
	<?php if ( $res ) : ?>
		<section class="wrap" style="padding: 0 24px 48px">
			<h2 data-rev="up" style="margin-bottom: 20px"><span class="slash"><i></i><i></i></span>Результат</h2>
			<div class="result" data-rev-group="up">
				<?php foreach ( $res as $r ) : ?>
					<div><?php echo fr_icon( (string) ( $r['icon'] ?: 'i-medal' ), 30 ); ?><b><?php echo esc_html( (string) $r['title'] ); ?></b></div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( get_the_content() ) : ?>
		<section class="wrap" style="padding: 0 24px 48px"><div class="entry"><?php the_content(); ?></div></section>
	<?php endif; ?>

	<section class="final" id="final">
		<div class="final-media ph">[ФОТО: мотоцикліст на серпантині, захід сонця]</div>
		<div class="final-veil"></div>
		<div class="wrap final-in">
			<h2>Готові почати?</h2>
			<?php if ( $ft = get_field( 'final_text', $cid ) ) : ?>
				<p><?php echo esc_html( $ft ); ?></p>
			<?php endif; ?>
			<?php get_template_part( 'parts/cta-trial' ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
