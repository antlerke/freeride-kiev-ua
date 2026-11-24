<?php
/**
 * Опис полів.
 *
 * Поля оголошені кодом, а не накликані в адмінці. Це навмисно:
 * опис лежить у git, їде на бойовий сервер разом із релізом і не
 * потребує переносу бази. Редактор у адмінці бачить звичайні поля
 * й нічого про це не знає, зате випадково зламати схему не може.
 *
 * Плагін — Secure Custom Fields (форк ACF від Automattic, безкоштовний).
 * Функції лишилися з префіксом acf_, тож код сумісний і з ACF Pro,
 * якщо колись знадобиться перейти.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Іконки зі спрайта теми. Ключ — id у <defs>, значення — підпис у списку. */
function fr_icon_choices(): array {
	return [
		'i-cone'    => 'Конус (майданчик)',
		'i-gauge'   => 'Спідометр (контроль)',
		'i-road'    => 'Дорога (місто)',
		'i-medal'   => 'Медаль (повний курс)',
		'i-moto'    => 'Мотоцикл',
		'i-scooter' => 'Скутер',
		'i-city'    => 'Місто',
		'i-target'  => 'Мішень',
		'i-layers'  => 'Шари',
		'i-bolt'    => 'Блискавка',
		'i-shield'  => 'Щит',
	];
}

add_action( 'acf/init', static function (): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// ── Сторінки налаштувань ────────────────────────────────────────
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( [
			'page_title' => 'Ціни та калькулятор',
			'menu_title' => 'Ціни',
			'menu_slug'  => 'fr-pricing',
			'parent_slug'=> 'edit.php?post_type=' . FR_COURSE,
			'capability' => 'edit_pages',
			'autoload'   => true,
		] );
		acf_add_options_page( [
			'page_title' => 'Контакти та реквізити',
			'menu_title' => 'Контакти сайту',
			'menu_slug'  => 'fr-contacts',
			'icon_url'   => 'dashicons-phone',
			'position'   => 22,
			'capability' => 'edit_pages',
			'autoload'   => true,
		] );
	}

	// ── Курс ────────────────────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_course',
		'title'    => 'Дані курсу',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => FR_COURSE ] ] ],
		'position' => 'normal',
		'style'    => 'default',
		'fields'   => [
			[
				'key' => 'field_fr_code', 'name' => 'code', 'label' => 'Код курсу',
				'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 25 ],
				'instructions' => 'STARTER, CONTROL, STREET, ADVANCED. Показується великими літерами на картках.',
			],
			[
				'key' => 'field_fr_legacy', 'name' => 'legacy', 'label' => 'Стара назва',
				'type' => 'text', 'wrapper' => [ 'width' => 25 ],
				'instructions' => 'EXAM, INTENSIVE, BASIC. Дрібним під кодом, щоб давні учні впізнали курс. Порожньо — не показується.',
			],
			[
				'key' => 'field_fr_icon', 'name' => 'icon', 'label' => 'Іконка',
				'type' => 'select', 'choices' => fr_icon_choices(),
				'default_value' => 'i-cone', 'wrapper' => [ 'width' => 25 ],
			],
			[
				'key' => 'field_fr_price', 'name' => 'price', 'label' => 'Ціна, грн',
				'type' => 'number', 'required' => 1, 'min' => 0, 'step' => 100,
				'wrapper' => [ 'width' => 25 ],
				'instructions' => 'Ціна всього пакета. Кількість годин сайт рахує сам зі складу нижче.',
			],
			[
				'key' => 'field_fr_summary', 'name' => 'summary', 'label' => 'Короткий опис',
				'type' => 'textarea', 'rows' => 2, 'new_lines' => '',
				'instructions' => 'Один рядок під назвою на картці курсу. До ~90 символів.',
			],
			[
				'key' => 'field_fr_mix', 'name' => 'mix', 'label' => 'Склад годин',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати тип занять',
				'instructions' => 'З цього сайт рахує загальну кількість годин і будує калькулятор. Типи занять задаються в «Курси → Ціни».',
				'sub_fields' => [
					[
						'key' => 'field_fr_mix_type', 'name' => 'type', 'label' => 'Тип занять',
						'type' => 'select', 'choices' => [], 'required' => 1, 'wrapper' => [ 'width' => 60 ],
					],
					[
						'key' => 'field_fr_mix_hours', 'name' => 'hours', 'label' => 'Годин',
						'type' => 'number', 'min' => 0, 'required' => 1, 'wrapper' => [ 'width' => 40 ],
					],
				],
			],
			[
				'key' => 'field_fr_docs', 'name' => 'docs', 'label' => 'Категорії в ціні',
				'type' => 'checkbox', 'choices' => [ 'A' => 'А', 'A1' => 'А1' ],
				'default_value' => [ 'A' ], 'layout' => 'horizontal',
				'instructions' => 'Які категорії вже входять у вартість курсу. Калькулятор не продасть їх удруге.',
			],
			[
				'key' => 'field_fr_includes', 'name' => 'includes', 'label' => 'Що входить',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати пункт',
				'sub_fields' => [
					[ 'key' => 'field_fr_inc_text', 'name' => 'text', 'label' => 'Пункт', 'type' => 'text', 'required' => 1 ],
				],
			],
			[
				'key' => 'field_fr_for', 'name' => 'for_whom', 'label' => 'Для кого',
				'type' => 'textarea', 'rows' => 3,
			],
			[
				'key' => 'field_fr_in_calc', 'name' => 'in_calculator', 'label' => 'Показувати в калькуляторі',
				'type' => 'true_false', 'default_value' => 1, 'ui' => 1,
				'instructions' => 'Пробний урок, наприклад, у калькуляторі не потрібен.',
			],
		],
	] );

	// ── Ціни та калькулятор ─────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_pricing',
		'title'    => 'Прайс і калькулятор',
		'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fr-pricing' ] ] ],
		'fields'   => [
			[
				'key' => 'field_fr_tab_cat', 'label' => 'Каталог курсів', 'type' => 'tab',
			],
			[
				'key' => 'field_fr_arch_title', 'name' => 'archive_title', 'label' => 'Заголовок каталогу',
				'type' => 'text', 'default_value' => 'Програми навчання',
				'instructions' => 'Великий заголовок на сторінці /motocourses/.',
			],
			[
				'key' => 'field_fr_arch_intro', 'name' => 'archive_intro', 'label' => 'Вступний абзац каталогу',
				'type' => 'textarea', 'rows' => 3, 'new_lines' => '',
			],
			[
				'key' => 'field_fr_tab_types', 'label' => 'Типи занять', 'type' => 'tab',
			],
			[
				'key' => 'field_fr_types', 'name' => 'lesson_types', 'label' => 'Типи занять',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати тип',
				'instructions' => 'Словник, з якого складаються курси. Ідентифікатор міняти не можна — на нього посилаються курси.',
				'sub_fields' => [
					[ 'key' => 'field_fr_t_id',   'name' => 'id',    'label' => 'Ідентифікатор', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 22 ] ],
					[ 'key' => 'field_fr_t_lbl',  'name' => 'label', 'label' => 'Назва',         'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 48 ] ],
					[ 'key' => 'field_fr_t_icon', 'name' => 'icon',  'label' => 'Іконка',        'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 30 ] ],
				],
			],
			[
				'key' => 'field_fr_tab_extras', 'label' => 'Окремі заняття', 'type' => 'tab',
			],
			[
				'key' => 'field_fr_extras', 'name' => 'extras', 'label' => 'Заняття поштучно',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати позицію',
				'instructions' => 'Те, що людина може докупити годинами в калькуляторі.',
				'sub_fields' => [
					[ 'key' => 'field_fr_x_type', 'name' => 'type',  'label' => 'Тип занять', 'type' => 'select', 'choices' => [], 'required' => 1, 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_x_lbl',  'name' => 'label', 'label' => 'Назва',      'type' => 'text',   'required' => 1, 'wrapper' => [ 'width' => 36 ] ],
					[ 'key' => 'field_fr_x_rate', 'name' => 'rate',  'label' => 'Грн/год',    'type' => 'number', 'required' => 1, 'min' => 0, 'wrapper' => [ 'width' => 17 ] ],
					[ 'key' => 'field_fr_x_max',  'name' => 'max',   'label' => 'Макс. годин','type' => 'number', 'default_value' => 20, 'min' => 1, 'wrapper' => [ 'width' => 17 ] ],
				],
			],
			[
				'key' => 'field_fr_docs_list', 'name' => 'documents', 'label' => 'Документи поштучно',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати документ',
				'sub_fields' => [
					[ 'key' => 'field_fr_d_id',    'name' => 'id',    'label' => 'Код',     'type' => 'text',   'required' => 1, 'wrapper' => [ 'width' => 15 ] ],
					[ 'key' => 'field_fr_d_lbl',   'name' => 'label', 'label' => 'Назва',   'type' => 'text',   'required' => 1, 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_d_price', 'name' => 'price', 'label' => 'Грн',     'type' => 'number', 'required' => 1, 'min' => 0, 'wrapper' => [ 'width' => 15 ] ],
					[ 'key' => 'field_fr_d_note',  'name' => 'note',  'label' => 'Пояснення','type' => 'text',  'wrapper' => [ 'width' => 40 ] ],
				],
			],
			[
				'key' => 'field_fr_tab_custom', 'label' => 'Без пакета', 'type' => 'tab',
			],
			[
				'key' => 'field_fr_c_on', 'name' => 'custom_enabled', 'label' => 'Пропонувати збірку без пакета',
				'type' => 'true_false', 'default_value' => 1, 'ui' => 1,
				'instructions' => 'Пʼятий варіант у калькуляторі: людина платить тільки за окремі заняття.',
			],
			[ 'key' => 'field_fr_c_name',  'name' => 'custom_name',  'label' => 'Назва',   'type' => 'text', 'default_value' => 'Лише окремі заняття', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_fr_c_badge', 'name' => 'custom_badge', 'label' => 'Плашка',  'type' => 'text', 'default_value' => 'БЕЗ ПАКЕТА', 'wrapper' => [ 'width' => 25 ] ],
			[ 'key' => 'field_fr_c_cta',   'name' => 'custom_cta',   'label' => 'Кнопка',  'type' => 'text', 'default_value' => 'Записатися на заняття', 'wrapper' => [ 'width' => 25 ] ],
			[ 'key' => 'field_fr_c_note',  'name' => 'custom_note',  'label' => 'Пояснення', 'type' => 'text', 'default_value' => 'Без пакета: платите тільки за ті заняття, які оберете в кроці 2.' ],
			[
				'key' => 'field_fr_tab_cmp', 'label' => 'Порівняння', 'type' => 'tab',
			],
			[
				'key' => 'field_fr_cmp_on', 'name' => 'compare_enabled', 'label' => 'Показувати порівняння пакетів',
				'type' => 'true_false', 'default_value' => 1, 'ui' => 1,
			],
			[
				'key' => 'field_fr_cmp_target', 'name' => 'compare_target', 'label' => 'З чим порівнювати',
				'type' => 'post_object', 'post_type' => [ FR_COURSE ], 'return_format' => 'id',
				'instructions' => 'Повний курс, до складу якого добудовується менший пакет.',
			],
		],
	] );

	// ── Контакти ────────────────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_contacts',
		'title'    => 'Контакти',
		'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fr-contacts' ] ] ],
		'fields'   => [
			[ 'key' => 'field_fr_phone',  'name' => 'phone',  'label' => 'Телефон',        'type' => 'text', 'wrapper' => [ 'width' => 34 ], 'instructions' => 'Як показувати: +38 (067) 600 88 00' ],
			[ 'key' => 'field_fr_tel',    'name' => 'phone_raw','label' => 'Телефон для tel:', 'type' => 'text', 'wrapper' => [ 'width' => 33 ], 'instructions' => 'Самі цифри з плюсом: +380676008800' ],
			[ 'key' => 'field_fr_email',  'name' => 'email',  'label' => 'Пошта',          'type' => 'email','wrapper' => [ 'width' => 33 ] ],
			[ 'key' => 'field_fr_addr',   'name' => 'address','label' => 'Адреса',         'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_fr_hours',  'name' => 'hours',  'label' => 'Години роботи',  'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_fr_map',    'name' => 'map_url','label' => 'Посилання на карту', 'type' => 'url' ],
			[
				'key' => 'field_fr_foot_about', 'name' => 'footer_about', 'label' => 'Абзац про школу в підвалі',
				'type' => 'textarea', 'rows' => 3, 'new_lines' => '',
				'instructions' => 'Два-три рядки. Порожньо — абзац не показується.',
			],
			[
				'key' => 'field_fr_msg', 'name' => 'messengers', 'label' => 'Месенджери',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'instructions' => 'Порядок тут задає порядок іконок у віджеті звʼязку.',
				'sub_fields' => [
					[
						'key' => 'field_fr_msg_kind', 'name' => 'kind', 'label' => 'Канал', 'type' => 'select',
						'choices' => [ 'tg' => 'Telegram', 'wa' => 'WhatsApp', 'viber' => 'Viber', 'phone' => 'Телефон', 'mail' => 'Пошта' ],
						'required' => 1, 'wrapper' => [ 'width' => 30 ],
					],
					[ 'key' => 'field_fr_msg_url', 'name' => 'url', 'label' => 'Посилання', 'type' => 'url', 'required' => 1, 'wrapper' => [ 'width' => 70 ] ],
				],
			],
			[
				'key' => 'field_fr_social', 'name' => 'socials', 'label' => 'Соцмережі',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [
					[
						'key' => 'field_fr_soc_kind', 'name' => 'kind', 'label' => 'Мережа', 'type' => 'select',
						'choices' => [ 'ig' => 'Instagram', 'fb' => 'Facebook', 'yt' => 'YouTube', 'tt' => 'TikTok' ],
						'required' => 1, 'wrapper' => [ 'width' => 30 ],
					],
					[ 'key' => 'field_fr_soc_url', 'name' => 'url', 'label' => 'Посилання', 'type' => 'url', 'required' => 1, 'wrapper' => [ 'width' => 70 ] ],
				],
			],
		],
	] );
} );

/**
 * Списки типів занять підставляються з прайсу, а не дублюються в коді.
 * Додали тип у «Курси → Ціни» — він одразу зʼявився і в складі курсу,
 * і в окремих заняттях.
 */
function fr_lesson_type_choices( array $field ): array {
	$out = [];
	if ( function_exists( 'get_field' ) ) {
		foreach ( (array) get_field( 'lesson_types', 'option' ) as $row ) {
			if ( ! empty( $row['id'] ) ) {
				$out[ $row['id'] ] = ( $row['label'] ?? $row['id'] ) . ' (' . $row['id'] . ')';
			}
		}
	}
	$field['choices'] = $out;
	return $field;
}
add_filter( 'acf/load_field/key=field_fr_mix_type', 'fr_lesson_type_choices' );
add_filter( 'acf/load_field/key=field_fr_x_type', 'fr_lesson_type_choices' );
