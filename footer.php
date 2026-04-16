<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Aestazia
 */

?>

		</div><!-- .row -->

	</div><!-- .container -->

<footer id="colophon" class="site-footer">

    <div class="container-xl">

        <div class="row footer-widgets">

            <div class="col-md-4">
                <?php if ( is_active_sidebar( 'footer-1' ) ) {
                    dynamic_sidebar( 'footer-1' );
                } ?>
            </div>

            <div class="col-md-4">
                <?php if ( is_active_sidebar( 'footer-2' ) ) {
                    dynamic_sidebar( 'footer-2' );
                } ?>
            </div>

            <div class="col-md-4">
                <?php if ( is_active_sidebar( 'footer-3' ) ) {
                    dynamic_sidebar( 'footer-3' );
                } ?>
            </div>

        </div>

        <div class="site-info text-center pt-4 border-top">
            <?php aestazia_site_info(); ?>
        </div>

    </div>

</footer>

<?php wp_footer(); ?>

</body>
</html>
