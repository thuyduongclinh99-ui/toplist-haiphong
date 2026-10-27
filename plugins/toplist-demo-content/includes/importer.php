<?php
/**
 * Import steps for the Toplist Hải Phòng sample content. Each step is short and
 * safe to repeat, so the admin screen can run them one request at a time on
 * hosts with a low PHP time limit; WordPress Playground runs them in a row.
 *
 * @package toplist-demo-content
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ordered step ids.
 *
 * @return string[]
 */
function thp_demo_steps() {
	return array( 'setup', 'posts', 'images', 'menus', 'languages', 'english' );
}

/**
 * Run one step.
 *
 * @param string $step Step id.
 * @return string 'done', 'again' (more work left in this step) or 'skipped'.
 */
function thp_demo_run_step( $step ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	$callback = 'thp_demo_step_' . $step;
	return function_exists( $callback ) ? call_user_func( $callback ) : 'skipped';
}

/**
 * Run a step until it is finished (for Playground, where there is no time limit).
 *
 * @param string $step Step id.
 * @return string Final status.
 */
function thp_demo_run_step_fully( $step ) {
	do {
		$status = thp_demo_run_step( $step );
	} while ( 'again' === $status );
	return $status;
}

/**
 * Sample data file.
 *
 * @param string $name File name inside data/.
 * @return mixed
 */
function thp_demo_data( $name ) {
	return require THP_DEMO_DIR . 'data/' . $name;
}

/**
 * Vietnamese categories: slug => [name, description].
 *
 * @return array
 */
function thp_demo_categories() {
	return array(
		'du-lich'  => array( 'Du Lịch', 'Danh thắng, di tích, đền chùa và vịnh đảo Hải Phòng.' ),
		'am-thuc'  => array( 'Ẩm Thực', 'Nhà hàng, quán ngon và đặc sản đất Cảng được review thực tế.' ),
		'luu-tru'  => array( 'Lưu Trú', 'Khách sạn, homestay và căn hộ dịch vụ tại Hải Phòng.' ),
		'cafe'     => array( 'Cà Phê', 'Quán cà phê đẹp, yên tĩnh và đáng ghé ở Hải Phòng.' ),
		'giai-tri' => array( 'Giải Trí', 'Rạp phim, khu vui chơi và điểm giải trí cuối tuần.' ),
		'mua-sam'  => array( 'Mua Sắm', 'Trung tâm thương mại, chợ và nơi mua đặc sản Hải Phòng.' ),
		'the-thao' => array( 'Thể Thao', 'Sân vận động, sân golf và địa điểm luyện tập thể thao.' ),
		'van-tai'  => array( 'Vận Tải', 'Sân bay, nhà ga, bến tàu và cách di chuyển tới Hải Phòng.' ),
		'y-te'     => array( 'Y Tế', 'Bệnh viện, phòng khám và thông tin khám chữa bệnh.' ),
		'giao-duc' => array( 'Giáo Dục', 'Trường đại học, trường học và trung tâm đào tạo.' ),
		'lam-dep'  => array( 'Làm Đẹp', 'Spa, salon tóc, nail và kinh nghiệm làm đẹp.' ),
	);
}

/**
 * English categories keyed by Vietnamese slug: [name, slug, description].
 *
 * @return array
 */
function thp_demo_categories_en() {
	return array(
		'du-lich'  => array( 'Travel', 'travel', 'Landmarks, historic sites, temples and islands of Hai Phong.' ),
		'am-thuc'  => array( 'Food', 'food', 'Restaurants, eateries and Port City specialities, reviewed in practice.' ),
		'luu-tru'  => array( 'Stays', 'stays', 'Hotels, homestays and serviced apartments in Hai Phong.' ),
		'cafe'     => array( 'Coffee', 'coffee', 'Beautiful, quiet and worthwhile cafés in Hai Phong.' ),
		'giai-tri' => array( 'Entertainment', 'entertainment', 'Cinemas, play areas and weekend fun.' ),
		'mua-sam'  => array( 'Shopping', 'shopping', 'Malls, markets and where to buy Hai Phong specialities.' ),
		'the-thao' => array( 'Sports', 'sports', 'Stadiums, golf courses and places to train.' ),
		'van-tai'  => array( 'Transport', 'transport', 'Airport, railway station, piers and getting to Hai Phong.' ),
		'y-te'     => array( 'Healthcare', 'healthcare', 'Hospitals, clinics and medical information.' ),
		'giao-duc' => array( 'Education', 'education', 'Universities, schools and training centres.' ),
		'lam-dep'  => array( 'Beauty', 'beauty', 'Spas, hair and nail salons and beauty tips.' ),
	);
}

/**
 * Categories grouped under "Danh mục khác" in the header, by Vietnamese slug.
 *
 * @return string[]
 */
function thp_demo_more_categories() {
	return array( 'y-te', 'giao-duc', 'the-thao', 'lam-dep', 'mua-sam' );
}

/**
 * Vietnamese pages: slug => [title, content].
 *
 * @return array
 */
function thp_demo_pages() {
	$email = get_option( 'admin_email' );
	return array(
		'gioi-thieu'  => array(
			'Giới thiệu',
			'<p>Toplist Hải Phòng là cẩm nang số dành cho người yêu thành phố Cảng. Ban biên tập tổng hợp đánh giá thực tế, đi khảo sát và viết lại thành những bài review ngắn gọn, khách quan về địa điểm du lịch, quán ăn, chỗ nghỉ, quán cà phê và nơi vui chơi.</p><h2>Cách chúng tôi xếp hạng</h2><ul><li>Điểm số dựa trên đánh giá công khai của khách hàng và trải nghiệm của Ban biên tập.</li><li>Bảng Top 10 sắp xếp theo điểm, sau đó theo số lượt đánh giá.</li><li>Thông tin được cập nhật khi có thay đổi về giá, địa chỉ hoặc chất lượng dịch vụ.</li></ul>',
		),
		'gui-de-xuat' => array(
			'Gửi đề xuất địa điểm',
			'<p>Bạn biết một quán ngon, một homestay dễ thương hay một góc check-in mới ở Hải Phòng? Hãy gửi cho Ban biên tập để thẩm định và đưa vào Toplist.</p><h2>Thông tin cần gửi</h2><ul><li>Tên địa điểm và địa chỉ cụ thể.</li><li>Số điện thoại hoặc trang mạng xã hội (nếu có).</li><li>Khoảng giá và điều bạn thích nhất ở đó.</li><li>Một vài ảnh do bạn chụp.</li></ul><p>Gửi email tới <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>. Ban biên tập sẽ phản hồi trong vòng vài ngày.</p>',
		),
		'danh-muc'    => array(
			'Tất cả danh mục',
			'<p>Chọn một chủ đề để xem các địa điểm đã được review và xếp hạng.</p>',
		),
	);
}

/**
 * English pages keyed by Vietnamese slug: [slug, title, content].
 *
 * @return array
 */
function thp_demo_pages_en() {
	$email = get_option( 'admin_email' );
	return array(
		'gioi-thieu'  => array(
			'about',
			'About us',
			'<p>Toplist Hai Phong is a digital guide for people who love the Port City. Our editors gather real public reviews, visit places in person and turn them into short, honest reviews of sights, restaurants, stays, cafés and things to do.</p><h2>How we rank</h2><ul><li>Scores are based on public customer reviews and our editors’ own experience.</li><li>The Top 10 is sorted by score, then by number of reviews.</li><li>Information is updated when prices, addresses or service quality change.</li></ul>',
		),
		'gui-de-xuat' => array(
			'suggest-a-place',
			'Suggest a place',
			'<p>Know a great eatery, a lovely homestay or a new photo spot in Hai Phong? Send it to our editors to review and add to the Toplist.</p><h2>What to include</h2><ul><li>The name and exact address.</li><li>Phone number or social media page, if any.</li><li>Price range and what you like most about it.</li><li>A few photos you took.</li></ul><p>Email <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>. Our editors will reply within a few days.</p>',
		),
		'danh-muc'    => array(
			'all-categories',
			'All categories',
			'<p>Pick a topic to see the places we have reviewed and ranked.</p>',
		),
	);
}

/**
 * Category id by Vietnamese slug (ignores the Polylang language filter).
 *
 * @param string $slug Slug.
 * @return int
 */
function thp_demo_category_id( $slug ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'slug'       => $slug,
			'hide_empty' => false,
			'number'     => 1,
			'fields'     => 'ids',
			'lang'       => '',
		)
	);
	return $terms && ! is_wp_error( $terms ) ? (int) $terms[0] : 0;
}

/**
 * Author for imported posts: the current user, else the first administrator.
 *
 * @return int
 */
function thp_demo_author() {
	$id = get_current_user_id();
	if ( $id ) {
		return $id;
	}
	$admins = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => 'ID',
		)
	);
	return $admins ? (int) $admins[0] : 1;
}

/* ------------------------------------------------------------------ Steps */

/**
 * Site settings, Vietnamese categories and pages.
 *
 * @return string
 */
function thp_demo_step_setup() {
	global $wp_rewrite;

	update_option( 'blogname', 'Toplist Hải Phòng' );
	update_option( 'blogdescription', 'Cẩm nang du lịch, ẩm thực và lưu trú Hải Phòng' );
	update_option( 'timezone_string', 'Asia/Ho_Chi_Minh' );
	update_option( 'date_format', 'd/m/Y' );
	update_option( 'posts_per_page', 9 );
	$wp_rewrite->set_permalink_structure( '/%postname%/' );

	// Show "Ban Biên Tập" on cards instead of a bare login name.
	$author = get_userdata( thp_demo_author() );
	if ( $author && $author->display_name === $author->user_login ) {
		wp_update_user(
			array(
				'ID'           => $author->ID,
				'display_name' => 'Ban Biên Tập',
				'nickname'     => 'Ban Biên Tập',
			)
		);
	}

	foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
		$starter = get_page_by_path( $slug, OBJECT, $type );
		if ( $starter ) {
			wp_delete_post( $starter->ID, true );
		}
	}
	wp_update_term( (int) get_option( 'default_category' ), 'category', array( 'name' => 'Chưa phân loại' ) );

	foreach ( thp_demo_categories() as $slug => $cat ) {
		if ( ! thp_demo_category_id( $slug ) ) {
			wp_insert_term(
				$cat[0],
				'category',
				array(
					'slug'        => $slug,
					'description' => $cat[1],
				)
			);
		}
	}

	foreach ( thp_demo_pages() as $slug => $page ) {
		if ( ! get_page_by_path( $slug ) ) {
			wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_author'  => thp_demo_author(),
					'post_name'    => $slug,
					'post_title'   => $page[0],
					'post_content' => $page[1],
				)
			);
		}
	}
	return 'done';
}

/**
 * Vietnamese posts with place details (images come in the next step).
 *
 * @return string
 */
function thp_demo_step_posts() {
	$posts = thp_demo_data( 'posts.php' );
	$now   = time();
	// The source list is newest first, so date the posts backwards.
	foreach ( $posts as $index => $item ) {
		if ( get_page_by_path( $item['slug'], OBJECT, 'post' ) ) {
			continue;
		}
		$post_id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_author'   => thp_demo_author(),
				'post_name'     => $item['slug'],
				'post_title'    => $item['title'],
				'post_excerpt'  => $item['excerpt'],
				'post_content'  => $item['content'],
				'post_category' => array( thp_demo_category_id( $item['cat'] ) ),
				'tags_input'    => array_filter( array( $item['tag'] ) ),
				'post_date'     => wp_date( 'Y-m-d H:i:s', $now - ( $index + 1 ) * 3 * HOUR_IN_SECONDS ),
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		if ( '' !== $item['rating'] ) {
			update_post_meta( $post_id, '_thp_rating', $item['rating'] );
			update_post_meta( $post_id, '_thp_review_count', (string) $item['reviews'] );
		}
		foreach ( array( 'address', 'price', 'phone' ) as $field ) {
			if ( $item[ $field ] ) {
				update_post_meta( $post_id, '_thp_' . $field, $item[ $field ] );
			}
		}
		if ( $item['sticky'] ) {
			stick_post( $post_id );
		}
	}
	return 'done';
}

/**
 * Featured images, a few per request (image resizing is the slow part).
 *
 * @return string
 */
function thp_demo_step_images() {
	$batch = 3;
	$left  = 0;
	foreach ( thp_demo_data( 'posts.php' ) as $item ) {
		$post = $item['image'] ? get_page_by_path( $item['slug'], OBJECT, 'post' ) : null;
		if ( ! $post || has_post_thumbnail( $post ) ) {
			continue;
		}
		if ( $batch <= 0 ) {
			++$left;
			continue;
		}
		--$batch;
		$tmp = thp_demo_image_file( $item['image'] );
		if ( ! $tmp ) {
			continue;
		}
		$id = media_handle_sideload(
			array(
				'name'     => $item['image'],
				'tmp_name' => $tmp,
			),
			$post->ID,
			$item['title']
		);
		if ( ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_attachment_image_alt', $item['title'] );
			set_post_thumbnail( $post, $id );
		}
	}
	return $left ? 'again' : 'done';
}

/**
 * Vietnamese menus and menu locations.
 *
 * @return string
 */
function thp_demo_step_menus() {
	$page_ids = array();
	foreach ( array_keys( thp_demo_pages() ) as $slug ) {
		$page              = get_page_by_path( $slug );
		$page_ids[ $slug ] = $page ? $page->ID : 0;
	}
	$cat_items = array();
	foreach ( array_slice( array_keys( thp_demo_categories() ), 0, 5 ) as $slug ) {
		$cat_items[] = array( 'category', thp_demo_category_id( $slug ) );
	}
	$more_items = array();
	foreach ( thp_demo_more_categories() as $slug ) {
		$more_items[] = array( 'category', thp_demo_category_id( $slug ) );
	}

	set_theme_mod(
		'nav_menu_locations',
		array(
			'primary' => thp_demo_menu(
				'Menu chính',
				array_merge(
					array( array( 'custom', 'Trang chủ', home_url( '/' ) ) ),
					$cat_items,
					array( array( 'custom', 'Danh mục khác', get_permalink( $page_ids['danh-muc'] ), $more_items ) )
				)
			),
			'footer'  => thp_demo_menu( 'Khám phá', array_merge( $cat_items, array( array( 'page', $page_ids['danh-muc'] ) ) ) ),
			'portal'  => thp_demo_menu(
				'Liên hệ',
				array(
					array( 'page', $page_ids['gioi-thieu'] ),
					array( 'page', $page_ids['gui-de-xuat'] ),
					array( 'page', $page_ids['danh-muc'] ),
				)
			),
		)
	);
	flush_rewrite_rules();
	return 'done';
}

/**
 * Polylang languages: Vietnamese (default) and English. Skipped without Polylang.
 *
 * @return string
 */
function thp_demo_step_languages() {
	if ( ! class_exists( 'PLL_Admin_Model' ) || ! class_exists( 'WP_Syntex\\Polylang\\Options\\Options' ) ) {
		return 'skipped';
	}
	$model = new PLL_Admin_Model( new WP_Syntex\Polylang\Options\Options() );
	$langs = array(
		array(
			'locale'         => 'vi',
			'slug'           => 'vi',
			'name'           => 'Tiếng Việt',
			'flag'           => 'vn',
			'term_group'     => 0,
			'no_default_cat' => true,
		),
		array(
			'locale'         => 'en_US',
			'slug'           => 'en',
			'name'           => 'English',
			'flag'           => 'gb',
			'term_group'     => 1,
			'no_default_cat' => true,
		),
	);
	foreach ( $langs as $lang ) {
		if ( ! $model->languages->get( $lang['slug'] ) ) {
			$model->languages->add( $lang );
		}
	}
	return 'done';
}

/**
 * English translations of categories, tags, posts, pages and menus. Runs in a
 * request after the languages step, once Polylang has loaded its API.
 *
 * @return string
 */
function thp_demo_step_english() {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'PLL' ) ) {
		return 'skipped';
	}

	// Mark everything that has no language yet as Vietnamese.
	$ids = get_posts(
		array(
			'post_type'      => array( 'post', 'page' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'lang'           => '',
		)
	);
	foreach ( $ids as $id ) {
		if ( ! pll_get_post_language( $id ) ) {
			pll_set_post_language( $id, 'vi' );
		}
	}
	$terms = get_terms(
		array(
			'taxonomy'   => array( 'category', 'post_tag' ),
			'hide_empty' => false,
			'lang'       => '',
		)
	);
	foreach ( $terms as $term ) {
		if ( (int) $term->term_id !== (int) get_option( 'default_category' ) && ! pll_get_term_language( $term->term_id ) ) {
			pll_set_term_language( $term->term_id, 'vi' );
		}
	}

	$en_cats = array();
	foreach ( thp_demo_categories_en() as $vi_slug => $cat ) {
		$vi = get_term( thp_demo_category_id( $vi_slug ), 'category' );
		if ( $vi instanceof WP_Term ) {
			$en_cats[ $vi_slug ] = thp_demo_en_term( $vi, $cat[0], $cat[1], $cat[2] );
		}
	}

	$posts_en  = thp_demo_data( 'posts-en.php' );
	$en_sticky = array();
	foreach ( thp_demo_data( 'posts.php' ) as $item ) {
		$vi = get_page_by_path( $item['slug'], OBJECT, 'post' );
		if ( ! $vi || empty( $posts_en[ $item['slug'] ] ) ) {
			continue;
		}
		$existing = pll_get_post( $vi->ID, 'en' );
		if ( $existing ) {
			if ( $item['sticky'] ) {
				$en_sticky[] = (int) $existing;
			}
			continue;
		}
		$en   = $posts_en[ $item['slug'] ];
		$tags = array();
		foreach ( wp_get_post_tags( $vi->ID ) as $vi_tag ) {
			$name   = thp_demo_en_tag_name( $vi_tag->name );
			$tag_id = thp_demo_en_term( $vi_tag, $name, sanitize_title( $name ) );
			if ( $tag_id ) {
				$tags[] = $tag_id;
			}
		}
		$en_id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_author'   => $vi->post_author,
				'post_name'     => $en['slug'],
				'post_title'    => $en['title'],
				'post_excerpt'  => $en['excerpt'],
				'post_content'  => $en['content'],
				'post_date'     => $vi->post_date,
				'post_category' => array( $en_cats[ $item['cat'] ] ),
			),
			true
		);
		if ( is_wp_error( $en_id ) ) {
			continue;
		}
		pll_set_post_language( $en_id, 'en' );
		wp_set_post_terms( $en_id, $tags, 'post_tag' );
		pll_save_post_translations(
			array(
				'vi' => $vi->ID,
				'en' => $en_id,
			)
		);
		foreach ( array( '_thp_rating', '_thp_review_count', '_thp_address', '_thp_price', '_thp_phone' ) as $key ) {
			$value = '_thp_price' === $key && ! empty( $en['price'] ) ? $en['price'] : get_post_meta( $vi->ID, $key, true );
			if ( '' !== $value ) {
				update_post_meta( $en_id, $key, $value );
			}
		}
		$thumb = get_post_thumbnail_id( $vi->ID );
		if ( $thumb ) {
			set_post_thumbnail( $en_id, $thumb );
		}
		if ( $item['sticky'] ) {
			$en_sticky[] = (int) $en_id;
		}
	}
	thp_demo_add_sticky( $en_sticky );

	$page_en_ids = array();
	foreach ( thp_demo_pages_en() as $vi_slug => $page ) {
		$vi = get_page_by_path( $vi_slug );
		if ( ! $vi ) {
			continue;
		}
		$existing = pll_get_post( $vi->ID, 'en' );
		if ( $existing ) {
			$page_en_ids[ $vi_slug ] = (int) $existing;
			continue;
		}
		$en_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_author'  => $vi->post_author,
				'post_name'    => $page[0],
				'post_title'   => $page[1],
				'post_content' => $page[2],
			)
		);
		pll_set_post_language( $en_id, 'en' );
		pll_save_post_translations(
			array(
				'vi' => $vi->ID,
				'en' => $en_id,
			)
		);
		$page_en_ids[ $vi_slug ] = $en_id;
	}

	// English menus, then per-language menu locations in Polylang's options.
	$cat_items = array();
	foreach ( array_slice( $en_cats, 0, 5 ) as $id ) {
		$cat_items[] = array( 'category', $id );
	}
	$more_items = array();
	foreach ( thp_demo_more_categories() as $slug ) {
		$more_items[] = array( 'category', $en_cats[ $slug ] );
	}
	$en_menus = array(
		'primary' => thp_demo_menu(
			'Main menu',
			array_merge(
				array( array( 'custom', 'Home', pll_home_url( 'en' ) ) ),
				$cat_items,
				array( array( 'custom', 'More categories', get_permalink( $page_en_ids['danh-muc'] ), $more_items ) )
			)
		),
		'footer'  => thp_demo_menu( 'Explore', array_merge( $cat_items, array( array( 'page', $page_en_ids['danh-muc'] ) ) ) ),
		'portal'  => thp_demo_menu(
			'Contact',
			array(
				array( 'page', $page_en_ids['gioi-thieu'] ),
				array( 'page', $page_en_ids['gui-de-xuat'] ),
				array( 'page', $page_en_ids['danh-muc'] ),
			)
		),
	);
	// Vietnamese menus are looked up by name: with Polylang active the
	// nav_menu_locations theme mod is filtered and reads back empty here.
	$vi_menus  = array(
		'primary' => 'Menu chính',
		'footer'  => 'Khám phá',
		'portal'  => 'Liên hệ',
	);
	$nav_menus = array();
	foreach ( $en_menus as $location => $menu_id ) {
		$vi_menu = wp_get_nav_menu_object( $vi_menus[ $location ] );

		$nav_menus[ get_stylesheet() ][ $location ] = array(
			'vi' => $vi_menu ? (int) $vi_menu->term_id : 0,
			'en' => $menu_id,
		);
	}
	PLL()->options->set( 'nav_menus', $nav_menus );
	PLL()->options->save();

	$en_lang = PLL()->model->get_language( 'en' );
	if ( $en_lang && class_exists( 'PLL_MO' ) ) {
		$mo = new PLL_MO();
		$mo->import_from_db( $en_lang );
		$mo->add_entry( $mo->make_entry( get_option( 'blogdescription' ), 'Your guide to travel, food and stays in Hai Phong' ) );
		$mo->export_to_db( $en_lang );
	}

	flush_rewrite_rules();
	delete_transient( 'thp_total_reviews_vi' );
	delete_transient( 'thp_total_reviews_en' );
	return 'done';
}

/* ---------------------------------------------------------------- Helpers */

/**
 * Temporary copy of a sample image: the bundled file when present, otherwise
 * downloaded from its original URL.
 *
 * @param string $name Image file name.
 * @return string Temp file path, '' on failure.
 */
function thp_demo_image_file( $name ) {
	$local = THP_DEMO_DIR . 'data/images/' . $name;
	if ( file_exists( $local ) ) {
		$tmp = wp_tempnam( $name );
		copy( $local, $tmp );
		return $tmp;
	}
	$sources = thp_demo_data( 'image-sources.php' );
	if ( empty( $sources[ $name ] ) ) {
		return '';
	}
	$tmp = download_url( $sources[ $name ], 60 );
	return is_wp_error( $tmp ) ? '' : $tmp;
}

/**
 * Find or create an English term linked to a Vietnamese one.
 *
 * @param WP_Term $vi   Vietnamese term.
 * @param string  $name English name.
 * @param string  $slug English slug.
 * @param string  $desc English description.
 * @return int English term id, 0 on failure.
 */
function thp_demo_en_term( $vi, $name, $slug, $desc = '' ) {
	$existing = pll_get_term( $vi->term_id, 'en' );
	if ( $existing ) {
		return (int) $existing;
	}
	$created = wp_insert_term(
		$name,
		$vi->taxonomy,
		array(
			'slug'        => $slug,
			'description' => $desc,
		)
	);
	if ( is_wp_error( $created ) ) {
		return 0;
	}
	pll_set_term_language( $created['term_id'], 'en' );
	pll_save_term_translations(
		array(
			'vi' => $vi->term_id,
			'en' => $created['term_id'],
		)
	);
	return (int) $created['term_id'];
}

/**
 * English name for a Vietnamese ward/zone tag: "Phường Hồng Bàng" → "Hong Bang Ward".
 *
 * @param string $name Vietnamese tag name.
 * @return string
 */
function thp_demo_en_tag_name( $name ) {
	$plain = remove_accents( $name );
	if ( 0 === strpos( $plain, 'Phuong ' ) ) {
		return substr( $plain, 7 ) . ' Ward';
	}
	if ( 0 === strpos( $plain, 'Dac khu ' ) ) {
		return substr( $plain, 8 ) . ' Special Zone';
	}
	return $plain;
}

/**
 * Add posts to the sticky list. Polylang filters the sticky_posts option by
 * language, so the stored value is read and written directly.
 *
 * @param int[] $ids Post ids.
 */
function thp_demo_add_sticky( $ids ) {
	global $wpdb;
	if ( ! $ids ) {
		return;
	}
	$raw    = $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name = 'sticky_posts'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$sticky = array_values( array_unique( array_map( 'intval', array_merge( (array) maybe_unserialize( $raw ), $ids ) ) ) );
	$wpdb->update( $wpdb->options, array( 'option_value' => maybe_serialize( $sticky ) ), array( 'option_name' => 'sticky_posts' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	wp_cache_delete( 'alloptions', 'options' );
	wp_cache_delete( 'sticky_posts', 'options' );
}

/**
 * Create a nav menu and fill it, or return the existing one.
 *
 * @param string $name  Menu name.
 * @param array  $items Items: ['custom', title, url, children?] | ['category', id] | ['page', id].
 * @return int Menu id.
 */
function thp_demo_menu( $name, $items ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		return (int) $menu->term_id;
	}
	$menu_id = wp_create_nav_menu( $name );
	thp_demo_menu_items( $menu_id, $items, 0 );
	return $menu_id;
}

/**
 * Add items (and nested children) to a menu.
 *
 * @param int   $menu_id Menu id.
 * @param array $items   Items, see thp_demo_menu().
 * @param int   $parent  Parent menu item id.
 */
function thp_demo_menu_items( $menu_id, $items, $parent ) {
	foreach ( $items as $item ) {
		$args = array(
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
		);
		if ( 'custom' === $item[0] ) {
			$args += array(
				'menu-item-type'  => 'custom',
				'menu-item-title' => $item[1],
				'menu-item-url'   => $item[2],
			);
		} else {
			$args += array(
				'menu-item-type'      => 'category' === $item[0] ? 'taxonomy' : 'post_type',
				'menu-item-object'    => $item[0],
				'menu-item-object-id' => $item[1],
			);
		}
		$item_id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( ! empty( $item[3] ) && ! is_wp_error( $item_id ) ) {
			thp_demo_menu_items( $menu_id, $item[3], $item_id );
		}
	}
}
