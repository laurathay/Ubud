<?php
/**
 * The template part for displaying image attachment meta information
 *
 * @package Bayleaf
 * @since 1.0.0
 */

if ( ! is_attachment() || ! wp_attachment_is_image() ) {
	return;
}

// Retrieve attachment metadata.
$bayleaf_metadata = wp_get_attachment_metadata();
if ( false === $bayleaf_metadata ) {
	return;
}
printf(
	'<span%1$s"><a href="%2$s" title="%3$s">%4$s (%5$s &times; %6$s)</a></span>',
	bayleaf_get_attr( 'attachment-meta', [ 'class' => 'full-size-link' ] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( wp_get_attachment_url() ),
	esc_attr_x( 'Link to full-size image', 'Attachment image link title attribute', 'bayleaf' ),
	esc_html__( 'Full resolution', 'bayleaf' ),
	absint( $bayleaf_metadata['width'] ),
	absint( $bayleaf_metadata['height'] )
);
