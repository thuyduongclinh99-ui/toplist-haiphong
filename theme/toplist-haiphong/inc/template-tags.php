<?php
/**
 * Small rendering helpers shared by templates.
 *
 * @package toplist-haiphong
 */

defined( 'ABSPATH' ) || exit;

/**
 * Material Symbols icon.
 *
 * @param string $name  Icon ligature name.
 * @param string $class Extra classes.
 * @param bool   $fill  Filled variant.
 * @return string
 */
function thp_icon( $name, $class = 'text-[18px]', $fill = false ) {
	return sprintf(
		'<span class="material-symbols-outlined%s %s" aria-hidden="true">%s</span>',
		$fill ? ' fill' : '',
		esc_attr( $class ),
		esc_html( $name )
	);
}

/**
 * Site logo: Customizer logo if set, otherwise the bundled one.
 *
 * @param string $class Image classes.
 */
function thp_logo( $class = 'h-12 w-auto' ) {
	$name = get_bloginfo( 'name' );
	$id   = get_theme_mod( 'custom_logo' );
	echo '<a class="flex items-center shrink-0" href="' . esc_url( thp_home_url() ) . '" rel="home">';
	if ( $id ) {
		echo wp_get_attachment_image(
			$id,
			'medium',
			false,
			array(
				'class' => $class . ' object-contain',
				'alt'   => $name,
			)
		);
	} else {
		printf( '<img class="%s object-contain" src="%s" alt="%s" width="420" height="120">', esc_attr( $class ), esc_url( THP_URI . '/assets/images/logo.jpg' ), esc_attr( $name ) );
	}
	echo '<span class="screen-reader-text">' . esc_html( $name ) . '</span></a>';
}

/**
 * First category of a post.
 *
 * @param int|null $post_id Post id.
 * @return WP_Term|null
 */
function thp_primary_category( $post_id = null ) {
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0] : null;
}

/**
 * Short place name from a post title: the part before the first colon,
 * without a leading "Review". "Hải đăng Long Châu: hành trình…" → "Hải đăng Long Châu".
 *
 * @param WP_Post|int $post Post.
 * @return string
 */
function thp_place_name( $post ) {
	$title = get_the_title( $post );
	$name  = trim( strtok( $title, ':' ) );
	$name  = preg_replace( '/^Review\s+/iu', '', $name );
	return '' !== $name ? $name : $title;
}

/**
 * Material icon standing in for a missing image, chosen by the category's
 * Vietnamese slug (translated categories resolve to their Vietnamese source).
 *
 * @param WP_Term|null $term Category.
 * @return string Icon name.
 */
function thp_category_icon( $term ) {
	if ( $term && function_exists( 'pll_get_term' ) ) {
		$source = pll_get_term( $term->term_id, 'vi' );
		if ( $source && $source !== $term->term_id ) {
			$term = get_term( $source, 'category' );
		}
	}
	$icons = array(
		'du-lich'  => 'landscape',
		'am-thuc'  => 'restaurant',
		'luu-tru'  => 'hotel',
		'cafe'     => 'local_cafe',
		'giai-tri' => 'theater_comedy',
		'mua-sam'  => 'shopping_bag',
		'the-thao' => 'sports_soccer',
		'van-tai'  => 'flight',
		'y-te'     => 'local_hospital',
		'giao-duc' => 'school',
		'lam-dep'  => 'spa',
	);
	return $term && isset( $icons[ $term->slug ] ) ? $icons[ $term->slug ] : 'photo';
}

/**
 * Accent class sets. Kept as full literals so Tailwind can see them.
 *
 * @param string $accent primary|secondary|tertiary.
 * @return array
 */
function thp_accent( $accent ) {
	$map = array(
		'primary'   => array(
			'bar'    => 'bg-primary',
			'chip'   => 'bg-primary text-on-primary',
			'link'   => 'text-primary hover:text-primary-container',
			'avatar' => 'bg-primary-fixed text-primary',
		),
		'secondary' => array(
			'bar'    => 'bg-secondary',
			'chip'   => 'bg-secondary text-on-secondary',
			'link'   => 'text-secondary hover:text-secondary-container',
			'avatar' => 'bg-secondary-fixed text-secondary',
		),
		'tertiary'  => array(
			'bar'    => 'bg-tertiary',
			'chip'   => 'bg-tertiary text-on-tertiary',
			'link'   => 'text-tertiary hover:text-tertiary-container',
			'avatar' => 'bg-tertiary-fixed text-tertiary',
		),
	);
	return isset( $map[ $accent ] ) ? $map[ $accent ] : $map['primary'];
}

/**
 * Rating value formatted with one decimal, or '' if unset.
 *
 * @param int|null $post_id Post id.
 * @return string
 */
function thp_rating( $post_id = null ) {
	$value = get_post_meta( $post_id ? $post_id : get_the_ID(), '_thp_rating', true );
	return '' === $value ? '' : number_format_i18n( (float) $value, 1 );
}

/**
 * Review count.
 *
 * @param int|null $post_id Post id.
 * @return int
 */
function thp_review_count( $post_id = null ) {
	return (int) get_post_meta( $post_id ? $post_id : get_the_ID(), '_thp_review_count', true );
}

/**
 * Compact number: 19200 -> "19,2k".
 *
 * @param int $n Number.
 * @return string
 */
function thp_compact_number( $n ) {
	if ( $n >= 1000 ) {
		return number_format_i18n( $n / 1000, $n >= 10000 ? 0 : 1 ) . 'k';
	}
	return number_format_i18n( $n );
}

/**
 * Estimated reading time in minutes (200 words per minute).
 *
 * @param int|null $post_id Post id.
 * @return int
 */
function thp_reading_minutes( $post_id = null ) {
	$text  = wp_strip_all_tags( get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() ) );
	$words = count( preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Views stored by the view counter.
 *
 * @param int|null $post_id Post id.
 * @return int
 */
function thp_views( $post_id = null ) {
	return (int) get_post_meta( $post_id ? $post_id : get_the_ID(), '_thp_views', true );
}

/**
 * Two-letter initials for author avatars.
 *
 * @param string $name Display name.
 * @return string
 */
function thp_initials( $name ) {
	$parts = preg_split( '/\s+/u', trim( $name ), -1, PREG_SPLIT_NO_EMPTY );
	if ( ! $parts ) {
		return '';
	}
	$first = mb_substr( $parts[0], 0, 1 );
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : mb_substr( $parts[0], 1, 1 );
	return mb_strtoupper( $first . $last );
}

/**
 * Google Maps search link for an address.
 *
 * @param string $address Address.
 * @return string
 */
function thp_maps_url( $address ) {
	return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address );
}

/**
 * tel: link target from a display phone number.
 *
 * @param string $phone Phone.
 * @return string
 */
function thp_tel( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * Rating pill used on cards and hero.
 *
 * @param int|null $post_id Post id.
 * @param string   $size    'sm' for cards, 'lg' for hero.
 */
function thp_rating_pill( $post_id = null, $size = 'sm' ) {
	$rating = thp_rating( $post_id );
	if ( '' === $rating ) {
		return;
	}
	$count = thp_review_count( $post_id );
	if ( 'lg' === $size ) {
		$stars = (int) round( (float) get_post_meta( $post_id, '_thp_rating', true ) );
		echo '<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest/90 backdrop-blur-md text-on-surface text-label-sm shadow-sm"><span class="flex text-amber-500">';
		for ( $i = 1; $i <= 5; $i++ ) {
			echo thp_icon( 'star', 'text-[14px]' . ( $i > $stars ? ' text-outline-variant' : '' ), true ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		printf(
			'</span><span class="font-bold">%s/5</span><span class="text-on-surface-variant font-medium">(%s)</span></div>',
			esc_html( $rating ),
			esc_html( sprintf( thp__( '%s đánh giá' ), number_format_i18n( $count ) ) )
		);
		return;
	}
	printf(
		'<div class="px-2 py-0.5 rounded-lg bg-surface-container-lowest/90 backdrop-blur-md text-on-surface text-[11px] font-semibold flex items-center gap-1 shadow-sm">%s<span class="font-bold">%s/5</span><span class="text-[10px] text-on-surface-variant">(%s)</span></div>',
		thp_icon( 'star', 'text-[13px] text-amber-500', true ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( $rating ),
		esc_html( number_format_i18n( $count ) )
	);
}

/**
 * Section heading with coloured bar, as in the design.
 *
 * @param string $title    Heading.
 * @param string $subtitle Sub text.
 * @param string $accent   Accent name.
 * @param string $topic    Topic name shown in the chip.
 * @param string $link     "Xem tất cả" URL.
 */
function thp_section_heading( $title, $subtitle = '', $accent = 'primary', $topic = '', $link = '' ) {
	$a = thp_accent( $accent );
	?>
	<div class="flex items-center justify-between gap-4 pb-2 border-b border-surface-container-high">
		<div class="flex items-center gap-2.5 min-w-0">
			<span class="w-2.5 h-6 <?php echo esc_attr( $a['bar'] ); ?> rounded-full inline-block shrink-0"></span>
			<div class="min-w-0">
				<div class="flex flex-wrap items-center gap-2">
					<?php if ( $topic ) : ?>
						<span class="px-2 py-0.5 rounded <?php echo esc_attr( $a['chip'] ); ?> text-[11px] font-bold uppercase whitespace-nowrap"><?php echo esc_html( sprintf( thp__( 'Chủ đề: %s' ), $topic ) ); ?></span>
					<?php endif; ?>
					<h2 class="text-headline-sm sm:text-headline-md text-on-surface font-bold tracking-tight"><?php echo esc_html( $title ); ?></h2>
				</div>
				<?php if ( $subtitle ) : ?>
					<p class="text-body-sm text-on-surface-variant mt-0.5"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $link ) : ?>
			<a href="<?php echo esc_url( $link ); ?>" class="hidden sm:inline-flex items-center gap-1 shrink-0 <?php echo esc_attr( $a['link'] ); ?> text-label-md font-bold transition-colors">
				<span><?php thp_e( 'Xem tất cả' ); ?></span><?php echo thp_icon( 'arrow_forward' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Breadcrumb trail: Trang chủ › Category › Title.
 */
function thp_breadcrumb() {
	$items = array( array( thp_home_url(), thp__( 'Trang chủ' ) ) );
	if ( is_singular( 'post' ) ) {
		$term = thp_primary_category();
		if ( $term ) {
			$items[] = array( get_category_link( $term ), $term->name );
		}
		$items[] = array( '', get_the_title() );
	} elseif ( is_search() ) {
		// Checked before categories: a search filtered with ?cat= is also is_category().
		$items[] = array( '', thp__( 'Tìm kiếm' ) );
	} elseif ( is_category() || is_tag() ) {
		$items[] = array( '', single_term_title( '', false ) );
	} elseif ( is_page() ) {
		$items[] = array( '', get_the_title() );
	}
	echo '<nav aria-label="Breadcrumb" class="text-label-md text-on-surface-variant"><ol class="flex flex-wrap items-center gap-1">';
	foreach ( $items as $i => $item ) {
		if ( $i ) {
			echo '<li aria-hidden="true">' . thp_icon( 'chevron_right', 'text-[16px] text-outline' ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $item[0]
			? '<li><a class="hover:text-primary transition-colors" href="' . esc_url( $item[0] ) . '">' . esc_html( $item[1] ) . '</a></li>'
			: '<li class="text-on-surface font-semibold truncate max-w-[60vw]" aria-current="page">' . esc_html( $item[1] ) . '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Search filters of the current request. Several categories or locations can be
 * ticked at once; WordPress matches any of them (?cat=3,5&tag=a,b).
 *
 * @return array { s: string, cats: int[], tags: string[] }
 */
function thp_filter_state() {
	global $wp;
	// Read the request vars: WP_Query rewrites 'cat' to a single id while parsing.
	$vars = $wp instanceof WP ? $wp->query_vars : array();
	$cat  = isset( $vars['cat'] ) ? (string) $vars['cat'] : '';
	$tag  = isset( $vars['tag'] ) ? (string) $vars['tag'] : '';
	return array(
		's'    => get_search_query( false ),
		'cats' => array_values( array_filter( array_map( 'absint', explode( ',', $cat ) ) ) ),
		'tags' => array_values( array_filter( array_map( 'sanitize_title', explode( ',', $tag ) ) ) ),
	);
}

/**
 * Search URL for a filter combination, keeping the current keyword.
 *
 * @param int[]    $cats Category ids.
 * @param string[] $tags Location tag slugs.
 * @return string
 */
function thp_filter_url( $cats, $tags ) {
	return add_query_arg(
		array_filter(
			array(
				's'   => rawurlencode( get_search_query( false ) ),
				'cat' => implode( ',', $cats ),
				'tag' => implode( ',', $tags ),
			),
			'strlen'
		),
		thp_home_url()
	);
}

/**
 * Checkbox-style filter row: a link that ticks or unticks one value.
 *
 * @param string $label   Text.
 * @param string $url     URL with the value toggled.
 * @param bool   $checked Whether the value is currently ticked.
 * @param int    $count   Post count.
 */
function thp_filter_item( $label, $url, $checked, $count ) {
	printf(
		'<a class="group/item flex items-center gap-2.5 py-1.5 text-body-sm %s" href="%s" role="checkbox" aria-checked="%s"><span class="flex w-4 h-4 shrink-0 items-center justify-center rounded-sm border %s">%s</span><span class="flex-1 min-w-0 truncate">%s</span><span class="text-label-md text-outline">%d</span></a>',
		$checked ? 'text-secondary font-semibold' : 'text-on-surface hover:text-secondary',
		esc_url( $url ),
		$checked ? 'true' : 'false',
		$checked ? 'bg-secondary border-secondary text-on-secondary' : 'bg-surface-container-lowest border-outline-variant group-hover/item:border-secondary',
		$checked ? thp_icon( 'check', 'text-[13px] font-bold' ) : '', // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( $label ),
		(int) $count
	);
}

/**
 * Numbered pagination for archives.
 */
function thp_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => thp_icon( 'chevron_left' ) . '<span class="screen-reader-text">' . esc_html( thp__( 'Trang trước' ) ) . '</span>',
			'next_text'          => thp_icon( 'chevron_right' ) . '<span class="screen-reader-text">' . esc_html( thp__( 'Trang sau' ) ) . '</span>',
			'screen_reader_text' => thp__( 'Phân trang' ),
			'class'              => 'pagination mt-space-xl',
		)
	);
}

/**
 * Fallback for the primary menu: the five biggest categories as links, the
 * rest under a "Danh mục khác" dropdown.
 *
 * @param array $args wp_nav_menu args.
 */
function thp_menu_fallback( $args ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'orderby'    => 'count',
			'order'      => 'DESC',
			'hide_empty' => true,
			'exclude'    => array( (int) get_option( 'default_category' ) ),
		)
	);
	$terms = is_wp_error( $terms ) ? array() : $terms;
	$link  = function ( $term ) {
		$current = is_category( $term->term_id ) ? 'current-menu-item' : '';
		return '<li class="' . esc_attr( $current ) . '"><a href="' . esc_url( get_category_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
	};

	echo '<ul class="' . esc_attr( $args['menu_class'] ) . '">';
	echo '<li class="' . ( is_front_page() ? 'current-menu-item' : '' ) . '"><a href="' . esc_url( thp_home_url() ) . '">' . esc_html( thp__( 'Trang chủ' ) ) . '</a></li>';
	foreach ( array_slice( $terms, 0, 5 ) as $term ) {
		echo $link( $term ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $link.
	}
	$more = array_slice( $terms, 5 );
	if ( $more ) {
		$all = thp_page_url( 'danh-muc' );
		echo '<li class="menu-item-has-children"><a href="' . esc_url( $all ? $all : '#' ) . '">' . esc_html( thp__( 'Danh mục khác' ) ) . thp_icon( 'expand_more', 'text-[18px]' ) . '</a><ul class="sub-menu">'; // phpcs:ignore WordPress.Security.EscapeOutput
		foreach ( $more as $term ) {
			echo $link( $term ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $link.
		}
		echo '</ul></li>';
	}
	echo '</ul>';
}
