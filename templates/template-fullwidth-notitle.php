<?php
/**
 * Template Name: Full-Width Template - No Title
 *
 * @link https://developer.wordpress.org/themes/template-files-section/page-template-files/
 *
 * @package Aestazia_Child
 */

get_header();
?>

	<main id="primary" class="site-main col">

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
get_footer();