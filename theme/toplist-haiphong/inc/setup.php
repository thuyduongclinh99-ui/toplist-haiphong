<?php
/**
 * Theme supports, assets, menus and Customizer settings.
 *
 * @package toplist-haiphong
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'toplist-haiphong', THP_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 120,
				'width'       => 420,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		add_image_size( 'thp-card', 640, 400, true );
		add_image_size( 'thp-hero', 1280, 820, true );

		register_nav_menus(
			array(
				'primary' => __( 'Menu chính', 'toplist-haiphong' ),
				'footer'  => __( 'Menu chân trang: Khám phá', 'toplist-haiphong' ),
				'portal'  => __( 'Menu chân trang: Liên hệ', 'toplist-haiphong' ),
				'legal'   => __( 'Menu chân trang: Pháp lý', 'toplist-haiphong' ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'thp-fonts', 'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap', array(), null );
		wp_enqueue_style( 'thp-icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block', array(), null );
		wp_enqueue_style( 'thp-main', THP_URI . '/assets/css/main.css', array(), filemtime( THP_DIR . '/assets/css/main.css' ) );
		wp_enqueue_script( 'thp-main', THP_URI . '/assets/js/main.js', array(), filemtime( THP_DIR . '/assets/js/main.js' ), array( 'strategy' => 'defer' ) );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
);

// Chevron after top-level primary menu items that open a dropdown.
add_filter(
	'nav_menu_item_title',
	function ( $title, $item, $args, $depth ) {
		if ( 'primary' === $args->theme_location && 0 === $depth && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			$title .= thp_icon( 'expand_more', 'text-[18px]' );
		}
		return $title;
	},
	10,
	4
);

// Search results list places (posts) only, not static pages.
add_action(
	'pre_get_posts',
	function ( WP_Query $query ) {
		if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
			$query->set( 'post_type', 'post' );
		}
	}
);

// A search filtered by category also counts as a category page; don't highlight menu items there.
add_filter(
	'nav_menu_css_class',
	function ( $classes ) {
		return is_search() ? array_diff( $classes, array( 'current-menu-item', 'current-menu-ancestor', 'current-post-ancestor' ) ) : $classes;
	}
);

add_filter(
	'wp_resource_hints',
	function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}
		return $urls;
	},
	10,
	2
);

/**
 * Customizer: editable contact info, submit link and intro block.
 */
add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			'thp_site',
			array(
				'title'    => __( 'Toplist Hải Phòng', 'toplist-haiphong' ),
				'priority' => 30,
			)
		);

		$fields = array(
			'thp_intro_title'   => array( __( 'Tiêu đề khối giới thiệu', 'toplist-haiphong' ), 'text' ),
			'thp_intro_text'    => array( __( 'Nội dung khối giới thiệu', 'toplist-haiphong' ), 'textarea' ),
			'thp_submit_url'    => array( __( 'Link "Gửi đề xuất" (để trống: trang /gui-de-xuat/)', 'toplist-haiphong' ), 'url' ),
			'thp_address'       => array( __( 'Địa chỉ', 'toplist-haiphong' ), 'text' ),
			'thp_hotline'       => array( __( 'Hotline', 'toplist-haiphong' ), 'text' ),
			'thp_email'         => array( __( 'Email', 'toplist-haiphong' ), 'email' ),
			'thp_footer_about'  => array( __( 'Mô tả ở chân trang', 'toplist-haiphong' ), 'textarea' ),
			'thp_map_query'     => array( __( 'Địa điểm cho bản đồ ở thanh bên', 'toplist-haiphong' ), 'text' ),
		);

		foreach ( $fields as $id => $field ) {
			$sanitize = array(
				'url'      => 'esc_url_raw',
				'email'    => 'sanitize_email',
				'textarea' => 'sanitize_textarea_field',
			);
			$wp_customize->add_setting(
				$id,
				array(
					'default'           => thp_default( $id ),
					'sanitize_callback' => isset( $sanitize[ $field[1] ] ) ? $sanitize[ $field[1] ] : 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				$id,
				array(
					'label'   => $field[0],
					'section' => 'thp_site',
					'type'    => $field[1],
				)
			);
		}
	}
);

/**
 * Default values for Customizer settings.
 *
 * @param string $key Setting id.
 * @return string
 */
function thp_default( $key ) {
	$defaults = array(
		'thp_intro_title'  => 'Toplist Hải Phòng - Linh hồn phố Cảng đương đại',
		'thp_intro_text'   => 'Hệ thống xếp hạng và cẩm nang trải nghiệm độc lập tại Hải Phòng. Chúng tôi đi từng ngõ phố, đối chiếu đánh giá thực tế và tuyển chọn những điểm ăn ngon, chốn nghỉ chân, danh thắng và cách sống đậm chất Đất Cảng.',
		'thp_submit_url'   => '',
		'thp_address'      => 'Đường Hoàng Văn Thụ, Hồng Bàng, TP. Hải Phòng',
		'thp_hotline'      => '',
		'thp_email'        => get_option( 'admin_email' ),
		'thp_footer_about' => 'Cổng thông tin và cẩm nang tuyển chọn của thành phố Cảng. Đánh giá khách quan, gợi ý ẩm thực, lưu trú, cà phê và trải nghiệm văn hóa Hải Phòng.',
		'thp_map_query'    => 'Hải Phòng, Việt Nam',
	);
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Read a Customizer setting in the current language. Saved texts go through
 * Polylang string translations; built-in defaults through thp__().
 *
 * @param string $key Setting id.
 * @return string
 */
function thp_option( $key ) {
	$value = get_theme_mod( $key );
	if ( $value ) {
		return function_exists( 'pll__' ) ? pll__( $value ) : $value;
	}
	return thp__( thp_default( $key ) );
}
