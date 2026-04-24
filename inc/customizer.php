<?php
/**
 * Register Customizer settings for category colors and element application
 */
add_action('customize_register', function ($wp_customize) {

    /**
     * Section: Category Colors
     */
    $wp_customize->add_section('aestazia_category_colors', [
        'title'    => 'Category Colors',
        'priority' => 30,
    ]);

    $categories = get_categories( array(
    'hide_empty' => false,
) );

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
     * Section: Category Color Application
     */
    $wp_customize->add_section('aestazia_category_color_elements', [
        'title'    => 'Category Color Application',
        'priority' => 31,
    ]);

    $elements = [
        'card_border' => 'Apply to Card Border',
        'title'       => 'Apply to Post Title',
        'read_more'   => 'Apply to Read More Link',
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
            'section' => 'aestazia_category_color_elements',
        ]);
    }

});

/**
 * Output dynamic CSS for category colors
 *
 * Uses CSS variables for flexibility and cleaner styling.
 */
add_action('wp_head', function () {

    $categories = get_categories();

    echo '<style id="aestazia-category-colors">';

    foreach ($categories as $cat) {

        $color = get_theme_mod('cat_color_' . $cat->term_id);

        if (!$color) {
            continue;
        }

        $slug  = sanitize_html_class($cat->slug);
        $color = sanitize_hex_color($color);

        // Scope: ONLY post cards (important)
        $selector = '.post-card.primary-cat-' . $slug;

        /**
         * Base variable
         */
        echo "$selector { --cat-color: $color; }";

        /**
         * Token mapping instead of direct styling
         */

        // Title
        if (get_theme_mod('cat_color_apply_title')) {
            echo "$selector { --bs-heading-color: $color; --bs-link-color: $color; }";
        }

        // Read more (button-aware)
        if (get_theme_mod('cat_color_apply_read_more')) {
            echo "$selector { 
                --bs-btn-bg: $color;
                --bs-btn-border-color: $color;
                --bs-btn-color: #fff;
            }";
        }

        // Border (still component-level, but acceptable)
        if (get_theme_mod('cat_color_apply_card_border')) {
            echo "$selector { --post-card-border: $color; }";
        }
    }

    echo '</style>';
});
