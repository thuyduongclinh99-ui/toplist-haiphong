<?php
/**
 * Plugin Name: Toplist Hải Phòng – Dữ liệu mẫu
 * Description: Nạp nội dung mẫu cho theme Toplist Hải Phòng: 28 bài, 11 danh mục, các trang, menu và bản tiếng Anh (khi có Polylang). Vào Công cụ → Nạp dữ liệu Toplist. Có thể xóa plugin sau khi nạp xong.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Text Domain: toplist-demo-content
 *
 * @package toplist-demo-content
 */

defined( 'ABSPATH' ) || exit;

define( 'THP_DEMO_DIR', plugin_dir_path( __FILE__ ) );

require THP_DEMO_DIR . 'includes/importer.php';

add_action(
	'admin_menu',
	function () {
		add_management_page( 'Nạp dữ liệu Toplist', 'Nạp dữ liệu Toplist', 'manage_options', 'thp-demo', 'thp_demo_render_page' );
	}
);

/**
 * Admin screen: checklist and the import button.
 */
function thp_demo_render_page() {
	$theme_ok = 'toplist-haiphong' === get_template();
	$pll_ok   = defined( 'POLYLANG_VERSION' );
	$done     = isset( $_GET['thp_done'] ); // phpcs:ignore WordPress.Security.NonceVerification -- display only.
	$skipped  = isset( $_GET['thp_skipped'] ) ? sanitize_text_field( wp_unslash( $_GET['thp_skipped'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- display only.
	?>
	<div class="wrap">
		<h1>Nạp dữ liệu Toplist Hải Phòng</h1>
		<?php if ( $done ) : ?>
			<div class="notice notice-success"><p><strong>Đã nạp xong.</strong> <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Xem trang chủ</a>.
			<?php if ( $skipped ) : ?>
				Bỏ qua phần tiếng Anh vì chưa kích hoạt Polylang; kích hoạt Polylang rồi bấm nạp lại để thêm bản tiếng Anh.
			<?php endif; ?>
			</p></div>
		<?php endif; ?>
		<p>Plugin sẽ tạo 11 danh mục, 28 bài viết kèm ảnh và thông tin địa điểm, các trang Giới thiệu / Gửi đề xuất / Tất cả danh mục, menu, và đặt tên site, múi giờ, đường dẫn tĩnh dạng <code>/ten-bai-viet/</code>. Bấm nạp nhiều lần cũng không tạo trùng.</p>
		<ul>
			<li><?php echo $theme_ok ? '✅' : '⚠️'; ?> Theme <strong>Toplist Hải Phòng</strong> <?php echo $theme_ok ? 'đang được dùng' : 'chưa được kích hoạt (Giao diện → Giao diện)'; ?></li>
			<li><?php echo $pll_ok ? '✅' : '⚠️'; ?> Plugin <strong>Polylang</strong> <?php echo $pll_ok ? 'đã kích hoạt: sẽ tạo thêm bản tiếng Anh' : 'chưa kích hoạt: chỉ nạp tiếng Việt'; ?></li>
		</ul>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="thp_demo_import">
			<input type="hidden" name="step" value="0">
			<?php wp_nonce_field( 'thp_demo_import' ); ?>
			<?php submit_button( 'Nạp dữ liệu mẫu', 'primary', 'submit', false, $theme_ok ? array() : array( 'disabled' => 'disabled' ) ); ?>
		</form>
		<p class="description">Quá trình chạy qua nhiều bước ngắn và tự chuyển trang, mất khoảng 1–2 phút. Đừng đóng tab cho đến khi thấy thông báo "Đã nạp xong".</p>
	</div>
	<?php
}

/**
 * Run one step, then redirect to the next one so each request stays short.
 */
add_action(
	'admin_post_thp_demo_import',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Bạn không có quyền thực hiện thao tác này.' );
		}
		check_admin_referer( 'thp_demo_import' );
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors -- some hosts disable it.
		}

		$steps   = thp_demo_steps();
		$index   = isset( $_REQUEST['step'] ) ? absint( $_REQUEST['step'] ) : 0;
		$skipped = isset( $_REQUEST['skipped'] ) ? sanitize_key( wp_unslash( $_REQUEST['skipped'] ) ) : '';
		if ( ! isset( $steps[ $index ] ) ) {
			wp_safe_redirect( admin_url( 'tools.php?page=thp-demo&thp_done=1' . ( $skipped ? '&thp_skipped=' . $skipped : '' ) ) );
			exit;
		}

		$status = thp_demo_run_step( $steps[ $index ] );
		if ( 'skipped' === $status && in_array( $steps[ $index ], array( 'languages', 'english' ), true ) ) {
			$skipped = 'en';
		}
		$next = 'again' === $status ? $index : $index + 1;

		// Built with add_query_arg, not wp_nonce_url: that one HTML-escapes "&"
		// to "&amp;", which breaks the query string of a redirect.
		$url = add_query_arg(
			array(
				'action'   => 'thp_demo_import',
				'step'     => $next,
				'skipped'  => $skipped,
				'_wpnonce' => wp_create_nonce( 'thp_demo_import' ),
			),
			admin_url( 'admin-post.php' )
		);
		wp_safe_redirect( $url );
		exit;
	}
);
