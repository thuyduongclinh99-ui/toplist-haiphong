<?php
/**
 * Site header: sticky glass bar, search, primary menu, language switcher and trending strip.
 *
 * @package toplist-haiphong
 */

$thp_submit = thp_submit_url();
$thp_langs  = thp_languages();
$thp_latest = get_posts(
	array(
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
	)
);

/**
 * VI / EN pill switcher.
 *
 * @param array  $langs Languages from thp_languages().
 * @param string $class Wrapper classes.
 */
$thp_switcher = function ( $langs, $class ) {
	if ( count( $langs ) < 2 ) {
		return;
	}
	echo '<nav class="' . esc_attr( $class ) . ' items-center rounded-lg bg-surface-container-low p-0.5" aria-label="' . esc_attr( thp__( 'Ngôn ngữ' ) ) . '">';
	foreach ( $langs as $lang ) {
		printf(
			'<a href="%s" hreflang="%s" lang="%s" class="px-2 py-1 rounded-md text-label-sm font-bold uppercase transition-colors %s"%s title="%s">%s</a>',
			esc_url( $lang['url'] ),
			esc_attr( $lang['slug'] ),
			esc_attr( $lang['slug'] ),
			$lang['current'] ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-primary',
			$lang['current'] ? ' aria-current="true"' : '',
			esc_attr( $lang['name'] ),
			esc_html( 'vi' === $lang['slug'] ? 'VI' : strtoupper( $lang['slug'] ) )
		);
	}
	echo '</nav>';
};
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[60] focus:bg-surface-container-lowest focus:px-4 focus:py-2 focus:rounded-lg" href="#main"><?php thp_e( 'Bỏ qua đến nội dung' ); ?></a>

<header class="fixed top-[var(--wp-admin--admin-bar--height,0px)] left-0 right-0 z-50 bg-surface/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
	<div class="h-20 max-w-7xl mx-auto px-4 sm:px-margin flex items-center justify-between gap-space-md">
		<?php thp_logo( 'h-11 sm:h-12 w-auto' ); ?>

		<nav class="hidden xl:block" aria-label="<?php echo esc_attr( thp__( 'Menu chính' ) ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu flex items-center gap-0.5',
					'depth'          => 2,
					'fallback_cb'    => 'thp_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="flex items-center gap-space-sm shrink-0">
			<?php $thp_switcher( $thp_langs, 'hidden sm:flex' ); ?>
			<a class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 bg-secondary text-on-secondary rounded-lg text-label-sm font-semibold hover:bg-secondary-container transition-colors shadow-sm" href="<?php echo esc_url( $thp_submit ); ?>">
				<?php echo thp_icon( 'add_location_alt', 'text-[16px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php thp_e( 'Gửi đề xuất' ); ?></span>
			</a>
			<a class="w-9 h-9 rounded-full bg-primary flex items-center justify-center shrink-0 text-on-primary hover:bg-primary-container transition-colors" href="<?php echo esc_url( is_user_logged_in() ? admin_url() : wp_login_url() ); ?>" aria-label="<?php echo esc_attr( thp__( is_user_logged_in() ? 'Trang quản trị' : 'Đăng nhập' ) ); ?>">
				<?php echo thp_icon( 'person', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<button type="button" class="xl:hidden w-9 h-9 rounded-lg flex items-center justify-center text-on-surface hover:bg-surface-container-low" data-thp-menu-toggle aria-controls="thp-mobile-menu" aria-expanded="false" aria-label="<?php echo esc_attr( thp__( 'Mở menu' ) ); ?>">
				<?php echo thp_icon( 'menu', 'text-[24px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>
	</div>

	<div id="thp-mobile-menu" class="xl:hidden hidden border-t border-surface-container-high bg-surface-container-lowest">
		<div class="max-w-7xl mx-auto px-4 sm:px-margin py-4 flex flex-col gap-4">
			<div class="lg:hidden"><?php get_search_form(); ?></div>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu mobile flex flex-col gap-1',
					'depth'          => 2,
					'fallback_cb'    => 'thp_menu_fallback',
				)
			);
			?>
			<div class="sm:hidden flex items-center justify-between gap-3">
				<?php $thp_switcher( $thp_langs, 'flex' ); ?>
				<a class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-secondary text-on-secondary rounded-lg text-label-lg" href="<?php echo esc_url( $thp_submit ); ?>">
					<?php echo thp_icon( 'add_location_alt', 'text-[18px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php thp_e( 'Gửi đề xuất' ); ?>
				</a>
			</div>
		</div>
	</div>
</header>

<main id="main" class="w-full pt-20 min-h-[calc(100vh-80px)]">
	<div class="w-full bg-surface-container-low py-2.5 px-4 sm:px-margin">
		<div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-on-surface-variant text-label-md">
			<?php if ( $thp_latest ) : ?>
				<div class="flex items-center gap-2 min-w-0">
					<span class="inline-flex shrink-0 items-center px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-bold tracking-wide uppercase text-[10px]"><?php thp_e( 'Mới nhất' ); ?></span>
					<a class="text-on-surface font-medium truncate hover:text-primary transition-colors" href="<?php echo esc_url( get_permalink( $thp_latest[0] ) ); ?>"><?php echo esc_html( get_the_title( $thp_latest[0] ) ); ?></a>
				</div>
				<div class="hidden sm:flex items-center gap-4">
					<span class="flex items-center gap-1"><?php echo thp_icon( 'verified', 'text-[16px] text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php thp_e( 'Cập nhật' ); ?> <?php echo esc_html( get_the_modified_date( 'd/m/Y', $thp_latest[0] ) ); ?></span>
					<span class="flex items-center gap-1"><?php echo thp_icon( 'trending_up', 'text-[16px] text-secondary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( sprintf( thp__( '%s bài viết cẩm nang' ), number_format_i18n( thp_post_count() ) ) ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
