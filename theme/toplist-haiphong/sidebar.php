<?php
/**
 * Right column: search box, Top 10 ranking, map and submit callout. Filters
 * appear on the search results page (template-parts/filters.php).
 *
 * @package toplist-haiphong
 */

$thp_top = thp_top_posts( 10 );
$thp_map = thp_option( 'thp_map_query' );
?>
<aside class="lg:col-span-4 flex flex-col gap-space-lg w-full">
	<div class="hidden lg:block w-full rounded-2xl bg-surface-container-lowest p-4 shadow-sm">
		<?php get_search_form(); ?>
	</div>

	<?php if ( $thp_top ) : ?>
		<section class="w-full rounded-2xl bg-surface-container-lowest p-5 sm:p-6 shadow-md flex flex-col gap-4" aria-labelledby="thp-top-title">
			<div class="flex items-start justify-between gap-2 bg-surface-container-low -mx-5 -mt-5 sm:-mx-6 sm:-mt-6 p-4 rounded-t-2xl">
				<div class="flex items-center gap-2.5 min-w-0">
					<span class="w-8 h-8 rounded-lg bg-secondary text-on-secondary flex items-center justify-center shadow-sm shrink-0"><?php echo thp_icon( 'format_list_numbered', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<div class="min-w-0">
						<h2 id="thp-top-title" class="text-[18px] leading-6 text-on-surface font-bold uppercase"><?php thp_e( 'Top list Hải Phòng' ); ?></h2>
						<p class="text-label-sm text-on-surface-variant"><?php thp_e( 'Xếp hạng theo điểm & lượt đánh giá' ); ?></p>
					</div>
				</div>
				<span class="px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed text-[11px] font-bold whitespace-nowrap shrink-0">TOP <?php echo count( $thp_top ); ?></span>
			</div>

			<ol class="flex flex-col gap-1">
				<?php
				foreach ( $thp_top as $thp_i => $thp_post ) :
					$thp_rank = $thp_i + 1;
					$thp_addr = get_post_meta( $thp_post->ID, '_thp_address', true );
					if ( $thp_rank <= 3 ) {
						$thp_rank_class = 'bg-secondary text-on-secondary shadow-sm';
					} elseif ( $thp_rank <= 4 ) {
						$thp_rank_class = 'bg-primary-fixed-dim text-on-primary-fixed';
					} else {
						$thp_rank_class = 'bg-surface-container-high text-on-surface';
					}
					?>
					<li>
						<a class="group flex items-center justify-between p-2.5 rounded-xl hover:bg-surface-container-low transition-all" href="<?php echo esc_url( get_permalink( $thp_post ) ); ?>">
							<span class="flex items-center gap-3 min-w-0">
								<span class="w-8 h-8 rounded-lg <?php echo esc_attr( $thp_rank_class ); ?> flex items-center justify-center font-bold text-sm shrink-0"><?php echo (int) $thp_rank; ?></span>
								<span class="flex flex-col min-w-0">
									<span class="text-label-md text-on-surface group-hover:text-primary font-bold truncate"><?php echo esc_html( get_the_title( $thp_post ) ); ?></span>
									<?php if ( $thp_addr ) : ?>
										<span class="text-[12px] text-on-surface-variant truncate"><?php echo esc_html( $thp_addr ); ?></span>
									<?php endif; ?>
								</span>
							</span>
							<span class="text-label-sm <?php echo $thp_rank <= 3 ? 'text-secondary' : 'text-primary'; ?> font-semibold shrink-0 ml-2"><?php echo esc_html( thp_rating( $thp_post->ID ) ); ?>★</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endif; ?>

	<?php if ( $thp_map ) : ?>
		<section class="w-full rounded-2xl bg-surface-container-lowest p-5 shadow-sm flex flex-col gap-3" aria-labelledby="thp-map-title">
			<div class="flex items-center gap-2">
				<?php echo thp_icon( 'pin_drop', 'text-secondary text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2 id="thp-map-title" class="text-[18px] leading-7 text-on-surface font-bold"><?php thp_e( 'Bản đồ Hải Phòng' ); ?></h2>
			</div>
			<p class="text-body-sm text-on-surface-variant"><?php thp_e( 'Xem nhanh vị trí các khu phố, quán ăn và điểm tham quan trước khi lên đường.' ); ?></p>
			<div class="relative w-full h-48 rounded-xl overflow-hidden bg-surface-container shadow-inner">
				<iframe class="absolute inset-0 w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr( thp__( 'Bản đồ Hải Phòng' ) ); ?>" src="<?php echo esc_url( 'https://www.google.com/maps?output=embed&z=12&hl=' . thp_lang() . '&q=' . rawurlencode( $thp_map ) ); ?>"></iframe>
			</div>
			<a class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-surface-container-low text-on-surface text-label-sm font-bold hover:bg-surface-container transition-colors" href="<?php echo esc_url( thp_maps_url( $thp_map ) ); ?>" target="_blank" rel="noopener">
				<?php echo thp_icon( 'location_on', 'text-[16px] text-secondary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php thp_e( 'Mở Google Maps' ); ?>
			</a>
		</section>
	<?php endif; ?>

	<section class="w-full rounded-2xl bg-gradient-to-br from-primary to-primary-container p-6 text-on-primary shadow-md flex flex-col gap-3">
		<div class="w-10 h-10 rounded-xl bg-on-primary/10 flex items-center justify-center"><?php echo thp_icon( 'add_business', 'text-[24px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div>
			<h2 class="text-headline-sm font-bold"><?php thp_e( 'Bạn biết địa điểm tuyệt vời?' ); ?></h2>
			<p class="text-body-sm text-on-primary/90 mt-1"><?php thp_e( 'Gửi đề xuất quán mới hoặc điểm check-in độc đáo để Ban biên tập thẩm định và đưa vào Toplist.' ); ?></p>
		</div>
		<a class="inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-on-primary text-primary text-label-md font-bold hover:bg-surface-container-low transition-all shadow-sm" href="<?php echo esc_url( thp_submit_url() ); ?>">
			<span><?php thp_e( 'Gửi đề xuất ngay' ); ?></span><?php echo thp_icon( 'arrow_forward', 'text-[16px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</section>
</aside>
