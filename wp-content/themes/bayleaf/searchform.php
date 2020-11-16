<?php
/**
 * Display search form
 *
 * @link https://developer.wordpress.org/reference/functions/get_search_form
 *
 * @package Bayleaf
 * @since 1.0.0
 */

?>

<form method="get"<?php bayleaf_attr( 'search-form' ); ?> action="<?php echo esc_url( home_url( '/' ) ); ?>">
<label class="label-search">
	<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'bayleaf' ); ?></span>
	<input type="search"<?php bayleaf_attr( 'search-field' ); ?> placeholder="<?php echo esc_attr_x( 'Search', 'placeholder', 'bayleaf' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'bayleaf' ); ?>" />
</label>
<button type="submit"<?php bayleaf_attr( 'search-submit' ); ?>><?php bayleaf_icon( [ 'icon' => 'search' ] ); ?><span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'bayleaf' ); ?></span></button>
</form>
