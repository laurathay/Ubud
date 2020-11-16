<?php
/**
 * Get related posts of any given post.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * Fetch related posts.
 *
 * @since  1.0.0
 */
class Related_Posts {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Returns the instance of this class.
	 *
	 * @since  1.0.0
	 *
	 * @return object Instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {

		add_action( 'bayleaf_after_site_content', [ self::get_instance(), 'related_posts' ] );
		add_action( 'bayleaf_inside_main_content', [ self::get_instance(), 'remove_nav' ], 9 );
	}

	/**
	 * Display related posts.
	 *
	 * @since 1.0.0
	 */
	public function related_posts() {

		global $post;

		if ( ! ( is_single() && bayleaf_get_mod( 'bayleaf_related_posts', 'none' ) ) ) {
			return;
		}

		$number_posts = apply_filters( 'bayleaf_number_of_related_posts', 3 );
		$categories   = get_the_terms( $post->ID, 'category' );
		$tags         = get_the_terms( $post->ID, 'post_tag' );
		$exclude      = [ $post->ID ];

		if ( false === $categories || is_wp_error( $categories ) ) {
			return;
		} else {
			$categories = wp_list_pluck( $categories, 'term_id' );
		}

		if ( false === $tags || is_wp_error( $tags ) ) {
			$tags = false;
		} else {
			$tags = wp_list_pluck( $tags, 'term_id' );
		}

		// Prepare the query.
		$query_args = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post__not_in'        => $exclude,
			'category__in'        => $categories,
			'orderby'             => 'rand',
			'order'               => 'DESC',
			'posts_per_page'      => $number_posts,
			'fields'              => 'ids',
		];

		if ( $tags ) {
			$query_args['tag__in'] = $tags;
		}

		$first_query = new \WP_Query( $query_args );

		if ( $tags && ( ! $first_query->have_posts() || $number_posts > $first_query->post_count ) ) {

			$exclude     = array_merge( $exclude, $first_query->posts );
			$added_posts = $number_posts - $first_query->post_count;

			// Prepare the query.
			$query_args = [
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'post__not_in'        => $exclude,
				'orderby'             => 'rand',
				'order'               => 'DESC',
				'posts_per_page'      => $added_posts,
				'fields'              => 'ids',
				'tax_query'           => [
					'relation' => 'OR',
					[
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $categories,
					],
					[
						'taxonomy' => 'post_tag',
						'field'    => 'term_id',
						'terms'    => $tags,
					],
				],
			];

			$second_query = new \WP_Query( $query_args );
		} else {
			$second_query = false;
		}

		if ( false !== $second_query && $second_query->have_posts() ) {
			$ids = array_merge( $first_query->posts, $second_query->posts );
		} else {
			$ids = $first_query->posts;
		}

		if ( empty( $ids ) ) {
			return;
		}

		$query_args = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post__in'            => $ids,
			'posts_per_page'      => $number_posts,
		];

		$final_query = new \WP_Query( $query_args );

		if ( $final_query->have_posts() ) {
			$this->display_posts( $final_query );
			wp_reset_postdata();
		}
	}

	/**
	 * Display Posts.
	 *
	 * @since 1.0.0
	 *
	 * @param Obj $query WP query object.
	 */
	public function display_posts( $query ) {

		echo '<div class="entry-related-posts">';
		echo '<div class="wrapper"><h3 class="related-posts-title">';
		esc_html_e( 'You may also like', 'bayleaf' );
		echo '</h3></div>';

		echo '<div class="bl-related-posts dp-wrapper grid-view1 index-view flex-wrapper dp-grid">';
		while ( $query->have_posts() ) :
			$query->the_post();
			bayleaf_get_template_partial( 'add-on/related-posts/templates', 'entry' );
		endwhile;
		echo '</div></div>';
	}

	/**
	 * Remove single posts navigation.
	 *
	 * @since 1.0.0
	 */
	public function remove_nav() {
		if ( bayleaf_get_mod( 'bayleaf_related_posts', 'none' ) ) {
			remove_action( 'bayleaf_inside_main_content', 'bayleaf_post_navigation' );
		}
	}
}
Related_Posts::init();
