<?php
/**
 * Bayleaf Theme Customizer.
 *
 * @link https://codex.wordpress.org/Theme_Customization_API
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Add theme modification options to Theme Customizer.
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function bayleaf_customize_register( $wp_customize ) {

	/**
	 * Filters required information to generate customizer panels.
	 *
	 * The filter fecilitates inserting array of data to control customizer panels.
	 *
	 * @since 1.0.0
	 *
	 * @param array $panels {
	 *     @type WP_Customize_Panel|string $id   Customize Panel object, or Panel ID.
	 *     @type array                     $args {
	 *         Optional. Array of properties for the new Panel object. Default empty array.
	 *         @type int          $priority        Priority of the panel, defining the display order of panels and sections. Default 160.
	 *         @type string       $capability      Capability required for the panel. Default `edit_theme_options`
	 *         @type string|array $theme_supports  Theme features required to support the panel.
	 *         @type string       $title           Title of the panel to show in UI.
	 *         @type string       $description     Description to show in the UI.
	 *     }
	 * }
	 */
	$panels = apply_filters( 'bayleaf_theme_panels', [] );
	if ( ! empty( $panels ) ) {
		foreach ( $panels as $id => $args ) {
			$wp_customize->add_panel( $id, $args );
		}
	}

	/**
	 * Filters required information to generate customizer sections.
	 *
	 * The filter fecilitates inserting array of data to control customizer sections.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections {
	 *     @type WP_Customize_Section|string $id   Customize Section object, or Section ID.
	 *     @type array                     $args {
	 *         Optional. Array of properties for the new Panel object. Default empty array.
	 *         @type int          $priority           Priority of the panel, defining the display order of panels and sections. Default 160.
	 *         @type string       $panel              The panel this section belongs to.
	 *         @type string       $capability         Capability required for the panel. Default 'edit_theme_options'
	 *         @type string|array $theme_supports     Theme features required to support the panel.
	 *         @type string       $title              Title of the panel to show in UI.
	 *         @type string       $description        Description to show in the UI.
	 *         @type string       $type               Type of the panel.
	 *         @type callable     $active_callback    Active callback.
	 *         @type bool         $description_hidden Hide the description behind a help icon, instead of . Default false.
	 *     }
	 * }
	 */
	$sections = apply_filters( 'bayleaf_theme_sections', [] );
	if ( ! empty( $sections ) ) {
		foreach ( $sections as $id => $args ) {
			$wp_customize->add_section( $id, $args );
		}
	}

	/**
	 * Filters required information to generate customizer settings & controls.
	 *
	 * The filter fecilitates inserting array of data to control customizer settings
	 * and controls.
	 *
	 * @since 1.0.0
	 *
	 * @param array $controls {
	 *     @type WP_Customize_Control|string $id   Customize Control object, or ID.
	 *     @type array                       $args {
	 *         Optional. Array of properties for the new Control object. Default empty array.
	 *
	 *         @type array        $settings          All settings tied to the control. If undefined, defaults to `$setting`. IDs in the array correspond to the ID of a registered `WP_Customize_Setting`.
	 *         @type string       $setting           The primary setting for the control (if there is one). Default is 'default'.
	 *         @type string       $capability        Capability required to use this control. Normally derived from `$settings`.
	 *         @type int          $priority          Order priority to load the control. Default 10.
	 *         @type string       $section           The section this control belongs to. Default empty.
	 *         @type string       $label             Label for the control. Default empty.
	 *         @type string       $description       Description for the control. Default empty.
	 *         @type array        $choices           List of choices for 'radio' or 'select' type controls, where values are the keys, and labels are the values. Default empty array.
	 *         @type array        $input_attrs       List of custom input attributes for control output, where attribute names are the keys and values are the values. Default empty array.
	 *         @type bool         $allow_addition    Show UI for adding new content, currently only used for the dropdown-pages control. Default false.
	 *         @type string       $type              The type of the control. Default 'text'.
	 *         @type callback     $active_callback   Active callback.
	 *         @type string       $default           Default value for the setting. Default is empty string.
	 *         @type string       $transport         Options for rendering the live preview of changes in Theme Customizer. Using 'refresh' makes the change visible by reloading the whole preview. Using 'postMessage' allows a custom JavaScript to handle live changes. @link https://developer.wordpress.org/themes/customize-api. Default is 'refresh'
	 *         @type callable     $sanitize_callback Callback to filter a Customize setting value in un-slashed form.
	 *         @type string       $path              File name without .php extension of custom control class.
	 *         @type string       $class             This is Bayleaf theme custom option. Custom control class name.
	 *         @type string       $js_template       This is Bayleaf theme custom option. Customizer JavaScript API to be used or not with custom control class.
	 *         @type array        $select_refresh    This is Bayleaf theme custom option. Array of options for selective refresh.
	 *     }
	 * }
	 */
	$controls = apply_filters( 'bayleaf_theme_controls', [] );
	if ( empty( $controls ) ) {
		return;
	}

	foreach ( $controls as $control ) {

		// Filler values for customizer controls.
		$default_args = [
			'transport'      => 'refresh',
			'class'          => '',
			'path'           => '',
			'js_template'    => '',
			'select_refresh' => '',
		];

		// Merge customizer control supplied args into defaults args.
		$control = wp_parse_args( $control, $default_args );

		if ( ! empty( $control['path'] ) ) {
			$path = get_template_directory() . "/lib/customizer/controls/{$control['path']}.php";

			/**
			 * Filters file path to the custom control class.
			 *
			 * For changing custom control class file path (in-case file is at different location
			 * then the theme default location).
			 *
			 * @since 1.0.0
			 *
			 * @param string $path             Control class file path.
			 * @param string $control['settings'] Customize Control object, or ID.
			 */
			$control['path'] = apply_filters( 'bayleaf_control_class_path', $path, $control['setting'] );
		}

		// Adds theme settings that can be customized using the Theme Customization API.
		if ( isset( $control['settings'] ) ) {
			foreach ( (array) $control['settings'] as $key => $setting ) {
				$wp_customize->add_setting(
					$setting,
					[
						'default'           => bayleaf_get_default_value( $setting ),
						'sanitize_callback' => 'bayleaf_sanitization',
						'transport'         => $control['transport'],
					]
				);
			}
		} else {
			$wp_customize->add_setting(
				$control['setting'],
				[
					'default'           => bayleaf_get_default_value( $control['setting'] ),
					'sanitize_callback' => 'bayleaf_sanitization',
					'transport'         => $control['transport'],
				]
			);
		}

		// Load custom control class file, if not already loaded.
		if ( $control['class'] && $control['path'] ) {
			include_once $control['path'];
		}

		// Displays a new controller on the Theme Customization admin screen.
		if ( $control['class'] && class_exists( $control['class'] ) ) {
			$class = $control['class'];
			$wp_customize->add_control( new $class( $wp_customize, $control['setting'], $control ) );
		} else {
			$wp_customize->add_control( $control['setting'], $control );
		}

		// Register control type if we are using Customizer JavaScript API.
		if ( $control['class'] && $control['js_template'] ) {
			$wp_customize->register_control_type( $control['class'] );
		}

		// Implement Customizer selective refresh.
		if ( $control['select_refresh'] && is_array( $control['select_refresh'] ) ) {
			$wp_customize->selective_refresh->add_partial( $control['setting'], $control['select_refresh'] );
		}
	} // End foreach.
}
add_action( 'customize_register', 'bayleaf_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since 1.0.0
 */
function bayleaf_customize_preview_js() {
	wp_enqueue_script(
		'bayleaf_customizer',
		get_template_directory_uri() . '/assets/admin/js/customize-preview.js',
		[ 'customize-preview' ],
		BAYLEAF_THEME_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'bayleaf_customize_preview_js' );

/**
 * Enqueue customizer control JS file.
 *
 * @since 1.0.0
 */
function bayleaf_customize_control_js() {
	wp_enqueue_script(
		'bayleaf_customizer_control',
		get_template_directory_uri() . '/assets/admin/js/customize-control.js',
		[ 'customize-controls', 'jquery' ],
		BAYLEAF_THEME_VERSION,
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'bayleaf_customize_control_js' );

/**
 * Enqueue customizer control CSS file.
 *
 * @since 1.0.0
 */
function bayleaf_customize_control_css() {
	wp_enqueue_style(
		'bayleaf_customizer_control_style',
		get_template_directory_uri() . '/assets/admin/css/customize-control.css',
		[],
		BAYLEAF_THEME_VERSION
	);
}
add_action( 'customize_controls_print_styles', 'bayleaf_customize_control_css' );

/**
 * Returns sanitized customizer options.
 *
 * @since 1.0.0
 *
 * @param  Mixed                $option  Selected customizer option.
 * @param  WP_Customize_Setting $setting Setting instance.
 * @return Mixed Returns sanitized value of customizer option.
 */
function bayleaf_sanitization( $option, $setting ) {
	if ( $setting->manager->get_control( $setting->id ) ) {
		$type = $setting->manager->get_control( $setting->id )->type;
		$attr = $setting->manager->get_control( $setting->id )->input_attrs;
	} else {
		// Multiple settings assigned to single customizer control.
		$type     = '';
		$attr     = [];
		$controls = $setting->manager->controls();
		foreach ( $controls as $id => $object ) {
			foreach ( $object->settings as $key => $control_setting ) {
				if ( $setting->id === $control_setting->id ) {
					$type = $object->type;
					$attr = $object->input_attrs;
					break 2;
				}
			}
		}
	}

	if ( ! $type ) {
		return $setting->default;
	}
	switch ( $type ) {
		case 'select':
			$sanitized_value = bayleaf_sanitize_select( $option, $setting );
			break;

		case 'checkbox':
			$sanitized_value = $option ? 1 : '';
			break;

		case 'number':
		case 'range':
			$sanitized_value = bayleaf_sanitize_number( $option, $setting, $attr );
			break;

		case 'tel':
		case 'text':
			$sanitized_value = sanitize_text_field( $option );
			break;

		case 'textarea':
			$sanitized_value = wp_kses_post( $option );
			break;

		case 'url':
		case 'image':
			$sanitized_value = esc_url_raw( $option );
			break;

		case 'color':
			$sanitized_value = sanitize_hex_color( $option );
			break;

		case 'email':
			$value           = sanitize_email( $option );
			$sanitized_value = is_email( $value ) ? $value : $setting->default;
			break;

		default:
			$sanitized_value = apply_filters( 'bayleaf_sanitized_value', $setting->default, $type, $option, $setting );
			break;
	} // End switch.

	return $sanitized_value;
}

/**
 * Sanitize select choices.
 *
 * @since 1.0.0
 *
 * @param str                  $option  Customizer Option selected.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return string
 */
function bayleaf_sanitize_select( $option, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	if ( array_key_exists( $option, $choices ) ) :
		return $option;
	elseif ( in_array( $option, $choices, true ) ) :
		return $option;
	else :
		return $setting->default;
	endif;
}

/**
 * Sanitize and Validate number
 *
 * @since 1.0.0
 *
 * @param int                  $option  excerpt length.
 * @param WP_Customize_Setting $setting Setting instance.
 * @param array                $attr    Input Attributes array.
 * @return Mixed
 */
function bayleaf_sanitize_number( $option, $setting, $attr ) {
	if ( '' === $option && '' === $setting->default ) {
		return $setting->default;
	}

	$option = abs( $option );

	if ( isset( $attr['max'] ) ) {
		$option = $option > $attr['max'] ? $attr['max'] : $option;
	}

	if ( isset( $attr['min'] ) ) {
		$option = $option < $attr['min'] ? $attr['min'] : $option;
	}

	if ( isset( $attr['step'] ) && is_float( $attr['step'] ) ) {
		$option = abs( floatval( $option ) );
	} else {
		$option = absint( $option );
	}

	if ( $option || 0 === $option ) {
		return $option;
	}

	return $setting->default;
}
