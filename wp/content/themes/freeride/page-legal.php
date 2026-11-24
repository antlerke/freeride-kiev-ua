<?php
/**
 * Template Name: Юридична сторінка
 *
 * Документ, який читають, а не розглядають: вузька колонка під довгий
 * текст і зміст збоку. Зміст будується з заголовків самого тексту —
 * редактор не мусить вести його руками й не забуде оновити.
 */

declare( strict_types = 1 );
get_header();

while ( have_posts() ) :
	the_post();
	$id      = get_the_ID();
	$content = apply_filters( 'the_content', get_the_content() );

	// Проставляємо id заголовкам і збираємо з них зміст
	$toc = [];
	$content = (string) preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/is',
		static function ( array $m ) use ( &$toc ): string {
			$text = trim( wp_strip_all_tags( $m[2] ) );
			$slug = 'r' . ( count( $toc ) + 1 );
			$toc[] = [ 'id' => $slug, 'text' => $text ];
			return '<h2 id="' . esc_attr( $slug ) . '"' . $m[1] . '>' . $m[2] . '</h2>';
		},
		$content
	);
	$show_toc = get_field( 'show_toc', $id ) && count( $toc ) > 2;
	$file     = get_field( 'file', $id );
	$updated  = get_field( 'updated', $id );
	?>
	<section class="wrap legal-head">
		<?php fr_breadcrumbs(); ?>
		<h1><span class="slash"><i></i><i></i></span><?php the_title(); ?></h1>
		<div class="legal-meta">
			<?php if ( $updated ) : ?>
				<?php // Українська локаль віддає місяць із великої — у середині речення це помилка
				$d = mb_strtolower( (string) wp_date( 'j F Y', strtotime( (string) $updated ) ), 'UTF-8' ); ?>
				<span><?php echo fr_icon( 'i-clock', 18 ); ?>Оновлено <?php echo esc_html( $d ); ?></span>
			<?php endif; ?>
			<?php if ( is_array( $file ) && ! empty( $file['url'] ) ) : ?>
				<a href="<?php echo esc_url( $file['url'] ); ?>" download><?php echo fr_icon( 'i-doc', 18 ); ?>Завантажити файлом</a>
			<?php endif; ?>
		</div>
	</section>

	<section class="wrap legal<?php echo $show_toc ? '' : ' is-solo'; ?>">
		<?php if ( $show_toc ) : ?>
			<nav class="legal-toc" aria-label="Зміст документа">
				<b>Зміст</b>
				<?php foreach ( $toc as $t ) : ?>
					<a href="#<?php echo esc_attr( $t['id'] ); ?>"><?php echo esc_html( $t['text'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<div class="legal-body entry"><?php echo $content; ?></div>
	</section>
	<?php
endwhile;

get_footer();
