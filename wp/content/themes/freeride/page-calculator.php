<?php
/**
 * Template Name: Калькулятор курсу
 *
 * Розмітка без жодного числа: склад курсів, ставки й назви приходять
 * із адмінки через /wp-json/freeride/v1/pricing тією самою схемою,
 * якою раніше віддавався статичний pricing.json. Через це перехід
 * на адмінку не зачепив жодного рядка в calc.js.
 */

declare( strict_types = 1 );
get_header();
?>
<section class="hero hero-page">
	<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
		<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО: мотоцикл на майданчику між конусами]'; ?>
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

<section class="wrap" style="padding: 0 24px 48px">
  <div class="calc" id="calc">

    <p class="calc-err" id="calc-error" hidden></p>

    <!-- ЛІВОРУЧ: ВИБІР -->
    <div class="calc-col">
      <div class="step">
        <div class="step-head"><span class="num">1</span><h2>Оберіть курс</h2><s>або лише окремі заняття</s></div>
        <fieldset style="border:0; margin:0; padding:0">
          <legend class="sr">Курс або окремі заняття</legend>
          <div class="courses" id="courses"></div>
        </fieldset>
      </div>

      <div class="step">
        <div class="step-head"><span class="num">2</span><h2 id="step2-head">Додайте додаткові заняття</h2><s id="step2-note">за потреби</s></div>
        <div id="extras"></div>
      </div>

      <div class="step" id="step-docs">
        <div class="step-head"><span class="num">3</span><h2>Додаткові документи</h2></div>
        <div id="docs"></div>
      </div>

      <p class="hint" id="mix-hint"></p>
    </div>

    <!-- ПРАВОРУЧ: ПІДСУМОК -->
    <div class="calc-col" aria-live="polite">
      <div class="sum-head">
        <h2>Ваша програма</h2>
        <span class="badge" id="sum-badge"></span>
      </div>
      <div id="mix"></div>

      <h3 class="sum-sub">Вартість програми</h3>
      <p class="cost-cap" id="cost-base-cap">Базовий пакет</p>
      <div id="cost-base"></div>
      <div id="cost-add-wrap" hidden>
        <p class="cost-cap is-add" id="cost-add-cap">Додано вручну</p>
        <div id="cost-add"></div>
      </div>
      <div class="total">
        <span>Разом</span>
        <b id="total"></b>
        <small id="total-split" hidden></small>
      </div>
      <p class="upsell" id="upsell" hidden></p>

      <div class="vs" id="vs" hidden>
        <h3 class="sum-sub" style="margin-bottom:6px">Порівняння пакетів</h3>
        <p class="vs-rule">Скільки коштувало б зібрати склад повного курсу з меншого пакета
          плюс окремі заняття. Розрахунок не залежить від того, що ви додали вручну вище.</p>
        <div class="vs-grid">
          <div class="vs-card">
            <b id="vs-a-name"></b>
            <span id="vs-a-note"></span>
            <strong id="vs-a-sum"></strong>
          </div>
          <span class="vs-mid" aria-hidden="true">VS</span>
          <div class="vs-card win">
            <b id="vs-b-name"></b>
            <span>повна програма</span>
            <strong id="vs-b-sum"></strong>
          </div>
        </div>
        <p class="save" id="save" hidden>
          <svg width="26" height="26" viewBox="0 0 28 28" aria-hidden="true"><use href="#i-piggy"/></svg>
          Економія: <span id="save-sum"></span>
        </p>
        <p class="save" id="vs-equal" hidden style="color: var(--ink-2); font-size:15px">
          Різниці у вартості немає
        </p>
      </div>

      <a class="btn btn-go btn-xl calc-cta" id="calc-cta" href="#final">
        <svg width="21" height="21" viewBox="0 0 28 28"><use href="#i-cal"/></svg>
        <span><b id="cta-label">Записатися на курс</b></span>
        <svg width="20" height="20" viewBox="0 0 20 20"><use href="#i-arrow"/></svg></a>
    </div>
  </div>

  <p class="calc-err" style="margin-top:20px" id="nojs">
    Для розрахунку потрібен JavaScript. Повний прайс курсів і окремих занять —
    на <a href="<?php echo esc_url( (string) get_post_type_archive_link( FR_COURSE ) ); ?>">сторінці курсів</a>.
  </p>
</section>

<section class="wrap" style="padding: 0 24px 48px">
  <div class="bene" data-rev-group="up">
    <div><svg width="28" height="28" viewBox="0 0 28 28"><use href="#i-target"/></svg><b>Прозорий<br>розрахунок</b></div>
    <div><svg width="28" height="28" viewBox="0 0 28 28"><use href="#i-layers"/></svg><b>Реальні пакети<br>та економія</b></div>
    <div><svg width="28" height="28" viewBox="0 0 28 28"><use href="#i-bolt"/></svg><b>Можливість<br>додати практику</b></div>
    <div><svg width="28" height="28" viewBox="0 0 28 28"><use href="#i-cal"/></svg><b>Оплата<br>частинами</b></div>
    <div><svg width="28" height="28" viewBox="0 0 28 28"><use href="#i-headset"/></svg><b>Допомога у виборі<br>від інструктора</b></div>
  </div>
</section>
<section class="final" id="final">
	<div class="final-media ph">[ФОТО: мотоцикліст на серпантині, захід сонця]</div>
	<div class="final-veil"></div>
	<div class="wrap final-in">
		<h2>Не впевнені, який варіант ваш?</h2>
		<p>Запишіться на пробний урок — інструктор оцінить навички та підкаже програму.</p>
		<?php
		$trial = get_posts( [ 'post_type' => FR_COURSE, 'name' => 'probnyj', 'numberposts' => 1 ] );
		if ( $trial ) :
			$price = (int) get_field( 'price', $trial[0]->ID );
			?>
			<a class="btn btn-go btn-xl" href="<?php echo esc_url( get_permalink( $trial[0] ) ); ?>">
				<?php echo fr_icon( 'i-cal', 21 ); ?>
				<span><b>Пробний урок</b><s><?php echo esc_html( number_format_i18n( $price ) ); ?> грн/год</s></span>
				<?php echo fr_icon( 'i-arrow', 20 ); ?></a>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
