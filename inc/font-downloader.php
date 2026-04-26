<?php
/**
 * Local Font Downloader
 *
 * Downloads selected fonts from Bunny Fonts CDN to the local uploads directory
 * to ensure GDPR compliance and better performance.
 *
 * @package Aestazia_Child
 */

/**
 * Trigger the font download process when Customizer is saved.
 *
 * @param WP_Customize_Manager $manager Customizer object.
 */
function aestazia_child_download_fonts_locally( $manager ) {
	$body_setting    = $manager->get_setting( 'aestazia_font_body' );
	$heading_setting = $manager->get_setting( 'aestazia_font_heading' );
	$subhead_setting = $manager->get_setting( 'aestazia_font_subhead' );

	$fonts = array(
		$body_setting && null !== $body_setting->post_value() ? $body_setting->post_value() : get_theme_mod( 'aestazia_font_body' ),
		$heading_setting && null !== $heading_setting->post_value() ? $heading_setting->post_value() : get_theme_mod( 'aestazia_font_heading' ),
		$subhead_setting && null !== $subhead_setting->post_value() ? $subhead_setting->post_value() : get_theme_mod( 'aestazia_font_subhead' ),
	);

	$fonts = array_unique( array_filter( $fonts ) );

	$upload_dir = wp_upload_dir();
	$fonts_dir  = trailingslashit( $upload_dir['basedir'] ) . 'aestazia-fonts';

	// If no fonts are selected, clean up the directory and return.
	if ( empty( $fonts ) ) {
		aestazia_child_clear_font_directory( $fonts_dir );
		return;
	}

	$families = array();
	foreach ( $fonts as $font ) {
		$families[] = str_replace( ' ', '+', $font ) . ':400,500,600,700';
	}

	$api_url = 'https://fonts.bunny.net/css?family=' . implode( '|', $families );

	// Fetch the CSS from Bunny Fonts.
	$response = wp_remote_get(
		$api_url,
		array(
			'timeout' => 15,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return; // Silent failure on API error.
	}

	$css = wp_remote_retrieve_body( $response );

	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
	global $wp_filesystem;

	$fs = $wp_filesystem;

	if ( empty( $fs ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		$fs = new WP_Filesystem_Direct( null );
	}

	if ( ! $fs->is_dir( $fonts_dir ) ) {
		wp_mkdir_p( $fonts_dir );
	} else {
		// Clear old fonts from the directory.
		aestazia_child_clear_font_directory( $fonts_dir );
	}

	// Extract all woff2 URLs from the CSS.
	preg_match_all( '/url\((https:\/\/fonts\.bunny\.net[^)]+\.woff2)\)/i', $css, $matches );

	$fonts_url = trailingslashit( $upload_dir['baseurl'] ) . 'aestazia-fonts';

	if ( ! empty( $matches[1] ) ) {
		$urls = array_unique( $matches[1] );
		foreach ( $urls as $url ) {
			// Download the font file.
			$font_response = wp_remote_get(
				$url,
				array(
					'timeout' => 15,
				)
			);

			if ( is_wp_error( $font_response ) || 200 !== wp_remote_retrieve_response_code( $font_response ) ) {
				continue;
			}

			$font_content = wp_remote_retrieve_body( $font_response );
			$filename     = basename( wp_parse_url( $url, PHP_URL_PATH ) );

			// Save the font file locally.
			$saved = $fs->put_contents( trailingslashit( $fonts_dir ) . $filename, $font_content, FS_CHMOD_FILE );

			if ( $saved ) {
				// Replace the remote URL with the local URL in the CSS.
				$local_url = trailingslashit( $fonts_url ) . $filename;
				$css       = str_replace( $url, $local_url, $css );
			}
		}
	}

	// Save the final CSS file.
	$fs->put_contents( trailingslashit( $fonts_dir ) . 'fonts.css', $css, FS_CHMOD_FILE );
}
add_action( 'customize_save_after', 'aestazia_child_download_fonts_locally' );

/**
 * Delete all files in the fonts directory.
 *
 * @param string $dir The directory path to clear.
 */
function aestazia_child_clear_font_directory( $dir ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
	global $wp_filesystem;

	$fs = $wp_filesystem;

	if ( empty( $fs ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		$fs = new WP_Filesystem_Direct( null );
	}

	if ( ! $fs->is_dir( $dir ) ) {
		return;
	}

	$files = glob( trailingslashit( $dir ) . '*' );
	if ( ! empty( $files ) ) {
		foreach ( $files as $file ) {
			if ( $fs->is_file( $file ) ) {
				$fs->delete( $file );
			}
		}
	}
}
