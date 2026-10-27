<?php
/**
 * Place details stored on each post: rating, review count, address, price, phone, views.
 *
 * @package toplist-haiphong
 */

defined( 'ABSPATH' ) || exit;

/**
 * Editable place fields: meta key => [label, input type, schema type].
 *
 * @return array
 */
function thp_place_fields() {
	return array(
		'_thp_rating'       => array( __( 'Điểm đánh giá (0 - 5)', 'toplist-haiphong' ), 'number', 'number' ),
		'_thp_review_count' => array( __( 'Số lượt đánh giá', 'toplist-haiphong' ), 'number', 'integer' ),
		'_thp_address'      => array( __( 'Địa chỉ', 'toplist-haiphong' ), 'text', 'string' ),
		'_thp_price'        => array( __( 'Khoảng giá', 'toplist-haiphong' ), 'text', 'string' ),
		'_thp_phone'        => array( __( 'Số điện thoại', 'toplist-haiphong' ), 'text', 'string' ),
	);
}

add_action(
	'init',
	function () {
		$fields               = thp_place_fields();
		$fields['_thp_views'] = array( '', '', 'integer' );

		foreach ( $fields as $key => $field ) {
			register_post_meta(
				'post',
				$key,
				array(
					'type'          => $field[2],
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
);

add_action(
	'add_meta_boxes_post',
	function () {
		add_meta_box( 'thp-place', __( 'Thông tin địa điểm', 'toplist-haiphong' ), 'thp_render_place_box', 'post', 'normal', 'high' );
	}
);

/**
 * Meta box markup.
 *
 * @param WP_Post $post Current post.
 */
function thp_render_place_box( $post ) {
	wp_nonce_field( 'thp_place_save', 'thp_place_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( thp_place_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$attrs = '_thp_rating' === $key ? ' min="0" max="5" step="0.1"' : ( 'number' === $field[1] ? ' min="0" step="1"' : '' );
		printf(
			'<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><input class="regular-text" type="%3$s" id="%1$s" name="%1$s" value="%4$s"%5$s></td></tr>',
			esc_attr( $key ),
			esc_html( $field[0] ),
			esc_attr( $field[1] ),
			esc_attr( $value ),
			$attrs // phpcs:ignore WordPress.Security.EscapeOutput -- static attribute string.
		);
	}
	echo '</tbody></table>';
}

add_action(
	'save_post_post',
	function ( $post_id ) {
		if ( ! isset( $_POST['thp_place_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['thp_place_nonce'] ), 'thp_place_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( thp_place_fields() as $key => $field ) {
			$raw = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
			if ( '' === $raw ) {
				delete_post_meta( $post_id, $key );
				continue;
			}
			if ( '_thp_rating' === $key ) {
				$raw = (string) min( 5, max( 0, round( (float) str_replace( ',', '.', $raw ), 1 ) ) );
			} elseif ( 'integer' === $field[2] ) {
				$raw = (string) absint( $raw );
			}
			update_post_meta( $post_id, $key, $raw );
		}
	}
);

/**
 * Count one view per single-post page load (skips previews and logged-in editors).
 */
add_action(
	'wp',
	function () {
		if ( ! is_singular( 'post' ) || is_preview() || current_user_can( 'edit_posts' ) ) {
			return;
		}
		$id = get_queried_object_id();
		update_post_meta( $id, '_thp_views', (int) get_post_meta( $id, '_thp_views', true ) + 1 );
	}
);
