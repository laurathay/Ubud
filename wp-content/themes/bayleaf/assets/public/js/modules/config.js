let config = {

	global: window.bayleafScreenReaderText || { menu: 'primary' },
	elems: { /* HTML elements */
		header       : '#masthead',
		content      : '#content',
		footer       : '#colophon',
		main         : '#main',
		eContent     : '.entry-content',
		mianNav      : '#site-navigation',
		subMenuTog   : '.sub-menu-toggle',
		menuToggle   : '.menu-toggle',
		menuItems    : '.menu-item-has-children, .page_item_has_children',
		menuLinks    : '.menu-item-has-children a, .page_item_has_children a',
		dirLinks     : '.menu-item-has-children > a, .page_item_has_children > a',
		headWidToggle: '.action-toggle',
		headWid      : '#header-widget-area',
		hSearchToggle: '.search-toggle',
		headSearch   : '#header-search-wrapper',
		scrlTop      : '.scrl-to-top',
		comments     : '.comments-area',
		cToggle      : '.comments-toggle',
		dToggle      : '.archive-desc-toggle',
		dContent     : '.page-header-description',
		widthStyle   : '.has-featured-img, .slider2, .dp-wrapper',
	},
	cls: { /* HTML Classes */
		toggled: 'toggled-on',
		toggler: 'toggled-btn',
		single : 'singular-view',
		visible: 'makeitvisible',
	},
	vidsel: [ /* Embedded video selectors */
		'iframe[src*="youtube.com"]',
		'iframe[src*="youtube-nocookie.com"]',
		'iframe[src*="vimeo.com"]',
		'iframe[src*="dailymotion.com"]',
		'iframe[src*="videopress.com"]'
	]
}

export default config;
