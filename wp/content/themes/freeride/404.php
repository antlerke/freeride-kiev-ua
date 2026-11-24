<?php
/** Сторінки немає. Замість глухого кута — дорога до курсів. */
declare( strict_types = 1 );
get_header();
?>
<section class="wrap" style="padding: 64px 24px">
	<h1><span class="slash"><i></i><i></i></span>Такої сторінки немає</h1>
	<p class="lede">Можливо, вона переїхала під час оновлення сайту.</p>
	<p style="margin-top: 22px">
		<a class="btn btn-go btn-xl" href="<?php echo esc_url( (string) get_post_type_archive_link( FR_COURSE ) ); ?>">
			<?php echo fr_icon( 'i-cal', 21 ); ?><span><b>Подивитися курси</b></span><?php echo fr_icon( 'i-arrow', 20 ); ?></a>
	</p>
</section>
<?php get_footer();
