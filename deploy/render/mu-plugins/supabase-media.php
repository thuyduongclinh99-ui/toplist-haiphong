<?php
/**
 * Plugin Name: Supabase Media
 * Description: Lưu ảnh tải lên vào Supabase Storage (bucket công khai) vì ổ đĩa của Render bị xóa mỗi lần khởi động lại.
 *
 * Configured by environment variables: SUPABASE_URL, SUPABASE_SERVICE_ROLE_KEY
 * and SUPABASE_BUCKET (default "media"). Without them WordPress keeps files locally.
 *
 * @package toplist-render
 */

defined( 'ABSPATH' ) || exit;

/**
 * Storage settings from the environment, or null when not configured.
 *
 * @return array|null { api: string, key: string, bucket: string, public: string }
 */
function thp_supabase_config() {
	$url    = rtrim( (string) getenv( 'SUPABASE_URL' ), '/' );
	$key    = (string) getenv( 'SUPABASE_SERVICE_ROLE_KEY' );
	$bucket = getenv( 'SUPABASE_BUCKET' ) ? getenv( 'SUPABASE_BUCKET' ) : 'media';
	if ( '' === $url || '' === $key ) {
		return null;
	}
	return array(
		'api'    => $url . '/storage/v1',
		'key'    => $key,
		'bucket' => $bucket,
		'public' => $url . '/storage/v1/object/public/' . $bucket,
	);
}

/**
 * Supabase Storage API request.
 *
 * @param string $method HTTP method.
 * @param string $path   Path after /storage/v1.
 * @param array  $args   Extra wp_remote_request args (headers, body).
 * @return array|WP_Error
 */
function thp_supabase_request( $method, $path, $args = array() ) {
	$config = thp_supabase_config();
	if ( ! $config ) {
		return new WP_Error( 'thp_supabase', 'Supabase Storage is not configured.' );
	}
	$args            = wp_parse_args( $args, array( 'headers' => array() ) );
	$args['method']  = $method;
	$args['timeout'] = 60;
	$args['headers'] = array_merge(
		array(
			'Authorization' => 'Bearer ' . $config['key'],
			'apikey'        => $config['key'],
		),
		$args['headers']
	);
	return wp_remote_request( $config['api'] . $path, $args );
}

/**
 * Upload one file under its uploads-relative path, replacing any existing copy.
 *
 * @param string $file     Absolute local path.
 * @param string $relative Path inside the bucket, e.g. 2026/10/photo.webp.
 */
function thp_supabase_put( $file, $relative ) {
	$config = thp_supabase_config();
	if ( ! $config || ! is_readable( $file ) ) {
		return;
	}
	$type     = wp_check_filetype( $file );
	$response = thp_supabase_request(
		'POST',
		'/object/' . $config['bucket'] . '/' . ltrim( $relative, '/' ),
		array(
			'headers' => array(
				'Content-Type'  => $type['type'] ? $type['type'] : 'application/octet-stream',
				'x-upsert'      => 'true',
				'Cache-Control' => 'max-age=31536000',
			),
			'body'    => file_get_contents( $file ), // phpcs:ignore WordPress.WP.AlternativeFunctions -- local file.
		)
	);
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) >= 300 ) {
		error_log( 'Supabase upload failed for ' . $relative . ': ' . ( is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_body( $response ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	}
}

/**
 * Every file of an attachment (original, scaled copy, generated sizes), relative to uploads.
 *
 * @param int   $id   Attachment id.
 * @param array $meta Attachment metadata.
 * @return string[] Map of absolute path => relative path.
 */
function thp_supabase_attachment_files( $id, $meta ) {
	$main  = get_attached_file( $id, true );
	$files = array();
	if ( $main ) {
		$files[ $main ] = _wp_relative_upload_path( $main );
	}
	$dir = $main ? dirname( $main ) : '';
	$rel = $main ? dirname( _wp_relative_upload_path( $main ) ) : '';
	foreach ( (array) ( isset( $meta['sizes'] ) ? $meta['sizes'] : array() ) as $size ) {
		if ( ! empty( $size['file'] ) && $dir ) {
			$files[ $dir . '/' . $size['file'] ] = ltrim( $rel . '/' . $size['file'], './' );
		}
	}
	if ( ! empty( $meta['original_image'] ) && $dir ) {
		$files[ $dir . '/' . $meta['original_image'] ] = ltrim( $rel . '/' . $meta['original_image'], './' );
	}
	return $files;
}

if ( thp_supabase_config() ) {
	// Public URLs of uploads point at the bucket; files are still written locally first.
	add_filter(
		'upload_dir',
		function ( $dirs ) {
			$config          = thp_supabase_config();
			$dirs['baseurl'] = $config['public'];
			$dirs['url']     = $config['public'] . $dirs['subdir'];
			return $dirs;
		}
	);

	// After WordPress has generated the image sizes, push every file to the bucket.
	add_filter(
		'wp_generate_attachment_metadata',
		function ( $meta, $id ) {
			foreach ( thp_supabase_attachment_files( $id, $meta ) as $file => $relative ) {
				thp_supabase_put( $file, $relative );
			}
			return $meta;
		},
		20,
		2
	);

	// Remove the bucket copies when an attachment is deleted.
	add_action(
		'delete_attachment',
		function ( $id ) {
			$config = thp_supabase_config();
			$paths  = array_values( thp_supabase_attachment_files( $id, wp_get_attachment_metadata( $id ) ) );
			if ( $paths ) {
				thp_supabase_request(
					'DELETE',
					'/object/' . $config['bucket'],
					array(
						'headers' => array( 'Content-Type' => 'application/json' ),
						'body'    => wp_json_encode( array( 'prefixes' => $paths ) ),
					)
				);
			}
		}
	);
}
