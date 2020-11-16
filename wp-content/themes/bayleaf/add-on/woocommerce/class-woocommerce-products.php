<?php
/**
 * Widget API: WooCommerce Products Display Widget class
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * Class used to display WooCommerce Products widget.
 *
 * @since 1.0.0
 *
 * @see WP_Widget
 */
class Woocommerce_Products extends \WP_Widget {

	/**
	 * Holds Product Selection Rules.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $productsby = [];

	/**
	 * Holds sort orderby options.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $orderby = [];

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
		$this->defaults = [
			'products_by' => 'date',
			'number'      => 4,
			'columns'     => 4,
			'title'       => '',
		];

		// Product Selection Rules.
		$this->productsby = [
			'date'       => esc_html__( 'Newly Added Products', 'bayleaf' ),
			'featured'   => esc_html__( 'Featured Products', 'bayleaf' ),
			'Popular'    => esc_html__( 'Popular Products', 'bayleaf' ),
			'onsale'     => esc_html__( 'Products on Sale', 'bayleaf' ),
			'popularity' => esc_html__( 'Best Sellers', 'bayleaf' ),
		];

		// Set the widget options.
		$widget_ops = [
			'classname'                   => 'woo_products',
			'description'                 => esc_html__( 'Create a WooCommerce Products widget.', 'bayleaf' ),
			'customize_selective_refresh' => true,
		];
		parent::__construct( 'bayleaf_woo_products', esc_html__( 'WooCommerce Products', 'bayleaf' ), $widget_ops );
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

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$after_title = apply_filters( 'bayleaf_after_woo_widget_title', $args['after_title'], $instance );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $args['before_title'] . $title . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$product_args = apply_filters(
			'bayleaf_woocommerce_products_args',
			array(
				'per_page' => intval( $instance['number'] ),
				'columns'  => intval( $instance['columns'] ),
				'order'    => 'desc',
			)
		);

		switch ( $instance['products_by'] ) {
			case 'featured':
				$product_args['orderby']    = 'date';
				$product_args['visibility'] = 'featured';
				break;
			case 'popular':
				$product_args['orderby'] = 'rating';
				break;
			case 'onsale':
				$product_args['orderby'] = 'date';
				$product_args['on_sale'] = 'true';
				break;
			case 'popularity':
				$product_args['orderby'] = 'popularity';
				break;
			default:
				$product_args['orderby'] = 'date';
				break;
		}

		global $shortcode_tags;

		if ( ! isset( $shortcode_tags['products'] ) ) {
			return false;
		}

		$shortcode_content = call_user_func( $shortcode_tags['products'], $product_args );
		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo $shortcode_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

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
		?>
		<p>
			<?php $this->label( 'title', esc_html__( 'Title:', 'bayleaf' ) ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p class="products-by">
			<?php
			$this->label( 'products_by', esc_html__( 'Select Products to Display', 'bayleaf' ) );
			$this->select( 'products_by', $this->productsby, $instance['products_by'] );
			?>
		</p>

		<p class="number-of-products">
			<?php $this->label( 'number', esc_html__( 'Number of Products', 'bayleaf' ) ); ?>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo absint( $instance['number'] ); ?>" size="3" />
		</p>

		<p class="number-of-columns">
			<?php $this->label( 'columns', esc_html__( 'Number of Columns', 'bayleaf' ) ); ?>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" type="number" step="1" min="2" max="6" value="<?php echo absint( $instance['columns'] ); ?>" size="1" />
		</p>
		<?php
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

		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		$instance['products_by'] = array_key_exists( $new_instance['products_by'], $this->productsby ) ? $new_instance['products_by'] : '';

		$instance['number']  = absint( $new_instance['number'] );
		$instance['columns'] = absint( $new_instance['columns'] );

		return $instance;
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
}
