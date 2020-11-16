import conf from './config';
import lib from './library';

class Menu {

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		let elems       = conf.elems;
		this.header     = lib.get(elems.header);
		this.nav        = lib.get(elems.mianNav);
		this.menu       = lib.get(`#${conf.global.menu}`);
		this.menuToggle = lib.get(elems.menuToggle, this.header);
		this.subToggle  = lib.get(elems.subMenuTog, this.menu);
		this.items      = lib.get(elems.menuItems, this.menu);
		this.links      = lib.get(elems.menuLinks, this.menu);
		this.dLinks     = lib.get(elems.dirLinks, this.menu);

		this.events();
	}

	/**
	 * JS event handling.
	 * 
	 * @since 1.3.5
	 */
	events() {

		lib.on('click', this.menuToggle, this.toggleMenu.bind(this));
		lib.on('click', this.subToggle, this.toggleSubMenu);
		lib.on('mouseenter, mouseleave', this.items, this.toggleItem );
		lib.on('focus, blur', this.links, this.toggleLinkItems);
		lib.on('touchstart', lib.body, this.resetMenu.bind(this));
		lib.on('touchstart, click', this.dLinks, this.activateItem);
	}

	/**
	 * Toggle navigation menu.
	 * 
	 * @since 1.3.5
	 */
	toggleMenu() {

		lib.toggleClass(this.menuToggle, conf.cls.toggler);
		lib.toggleClass(this.nav, conf.cls.toggled);
	}

	/**
	 * Toggle navigation menu.
	 * 
	 * @since 1.3.5
	 */
	toggleSubMenu() {

		let subMenu = this.nextElementSibling;
		lib.toggleClass(this, conf.cls.toggler);
		lib.toggleClass(subMenu, conf.cls.toggled);
		if (lib.hasClass(this, conf.cls.toggler)) {
			lib.slideDown(subMenu, 200);
		} else {
			lib.slideUp(subMenu, 200);
		}
	}

	/**
	 * Toggle navigation menu items.
	 * 
	 * @since 1.3.5
	 */
	toggleItem(e) {
		const eType = e.type;
		if ('mouseenter' === eType) {
			lib.addClass(this, conf.cls.toggled);
		} else if ('mouseleave' === eType) {
			lib.removeClass(this, conf.cls.toggled);
		}
	}

	/**
	 * Toggle navigation menu items.
	 * 
	 * @since 1.3.5
	 */
	toggleLinkItems(e) {

		let el = this;
		const eType = e.type;
		while (null !== el && ! lib.hasClass(el, 'nav-menu')) {
			if (lib.hasClass(el, 'menu-item, page_item')) {
				if ( 'focus' === eType ) {
					lib.addClass(el, conf.cls.toggled);
				} else {
					lib.removeClass(el, conf.cls.toggled);
				}
			}
			el = el.parentElement;
		}
	}

	/**
	 * Close all open menu items if clicked/touched outside menu.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {Event} e
	 */
	resetMenu(e) {

		if (lib.isHidden(this.menuToggle)) return;
		if (this.menu.contains(e.target)) return;
		lib.removeClass(this.items, conf.cls.toggled);
	}

	/**
	 * Activate currently touched menu item.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {Event} e
	 */
	activateItem(e) {

		let item = link.parentElement;
		let siblings = item.parentElement.children;
		if (lib.isHidden(lib.get(conf.elems.menuToggle))) return;
		if (lib.hasClass(item, conf.cls.toggled)) return;
		e.preventDefault();
		lib.removeClass(siblings, conf.cls.toggled);
		lib.addClass(item, conf.cls.toggled);
	}
}

export default Menu;
