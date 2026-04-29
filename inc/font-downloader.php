<?php
/**
 * Local Font Downloader
 *
 * Downloads selected fonts from Bunny Fonts CDN to the local uploads directory
 * to ensure GDPR compliance and better performance.
 *
 * Flow on every Customizer save:
 *   1. Read the current font selection from theme_mods (already persisted by
 *      the time customize_save_after fires).
 *   2. Compare against the previously-downloaded set stored in fonts-meta.json.
 *   3. If the set is unchanged AND fonts.css already exists, return early —
 *      no HTTP requests made, no files touched.
 *   4. If the set changed (or fonts.css is missing), clear the directory,
 *      fetch the CSS from Bunny Fonts, download every .woff2/.woff file
 *      locally, rewrite the CSS to use absolute local URLs, and save both
 *      fonts.css and fonts-meta.json.
 *
 * @package Aestazia_Child
 */

/**
 * Initialise the WP_Filesystem abstraction layer and return the object.
 *
 * Extracted into its own function so both aestazia_child_download_fonts_locally()
 * and aestazia_child_clear_font_directory() share the same bootstrap logic
 * without duplicating it.
 *
 * @return WP_Filesystem_Base|false Filesystem object on success, false on failure.
 */
function aestazia_child_get_filesystem() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
	global $wp_filesystem;

	if ( ! empty( $wp_filesystem ) ) {
		return $wp_filesystem;
	}

	// WP_Filesystem() can return false when no credentials are available
	// (e.g. FTP method with no credentials stored). Fall back to the Direct
	// driver, which works when PHP has write access to the uploads directory —
	// the normal case for standard shared and VPS hosting.
	require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

	return new WP_Filesystem_Direct( null );
}

/**
 * Trigger the font download process when the Customizer is saved.
 *
 * Hooked to customize_save_after, at which point all settings have already
 * been persisted to the database. Reading via get_theme_mod() therefore
 * returns the values the user just saved, making the old post_value() calls
 * unnecessary (and incorrect — post_value() is only valid during the preview
 * phase, not after save).
 *
 * @param WP_Customize_Manager $manager Customizer object (unused; kept for
 *                                       hook signature compatibility).
 */
function aestazia_child_download_fonts_locally( $manager ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

	// --- 1. Read the current font selection. ---
	// get_theme_mod() is correct here: by the time customize_save_after fires,
	// WP has already written the new values to the database, so these calls
	// return exactly what the user selected in the Customizer.
	$fonts = array(
		get_theme_mod( 'aestazia_font_body',    '' ),
		get_theme_mod( 'aestazia_font_heading', '' ),
		get_theme_mod( 'aestazia_font_subhead', '' ),
	);

	$fonts = array_unique( array_filter( $fonts ) );

	$upload_dir = wp_upload_dir();
	$fonts_dir  = trailingslashit( $upload_dir['basedir'] ) . 'aestazia-fonts';

	// --- 2. If no fonts are selected, wipe the directory and return. ---
	if ( empty( $fonts ) ) {
		aestazia_child_clear_font_directory( $fonts_dir );
		return;
	}

	// --- 3. Compare with the previously-downloaded set. ---
	// fonts-meta.json is a small sidecar file that records which font names
	// were downloaded last time. If the set is identical and fonts.css exists,
	// there is nothing to do — skip every HTTP request.
	$css_file  = trailingslashit( $fonts_dir ) . 'fonts.css';
	$meta_file = trailingslashit( $fonts_dir ) . 'fonts-meta.json';

	$previously_downloaded = array();
	if ( file_exists( $meta_file ) ) {
		$raw = file_get_contents( $meta_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false !== $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$previously_downloaded = $decoded;
			}
		}
	}

	$fonts_sorted    = $fonts;
	$previous_sorted = $previously_downloaded;
	sort( $fonts_sorted );
	sort( $previous_sorted );

	if ( $fonts_sorted === $previous_sorted && file_exists( $css_file ) ) {
		// Font set unchanged and files already present — nothing to do.
		return;
	}

	// --- 4. Build the Bunny Fonts API request URL. ---
	$families = array();
	foreach ( $fonts as $font ) {
		// Font names from the Customizer have already been validated by
		// aestazia_child_sanitize_fonts() → sanitize_text_field(), so no
		// additional sanitization is needed here beyond what we pass to the URL.
		$families[] = rawurlencode( str_replace( ' ', '+', $font ) ) . ':400,500,600,700';
	}

	$api_url = 'https://fonts.bunny.net/css?family=' . implode( '|', $families );

	// --- 5. Fetch the CSS from Bunny Fonts. ---
	$response = wp_remote_get(
		$api_url,
		array(
			'timeout' => 15,
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			'Aestazia Child — Bunny Fonts CSS fetch failed: ' . $response->get_error_message()
		);
		return;
	}

	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			'Aestazia Child — Bunny Fonts CSS fetch returned HTTP ' .
			wp_remote_retrieve_response_code( $response )
		);
		return;
	}

	$css = wp_remote_retrieve_body( $response );

	// --- 6. Initialise the filesystem and prepare the directory. ---
	$fs = aestazia_child_get_filesystem();

	if ( false === $fs ) {
		error_log( 'Aestazia Child — could not initialise WP_Filesystem.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return;
	}

	// Clear the directory before writing new files so stale font files from
	// a previous selection do not accumulate. We do this AFTER the HTTP fetch
	// succeeds, so a network failure never leaves the directory empty.
	if ( $fs->is_dir( $fonts_dir ) ) {
		aestazia_child_clear_font_directory( $fonts_dir );
	}

	wp_mkdir_p( $fonts_dir );

	// --- 7. Download each .woff2 / .woff file and rewrite the CSS. ---
	// The regex anchors to fonts.bunny.net so we never follow a URL injected
	// into the CSS by a compromised CDN response.
	preg_match_all( '/url\((https:\/\/fonts\.bunny\.net[^)]+\.woff2?)\)/i', $css, $matches );

	if ( ! empty( $matches[1] ) ) {
		// Use the full absolute baseurl (not the root-relative path) so the
		// saved fonts.css works correctly on installs in subdirectories,
		// on multisite, and behind reverse proxies.
		$fonts_base_url = trailingslashit( $upload_dir['baseurl'] ) . 'aestazia-fonts';

		$urls = array_unique( $matches[1] );
		foreach ( $urls as $remote_url ) {
			$font_response = wp_remote_get(
				$remote_url,
				array(
					'timeout' => 15,
				)
			);

			if ( is_wp_error( $font_response ) || 200 !== wp_remote_retrieve_response_code( $font_response ) ) {
				// Log and skip this file; the loop continues so other fonts
				// in the same request still get downloaded.
				error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					'Aestazia Child — could not download font file: ' . $remote_url
				);
				continue;
			}

			$font_content = wp_remote_retrieve_body( $font_response );
			$filename     = basename( wp_parse_url( $remote_url, PHP_URL_PATH ) );

			$saved = $fs->put_contents(
				trailingslashit( $fonts_dir ) . $filename,
				$font_content,
				FS_CHMOD_FILE
			);

			if ( $saved ) {
				// Rewrite the remote URL to the local absolute URL in the CSS.
				// Using baseurl (https://example.com/wp-content/uploads/aestazia-fonts/)
				// rather than a root-relative path (/wp-content/uploads/aestazia-fonts/)
				// makes this work correctly in subdirectory installs.
				$local_url = trailingslashit( $fonts_base_url ) . $filename;
				$css       = str_replace( $remote_url, $local_url, $css );
			}
		}
	}

	// --- 8. Save the rewritten CSS and the meta sidecar. ---
	$fs->put_contents( $css_file, $css, FS_CHMOD_FILE );

	// Write the meta sidecar so the next save can detect whether the font
	// selection has changed and skip the download when it hasn't.
	$fs->put_contents( $meta_file, wp_json_encode( $fonts ), FS_CHMOD_FILE );
}
add_action( 'customize_save_after', 'aestazia_child_download_fonts_locally' );

/**
 * Delete all files inside the fonts directory without removing the directory.
 *
 * Called both when all fonts are deselected (full cleanup) and before a
 * fresh download (to remove stale files from a previous selection).
 *
 * @param string $dir Absolute path to the fonts directory.
 */
function aestazia_child_clear_font_directory( $dir ) {
	$fs = aestazia_child_get_filesystem();

	if ( false === $fs || ! $fs->is_dir( $dir ) ) {
		return;
	}

	// glob() is used intentionally here: WP_Filesystem has no native directory-
	// listing method that returns a simple flat list of file paths, and glob()
	// operates on the local filesystem directly, which is appropriate because
	// we constructed $dir from wp_upload_dir() — a trusted, server-side value.
	$files = glob( trailingslashit( $dir ) . '*' );
	if ( ! empty( $files ) ) {
		foreach ( $files as $file ) {
			if ( $fs->is_file( $file ) ) {
				$fs->delete( $file );
			}
		}
	}
}
