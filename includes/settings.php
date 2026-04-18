<?php
/**
 * Settings API registration and sanitization.
 *
 * @package ReadingProgressIndicator
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the plugin setting and admin menu page.
 *
 * @return void
 */
function rpi_register_settings() {
	register_setting(
		'rpi_settings_group',
		'rpi_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'rpi_sanitize_settings',
			'default'           => rpi_get_defaults(),
		)
	);

	/* ── General Section ──────────────────────────────────── */
	add_settings_section(
		'rpi_section_general',
		__( 'General', 'reading-progress-indicator' ),
		'__return_null',
		'reading-progress'
	);

	add_settings_field(
		'rpi_enabled',
		__( 'Enable Plugin', 'reading-progress-indicator' ),
		'rpi_field_toggle',
		'reading-progress',
		'rpi_section_general',
		array( 'key' => 'enabled', 'label' => __( 'Show reading progress bar on the front end', 'reading-progress-indicator' ) )
	);

	add_settings_field(
		'rpi_position',
		__( 'Bar Position', 'reading-progress-indicator' ),
		'rpi_field_select',
		'reading-progress',
		'rpi_section_general',
		array(
			'key'     => 'position',
			'options' => array(
				'top'    => __( 'Top', 'reading-progress-indicator' ),
				'bottom' => __( 'Bottom', 'reading-progress-indicator' ),
				'left'   => __( 'Left', 'reading-progress-indicator' ),
				'right'  => __( 'Right', 'reading-progress-indicator' ),
			),
		)
	);

	/* ── Appearance Section ───────────────────────────────── */
	add_settings_section(
		'rpi_section_appearance',
		__( 'Appearance', 'reading-progress-indicator' ),
		'__return_null',
		'reading-progress'
	);

	add_settings_field(
		'rpi_style',
		__( 'Bar Style', 'reading-progress-indicator' ),
		'rpi_field_radio',
		'reading-progress',
		'rpi_section_appearance',
		array(
			'key'     => 'style',
			'options' => array(
				'linear'   => __( 'Linear', 'reading-progress-indicator' ),
				'gradient' => __( 'Gradient', 'reading-progress-indicator' ),
				'neon'     => __( 'Neon Glow', 'reading-progress-indicator' ),
			),
		)
	);

	add_settings_field(
		'rpi_color',
		__( 'Bar Color', 'reading-progress-indicator' ),
		'rpi_field_color',
		'reading-progress',
		'rpi_section_appearance',
		array( 'key' => 'color' )
	);

	add_settings_field(
		'rpi_gradient_end_color',
		__( 'Gradient End Color', 'reading-progress-indicator' ),
		'rpi_field_color',
		'reading-progress',
		'rpi_section_appearance',
		array( 'key' => 'gradient_end_color' )
	);

	add_settings_field(
		'rpi_thickness',
		__( 'Bar Thickness', 'reading-progress-indicator' ),
		'rpi_field_range',
		'reading-progress',
		'rpi_section_appearance',
		array( 'key' => 'thickness', 'min' => 2, 'max' => 20, 'step' => 1, 'unit' => 'px' )
	);

	add_settings_field(
		'rpi_border_radius',
		__( 'Border Radius', 'reading-progress-indicator' ),
		'rpi_field_range',
		'reading-progress',
		'rpi_section_appearance',
		array( 'key' => 'border_radius', 'min' => 0, 'max' => 20, 'step' => 1, 'unit' => 'px' )
	);

	add_settings_field(
		'rpi_opacity',
		__( 'Opacity', 'reading-progress-indicator' ),
		'rpi_field_range',
		'reading-progress',
		'rpi_section_appearance',
		array( 'key' => 'opacity', 'min' => 0.1, 'max' => 1.0, 'step' => 0.05, 'unit' => '' )
	);

	/* ── Animation Section ────────────────────────────────── */
	add_settings_section(
		'rpi_section_animation',
		__( 'Animation', 'reading-progress-indicator' ),
		'__return_null',
		'reading-progress'
	);

	add_settings_field(
		'rpi_animation',
		__( 'Animation Type', 'reading-progress-indicator' ),
		'rpi_field_select',
		'reading-progress',
		'rpi_section_animation',
		array(
			'key'     => 'animation',
			'options' => array(
				'smooth'      => __( 'Smooth', 'reading-progress-indicator' ),
				'pulse'       => __( 'Pulse', 'reading-progress-indicator' ),
				'color_shift' => __( 'Color Shift', 'reading-progress-indicator' ),
			),
		)
	);

	add_settings_field(
		'rpi_show_percentage',
		__( 'Show Percentage', 'reading-progress-indicator' ),
		'rpi_field_toggle',
		'reading-progress',
		'rpi_section_animation',
		array( 'key' => 'show_percentage', 'label' => __( 'Display a floating percentage badge', 'reading-progress-indicator' ) )
	);

	add_settings_field(
		'rpi_show_congrats',
		__( 'Completion Celebration', 'reading-progress-indicator' ),
		'rpi_field_toggle',
		'reading-progress',
		'rpi_section_animation',
		array( 'key' => 'show_congrats', 'label' => __( 'Show a congratulations animation when the reader finishes the article', 'reading-progress-indicator' ) )
	);

	/* ── Display Section ──────────────────────────────────── */
	add_settings_section(
		'rpi_section_display',
		__( 'Display Conditions', 'reading-progress-indicator' ),
		'__return_null',
		'reading-progress'
	);

	add_settings_field(
		'rpi_display_on',
		__( 'Show On', 'reading-progress-indicator' ),
		'rpi_field_display_on',
		'reading-progress',
		'rpi_section_display'
	);
}
add_action( 'admin_init', 'rpi_register_settings' );

/**
 * Register the admin menu page under Settings.
 *
 * @return void
 */
function rpi_add_settings_page() {
	add_options_page(
		__( 'Reading Progress', 'reading-progress-indicator' ),
		__( 'Reading Progress', 'reading-progress-indicator' ),
		'manage_options',
		'reading-progress',
		'rpi_render_settings_page'
	);
}
add_action( 'admin_menu', 'rpi_add_settings_page' );

/**
 * Render the settings page (delegated to template).
 *
 * @return void
 */
function rpi_render_settings_page() {
	require_once RPI_PLUGIN_DIR . 'admin/settings-page.php';
}

/* ────────────────────────────────────────────────────────────
 * Field renderers
 * ──────────────────────────────────────────────────────────── */

/**
 * Render a toggle (checkbox) field.
 *
 * @param array $args Field arguments containing 'key' and 'label'.
 * @return void
 */
function rpi_field_toggle( $args ) {
	$settings = rpi_get_settings();
	$key      = $args['key'];
	$checked  = ! empty( $settings[ $key ] ) ? 'checked' : '';
	printf(
		'<label class="rpi-toggle"><input type="checkbox" name="rpi_settings[%s]" value="1" %s /><span class="rpi-toggle-slider"></span> %s</label>',
		esc_attr( $key ),
		esc_attr( $checked ),
		esc_html( $args['label'] )
	);
}

/**
 * Render a select dropdown field.
 *
 * @param array $args Field arguments containing 'key' and 'options'.
 * @return void
 */
function rpi_field_select( $args ) {
	$settings = rpi_get_settings();
	$key      = $args['key'];
	$current  = $settings[ $key ];

	printf( '<select name="rpi_settings[%s]" id="rpi_%s">', esc_attr( $key ), esc_attr( $key ) );
	foreach ( $args['options'] as $value => $label ) {
		printf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $value ),
			selected( $current, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select>';
}

/**
 * Render a radio button group.
 *
 * @param array $args Field arguments containing 'key' and 'options'.
 * @return void
 */
function rpi_field_radio( $args ) {
	$settings = rpi_get_settings();
	$key      = $args['key'];
	$current  = $settings[ $key ];

	echo '<fieldset class="rpi-radio-group">';
	foreach ( $args['options'] as $value => $label ) {
		printf(
			'<label class="rpi-radio-label"><input type="radio" name="rpi_settings[%s]" value="%s" %s /> %s</label>',
			esc_attr( $key ),
			esc_attr( $value ),
			checked( $current, $value, false ),
			esc_html( $label )
		);
	}
	echo '</fieldset>';
}

/**
 * Render a WordPress color picker field.
 *
 * @param array $args Field arguments containing 'key'.
 * @return void
 */
function rpi_field_color( $args ) {
	$settings = rpi_get_settings();
	$key      = $args['key'];
	printf(
		'<input type="text" name="rpi_settings[%s]" value="%s" class="rpi-color-picker" data-default-color="%s" />',
		esc_attr( $key ),
		esc_attr( $settings[ $key ] ),
		esc_attr( rpi_get_defaults()[ $key ] )
	);
}

/**
 * Render a range slider field.
 *
 * @param array $args Field arguments with 'key', 'min', 'max', 'step', 'unit'.
 * @return void
 */
function rpi_field_range( $args ) {
	$settings = rpi_get_settings();
	$key      = $args['key'];
	$value    = $settings[ $key ];
	printf(
		'<div class="rpi-range-wrap"><input type="range" name="rpi_settings[%s]" min="%s" max="%s" step="%s" value="%s" class="rpi-range" /><span class="rpi-range-value">%s%s</span></div>',
		esc_attr( $key ),
		esc_attr( $args['min'] ),
		esc_attr( $args['max'] ),
		esc_attr( $args['step'] ),
		esc_attr( $value ),
		esc_html( $value ),
		esc_html( $args['unit'] )
	);
}

/**
 * Render the "Display on" checkboxes.
 *
 * @return void
 */
function rpi_field_display_on() {
	$settings   = rpi_get_settings();
	$display_on = (array) $settings['display_on'];

	$post_types = array(
		'all'  => __( 'All Pages', 'reading-progress-indicator' ),
		'post' => __( 'Single Posts', 'reading-progress-indicator' ),
		'page' => __( 'Pages', 'reading-progress-indicator' ),
	);

	$custom_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );
	foreach ( $custom_types as $cpt ) {
		$post_types[ $cpt->name ] = $cpt->labels->singular_name;
	}

	echo '<fieldset class="rpi-checkbox-group">';
	foreach ( $post_types as $value => $label ) {
		printf(
			'<label><input type="checkbox" name="rpi_settings[display_on][]" value="%s" %s /> %s</label><br>',
			esc_attr( $value ),
			checked( in_array( $value, $display_on, true ), true, false ),
			esc_html( $label )
		);
	}
	echo '</fieldset>';
}

/* ────────────────────────────────────────────────────────────
 * Sanitization
 * ──────────────────────────────────────────────────────────── */

/**
 * Sanitize all plugin settings before saving.
 *
 * @param array $input Raw form input.
 * @return array Sanitized settings.
 */
function rpi_sanitize_settings( $input ) {
	$defaults  = rpi_get_defaults();
	$sanitized = array();

	$sanitized['enabled']         = ! empty( $input['enabled'] ) ? 1 : 0;
	$sanitized['show_percentage'] = ! empty( $input['show_percentage'] ) ? 1 : 0;
	$sanitized['show_congrats']   = ! empty( $input['show_congrats'] ) ? 1 : 0;

	$sanitized['position'] = in_array( $input['position'] ?? '', array( 'top', 'bottom', 'left', 'right' ), true )
		? $input['position']
		: $defaults['position'];

	$sanitized['style'] = in_array( $input['style'] ?? '', array( 'linear', 'gradient', 'neon' ), true )
		? $input['style']
		: $defaults['style'];

	$sanitized['animation'] = in_array( $input['animation'] ?? '', array( 'smooth', 'pulse', 'color_shift' ), true )
		? $input['animation']
		: $defaults['animation'];

	$sanitized['color']              = sanitize_hex_color( $input['color'] ?? '' ) ?: $defaults['color'];
	$sanitized['gradient_end_color'] = sanitize_hex_color( $input['gradient_end_color'] ?? '' ) ?: $defaults['gradient_end_color'];

	$sanitized['thickness']     = max( 2, min( 20, intval( $input['thickness'] ?? $defaults['thickness'] ) ) );
	$sanitized['border_radius'] = max( 0, min( 20, intval( $input['border_radius'] ?? $defaults['border_radius'] ) ) );
	$sanitized['opacity']       = max( 0.1, min( 1.0, floatval( $input['opacity'] ?? $defaults['opacity'] ) ) );

	$valid_display = array_merge( array( 'all', 'post', 'page' ), array_keys( get_post_types( array( 'public' => true, '_builtin' => false ) ) ) );
	$sanitized['display_on'] = array();
	if ( ! empty( $input['display_on'] ) && is_array( $input['display_on'] ) ) {
		foreach ( $input['display_on'] as $type ) {
			if ( in_array( $type, $valid_display, true ) ) {
				$sanitized['display_on'][] = $type;
			}
		}
	}
	if ( empty( $sanitized['display_on'] ) ) {
		$sanitized['display_on'] = $defaults['display_on'];
	}

	return $sanitized;
}
