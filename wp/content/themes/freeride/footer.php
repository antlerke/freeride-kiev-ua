<?php declare( strict_types = 1 ); $c = fr_contacts(); $labels = fr_channel_labels(); ?>
</main>

<?php fr_contact_widget(); ?>

<footer class="slab sfoot">
  <div class="wrap sfoot-in">
    <div class="sfoot-brand">
      <?php fr_logo(); ?>
      <?php if ( $about = get_field( 'footer_about', 'option' ) ) : ?>
        <p><?php echo esc_html( $about ); ?></p>
      <?php endif; ?>
      <?php if ( $motto = get_bloginfo( 'description' ) ) : ?>
        <p class="sfoot-motto"><?php echo esc_html( $motto ); ?></p>
      <?php endif; ?>
    </div>

    <?php if ( has_nav_menu( 'footer' ) ) : ?>
      <nav class="sfoot-col" aria-label="Навчання">
        <h3>Навчання</h3>
        <?php wp_nav_menu( [
          'theme_location' => 'footer',
          'container'      => false,
          'items_wrap'     => '%3$s',
          'depth'          => 1,
          'fallback_cb'    => false,
          'walker'         => new FR_Plain_Walker(),
        ] ); ?>
      </nav>
    <?php endif; ?>

    <div class="sfoot-col">
      <h3>Контакти</h3>
      <?php if ( $c['phone'] ) : ?>
        <a class="sfoot-tel" href="tel:<?php echo esc_attr( $c['phone_raw'] ?: $c['phone'] ); ?>"><?php echo esc_html( $c['phone'] ); ?></a>
      <?php endif; ?>
      <?php if ( $c['email'] ) : ?>
        <a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a>
      <?php endif; ?>
      <?php if ( $c['address'] ) : ?><span><?php echo esc_html( $c['address'] ); ?></span><?php endif; ?>
      <?php if ( $c['hours'] ) : ?><span><?php echo esc_html( $c['hours'] ); ?></span><?php endif; ?>
    </div>

    <?php if ( $c['socials'] ) : ?>
      <div class="sfoot-col">
        <h3>Ми в мережах</h3>
        <div class="sfoot-soc">
          <?php foreach ( $c['socials'] as $s ) :
            $kind = (string) ( $s['kind'] ?? '' );
            if ( empty( $s['url'] ) || ! isset( $labels[ $kind ] ) ) { continue; }
            [ $icon, $label ] = $labels[ $kind ]; ?>
            <a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo fr_icon( $icon, 21 ); ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="wrap sfoot-bottom">
    <span>© <?php echo esc_html( (string) wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
    <?php
    $privacy = get_option( 'wp_page_for_privacy_policy' );
    if ( $privacy ) :
      ?><a href="<?php echo esc_url( (string) get_permalink( (int) $privacy ) ); ?>">Політика конфіденційності</a><?php
    endif;
    ?>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
