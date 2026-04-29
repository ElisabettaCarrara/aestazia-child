<?php
/**
 * Aestazia Child Customizer Functionality
 *
 * Handles the registration of Customizer sections and the generation of dynamic CSS.
 *
 * @package Aestazia_Child
 */

/**
 * Fetch the full list of font families available on Bunny Fonts.
 *
 * Results are cached in a transient for 7 days to avoid an HTTP request
 * on every Customizer page load. Falls back to a small built-in list if
 * the remote request fails.
 *
 * @return array Associative array of 'Font Name' => 'Font Name' (plus '' => 'Default').
 */
function aestazia_child_get_bunny_font_choices() {
	$transient_key = 'aestazia_bunny_fonts_v1';
	$cached        = get_transient( $transient_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		'https://fonts.bunny.net/list',
		array(
			'timeout'    => 5,
			'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
		)
	);

	$choices = array(
		'' => esc_html__( 'Default (Theme)', 'aestazia-child' ),
	);

	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( is_array( $data ) ) {
			foreach ( $data as $slug => $meta ) {
				// The API returns an object keyed by slug; the display name may be
				// in $meta['family'] or we can title-case the slug as fallback.
				if ( isset( $meta['family'] ) && '' !== $meta['family'] ) {
					$name = sanitize_text_field( $meta['family'] );
				} else {
					// Convert slug 'open-sans' → 'Open Sans' as a readable fallback.
					$name = ucwords( str_replace( '-', ' ', sanitize_key( $slug ) ) );
				}
				$choices[ $name ] = $name;
			}
			asort( $choices ); // Alphabetical, keeping '' first is fine since it has no letter.
		}
	}

	// If we only have the default entry the request failed — use the built-in fallback
	// and cache for only 1 hour so we retry sooner.
	if ( count( $choices ) <= 1 ) {
		$choices = array(
			''                 => esc_html__( 'Default (Theme)', 'aestazia-child' ),
			'Inter'            => 'Inter',
			'Lora'             => 'Lora',
			'Montserrat'       => 'Montserrat',
			'Playfair Display' => 'Playfair Display',
			'Poppins'          => 'Poppins',
			'Roboto'           => 'Roboto',
		);
		set_transient( $transient_key, $choices, HOUR_IN_SECONDS );
		return $choices;
	}

	set_transient( $transient_key, $choices, WEEK_IN_SECONDS );
	return $choices;
}

/**
 * Register all Customizer settings, sections, and panels.
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function aestazia_child_customize_register( $wp_customize ) {

	/**
	 * PANEL: Theme Design
	 */
	$wp_customize->add_panel(
		'aestazia_design_panel',
		array(
			'title'    => esc_html__( 'Theme Design', 'aestazia-child' ),
			'priority' => 20,
		)
	);

	/**
	 * SECTION: Bootstrap Palette
	 */
	$wp_customize->add_section(
		'aestazia_palette',
		array(
			'title'       => esc_html__( 'Bootstrap Palette', 'aestazia-child' ),
			'description' => esc_html__( 'Customize the main Bootstrap theme colors.', 'aestazia-child' ),
			'panel'       => 'aestazia_design_panel',
		)
	);

	$colors = array(
		'primary'   => array( 'label' => esc_html__( 'Primary Color', 'aestazia-child' ), 'default' => '#0d6efd' ),
		'secondary' => array( 'label' => esc_html__( 'Secondary Color', 'aestazia-child' ), 'default' => '#6c757d' ),
		'light'     => array( 'label' => esc_html__( 'Light Color', 'aestazia-child' ), 'default' => '#f8f9fa' ),
		'dark'      => array( 'label' => esc_html__( 'Dark Color', 'aestazia-child' ), 'default' => '#212529' ),
		'body_bg'   => array( 'label' => esc_html__( 'Body Background', 'aestazia-child' ), 'default' => '#ffffff' ),
	);

	foreach ( $colors as $key => $data ) {
		$setting_id = 'aestazia_color_' . $key;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $data['default'],
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $data['label'],
					'section' => 'aestazia_palette',
				)
			)
		);
	}

	/**
	 * SECTION: Typography
	 */
	$wp_customize->add_section(
		'aestazia_typography',
		array(
			'title' => esc_html__( 'Typography', 'aestazia-child' ),
			'panel' => 'aestazia_design_panel',
		)
	);

	$font_choices = aestazia_child_get_bunny_font_choices();

	$font_settings = array(
		'body'    => esc_html__( 'Body Font', 'aestazia-child' ),
		'heading' => esc_html__( 'Headings H1–H3', 'aestazia-child' ),
		'subhead' => esc_html__( 'Headings H4–H6', 'aestazia-child' ),
	);

	foreach ( $font_settings as $key => $label ) {
		$setting_id = 'aestazia_font_' . $key;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'aestazia_child_sanitize_fonts',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'type'    => 'select',
				'label'   => $label,
				'section' => 'aestazia_typography',
				'choices' => $font_choices,
			)
		);
	}

	/**
	 * SECTION: Category Colors
	 */
	$wp_customize->add_section(
		'aestazia_category_colors',
		array(
			'title' => esc_html__( 'Category Colors', 'aestazia-child' ),
			'panel' => 'aestazia_design_panel',
		)
	);

	$categories = get_categories( array( 'hide_empty' => false ) );

	foreach ( $categories as $cat ) {
		$setting_id = 'cat_color_' . $cat->term_id;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $cat->name,
					'section' => 'aestazia_category_colors',
				)
			)
		);
	}

	/**
	 * SECTION: Category Behavior (FIXED)
	 */
	$wp_customize->add_section(
		'aestazia_category_behavior',
		array(
			'title' => esc_html__( 'Category Color Application', 'aestazia-child' ),
			'panel' => 'aestazia_design_panel',
		)
	);

	$elements = array(
		'card_border' => esc_html__( 'Apply to Card Border', 'aestazia-child' ),
		'title'       => esc_html__( 'Apply to Post Title', 'aestazia-child' ),
		'read_more'   => esc_html__( 'Apply to Read More Button', 'aestazia-child' ),
	);

	foreach ( $elements as $key => $label ) {
		$setting_id = 'cat_color_apply_' . $key;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => 0,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'type'     => 'checkbox',
				'label'    => $label,
				'section'  => 'aestazia_category_behavior',
				'settings' => $setting_id,
			)
		);
	}
}
add_action( 'customize_register', 'aestazia_child_customize_register' );

/**
 * Sanitize font selection.
 *
 * Validates the submitted value against the current Bunny Fonts list
 * (from the transient cache). Falls back to '' if the value is not found.
 *
 * @param string $value The selected font name.
 * @return string Validated font name or empty string.
 */
function aestazia_child_sanitize_fonts( $value ) {
	if ( '' === $value ) {
		return '';
	}
	$valid_fonts = aestazia_child_get_bunny_font_choices();
	return array_key_exists( $value, $valid_fonts ) ? sanitize_text_field( $value ) : '';
}

/**
 * Build the CSS custom-property value for a font family.
 *
 * Each piece is sanitized individually before being concatenated, so the
 * assembled string never needs a second pass through esc_html() (which
 * would convert quotes to HTML entities and break the CSS).
 *
 * esc_attr() is the correct escaper for a value that will appear inside
 * a CSS string context: it encodes <, >, &, " and ' as HTML entities only
 * when the string is later placed in an HTML attribute — but here we call
 * it purely to strip any characters that should not appear in a font name
 * (control characters, HTML tags). The result is safe because every font
 * name coming in has already been validated by aestazia_child_sanitize_fonts(),
 * which passes it through sanitize_text_field(). esc_attr() therefore acts
 * as a final belt-and-suspenders guard.
 *
 * @param string $font     Validated font family name, e.g. 'Playfair Display'.
 * @param string $fallback Generic fallback stack, e.g. 'sans-serif'.
 * @return string Ready-to-embed CSS value, e.g. "'Playfair Display', sans-serif".
 */
function aestazia_child_font_css_value( $font, $fallback ) {
	return "'" . esc_attr( $font ) . "', " . $fallback;
}

/**
 * Output dynamic CSS in wp_head.
 *
 * All values are sanitized at the point they are added to the output
 * arrays, not with a blanket esc_html() at the end. Wrapping assembled
 * CSS in esc_html() is incorrect because it converts single quotes to
 * &#039; and double quotes to &quot;, which breaks font-family declarations
 * and any other CSS value that uses quotes.
 *
 * Safe output is achieved by:
 *   - sanitize_hex_color() on every color value.
 *   - aestazia_child_font_css_value() (which uses esc_attr() internally)
 *     on every font name.
 *   - sanitize_html_class() on every category slug.
 * None of these produce characters that need further HTML-escaping inside
 * a <style> block, so the final echo is preceded by a PHPCS ignore comment
 * that explains why.
 */
function aestazia_child_render_custom_css() {
	$root_vars     = array();
	$scoped_blocks = array();

	// 1. Global Palette.
	$palette_map = array(
		'primary'   => array( 'var' => '--bs-primary',   'default' => '#0d6efd' ),
		'secondary' => array( 'var' => '--bs-secondary', 'default' => '#6c757d' ),
		'light'     => array( 'var' => '--bs-light',     'default' => '#f8f9fa' ),
		'dark'      => array( 'var' => '--bs-dark',      'default' => '#212529' ),
		'body_bg'   => array( 'var' => '--bs-body-bg',   'default' => '#ffffff' ),
	);

	foreach ( $palette_map as $key => $data ) {
		$value = get_theme_mod( 'aestazia_color_' . $key, $data['default'] );
		// Only output if it differs from the standard default.
		if ( $value && strtolower( $value ) !== strtolower( $data['default'] ) ) {
			// sanitize_hex_color() returns a clean '#rrggbb' string with no
			// characters that need HTML-escaping inside a <style> block.
			$root_vars[] = $data['var'] . ': ' . sanitize_hex_color( $value );
		}
	}

	// 2. Typography vars.
	$body    = get_theme_mod( 'aestazia_font_body' );
	$heading = get_theme_mod( 'aestazia_font_heading' );
	$subhead = get_theme_mod( 'aestazia_font_subhead' );

	if ( $body ) {
		// aestazia_child_font_css_value() sanitizes the name with esc_attr()
		// before embedding it in the single-quoted CSS string.
		$root_vars[] = '--font-body: ' . aestazia_child_font_css_value( $body, 'sans-serif' );
		$root_vars[] = '--bs-body-font-family: var(--font-body)';
	}
	if ( $heading ) {
		$root_vars[] = '--font-heading-main: ' . aestazia_child_font_css_value( $heading, 'serif' );
	}
	if ( $subhead ) {
		$root_vars[] = '--font-heading-sub: ' . aestazia_child_font_css_value( $subhead, 'serif' );
	}

	// 3. Category system.
	$categories = get_categories( array( 'hide_empty' => false ) );
	foreach ( $categories as $cat ) {
		$color = get_theme_mod( 'cat_color_' . $cat->term_id );
		if ( ! $color ) {
			continue;
		}

		// sanitize_hex_color() and sanitize_html_class() both produce
		// strings that are safe inside a <style> block without further escaping.
		$color = sanitize_hex_color( $color );
		$slug  = sanitize_html_class( $cat->slug );

		$card_selector = '.post-card.primary-cat-' . $slug;
		$btn_selector  = '.post-card.primary-cat-' . $slug . ' .btn-primary';
		$card_rules    = array();
		$btn_rules     = array();

		if ( get_theme_mod( 'cat_color_apply_card_border' ) ) {
			$card_rules[] = '--post-card-border: ' . $color;
		}
		if ( get_theme_mod( 'cat_color_apply_title' ) ) {
			$card_rules[] = '--bs-link-color: ' . $color;
		}
		if ( get_theme_mod( 'cat_color_apply_read_more' ) ) {
			$btn_rules[] = '--bs-btn-bg: ' . $color;
			$btn_rules[] = '--bs-btn-border-color: ' . $color;
			$btn_rules[] = '--bs-btn-color: #fff';
			$btn_rules[] = '--bs-btn-hover-bg: ' . $color;
			$btn_rules[] = '--bs-btn-hover-border-color: ' . $color;
		}

		if ( ! empty( $card_rules ) ) {
			$scoped_blocks[] = $card_selector . ' { ' . implode( '; ', $card_rules ) . '; }';
		}
		if ( ! empty( $btn_rules ) ) {
			$scoped_blocks[] = $btn_selector . ' { ' . implode( '; ', $btn_rules ) . '; }';
		}
	}

	// Exit early if nothing to output.
	if ( empty( $root_vars ) && empty( $scoped_blocks ) ) {
		return;
	}

	// Assemble the final CSS string from individually-sanitized pieces.
	$final_css = '';
	if ( ! empty( $root_vars ) ) {
		$final_css .= ':root { ' . implode( '; ', $root_vars ) . '; }';
	}
	if ( ! empty( $scoped_blocks ) ) {
		$final_css .= implode( ' ', $scoped_blocks );
	}

	$final_css .= '
		body { font-family: var(--font-body, inherit) !important; }
		h1, h2, h3 { font-family: var(--font-heading-main, inherit) !important; }
		h4, h5, h6 { font-family: var(--font-heading-sub, inherit) !important; }
	';

	// The CSS string is safe to output without esc_html() because every
	// value it contains was sanitized individually above:
	//   - Colors: sanitize_hex_color()    → only [#0-9a-fA-F] characters.
	//   - Font names: esc_attr() via aestazia_child_font_css_value() → no HTML tags.
	//   - Slugs: sanitize_html_class()    → only [a-z0-9-_] characters.
	//   - Static strings: hard-coded in this function, no user input.
	// Applying esc_html() here would corrupt the output by converting the
	// single quotes in font-family values to &#039;, breaking the CSS.
	echo '<style id="aestazia-design-system">';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $final_css;
	echo '</style>';
}
add_action( 'wp_head', 'aestazia_child_render_custom_css' );

/**
 * Enqueue Bunny Fonts based on Customizer selection.
 *
 * On first page load after a save, fonts.css will have been written locally
 * by aestazia_child_download_fonts_locally() (font-downloader.php). We
 * enqueue that local file. If it does not exist yet (e.g. the download
 * failed), we fall back to the Bunny Fonts CDN so the selected fonts still
 * render — though that CDN request is not GDPR-local until the next
 * successful save triggers the downloader.
 */
function aestazia_child_enqueue_custom_fonts() {
	$fonts = array(
		get_theme_mod( 'aestazia_font_body' ),
		get_theme_mod( 'aestazia_font_heading' ),
		get_theme_mod( 'aestazia_font_subhead' ),
	);

	$fonts = array_unique( array_filter( $fonts ) );

	if ( empty( $fonts ) ) {
		return;
	}

	$upload_dir = wp_upload_dir();
	$fonts_dir  = trailingslashit( $upload_dir['basedir'] ) . 'aestazia-fonts';
	$fonts_url  = trailingslashit( $upload_dir['baseurl'] ) . 'aestazia-fonts';
	$css_file   = trailingslashit( $fonts_dir ) . 'fonts.css';

	if ( file_exists( $css_file ) ) {
		$url = trailingslashit( $fonts_url ) . 'fonts.css';
		wp_enqueue_style( 'aestazia-local-fonts', esc_url( $url ), array(), filemtime( $css_file ) );
	} else {
		// CDN fallback: fonts are served from Bunny Fonts until the next
		// Customizer save triggers a successful local download.
		$families = array();
		foreach ( $fonts as $font ) {
			$families[] = str_replace( ' ', '+', $font ) . ':400,500,600,700';
		}
		$url = 'https://fonts.bunny.net/css?family=' . implode( '|', $families );
		wp_enqueue_style( 'aestazia-bunny-fonts', esc_url( $url ), array(), '1.0' );
	}
}
add_action( 'wp_enqueue_scripts', 'aestazia_child_enqueue_custom_fonts' );
