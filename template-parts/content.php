<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Aestazia_Child
 */

global $wp_query;

$is_even = ( $wp_query->current_post % 2 ) === 1;

$image_classes   = $is_even
	? 'col-12 col-sm-5 ps-sm-1 order-sm-2'
	: 'col-12 col-sm-5 pe-sm-1 order-sm-1';

$summary_classes = $is_even
	? 'col-12 col-sm-7 entry-summary order-sm-1'
	: 'col-12 col-sm-7 entry-summary order-sm-2';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'pt-1 mb-4 pb-4 border-bottom' ); ?>>

	<header class="entry-header mb-3">
		<?php
		the_title(
			sprintf( '<h2 class="entry-title"><a href="%s">', esc_url( get_permalink() ) ),
			( is_sticky() ? '</a>&#128204;</h2>' : '</a></h2>' )
		);

		if ( 'post' === get_post_type() ) :
			?>
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