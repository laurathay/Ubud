import conf from './config';
import lib from './library';

/**
 * Toggle header widget on button click.
 */
class HeaderWidgetToggle {

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		const elem    = conf.elems;
		this.header   = lib.get(elem.header);
		this.widget   = lib.get(elem.headWid);
		this.toggler  = lib.get(elem.headWidToggle, this.header);
		this.search   = lib.get(elem.headSearch);
		this.sToggler = lib.get(elem.hSearchToggle, this.header);

		if (null === this.widget) return;
		this.events();
	}

	/**
	 * JS event handling.
	 * 
	 * @since 1.3.5
	 */
	events() {

		lib.on('click', this.toggler, this.toggleWidget.bind(this));
		lib.on('click', this.sToggler, this.toggleSearch.bind(this));
	}

	/**
	 * Toggle navigation menu.
	 * 
	 * @since 1.3.5
	 */
	toggleWidget() {

		lib.toggleClass(this.toggler, conf.cls.toggler);
		lib.toggleClass(this.widget, conf.cls.toggled);
		lib.hasClass(this.widget, conf.cls.toggled) ? lib.scrollDisable() : lib.scrollEnable();
	}

	/**
	 * Toggle search.
	 * 
	 * @since 1.0.3
	 */
	toggleSearch() {

		let searchField = lib.get('.search-field', this.search);
		setTimeout(() => { searchField[0].focus() }, 250);

		lib.toggleClass(this.sToggler, conf.cls.toggler);
		lib.toggleClass(this.search, conf.cls.toggled);
		lib.hasClass(this.search, conf.cls.toggled) ? lib.scrollDisable() : lib.scrollEnable();
	}
}

export default HeaderWidgetToggle;