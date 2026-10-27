<?php
/**
 * Article card (image, rating, reading time, views, excerpt, author).
 * Expects the global post to be set; $args['accent'] picks the link colour.
 *
 * @package toplist-haiphong
 */

$thp_a      = thp_accent( isset( $args['accent'] ) ? $args['accent'] : 'primary' );
$thp_author = get_the_author();
$thp_views  = thp_views();
?>
<article <?php post_class( 'card-lift flex flex-col rounded-2xl overflow-hidden bg-surface-container-lowest shadow-sm border border-surface-container-high/60 group relative' ); ?>>
	<div class="relative aspect-[16/10] w-full overflow-hidden bg-surface-container">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'thp-card', array( 'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500', 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-fixed to-surface-container text-primary/50"><?php echo thp_icon( thp_category_icon( thp_primary_category() ), 'text-[56px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<?php endif; ?>
		<div class="absolute top-2.5 left-2.5"><?php thp_rating_pill( get_the_ID() ); ?></div>
	</div>
	<div class="p-4 flex flex-col flex-1 justify-between gap-3">
		<div class="flex flex-col gap-1.5">
			<div class="flex items-center gap-2 text-[11px] text-on-surface-variant">
				<span class="flex items-center gap-0.5"><?php echo thp_icon( 'schedule', 'text-[13px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( sprintf( thp__( '%d phút đọc' ), thp_reading_minutes() ) ); ?></span>
				<?php if ( $thp_views ) : ?>
					<span aria-hidden="true">•</span>
					<span class="flex items-center gap-0.5"><?php echo thp_icon( 'visibility', 'text-[13px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( sprintf( thp__( '%s xem' ), thp_compact_number( $thp_views ) ) ); ?></span>
				<?php endif; ?>
			</div>
			<h3 class="text-[16px] leading-snug text-on-surface font-bold group-hover:text-primary transition-colors line-clamp-2">
				<a class="after:absolute after:inset-0" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>
			<p class="text-[13px] leading-5 text-on-surface-variant line-clamp-2"><?php echo esc_html( get_the_excerpt() ); ?></p>
		</div>
		<div class="flex items-center justify-between pt-2 border-t border-surface-container-high/60">
			<span class="flex items-center gap-1.5 min-w-0">
				<span class="w-5 h-5 rounded-full <?php echo esc_attr( $thp_a['avatar'] ); ?> flex items-center justify-center font-bold text-[9px] shrink-0"><?php echo esc_html( thp_initials( $thp_author ) ); ?></span>
				<span class="text-[11px] text-on-surface font-medium truncate max-w-[110px]"><?php echo esc_html( $thp_author ); ?></span>
			</span>
			<span class="<?php echo esc_attr( $thp_a['link'] ); ?> text-[11px] font-bold flex items-center gap-0.5 group-hover:translate-x-1 transition-transform"><?php thp_e( 'Xem bài viết' ); ?> <?php echo thp_icon( 'chevron_right', 'text-[14px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
	</div>
</article>
