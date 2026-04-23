<?php
/**
 * Aestazia Child Theme Functions
 *
 * Handles:
 * - Styles enqueue
 * - Footer widget areas
 * - Alternating post layout (archive/search/home)
 * - Category color system (Customizer)
 *
 * @package Aestazia_Child
 */

/**
 * Enqueue parent and child styles
 */
function aestazia_child_enqueue_styles() {

    wp_enqueue_style(
        'aestazia-parent',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'aestazia-child',
        get_stylesheet_uri(),
        array('aestazia-parent'),
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'aestazia_child_enqueue_styles');


/**
 * Register footer widget areas
 */
function aestazia_child_footer_widgets() {

    register_sidebar( array(
        'name'          => 'Footer Column 1',
        'id'            => 'footer-1',
        'description'   => 'First footer widget area',
        'before_widget' => '<div class="footer-widget mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ) );

    register_sidebar( array(
        'name'          => 'Footer Column 2',
        'id'            => 'footer-2',
        'description'   => 'Second footer widget area',
        'before_widget' => '<div class="footer-widget mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ) );

    register_sidebar( array(
        'name'          => 'Footer Column 3',
        'id'            => 'footer-3',
        'description'   => 'Third footer widget area',
        'before_widget' => '<div class="footer-widget mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ) );

}
add_action( 'widgets_init', 'aestazia_child_footer_widgets' );


/**
 * Add alternating layout classes to posts in archive views
 *
 * Adds:
 * - layout-left
 * - layout-right
 */
add_filter('post_class', function ($classes) {

    if (is_home() || is_archive() || is_search()) {

        global $wp_query;

        if ($wp_query && $wp_query->in_the_loop && isset($wp_query->current_post)) {

            $is_even = ($wp_query->current_post % 2) === 0;
            $classes[] = $is_even ? 'layout-left' : 'layout-right';
        }
    }

    return $classes;
});


/**
 * Add primary category class to posts
 *
 * This ensures ONE deterministic category color per post.
 * Output example:
 * - primary-cat-news
 */
add_filter('post_class', function ($classes) {

    if (is_singular() || is_home() || is_archive() || is_search()) {

        $categories = get_the_category();

        if (!empty($categories)) {
            $primary = $categories[0]; // Basic primary category logic
            $classes[] = 'primary-cat-' . sanitize_html_class($primary->slug);
        }
    }

    return $classes;
});


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

    $categories = get_categories();

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

        $slug     = sanitize_html_class($cat->slug);
        $selector = '.primary-cat-' . $slug;
        $color    = sanitize_hex_color($color);

        /**
         * Define CSS variable
         */
        echo esc_attr($selector) . ' { --cat-color: ' . esc_attr($color) . '; }';

        /**
         * Apply styles conditionally
         */

        // Card border
        if (get_theme_mod('cat_color_apply_card_border')) {
            echo esc_attr($selector) . ' { border-left: 4px solid var(--cat-color); }';
        }

        // Title
        if (get_theme_mod('cat_color_apply_title')) {
            echo esc_attr($selector) . ' .entry-title a { color: var(--cat-color); }';
        }

        // Read more
        if (get_theme_mod('cat_color_apply_read_more')) {
            echo esc_attr($selector) . ' .read-more { color: var(--cat-color); }';
        }
    }

    echo '</style>';
});
