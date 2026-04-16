<?php
/**
 * Template Name: Default Page - No Title
 *
 * This is the template that displays pages with a sidebar but without the page title.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Aestazia_Child
 */

get_header();
?>

	<main id="primary" class="site-main col-md-8 mx-auto">

		<?php
		while ( have_posts() ) :
			the_post();

			// This now calls template-parts/content-page-no-title.php
			get_template_part( 'template-parts/content', 'page-no-title' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();