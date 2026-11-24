<?php
/**
 * Кнопка «Пробний урок». Ціна й назва — з курсу, не з розмітки:
 * кнопка стоїть на трьох сторінках, і три захардкоджені ціни
 * розійшлися б при першій же зміні прайсу.
 *
 * @var array $args  ['size' => 'xl'|'', 'class' => '']
 */
declare( strict_types = 1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }

$trial = get_posts( [ 'post_type' => FR_COURSE, 'name' => 'probnyj', 'numberposts' => 1 ] );
if ( ! $trial ) {
	return;
}
$price = (int) get_field( 'price', $trial[0]->ID );
$class = 'btn btn-go ' . ( $args['size'] ?? 'btn-xl' ) . ' ' . ( $args['class'] ?? '' );
?>
<a class="<?php echo esc_attr( trim( $class ) ); ?>" href="<?php echo esc_url( get_permalink( $trial[0] ) ); ?>">
	<?php echo fr_icon( 'i-cal', 21 ); ?>
	<span><b><?php echo esc_html( get_the_title( $trial[0] ) ); ?></b>
		<?php if ( $price ) : ?><s><?php echo esc_html( number_format_i18n( $price ) ); ?> грн/год</s><?php endif; ?>
	</span>
	<?php echo fr_icon( 'i-arrow', 20 ); ?>
</a>
