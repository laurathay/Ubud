import conf from './config';
import lib from './library';

/**
 * ScrollToTop
 *
 * Make iframe videos responsive on post/page content.
 */
class ScrollToTop {

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		const elem = conf.elems;
		this.scrlTop = lib.get(elem.scrlTop);

		lib.addRaf(this.scrlTopVisible.bind(this));
		this.events();
	}

	/**
	 * JS event handling.
	 * 
	 * @since 1.3.5
	 */
	events() {

		lib.on('click', this.scrlTop, this.scrollTop);
	}

	/**
	 * Make scroll to top button visible on scroll.
	 * 
	 * @since 1.3.5
	 */
	scrlTopVisible() {

		lib.toggleClass(this.scrlTop, conf.cls.visible, 300 < lib.scrollTop);
	}

	/**
	 * Scroll to top.
	 * 
	 * @since 1.3.5
	 */
	scrollTop() {

		const elem = lib.scrollingElem;
		lib.animate(elem, elem.scrollTop, 0, 300, (elem, value) => { elem.scrollTop = value } );
	}
}

export default ScrollToTop;