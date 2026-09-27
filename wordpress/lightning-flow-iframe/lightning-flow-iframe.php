<?php
/**
 * iFrame Lightning Flow
 *
 * @package       IFRAMESFL
 * @author        Jason Best
 * @license       gplv2
 * @version       1.1.5
 *
 * @wordpress-plugin
 * Plugin Name:   Lightning Flow iFrame
 * Plugin URI:    https://github.com/jason-best/lightning-flow-iframe
 * Description:   Embed Salesforce Lightning Flows via shortcode. Supports FlowIframeEmbed (flow, endUrl, inputVars, page background and padding) with plugin defaults, plus legacy Visualforce embed mode.
 * Version:       1.1.5
 * Author:        Jason Best
 * Author URI:    https://threelevers.com
 * Text Domain:   iframe-lightning-flow
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with iFrame Lightning Flow. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TLSFLFI_LEGACY_DEMO_URL', 'https://threelevers.com/plugins/iframe-embed' );
define( 'TLSFLFI_RESERVED_PARAMS', 'flow,endUrl,inputVars,bg,padding,primary,primaryHover,secondary,scrollOffset' );
define( 'TLSFLFI_PLUGIN_FILE', __FILE__ );
define( 'TLSFLFI_DOCS_URL', 'https://threelevers.com/support/products/lightning-flow-iframe/wordpress/' );

require_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';

/**
 * Sanitize a Salesforce Flow API Developer Name.
 *
 * @param string $value Raw value.
 * @return string
 */
function tlsflfi_sanitize_flow_name( $value ) {
	$value = sanitize_text_field( $value );
	return preg_replace( '/[^a-zA-Z0-9_]/', '', $value );
}

/**
 * Sanitize a page background color for the iframe body.
 *
 * @param string $value Raw value.
 * @return string
 */
function tlsflfi_sanitize_bg( $value ) {
	$value = trim( sanitize_text_field( $value ) );
	if ( $value === '' ) {
		return '';
	}
	if ( preg_match( '/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value ) ) {
		return $value;
	}
	if ( preg_match( '/^(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value ) ) {
		return '#' . $value;
	}
	if ( preg_match( '/^(?:rgb|rgba|hsl|hsla)\([0-9.%\s,\/]+\)$/', $value ) ) {
		return $value;
	}
	if ( preg_match( '/^[a-zA-Z]{1,30}$/', $value ) ) {
		return $value;
	}
	return '';
}

/**
 * Sanitize iframe body padding. A bare number becomes pixels.
 *
 * @param string $value Raw value.
 * @return string
 */
function tlsflfi_sanitize_padding( $value ) {
	$value = trim( sanitize_text_field( (string) $value ) );
	$value = preg_replace( '/\s+/', ' ', $value );
	if ( $value === '' ) {
		return '';
	}
	if ( preg_match( '/^(?:0|(?:\d+(?:\.\d+)?)(?:px|rem|em|%))(?: (?:0|(?:\d+(?:\.\d+)?)(?:px|rem|em|%))){0,3}$/', $value ) ) {
		return $value;
	}
	if ( preg_match( '/^\d+(?:\.\d+)?$/', $value ) ) {
		return $value . 'px';
	}
	return '';
}

/**
 * Parent scroll offset in pixels. Not sent to the iframe.
 *
 * @param string $value Raw setting or shortcode value.
 * @return string
 */
function tlsflfi_sanitize_scroll_offset( $value ) {
	$value = trim( sanitize_text_field( (string) $value ) );
	$value = preg_replace( '/px$/i', '', $value );
	if ( $value === '' ) {
		return '';
	}
	if ( preg_match( '/^\d+(?:\.\d+)?$/', $value ) ) {
		$offset = (float) $value;
		if ( $offset >= 0 && $offset <= 1000 ) {
			return (string) $offset;
		}
	}
	return '';
}

/**
 * Return the next unique iframe element id for this request.
 *
 * @return string
 */
function tlsflfi_next_iframe_id() {
	static $count = 0;
	$count++;
	return 'tl-iframe-' . $count;
}

/**
 * Normalize inputvars to a list of trimmed names.
 *
 * @param string $inputvars Comma-separated allowlist.
 * @return array
 */
function tlsflfi_normalize_input_vars( $inputvars ) {
	if ( ! is_string( $inputvars ) || trim( $inputvars ) === '' ) {
		return array();
	}

	$parts = explode( ',', $inputvars );
	$keys  = array();

	foreach ( $parts as $part ) {
		$key = trim( $part );
		if ( $key !== '' ) {
			$keys[] = $key;
		}
	}

	return $keys;
}

/**
 * Parse extraqs into key/value pairs.
 *
 * @param string $extraqs Query fragment such as "a=1&b=2".
 * @return array
 */
function tlsflfi_parse_extraqs( $extraqs ) {
	$params = array();
	if ( ! is_string( $extraqs ) || trim( $extraqs ) === '' ) {
		return $params;
	}

	parse_str( $extraqs, $params );
	return is_array( $params ) ? $params : array();
}

/**
 * Read query parameters from the current WordPress page request.
 *
 * Uses $_GET so custom params (recordId, source, etc.) are available even when
 * they are not registered WordPress query vars.
 *
 * @return array
 */
function tlsflfi_get_parent_query_array() {
	$params = array();

	if ( empty( $_GET ) || ! is_array( $_GET ) ) {
		return $params;
	}

	foreach ( $_GET as $key => $value ) {
		if ( ! is_string( $key ) || $key === '' ) {
			continue;
		}
		if ( ! preg_match( '/^[a-zA-Z0-9_]+$/', $key ) ) {
			continue;
		}
		if ( is_array( $value ) ) {
			$value = reset( $value );
		}
		if ( $value === null || $value === '' ) {
			continue;
		}

		$params[ $key ] = sanitize_text_field( wp_unslash( $value ) );
	}

	return $params;
}

/**
 * Collect flow input values allowed by inputvars.
 *
 * Precedence (lowest to highest): extraqs, parent page query string, shortcode attrs.
 *
 * @param array $atts             Effective shortcode attributes.
 * @param array $parent_query_array Parent page query parameters.
 * @param array $raw_atts         Raw shortcode attributes from the shortcode tag.
 * @return array
 */
function tlsflfi_collect_flow_params( $atts, $parent_query_array, $raw_atts = array() ) {
	$allowed_keys = tlsflfi_normalize_input_vars( $atts['inputvars'] );
	if ( count( $allowed_keys ) === 0 ) {
		return array();
	}

	$flow_params = array();

	foreach ( tlsflfi_parse_extraqs( $atts['extraqs'] ) as $key => $value ) {
		if ( tlsflfi_is_shell_param( $key ) ) {
			continue;
		}
		if ( in_array( $key, $allowed_keys, true ) && $value !== '' && $value !== null ) {
			$flow_params[ $key ] = sanitize_text_field( $value );
		}
	}

	if ( is_array( $parent_query_array ) ) {
		foreach ( $parent_query_array as $key => $value ) {
			if ( tlsflfi_is_shell_param( $key ) ) {
				continue;
			}
			if ( in_array( $key, $allowed_keys, true ) && $value !== '' && $value !== null ) {
				$flow_params[ $key ] = sanitize_text_field( $value );
			}
		}
	}

	if ( is_array( $raw_atts ) ) {
		$reserved = array( 'endurl', 'iframeurl', 'embedurl', 'flow', 'inputvars', 'height', 'extraqs', 'ease', 'easespeed', 'lazy', 'bg', 'padding', 'primary', 'primaryhover', 'secondary', 'scrolloffset' );
		foreach ( $raw_atts as $key => $value ) {
			if ( in_array( $key, $reserved, true ) ) {
				continue;
			}
			if ( ! in_array( $key, $allowed_keys, true ) ) {
				continue;
			}
			if ( is_array( $value ) ) {
				$value = reset( $value );
			}
			if ( $value === null || $value === '' ) {
				continue;
			}
			$flow_params[ $key ] = sanitize_text_field( $value );
		}
	}

	return $flow_params;
}

/**
 * Background and padding are page-shell settings, not flow inputs.
 *
 * @param string $key Parameter name.
 * @return bool
 */
function tlsflfi_is_shell_param( $key ) {
	return in_array( strtolower( (string) $key ), array( 'bg', 'padding', 'primary', 'primaryhover', 'secondary', 'scrolloffset' ), true );
}

/**
 * Merge shortcode attributes with plugin defaults.
 *
 * @param array $atts Shortcode attributes.
 * @return array
 */
function tlsflfi_get_effective_atts( $atts ) {
	$defaults = array(
		'endurl'    => '',
		'iframeurl' => '',
		'embedurl'  => '',
		'flow'      => '',
		'inputvars' => '',
		'height'    => '50px',
		'extraqs'   => '',
		'ease'      => 'false',
		'easespeed' => '0.2',
		'lazy'      => 'true',
		'bg'           => '',
		'padding'      => '',
		'primary'      => '',
		'primaryhover' => '',
		'secondary'    => '',
		'scrolloffset' => '',
	);

	$atts = shortcode_atts( $defaults, $atts, 'Lightning-Flow-iFrame' );

	$iframe_from_shortcode = trim( $atts['embedurl'] ) !== '' ? trim( $atts['embedurl'] ) : trim( $atts['iframeurl'] );
	$default_iframe = get_option( TLSFLFI_OPTION_IFRAME_URL, '' );
	$default_flow   = get_option( TLSFLFI_OPTION_FLOW_NAME, '' );
	$default_end    = get_option( TLSFLFI_OPTION_END_URL, '' );
	$default_bg     = get_option( TLSFLFI_OPTION_BG, '' );
	$default_pad    = get_option( TLSFLFI_OPTION_PADDING, '' );
	$default_primary = get_option( TLSFLFI_OPTION_PRIMARY, '' );
	$default_hover   = get_option( TLSFLFI_OPTION_PRIMARY_HOVER, '' );
	$default_secondary = get_option( TLSFLFI_OPTION_SECONDARY, '' );
	$default_scroll    = get_option( TLSFLFI_OPTION_SCROLL_OFFSET, '' );

	$effective_iframe = $iframe_from_shortcode !== '' ? $iframe_from_shortcode : $default_iframe;
	if ( $effective_iframe === '' ) {
		$effective_iframe = TLSFLFI_LEGACY_DEMO_URL;
	}

	$effective_flow = trim( $atts['flow'] ) !== '' ? trim( $atts['flow'] ) : trim( $default_flow );
	$effective_end  = trim( $atts['endurl'] ) !== '' ? trim( $atts['endurl'] ) : trim( $default_end );

	$atts['iframeurl'] = esc_url_raw( sanitize_url( $effective_iframe ) );
	$atts['flow']      = tlsflfi_sanitize_flow_name( $effective_flow );
	$atts['endurl']    = $effective_end !== '' ? esc_url_raw( sanitize_url( $effective_end ) ) : '';
	$atts['height']    = sanitize_text_field( $atts['height'] );
	$atts['extraqs']   = sanitize_text_field( $atts['extraqs'] );
	$atts['inputvars'] = sanitize_text_field( $atts['inputvars'] );
	$atts['ease']      = sanitize_text_field( $atts['ease'] );
	$atts['easespeed'] = sanitize_text_field( $atts['easespeed'] );
	$atts['lazy']      = sanitize_text_field( $atts['lazy'] );

	$bg_source      = trim( (string) $atts['bg'] ) !== '' ? $atts['bg'] : $default_bg;
	$padding_source = trim( (string) $atts['padding'] ) !== '' ? $atts['padding'] : $default_pad;
	$atts['bg']     = tlsflfi_sanitize_bg( $bg_source );
	$atts['padding'] = tlsflfi_sanitize_padding( $padding_source );

	$primary_source = trim( (string) $atts['primary'] ) !== '' ? $atts['primary'] : $default_primary;
	$hover_source   = trim( (string) $atts['primaryhover'] ) !== '' ? $atts['primaryhover'] : $default_hover;
	$secondary_source = trim( (string) $atts['secondary'] ) !== '' ? $atts['secondary'] : $default_secondary;
	$atts['primary']      = tlsflfi_sanitize_bg( $primary_source );
	$atts['primaryhover'] = tlsflfi_sanitize_bg( $hover_source );
	$atts['secondary']    = tlsflfi_sanitize_bg( $secondary_source );
	$scroll_source        = trim( (string) $atts['scrolloffset'] ) !== '' ? $atts['scrolloffset'] : $default_scroll;
	$atts['scrolloffset'] = tlsflfi_sanitize_scroll_offset( $scroll_source );

	return $atts;
}

/**
 * Build legacy iframe src (parent query string + extraqs + endurl).
 *
 * @param array  $atts                Effective shortcode attributes.
 * @param string $parent_query_string Parent page query string without leading ?.
 * @return string
 */
function tlsflfi_build_legacy_src( $atts, $parent_query_string ) {
	$iframesrc = $atts['iframeurl'];
	$endurl    = $atts['endurl'];
	$extraqs   = $atts['extraqs'];

	$src = $iframesrc . '?' . $parent_query_string;
	if ( $extraqs !== '' ) {
		$src .= '&' . $extraqs;
	}
	$src .= '&endurl=' . $endurl;

	return $src;
}

/**
 * Build FlowIframeEmbed iframe src.
 *
 * @param array $atts                Effective shortcode attributes.
 * @param array $parent_query_array  Parent page query parameters.
 * @param array $raw_atts            Raw shortcode attributes.
 * @return string|false
 */
function tlsflfi_build_embed_src( $atts, $parent_query_array, $raw_atts = array() ) {
	if ( $atts['flow'] === '' ) {
		return false;
	}

	if ( $atts['iframeurl'] === '' ) {
		return false;
	}

	$args = array(
		'flow' => $atts['flow'],
	);

	if ( $atts['endurl'] !== '' ) {
		$args['endUrl'] = $atts['endurl'];
	}

	$allowed_keys = tlsflfi_normalize_input_vars( $atts['inputvars'] );
	if ( count( $allowed_keys ) > 0 ) {
		$args['inputVars'] = implode( ',', $allowed_keys );
	}

	$flow_params = tlsflfi_collect_flow_params( $atts, $parent_query_array, $raw_atts );
	$args        = array_merge( $args, $flow_params );

	if ( $atts['bg'] !== '' ) {
		$args['bg'] = $atts['bg'];
	}
	if ( $atts['padding'] !== '' ) {
		$args['padding'] = $atts['padding'];
	}
	if ( $atts['primary'] !== '' ) {
		$args['primary'] = $atts['primary'];
	}
	if ( $atts['primaryhover'] !== '' ) {
		$args['primaryHover'] = $atts['primaryhover'];
	}
	if ( $atts['secondary'] !== '' ) {
		$args['secondary'] = $atts['secondary'];
	}

	return add_query_arg( $args, $atts['iframeurl'] );
}

/**
 * Render iframe markup and resize listener.
 *
 * @param string $iframe_id Unique iframe element id.
 * @param string $src       Iframe src URL.
 * @param array  $atts      Effective shortcode attributes.
 * @return string
 */
function tlsflfi_render_iframe( $iframe_id, $src, $atts ) {
	$height = $atts['height'];
	$ease   = $atts['ease'];
	$es     = $atts['easespeed'];
	$lazy   = $atts['lazy'];

	ob_start();

	if ( $ease === 'true' ) {
		printf(
			'<style>#%1$s { -moz-transition: height %2$ss ease; -webkit-transition: height %2$ss ease; -o-transition: height %2$ss ease; transition: height %2$ss ease; }</style>',
			esc_attr( $iframe_id ),
			esc_attr( $es )
		);
	}

	$lazy_attr = ( $lazy === 'true' ) ? ' loading="lazy"' : '';
	$scroll_offset = isset( $atts['scrolloffset'] ) ? (float) $atts['scrolloffset'] : 0;
	$iframe_style  = 'height:' . $height . ';';
	if ( $scroll_offset > 0 ) {
		$iframe_style .= 'scroll-margin-top:' . $scroll_offset . 'px;';
	}

	printf(
		'<iframe id="%1$s"%2$s src="%3$s" width="100%%" scrolling="no" style="%4$s" frameborder="0" title="%5$s"></iframe>',
		esc_attr( $iframe_id ),
		$lazy_attr,
		esc_url( $src ),
		esc_attr( $iframe_style ),
		esc_attr__( 'Salesforce Flow', 'iframe-lightning-flow' )
	);
	?>
<script>
(function () {
  var iframeId = <?php echo wp_json_encode( $iframe_id ); ?>;
  var scrollOffset = <?php echo wp_json_encode( $scroll_offset ); ?>;
  window.addEventListener('message', function (e) {
    if (!e.data) {
      return;
    }
    var iframe = document.getElementById(iframeId);
    if (!iframe || e.source !== iframe.contentWindow) {
      return;
    }
    if (e.data.frameScroll === 'top' && iframe.scrollIntoView) {
      if (scrollOffset > 0) {
        iframe.style.scrollMarginTop = scrollOffset + 'px';
      }
      iframe.scrollIntoView({ block: 'start', behavior: 'smooth' });
    }
    if (typeof e.data.frameHeight === 'number') {
      iframe.style.height = (e.data.frameHeight + 20) + 'px';
    }
  });
})();
</script>
	<?php

	return ob_get_clean();
}

/**
 * Shortcode callback.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function tlsflfi_iframe_prefs( $atts ) {
	wp_enqueue_script( 'iframe-resizer', plugin_dir_url( __FILE__ ) . 'js/iframeResizer.min.js', array(), '1.1.5', true );

	$raw_atts             = is_array( $atts ) ? $atts : array();
	$atts                 = tlsflfi_get_effective_atts( $atts );
	$parent_query_array   = tlsflfi_get_parent_query_array();
	$parent_query_string  = http_build_query( $parent_query_array );

	$iframe_id = tlsflfi_next_iframe_id();
	$is_embed  = $atts['flow'] !== '';

	if ( $is_embed ) {
		$src = tlsflfi_build_embed_src( $atts, $parent_query_array, $raw_atts );
		if ( $src === false ) {
			return '<!-- Lightning Flow iFrame: missing flow or iframe URL -->';
		}
	} else {
		$src = tlsflfi_build_legacy_src( $atts, $parent_query_string );
	}

	return tlsflfi_render_iframe( $iframe_id, $src, $atts );
}
add_shortcode( 'Lightning-Flow-iFrame', 'tlsflfi_iframe_prefs' );
