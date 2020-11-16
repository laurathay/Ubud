<?php
/**
 * Customizer data
 *
 * Theme Customizer's sections and control field data.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Set theme customizer panels.
 *
 * @since 1.0.0
 *
 * @param  array $panels Array of theme customizer panels.
 * @return array Returns array of theme customizer panels.
 */
function bayleaf_get_theme_panels( $panels = [] ) {
	return array_merge(
		$panels,
		[
			'bayleaf_theme_panel' =>
			[
				'title'       => esc_html__( 'Theme Options', 'bayleaf' ),
				'priority'    => 6,
				'description' => esc_html__( 'Theme options to customize your site.', 'bayleaf' ),
			],
		]
	);
}
add_filter( 'bayleaf_theme_panels', 'bayleaf_get_theme_panels' );

/**
 * Set theme customizer sections.
 *
 * @since 1.0.0
 *
 * @param  array $sections array of theme customizer sections.
 * @return array Returns array of theme customizer sections.
 */
function bayleaf_get_theme_sections( $sections = [] ) {
	return array_merge(
		$sections,
		[
			'bayleaf_general_section' =>
			[
				'title' => esc_html__( 'General Settings', 'bayleaf' ),
				'panel' => 'bayleaf_theme_panel',
			],
			'bayleaf_layout_section'  =>
			[
				'title' => esc_html__( 'Layout Settings', 'bayleaf' ),
				'panel' => 'bayleaf_theme_panel',
			],
		]
	);
}
add_filter( 'bayleaf_theme_sections', 'bayleaf_get_theme_sections' );

/**
 * Set theme customizer controls and settings.
 *
 * @since 1.0.0
 *
 * @param  array $controls array of theme controls and settings.
 * @return array Returns array of theme controls and settings.
 */
function bayleaf_get_theme_controls( $controls = [] ) {
	return array_merge(
		$controls,
		[
			[
				'label'     => esc_html__( 'Display Site Title', 'bayleaf' ),
				'section'   => 'title_tagline',
				'setting'   => 'bayleaf_display_site_title',
				'type'      => 'checkbox',
				'transport' => 'postMessage',
			],
			[
				'label'     => esc_html__( 'Display Tagline', 'bayleaf' ),
				'section'   => 'title_tagline',
				'setting'   => 'bayleaf_display_site_desc',
				'type'      => 'checkbox',
				'transport' => 'postMessage',
				'priority'  => 20,
			],
			[
				'label'       => esc_html__( 'Change Color Scheme', 'bayleaf' ),
				'section'     => 'bayleaf_general_section',
				'setting'     => 'bayleaf_color_scheme',
				'class'       => 'WP_Customize_Color_Control',
				'description' => esc_html__( 'Change theme color scheme.', 'bayleaf' ),
			],
			[
				'label'       => esc_html__( 'Overall Border Radius (in px)', 'bayleaf' ),
				'section'     => 'bayleaf_general_section',
				'setting'     => 'bayleaf_border_radius',
				'type'        => 'number',
				'input_attrs' => array(
					'step' => 1,
					'min'  => 0,
					'max'  => 100,
				),
			],
			[
				'label'   => esc_html__( 'Show Thumbnail (Featured Image) on Single Posts', 'bayleaf' ),
				'section' => 'bayleaf_general_section',
				'setting' => 'bayleaf_show_featured',
				'type'    => 'checkbox',
			],
			[
				'label'   => esc_html__( 'Use Google Fonts', 'bayleaf' ),
				'section' => 'bayleaf_general_section',
				'setting' => 'bayleaf_use_google_fonts',
				'type'    => 'checkbox',
			],
			[
				'label'   => esc_html__( 'Show Primary Navigation', 'bayleaf' ),
				'section' => 'bayleaf_general_section',
				'setting' => 'bayleaf_primary_nav',
				'type'    => 'checkbox',
			],
			[
				'label'   => esc_html__( 'Display Header Search', 'bayleaf' ),
				'section' => 'bayleaf_general_section',
				'setting' => 'bayleaf_header_search',
				'type'    => 'checkbox',
			],
			[
				'label'   => esc_html__( 'Display related posts after post content', 'bayleaf' ),
				'section' => 'bayleaf_general_section',
				'setting' => 'bayleaf_related_posts',
				'type'    => 'checkbox',
			],
			[
				'label'   => esc_html__( 'Show Thumbnail Placeholder', 'bayleaf' ),
				'section' => 'bayleaf_general_section',
				'setting' => 'bayleaf_thumbnail_placeholder',
				'type'    => 'checkbox',
			],
			[
				'label'   => esc_html__( 'Blog Title', 'bayleaf' ),
				'section' => 'bayleaf_general_section',
				'setting' => 'bayleaf_blog_title',
				'type'    => 'text',
			],
			[
				'label'   => esc_html__( 'Page Sidebar Layout', 'bayleaf' ),
				'section' => 'bayleaf_layout_section',
				'setting' => 'bayleaf_page_sidebar',
				'type'    => 'select',
				'choices' => [
					'sidebar-left'  => esc_html__( 'Sidebar-Content', 'bayleaf' ),
					'sidebar-right' => esc_html__( 'Content-Sidebar', 'bayleaf' ),
					'no-sidebar'    => esc_html__( 'Only Content- No Sidebar', 'bayleaf' ),
				],
			],
			[
				'label'   => esc_html__( 'Post Sidebar Layout', 'bayleaf' ),
				'section' => 'bayleaf_layout_section',
				'setting' => 'bayleaf_post_sidebar',
				'type'    => 'select',
				'choices' => [
					'sidebar-left'  => esc_html__( 'Sidebar-Content', 'bayleaf' ),
					'sidebar-right' => esc_html__( 'Content-Sidebar', 'bayleaf' ),
					'no-sidebar'    => esc_html__( 'Only Content- No Sidebar', 'bayleaf' ),
				],
			],
			[
				'label'       => esc_html__( 'Footer Text', 'bayleaf' ),
				'section'     => 'bayleaf_general_section',
				'setting'     => 'bayleaf_footer_text',
				'type'        => 'text',
				'description' => esc_html__( 'Change footer copyright & credit text at the bottom of your site.', 'bayleaf' ) . ' ' . esc_html__( 'For Site Title, Use ', 'bayleaf' ) . '[site_title]. ' . esc_html__( 'For Copyright symbol, Use ', 'bayleaf' ) . '[copy_symbol]. ' . esc_html__( 'For Current Year, Use ', 'bayleaf' ) . '[current_year]',
			],
		]
	);
}
add_filter( 'bayleaf_theme_controls', 'bayleaf_get_theme_controls' );

/**
 * Set default values for theme customization options.
 *
 * @since 1.0.0
 *
 * @param  array $defaults Array of customizer option default values.
 * @return array Returns Array of customizer option default values.
 */
function bayleaf_get_theme_defaults( $defaults = [] ) {
	return array_merge(
		$defaults,
		[
			'bayleaf_border_radius'         => 8,
			'bayleaf_display_site_title'    => 1,
			'bayleaf_display_site_desc'     => 1,
			'bayleaf_use_google_fonts'      => 1,
			'bayleaf_primary_nav'           => 1,
			'bayleaf_header_search'         => 1,
			'bayleaf_related_posts'         => 1,
			'bayleaf_thumbnail_placeholder' => 1,
			'bayleaf_show_featured'         => 1,
			'bayleaf_page_sidebar'          => 'sidebar-right',
			'bayleaf_post_sidebar'          => 'sidebar-right',
			'bayleaf_blog_title'            => '',
			'bayleaf_footer_text'           => '[site_title] [copy_symbol] [current_year] &middot; ' . esc_html__( 'All rights reserved', 'bayleaf' ), // Note: Translation friendly instructions for using footer text placeholders has been given in customizer control description.
		]
	);
}
add_filter( 'bayleaf_theme_defaults', 'bayleaf_get_theme_defaults' );
