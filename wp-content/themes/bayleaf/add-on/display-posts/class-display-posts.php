<?php
/**
 * A widget to customize display of Posts, pages and custom post types.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * Customize display of Posts, pages and custom post types.
 *
 * @since  1.0.0
 */
class Display_Posts {

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
		add_filter( 'bayleaf_widget_custom_classes', [ self::get_instance(), 'widget_classes' ], 10, 2 );
		add_filter( 'bayleaf_dp_wrapper_classes', [ self::get_instance(), 'wrapper_classes' ], 10, 2 );
		add_filter( 'bayleaf_dp_entry_classes', [ self::get_instance(), 'entry_classes' ], 10, 2 );
		add_filter( 'bayleaf_dp_styles', [ self::get_instance(), 'dp_styles' ] );
		add_filter( 'bayleaf_after_dp_widget_title', [ self::get_instance(), 'dp_wid_title' ], 10, 2 );
		add_filter( 'bayleaf_dp_excerpt_length', [ self::get_instance(), 'excerpt_length' ], 10, 2 );
		add_filter( 'bayleaf_dp_excerpt_text', [ self::get_instance(), 'excerpt_text' ], 12, 2 );
		add_action( 'widgets_init', [ self::get_instance(), 'register_custom_widget' ] );
		add_action( 'admin_enqueue_scripts', [ self::get_instance(), 'enqueue_admin' ] );
		add_action( 'bayleaf_dp_entry', [ self::get_instance(), 'dp_entry' ], 10, 2 );
		add_action( 'bayleaf_after_dp_loop', [ self::get_instance(), 'navigate' ] );
	}

	/**
	 * Register widget display styles.
	 *
	 * @since 1.0.0
	 *
	 * @param array $styles   Array of supported posts display styles.
	 * @return array Array of supported display styles.
	 */
	public function dp_styles( $styles = [] ) {
		return [
			'list-view1' => [
				'label'   => esc_html__( 'List View 1', 'bayleaf' ),
				'support' => [ 'thumbnailtitle', 'excerpt', 'media', 'ialign' ],
			],
			'grid-view1' => [
				'label'   => esc_html__( 'Grid View 1', 'bayleaf' ),
				'support' => [ 'thumbnailtitle', 'media', 'multicol', 'imgcrop' ],
			],
			'grid-view2' => [
				'label'   => esc_html__( 'Grid View 2', 'bayleaf' ),
				'support' => [ 'thumbnailtitle', 'category', 'media' ],
			],
			'grid-view3' => [
				'label'   => esc_html__( 'Grid View 3', 'bayleaf' ),
				'support' => [ 'thumbnailtitle', 'category', 'media', 'multicol', 'imgcrop' ],
			],
			'slider1'    => [
				'label'   => esc_html__( 'Slider 1', 'bayleaf' ),
				'support' => [ 'thumbnailtitle', 'category', 'media' ],
			],
			'slider2'    => [
				'label'   => esc_html__( 'Slider 2', 'bayleaf' ),
				'support' => [ 'thumbnailtitle', 'media', 'excerpt' ],
			],
		];
	}

	/**
	 * Add classes to widget's main wrapper.
	 *
	 * @param str   $classes  Comma separated widget classes.
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return array Widget classes.
	 */
	public function widget_classes( $classes, $widget_data ) {
		$instance = $widget_data[2];
		if ( isset( $instance['styles'] ) && false !== strpos( $instance['styles'], 'grid' ) ) {
			$classes[] = 'posts-grid';
		}

		return $classes;
	}

	/**
	 * Register widget display posts entry wrapper classes.
	 *
	 * @param str   $classes  Comma separated entry posts classes.
	 * @param array $instance Settings for the current widget instance.
	 * @return array Entry posts classes.
	 */
	public function wrapper_classes( $classes, $instance ) {
		$classes[] = 'index-view';

		if ( false !== strpos( $instance['styles'], 'grid' ) ) {
			$classes[] = 'flex-wrapper';
			$classes[] = 'dp-grid';
		} elseif ( false !== strpos( $instance['styles'], 'list' ) ) {
			$classes[] = 'dp-list';
		}

		if ( in_array( $instance['styles'], [ 'slider1', 'slider2' ], true ) ) {
			$classes[] = 'slider-wrapper';
			$classes[] = 'widescreen';
			$classes[] = 'dp-list';
		}

		$classes[] = $instance['image_crop'];

		return $classes;
	}

	/**
	 * Register widget display posts entry classes.
	 *
	 * @param str   $classes  Comma separated entry posts classes.
	 * @param array $instance Settings for the current widget instance.
	 * @return str Entry posts classes.
	 */
	public function entry_classes( $classes, $instance ) {

		if ( false !== strpos( $instance['styles'], 'grid' ) ) {
			if ( 'grid-view2' === $instance['styles'] ) {
				$classes[] = 'entry fw-tab-6 fw-tabr-6';
			} else {
				$classes[] = 'entry fw-tab-6 fw-tabr-4';
			}
		}

		return $classes;
	}

	/**
	 * Display widget content to front-end.
	 *
	 * @param array $args     Widget display arguments.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function dp_entry( $args, $instance ) {
		$display = $this->get_display_map( $instance['styles'] );
		$display = $this->filter_display_map( $display, $instance );
		if ( ! empty( $display ) ) {
			echo '<div class="dp-index-wrapper">';
			$this->dp_display_entry( $display, $instance );
			echo '</div>';
		}
	}

	/**
	 * Get args for displaying elements for specific dp style.
	 *
	 * @param str $style Style for this widget instance.
	 * @return array
	 */
	public function get_display_map( $style ) {
		/*
		 * Default element display instructions.
		 * Instructions array to display particular HTML element as per given sequence.
		 */
		$display_map = apply_filters(
			'bayleaf_dp_style_args',
			[
				'list-view1' => [ 'thumbnail-medium', [ 'title', 'excerpt' ] ],
				'grid-view1' => [ 'thumbnail-medium', [ 'title' ] ],
				'grid-view2' => [ 'thumbnail-medium', [ 'category', 'title' ] ],
				'grid-view3' => [ 'thumbnail-medium', [ 'category', 'title' ] ],
				'slider1'    => [ 'thumbnail-large', [ 'category', 'title', 'excerpt' ] ],
				'slider2'    => [ 'thumbnail-large', [ [ 'title', 'excerpt' ] ] ],
			]
		);

		return isset( $display_map[ $style ] ) ? $display_map[ $style ] : [];
	}

	/**
	 * Display widget content to front-end.
	 *
	 * @param array $items content to be displayed.
	 * @param array $args Widget display arguments.
	 */
	public function filter_display_map( $items, $args ) {

		if ( ! isset( $args['style_sup'] ) || ! $args['style_sup'] ) {
			return $items;
		}

		// Compatibility with older version.
		if ( 'slider1' === $args['styles'] ) {
			$args['style_sup'][] = 'excerpt';
		}

		foreach ( $items as $key => $item ) {
			if ( is_array( $item ) ) {
				$items[ $key ] = $this->filter_display_map( $item, $args );
			} else {
				$unset = true;
				if ( in_array( $item, $args['style_sup'], true ) ) {
					$unset = false;
				} elseif ( false !== strpos( $item, 'thumbnail' ) || 'title' === $item ) {
					if ( in_array( 'thumbnailtitle', $args['style_sup'], true ) ) {
						$unset = false;
					}
				}
				if ( $unset ) {
					unset( $items[ $key ] );
				}
			}
		}

		return $items;
	}

	/**
	 * Add items to widget title area.
	 *
	 * @param array $after_title Items before closing of widget title.
	 * @param array $instance    Settings for the current widget instance.
	 * @return str
	 */
	public function dp_wid_title( $after_title, $instance ) {
		$link_html = '';

		// Change only if theme specific after_title args has not been altered.
		if ( '</span></h3>' !== $after_title ) {
			return $after_title;
		}

		if ( $instance['taxonomy'] && ! empty( $instance['terms'] ) ) {
			foreach ( $instance['terms'] as $cur_term ) {
				$term_link = get_term_link( $cur_term, $instance['taxonomy'] );
				if ( ! is_wp_error( $term_link ) ) {
					$link_html = sprintf( '<span class="dp-term-links"><a class="term-link" href="%1$s">%2$s %3$s</a></span>', esc_url( $term_link ), esc_html__( 'View All', 'bayleaf' ), bayleaf_get_icon( array( 'icon' => 'long-arrow-right' ) ) );
					break;
				}
			}
		}

		return '</span>' . $link_html . '</h3>';
	}

	/**
	 * Display entry content to front-end.
	 *
	 * @param array $display_args Content display arguments.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function dp_display_entry( $display_args, $instance ) {
		$style = $instance['styles'];
		$fetch = isset( $instance['fetch_media'] ) ? $instance['fetch_media'] : false;
		foreach ( $display_args as $args ) {
			if ( is_array( $args ) ) {
				bayleaf_markup( 'sub-entry', [ [ [ $this, 'dp_display_entry' ], $args, $instance ] ] );
			} else {
				switch ( $args ) {
					case 'title':
						$this->title();
						break;
					case 'date':
						$this->date();
						break;
					case 'ago':
						$this->ago();
						break;
					case 'author':
						$this->author();
						break;
					case 'content':
						$this->content();
						break;
					case 'excerpt':
						$this->excerpt( $instance );
						break;
					case 'category':
						$this->category();
						break;
					case 'meta':
						$this->meta();
						break;
					case 'meta-alt':
						$this->meta_alt();
						break;
					case 'thumbnail-small':
						$this->featured( 'thumbnail', $instance, $fetch );
						break;
					case 'thumbnail-medium':
						$this->featured( 'bayleaf-medium', $instance, $fetch );
						break;
					case 'thumbnail-large':
						$this->featured( 'bayleaf-large', $instance, $fetch );
						break;
					case 'no-thumb':
						$this->featured( false, $instance, $fetch );
						break;
					default:
						do_action( 'bayleaf_display_dp_item', $args );
						break;
				}
			}
		}
	}

	/**
	 * Enqueue scripts and styles to admin.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->id, array( 'page', 'widgets', 'customize' ), true ) ) {
			return;
		}

		wp_enqueue_style(
			'bayleaf_display_posts_admin_style',
			get_template_directory_uri() . '/add-on/display-posts/admin/displayposts.css',
			array(),
			BAYLEAF_THEME_VERSION,
			'all'
		);
		wp_enqueue_script(
			'bayleaf_display_posts_admin_js',
			get_template_directory_uri() . '/add-on/display-posts/admin/displayposts.js',
			[ 'jquery' ],
			BAYLEAF_THEME_VERSION,
			true
		);
	}

	/**
	 * Display post entry title.
	 *
	 * @since 1.0.0
	 */
	public function title() {
		if ( get_the_title() ) {
			the_title(
				sprintf(
					'<h2 class="dp-title"><a class="dp-title-link" href="%s" rel="bookmark">',
					esc_url( get_permalink() )
				),
				'</a></h2>'
			);
		}
	}

	/**
	 * Display post entry date.
	 *
	 * @since 1.0.0
	 */
	public function date() {

		printf(
			'<div class="dp-date"><time datetime="%s">%s</time></div>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date( 'M j, Y' ) )
		);
	}

	/**
	 * Display human readable post entry date.
	 *
	 * @since 1.0.0
	 */
	public function ago() {

		$time = sprintf(
			/* translators: %s: human-readable time difference */
			esc_html_x( '%s ago', 'human-readable time difference', 'bayleaf' ),
			esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) )
		);

		printf( '<div class="dp-date">%s</div>', $time ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display post entry author.
	 *
	 * @since 1.0.0
	 */
	public function author() {

		printf(
			'<div class="dp-author"><a href="%s"><span>%s</span></a></div>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author_meta( 'display_name' ) )
		);
	}

	/**
	 * Display post featured content.
	 *
	 * @since 1.0.0
	 *
	 * @param str   $size Thumbanil Size.
	 * @param array $instance Current display post settings.
	 * @param str   $fetch Media type to be fetched from post content.
	 */
	public function featured( $size, $instance, $fetch = false ) {
		$style = $instance['styles'];
		if ( bayleaf_get_mod( 'bayleaf_thumbnail_placeholder', 'none' ) || has_post_thumbnail() ) {

			if ( $style && false !== strpos( $style, 'slider' ) ) {
				$featured_content = [
					[ [ $this, 'thumbnail' ], $size, $instance ],
				];
			} else {
				$featured_content = [
					[ 'bayleaf_get_template_partial', 'template-parts/meta', 'meta-permalink' ],
					[ [ $this, 'thumbnail' ], $size, $instance ],
				];
			}

			if ( $fetch ) {
				$featured_content = apply_filters( 'bayleaf_dp_fetaured_content', $featured_content, $fetch, $style );
			}
			bayleaf_markup( 'dp-featured-content', $featured_content );
		}
	}

	/**
	 * Display post entry thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @param str   $size Thumbanil Size.
	 * @param array $instance Current display post settings.
	 */
	public function thumbnail( $size, $instance ) {
		if ( ! has_post_thumbnail() || ! $size ) {
			return;
		}

		$full    = 1280;
		$class   = '';
		$attr    = [];
		$width   = 0;
		$sizes   = '';
		$pad     = '';
		$id      = get_post_thumbnail_id();
		$imgmeta = wp_get_attachment_metadata( $id );
		if ( is_array( $imgmeta ) ) {
			if ( isset( $imgmeta['height'] ) && $imgmeta['height'] && isset( $imgmeta['width'] ) && $imgmeta['width'] ) {
				if ( isset( $instance['img_aspect'] ) && 'ncrop' === $instance['img_aspect'] ) {
					$pad = $imgmeta['height'] / $imgmeta['width'] * 100;
				}
			}
			$size_array = $this->get_image_size_from_meta( $size, $imgmeta );
			if ( $size_array ) {
				$width = absint( $size_array[0] );
			}
			if ( $width ) {
				if ( isset( $instance['styles'] ) && false !== strpos( $instance['styles'], 'grid' ) ) {
					if ( 'bp-grid5' === $instance['styles'] ) {
						$sizes = '(max-width: 639px) 100vw, (max-width: 1279px) 50vw, (min-width: 1280px) 640px, 100vw';
					} else {
						$sizes = '(max-width: 639px) 100vw, (max-width: 1023px) 50vw, (max-width: 1365px) 33vw';
						if ( isset( $instance['colnum'] ) && absint( $instance['colnum'] ) ) {
							$lapsize = ceil( $full / absint( $instance['colnum'] ) );
							$lapsize = sprintf( ', (min-width: 1366px) %spx, 100vw', $lapsize );
							$sizes   = $sizes . $lapsize;
						} else {
							$lapsize = ceil( $full / 3 );
							$lapsize = sprintf( ', (min-width: 1366px) %spx, 100vw', $lapsize );
							$sizes   = $sizes . $lapsize;
						}
					}
				}
				$attr['sizes'] = $sizes;
			}
		}

		if ( $size ) {
			$style = '';
			if ( empty( $attr ) ) {
				$attr = '';
			}
			if ( $pad ) {
				$style = 'style="position: relative; padding-top: ' . $pad . '%;"';
			}
			echo '<div class="dp-thumbnail ' . esc_attr( $class ) . '" ' . $style . '>';
			the_post_thumbnail( $size, $attr );
			echo '</div>';
		}
	}

	/**
	 * Get the image size as array from its meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param str   $size_name  Image size. Accepts any valid image size name.
	 * @param array $image_meta The image meta data.
	 */
	public function get_image_size_from_meta( $size_name, $image_meta ) {
		if ( 'full' === $size_name ) {
			return array(
				absint( $image_meta['width'] ),
				absint( $image_meta['height'] ),
			);
		} elseif ( ! empty( $image_meta['sizes'][ $size_name ] ) ) {
			return array(
				absint( $image_meta['sizes'][ $size_name ]['width'] ),
				absint( $image_meta['sizes'][ $size_name ]['height'] ),
			);
		}
		return false;
	}

	/**
	 * Display post content.
	 *
	 * @since 1.0.0
	 */
	public function content() {
		echo '<div class="dp-content">';
		the_content();
		echo '</div>';
	}

	/**
	 * Display post content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Settings array for current widget instance.
	 */
	public function excerpt( $instance ) {

		// Short circuit filter.
		$check = apply_filters( 'bayleaf_display_posts_excerpt', false, $instance );
		if ( false !== $check ) {
			return;
		}

		$style = $instance['styles'];
		$text  = get_the_content( '' );
		$text  = wp_strip_all_tags( strip_shortcodes( $text ) );
		$text  = str_replace( ']]>', ']]&gt;', $text );

		// Return if not post content.
		if ( ! $text ) {
			return;
		}

		// Default value of excerpt length. Backward styles compatibility.
		if ( in_array( $style, [ 'list-view1', 'slider2' ], true ) ) {
			$len = 55;
		} else {
			$len = 20;
		}

		/**
		 * Filters the number of words in an excerpt.
		 *
		 * @since 1.0.0
		 *
		 * @param int $number The number of words.
		 * @param arr $instance Settings for current widget instance.
		 */
		$excerpt_length = apply_filters( 'bayleaf_dp_excerpt_length', $len, $instance );

		// Generate excerpt teaser text and link.
		$exrpt_url   = esc_url( get_permalink() );
		$exrpt_text  = esc_html__( 'Continue Reading', 'bayleaf' );
		$exrpt_text  = apply_filters( 'bayleaf_dp_excerpt_text', $exrpt_text, $instance );
		$exrpt_title = get_the_title();

		if ( 0 === strlen( $exrpt_title ) ) {
			$screen_reader = '';
		} else {
			$screen_reader = sprintf( '<span class="screen-reader-text">%s</span>', $exrpt_title );
		}

		$excerpt_teaser = $exrpt_text ? sprintf( '<p class="dp-link-more"><a class="dp-more-link" href="%1$s">%2$s %3$s</a></p>', $exrpt_url, $exrpt_text, $screen_reader ) : '';

		/**
		 * Filters the string in the "more" link displayed after a trimmed excerpt.
		 *
		 * @since 1.0.0
		 *
		 * @param string $more_string The string shown within the more link.
		 * @param arr $instance Settings for current widget instance.
		 */
		$excerpt_more = apply_filters( 'bayleaf_dp_excerpt_more', ' ' . $excerpt_teaser, $instance );
		$text         = wp_trim_words( $text, $excerpt_length, $excerpt_more );

		printf( '<div class="dp-excerpt">%s</div>', $text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Modify display post's excerpt length.
	 *
	 * @since 1.0.0
	 *
	 * @param int $length Excerpt length.
	 * @param arr $instance Settings for current widget instance.
	 * @return int Excerpt length.
	 */
	public function excerpt_length( $length, $instance ) {
		$style = $instance['styles'];

		if ( 'slider1' === $style ) {
			$length = 0;
		}

		if ( 'slider2' === $style ) {
			$length = 25;
		}

		return $length;
	}

	/**
	 * Modify display post's excerpt teaser text.
	 *
	 * @since 1.0.0
	 *
	 * @param str $text Excerpt teaser text.
	 * @param arr $instance Settings for current widget instance.
	 * @return int Excerpt teaser.
	 */
	public function excerpt_text( $text, $instance ) {
		$style = $instance['styles'];

		if ( 'slider1' === $style ) {
			$text = esc_html__( 'Read More', 'bayleaf' );
		}

		return $text;
	}

	/**
	 * Display slider navigation.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Settings & args for the current widget instance.
	 */
	public function navigate( $args ) {
		$instance = $args['instance'];
		$query    = $args['query'];

		if ( 1 >= $query->post_count ) {
			return;
		}

		if ( ! in_array( $instance['styles'], [ 'slider1', 'slider2' ], true ) ) {
			return;
		}

		$navigation  = sprintf(
			'<button class="dp-prev-slide">%1$s<span class="screen-reader-text">%2$s</span></button>',
			bayleaf_get_icon( [ 'icon' => 'flickity-button' ] ),
			esc_html__( 'Previous Slide', 'bayleaf' )
		);
		$navigation .= sprintf(
			'<button class="dp-next-slide">%1$s<span class="screen-reader-text">%2$s</span></button>',
			bayleaf_get_icon( [ 'icon' => 'flickity-button' ] ),
			esc_html__( 'Next Slide', 'bayleaf' )
		);

		if ( 'slider2' === $instance['styles'] ) {
			echo $navigation; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			printf( '<div class="dp-slide-navigate">%s</div>', $navigation ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Display post categories.
	 *
	 * @since 1.0.0
	 */
	public function category() {
		echo '<div class="dp-categories">';
		the_category( ', ' );
		echo '</div>';
	}

	/**
	 * Display post meta.
	 *
	 * @since 1.0.0
	 */
	public function meta() {
		echo '<div class="dp-meta">';
		$this->author();
		esc_html_e( 'on', 'bayleaf' );
		$this->date();
		echo '</div>';
	}

	/**
	 * Display alternative post meta markup.
	 *
	 * @since 1.0.0
	 */
	public function meta_alt() {
		echo '<div class="dp-meta-alt">';
		echo get_avatar( get_the_author_meta( 'user_email' ), 42 );
		echo '<div class="meta-info-alt"><div class="dp-author-alt">';
		echo esc_html_e( 'By', 'bayleaf' );
		$this->author();
		echo '</div>';
		$this->date();
		echo '</div></div>';
	}

	/**
	 * Register the custom Widget.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_widget() {
		require_once get_template_directory() . '/add-on/display-posts/class-instance-counter.php';
		require_once get_template_directory() . '/add-on/display-posts/class-display-posts-widget.php';
		register_widget( 'bayleaf\Display_Posts_Widget' );
	}
}

Display_Posts::init();
