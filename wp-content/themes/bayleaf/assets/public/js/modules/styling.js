import conf from './config';
import lib from './library';

/**
 * Dynamically controls elements styling.
 */
class StylingControl {

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		const elem = conf.elems;
		this.cutOffWidth = 800;
		this.elems = lib.get(elem.widthStyle);

		this.widthStyleControl();
		lib.addRaf(this.widthStyleControl.bind(this), 'resize');
	}

	/**
	 * Provision to add separate styles for narrow and wide elements.
	 * 
	 * @since 1.3.5
	 */
	widthStyleControl() {

		lib.eachElement(this.elems, elem => {
			if (this.cutOffWidth > elem.offsetWidth) {
				lib.removeClass(elem, 'widescreen');
			} else {
				lib.addClass(elem, 'widescreen');
			}
		});
	}
}

export default StylingControl;