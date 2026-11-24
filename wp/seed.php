<?php
/**
 * Первинне наповнення: курси, прайс, сторінки, меню.
 *
 * Запуск:  docker-compose exec -T cli wp --allow-root eval-file /var/www/html/seed.php
 * (зручніше через ./seed.sh)
 *
 * Ідемпотентний: шукає запис за слагом і оновлює його, а не плодить дублі.
 * Слаги курсів і сторінок затверджені замовником і звʼязані з картою 301
 * у deploy/redirects/ — міняти їх тут без правки карти не можна.
 *
 * Цифри тимчасові, з макетів. Замовник їх ще не підтвердив.
 */

// Без declare(strict_types): wp eval-file виконує файл через eval(),
// а там ця директива заборонена.

function fr_seed_post( string $type, string $slug, array $data ): int {
	$found = get_posts( [
		'post_type'   => $type,
		'name'        => $slug,
		'post_status' => [ 'publish', 'draft', 'pending' ],
		'numberposts' => 1,
	] );
	// WordPress 7 перевіряє шаблон під час оновлення: якщо файл теми
	// зник, оновити запис не вийде взагалі. Чистимо биту прив'язку.
	if ( $found ) {
		$tpl = (string) get_post_meta( $found[0]->ID, '_wp_page_template', true );
		if ( $tpl && 'default' !== $tpl && ! locate_template( $tpl ) ) {
			delete_post_meta( $found[0]->ID, '_wp_page_template' );
		}
	}
	$base = [
		'post_type'    => $type,
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_content' => '',
	];
	if ( $found ) {
		$base['ID'] = $found[0]->ID;
		$id = wp_update_post( array_merge( $base, $data ), true );
	} else {
		$id = wp_insert_post( array_merge( $base, $data ), true );
	}
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $slug . ': ' . $id->get_error_message() );
	}
	return (int) $id;
}

// ── Прайс ───────────────────────────────────────────────────────────
update_field( 'lesson_types', [
	[ 'id' => 'small',   'label' => 'Малокубатурний мотоцикл', 'icon' => 'i-moto' ],
	[ 'id' => 'scooter', 'label' => 'Скутер',                  'icon' => 'i-scooter' ],
	[ 'id' => 'big',     'label' => 'Великий мотоцикл',        'icon' => 'i-moto' ],
	[ 'id' => 'city',    'label' => 'Практика в місті',        'icon' => 'i-city' ],
], 'option' );

update_field( 'extras', [
	[ 'type' => 'big',     'label' => 'Великий мотоцикл',    'rate' => 2500, 'max' => 20 ],
	[ 'type' => 'city',    'label' => 'Виїзд у місто',       'rate' => 3000, 'max' => 20 ],
	[ 'type' => 'scooter', 'label' => 'Практика на скутері', 'rate' => 1500, 'max' => 20 ],
], 'option' );

update_field( 'documents', [
	[ 'id' => 'A1', 'label' => 'Категорія А1', 'price' => 4000,
	  'note' => 'Додаткова підготовка та документи на категорію А1.' ],
], 'option' );

update_field( 'custom_enabled', 1, 'option' );
update_field( 'custom_name',  'Лише окремі заняття', 'option' );
update_field( 'custom_badge', 'БЕЗ ПАКЕТА', 'option' );
update_field( 'custom_cta',   'Записатися на заняття', 'option' );
update_field( 'custom_note',  'Без пакета: платите тільки за ті заняття, які оберете в кроці 2.', 'option' );
update_field( 'compare_enabled', 1, 'option' );
update_field( 'archive_title', 'Програми навчання', 'option' );
update_field( 'archive_intro', 'Чотири рівні — від перших метрів на майданчику до повного контролю в реальному міському русі. Мотоцикл, екіпірування, інструктор і паливо входять у вартість кожної програми.', 'option' );

// ── Контакти ────────────────────────────────────────────────────────
update_field( 'phone',     '+38 (067) 600 88 00', 'option' );
update_field( 'phone_raw', '+380676008800', 'option' );
update_field( 'email',     'info@freeride.kiev.ua', 'option' );
update_field( 'address',   'Київ, район м. Лісова', 'option' );
update_field( 'footer_about', 'Мотошкола в Києві. Вчимо керувати мотоциклом впевнено, безпечно та з задоволенням — від перших метрів на майданчику до реального міського руху.', 'option' );
update_field( 'messengers', [
	[ 'kind' => 'phone', 'url' => 'tel:+380676008800' ],
	[ 'kind' => 'viber', 'url' => 'viber://chat?number=%2B380676008800' ],
	[ 'kind' => 'wa',    'url' => 'https://wa.me/380676008800' ],
	[ 'kind' => 'tg',    'url' => 'https://t.me/+380676008800' ],
	[ 'kind' => 'mail',  'url' => 'mailto:info@freeride.kiev.ua' ],
], 'option' );

// ── Курси ───────────────────────────────────────────────────────────
// Слаги — зі старого сайту, затверджені замовником.
$courses = [
	[ 'slug' => 'gorod', 'title' => 'Початківець', 'order' => 10, 'calc' => 1,
	  'code' => 'STARTER', 'legacy' => 'EXAM', 'icon' => 'i-cone', 'price' => 13500,
	  'summary' => 'Перші навички, базовий контроль, впевненість на майданчику.',
	  'mix' => [ 'small' => 5 ], 'docs' => [ 'A' ],
	  'includes' => [ '5 індивідуальних занять по 60 хвилин', 'Екзаменаційні вправи та корекція помилок',
	                  'Підготовка до категорії А', 'Документи категорії А — включені' ] ],

	[ 'slug' => 'intensiv', 'title' => 'Інтенсив', 'order' => 20, 'calc' => 1,
	  'code' => 'CONTROL', 'legacy' => 'INTENSIVE', 'icon' => 'i-gauge', 'price' => 23000,
	  'summary' => 'Упевнене керування й контроль мотоцикла на майданчику.',
	  'mix' => [ 'small' => 10 ], 'docs' => [ 'A' ],
	  'includes' => [ '10 індивідуальних занять по 60 хвилин', 'Повний набір вправ на контроль',
	                  'Екстрене гальмування та маневрування', 'Документи категорії А — включені' ] ],

	[ 'slug' => 'bazovyj', 'title' => 'Базовий', 'order' => 30, 'calc' => 1,
	  'code' => 'STREET', 'legacy' => 'BASIC', 'icon' => 'i-road', 'price' => 32200,
	  'summary' => 'Майданчик плюс перший виїзд у реальний міський рух.',
	  'mix' => [ 'small' => 13, 'big' => 1, 'city' => 1 ], 'docs' => [ 'A' ],
	  'includes' => [ '13 занять на малокубатурному мотоциклі', '1 заняття на великому мотоциклі',
	                  '1 виїзд у місто з інструктором', 'Документи категорії А — включені' ] ],

	[ 'slug' => 'polnyj-kurs', 'title' => 'Повний курс', 'order' => 40, 'calc' => 1,
	  'code' => 'ADVANCED', 'legacy' => '', 'icon' => 'i-medal', 'price' => 47200,
	  'summary' => 'Повна програма: майданчик, скутер, великий мотоцикл і місто.',
	  'mix' => [ 'small' => 13, 'scooter' => 2, 'big' => 1, 'city' => 4 ], 'docs' => [ 'A', 'A1' ],
	  'includes' => [ '13 занять на малокубатурному мотоциклі', '2 заняття на скутері',
	                  '1 заняття на великому мотоциклі', '4 виїзди в місто',
	                  'Документи категорій А та А1 — включені' ] ],

	[ 'slug' => 'probnyj', 'title' => 'Пробний урок', 'order' => 5, 'calc' => 0,
	  'code' => 'TRIAL', 'legacy' => '', 'icon' => 'i-target', 'price' => 1500,
	  'summary' => 'Одна година з інструктором, щоб зрозуміти, чи ваше це взагалі.',
	  'mix' => [ 'small' => 1 ], 'docs' => [],
	  'includes' => [ '60 хвилин з інструктором', 'Мотоцикл і екіпірування — наші',
	                  'Оцінка навичок і порада щодо програми' ] ],
];

$ids = [];
foreach ( $courses as $c ) {
	$id = fr_seed_post( 'fr_course', $c['slug'], [
		'post_title'   => $c['title'],
		'menu_order'   => $c['order'],
		'post_excerpt' => $c['summary'],
	] );
	$ids[ $c['slug'] ] = $id;

	update_field( 'code',    $c['code'],    $id );
	update_field( 'legacy',  $c['legacy'],  $id );
	update_field( 'icon',    $c['icon'],    $id );
	update_field( 'price',   $c['price'],   $id );
	update_field( 'summary', $c['summary'], $id );
	update_field( 'docs',    $c['docs'],    $id );
	update_field( 'in_calculator', $c['calc'], $id );
	update_field( 'mix', array_map(
		static fn( $t, $h ) => [ 'type' => $t, 'hours' => $h ],
		array_keys( $c['mix'] ), array_values( $c['mix'] )
	), $id );
	update_field( 'includes', array_map(
		static fn( $t ) => [ 'text' => $t ], $c['includes']
	), $id );
	WP_CLI::log( sprintf( '  курс %-12s → /motocourses/%s/', $c['code'], $c['slug'] ) );
}

update_field( 'compare_target', $ids['polnyj-kurs'], 'option' );

// ── Сторінки ────────────────────────────────────────────────────────
// Слаги звʼязані з картою 301. Ті, що «нове», старого відповідника не мають.
$pages = [
	[ 'home',                      'Головна',                     '' ],
	[ 'pro-nas',                   'Про нас',                     'page-about.php' ],
	[ 'komanda',                   'Команда',                     'page-team.php' ],
	[ 'otzyvy',                    'Відгуки',                     'page-reviews.php' ],
	[ 'galereya',                  'Галерея',                     'page-gallery.php' ],
	[ 'kalkulyator',               'Калькулятор курсу',           'page-calculator.php' ],
	[ 'podbor-motoczikla',         'Підбір мотоцикла',            '' ],
	[ 'sertifikat',                'Подарунковий сертифікат',     '' ],
	[ 'kontakty',                  'Контакти',                    'page-contacts.php' ],
	[ 'polityka-konfidencijnosti', 'Політика конфіденційності',   '' ],
];

$page_ids = [];
foreach ( $pages as [ $slug, $title, $tpl ] ) {
	$id = fr_seed_post( 'page', $slug, [ 'post_title' => $title ] );
	$page_ids[ $slug ] = $id;
	if ( $tpl && locate_template( $tpl ) ) {
		update_post_meta( $id, '_wp_page_template', $tpl );
	} elseif ( $tpl ) {
		WP_CLI::warning( 'шаблону ' . $tpl . ' у темі немає — сторінка піде на page.php' );
	}
	WP_CLI::log( sprintf( '  сторінка %-28s → /%s/', $title, $slug ) );
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['home'] );
update_option( 'page_for_posts', 0 );
update_option( 'wp_page_for_privacy_policy', $page_ids['polityka-konfidencijnosti'] );

// ── Тексти сторінки контактів ───────────────────────────────────────
$kc = $page_ids['kontakty'];
wp_update_post( [
	'ID'           => $kc,
	'post_title'   => 'Контакти FreeRide',
	'post_excerpt' => 'Звʼяжіться з нами, щоб отримати консультацію, обрати курс або записатися на навчання.',
] );
update_field( 'loc_eyebrow', 'Наша локація', $kc );
update_field( 'loc_title',   'Навчальний майданчик FreeRide', $kc );
update_field( 'loc_metro',   'Лісова', $kc );
update_field( 'loc_note',    'точну адресу та схему проїзду надсилаємо після запису', $kc );
update_field( 'form_title',  'Звʼяжіться з нами', $kc );
update_field( 'form_button', 'Надіслати запит', $kc );
update_field( 'form_success','Дякуємо! Передзвонимо найближчим часом.', $kc );
update_field( 'form_topics', array_map(
	static fn( $t ) => [ 'topic' => $t ],
	[ 'Обрати курс', 'Пробний урок', 'Подарунковий сертифікат', 'Підбір мотоцикла', 'Інше питання' ]
), $kc );
update_field( 'book_title',  'Найпростіший спосіб — забронювати час онлайн', $kc );
update_field( 'book_text',   'Оберіть зручну дату, курс і одразу оплатіть заняття в системі Appointer.', $kc );
update_field( 'book_button', 'Записатися на заняття', $kc );
update_field( 'book_url',    'https://get.appointer.ua/', $kc );
update_field( 'benefits', [
	[ 'icon' => 'i-pin',    'title' => 'Зручна локація', 'text' => 'біля м. Лісова' ],
	[ 'icon' => 'i-moto',   'title' => 'Парковка',       'text' => 'для авто і мотоциклів' ],
	[ 'icon' => 'i-helmet', 'title' => 'Сучасна інфраструктура', 'text' => 'та безпечний майданчик' ],
	[ 'icon' => 'i-shield', 'title' => 'Комфортні умови','text' => 'навчання' ],
], $kc );

// Графік роботи — в загальні контакти, бо потрібен не лише тут
update_field( 'hours', 'Щодня, 08:00 – 20:00', 'option' );

// ── Соцмережі ───────────────────────────────────────────────────────
// Посилання взяті з живого старого сайту, не вигадані.
update_field( 'socials', [
	[ 'kind' => 'ig', 'url' => 'https://www.instagram.com/freeridekievua/' ],
	[ 'kind' => 'fb', 'url' => 'https://www.facebook.com/freeride.kiev.ua/' ],
	[ 'kind' => 'yt', 'url' => 'https://www.youtube.com/channel/UCT4MeFFmHkUqWR6xDjjfBhg' ],
], 'option' );

// ── Тексти сторінки галереї ─────────────────────────────────────────
$gl = $page_ids['galereya'];
wp_update_post( [
	'ID'           => $gl,
	'post_title'   => 'Медіа FreeRide',
	'post_excerpt' => 'Навчання, події, люди та мотоцикли — життя нашої школи в фото і відео.',
] );
update_field( 'eyebrow', 'Фото / Відео / Життя школи', $gl );
update_field( 'featured', [
	[ 'kind' => 'video', 'tag' => 'Тренування', 'title' => 'Тренування в місті', 'length' => '4:36' ],
	[ 'kind' => 'photo', 'tag' => 'Тренування', 'title' => '' ],
	[ 'kind' => 'photo', 'tag' => 'Спільнота',  'title' => '' ],
], $gl );
update_field( 'albums', [
	[ 'title' => 'Навчання',  'tag' => 'Тренування', 'count' => 320 ],
	[ 'title' => 'Подорожі',  'tag' => 'Спільнота',  'count' => 148 ],
	[ 'title' => 'Події',     'tag' => 'Події',      'count' => 97 ],
	[ 'title' => 'За кадром', 'tag' => 'Спільнота',  'count' => 73 ],
	[ 'title' => 'Мотоцикли', 'tag' => 'Тренування', 'count' => 102 ],
], $gl );
update_field( 'cta_title',  'Більше контенту в наших соцмережах', $gl );
update_field( 'cta_button', 'Підписатися на FreeRide', $gl );
update_field( 'cta_url',    'https://www.instagram.com/freeridekievua/', $gl );

// ── Меню ────────────────────────────────────────────────────────────
foreach ( [ 'primary' => 'Головне меню', 'footer' => 'Підвал' ] as $loc => $name ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $name );
	} else {
		$menu_id = (int) $menu->term_id;
		foreach ( wp_get_nav_menu_items( $menu_id ) ?: [] as $it ) {
			wp_delete_post( $it->ID, true );
		}
	}

	$items = 'primary' === $loc
		? [
			[ 'Навчання',    get_post_type_archive_link( 'fr_course' ) ],
			[ 'Калькулятор', get_permalink( $page_ids['kalkulyator'] ) ],
			[ 'Відгуки',     get_permalink( $page_ids['otzyvy'] ) ],
			[ 'Про нас',     get_permalink( $page_ids['pro-nas'] ) ],
			[ 'Галерея',     get_permalink( $page_ids['galereya'] ) ],
			[ 'Контакти',    get_permalink( $page_ids['kontakty'] ) ],
		]
		: [
			[ 'Усі курси та ціни',       get_post_type_archive_link( 'fr_course' ) ],
			[ 'Калькулятор курсу',       get_permalink( $page_ids['kalkulyator'] ) ],
			[ 'Відгуки',                 get_permalink( $page_ids['otzyvy'] ) ],
			[ 'Команда',                 get_permalink( $page_ids['komanda'] ) ],
			[ 'Подарунковий сертифікат', get_permalink( $page_ids['sertifikat'] ) ],
			[ 'Підбір мотоцикла',        get_permalink( $page_ids['podbor-motoczikla'] ) ],
		];

	foreach ( $items as $i => [ $title, $url ] ) {
		wp_update_nav_menu_item( $menu_id, 0, [
			'menu-item-title'    => $title,
			'menu-item-url'      => $url,
			'menu-item-status'   => 'publish',
			'menu-item-position' => $i + 1,
		] );
	}
	$locations = get_theme_mod( 'nav_menu_locations', [] );
	$locations[ $loc ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

delete_transient( 'fr_pricing_payload' );
flush_rewrite_rules();
WP_CLI::success( 'Наповнено.' );
