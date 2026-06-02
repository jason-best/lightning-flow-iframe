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
 * Settings section intro.
 */
function tlsflfi_render_defaults_section() {
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
