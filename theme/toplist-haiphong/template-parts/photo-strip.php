<?php
/**
 * Homepage photo strip: featured images of travel posts scrolling sideways in
 * an endless loop. The list is rendered twice so the CSS animation can wrap
 * seamlessly; the copy is hidden from assistive tech and keyboard focus.
 *
 * @package toplist-haiphong
 */

$thp_photos = thp_travel_photo_posts( 12 );
$thp_travel = thp_category( 'du-lich' );
if ( ! $thp_photos ) {
	return;
}
?>
<div class="flex flex-col gap-3">
	<div class="flex items-center justify-between gap-3">
		<div class="flex items-center gap-2">
			<span class="w-2.5 h-6 bg-primary rounded-full inline-block"></span>
			<h2 class="text-headline-sm text-on-surface font-bold"><?php thp_e( 'Điểm đến du lịch Hải Phòng' ); ?></h2>
		</div>
		<?php if ( $thp_travel ) : ?>
			<a class="inline-flex items-center gap-1 shrink-0 text-primary hover:text-primary-container text-label-md font-bold transition-colors" href="<?php echo esc_url( get_category_link( $thp_travel ) ); ?>">
				<span><?php thp_e( 'Xem tất cả' ); ?></span><?php echo thp_icon( 'arrow_forward' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="thp-marquee overflow-hidden rounded-2xl">
		<ul class="thp-marquee-track flex w-max" style="--thp-marquee-duration: <?php echo (int) max( 20, count( $thp_photos ) * 6 ); ?>s">
			<?php for ( $thp_copy = 0; $thp_copy < 2; $thp_copy++ ) : ?>
				<?php foreach ( $thp_photos as $thp_post ) : ?>
					<li class="shrink-0 w-60 sm:w-72 pr-3"<?php echo $thp_copy ? ' aria-hidden="true"' : ''; ?>>
						<a class="group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-surface-container shadow-sm" href="<?php echo esc_url( get_permalink( $thp_post ) ); ?>"<?php echo $thp_copy ? ' tabindex="-1"' : ''; ?>>
							<?php echo get_the_post_thumbnail( $thp_post, 'thp-card', array( 'class' => 'absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105', 'alt' => '', 'loading' => 'lazy' ) ); ?>
							<span class="absolute inset-0 bg-gradient-to-t from-on-background/75 via-transparent to-transparent"></span>
							<span class="absolute inset-x-0 bottom-0 p-3.5 text-body-md font-bold text-on-primary truncate"><?php echo esc_html( thp_place_name( $thp_post ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			<?php endfor; ?>
		</ul>
	</div>
</div>
