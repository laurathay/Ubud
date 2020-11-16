<?php
/**
 * Widget API: Blank_Widget class
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
class Blank_Widget extends \WP_Widget {

	/**
	 * Sets up a new Blank widget instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'blank_widget',
			'description'                 => esc_html__( 'Create a blank widget.', 'bayleaf' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'bayleaf_blank_widget', esc_html__( 'BayLeaf - Divider', 'bayleaf' ), $widget_ops );
		add_filter( 'bayleaf_widget_custom_css', [ $this, 'add_widget_css' ], 10, 2 );
		add_filter( 'bayleaf_widgetlayer_widget_options', [ $this, 'widget_options' ] );
		add_filter( 'bayleaf_widget_custom_classes', [ $this, 'widget_classes' ], 10, 3 );
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
		echo $args['before_widget'] . $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		?>
		<p class="options-widget">
			<?php esc_html_e( 'This is an bayleaf theme widgetlayer specific widget. It should not be used outside widgetlayer widget area. Widlayer widget areas are, 1. Homepage Above Content, 2. Homepage Below Content, 3. Footer Widgets', 'bayleaf' ); ?>
		</p>
		<?php
	}

	/**
	 * Adds widget specific form options on admin pages.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Widget Options.
	 * @return array
	 */
	public function widget_options( $options ) {
		$blank_widget_options = [
			'bayleaf_blank_widget_height'        => [
				'setting' => 'bayleaf_blank_widget_height',
				'label'   => esc_html__( 'Height on desktop', 'bayleaf' ),
				'default' => esc_html__( '2 x Line-height', 'bayleaf' ),
				'type'    => 'select',
				'id_base' => 'bayleaf_blank_widget',
				'choices' => [
					'1' => esc_html__( '1 x Line-height', 'bayleaf' ),
					'3' => esc_html__( '3 x Line-height', 'bayleaf' ),
					'4' => esc_html__( '4 x Line-height', 'bayleaf' ),
					'5' => esc_html__( '5 x Line-height', 'bayleaf' ),
					'6' => esc_html__( '6 x Line-height', 'bayleaf' ),
				],
			],
			'bayleaf_blank_widget_height_tablet' => [
				'setting' => 'bayleaf_blank_widget_height_tablet',
				'label'   => esc_html__( 'Height on Tablet', 'bayleaf' ),
				'default' => esc_html__( '2 x Line-height', 'bayleaf' ),
				'type'    => 'select',
				'id_base' => 'bayleaf_blank_widget',
				'choices' => [
					'1' => esc_html__( '1 x Line-height', 'bayleaf' ),
					'3' => esc_html__( '3 x Line-height', 'bayleaf' ),
					'4' => esc_html__( '4 x Line-height', 'bayleaf' ),
					'5' => esc_html__( '5 x Line-height', 'bayleaf' ),
					'6' => esc_html__( '6 x Line-height', 'bayleaf' ),
				],
			],
			'bayleaf_show_divider_line'          => [
				'setting' => 'bayleaf_show_divider_line',
				'label'   => esc_html__( 'Horizontal divider line', 'bayleaf' ),
				'default' => esc_html__( 'None', 'bayleaf' ),
				'type'    => 'select',
				'id_base' => 'bayleaf_blank_widget',
				'choices' => [
					'wide-width' => esc_html__( 'Wide Line', 'bayleaf' ),
					'full-bleed' => esc_html__( 'Full bleed line', 'bayleaf' ),
				],
			],
		];

		return array_merge( $blank_widget_options, $options );
	}

	/**
	 * Adds widget specific css to front end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $css          Array of css rules.
	 * @param array $widget_data {.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return array
	 */
	public function add_widget_css( $css, $widget_data ) {
		if ( 'bayleaf_blank_widget' !== $widget_data[3] ) {
			return $css;
		}
		$line_height     = 1.75;
		$settings        = $widget_data[2];
		$css['common'][] = 'margin-bottom: 0';
		$css['common'][] = 'padding-top: 0';
		$css['common'][] = 'padding-bottom: 0';
		if ( isset( $settings['bayleaf_blank_widget_height'] ) ) {
			$d_height = $settings['bayleaf_blank_widget_height'] ? $settings['bayleaf_blank_widget_height'] : '2';

			switch ( $d_height ) {
				case '1':
					$css['desktop'][] = 'height: ' . 1 * $line_height . 'rem';
					break;
				case '2':
					$css['desktop'][] = 'height: ' . 2 * $line_height . 'rem';
					break;
				case '3':
					$css['desktop'][] = 'height: ' . 3 * $line_height . 'rem';
					break;
				case '4':
					$css['desktop'][] = 'height: ' . 4 * $line_height . 'rem';
					break;
				case '5':
					$css['desktop'][] = 'height: ' . 5 * $line_height . 'rem';
					break;
				case '6':
					$css['desktop'][] = 'height: ' . 6 * $line_height . 'rem';
					break;
				default:
					break;
			}
		}
		if ( isset( $settings['bayleaf_blank_widget_height_tablet'] ) ) {
			$tab_height = $settings['bayleaf_blank_widget_height_tablet'] ? $settings['bayleaf_blank_widget_height_tablet'] : '2';

			switch ( $tab_height ) {
				case '1':
					$css['tablet_only'][] = 'height: ' . 1 * $line_height . 'rem';
					break;
				case '2':
					$css['tablet_only'][] = 'height: ' . 2 * $line_height . 'rem';
					break;
				case '3':
					$css['tablet_only'][] = 'height: ' . 3 * $line_height . 'rem';
					break;
				case '4':
					$css['tablet_only'][] = 'height: ' . 4 * $line_height . 'rem';
					break;
				case '5':
					$css['tablet_only'][] = 'height: ' . 5 * $line_height . 'rem';
					break;
				case '6':
					$css['tablet_only'][] = 'height: ' . 6 * $line_height . 'rem';
					break;
				default:
					break;
			}
		}
		return $css;
	}

	/**
	 * Adds widget specific css to front end.
	 *
	 * @since 1.0.1
	 *
	 * @param array $classes  Array of css rules.
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return array
	 */
	public function widget_classes( $classes, $widget_data ) {

		$instance = $widget_data[2];
		$id_base  = $widget_data[3];

		if ( 'bayleaf_blank_widget' === $id_base ) {
			if ( isset( $instance['bayleaf_show_divider_line'] ) ) {
				if ( 'wide-width' === $instance['bayleaf_show_divider_line'] ) {
					$classes[] = 'has-ww-line';
				} elseif ( 'full-bleed' === $instance['bayleaf_show_divider_line'] ) {
					$classes[] = 'has-fb-line';
				}
			}
		}

		return $classes;
	}
}
