<?php
/**
 * Plain search box in the hero-search look. A new keyword starts a fresh
 * search; filters are picked on the results page.
 *
 * @package toplist-haiphong
 */

$thp_id = wp_unique_id( 'thp-search-' );
?>
<form role="search" method="get" class="relative w-full" action="<?php echo esc_url( thp_home_url() ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $thp_id ); ?>"><?php thp_e( 'Tìm kiếm' ); ?></label>
	<span class="absolute left-3 top-1/2 -translate-y-1/2 text-primary pointer-events-none"><?php echo thp_icon( 'search', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	<input id="<?php echo esc_attr( $thp_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low rounded-xl text-body-sm text-on-surface placeholder:text-outline border border-transparent focus:outline-none focus:bg-surface-container-lowest focus:border-primary focus:ring-4 focus:ring-primary/15 transition-all" placeholder="<?php echo esc_attr( thp__( 'Tìm địa điểm, quán ăn, khách sạn tại Hải Phòng...' ) ); ?>">
</form>
