<?php
/**
 * Admin settings page template.
 *
 * @package ReadingProgressIndicator
 */

defined( 'ABSPATH' ) || exit;

$settings = rpi_get_settings();
?>
<div class="wrap rpi-settings-wrap">
	<h1><?php esc_html_e( 'Reading Progress Indicator', 'reading-progress-indicator' ); ?></h1>
	<p class="rpi-subtitle"><?php esc_html_e( 'Configure how the progress bar looks and behaves on your site.', 'reading-progress-indicator' ); ?></p>

	<div class="rpi-layout">
		<div class="rpi-settings-col">
			<form method="post" action="options.php" id="rpi-settings-form">
				<?php
				settings_fields( 'rpi_settings_group' );
				do_settings_sections( 'reading-progress' );
				submit_button( __( 'Save Settings', 'reading-progress-indicator' ) );
				?>
			</form>
		</div>

		<div class="rpi-preview-col">
			<div class="rpi-preview-card">
				<h3><?php esc_html_e( 'Live Preview', 'reading-progress-indicator' ); ?></h3>
				<div id="rpi-preview-container">
					<div id="rpi-preview-wrap" role="progressbar">
						<div id="rpi-preview-bar"></div>
					</div>
					<div id="rpi-preview-badge" style="display:none;">0%</div>

					<!-- Mini congrats overlay inside preview -->
					<div id="rpi-mini-congrats">
						<div class="rpi-mini-confetti-wrap"></div>
						<div class="rpi-mini-congrats-inner">
							<svg viewBox="0 0 64 64" width="36" height="36" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="32" cy="32" r="30" stroke="#10B981" stroke-width="3" opacity="0.15"/>
								<circle cx="32" cy="32" r="30" stroke="#10B981" stroke-width="3" class="rpi-mini-check-circle"/>
								<path d="M20 33l8 8 16-16" stroke="#10B981" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" class="rpi-mini-check-mark"/>
							</svg>
							<span class="rpi-mini-congrats-text">Congratulations!</span>
							<span class="rpi-mini-congrats-sub">Article finished</span>
						</div>
					</div>

					<div class="rpi-preview-content">
						<div class="rpi-preview-title"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-80"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-60"></div>
						<div class="rpi-preview-spacer"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-90"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-70"></div>
						<div class="rpi-preview-spacer"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-80"></div>
						<div class="rpi-preview-line rpi-line-full"></div>
						<div class="rpi-preview-line rpi-line-50"></div>
					</div>
				</div>
				<div class="rpi-preview-slider-wrap">
					<label for="rpi-preview-slider"><?php esc_html_e( 'Simulate scroll', 'reading-progress-indicator' ); ?></label>
					<input type="range" id="rpi-preview-slider" min="0" max="100" value="35" />
					<span id="rpi-preview-slider-value">35%</span>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
(function () {
	'use strict';

	var form, bar, wrap, badge, slider, sliderLabel, miniCongrats, miniConfettiWrap;
	var miniCongratsVisible = false;

	var CONFETTI_COLORS = ['#3B82F6','#8B5CF6','#EC4899','#F59E0B','#10B981','#EF4444','#6366F1','#14B8A6'];

	/**
	 * Read a form value by name attribute.
	 * @param {string} name Setting key.
	 * @returns {string} Current value.
	 */
	function val(name) {
		var el = form.querySelector('[name="rpi_settings[' + name + ']"]');
		if (!el) return '';
		return el.value;
	}

	/**
	 * Read a checked radio value.
	 * @param {string} name Setting key.
	 * @returns {string} Checked radio value.
	 */
	function radioVal(name) {
		var el = form.querySelector('[name="rpi_settings[' + name + ']"]:checked');
		return el ? el.value : '';
	}

	/**
	 * Read a checkbox state.
	 * @param {string} name Setting key.
	 * @returns {boolean}
	 */
	function isChecked(name) {
		var el = form.querySelector('[name="rpi_settings[' + name + ']"]');
		return el ? el.checked : false;
	}

	/**
	 * Get color value accounting for wpColorPicker wrapper.
	 * @param {string} key Setting key.
	 * @returns {string} Hex color.
	 */
	function colorVal(key) {
		var el = form.querySelector('[name="rpi_settings[' + key + ']"]');
		if (!el) return '#3B82F6';
		return el.value || '#3B82F6';
	}

	/**
	 * Populate confetti particles inside the mini preview overlay.
	 */
	function buildMiniConfetti() {
		if (!miniConfettiWrap || miniConfettiWrap.children.length > 0) return;

		for (var i = 0; i < 24; i++) {
			var p = document.createElement('span');
			p.className = 'rpi-mini-confetti';

			var color = CONFETTI_COLORS[i % CONFETTI_COLORS.length];
			var left  = Math.random() * 100;
			var delay = Math.random() * 1.5;
			var dur   = 1.8 + Math.random() * 1.2;
			var size  = 4 + Math.random() * 4;
			var shape = Math.random() > 0.5 ? '50%' : '1px';

			p.style.cssText =
				'left:' + left + '%;' +
				'animation-delay:' + delay + 's;' +
				'animation-duration:' + dur + 's;' +
				'width:' + size + 'px;' +
				'height:' + size + 'px;' +
				'background:' + color + ';' +
				'border-radius:' + shape + ';';

			miniConfettiWrap.appendChild(p);
		}
	}

	/**
	 * Show or hide the mini congrats overlay based on progress and setting.
	 * @param {number} pct Current progress 0-100.
	 */
	function toggleMiniCongrats(pct) {
		if (!miniCongrats) return;

		var showCongrats = isChecked('show_congrats');
		var shouldShow   = showCongrats && pct >= 100;

		if (shouldShow && !miniCongratsVisible) {
			buildMiniConfetti();
			miniCongrats.classList.add('rpi-mini-congrats-visible');
			miniCongratsVisible = true;
		} else if (!shouldShow && miniCongratsVisible) {
			miniCongrats.classList.remove('rpi-mini-congrats-visible');
			miniCongratsVisible = false;
		}
	}

	/**
	 * Read all current form values and refresh the entire preview.
	 */
	function updatePreview() {
		if (!form || !bar || !wrap) return;

		var position  = val('position');
		var style     = radioVal('style');
		var color     = colorVal('color');
		var gradEnd   = colorVal('gradient_end_color');
		var thickness = val('thickness');
		var radius    = val('border_radius');
		var opacity   = val('opacity');
		var animation = val('animation');
		var showPct   = isChecked('show_percentage');
		var isH       = (position === 'top' || position === 'bottom');

		wrap.style.removeProperty('--rpi-height');
		wrap.style.removeProperty('--rpi-width');
		wrap.removeAttribute('class');
		wrap.classList.add('rpi-position-' + position);

		wrap.style.setProperty('--rpi-color', color);
		wrap.style.setProperty('--rpi-gradient-end', gradEnd);
		wrap.style.setProperty('--rpi-radius', radius + 'px');
		wrap.style.setProperty('--rpi-opacity', opacity);

		if (isH) {
			wrap.style.setProperty('--rpi-height', thickness + 'px');
		} else {
			wrap.style.setProperty('--rpi-width', thickness + 'px');
		}

		bar.removeAttribute('class');
		bar.classList.add('rpi-style-' + style);
		if (animation === 'color_shift') bar.classList.add('rpi-anim-color-shift');
		if (animation === 'pulse')       bar.classList.add('rpi-anim-pulse');

		if (showPct) {
			badge.style.display = '';
			badge.removeAttribute('class');
			badge.classList.add('rpi-badge-' + position);
		} else {
			badge.style.display = 'none';
		}

		applyProgress(parseFloat(slider.value));
	}

	/**
	 * Set the preview bar to a specific progress percentage.
	 * @param {number} pct Value between 0 and 100.
	 */
	function applyProgress(pct) {
		if (!bar || !wrap) return;

		var classes = wrap.className;
		var isH = (classes.indexOf('position-top') !== -1 || classes.indexOf('position-bottom') !== -1);

		if (isH) {
			bar.style.width  = pct + '%';
			bar.style.height = '100%';
		} else {
			bar.style.height = pct + '%';
			bar.style.width  = '100%';
		}

		if (badge) {
			badge.textContent = Math.round(pct) + '%';
			badge.style.opacity = pct > 5 ? '1' : '0';
		}

		sliderLabel.textContent = Math.round(pct) + '%';

		toggleMiniCongrats(pct);
	}

	/**
	 * Bind all form change events to trigger live preview update.
	 */
	function bindEvents() {
		form.querySelectorAll('select, input[type="radio"]').forEach(function (el) {
			el.addEventListener('change', updatePreview);
		});

		form.querySelectorAll('input[type="checkbox"]').forEach(function (el) {
			el.addEventListener('change', updatePreview);
		});

		form.querySelectorAll('.rpi-range').forEach(function (el) {
			el.addEventListener('input', function () {
				var display = el.closest('.rpi-range-wrap').querySelector('.rpi-range-value');
				if (display) {
					var suffix = el.name.indexOf('opacity') !== -1 ? '' : 'px';
					display.textContent = el.value + suffix;
				}
				updatePreview();
			});
		});

		slider.addEventListener('input', function () {
			applyProgress(parseFloat(slider.value));
		});

		if (typeof jQuery !== 'undefined' && jQuery.fn.wpColorPicker) {
			jQuery('.rpi-color-picker').wpColorPicker({
				change: function () { setTimeout(updatePreview, 20); },
				clear:  function () { setTimeout(updatePreview, 20); }
			});
		}
	}

	/**
	 * Initialize the preview system after DOM is ready.
	 */
	function init() {
		form             = document.getElementById('rpi-settings-form');
		bar              = document.getElementById('rpi-preview-bar');
		wrap             = document.getElementById('rpi-preview-wrap');
		badge            = document.getElementById('rpi-preview-badge');
		slider           = document.getElementById('rpi-preview-slider');
		sliderLabel      = document.getElementById('rpi-preview-slider-value');
		miniCongrats     = document.getElementById('rpi-mini-congrats');
		miniConfettiWrap = miniCongrats ? miniCongrats.querySelector('.rpi-mini-confetti-wrap') : null;

		if (!form || !bar || !wrap || !slider) return;

		bindEvents();
		updatePreview();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
</script>
