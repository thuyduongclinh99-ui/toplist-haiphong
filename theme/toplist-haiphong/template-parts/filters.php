<?php
/**
 * Search filter panel shown next to search results: tick any number of
 * locations and categories. Each group shows a few rows and hides the rest
 * behind "Thêm" (CSS-only toggle).
 *
 * @package toplist-haiphong
 */

$thp_state = thp_filter_state();

/**
 * Toggle one value inside a list.
 *
 * @param array      $list  Current values.
 * @param int|string $value Value to add or remove.
 * @return array
 */
$thp_toggle = function ( $list, $value ) {
	return in_array( $value, $list, true ) ? array_values( array_diff( $list, array( $value ) ) ) : array_merge( $list, array( $value ) );
};

$thp_groups = array(
	array(
		'title' => thp__( 'Theo địa điểm' ),
		'terms' => get_terms(
			array(
				'taxonomy'   => 'post_tag',
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		),
		'key'   => 'slug',
		'list'  => $thp_state['tags'],
		'url'   => function ( $tags ) use ( $thp_state ) {
			return thp_filter_url( $thp_state['cats'], $tags );
		},
	),
	array(
		'title' => thp__( 'Theo danh mục' ),
		'terms' => get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'exclude'    => array( (int) get_option( 'default_category' ) ),
			)
		),
		'key'   => 'term_id',
		'list'  => $thp_state['cats'],
		'url'   => function ( $cats ) use ( $thp_state ) {
			return thp_filter_url( $cats, $thp_state['tags'] );
		},
	),
);

// Rows shown before "Thêm".
$thp_visible = 4;
?>
<section class="w-full flex flex-col" aria-labelledby="thp-filter-title">
	<h2 id="thp-filter-title" class="hidden lg:flex items-center gap-2 pb-4 text-label-lg font-extrabold uppercase tracking-wide text-on-surface">
		<?php echo thp_icon( 'filter_alt', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php thp_e( 'Bộ lọc tìm kiếm' ); ?>
	</h2>

	<?php
	foreach ( $thp_groups as $thp_n => $thp_group ) :
		if ( ! $thp_group['terms'] || is_wp_error( $thp_group['terms'] ) ) {
			continue;
		}
		$thp_more_id = 'thp-more-' . $thp_n;
		// Keep the list open when a ticked value sits in the hidden part.
		$thp_open = (bool) array_intersect( $thp_group['list'], wp_list_pluck( array_slice( $thp_group['terms'], $thp_visible ), $thp_group['key'] ) );
		?>
		<div class="py-4 border-t border-surface-container-high">
			<h3 class="mb-2 text-body-sm font-semibold text-on-surface"><?php echo esc_html( $thp_group['title'] ); ?></h3>
			<input type="checkbox" id="<?php echo esc_attr( $thp_more_id ); ?>" class="peer sr-only"<?php echo $thp_open ? ' checked' : ''; ?>>
			<ul class="flex flex-col peer-checked:[&>li.thp-extra]:block">
				<?php
				foreach ( $thp_group['terms'] as $thp_i => $thp_term ) :
					$thp_value = 'slug' === $thp_group['key'] ? $thp_term->slug : (int) $thp_term->term_id;
					?>
					<li<?php echo $thp_i >= $thp_visible ? ' class="thp-extra hidden"' : ''; ?>>
						<?php thp_filter_item( $thp_term->name, $thp_group['url']( $thp_toggle( $thp_group['list'], $thp_value ) ), in_array( $thp_value, $thp_group['list'], true ), $thp_term->count ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( count( $thp_group['terms'] ) > $thp_visible ) : ?>
				<label for="<?php echo esc_attr( $thp_more_id ); ?>" class="mt-1 inline-flex cursor-pointer items-center gap-1 pl-[26px] text-body-sm text-on-surface-variant hover:text-secondary peer-checked:[&>.thp-more]:hidden peer-checked:[&>.thp-less]:inline-flex">
					<span class="thp-more inline-flex items-center gap-1"><?php thp_e( 'Thêm' ); ?><?php echo thp_icon( 'expand_more', 'text-[18px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="thp-less hidden items-center gap-1"><?php thp_e( 'Thu gọn' ); ?><?php echo thp_icon( 'expand_less', 'text-[18px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</label>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<?php if ( $thp_state['cats'] || $thp_state['tags'] ) : ?>
		<div class="pt-4 border-t border-surface-container-high">
			<a class="flex w-full items-center justify-center rounded-lg bg-secondary px-4 py-2.5 text-label-lg uppercase text-on-secondary shadow-sm hover:bg-secondary-container transition-colors" href="<?php echo esc_url( thp_filter_url( array(), array() ) ); ?>"><?php thp_e( 'Xóa tất cả' ); ?></a>
		</div>
	<?php endif; ?>
</section>
