<?php
/**
 * Single place review.
 *
 * @package toplist-haiphong
 */

get_header();

while ( have_posts() ) :
	the_post();
	$thp_term    = thp_primary_category();
	$thp_rating  = thp_rating();
	$thp_address = get_post_meta( get_the_ID(), '_thp_address', true );
	$thp_price   = get_post_meta( get_the_ID(), '_thp_price', true );
	$thp_phone   = get_post_meta( get_the_ID(), '_thp_phone', true );
	$thp_tags    = get_the_tags();
	$thp_related = thp_related_posts( get_the_ID(), 3 );
	?>
	<div class="max-w-7xl mx-auto px-4 sm:px-margin py-space-md w-full">
		<div class="mb-4"><?php thp_breadcrumb(); ?></div>
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
			<article <?php post_class( 'lg:col-span-8 flex flex-col gap-space-lg min-w-0' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="relative w-full rounded-2xl overflow-hidden shadow-lg bg-surface-container">
						<?php the_post_thumbnail( 'thp-hero', array( 'class' => 'w-full aspect-[16/9] object-cover' ) ); ?>
					</figure>
				<?php endif; ?>

				<header class="rounded-2xl bg-surface-container-lowest p-5 sm:p-8 shadow-sm border border-surface-container-high/60 flex flex-col gap-4">
					<div class="flex flex-wrap items-center gap-2">
						<?php if ( $thp_term ) : ?>
							<a class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed text-label-sm uppercase hover:bg-primary hover:text-on-primary transition-colors" href="<?php echo esc_url( get_category_link( $thp_term ) ); ?>"><?php echo esc_html( $thp_term->name ); ?></a>
						<?php endif; ?>
						<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#FFF1F2] border border-[#FFE4E6] text-secondary text-label-sm"><?php echo thp_icon( 'verified', 'text-[14px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php thp_e( 'Đã kiểm duyệt' ); ?></span>
					</div>
					<h1 class="text-[26px] leading-8 sm:text-headline-xl font-bold tracking-tight text-on-surface"><?php the_title(); ?></h1>

					<div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-body-sm text-on-surface-variant">
						<?php if ( '' !== $thp_rating ) : ?>
							<span class="inline-flex items-center gap-1.5 rounded-full border border-amber-100 bg-amber-50 px-3 py-1 font-semibold text-amber-700">
								<?php echo thp_icon( 'star', 'text-[16px] text-amber-500', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $thp_rating ); ?>/5
								<span class="font-medium text-on-surface-variant">(<?php echo esc_html( sprintf( thp__( '%s đánh giá' ), number_format_i18n( thp_review_count() ) ) ); ?>)</span>
							</span>
						<?php endif; ?>
						<span class="inline-flex items-center gap-1"><?php echo thp_icon( 'schedule', 'text-[16px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( sprintf( thp__( '%d phút đọc' ), thp_reading_minutes() ) ); ?></span>
						<span class="inline-flex items-center gap-1"><?php echo thp_icon( 'edit_calendar', 'text-[16px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( get_the_modified_date( 'd/m/Y' ) ); ?></span>
						<span class="inline-flex items-center gap-1"><?php echo thp_icon( 'person', 'text-[16px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php the_author(); ?></span>
					</div>

					<?php if ( $thp_address || $thp_price || $thp_phone ) : ?>
						<dl class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
							<?php
							$thp_facts = array(
								array( 'location_on', 'Địa chỉ', $thp_address ),
								array( 'payments', 'Khoảng giá', $thp_price ),
								array( 'call', 'Điện thoại', $thp_phone ),
							);
							foreach ( $thp_facts as $thp_fact ) :
								if ( ! $thp_fact[2] ) {
									continue;
								}
								?>
								<div class="flex gap-2.5 rounded-xl bg-surface-container-low p-3">
									<?php echo thp_icon( $thp_fact[0], 'text-[20px] text-primary mt-0.5' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<div class="min-w-0">
										<dt class="text-label-sm uppercase text-on-surface-variant"><?php thp_e( $thp_fact[1] ); ?></dt>
										<dd class="text-body-sm font-semibold text-on-surface break-words"><?php echo esc_html( $thp_fact[2] ); ?></dd>
									</div>
								</div>
							<?php endforeach; ?>
						</dl>
						<div class="flex flex-wrap gap-3">
							<?php if ( $thp_phone ) : ?>
								<a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-secondary text-on-secondary text-label-lg shadow-sm hover:bg-secondary-container transition-colors" href="<?php echo esc_url( thp_tel( $thp_phone ) ); ?>"><?php echo thp_icon( 'call' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php thp_e( 'Gọi điện' ); ?></a>
							<?php endif; ?>
							<?php if ( $thp_address ) : ?>
								<a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-[1.5px] border-primary text-primary text-label-lg hover:bg-primary-fixed/40 transition-colors" href="<?php echo esc_url( thp_maps_url( $thp_address ) ); ?>" target="_blank" rel="noopener"><?php echo thp_icon( 'near_me' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php thp_e( 'Chỉ đường' ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</header>

				<div class="rounded-2xl bg-surface-container-lowest p-5 sm:p-8 shadow-sm border border-surface-container-high/60">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
					<?php
					wp_link_pages(
						array(
							'before' => '<nav class="mt-6 flex gap-2 text-label-lg">' . esc_html( thp__( 'Trang:' ) ),
							'after'  => '</nav>',
						)
					);
					?>
					<?php if ( $thp_tags ) : ?>
						<div class="mt-8 pt-5 border-t border-surface-container-high flex flex-wrap gap-2">
							<?php foreach ( $thp_tags as $thp_tag ) : ?>
								<a class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full bg-surface-container-low text-[13px] text-on-surface-variant hover:bg-primary hover:text-on-primary transition-colors" href="<?php echo esc_url( get_tag_link( $thp_tag ) ); ?>"><?php echo thp_icon( 'location_on', 'text-[14px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $thp_tag->name ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $thp_related ) : ?>
					<section class="flex flex-col gap-4">
						<?php thp_section_heading( thp__( 'Địa điểm tương tự' ), $thp_term ? sprintf( thp__( 'Cùng chuyên mục %s' ), $thp_term->name ) : '', 'secondary', '', $thp_term ? get_category_link( $thp_term ) : '' ); ?>
						<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
							<?php
							global $post;
							foreach ( $thp_related as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- restored by wp_reset_postdata().
								setup_postdata( $post );
								get_template_part( 'template-parts/card', null, array( 'accent' => 'secondary' ) );
							}
							wp_reset_postdata();
							?>
						</div>
					</section>
				<?php endif; ?>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>

			<?php get_sidebar(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
