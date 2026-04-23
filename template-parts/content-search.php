<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Aestazia_Child
 */

<article id="post-<?php the_ID(); ?>" <?php post_class( 'pt-1 mb-4 pb-4 border-bottom' ); ?>>

	<header class="entry-header mb-3">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			aestazia_posted_on();
			aestazia_posted_by();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="row">
		<div class="<?php echo esc_attr( $image_classes ); ?>">
			<?php aestazia_post_thumbnail(); ?>
		</div>
		<div class="<?php echo esc_attr( $summary_classes ); ?>">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	</div>

	<div class="clearfix"></div>

	<footer class="entry-footer">
		<?php aestazia_entry_footer(); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
