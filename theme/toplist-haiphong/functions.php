<?php
/**
 * Toplist Hải Phòng theme bootstrap.
 *
 * @package toplist-haiphong
 */

defined( 'ABSPATH' ) || exit;

define( 'THP_VERSION', '1.0.0' );
define( 'THP_DIR', get_template_directory() );
define( 'THP_URI', get_template_directory_uri() );

require THP_DIR . '/inc/i18n.php';
require THP_DIR . '/inc/setup.php';
require THP_DIR . '/inc/place-meta.php';
require THP_DIR . '/inc/queries.php';
require THP_DIR . '/inc/template-tags.php';
