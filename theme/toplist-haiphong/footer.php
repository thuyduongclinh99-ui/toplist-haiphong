<?php
/**
 * Site footer.
 *
 * @package toplist-haiphong
 */

$thp_footer_menu = function ( $location, $title ) {
	if ( ! has_nav_menu( $location ) ) {
		return;
	}
	echo '<div><h2 class="text-headline-sm font-bold text-on-primary mb-space-sm">' . esc_html( thp__( $title ) ) . '</h2>';
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => 'footer-menu flex flex-col gap-2 text-body-sm text-on-primary/80',
			'depth'          => 1,
		)
	);
	echo '</div>';
};
$thp_hotline     = thp_option( 'thp_hotline' );
$thp_email       = thp_option( 'thp_email' );
?>
</main>

<footer class="w-full bg-gradient-to-br from-primary to-primary-container text-on-primary mt-space-xl">
	<div class="max-w-7xl mx-auto px-4 sm:px-margin py-space-xl">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-space-lg mb-space-lg">
			<div class="lg:col-span-2 flex flex-col gap-space-sm">
				<div class="self-start rounded-xl bg-surface-container-lowest px-3 py-2 shadow-sm"><?php thp_logo( 'h-12 w-auto' ); ?></div>
				<p class="text-body-sm text-on-primary/80 max-w-sm mt-space-xs"><?php echo esc_html( thp_option( 'thp_footer_about' ) ); ?></p>
				<div class="flex items-center gap-space-sm mt-space-xs text-on-primary/90">
					<?php echo thp_icon( 'verified', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="text-label-sm"><?php thp_e( 'Bình chọn & xếp hạng độc lập' ); ?></span>
				</div>
			</div>
			<?php
			$thp_footer_menu( 'footer', 'Khám phá' );
			$thp_footer_menu( 'portal', 'Liên hệ' );
			?>
			<div>
				<h2 class="text-headline-sm font-bold text-on-primary mb-space-sm"><?php thp_e( 'Địa chỉ' ); ?></h2>
				<p class="text-body-sm text-on-primary/80 mb-2"><?php echo esc_html( thp_option( 'thp_address' ) ); ?></p>
				<?php if ( $thp_hotline ) : ?>
					<p class="text-body-sm text-on-primary/80 mb-2">Hotline: <a class="hover:text-on-primary hover:underline" href="<?php echo esc_url( thp_tel( $thp_hotline ) ); ?>"><?php echo esc_html( $thp_hotline ); ?></a></p>
				<?php endif; ?>
				<?php if ( $thp_email ) : ?>
					<p class="text-body-sm text-on-primary/80">Email: <a class="hover:text-on-primary hover:underline" href="mailto:<?php echo esc_attr( antispambot( $thp_email ) ); ?>"><?php echo esc_html( antispambot( $thp_email ) ); ?></a></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="py-space-md bg-on-background/15 rounded-xl px-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm text-label-sm text-on-primary/80">
			<p>© <?php echo esc_html( wp_date( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?>. <?php thp_e( 'Bảo lưu mọi quyền.' ); ?></p>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'menu_class'     => 'footer-menu flex items-center gap-space-md',
						'depth'          => 1,
					)
				);
			}
			?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
