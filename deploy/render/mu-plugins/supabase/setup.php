<?php
/**
 * Run on every boot with `wp eval-file`:
 *  - enables Row Level Security on the WordPress tables so Supabase's public
 *    REST API cannot read or change them (WordPress connects as the table
 *    owner, which RLS does not restrict);
 *  - makes sure the public media bucket exists.
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

if ( function_exists( 'thp_supabase_config' ) && thp_supabase_config() ) {
	$config   = thp_supabase_config();
	$response = thp_supabase_request(
		'POST',
		'/bucket',
		array(
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => wp_json_encode(
				array(
					'id'     => $config['bucket'],
					'name'   => $config['bucket'],
					'public' => true,
				)
			),
		)
	);
	$code = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );
	// 200 = created; 400/409 = already exists.
	echo in_array( $code, array( 200, 400, 409 ), true ) ? "Media bucket ready.\n" : 'Bucket check failed: ' . ( is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_body( $response ) ) . "\n";
}
