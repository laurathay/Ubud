/**
 * Responsive videos
 *
 * Make iframe videos responsive on post/page content.
 */
class PolyFill {

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		this.objectFit();
		this.closest();
		this.objectAssign();
		this.arrayFind();
	}

	/**
	 * Object Fit polyfill.
	 * 
	 * @since 1.3.5
	 */
	objectFit() {

		let selectors = [
			'.entry-thumbnail',
			'.dp-thumbnail',
			'.gallery-icon a',
			'.header-image',
			'.has-featured-img .thumb-wrapper'
		];

		if (false === 'objectFit' in document.documentElement.style) {
			const elems = document.querySelectorAll(selectors.join(','));
			const elemsArr = Array.prototype.slice.call(elems);
			elemsArr.forEach(elem => {
				const image  = elem.getElementsByTagName('img');
				const imgsrc = image.length ? image[0].src : '';
				if (!imgsrc) return;
				image[0].style.visibility     = 'hidden';
				elem.style.backgroundImage = 'url(' + imgsrc + ')';
				elem.style.backgroundSize  = 'cover';
				
				// Position for display posts will be handled by css.
				if (! elem.classList.contains('dp-thumbnail')) {
					elem.style.backgroundPosition = 'center center';
				}
			});
		}
	}

	/**
	 * closest polyfill.
	 * 
	 * @since 1.3.5
	 */
	closest() {
		if (!Element.prototype.matches) {
			Element.prototype.matches = Element.prototype.msMatchesSelector || 
										Element.prototype.webkitMatchesSelector;
		}
		
		if (!Element.prototype.closest) {
			Element.prototype.closest = function(s) {
				var el = this;
		  
				do {
					if (el.matches(s)) return el;
					el = el.parentElement || el.parentNode;
				} while (el !== null && el.nodeType === 1);
				return null;
			};
		}
	}

	/**
	 * Object Assign polyfill.
	 *
	 * @since 1.0.0
	 */
	objectAssign() {
		if (typeof Object.assign !== 'function') {
			// Must be writable: true, enumerable: false, configurable: true
			Object.defineProperty(Object, "assign", {
				value: function assign(target, varArgs) { // .length of function is 2
					'use strict';
					if (target === null || target === undefined) {
						throw new TypeError('Cannot convert undefined or null to object');
					}
					var to = Object(target);
					for (var index = 1; index < arguments.length; index++) {
						var nextSource = arguments[index];
			
						if (nextSource !== null && nextSource !== undefined) { 
							for (var nextKey in nextSource) {
								// Avoid bugs when hasOwnProperty is shadowed
								if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
									to[nextKey] = nextSource[nextKey];
								}
							}
						}
					}
					return to;
				},
				writable: true,
				configurable: true
			});
		}
	}

	/**
	 * Array Find polyfill.
	 *
	 * @since 1.0.0
	 */
	arrayFind() {
		if (!Array.prototype.find) {
			Object.defineProperty(Array.prototype, 'find', {
				value: function(predicate) {
					// 1. Let O be ? ToObject(this value).
					if (this == null) {
						throw TypeError('"this" is null or not defined');
					}
			
					var o = Object(this);
			
					// 2. Let len be ? ToLength(? Get(O, "length")).
					var len = o.length >>> 0;
			
					// 3. If IsCallable(predicate) is false, throw a TypeError exception.
					if (typeof predicate !== 'function') {
						throw TypeError('predicate must be a function');
					}
			
					// 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
					var thisArg = arguments[1];
			
					// 5. Let k be 0.
					var k = 0;
			
					// 6. Repeat, while k < len
					while (k < len) {
						// a. Let Pk be ! ToString(k).
						// b. Let kValue be ? Get(O, Pk).
						// c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
						// d. If testResult is true, return kValue.
						var kValue = o[k];
						if (predicate.call(thisArg, kValue, k, o)) {
							return kValue;
						}
						// e. Increase k by 1.
						k++;
					}
			
					// 7. Return undefined.
					return undefined;
				},
				configurable: true,
				writable: true
			});
		}
	}
}

export default PolyFill;