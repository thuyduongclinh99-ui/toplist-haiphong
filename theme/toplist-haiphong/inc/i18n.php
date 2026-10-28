<?php
/**
 * Bilingual helpers. Vietnamese is the source language; English strings live in
 * inc/strings-en.php. Language detection and URLs use Polylang when it is active
 * and fall back to Vietnamese without it.
 *
 * @package toplist-haiphong
 */

defined( 'ABSPATH' ) || exit;

/**
 * Current language slug: 'vi' or 'en'.
 *
 * @return string
 */
function thp_lang() {
	$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : '';
	// REST requests carry no Polylang language; the chatbot sets it through this filter.
	return apply_filters( 'thp_lang', $lang ? $lang : 'vi' );
}

/**
 * Translate a Vietnamese UI string into the current language.
 *
 * @param string $vi Vietnamese source text.
 * @return string
 */
function thp__( $vi ) {
	static $en = null;
	if ( 'en' !== thp_lang() ) {
		return $vi;
	}
	if ( null === $en ) {
		$en = require THP_DIR . '/inc/strings-en.php';
	}
	return isset( $en[ $vi ] ) ? $en[ $vi ] : $vi;
}

/**
 * Echo a translated, escaped UI string.
 *
 * @param string $vi Vietnamese source text.
 */
function thp_e( $vi ) {
	echo esc_html( thp__( $vi ) );
}

/**
 * Home URL of the current language.
 *
 * @return string
 */
function thp_home_url() {
	return function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
}

/**
 * Permalink of a page (looked up by its Vietnamese slug) in the current language.
 *
 * @param string $slug Vietnamese page slug.
 * @return string Empty when the page does not exist.
 */
function thp_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		return '';
	}
	$id = function_exists( 'pll_get_post' ) ? pll_get_post( $page->ID ) : $page->ID;
	return get_permalink( $id ? $id : $page->ID );
}

/**
 * Category (looked up by its Vietnamese slug) in the current language.
 *
 * @param string $slug Vietnamese category slug.
 * @return WP_Term|null
 */
function thp_category( $slug ) {
	// 'lang' => '' stops Polylang from limiting the lookup to the current language.
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'slug'       => $slug,
			'hide_empty' => false,
			'number'     => 1,
			'lang'       => '',
		)
	);
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	$term = $terms[0];
	if ( function_exists( 'pll_get_term' ) ) {
		$id = pll_get_term( $term->term_id );
		if ( $id && $id !== $term->term_id ) {
			$term = get_term( $id, 'category' );
		}
	}
	return $term instanceof WP_Term ? $term : null;
}

/**
 * "Gửi đề xuất" link: Customizer override, else the submit page in the current language.
 *
 * @return string
 */
function thp_submit_url() {
	$custom = get_theme_mod( 'thp_submit_url' );
	if ( $custom ) {
		return $custom;
	}
	$url = thp_page_url( 'gui-de-xuat' );
	return $url ? $url : thp_home_url();
}

/**
 * Languages for the header switcher.
 *
 * @return array[] { slug, name, url, current }
 */
function thp_languages() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}
	$langs = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_no_translation' => 0,
		)
	);
	$out   = array();
	foreach ( (array) $langs as $lang ) {
		$out[] = array(
			'slug'    => $lang['slug'],
			'name'    => $lang['name'],
			'url'     => $lang['url'],
			'current' => ! empty( $lang['current_lang'] ),
		);
	}
	return $out;
}

/**
 * Translations of the "danh-muc" page use the same template (page-danh-muc.php).
 */
add_filter(
	'page_template_hierarchy',
	function ( $templates ) {
		if ( ! function_exists( 'pll_get_post' ) ) {
			return $templates;
		}
		$source = get_page_by_path( 'danh-muc' );
		if ( $source && (int) pll_get_post( get_queried_object_id(), 'vi' ) === (int) $source->ID ) {
			array_unshift( $templates, 'page-danh-muc.php' );
		}
		return $templates;
	}
);

/**
 * Make Customizer texts translatable in Languages → Translations.
 */
add_action(
	'init',
	function () {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}
		foreach ( array( 'thp_intro_title', 'thp_intro_text', 'thp_address', 'thp_footer_about' ) as $key ) {
			$value = get_theme_mod( $key );
			if ( $value ) {
				pll_register_string( $key, $value, 'Toplist Hải Phòng', false !== strpos( $key, 'text' ) || false !== strpos( $key, 'about' ) );
			}
		}
	}
);
