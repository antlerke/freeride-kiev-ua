<?php
/**
 * Поля окремих сторінок.
 *
 * Прив'язані до шаблону, а не до конкретної сторінки: поміняли шаблон —
 * поля поїхали за ним. Так редактор не побачить у «Контактах» поля
 * галереї й навпаки.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fr_tpl_location( string $tpl ): array {
	return [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => $tpl ] ] ];
}

add_action( 'acf/init', static function (): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// ── Контакти ────────────────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_contacts_page',
		'title'    => 'Блоки сторінки «Контакти»',
		'location' => fr_tpl_location( 'page-contacts.php' ),
		'fields'   => [
			[ 'key' => 'field_fr_cp_tab1', 'label' => 'Локація', 'type' => 'tab' ],
			[ 'key' => 'field_fr_cp_eyebrow', 'name' => 'loc_eyebrow', 'label' => 'Надзаголовок', 'type' => 'text', 'default_value' => 'Наша локація', 'wrapper' => [ 'width' => 35 ] ],
			[ 'key' => 'field_fr_cp_title',   'name' => 'loc_title',   'label' => 'Заголовок',    'type' => 'text', 'default_value' => 'Навчальний майданчик FreeRide', 'wrapper' => [ 'width' => 65 ] ],
			[ 'key' => 'field_fr_cp_metro',   'name' => 'loc_metro',   'label' => 'Станція метро','type' => 'text', 'wrapper' => [ 'width' => 35 ] ],
			[ 'key' => 'field_fr_cp_note',    'name' => 'loc_note',    'label' => 'Примітка під адресою', 'type' => 'text', 'wrapper' => [ 'width' => 65 ], 'instructions' => 'Наприклад: точну адресу та схему проїзду надсилаємо після запису.' ],
			[ 'key' => 'field_fr_cp_map',     'name' => 'loc_map',     'label' => 'Зображення карти', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Статичний знімок карти. Так сторінка не тягне чужі скрипти й не ставить кукі Google до згоди людини.' ],
			[ 'key' => 'field_fr_cp_yard',    'name' => 'loc_photo',   'label' => 'Фото майданчика', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 50 ] ],

			[ 'key' => 'field_fr_cp_tab2', 'label' => 'Форма', 'type' => 'tab' ],
			[ 'key' => 'field_fr_cp_ftitle', 'name' => 'form_title', 'label' => 'Заголовок форми', 'type' => 'text', 'default_value' => 'Звʼяжіться з нами', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_fr_cp_fbtn',   'name' => 'form_button','label' => 'Напис на кнопці', 'type' => 'text', 'default_value' => 'Надіслати запит', 'wrapper' => [ 'width' => 50 ] ],
			[
				'key' => 'field_fr_cp_topics', 'name' => 'form_topics', 'label' => 'Теми запиту',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати тему',
				'instructions' => 'Список у випадайці. Порожньо — поле теми не показується.',
				'sub_fields' => [ [ 'key' => 'field_fr_cp_topic', 'name' => 'topic', 'label' => 'Тема', 'type' => 'text', 'required' => 1 ] ],
			],
			[ 'key' => 'field_fr_cp_fok', 'name' => 'form_success', 'label' => 'Текст після відправки', 'type' => 'text', 'default_value' => 'Дякуємо! Передзвонимо найближчим часом.' ],

			[ 'key' => 'field_fr_cp_tab3', 'label' => 'Онлайн-запис', 'type' => 'tab' ],
			[ 'key' => 'field_fr_cp_btitle', 'name' => 'book_title', 'label' => 'Заголовок', 'type' => 'text', 'default_value' => 'Найпростіший спосіб — забронювати час онлайн' ],
			[ 'key' => 'field_fr_cp_btext',  'name' => 'book_text',  'label' => 'Текст', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '' ],
			[ 'key' => 'field_fr_cp_bbtn',   'name' => 'book_button','label' => 'Кнопка', 'type' => 'text', 'default_value' => 'Записатися на заняття', 'wrapper' => [ 'width' => 40 ] ],
			[ 'key' => 'field_fr_cp_burl',   'name' => 'book_url',   'label' => 'Посилання на запис', 'type' => 'url', 'wrapper' => [ 'width' => 60 ] ],
			[ 'key' => 'field_fr_cp_bimg',   'name' => 'book_image', 'label' => 'Зображення', 'type' => 'image', 'return_format' => 'id' ],

			[ 'key' => 'field_fr_cp_tab4', 'label' => 'Смуга переваг', 'type' => 'tab' ],
			[
				'key' => 'field_fr_cp_bene', 'name' => 'benefits', 'label' => 'Переваги',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [
					[ 'key' => 'field_fr_cp_b_icon', 'name' => 'icon', 'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_cp_b_t',    'name' => 'title','label' => 'Назва',  'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 35 ] ],
					[ 'key' => 'field_fr_cp_b_s',    'name' => 'text', 'label' => 'Уточнення', 'type' => 'text', 'wrapper' => [ 'width' => 40 ] ],
				],
			],
		],
	] );

	// ── Галерея ─────────────────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_gallery_page',
		'title'    => 'Блоки сторінки «Галерея»',
		'location' => fr_tpl_location( 'page-gallery.php' ),
		'fields'   => [
			[ 'key' => 'field_fr_gp_eyebrow', 'name' => 'eyebrow', 'label' => 'Надзаголовок', 'type' => 'text', 'default_value' => 'Фото / Відео / Життя школи' ],
			[ 'key' => 'field_fr_gp_vurl',    'name' => 'hero_video', 'label' => 'Відео про школу', 'type' => 'url', 'instructions' => 'Кнопка «Дивитися відео» в шапці. Порожньо — кнопки немає.' ],
			[
				'key' => 'field_fr_gp_feat', 'name' => 'featured', 'label' => 'Добірка',
				'type' => 'repeater', 'button_label' => 'Додати матеріал', 'layout' => 'block',
				'instructions' => 'Перший матеріал показується великим, решта — у колонці праворуч.',
				'sub_fields' => [
					[ 'key' => 'field_fr_gp_f_img',  'name' => 'image', 'label' => 'Зображення', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_gp_f_kind', 'name' => 'kind',  'label' => 'Тип', 'type' => 'select', 'choices' => [ 'photo' => 'Фото', 'video' => 'Відео' ], 'default_value' => 'photo', 'wrapper' => [ 'width' => 15 ] ],
					[ 'key' => 'field_fr_gp_f_tag',  'name' => 'tag',   'label' => 'Розділ', 'type' => 'text', 'wrapper' => [ 'width' => 20 ], 'instructions' => 'Події, Тренування, Спільнота…' ],
					[ 'key' => 'field_fr_gp_f_t',    'name' => 'title', 'label' => 'Підпис', 'type' => 'text', 'wrapper' => [ 'width' => 20 ] ],
					[ 'key' => 'field_fr_gp_f_len',  'name' => 'length','label' => 'Тривалість', 'type' => 'text', 'wrapper' => [ 'width' => 15 ], 'instructions' => 'Для відео, напр. 4:36' ],
					[ 'key' => 'field_fr_gp_f_url',  'name' => 'url',   'label' => 'Посилання', 'type' => 'url' ],
				],
			],
			[
				'key' => 'field_fr_gp_alb', 'name' => 'albums', 'label' => 'Альбоми',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати альбом',
				'sub_fields' => [
					[ 'key' => 'field_fr_gp_a_img', 'name' => 'image', 'label' => 'Обкладинка', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 20 ] ],
					[ 'key' => 'field_fr_gp_a_t',   'name' => 'title', 'label' => 'Назва', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_gp_a_tag', 'name' => 'tag',   'label' => 'Розділ', 'type' => 'text', 'wrapper' => [ 'width' => 20 ] ],
					[ 'key' => 'field_fr_gp_a_n',   'name' => 'count', 'label' => 'Знімків', 'type' => 'number', 'min' => 0, 'wrapper' => [ 'width' => 15 ] ],
					[ 'key' => 'field_fr_gp_a_url', 'name' => 'url',   'label' => 'Посилання', 'type' => 'url', 'wrapper' => [ 'width' => 20 ] ],
				],
			],
			[ 'key' => 'field_fr_gp_cta_t', 'name' => 'cta_title', 'label' => 'Заклик унизу', 'type' => 'text', 'default_value' => 'Більше контенту в наших соцмережах', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_fr_gp_cta_b', 'name' => 'cta_button','label' => 'Кнопка', 'type' => 'text', 'default_value' => 'Підписатися на FreeRide', 'wrapper' => [ 'width' => 25 ] ],
			[ 'key' => 'field_fr_gp_cta_u', 'name' => 'cta_url',   'label' => 'Посилання', 'type' => 'url', 'wrapper' => [ 'width' => 25 ] ],
			[ 'key' => 'field_fr_gp_cta_i', 'name' => 'cta_image', 'label' => 'Тло заклику', 'type' => 'image', 'return_format' => 'id' ],
		],
	] );

	// ── Головна ─────────────────────────────────────────────────────
	// Прив'язка до головної, а не до шаблону: головну призначають
	// у «Налаштування → Читання», і окремий шаблон їй не потрібен.
	acf_add_local_field_group( [
		'key'      => 'group_fr_front',
		'title'    => 'Блоки головної',
		'location' => [ [ [ 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ] ] ],
		'fields'   => [
			[ 'key' => 'field_fr_fp_tab1', 'label' => 'Перший екран', 'type' => 'tab' ],
			[ 'key' => 'field_fr_fp_sub',   'name' => 'hero_sub',   'label' => 'Підзаголовок', 'type' => 'text' ],
			[ 'key' => 'field_fr_fp_motto', 'name' => 'hero_motto', 'label' => 'Гасло', 'type' => 'text', 'wrapper' => [ 'width' => 60 ], 'instructions' => 'Останнє слово підсвічується зеленим автоматично.' ],
			[ 'key' => 'field_fr_fp_vlab',  'name' => 'video_label','label' => 'Напис кнопки відео', 'type' => 'text', 'default_value' => 'Дивитись відео', 'wrapper' => [ 'width' => 40 ] ],
			[ 'key' => 'field_fr_fp_vurl',  'name' => 'video_url',  'label' => 'Посилання на відео', 'type' => 'url', 'instructions' => 'Порожньо — другої кнопки немає.' ],

			[ 'key' => 'field_fr_fp_tab2', 'label' => 'Три переваги', 'type' => 'tab' ],
			[
				'key' => 'field_fr_fp_perks', 'name' => 'perks', 'label' => 'Переваги',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [
					[ 'key' => 'field_fr_fp_p_i', 'name' => 'icon',  'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_fp_p_t', 'name' => 'title', 'label' => 'Назва', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 35 ] ],
					[ 'key' => 'field_fr_fp_p_s', 'name' => 'text',  'label' => 'Уточнення', 'type' => 'text', 'wrapper' => [ 'width' => 40 ] ],
				],
			],

			[ 'key' => 'field_fr_fp_tab3', 'label' => 'Rider Card', 'type' => 'tab' ],
			[ 'key' => 'field_fr_fp_rlede', 'name' => 'rider_lede', 'label' => 'Підзаголовок', 'type' => 'text', 'default_value' => 'Твій прогрес у навчанні' ],
			[
				'key' => 'field_fr_fp_rlist', 'name' => 'rider_points', 'label' => 'Що дає картка',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати пункт',
				'sub_fields' => [ [ 'key' => 'field_fr_fp_rl', 'name' => 'text', 'label' => 'Пункт', 'type' => 'text', 'required' => 1 ] ],
			],
			[ 'key' => 'field_fr_fp_rtxt', 'name' => 'rider_text', 'label' => 'Текст збоку', 'type' => 'textarea', 'rows' => 2, 'new_lines' => 'br', 'wrapper' => [ 'width' => 60 ] ],
			[ 'key' => 'field_fr_fp_rurl', 'name' => 'rider_url',  'label' => 'Посилання «Детальніше»', 'type' => 'url', 'wrapper' => [ 'width' => 40 ] ],

			[ 'key' => 'field_fr_fp_tab4', 'label' => 'Відгук', 'type' => 'tab' ],
			[ 'key' => 'field_fr_fp_qt',  'name' => 'quote_text',   'label' => 'Текст відгуку', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '' ],
			[ 'key' => 'field_fr_fp_qw',  'name' => 'quote_author', 'label' => 'Хто', 'type' => 'text', 'wrapper' => [ 'width' => 45 ] ],
			[ 'key' => 'field_fr_fp_qr',  'name' => 'quote_rating', 'label' => 'Оцінка', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5, 'wrapper' => [ 'width' => 20 ] ],
			[ 'key' => 'field_fr_fp_qa',  'name' => 'quote_photo',  'label' => 'Фото', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 35 ] ],

			[ 'key' => 'field_fr_fp_tab5', 'label' => 'Заклик унизу', 'type' => 'tab' ],
			[ 'key' => 'field_fr_fp_ftxt', 'name' => 'final_text',  'label' => 'Текст', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '' ],
			[ 'key' => 'field_fr_fp_fimg', 'name' => 'final_image', 'label' => 'Тло', 'type' => 'image', 'return_format' => 'id' ],
		],
	] );

	// ── Про нас ─────────────────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_about',
		'title'    => 'Блоки сторінки «Про нас»',
		'location' => fr_tpl_location( 'page-about.php' ),
		'fields'   => [
			[ 'key' => 'field_fr_ab_tab1', 'label' => 'Засновник', 'type' => 'tab' ],
			[ 'key' => 'field_fr_ab_fphoto','name' => 'founder_photo','label' => 'Портрет', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 30 ] ],
			[ 'key' => 'field_fr_ab_fname', 'name' => 'founder_name', 'label' => 'Імʼя', 'type' => 'text', 'wrapper' => [ 'width' => 35 ] ],
			[ 'key' => 'field_fr_ab_frole', 'name' => 'founder_role', 'label' => 'Роль', 'type' => 'text', 'wrapper' => [ 'width' => 35 ] ],
			[ 'key' => 'field_fr_ab_fquote','name' => 'founder_quote','label' => 'Пряма мова', 'type' => 'textarea', 'rows' => 4, 'new_lines' => '' ],
			[ 'key' => 'field_fr_ab_fsign', 'name' => 'founder_sign', 'label' => 'Підпис', 'type' => 'text', 'wrapper' => [ 'width' => 40 ] ],
			[
				'key' => 'field_fr_ab_facts', 'name' => 'founder_facts', 'label' => 'Факти',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати факт',
				'sub_fields' => [
					[ 'key' => 'field_fr_ab_f_i', 'name' => 'icon',  'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_ab_f_v', 'name' => 'value', 'label' => 'Головне', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_ab_f_l', 'name' => 'label', 'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 45 ] ],
				],
			],

			[ 'key' => 'field_fr_ab_tab2', 'label' => 'Місія і принципи', 'type' => 'tab' ],
			[ 'key' => 'field_fr_ab_mimg', 'name' => 'mission_image', 'label' => 'Тло', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 35 ] ],
			[ 'key' => 'field_fr_ab_mtxt', 'name' => 'mission_text',  'label' => 'Місія', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '', 'wrapper' => [ 'width' => 65 ] ],
			[
				'key' => 'field_fr_ab_prin', 'name' => 'principles', 'label' => 'Принципи',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати принцип',
				'sub_fields' => [
					[ 'key' => 'field_fr_ab_pr_i', 'name' => 'icon', 'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_ab_pr_t', 'name' => 'title','label' => 'Назва', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 70 ] ],
				],
			],

			[ 'key' => 'field_fr_ab_tab3', 'label' => 'Цифри', 'type' => 'tab' ],
			[
				'key' => 'field_fr_ab_stats', 'name' => 'stats', 'label' => 'Цифри школи',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [
					[ 'key' => 'field_fr_ab_s_i', 'name' => 'icon',  'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_ab_s_v', 'name' => 'value', 'label' => 'Число', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_ab_s_l', 'name' => 'label', 'label' => 'Підпис', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
				],
			],

			[ 'key' => 'field_fr_ab_tab4', 'label' => 'Як ми навчаємо', 'type' => 'tab' ],
			[
				'key' => 'field_fr_ab_steps', 'name' => 'steps', 'label' => 'Етапи',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати етап',
				'sub_fields' => [
					[ 'key' => 'field_fr_ab_st_i', 'name' => 'icon',  'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 20 ] ],
					[ 'key' => 'field_fr_ab_st_t', 'name' => 'title', 'label' => 'Назва', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 32 ] ],
					[ 'key' => 'field_fr_ab_st_s', 'name' => 'text',  'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 48 ] ],
				],
			],
			[ 'key' => 'field_fr_ab_stimg', 'name' => 'steps_image', 'label' => 'Фото до блоку', 'type' => 'image', 'return_format' => 'id' ],

			[ 'key' => 'field_fr_ab_tab5', 'label' => 'Техніка', 'type' => 'tab' ],
			[ 'key' => 'field_fr_ab_gimg', 'name' => 'gear_image', 'label' => 'Фото мотопарку', 'type' => 'image', 'return_format' => 'id' ],
			[
				'key' => 'field_fr_ab_chips', 'name' => 'gear_chips', 'label' => 'Позначки',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [
					[ 'key' => 'field_fr_ab_c_i', 'name' => 'icon', 'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_ab_c_t', 'name' => 'text', 'label' => 'Текст', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 70 ] ],
				],
			],

			[ 'key' => 'field_fr_ab_tab6', 'label' => 'Заклик унизу', 'type' => 'tab' ],
			[ 'key' => 'field_fr_ab_ftxt', 'name' => 'final_text',  'label' => 'Текст', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '' ],
			[ 'key' => 'field_fr_ab_fimg', 'name' => 'final_image', 'label' => 'Тло', 'type' => 'image', 'return_format' => 'id' ],
		],
	] );

	// ── Подарунковий сертифікат ─────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_cert',
		'title'    => 'Блоки сторінки «Сертифікат»',
		'location' => fr_tpl_location( 'page-certificate.php' ),
		'fields'   => [
			[ 'key' => 'field_fr_ct_tab1', 'label' => 'Сам сертифікат', 'type' => 'tab' ],
			[ 'key' => 'field_fr_ct_cap',  'name' => 'card_caption', 'label' => 'Напис на картці', 'type' => 'text', 'default_value' => 'Подарунковий сертифікат', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_fr_ct_note', 'name' => 'card_note',    'label' => 'Дрібний рядок на картці', 'type' => 'text', 'default_value' => 'Мотошкола в Києві', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_fr_ct_val',  'name' => 'card_value',   'label' => 'Номінал на картці', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Що надруковано великим на самому сертифікаті. Порожньо — рядка немає.' ],
			[ 'key' => 'field_fr_ct_term', 'name' => 'card_term',    'label' => 'Термін дії', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Наприклад: діє 12 місяців. Порожньо — рядок не показується.' ],
			[ 'key' => 'field_fr_ct_env',  'name' => 'envelope_note','label' => 'Про конверт', 'type' => 'text', 'default_value' => 'Сертифікат іде в комплекті з конвертом', 'wrapper' => [ 'width' => 50 ] ],

			[ 'key' => 'field_fr_ct_tab2', 'label' => 'Що подарувати', 'type' => 'tab' ],
			[
				'key' => 'field_fr_ct_gifts', 'name' => 'gifts', 'label' => 'Варіанти',
				'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Додати варіант',
				'instructions' => 'Якщо вибрати курс, ціна береться з нього і сама оновлюється разом із прайсом.',
				'sub_fields' => [
					[ 'key' => 'field_fr_ct_g_t',  'name' => 'title',  'label' => 'Назва', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 34 ] ],
					[ 'key' => 'field_fr_ct_g_i',  'name' => 'icon',   'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 22 ] ],
					[ 'key' => 'field_fr_ct_g_c',  'name' => 'course', 'label' => 'Курс', 'type' => 'post_object', 'post_type' => [ FR_COURSE ], 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => [ 'width' => 44 ] ],
					[ 'key' => 'field_fr_ct_g_s',  'name' => 'text',   'label' => 'Опис', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '' ],
					[ 'key' => 'field_fr_ct_g_p',  'name' => 'price_note', 'label' => 'Напис замість ціни', 'type' => 'text', 'instructions' => 'Якщо курс не вибрано: «від 13 500 грн», «будь-яка сума» тощо.' ],
				],
			],

			[ 'key' => 'field_fr_ct_tab3', 'label' => 'Як це працює', 'type' => 'tab' ],
			[
				'key' => 'field_fr_ct_steps', 'name' => 'steps', 'label' => 'Кроки',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати крок',
				'sub_fields' => [
					[ 'key' => 'field_fr_ct_s_t', 'name' => 'title', 'label' => 'Крок', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 35 ] ],
					[ 'key' => 'field_fr_ct_s_s', 'name' => 'text',  'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 65 ] ],
				],
			],

			[ 'key' => 'field_fr_ct_tab4', 'label' => 'Як отримати', 'type' => 'tab' ],
			[
				'key' => 'field_fr_ct_ship', 'name' => 'delivery', 'label' => 'Способи',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати спосіб',
				'instructions' => 'Порядок тут — порядок на сторінці. Найшвидший спосіб краще ставити першим.',
				'sub_fields' => [
					[ 'key' => 'field_fr_ct_d_i', 'name' => 'icon',  'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 20 ] ],
					[ 'key' => 'field_fr_ct_d_t', 'name' => 'title', 'label' => 'Спосіб', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_ct_d_s', 'name' => 'text',  'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
				],
			],
			[ 'key' => 'field_fr_ct_ftxt', 'name' => 'form_intro', 'label' => 'Текст над формою', 'type' => 'text', 'default_value' => 'Залиште номер — передзвонимо й оформимо сертифікат.' ],
		],
	] );

	// ── Підбір мотоцикла ────────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_pick',
		'title'    => 'Блоки сторінки «Підбір мотоцикла»',
		'location' => fr_tpl_location( 'page-bikepick.php' ),
		'fields'   => [
			[ 'key' => 'field_fr_pk_tab1', 'label' => 'Як ми працюємо', 'type' => 'tab' ],
			[ 'key' => 'field_fr_pk_lead', 'name' => 'intro', 'label' => 'Вступ', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '' ],
			[
				'key' => 'field_fr_pk_steps', 'name' => 'steps', 'label' => 'Кроки',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати крок',
				'instructions' => 'Нумерація проставляється сама — порядок задається перетягуванням.',
				'sub_fields' => [
					[ 'key' => 'field_fr_pk_s_t', 'name' => 'title', 'label' => 'Крок', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 40 ] ],
					[ 'key' => 'field_fr_pk_s_s', 'name' => 'text',  'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 60 ] ],
				],
			],

			[ 'key' => 'field_fr_pk_tab2', 'label' => 'Що ви отримуєте', 'type' => 'tab' ],
			[
				'key' => 'field_fr_pk_gets', 'name' => 'deliverables', 'label' => 'Результат',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [
					[ 'key' => 'field_fr_pk_g_i', 'name' => 'icon',  'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 20 ] ],
					[ 'key' => 'field_fr_pk_g_t', 'name' => 'title', 'label' => 'Назва', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 30 ] ],
					[ 'key' => 'field_fr_pk_g_s', 'name' => 'text',  'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
				],
			],

			[ 'key' => 'field_fr_pk_tab3', 'label' => 'Ціна', 'type' => 'tab' ],
			[ 'key' => 'field_fr_pk_p1v', 'name' => 'price_first',  'label' => 'Передоплата', 'type' => 'text', 'wrapper' => [ 'width' => 30 ], 'instructions' => 'Наприклад: 100 $' ],
			[ 'key' => 'field_fr_pk_p1t', 'name' => 'price_first_note', 'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 70 ] ],
			[ 'key' => 'field_fr_pk_p2v', 'name' => 'price_second', 'label' => 'Друга частина', 'type' => 'text', 'wrapper' => [ 'width' => 30 ], 'instructions' => 'Наприклад: половина торгу' ],
			[ 'key' => 'field_fr_pk_p2t', 'name' => 'price_second_note', 'label' => 'Пояснення', 'type' => 'text', 'wrapper' => [ 'width' => 70 ] ],
			[ 'key' => 'field_fr_pk_pnote','name' => 'price_logic', 'label' => 'Головна думка про ціну', 'type' => 'text', 'instructions' => 'Один рядок великим шрифтом під цінами.' ],

			[ 'key' => 'field_fr_pk_tab4', 'label' => 'Довіра', 'type' => 'tab' ],
			[ 'key' => 'field_fr_pk_pv',  'name' => 'proof_value', 'label' => 'Цифра', 'type' => 'text', 'wrapper' => [ 'width' => 25 ] ],
			[ 'key' => 'field_fr_pk_pt',  'name' => 'proof_text',  'label' => 'Підпис до цифри', 'type' => 'text', 'wrapper' => [ 'width' => 75 ] ],
			[ 'key' => 'field_fr_pk_pimg','name' => 'proof_image', 'label' => 'Фото', 'type' => 'image', 'return_format' => 'id' ],
			[ 'key' => 'field_fr_pk_ftxt','name' => 'form_intro',  'label' => 'Текст над формою', 'type' => 'text', 'default_value' => 'Розкажіть, який мотоцикл шукаєте — передзвонимо й обговоримо.' ],
		],
	] );

	// ── Юридичні сторінки ───────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_legal',
		'title'    => 'Юридична сторінка',
		'location' => fr_tpl_location( 'page-legal.php' ),
		'fields'   => [
			[ 'key' => 'field_fr_lg_upd', 'name' => 'updated', 'label' => 'Дата оновлення', 'type' => 'date_picker', 'display_format' => 'j F Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 35 ] ],
			[ 'key' => 'field_fr_lg_file','name' => 'file',    'label' => 'Файл для завантаження', 'type' => 'file', 'return_format' => 'array', 'wrapper' => [ 'width' => 65 ], 'instructions' => 'Необовʼязково: копія документа для тих, кому потрібен файл.' ],
			[ 'key' => 'field_fr_lg_toc', 'name' => 'show_toc','label' => 'Показувати зміст збоку', 'type' => 'true_false', 'default_value' => 1, 'ui' => 1, 'instructions' => 'Зміст будується сам із заголовків другого рівня в тексті.' ],
		],
	] );

	// ── Відгуки ─────────────────────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_rev_page',
		'title'    => 'Блоки сторінки «Відгуки»',
		'location' => fr_tpl_location( 'page-reviews.php' ),
		'fields'   => [
			[ 'key' => 'field_fr_rp_tab1', 'label' => 'Цифри згори', 'type' => 'tab' ],
			[
				'key' => 'field_fr_rp_stats', 'name' => 'stats', 'label' => 'Цифри',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'instructions' => 'Середня оцінка й кількість відгуків на сайті рахуються самі — тут решта.',
				'sub_fields' => [
					[ 'key' => 'field_fr_rp_s_i', 'name' => 'icon',  'label' => 'Іконка', 'type' => 'select', 'choices' => fr_icon_choices(), 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_rp_s_v', 'name' => 'value', 'label' => 'Число', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_rp_s_l', 'name' => 'label', 'label' => 'Підпис', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
				],
			],
			[ 'key' => 'field_fr_rp_tab2', 'label' => 'Google', 'type' => 'tab' ],
			[ 'key' => 'field_fr_rp_gurl', 'name' => 'google_url',   'label' => 'Посилання на профіль', 'type' => 'url' ],
			[ 'key' => 'field_fr_rp_grate','name' => 'google_rating','label' => 'Оцінка в Google', 'type' => 'text', 'wrapper' => [ 'width' => 30 ] ],
			[ 'key' => 'field_fr_rp_gcnt', 'name' => 'google_count', 'label' => 'Скільки відгуків', 'type' => 'text', 'wrapper' => [ 'width' => 70 ] ],
			[ 'key' => 'field_fr_rp_tab3', 'label' => 'Про нас пишуть', 'type' => 'tab' ],
			[
				'key' => 'field_fr_rp_press', 'name' => 'press', 'label' => 'Згадки в медіа',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [
					[ 'key' => 'field_fr_rp_p_l', 'name' => 'logo', 'label' => 'Логотип', 'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 25 ] ],
					[ 'key' => 'field_fr_rp_p_n', 'name' => 'name', 'label' => 'Видання', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 40 ] ],
					[ 'key' => 'field_fr_rp_p_u', 'name' => 'url',  'label' => 'Посилання', 'type' => 'url', 'wrapper' => [ 'width' => 35 ] ],
				],
			],
		],
	] );

	// ── Куди надсилати заявки ───────────────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_fr_form_opts',
		'title'    => 'Заявки',
		'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fr-contacts' ] ] ],
		'menu_order' => 5,
		'fields'   => [
			[
				'key' => 'field_fr_form_to', 'name' => 'form_recipient', 'label' => 'Куди надсилати заявки',
				'type' => 'email',
				'instructions' => 'Порожньо — на адресу адміністратора сайту. Заявка в будь-якому разі зберігається в розділі «Заявки», навіть якщо лист не пішов.',
			],
		],
	] );
} );
