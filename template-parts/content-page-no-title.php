<?php
/**
 * Template part for displaying page content without a title
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Aestazia_Child
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'pt-1' ); ?>>
	
    <!-- Title header has been removed for this template -->

	<?php aestazia_single_featured_image(); ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="clearfix"></div><div class="page-links">' . esc_html__( 'Pages:', 'aestazia' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
	<div class="clearfix"></div>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="screen-reader-text">%s</span>', 'aestazia' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->