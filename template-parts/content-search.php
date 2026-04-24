<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Aestazia_Child
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>

	<div class="post-card-inner d-flex align-items-center">

		<div class="post-thumb flex-shrink-0">
			<?php aestazia_post_thumbnail(); ?>
		</div>

		<div class="post-content flex-grow-1 ps-3">

			<header class="entry-header mb-1">
				<?php the_title( sprintf( '<h2 class="entry-title mb-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

				<?php if ( 'post' === get_post_type() ) : ?>
					<div class="entry-meta small text-muted">
						<?php
						aestazia_posted_on();
						aestazia_posted_by();
						?>
					</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->

			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->

			<a href="<?php the_permalink(); ?>" class="btn btn-sm btn-primary read-more mt-2">
				<?php esc_html_e( 'Read More', 'aestazia-child' ); ?>
			</a>

		</div><!-- .post-content -->

	</div><!-- .post-card-inner -->

	<footer class="entry-footer mt-2 pt-2 border-top">
		<?php aestazia_entry_footer(); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
