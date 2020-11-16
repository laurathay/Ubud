<?php
/**
 * Build page using widget layout methods.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * Build page using widget layout methods.
 *
 * @since  1.0.0
 */
class WidgetLayer {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Array of all widget settings.
	 *
	 * @since  1.0.0
	 * @var    array
	 */
	private $widget_options = [];

	/**
	 * Array of all widget Areas.
	 *
	 * @since  1.0.0
	 * @var    array
	 */
	private $widget_areas = [];

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
		add_filter( 'bayleaf_inline_styles', [ self::get_instance(), 'generate_custom_css' ] );
		add_action( 'admin_enqueue_scripts', [ self::get_instance(), 'enqueue_admin' ] );
		add_action( 'widgets_init', [ self::get_instance(), 'register_custom_widget' ] );
		add_action( 'in_widget_form', [ self::get_instance(), 'extend_widget_form' ], 9, 3 );
		add_filter( 'dynamic_sidebar_params', [ self::get_instance(), 'add_widget_customizations' ] );
		add_filter( 'widget_update_callback', [ self::get_instance(), 'update_settings' ], 10, 2 );
	}

	/**
	 * Get widget areas.
	 *
	 * @since 1.0.0
	 */
	public function get_widget_areas() {

		if ( ! empty( $this->widget_areas ) ) {
			return $this->widget_areas;
		}

		$this->widget_areas = apply_filters(
			'bayleaf_widgetlayer_widget_areas',
			[
				'home-widgetlayer-1',
				'home-widgetlayer-2',
				'home-widgetlayer-3',
			]
		);

		return $this->widget_areas;
	}

	/**
	 * Enqueue scripts and styles to admin.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin() {

		$screen = get_current_screen();
		if ( ! in_array( $screen->id, [ 'page', 'widgets', 'customize' ], true ) ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'bayleaf_widgetlayer_pro_admin_js',
			get_template_directory_uri() . '/add-on/widgetlayer/admin/widgetlayer.js',
			[ 'jquery' ],
			BAYLEAF_THEME_VERSION,
			true
		);
		// Theme localize scripts data.
		$l10n = apply_filters(
			'bayleaf_localize_script_data',
			[
				'uploader_title'       => esc_html__( 'Set Image', 'bayleaf' ),
				'uploader_button_text' => esc_html__( 'Select', 'bayleaf' ),
				'set_featured_img'     => esc_html__( 'Set Image', 'bayleaf' ),
			]
		);
		wp_localize_script( 'bayleaf_widgetlayer_pro_admin_js', 'bayleafImageUploadText', $l10n );

		wp_enqueue_style(
			'bayleaf_widgetlayer_admin_style',
			get_template_directory_uri() . '/add-on/widgetlayer/admin/widgetlayer.css',
			[],
			BAYLEAF_THEME_VERSION,
			'all'
		);
	}

	/**
	 * Generate inline css for current page.
	 *
	 * @since 1.0.0
	 *
	 * @param str $css Dynamically generated css string.
	 * @return string Verified css string or empty string.
	 */
	public function generate_custom_css( $css ) {

		// We will put inline css to individual widgets separately in customize preview.
		if ( is_customize_preview() ) {
			return $css;
		}

		$css_array        = [];
		$widget_areas     = $this->get_widget_areas();
		$sidebars_widgets = get_option( 'sidebars_widgets', [] );

		foreach ( $widget_areas as $area ) {

			// Continue only if widget area is registered.
			if ( ! isset( $sidebars_widgets[ $area ] ) ) {
				continue;
			}

			foreach ( $sidebars_widgets[ $area ] as $widget ) {

				$widget_data = $this->get_widget_data_from_id( $area, $widget );
				if ( false === $widget_data ) {
					continue;
				}
				$css_array = array_merge_recursive( $css_array, $this->get_widget_css( $widget_data ) );
			}
		}

		$final_css = $css_array ? $this->widget_css_array_to_string( $css_array ) : '';
		$css      .= $final_css;

		return $css;
	}

	/**
	 * Get dynamically generated inline css from widget id.
	 *
	 * @since 1.0.0
	 *
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return string Verified css string or empty string.
	 */
	public function get_widget_css( $widget_data ) {
		if ( ! $widget_data || ! is_array( $widget_data ) ) {
			return [];
		}

		$widget_css             = [];
		$widget_id              = $widget_data[0];
		$widget_pos             = $widget_data[1];
		$instance               = $widget_data[2];
		$id_base                = $widget_data[3];
		$widget_css['common'][] = 'order:' . 2 * $widget_pos;
		$wid_settings           = array_intersect_key( $instance, $this->get_widget_options() );

		if ( ! empty( $wid_settings ) ) {
			foreach ( $this->get_widget_options() as $key => $args ) {
				if ( ! isset( $instance[ $key ] ) || '' === $instance[ $key ] ) {
					continue;
				}
				$val = $instance[ $key ];
				switch ( $key ) {
					case 'bayleaf_vert_align':
						$widget_css['tablet'][] = 'display:flex';
						$widget_css['tablet'][] = 'flex-direction:column';
						$widget_css['tablet'][] = ( 'middle' === $val ) ? 'justify-content:center' : 'justify-content:flex-end';
						break;
					case 'bayleaf_text_align':
						$widget_css['common'][] = ( 'center' === $val ) ? 'text-align:center' : 'text-align:right';
						break;
					case 'bayleaf_show_mobile':
						$widget_css['mobile_only'][] = 'display:none';
						break;
					case 'bayleaf_push_down':
						$widget_css['mobile_only'][] = 'order:' . ( 2 * ( $widget_pos + 1 ) + 1 );
						break;
					case 'bayleaf_push_down_tablet':
						$widget_css['tablet_only'][] = 'order:' . ( 2 * ( $widget_pos + 1 ) + 1 );
						break;
					default:
						break;
				}
			}
		}

		$widget_css = apply_filters( 'bayleaf_widget_custom_css', $widget_css, $widget_data );

		if ( ! empty( $widget_css ) ) {
			foreach ( $widget_css as $key => $rules ) {
				if ( false === strpos( $key, 'direct' ) ) {
					$widget_css[ $key ] = (array) sprintf( '.widgetlayer .%s{%s}', $widget_id, implode( ';', $rules ) );
				}
			}
			foreach ( $widget_css as $key => $rules ) {
				if ( false !== strpos( $key, 'direct' ) ) {
					$key_arr = explode( '-', $key );
					$new_key = isset( $key_arr[1] ) ? $key_arr[1] : false;
					if ( $new_key && is_array( $rules ) ) {
						foreach ( $rules as $rule ) {
							if ( isset( $widget_css[ $new_key ] ) ) {
								$widget_css[ $new_key ][] = $rule;
							} else {
								$widget_css[ $new_key ] = (array) $rule;
							}
						}
					}
				}
			}
		}

		return $widget_css;
	}

	/**
	 * Properly format Widget CSS array to css string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $css_arr Array of css strings.
	 * @return string Formatted css string.
	 */
	public function widget_css_array_to_string( $css_arr ) {
		if ( empty( $css_arr ) ) {
			return '';
		}

		$css_str = '';

		if ( isset( $css_arr['common'] ) ) {
			$css_str .= implode( '', $css_arr['common'] );
		}

		if ( isset( $css_arr['mobile_only'] ) ) {
			$css_str .= sprintf( '@media only screen and (max-width: %s) {%s}', '767px', implode( '', $css_arr['mobile_only'] ) );
		}

		if ( isset( $css_arr['tablet'] ) ) {
			$css_str .= sprintf( '@media only screen and (min-width: %s) {%s}', '768px', implode( '', $css_arr['tablet'] ) );
		}

		if ( isset( $css_arr['tablet_only'] ) ) {
			$css_str .= sprintf( '@media only screen and (min-width: %s) and (max-width: %s) {%s}', '768px', '1024px', implode( '', $css_arr['tablet_only'] ) );
		}

		if ( isset( $css_arr['desktop'] ) ) {
			$css_str .= sprintf( '@media only screen and (min-width: %s) {%s}', '1025px', implode( '', $css_arr['desktop'] ) );
		}

		if ( ! $css_str ) {
			return '';
		}

		return bayleaf_prepare_css( $css_str );
	}

	/**
	 * Get dynamically generated Widget html classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return array  Verified class string or empty string.
	 */
	public function get_widget_classes( $widget_data ) {
		if ( ! $widget_data || ! is_array( $widget_data ) ) {
			return '';
		}

		$classes   = [];
		$classes[] = 'brick-' . $widget_data[1];

		$widget_id    = $widget_data[0];
		$widget_pos   = $widget_data[1];
		$instance     = $widget_data[2];
		$id_base      = $widget_data[3];
		$wid_settings = array_intersect_key( $instance, $this->get_widget_options() );

		if ( ! empty( $wid_settings ) ) {
			foreach ( $this->get_widget_options() as $key => $args ) {
				if ( ! isset( $instance[ $key ] ) ) {
					continue;
				}
				$val = $instance[ $key ];
				switch ( $key ) {
					case 'bayleaf_width':
						$col       = $val ? $val : '12';
						$classes[] = esc_html( 'fw-tabr-' . $col );
						if ( '' === $instance['bayleaf_width_tablet'] ) {
							$classes[] = esc_html( 'fw-tab-' . $col );
						}
						break;
					case 'bayleaf_width_tablet':
						if ( '' !== $val ) {
							$col       = 'fw-tab-' . $val;
							$classes[] = esc_html( $col );
						}
						break;
					case 'bayleaf_text_widget_title':
						if ( 'large' === $val ) {
							$classes[] = 'lg-title';
						} elseif ( 'small' === $val ) {
							$classes[] = 'sm-title';
						}
						break;
					case 'bayleaf_widget_featured_image':
						if ( $val ) {
							$classes[] = 'has-featured-img';
							$classes[] = 'widescreen';
						}
						break;
					default:
						break;
				}
			}
		} else {
			$classes[] = 'fw-tabr-12 fw-tab-12';
		}

		if ( 'text' === $id_base ) {
			if ( isset( $instance['text'] ) && empty( $instance['text'] ) ) {
				$classes[] = 'only-title';
			}
		}

		$widget_classes = apply_filters( 'bayleaf_widget_custom_classes', $classes, $widget_data );
		$widget_classes = array_map( 'esc_attr', $widget_classes );
		$widget_classes = array_unique( $widget_classes );

		return join( ' ', $widget_classes );
	}

	/**
	 * Get array of all widget options.
	 *
	 * @since 1.0.0
	 */
	public function get_widget_options() {
		if ( ! empty( $this->widget_options ) ) {
			return $this->widget_options;
		}

		$this->widget_options = apply_filters(
			'bayleaf_widgetlayer_widget_options',
			[
				'bayleaf_text_widget_title' => [
					'setting' => 'bayleaf_text_widget_title',
					'label'   => esc_html__( 'Widget Title Font Size', 'bayleaf' ),
					'default' => esc_html__( 'Normal', 'bayleaf' ),
					'type'    => 'select',
					'id_base' => 'text',
					'choices' => [
						'large' => esc_html__( 'Large', 'bayleaf' ),
						'small' => esc_html__( 'Small', 'bayleaf' ),
					],
				],
				'bayleaf_width'             => [
					'setting' => 'bayleaf_width',
					'label'   => esc_html__( 'Width on desktop', 'bayleaf' ),
					'default' => esc_html__( '12 of 12', 'bayleaf' ),
					'type'    => 'select',
					'choices' => [
						'1'  => esc_html__( '1 of 12', 'bayleaf' ),
						'2'  => esc_html__( '2 of 12', 'bayleaf' ),
						'3'  => esc_html__( '3 of 12', 'bayleaf' ),
						'4'  => esc_html__( '4 of 12', 'bayleaf' ),
						'5'  => esc_html__( '5 of 12', 'bayleaf' ),
						'6'  => esc_html__( '6 of 12', 'bayleaf' ),
						'7'  => esc_html__( '7 of 12', 'bayleaf' ),
						'8'  => esc_html__( '8 of 12', 'bayleaf' ),
						'9'  => esc_html__( '9 of 12', 'bayleaf' ),
						'10' => esc_html__( '10 of 12', 'bayleaf' ),
						'11' => esc_html__( '11 of 12', 'bayleaf' ),
					],
				],
				'bayleaf_width_tablet'      => [
					'setting' => 'bayleaf_width_tablet',
					'label'   => esc_html__( 'Width on tablet', 'bayleaf' ),
					'default' => esc_html__( 'Same as Desktop', 'bayleaf' ),
					'type'    => 'select',
					'choices' => [
						'1'  => esc_html__( '1 of 12', 'bayleaf' ),
						'2'  => esc_html__( '2 of 12', 'bayleaf' ),
						'3'  => esc_html__( '3 of 12', 'bayleaf' ),
						'4'  => esc_html__( '4 of 12', 'bayleaf' ),
						'5'  => esc_html__( '5 of 12', 'bayleaf' ),
						'6'  => esc_html__( '6 of 12', 'bayleaf' ),
						'7'  => esc_html__( '7 of 12', 'bayleaf' ),
						'8'  => esc_html__( '8 of 12', 'bayleaf' ),
						'9'  => esc_html__( '9 of 12', 'bayleaf' ),
						'10' => esc_html__( '10 of 12', 'bayleaf' ),
						'11' => esc_html__( '11 of 12', 'bayleaf' ),
						'12' => esc_html__( '12 of 12', 'bayleaf' ),
					],
				],
				'bayleaf_vert_align'        => [
					'setting' => 'bayleaf_vert_align',
					'label'   => esc_html__( 'Content Vertical Alignment', 'bayleaf' ),
					'default' => esc_html__( 'Top', 'bayleaf' ),
					'type'    => 'select',
					'choices' => [
						'middle' => esc_html__( 'Middle', 'bayleaf' ),
						'bottom' => esc_html__( 'Bottom', 'bayleaf' ),
					],
				],
				'bayleaf_text_align'        => [
					'setting' => 'bayleaf_text_align',
					'label'   => esc_html__( 'Text Alignment', 'bayleaf' ),
					'default' => esc_html__( 'Left', 'bayleaf' ),
					'type'    => 'select',
					'choices' => [
						'center' => esc_html__( 'Center', 'bayleaf' ),
						'right'  => esc_html__( 'Right', 'bayleaf' ),
					],
				],
				'bayleaf_show_mobile'       => [
					'setting' => 'bayleaf_show_mobile',
					'label'   => esc_html__( 'Hide widget on mobile', 'bayleaf' ),
					'type'    => 'checkbox',
				],
				'bayleaf_push_down_tablet'  => [
					'setting' => 'bayleaf_push_down_tablet',
					'label'   => esc_html__( 'Move below next widget on tablet', 'bayleaf' ),
					'type'    => 'checkbox',
				],
				'bayleaf_push_down'         => [
					'setting' => 'bayleaf_push_down',
					'label'   => esc_html__( 'Move below next widget on mobile', 'bayleaf' ),
					'type'    => 'checkbox',
				],
			]
		);

		return $this->widget_options;
	}

	/**
	 * Adds a text filed to widgets for adding classes.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget The widget instance (passed by reference).
	 * @param null   $return Return null if new fields are added.
	 * @param array  $instance An array of the widget's settings.
	 */
	public function extend_widget_form( $widget, $return, $instance ) {
		$fields = [];
		foreach ( $this->get_widget_options() as $option => $value ) {
			$setting     = $value['setting'];
			$id          = esc_attr( $widget->get_field_id( $setting ) );
			$name        = esc_attr( $widget->get_field_name( $setting ) );
			$instance    = wp_parse_args( $instance, [ $setting => '' ] );
			$value       = wp_parse_args(
				$value,
				[
					'default'        => '',
					'description'    => '',
					'id_base'        => 'all',
					'premium_option' => false,
				]
			);
			$input_attrs = isset( $value['input_attrs'] ) ? (array) $value['input_attrs'] : [];
			$description = $value['description'] ? sprintf( '<span class="%s wid-setting-desc">%s</span>', esc_attr( $value['setting'] ) . '-desc', esc_html( $value['description'] ) ) : '';

			if ( is_array( $value['id_base'] ) ) {
				// Check if current Widget Option to be shown for this widget type.
				if ( ! in_array( $widget->id_base, $value['id_base'], true ) ) {
					continue;
				}
			} else {
				// Check if current Widget Option to be shown for this widget type.
				if ( 'all' !== $value['id_base'] && $widget->id_base !== $value['id_base'] ) {
					continue;
				}
			}

			// Prepare markup for custom widget options.
			switch ( $value['type'] ) {
				case 'select':
					$field  = '<label for="' . $id . '">' . $value['label'] . ': </label>';
					$field .= $description;
					// Select option field.
					$field .= sprintf( '<select name="%s" id="%s">', $name, $id );
					$field .= sprintf( '<option value="">%s</option>', $value['default'] );
					foreach ( $value['choices'] as $val => $label ) {
						$field .= sprintf(
							'<option value="%s" %s>%s</option>',
							esc_attr( $val ),
							selected( $instance[ $setting ], $val, false ),
							$label
						);
					}
					$field .= '</select>';
					$field  = sprintf( '<p class="%s widget-setting">%s</p>', esc_attr( $setting ), $field );
					break;
				case 'checkbox':
					$field  = sprintf( '<input name="%s" id="%s" type="checkbox" value="yes" %s />', $name, $id, checked( $instance[ $setting ], 'yes', false ) );
					$field .= '<label for="' . $id . '">' . $value['label'] . '</label>';
					$field .= $description;
					$field  = sprintf( '<p class="%s widget-small-text">%s</p>', esc_attr( $setting ), $field );
					break;
				case 'image_upload':
					$field  = '<label for="' . $id . '">' . $value['label'] . '</label>';
					$field .= $description;
					$field .= $this->image_upload_form( '', $setting, $id, $name, $instance[ $setting ] );
					$field  = sprintf( '<p class="%s  widget-setting">%s</p>', esc_attr( $setting ), $field );
					break;
				case 'custom':
					$field  = '<label for="' . $id . '">' . $value['label'] . '</label>';
					$field .= $description;
					$field .= apply_filters( 'bayleaf_custom_widget_form', '', $setting, $id, $name, $instance[ $setting ] );
					$field  = sprintf( '<p class="%s  widget-setting">%s</p>', esc_attr( $setting ), $field );
					break;
				default:
					$field  = '<label for="' . $id . '">' . $value['label'] . ': </label>';
					$field .= $description;
					$field .= sprintf( '<input name="%s" id="%s" type="%s" ', $name, $id, esc_attr( $value['type'] ) );
					foreach ( $input_attrs as $attr => $val ) {
						$field .= esc_html( $attr ) . '="' . esc_attr( $val ) . '" ';
					}
					if ( ! isset( $input_attrs['value'] ) ) {
						$field .= sprintf( 'value=%s', ( '' !== $instance[ $setting ] ) ? $instance[ $setting ] : $value['default'] );
					}
					$field .= ' />';
					$field  = sprintf( '<p class="%s widget-setting">%s</p>', esc_attr( $setting ), $field );
					break;
			}
			if ( false === $value['premium_option'] ) {
				$fields['basic'][] = $field;
			} else {
				$fields['premium'][] = $field;
			}
		}

		if ( ! empty( $fields ) ) {
			// Add widget options title.
			$title = sprintf( '<h4 class="widget-options-title">%s</h4>', esc_html__( 'Theme specific styling options', 'bayleaf' ) );

			// Add widget Options Content.
			$content = sprintf( '<div class="widget-options-content">%s</div>', implode( '', $fields['basic'] ) );

			// Display Widget Options.
			printf( '<div class="widget-options-section">%s%s</div>', $title, $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Image upload option markup.
	 *
	 * @since 1.0.2
	 *
	 * @param str $markup  Widget form image upload markup.
	 * @param str $setting Setting Name.
	 * @param str $id      Field ID.
	 * @param str $name    Field Name.
	 * @param int $value   Uploaded image id.
	 * @return str Widget form image upload markup.
	 */
	public function image_upload_form( $markup, $setting, $id, $name, $value ) {

		$value          = absint( $value );
		$uploader_class = '';
		$class          = 'bayleaf-hidden';

		if ( $value ) {
			$image_src = wp_get_attachment_image_src( $value, 'bayleaf-medium' );
			if ( $image_src ) {
				$featured_markup = sprintf( '<img class="custom-widget-thumbnail" src="%s">', esc_url( $image_src[0] ) );
				$class           = '';
				$uploader_class  = 'has-image';
			} else {
				$featured_markup = esc_html__( 'Set Featured Image', 'bayleaf' );
			}
		} else {
			$featured_markup = esc_html__( 'Set Featured Image', 'bayleaf' );
		}

		$markup  = sprintf( '<a class="bayleaf-widget-img-uploader %s">%s</a>', $uploader_class, $featured_markup );
		$markup .= sprintf( '<span class="bayleaf-widget-img-instruct %s">%s</span>', $class, esc_html__( 'Click the image to edit/update', 'bayleaf' ) );
		$markup .= sprintf( '<a class="bayleaf-widget-img-remover %s">%s</a>', $class, esc_html__( 'Remove Featured Image', 'bayleaf' ) );
		$markup .= sprintf( '<input class="bayleaf-widget-img-id" name="%s" id="%s" value="%s" type="hidden" />', $name, $id, $value );
		return $markup;
	}

	/**
	 * Update settings for current widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance The current widget instance's settings.
	 * @param array $new_instance Array of new widget settings.
	 * @return false|array
	 */
	public function update_settings( $instance, $new_instance ) {

		foreach ( $this->get_widget_options() as $option => $value ) {
			$setting      = $value['setting'];
			$instance     = wp_parse_args( $instance, [ $setting => '' ] );
			$new_instance = wp_parse_args( $new_instance, [ $setting => '' ] );
			switch ( $value['type'] ) {
				case 'select':
					$instance[ $setting ] = array_key_exists( $new_instance[ $setting ], $value['choices'] ) ? $new_instance[ $setting ] : '';
					break;
				case 'checkbox':
					$instance[ $setting ] = ( 'yes' === $new_instance[ $setting ] ) ? 'yes' : '';
					break;
				case 'text':
					$instance[ $setting ] = sanitize_text_field( $new_instance[ $setting ] );
					break;
				case 'url':
					$instance[ $setting ] = esc_url_raw( $new_instance[ $setting ] );
					break;
				case 'number':
					$number = $new_instance[ $setting ];
					$attr   = isset( $value['input_attrs'] ) ? (array) $value['input_attrs'] : array();

					if ( '' !== $number ) {
						if ( isset( $attr['max'] ) ) {
							$number = $number > $attr['max'] ? $attr['max'] : $number;
						}

						if ( isset( $attr['min'] ) ) {
							$number = $number < $attr['min'] ? $attr['min'] : $number;
						}

						if ( isset( $attr['step'] ) && is_float( $attr['step'] ) ) {
							$number = abs( floatval( $number ) );
						} else {
							$number = absint( $number );
						}
					}

					$instance[ $setting ] = ( '' !== $number ) ? $number : '';
					break;
				case 'image_upload':
					$img_id               = absint( $new_instance[ $setting ] );
					$img_url              = wp_get_attachment_image_src( $img_id );
					$instance[ $setting ] = $img_url ? $img_id : $instance[ $setting ];
					break;
				case 'custom':
					$instance[ $setting ] = '';
					$instance[ $setting ] = apply_filters( 'bayleaf_custom_widget_form_update', $instance[ $setting ], $setting, $new_instance );
					break;
				default:
					$instance[ $setting ] = '';
					break;
			}
		}
		return $instance;
	}

	/**
	 * Adds the classes to the widget in the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params Parameters passed to a widget's display callback.
	 * @return false|array
	 */
	public function add_widget_customizations( $params ) {

		if ( is_admin() ) {
			return $params;
		}

		$widget_data = false;

		$widget_areas = $this->get_widget_areas();
		foreach ( $widget_areas as $area ) {
			if ( ! is_active_sidebar( $area ) ) {
				$widget_data = false;
				continue;
			}

			$widget_data = $this->get_widget_data_from_id( $area, $params[0]['widget_id'] );
			if ( false !== $widget_data ) {
				break;
			}
		}

		if ( false === $widget_data ) {
			return $params;
		}

		$custom_classes = $this->get_widget_classes( $widget_data );
		$custom_classes = $custom_classes ? esc_attr( $params[0]['widget_id'] ) . ' ' . $custom_classes : esc_attr( $params[0]['widget_id'] );

		// Add class(es) to widget front end.
		$params[0]['before_widget'] = str_replace( 'brick', 'brick ' . $custom_classes, $params[0]['before_widget'] );

		// Change markup for Blank Widget.
		if ( false !== strpos( $params[0]['widget_id'], 'bayleaf_blank_widget' ) ) {
			$params[0]['before_widget'] = str_replace( '<section', '<span aria-hidden="true"', $params[0]['before_widget'] );
			$params[0]['after_widget']  = '</span>';
		}

		$before_widget_content      = $this->get_before_widget_content( $widget_data );
		$after_widget_content       = $this->get_after_widget_content( $widget_data );
		$params[0]['before_widget'] = $params[0]['before_widget'] . $before_widget_content;
		$params[0]['after_widget']  = $after_widget_content . $params[0]['after_widget'];

		// Add inline style, only if we are in customizer preview.
		if ( ! is_customize_preview() ) {
			return $params;
		}

		$inline_css = $this->get_widget_css( $widget_data );
		$inline_css = $this->widget_css_array_to_string( $inline_css );
		if ( $inline_css ) {
			echo '<style>' . $inline_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $params;
	}

	/**
	 * Get widget settings and other information from widget id.
	 *
	 * @since 1.0.0
	 *
	 * @param int $widget_area Widget Area.
	 * @param str $widget_id   Widget ID.
	 * @return false|array
	 */
	public function get_widget_data_from_id( $widget_area, $widget_id ) {
		global $wp_registered_widgets;

		$sidebars_widgets = get_option( 'sidebars_widgets', [] );
		$widget_pos       = array_search( $widget_id, $sidebars_widgets[ $widget_area ], true );
		if ( false === $widget_pos ) {
			return false;
		}

		// Get widget parameters.
		if ( isset( $wp_registered_widgets[ $widget_id ] ) ) {
			$widget_params = $wp_registered_widgets[ $widget_id ];
		} else {
			return false;
		}

		/*
		 * Widget's display callback function is actually an array of widget object
		 * and 'display callback' method. Let's use that object to get widget settings.
		 */
		if ( ! ( is_array( $widget_params['callback'] ) && is_object( $widget_params['callback'][0] ) ) ) {
			return false;
		}
		$widget_obj = $widget_params['callback'][0];
		if ( ! ( method_exists( $widget_obj, 'get_settings' ) && isset( $widget_params['params'][0]['number'] ) ) ) {
			return false;
		}
		$instances = $widget_obj->get_settings();
		$number    = $widget_params['params'][0]['number'];
		if ( array_key_exists( $number, $instances ) ) {
			$instance = $instances[ $number ];
			$id_base  = property_exists( $widget_obj, 'id_base' ) ? $widget_obj->id_base : '';
		} else {
			return false;
		}

		return [ $widget_id, $widget_pos, $instance, $id_base ];
	}

	/**
	 * Get before widget customized content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return string Widget customized content markup.
	 */
	public function get_before_widget_content( $widget_data ) {

		return apply_filters( 'bayleaf_before_widget_content', '', $widget_data );
	}

	/**
	 * Get after widget customized content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return string Widget customized content markup.
	 */
	public function get_after_widget_content( $widget_data ) {

		return apply_filters( 'bayleaf_after_widget_content', '', $widget_data );
	}

	/**
	 * Register Custom Blank Widget.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_widget() {
		require_once get_template_directory() . '/add-on/widgetlayer/class-blank-widget.php';
		register_widget( 'bayleaf\Blank_Widget' );
	}
}

WidgetLayer::init();
