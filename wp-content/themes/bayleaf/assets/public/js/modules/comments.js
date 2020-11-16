import conf from './config';
import lib from './library';

class Comments {

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		let elems     = conf.elems;
		this.comments = lib.get(elems.comments);
		this.cToggle  = lib.get(elems.cToggle);

		this.events();
	}

	/**
	 * JS event handling.
	 * 
	 * @since 1.3.5
	 */
	events() {

		lib.on('click', this.cToggle, this.toggleComments.bind(this));
	}

	/**
	 * Toggle navigation menu.
	 * 
	 * @since 1.3.5
	 */
	toggleComments() {

		lib.toggleClass(this.cToggle, conf.cls.toggler);
		if (lib.hasClass(this.cToggle, conf.cls.toggler)) {
			lib.slideDown(this.comments[0]);
		} else {
			lib.slideUp(this.comments[0]);
		}
	}
}

export default Comments;
