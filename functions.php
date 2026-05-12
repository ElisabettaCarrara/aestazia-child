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
