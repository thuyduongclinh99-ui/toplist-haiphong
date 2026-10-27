<?php
/**
 * Generic listing: archives, search results and the posts page.
 *
 * @package toplist-haiphong
 */

get_header();

$thp_search = is_search();
$thp_found  = number_format_i18n( $wp_query->found_posts );

if ( $thp_search ) {
	$thp_state = thp_filter_state();
	$thp_names = array();
	foreach ( $thp_state['cats'] as $thp_id ) {
		$thp_term    = get_term( $thp_id, 'category' );
		$thp_names[] = $thp_term instanceof WP_Term ? $thp_term->name : '';
	}
	foreach ( $thp_state['tags'] as $thp_slug ) {
		$thp_term    = get_term_by( 'slug', $thp_slug, 'post_tag' );
		$thp_names[] = $thp_term instanceof WP_Term ? $thp_term->name : '';
	}
	// Readable filter summary such as "Ẩm Thực, Phường Gia Viên".
	$thp_scope = implode( ', ', array_filter( $thp_names ) );
	$thp_title = sprintf( thp__( 'Kết quả cho “%s”' ), get_search_query( false ) );
	$thp_desc  = $thp_scope
		? sprintf( thp__( 'Tìm thấy %1$s bài viết trong %2$s.' ), $thp_found, $thp_scope )
		: sprintf( thp__( 'Tìm thấy %s bài viết phù hợp.' ), $thp_found );
	$thp_icon  = 'search';
} elseif ( is_archive() ) {
	$thp_title = single_term_title( '', false );
	$thp_title = $thp_title ? $thp_title : wp_strip_all_tags( get_the_archive_title() );
	$thp_desc  = wp_strip_all_tags( get_the_archive_description() );
	$thp_desc  = $thp_desc ? $thp_desc : sprintf( thp__( '%s bài viết đã được Ban biên tập review và xếp hạng.' ), $thp_found );
	$thp_icon  = is_tag() ? 'location_on' : ( is_category() ? thp_category_icon( get_queried_object() ) : 'category' );
} else {
	$thp_title = thp__( 'Bài viết mới nhất' );
	$thp_desc  = thp__( 'Cẩm nang du lịch, ẩm thực và lưu trú Hải Phòng.' );
	$thp_icon  = 'auto_stories';
}
?>
<div class="max-w-7xl mx-auto px-4 sm:px-margin py-space-md w-full">
	<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
		<?php if ( $thp_search ) : ?>
			<aside class="lg:col-span-3 rounded-2xl bg-surface-container-lowest p-5 shadow-sm">
				<?php // On phones the panel starts collapsed behind a toggle; from lg it is always open. ?>
				<input type="checkbox" id="thp-filter-toggle" class="peer sr-only"<?php echo ( thp_filter_state()['cats'] || thp_filter_state()['tags'] ) ? ' checked' : ''; ?>>
				<label for="thp-filter-toggle" class="lg:hidden flex cursor-pointer items-center gap-2 text-label-lg font-extrabold uppercase tracking-wide text-on-surface peer-checked:[&>.thp-chevron]:rotate-180">
					<?php echo thp_icon( 'filter_alt', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php thp_e( 'Bộ lọc tìm kiếm' ); ?>
					<?php echo thp_icon( 'expand_more', 'thp-chevron ml-auto text-[22px] transition-transform' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</label>
				<div class="hidden peer-checked:block lg:block mt-4 lg:mt-0">
					<?php get_template_part( 'template-parts/filters' ); ?>
				</div>
			</aside>
		<?php endif; ?>
		<section class="<?php echo $thp_search ? 'lg:col-span-9' : 'lg:col-span-8'; ?> flex flex-col gap-space-lg min-w-0">
			<header class="relative overflow-hidden rounded-2xl bg-surface-container-low p-6 sm:p-8 shadow-sm flex flex-col gap-3">
				<?php thp_breadcrumb(); ?>
				<div class="flex items-center gap-3">
					<span class="w-11 h-11 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-sm shrink-0"><?php echo thp_icon( $thp_icon, 'text-[24px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h1 class="text-[26px] leading-8 sm:text-headline-lg font-bold tracking-tight text-on-surface"><?php echo esc_html( $thp_title ); ?></h1>
				</div>
				<p class="text-body-md text-on-surface-variant max-w-2xl"><?php echo esc_html( $thp_desc ); ?></p>
				<?php if ( is_search() ) : ?>
					<div class="hidden lg:block max-w-lg mt-2"><?php get_search_form(); ?></div>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
					<?php
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/card' );
					}
					?>
				</div>
				<?php thp_pagination(); ?>
			<?php else : ?>
				<div class="rounded-2xl bg-surface-container-lowest p-8 text-center shadow-sm flex flex-col items-center gap-3">
					<?php echo thp_icon( 'travel_explore', 'text-[48px] text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h2 class="text-headline-sm font-bold"><?php thp_e( 'Chưa có bài viết nào' ); ?></h2>
					<p class="text-body-md text-on-surface-variant"><?php thp_e( 'Thử từ khóa hoặc danh mục khác, hoặc quay lại trang chủ để khám phá các danh mục nổi bật.' ); ?></p>
					<a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary text-label-lg hover:bg-primary-container" href="<?php echo esc_url( thp_home_url() ); ?>"><?php thp_e( 'Về trang chủ' ); ?></a>
				</div>
			<?php endif; ?>
		</section>

		<?php
		if ( ! $thp_search ) {
			get_sidebar();
		}
		?>
	</div>
</div>
<?php
get_footer();
