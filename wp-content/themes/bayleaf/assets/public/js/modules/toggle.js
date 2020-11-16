import conf from './config';
import lib from './library';

/**
 * Unspecified toggle events.
 */
class Toggle {

	/**
	 * The constructor function.
	 *
	 * @since 1.4.9
	 */
	constructor() {

		const elems   = conf.elems;
		this.dToggle  = lib.get(elems.dToggle);
		this.dContent = lib.get(elems.dContent);
		this.events();
	}

	/**
	 * JS event handling.
	 * 
	 * @since 1.4.9
	 */
	events() {

		lib.on('click', this.dToggle, this.toggleDesc.bind(this));
	}

	/**
	 * Toggle archive description on archive pages.
	 * 
	 * @since 1.4.9
	 */
	toggleDesc() {
		lib.toggleClass(this.dToggle, conf.cls.toggler);
		lib.toggleClass(this.dContent, conf.cls.toggled);
	}
}

export default Toggle;