<?php
/**
 * Data lookups for the homepage and sidebar.
 *
 * @package toplist-haiphong
 */

defined( 'ABSPATH' ) || exit;

/**
 * Category sections shown on the homepage, in order. `slug` is the Vietnamese
 * category slug; missing categories are skipped. Texts are Vietnamese sources
 * for thp__().
 *
 * @return array[]
 */
function thp_home_sections() {
	return apply_filters(
		'thp_home_sections',
		array(
			array(
				'slug'     => 'du-lich',
				'title'    => 'Khám phá địa phương & cảnh đẹp Đất Cảng',
				'subtitle' => 'Cẩm nang danh thắng, di tích cổ kính và vịnh đảo Hải Phòng',
				'accent'   => 'primary',
			),
			array(
				'slug'     => 'am-thuc',
				'title'    => 'Tinh hoa ẩm thực & bản đồ food tour',
				'subtitle' => 'Quán ngon gia truyền, nhà hàng đặc sản và món phải thử khi tới phố Cảng',
				'accent'   => 'secondary',
			),
			array(
				'slug'     => 'luu-tru',
				'title'    => 'Lưu trú, khách sạn & homestay',
				'subtitle' => 'Chỗ nghỉ được đánh giá thực tế, từ homestay trẻ trung tới căn hộ dịch vụ',
				'accent'   => 'tertiary',
			),
		)
	);
}

/**
 * Hero slides: sticky posts with a thumbnail, topped up with the latest ones.
 *
 * @param int $limit Number of slides.
 * @return WP_Post[]
 */
function thp_hero_posts( $limit = 4 ) {
	$sticky = array_filter( array_map( 'absint', (array) get_option( 'sticky_posts' ) ) );
	$posts  = array();

	if ( $sticky ) {
		$posts = get_posts(
			array(
				'post__in'       => $sticky,
				'posts_per_page' => $limit,
				'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery -- small capped query.
			)
		);
	}

	if ( count( $posts ) < $limit ) {
		$posts = array_merge(
			$posts,
			get_posts(
				array(
					'posts_per_page' => $limit - count( $posts ),
					'post__not_in'   => wp_list_pluck( $posts, 'ID' ),
					'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery -- small capped query.
				)
			)
		);
	}

	return $posts;
}

/**
 * Travel posts with a featured image, for the homepage photo strip.
 *
 * @param int $limit Maximum number of posts.
 * @return WP_Post[]
 */
function thp_travel_photo_posts( $limit = 12 ) {
	$term = thp_category( 'du-lich' );
	if ( ! $term ) {
		return array();
	}
	return get_posts(
		array(
			'posts_per_page'      => $limit,
			'cat'                 => $term->term_id,
			'meta_key'            => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery -- small capped query.
			'ignore_sticky_posts' => true,
		)
	);
}

/**
 * Highest-rated places, ties broken by review count.
 *
 * @param int $limit Number of rows.
 * @return WP_Post[]
 */
function thp_top_posts( $limit = 10 ) {
	return get_posts(
		array(
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- small capped query.
				'rating'  => array(
					'key'  => '_thp_rating',
					'type' => 'DECIMAL(3,1)',
				),
				'reviews' => array(
					'key'  => '_thp_review_count',
					'type' => 'NUMERIC',
				),
			),
			'orderby'             => array(
				'rating'  => 'DESC',
				'reviews' => 'DESC',
			),
		)
	);
}

/**
 * Non-empty categories by size, each paired with the newest thumbnail inside it.
 *
 * @param int $limit Number of categories.
 * @return array[] { term: WP_Term, image: int }
 */
function thp_quick_categories( $limit = 5 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => $limit,
			'exclude'    => array( (int) get_option( 'default_category' ) ),
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}

	$items = array();
	foreach ( $terms as $term ) {
		$latest  = get_posts(
			array(
				'posts_per_page' => 1,
				'cat'            => $term->term_id,
				'meta_key'       => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery -- single row.
				'fields'         => 'ids',
			)
		);
		$items[] = array(
			'term'  => $term,
			'image' => $latest ? get_post_thumbnail_id( $latest[0] ) : 0,
		);
	}
	return $items;
}

/**
 * Posts sharing the first category of a post.
 *
 * @param int $post_id Post id.
 * @param int $limit   Number of posts.
 * @return WP_Post[]
 */
function thp_related_posts( $post_id, $limit = 3 ) {
	$term = thp_primary_category( $post_id );
	if ( ! $term ) {
		return array();
	}
	return get_posts(
		array(
			'posts_per_page'      => $limit,
			'cat'                 => $term->term_id,
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
		)
	);
}

/**
 * Published post ids in the current language (Polylang filters the query).
 *
 * @return int[]
 */
function thp_post_ids() {
	static $ids = null;
	if ( null === $ids ) {
		$ids = get_posts(
			array(
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);
	}
	return $ids;
}

/**
 * Number of published posts in the current language.
 *
 * @return int
 */
function thp_post_count() {
	return count( thp_post_ids() );
}

/**
 * Number of non-empty categories in the current language.
 *
 * @return int
 */
function thp_category_count() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
			'fields'     => 'ids',
			'exclude'    => array( (int) get_option( 'default_category' ) ),
		)
	);
	return is_wp_error( $terms ) ? 0 : count( $terms );
}

/**
 * Sum of review counts in the current language, for the intro stats ribbon.
 *
 * @return int
 */
function thp_total_reviews() {
	$key    = 'thp_total_reviews_' . thp_lang();
	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return (int) $cached;
	}
	$total = 0;
	foreach ( thp_post_ids() as $id ) {
		$total += (int) get_post_meta( $id, '_thp_review_count', true );
	}
	set_transient( $key, $total, HOUR_IN_SECONDS );
	return $total;
}

add_action(
	'save_post_post',
	function () {
		delete_transient( 'thp_total_reviews_vi' );
		delete_transient( 'thp_total_reviews_en' );
	},
	20
);
