<?php
/**
 * Static page.
 *
 * @package toplist-haiphong
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="max-w-7xl mx-auto px-4 sm:px-margin py-space-md w-full">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
			<article <?php post_class( 'lg:col-span-8 rounded-2xl bg-surface-container-lowest p-5 sm:p-8 shadow-sm border border-surface-container-high/60 flex flex-col gap-4 min-w-0' ); ?>>
				<?php thp_breadcrumb(); ?>
				<h1 class="text-[26px] leading-8 sm:text-headline-lg font-bold tracking-tight"><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
			<?php get_sidebar(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
