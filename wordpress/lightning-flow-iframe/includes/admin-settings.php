<?php
/**
 * Admin settings for Lightning Flow iFrame.
 *
 * @package IFRAMESFL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TLSFLFI_OPTION_IFRAME_URL', 'tlsflfi_default_iframe_url' );
define( 'TLSFLFI_OPTION_FLOW_NAME', 'tlsflfi_default_flow_name' );
define( 'TLSFLFI_OPTION_END_URL', 'tlsflfi_default_end_url' );
define( 'TLSFLFI_OPTION_BG', 'tlsflfi_default_bg' );
define( 'TLSFLFI_OPTION_PADDING', 'tlsflfi_default_padding' );
define( 'TLSFLFI_OPTION_PRIMARY', 'tlsflfi_default_primary' );
define( 'TLSFLFI_OPTION_PRIMARY_HOVER', 'tlsflfi_default_primary_hover' );
define( 'TLSFLFI_OPTION_SECONDARY', 'tlsflfi_default_secondary' );
define( 'TLSFLFI_OPTION_SCROLL_OFFSET', 'tlsflfi_default_scroll_offset' );

/**
 * Register settings, sections, and fields.
 */
function tlsflfi_register_settings() {
	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_IFRAME_URL,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_FLOW_NAME,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'tlsflfi_sanitize_flow_name',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_END_URL,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_BG,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'tlsflfi_sanitize_bg',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_PADDING,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'tlsflfi_sanitize_padding',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_PRIMARY,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'tlsflfi_sanitize_bg',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_PRIMARY_HOVER,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'tlsflfi_sanitize_bg',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_SECONDARY,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'tlsflfi_sanitize_bg',
			'default'           => '',
		)
	);

	register_setting(
		'tlsflfi_settings',
		TLSFLFI_OPTION_SCROLL_OFFSET,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'tlsflfi_sanitize_scroll_offset',
			'default'           => '',
		)
	);

	add_settings_section(
		'tlsflfi_defaults_section',
		__( 'Default embed values', 'iframe-lightning-flow' ),
		'tlsflfi_render_defaults_section',
		'tlsflfi-settings'
	);

	add_settings_field(
		TLSFLFI_OPTION_IFRAME_URL,
		__( 'Default iFrame URL', 'iframe-lightning-flow' ),
		'tlsflfi_render_iframe_url_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_FLOW_NAME,
		__( 'Default Flow Name', 'iframe-lightning-flow' ),
		'tlsflfi_render_flow_name_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_END_URL,
		__( 'Default End URL', 'iframe-lightning-flow' ),
		'tlsflfi_render_end_url_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_BG,
		__( 'Background color', 'iframe-lightning-flow' ),
		'tlsflfi_render_bg_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_PADDING,
		__( 'Padding', 'iframe-lightning-flow' ),
		'tlsflfi_render_padding_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_PRIMARY,
		__( 'Primary color', 'iframe-lightning-flow' ),
		'tlsflfi_render_primary_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_PRIMARY_HOVER,
		__( 'Primary hover color', 'iframe-lightning-flow' ),
		'tlsflfi_render_primary_hover_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_SECONDARY,
		__( 'Secondary color', 'iframe-lightning-flow' ),
		'tlsflfi_render_secondary_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);

	add_settings_field(
		TLSFLFI_OPTION_SCROLL_OFFSET,
		__( 'Scroll offset', 'iframe-lightning-flow' ),
		'tlsflfi_render_scroll_offset_field',
		'tlsflfi-settings',
		'tlsflfi_defaults_section'
	);
}
add_action( 'admin_init', 'tlsflfi_register_settings' );

/**
 * Add options page under Settings.
 */
function tlsflfi_add_settings_page() {
	add_options_page(
		__( 'Lightning Flow iFrame', 'iframe-lightning-flow' ),
		__( 'Lightning Flow iFrame', 'iframe-lightning-flow' ),
		'manage_options',
		'tlsflfi-settings',
		'tlsflfi_render_settings_page'
	);
}
add_action( 'admin_menu', 'tlsflfi_add_settings_page' );

/**
 * Documentation URL for admin links.
 *
 * @return string
 */
function tlsflfi_get_docs_url() {
	if ( defined( 'TLSFLFI_DOCS_URL' ) ) {
		return TLSFLFI_DOCS_URL;
	}

	return 'https://threelevers.com/support/products/lightning-flow-iframe/wordpress/';
}

/**
 * Add Documentation link on the Plugins list row.
 *
 * @param array  $links Plugin row meta links.
 * @param string $file  Plugin basename.
 * @return array
 */
function tlsflfi_plugin_row_meta( $links, $file ) {
	if ( ! defined( 'TLSFLFI_PLUGIN_FILE' ) || plugin_basename( TLSFLFI_PLUGIN_FILE ) !== $file ) {
		return $links;
	}

	$links[] = sprintf(
		'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
		esc_url( tlsflfi_get_docs_url() ),
		esc_html__( 'Documentation', 'iframe-lightning-flow' )
	);

	return $links;
}
add_filter( 'plugin_row_meta', 'tlsflfi_plugin_row_meta', 10, 2 );

/**
 * Settings section intro.
 */
function tlsflfi_render_defaults_section() {
	echo '<p>';
	printf(
		wp_kses(
			/* translators: %s: plugin documentation URL */
			__( 'See the <a href="%s" target="_blank" rel="noopener noreferrer">plugin documentation</a> for shortcode examples, inputvars, Salesforce setup, and legacy vs embed mode.', 'iframe-lightning-flow' ),
			array(
				'a' => array(
					'href'   => array(),
					'target' => array(),
					'rel'    => array(),
				),
			)
		),
		esc_url( tlsflfi_get_docs_url() )
	);
	echo '</p>';
	echo '<p>';
	esc_html_e(
		'Configure defaults so pages can use the bare shortcode [Lightning-Flow-iFrame]. Shortcode attributes always override these values. inputvars is optional—omit it when your flow needs no URL inputs.',
		'iframe-lightning-flow'
	);
	echo '</p>';
	echo '<p><strong>';
	esc_html_e(
		'Important: Setting Default Flow Name enables FlowIframeEmbed mode for every shortcode that does not specify its own flow attribute. Leave it blank to preserve legacy behavior for existing sites.',
		'iframe-lightning-flow'
	);
	echo '</strong></p>';
}

/**
 * Default iFrame URL field.
 */
function tlsflfi_render_iframe_url_field() {
	$value = get_option( TLSFLFI_OPTION_IFRAME_URL, '' );
	printf(
		'<input type="url" class="large-text" name="%1$s" id="%1$s" value="%2$s" placeholder="https://your-site.force.com/site-prefix/FlowIframeEmbed" />',
		esc_attr( TLSFLFI_OPTION_IFRAME_URL ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Full Salesforce Site URL to the FlowIframeEmbed Visualforce page, without query parameters. Example: https://your-site.force.com/site-prefix/FlowIframeEmbed. Used when a shortcode does not specify iframeurl or embedurl. In legacy mode (no default flow), this replaces the built-in demo URL. Use HTTPS in production.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Default Flow Name field.
 */
function tlsflfi_render_flow_name_field() {
	$value = get_option( TLSFLFI_OPTION_FLOW_NAME, '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" placeholder="Check_In_Dispatch" />',
		esc_attr( TLSFLFI_OPTION_FLOW_NAME ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'The Salesforce Flow API Developer Name (not the label), for example Check_In_Dispatch. When set, shortcodes without a flow attribute use this value and run in embed mode (FlowIframeEmbed). Leave blank to keep legacy behavior for shortcodes that do not specify flow. The flow must be activated and guest-accessible on your Site.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Default End URL field.
 */
function tlsflfi_render_end_url_field() {
	$value = get_option( TLSFLFI_OPTION_END_URL, '' );
	printf(
		'<input type="url" class="large-text" name="%1$s" id="%1$s" value="%2$s" placeholder="https://yoursite.com/thanks" />',
		esc_attr( TLSFLFI_OPTION_END_URL ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Absolute https URL on your public site. When the flow reaches FINISHED, the parent browser tab navigates here (sent as endUrl on the iframe URL). Used when a shortcode does not specify endurl. Optional—leave blank for no redirect.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Background color field. Applied to the iframe page body, not the flow.
 */
function tlsflfi_render_bg_field() {
	$value = get_option( TLSFLFI_OPTION_BG, '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" placeholder="F5F3F0" />',
		esc_attr( TLSFLFI_OPTION_BG ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Optional color for the iframe page body. Examples: F5F3F0, #ffffff, white, or transparent. Leave blank to keep the page default. This does not restyle the flow.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Padding field. Applied to the iframe page body.
 */
function tlsflfi_render_padding_field() {
	$value = get_option( TLSFLFI_OPTION_PADDING, '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" placeholder="0" />',
		esc_attr( TLSFLFI_OPTION_PADDING ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Optional space around the flow on the iframe page body. Use 0, 16, 1rem, or 1rem 2rem. A bare number is pixels. Leave blank to keep the page default.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Primary button color. Next and Finish.
 */
function tlsflfi_render_primary_field() {
	$value = get_option( TLSFLFI_OPTION_PRIMARY, '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" placeholder="C75B39" />',
		esc_attr( TLSFLFI_OPTION_PRIMARY ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Optional color for Next, Finish, and other primary buttons. Example: C75B39. Leave blank to keep rust.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Primary button hover color.
 */
function tlsflfi_render_primary_hover_field() {
	$value = get_option( TLSFLFI_OPTION_PRIMARY_HOVER, '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" placeholder="A84A2E" />',
		esc_attr( TLSFLFI_OPTION_PRIMARY_HOVER ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Optional hover color for primary buttons. Leave blank and a darker shade of the primary color is used.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Secondary button color. Previous.
 */
function tlsflfi_render_secondary_field() {
	$value = get_option( TLSFLFI_OPTION_SECONDARY, '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" placeholder="181818" />',
		esc_attr( TLSFLFI_OPTION_SECONDARY ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Optional text and border color for Previous and other secondary buttons. Leave blank to keep the current dark text and gray border.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Pixels to keep clear below a fixed header when Next scrolls the page.
 */
function tlsflfi_render_scroll_offset_field() {
	$value = get_option( TLSFLFI_OPTION_SCROLL_OFFSET, '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" placeholder="81" />',
		esc_attr( TLSFLFI_OPTION_SCROLL_OFFSET ),
		esc_attr( $value )
	);
	echo '<p class="description">';
	esc_html_e(
		'Optional pixels to leave below a fixed header when Next or Finish scrolls the page to the iframe. Example: 81. Leave blank to align the iframe with the top of the window.',
		'iframe-lightning-flow'
	);
	echo '</p>';
}

/**
 * Render settings page.
 */
function tlsflfi_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'tlsflfi_settings' );
			do_settings_sections( 'tlsflfi-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
