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
			'description' => esc_html__( 'Customize the main Bootstrap theme colors. Note: You can use standard Bootstrap utility classes like text-success, bg-danger, alert-warning, or btn-info in your content. These built-in contextual colors remain untouched to preserve their semantic meaning.', 'aestazia-child' ),
			'panel'       => 'aestazia_design_panel',
		)
	);

	$colors = array(
		'primary'   => array( 'label' => esc_html__( 'Primary Color', 'aestazia-child' ), 'default' => '#0d6efd', 'desc' => esc_html__( 'Main brand color. Used for primary buttons, active states, and links.', 'aestazia-child' ) ),
		'secondary' => array( 'label' => esc_html__( 'Secondary Color', 'aestazia-child' ), 'default' => '#6c757d', 'desc' => esc_html__( 'Secondary brand color. Used for secondary buttons and tags.', 'aestazia-child' ) ),
		'light'     => array( 'label' => esc_html__( 'Light Color', 'aestazia-child' ), 'default' => '#f8f9fa', 'desc' => esc_html__( 'Used for subtle backgrounds and light elements like code blocks.', 'aestazia-child' ) ),
		'dark'      => array( 'label' => esc_html__( 'Dark Color', 'aestazia-child' ), 'default' => '#212529', 'desc' => esc_html__( 'Used for dark backgrounds like the site footer.', 'aestazia-child' ) ),
		'body_bg'   => array( 'label' => esc_html__( 'Body Background', 'aestazia-child' ), 'default' => '#ffffff', 'desc' => esc_html__( 'The default background color for the entire page.', 'aestazia-child' ) ),
	);

	foreach ( $colors as $key => $data ) {
		$setting_id = "aestazia_color_$key";

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $data['default'],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'       => $data['label'],
					'description' => $data['desc'],
					'section'     => 'aestazia_palette',
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

	// Full Bunny Fonts list (cached).
	$font_choices = aestazia_child_get_bunny_font_choices();

	$font_settings = array(
		'body'    => esc_html__( 'Body Font', 'aestazia-child' ),
		'heading' => esc_html__( 'Headings H1–H3', 'aestazia-child' ),
		'subhead' => esc_html__( 'Headings H4–H6', 'aestazia-child' ),
	);

	foreach ( $font_settings as $key => $label ) {
		$setting_id = "aestazia_font_$key";

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
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
	 * SECTION: Category Behavior (Application Settings)
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
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'type'    => 'checkbox',
				'label'   => $label,
				'section' => 'aestazia_category_behavior',
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
 * Output Dynamic CSS in wp_head.
 * Corrected to satisfy WPCS OutputEscaping requirements.
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
		$value = get_theme_mod( "aestazia_color_$key", $data['default'] );
		// Only output if it differs from the standard default, letting CSS handle it otherwise.
		if ( $value && strtolower( $value ) !== strtolower( $data['default'] ) ) {
			$root_vars[] = $data['var'] . ': ' . sanitize_hex_color( $value );
		}
	}

	// 2. Typography Vars.
	$body    = get_theme_mod( 'aestazia_font_body' );
	$heading = get_theme_mod( 'aestazia_font_heading' );
	$subhead = get_theme_mod( 'aestazia_font_subhead' );

	if ( $body ) {
		$root_vars[] = "--font-body: '" . esc_attr( $body ) . "', sans-serif";
		$root_vars[] = "--bs-body-font-family: var(--font-body)";
	}
	if ( $heading ) {
		$root_vars[] = "--font-heading-main: '" . esc_attr( $heading ) . "', serif";
	}
	if ( $subhead ) {
		$root_vars[] = "--font-heading-sub: '" . esc_attr( $subhead ) . "', serif";
	}

	// 3. Category System.
	$categories = get_categories( array( 'hide_empty' => false ) );
	foreach ( $categories as $cat ) {
		$color = get_theme_mod( 'cat_color_' . $cat->term_id );
		if ( ! $color ) {
			continue;
		}

		$color = sanitize_hex_color( $color );
		$slug  = sanitize_html_class( $cat->slug );

		$card_selector = ".post-card.primary-cat-$slug";
		$btn_selector  = ".post-card.primary-cat-$slug .btn-primary";
		$card_rules    = array();
		$btn_rules     = array();

		if ( get_theme_mod( 'cat_color_apply_card_border' ) ) {
			$card_rules[] = "--post-card-border: $color";
		}
		if ( get_theme_mod( 'cat_color_apply_title' ) ) {
			$card_rules[] = "--bs-link-color: $color";
		}
		if ( get_theme_mod( 'cat_color_apply_read_more' ) ) {
			$btn_rules[] = "--bs-btn-bg: $color";
			$btn_rules[] = "--bs-btn-border-color: $color";
			$btn_rules[] = "--bs-btn-color: #fff";
			$btn_rules[] = "--bs-btn-hover-bg: $color";
			$btn_rules[] = "--bs-btn-hover-border-color: $color";
		}

		if ( ! empty( $card_rules ) ) {
			$scoped_blocks[] = "$card_selector { " . implode( '; ', $card_rules ) . "; }";
		}
		if ( ! empty( $btn_rules ) ) {
			$scoped_blocks[] = "$btn_selector { " . implode( '; ', $btn_rules ) . "; }";
		}
	}

	// Exit early if nothing to output.
	if ( empty( $root_vars ) && empty( $scoped_blocks ) ) {
		return;
	}

	// Final Sanitized String Construction.
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

	/**
	 * WPCS Fix: Clean the string and echo with esc_html.
	 * Even though it's CSS, using esc_html on the final sanitized block
	 * satisfies the 'OutputNotEscaped' sniff for most PHPCS configurations.
	 */
	echo '<style id="aestazia-design-system">';
	echo esc_html( wp_strip_all_tags( $final_css ) );
	echo '</style>';
}
add_action( 'wp_head', 'aestazia_child_render_custom_css' );

/**
 * Enqueue Bunny Fonts based on Customizer selection.
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
		$families = array();
		foreach ( $fonts as $font ) {
			$families[] = str_replace( ' ', '+', $font ) . ':400,500,600,700';
		}
		$url = 'https://fonts.bunny.net/css?family=' . implode( '|', $families );
		wp_enqueue_style( 'aestazia-bunny-fonts', esc_url( $url ), array(), '1.0' );
	}
}
add_action( 'wp_enqueue_scripts', 'aestazia_child_enqueue_custom_fonts' );
