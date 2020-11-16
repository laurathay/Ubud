<?php
/**
 * The template for displaying comments
 *
 * Template to display both current comments and comment form.
 *
 * @link https://codex.wordpress.org/Comments_in_WordPress
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments"<?php bayleaf_attr( 'discussion-area' ); ?>>

	<?php
	/**
	 * Fires immediately before comments area markup.
	 *
	 * @since 1.0.0
	 *
	 * @param str $calledby Hook by which the function has been called.
	 */
	do_action( 'bayleaf_before_comments', 'before_comments' );
	?>

	<div id="comments"<?php bayleaf_attr( 'comments-area' ); ?>>
	<?php
	if ( have_comments() ) :
		?>
		<h2<?php bayleaf_attr( 'comments-title' ); ?>>
			<?php
			$bayleaf_comments_number = get_comments_number();
			if ( 1 === $bayleaf_comments_number ) {
				printf(
					/* translators: %s: post title */
					esc_html_x( 'One comment on &ldquo;%s&rdquo;', 'comments title', 'bayleaf' ),
					'<span>' . get_the_title() . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			} else {
				printf(
					esc_html(
						/* translators: 1: number of comments, 2: post title */
						_nx(
							'%1$s comment on &ldquo;%2$s&rdquo;',
							'%1$s comments on &ldquo;%2$s&rdquo;',
							$bayleaf_comments_number,
							'comments title',
							'bayleaf'
						)
					),
					esc_html( number_format_i18n( $bayleaf_comments_number ) ),
					'<span>' . get_the_title() . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
			?>
		</h2><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ol<?php bayleaf_attr( 'comments-list' ); ?>>

			<?php
			wp_list_comments(
				[
					'avatar_size' => 46,
					'style'       => 'ol',
					'short_ping'  => true,
					'reply_text'  => bayleaf_get_icon( [ 'icon' => 'mail-reply' ] ) . esc_html__( ' Reply', 'bayleaf' ),
				]
			);
			?>

		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation();

		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
			<p<?php bayleaf_attr( 'no-comments' ); ?>><?php esc_html_e( 'Comments are closed.', 'bayleaf' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().

	comment_form(
		[
			'title_reply_before' => '<h2 id="reply-title"' . bayleaf_get_attr( 'comment-reply-title' ) . '>',
			'title_reply_after'  => '</h2>',
		]
	);
	?>
	</div><!-- .comments-area -->

	<?php
	/**
	 * Fires immediately after comments area markup.
	 *
	 * @since 1.0.0
	 *
	 * @param str $calledby Hook by which the function has been called.
	 */
	do_action( 'bayleaf_after_comments', 'after_comments' );
	?>

</div><!-- #comments -->
