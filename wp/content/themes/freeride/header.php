<?php declare( strict_types = 1 ); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php fr_sprite(); ?>
<a class="sr" href="#main">До основного вмісту</a>

<header class="bar">
  <div class="bar-in">
    <div class="brandblock">
      <?php fr_logo(); ?>
      <?php if ( $motto = get_bloginfo( 'description' ) ) : ?>
        <p class="motto"><?php echo esc_html( $motto ); ?></p>
      <?php endif; ?>
    </div>
    <div class="drawer" id="menu">
      <?php
      wp_nav_menu( [
        'theme_location' => 'primary',
        'container'      => 'nav',
        'container_class'=> 'nav',
        'items_wrap'     => '%3$s',
        'depth'          => 1,
        'fallback_cb'    => false,
        'walker'         => new FR_Nav_Walker(),
      ] );
      ?>
      <a class="btn btn-go hdr-cta" style="font-size: 15px; padding: 13px 20px" href="<?php echo esc_url( home_url( '/#final' ) ); ?>">
        <?php echo fr_icon( 'i-cal', 19 ); ?>Запис на урок</a>
    </div>
    <?php fr_lang_switcher(); ?>
    <button class="burger" type="button" aria-expanded="false" aria-controls="menu">
      <span class="sr">Меню</span>
      <span class="burger-box" aria-hidden="true"><i></i><i></i><i></i></span>
    </button>
  </div>
</header>

<main id="main">
