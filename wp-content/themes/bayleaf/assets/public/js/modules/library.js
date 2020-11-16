let body = document.getElementsByTagName("BODY")[0];
let scrollingElem = document.scrollingElement || document.documentElement || document.body;
let scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
let bodyHeight = Math.max( body.offsetHeight, body.scrollHeight );
let isScrolling = false;
let isResizing = false;
let isRafRegistered = false;
let bodyScrollDisabled = false;
let scrollPosition = 0;
let windowHeight = window.innerHeight;
let library = {
	
	body, scrollingElem, scrollTop, bodyScrollDisabled, scrollPosition, windowHeight,
	bodyHeight, isScrolling, isResizing, rafScrollCallbacks: [], rafResizeCallbacks: [], isRafRegistered,

	/**
	 * Check if we are on Ios.
	 * 
	 * @since 1.3.5
	 */
	isIos() {
		return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
	},

	/**
	 * Check if we are on IE.
	 * 
	 * @since 1.3.5
	 */
	isIe() {
		return /(trident|msie)/i.test( navigator.userAgent );
	},

	/**
	 * Check if we are on a touch device.
	 * 
	 * @since 1.3.5
	 */
	isTouch() {
		if ( ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch || navigator.maxTouchPoints ) {
			return true;
		}
		return false;
	},

	/**
	 * Add a function to request animationFrame
	 *
	 * @since 1.3.5
	 *
	 * @param {function} callback 
	 */
	onRaf() {

		const scrl = this.rafScrollCallbacks.length;
		const resz = this.rafResizeCallbacks.length;
		if (0 === scrl && 0 === resz) {
			this.isRafRegistered = false;
			return;
		}

		if (this.isScrolling) this.runRafScroll();
		if (this.isResizing) this.runRafResize();

		if (window.requestAnimationFrame) {
			window.requestAnimationFrame(this.onRaf.bind(this));
		} else {
			setTimeout(this.onRaf.bind(this), 1000/60);
		}
	},

	/**
	 * Run all stacked functions at RAF.
	 * 
	 * @since 1.3.5
	 */
	runRafScroll() {

		let callbacks = this.rafScrollCallbacks.filter( callback => 'unbind' !== callback() );
		this.rafScrollCallbacks = callbacks;
	},

	/**
	 * Run all stacked functions at RAF.
	 * 
	 * @since 1.3.5
	 */
	runRafResize() {

		let callbacks = this.rafResizeCallbacks.filter( callback => 'unbind' !== callback() );
		this.rafResizeCallbacks = callbacks;
	},

	/**
	 * Add a callback to the stack.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {function} callback 
	 * @param {string} type Type of continuous event
	 */
	addRaf(callback, type = 'scroll') {

		if ('function' !== typeof callback) return;
		callback();

		if ('resize' === type) {
			this.rafResizeCallbacks.push(callback);
		} else {
			this.rafScrollCallbacks.push(callback);
		}

		if ( false === this.isRafRegistered ) {
			this.onRaf();
			this.isRafRegistered = true;
		}
	},

	/**
	 * Update scrollTop at each animationFrame.
	 * 
	 * @since 1.3.5
	 */
	updateOnScroll() {
		let scrollTimer = null;
		this.addRaf(() => {
			this.scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
			this.bodyHeight = Math.max( body.offsetHeight, body.scrollHeight );
		});

		window.addEventListener('scroll', e => {
			this.isScrolling = true;
			clearTimeout(scrollTimer);
			scrollTimer = setTimeout( () => {
				this.isScrolling = false;
			}, 1000/60 );
		});
	},

	/**
	 * Update globals on screen resize.
	 * 
	 * @since 1.3.5
	 */
	updateOnResize() {
		let resizeTimer1 = null;
		let resizeTimer2 = null;

		window.addEventListener('resize', e => {
			clearTimeout(resizeTimer1);
			resizeTimer1 = setTimeout( () => {
				this.isResizing = true;
				clearTimeout(resizeTimer2);
				resizeTimer2 = setTimeout( () => {
					this.isResizing = false;
				}, 60 );
			}, 1000/60 );
		});
	},

	/**
	 * Disable scroll on the element that scrolls the document.
	 * 
	 * @since 1.3.5
	 */
	scrollDisable() {

		// Return if scroll is already disabled.
		if (this.bodyScrollDisabled) {
			return;
		}

		this.scrollPosition = this.scrollingElem.scrollTop;
		this.scrollingElem.scrollTop = 0;
		this.scrollingElem.classList.add('no-scroll');
		this.bodyScrollDisabled = true;
	},

	/**
	 * Enable scroll on the element that scrolls the document.
	 * 
	 * @since 1.3.5
	 */
	scrollEnable() {

		// Return if scroll is already Enabled.
		if (! this.bodyScrollDisabled) {
			return;
		}

		this.scrollingElem.classList.remove('no-scroll');
		this.scrollingElem.scrollTop = this.scrollPosition;
		this.bodyScrollDisabled = false;
	},

	/**
	 * Convert HTML DOM objects into JS array.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} obj
	 */
	objToArr(obj) {

		let elemsArr;
		if (obj !== Object(obj) || Array.isArray(obj)) return obj;
		elemsArr = Array.prototype.slice.call(obj);
		if (0 === elemsArr.length) elemsArr.push(obj);
		return elemsArr;
	},

	/**
	 * Converts an HTML string to HTML element.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {string} str
	 */
	strToHTML(str) {

		if ('string' !== typeof str ) return str;
		let html = new DOMParser().parseFromString(str, 'text/html');
		return html.body.firstChild;
	},

	/**
	 * Run a callback on each DOM element.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elems 
	 * @param {function} callback 
	 */
	eachElement(elems, callback) {
		if (null === elems) return;
		this.objToArr(elems).forEach( elem => { callback.call(this, elem) } );
	},

	/**
	 * Wrapper function for registering javascript events.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {string} events 
	 * @param {object} elems 
	 * @param {function} callback
	 */
	on(events, elems, callback) {
		let eventsArr = events.split(',').map( item => item.trim() );
		eventsArr.forEach( event => {
			this.eachElement(elems, elem => { elem.addEventListener(event, callback) });
		});
	},

	/**
	 * Wrapper function for un-registering javascript events.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {string} events 
	 * @param {object} elems 
	 * @param {function} callback
	 */
	off(events, elems, callback) {
		let eventsArr = events.split(',').map( item => item.trim() );
		eventsArr.forEach( event => {
			this.eachElement(elems, elem => { elem.removeEventListener(event, callback) });
		});
	},

	/**
	 * Add HTML class to elements.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elems 
	 * @param {string} classname
	 */
	addClass(elems, classname) {
		if (! classname) return;
		let classArrs = classname.split(',').map( item => item.trim() );
		classArrs.forEach(classArr => {
			this.eachElement(elems, elem => { elem.classList.add(classArr) });
		});
	},

	/**
	 * Remove HTML class from elements.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elems 
	 * @param {string} classname
	 */
	removeClass(elems, classname) {
		if (! classname) return;
		let classArrs = classname.split(',').map( item => item.trim() );
		classArrs.forEach(classArr => {
			this.eachElement(elems, elem => { elem.classList.remove(classArr) });
		});
	},

	/**
	 * Toggle HTML class from elements.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elems 
	 * @param {string} classname
	 * @param {bool} bool
	 */
	toggleClass(elems, classname, bool = null) {

		if (! classname) return;
		if (false === bool) {
			this.removeClass(elems, classname);
		} else if (true === bool) {
			this.addClass(elems, classname);
		} else {
			this.eachElement(elems, elem => { elem.classList.toggle(classname) });
		}
	},

	/**
	 * Check if a class exist in an element.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elem
	 * @param {string} classnames
	 */
	hasClass(elem, classnames) {
		let classArr = classnames.split(',').map( item => item.trim() );
		if ('undefined' !== typeof elem.length) elem = elem[0];
		let has = classArr.find( item => elem.classList.contains(item) );
		return ('undefined' !== typeof has) ? true : false;
	},

	/**
	 * Check if element is hidden or not.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elem 
	 */
	isHidden(elem) {
		if ('undefined' !== typeof elem.length) elem = elem[0];
		let display = elem.currentStyle ? elem.currentStyle.display :
		getComputedStyle(elem, null).display;
		return 'none' === display;
	},

	/**
	 * Wrapper function to simplify DOM selection.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {string} selector 
	 * @param {string} context 
	 */
	get(selector, context) {

		context = context || document;
		return this.objToArr(context).reduce((previous, item) => {
			let element = this.getElement(selector, item);
			if (null === element || 0 === element.length) return previous;
			return previous.concat(this.objToArr(element));
		}, []);
	},

	/**
	 * Wrapper function to simplify DOM selection.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {string} selector 
	 * @param {string} context 
	 */
	getElement(selector, context) {
		const idReg = /^#[\w-]*$/;
		const clReg = /^\.[\w-]*$/;
		const tgReg = /^\w+$/;

		if (context !== document && context.nodeType !== 1) return null;
		if ('string' !== typeof selector) return null;
		if (idReg.test(selector)) return document.getElementById(selector.slice(1));
		if (clReg.test(selector)) return context.getElementsByClassName(selector.slice(1));
		if (tgReg.test(selector)) return context.getElementsByTagName(selector);
		return context.querySelectorAll(selector);
	},

	/**
	 * Javascript custom animation function.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elem
	 * @param {number} from
	 * @param {number} to
	 * @param {number} duration
	 * @param {function} fn
	 * @param {function} lastfn
	 */
	animate(elem, from, to, duration, fn, lastfn) {

		let change = to - from;
		let currentTime = 0;
		this.animation(elem, currentTime, from, change, duration, fn, lastfn);
	},

	/**
	 * Animate element's property.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} elem 
	 * @param {number} time 
	 * @param {number} from 
	 * @param {number} change 
	 * @param {number} duration 
	 * @param {function} fn 
	 * @param {funtion} fn
	 */
	animation(elem, time, from, change, duration, fn, lastfn) {

		const increment = 1000/60;
		time += increment;
		let value = this.easeInOutQuad(time, from, change, duration);
		fn(elem, value);
		if(time < duration) {
			setTimeout(() => {
				this.animation(elem, time, from, change, duration, fn, lastfn);
			}, increment);
		} else {
			fn(elem, from + change);
			if ('function' === typeof lastfn) lastfn(elem);
		}
	},

	/**
	 * Animation timing function.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {current time} t 
	 * @param {start value} b 
	 * @param {change in value} c 
	 * @param {duration} d 
	 */
	easeInOutQuad(t, b, c, d) {

		t /= d/2;
		if (t < 1) {
			return c/2*t*t + b;
		}
		t--;
		return -c/2 * (t*(t-2) - 1) + b;
	},

	/**
	 * Add reveal on scroll animation to elements.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {string} elem 
	 * @param {string} ani 
	 * @param {number} rp 
	 * @param {number} duration
	 * @param {number} delay 
	 * @param {string} easing
	 */
	addRosObject(elem, ani = 'fadein', rp = 20, duration = 250, delay = 0, easing = 'easeinout') {

		let index = 0;
		let prevParent = null;
		let elements = this.get(elem);
		if (null === elements || 0 === elements.length) return;
		this.addClass(elements, 'rosobj');
		elements.forEach( element => {
			let thisParent = element.parentNode;
			let height = this.bodyHeight;
			let top = element.getBoundingClientRect().top + this.scrollTop;
			let revealon = 100 - rp;
			element.style.transitionDuration = duration + 'ms';
			element.style.transitionTimingFunction = easing;
			if (0 !== delay ) {
				if (prevParent !== thisParent) index = 1;
				prevParent = thisParent;
				element.style.transitionDelay = index * delay + 'ms';
				index++;
			}
			if (! ani) return;
			this.addRaf( () => {
				let offset = top - this.scrollTop;
				if (height !== this.bodyHeight) offset = element.getBoundingClientRect().top;
				let percent = Math.floor( offset / this.windowHeight * 100 );

				if ( percent < revealon ) {
					element.classList.add(ani);
					return 'unbind';
				}
			} );
		} );
	},

	/**
	 * Add Slider objects.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} container 
	 * @param {object} items 
	 * @param {string} animation 
	 * @param {object} nav
	 */
	addSlider(container, items, animation, nav) {

		let wrappers = this.get(container);
		if (null === wrappers || 0 === wrappers.length) return;
		wrappers.forEach( wrapper => { this.sliderFunctionality(wrapper, items, animation, nav) } );
	},

	/**
	 * Slider Functionality.
	 * 
	 * @since 1.3.5
	 * 
	 * @param {object} wrapper 
	 * @param {object} items 
	 * @param {string} animation
	 * @param {object} nav
	 */
	sliderFunctionality(wrapper, items, animation, nav = 'new') {

		let slides = this.get(items, wrapper);
		let next = 0;
		let prev = 0;
		let total = slides.length - 1;
		let nxtbtn = this.get(nav.next, wrapper);
		let prevbtn = this.get(nav.prev, wrapper);
		let nextSlide = () => {

			this.addClass(slides[next], 'makeitvisible');
			this.removeClass(slides[prev], 'makeitvisible');
			let h1 = slides[prev].offsetHeight;
			let h2 = slides[next].offsetHeight;
			this.animate(wrapper, h1, h2, 600, (ele, value) => { ele.style.height = value + 'px' } );
		};

		wrapper.style.overflow = 'hidden';
		this.addClass(wrapper, animation);
		this.addClass(slides[0], 'makeitvisible, firstslide');
		this.on('click', nxtbtn, () => {
			next++;
			if (next > total) next = 0;
			prev = next - 1;
			if (prev < 0) prev = total;
			nextSlide();
		});

		this.on('click', prevbtn, () => {
			next--;
			if (next < 0) next = total;
			prev = next + 1;
			if (prev > total) prev = 0;
			nextSlide();
		});
	},

	/**
	 * Make element visible with slideDown animation.
	 *
	 * @since 1.3.5
	 *
	 * @param {object} elem
	 * @param {int} time
	 */
	slideDown(elems, time) {

		let speed = time || 400;
		this.eachElement(elems, elem => {
			elem.style.cssText = 'display: block; overflow: auto';
			const height = Math.max(elem.offsetHeight, elem.scrollHeight);
			elem.style.cssText = 'display: block; overflow: hidden; height: 0;';
			this.animate(elem, 0, height, speed, (elem, value) => { elem.style.height = value + 'px' }, elem => { elem.style.cssText = 'display: block'; } );
		} );
	},

	/**
	 * Make element visible with slideDown animation.
	 *
	 * @since 1.3.5
	 *
	 * @param {object} elem
	 * @param {int} time
	 */
	slideUp(elems, time) {

		let speed = time || 400;
		this.eachElement(elems, elem => {
			elem.style.cssText = 'display: block; overflow: auto';
			const height = Math.max(elem.offsetHeight, elem.scrollHeight);
			elem.style.cssText = 'display: block; overflow: hidden; height: 0;';
			this.animate(elem, height, 0, speed, (elem, value) => { elem.style.height = value + 'px' }, elem => { elem.style.cssText = 'display: none'; } );
		} );
	}
}

export default library;
