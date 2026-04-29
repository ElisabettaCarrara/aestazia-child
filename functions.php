<?php
/**
 * Aestazia Child Theme Functions
 *
 * Handles:
 * - Styles enqueue
 * - Footer widget areas
 * - Post class injection (alternating layout + primary category colour)
 * - Category color system (Customizer)
 *
 * @package Aestazia_Child
 */

require get_stylesheet_directory() . '/inc/customizer.php';
require get_stylesheet_directory() . '/inc/font-downloader.php';

/**
 * Enqueue parent and child styles.
 */
function aestazia_child_enqueue_styles() {
	wp_enqueue_style(
		'aestazia-parent',
		get_template_directory_uri() . '/style.css'
	);

	wp_enqueue_style(
		'aestazia-child',
		get_stylesheet_uri(),
		array( 'aestazia-parent' ),
		'1.2.1'
	);
}
add_action( 'wp_enqueue_scripts', 'aestazia_child_enqueue_styles' );


/**
 * Register footer widget areas.
 */
function aestazia_child_footer_widgets() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column 1', 'aestazia-child' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'First footer widget area', 'aestazia-child' ),
			'before_widget' => '<div class="footer-widget mb-4">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="footer-widget-title">',
			'after_title'   => '</h5>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column 2', 'aestazia-child' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Second footer widget area', 'aestazia-child' ),
			'before_widget' => '<div class="footer-widget mb-4">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="footer-widget-title">',
			'after_title'   => '</h5>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column 3', 'aestazia-child' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Third footer widget area', 'aestazia-child' ),
			'before_widget' => '<div class="footer-widget mb-4">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="footer-widget-title">',
			'after_title'   => '</h5>',
		)
	);
}
add_action( 'widgets_init', 'aestazia_child_footer_widgets' );


/**
 * Inject layout and primary-category classes onto post <article> elements.
 *
 * Adds two types of class:
 *
 * 1. layout-left / layout-right — alternates on every post in archive, search,
 *    and home loop views to produce the magazine-style card arrangement.
 *
 * 2. primary-cat-{slug} — added in all applicable contexts (including singular)
 *    so the category colour system in the Customizer can scope its CSS variables
 *    to a specific post card.
 *
 * Previously there were two separate post_class filters registered in this file:
 * an anonymous closure (lines 83–97 of the original) that handled layout classes,
 * and this named function that handled both layout and category classes. Both ran
 * on every request, causing layout-left / layout-right to be added twice to every
 * post. The anonymous filter has been removed; this named function is the single
 * authoritative handler for all post class injection.
 *
 * @param string[] $classes Existing CSS classes for the post.
 * @return string[] Modified class list.
 */
function aestazia_child_post_classes( $classes ) {
	if ( is_home() || is_archive() || is_search() || is_singular() ) {
		global $wp_query;

		// 1. Alternating layout — only applies inside a loop, not on singular.
		if ( isset( $wp_query->current_post ) && $wp_query->in_the_loop ) {
			$classes[] = ( 0 === $wp_query->current_post % 2 ) ? 'layout-left' : 'layout-right';
		}

		// 2. Primary category colour class.
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			// Use the first assigned category as the primary one for styling.
			$classes[] = 'primary-cat-' . sanitize_html_class( $categories[0]->slug );
		}
	}

	return $classes;
}
add_filter( 'post_class', 'aestazia_child_post_classes' );
