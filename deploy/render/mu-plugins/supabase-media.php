<?php
/**
 * Plugin Name: Supabase Media
 * Description: Lưu ảnh tải lên vào Supabase Storage (bucket công khai) vì ổ đĩa của Render bị xóa mỗi lần khởi động lại.
 *
 * Uses Supabase Storage's S3-compatible API (works with the current secret-key
 * model). Configured by environment variables:
 *   SUPABASE_URL                    https://<ref>.supabase.co
 *   SUPABASE_S3_ACCESS_KEY_ID       Storage → Settings → S3 access keys
 *   SUPABASE_S3_SECRET_ACCESS_KEY
 *   SUPABASE_S3_REGION              project region, default ap-southeast-1
 *   SUPABASE_BUCKET                 public bucket, default "media"
 * Without them WordPress keeps files locally.
 *
 * @package toplist-render
 */

defined( 'ABSPATH' ) || exit;

/**
 * Storage settings from the environment, or null when not configured.
 *
 * @return array|null
 */
function thp_supabase_config() {
	$url    = rtrim( (string) getenv( 'SUPABASE_URL' ), '/' );
	$key_id = (string) getenv( 'SUPABASE_S3_ACCESS_KEY_ID' );
	$secret = (string) getenv( 'SUPABASE_S3_SECRET_ACCESS_KEY' );
	if ( '' === $url || '' === $key_id || '' === $secret ) {
		return null;
	}
	$bucket = getenv( 'SUPABASE_BUCKET' ) ? getenv( 'SUPABASE_BUCKET' ) : 'media';
	// https://<ref>.supabase.co → https://<ref>.storage.supabase.co/storage/v1/s3 (the endpoint shown in Storage → S3).
	$s3 = getenv( 'SUPABASE_S3_ENDPOINT' ) ? rtrim( getenv( 'SUPABASE_S3_ENDPOINT' ), '/' ) : preg_replace( '#^(https://[^.]+)\.supabase\.co$#', '$1.storage.supabase.co', $url ) . '/storage/v1/s3';
	return array(
		's3'     => $s3,
		'region' => getenv( 'SUPABASE_S3_REGION' ) ? getenv( 'SUPABASE_S3_REGION' ) : 'ap-southeast-1',
		'key_id' => $key_id,
		'secret' => $secret,
		'bucket' => $bucket,
		'public' => $url . '/storage/v1/object/public/' . $bucket,
	);
}

/**
 * AWS Signature Version 4 "Authorization" header for an S3 request.
 *
 * @param string $method   HTTP method.
 * @param string $uri      Canonical (already URI-encoded) path.
 * @param array  $headers  Headers to sign, lowercase names; must include host,
 *                         x-amz-date and x-amz-content-sha256.
 * @param string $region   Region.
 * @param string $key_id   Access key id.
 * @param string $secret   Secret access key.
 * @return string
 */
function thp_s3_authorization( $method, $uri, $headers, $region, $key_id, $secret ) {
	ksort( $headers );
	$canonical_headers = '';
	foreach ( $headers as $name => $value ) {
		$canonical_headers .= $name . ':' . trim( $value ) . "\n";
	}
	$signed_headers = implode( ';', array_keys( $headers ) );
	$amz_date       = $headers['x-amz-date'];
	$date           = substr( $amz_date, 0, 8 );
	$scope          = $date . '/' . $region . '/s3/aws4_request';

	$canonical_request = implode( "\n", array( $method, $uri, '', $canonical_headers, $signed_headers, $headers['x-amz-content-sha256'] ) );
	$string_to_sign    = implode( "\n", array( 'AWS4-HMAC-SHA256', $amz_date, $scope, hash( 'sha256', $canonical_request ) ) );

	$signing_key = hash_hmac( 'sha256', 'aws4_request', hash_hmac( 'sha256', 's3', hash_hmac( 'sha256', $region, hash_hmac( 'sha256', $date, 'AWS4' . $secret, true ), true ), true ), true );
	$signature   = hash_hmac( 'sha256', $string_to_sign, $signing_key );

	return 'AWS4-HMAC-SHA256 Credential=' . $key_id . '/' . $scope . ', SignedHeaders=' . $signed_headers . ', Signature=' . $signature;
}

/**
 * Signed request to one object in the bucket.
 *
 * @param string $method       PUT or DELETE.
 * @param string $relative     Object key, e.g. 2026/10/photo.webp.
 * @param string $body         Request body.
 * @param string $content_type Content type for uploads.
 * @return array|WP_Error
 */
function thp_s3_object_request( $method, $relative, $body = '', $content_type = '' ) {
	$config = thp_supabase_config();
	if ( ! $config ) {
		return new WP_Error( 'thp_supabase', 'Supabase Storage is not configured.' );
	}
	$endpoint = wp_parse_url( $config['s3'] );
	$segments = array_map( 'rawurlencode', explode( '/', ltrim( $relative, '/' ) ) );
	$uri      = ( isset( $endpoint['path'] ) ? rtrim( $endpoint['path'], '/' ) : '' ) . '/' . rawurlencode( $config['bucket'] ) . '/' . implode( '/', $segments );
	$headers  = array(
		'host'                 => $endpoint['host'],
		'x-amz-content-sha256' => hash( 'sha256', $body ),
		'x-amz-date'           => gmdate( 'Ymd\THis\Z' ),
	);
	if ( '' !== $content_type ) {
		$headers['content-type'] = $content_type;
	}
	$headers['authorization'] = thp_s3_authorization( $method, $uri, $headers, $config['region'], $config['key_id'], $config['secret'] );
	unset( $headers['host'] ); // Added by the HTTP client.

	return wp_remote_request(
		$endpoint['scheme'] . '://' . $endpoint['host'] . $uri,
		array(
			'method'  => $method,
			'headers' => $headers,
			'body'    => $body,
			'timeout' => 60,
		)
	);
}

/**
 * Upload one file under its uploads-relative path, replacing any existing copy.
 *
 * @param string $file     Absolute local path.
 * @param string $relative Path inside the bucket.
 * @return bool Whether the upload succeeded.
 */
function thp_supabase_put( $file, $relative ) {
	if ( ! is_readable( $file ) ) {
		return false;
	}
	$type     = wp_check_filetype( $file );
	$response = thp_s3_object_request( 'PUT', $relative, file_get_contents( $file ), $type['type'] ? $type['type'] : 'application/octet-stream' ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- local file.
	$code     = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 ) {
		error_log( 'Supabase upload failed for ' . $relative . ': ' . ( is_wp_error( $response ) ? $response->get_error_message() : $code . ' ' . wp_remote_retrieve_body( $response ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		return false;
	}
	return true;
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
			foreach ( thp_supabase_attachment_files( $id, wp_get_attachment_metadata( $id ) ) as $relative ) {
				thp_s3_object_request( 'DELETE', $relative );
			}
		}
	);
}
