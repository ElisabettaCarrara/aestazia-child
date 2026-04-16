<?php

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