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

require get_stylesheet_directory() . '/inc/customizer.php';

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
