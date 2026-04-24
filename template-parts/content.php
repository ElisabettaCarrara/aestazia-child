<?php
/**
 * Template part for displaying posts
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
				<?php
				the_title(
					sprintf( '<h2 class="entry-title mb-0"><a href="%s">', esc_url( get_permalink() ) ),
					( is_sticky() ? '</a>&#128204;</h2>' : '</a></h2>' )
				);

				if ( 'post' === get_post_type() ) :
					?>
					<div class="entry-meta small text-muted mt-1">
						<?php
						// Author and Date
						aestazia_posted_on();
						aestazia_posted_by();

						// Categories
						$categories_list = get_the_category_list( esc_html__( ', ', 'aestazia-child' ) );
						if ( $categories_list ) {
							// Using 'ms-2' Bootstrap class to add a small gap after the date/author
							printf( '<span class="cat-links ms-2">%s</span>', $categories_list ); 
						}

						// Tags
						$tags_list = get_the_tag_list( '', esc_html__( ', ', 'aestazia-child' ) );
						if ( $tags_list ) {
							// Using 'ms-2' Bootstrap class to add a small gap after the categories
							printf( '<span class="tags-links ms-2">%s</span>', $tags_list );
						}
						?>
					</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->

			<div class="entry-summary mt-2">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->

			<a href="<?php the_permalink(); ?>" class="btn btn-sm btn-primary read-more mt-2">
				<?php esc_html_e( 'Read More', 'aestazia-child' ); ?>
			</a>

		</div><!-- .post-content -->

	</div><!-- .post-card-inner -->

</article><!-- #post-<?php the_ID(); ?> -->
