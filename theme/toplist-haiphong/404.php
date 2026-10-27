<?php
/**
 * Not found.
 *
 * @package toplist-haiphong
 */

get_header();
?>
<div class="max-w-3xl mx-auto px-4 sm:px-margin py-16 w-full">
	<div class="rounded-2xl bg-surface-container-lowest p-8 sm:p-12 shadow-sm text-center flex flex-col items-center gap-4">
		<span class="text-[72px] leading-none font-extrabold text-primary">404</span>
		<h1 class="text-headline-md font-bold"><?php thp_e( 'Không tìm thấy trang' ); ?></h1>
		<p class="text-body-md text-on-surface-variant max-w-md"><?php thp_e( 'Đường dẫn có thể đã thay đổi hoặc địa điểm không còn. Hãy thử tìm kiếm bên dưới.' ); ?></p>
		<div class="w-full max-w-md"><?php get_search_form(); ?></div>
		<a class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-secondary text-on-secondary text-label-lg shadow-md hover:bg-secondary-container" href="<?php echo esc_url( thp_home_url() ); ?>"><?php echo thp_icon( 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php thp_e( 'Về trang chủ' ); ?></a>
	</div>
</div>
<?php
get_footer();
