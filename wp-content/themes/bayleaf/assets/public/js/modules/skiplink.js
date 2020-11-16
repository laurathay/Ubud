import lib from './library';

/**
 * File skip-link-focus-fix.js.
 *
 * Helps with accessibility for keyboard only users.
 *
 * Learn more: https://git.io/vWdr2
 */
class SkipLink {

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		if (! lib.isIe()) return;
		this.events();
	}

	/**
	 * JS event handling.
	 * 
	 * @since 1.3.5
	 */
	events() {

		window.addEventListener( 'hashchange', this.focusFix );
	}

	/**
	 * Change Input focus with Skip to content link.
	 * 
	 * @since 1.3.5
	 */
	focusFix() {

		const id = location.hash.substring( 1 );
		let element;

		if ( ! ( /^[A-z0-9_-]+$/.test( id ) ) ) {
			return;
		}
		element = document.getElementById( id );
		if ( element ) {
			if ( ! ( /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) ) {
				element.tabIndex = -1;
			}
			element.focus();
		}
	}
}

export default SkipLink;