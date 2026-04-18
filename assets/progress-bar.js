/**
 * Reading Progress Indicator – front-end logic.
 *
 * Pure vanilla JavaScript, no jQuery dependency.
 *
 * @package ReadingProgressIndicator
 */

(function () {
	'use strict';

	/** @type {Object} Settings injected via wp_localize_script */
	var cfg = window.rpiSettings || {};

	/** @type {boolean} Whether we are inside the admin preview */
	var isPreview = Boolean(cfg.isPreview);

	/** @type {HTMLElement|null} */
	var bar = null;

	/** @type {HTMLElement|null} */
	var badge = null;

	/** @type {HTMLElement|null} */
	var wrap = null;

	/** @type {boolean} */
	var isHorizontal = (cfg.position === 'top' || cfg.position === 'bottom');

	/** @type {number} Previous progress value for pulse detection */
	var prevMilestone = 0;

	/** @type {boolean} Whether the congrats overlay has been shown this session */
	var congratsShown = false;

	/** @type {HTMLElement|null} Cached congrats overlay element */
	var congratsOverlay = null;

	/** @type {number|null} Auto-dismiss timer ID */
	var congratsTimer = null;

	/**
	 * Calculate scroll progress as a 0-100 number.
	 *
	 * @returns {number} Scroll percentage.
	 */
	function getScrollProgress() {
		var scrollTop = window.scrollY || document.documentElement.scrollTop;
		var docHeight = document.documentElement.scrollHeight - window.innerHeight;

		if (docHeight <= 0) {
			return 100;
		}

		return Math.min((scrollTop / docHeight) * 100, 100);
	}

	/**
	 * Apply progress value to the bar element.
	 *
	 * @param {number} progress 0–100 value.
	 */
	function applyProgress(progress) {
		if (!bar) return;

		if (isHorizontal) {
			bar.style.width  = progress + '%';
			bar.style.height = '100%';
		} else {
			bar.style.height = progress + '%';
			bar.style.width  = '100%';
		}

		if (wrap) {
			wrap.setAttribute('aria-valuenow', Math.round(progress));
		}

		/* Pulse animation at every 25% milestone */
		if (cfg.animation === 'pulse') {
			var currentMilestone = Math.floor(progress / 25);
			if (currentMilestone > prevMilestone && currentMilestone > 0) {
				bar.classList.remove('rpi-pulse-tick');
				void bar.offsetWidth;
				bar.classList.add('rpi-pulse-tick');
			}
			prevMilestone = currentMilestone;
		}

		/* Percentage badge */
		if (badge) {
			badge.textContent = Math.round(progress) + '%';
			badge.style.opacity = progress > 5 ? '1' : '0';
		}

		/* Congratulations overlay at 100% */
		if (parseInt(cfg.showCongrats, 10) && progress >= 99.5 && !congratsShown) {
			showCongrats();
		}
	}

	/* ── Congrats overlay ──────────────────────────────────── */

	/** Confetti color palette */
	var CONFETTI_COLORS = [
		'#3B82F6', '#8B5CF6', '#EC4899', '#F59E0B',
		'#10B981', '#EF4444', '#6366F1', '#14B8A6'
	];

	/**
	 * Build and inject the congratulations overlay into the DOM.
	 */
	function buildCongratsOverlay() {
		if (congratsOverlay) return;

		var overlay = document.createElement('div');
		overlay.id = 'rpi-congrats-overlay';
		overlay.setAttribute('aria-live', 'polite');

		/* Confetti particles */
		var confettiWrap = document.createElement('div');
		confettiWrap.className = 'rpi-confetti-wrap';

		for (var i = 0; i < 60; i++) {
			var p = document.createElement('span');
			p.className = 'rpi-confetti';

			var color = CONFETTI_COLORS[i % CONFETTI_COLORS.length];
			var left  = Math.random() * 100;
			var delay = Math.random() * 2.5;
			var dur   = 2.5 + Math.random() * 2;
			var size  = 6 + Math.random() * 6;
			var rot   = Math.floor(Math.random() * 360);
			var shape = Math.random() > 0.5 ? '50%' : (Math.random() > 0.5 ? '2px' : '0');

			p.style.cssText =
				'left:' + left + '%;' +
				'animation-delay:' + delay + 's;' +
				'animation-duration:' + dur + 's;' +
				'width:' + size + 'px;' +
				'height:' + size + 'px;' +
				'background:' + color + ';' +
				'border-radius:' + shape + ';' +
				'transform:rotate(' + rot + 'deg);';

			confettiWrap.appendChild(p);
		}
		overlay.appendChild(confettiWrap);

		/* Center card */
		var card = document.createElement('div');
		card.className = 'rpi-congrats-card';

		card.innerHTML =
			'<div class="rpi-congrats-icon">' +
				'<svg viewBox="0 0 64 64" width="64" height="64" fill="none" xmlns="http://www.w3.org/2000/svg">' +
					'<circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" opacity="0.15"/>' +
					'<circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" class="rpi-check-circle"/>' +
					'<path d="M20 33l8 8 16-16" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" class="rpi-check-mark"/>' +
				'</svg>' +
			'</div>' +
			'<h2 class="rpi-congrats-title">Congratulations!</h2>' +
			'<p class="rpi-congrats-sub">You\'ve finished reading this article.</p>' +
			'<button type="button" class="rpi-congrats-close" aria-label="Close">&times;</button>';

		overlay.appendChild(card);

		document.body.appendChild(overlay);
		congratsOverlay = overlay;

		/* Close handlers */
		overlay.addEventListener('click', function (e) {
			if (e.target === overlay || e.target.closest('.rpi-congrats-close')) {
				hideCongrats();
			}
		});

		document.addEventListener('keydown', function onEsc(e) {
			if (e.key === 'Escape' && congratsOverlay && congratsOverlay.classList.contains('rpi-congrats-visible')) {
				hideCongrats();
				document.removeEventListener('keydown', onEsc);
			}
		});
	}

	/**
	 * Show the congratulations overlay with entrance animation.
	 */
	function showCongrats() {
		congratsShown = true;
		buildCongratsOverlay();

		void congratsOverlay.offsetWidth;
		congratsOverlay.classList.add('rpi-congrats-visible');

		congratsTimer = setTimeout(hideCongrats, 6000);
	}

	/**
	 * Hide the congratulations overlay with exit animation.
	 */
	function hideCongrats() {
		if (!congratsOverlay) return;
		if (congratsTimer) {
			clearTimeout(congratsTimer);
			congratsTimer = null;
		}

		congratsOverlay.classList.remove('rpi-congrats-visible');
		congratsOverlay.classList.add('rpi-congrats-hiding');

		setTimeout(function () {
			if (congratsOverlay) {
				congratsOverlay.classList.remove('rpi-congrats-hiding');
			}
		}, 500);
	}

	/* ── Scroll handler ────────────────────────────────────── */

	var ticking = false;

	/**
	 * Scroll and resize handler (throttled via rAF).
	 */
	function onScroll() {
		if (ticking) return;
		ticking = true;

		window.requestAnimationFrame(function () {
			applyProgress(getScrollProgress());
			ticking = false;
		});
	}

	/**
	 * Initialise the progress bar on the front end.
	 */
	function init() {
		if (isPreview) return;

		bar   = document.getElementById('rpi-progress-bar');
		wrap  = document.getElementById('rpi-progress-wrap');
		badge = document.getElementById('rpi-percentage-badge');

		if (!bar || !wrap) return;

		bar.classList.add('rpi-style-' + (cfg.style || 'linear'));

		if (cfg.animation === 'color_shift') {
			bar.classList.add('rpi-anim-color-shift');
		}

		if (cfg.animation === 'pulse') {
			bar.classList.add('rpi-anim-pulse');
		}

		applyProgress(getScrollProgress());

		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll, { passive: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
