<?php
/**
 * Facilitate HTML markup
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Outputs a HTML element.
 *
 * @since  1.0.0
 *
 * @param string   $class     Markup HTML class(es).
 * @param callable $callbacks Callback functions to echo content inside the wrapper.
 * @param string   $open      Markup wrapper opening div.
 * @param string   $close     Markup wrapper closing div.
 * @return void
 */
function bayleaf_markup( $class = '', $callbacks = [], $open = '<div%s>', $close = '</div>' ) {
	if ( ! $class ) {
		return;
	}

	if ( is_array( $class ) ) {
		// First HTML class will become context for the element.
		$context = array_shift( $class );
		// Remaining classes will simply be added to the element.
		$classes = join( ' ', array_map( 'esc_attr', $class ) );
	} else {
		$context = $class;
		$classes = '';
	}

	$hook = str_replace( '-', '_', $context );

	/**
	 * Filter array of all supplied callable functions for this context.
	 *
	 * @since 1.0.0
	 *
	 * @param arrray $callbacks Array of callback functions (may be with args).
	 */
	$callbacks = apply_filters( "bayleaf_markup_{$hook}", $callbacks );

	// Return if there are no display functions.
	if ( empty( $callbacks ) ) {
		return;
	}

	printf( $open, bayleaf_get_attr( $context, [ 'class' => $classes ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	foreach ( $callbacks as $callback ) {
		$callback = (array) $callback;
		$function = array_shift( $callback );

		// Display output of a function which returns the markup.
		if ( 'echo' === $function ) {
			$function = array_shift( $callback );

			if ( is_callable( $function ) ) {
				echo call_user_func_array( $function, $callback ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		} else {
			if ( is_callable( $function ) ) {
				call_user_func_array( $function, $callback );
			}
		}
	}

	echo $close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Outputs an HTML element's attributes.
 *
 * The purposes of this is to provide a way to hook into the attributes for specific
 * HTML elements and create new or modify existing attributes, without modifying actual
 * markup templates.
 *
 * @since  1.0.0
 *
 * @param  str   $slug The slug/ID of the element (e.g., 'sidebar').
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 */
function bayleaf_attr( $slug, $attr = [] ) {
	echo bayleaf_get_attr( $slug, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Gets an HTML element's attributes.
 *
 * This code is inspired (but totally modified) from Stargazer WordPress Theme,
 * Copyright 2013 – 2018 Justin Tadlock. Stargazer is distributed
 * under the terms of the GNU GPL.
 *
 * @since  1.0.0
 *
 * @param  str   $slug The slug/ID of the element (e.g., 'sidebar').
 * @param  array $attr Array of attributes to pass in (overwrites filters).
 * @return string
 */
function bayleaf_get_attr( $slug, $attr = [] ) {
	if ( ! $slug ) {
		return '';
	}

	$out = '';

	if ( false !== $attr ) {
		if ( isset( $attr['class'] ) ) {
			$attr['class'] .= ' ' . $slug;
		} else {
			$attr['class'] = $slug;
		}
	}

	$hook = str_replace( '-', '_', $slug );

	/**
	 * Filter element's attributes.
	 *
	 * @since 1.0.0
	 */
	$attr = apply_filters( "bayleaf_get_attr_{$hook}", $attr, $slug );

	if ( $attr ) {
		foreach ( $attr as $name => $value ) {
			$out .= sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) );
		}
	}

	return $out;
}

/**
 * Output a font icon.
 *
 * @since 1.0.0
 *
 * @param array $args Parameters needed to display a font icon.
 */
function bayleaf_icon( $args = [] ) {
	$icon_markup = bayleaf_get_icon( $args );
	if ( $icon_markup ) {
		echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Return font icon SVG markup.
 *
 * This function incorporates code from Twenty Seventeen WordPress Theme,
 * Copyright 2016-2017 WordPress.org. Twenty Seventeen is distributed
 * under the terms of the GNU GPL.
 *
 * @param array $args {
 *     Parameters needed to display an SVG.
 *
 *     @type string $icon  Required SVG icon filename.
 *     @type string $title Optional SVG title.
 *     @type string $desc  Optional SVG description.
 * }
 * @return string Font icon SVG markup.
 */
function bayleaf_get_icon( $args = [] ) {
	// Make sure $args are an array.
	if ( empty( $args ) ) {
		return esc_html__( 'Please define default parameters in the form of an array.', 'bayleaf' );
	}

	// Define an icon.
	if ( false === array_key_exists( 'icon', $args ) ) {
		return esc_html__( 'Please define an SVG icon filename.', 'bayleaf' );
	}

	// Add icon to icon loader array.
	$loader = bayleaf\Icon_Loader::get_instance();
	$loader->add( $args['icon'] );

	// Set defaults.
	$defaults = [
		'icon'     => '',
		'title'    => '',
		'desc'     => '',
		'fallback' => false,
	];

	// Parse args.
	$args = wp_parse_args( $args, $defaults );

	// Set aria hidden.
	$aria_hidden = ' aria-hidden="true"';

	// Set ARIA.
	$aria_labelledby = '';

	/*
	 * Bayleaf doesn't use the SVG title or description attributes; non-decorative icons are
	 * described with .screen-reader-text. However, child themes can use the title and description
	 * to add information to non-decorative SVG icons to improve accessibility.
	 *
	 * Example 1 with title: <?php echo bayleaf_get_svg( [ 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ) ] ); ?>
	 *
	 * Example 2 with title and description: <?php echo bayleaf_get_svg( [ 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ), 'desc' => __( 'This is the description', 'textdomain' ) ] ); ?>
	 *
	 * See https://www.paciellogroup.com/blog/2013/12/using-aria-enhance-svg-accessibility/.
	 */
	if ( $args['title'] ) {
		$aria_hidden     = '';
		$unique_id       = uniqid();
		$aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';

		if ( $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
		}
	}

	// Begin SVG markup.
	$svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img" focusable="false">';

	// Display the title.
	if ( $args['title'] ) {
		$svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

		// Display the desc only if the title is already set.
		if ( $args['desc'] ) {
			$svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
		}
	}

	/*
	 * Display the icon.
	 *
	 * The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
	 *
	 * See https://core.trac.wordpress.org/ticket/38387.
	 */
	$svg .= ' <use href="#icon-' . esc_attr( $args['icon'] ) . '" xlink:href="#icon-' . esc_attr( $args['icon'] ) . '"></use> ';

	// Add some markup to use as a fallback for browsers that do not support SVGs.
	if ( $args['fallback'] ) {
		$svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) . '"></span>';
	}

	$svg .= '</svg>';

	return $svg;
}

/**
 * Get navigation menu markup.
 *
 * Create navigation menu markup based on arguments provided.
 *
 * @since 1.0.0
 *
 * @param string $nav_classes Menu container ID.
 * @param string $label       Menu label.
 * @param array  $args        Additional wp_nav_menu args.
 */
function bayleaf_nav_menu( $nav_classes, $label, $args = [] ) {

	$menu  = sprintf( '<h2 class="screen-reader-text">%s</h2>', esc_html( $label ) );
	$menu .= wp_nav_menu( array_merge( $args, [ 'echo' => false ] ) );

	if ( is_array( $nav_classes ) ) {
		$nav_id      = $nav_classes[0];
		$nav_classes = array_map( 'esc_attr', $nav_classes );
		$nav_classes = join( ' ', $nav_classes );
	} else {
		$nav_id = $nav_classes;
	}

	printf(
		'<nav id="%1$s" class="%2$s" aria-label="%3$s">%4$s</nav>',
		esc_attr( $nav_id ),
		esc_attr( $nav_classes ),
		esc_attr( $label ),
		$menu // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * Get widget markup.
 *
 * Create widget markup based on arguments provided.
 *
 * @since 1.0.0
 *
 * @param string $id     Widget ID.
 * @param string $class  Widget HTML class.
 * @param string $label  Widget label.
 * @param string $widget Registered sidebar to be displayed.
 * @param bool   $aside  Aside or Article.
 */
function bayleaf_widgets( $id, $class, $label, $widget = 'sidebar', $aside = true ) {

	// Return if no ID.
	if ( ! $id ) {
		return;
	}

	// Short circuit filter.
	$check = apply_filters( "bayleaf_widgets_{$id}", false, $widget );
	if ( false !== $check ) {
		return;
	}

	// Return if sidebar is not in use.
	if ( ! is_active_sidebar( $widget ) ) {
		return;
	}
	?>

	<?php if ( $aside ) : ?>
		<aside id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<h2 class="screen-reader-text"><?php echo esc_html( $label ); ?></h2>
			<?php dynamic_sidebar( $widget ); ?>
		</aside>
	<?php else : ?>
		<article id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php dynamic_sidebar( $widget ); ?>
		</article>
	<?php endif; ?>

	<?php
}

/**
 * Get a theme template part.
 *
 * This is a wrapper function on WordPress built-in function "get_template_part()"
 * to provide additional filter to change required template.
 *
 * @since 1.0.0
 *
 * @param string $path      Template file path.
 * @param string $file_name Template file name.
 * @param string $arg       Additional arguments for identification.
 * @return void
 */
function bayleaf_get_template_partial( $path, $file_name, $arg = '' ) {
	$file = $path . '/' . $file_name;

	$file_name = str_replace( '-', '_', $file_name );
	$file      = apply_filters( "bayleaf_template_{$file_name}", $file, $arg );

	// Load the template file.
	if ( $file ) {
		get_template_part( $file );
	}
}

/**
 * Wrapper for 'add_action()' for theme specific hooks.
 *
 * This is a wrapper function on WordPress built-in function "add_action()"
 * to make it slightly clean and redable.
 *
 * @since 1.0.0
 *
 * @param string $func_name     Name of the functions to be hooked.
 * @param string $hook Template Name of the hook.
 * @param int    $priority      Priority of execution.
 * @param int    $args          Number of arguments passed to the function.
 * @return void
 */
function bayleaf_add_markup_for( $func_name, $hook, $priority = 10, $args = 1 ) {
	add_action( "bayleaf_{$hook}", "bayleaf_{$func_name}", $priority, $args );
}

/**
 * Wrapper for 'remove_action()' for theme specific hooks.
 *
 * This is a wrapper function on WordPress built-in function "remove_action()"
 * to make it slightly clean and redable.
 *
 * @since 1.0.0
 *
 * @param string $func_name     Name of the functions to be unhooked.
 * @param string $hook Template Name of the hook.
 * @param int    $priority      Priority of execution.
 * @param int    $args          Number of arguments passed to the function.
 * @return void
 */
function bayleaf_remove_markup_for( $func_name, $hook, $priority = 10, $args = 1 ) {
	remove_action( "bayleaf_{$hook}", "bayleaf_{$func_name}", $priority, $args );
}

/**
 * Set default values for theme customization options.
 *
 * @since 1.0.0
 *
 * @param str $option Control option for which default value is required.
 * @return mixed Returns integer, string or array option values.
 */
function bayleaf_get_default_value( $option ) {

	/**
	 * Filter default values for customizer options.
	 *
	 * @since 1.0.0
	 */
	$defaults = apply_filters( 'bayleaf_theme_defaults', [] );
	return isset( $defaults[ $option ] ) ? $defaults[ $option ] : '';
}

/**
 * Prepare inline css strings to be enqueued.
 *
 * Strip HTML tags including script/style and remove left over line breaks and white space chars.
 * Also, bit of css minification.
 *
 * @since 1.0.0
 *
 * @param string $styles css string to be prepared.
 * @return string
 */
function bayleaf_prepare_css( $styles ) {
	/*
	 * Properly strip all HTML tags including script/style and
	 * remove left over line breaks and white space chars.
	 */
	$styles = wp_strip_all_tags( $styles, true );

	// Bit of css minification.
	$to_be_replaced = [ ': ', '; ', ' {', ', ', ';}', ' + ' ];
	$replace_with   = [ ':', ';', '{', ',', '}', '+' ];
	$styles         = str_replace( $to_be_replaced, $replace_with, $styles );

	return $styles;
}

/**
 * Convert hex color code to equivalent RGB code.
 *
 * @since 1.0.0
 *
 * @param string  $hex_color Hexadecimal color value.
 * @param boolean $as_string Return as string or associative array.
 * @param string  $sep       String to separate RGB values.
 * @return string
 */
function bayleaf_hex_to_rgb( $hex_color, $as_string, $sep = ',' ) {
	$hex_color = preg_replace( '/[^0-9A-Fa-f]/', '', $hex_color );
	$rgb_array = [];
	if ( 6 === strlen( $hex_color ) ) {
		$color_val          = hexdec( $hex_color );
		$rgb_array['red']   = 0xFF & ( $color_val >> 0x10 );
		$rgb_array['green'] = 0xFF & ( $color_val >> 0x8 );
		$rgb_array['blue']  = 0xFF & $color_val;
	} elseif ( 3 === strlen( $hex_color ) ) {
		$rgb_array['red']   = hexdec( str_repeat( substr( $hex_color, 0, 1 ), 2 ) );
		$rgb_array['green'] = hexdec( str_repeat( substr( $hex_color, 1, 1 ), 2 ) );
		$rgb_array['blue']  = hexdec( str_repeat( substr( $hex_color, 2, 1 ), 2 ) );
	} else {
		return false; // Invalid hex color code.
	}
	return $as_string ? implode( $sep, $rgb_array ) : $rgb_array;
}

/**
 * Toggle button for Bayleaf archive description.
 *
 * @since 1.4.9
 *
 * @return string
 */
function bayleaf_archive_description_toggle() {
	$desc = get_the_archive_description();
	if ( $desc ) {
		return sprintf(
			'<button class="archive-desc-toggle"><span class="screen-reader-text">%1$s</span><span aria-hidden="true"><span class="archive-topen">%2$s</span><span class="archive-tclose">%3$s</span></span></button>',
			esc_html__( 'Archive description toggle', 'bayleaf' ),
			esc_html__( '+', 'bayleaf' ),
			esc_html__( '-', 'bayleaf' )
		);
	} else {
		return '';
	}
}
