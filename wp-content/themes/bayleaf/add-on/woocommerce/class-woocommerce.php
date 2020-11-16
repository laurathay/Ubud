<?php
/**
 * WooCommerce Plugin Support.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * WooCommerce plugin support.
 *
 * @since  1.0.0
 */
class WooCommerce {

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
		// Return if WooCommerce is not activated.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		add_action( 'wp_enqueue_scripts', [ self::get_instance(), 'enqueue_front' ] );
		add_action( 'after_setup_theme', [ self::get_instance(), 'add_woo_support' ] );
		add_filter( 'bayleaf_register_sidebar', [ self::get_instance(), 'register_widget_areas' ] );
		add_filter( 'bayleaf_inside_sidebar', [ self::get_instance(), 'display_sidebar' ] );
		add_filter( 'bayleaf_markup_header_widgets', [ self::get_instance(), 'header_widgets' ] );
		add_filter( 'woocommerce_add_to_cart_fragments', [ self::get_instance(), 'update_cart_on_ajax' ] );
		add_filter( 'woocommerce_cart_item_remove_link', [ self::get_instance(), 'mini_cart_remove_link' ] );
		add_filter( 'body_class', [ self::get_instance(), 'woocommerce_body_classes' ] );
		add_filter( 'post_class', [ self::get_instance(), 'woocommerce_product_classes' ] );
		add_filter( 'get_product_search_form', [ self::get_instance(), 'product_search_form' ] );
		add_action( 'woocommerce_after_shop_loop_item_title', [ self::get_instance(), 'out_of_stock' ] );
		add_action( 'woocommerce_before_main_content', [ self::get_instance(), 'woocommerce_before_start' ] );
		add_action( 'woocommerce_after_main_content', [ self::get_instance(), 'woocommerce_before_end' ] );
		add_action( 'woocommerce_sidebar', [ self::get_instance(), 'woocommerce_after_sidebar' ], 12 );
		add_filter( 'bayleaf_theme_sections', [ self::get_instance(), 'customizer_section' ] );
		add_filter( 'bayleaf_theme_controls', [ self::get_instance(), 'customizer_controls' ] );
		add_filter( 'bayleaf_theme_defaults', [ self::get_instance(), 'customizer_defaults' ] );
		add_action( 'widgets_init', [ self::get_instance(), 'register_custom_widget' ] );
		add_action( 'woocommerce_before_shop_loop_item', [ self::get_instance(), 'product_wrapper_open' ], 0 );
		add_action( 'woocommerce_after_shop_loop_item', [ self::get_instance(), 'product_wrapper_close' ], 12 );
		add_filter( 'woocommerce_product_get_rating_html', [ self::get_instance(), 'modify_rating_html' ], 10, 3 );
		add_filter( 'bayleaf_get_attr_site_main', [ self::get_instance(), 'remove_wrapper' ] );
		add_filter( 'bayleaf_after_woo_widget_title', [ self::get_instance(), 'woo_wid_title' ], 10, 2 );
		add_filter( 'get_terms_defaults', [ self::get_instance(), 'bug_fix_wc_change_get_terms_defaults' ], 9, 2 );
	}

	/**
	 * Declare WooCommerce & its features support.
	 *
	 * @since 1.0.0
	 */
	public function add_woo_support() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

	/**
	 * WooCommerce markup before opening wrapper.
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_before_start() {
		?>
		<div id="content"<?php bayleaf_attr( 'site-content' ); ?>>
		<?php
		// This hook is documented in index.php.
		do_action( 'bayleaf_on_top_of_site_content', 'on_top_of_site_content' );
		?>
		<div id="primary"<?php bayleaf_attr( 'content-area' ); ?>>
		<main id="main"<?php bayleaf_attr( 'site-main' ); ?>>
		<?php
	}

	/**
	 * WooCommerce markup for closing wrapper.
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_before_end() {
		echo '</main></div>';
	}

	/**
	 * WooCommerce markup after sidebar.
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_after_sidebar() {
		// This hook is documented in index.php.
		do_action( 'bayleaf_bottom_of_site_content', 'bottom_of_site_content' );
		?>
		</div><!-- #content -->
		<?php
	}

	/**
	 * Register widget area.
	 *
	 * @since 1.0.0
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 *
	 * @param array $widgets Array of arguments for the sidebar being registered.
	 * @return array Array of arguments for the sidebar being registered.
	 */
	public function register_widget_areas( $widgets ) {
		return array_merge(
			$widgets,
			[
				[
					'name'          => esc_html__( 'WooCommerce - Archive Filters', 'bayleaf' ),
					'id'            => 'wc-archive-filters',
					'before_widget' => '<section id="%1$s" class="widget wc-filter %2$s">',
				],
			]
		);
	}

	/**
	 * Conditionally display sidebar on a wooCommerce page.
	 *
	 * @since 1.0.0
	 */
	public function display_sidebar() {
		// No sidebar required on Single product, cart and checkout pages.
		if ( is_product() || is_cart() || is_checkout() ) {
			return;
		}

		// Replace default sidebar with Filters sidebar on Product archive pages.
		if ( is_shop() || is_product_taxonomy() ) {
			bayleaf_widgets(
				'wc-archive-filters',
				'wc-archive-filters sidebar-widget-area',
				esc_html__( 'WooCommerce - Procuct Archive Filters', 'bayleaf' ),
				'wc-archive-filters'
			);
		}
	}

	/**
	 * Add shopping cart to site header widgets area.
	 *
	 * If default widget area is active, this function will add shopping cart to it.
	 *
	 * @since  1.0.0
	 *
	 * @param arrray $callbacks Array of callback functions (may be with args).
	 * @return string
	 */
	public function header_widgets( $callbacks ) {
		if ( bayleaf_get_mod( 'bayleaf_woo_mini_cart', 'none' ) ) {
			array_unshift( $callbacks, [ [ $this, 'add_cart_icon' ] ] );
		}
		return $callbacks;
	}

	/**
	 * Header Cart markup.
	 *
	 * @since 1.0.0
	 */
	public function add_cart_icon() {
		$cart_contents_count = WC()->cart->get_cart_contents_count();
		$cart_class          = $cart_contents_count ? '' : ' screen-reader-text';

		// Shopping Text.
		$cart_text = $cart_contents_count ? esc_html__( 'Continue Shopping', 'bayleaf' ) : esc_html__( 'Start Shopping', 'bayleaf' );

		// Markup for number of items added to the cart (if any).
		$display_items_count = sprintf(
			'<span class="wc-cart-items %1$s">%2$s</span>',
			$cart_class,
			absint( $cart_contents_count )
		);

		// Shop page link markup.
		$shopping_page_link = sprintf(
			'<a class="wc-shop-pagelink" href="%1$s">%2$s <span class="long-arrow-right">%3$s</span></a>',
			esc_url( wc_get_page_permalink( 'shop' ) ),
			$cart_text,
			bayleaf_get_icon( [ 'icon' => 'long-arrow-right' ] )
		);

		// Shop cart widget toggle button.
		printf(
			'<button aria-expanded="false" class="wc-cart-toggle" %1$s>%2$s<span class="screen-reader-text">%3$s</span>%4$s</button>',
			is_cart() || is_checkout() ? 'disabled' : '',
			bayleaf_get_icon( [ 'icon' => 'cart' ] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html__( 'View shopping cart', 'bayleaf' ),
			$display_items_count // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);

		// Display woocommerce cart widget.
		the_widget(
			'WC_Widget_Cart',
			[ 'title' => '' ],
			[
				'before_widget' => '<div id="wc-cart-widget" class="widget %s">',
				'after_widget'  => $shopping_page_link . '</div>',
			]
		);
	}

	/**
	 * Cart Fragments to be updated.
	 * Ensure cart contents update when products are added to the cart via AJAX
	 *
	 * @param  array $fragments Fragments to refresh via AJAX.
	 * @return array            Fragments to refresh via AJAX
	 */
	public function update_cart_on_ajax( $fragments ) {
		$cart_contents_count = WC()->cart->get_cart_contents_count();
		$cart_class          = $cart_contents_count ? '' : ' screen-reader-text';

		// Text to display for 'Shop' page link.
		$cart_text = $cart_contents_count ? esc_html__( 'Continue Shopping', 'bayleaf' ) : esc_html__( 'Start Shopping', 'bayleaf' );

		// Update number of items in the cart.
		$fragments['span.wc-cart-items'] = '<span class="wc-cart-items' . $cart_class . '">' . absint( $cart_contents_count ) . '</span>';

		// Update shop page link text.
		$fragments['a.wc-shop-pagelink'] = '<a class="wc-shop-pagelink" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . $cart_text . ' <span class="long-arrow-right">' . bayleaf_get_icon( [ 'icon' => 'long-arrow-right' ] ) . '</span></a>';

		return $fragments;
	}

	/**
	 * Modify mini cart remove link button. Add delete icon inplace of 'x'.
	 *
	 * @since  1.0.0
	 *
	 * @param string $markup Cart remove link markup.
	 * @return string
	 */
	public function mini_cart_remove_link( $markup ) {
		return str_replace( '&times;', bayleaf_get_icon( [ 'icon' => 'trash' ] ), $markup );
	}

	/**
	 * Extend the default WooCommerce product classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the WooCommerce Product.
	 * @return array
	 */
	public function woocommerce_product_classes( $classes ) {
		if ( ! is_woocommerce() ) {
			return $classes;
		}
		global $product;
		$attachment_ids = $product->get_gallery_image_ids();
		if ( $attachment_ids && is_array( $attachment_ids ) && count( $attachment_ids ) ) {
			$classes[] = 'multiple-product-images';
		}

		return $classes;
	}

	/**
	 * Extend the default WooCommerce body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the WooCommerce Pages.
	 * @return array
	 */
	public function woocommerce_body_classes( $classes ) {

		if ( is_shop() || is_product_category() || is_product_tag() ) {

			// Remove default sidebar layout classes.
			$vals = [ 'no-sidebar', 'sidebar-left', 'sidebar-right' ];
			foreach ( $vals as $val ) {
				$key = array_search( $val, $classes, true );
				if ( false !== $key ) {
					unset( $classes[ $key ] );
				}
			}

			// Add Shop page specific classes.
			if ( ! is_active_sidebar( 'wc-archive-filters' ) ) {
				$classes[] = 'no-sidebar';
			} else {
				$classes[] = bayleaf_get_mod( 'bayleaf_woo_shop_layout', 'attr' );
			}
		}

		return $classes;
	}

	/**
	 * Replace wooCommerce default product searchform.
	 *
	 * @since  1.0.0
	 *
	 * @param str $form Product Searchform markup.
	 * @return string
	 */
	public function product_search_form( $form ) {
		ob_start();
		bayleaf_get_template_partial( 'add-on/woocommerce', 'searchform' );
		$form = ob_get_clean();
		return $form;
	}

	/**
	 * Add out of stock text on archive pages.
	 *
	 * @since  1.0.0
	 */
	public function out_of_stock() {
		$classes = get_post_class();
		// Check if 'outofstock' class exist for current product.
		if ( in_array( 'outofstock', $classes, true ) ) {
			printf( '<span class="woo-sold-out">%s</span>', esc_html__( 'Out of stock', 'bayleaf' ) );
		}
	}

	/**
	 * Add wrapper markup on woocommerce product in loop.
	 *
	 * @since  1.0.0
	 */
	public function product_wrapper_open() {
		echo '<div class="loop-product-wrapper">';
	}

	/**
	 * Add wrapper close markup on woocommerce product in loop.
	 *
	 * @since  1.0.0
	 */
	public function product_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Display rating HTML even if no rating has been given to the product.
	 *
	 * @since 1.0.0
	 *
	 * @param str   $html Rating html markup.
	 * @param float $rating Rating being shown.
	 * @param int   $count  Total number of ratings.
	 * @return string
	 */
	public function modify_rating_html( $html, $rating, $count ) {
		if ( 0 < $rating ) {
			return $html;
		} else {
			$html  = '<div class="star-rating">';
			$html .= wc_get_star_rating_html( $rating, $count );
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Remove flex wrapper class to appropriate site elements.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attr attribute values array.
	 * @return array
	 */
	public function remove_wrapper( $attr ) {
		if ( is_shop() || is_product_category() || is_product_tag() ) {

			// Remove default wrapper classes.
			$attr['class'] = str_replace( [ 'flex-wrapper' ], 'wrapper', $attr['class'] );
		}

		return $attr;
	}

	/**
	 * Set theme customizer sections.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $sections array of theme customizer sections.
	 * @return array Returns array of theme customizer sections.
	 */
	public function customizer_section( $sections = [] ) {
		return array_merge(
			$sections,
			[
				'bayleaf_woocommerce_section' =>
				[
					'title' => esc_html__( 'Theme Options', 'bayleaf' ),
					'panel' => 'woocommerce',
				],
			]
		);
	}

	/**
	 * Set theme customizer controls and settings.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $controls array of theme controls and settings.
	 * @return array Returns array of theme controls and settings.
	 */
	public function customizer_controls( $controls = [] ) {
		return array_merge(
			$controls,
			[
				[
					'label'   => esc_html__( 'Shop Page Sidebar Layout', 'bayleaf' ),
					'section' => 'bayleaf_woocommerce_section',
					'setting' => 'bayleaf_woo_shop_layout',
					'type'    => 'select',
					'choices' => [
						'sidebar-left'  => esc_html__( 'Sidebar-Content', 'bayleaf' ),
						'sidebar-right' => esc_html__( 'Content-Sidebar', 'bayleaf' ),
					],
				],
				[
					'label'   => esc_html__( 'Show WooCommerce mini cart in Site Header.', 'bayleaf' ),
					'section' => 'bayleaf_woocommerce_section',
					'setting' => 'bayleaf_woo_mini_cart',
					'type'    => 'checkbox',
				],
			]
		);
	}

	/**
	 * Set default values for theme customization options.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults Array of customizer option default values.
	 * @return array Returns Array of customizer option default values.
	 */
	public function customizer_defaults( $defaults = [] ) {
		return array_merge(
			$defaults,
			[
				'bayleaf_woo_shop_layout' => 'sidebar-left',
				'bayleaf_woo_mini_cart'   => 1,
			]
		);
	}

	/**
	 * Add items to widget title area.
	 *
	 * @param array $after_title Items before closing of widget title.
	 * @param array $instance    Settings for the current widget instance.
	 * @return str
	 */
	public function woo_wid_title( $after_title, $instance ) {
		$link_html = '';

		// Change only if theme specific after_title args has not been altered.
		if ( '</span></h3>' !== $after_title ) {
			return $after_title;
		}

		$link_html = sprintf( '<span class="dp-term-links"><a class="term-link" href="%1$s">%2$s %3$s</a></span>', esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'View Shop', 'bayleaf' ), bayleaf_get_icon( array( 'icon' => 'long-arrow-right' ) ) );

		return '</span>' . $link_html . '</h3>';
	}

	/**
	 * Enqueue scripts and styles to front end.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_front() {
		wp_enqueue_script(
			'bayleaf_woocommerce_script',
			get_template_directory_uri() . '/add-on/woocommerce/assets/woocommerce.js',
			[],
			BAYLEAF_THEME_VERSION,
			true
		);

		wp_enqueue_style(
			'bayleaf_woocommerce_style',
			get_template_directory_uri() . '/add-on/woocommerce/assets/woocommerce.css',
			[],
			BAYLEAF_THEME_VERSION,
			'all'
		);
		wp_style_add_data( 'bayleaf_woocommerce_style', 'rtl', 'replace' );
	}

	/**
	 * Bug fix for WooCommerce 'wc_change_get_terms_defaults' function in 'includes/wc-term-fruntions.php'.
	 *
	 * To be removed once this bug is resolved in WooCommerce.
	 *
	 * @since 1.3.6
	 *
	 * @param array $defaults   An array of default get_terms() arguments.
	 * @param array $taxonomies An array of taxonomies.
	 * @return array
	 */
	public function bug_fix_wc_change_get_terms_defaults( $defaults, $taxonomies ) {
		if ( is_array( $taxonomies ) && ! isset( $taxonomies[0] ) ) {
			remove_filter( 'get_terms_defaults', 'wc_change_get_terms_defaults', 10, 2 );
			return $defaults;
		}

		return $defaults;
	}

	/**
	 * Register the custom Widget.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_widget() {
		require_once get_template_directory() . '/add-on/woocommerce/class-woocommerce-products.php';
		register_widget( 'bayleaf\Woocommerce_Products' );
	}
}

WooCommerce::init();
