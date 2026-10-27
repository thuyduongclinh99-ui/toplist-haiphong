<?php
/**
 * Homepage: hero slider, quick categories, intro, category sections, sidebar.
 *
 * @package toplist-haiphong
 */

get_header();

$thp_hero       = thp_hero_posts( 4 );
$thp_post_count = thp_post_count();
$thp_cat_count  = thp_category_count();
$thp_all_url    = thp_page_url( 'danh-muc' );
$thp_all_url    = $thp_all_url ? $thp_all_url : add_query_arg( 's', '', thp_home_url() );
$thp_badges     = array( 'bg-secondary text-on-secondary', 'bg-primary text-on-primary', 'bg-secondary-container text-on-secondary-container', 'bg-tertiary text-on-tertiary' );
$thp_cta        = array( 'bg-secondary text-on-secondary hover:bg-secondary-container', 'bg-primary text-on-primary hover:bg-primary-container', 'bg-secondary text-on-secondary hover:bg-secondary-container', 'bg-tertiary text-on-tertiary hover:bg-tertiary-container' );
?>
<div class="max-w-7xl mx-auto px-4 sm:px-margin py-space-md w-full">
	<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
		<section class="lg:col-span-8 flex flex-col gap-space-xl min-w-0">

			<?php if ( $thp_hero ) : ?>
				<div class="relative w-full rounded-2xl overflow-hidden shadow-lg bg-surface-container" data-thp-slider aria-roledescription="carousel" aria-label="<?php echo esc_attr( thp__( 'Tiêu điểm' ) ); ?>">
					<div class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth w-full no-scrollbar" data-thp-track>
						<?php
						foreach ( $thp_hero as $thp_i => $thp_post ) :
							$thp_term = thp_primary_category( $thp_post->ID );
							$thp_img  = get_the_post_thumbnail_url( $thp_post, 'thp-hero' );
							?>
							<div class="snap-start shrink-0 w-full relative h-[420px] sm:h-[460px] md:h-[490px] bg-cover bg-center flex flex-col justify-end p-6 sm:p-10" style="background-image:url('<?php echo esc_url( $thp_img ); ?>')" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $thp_i + 1 ) . ' / ' . count( $thp_hero ) ); ?>">
								<div class="absolute inset-0 bg-gradient-to-t from-on-background/95 via-on-background/50 to-transparent"></div>
								<div class="relative z-10 flex flex-col gap-3 max-w-2xl">
									<div class="flex flex-wrap items-center gap-2">
										<?php if ( $thp_term ) : ?>
											<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo esc_attr( $thp_badges[ $thp_i % 4 ] ); ?> text-label-sm shadow-sm"><?php echo thp_icon( 'local_fire_department', 'text-[14px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $thp_term->name ); ?></span>
										<?php endif; ?>
										<?php thp_rating_pill( $thp_post->ID, 'lg' ); ?>
									</div>
									<<?php echo 0 === $thp_i ? 'h1' : 'h2'; ?> class="text-[26px] leading-[34px] sm:text-headline-xl font-extrabold tracking-tight text-on-tertiary uppercase line-clamp-3"><?php echo esc_html( get_the_title( $thp_post ) ); ?></<?php echo 0 === $thp_i ? 'h1' : 'h2'; ?>>
									<p class="text-body-md text-surface-container-low line-clamp-2 max-w-xl"><?php echo esc_html( get_the_excerpt( $thp_post ) ); ?></p>
									<div class="flex flex-wrap items-center gap-3 pt-2">
										<a class="inline-flex items-center gap-2 px-6 py-3 rounded-xl <?php echo esc_attr( $thp_cta[ $thp_i % 4 ] ); ?> text-label-lg shadow-md hover:shadow-lg transition-all duration-200 group/btn" href="<?php echo esc_url( get_permalink( $thp_post ) ); ?>">
											<span><?php thp_e( 'Khám phá ngay' ); ?></span><?php echo thp_icon( 'arrow_forward', 'text-[18px] transition-transform group-hover/btn:translate-x-1' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										</a>
										<?php if ( $thp_term ) : ?>
											<a class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-surface-container-lowest/90 backdrop-blur-md text-primary text-label-lg shadow-sm hover:bg-surface-container-lowest transition-all duration-200" href="<?php echo esc_url( get_category_link( $thp_term ) ); ?>">
												<?php echo thp_icon( 'explore', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( sprintf( thp__( 'Xem %s' ), mb_strtolower( $thp_term->name ) ) ); ?></span>
											</a>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php if ( count( $thp_hero ) > 1 ) : ?>
						<button type="button" data-thp-prev aria-label="<?php echo esc_attr( thp__( 'Slide trước' ) ); ?>" class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-surface-container-lowest/80 backdrop-blur-md text-on-surface hover:text-primary hover:bg-surface-container-lowest items-center justify-center shadow-md transition-all"><?php echo thp_icon( 'chevron_left', 'text-[22px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
						<button type="button" data-thp-next aria-label="<?php echo esc_attr( thp__( 'Slide tiếp' ) ); ?>" class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-surface-container-lowest/80 backdrop-blur-md text-on-surface hover:text-primary hover:bg-surface-container-lowest items-center justify-center shadow-md transition-all"><?php echo thp_icon( 'chevron_right', 'text-[22px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
						<div class="absolute top-4 right-4 sm:top-auto sm:bottom-4 sm:right-6 z-20 flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-lowest/80 backdrop-blur-md">
							<span class="text-[11px] font-bold text-on-surface tracking-wide uppercase"><?php thp_e( 'Khám phá' ); ?></span>
							<?php for ( $thp_d = 0; $thp_d < count( $thp_hero ); $thp_d++ ) : // phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall ?>
								<button type="button" data-thp-dot="<?php echo (int) $thp_d; ?>" aria-label="<?php echo esc_attr( sprintf( thp__( 'Tới slide %d' ), $thp_d + 1 ) ); ?>" class="w-2.5 h-2.5 rounded-full bg-outline-variant hover:bg-primary transition-all aria-[current=true]:bg-secondary aria-[current=true]:w-5"></button>
							<?php endfor; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/photo-strip' ); ?>

			<div class="relative overflow-hidden rounded-2xl bg-surface-container-low p-6 sm:p-8 shadow-sm">
				<svg class="absolute -right-12 -bottom-12 w-64 h-64 opacity-5 pointer-events-none text-primary" fill="currentColor" viewBox="0 0 200 200" aria-hidden="true"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.5,90.1,-16.3,88.4,-0.9C86.8,14.4,80.7,28.8,72.2,41.4C63.6,54,52.5,64.7,39.6,71.5C26.7,78.2,12,81,-2.7,85.6C-17.5,90.3,-32.3,96.8,-45.4,92.5C-58.5,88.2,-69.8,73.1,-77.8,58.3C-85.8,43.4,-90.4,28.8,-89.6,14.7C-88.7,0.6,-82.3,-13,-75,-25.7C-67.7,-38.4,-59.6,-50.2,-48.6,-58.5C-37.5,-66.8,-23.5,-71.5,-8.8,-60.1C5.9,-48.7,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)"/></svg>
				<div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
					<div class="flex-1">
						<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed text-label-sm mb-3"><?php echo thp_icon( 'auto_stories', 'text-[16px] text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php thp_e( 'Cẩm nang số Đất Cảng' ); ?></div>
						<h2 class="text-headline-md text-on-surface font-bold tracking-tight mb-2"><?php echo esc_html( thp_option( 'thp_intro_title' ) ); ?></h2>
						<p class="text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( thp_option( 'thp_intro_text' ) ); ?></p>
					</div>
					<div class="grid grid-cols-3 gap-3 w-full md:w-auto shrink-0">
						<div class="flex flex-col items-center justify-center p-3.5 rounded-xl bg-surface-container-lowest shadow-sm text-center min-w-[90px]">
							<span class="text-headline-md font-extrabold text-primary"><?php echo esc_html( thp_compact_number( $thp_post_count ) ); ?></span>
							<span class="text-label-sm text-on-surface-variant mt-0.5"><?php thp_e( 'Bài viết' ); ?></span>
						</div>
						<div class="flex flex-col items-center justify-center p-3.5 rounded-xl bg-surface-container-lowest shadow-sm text-center min-w-[90px]">
							<span class="text-headline-md font-extrabold text-secondary"><?php echo esc_html( thp_compact_number( thp_total_reviews() ) ); ?></span>
							<span class="text-label-sm text-on-surface-variant mt-0.5"><?php thp_e( 'Lượt đánh giá' ); ?></span>
						</div>
						<div class="flex flex-col items-center justify-center p-3.5 rounded-xl bg-surface-container-lowest shadow-sm text-center min-w-[90px]">
							<span class="text-headline-md font-extrabold text-tertiary"><?php echo (int) $thp_cat_count; ?></span>
							<span class="text-label-sm text-on-surface-variant mt-0.5"><?php thp_e( 'Chủ đề' ); ?></span>
						</div>
					</div>
				</div>
			</div>

			<?php
			foreach ( thp_home_sections() as $thp_section ) :
				$thp_term = thp_category( $thp_section['slug'] );
				if ( ! $thp_term ) {
					continue;
				}
				$thp_q = new WP_Query(
					array(
						'cat'                 => $thp_term->term_id,
						'posts_per_page'      => 3,
						'ignore_sticky_posts' => true,
					)
				);
				if ( ! $thp_q->have_posts() ) {
					continue;
				}
				?>
				<div class="flex flex-col gap-4">
					<?php thp_section_heading( thp__( $thp_section['title'] ), thp__( $thp_section['subtitle'] ), $thp_section['accent'], $thp_term->name, get_category_link( $thp_term ) ); ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
						<?php
						while ( $thp_q->have_posts() ) {
							$thp_q->the_post();
							get_template_part( 'template-parts/card', null, array( 'accent' => $thp_section['accent'] ) );
						}
						wp_reset_postdata();
						?>
					</div>
					<a class="sm:hidden inline-flex items-center justify-center gap-1 py-2 text-primary text-label-md font-bold" href="<?php echo esc_url( get_category_link( $thp_term ) ); ?>"><?php echo esc_html( sprintf( thp__( 'Xem tất cả %s' ), mb_strtolower( $thp_term->name ) ) ); ?> <?php echo thp_icon( 'arrow_forward' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				</div>
			<?php endforeach; ?>

			<div class="flex justify-center pt-2">
				<a class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-surface-container-high hover:bg-surface-container text-on-surface text-label-md font-semibold transition-all shadow-sm" href="<?php echo esc_url( $thp_all_url ); ?>">
					<span><?php echo esc_html( sprintf( thp__( 'Xem trọn bộ %s bài viết cẩm nang Hải Phòng' ), number_format_i18n( $thp_post_count ) ) ); ?></span><?php echo thp_icon( 'expand_more' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</div>
		</section>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
