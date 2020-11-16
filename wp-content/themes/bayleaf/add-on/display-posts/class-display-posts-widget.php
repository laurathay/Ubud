<?php
/**
 * Display Posts Widget.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * Class used to display Blank widget.
 *
 * @since 1.0.0
 *
 * @see WP_Widget
 */
class Display_Posts_Widget extends \WP_Widget {

	/**
	 * Holds all registered post type objects.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $post_types = [];

	/**
	 * Holds sort orderby options.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $orderby = [];

	/**
	 * Holds image cropping options.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $imagecrop = [];

	/**
	 * Holds all display styles.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	protected $styles = [];

	/**
	 * Holds all display styles supported items.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	public $style_supported = [];

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * Sets up a new Blank widget instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Set widget instance settings default values.
		$this->defaults = apply_filters(
			'bayleaf_dp_widget_defaults',
			[
				'title'      => '',
				'post_type'  => '',
				'taxonomy'   => '',
				'terms'      => [],
				'post_ids'   => '',
				'pages'      => [],
				'number'     => 5,
				'offset'     => 0,
				'orderby'    => 'date',
				'order'      => 'DESC',
				'styles'     => 'grid-view1',
				'image_crop' => 'centercrop',
			]
		);

		// Set the options for orderby.
		$this->orderby = [
			'date'          => esc_html__( 'Publish Date', 'bayleaf' ),
			'modified'      => esc_html__( 'Modified Date', 'bayleaf' ),
			'title'         => esc_html__( 'Title', 'bayleaf' ),
			'author'        => esc_html__( 'Author', 'bayleaf' ),
			'comment_count' => esc_html__( 'Comment Count', 'bayleaf' ),
			'rand'          => esc_html__( 'Random', 'bayleaf' ),
		];

		$this->imagecrop = [
			'topleftcrop'      => esc_html__( 'Top Left Cropping', 'bayleaf' ),
			'topcentercrop'    => esc_html__( 'Top Center Cropping', 'bayleaf' ),
			'centercrop'       => esc_html__( 'Center Cropping', 'bayleaf' ),
			'bottomleftcrop'   => esc_html__( 'Bottom Left Cropping', 'bayleaf' ),
			'bottomcentercrop' => esc_html__( 'Bottom Center Cropping', 'bayleaf' ),
		];

		// Set the widget options.
		$widget_ops = [
			'classname'                   => 'display_posts',
			'description'                 => esc_html__( 'Create a display posts widget.', 'bayleaf' ),
			'customize_selective_refresh' => true,
		];
		parent::__construct( 'bayleaf_display_posts', esc_html__( 'BayLeaf - Display Posts', 'bayleaf' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {

		$args['widget_id'] = isset( $args['widget_id'] ) ? $args['widget_id'] : $this->id;

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		// Run 'get_display_styles' to populate style_supported array.
		$styles = $this->get_display_styles();

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$wrapper_class = apply_filters( 'bayleaf_dp_wrapper_classes', [ $instance['styles'] ], $instance, $this );
		$wrapper_class = array_map( 'esc_attr', $wrapper_class );

		$after_title = apply_filters( 'bayleaf_after_dp_widget_title', $args['after_title'], $instance );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $args['before_title'] . $title . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Prepare the query.
		$query_args = [];
		if ( ! $instance['post_type'] ) {
			return;
		} elseif ( 'page' === $instance['post_type'] ) {
			$query_args = [
				'post_type'           => 'page',
				'post__in'            => $instance['pages'],
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			];
		} else {
			$query_args = [
				'post_type'           => $instance['post_type'],
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'posts_per_page'      => $instance['number'],
				'orderby'             => $instance['orderby'],
				'order'               => $instance['order'],
			];

			if ( $instance['taxonomy'] ) {
				$query_args['tax_query'] = [
					[
						'taxonomy' => $instance['taxonomy'],
						'field'    => 'slug',
						'terms'    => $instance['terms'],
					],
				];
			}

			if ( $instance['post_ids'] ) {
				$query_args['post__in'] = explode( ',', $instance['post_ids'] );
			}

			if ( isset( $instance['offset'] ) && 0 !== $instance['offset'] ) {
				$query_args['offset'] = $instance['offset'];
			}
		}

		$query_args = apply_filters( 'bayleaf_display_posts_args', $query_args, $instance );
		$post_query = new \WP_Query( $query_args );

		if ( $post_query->have_posts() ) :
			$action_args = [
				'instance' => $instance,
				'query'    => $post_query,
			];

			$inst_class  = Instance_Counter::get_instance();
			$instance_id = $inst_class->get();

			/**
			 * Fires before display posts wrapper.
			 *
			 * @since 1.0.0
			 *
			 * @param array $action_args Settings & args for the current widget instance..
			 * @param int   $instance_id Current display post instance ID.
			 */
			do_action( 'bayleaf_before_dp_wrapper', $action_args, $instance_id );
			?>
			<div id="dp-wrapper-<?php echo absint( $instance_id ); ?>" class="dp-wrapper <?php echo join( ' ', $wrapper_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">

			<?php
			/**
			 * Fires before custom loop starts.
			 *
			 * @since 1.0.0
			 *
			 * @param array $action_args Settings & args for the current widget instance..
			 */
			do_action( 'bayleaf_before_dp_loop', $action_args );

			while ( $post_query->have_posts() ) :
				$post_query->the_post();
				$entry_class = apply_filters( 'bayleaf_dp_entry_classes', [], $instance, $this );
				$entry_class = array_map( 'esc_attr', $entry_class );
				?>
				<div class="dp-entry <?php echo join( ' ', $entry_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
					<?php do_action( 'bayleaf_dp_entry', $args, $instance ); ?>
				</div><!-- .dp-entry -->
				<?php
			endwhile;

			/**
			 * Fires after custom loop starts.
			 *
			 * @since 1.0.0
			 *
			 * @param array $action_args Settings & args for the current widget instance..
			 */
			do_action( 'bayleaf_after_dp_loop', $action_args );
			?>

			</div>
			<?php

			// Reset the global $the_post as this query will have stomped on it.
			wp_reset_postdata();

			/**
			 * Fires after display posts wrapper.
			 *
			 * @since 1.0.0
			 *
			 * @param array $action_args Settings & args for the current widget instance..
			 */
			do_action( 'bayleaf_after_dp_wrapper', $action_args );
		endif;

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		// Get all display post styles.
		$styles = $this->get_display_styles();

		?>
		<p>
			<?php $this->label( 'title', esc_html__( 'Title:', 'bayleaf' ) ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<?php
			$post_type = $this->get_post_types();
			$post_type = array_merge( [ '' => esc_html__( 'None', 'bayleaf' ) ], $post_type );
			$this->label( 'post_type', esc_html__( 'Select Post Type', 'bayleaf' ) );
			$this->select( 'post_type', $post_type, $instance['post_type'] );
			?>
		</p>

		<a class="dp-filter dp-settings-toggle" <?php echo ( ! $instance['post_type'] ) ? ' style="display:none;"' : ''; ?>><?php esc_html_e( 'Get items to be displayed', 'bayleaf' ); ?></a>
		<div class="dp-filter-content dp-settings-content">
			<div class="page-panel" <?php echo 'page' !== $instance['post_type'] ? ' style="display:none;"' : ''; ?>>
				<?php $this->pages_checklist( $instance['pages'] ); ?>
			</div><!-- .page-panel -->

			<div class="post-panel" <?php echo ( ! $instance['post_type'] || 'page' === $instance['post_type'] ) ? ' style="display:none;"' : ''; ?>>

				<p class="post-ids">
					<?php $this->label( 'post_ids', esc_html__( 'Post IDs (if any)', 'bayleaf' ) ); ?>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_ids' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_ids' ) ); ?>" type="text" placeholder="<?php echo esc_attr_x( 'Comma separated ids, i.e. 230,300', 'Placeholder text for post ids', 'bayleaf' ); ?>" value="<?php echo esc_attr( $instance['post_ids'] ); ?>" />
				</p>

				<div class="taxonomies">
					<?php $this->taxonomies_select( $instance['post_type'], $instance['taxonomy'] ); ?>
				</div><!-- .taxonomies -->

				<div class="terms-panel" <?php echo '' === $instance['taxonomy'] ? ' style="display:none;"' : ''; ?>>
					<?php $this->terms_checklist( $instance['taxonomy'], $instance['terms'] ); ?>
				</div><!-- .terms-panel -->

				<p class="number-of-posts">
					<?php $this->label( 'number', esc_html__( 'Number of Posts', 'bayleaf' ) ); ?>
					<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo absint( $instance['number'] ); ?>" size="3" />
				</p>

				<p class="post-offset">
					<?php $this->label( 'offset', esc_html__( 'Offset (number of posts to displace)', 'bayleaf' ) ); ?>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="number" step="1" min="0" value="<?php echo absint( $instance['offset'] ); ?>" size="3" />
				</p>

				<?php do_action( 'bayleaf_dp_widget_extend_filters', $this, $instance ); ?>

				<div class="dp-sort-panel">
					<p class="posts-orderby">
						<?php
						$this->label( 'orderby', esc_html__( 'Order By', 'bayleaf' ) );
						$this->select( 'orderby', $this->orderby, $instance['orderby'] );
						?>
					</p>
					<p class="order">
						<?php
						$this->label( 'order', esc_html__( 'Sort Order', 'bayleaf' ) );
						$order = [
							'DESC' => esc_html__( 'Descending', 'bayleaf' ),
							'ASC'  => esc_html__( 'Ascending', 'bayleaf' ),
						];
						$this->select( 'order', $order, $instance['order'] );
						?>
					</p>
				</div>
			</div><!-- .post-panel -->
		</div>

		<a class="dp-style dp-settings-toggle" <?php echo ( ! $instance['post_type'] ) ? ' style="display:none;"' : ''; ?>><?php esc_html_e( 'Displaying selected items', 'bayleaf' ); ?></a>
		<div class="dp-display-content dp-settings-content<?php echo ( 'post' !== $instance['post_type'] ) ? ' not-post' : ''; ?>">
			<div class="posts-display">
				<p class="posts-dstyle">
					<?php
					$this->label( 'styles', esc_html__( 'Display Style', 'bayleaf' ) );
					$this->select( 'styles', $styles, $instance['styles'] );
					?>
				</p>

				<?php do_action( 'bayleaf_dp_widget_extend_style', $this, $instance ); ?>

				<p class="posts-imgcrop">
					<?php
					$this->label( 'image_crop', esc_html__( 'Image Cropping Position', 'bayleaf' ) );
					$this->select( 'image_crop', $this->imagecrop, $instance['image_crop'] );
					?>
				</p>
			</div><!-- .posts-styles -->
		</div>
		<?php
		do_action( 'bayleaf_dp_widget_extend', $this, $instance );
	}

	/**
	 * Handles updating the settings for the current widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {

		$new_instance = wp_parse_args( (array) $new_instance, $this->defaults );

		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		$valid_post_types      = array_keys( $this->get_post_types() );
		$instance['post_type'] = in_array( $new_instance['post_type'], $valid_post_types, true ) ? $new_instance['post_type'] : '';

		if ( 'page' === $instance['post_type'] ) {
			// Get list of all pages.
			$pages       = get_pages( [ 'exclude' => get_option( 'page_for_posts' ) ] );
			$valid_pages = wp_list_pluck( $pages, 'ID' );

			$instance['pages']    = array_intersect( $new_instance['pages'], $valid_pages );
			$instance['taxonomy'] = '';
		} else {
			$instance['pages'] = [];
		}

		if ( $instance['post_type'] && 'page' !== $instance['post_type'] && $new_instance['post_ids'] ) {
			$post_ids             = array_map( 'absint', explode( ',', $new_instance['post_ids'] ) );
			$instance['post_ids'] = implode( ',', $post_ids );
		} else {
			$instance['post_ids'] = '';
		}

		if ( $instance['post_type'] && 'page' !== $instance['post_type'] && $new_instance['taxonomy'] ) {
			// Get list of all taxonomies for a post type.
			$taxonomies = get_object_taxonomies( $instance['post_type'], 'objects' );
			$taxonomies = wp_list_pluck( $taxonomies, 'label', 'name' );

			$instance['taxonomy'] = array_key_exists( $new_instance['taxonomy'], $taxonomies ) ? $new_instance['taxonomy'] : '';
		} else {
			$instance['taxonomy'] = '';
		}

		if ( $instance['taxonomy'] && $new_instance['terms'] ) {
			// Get list of all terms.
			$terms       = get_terms( [ 'taxonomy' => $instance['taxonomy'] ] );
			$terms       = wp_list_pluck( $terms, 'name', 'slug' );
			$valid_terms = array_keys( $terms );

			$instance['terms'] = array_intersect( $new_instance['terms'], $valid_terms );
		} else {
			$instance['terms'] = [];
		}

		$instance['number']  = absint( $new_instance['number'] );
		$instance['offset']  = absint( $new_instance['offset'] );
		$instance['orderby'] = ( array_key_exists( $new_instance['orderby'], $this->orderby ) ) ? $new_instance['orderby'] : 'date';

		$instance['order'] = ( 'DESC' === $new_instance['order'] ) ? 'DESC' : 'ASC';

		$instance['image_crop'] = ( array_key_exists( $new_instance['image_crop'], $this->imagecrop ) ) ? $new_instance['image_crop'] : 'centercrop';

		$valid_styles       = $this->get_display_styles();
		$instance['styles'] = array_key_exists( $new_instance['styles'], $valid_styles ) ? $new_instance['styles'] : '';

		return apply_filters( 'bayleaf_dp_widget_update', $instance, $new_instance, $this );
	}

	/**
	 * Prints a checkbox list of all pages.
	 *
	 * @param array $selected_pages Checked pages.
	 * @return void
	 */
	public function pages_checklist( $selected_pages ) {

		// Get list of all pages.
		$pages = get_pages( [ 'exclude' => get_option( 'page_for_posts' ) ] );
		$pages = wp_list_pluck( $pages, 'post_title', 'ID' );

		$this->label( 'pages', esc_html__( 'Select Pages', 'bayleaf' ) );
		$this->mu_checkbox( 'pages', $pages, $selected_pages );
	}

	/**
	 * Prints a checkbox list of all terms for a taxonomy.
	 *
	 * @param str   $taxonomy       Selected Taxonomy.
	 * @param array $selected_terms Selected Terms.
	 * @return void
	 */
	public function terms_checklist( $taxonomy, $selected_terms = [] ) {

		// Get list of all registered terms.
		$terms = get_terms(
			[
				'hide_empty' => true,
			]
		);

		// Get 'checkbox' options as value => label.
		$options = wp_list_pluck( $terms, 'name', 'slug' );

		// Get HTML classes for checkbox options.
		$classes = [];
		foreach ( $terms as $term ) {
			$classes[ $term->slug ][] = $term->taxonomy;
		}

		foreach ( $classes as $slug => $taxonomies ) {
			if ( $taxonomy && ! in_array( $taxonomy, $taxonomies, true ) ) {
				$classes[ $slug ][] = 'bayleaf-hidden';
			}
			$classes[ $slug ] = implode( ' ', $classes[ $slug ] );
		}

		// Terms Checkbox markup.
		$this->label( 'terms', esc_html__( 'Select Terms', 'bayleaf' ) );
		$this->mu_checkbox( 'terms', $options, $selected_terms, $classes );
	}

	/**
	 * Prints select list of all taxonomies for a post type.
	 *
	 * @param str   $post_type Selected post type.
	 * @param array $selected  Selected taxonomy in widget form.
	 * @return void
	 */
	public function taxonomies_select( $post_type, $selected = [] ) {

		// Get list of all registered taxonomies.
		$taxonomies = get_taxonomies( [], 'objects' );

		// Get 'select' options as value => label.
		$options = wp_list_pluck( $taxonomies, 'label', 'name' );
		$options = array_merge( [ '' => esc_html__( 'Ignore Taxonomy', 'bayleaf' ) ], $options );

		// Get HTML classes for select options.
		$classes = wp_list_pluck( $taxonomies, 'object_type', 'name' );
		if ( $post_type && 'page' !== $post_type ) {
			foreach ( $classes as $name => $type ) {
				$type = (array) $type;
				if ( ! in_array( $post_type, $type, true ) ) {
					$type[]           = 'bayleaf-hidden';
					$classes[ $name ] = $type;
				}
			}
		}
		$classes[''] = 'always-visible';

		// Taxonomy Select markup.
		$this->label( 'taxonomy', esc_html__( 'Select Taxonomy', 'bayleaf' ) );
		$this->select( 'taxonomy', $options, $selected, $classes );
	}

	/**
	 * Markup for 'label' for widget input options.
	 *
	 * @param str  $for  Label for which ID.
	 * @param str  $text Label text.
	 * @param bool $echo Display or Return.
	 * @return void|string
	 */
	public function label( $for, $text, $echo = true ) {
		$label = sprintf( '<label for="%s">%s:</label>', esc_attr( $this->get_field_id( $for ) ), esc_html( $text ) );
		if ( $echo ) {
			echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $label;
		}
	}

	/**
	 * Markup for Select dropdown lists for widget options.
	 *
	 * @param str   $for      Select for which ID.
	 * @param array $options  Select options as 'value => label' pair.
	 * @param str   $selected selected option.
	 * @param array $classes  Options HTML classes.
	 * @param bool  $echo     Display or return.
	 * @return void|string
	 */
	public function select( $for, $options, $selected, $classes = [], $echo = true ) {
		$select      = '';
		$final_class = '';
		foreach ( $options as $value => $label ) {
			if ( isset( $classes[ $value ] ) ) {
				$option_classes = (array) $classes[ $value ];
				$option_classes = array_map( 'esc_attr', $option_classes );
				$final_class    = 'class="' . join( ' ', $option_classes ) . '"';
			}
			$select .= sprintf( '<option value="%1$s" %2$s %3$s>%4$s</option>', esc_attr( $value ), $final_class, selected( $value, $selected, false ), esc_html( $label ) );
		}

		$select = sprintf(
			'<select id="%1$s" name="%2$s" class="bayleaf-%3$s widefat">%4$s</select>',
			esc_attr( $this->get_field_id( $for ) ),
			esc_attr( $this->get_field_name( $for ) ),
			esc_attr( str_replace( '_', '-', $for ) ),
			$select
		);

		if ( $echo ) {
			echo $select; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $select;
		}
	}

	/**
	 * Markup for multiple checkbox for widget options.
	 *
	 * @param str   $for      Select for which ID.
	 * @param array $options  Select options as 'value => label' pair.
	 * @param str   $selected selected option.
	 * @param array $classes  Checkbox input HTML classes.
	 * @param bool  $echo     Display or return.
	 * @return void|string
	 */
	public function mu_checkbox( $for, $options, $selected, $classes = [], $echo = true ) {

		$final_class = '';

		$mu_checkbox = '<div class="' . esc_attr( $for ) . '-checklist"><ul id="' . esc_attr( $this->get_field_id( $for ) ) . '">';

		$selected    = array_map( 'strval', $selected );
		$rev_options = $options;

		// Moving selected items on top of the array.
		foreach ( $options as $id => $label ) {
			if ( in_array( strval( $id ), $selected, true ) ) {
				$rev_options = [ $id => $label ] + $rev_options;
			}
		}

		foreach ( $rev_options as $id => $label ) {
			if ( isset( $classes[ $id ] ) ) {
				$final_class = ' class="' . esc_attr( $classes[ $id ] ) . '"';
			}
			$mu_checkbox .= "\n<li$final_class>" . '<label class="selectit"><input value="' . esc_attr( $id ) . '" type="checkbox" name="' . esc_attr( $this->get_field_name( $for ) ) . '[]"' . checked( in_array( strval( $id ), $selected, true ), true, false ) . ' /> ' . esc_html( $label ) . "</label></li>\n";
		}
		$mu_checkbox .= "</ul></div>\n";

		if ( $echo ) {
			echo $mu_checkbox; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $mu_checkbox;
		}
	}

	/**
	 * Get list of all registered post types.
	 *
	 * @return array
	 */
	public function get_post_types() {

		if ( ! empty( $this->post_types ) ) {
			return $this->post_types;
		}

		// Default Post and Pages post types.
		$default = [
			'post' => esc_html__( 'Posts', 'bayleaf' ),
			'page' => esc_html__( 'Pages', 'bayleaf' ),
		];

		// Get the registered post types.
		$post_types = get_post_types(
			[
				'public'   => true,
				'_builtin' => false,
			],
			'objects'
		);

		$post_types       = wp_list_pluck( $post_types, 'label', 'name' );
		$this->post_types = array_merge( $default, $post_types );

		return $this->post_types;
	}

	/**
	 * Get display styles.
	 *
	 * @return array
	 */
	public function get_display_styles() {
		if ( ! empty( $this->styles ) ) {
			return $this->styles;
		}

		$styles = apply_filters( 'bayleaf_dp_styles', [] );
		foreach ( $styles as $style => $args ) {
			$this->styles[ $style ]          = $args['label'];
			$this->style_supported[ $style ] = $args['support'];
		}

		return $this->styles;
	}
}
