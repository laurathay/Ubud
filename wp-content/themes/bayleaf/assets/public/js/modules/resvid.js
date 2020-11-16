import conf from './config';
import lib from './library';

/**
 * Responsive videos
 *
 * Make iframe videos responsive on post/page content.
 */
class ResponsiveVideos {

	/**
	 * Holds video iframes from the post content.
	 */
	frames;

	/**
	 * The constructor function.
	 *
	 * @since 1.3.5
	 */
	constructor() {

		const elem = conf.elems;
		this.main = lib.get(elem.main);
		this.content = lib.get(elem.eContent, this.main);

		if(! lib.hasClass(lib.body, conf.cls.single)) return;
		if(0 === this.content.length) return;
		this.frames = lib.get(conf.vidsel.join(','), this.content);
		if(0 === this.frames.length) return;

		this.excludeAlreadyProcessed();
		this.makeResponsive();
	}

	/**
	 * Exclude iFrames which may be already processed by some other script.
	 * 
	 * @since 1.3.5
	 */
	excludeAlreadyProcessed() {

		/* We have made an assumption that if an iFrame is absolutely positioned,
		it is probably already processed.*/
		let finalFrames = this.frames.filter( frame => {
			const framePosition = frame.currentStyle ? frame.currentStyle.position :
			getComputedStyle(frame, null).position;
			return 'absolute' !== framePosition;
		} );
		this.frames = finalFrames;
	}

	/**
	 * Making videos responsice
	 */
	makeResponsive() {

		this.frames.forEach(frame => {
			let wrapper = document.createElement('div');
			wrapper.className = 'video-container';
			frame.parentNode.insertBefore(wrapper, frame);
			wrapper.appendChild(frame);
		});
	}
}

export default ResponsiveVideos;