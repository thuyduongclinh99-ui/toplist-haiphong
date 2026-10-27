<?php
/**
 * Run on every boot with `wp eval-file`:
 *  - enables Row Level Security on the WordPress tables so Supabase's public
 *    REST API cannot read or change them (WordPress connects as the table
 *    owner, which RLS does not restrict);
 *  - re-imports featured images that never reached the Storage bucket (for
 *    example when the bucket or keys were not set up yet), since local copies
 *    are wiped on every restart.
 *
 * Not loaded as a must-use plugin (it sits in a sub-folder).
 *
 * @package toplist-render
 */

global $wpdb;

$conn = isset( $GLOBALS['pg4wp_conn'] ) ? $GLOBALS['pg4wp_conn'] : null;
if ( $conn ) {
	$prefix = pg_escape_string( $conn, str_replace( '_', '\\_', $wpdb->prefix ) );
	$sql    = "DO \$\$ DECLARE r record; BEGIN
		FOR r IN SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tablename LIKE '{$prefix}%' LOOP
			EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY', r.tablename);
		END LOOP;
	END \$\$;";
	$result = pg_query( $conn, $sql );
	echo $result ? "Row Level Security enabled on WordPress tables.\n" : 'RLS failed: ' . pg_last_error( $conn ) . "\n";
} else {
	echo "No PostgreSQL connection (PG4WP not active?), skipping RLS.\n";
}

if ( ! function_exists( 'thp_supabase_config' ) || ! thp_supabase_config() || ! function_exists( 'thp_demo_run_step_fully' ) || '1' === get_option( 'thp_demo_imported' ) && '1' === get_option( 'thp_media_synced' ) ) {
	return;
}

// Only check once the sample import has created its posts.
if ( '1' !== get_option( 'thp_demo_imported' ) ) {
	return;
}

$attachments = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'lang'           => '',
	)
);
$broken = 0;
foreach ( $attachments as $id ) {
	$head = wp_remote_head( wp_get_attachment_url( $id ), array( 'timeout' => 20 ) );
	if ( is_wp_error( $head ) || 200 !== (int) wp_remote_retrieve_response_code( $head ) ) {
		wp_delete_attachment( $id, true );
		++$broken;
	}
}

if ( $broken ) {
	echo "Re-importing {$broken} images missing from Storage...\n";
	thp_demo_run_step_fully( 'images' );
	// English translations share the Vietnamese featured image.
	if ( function_exists( 'pll_get_post' ) ) {
		foreach ( thp_demo_data( 'posts.php' ) as $item ) {
			$vi    = get_page_by_path( $item['slug'], OBJECT, 'post' );
			$en    = $vi ? pll_get_post( $vi->ID, 'en' ) : 0;
			$thumb = $vi ? get_post_thumbnail_id( $vi->ID ) : 0;
			if ( $en && $thumb ) {
				set_post_thumbnail( $en, $thumb );
			}
		}
	}
}

// Mark as synced only when every featured image is now reachable.
$missing = 0;
foreach ( thp_demo_data( 'posts.php' ) as $item ) {
	$post = $item['image'] ? get_page_by_path( $item['slug'], OBJECT, 'post' ) : null;
	if ( $post ) {
		$head = wp_remote_head( (string) get_the_post_thumbnail_url( $post, 'full' ), array( 'timeout' => 20 ) );
		if ( is_wp_error( $head ) || 200 !== (int) wp_remote_retrieve_response_code( $head ) ) {
			++$missing;
		}
	}
}
if ( 0 === $missing ) {
	update_option( 'thp_media_synced', '1' );
	echo "All featured images are in Storage.\n";
} else {
	echo "{$missing} featured images still missing from Storage; will retry on next boot.\n";
}
