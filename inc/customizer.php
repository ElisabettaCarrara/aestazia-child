<?php

/**
 * =========================
 * CUSTOMIZER REGISTRATION
 * =========================
 */
add_action('customize_register', function ($wp_customize) {

    /**
     * PANEL: Theme Design
     */
    $wp_customize->add_panel('aestazia_design_panel', [
        'title'    => 'Theme Design',
        'priority' => 20,
    ]);

    /**
     * =========================
     * SECTION: Bootstrap Palette
     * =========================
     */
    $wp_customize->add_section('aestazia_palette', [
        'title' => 'Bootstrap Palette',
        'panel' => 'aestazia_design_panel',
    ]);

    $colors = [
        'primary'   => 'Primary Color',
        'secondary' => 'Secondary Color',
        'body_bg'   => 'Body Background',
        'body_text' => 'Body Text',
        'heading'   => 'Heading Color',
        'accent'    => 'Accent Color',
    ];

    foreach ($colors as $key => $label) {

        $setting_id = "aestazia_color_$key";

        $wp_customize->add_setting($setting_id, [
            'default'           => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        $wp_customize->add_control(new WP_Customize_Color_Control(
            $wp_customize,
            $setting_id,
            [
                'label'   => $label,
                'section' => 'aestazia_palette',
            ]
        ));
    }

    /**
     * =========================
     * SECTION: Typography
     * =========================
     */
    $wp_customize->add_section('aestazia_typography', [
        'title' => 'Typography',
        'panel' => 'aestazia_design_panel',
    ]);

    $font_choices = [
        '' => 'Default (Theme)',
        'Inter' => 'Inter',
        'Roboto' => 'Roboto',
        'Lora' => 'Lora',
        'Playfair Display' => 'Playfair Display',
        'Poppins' => 'Poppins',
        'Montserrat' => 'Montserrat',
    ];

    $font_settings = [
        'body'    => 'Body Font',
        'heading' => 'Headings H1–H3',
        'subhead' => 'Headings H4–H6',
    ];

    foreach ($font_settings as $key => $label) {

        $setting_id = "aestazia_font_$key";

        $wp_customize->add_setting($setting_id, [
            'default'           => '',
            'sanitize_callback' => function ($value) use ($font_choices) {
                return array_key_exists($value, $font_choices) ? $value : '';
            },
        ]);

        $wp_customize->add_control($setting_id, [
            'type'    => 'select',
            'label'   => $label,
            'section' => 'aestazia_typography',
            'choices' => $font_choices,
        ]);
    }

    /**
     * =========================
     * SECTION: Category Colors
     * =========================
     */
    $wp_customize->add_section('aestazia_category_colors', [
        'title' => 'Category Colors',
        'panel' => 'aestazia_design_panel',
    ]);

    $categories = get_categories(['hide_empty' => false]);

    foreach ($categories as $cat) {

        $setting_id = 'cat_color_' . $cat->term_id;

        $wp_customize->add_setting($setting_id, [
            'default'           => '',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);

        $wp_customize->add_control(new WP_Customize_Color_Control(
            $wp_customize,
            $setting_id,
            [
                'label'   => $cat->name,
                'section' => 'aestazia_category_colors',
            ]
        ));
    }

    /**
     * =========================
     * SECTION: Category Behavior
     * =========================
     */
    $wp_customize->add_section('aestazia_category_behavior', [
        'title' => 'Category Color Application',
        'panel' => 'aestazia_design_panel',
    ]);

    $elements = [
        'card_border' => 'Apply to Card Border',
        'title'       => 'Apply to Post Title',
        'read_more'   => 'Apply to Read More Button',
    ];

    foreach ($elements as $key => $label) {

        $setting_id = 'cat_color_apply_' . $key;

        $wp_customize->add_setting($setting_id, [
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ]);

        $wp_customize->add_control($setting_id, [
            'type'    => 'checkbox',
            'label'   => $label,
            'section' => 'aestazia_category_behavior',
        ]);
    }
});


/**
 * =========================
 * SINGLE CSS OUTPUT (FINAL)
 * =========================
 */
add_action('wp_head', function () {

    $root_vars = [];
    $scoped_blocks = [];

    /**
     * GLOBAL PALETTE
     */
    $map = [
        'primary'   => '--bs-primary',
        'secondary' => '--bs-secondary',
        'body_bg'   => '--bs-body-bg',
        'body_text' => '--bs-body-color',
        'heading'   => '--bs-heading-color',
        'accent'    => '--color-accent',
    ];

    foreach ($map as $key => $var) {
        $value = get_theme_mod("aestazia_color_$key");
        if ($value) {
            $root_vars[] = "$var: $value";
        }
    }

    /**
     * TYPOGRAPHY
     */
    $body    = get_theme_mod('aestazia_font_body');
    $heading = get_theme_mod('aestazia_font_heading');
    $subhead = get_theme_mod('aestazia_font_subhead');

    if ($body) {
        $root_vars[] = "--font-body: '$body', sans-serif";
    }

    if ($heading) {
        $root_vars[] = "--font-heading-main: '$heading', serif";
    }

    if ($subhead) {
        $root_vars[] = "--font-heading-sub: '$subhead', serif";
    }

    /**
     * CATEGORY SYSTEM
     */
    $categories = get_categories(['hide_empty' => false]);

    foreach ($categories as $cat) {

        $color = get_theme_mod('cat_color_' . $cat->term_id);

        if (!$color) continue;

        $color = sanitize_hex_color($color);
        $slug  = sanitize_html_class($cat->slug);

        $selector = ".post-card.primary-cat-$slug";

        $rules = [];

        if (get_theme_mod('cat_color_apply_title')) {
            $rules[] = "--bs-link-color: $color";
        }

        if (get_theme_mod('cat_color_apply_read_more')) {
            $rules[] = "--bs-btn-bg: $color";
            $rules[] = "--bs-btn-border-color: $color";
            $rules[] = "--bs-btn-color: #fff";
        }

        if (get_theme_mod('cat_color_apply_card_border')) {
            $rules[] = "--post-card-border: $color";
        }

        if (!empty($rules)) {
            $scoped_blocks[] = "$selector { " . implode('; ', $rules) . "; }";
        }
    }

    /**
     * OUTPUT
     */
    if (empty($root_vars) && empty($scoped_blocks)) {
        return;
    }

    echo '<style id="aestazia-design-system">';

    if (!empty($root_vars)) {
        echo ':root { ' . implode('; ', $root_vars) . '; }';
    }

    if (!empty($scoped_blocks)) {
        echo implode(' ', $scoped_blocks);
    }

    echo '
        body { font-family: var(--font-body, inherit); }
        h1, h2, h3 { font-family: var(--font-heading-main, inherit); }
        h4, h5, h6 { font-family: var(--font-heading-sub, inherit); }
    ';

    echo '</style>';
});


/**
 * =========================
 * BUNNY FONTS LOADER
 * =========================
 */
add_action('wp_enqueue_scripts', function () {

    $fonts = [
        get_theme_mod('aestazia_font_body'),
        get_theme_mod('aestazia_font_heading'),
        get_theme_mod('aestazia_font_subhead'),
    ];

    $fonts = array_unique(array_filter($fonts));

    if (empty($fonts)) return;

    $families = [];

    foreach ($fonts as $font) {
        $families[] = str_replace(' ', '+', $font) . ':400,500,600,700';
    }

    $url = 'https://fonts.bunny.net/css?family=' . implode('&family=', $families);

    wp_enqueue_style('aestazia-bunny-fonts', $url, [], null);
});
