=== Lightning Flow iFrame ===
Author URI: https://threelevers.com
Plugin URI: https://github.com/jason-best/lightning-flow-iframe
Contributors: jasonbest
Tags: Salesforce, iframe, flow
Requires at least: 4.9
Tested up to: 6.7
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Shortcode to embed a scalable Salesforce Lightning Flow iframe with FlowIframeEmbed support.

== Description ==

Lightning Flow iFrame is a WordPress plugin that embeds Salesforce Lightning Screen Flows in a scalable iframe using a simple shortcode.

Features:

 * Auto height adjustment based on flow content (postMessage)
 * FlowIframeEmbed mode: flow, endUrl, optional inputVars
 * Plugin defaults under Settings → Lightning Flow iFrame
 * Bare shortcode `[Lightning-Flow-iFrame]` when defaults are configured
 * Legacy mode preserved for existing Visualforce integrations

== Frequently Asked Questions ==

= How do I set up Salesforce? =

Install the FlowIframeEmbed package (or deploy equivalent metadata), activate your Screen Flow, expose FlowIframeEmbed on a Salesforce Site, and grant guest access. See the repository docs and https://threelevers.com/support/products/lightning-flow-iframe/salesforce-setup/

= Can I use just [Lightning-Flow-iFrame] with no attributes? =

Yes, when you configure Default iFrame URL, Default Flow Name, and optionally Default End URL under Settings → Lightning Flow iFrame.

= Is inputvars required? =

No. Omit inputvars when your flow needs no URL inputs. When set, only listed parameters (from extraqs or the parent page query string) are passed to the flow.

= How do I keep legacy behavior? =

Leave Default Flow Name blank and do not add a flow attribute to your shortcode. Legacy mode forwards the full parent page query string and uses endurl (lowercase).

== Installation ==

1. Go to Plugins in the Admin menu
2. Click Add New → Upload Plugin
3. Upload lightning-flow-iframe.zip
4. Activate the plugin
5. Configure Settings → Lightning Flow iFrame (optional)
6. Add `[Lightning-Flow-iFrame]` to a page or post

== Changelog ==

= 1.1.0 =
* FlowIframeEmbed embed mode (flow, endUrl, optional inputVars)
* Settings page: Default iFrame URL, Default Flow Name, Default End URL
* Bare shortcode support when defaults are configured
* Legacy mode unchanged when Default Flow Name is blank

= 1.0.0: January 6, 2023 =
* Initial release
