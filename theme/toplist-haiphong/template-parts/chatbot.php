<?php
/**
 * Floating travel chatbot (behaviour in assets/js/chatbot.js, answers from inc/chatbot.php).
 *
 * @package toplist-haiphong
 */

?>
<div class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-3" data-thp-chat>
	<section id="thp-chat-panel" class="hidden w-[calc(100vw-2rem)] sm:w-[380px] h-[min(560px,calc(100dvh-6.5rem))] flex-col overflow-hidden rounded-2xl border border-surface-container-high bg-surface-container-lowest shadow-[0_24px_48px_-12px_rgba(15,23,42,0.28)]" role="dialog" aria-labelledby="thp-chat-title">
		<header class="flex items-center gap-3 bg-gradient-to-br from-primary to-primary-container px-4 py-3 text-on-primary">
			<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-on-primary/15"><?php echo thp_icon( 'travel_explore', 'text-[22px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div class="min-w-0 flex-1">
				<h2 id="thp-chat-title" class="text-label-lg font-bold"><?php thp_e( 'Trợ lý du lịch' ); ?></h2>
				<p class="text-label-md font-medium text-on-primary/80"><?php thp_e( 'Chỉ trả lời câu hỏi về du lịch' ); ?></p>
			</div>
			<button type="button" class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-on-primary/15" data-thp-chat-clear title="<?php echo esc_attr( thp__( 'Xóa cuộc trò chuyện' ) ); ?>" aria-label="<?php echo esc_attr( thp__( 'Xóa cuộc trò chuyện' ) ); ?>"><?php echo thp_icon( 'delete_sweep', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			<button type="button" class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-on-primary/15" data-thp-chat-close aria-label="<?php echo esc_attr( thp__( 'Đóng' ) ); ?>"><?php echo thp_icon( 'close', 'text-[22px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</header>

		<div class="flex flex-1 flex-col gap-3 overflow-y-auto bg-surface p-4" data-thp-chat-log aria-live="polite" data-greeting="<?php echo esc_attr( thp__( 'Xin chào! Mình là trợ lý du lịch của Toplist Hải Phòng. Bạn muốn hỏi về điểm tham quan, món ngon, chỗ ở hay lịch trình nào?' ) ); ?>" data-typing="<?php echo esc_attr( thp__( 'Đang trả lời…' ) ); ?>"></div>

		<form class="flex items-end gap-2 border-t border-surface-container-high p-3" data-thp-chat-form>
			<label class="sr-only" for="thp-chat-input"><?php thp_e( 'Hỏi trợ lý du lịch' ); ?></label>
			<textarea id="thp-chat-input" rows="1" maxlength="500" required class="max-h-28 min-h-[44px] flex-1 resize-none rounded-xl border border-outline-variant bg-surface-container-lowest px-3.5 py-2.5 text-body-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="<?php echo esc_attr( thp__( 'Nhập câu hỏi về du lịch…' ) ); ?>"></textarea>
			<button type="submit" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary text-on-primary hover:bg-primary-container disabled:opacity-50" aria-label="<?php echo esc_attr( thp__( 'Gửi' ) ); ?>"><?php echo thp_icon( 'send', 'text-[20px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</form>
	</section>

	<button type="button" class="flex h-14 w-14 items-center justify-center rounded-full bg-secondary text-on-secondary shadow-lg ring-4 ring-surface-container-lowest transition-transform hover:scale-105 hover:bg-secondary-container" data-thp-chat-toggle aria-controls="thp-chat-panel" aria-expanded="false" aria-label="<?php echo esc_attr( thp__( 'Hỏi trợ lý du lịch' ) ); ?>">
		<span data-thp-chat-icon="closed"><?php echo thp_icon( 'chat', 'text-[28px]', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="hidden" data-thp-chat-icon="open"><?php echo thp_icon( 'close', 'text-[28px]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	</button>
</div>
