# WordPress plugin

The **Lightning Flow iFrame** WordPress plugin is included in this repository at [`wordpress/lightning-flow-iframe/`](../wordpress/lightning-flow-iframe/).

It embeds Salesforce flows via shortcode with dynamic iframe height. **v1.1.0** adds **FlowIframeEmbed** support (`flow`, `endUrl`, optional `inputVars`) while preserving legacy behavior for existing sites.

## Install from this repository

1. Zip the plugin folder so WordPress sees `lightning-flow-iframe/` at the root of the archive:
   - On Windows: right-click `wordpress/lightning-flow-iframe` → **Send to** → **Compressed (zipped) folder**
   - Or from repo root: compress `wordpress/lightning-flow-iframe` (not the whole repo)
2. In WordPress: **Plugins → Add New → Upload Plugin** → choose the zip → **Install Now** → **Activate**
3. Complete [Salesforce Site setup](INSTALL.md) and expose **FlowIframeEmbed** on a public Site
4. Configure defaults under **Settings → Lightning Flow iFrame** (optional)

## Settings page

**Settings → Lightning Flow iFrame**

| Setting | Description |
|---------|-------------|
| **Default iFrame URL** | Salesforce Site URL to **FlowIframeEmbed** (no query string). Example: `https://your-site.force.com/site-prefix/FlowIframeEmbed` |
| **Default Flow Name** | Flow API Developer Name (e.g. `Check_In_Dispatch`). When set, shortcodes without `flow` use embed mode. **Leave blank** to keep legacy behavior. |
| **Default End URL** | Parent redirect when the flow finishes (`endUrl` on iframe URL). Optional. |

When all three defaults are configured, the minimal shortcode is:

```
[Lightning-Flow-iFrame]
```

Shortcode attributes always override settings. `inputvars` is **optional**—omit it when the flow needs no URL inputs.

## Shortcode modes

### Embed mode (FlowIframeEmbed)

Active when **effective `flow`** is set (shortcode `flow` or **Default Flow Name** in settings).

```
[Lightning-Flow-iFrame]
```

Or with optional flow inputs:

```
[Lightning-Flow-iFrame
  flow="Check_In_Dispatch"
  iframeurl="https://your-site.force.com/site-prefix/FlowIframeEmbed"
  endurl="https://yoursite.com/thanks"
  inputvars="recordId,source"
  extraqs="recordId=001xxx&source=homepage"
  height="75px"
  ease="true"
  easespeed="0.2"
  lazy="true"]
```

| Attribute | Description |
|-----------|-------------|
| `iframeurl` / `embedurl` | Site URL to **FlowIframeEmbed** (no query) |
| `flow` | Flow API Developer Name |
| `endurl` | Sent as `endUrl` on iframe URL |
| `inputvars` | Optional comma-separated allowlist; when omitted, no flow inputs are passed |
| `extraqs` | `key=value&...`; only keys in `inputvars` are appended |
| `height` | Initial iframe height (e.g. `75px`) |
| `ease` | `true` to animate height changes |
| `easespeed` | Transition duration in seconds (default `0.2`) |
| `lazy` | `true` for `loading="lazy"` on the iframe |

Parent page query parameters are forwarded **only** when listed in `inputvars`.

### Legacy mode

Active when **no effective `flow`** is configured (Default Flow Name blank and shortcode has no `flow`).

```
[Lightning-Flow-iFrame iframeurl="https://your-site.force.com/site-prefix/YourVfPage" endurl="https://yoursite.com/thanks" height="75px" extraqs="inputVariable=value"]
```

Legacy mode appends **all** query parameters from the current WordPress page to the iframe URL and uses `endurl` (lowercase).

## Legacy vs current embed

| WordPress plugin (embed mode) | `three-levers-flow-embed.js` |
|-------------------------------|------------------------------|
| Shortcode `[Lightning-Flow-iFrame]` | `window.tlFlowEmbed` config |
| Settings defaults | Manual config object |
| `iframeurl` / `embedurl` | `embedUrl` |
| `endurl` | `endUrl` |
| `inputvars` + `extraqs` | `inputVars` + `params` |
| Parent QS when in `inputvars` | Explicit `params` only |

See [README migration section](../README.md#migration-from-legacy-ifc-widget) and [EMBED.md](EMBED.md).

## Plugin files

```text
wordpress/lightning-flow-iframe/
  lightning-flow-iframe.php       Main plugin + shortcode
  includes/admin-settings.php     Settings page (defaults)
  readme.txt                      WordPress.org-style readme
  js/                             iframe-resizer + legacy helper scripts
```
