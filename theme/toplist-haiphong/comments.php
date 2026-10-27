<?php
/**
 * Reader reviews (comments).
 *
 * @package toplist-haiphong
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="rounded-2xl bg-surface-container-lowest p-5 sm:p-8 shadow-sm border border-surface-container-high/60 flex flex-col gap-5">
	<h2 class="text-headline-sm font-bold flex items-center gap-2">
		<?php echo thp_icon( 'forum', 'text-[22px] text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php thp_e( 'Chia sẻ trải nghiệm' ); ?> (<?php echo (int) get_comments_number(); ?>)
	</h2>

	<?php if ( have_comments() ) : ?>
		<ol class="flex flex-col gap-4">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'        => thp__( 'Viết đánh giá của bạn' ),
			'title_reply_before' => '<h3 id="reply-title" class="text-label-lg text-on-surface mb-2">',
			'title_reply_after'  => '</h3>',
			'label_submit'       => thp__( 'Gửi đánh giá' ),
		)
	);
	?>
</section>
