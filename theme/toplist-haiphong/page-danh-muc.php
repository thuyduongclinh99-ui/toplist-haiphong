<?php
/**
 * Page with slug "danh-muc": every category with its newest image and count.
 *
 * @package toplist-haiphong
 */

get_header();

$thp_items = thp_quick_categories( 0 );
?>
<div class="max-w-7xl mx-auto px-4 sm:px-margin py-space-md w-full">
	<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
		<section class="lg:col-span-8 flex flex-col gap-space-lg min-w-0">
			<header class="rounded-2xl bg-surface-container-low p-6 sm:p-8 shadow-sm flex flex-col gap-3">
				<?php thp_breadcrumb(); ?>
				<h1 class="text-[26px] leading-8 sm:text-headline-lg font-bold tracking-tight"><?php the_title(); ?></h1>
				<?php
				while ( have_posts() ) {
					the_post();
					echo '<div class="text-body-md text-on-surface-variant">';
					the_content();
					echo '</div>';
				}
				?>
			</header>
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<?php foreach ( $thp_items as $thp_item ) : ?>
					<a class="card-lift group flex items-center gap-4 p-4 rounded-2xl bg-surface-container-lowest shadow-sm border border-surface-container-high/60" href="<?php echo esc_url( get_category_link( $thp_item['term'] ) ); ?>">
						<span class="relative w-20 h-20 rounded-xl overflow-hidden shrink-0 bg-surface-container block">
							<?php
							if ( $thp_item['image'] ) {
								echo wp_get_attachment_image( $thp_item['image'], 'thumbnail', false, array( 'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-300', 'alt' => '' ) );
							} else {
								echo '<span class="w-full h-full flex items-center justify-center bg-primary-fixed text-primary">' . thp_icon( thp_category_icon( $thp_item['term'] ), 'text-[36px]' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
						</span>
						<span class="flex flex-col gap-1 min-w-0">
							<span class="text-headline-sm font-bold text-on-surface group-hover:text-primary transition-colors"><?php echo esc_html( $thp_item['term']->name ); ?></span>
							<span class="text-body-sm text-on-surface-variant"><?php echo esc_html( sprintf( thp__( '%s bài viết' ), number_format_i18n( $thp_item['term']->count ) ) ); ?></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
